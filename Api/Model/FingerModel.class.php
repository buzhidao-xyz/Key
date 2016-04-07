<?php
/**
 * 指纹模型
 * buzhidao
 * 2016-04-04
 */
namespace Api\Model;

class FingerModel extends CommonModel
{
    public function __construct()
    {
        parent::__construct();
    }

    //获取员工指纹信息
    public function getUserFinger($departmentno=null, $userno=null, $fingerindex=null)
    {
        $where = array();
        if ($departmentno) $where['departmentno'] = is_array($departmentno) ? array('in', $departmentno) : $departmentno;
        if ($userno) $where['userno'] = is_array($userno) ? array('in', $userno) : $userno;
        if ($fingerindex) $where['fingerindex'] = $fingerindex;

        $total = M('userfinger')->where($where)->count();
        $result = M('userfinger')->where($where)->order('departmentno asc, userno asc')->select();

        //解析指纹信息
        $data = array();
        if (is_array($result)) {
            foreach ($result as $d) {
                $data[$d['departmentno'].'-'.$d['userno']][] = $d;
            }
        }

        return array('total'=>$total, 'data'=>$data);
    }

    //保存员工指纹信息
    public function saveUserFinger($departmentno=null, $userno=null, $data=array())
    {
        if (!$departmentno || !$userno || !is_array($data) || empty($data)) return false;

        $fingerindex = array();
        foreach ($data as $d) {
            $fingerindex[] = $d['fingerindex'];
        }

        M('userfinger')->where(array('departmentno'=>$departmentno, 'userno'=>$userno, 'fingerindex'=>array('in', $fingerindex)))->delete();

        $result = M('userfinger')->addAll($data);

        return $result;
    }
}