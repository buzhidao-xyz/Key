<?php
/**
 * 菜单
 * buzhidao
 * 2016-03-26
 */
namespace Admin\Controller;

class MenuController extends CommonController
{
    //默认选中组菜单id=1(实时监控)
    private $_default_groupid = 1;

    public function __construct()
    {
        parent::__construct();
    }

    //获取组菜单id
    private function _getGroupid()
    {
        $groupid = mRequest('groupid');
        $this->assign('groupid', $groupid);

        return $groupid;
    }

    public function index()
    {
        $this->display();
    }

    public function group(){}

    public function node(){}

    //ajax获取菜单html
    public function ajaxNodeHtml()
    {
        $groupid = $this->_getGroupid();
        if (!$groupid) $this->ajaxReturn(1, '菜单组错误！');

        //sidebar菜单class
        $sidebarclass = mRequest('sidebarclass');
        $this->assign('sidebarclass', $sidebarclass);
        
        $this->assign('default_groupid', $this->_default_groupid);

        $this->assign('nodemenu', isset($this->managerinfo['access'][$groupid]) ? $this->managerinfo['access'][$groupid]['nodelist'] : array());

        $html = $this->fetch('Public/menu');

        $this->ajaxReturn(0, '', array(
            'html' => $html
        ));
    }
}