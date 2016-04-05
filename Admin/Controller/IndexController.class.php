<?php
/**
 * Admin Module Main Enter
 * imbzd
 * 2015-05-11
 */
namespace Admin\Controller;

class IndexController extends CommonController
{
    //默认选中组菜单id=1(实时监控)
    private $_default_groupid = 1;
    //默认选中节点菜单id=1(控制面板)
    private $_default_nodeid = 1;
    
    public function __construct()
    {
        parent::__construct();
    }

    //系统主框架页面
    public function index()
    {
        //节点菜单
        $nodemenu = isset($this->managerinfo['access'][$this->_default_groupid]) ? $this->managerinfo['access'][$this->_default_groupid]['nodelist'] : array();
        $this->assign('nodemenu', $nodemenu);

        $this->assign('groupid', $this->_default_groupid);
        //默认组菜单
        $this->assign('default_groupid', $this->_default_groupid);
        //默认节点菜单
        $this->assign('default_nodeid', $this->_default_nodeid);
        $this->display();
    }

    //系统主界面-控制面板
    public function dashboard()
    {
        //公司列表
        $subcompanylist = $this->company['subcompany'];
        $this->assign('subcompanylist', $subcompanylist);
        //当前subcompany
        $subcompany = array();
        $subcompanyno = $this->_getSubcompanyno();
        if ($subcompanyno) {
            $subcompany = $subcompanylist[$subcompanyno];
        } else {
            $subcompany = current($subcompanylist);
        }
        $subcompanyno = $subcompany['subcompanyno'];
        $this->assign('subcompanyno', $subcompanyno);

        //获取部门统计信息
        foreach ($subcompany['department'] as $dpm) {
            $department = $dpm;
        }

        // dump($subcompany);exit;
        $this->assign('subcompany', $subcompany);
        $this->display();
    }
}