<?php
/**
 * 日志模型
 * buzhidao
 * 2016-04-04
 */

namespace Admin\Model;

class LogModel extends CommonModel
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 获取钥匙柜开关门日志
     * @param int action 0:开门 1:关门
     * @param int alarm 0:正常开关门 1:异常开门 2:长时间未关门
     */
    public function getCabinetdoorLog($logid=null, $departmentno=null, $cabinetno=null, $userno=null, $username=null, $action=null, $alarm=null, $begintime=null, $endtime=null, $start=0, $length=9999)
    {
        $where = array();
        if ($logid) $where['logid'] = is_array($logid) ? array('in', $logid) : $logid;
        if ($departmentno) $where['a.departmentno'] = is_array($departmentno) ? array('in', $departmentno) : $departmentno;
        if ($cabinetno) $where['a.cabinetno'] = is_array($cabinetno) ? array('in', $cabinetno) : $cabinetno;
        if ($userno) $where['userno'] = $userno;
        if ($action!==null) $where['action'] = $action;
        if ($alarm!==null) $where['alarm'] = $alarm;
        if ($begintime!==null) $where['logtime'] = array('egt', $begintime);
        if ($endtime!==null) $where['logtime'] = array('elt', $endtime);
        if ($begintime!==null && $endtime!==null) $where['logtime'] = array('between', array($begintime, $endtime));

        $total = M('cabinetdoorlog')->alias('a')->where($where)->count();
        $data = M('cabinetdoorlog')->alias('a')
                                   ->field('a.*, c.cabinetname, d.departmentname, v.videofile, v.videosize, v.videolength')
                                   ->join(' __CABINET__ c on a.departmentno=c.departmentno and a.cabinetno=c.cabinetno ')
                                   ->join(' __DEPARTMENT__ d on a.departmentno=d.departmentno ')
                                   ->join(' LEFT JOIN __VIDEOLOG__ v on v.videologid=a.videologid ')
                                   ->where($where)
                                   ->order('logtime desc')
                                   ->limit($start, $length)
                                   ->select();

        return array('total'=>$total, 'data'=>is_array($data)?$data:array());
    }

    //获取钥匙柜开关门日志数量
    public function getCabinetdoorLognumByDepartmentCabinet($departmentno=null, $cabinetno=null, $action=null, $alarm=null, $begintime=null, $endtime=null)
    {
        $where = array();
        if ($departmentno) $where['departmentno'] = is_array($departmentno) ? array('in', $departmentno) : $departmentno;
        if ($cabinetno) $where['cabinetno'] = is_array($cabinetno) ? array('in', $cabinetno) : $cabinetno;
        if ($action!==null) $where['action'] = $action;
        if ($alarm!==null) $where['alarm'] = $alarm;
        if ($begintime!==null) $where['logtime'] = array('egt', $begintime);
        if ($endtime!==null) $where['logtime'] = array('elt', $endtime);
        if ($begintime!==null && $endtime!==null) $where['logtime'] = array('between', array($begintime, $endtime));

        $cabinetdoorloglist = M('cabinetdoorlog')->field('departmentno, cabinetno, count(logid) as lognum')->where($where)->group('departmentno, cabinetno')->select();
        $cabinetdoorlognumlist = array();
        if (is_array($cabinetdoorloglist)&&!empty($cabinetdoorloglist)) {
            foreach ($cabinetdoorloglist as $cdl) {
                $cabinetdoorlognumlist[$cdl['departmentno'].'-'.$cdl['cabinetno']] = $cdl['lognum'];
            }
        }

        return $cabinetdoorlognumlist;
    }

    /**
     * 获取钥匙使用日志
     * @param int action 0:领取 1:归还
     * @param int actionflag 0:正常领取/归还 1:异常领取（没有权限） 2:归还错位
     */
    public function getKeyuseLog($logid=null, $departmentno=null, $cabinetno=null, $keyno=null, $userno=null, $username=null, $action=null, $actionflag=null, $begintime=null, $endtime=null, $start=0, $length=9999)
    {
        $where = array();
        if ($logid) $where['logid'] = is_array($logid) ? array('in', $logid) : $logid;
        if ($departmentno) $where['a.departmentno'] = is_array($departmentno) ? array('in', $departmentno) : $departmentno;
        if ($cabinetno) $where['a.cabinetno'] = is_array($cabinetno) ? array('in', $cabinetno) : $cabinetno;
        if ($keyno) $where['a.keyno'] = is_array($keyno) ? array('in', $keyno) : $keyno;
        if ($userno) $where['userno'] = $userno;
        if ($action!==null) $where['action'] = $action;
        if ($actionflag!==null) $where['actionflag'] = $actionflag;
        if ($begintime!==null) $where['logtime'] = array('egt', $begintime);
        if ($endtime!==null) $where['logtime'] = array('elt', $endtime);
        if ($begintime!==null && $endtime!==null) $where['logtime'] = array('between', array($begintime, $endtime));

        $total = M('keyuselog')->alias('a')->where($where)->count();
        $data = M('keyuseLog')->alias('a')
                              ->field('a.*, b.keyname, c.cabinetname, d.departmentname, v.videofile, v.videosize, v.videolength')
                              ->join(' __KEYS__ b on a.departmentno=b.departmentno and a.cabinetno=b.cabinetno and a.keyno=b.keyno ')
                              ->join(' __CABINET__ c on a.departmentno=c.departmentno and a.cabinetno=c.cabinetno ')
                              ->join(' __DEPARTMENT__ d on a.departmentno=d.departmentno ')
                              ->join(' LEFT JOIN __VIDEOLOG__ v on v.videologid=a.videologid ')
                              ->where($where)
                              ->order('logtime desc')
                              ->limit($start, $length)
                              ->select();

        return array('total'=>$total, 'data'=>is_array($data)?$data:array());
    }

    //获取钥匙使用日志数量
    public function getKeyuseLognumByDepartmentCabinet($departmentno=null, $cabinetno=null, $action=null, $actionflag=null, $begintime=null, $endtime=null)
    {
        $where = array();
        if ($departmentno) $where['departmentno'] = is_array($departmentno) ? array('in', $departmentno) : $departmentno;
        if ($cabinetno) $where['cabinetno'] = is_array($cabinetno) ? array('in', $cabinetno) : $cabinetno;
        if ($action!==null) $where['action'] = $action;
        if ($actionflag!==null) $where['actionflag'] = $actionflag;
        if ($begintime!==null) $where['logtime'] = array('egt', $begintime);
        if ($endtime!==null) $where['logtime'] = array('elt', $endtime);
        if ($begintime!==null && $endtime!==null) $where['logtime'] = array('between', array($begintime, $endtime));

        $keyuseloglist = M('keyuselog')->field('departmentno, cabinetno, count(logid) as lognum')->where($where)->group('departmentno, cabinetno')->select();
        $keyuselognumlist = array();
        if (is_array($keyuseloglist)&&!empty($keyuseloglist)) {
            foreach ($keyuseloglist as $cdl) {
                $keyuselognumlist[$cdl['departmentno'].'-'.$cdl['cabinetno']] = $cdl['lognum'];
            }
        }

        return $keyuselognumlist;
    }
}