<?php
/**
 * 钥匙柜业务逻辑
 * buzhidao
 * 2016-03-29
 */
namespace Admin\Controller;

use Any\Upload;

class CabinetController extends CommonController
{
    public function __construct()
    {
        parent::__construct();
    }

    //获取钥匙柜名称
    private function _getCabinetname()
    {
        $cabinetname = mRequest('cabinetname');
        $this->assign('cabinetname', $cabinetname);

        return $cabinetname;
    }

    public function index(){}

    //新增钥匙柜
    public function newcabinet()
    {
        $this->display();
    }

    //保存钥匙柜
    public function newcabinetsave()
    {
        $cabinetname = $this->_getCabinetname();
        if (!$cabinetname) $this->ajaxReturn(1, '请填写钥匙柜名称！');
        $departmentno = $this->_getDepartmentno();
        if (!$departmentno) $this->ajaxReturn(1, '请选择派出所！');
        $keylocknonum = mRequest('keylocknonum');
        if (!$keylocknonum) $this->ajaxReturn(1, '请填写钥匙锁孔数量！');

        //获取某部门最大的钥匙柜编号
        $maxcabinetno = D('Cabinet')->getMaxCabinetno($departmentno);

        $cabinetno = $maxcabinetno+1;
        $data = array(
            'cabinetname'  => $cabinetname,
            'departmentno' => $departmentno,
            'cabinetno'    => $cabinetno,
            'keylocknonum' => $keylocknonum,
            'isdelete'     => 0,
            'createtime'   => mkDateTime(),
            'updatetime'   => mkDateTime(),
        );
        $result = D('Cabinet')->savecabinet(null, $data);
        if ($result) {
            $this->ajaxReturn(0, '保存成功！');
        } else {
            $this->ajaxReturn(1, '保存失败！');
        }
    }

    //管理钥匙柜
    public function cabinetlist()
    {
        $subcompanyno = $this->_getSubcompanyno();

        $departmentno = $this->_getDepartmentno();
        if ($subcompanyno && !$departmentno) {
            if (isset($this->company['subcompany'][$subcompanyno]['department'])) {
                foreach ($this->company['subcompany'][$subcompanyno]['department'] as $department) {
                    $departmentno[] = $department['departmentno'];
                }
            }
        }

        $cabinetname = $this->_getCabinetname();

        list($start, $length) = $this->_mkPage();
        $cabinetlist = D('Cabinet')->getCabinet(null, $cabinetname, $departmentno, $start, $length);
        $total = $cabinetlist['total'];
        $datalist = $cabinetlist['data'];

        foreach ($datalist as $k=>$d) {
            $subcompanyno = 0;
            foreach ($this->company['subcompany'] as $subcompany) {
                if (isset($subcompany['department'])) {
                    foreach ($subcompany['department'] as $department) {
                        if ($department['departmentno'] == $d['departmentno']) {
                            $subcompanyno = $subcompany['subcompanyno'];
                        }
                    }
                }
            }

            $datalist[$k]['subcompanyno'] = $subcompanyno;
        }
        $this->assign('datalist', $datalist);

        $params = array(
            'subcompanyno' => $subcompanyno,
            'departmentno' => $departmentno,
            'cabinetname' => $cabinetname,
        );
        $this->assign('param', $params);
        //解析分页数据
        $this->_mkPagination($total, $params);

        $this->display();
    }

    //编辑钥匙柜信息
    public function ajaxGetCabinetHtml()
    {
        $cabinetid = mRequest('cabinetid');
        if (!$cabinetid) $this->ajaxReturn(1, '钥匙柜信息错误！');

        $cabinetinfo = D('Cabinet')->getCabinetByID($cabinetid);

        //subcompanyno
        foreach ($this->company['subcompany'] as $subcompany) {
            if (isset($subcompany['department'])) {
                foreach ($subcompany['department'] as $department) {
                    if ($department['departmentno'] == $cabinetinfo['departmentno']) {
                        $cabinetinfo['subcompanyno'] = $subcompany['subcompanyno'];
                        break(2);
                    }
                }
            }
        }
        $this->assign('subcompanyno', $cabinetinfo['subcompanyno']);
        $this->assign('departmentno', $cabinetinfo['departmentno']);

        $this->assign('cabinetinfo', $cabinetinfo);
        $html = $this->fetch('Cabinet/upcabinet_modal');

        $this->ajaxReturn(0, '', array(
            'html' => $html
        ));
    }

    //编辑钥匙柜信息
    public function upcabinetsave()
    {
        $cabinetid = mRequest('cabinetid');
        if (!$cabinetid) $this->ajaxReturn(1, '钥匙柜信息错误！');

        $cabinetname = $this->_getCabinetname();
        if (!$cabinetname) $this->ajaxReturn(1, '请填写钥匙柜名称！');
        $departmentno = $this->_getDepartmentno();
        if (!$departmentno) $this->ajaxReturn(1, '请选择派出所！');
        $keylocknonum = mRequest('keylocknonum');
        if (!$keylocknonum) $this->ajaxReturn(1, '请填写钥匙锁孔数量！');

        $data = array(
            'cabinetname'  => $cabinetname,
            'departmentno' => $departmentno,
            'keylocknonum' => $keylocknonum,
            'updatetime'   => mkDateTime(),
        );
        $result = D('Cabinet')->savecabinet($cabinetid, $data);
        if ($result) {
            $this->ajaxReturn(0, '保存成功！');
        } else {
            $this->ajaxReturn(1, '保存失败！');
        }
    }

    //ajax获取钥匙柜信息
    public function ajaxGetCabinet()
    {
        $departmentno = $this->_getDepartmentno();
        if (!$departmentno) $this->ajaxReturn(1, '钥匙柜信息错误！');

        $cabinetlist = D('Cabinet')->getCabinet(null, null, $departmentno, 0, 9999);
        $cabinetlist = $cabinetlist['data'];

        $this->ajaxReturn(0, '', array(
            'cabinetlist' => $cabinetlist
        ));
    }
}