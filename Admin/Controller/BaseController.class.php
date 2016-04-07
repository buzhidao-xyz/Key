<?php
/**
 * Admin Module 基类
 * imbzd
 * 2015-05-11
 */
namespace Admin\Controller;

use Any\Controller;
use Org\Util\Log;

class BaseController extends Controller
{
    //设备类型
    //主控板
    protected $_DEVICETYPE_ZKB = 1;
    //门禁机
    protected $_DEVICETYPE_MJJ = 2;
    //摄像头
    protected $_DEVICETYPE_SXT = 3;

    public function __construct()
    {
        parent::__construct();

        //加载语言包
        $this->_loadLang();

        //输出系统配置
        $this->_assignConfig();
        //输出系统参数
        $this->_assignSystem();
        //输出框架参数
        $this->_assignAny();

        //记录请求日志
        $this->_accessLog();
    }

    /**
     * 加载语言包
     */
    private function _loadLang()
    {
        $lang = C('DEFAULT_LANG');

        //加载公共语言包
        include(LANG_PATH.$lang.'.php');
        L($lang);
        //加载控制器语言包
        include(LANG_PATH.$lang.'/'.CONTROLLER_NAME.'.php');
        L($lang);
    }

    /**
     * 输出系统配置
     */
    private function _assignConfig()
    {
        $SERVER = array();

        //服务器HOST
        $HOST = C('HOST');
        $SERVER['HOST'] = $HOST;
        $this->assign('SERVER', $SERVER);
    }

    //输出系统参数
    private function _assignSystem()
    {
        $SYSTEM = array(
            'systemtitle' => array(
                'name'  => '系统名称',
                'key'   => 'systemtitle',
                'value' => '钥匙柜管理系统',
            ),
        );
        $this->assign('SYSTEM', $SYSTEM);
    }

    //输出框架参数
    private function _assignAny()
    {
        $ANY = array(
            '__APP__' => __APP__,
        );
        $this->assign('ANY', $ANY);
    }

    /**
     * 记录请求日志
     */
    private function _accessLog()
    {
        Log::record('access',array(
            'ModuleName'  => MODULE_NAME,
            'ServerIp'    => $_SERVER['SERVER_ADDR'].':'.$_SERVER['SERVER_PORT'],
            'ClientIp'    => get_client_ip(),
            'DateTime'    => date('Y-m-d H:i:s', TIMESTAMP),
            'TimeZone'    => 'UTC'.date('O',TIMESTAMP),
            'Method'      => $_SERVER['REQUEST_METHOD'],
            'URL'         => $_SERVER['REQUEST_URI'],
            'Protocol'    => $_SERVER['SERVER_PROTOCOL'],
            'RequestData' => $_REQUEST,
        ));
    }

    /**
     * 检查请求类型 是否get/post
     * @param string $quest 请求类型 get/post/put/delete
     */
    protected function CKQuest($quest=null)
    {
        if (!$quest) return false;

        $flag = true;
        switch ($quest) {
            case 'get':
                if (!IS_GET) $flag = false;
                break;
            case 'post':
                if (!IS_POST) $flag = false;
                break;
            case 'put':
                if (!IS_PUT) $flag = false;
                break;
            case 'delete':
                if (!IS_DELETE) $flag = false;
                break;
            default:
                break;
        }
        if (!$flag) $this->appReturn(1,L('quest_error'));

        return true;
    }

    /**
     * AJAX返回数据
     * @param int $error 是否产生错误信息 0没有错误信息 1有错误信息
     * @param string $msg 如果有错 msg为错误信息
     * @param array $data 返回的数据 多维数组
     * @return json 统一返回json数据
     */
    protected function ajaxReturn($error=0,$msg=null,$data=array())
    {
        if ($error && !$msg) {
            $error = 1;
            $msg   = L('ajaxreturn_error_msg');
            $data  = array();
        }

        if (!$error && !is_array($data)) {
            $error = 1;
            $msg = L('ajaxreturn_error_msg');
            $data = array();
        }

        //APP返回
        $return = array(
            'error' => $error,
            'msg'   => $msg,
            'data'  => $data
        );

        $type = 'json';
        switch ($type) {
            case 'json':
                header('Content-Type: application/json');
                $return = json_encode($return);
                break;
            default:
                header('Content-Type: application/json');
                $return = json_encode($return);
                break;
        }

        echo $return;
        exit;
    }

    /**
     * 页面返回数据 展示提示信息
     * @param int $error 是否产生错误信息 0没有错误信息 1有错误信息 大于1为其他错误码
     * @param string $msg 如果有错 msg为错误信息
     * @param array $data 返回的数据 多维数组
     */
    protected function pageReturn($error=0,$msg=null,$data=array())
    {
        if ($error && !$msg) {
            $error = 1;
            $msg   = L('pagereturn_error_msg');
            $data  = array();
        }

        if (!$error && !is_array($data)) {
            $error = 1;
            $msg = L('pagereturn_error_msg');
            $data = array();
        }

        //page数据
        $pageReturn = array(
            'error' => $error,
            'msg'   => $msg,
            'data'  => $data
        );
        $this->assign('pagereturn', $pageReturn);

        $this->display('Public/pagereturn');
        exit;
    }

    //goto登录页
    protected function _gotoLogin($goto=true)
    {
        $location = __APP__.'?s=Admin/login';
        if ($goto) {
            header('Location:'.$location);
            exit;
        } else {
            return $location;
        }
    }

    //goto登出页
    //bool $goto 是否跳转 true:自动跳转 false:不跳转返回location
    protected function _gotoLogout($goto=true)
    {
        $location = __APP__.'?s=Admin/logout';
        if ($goto) {
            header('Location:'.$location);
            exit;
        } else {
            return $location;
        }
    }

    //跳转到系统首页
    protected function _gotoIndex($goto=true)
    {
        $location = __APP__.'?s=Index/index';
        if ($goto) {
            header('Location:'.$location);
            exit;
        } else {
            return $location;
        }
    }

    /**
     * socket-tcp交互
     * $data:'aa bb 00 05 55 01 01 01 00 00 ee ff'
     */
    protected function _socketTcpSend($ip=null, $port=null, $data=null)
    {
        $result = 0;

        $data = str_split(str_replace(' ', '', $data), 2);
        $socket = socket_create(AF_INET, SOCK_STREAM, getprotobyname("tcp"));
        //设置超时5秒
        socket_set_option($socket, SOL_SOCKET, SO_SNDTIMEO, array(
            "sec"  => 5, // Timeout in seconds
            "usec" => 0   // I assume timeout in microseconds
        ));
        if (socket_connect($socket, $ip, $port)) {
            //逐组数据发送
            foreach ($data as $d) {
                socket_write($socket, chr(hexdec($d)));
            }

            //采用2进制方式接收数据
            $resultb = socket_read($socket, 1024, PHP_BINARY_READ);
            //将2进制数据转换成16进制
            $result = bin2hex($resultb);
        }
        //关闭Socket
        socket_close($socket);

        return $result;
    }
}