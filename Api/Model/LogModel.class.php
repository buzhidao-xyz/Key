<?php
/**
 * 日志模型
 * buzhidao
 * 2016-04-04
 */

namespace Api\Model;

class LogModel extends CommonModel
{
    public function __construct()
    {
        parent::__construct();
    }

    //保存图片日志
    public function savePhotolog($photologid=null, $data=array())
    {
        if (!is_array($data) || empty($data)) return false;

        if ($photologid) {
            $return = M("photolog")->where(array('photologid'=>$photologid))->save($data);
        } else {
            $return = M("photolog")->add($data);
        }

        return $return;
    }

    //保存视频日志
    public function saveVideolog($videologid=null, $data=array())
    {
        if (!is_array($data) || empty($data)) return false;

        if ($videologid) {
            $return = M("videolog")->where(array('videologid'=>$videologid))->save($data);
        } else {
            $return = M("videolog")->add($data);
        }

        return $return;
    }

    //保存钥匙柜开关门日志
    public function saveCabinetdoorlog($logid=null, $data=array())
    {
        if (!is_array($data) || empty($data)) return false;

        if ($logid) {
            $return = M("cabinetdoorlogg")->where(array('logid'=>$logid))->save($data);
        } else {
            $return = M("cabinetdoorlogg")->add($data);
        }

        return $return;
    }

    //保存钥匙使用日志
    public function saveKeyuselog($logid=null, $data=array())
    {
        if (!is_array($data) || empty($data)) return false;

        if ($logid) {
            $return = M("keyuselog")->where(array('logid'=>$logid))->save($data);
        } else {
            $return = M("keyuselog")->add($data);
        }

        return $return;
    }
}