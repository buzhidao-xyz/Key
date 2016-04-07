<?php
/**
 * 日志业务逻辑
 * buzhidao
 * 2016-04-05
 */
namespace Admin\Controller;

use Any\Upload;

class LogController extends CommonController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index(){}

    //钥匙使用日志
    public function keyuselog()
    {
        $this->display();
    }
}