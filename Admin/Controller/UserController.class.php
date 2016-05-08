<?php
/**
 * 员工管理模块
 * buzhidao
 * 2016-03-27
 */
namespace Admin\Controller;

use Any\Upload;

class UserController extends CommonController
{
    public function __construct()
    {
        parent::__construct();
    }

    //获取userid
    private function _getUserid()
    {
        $userid = mRequest('userid');
        $this->assign('userid', $userid);

        return $userid;
    }

    //获取员工名称
    private function _getUsername()
    {
        $username = mRequest('username');
        $this->assign('username', $username);

        return $username;
    }

    //获取员工编号
    private function _getCodeno()
    {
        $codeno = mRequest('codeno');
        $this->assign('codeno', $codeno);

        return $codeno;
    }

    //获取员工卡号
    private function _getCardno()
    {
        $cardno = mRequest('cardno');
        $this->assign('cardno', $cardno);

        return $cardno;
    }

    //获取员工手机号
    private function _getPhone()
    {
        $phone = mRequest('phone');
        $this->assign('phone', $phone);

        return $phone;
    }

    //获取员工名称
    private function _getPosition()
    {
        $position = mRequest('position');
        $this->assign('position', $position);

        return $position;
    }

    //获取员工照片
    private function _getPhoto()
    {
        $photo = mRequest('photo');
        $this->assign('photo', $photo);

        if (!$photo) $photo = '/Public/images/user/user_avatar_default.jpg';

        return $photo;
    }

    //获取员工控制权限
    private function _getAccess()
    {
        $access = mRequest('access');
        $this->assign('access', $access);

        return $access;
    }

    //获取员工状态
    private function _getStatus()
    {
        $status = mRequest('status');
        $this->assign('status', $status);

        return (int)$status;
    }

    public function index(){}

    //录入员工信息
    public function newuser()
    {
        $this->display();
    }

