<?php
/**
 * 监控软件管理模块
 * buzhidao
 * 2016-03-29
 */
namespace Admin\Controller;

use Any\Upload;

class MonitorServerController extends CommonController
{
    public function __construct()
    {
        parent::__construct();
    }

    //获取服务器IP
    private function _getMtserverip()
    {
        $mtserverip = mRequest('mtserverip');
        $this->assign('mtserverip', $mtserverip);

        return $mtserverip;
    }

    //获取服务器port
    private function _getMtserverport()
    {
        $mtserverport = mRequest('mtserverport');
        $this->assign('mtserverport', $mtserverport);

        return $mtserverport;
    }

    //获取在线状态
    private function _getOnline()
    {
        $online = mRequest('online');
        $this->assign('online', $online);

        return (int)$online;
    }

    public function index(){}

    //管理监控软件
    public function mtserver()
    {
        $subcompanyno = $this->_getSubcompanyno();
        $departmentno = $this->_getDepartmentno();
        $mtserverip = $this->_getMtserverip();

        list($start, $length) = $this->_mkPage();
        $data = D('MonitorServer')->getMtserver(null, $mtserverip, $departmentno, $start, $length);
        $total = $data['total'];
        $datalist = $data['data'];

        $this->assign('datalist', $datalist);

        $params = array(
            'subcompanyno' => $subcompanyno,
            'departmentno' => $departmentno,
            'mtserverip' => $mtserverip,
        );
        $this->assign('param', $params);
        //解析分页数据
        $this->_mkPagination($total, $params);

        $this->display();
    }

    //添加监控软件
    public function newmtserver()
    {
        $this->display();
    }

    //添加监控软件 - 保存
    public function newmtserversave()
    {
        $mtserverip = $this->_getMtserverip();
        if (!$mtserverip) $this->ajaxReturn(1, '请填写服务器IP！');
        $mtserverport = $this->_getMtserverport();
        if (!$mtserverport) $this->ajaxReturn(1, '请填写服务器port！');
        
        $online = $this->_getOnline();

        $data = array(
            'mtserverip'        => $mtserverip,
            'mtserverport'      => $mtserverport,
            'online'            => $online,
            'lastheartbeattime' => mkDateTime(),
            'createtime'        => mkDateTime(),
        );
        $result = D('MonitorServer')->savemtserver(null, $data);
        if ($result) {
            $this->ajaxReturn(0, '保存成功！');
        } else {
            $this->ajaxReturn(1, '保存失败！');
        }
    }

    //编辑监控软件
    public function ajaxGetMTServerHtml()
    {
        $mtserverid = mRequest('mtserverid');
        if (!$mtserverid) $this->ajaxReturn(1, '未知监控软件！');

        //获取监控软件列表
        $mtserverlist = D('MonitorServer')->getMtserver($mtserverid);
        if (!$mtserverlist['total']) $this->ajaxReturn(1, '未知监控软件！');

        $this->assign("mtserverinfo", current($mtserverlist['data']));

        $html = $this->fetch('MonitorServer/upmtserver_modal');

        $this->ajaxReturn(0, '', array(
            'html' => $html
        ));
    }

    //编辑监控软件-保存
    public function upmtserversave()
    {
        $mtserverid = mRequest('mtserverid');
        if (!$mtserverid) $this->ajaxReturn(1, '未知监控软件！');

        $mtserverip = $this->_getMtserverip();
        if (!$mtserverip) $this->ajaxReturn(1, '请填写服务器IP！');
        $mtserverport = $this->_getMtserverport();
        if (!$mtserverport) $this->ajaxReturn(1, '请填写服务器port！');
        
        $data = array(
            'mtserverip'   => $mtserverip,
            'mtserverport' => $mtserverport,
        );
        $result = D('MonitorServer')->savemtserver($mtserverid, $data);
        if ($result) {
            $this->ajaxReturn(0, '保存成功！');
        } else {
            $this->ajaxReturn(1, '保存失败！');
        }
    }
}