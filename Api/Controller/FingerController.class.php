<?php
/**
 * 指纹逻辑
 * imbzd
 * 2016-04-04
 */
namespace Api\Controller;

class KeyController extends CommonController
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

    //录入员工指纹信息
    public function newfinger()
    {
        $departmentno = $this->_getDepartmentno();
        if (!$departmentno) $this->apiReturn(1, '未知departmentno！');

        $userno = $this->_getUserno();

        //获取指纹信息
        $finger = mRequest('finger', false);
        $finger = json_decode($finger, true);
        if (!is_array($finger) || empty($finger)) $this->apiReturn(1, '未知finger！');

        //获取员工信息
        $userinfo = D('User')->getUserByUserno($departmentno, $userno);

        $data = array();
        foreach ($finger as $d) {
            $userfingerid = guid();
            $data[] = array(
                'userfingerid' => $userfingerid,
                'departmentno' => $departmentno,
                'userno'       => $userno,
                'fingerindex'  => $d['index'],
                'flag'         => $d['flag'],
                'fingerdata'   => $d['data'],
                'length'       => $d['length'],
                'createtime'   => mkDateTime(),
                'updatetime'   => mkDateTime(),
            );
        }
        $result = D('Finger')->saveUserFinger($departmentno, $userno, $data);
        if ($result) {
            $this->apiReturn(0, '录入成功！', array(
                'status' => 1
            ));
        } else {
            $this->apiReturn(1, '录入失败！', array(
                'status' => 0
            ));
        }
    }
}