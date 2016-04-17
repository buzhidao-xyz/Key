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

    //获取实时监控数据
    private function _rtmonitordata($departmentno=null, $cabinetno=null)
    {
        if (!$departmentno) return false;

        //获取钥匙柜列表
        $cabinetList = D('Cabinet')->getCabinet(null, null, $departmentno);
        $cabinetTotal = $cabinetList['total'];
        $cabinetList = $cabinetList['data'];
        $this->assign('cabinetList', $cabinetList);

        //当前钥匙柜信息
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

        //钥匙数量统计-总数量
        $keynumlist = D('Key')->getKeynumByDepartmentCabinet($departmentno, $cabinetno);
        $cabinet['keynum'] = isset($keynumlist[$cabinet['departmentno'].'-'.$cabinet['cabinetno']]) ? $keynumlist[$cabinet['departmentno'].'-'.$cabinet['cabinetno']] : 0;
        //钥匙数量统计-在位数量
        $keyinnumlist = D('Key')->getKeynumByDepartmentCabinet($departmentno, $cabinetno, 1);
        $cabinet['keyinnum'] = isset($keyinnumlist[$cabinet['departmentno'].'-'.$cabinet['cabinetno']]) ? $keyinnumlist[$cabinet['departmentno'].'-'.$cabinet['cabinetno']] : 0;
        //钥匙数量统计-离位数量
        $keyoutnumlist = D('Key')->getKeynumByDepartmentCabinet($departmentno, $cabinetno, 0);
        $cabinet['keyoutnum'] = isset($keyoutnumlist[$cabinet['departmentno'].'-'.$cabinet['cabinetno']]) ? $keyoutnumlist[$cabinet['departmentno'].'-'.$cabinet['cabinetno']] : 0;
        //钥匙数量统计-错位数量
        $keyerrnumlist = D('Key')->getKeynumByDepartmentCabinet($departmentno, $cabinetno, 2);
        $cabinet['keyerrnum'] = isset($keyerrnumlist[$cabinet['departmentno'].'-'.$cabinet['cabinetno']]) ? $keyerrnumlist[$cabinet['departmentno'].'-'.$cabinet['cabinetno']] : 0;

        //获取钥匙列表
        $keyLists = D('Key')->getKey(null, null, null, $departmentno, $cabinetno);
        $keyTotal = $keyLists['total'];
        $keyLists = $keyLists['data'];
        $keyList = array();
        foreach ($keyLists as $key) {
            $keyList[$key['keypos']] = array_merge($key, array(
                'upflag' => 0,
                'keymonitorname' => $key['keyname']
            ));
        }
        foreach ($keyList as $keypos=>$key) {
            if ($key['keystatus'] == 2) {
                if (!$key['upflag']) $keyList[$key['keypos']]['keystatus'] = 0;

                $keyList[$key['keyposcurrent']]['keymonitorname'] = $key['keyname'];
                $keyList[$key['keyposcurrent']]['keystatus'] = 2;
                $keyList[$key['keyposcurrent']]['upflag'] = 1;
            }
        }
        $this->assign('keyList', $keyList);

        //获取钥匙使用日志
        $ymd = date('Y-m-d', TIMESTAMP);
        $begintime = $ymd.' 00:00:00';
        $endtime = $ymd.' 23:59:59';
        $keyuseLogList = D('Log')->getKeyuseLog(null, $departmentno, $cabinetno, null, null, null, null, null, $begintime, $endtime, 0, 99);
        $keyuseLogTotal = $keyuseLogList['total'];
        $keyuseLogList = $keyuseLogList['data'];
        $this->assign('keyuseLogList', $keyuseLogList);

        //获取钥匙柜开关门日志
        $ymd = date('Y-m-d', TIMESTAMP);
        $begintime = $ymd.' 00:00:00';
        $endtime = $ymd.' 23:59:59';
        $cabinetdoorLogList = D('Log')->getCabinetdoorLog(null, $departmentno, $cabinetno, null, null, null, null, $begintime, $endtime, 0, 99);
        $cabinetdoorLogTotal = $cabinetdoorLogList['total'];
        $cabinetdoorLogList = $cabinetdoorLogList['data'];
        $this->assign('cabinetdoorLogList', $cabinetdoorLogList);

        $this->assign('cabinet', $cabinet);

        return array(
            'cabinet' => $cabinet,
            'keyList' => $keyList,
            'keyuseLogList' => $keyuseLogList,
            'cabinetdoorLogList' => $cabinetdoorLogList,
        );
    }

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
        $this->assign('department', $department);

        //获取实时监控数据
        $this->_rtmonitordata($departmentno, $cabinetno);

        $this->display();
    }

    //AJAX实时监控
    public function rtmonitorRun()
    {
        $departmentno = $this->_getDepartmentno();
        if (!$departmentno) $this->ajaxReturn(1, '未知departmentno！');

        $cabinetno = $this->_getCabinetno();
        if (!$cabinetno) $this->ajaxReturn(1, '未知cabinetno！');

        //获取实时监控数据
        $rtmonitordata = $this->_rtmonitordata($departmentno, $cabinetno);

        //钥匙柜信息
        $cabinethtml = $this->fetch('Monitor/rtmonitor_cabinet');
        //钥匙列表
        $keyhtml = $this->fetch('Monitor/rtmonitor_key');
        //钥匙使用日志
        $keyuseloghtml = $this->fetch('Monitor/rtmonitor_keyuselog');
        //钥匙使用日志
        $cabinetdoorloghtml = $this->fetch('Monitor/rtmonitor_cabinetdoorlog');

        $this->ajaxReturn(0, '', array(
            'cabinethtml' => $cabinethtml,
            'keyhtml' => $keyhtml,
            'keyuseloghtml' => $keyuseloghtml,
            'cabinetdoorloghtml' => $cabinetdoorloghtml,
        ));
    }

    //车辆监控
    public function car()
    {
        $departmentno = $this->_getDepartmentno();
        if (!$departmentno) $this->ajaxReturn(1, '未知departmentno！');

        $cabinetno = $this->_getCabinetno();
        if (!$cabinetno) $this->ajaxReturn(1, '未知cabinetno！');

        //获取部门信息
        $department = array();
        foreach ($this->company['subcompany'] as $subcompany) {
            if (isset($subcompany['department'][$departmentno])) $department = $subcompany['department'][$departmentno];
        }
        if (!is_array($department) || empty($department)) exit;
        $this->assign('department', $department);

        //获取钥匙柜列表
        $cabinetList = D('Cabinet')->getCabinet(null, null, $departmentno);
        $cabinetTotal = $cabinetList['total'];
        $cabinetList = $cabinetList['data'];
        $this->assign('cabinetList', $cabinetList);

        //当前钥匙柜信息
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

        //获取车辆信息
        $carlist = D('Key')->getKey(null, null, null, $departmentno, $cabinetno, null, null, null, 0, 9999, 1);
        $cartotal = $carlist['total'];
        $carlist = $carlist['data'];

        //车辆数量
        $cabinet['cartotal'] = $cartotal;

        //解析车辆信息数据
        $insurecarlist = array();
        $repaircarlist = array();
        if (is_array($carlist)) {
            foreach ($carlist as $car) {
                if ($car['insureexpiretime'] > 0) {
                    //判断保险信息是否到期
                    $insureexpiretime_timestamp = strtotime($car['insureexpiretime']);
                    $insuretotime_timestamp = $insureexpiretime_timestamp - TIMESTAMP;
                    if ($insuretotime_timestamp <= $this->_car_insure_time*24*3600) {
                        $insuredaycaption = $insuretotime_timestamp>0 ? '' : '【已超期】';
                        $insuretodays = Sec2Days(abs($insuretotime_timestamp));
                        $insurecarlist[] = array_merge($car, array(
                            'insuretotime'     => $insuretotime_timestamp,
                            'insuretodays'     => $insuretodays,
                            'insuredaycaption' => $insuredaycaption,
                        ));
                    }
                }

                //判断保养信息是否到期
                //行驶公里
                if ($car['repairkilometer'] > 0) {
                    $repairtokilometer = (int)$car['repairkilometer']-(int)$car['currentkilometer'];
                    $repaircaption = $repairtokilometer>0 ? '' : '【已超过】';
                    $car['repairtokilometer'] = abs($repairtokilometer);
                    $car['repaircaption'] = $repaircaption;
                    if ($repairtokilometer <= $this->_car_repair_kilometer) {
                        $repaircarlist[$car['carid']] = $car;
                    }
                }
                //保养时限
                if ($car['lastrepairtime'] && $car['repairperiodtime']) {
                    $lastrepairtime_timestamp = strtotime($car['lastrepairtime']);
                    $nextrepairtime_timestamp = strtotime($car['lastrepairtime'].' + '.$car['repairperiodtime'].' month');
                    $repairtotime_timestamp = $nextrepairtime_timestamp - TIMESTAMP;
                    if ($repairtotime_timestamp <= $this->_car_repair_time*24*3600) {
                        $repairdaycaption = $repairtotime_timestamp>0 ? '' : '【已超期】';
                        $repairtodays = Sec2Days(abs($repairtotime_timestamp));
                        $car['repairtotime'] = $repairtotime_timestamp;
                        $car['repairtodays'] = $repairtodays;
                        $car['repairdaycaption'] = $repairdaycaption;
                        $car['nextrepairtime'] = date('Y-m-d', $nextrepairtime_timestamp);
                        $repaircarlist[$car['carid']] = $car;
                    }
                }
            }
        }

        $this->assign('carlist', $carlist);
        $this->assign('insurecarlist', $insurecarlist);
        $this->assign('repaircarlist', $repaircarlist);

        $this->assign('cabinet', $cabinet);
        $this->display();
    }
}