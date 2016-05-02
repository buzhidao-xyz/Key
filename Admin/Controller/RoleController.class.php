<?php
/**
 * 角色管理
 * buzhidao
 * 2015-08-03
 */
namespace Admin\Controller;

class RoleController extends CommonController
{
    public function __construct()
    {
        parent::__construct();
    }

    //获取roleid
    public function _getRoleid($ck=false, $ajax=true)
    {
        $roleid = mRequest('roleid');
        $this->assign('roleid', $roleid);

        $ck&&!$roleid&&$ajax ? $this->ajaxReturn(1, '未知角色！') : null;
        $ck&&!$roleid&&!$ajax ? $this->pageReturn(1, '未知角色！') : null;

        return $roleid;
    }

    //获取rolename
    public function _getRolename()
    {
        $rolename = mRequest('rolename');
        $this->assign('rolename', $rolename);

        return $rolename;
    }

    //获取access
    public function _getAccess()
    {
        $access = mRequest('access', false);
        $this->assign('access', $access);

        if (!is_array($access) || empty($access)) $this->ajaxReturn(1, '请选择菜单权限！');

        return $access;
    }

    //角色
    public function index()
    {
        $rolename = $this->_getRolename();

        list($start, $length) = $this->_mkPage();
        $data = D('Role')->getRole(null, $rolename, $start, $length);
        $total = $data['total'];
        $datalist = $data['data'];

        $this->assign('datalist', $datalist);

        $params = array(
            'rolename' => $rolename,
        );
        $this->assign('params', $params);
        //解析分页数据
        $this->_mkPagination($total, $params);

        $paramstr = null;
        foreach ($params as $key=>$value) {
            $paramstr .= '&'.$key.'='.$value;
        }
        $this->assign('paramstr', $paramstr);

        $this->display();
    }

    //新增角色
    public function newrole()
    {
        $this->display();
    }

    //编辑角色
    public function uprole()
    {
        $roleid = $this->_getRoleid(true,false);

        //获取角色信息
        $roleinfo = D('Role')->getRoleByID($roleid);

        //获取角色菜单
        $roleaccess = D('Manager')->getManagerAccess(0, $roleid);
        $roleinfo['access'] = $roleaccess;

        //获取系统菜单
        $accesslist = D('Manager')->getManagerAccess(1);
        $this->assign('accesslist', $accesslist);

        // dump($roleinfo);exit;
        $this->assign('roleinfo', $roleinfo);
        $this->display();
    }

    //编辑角色 - 保存
    public function uprolesave()
    {
        $roleid = $this->_getRoleid(true);
        $access = $this->_getAccess();

        $data = array();
        foreach ($access as $d) {
            $groupid = $d['groupid'];
            foreach ($d['nodeids'] as $nodeid) {
                $data[] = array(
                    'roleid'  => (int)$roleid,
                    'groupid' => (int)$groupid,
                    'nodeid'  => (int)$nodeid,
                );
            }
        }

        //开启事务
        M('role_node')->startTrans();

        //删除原来的
        $delresult = true;
        if (M('role_node')->where(array('roleid'=>$roleid))->count()) {
            $delresult = M('role_node')->where(array('roleid'=>$roleid))->delete();
        }
        //批量插入
        $result = M('role_node')->addAll($data);

        if ($delresult && $result) {
            M('role_node')->commit();
            $this->ajaxReturn(0, '保存成功！');
        } else {
            M('role_node')->rollback();
            $this->ajaxReturn(1, '保存失败！');
        }
    }

    //删除角色
    public function delrole()
    {
        $this->display();
    }
}