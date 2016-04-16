<?php
/**
 * 设备模型
 * buzhidao
 * 2016-03-27
 */

namespace Admin\Model;

class DeviceModel extends CommonModel
{
    public function __construct()
    {
        parent::__construct();
    }

    //获取设备类型
    public function getDeviceType($devicetypeid=null)
    {
        $where = array();
        if ($devicetypeid) $where['devicetypeid'] = $devicetypeid;

        $result = M('devicetype')->where($where)->select();
        $data = array();
        if (is_array($result)) {
            foreach ($result as $d) {
                $data[$d['devicetypeid']] = $d;
            }
        }

        return $data;
    }

    //获取设备类型通过ID
    public function getDeviceTypeByID($devicetypeid=null)
    {
        if (!$devicetypeid) return false;

        $devicetypeinfo = $this->getDeviceType($devicetypeid);

        return !empty($devicetypeinfo) ? array_shift($devicetypeinfo) : array();
    }

    //保存设备类型信息
    public function savedevicetype($devicetypeid=null, $data=array())
    {
        if (!is_array($data) || empty($data)) return false;

        if ($devicetypeid) {
            $return = M("devicetype")->where(array('devicetypeid'=>$devicetypeid))->save($data);
        } else {
            $return = M("devicetype")->add($data);
        }

        return $return;
    }

    //获取某部门某种设备类型最大的设备编号
    public function getMaxDeviceno($departmentno=null, $devicetypeid=null)
    {
        if (!$departmentno || !$devicetypeid) return false;

        $deviceinfo = M("device")->where(array('departmentno'=>$departmentno, 'devicetypeid'=>$devicetypeid))->order('convert(int,deviceno) desc')->find();

        return is_array($deviceinfo)&&!empty($deviceinfo) ? $deviceinfo['deviceno'] : 0;
    }

    //保存设备信息
    public function savedevice($deviceid=null, $data=array())
    {
        if (!is_array($data) || empty($data)) return false;

        if ($deviceid) {
            $return = M("device")->where(array('deviceid'=>$deviceid))->save($data);
        } else {
            $return = M("device")->add($data);
        }

        return $return;
    }

    //保存钥匙柜关联的设备
    public function savedevicecabinet($deviceid=null, $data=array())
    {
        if (!$deviceid || !is_array($data) || empty($data)) return false;

        M('cabinet_device')->where(array('deviceid'=>$deviceid))->delete();

        M('cabinet_device')->addAll($data);
    }
}