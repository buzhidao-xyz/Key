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

        return (int)$userno;
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

        $usernos = array(0);
        foreach ($cabinetuser as $d) {
            $usernos[] = $d['userno'];
        }

        //获取员工信息
        $userlistdata = D('User')->getUser(null, null, $departmentno, $usernos);
        $total = $userlistdata['total'];
        $userlistdata = $userlistdata['data'];
        if (!is_array($userlistdata)||empty($userlistdata)) $this->apiReturn(0, '', array(
            'total' => 0,
            'userlist' => array(),
        ));

        //获取员工指纹信息
        $userfingerlist = D('Finger')->getUserFinger($departmentno, $usernos);
        $userfingerlist = $userfingerlist['data'];

        //获取员工关联的钥匙信息
        $userkeylist = D('Access')->getUserkey($departmentno, $cabinetno, $usernos);

        //解析数据
        $userlist = array();
        foreach ($userlistdata as $d) {
            //指纹信息
            $finger = array();
            if (isset($userfingerlist[$departmentno.'-'.$d['userno']])) {
                $userfinger = $userfingerlist[$departmentno.'-'.$d['userno']];
                foreach ($userfinger as $fd) {
                    $finger[] = array(
                        'index'  => (int)$fd['fingerindex'],
                        'flag'   => (int)$fd['flag'],
                        'data'   => $fd['fingerdata'],
                        'length' => (int)$fd['length'],
                    );
                }
            }

            //钥匙信息
            $keylocknos = isset($userkeylist[$departmentno.'-'.$cabinetno.'-'.$d['userno']]) ? $userkeylist[$departmentno.'-'.$cabinetno.'-'.$d['userno']] : array();

            $userlist[] = array(
                'userno'     => (int)$d['userno'],
                'username'   => $d['username'],
                'usercodeno' => $d['codeno'],
                'usercardno' => $d['cardno'],
                'status'     => (int)$d['status'],
                'finger'     => $finger,
                'keylocknos' => $keylocknos,
            );
        }
        
        $this->apiReturn(0, '', array(
            'total' => (int)$total,
            'userlist' => $userlist,
        ));
    }
}