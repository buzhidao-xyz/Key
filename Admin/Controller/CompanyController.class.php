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

    //公司管理
    public function companym()
    {

    }

    //子公司管理
    public function subcompany()
    {
        
    }

    //部门管理
    public function department()
    {
        
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