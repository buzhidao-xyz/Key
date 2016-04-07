<?php
/**
 * 权限模型
 * buzhidao
 * 2016-04-04
 */
namespace Api\Model;

class AccessModel extends CommonModel
{
    public function __construct()
    {
        parent::__construct();
    }

    //获取钥匙柜关联的员工信息
    public function getCabinetAccess($departmentno=null, $cabinetno=null, $userno=null)
    {
        if (!$departmentno || !$cabinetno) return false;

        $where = array(
            'departmentno' => $departmentno,
            'cabinetno' => $cabinetno,
        );
        if ($userno) $where['userno'] = $userno;

        $total = M('cabinet_user')->where($where)->count();
        $data = M('cabinet_user')->where($where)->select();

        return array('total'=>$total, 'data'=>is_array($data)?$data:array());
    }

    //获取员工关联的钥匙信息
    public function getUserkey($departmentno=null, $cabinetno=null, $userno=null)
    {
        if (!$departmentno || !$cabinetno) return false;

        $where = array(
            'departmentno' => $departmentno,
            'cabinetno' => $cabinetno,
        );
        if ($userno) $where['userno'] = is_array($userno) ? array('in', $userno) : $userno;

        $result = M('userkey')->where($where)->select();
        $data = array();
        if (is_array($result)) {
            foreach ($result as $d) {
                $data[$departmentno.'-'.$cabinetno.'-'.$d['userno']][] = (int)$d['keyno'];
            }
        }

        return $data;
    }
}