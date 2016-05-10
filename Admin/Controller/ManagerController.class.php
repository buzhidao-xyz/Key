<?php
/**
 * 管理员管理
 * buzhidao
 * 2015-08-03
 */
namespace Admin\Controller;

use Org\Util\Filter;

class ManagerController extends CommonController
{
    public function __construct()
    {
        parent::__construct();

        $this->_rolelist = M('role')->select();
        $this->assign('rolelist', $this->_rolelist);
    }

    public function index(){}

    //获取managerid
    private function _getManagerid()
    {
        $managerid = mRequest('managerid');
        $this->assign('managerid', $managerid);

        return $managerid;
    }

    //获取账户Account
    private function _getAccount()
    {
        $account = mRequest('account');
        $this->assign('account', $account);

        return $account;
    }

    //获取账户password
    private function _getPassword()
    {
        $password = mRequest('password');
        $this->assign('password', $password);

        return $password;
    }

    //获取账户passwordc
    private function _getPasswordc()
    {
        $passwordc = mRequest('passwordc');
        $this->assign('passwordc', $passwordc);

        return $passwordc;
    }

    //超级管理员标识
    private function _getSuper()
    {
        $super = mRequest('super');
        $this->assign('super', $super);

        return $super;
    }

    //获取关联员工ID
    private function _getUserid()
    {
        $userid = mRequest('userid');
        $this->assign('userid', $userid);

        return $userid;
    }

    //获取username
    private function _getUsername()
    {
        $username = mRequest('username');
        $this->assign('username', $username);

        return $username;
    }

    //获取状态
    private function _getStatus()
    {
        $status = mRequest('status');
        $this->assign('status', $status);

        return $status;
    }

    //获取角色信息
    private function _getRoleid()
    {
        $roleid = mRequest('roleid');
        $this->assign('roleid', $roleid);

        return $roleid;
    }

    //获取管理员
    public function _getManager($start=0, $length=0)
    {
        //账户
        $account = $this->_getAccount();
        //员工
        $username = $this->_getUsername();

        //获取管理员列表
        $result = D('Manager')->getManager(null, $account, $username, $start, $length);
        $datatotal = $result['total'];
        $this->assign('datatotal', $datatotal);

        $datalist = array();
        if (is_array($result['data']) && !empty($result['data'])) {
            $autoindex = $start ? $start+1 : 1;
            foreach ($result['data'] as $manager) {
                $manager['autoindex'] = $autoindex++;

                $manager['supername'] = $manager['super'] ? '是' : '否';

                $datalist[] = $manager;
            }
        }
        $this->assign('datalist', $datalist);

        $params = array(
            'account'  => $account,
            'username' => $username,
        );
        $this->assign('params', $params);
        //解析分页数据
        $this->_mkPagination($datatotal, $params);

        return array($datatotal, $datalist);
    }

    //管理员
    public function managerlist()
    {
        list($start, $length) = $this->_mkPage();
        $this->_getManager($start, $length);

        $this->display();
    }

    //启用、禁用管理员
    public function enablemanager()
    {
        $managerid = $this->_getManagerid();
        if (!$managerid) $this->ajaxReturn(1, '未知管理员！');

        //获取管理员信息
        $managerinfo = D('Manager')->getManagerByID($managerid);
        if (empty($managerinfo)) $this->ajaxReturn(1, '未知管理员！');
        if ($managerinfo['super']) $this->ajaxReturn(1, '非法操作！超级管理员！');

        $status = $this->_getStatus();
        $status = $status ? 1 : 0;

        $result = D('Manager')->enableManager($managerid, $status);
        if ($result) {
            $this->ajaxReturn(0, '操作成功！');
        } else {
            $this->ajaxReturn(1, '操作失败！');
        }
    }

    //删除管理员
    public function delmanager()
    {
        $managerid = $this->_getManagerid();
        if (!$managerid) $this->ajaxReturn(1, '未知管理员！');

        //获取管理员信息
        $managerinfo = D('Manager')->getManagerByID($managerid);
        if (empty($managerinfo)) $this->ajaxReturn(1, '未知管理员！');
        if ($managerinfo['super']) $this->ajaxReturn(1, '非法操作！超级管理员！');

        $result = M('manager')->where(array('managerid'=>$managerid))->delete();
        if ($result) {
            $this->ajaxReturn(0, '删除成功！');
        } else {
            $this->ajaxReturn(1, '删除失败！');
        }
    }

    //新增管理员
    public function newmanager()
    {
        $this->display();
    }

