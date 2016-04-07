<?php
/**
 * 门禁钥匙权限模型
 * buzhidao
 * 2016-04-05
 */

namespace Admin\Model;

class AccessModel extends CommonModel
{
    public function __construct()
    {
        parent::__construct();
    }

    //保存钥匙柜门禁配置
    public function savecabinetuser($departmentno=null, $cabinetno=null, $data=array())
    {
        if (!$departmentno || !$cabinetno || !is_array($data) || empty($data)) return false;

        M('cabinet_user')->where(array('departmentno'=>$departmentno, 'cabinetno'=>$cabinetno))->delete();

        $result = M('cabinet_user')->addAll($data);

        return $result;
    }

    //保存警员钥匙配置
    public function saveUserkeyaccess($departmentno=null, $userno=null, $cabinetno=null, $data=array())
    {
        if (!$departmentno || !is_array($userno) || empty($userno) || !$cabinetno || !is_array($data) || empty($data)) return false;

        M('userkey')->where(array('departmentno'=>$departmentno, 'userno'=>array('in', $userno), 'cabinetno'=>$cabinetno))->delete();

        $result = M('userkey')->addAll($data);

        return $result;
    }
}