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
    public function getUser()
    {

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