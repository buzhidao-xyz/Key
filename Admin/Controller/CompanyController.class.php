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

    public function index(){}

    //获取subcompanyno
    private function _getSubcompanyno()
    {
        $subcompanyno = mRequest('subcompanyno');
        $this->assign('subcompanyno', $subcompanyno);

        return $subcompanyno;
    }

    //AJAX获取部门列表 通过subcompanyno
    public function ajaxDepartment()
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