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
        if (!$departmentno || !$userno || !$cabinetno || !is_array($data) || empty($data)) return false;

        $where = array(
            'departmentno' => $departmentno,
            'userno'       => is_array($userno) ? array('in', $userno) : $userno,
            'cabinetno'    => is_array($cabinetno) ? array('in', $cabinetno) : $cabinetno,
        );
        M('userkey')->where($where)->delete();

        $result = M('userkey')->addAll($data);

        return $result;
    }

    //获取钥匙柜权限
    public function getCabinetUser($departmentno=null, $cabinetno=null)
    {
        if (!$departmentno || !$cabinetno) return false;

        $result = M('cabinet_user')->where(array('departmentno'=>$departmentno, 'cabinetno'=>$cabinetno))->select();
        $data = array();
        if (is_array($result)) {
            foreach ($result as $d) {
                $data[] = $d['userno'];
            }
        }

        return $data;
    }

    //获取钥匙权限信息
    public function getUserkeyByUserno($departmentno=null, $userno=null)
    {
        if (!$departmentno || !$userno) return false;

        $result = M('userkey')->where(array('departmentno'=>$departmentno, 'userno'=>$userno))->select();
        $data = array();
        if (is_array($result)) {
            foreach ($result as $d) {
                $data[] = $d['cabinetno'].'-'.$d['keyno'];
            }
        }

        return $data;
    }
}