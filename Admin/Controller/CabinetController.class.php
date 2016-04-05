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

        //获取某部门最大的钥匙柜编号
        $maxcabinetno = D('Cabinet')->getMaxCabinetno($departmentno);

        $cabinetno = $maxcabinetno+1;
        $data = array(
            'cabinetname'  => $cabinetname,
            'departmentno' => $departmentno,
            'cabinetno'    => $cabinetno,
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