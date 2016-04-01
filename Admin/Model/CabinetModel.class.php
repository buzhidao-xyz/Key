<?php
/**
 * 钥匙柜模型
 * buzhidao
 * 2016-03-27
 */

namespace Admin\Model;

class CabinetModel extends CommonModel
{
    public function __construct()
    {
        parent::__construct();
    }

    //获取钥匙柜
    public function getCabinet($cabinetid=null, $cabinetname=null, $departmentno=null, $start=0, $length=0)
    {

    }

    //获取钥匙柜信息
    public function getCabinetByID($cabinetid=null)
    {

    }

    //获取某部门最大的钥匙柜编号
    public function getMaxCabinetno($departmentno=null)
    {
        if (!$departmentno) return false;

        $cabinetinfo = M("cabinet")->where(array('departmentno'=>$departmentno))->order('cabinetno desc')->find();

        return is_array($cabinetinfo)&&!empty($cabinetinfo) ? $cabinetinfo['cabinetno'] : 0;
    }

    //保存钥匙柜信息
    public function savecabinet($cabinetid=null, $data=array())
    {
        if (!is_array($data) || empty($data)) return false;

        if ($cabinetid) {
            $return = M("cabinet")->where(array('cabinetid'=>$cabinetid))->save($data);
        } else {
            $return = M("cabinet")->add($data);
        }

        return $return;
    }
}