<?php
/**
 * 管理业务逻辑
 * 注册、登录、登出等
 * imbzd
 * 2015-05-22
 */
namespace Admin\Controller;

use Org\Util\Filter;

class AdminController extends BaseController
{
    //登录结果
    private $_loginlog_result = array(
        'FAILED'  => 0,
        'SUCCESS' => 1,
    );

    //对象初始化
    public function __construct()
    {
        parent::__construct();
    }

    //检测如果已登录 则跳转到主页面
    private function _checkAdminLogon()
    {
        $managerinfo = session('managerinfo');
        if (is_array($managerinfo) && !empty($managerinfo)) {
            $this->_gotoIndex();
        }
    }

    //获取账号 规则：字母开始 字母数字下划线 长度5-20
    private function _getAccount()
    {
        $account = mRequest("account");
        if (!Filter::F_Account($account)) {
            $this->ajaxReturn(1, "账号或密码错误！");
        }

        return $account;
    }

    /**
     * 获取密码 规则：字母数字开始 字母数字下划线!@#$% 长度5-20
     */
    private function _getPassword()
    {
        $password = mRequest('password');
        if (!Filter::F_Password($password)) {
            $this->ajaxReturn(1, "账号或密码错误！");
        }

        return $password;
    }

    //获取验证码
    private function _CKVCode()
    {
        $vcode = mRequest('vcode');
        if (!CR('Org')->CKVcode($vcode)) {
            $this->ajaxReturn(1, "验证码错误！");
        }
        return true;
    }

    //登录 AJAX
    public function login()
    {
        $this->_checkAdminLogon();

        $this->assign('extr_page', 1);
        $this->display();
    }

    //执行登录检查逻辑 AJAX登录
    public function loginck()
    {
        $this->_checkAdminLogon();
        
        $account = $this->_getAccount();
        $password = $this->_getPassword();

        //检查验证码
        // $this->_CKVCode();
        
        $ip = get_client_ip();

        //查询管理员信息
        $managerInfo = D('Manager')->getManagerByAccount($account);

        //逻辑判断 如果登录失败
        if (!is_array($managerInfo) || empty($managerInfo)
         || !isset($managerInfo['password']) || !isset($managerInfo['mkey'])
         || $managerInfo['status'] == 0
         || D('Manager')->passwordEncrypt($password, $managerInfo['mkey']) != $managerInfo['password']) {
            $managerid = isset($managerInfo['managerid']) ? $managerInfo['managerid'] : 0;
            //记录登录日志
            D('Manager')->saveManagerLoginLog(array(
                'managerid' => $managerid,
                'logintime' => mkDateTime(),
                'loginip'   => $ip,
                'result'    => $this->_loginlog_result['FAILED'],
                'browser'   => $_SERVER['HTTP_USER_AGENT'],
                'resume'    => ''
            ));

            $this->ajaxReturn(1, L('LOGIN_ERROR'));
        }

        //登录成功
        $managerid = $managerInfo['managerid'];
        
        //更新管理员账户信息
        D('Manager')->saveManager($managerid, array(
            'lastlogintime' => mkDateTime(),
            'lastloginip'   => $ip,
            'logincount'    => array('exp', 'logincount+1'),
            'updatetime'    => mkDateTime(),
        ));
        
        //记录登录日志
        D('Manager')->saveManagerLoginLog(array(
            'managerid' => $managerid,
            'logintime' => mkDateTime(),
            'loginip'   => $ip,
            'result'    => $this->_loginlog_result['SUCCESS'],
            'browser'   => $_SERVER['HTTP_USER_AGENT'],
            'resume'    => ''
        ));

        //获取权限菜单信息
        $access = D('Manager')->getManagerAccess($managerInfo['super'], $managerInfo['roleid']);

        //session缓存管理员信息
        session('managerinfo', array(
            'managerid'     => $managerid,
            'account'       => $managerInfo['account'],
            'super'         => $managerInfo['super'],
            'roleid'        => $managerInfo['roleid'],
            'lastlogintime' => $managerInfo['lastlogintime'],
            'lastloginip'   => $managerInfo['lastloginip'],
            'logincount'    => $managerInfo['logincount'],
            'access'        => $access,
        ));

        $this->ajaxReturn(0, L('LOGIN_SUCCESS'), array(
            'location' => $this->_gotoIndex(false),
        ));
    }

    //登出
    public function logout()
    {
        session('managerinfo',null);

        session_destroy();

        $this->_gotoLogin();
    }
}