    //员工头像上传
    public function photoupload()
    {
        //初始化上传类
        $Upload = new Upload();
        $Upload->maxSize  = 50*1024;
        $Upload->exts     = array('jpg', 'gif', 'png', 'jpeg');
        $Upload->rootPath = UPLOAD_PATH;
        $Upload->savePath = 'User/photo/';
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

    //保存员工信息
    public function newusersave()
    {
        $username = $this->_getUsername();
        if (!$username) $this->ajaxReturn(1, '请填写'.L('WordLang.UserLang').'名称！');

        $codeno = $this->_getCodeno();
        if (!$codeno) $this->ajaxReturn(1, '请填写'.L('WordLang.UserLang').'编号！');
        //查询'.L('WordLang.UserLang').'编号是否已存在
        if (D("User")->ckUserCodenoExists(null, $codeno)) $this->ajaxReturn(1, '该'.L('WordLang.UserLang').'编号已存在！');

        $cardno = $this->_getCardno();
        $phone = $this->_getPhone();
        $position = $this->_getPosition();
        $photo = $this->_getPhoto();
        $access = $this->_getAccess();
        $status = $this->_getStatus();

        $subcompanyno = $this->_getSubcompanyno();
        $departmentno = $this->_getDepartmentno();
        if (!$departmentno) $this->ajaxReturn(1, '请选择'.L('WordLang.DepartmentLang').'！');

        //获取部门信息
        $departmentinfo = D('Company')->getDepartmentByNO($departmentno);
        $userno = $departmentinfo['maxuserno']+1;

        $userid = guid();
        $data = array(
            'userid'   => $userid,
            'userno'   => $userno,
            'username' => $username,
            'codeno'   => $codeno,
            'cardno'   => $cardno,
            'phone'    => $phone,
            'departmentno' => $departmentno,
            'position' => $position,
            'photo'    => $photo,
            'access'   => $access,
            'status'   => 1,
            'createtime' => mkDateTime(),
            'updatetime' => mkDateTime()
        );
        $result = D('User')->saveuser(null, $data);
        if ($result) {
            //更新部门信息 - maxuserno
            D('Company')->savedepartment($departmentinfo['departmentid'], array(
                'maxuserno' => $userno
            ));

            $this->ajaxReturn(0, '保存成功！');
        } else {
            $this->ajaxReturn(1, '保存失败！');
        }
    }

    //导入员工信息
    public function importuser()
    {
        $this->display();
    }

    //管理员工
    public function userlist()
    {
        $subcompanyno = $this->_getSubcompanyno();
        $departmentno = $this->_getDepartmentno();

        $username = $this->_getUsername();

        list($start, $length) = $this->_mkPage();
        $data = D('User')->getUser(null, $username, $departmentno, null, null, null, $start, $length);
        $total = $data['total'];
        $datalist = $data['data'];

        $this->assign('datalist', $datalist);

        $params = array(
            'subcompanyno' => $subcompanyno,
            'departmentno' => $departmentno,
            'username' => $username,
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

    //编辑员工信息
    public function upuser()
    {
        $userid = $this->_getUserid();
        if (!$userid) exit;

        $userinfo = D('User')->getUserByID($userid);
        if (!is_array($userinfo) || empty($userinfo)) exit;

        //subcompanyno
        foreach ($this->company['subcompany'] as $subcompany) {
            if (isset($subcompany['department'])) {
                foreach ($subcompany['department'] as $department) {
                    if ($department['departmentno'] == $userinfo['departmentno']) {
                        $userinfo['subcompanyno'] = $subcompany['subcompanyno'];
                        break(2);
                    }
                }
            }
        }

        $this->assign('subcompanyno', $userinfo['subcompanyno']);
        $this->assign('departmentno', $userinfo['departmentno']);

        $this->assign('userinfo', $userinfo);
        $this->display();
    }

    //编辑员工信息-保存
    public function upusersave()
    {
        $userid = $this->_getUserid();
        if (!$userid) $this->ajaxReturn(1, '未知'.L('WordLang.UserLang').'信息！');

        $username = $this->_getUsername();
        if (!$username) $this->ajaxReturn(1, '请填写'.L('WordLang.UserLang').'名称！');

        $codeno = $this->_getCodeno();
        if (!$codeno) $this->ajaxReturn(1, '请填写'.L('WordLang.UserLang').'编号！');
        //查询'.L('WordLang.UserLang').'编号是否已存在
        if (D("User")->ckUserCodenoExists($userid, $codeno)) $this->ajaxReturn(1, '该'.L('WordLang.UserLang').'编号已存在！');

        $cardno = $this->_getCardno();
        $phone = $this->_getPhone();
        $position = $this->_getPosition();
        $photo = $this->_getPhoto();
        $access = $this->_getAccess();

        $subcompanyno = $this->_getSubcompanyno();
        $departmentno = $this->_getDepartmentno();
        if (!$departmentno) $this->ajaxReturn(1, '请选择'.L('WordLang.DepartmentLang').'！');

        //获取部门信息
        $departmentinfo = D('Company')->getDepartmentByNO($departmentno);

        $data = array(
            'username' => $username,
            'codeno'   => $codeno,
            'cardno'   => $cardno,
            'phone'    => $phone,
            'departmentno' => $departmentno,
            'position' => $position,
            'photo'    => $photo,
            'access'   => $access,
            'updatetime' => mkDateTime()
        );
        $result = D('User')->saveuser($userid, $data);
        if ($result) {
            $this->ajaxReturn(0, '保存成功！');
        } else {
            $this->ajaxReturn(1, '保存失败！');
        }
    }

    //删除员工
    public function deleteuser()
    {
        $userid = $this->_getUserid();
        if (!$userid) $this->ajaxReturn(1, '未知'.L('WordLang.UserLang').'信息！');

        $result = M('user')->where(array('userid'=>$userid))->save(array('status'=>0));
        if ($result) {
            $this->ajaxReturn(0, '删除成功！');
        } else {
            $this->ajaxReturn(1, '删除失败！');
        }
    }

    //AJAX获取'.L('WordLang.UserLang').'信息
    public function ajaxGetUser()
    {
        $departmentno = $this->_getDepartmentno();
        if (!$departmentno) $this->ajaxReturn(1, '钥匙柜信息错误！');

        $userlist = D('User')->getUser(null, null, $departmentno);
        $userlist = $userlist['data'];

        $this->ajaxReturn(0, '', array(
            'userlist' => $userlist
        ));
    }

    //导出员工信息
    public function export()
    {
        $exportaction = $this->_getExportaction();

        require VENDOR_PATH.'PHPExcel/PHPExcel.php';

        // 创建一个处理对象实例
        $objPHPExcel = new \PHPExcel();

        //设置当前的sheet索引，用于后续的内容操作。
        $objPHPExcel->setActiveSheetIndex(0);       
        $objActSheet = $objPHPExcel->getActiveSheet();

        if ($exportaction == 'user') {
            $title = '智能钥匙柜_'.L('WordLang.UserLang').'信息';

            $subcompanyno = $this->_getSubcompanyno();
            $departmentno = $this->_getDepartmentno();

            $username = $this->_getUsername();

            $data = D('User')->getUser(null, $username, $departmentno);
            $datalist = $data['data'];

            //设置当前活动sheet的名称       
            $objActSheet->setTitle($title);

            //设置宽度，这个值和EXCEL里的不同，不知道是什么单位，略小于EXCEL中的宽度
            $objActSheet->getColumnDimension('A')->setWidth(5);
            $objActSheet->getColumnDimension('B')->setWidth(15);
            $objActSheet->getColumnDimension('C')->setWidth(20);
            $objActSheet->getColumnDimension('D')->setWidth(30);
            $objActSheet->getColumnDimension('E')->setWidth(17);
            $objActSheet->getColumnDimension('F')->setWidth(30);
            $objActSheet->getColumnDimension('G')->setWidth(20);
            $objActSheet->getColumnDimension('H')->setWidth(15);

            //设置单元格的值
            // $objActSheet->setCellValue('A1', '总标题显示');
            
            //设置表格标题栏内容
            $objActSheet->setCellValue('A1', '序号');
            $objActSheet->setCellValue('B1', ''.L('WordLang.UserLang').'名称');
            $objActSheet->setCellValue('C1', ''.L('WordLang.UserLang').'编号');
            $objActSheet->setCellValue('D1', 'RFID卡号');
            $objActSheet->setCellValue('E1', '手机号');
            $objActSheet->setCellValue('F1', ''.L('WordLang.DepartmentLang').'');
            $objActSheet->setCellValue('G1', '职务');
            $objActSheet->setCellValue('H1', '状态');
            
            //遍历数据
            $n = 2;
            foreach ($datalist as $d) {
                $status = $d['status'] ? '正常' : '已删除';

                $objActSheet->setCellValue('A'.$n, $d["userno"]);
                $objActSheet->setCellValue('B'.$n, $d["username"]);
                $objActSheet->setCellValue('C'.$n, $d['codeno']);
                $objActSheet->setCellValue('D'.$n, $d["cardno"]);
                $objActSheet->setCellValue('E'.$n, $d["phone"]);
                $objActSheet->setCellValue('F'.$n, $d["departmentname"]);
                $objActSheet->setCellValue('G'.$n, $d["position"]);
                $objActSheet->setCellValue('H'.$n, $status);

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