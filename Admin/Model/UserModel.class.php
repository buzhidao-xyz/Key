<?php
/**
 * 员工模型
 * buzhidao
 * 2016-03-27
 */

namespace Admin\Model;

class UserModel extends CommonModel
{
    public function __construct()
    {
        parent::__construct();
    }

    //获取员工信息
    public function getUser($userid=null, $username=null, $departmentno=null, $userno=null, $codeno=null, $cardno=null, $start=0, $length=9999)
    {
        $where = array(
            'status' => 1
        );
        if ($userid) $where['userid'] = is_array($userid) ? array('in', $userid) : $userid;
        if ($username) $where['_complex'] = array(
            '_logic'   => 'or',
            'username' => array('like', '%'.$username.'%'),
            'codeno'   => array('like', '%'.$username.'%'),
        );
        if ($departmentno) $where['a.departmentno'] = $departmentno;
        if ($userno) $where['userno'] = $userno;
        if ($codeno) $where['codeno'] = $codeno;
        if ($cardno) $where['cardno'] = $cardno;

        $total = M('user')->alias('a')->where($where)->count();
        $data = M('user')->alias('a')
                         ->field('a.*, b.departmentname')
                         ->join(' __DEPARTMENT__ b on a.departmentno=b.departmentno ')
                         ->where($where)
                         ->order('convert(int, anyphp.departmentno) asc, username asc, convert(int, userno) asc')
                         ->limit($start, $length)
                         ->select();

        return array('total'=>$total, 'data'=>is_array($data)?$data:array());
    }

    //获取员工信息 By departmentno+userno
    public function getUserByUserno($departmentno=null, $userno=null)
    {
        if (!$departmentno || !$userno) return false;

        $userinfo = $this->getUser(null, null, $departmentno, $userno, null, null);

        return $userinfo['total'] ? array_shift($userinfo['data']) : array();
    }

    //获取员工信息 By userid
    public function getUserByID($userid=null)
    {
        if (!$userid) return false;

        $userinfo = $this->getUser($userid);

        return $userinfo['total'] ? array_shift($userinfo['data']) : array();
    }

    //查询员工编号是否已存在
    public function ckUserCodenoExists($userid=null, $codeno=null)
    {
        if (!$codeno) return false;

        $where = array(
            'status' => 1,
            'codeno' => $codeno
        );
        if ($userid) $where['userid']=array('neq', $userid);

        $n = M('user')->where($where)->count();

        return $n ? true : false;
    }

    //保存员工
    public function saveuser($userid=null, $data=array())
    {
        if (!is_array($data) || empty($data)) return false;

        if ($userid) {
            $return = M("user")->where(array('userid'=>$userid))->save($data);
        } else {
            $return = M("user")->add($data);
        }

        return $return;
    }
}