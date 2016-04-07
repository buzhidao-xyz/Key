<?php
/**
 * 监控软件模型
 * buzhidao
 * 2016-03-27
 */

namespace Admin\Model;

class MonitorServerModel extends CommonModel
{
    public function __construct()
    {
        parent::__construct();
    }

    //获取监控软件
    public function getMtserver($mtserverid=null, $mtserverip=null, $departmentno=null, $start=0, $length=9999)
    {
        if (!$length) return false;

        $where = array();
        if ($mtserverid) $where['a.mtserverid'] = $mtserverid;
        if ($mtserverip) $where['a.mtserverip'] = array('like', '%'.$mtserverip.'%');

        $field = 'a.*';
        $join = null;
        if ($departmentno) {
            $field .= ', b.departmentno';
            $join = ' __DEPARTMENT_MONITORSERVER__ b on a.mtserverid=b.mtserverid and departmentno='.$departmentno;
        }

        $total = M('monitorserver')->alias('a')->join($join)->where($where)->count();
        $data = M('monitorserver')->alias('a')->field($field)->join($join)->where($where)->order('createtime asc')->limit($start, $length)->select();

        //获取监控软件监控的部门
        $where1 = array();
        if ($departmentno) $where1['departmentno'] = $departmentno;
        if ($mtserverid) $where1['mtserverid'] = $mtserverid;
        $mtserverdepartment = M('department_monitorserver')->where($where1)->select();

        //组合关联的departmentno
        if (is_array($data)&&!empty($data)) {
            foreach ($data as $k=>$d) {
                $data[$k]['departmentno'] = array();
                foreach ($mtserverdepartment as $mtsdpm) {
                    if ($mtsdpm['mtserverid'] == $d['mtserverid']) {
                        $data[$k]['departmentno'][] = $mtsdpm['departmentno'];
                    }
                }
            }
        }

        return array('total'=>$total, 'data'=>is_array($data)?$data:array());
    }

    //保存监控软件
    public function savemtserver($mtserverid=null, $data=array())
    {
        if (!is_array($data) || empty($data)) return false;

        if ($mtserverid) {
            $return = M("monitorserver")->where(array('mtserverid'=>$mtserverid))->save($data);
        } else {
            $return = M("monitorserver")->add($data);
        }

        return $return;
    }
}