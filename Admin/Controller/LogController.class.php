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
    //钥匙动作
    private $_key_actionlist = array(
        0 => array('action'=>0, 'name'=>'领取', 'color'=>'danger'),
        1 => array('action'=>1, 'name'=>'归还', 'color'=>'sky'),
    );

    public function __construct()
    {
        parent::__construct();

        $this->assign('keyactionlist', $this->_key_actionlist);
    }

    //获取keyname
    private function _getKeyname()
    {
        $keyname = mRequest('keyname');
        $this->assign('keyname', $keyname);

        return $keyname;
    }

    //获取username
    private function _getUsername()
    {
        $username = mRequest('username');
        $this->assign('username', $username);

        return $username;
    }

    //获取begintime
    private function _getBegintime()
    {
        $begintime = mRequest('begintime');
        if (!$begintime) $begintime = '2016-01-01 00:00:00';

        $this->assign('begintime', $begintime);
        return $begintime;
    }

    //获取endtime
    private function _getEndtime()
    {
        $endtime = mRequest('endtime');
        if (!$endtime) $endtime = date('Y-m-d H:i:s', TIMESTAMP);

        $this->assign('endtime', $endtime);
        return $endtime;
    }

    public function index(){}

    //钥匙使用日志
    public function keyuselog()
    {
        $subcompanyno = $this->_getSubcompanyno();
        $departmentno = $this->_getDepartmentno();
        if ($departmentno) {
            $cabinetlist = D('Cabinet')->getCabinet(null, null, $departmentno);
            $this->assign('cabinetlist', $cabinetlist['data']);
        }
        $cabinetno = $this->_getCabinetno();
        //钥匙名称、显示名称
        $keyname = $this->_getKeyname();
        $keynos = null;
        if ($keyname) {
            $keylist = D('Key')->getKey(null, null, $keyname, $departmentno, $cabinetno);
            $keylist = $keylist['data'];
            foreach ($keylist as $key) {
                $keynos[] = $key['keyno'];
            }
        }
        $username = $this->_getUsername();

        $begintime = $this->_getBegintime();
        $endtime = $this->_getEndtime();

        list($start, $length) = $this->_mkPage();
        $data = D('Log')->getKeyuseLog(null, $departmentno, $cabinetno, $keynos, null, $username, null, null, $begintime, $endtime, $start, $length);
        $total = $data['total'];
        $datalist = $data['data'];

        $this->assign('datalist', $datalist);

        $params = array(
            'subcompanyno' => $subcompanyno,
            'departmentno' => $departmentno,
            'cabinetno'    => $cabinetno,
            'keyname'      => $keyname,
            'username'     => $username,
            'begintime'    => $begintime,
            'endtime'      => $endtime,
        );
        $this->assign('param', $params);
        //解析分页数据
        $this->_mkPagination($total, $params);

        $this->display();
    }
    //钥匙柜开关门日志
    public function dooropenlog()
    {
        $subcompanyno = $this->_getSubcompanyno();
        $departmentno = $this->_getDepartmentno();
        if ($departmentno) {
            $cabinetlist = D('Cabinet')->getCabinet(null, null, $departmentno);
            $this->assign('cabinetlist', $cabinetlist['data']);
        }
        $cabinetno = $this->_getCabinetno();
        $username = $this->_getUsername();

        $begintime = $this->_getBegintime();
        $endtime = $this->_getEndtime();

        list($start, $length) = $this->_mkPage();
        $data = D('Log')->getCabinetdoorLog(null, $departmentno, $cabinetno, null, $username, null, null, $begintime, $endtime, $start, $length);
        $total = $data['total'];
        $datalist = $data['data'];

        $this->assign('datalist', $datalist);

        $params = array(
            'subcompanyno' => $subcompanyno,
            'departmentno' => $departmentno,
            'cabinetno'    => $cabinetno,
            'username'     => $username,
            'begintime'    => $begintime,
            'endtime'      => $endtime,
        );
        $this->assign('param', $params);
        //解析分页数据
        $this->_mkPagination($total, $params);

        $this->display();
    }

    //播放视频
    public function videoplay()
    {
        
    }
}