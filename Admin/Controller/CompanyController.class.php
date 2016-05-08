<?php
/**
 * 企业逻辑
 * buzhidao
 * 2016-03-26
 */
namespace Admin\Controller;

class CompanyController extends CommonController
{
    public function __construct()
    {
        parent::__construct();
    }

    //获取subcompanyid
    private function _getSubcompanyid($ck=false, $ajax=true)
    {
        $subcompanyid = mRequest('subcompanyid');
        $this->assign('subcompanyid', $subcompanyid);

        $ck&&!$subcompanyid&&$ajax ? $this->ajaxReturn(1, '未知'.L('WordLang.SubCompanyLang').'ID！') : null;
        $ck&&!$subcompanyid&&!$ajax ? $this->pageReturn(1, '未知'.L('WordLang.SubCompanyLang').'ID！') : null;

        return $subcompanyid;
    }

    //获取subcompanyname
    private function _getSubcompanyname($ck=false, $ajax=true)
    {
        $subcompanyname = mRequest('subcompanyname');
        $this->assign('subcompanyname', $subcompanyname);

        $ck&&!$subcompanyname&&$ajax ? $this->ajaxReturn(1, '请填写'.L('WordLang.SubCompanyLang').'名称！') : null;
        $ck&&!$subcompanyname&&!$ajax ? $this->pageReturn(1, '请填写'.L('WordLang.SubCompanyLang').'名称！') : null;

        return $subcompanyname;
    }

    //获取departmentid
    private function _getDepartmentid()
    {
        $departmentid = mRequest('departmentid');
        $this->assign('departmentid', $departmentid);

        return $departmentid;
    }

    //获取departmentname
    private function _getDepartmentname()
    {
        $departmentname = mRequest('departmentname');
        $this->assign('departmentname', $departmentname);

        return $departmentname;
    }

    public function index(){}

    //公司管理
    public function companym()
    {
        $this->display();
    }

    //保存公司信息
    public function companymsave()
    {
        $companyno = mRequest('companyno');
        if (!$companyno) $this->ajaxReturn(1, '未知'.L('WordLang.CompanyLang').'信息！');

        $companyname = mRequest('companyname');
        if (!$companyname) $this->ajaxReturn(1, '未知'.L('WordLang.CompanyLang').'名称！');

        $result = M('company')->where(array('companyno'=>$companyno))->save(array('companyname'=>$companyname));
        if ($result) {
            $this->ajaxReturn(0, '保存成功！');
        } else {
            $this->ajaxReturn(1, '保存失败！');
        }
    }

    //子公司管理
    public function subcompany()
    {
        $subcompanyname = $this->_getSubcompanyname();

        $datalist = D('Company')->getSubCompany(null, null, $subcompanyname);
        $this->assign('datalist', $datalist['data']);

        $this->display();
    }

    //新增部门
    public function newsubcompany()
    {
        $this->display();
    }

    //新增部门-保存
    public function newsubcompanysave()
    {
        $subcompanyname = $this->_getSubcompanyname(true);

        $maxsubcompanyno = D('Company')->getMaxSubcompanyno();

        $subcompanyno = $maxsubcompanyno+1;
        $data = array(
            'subcompanyname' => $subcompanyname,
            'subcompanyno'   => $subcompanyno,
            'companyid'      => $this->company['companyid'],
            'createtime'     => mkDateTime(),
            'updatetime'     => mkDateTime(),
        );
        $result = D('Company')->savesubcompany(null, $data);
        if ($result) {
            $this->ajaxReturn(0, '保存成功！');
        } else {
            $this->ajaxReturn(1, '保存失败！');
        }
    }

    //AJAX获取编辑html
    public function ajaxGetSubcompanyHtml()
    {
        $subcompanyno = $this->_getSubcompanyno(true);

        $subcompanyinfo = D('Company')->getSubCompanyByNo($subcompanyno);
        $this->assign('subcompanyinfo', $subcompanyinfo);

        $html = $this->fetch('Company/subcompany_modal');

        $this->ajaxReturn(0, '', array(
            'html' => $html
        ));
    }

    //编辑子公司-保存
    public function upsubcompanysave()
    {
        $subcompanyid = $this->_getSubcompanyid(true);
        $subcompanyname = $this->_getSubcompanyname(true);

        $data = array(
            'subcompanyname' => $subcompanyname,
            'updatetime'     => mkDateTime(),
        );
        $result = D('Company')->savesubcompany($subcompanyid, $data);
        if ($result) {
            $this->ajaxReturn(0, '保存成功！');
        } else {
            $this->ajaxReturn(1, '保存失败！');
        }
    }

