<?php
/**
 * 日志逻辑
 * buzhidao
 * 2016-04-04
 */
namespace Api\Controller;

class LogController extends CommonController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index(){}

    //获取usercardno
    private function _getUsercardno()
    {
        $usercardno = mRequest('usercardno');

        return $usercardno;
    }

    //获取userno
    private function _getUserno()
    {
        $userno = mRequest('userno');

        return $userno;
    }

    //获取action
    private function _getAction()
    {
        $action = mRequest('action');

        return (int)$action;
    }

    //获取alarm
    private function _getAlarm()
    {
        $alarm = mRequest('alarm');

        return $alarm;
    }

    //获取keylockno
    private function _getKeylockno()
    {
        $keylockno = mRequest('keylockno');

        return $keylockno;
    }

    //获取keycardid
    private function _getKeycardid()
    {
        $keycardid = mRequest('keycardid');

        return $keycardid;
    }

    //获取actionflag
    private function _getActionflag()
    {
        $actionflag = mRequest('actionflag');

        return (int)$actionflag;
    }

    //获取photologid
    private function _getPhotologid()
    {
        $photologid = mRequest('photologid');

        return $photologid;
    }

    //获取photopath
    private function _getPhotopath()
    {
        $photopath = mRequest('photopath');

        return $photopath;
    }

    //获取photosize
    private function _getPhotosize()
    {
        $photosize = mRequest('photosize');

        return $photosize;
    }

    //获取videologid
    private function _getVideologid()
    {
        $videologid = mRequest('videologid');

        return $videologid;
    }

    //获取videopath
    private function _getVideopath()
    {
        $videopath = mRequest('videopath');

        return $videopath;
    }

    //获取videosize
    private function _getVideosize()
    {
        $videosize = mRequest('videosize');

        return $videosize;
    }

    //获取videotime
    private function _getVideotime()
    {
        $videotime = mRequest('videotime');

        return $videotime;
    }

    //获取cabinetdoorlogid
    private function _getCabinetdoorlogid()
    {
        $cabinetdoorlogid = mRequest('cabinetdoorlogid');

        return $cabinetdoorlogid;
    }

    //获取keyuselogid
    private function _getKeyuselogid()
    {
        $keyuselogid = mRequest('keyuselogid');

        return $keyuselogid;
    }

    //新增图片日志
    public function newphotolog()
    {
        $departmentno = $this->_getDepartmentno();
        if (!$departmentno) $this->apiReturn(1, '未知departmentno！');
        $cabinetno = $this->_getCabinetno();
        if (!$cabinetno) $this->apiReturn(1, '未知cabinetno！');
        
        $photopath = $this->_getPhotopath();

        $photologid = guid();
        $data = array(
            'photologid'   => $photologid,
            'departmentno' => $departmentno,
            'cabinetno'    => $cabinetno,
            'photofile'    => $photopath,
            'rectime'      => mkDateTime()
        );
        $result = D('Log')->savePhotolog(null, $data);
        if ($result) {
            $this->apiReturn(0, '新增成功！', array(
                'photologid' => $photologid
            ));
        } else {
            $this->apiReturn(1, '新增失败！', array(
                'photologid' => ''
            ));
        }
    }

    //更新图片日志
    public function upphotolog()
    {
        $photologid = $this->_getPhotologid();
        if (!$photologid) $this->apiReturn(1, '未知photologid！');

        $photopath = $this->_getPhotopath();
        $photosize = $this->_getPhotosize();

        $data = array(
            'photofile' => $photopath,
            'photosize' => $photosize,
        );
        $result = D('Log')->savePhotolog($photologid, $data);
        if ($result) {
            $this->apiReturn(0, '更新成功！', array(
                'status' => 1
            ));
        } else {
            $this->apiReturn(1, '更新失败！', array(
                'status' => 0
            ));
        }
    }

    //新增视频日志
    public function newvideolog()
    {
        $departmentno = $this->_getDepartmentno();
        if (!$departmentno) $this->apiReturn(1, '未知departmentno！');
        $cabinetno = $this->_getCabinetno();
        if (!$cabinetno) $this->apiReturn(1, '未知cabinetno！');
        
        $videopath = $this->_getVideopath();

        $videologid = guid();
        $data = array(
            'videologid'   => $videologid,
            'departmentno' => $departmentno,
            'cabinetno'    => $cabinetno,
            'videofile'    => $videopath,
            'rectime'      => mkDateTime()
        );
        $result = D('Log')->saveVideolog(null, $data);
        if ($result) {
            $this->apiReturn(0, '新增成功！', array(
                'videologid' => $videologid
            ));
        } else {
            $this->apiReturn(1, '新增失败！', array(
                'videologid' => ''
            ));
        }
    }

    //更新视频日志
    public function upvideolog()
    {
        $videologid = $this->_getVideologid();
        if (!$videologid) $this->apiReturn(1, '未知videologid！');

        $videopath = $this->_getVideopath();
        $videosize = $this->_getVideosize();
        $videotime = $this->_getVideotime();

        $data = array(
            'videofile'   => $videopath,
            'videosize'   => $videosize,
            'videolength' => $videotime,
        );
        $result = D('Log')->saveVideolog($videologid, $data);
        if ($result) {
            $this->apiReturn(0, '更新成功！', array(
                'status' => 1
            ));
        } else {
            $this->apiReturn(1, '更新失败！', array(
                'status' => 0
            ));
        }
    }

    //新增钥匙柜开/关门动作日志
    public function cabinetdoorlog()
    {
        $departmentno = $this->_getDepartmentno();
        if (!$departmentno) $this->apiReturn(1, '未知departmentno！');
        $cabinetno = $this->_getCabinetno();
        if (!$cabinetno) $this->apiReturn(1, '未知cabinetno！');
        
        $usercardno = $this->_getUsercardno();
        $userno = $this->_getUserno();
        $action = $this->_getAction();
        $alarm = $this->_getAlarm();

        //图片日志ID
        $photologid = $this->_getPhotologid();
        //视频日志ID
        $videologid = $this->_getVideologid();

        //获取员工信息
        $userinfo = D('User')->getUser(null, null, $departmentno, $userno, null, $usercardno);
        $userinfo = current($userinfo['data']);
        // if (!is_array($userinfo) || empty($userinfo)) $this->apiReturn(1, '未知警员信息！');

        $logid = guid();
        $data = array(
            'logid'        => $logid,
            'departmentno' => $departmentno,
            'cabinetno'    => $cabinetno,
            'userno'       => isset($userinfo['userno']) ? $userinfo['userno'] : 0,
            'username'     => isset($userinfo['username']) ? $userinfo['username'] : 0,
            'codeno'       => isset($userinfo['codeno']) ? $userinfo['codeno'] : 0,
            'action'       => $action,
            'alarm'        => $alarm,
            'photologid'   => $photologid,
            'videologid'   => $videologid,
            'logtime'      => mkDateTime(),
        );
        $result = D('Log')->saveCabinetdoorlog(null, $data);
        if ($result) {
            $this->apiReturn(0, '新增成功！', array(
                'logid' => $logid
            ));
        } else {
            $this->apiReturn(1, '新增失败！', array(
                'logid' => ''
            ));
        }
    }

    //新增钥匙领取/归还动作日志
    public function keyuselog()
    {
        $departmentno = $this->_getDepartmentno();
        if (!$departmentno) $this->apiReturn(1, '未知departmentno！');
        $cabinetno = $this->_getCabinetno();
        if (!$cabinetno) $this->apiReturn(1, '未知cabinetno！');

        $keylockno = $this->_getKeylockno();
        $keycardid = $this->_getKeycardid();
        $action = $this->_getAction();
        $actionflag = $this->_getActionflag();
        $userno = $this->_getUserno();

        //图片日志ID
        $photologid = $this->_getPhotologid();
        //视频日志ID
        $videologid = $this->_getVideologid();

        //获取员工信息
        $userinfo = D('User')->getUser(null, null, $departmentno, $userno);
        $userinfo = current($userinfo['data']);
        // if (!is_array($userinfo) || empty($userinfo)) $this->apiReturn(1, '未知警员信息！');

        //获取钥匙信息
        if ($keycardid) {
            $keyinfo = D('Key')->getKey(null, null, null, null, null, null, null, $keycardid);
        } else {
            $keyinfo = D('Key')->getKey(null, null, null, $departmentno, $cabinetno, null, $keylockno);
        }
        $keyinfo = current($keyinfo['data']);
        if (!is_array($keyinfo) || empty($keyinfo)) $this->apiReturn(1, '未知钥匙信息！');

        //判断是否错位
        $keystatus = (int)$action;
        if ($keystatus && $keycardid && $keylockno!=$keyinfo['keypos']) {
            $keystatus  = 2;
            $actionflag = 2;
        }

        //更新钥匙状态
        $keyposcurrent = $keystatus ? $keylockno : 0;
        M('keys')->where(array('keyid'=>$keyinfo['keyid']))->save(array(
            'keystatus'     => $keystatus,
            'keyposcurrent' => $keyposcurrent,
        ));

        //新增钥匙使用日志
        $logid = guid();
        $data = array(
            'logid'        => $logid,
            'departmentno' => $departmentno,
            'cabinetno'    => $cabinetno,
            'keyno'        => isset($keyinfo['keyno']) ? $keyinfo['keyno'] : 0,
            'keypos'       => isset($keyinfo['keypos']) ? $keyinfo['keypos'] : 0,
            'keyrfid'      => isset($keyinfo['keyrfid']) ? $keyinfo['keyrfid'] : 0,
            'keyshowname'  => isset($keyinfo['keyshowname']) ? $keyinfo['keyshowname'] : 0,
            'userno'       => isset($userinfo['userno']) ? $userinfo['userno'] : 0,
            'username'     => isset($userinfo['username']) ? $userinfo['username'] : 0,
            'codeno'       => isset($userinfo['codeno']) ? $userinfo['codeno'] : 0,
            'action'       => $action,
            'actionflag'   => $actionflag,
            'photologid'   => $photologid,
            'videologid'   => $videologid,
            'logtime'      => mkDateTime(),
        );
        if ($actionflag==2) $data['keyposwrong'] = $keylockno;

        $result = D('Log')->saveKeyuselog(null, $data);
        if ($result) {
            $this->apiReturn(0, '新增成功！', array(
                'logid' => $logid
            ));
        } else {
            $this->apiReturn(1, '新增失败！', array(
                'logid' => ''
            ));
        }
    }

    //关联图片日志
    public function linkphotolog()
    {
        $photologid = $this->_getPhotologid();
        if (!$photologid) $this->apiReturn(1, '未知photologid！');

        $cabinetdoorlogid = $this->_getCabinetdoorlogid();
        $keyuselogid = $this->_getKeyuselogid();

        //关联钥匙柜开关门日志
        $result1 = true;
        if ($cabinetdoorlogid) {
            $data = array(
                'photologid' => $photologid
            );
            $result1 = D('Log')->saveCabinetdoorlog($cabinetdoorlogid, $data);
        }

        //关联钥匙使用日志
        $result2 = true;
        if ($keyuselogid) {
            $data = array(
                'photologid' => $photologid
            );
            $result2 = D('Log')->saveKeyuselog($keyuselogid, $data);
        }

        if ($result1 && $result2) {
            $this->apiReturn(0, '关联成功！', array(
                'status' => 1
            ));
        } else {
            $this->apiReturn(1, '关联失败！', array(
                'status' => 0
            ));
        }
    }

    //关联视频日志
    public function linkvideolog()
    {
        $videologid = $this->_getVideologid();
        if (!$videologid) $this->apiReturn(1, '未知videologid！');

        $cabinetdoorlogid = $this->_getCabinetdoorlogid();
        $keyuselogid = $this->_getKeyuselogid();

        //关联钥匙柜开关门日志
        $result1 = true;
        if ($cabinetdoorlogid) {
            $data = array(
                'videologid' => $videologid
            );
            $result1 = D('Log')->saveCabinetdoorlog($cabinetdoorlogid, $data);
        }

        //关联钥匙使用日志
        $result2 = true;
        if ($keyuselogid) {
            $data = array(
                'videologid' => $videologid
            );
            $result2 = D('Log')->saveKeyuselog($keyuselogid, $data);
        }

        if ($result1 && $result2) {
            $this->apiReturn(0, '关联成功！', array(
                'status' => 1
            ));
        } else {
            $this->apiReturn(1, '关联失败！', array(
                'status' => 0
            ));
        }
    }

    //批量上传钥匙柜开/关门动作日志
    public function cabinetdoorlogupload()
    {
        // $departmentno = $this->_getDepartmentno();
        // if (!$departmentno) $this->apiReturn(1, '未知departmentno！');
        // $cabinetno = $this->_getCabinetno();
        // if (!$cabinetno) $this->apiReturn(1, '未知cabinetno！');

        $logs = mRequest('logs', false);
        if (!is_array($logs)||empty($logs)) $this->apiReturn(1, 'logs内容为空！');

        $departmentnos = array(0);
        $usernos = array(0);
        $usercardnos = array(0);
        $logdata = array();
        foreach ($logs as $log) {
            $departmentnos[] = $log['departmentno'];
            $usernos[] = $log['userno'];
            $usercardnos[] = $log['usercardno'];
            $logdata[] = array(
                'departmentno' => isset($log['departmentno']) ? $log['departmentno'] : 0,
                'cabinetno'    => isset($log['cabinetno']) ? $log['cabinetno'] : 0,
                'userno'       => isset($log['userno']) ? $log['userno'] : 0,
                'usercardno'   => isset($log['usercardno']) ? $log['usercardno'] : 0,
                'action'       => isset($log['action']) ? $log['action'] : 0,
                'alarm'        => isset($log['alarm']) ? $log['alarm'] : 0,
                'photologid'   => isset($log['photologid']) ? $log['photologid'] : '',
                'videologid'   => isset($log['videologid']) ? $log['videologid'] : '',
                'logtime'      => isset($log['logtime']) ? mkDateTime(strtotime($log['logtime'])) : mkDateTime(),
            );
        }

        //获取员工信息
        $userlist = D('User')->getUser(null, null, $departmentnos, $usernos, null, $usercardnos);
        $userlist = $userlist['data'];
        // if (!is_array($userlist) || empty($userlist)) $this->apiReturn(1, '未知警员信息！');

        $data = array();
        foreach ($logdata as $log) {
            //员工信息
            $userinfo = array();
            foreach ($userlist as $user) {
                if ($user['departmentno']==$log['departmentno'] && ($user['cardno']==$log['usercardno']||$user['userno']==$log['userno'])) {
                    $userinfo = $user;
                    break;
                }
            }

            //日志数据
            $data[] = array(
                'departmentno' => $log['departmentno'],
                'cabinetno'    => $log['cabinetno'],
                'userno'       => isset($userinfo['userno']) ? $userinfo['userno'] : 0,
                'username'     => isset($userinfo['username']) ? $userinfo['username'] : 0,
                'codeno'       => isset($userinfo['codeno']) ? $userinfo['codeno'] : 0,
                'action'       => $log['action'],
                'alarm'        => $log['alarm'],
                'photologid'   => $log['photologid'],
                'videologid'   => $log['videologid'],
                'logtime'      => $log['logtime'],
            );
        }
        $result = M("cabinetdoorlog")->addAll($data);
        if ($result) {
            $this->apiReturn(0, '上传成功！', array(
                'status' => 1
            ));
        } else {
            $this->apiReturn(1, '上传失败！', array(
                'status' => 0
            ));
        }
    }

    //批量上传钥匙领取/归还动作日志
    public function keyuselogupload()
    {
        // $departmentno = $this->_getDepartmentno();
        // if (!$departmentno) $this->apiReturn(1, '未知departmentno！');
        // $cabinetno = $this->_getCabinetno();
        // if (!$cabinetno) $this->apiReturn(1, '未知cabinetno！');

        $logs = mRequest('logs', false);
        if (!is_array($logs)||empty($logs)) $this->apiReturn(1, 'logs内容为空！');

        $departmentnos = array(0);
        $cabinetnos = array(0);
        $usernos = array(0);
        $keylocknos = array(0);
        $keycardids = array('');
        $logdata = array();
        foreach ($logs as $log) {
            $departmentnos[] = $log['departmentno'];
            $cabinetnos[] = $log['cabinetno'];
            $usernos[] = $log['userno'];
            $keylocknos[] = $log['keylockno'];
            $keycardids[] = $log['keycardid'];
            $logdata[] = array(
                'departmentno' => isset($log['departmentno']) ? $log['departmentno'] : 0,
                'cabinetno'    => isset($log['cabinetno']) ? $log['cabinetno'] : 0,
                'userno'       => isset($log['userno']) ? $log['userno'] : 0,
                'keylockno'    => isset($log['keylockno']) ? $log['keylockno'] : 0,
                'keycardid'    => isset($log['keycardid']) ? $log['keycardid'] : 0,
                'action'       => isset($log['action']) ? $log['action'] : 0,
                'actionflag'   => isset($log['actionflag']) ? $log['actionflag'] : 0,
                'photologid'   => isset($log['photologid']) ? $log['photologid'] : '',
                'videologid'   => isset($log['videologid']) ? $log['videologid'] : '',
                'logtime'      => isset($log['logtime']) ? mkDateTime(strtotime($log['logtime'])) : mkDateTime(),
            );
        }

        //获取员工信息
        $userlist = D('User')->getUser(null, null, $departmentnos, $usernos);
        $userlist = $userlist['data'];
        // if (!is_array($userlist) || empty($userlist)) $this->apiReturn(1, '未知警员信息！');

        //获取钥匙信息
        $keylist = array();
        $keylistc = array();
        if (is_array($keycardids)&&!empty($keycardids)) {
            $keylistc = D('Key')->getKey(null, null, null, null, null, null, null, $keycardids);
            $keylistc = $keylistc['data'];
        }
        $keylist = D('Key')->getKey(null, null, null, $departmentnos, $cabinetnos, null, $keylocknos);
        $keylist = array_merge($keylistc, $keylist['data']);
        if (!is_array($keylist) || empty($keylist)) $this->apiReturn(1, '未知钥匙信息！');

        $data = array();
        foreach ($logdata as $log) {
            //员工信息
            $userinfo = array();
            foreach ($userlist as $user) {
                if ($user['departmentno']==$log['departmentno'] && ($user['cardno']==$log['usercardno']||$user['userno']==$log['userno'])) {
                    $userinfo = $user;
                    break;
                }
            }

            //钥匙信息
            $keyinfo = array();
            foreach ($keylist as $key) {
                if ($key['departmentno']==$log['departmentno'] && $key['cabinetno']==$log['cabinetno'] && ($key['keyrfid']==$log['keycardid']||$key['keypos']==$log['keylockno'])) {
                    $keyinfo = $key;
                    break;
                }
            }

            //日志数据
            $data[] = array(
                'departmentno' => $log['departmentno'],
                'cabinetno'    => $log['cabinetno'],
                'keyno'        => isset($keyinfo['keyno']) ? $keyinfo['keyno'] : 0,
                'keypos'       => isset($keyinfo['keypos']) ? $keyinfo['keypos'] : 0,
                'keyrfid'      => isset($keyinfo['keyrfid']) ? $keyinfo['keyrfid'] : 0,
                'keyshowname'  => isset($keyinfo['keyshowname']) ? $keyinfo['keyshowname'] : 0,
                'userno'       => isset($userinfo['userno']) ? $userinfo['userno'] : 0,
                'username'     => isset($userinfo['username']) ? $userinfo['username'] : 0,
                'codeno'       => isset($userinfo['codeno']) ? $userinfo['codeno'] : 0,
                'action'       => $log['action'],
                'actionflag'   => $log['actionflag'],
                'photologid'   => $log['photologid'],
                'videologid'   => $log['videologid'],
                'keyposwrong'  => (int)$actionflag==2 ? $log['keylockno'] : 0,
                'logtime'      => $log['logtime'],
            );
        }
        $result = M("keyuselog")->addAll($data);
        if ($result) {
            $this->apiReturn(0, '上传成功！', array(
                'status' => 1
            ));
        } else {
            $this->apiReturn(1, '上传失败！', array(
                'status' => 0
            ));
        }
    }
}