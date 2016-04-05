<?php
/**
 * 实时监控逻辑
 * imbzd
 * 2016-04-04
 */
namespace Admin\Controller;

class MonitorController extends CommonController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index(){}

    //实时监控
    public function rtmonitor()
    {
        $departmentno = $this->_getDepartmentno();
        if (!$departmentno) exit;

        $cabinetno = $this->_getCabinetno();

        //获取部门信息
        $department = array();
        foreach ($this->company['subcompany'] as $subcompany) {
            if (isset($subcompany['department'][$departmentno])) $department = $subcompany['department'][$departmentno];
        }
        if (!is_array($department) || empty($department)) exit;

        //获取钥匙柜信息
        $cabinetList = D('Cabinet')->getCabinet(null, null, $departmentno);
        $cabinetTotal = $cabinetList['total'];
        $cabinetList = $cabinetList['data'];
        $this->assign('cabinetList', $cabinetList);

        //当前cabinet
        $cabinet = array();
        if ($cabinetno) {
            foreach ($cabinetList as $cbt) {
                if ($cbt['cabinetno'] == $cabinetno) $cabinet = $cbt;
            }
        } else {
            $cabinet = current($cabinetList);
        }
        $cabinetno = $cabinet['cabinetno'];
        $this->assign('cabinetno', $cabinetno);

        //获取钥匙列表
        $keyList = D('Key')->getKey(null, null, null, $departmentno, $cabinetno);
        $keyTotal = $keyList['total'];
        $keyList = $keyList['data'];

        //获取钥匙使用日志
        $ymd = date('Y-m-d', TIMESTAMP);
        $begintime = $ymd.' 00:00:00';
        $endtime = $ymd.' 23:59:59';
        $keyuseLogList = D('Log')->getKeyuseLog(null, $departmentno, $cabinetno, null, null, null, null, $begintime, $endtime, 0, 99);
        $keyuseLogTotal = $keyuseLogList['total'];
        $keyuseLogList = $keyuseLogList['data'];

        //获取钥匙柜开关门日志
        

        // dump($keyList);exit;
        $this->assign('keyList', $keyList);
        $this->assign('keyuseLogList', $keyuseLogList);
        $this->assign('department', $department);
        $this->assign('cabinet', $cabinet);
        $this->display();
    }
}