<?php
/**
 * 钥匙逻辑
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

    //获取keylockno
    private function _getKeylockno()
    {
        $keylockno = mRequest('keylockno');

        return $keylockno;
    }

    //获取keycardid
    private function _getKeycardid()
    {
        $keycardid = mRequest('keycardid');

        return $keycardid;
    }

    //获取actiontype
    private function _getActiontype()
    {
        $actiontype = mRequest('actiontype');

        return $actiontype;
    }

    public function index(){}

    //获取钥匙信息
    public function getkey()
    {
        $departmentno = $this->_getDepartmentno();
        if (!$departmentno) $this->apiReturn(1, '未知departmentno！');
        $cabinetno = $this->_getCabinetno();
        if (!$cabinetno) $this->apiReturn(1, '未知cabinetno！');

        $keylockno = $this->_getKeylockno();

        $keyLists = D('Key')->getKey(null, null, null, $departmentno, $cabinetno, null, $keylockno);
        $keyListTotal = $keyLists['total'];
        $keyLists = $keyLists['data'];

        //解析数据
        $keylist = array();
        foreach ($keyLists as $d) {
            $keylist[] = array(
                'keyno'            => (int)$d['keyno'],
                'keyname'          => $d['keyname'],
                'keyshowname'      => $d['keyshowname'],
                'keylockno'        => (int)$d['keypos'],
                'keyrfid'          => $d['keyrfid'],
                'keystatus'        => (int)$d['keystatus'],
                'usetimeflag'      => (int)$d['usetimeflag'],
                'usetime'          => $d['usetime'],
                'returntimeflag'   => (int)$d['returntimeflag'],
                'returntime'       => (int)$d['returntime'],
                'keylocknocurrent' => (int)$d['keyposcurrent'],
            );
        }

        $this->apiReturn(0, '', array(
            'total' => (int)$keyListTotal,
            'keylist' => $keylist
        ));
    }

    //更新钥匙状态
    public function synckeystatus()
    {
        $departmentno = $this->_getDepartmentno();
        if (!$departmentno) $this->apiReturn(1, '未知departmentno！');
        $cabinetno = $this->_getCabinetno();
        if (!$cabinetno) $this->apiReturn(1, '未知cabinetno！');

        $keylockno = $this->_getKeylockno();
        $keycardid = $this->_getKeycardid();
        $actiontype = $this->_getActiontype();

        $data = array(
            'keystatus' => $actiontype,
            'keyposcurrent' => $keylockno,
        );

        $result = false;
        if ($keycardid) {
            $result = M('keys')->where(array('keyefid'=>$keycardid))->save($data);
        } else {
            $result = M('keys')->where(array('departmentno'=>$departmentno,'cabinetno'=>$cabinetno,'keypos'=>$keylockno))->save($data);
        }
        if ($result) {
            $this->apiReturn(0, '更新成功！', array(
                'status' => 1
            ));
        } else {
            $this->apiReturn(1, '新增失败！', array(
                'status' => 0
            ));
        }
    }
}