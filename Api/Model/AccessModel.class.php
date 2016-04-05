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
}