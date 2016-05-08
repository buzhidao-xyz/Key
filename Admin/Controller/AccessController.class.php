<?php
/**
 * 门禁钥匙权限业务逻辑
 * buzhidao
 * 2016-04-05
 */
namespace Admin\Controller;

use Any\Upload;
use Org\Net\Http;

class AccessController extends CommonController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index(){}

    //钥匙柜门禁配置
    public function cabinetaccess()
    {
        $this->assign('subcompanyno', '');
        $this->display();
    }

    //钥匙柜门禁配置-保存
    public function cabinetaccesssave()
    {
        $departmentno = $this->_getDepartmentno();
        if (!$departmentno) $this->ajaxReturn(1, '请选择'.L('WordLang.DepartmentLang').'！');

        $cabinetnos = mRequest('cabinetnos', false);
        if (!is_array($cabinetnos) || empty($cabinetnos)) {
            $cabinetno = mRequest('cabinetno');
            if (!$cabinetno) $this->ajaxReturn(1, '请选择钥匙柜！');

            $cabinetnos = array($cabinetno);
        }

        $usernos = mRequest('usernos', false);
        if (!is_array($usernos) || empty($usernos)) $this->ajaxReturn(1, '请选择'.L('WordLang.UserLang').'！');

        foreach ($cabinetnos as $cabinetno) {
            $data = array();
            foreach ($usernos as $userno) {
                $data[] = array(
                    'departmentno' => $departmentno,
                    'cabinetno'    => $cabinetno,
                    'userno'       => $userno,
                );
            }
            $result = D('Access')->savecabinetuser($departmentno, $cabinetno, $data);
            if (!$result) $this->ajaxReturn(1, '保存失败！');
        }
        $this->ajaxReturn(0, '保存成功！');
    }

    //'.L('WordLang.UserLang').'钥匙配置
    public function userkeyaccess()
    {
        $this->display();
    }

    //'.L('WordLang.UserLang').'钥匙配置-保存
    public function userkeyaccesssave()
    {
        $departmentno = $this->_getDepartmentno();
        if (!$departmentno) $this->ajaxReturn(1, '请选择'.L('WordLang.DepartmentLang').'！');
        
        $usernos = mRequest('usernos', false);
        if (!is_array($usernos) || empty($usernos)) $this->ajaxReturn(1, '请选择'.L('WordLang.UserLang').'！');

        $cabinetno = $this->_getCabinetno();
        if (!$cabinetno) $this->ajaxReturn(1, '请选择钥匙柜！');

        $keynos = mRequest('keynos', false);
        if (!is_array($keynos) || empty($keynos)) $this->ajaxReturn(1, '请选择钥匙！');

        $data = array();
        foreach ($usernos as $userno) {
            foreach ($keynos as $keyno) {
                $data[] = array(
                    'departmentno' => $departmentno,
                    'userno'       => $userno,
                    'cabinetno'    => $cabinetno,
                    'keyno'        => $keyno,
                );
            }
        }
        $result = D('Access')->saveUserkeyaccess($departmentno, $usernos, $cabinetno, $data);
        if ($result) {
            $this->ajaxReturn(0, '保存成功！');
        } else {
            $this->ajaxReturn(1, '保存失败！');
        }
    }

    //【下发】钥匙信息
    public function sendCabinetKeys()
    {
        $departmentno = $this->_getDepartmentno();
        if (!$departmentno) $this->ajaxReturn(1, '未知'.L('WordLang.DepartmentLang').'！');

        $cabinetno = $this->_getCabinetno();
        if (!$cabinetno) $this->ajaxReturn(1, '未知钥匙柜！');

        //获取部门信息
        $departmentinfo = array();
        foreach ($this->company['subcompany'] as $subcompany) {
            if (isset($subcompany['department'])) {
                foreach ($subcompany['department'] as $department) {
                    if ($department['departmentno'] == $departmentno) {
                        $departmentinfo = $department;
                        break(2);
                    }
                }
            }
        }
        if (!$departmentinfo['mtserverid']) $this->ajaxReturn(1, '没有关联监控软件！');

        $departmentno = dechex($departmentno);
        if (strlen($departmentno)==1) $departmentno = '0'.$departmentno;

        $cabinetno = dechex($cabinetno);
        if (strlen($cabinetno)==1) $cabinetno = '0'.$cabinetno;
        
        $data = 'AA BB 00 05 56 '.$departmentno.' '.$cabinetno.' 00 00 00 EE FF';
        $result = $this->_socketTcpSend($departmentinfo['mtserverip'], $departmentinfo['mtserverport'], $data);
        if ($result) {
            $this->ajaxReturn(0, '下发钥匙信息成功！');
        } else {
            $this->ajaxReturn(1, '下发钥匙信息失败！');
        }
    }

    //【下发】门禁权限
    public function sendCabinetAccess()
    {
        $departmentno = $this->_getDepartmentno();
        if (!$departmentno) $this->ajaxReturn(1, '未知'.L('WordLang.DepartmentLang').'！');

        $cabinetno = $this->_getCabinetno();
        if (!$cabinetno) $this->ajaxReturn(1, '未知钥匙柜！');

        //获取部门信息
        $departmentinfo = array();
        foreach ($this->company['subcompany'] as $subcompany) {
            if (isset($subcompany['department'])) {
                foreach ($subcompany['department'] as $department) {
                    if ($department['departmentno'] == $departmentno) {
                        $departmentinfo = $department;
                        break(2);
                    }
                }
            }
        }
        if (!$departmentinfo['mtserverid']) $this->ajaxReturn(1, '没有关联监控软件！');

        $departmentno = dechex($departmentno);
        if (strlen($departmentno)==1) $departmentno = '0'.$departmentno;

        $cabinetno = dechex($cabinetno);
        if (strlen($cabinetno)==1) $cabinetno = '0'.$cabinetno;
        
        $data = 'AA BB 00 05 55 '.$departmentno.' '.$cabinetno.' 00 00 00 EE FF';
        $result = $this->_socketTcpSend($departmentinfo['mtserverip'], $departmentinfo['mtserverport'], $data);
        if ($result) {
            $this->ajaxReturn(0, '下发门禁权限成功！');
        } else {
            $this->ajaxReturn(1, '下发门禁权限失败！');
        }
    }

    //【下发】人员钥匙权限
    public function sendUserKeyAccess()
    {
        $departmentno = $this->_getDepartmentno();
        if (!$departmentno) $this->ajaxReturn(1, '未知'.L('WordLang.DepartmentLang').'！');

        $cabinetno = $this->_getCabinetno();
        if (!$cabinetno) $this->ajaxReturn(1, '未知钥匙柜！');

        //获取部门信息
        $departmentinfo = array();
        foreach ($this->company['subcompany'] as $subcompany) {
            if (isset($subcompany['department'])) {
                foreach ($subcompany['department'] as $department) {
                    if ($department['departmentno'] == $departmentno) {
                        $departmentinfo = $department;
                        break(2);
                    }
                }
            }
        }
        if (!$departmentinfo['mtserverid']) $this->ajaxReturn(1, '没有关联监控软件！');

        $departmentno = dechex($departmentno);
        if (strlen($departmentno)==1) $departmentno = '0'.$departmentno;

        $cabinetno = dechex($cabinetno);
        if (strlen($cabinetno)==1) $cabinetno = '0'.$cabinetno;
        
        $data = 'AA BB 00 05 57 '.$departmentno.' '.$cabinetno.' 00 00 00 EE FF';
        $result = $this->_socketTcpSend($departmentinfo['mtserverip'], $departmentinfo['mtserverport'], $data);
        if ($result) {
            $this->ajaxReturn(0, '下发'.L('WordLang.UserLang').'钥匙权限成功！');
        } else {
            $this->ajaxReturn(1, '下发'.L('WordLang.UserLang').'钥匙权限失败！');
        }
    }

    //钥匙柜有哪些人员权限
    public function cabinetuser()
    {
        $departmentno = $this->_getDepartmentno();
        if (!$departmentno) $this->ajaxReturn(1, '未知'.L('WordLang.DepartmentLang').'！');

        $cabinetno = $this->_getCabinetno();
        if (!$cabinetno) $this->ajaxReturn(1, '未知钥匙柜！');

        $departmentinfo = $this->_getDepartmentinfo($departmentno);

        $cabinetlist = D('Cabinet')->getCabinet(null, null, $departmentno, 0, 9999);
        $cabinetlist = $cabinetlist['data'];
        $this->assign('cabinetlist', $cabinetlist);

        $userlist = D('User')->getUser(null, null, $departmentno);
        $userlist = $userlist['data'];
        $this->assign('userlist', $userlist);

        $cabinetinfo = D('Cabinet')->getCabinetByDepartmentnoCabinetno($departmentno, $cabinetno);
        $this->assign('cabinetinfo', $cabinetinfo);

        //钥匙柜'.L('WordLang.UserLang').'权限列表
        $cabinetuser = D('Access')->getCabinetUser($departmentno, $cabinetno);
        $this->assign('cabinetuser', $cabinetuser);

        $this->display('Access/cabinetaccess');
    }

    //人员有哪些钥匙权限
    public function userkey()
    {
        $userid = mRequest('userid');
        $this->assign('userid', $userid);
        if (!$userid) exit;

        $userinfo = D('User')->getUserByID($userid);
        if (!is_array($userinfo) || empty($userinfo)) exit;

        //subcompanyno
        foreach ($this->company['subcompany'] as $subcompany) {
            if (isset($subcompany['department'])) {
                foreach ($subcompany['department'] as $department) {
                    if ($department['departmentno'] == $userinfo['departmentno']) {
                        $userinfo['subcompanyno'] = $subcompany['subcompanyno'];
                        $userinfo['subcompanyname'] = $subcompany['subcompanyname'];
                        break(2);
                    }
                }
            }
        }

        $this->assign('subcompanyno', $userinfo['subcompanyno']);
        $this->assign('departmentno', $userinfo['departmentno']);

        $this->assign('userinfo', $userinfo);

        //获取钥匙柜的钥匙信息
        $cabinetlist = D('Cabinet')->getCabinet(null, null, $userinfo['departmentno'], 0, 9999);
        $cabinetlist = $cabinetlist['data'];
        foreach ($cabinetlist as $key=>$cabinet) {
            $data = D('Key')->getKey(null, null, null, $departmentno, $cabinet['cabinetno']);
            $cabinetlist[$key]['keylist'] = $data['data'];
        }
        $this->assign('cabinetlist', $cabinetlist);

        //获取钥匙权限信息
        $userkeynos = D('Access')->getUserkeyByUserno($userinfo['departmentno'], $userinfo['userno']);
        $this->assign('userkeynos', $userkeynos);

        $this->display();
    }

    //人员有哪些钥匙权限 - 保存
    public function userkeysave()
    {
        $departmentno = $this->_getDepartmentno();
        if (!$departmentno) $this->ajaxReturn(1, '未知'.L('WordLang.DepartmentLang').'！');
        
        $userno = mRequest('userno', false);
        if (!$userno) $this->ajaxReturn(1, '未知'.L('WordLang.UserLang').'！');

        $keynos = mRequest('keynos', false);
        if (!is_array($keynos) || empty($keynos)) $this->ajaxReturn(1, '请选择钥匙！');

        $cabinetnos = array();
        $data = array();
        foreach ($keynos as $cabinetno=>$keynod) {
            $cabinetnos[] = $cabinetno;

            foreach ($keynod as $keyno) {
                $data[] = array(
                    'departmentno' => $departmentno,
                    'userno'       => $userno,
                    'cabinetno'    => $cabinetno,
                    'keyno'        => $keyno,
                );
            }
        }
        $result = D('Access')->saveUserkeyaccess($departmentno, $userno, $cabinetnos, $data);
        if ($result) {
            $this->ajaxReturn(0, '保存成功！');
        } else {
            $this->ajaxReturn(1, '保存失败！');
        }
    }

    //清除'.L('WordLang.UserLang').'钥匙权限
    public function cleanuserkeyaccess()
    {
        $this->display();
    }

    //清除'.L('WordLang.UserLang').'钥匙权限 - 保存
    public function cleanuserkeyaccesssave()
    {
        $departmentno = $this->_getDepartmentno();
        if (!$departmentno) $this->ajaxReturn(1, '请选择'.L('WordLang.DepartmentLang').'！');
        
        $usernos = mRequest('usernos', false);
        if (!is_array($usernos) || empty($usernos)) $this->ajaxReturn(1, '请选择'.L('WordLang.UserLang').'！');

        $where = array(
            'departmentno' => $departmentno,
            'userno' => array('in', $usernos)
        );
        $result = M('userkey')->where($where)->delete();
        if ($result) {
            $this->ajaxReturn(0, '清除成功！');
        } else {
            $this->ajaxReturn(1, '清除失败！');
        }
    }
}