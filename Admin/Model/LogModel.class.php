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

    //获取钥匙使用日志
    public function getKeyuseLog($logid=null, $departmentno=null, $cabinetno=null, $keyno=null, $userno=null, $action=null, $actionflag=null, $begintime=null, $endtime=null, $start=0, $length=9999)
    {
        $where = array();
        if ($logid) $where['logid'] = is_array($logid) ? array('in', $logid) : $logid;
        if ($departmentno) $where['departmentno'] = $departmentno;
        if ($cabinetno) $where['cabinetno'] = $cabinetno;
        if ($keyno) $where['keyno'] = $keyno;
        if ($userno) $where['userno'] = $userno;
        if ($action) $where['action'] = $action;
        if ($actionflag) $where['actionflag'] = $actionflag;
        if ($begintime) $where['logtime'] = array('egt', $begintime);
        if ($endtime) $where['logtime'] = array('elt', $endtime);
        if ($begintime && $endtime) $where['logtime'] = array('between', array($begintime, $endtime));

        $total = M('keyuselog')->where($where)->count();
        $data = M('keyuseLog')->where($where)
                              ->order('logtime desc')
                              ->limit($start, $length)
                              ->select();

        return array('total'=>$total, 'data'=>is_array($data)?$data:array());
    }
}