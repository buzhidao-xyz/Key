<?php
/**
 * Admin Module Main Enter
 * imbzd
 * 2015-05-11
 */
namespace Admin\Controller;

class IndexController extends CommonController
{
    //默认选中组菜单id=1(实时监控)
    private $_default_groupid = 1;
    //默认选中节点菜单id=1(控制面板)
    private $_default_nodeid = 1;
    
    public function __construct()
    {
        parent::__construct();
    }

    //系统主框架页面
    public function index()
    {
        //节点菜单
        $nodemenu = isset($this->managerinfo['access'][$this->_default_groupid]) ? $this->managerinfo['access'][$this->_default_groupid]['nodelist'] : array();
        $this->assign('nodemenu', $nodemenu);

        $this->assign('groupid', $this->_default_groupid);
        //默认组菜单
        $this->assign('default_groupid', $this->_default_groupid);
        //默认节点菜单
        $this->assign('default_nodeid', $this->_default_nodeid);
        $this->display();
    }

    //系统主界面-控制面板
    public function dashboard()
    {
        //公司列表
        $subcompanylist = $this->company['subcompany'];
        $this->assign('subcompanylist', $subcompanylist);
        //当前subcompany
        $subcompany = array();
        $subcompanyno = $this->_getSubcompanyno();
        if ($subcompanyno) {
            $subcompany = $subcompanylist[$subcompanyno];
        } else {
            $subcompany = current($subcompanylist);
        }
        $subcompanyno = $subcompany['subcompanyno'];
        $this->assign('subcompanyno', $subcompanyno);

        //获取子公司的departmentnos
        $departmentnos = array();
        foreach ($subcompany['department'] as $dpm) {
            $departmentnos[] = $dpm['departmentno'];
        }

        //获取部门的钥匙柜信息
        $cabinetlist = D('Cabinet')->getCabinet(null, null, $departmentnos);
        $cabinetlist = $cabinetlist['data'];

        //获取钥匙柜的统计信息
        
        //钥匙数量统计-总数量
        $keynumlist = D('Key')->getKeynumByDepartmentCabinet($departmentnos);
        //钥匙数量统计-在位数量
        $keyinnumlist = D('Key')->getKeynumByDepartmentCabinet($departmentnos, null, 1);
        //钥匙数量统计-离位数量
        $keyoutnumlist = D('Key')->getKeynumByDepartmentCabinet($departmentnos, null, 0);
        //钥匙数量统计-错位数量
        $keyerrnumlist = D('Key')->getKeynumByDepartmentCabinet($departmentnos, null, 2);

        $begintime = date('Y-m-d', TIMESTAMP).' 00:00:00';
        $endtime = date('Y-m-d', TIMESTAMP).' 23:59:59';
        //钥匙柜开关门日志数量-今天
        $cabinetdoorlognumlist = D('Log')->getCabinetdoorLognumByDepartmentCabinet($departmentnos, null, null, null, $begintime, $endtime);
        //钥匙使用日志数量-今天
        $keyuselognumlist = D('Log')->getKeyuseLognumByDepartmentCabinet($departmentnos, null, null, null, $begintime, $endtime);

        //最近七天钥匙使用日志数量
        $logdays = array();
        $keyuselognumlistdays = array();
        $TIMESTAMP = TIMESTAMP-6*24*3600;
        for ($i=0; $i<=6; $i++) {
            $day = date('Y-m-d', $TIMESTAMP+$i*24*3600);
            $logdays[] = $day;
            $begintime = $day.' 00:00:00';
            $endtime = $day.' 23:59:59';
            // 领取日志
            $keyuselognumlistdays['out'][$day] = D('Log')->getKeyuseLognumByDepartmentCabinet($departmentnos, null, 0, null, $begintime, $endtime);
            // 归还日志
            $keyuselognumlistdays['in'][$day] = D('Log')->getKeyuseLognumByDepartmentCabinet($departmentnos, null, 1, null, $begintime, $endtime);
        }

        //遍历钥匙柜 解析数据
        $dpmcabinetlist = array();
        if (is_array($cabinetlist)&&!empty($cabinetlist)) {
            foreach ($cabinetlist as $cbt) {
                $cbt['keynum'] = isset($keynumlist[$cbt['departmentno'].'-'.$cbt['cabinetno']]) ? (int)$keynumlist[$cbt['departmentno'].'-'.$cbt['cabinetno']] : 0;
                $cbt['keyinnum'] = isset($keyinnumlist[$cbt['departmentno'].'-'.$cbt['cabinetno']]) ? (int)$keyinnumlist[$cbt['departmentno'].'-'.$cbt['cabinetno']] : 0;
                $cbt['keyoutnum'] = isset($keyoutnumlist[$cbt['departmentno'].'-'.$cbt['cabinetno']]) ? (int)$keyoutnumlist[$cbt['departmentno'].'-'.$cbt['cabinetno']] : 0;
                $cbt['keyerrnum'] = isset($keyerrnumlist[$cbt['departmentno'].'-'.$cbt['cabinetno']]) ? (int)$keyerrnumlist[$cbt['departmentno'].'-'.$cbt['cabinetno']] : 0;

                //今天钥匙柜开关门日志数量
                $cbt['cabinetdoorlognum'] = isset($cabinetdoorlognumlist[$cbt['departmentno'].'-'.$cbt['cabinetno']]) ? (int)$cabinetdoorlognumlist[$cbt['departmentno'].'-'.$cbt['cabinetno']] : 0;
                //今天钥匙使用日志数量
                $cbt['keyuselognum'] = isset($keyuselognumlist[$cbt['departmentno'].'-'.$cbt['cabinetno']]) ? (int)$keyuselognumlist[$cbt['departmentno'].'-'.$cbt['cabinetno']] : 0;

                //最近七天钥匙领取日志数量
                foreach ($keyuselognumlistdays['out'] as $day=>$kd) {
                    $cbt['keyoutlognum'][$day] = isset($kd[$cbt['departmentno'].'-'.$cbt['cabinetno']]) ? (int)$kd[$cbt['departmentno'].'-'.$cbt['cabinetno']] : 0;
                }
                //最近七天钥匙领取日志数量
                foreach ($keyuselognumlistdays['in'] as $day=>$kd) {
                    $cbt['keyinlognum'][$day] = isset($kd[$cbt['departmentno'].'-'.$cbt['cabinetno']]) ? (int)$kd[$cbt['departmentno'].'-'.$cbt['cabinetno']] : 0;
                }

                $dpmcabinetlist[$cbt['departmentno']][] = $cbt;
            }
        }

        //获取部门统计信息
        foreach ($subcompany['department'] as $k=>$dpm) {
            $dpm['cabinetlist'] = isset($dpmcabinetlist[$dpm['departmentno']]) ? $dpmcabinetlist[$dpm['departmentno']] : array();

            //遍历钥匙柜信息 解析日志数量
            $dpm = array_merge($dpm, array(
                'keyoutlognum' => array(),
                'keyinlognum' => array()
            ));
            foreach ($logdays as $day) {
                $dpm['keyoutlognum'][$day] = 0;
                $dpm['keyinlognum'][$day] = 0;
                if (is_array($dpm['cabinetlist'])&&!empty($dpm['cabinetlist'])) {
                    foreach ($dpm['cabinetlist'] as $cbt) {
                        $dpm['keyoutlognum'][$day] += $cbt['keyoutlognum'][$day];
                        $dpm['keyinlognum'][$day] += $cbt['keyinlognum'][$day];
                    }
                }
            }

            $subcompany['department'][$k] = $dpm;
        }

        // dump($subcompany);exit;
        $this->assign('logdays', $logdays);
        $this->assign('subcompany', $subcompany);
        $this->display();
    }
}