<?php
/**
 * 管理员数据模型
 * 2015-07-12
 * buzhidao
 */
namespace Admin\Model;

class ManagerModel extends CommonModel
{
    public function __construct()
    {
        parent::__construct();
    }

    //加密管理员密码
    public function passwordEncrypt($password=null, $mkey=null)
    {
        return md5(md5($password).$mkey);
    }

    //获取管理员
    public function getManager($managerid=null, $account=null, $username=null, $start=0, $length=9999)
    {
        if ($start==0 && $length==0) return array();
        
        $where = array();
        if ($managerid) $where['a.managerid'] = $managerid;
        if ($account) $where['a.account'] = array('like', '%'.$account.'%');

        //如果有警员信息关键字
        if ($username) {
            $userlist = D('User')->getUser(null, $username);
        }

        //查询数据总量
        $total  = M('manager')->alias("a")
                              ->field('a.managerid')
                              ->where($where)
                              ->count('distinct a.managerid');

        //查询符合条件的子查询
        $SubQuery = M('manager')->alias("a")
                                ->distinct(true)
                                ->field('a.*')
                                ->where($where)
                                ->order('super desc, managerid asc')
                                ->limit($start, $length)
                                ->buildSql();
        //查询数据
        $result = M('manager')->alias('m')
                              ->field('m.*, b.rolename, b.rolerank')
                              ->join(' inner join '.$SubQuery.' sub on sub.managerid=m.managerid ')
                              ->join(' LEFT JOIN __ROLE__ b on m.roleid=b.roleid ')
                              ->select();
        $userids = array('00000000-0000-0000-0000-000000000000');
        $data = array();
        if (is_array($result)&&!empty($result)) {
            foreach ($result as $d) {
                if ($d['userid'] && $d['userid']!='0') $userids[] = $d['userid'];

                $data[$d['managerid']] = $d;
            }
        }

        //获取关联员工信息
        $userlist = M('user')->where(array('userid'=>array('in', $userids)))->select();
        if (is_array($userlist)&&!empty($userlist)) {
            foreach ($data as $k=>$d) {
                foreach ($userlist as $u) {
                    if ($d['userid']==$u['userid']) $data[$k]['username'] = $u['username'];
                }
            }
        }

        return array('total'=>$total, 'data'=>$data);
    }

    //获取管理员通过ID
    public function getManagerByID($managerid=null)
    {
        if (!$managerid) return false;
        $manager = $this->getManager($managerid);

        return $manager['total']>0 ? array_pop($manager['data']) : array();
    }

    //获取管理员通过account
    public function getManagerByAccount($account=null)
    {
        if (!$account) return false;
        $manager = $this->getManager(null,$account);
        if ($manager['total'] > 0) {
            foreach ($manager['data'] as $d) {
                if ($d['account'] == $account) return $d;
            }
        }

        return array();
    }

    //启用、禁用管理员
    public function enableManager($managerid=null, $status=1)
    {
        if (!$managerid || !in_array($status, array(0,1))) return false;

        $result = M('manager')->where(array('managerid'=>$managerid))->save(array('status'=>$status));

        return $result ? true : false;
    }

    //新增/修改管理员信息
    public function saveManager($managerid=null, $data=array(), $multi=false)
    {
        if (!is_array($data) || empty($data)) return false;

        if ($managerid) {
            $result = M('manager')->where(array('managerid'=>$managerid))->save($data);
        } else {
            $result = $multi ? M('manager')->addAll($data) : M('manager')->add($data);
        }

        return $result;
    }

    //新增管理员登录日志
    public function saveManagerLoginLog($data=array())
    {
        if (!is_array($data) || empty($data)) return false;

        $result = M('manager_loginlog')->add($data);

        return $result;
    }

    //获取管理员角色
    public function getManagerRole($managerid=null)
    {
        if (!$managerid) return false;

        $result = M('manager_role')->where(array('managerid'=>$managerid))->select();

        return is_array($result) ? $result : array();
    }

    //获取管理员管理的公司-子公司-部门
    public function getManagerDepartment($managerid=null)
    {
        if (!$managerid) return false;

        $result = M('manager_department')->where(array('managerid'=>$managerid))->select();
        $data = array();
        if (is_array($result)&&!empty($result)) {
            foreach ($result as $d) {
                $data[] = $d['departmentno'];
            }
        }

        return $data;
    }
}