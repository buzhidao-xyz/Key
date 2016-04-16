<?php
/**
 * 钥匙模型
 * buzhidao
 * 2016-03-27
 */

namespace Api\Model;

class KeyModel extends CommonModel
{
    public function __construct()
    {
        parent::__construct();
    }

    //获取钥匙信息
    public function getKey($keyid=null, $keytypeid=null, $keyshowname=null, $departmentno=null, $cabinetno=null, $keyno=null, $keypos=null, $keyrfid=null, $start=0, $legnth=9999)
    {
        $where = array(
            'isdelete' => 0
        );
        if ($keyid) $where['keyid'] = is_array($keyid) ? array('in', $keyid) : $keyid;
        if ($keytypeid) $where['keytypeid'] = $keytypeid;
        if ($keyshowname) $where['keyshowname'] = array('like', '%'.$keyshowname.'%');
        if ($departmentno) $where['a.departmentno'] = is_array($departmentno) ? array('in', $departmentno) : $departmentno;
        if ($cabinetno) $where['a.cabinetno'] = is_array($cabinetno) ? array('in', $cabinetno) : $cabinetno;
        if ($keyno) $where['a.keyno'] = is_array($keyno) ? array('in', $keyno) : $keyno;
        if ($keypos) $where['keypos'] = is_array($keypos) ? array('in', $keypos) : $keypos;
        if ($keyrfid) $where['keyrfid'] = is_array($keyrfid) ? array('in', $keyrfid) : $keyrfid;

        $total = M('keys')->alias('a')->where($where)->count();
        $data = M('keys')->alias('a')
                         ->field('a.*, cc.carid, cc.carname, cc.brand, cc.modelv, cc.parkplace, b.keytypename, b.keytypeimage')
                         ->join(' LEFT JOIN __CARS__ cc on a.departmentno=cc.departmentno and a.cabinetno=cc.cabinetno and a.keyno=cc.keyno ')
                         ->join(' __KEYTYPE__ b on a.keytypeid=b.keytypeid ')
                         ->where($where)
                         ->order('convert(int,anyphp.departmentno) asc, convert(int,anyphp.cabinetno) asc, convert(int,anyphp.keypos) asc')
                         ->limit($start, $length)
                         ->select();

        //获取使领取时限
        if (is_array($data)) {
            $keyids = array('0');
            foreach ($data as $k=>$d) {
                $keyids[] = $d['keyid'];
            }

            $keyusetime = M('keys_usetime')->where(array('keyid'=>array('in', $keyids)))->select();
            $keyusetimes = array();
            if (is_array($keyusetime)) {
                foreach ($keyusetime as $d) {
                    $keyusetimes[$d['keyid']][] = array(
                        'begintime' => $d['begintime'],
                        'endtime' => $d['endtime'],
                    );
                }
            }

            foreach ($data as $k=>$d) {
                $data[$k]['usetime'] = isset($keyusetimes[$d['keyid']]) ? $keyusetimes[$d['keyid']] : array();
            }
        }

        return array('total'=>$total, 'data'=>is_array($data)?$data:array());
    }
}