    //新增管理员 - 保存
    public function newmanagersave()
    {
        $account = $this->_getAccount();
        if (!Filter::F_Account($account)) $this->ajaxReturn(1, '请填写账号！');
        //检查账号是否已存在
        if (M('manager')->where(array('account'=>$account))->count()) $this->ajaxReturn(1, '该账号已存在！');

        $password = $this->_getPassword();
        if (!Filter::F_Password($password)) $this->ajaxReturn(1, '密码不正确！');
        $passwordc = $this->_getPasswordc();
        if ($password != $passwordc) $this->ajaxReturn(1, '两次输入的密码不一致！');

        $roleid = $this->_getRoleid();
        if (!$roleid) $this->ajaxReturn(1, '请选择管理员角色！');

        $departmentnos = $this->_getDepartmentnos();
        if (!is_array($departmentnos)||empty($departmentnos)) $this->ajaxReturn(1, '请选择管理的'.L('WordLang.DepartmentLang').'！');

        $managerid = guid();
        $mkey = \Org\Util\String::randString(6,3);
        $password = D('Manager')->passwordEncrypt($password, $mkey);
        $data = array(
            'managerid'  => $managerid,
            'account'    => $account,
            'password'   => $password,
            'mkey'       => $mkey,
            'status'     => 1,
            'super'      => 0,
            'roleid'     => $roleid,
            'userid'     => 0,
            'createtime' => mkDateTime(),
            'updatetime' => mkDateTime(),
            'lastlogintime' => mkDateTime(),
        );
        $result = D('Manager')->saveManager(null, $data);
        if ($result) {
            //保存管理的'.L('WordLang.DepartmentLang').'信息
            $mddata = array();
            foreach ($departmentnos as $departmentno) {
                $mddata[] = array(
                    'managerid' => $managerid,
                    'departmentno' => $departmentno,
                );
            }
            D('Manager')->saveManagerDepartment($managerid, $mddata);

            $this->ajaxReturn(0, '保存成功！');
        } else {
            $this->ajaxReturn(1, '保存失败！');
        }
    }

    //编辑管理员
    public function upmanager()
    {
        $managerid = $this->_getManagerid();
        $this->assign('managerid', $managerid);

        //获取管理员信息
        $managerinfo = D('Manager')->getManagerByID($managerid);
        if (empty($managerinfo) || $managerinfo['super']) exit;

        $this->assign('managerinfo', $managerinfo);

        $departmentnos = D('Manager')->getManagerDepartment($managerid);
        $this->assign('departmentnos', $departmentnos);

        $this->display();
    }

    //保存编辑管理员信息
    public function upmanagersave()
    {
        $managerid = $this->_getManagerid();
        $this->assign('managerid', $managerid);

        //获取管理员信息
        $managerinfo = D('Manager')->getManagerByID($managerid);
        if (empty($managerinfo)) $this->ajaxReturn(1, '未知管理员！');
        if ($managerinfo['super']) $this->ajaxReturn(1, '非法操作！超级管理员！');

        $account = $this->_getAccount();
        if (!Filter::F_Account($account)) $this->ajaxReturn(1, '请填写账号！');
        //检查账号是否已存在
        if (M('manager')->where(array('managerid'=>array('neq', $managerid),'account'=>$account))->count()) $this->ajaxReturn(1, '该账号已存在！');

        $password = $this->_getPassword();
        if ($password && !Filter::F_Password($password)) $this->ajaxReturn(1, '密码不正确！');

        $roleid = $this->_getRoleid();
        if (!$roleid) $this->ajaxReturn(1, '请选择管理员角色！');

        $departmentnos = $this->_getDepartmentnos();
        if (!is_array($departmentnos)||empty($departmentnos)) $this->ajaxReturn(1, '请选择管理的'.L('WordLang.DepartmentLang').'！');

        $data = array(
            'account' => $account,
            'roleid'  => $roleid,
        );
        if ($password) {
            $password = D('Manager')->passwordEncrypt($password, $managerinfo['mkey']);
            $data['password'] = $password;
        }
        $result = D('Manager')->saveManager($managerid, $data);
        if ($result) {
            //保存管理的'.L('WordLang.DepartmentLang').'信息
            $mddata = array();
            foreach ($departmentnos as $departmentno) {
                $mddata[] = array(
                    'managerid' => $managerid,
                    'departmentno' => $departmentno,
                );
            }
            D('Manager')->saveManagerDepartment($managerid, $mddata);

            $this->ajaxReturn(0, '保存成功！');
        } else {
            $this->ajaxReturn(1, '保存失败！');
        }
    }

    //管理员登录日志
    public function loginlog()
    {
        $this->display();
    }

    //账号密码
    public function chpasswd()
    {
        $this->display();
    }

    //账号密码-保存
    public function chpasswdsave()
    {
        $managerinfo = $this->managerinfo;

        //获取管理员信息
        $managerinfo = D('Manager')->getManagerByID($managerinfo['managerid']);

        $passwordo = mRequest('passwordo');
        if (D('Manager')->passwordEncrypt($passwordo, $managerinfo['mkey']) != $managerinfo['password']) $this->ajaxReturn(1, '原密码不正确！');

        $password = mRequest('password');
        if (!Filter::F_Password($password)) $this->ajaxReturn(1, '新密码不正确！');
        $passwordc = mRequest('passwordc');
        if ($password != $passwordc) $this->ajaxReturn(1, '两次输入的密码不一致！');

        $result = M('manager')->where(array('managerid'=>$managerinfo['managerid']))->save(array(
            'password' => D('Manager')->passwordEncrypt($password, $managerinfo['mkey']),
        ));
        if ($result) {
            $this->ajaxReturn(0, '修改成功！');
        } else {
            $this->ajaxReturn(1, '修改失败！');
        }
    }
}
