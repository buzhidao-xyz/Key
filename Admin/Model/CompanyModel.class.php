<?php
/**
 * 企业模型
 * buzhidao
 * 2016-03-27
 */
namespace Admin\Model;

class CompanyModel extends CommonModel
{
    public function __construct()
    {
        parent::__construct();
    }

    //获取公司信息
    public function getCompany()
    {
        $data = M('company')->find();

        return $data;
    }

    //获取子公司信息
    public function getSubCompany($companyid=null, $subcompanyid=null, $subcompanyname=null)
    {
        $where = array();
        if ($companyid) $where['companyid'] = $companyid;
        if ($subcompanyid) $where['subcompanyid'] = is_array($subcompanyid) ? array('in', $subcompanyid) : $subcompanyid;
        if ($subcompanyname) $where['subcompanyname'] = array('like', '%'.$subcompanyname.'%');

        $total = M('subcompany')->where($where)->count();
        $data = M('subcompany')->where($where)->order('convert(int,subcompanyno) asc')->select();

        return array('total'=>$total, 'data'=>is_array($data)?$data:array());
    }

    //获取子公司信息 通过no
    public function getSubCompanyByNo($subcompanyno=null)
    {
        if (!$subcompanyno) return false;

        $subcompany = M('subcompany')->where(array('subcompanyno'=>$subcompanyno))->find();

        return is_array($subcompany) ? $subcompany : array();
    }

    //获取子公司信息 通过ID
    public function getSubCompanyByID($subcompanyid=null)
    {
        if (!$subcompanyid) return false;

        $subcompany = $this->getSubCompany(null, $subcompanyid);

        return $subcompany['total'] ? array_shift($subcompany) : array();
    }

    //获取最大的subcompanyno
    public function getMaxSubcompanyno()
    {
        $subcompany = M('subcompany')->order('convert(int,subcompanyno) desc')->find();

        return is_array($subcompany)&&!empty($subcompany) ? $subcompany['subcompanyno'] : 0;
    }

    //保存子公司信息
    public function savesubcompany($subcompanyid=null, $data=array())
    {
        if (!is_array($data) || empty($data)) return false;

        if ($subcompanyid) {
            $return = M("subcompany")->where(array('subcompanyid'=>$subcompanyid))->save($data);
        } else {
            $return = M("subcompany")->add($data);
        }

        return $return;
    }

    //获取部门信息
    public function getDepartment($subcompanyid=null, $departmentid=null, $departmentname=null)
    {
        $where = array();
        if ($subcompanyid) $where['subcompanyid'] = is_array($subcompanyid) ? array('in', $subcompanyid) : $subcompanyid;
        if ($departmentid) $where['departmentid'] = is_array($departmentid) ? array('in', $departmentid) : $departmentid;
        if ($departmentname) $where['departmentname'] = array('like', '%'.$departmentname.'%');

        $total = M('department')->where($where)->count();
        $data = M('department')->alias('a')
              ->field('a.*, b.mtserverip, b.mtserverport, b.online, b.lastheartbeattime')
              ->join(' LEFT JOIN __MONITORSERVER__ b on a.mtserverid=b.mtserverid ')
              ->where($where)
              ->order('convert(int,departmentno) asc')
              ->select();

        return array('total'=>$total, 'data'=>is_array($data)?$data:array());
    }

    //获取部门信息 通过ID
    public function getDepartmentByID($departmentid=null)
    {
        if (!$departmentid) return false;

        $department = $this->getDepartment(null, $departmentid);

        return $department['total'] ? array_shift($department) : array();
    }

    //获取部门信息通过no
    public function getDepartmentByNO($departmentno=null)
    {
        if (!$departmentno) return false;

        $department = M('department')->where(array('departmentno'=>$departmentno))->find();

        return is_array($department) ? $department : array();
    }

    //获取最大的departmentno
    public function getMaxDepartmentno()
    {
        $department = M('department')->order('convert(int,departmentno) desc')->find();

        return is_array($department)&&!empty($department) ? $department['departmentno'] : 0;
    }

    //保存部门信息
    public function savedepartment($departmentid=null, $data=array())
    {
        if (!is_array($data) || empty($data)) return false;

        if ($departmentid) {
            $return = M("department")->where(array('departmentid'=>$departmentid))->save($data);
        } else {
            $return = M("department")->add($data);
        }

        return $return;
    }
}