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

        return $status;
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
        $codeno = $this->_getCodeno();
    }
}