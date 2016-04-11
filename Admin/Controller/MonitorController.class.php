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
        $keyList = D('Key')->getKey(null, null, null, $departmentno, $cabinetno);
        $keyTotal = $keyList['total'];
        $keyList = $keyList['data'];
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
}