<?php
/**
 * 钥匙业务逻辑
 * buzhidao
 * 2016-03-30
 */
namespace Admin\Controller;

use Any\Upload;

class KeyController extends CommonController
{
    //钥匙类型
    private $_keytypelist = array();

    public function __construct()
    {
        parent::__construct();

        //获取设备类型
        $this->_keytypelist = D('Key')->getKeyType();
        $this->assign('keytypelist', $this->_keytypelist);
    }

    //获取keytypeid
    private function _getKeytypeid()
    {
        $keytypeid = mRequest('keytypeid');
        $this->assign('keytypeid', $keytypeid);

        return $keytypeid;
    }

    //获取keytypename
    private function _getKeytypename()
    {
        $keytypename = mRequest('keytypename');
        $this->assign('keytypename', $keytypename);

        return $keytypename;
    }

    //获取keyname
    private function _getKeyname()
    {
        $keyname = mRequest('keyname');
        $this->assign('keyname', $keyname);

        return $keyname;
    }

    //获取keyshowname
    private function _getKeyshowname()
    {
        $keyshowname = mRequest('keyshowname');
        $this->assign('keyshowname', $keyshowname);

        return $keyshowname;
    }

    //获取keypos
    private function _getKeypos()
    {
        $keypos = mRequest('keypos');
        $this->assign('keypos', $keypos);

        return $keypos;
    }

    //获取keyrfid
    private function _getKeyrfid()
    {
        $keyrfid = mRequest('keyrfid');
        $this->assign('keyrfid', $keyrfid);

        return $keyrfid;
    }

    //获取usetimeflag
    private function _getUsetimeflag()
    {
        $usetimeflag = mRequest('usetimeflag');
        $this->assign('usetimeflag', $usetimeflag);

        return (int)$usetimeflag;
    }

    //获取usetime
    private function _getUsetime($usetimeflag=null)
    {
        $usetimebegin = mRequest('usetimebegin', false);
        $this->assign('usetimebegin', $usetimebegin);

        $usetimeend = mRequest('usetimeend', false);
        $this->assign('usetimeend', $usetimeend);

        $usetime = array();
        if ($usetimeflag) {
            if (is_array($usetimebegin)&&!empty($usetimebegin)) {
                foreach ($usetimebegin as $i=>$begin) {
                    $begintime = $begin;
                    $endtime   = $usetimeend[$i];

                    $ic = '';
                    switch ($i) {
                        case 1:
                            $ic = '一';
                            break;
                        case 2:
                            $ic = '二';
                            break;
                        case 3:
                            $ic = '三';
                            break;
                    }

                    //判断逻辑
                    if (($begintime&&!$endtime) || (!$begintime&&$endtime)) $this->ajaxReturn(1, '时间段'.$ic.'选择错误！');
                    if ($begintime && $endtime) {
                        if (strtotime('1970-01-01 '.$begintime.':00')>=strtotime('1970-01-01 '.$endtime.':00')) $this->ajaxReturn(1, '时间段'.$ic.'选择错误！');
                        $usetime[$i] = array(
                            'begintime' => $begintime,
                            'endtime'   => $endtime
                        );
                    }
                }
            }
        }

        return $usetime;
    }

    //获取returntimeflag
    private function _getReturntimeflag()
    {
        $returntimeflag = mRequest('returntimeflag');
        $this->assign('returntimeflag', $returntimeflag);

        return (int)$returntimeflag;
    }

    //获取returntime
    private function _getReturntime()
    {
        $returntime = mRequest('returntime');
        $this->assign('returntime', $returntime);

        return (int)$returntime;
    }

    //获取状态
    private function _getKeystatus()
    {
        $keystatus = mRequest('keystatus');
        $this->assign('keystatus', $keystatus);

        return (int)$keystatus;
    }

    public function index(){}

    //钥匙类型
    public function keytype()
    {
        $this->display();
    }

