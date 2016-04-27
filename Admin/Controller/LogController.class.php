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
        $username = $this->_getUsername();

        $begintime = $this->_getBegintime();
        $endtime = $this->_getEndtime();

        list($start, $length) = $this->_mkPage();
        $data = D('Log')->getKeyuseLog(null, $departmentno, $cabinetno, null, null, $username, null, null, $begintime, $endtime, $start, $length, $keyname);
        $total = $data['total'];
        $datalist = $data['data'];

        $this->assign('total', $total);
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
        $this->assign('params', $params);
        //解析分页数据
        $this->_mkPagination($total, $params);

        $paramstr = null;
        foreach ($params as $key=>$value) {
            $paramstr .= '&'.$key.'='.$value;
        }
        $this->assign('paramstr', $paramstr);

        $this->display();
    }

    //钥匙使用日志 详情
    public function keyuselogdetail()
    {

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

        $this->assign('total', $total);
        $this->assign('datalist', $datalist);

        $params = array(
            'subcompanyno' => $subcompanyno,
            'departmentno' => $departmentno,
            'cabinetno'    => $cabinetno,
            'username'     => $username,
            'begintime'    => $begintime,
            'endtime'      => $endtime,
        );
        $this->assign('params', $params);
        //解析分页数据
        $this->_mkPagination($total, $params);

        $paramstr = null;
        foreach ($params as $key=>$value) {
            $paramstr .= '&'.$key.'='.$value;
        }
        $this->assign('paramstr', $paramstr);

        $this->display();
    }

    //播放视频
    public function videoplay()
    {
        
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

        if ($exportaction == 'keyuselog') {
            $title = '智能钥匙柜_钥匙使用日志';

            $subcompanyno = $this->_getSubcompanyno();
            $departmentno = $this->_getDepartmentno();
            if ($departmentno) {
                $cabinetlist = D('Cabinet')->getCabinet(null, null, $departmentno);
                $this->assign('cabinetlist', $cabinetlist['data']);
            }
            $cabinetno = $this->_getCabinetno();
            //钥匙名称、显示名称
            $keyname = $this->_getKeyname();
            $username = $this->_getUsername();

            $begintime = $this->_getBegintime();
            $endtime = $this->_getEndtime();

            $data = D('Log')->getKeyuseLog(null, $departmentno, $cabinetno, null, null, $username, null, null, $begintime, $endtime, 0, 9999, $keyname);
            $datalist = $data['data'];

            //设置当前活动sheet的名称       
            $objActSheet->setTitle($title);

            //设置宽度，这个值和EXCEL里的不同，不知道是什么单位，略小于EXCEL中的宽度
            $objActSheet->getColumnDimension('A')->setWidth(20);
            $objActSheet->getColumnDimension('B')->setWidth(15);
            $objActSheet->getColumnDimension('C')->setWidth(10);
            $objActSheet->getColumnDimension('D')->setWidth(25);
            $objActSheet->getColumnDimension('E')->setWidth(17);
            $objActSheet->getColumnDimension('F')->setWidth(12);
            $objActSheet->getColumnDimension('G')->setWidth(15);
            $objActSheet->getColumnDimension('H')->setWidth(15);
            $objActSheet->getColumnDimension('I')->setWidth(20);

            //设置单元格的值
            // $objActSheet->setCellValue('A1', '总标题显示');

            //设置表格标题栏内容
            $objActSheet->setCellValue('A1', '钥匙名称');
            $objActSheet->setCellValue('B1', '钥匙柜');
            $objActSheet->setCellValue('C1', '锁位置');
            $objActSheet->setCellValue('D1', '派出所');
            $objActSheet->setCellValue('E1', '标签号');
            $objActSheet->setCellValue('F1', '动作');
            $objActSheet->setCellValue('G1', '警员名称');
            $objActSheet->setCellValue('H1', '警员编号');
            $objActSheet->setCellValue('I1', '记录时间');
            
            //遍历数据
            $n = 2;
            foreach ($datalist as $d) {
                $action = $d['action'] ? '归还' : '领取';
                if ($d['actionflag'] == 1) {
                    $action = '异常领取';
                } else if ($d['actionflag'] == 2) {
                    $action = '归还错位';
                }

                $objActSheet->setCellValue('A'.$n, $d["keyname"]);
                $objActSheet->setCellValue('B'.$n, $d["cabinetname"]);
                $objActSheet->setCellValue('C'.$n, $d["keypos"]);
                $objActSheet->setCellValue('D'.$n, $d['departmentname']);
                $objActSheet->setCellValue('E'.$n, $d['keyrfid']);
                $objActSheet->setCellValue('F'.$n, $action);
                $objActSheet->setCellValue('G'.$n, $d["username"]);
                $objActSheet->setCellValue('H'.$n, $d["codeno"]);
                $objActSheet->setCellValue('I'.$n, substr($d["logtime"], 0, 19));

                $n++;
            }
        }

        if ($exportaction == 'cabinetdoorlog') {
            $title = '智能钥匙柜_钥匙柜开关门日志';

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

            $data = D('Log')->getCabinetdoorLog(null, $departmentno, $cabinetno, null, $username, null, null, $begintime, $endtime);
            $datalist = $data['data'];

            //设置当前活动sheet的名称       
            $objActSheet->setTitle($title);

            //设置宽度，这个值和EXCEL里的不同，不知道是什么单位，略小于EXCEL中的宽度
            $objActSheet->getColumnDimension('A')->setWidth(20);
            $objActSheet->getColumnDimension('B')->setWidth(10);
            $objActSheet->getColumnDimension('C')->setWidth(25);
            $objActSheet->getColumnDimension('D')->setWidth(10);
            $objActSheet->getColumnDimension('E')->setWidth(15);
            $objActSheet->getColumnDimension('F')->setWidth(15);
            $objActSheet->getColumnDimension('G')->setWidth(15);
            $objActSheet->getColumnDimension('H')->setWidth(20);

            //设置单元格的值
            // $objActSheet->setCellValue('A1', '总标题显示');

            //设置表格标题栏内容
            $objActSheet->setCellValue('A1', '钥匙柜名称');
            $objActSheet->setCellValue('B1', '编号');
            $objActSheet->setCellValue('C1', '派出所');
            $objActSheet->setCellValue('D1', '动作');
            $objActSheet->setCellValue('E1', '报警');
            $objActSheet->setCellValue('F1', '警员名称');
            $objActSheet->setCellValue('G1', '警员编号');
            $objActSheet->setCellValue('H1', '记录时间');
            
            //遍历数据
            $n = 2;
            foreach ($datalist as $d) {
                $action = $d['action'] ? '关门' : '开门';
                $alarm = '无';
                if ($d['alarm'] == 1) {
                    $alarm = '异常开门';
                } else if ($d['actionflag'] == 2) {
                    $alarm = '长时间未关门';
                }

                $objActSheet->setCellValue('A'.$n, $d["cabinetname"]);
                $objActSheet->setCellValue('B'.$n, $d["cabinetno"]);
                $objActSheet->setCellValue('C'.$n, $d["departmentname"]);
                $objActSheet->setCellValue('D'.$n, $action);
                $objActSheet->setCellValue('E'.$n, $alarm);
                $objActSheet->setCellValue('F'.$n, $d["username"]);
                $objActSheet->setCellValue('G'.$n, $d["codeno"]);
                $objActSheet->setCellValue('H'.$n, substr($d["logtime"], 0, 19));

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