<?php
/**
 * 员工管理模块
 * buzhidao
 * 2016-03-27
 */
namespace Admin\Controller;

class UserController extends CommonController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index(){}

    //录入警员信息
    public function newuser()
    {
        $this->display();
    }
}