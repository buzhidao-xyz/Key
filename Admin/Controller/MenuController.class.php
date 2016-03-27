<?php
/**
 * 菜单
 * buzhidao
 * 2016-03-26
 */
namespace Admin\Controller;

class MenuController extends CommonController
{
    public function __construct()
    {
        parent::__construct();
    }

    //获取组菜单id
    private function _getGroupid()
    {
        $groupid = mRequest('groupid');

        return $groupid;
    }

    public function index()
    {
        $this->display();
    }

    //ajax获取菜单html
    public function ajaxNodeHtml()
    {
        $groupid = $this->_getGroupid();
        if (!$groupid) $this->ajaxReturn(1, '菜单组错误！');

        $this->assign('nodemenu', isset($this->managerinfo['access'][$groupid]) ? $this->managerinfo['access'][$groupid]['nodelist'] : array());

        $html = $this->fetch('Public/menu');

        $this->ajaxReturn(0, '', array(
            'html' => $html
        ));
    }
}