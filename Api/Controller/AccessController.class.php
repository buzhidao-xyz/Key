<?php
/**
 * 权限逻辑
 * imbzd
 * 2016-04-04
 */
namespace Api\Controller;

class AccessController extends CommonController
{
    public function __construct()
    {
        parent::__construct();
    }

    //获取userno
    private function _getUserno()
    {
        $userno = mRequest('userno');

        return $userno;
    }

    public function index(){}

    //获取钥匙柜关联的员工信息
    public function getcabinetuser()
    {
        $departmentno = $this->_getDepartmentno();
        if (!$departmentno) $this->apiReturn(1, '未知departmentno！');
        $cabinetno = $this->_getCabinetno();
        if (!$cabinetno) $this->apiReturn(1, '未知cabinetno！');

        $userno = $this->_getUserno();

        //获取钥匙柜关联的员工信息
        $cabinetuser = D('Access')->getCabinetAccess($departmentno, $cabinetno, $userno);
        $total = $cabinetuser['total'];
        $cabinetuser = $cabinetuser['data'];

        $usernos = array();
        foreach ($cabinetuser as $d) {
            $usernos[] = $d['userno'];
        }

        //获取员工信息
        $userlistdata = D('User')->getUser(null, null, $departmentno, $usernos);
        $total = $userlistdata['total'];
        $userlistdata = $userlistdata['data'];

        //获取员工指纹信息
        $userfingerlist = D('Finger')->getUserFinger($departmentno, $usernos);
        $userfingerlist = $userfingerlist['data'];

        //解析数据
        $userlist = array();
        foreach ($userlistdata as $d) {
            $finger = array(
                
            );

            $userlist[] = array(
                'userno'     => (int)$d['userno'],
                'username'   => $d['username'],
                'usercodeno' => $d['codeno'],
                'usercardno' => $d['cardno'],
                'status'     => (int)$d['status'],
                'finger'     => $finger,
            );
        }
        
        dump($userfingerlist);exit;
        $this->apiReturn(0, '', array(
            'total' => (int)$total,
            'userlist' => $userlist,
        ));
    }
}