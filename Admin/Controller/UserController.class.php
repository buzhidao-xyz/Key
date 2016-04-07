<?php
/**
 * 员工管理模块
 * buzhidao
 * 2016-03-27
 */
namespace Admin\Controller;

use Any\Upload;

class UserController extends CommonController
{
    public function __construct()
    {
        parent::__construct();
    }

    //获取员工名称
    private function _getUsername()
    {
        $username = mRequest('username');
        $this->assign('username', $username);

        return $username;
    }

    //获取员工编号
    private function _getCodeno()
    {
        $codeno = mRequest('codeno');
        $this->assign('codeno', $codeno);

        return $codeno;
    }

    //获取员工卡号
    private function _getCardno()
    {
        $cardno = mRequest('cardno');
        $this->assign('cardno', $cardno);

        return $cardno;
    }

    //获取员工手机号
    private function _getPhone()
    {
        $phone = mRequest('phone');
        $this->assign('phone', $phone);

        return $phone;
    }

    //获取员工名称
    private function _getPosition()
    {
        $position = mRequest('position');
        $this->assign('position', $position);

        return $position;
    }

    //获取员工照片
    private function _getPhoto()
    {
        $photo = mRequest('photo');
        $this->assign('photo', $photo);

        return $photo;
    }

    //获取员工控制权限
    private function _getAccess()
    {
        $access = mRequest('access');
        $this->assign('access', $access);

        return $access;
    }

    //获取员工状态
    private function _getStatus()
    {
        $status = mRequest('status');
        $this->assign('status', $status);

        return (int)$status;
    }

    public function index(){}

    //录入员工信息
    public function newuser()
    {
        $this->display();
    }

    //员工头像上传
    public function photoupload()
    {
        //初始化上传类
        $Upload = new Upload();
        $Upload->maxSize  = 50*1024;
        $Upload->exts     = array('jpg', 'gif', 'png', 'jpeg');
        $Upload->rootPath = UPLOAD_PATH;
        $Upload->savePath = 'User/photo/';
        $Upload->saveName = array('uniqid', array('', true));
        $Upload->autoSub  = true;
        $Upload->subName  = array('date', 'Ym');

        //上传
        $error = null;
        $msg = '上传成功！';
        $data = array();
        $info = $Upload->upload();
        if (!$info) {
            $error = 1;
            $msg = $Upload->getError();
        } else {
            $fileinfo = array_shift($info);
            $data = array(
                'filepath' => '/'.UPLOAD_PT.$fileinfo['savepath'],
                'filename' => $fileinfo['savename'],
            );
        }

        $this->ajaxReturn($error, $msg, $data);
    }

    //保存员工信息
    public function newusersave()
    {
        $username = $this->_getUsername();
        if (!$username) $this->ajaxReturn(1, '请填写警员名称！');

        $codeno = $this->_getCodeno();
        if (!$codeno) $this->ajaxReturn(1, '请填写警员编号！');
        //查询警员编号是否已存在
        if (D("User")->ckUserCodenoExists(null, $codeno)) $this->ajaxReturn(1, '该警员编号已存在！');

        $cardno = $this->_getCardno();
        $phone = $this->_getPhone();
        $position = $this->_getPosition();
        $photo = $this->_getPhoto();
        $access = $this->_getAccess();
        $status = $this->_getStatus();

        $subcompanyno = $this->_getSubcompanyno();
        $departmentno = $this->_getDepartmentno();
        if (!$departmentno) $this->ajaxReturn(1, '请选择派出所！');

        //获取部门信息
        $departmentinfo = D('Company')->getDepartmentByNO($departmentno);
        $userno = $departmentinfo['maxuserno']+1;

        $userid = guid();
        $data = array(
            'userid'   => $userid,
            'userno'   => $userno,
            'username' => $username,
            'codeno'   => $codeno,
            'cardno'   => $cardno,
            'phone'    => $phone,
            'departmentno' => $departmentno,
            'position' => $position,
            'photo'    => $photo,
            'access'   => $access,
            'status'   => $status,
            'createtime' => mkDateTime(),
            'updatetime' => mkDateTime()
        );
        $result = D('User')->saveuser(null, $data);
        if ($result) {
            //更新部门信息 - maxuserno
            D('Company')->savedepartment($departmentinfo['departmentid'], array(
                'maxuserno' => $userno
            ));

            $this->ajaxReturn(0, '保存成功！');
        } else {
            $this->ajaxReturn(1, '保存失败！');
        }
    }

    //导入员工信息
    public function importuser()
    {
        
    }

    //管理员工
    public function userlist()
    {
        
    }

    //AJAX获取警员信息
    public function ajaxGetUser()
    {
        $departmentno = $this->_getDepartmentno();
        if (!$departmentno) $this->ajaxReturn(1, '钥匙柜信息错误！');

        $userlist = D('User')->getUser(null, null, $departmentno);
        $userlist = $userlist['data'];

        $this->ajaxReturn(0, '', array(
            'userlist' => $userlist
        ));
    }
}