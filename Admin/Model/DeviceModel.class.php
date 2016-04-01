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
            $return = M("devicetype")->where(array('cabinetid'=>$cabinetid))->save($data);
        } else {
            $return = M("devicetype")->add($data);
        }

        return $return;
    }
}