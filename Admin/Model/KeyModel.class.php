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

        $keyinfo = M("keys")->where(array('departmentno'=>$departmentno, 'cabinetno'=>$cabinetno))->order('keyno desc')->find();

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

    //获取钥匙信息
    public function getKey($keyid=null, $keytypeid=null, $keyshowname=null, $departmentno=null, $cabinetno=null, $keyno=null, $keypos=null, $keyrfid=null, $start=0, $legnth=9999)
    {
        $where = array();
        if ($keyid) $where['keyid'] = is_array($keyid) ? array('in', $keyid) : $keyid;
        if ($keytypeid) $where['keytypeid'] = $keytypeid;
        if ($keyshowname) $where['keyshowname'] = array('like', '%'.$keyshowname.'%');
        if ($departmentno) $where['departmentno'] = $departmentno;
        if ($cabinetno) $where['cabinetno'] = $cabinetno;
        if ($keyno) $where['keyno'] = $keyno;
        if ($keypos) $where['keypos'] = $keypos;
        if ($keyrfid) $where['keyrfid'] = $keyrfid;

        $total = M('keys')->where($where)->count();
        $data = M('keys')->alias('a')
                         ->field('a.*, b.keytypename, b.keytypeimage')
                         ->join(' __KEYTYPE__ b on a.keytypeid=b.keytypeid ')
                         ->where($where)
                         ->order('anyphp.departmentno asc, anyphp.cabinetno asc, anyphp.keypos asc')
                         ->limit($start, $length)
                         ->select();

        //获取使领取时限
        if (is_array($data)) {
            $keyids = array('');
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