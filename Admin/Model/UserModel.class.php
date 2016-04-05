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
        $where = array();
        if ($userid) $where['userid'] = is_array($userid) ? array('in', $userid) : $userid;
        if ($username) $where['username'] = array('like', '%'.$username.'%');
        if ($departmentno) $where['departmentno'] = $departmentno;
        if ($userno) $where['userno'] = $userno;
        if ($codeno) $where['codeno'] = $codeno;
        if ($cardno) $where['cardno'] = $cardno;

        $total = M('user')->where($where)->count();
        $data = M('user')->where($where)->order('userno asc')->limit($start, $length)->select();

        return array('total'=>$total, 'data'=>is_array($data)?$data:array());
    }

    //获取员工信息 By departmentno+userno
    public function getUserByUserno($departmentno=null, $userno=null)
    {
        if (!$departmentno || !$userno) return false;

        $userinfo = $this->getUser(null, null, $departmentno, $userno, null, null);

        return $userinfo['total'] ? array_shift($userinfo) : array();
    }

    //查询员工编号是否已存在
    public function ckUserCodenoExists($userid=null, $codeno=null)
    {
        if (!$codeno) return false;

        $where = array(
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