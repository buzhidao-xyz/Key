<?php
/**
 * 钥匙模型
 * buzhidao
 * 2016-03-27
 */

namespace Admin\Model;

class KeyModel extends CommonModel
{
    public function __construct()
    {
        parent::__construct();
    }

    //获取钥匙类型
    public function getKeyType($keytypeid=null)
    {
        $where = array();
        if ($keytypeid) $where['keytypeid'] = $keytypeid;

        $result = M('keytype')->where($where)->order('createtime asc')->select();
        $data = array();
        if (is_array($result)) {
            foreach ($result as $d) {
                $data[$d['keytypeid']] = $d;
            }
        }

        return $data;
    }

    //获取钥匙类型通过ID
    public function getKeyTypeByID($keytypeid=null)
    {
        if (!$keytypeid) return false;

        $keytypeinfo = $this->getKeyType($keytypeid);

        return !empty($keytypeinfo) ? array_shift($keytypeinfo) : array();
    }

    //保存钥匙类型信息
    public function savekeytype($keytypeid=null, $data=array())
    {
        if (!is_array($data) || empty($data)) return false;

        if ($keytypeid) {
            $return = M("keytype")->where(array('keytypeid'=>$keytypeid))->save($data);
        } else {
            $return = M("keytype")->add($data);
        }

        return $return;
    }

    //获取某部门某个钥匙柜最大的钥匙编号
    public function getMaxKeyno($departmentno=null, $cabinetno=null)
    {
        if (!$departmentno || !$cabinetno) return false;

        $keyinfo = M("keys")->where(array('departmentno'=>$departmentno, 'cabinetno'=>$cabinetno))->order('convert(int,keyno) desc')->find();

        return is_array($keyinfo)&&!empty($keyinfo) ? $keyinfo['keyno'] : 0;
    }

    //保存钥匙信息
    public function savekey($keyid=null, $data=array())
    {
        if (!is_array($data) || empty($data)) return false;

        if ($keyid) {
            $return = M("keys")->where(array('keyid'=>$keyid))->save($data);
        } else {
            $return = M("keys")->add($data);
        }

        return $return;
    }

    //保存钥匙领取时限信息
    public function savekeyusetime($keyid=null, $data=array())
    {
        if (!$keyid || !is_array($data) || empty($data)) return false;

        M('keys_usetime')->where(array('keyid'=>$keyid))->delete();

        M('keys_usetime')->addAll($data);
    }

    //保存车辆信息
    public function savecar($carid=null, $data=array())
    {
        if (!is_array($data) || empty($data)) return false;

        if ($carid) {
            $return = M("cars")->where(array('carid'=>$carid))->save($data);
        } else {
            $where = array('departmentno'=>$data['departmentno'],'cabinetno'=>$data['cabinetno'],'keyno'=>$data['keyno']);
            if (M('cars')->where($where)->count()) M('cars')->where($where)->delete();

            $return = M("cars")->add($data);
        }

        return $return;
    }

    //获取钥匙信息
    public function getKey($keyid=null, $keytypeid=null, $keyname=null, $departmentno=null, $cabinetno=null, $keyno=null, $keypos=null, $keyrfid=null, $start=0, $length=9999, $car=0, $carname=null)
    {
        $where = array(
            'a.isdelete' => 0
        );
        if ($keyid) $where['keyid'] = is_array($keyid) ? array('in', $keyid) : $keyid;
        if ($keytypeid) $where['keytypeid'] = $keytypeid;
        if ($keyname) $where['_complex'] = array('_logic'=>'or', 'keyname'=>array('like', '%'.$keyname.'%'), 'keyshowname'=>array('like', '%'.$keyname.'%'));
        if ($departmentno) $where['a.departmentno'] = is_array($departmentno) ? array('in', $departmentno) : $departmentno;
        if ($cabinetno) $where['a.cabinetno'] = is_array($cabinetno) ? array('in', $cabinetno) : $cabinetno;
        if ($keyno) $where['keyno'] = is_array($keyno) ? array('in', $keyno) : $keyno;
        if ($keypos) $where['keypos'] = is_array($keypos) ? array('in', $keypos) : $keypos;
        if ($keyrfid) $where['keyrfid'] = is_array($keyrfid) ? array('in', $keyrfid) : $keyrfid;

        if ($car) $where['carname'] = array('neq', '');
        if ($carname) $where['carname'] = array('like', '%'.$carname.'%');

        $total = M('keys')->alias('a')->where($where)->count();
        $data = M('keys')->alias('a')
                         ->field('a.*, cc.carid, cc.carname, cc.brand, cc.modelv, cc.parkplace, cc.insurephoto, cc.insureexpiretime, cc.insureperson, cc.currentkilometer, cc.repairkilometer, cc.lastrepairtime, cc.repairperiodtime, b.keytypename, b.keytypeimage, c.cabinetname, d.departmentname')
                         ->join(' LEFT JOIN __CARS__ cc on a.departmentno=cc.departmentno and a.cabinetno=cc.cabinetno and a.keyno=cc.keyno ')
                         ->join(' __KEYTYPE__ b on a.keytypeid=b.keytypeid ')
                         ->join(' __CABINET__ c on a.departmentno=c.departmentno and a.cabinetno=c.cabinetno ')
                         ->join(' __DEPARTMENT__ d on a.departmentno=d.departmentno ')
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

    //获取钥匙信息 通过keyid
    public function getKeyByID($keyid=null)
    {
        if (!$keyid) return false;

        $keyinfo = $this->getKey($keyid);

        return $keyinfo['total'] ? current($keyinfo['data']) : array();
    }

    //钥匙数量统计
    public function getKeynumByDepartmentCabinet($departmentno=null, $cabinetno=null, $keystatus=null)
    {
        $where = array();
        if ($departmentno) $where['departmentno'] = is_array($departmentno) ? array('in', $departmentno) : $departmentno;
        if ($cabinetno) $where['cabinetno'] = is_array($cabinetno) ? array('in', $cabinetno) : $cabinetno;
        if ($keystatus!==null) $where['keystatus'] = $keystatus;

        $keylist = M('keys')->field('departmentno, cabinetno, count(keyid) as keynum')->where($where)->group('departmentno, cabinetno')->select();
        $keynumlist = array();
        if (is_array($keylist)&&!empty($keylist)) {
            foreach ($keylist as $key) {
                $keynumlist[$key['departmentno'].'-'.$key['cabinetno']] = $key['keynum'];
            }
        }

        return $keynumlist;
    }
}