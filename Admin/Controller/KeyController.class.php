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

    //获取keyid
    private function _getKeyid()
    {
        $keyid = mRequest('keyid');
        $this->assign('keyid', $keyid);

        return $keyid;
    }

    //获取keyname
    private function _getKeyname()
    {
        $keyname = mRequest('keyname');
        if (mb_strlen($keyname, 'UTF-8')>10) $this->ajaxReturn(1, '钥匙名称10个字以内！');

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

    //获取carid
    private function _getCarid()
    {
        $carid = mRequest('carid');
        $this->assign('carid', $carid);

        return $carid;
    }

    //获取carname
    private function _getCarname()
    {
        $carname = mRequest('carname');
        $this->assign('carname', $carname);

        return $carname;
    }

    //获取brand
    private function _getBrand()
    {
        $brand = mRequest('brand');
        $this->assign('brand', $brand);

        return $brand;
    }

    //获取modelv
    private function _getModelv()
    {
        $modelv = mRequest('modelv');
        $this->assign('modelv', $modelv);

        return $modelv;
    }

    //获取parkplace
    private function _getParkplace()
    {
        $parkplace = mRequest('parkplace');
        if (mb_strlen($parkplace, 'UTF-8')>20) $this->ajaxReturn(1, '错误：车辆停放位置超过20汉字！');
        $this->assign('parkplace', $parkplace);

        return $parkplace;
    }

    //获取insurephoto
    private function _getInsurephoto()
    {
        $insurephoto = mRequest('insurephoto');
        $this->assign('insurephoto', $insurephoto);

        return $insurephoto;
    }

    //获取insureexpiretime
    private function _getInsureexpiretime()
    {
        $insureexpiretime = mRequest('insureexpiretime');
        $this->assign('insureexpiretime', $insureexpiretime);

        return $insureexpiretime;
    }

    //获取insureperson
    private function _getInsureperson()
    {
        $insureperson = mRequest('insureperson');
        $this->assign('insureperson', $insureperson);

        return $insureperson;
    }

    //获取currentkilometer
    private function _getCurrentkilometer()
    {
        $currentkilometer = mRequest('currentkilometer');
        $this->assign('currentkilometer', $currentkilometer);

        return $currentkilometer;
    }

    //获取repairkilometer
    private function _getRepairkilometer()
    {
        $repairkilometer = mRequest('repairkilometer');
        $this->assign('repairkilometer', $repairkilometer);

        return $repairkilometer;
    }

    //获取lastrepairtime
    private function _getLastrepairtime()
    {
        $lastrepairtime = mRequest('lastrepairtime');
        $this->assign('lastrepairtime', $lastrepairtime);

        return $lastrepairtime;
    }

    //获取repairperiodtime
    private function _getRepairperiodtime()
    {
        $repairperiodtime = mRequest('repairperiodtime');
        $this->assign('repairperiodtime', $repairperiodtime);

        return $repairperiodtime;
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

    //保单图片上传
    public function insurephotoupload()
    {
        //初始化上传类
        $Upload = new Upload();
        $Upload->maxSize  = 500*1024;
        $Upload->exts     = array('jpg', 'gif', 'png', 'jpeg');
        $Upload->rootPath = UPLOAD_PATH;
        $Upload->savePath = 'Key/car/';
        $Upload->saveName = array('uniqid', array('', true));
        $Upload->autoSub  = true;
        $Upload->subName  = array('date', 'Ym');

        //上传
        $error = null;
        $msg = '上传成功！';
        $data = array();
        $info = $Upload->upload();
        if (!$info) {
            $error = 1;
            $msg = $Upload->getError();
        } else {
            $fileinfo = array_shift($info);
            $data = array(
                'filepath' => '/'.UPLOAD_PT.$fileinfo['savepath'],
                'filename' => $fileinfo['savename'],
            );
        }

        $this->ajaxReturn($error, $msg, $data);
    }

    //获取车辆信息
    private function _getCardata($carid=null, $departmentno=null, $cabinetno=null, $keyno=null)
    {
        $carname = $this->_getCarname();
        // if (!$carname) $this->ajaxReturn(1, '请填写车辆名称！');

        $brand = $this->_getBrand();
        $modelv = $this->_getModelv();

        $parkplace = $this->_getParkplace();
        // if (!$parkplace) $this->ajaxReturn(1, '请填写车辆停放位置！');

        $insurephoto = $this->_getInsurephoto();
        $insureexpiretime = $this->_getInsureexpiretime();
        $insureperson = $this->_getInsureperson();
        $currentkilometer = $this->_getCurrentkilometer();
        $repairkilometer = $this->_getRepairkilometer();
        $lastrepairtime = $this->_getLastrepairtime();
        $repairperiodtime = $this->_getRepairperiodtime();

        //保存车辆信息
        $cardata = array(
            'carname'          => $carname,
            'brand'            => $brand,
            'modelv'           => $modelv,
            'parkplace'        => $parkplace,
            'insurephoto'      => $insurephoto,
            'insureperson'     => $insureperson,
            'currentkilometer' => $currentkilometer ? (int)$currentkilometer : 0,
            'repairkilometer'  => $repairkilometer ? (int)$repairkilometer : 0,
            'repairperiodtime' => $repairperiodtime ? (int)$repairperiodtime : 0,
        );
        if ($insureexpiretime) $cardata['insureexpiretime'] = $insureexpiretime;
        if ($lastrepairtime) $cardata['lastrepairtime'] = $lastrepairtime;

        if (!$carid) {
            $carid = guid();
            $cardata = array_merge($cardata, array(
                'carid'        => $carid,
                'departmentno' => $departmentno,
                'cabinetno'    => $cabinetno,
                'keyno'        => $keyno,
                'createtime'   => mkDateTime(),
            ));
        }

        return $cardata;
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
        if (!$departmentno) $this->ajaxReturn(1, '请选择'.L('WordLang.DepartmentLang').'！');

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

        //获取车辆信息
        $cardata = $this->_getCardata(null, $departmentno, $cabinetno, $keyno);

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
            'isdelete'       => 0,
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

            //保存车辆信息
            D('Key')->savecar(null, $cardata);

            $this->ajaxReturn(0, '保存成功！');
        } else {
            $this->ajaxReturn(1, '保存失败！');
        }
    }

    //管理钥匙
    public function keylist()
    {
        $subcompanyno = $this->_getSubcompanyno();
        $departmentno = $this->_getDepartmentno();
        if ($departmentno) {
            $cabinetlist = D('Cabinet')->getCabinet(null, null, $departmentno);
            $this->assign('cabinetlist', $cabinetlist['data']);
        }
        $cabinetno = $this->_getCabinetno();
        $keytypeid = $this->_getKeytypeid();
        $keyname = $this->_getKeyname();

        list($start, $length) = $this->_mkPage();
        $data = D('Key')->getKey(null, $keytypeid, $keyname, $departmentno, $cabinetno, null, null, null, $start, $length);
        $total = $data['total'];
        $datalist = $data['data'];

        $this->assign('datalist', $datalist);

        $params = array(
            'subcompanyno' => $subcompanyno,
            'departmentno' => $departmentno,
            'cabinetno' => $cabinetno,
            'keytypeid' => $keytypeid,
            'keyname' => $keyname,
        );
        $this->assign('params', $params);
        //解析分页数据
        $this->_mkPagination($total, $params);

        $jumpurl = urlencode(str_replace('&', '|||||', $this->pagination['url'].$this->pagination['curtpage']));
        $this->assign('jumpurl', $jumpurl);

        $paramstr = null;
        foreach ($params as $key=>$value) {
            $paramstr .= '&'.$key.'='.$value;
        }
        $this->assign('paramstr', $paramstr);

        $this->display();
    }

    //编辑钥匙信息
    public function upkey()
    {
        $keyid = $this->_getKeyid();
        if (!$keyid) exit;

        $jumpurl = mRequest('jumpurl');
        $this->assign('jumpurl', $jumpurl);

        $keyinfo = D('Key')->getKeyByID($keyid);

        //subcompanyno
        foreach ($this->company['subcompany'] as $subcompany) {
            if (isset($subcompany['department'])) {
                foreach ($subcompany['department'] as $department) {
                    if ($department['departmentno'] == $keyinfo['departmentno']) {
                        $keyinfo['subcompanyno'] = $subcompany['subcompanyno'];
                        break(2);
                    }
                }
            }
        }

        //cabinetlist
        $cabinetlist = D('Cabinet')->getCabinet(null, null, $keyinfo['departmentno']);
        $this->assign('cabinetlist', $cabinetlist['data']);

        $this->assign('subcompanyno', $keyinfo['subcompanyno']);
        $this->assign('departmentno', $keyinfo['departmentno']);
        $this->assign('cabinetno', $keyinfo['cabinetno']);

        $this->assign('keyinfo', $keyinfo);
        $this->display();
    }

    //编辑钥匙信息
    public function upkeysave()
    {
        $keyid = $this->_getKeyid();
        if (!$keyid) $this->ajaxReturn(1, '请选择钥匙信息！');

        $jumpurl = mRequest('jumpurl');

        //获取钥匙信息
        $keyinfo = D('Key')->getKeyByID($keyid);
        if (!is_array($keyinfo)) $this->ajaxReturn(1, '请选择钥匙信息！');

        $keyname = $this->_getKeyname();
        if (!$keyname) $this->ajaxReturn(1, '请填写钥匙名称！');

        $keytypeid = $this->_getKeytypeid();
        if (!$keytypeid) $this->ajaxReturn(1, '请选择钥匙类型！');

        $keyshowname = $this->_getKeyshowname();
        if (!$keyshowname) $this->ajaxReturn(1, '请填写车牌号码！');

        $keyrfid = $this->_getKeyrfid();
        if (!$keyrfid) $this->ajaxReturn(1, '请填写钥匙RFID标签码！');

        $usetimeflag = $this->_getUsetimeflag();
        $usetime = $this->_getUsetime($usetimeflag);
        if ($usetimeflag && empty($usetime)) $this->ajaxReturn(1, '请选择领取时限（时间段）！');

        $returntimeflag = $this->_getReturntimeflag();
        $returntime = $this->_getReturntime();
        if ($returntimeflag && !$returntime) $this->ajaxReturn(1, '请填写归还时限（小时）！');
        !$returntimeflag ? $returntime = 0 : null;

        //获取carid
        $carid = $this->_getCarid();
        //获取车辆信息
        $cardata = $this->_getCardata($carid, $keyinfo['departmentno'], $keyinfo['cabinetno'], $keyinfo['keyno']);

        $data = array(
            'keyname'        => $keyname,
            'keytypeid'      => $keytypeid,
            'keyshowname'    => $keyshowname,
            'keyrfid'        => $keyrfid,
            'usetimeflag'    => $usetimeflag,
            'returntimeflag' => $returntimeflag,
            'returntime'     => $returntime,
            'updatetime'     => mkDateTime(),
        );
        $result = D('Key')->savekey($keyid, $data);
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

            //保存车辆信息
            D('Key')->savecar($carid, $cardata);

            $this->ajaxReturn(0, '保存成功！', array(
                'location' => str_replace('|||||', '&', $jumpurl)
            ));
        } else {
            $this->ajaxReturn(1, '保存失败！');
        }
    }

    //车辆信息
    public function car()
    {
        $keyid = $this->_getKeyid();
        if (!$keyid) exit;

        $jumpurl = mRequest('jumpurl');
        $this->assign('jumpurl', $jumpurl);

        $keyinfo = D('Key')->getKeyByID($keyid);

        $this->assign('keyinfo', $keyinfo);
        $this->display();
    }

    //车辆信息-保存
    public function carsave()
    {
        $keyid = $this->_getKeyid();
        if (!$keyid) $this->ajaxReturn(1, '未知车辆信息！');

        $jumpurl = mRequest('jumpurl');

        //获取钥匙信息
        $keyinfo = D('Key')->getKeyByID($keyid);
        if (!is_array($keyinfo)) $this->ajaxReturn(1, '未知车辆信息！');

        $carid = $this->_getCarid();
        if (!$carid) $this->ajaxReturn(1, '未知车辆信息！');

        //获取车辆信息
        $cardata = $this->_getCardata($carid, $keyinfo['departmentno'], $keyinfo['cabinetno'], $keyinfo['keyno']);

        //保存车辆信息
        $result = D('Key')->savecar($carid, $cardata);
        if ($result) {
            $this->ajaxReturn(0, '保存成功！');
        } else {
            $this->ajaxReturn(1, '保存失败！');
        }
    }

    //删除钥匙-isdelete=1
    public function deletekey()
    {
        $keyid = $this->_getKeyid();
        if (!$keyid) $this->ajaxReturn(1, '请选择钥匙信息！');

        $result = M('keys')->where(array('keyid'=>$keyid))->save(array('isdelete'=>1));
        if ($result) {
            $this->ajaxReturn(0, '删除成功！');
        } else {
            $this->ajaxReturn(1, '删除失败！');
        }
    }

    //ajax获取keys
    public function ajaxGetKey()
    {
        $departmentno = $this->_getDepartmentno();
        if (!$departmentno) $this->ajaxReturn(1, '请选择'.L('WordLang.DepartmentLang').'！');

        $cabinetno = $this->_getCabinetno();

        $keylist = D('Key')->getKey(null, null, null, $departmentno, $cabinetno);
        $keylist = $keylist['data'];

        $this->ajaxReturn(0, '', array(
            'keylist' => $keylist
        ));
    }

    //导出Excel
    public function export()
    {
        $exportaction = $this->_getExportaction();

        require VENDOR_PATH.'PHPExcel/PHPExcel.php';

        // 创建一个处理对象实例
        $objPHPExcel = new \PHPExcel();

        //设置当前的sheet索引，用于后续的内容操作。
        $objPHPExcel->setActiveSheetIndex(0);       
        $objActSheet = $objPHPExcel->getActiveSheet();

        if ($exportaction == 'key') {
            $title = '智能钥匙柜_钥匙信息';

            $subcompanyno = $this->_getSubcompanyno();
            $departmentno = $this->_getDepartmentno();
            if ($departmentno) {
                $cabinetlist = D('Cabinet')->getCabinet(null, null, $departmentno);
                $this->assign('cabinetlist', $cabinetlist['data']);
            }
            $cabinetno = $this->_getCabinetno();
            $keytypeid = $this->_getKeytypeid();
            $keyname = $this->_getKeyname();

            $data = D('Key')->getKey(null, $keytypeid, $keyname, $departmentno, $cabinetno);
            $datalist = $data['data'];

            //设置当前活动sheet的名称       
            $objActSheet->setTitle($title);

            //设置宽度，这个值和EXCEL里的不同，不知道是什么单位，略小于EXCEL中的宽度
            $objActSheet->getColumnDimension('A')->setWidth(15);
            $objActSheet->getColumnDimension('B')->setWidth(10);
            $objActSheet->getColumnDimension('C')->setWidth(20);
            $objActSheet->getColumnDimension('D')->setWidth(20);
            $objActSheet->getColumnDimension('E')->setWidth(15);
            $objActSheet->getColumnDimension('F')->setWidth(17);
            $objActSheet->getColumnDimension('G')->setWidth(25);

            //设置单元格的值
            // $objActSheet->setCellValue('A1', '总标题显示');
            
            //设置表格标题栏内容
            $objActSheet->setCellValue('A1', '钥匙柜');
            $objActSheet->setCellValue('B1', '锁位置');
            $objActSheet->setCellValue('C1', '钥匙名称');
            $objActSheet->setCellValue('D1', '显示名称');
            $objActSheet->setCellValue('E1', '类型');
            $objActSheet->setCellValue('F1', '标签号');
            $objActSheet->setCellValue('G1', ''.L('WordLang.DepartmentLang').'');
            
            //遍历数据
            $n = 2;
            foreach ($datalist as $d) {
                $objActSheet->setCellValue('A'.$n, $d["cabinetname"]);
                $objActSheet->setCellValue('B'.$n, $d["keypos"]);
                $objActSheet->setCellValue('C'.$n, $d["keyname"]);
                $objActSheet->setCellValue('D'.$n, $d['keyshowname']);
                $objActSheet->setCellValue('E'.$n, $this->_keytypelist[$d["keytypeid"]]['keytypename']);
                $objActSheet->setCellValue('F'.$n, $d["keyrfid"]);
                $objActSheet->setCellValue('G'.$n, $d["departmentname"]);

                $n++;
            }
        }

        //输出内容
        $outputFileName = $title.'_'.date('Ymd_His', TIMESTAMP).".xlsx";

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');

        //到文件
        // $objWriter->save($outputFileName);
        
        //到浏览器
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");
        header('Content-Disposition:inline;filename="'.iconv('UTF-8', 'GB2312', $outputFileName).'"');
        header("Content-Transfer-Encoding: binary");
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Pragma: no-cache");
        $objWriter->save("php://output");
        exit;
    }
}