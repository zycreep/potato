<?php

/**
 * 基础框架类
 *
 */
class Framework
{
    public static function run(){
        //通过spl_autoload_register()函数告知PHP,类的自动加载函数是谁?
//         spl_autoload_register("Framework::userAutoload");
           spl_autoload_register(array("Framework","userAutoload"));
//         spl_autoload_register(array(self,"userAutoload"));

        self::initPath();
        self::initConfig();
        self::initRequestParams();
        self::classMapping();
        self::dispache();
    }
    /**
     * 初始化目录常量
     */
     public static function initPath(){
         defined('DS') or define('DS',DIRECTORY_SEPARATOR);
         defined('ROOT_PATH') or define("ROOT_PATH",dirname($_SERVER['SCRIPT_FILENAME']).DS);//项目的根目录
         defined('APP_PATH') or define("APP_PATH",ROOT_PATH."Application".DS);//Application的目录
         defined('FRAME_PATH') or define("FRAME_PATH",ROOT_PATH."Framework".DS);//Framework的目录
         defined('PUBLIC_PATH') or define("PUBLIC_PATH",ROOT_PATH."Public".DS);//Public的目录
         defined('UPLOADS_PATH') or define("UPLOADS_PATH",ROOT_PATH."Uploads".DS);//Uploads的目录
         defined('CONFIG_PATH') or define("CONFIG_PATH",APP_PATH."Config".DS);//Config的目录
         defined('CONTROLLER_PATH') or define("CONTROLLER_PATH",APP_PATH."Controller".DS);//Controller的目录
         defined('MODEL_PATH') or define("MODEL_PATH",APP_PATH."Model".DS);//Model的目录
         defined('VIEW_PATH') or define("VIEW_PATH",APP_PATH."View".DS);//View的目录
         defined('TOOLS_PATH') or define("TOOLS_PATH",FRAME_PATH."Tools".DS);//Tools的目录
     }


    /**
     * 加载配置文件
     */
     public static  function initConfig(){
         $GLOBALS['config'] = require CONFIG_PATH.'application.config.php';
     }

    /**
     * 初始化请求参数
     */
    public static  function initRequestParams(){
        $p = isset($_GET['p'])?$_GET['p']:$GLOBALS['config']['default']['default_platform']; //指定的平台
        $c = isset($_GET['c'])?$_GET['c']:$GLOBALS['config']['default']['default_controller']; //指定的控制器
        $a = isset($_GET['a'])?$_GET['a']:$GLOBALS['config']['default']['default_action']; //指定的方法
        defined('CURRENT_CONTROLLER_PATH') or define("CURRENT_CONTROLLER_PATH",CONTROLLER_PATH.$p.DS);//当前控制器的平台目录
        defined('CURRENT_VIEW_PATH') or define("CURRENT_VIEW_PATH",VIEW_PATH.$p.DS.$c.DS);//当前控制器所对应的视图文件夹目录

        defined('PLATFORM_NAME') or define('PLATFORM_NAME',$p);
        defined('CONTROLLER_NAME') or define('CONTROLLER_NAME',$c);
        defined('ACTION_NAME') or define('ACTION_NAME',$a);

    }

    /**
     * 请求分发(根据请求参数调用控制器中的方法执行.)
     */
    public static  function dispache(){
        //>>1.加载p平台下的c指定的控制器
        $controller_name = CONTROLLER_NAME."Controller";  //拼接出完整的控制器名字
        $controller = new $controller_name();
//>>2.调用a参数指定的方法
        $action_name = ACTION_NAME;
        $controller->$action_name();
    }

    /**
     * 应该框架代码中的类和类文件.
     */
    public static  function classMapping(){
        $GLOBALS['classMapping']=[   //特殊类和类路径的映射.
            'DB'=>TOOLS_PATH.'DB.class.php',
            'Model'=>FRAME_PATH.'Model.class.php',
            'Controller'=>FRAME_PATH.'Controller.class.php',
        ];
    }

    /**
     * 根据类找到类所在的文件
     * @param $class_name
     */
    public static  function userAutoload($class_name){
            if(isset($GLOBALS['classMapping'][$class_name])){  //优先加载类映射数组中内容
                require $GLOBALS['classMapping'][$class_name];  //加载特殊的类
            }elseif(substr($class_name,-10)=='Controller'){  //加载控制器类
                require CURRENT_CONTROLLER_PATH.$class_name.'.class.php';
            }elseif(substr($class_name,-5)=='Model'){  //加载模型
                require MODEL_PATH.$class_name.'.class.php';
            }elseif(substr($class_name,-4)=='Tool'){  //加载工具类
                require TOOLS_PATH.$class_name.'.class.php';
            }
    }

}