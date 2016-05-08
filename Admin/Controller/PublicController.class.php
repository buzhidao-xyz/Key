<?php
/**
 * 公共逻辑
 * buzhidao
 * 2016-05-08
 */
namespace Admin\Controller;

use Any\Upload;

class PublicController extends CommonController
{
    public function __construct()
    {
        parent::__construct();
    }

    //播放视频
    public function videoplay()
    {
        //获取videologid
        $videologid = mRequest('videologid');
        if (!$videologid) $this->ajaxReturn(1, '未知视频信息！');

        //获取视频日志信息
        $videologinfo = M('videolog')->where(array('videologid'=>$videologid))->find();
        $this->assign('videologinfo', $videologinfo);
        
        $html = $this->fetch('Public/videoplay');

        $this->ajaxReturn(0, '', array(
            'html' => $html
        ));
    }
}