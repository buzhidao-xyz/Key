<?php
/**
 * 设备业务逻辑
 * buzhidao
 * 2016-03-30
 */
namespace Admin\Controller;

use Any\Upload;

class DeviceController extends CommonController
{
    //设备类型
    private $_devicetypelist = array();

    public function __construct()
    {
        parent::__construct();

        //获取设备类型
        $this->_devicetypelist = D('Device')->getDeviceType();
        $this->assign('devicetypelist', $this->_devicetypelist);
    }

    //获取devicetypeid
    private function _getDevicetypeid()
    {
        $devicetypeid = mRequest('devicetypeid');
        $this->assign('devicetypeid', $devicetypeid);

        return $devicetypeid;
    }

    //获取devicetypename
    private function _getDevicetypename()
    {
        $devicetypename = mRequest('devicetypename');
        $this->assign('devicetypename', $devicetypename);

        return $devicetypename;
    }

    //获取devicename
    private function _getDevicename()
    {
        $devicename = mRequest('devicename');
        $this->assign('devicename', $devicename);

        return $devicename;
    }

    //获取deviceip
    private function _getDeviceip()
    {
        $deviceip = mRequest('deviceip');
        $this->assign('deviceip', $deviceip);

        return $deviceip;
    }

    //获取deviceport
    private function _getDeviceport()
    {
        $deviceport = mRequest('deviceport');
        $this->assign('deviceport', $deviceport);

        return $deviceport;
    }

    //获取在线状态
    private function _getOnline()
    {
        $online = mRequest('online');
        $this->assign('online', $online);

        return (int)$online;
    }

    public function index(){}

    //设备类型管理
    public function devicetype()
    {
        $this->display();
    }

    //ajax获取设备类型html详情
    public function ajaxGetDeviceTypeHtml()
    {
        $devicetypeid = $this->_getDevicetypeid();

        $devicetypeinfo = $this->_devicetypelist[$devicetypeid];
        $this->assign('devicetypeinfo', $devicetypeinfo);

        $html = $this->fetch('Device/devicetype_modal');

        $this->ajaxReturn(0, '', array(
            'html' => $html
        ));
    }

    //保存设备类型信息
    public function devicetypesave()
    {
        $devicetypeid = $this->_getDevicetypeid();
        $devicetypename = $this->_getDevicetypename();

        $data = array(
            'devicetypename' => $devicetypename
        );
        $result = D('Device')->savedevicetype($devicetypeid, $data);
        if ($result) {
            $this->ajaxReturn(0, '保存成功！');
        } else {
            $this->ajaxReturn(1, '保存失败！');
        }
    }

    //新增设备
    public function newdevice()
    {
        $this->display();
    }

    //保存设备信息
    public function newdevicesave()
    {
        $devicename = $this->_getDevicename();
        if (!$devicename) $this->ajaxReturn(1, '请填写设备名称！');

        $devicetypeid = $this->_getDevicetypeid();
        if (!$devicetypeid) $this->ajaxReturn(1, '请选择设备类型！');

        $deviceip = $this->_getDeviceip();
        if (!$deviceip) $this->ajaxReturn(1, '请填写设备IP！');

        $deviceport = $this->_getDeviceport();
        if (!$deviceport) $this->ajaxReturn(1, '请填写设备端口！');

        $departmentno = $this->_getDepartmentno();
        if (!$departmentno) $this->ajaxReturn(1, '请选择派出所！');

        $online = $this->_getOnline();

        //获取cabinetnos
        $cabinetnos = $this->_getCabinetnos();
        if (!is_array($cabinetnos) || empty($cabinetnos)) $this->ajaxReturn(1, '请选择关联的钥匙柜！');

        //计算设备编号
        $maxDeviceno = D('Device')->getMaxDeviceno($departmentno, $devicetypeid);

        $deviceno = $maxDeviceno+1;
        $deviceid = guid();
        $data = array(
            'deviceid'          => $deviceid,
            'devicename'        => $devicename,
            'devicetypeid'      => $devicetypeid,
            'deviceip'          => $deviceip,
            'deviceport'        => $deviceport,
            'online'            => $online,
            'lastheartbeattime' => mkDateTime(),
            'departmentno'      => $departmentno,
            'deviceno'          => $deviceno,
            'createtime'        => mkDateTime(),
            'updatetime'        => mkDateTime(),
        );
        $result = D('Device')->savedevice(null, $data);
        if ($result) {
            //保存钥匙柜关联的设备信息
            $cddata = array();
            foreach ($cabinetnos as $cabinetno) {
                $cddata[] = array(
                    'departmentno' => $departmentno,
                    'cabinetno'    => $cabinetno,
                    'deviceid'     => $deviceid,
                );
            }
            D('Device')->savedevicecabinet($deviceid, $cddata);

            $this->ajaxReturn(0, '保存成功！');
        } else {
            $this->ajaxReturn(1, '保存失败！');
        }
    }

    //管理设备
    public function devicelist()
    {
        
    }
}