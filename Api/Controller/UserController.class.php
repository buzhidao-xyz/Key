<?php
/**
 * 员工逻辑
 * imbzd
 * 2016-04-04
 */
namespace Api\Controller;

class UserController extends CommonController
{
    public function __construct()
    {
        parent::__construct();
    }

    //获取userno
    private function _getUserno()
    {
        $userno = mRequest('userno');

        return (int)$userno;
    }

    //获取username
    private function _getUsername()
    {
        $username = mRequest('username');

        return $username;
    }

    //获取usercodeno
    private function _getUsercodeno()
    {
        $usercodeno = mRequest('usercodeno');

        return $usercodeno;
    }

    public function index(){}

    //获取员工信息
    public function getuser()
    {
        $departmentno = $this->_getDepartmentno();
        if (!$departmentno) $this->apiReturn(1, '未知departmentno！');

        $userno = $this->_getUserno();
        $username = $this->_getUsername();
        $usercodeno = $this->_getUsercodeno();

        $userlistdata = D('User')->getUser(null, $username, $departmentno, $userno, $usercodeno);
        $total = $userlistdata['total'];
        $userlistdata = $userlistdata['data'];

        $userlist = array();
        foreach ($userlistdata as $d) {
            $userlist[] = array(
                'userno'     => (int)$d['userno'],
                'username'   => $d['username'],
                'usercodeno' => $d['codeno'],
                'usercardno' => $d['cardno'],
                'status'     => (int)$d['status'],
            );
        }

        $this->apiReturn(0, '', array(
            'total' => (int)$total,
            'userlist' => $userlist
        ));
    }
}