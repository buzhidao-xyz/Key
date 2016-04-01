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
}