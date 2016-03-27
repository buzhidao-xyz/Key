<?php
/**
 * 菜单模型
 * buzhidao
 * 2015-8-1
 */
namespace Admin\Model;

class CompanyModel extends CommonModel
{
    public function __construct()
    {
        parent::__construct();
    }

    //获取公司
    public function getCompany($companyid=null)
    {
        $where = array();
        if ($companyid) $where['companyid'] = is_array($companyid) ? array('in', $companyid) : $companyid;

        $total = M('company')->where($where)->count();
        $data = M('company')->where($where)->select();

        return array('total'=>$total, 'data'=>is_array($data)?$data:array());
    }

    //获取子公司信息
    public function getSubCompany($subcompanyid=null)
    {
        $where = array();
        if ($companyid) $where['companyid'] = is_array($companyid) ? array('in', $companyid) : $companyid;

        $total = M('company')->where($where)->count();
        $data = M('company')->where($where)->select();

        return array('total'=>$total, 'data'=>is_array($data)?$data:array());
    }
}