    //管理部门
    public function department()
    {
        $departmentname = $this->_getDepartmentname();

        $datalist = array();
        foreach ($this->company['subcompany'] as $subcompany) {
            if (isset($subcompany['department'])) {
                foreach ($subcompany['department'] as $department) {
                    if ($departmentname && strpos($department['departmentname'], $departmentname)===false) continue;

                    $department['subcompanyname'] = $subcompany['subcompanyname'];
                    $datalist[] = $department;
                }
            }
        }
        $this->assign('datalist', $datalist);

        $this->display();
    }

    //新增部门
    public function newdepartment()
    {
        //获取监控软件列表
        $mtserverlist = D('MonitorServer')->getMtserver();
        $this->assign("mtserverlist", $mtserverlist['data']);

        $this->display();
    }

    //新增部门-保存
    public function newdepartmentsave()
    {
        $subcompanyid = $this->_getSubcompanyid();
        if (!$subcompanyid) $this->ajaxReturn(1, '请选择'.L('WordLang.SubCompanyLang').'！');

        $departmentname = $this->_getDepartmentname();
        if (!$departmentname) $this->ajaxReturn(1, '请填写'.L('WordLang.DepartmentLang').'名称！');

        $mtserverid = mRequest('mtserverid');
        if (!$mtserverid) $this->ajaxReturn(1, '请选择监控软件！');

        $maxdepartmentno = D('Company')->getMaxDepartmentno();

        $departmentno = $maxdepartmentno+1;
        $data = array(
            'departmentname' => $departmentname,
            'departmentno'   => $departmentno,
            'subcompanyid'   => $subcompanyid,
            'maxuserno'      => 0,
            'mtserverid'     => $mtserverid,
            'createtime'     => mkDateTime(),
            'updatetime'     => mkDateTime(),
        );
        $result = D('Company')->savedepartment(null, $data);
        if ($result) {
            $this->ajaxReturn(0, '保存成功！');
        } else {
            $this->ajaxReturn(1, '保存失败！');
        }
    }

    //编辑部门-ajax获取html
    public function ajaxGetDepartmentHtml()
    {
        $departmentno = $this->_getDepartmentno();
        if (!$departmentno) $this->ajaxReturn(1, '未知'.L('WordLang.DepartmentLang').'！');

        $departmentinfo = array();
        foreach ($this->company['subcompany'] as $subcompany) {
            if (isset($subcompany['department'])) {
                foreach ($subcompany['department'] as $department) {
                    if ($department['departmentno'] == $departmentno) {
                        $department['subcompanyname'] = $subcompany['subcompanyname'];
                        $departmentinfo = $department;
                        break(2);
                    }
                }
            }
        }
        $this->assign("departmentinfo", $departmentinfo);

        //获取监控软件列表
        $mtserverlist = D('MonitorServer')->getMtserver();
        $this->assign("mtserverlist", $mtserverlist['data']);

        $html = $this->fetch('Company/department_modal');

        $this->ajaxReturn(0, '', array(
            'html' => $html
        ));
    }

    //编辑部门-保存
    public function updepartmentsave()
    {
        $departmentid = $this->_getDepartmentid();
        if (!$departmentid) $this->ajaxReturn(1, '未知'.L('WordLang.DepartmentLang').'！');

        $departmentname = $this->_getDepartmentname();
        if (!$departmentname) $this->ajaxReturn(1, '请填写'.L('WordLang.DepartmentLang').'名称！');

        $mtserverid = mRequest('mtserverid');
        if (!$mtserverid) $this->ajaxReturn(1, '请选择监控软件！');

        $data = array(
            'departmentname' => $departmentname,
            'mtserverid'     => $mtserverid,
            'updatetime'     => mkDateTime()
        );
        $result = D('Company')->savedepartment($departmentid, $data);
        if ($result) {
            $this->ajaxReturn(0, '保存成功！');
        } else {
            $this->ajaxReturn(1, '保存失败！');
        }
    }

    //AJAX获取部门列表 通过subcompanyno
    public function ajaxGetDepartment()
    {
        $subcompanyno = $this->_getSubcompanyno();
        if (!$subcompanyno) $this->ajaxReturn(0, '', array('department'=>$department));

        $company = $this->company;

        $department = isset($company['subcompany'][$subcompanyno]) ? $company['subcompany'][$subcompanyno]['department'] : array();

        $this->ajaxReturn(0, '', array(
            'department' => $department
        ));
    }
}