    //ajax获取钥匙类型html详情
    public function ajaxGetKeyTypeHtml()
    {
        $keytypeid = $this->_getKeytypeid();

        $keytypeinfo = $this->_keytypelist[$keytypeid];
        $this->assign('keytypeinfo', $keytypeinfo);

        $html = $this->fetch('Key/keytype_modal');

        $this->ajaxReturn(0, '', array(
            'html' => $html
        ));
    }

    //保存钥匙类型信息
    public function keytypesave()
    {
        $keytypeid = $this->_getKeytypeid();
        $keytypename = $this->_getKeytypename();

        $data = array(
            'keytypename' => $keytypename
        );
        $result = D('Key')->savekeytype($keytypeid, $data);
        if ($result) {
            $this->ajaxReturn(0, '保存成功！');
        } else {
            $this->ajaxReturn(1, '保存失败！');
        }
    }

    //新增钥匙
    public function newkey()
    {
        $this->display();
    }

    //新增钥匙 - 保存
    public function newkeysave()
    {
        $keyname = $this->_getKeyname();
        if (!$keyname) $this->ajaxReturn(1, '请填写钥匙名称！');

        $keytypeid = $this->_getKeytypeid();
        if (!$keytypeid) $this->ajaxReturn(1, '请选择钥匙类型！');

        $keyshowname = $this->_getKeyshowname();
        if (!$keyshowname) $this->ajaxReturn(1, '请填写车牌号码！');

        $departmentno = $this->_getDepartmentno();
        if (!$departmentno) $this->ajaxReturn(1, '请选择派出所！');

        $cabinetno = $this->_getCabinetno();
        if (!$cabinetno) $this->ajaxReturn(1, '请选择所在钥匙柜！');

        $keypos = $this->_getKeypos();
        if (!$keypos) $this->ajaxReturn(1, '请填写钥匙位置！');

        $keyrfid = $this->_getKeyrfid();
        if (!$keyrfid) $this->ajaxReturn(1, '请填写钥匙RFID标签码！');

        $usetimeflag = $this->_getUsetimeflag();
        $usetime = $this->_getUsetime($usetimeflag);
        if ($usetimeflag && empty($usetime)) $this->ajaxReturn(1, '请选择领取时限（时间段）！');

        $returntimeflag = $this->_getReturntimeflag();
        $returntime = $this->_getReturntime();
        if ($returntimeflag && !$returntime) $this->ajaxReturn(1, '请填写归还时限（小时）！');
        !$returntimeflag ? $returntime = 0 : null;

        $keystatus = $this->_getKeystatus();

        //计算设备编号
        $maxKeyno = D('Key')->getMaxKeyno($departmentno, $cabinetno);

        $keyno = $maxKeyno+1;
        $keyid = guid();
        $data = array(
            'keyid'          => $keyid,
            'keyname'        => $keyname,
            'keyno'          => $keyno,
            'keytypeid'      => $keytypeid,
            'keyshowname'    => $keyshowname,
            'departmentno'   => $departmentno,
            'cabinetno'      => $cabinetno,
            'keypos'         => $keypos,
            'keyrfid'        => $keyrfid,
            'keystatus'      => $keystatus,
            'usetimeflag'    => $usetimeflag,
            'returntimeflag' => $returntimeflag,
            'returntime'     => $returntime,
            'keyposcurrent'  => $keypos,
            'createtime'     => mkDateTime(),
            'updatetime'     => mkDateTime(),
        );
        $result = D('Key')->savekey(null, $data);
        if ($result) {
            //保存钥匙领取时限信息
            if ($usetimeflag) {
                $utdata = array();
                foreach ($usetime as $ut) {
                    $utdata[] = array(
                        'keyid'     => $keyid,
                        'begintime' => $ut['begintime'],
                        'endtime'   => $ut['endtime'],
                    );
                }
                D('Key')->savekeyusetime($keyid, $utdata);
            }

            $this->ajaxReturn(0, '保存成功！');
        } else {
            $this->ajaxReturn(1, '保存失败！');
        }
    }

    //管理钥匙
    public function keylist()
    {
        
    }
}