<?php
/**
 * 员工模型
 * buzhidao
 * 2016-04-04
 */

namespace Api\Model;

class UserModel extends CommonModel
{
    public function __construct()
    {
        parent::__construct();
    }

    //获取员工信息
    public function getUser($userid=null, $username=null, $departmentno=null, $userno=null, $codeno=null, $cardno=null, $start=0, $length=9999)
    {
        $where = array();
        if ($userid) $where['userid'] = is_array($userid) ? array('in', $userid) : $userid;
        if ($username) $where['username'] = array('like', '%'.$username.'%');
        if ($departmentno) $where['departmentno'] = is_array($departmentno) ? array('in', $departmentno) : $departmentno;
        if ($userno) $where['userno'] = is_array($userno) ? array('in', $userno) : $userno;
        if ($codeno) $where['codeno'] = $codeno;
        if ($cardno) $where['cardno'] = is_array($cardno) ? array('in', $cardno) : $cardno;

        $total = M('user')->where($where)->count();
        $data = M('user')->where($where)->order('userno asc')->limit($start, $length)->select();

        return array('total'=>$total, 'data'=>is_array($data)?$data:array());
    }

    //获取员工信息 By departmentno+userno
    public function getUserByUserno($departmentno=null, $userno=null)
    {
        if (!$departmentno || !$userno) return false;

        $userinfo = $this->getUser(null, null, $departmentno, $userno);

        return $userinfo['total'] ? array_shift($userinfo['data']) : array();
    }
}