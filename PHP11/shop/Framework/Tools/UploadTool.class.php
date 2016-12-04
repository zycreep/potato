<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/27
 * Time: 14:05
 */
class UploadTool
{

    /**
     * 大小
     * @var
     */
    private $max_size;

    /**
     * 允许上传的类型
     * @var
     */
    private $allow_types;

    /**
     * 保存上传时的错误信息
     * @var
     */
    private $error;

    /**
     * UploadTool constructor.
     * @param $max_size
     * @param $allow_types
     */
    public function __construct($max_size='', $allow_types='')
    {
        $this->max_size = !empty($max_size)?$max_size:$GLOBALS['config']['upload']['max_size'] ;
        $this->allow_types = !empty($allow_types)?$allow_types:$GLOBALS['config']['upload']['allow_types'];
    }


    /**
     * 根据用户传入的上传文件信息
     * @param $fileinfo
     * @param $dir   Uploads下的一个文件夹
     * @return Uploads下的一个文件路径    goods/xxx/xxx.jpg
     *
     */
    public function uploadOne($fileinfo,$dir=''){ //$_FILES['上传表单元素的名字']
        //>>1.判断上传是否成功
        if($fileinfo['error']!==0){
            $this->error = '上传失败!';
            return false;
        }
        //>>2.判断上传的大小
        if($fileinfo['size']>$this->max_size){
            $this->error = '上传文件已经超出了指定的大小!';
            return false;
        }
        //>>3.判断上传的类型
        if(!in_array($fileinfo['type'],$this->allow_types)){
            $this->error = '上传文件类型不满足需求!';
            return false;
        }
        //>>4.判断是否为上传文件
        if(!is_uploaded_file($fileinfo['tmp_name'])){
            $this->error = '不是上传文件!';
            return false;
        }
        //>>5.处理文件的名字
        $dir.=date('Ymd').'/';  //在指定的目录下分让日期文件夹存放
        $dirname = UPLOADS_PATH.$dir;


        if(!is_dir($dirname)){//判断该路径是否为一个目录
            mkdir($dirname,0777,true);  //创建目录,   true表示上级目录不存在,先创建上级目录,再创建子目录
        }

        $filename = $dirname.uniqid().strrchr($fileinfo['name'],'.');  //绝对路径

        //>>6.移动文件
        if(!move_uploaded_file($fileinfo['tmp_name'],$filename)){
            $this->error = '移动文件失败!';
            return false;
        }
        return substr($filename,strlen(UPLOADS_PATH));  //相对路径(相对于Uploads)
    }

    /**
     * @param $files          $_FILES['表单元素的名字--以数组形式命名']
     * @param $path 上传文件路径
     * @return  上传成功之后的多张图片路径
     */
    public function uploadMore($files,$path){
            //>>1.重构多文件信息
            $filepaths = [];
            foreach($files['error'] as $key=>$error){
                if($error==0){
                    $fileinfo = [];
                    $fileinfo['name'] = $files['name'][$key];
                    $fileinfo['type'] = $files['type'][$key];
                    $fileinfo['tmp_name'] = $files['tmp_name'][$key];
                    $fileinfo['error'] = $error;
                    $fileinfo['size'] = $files['size'][$key];

                    $filepath = $this->uploadOne($fileinfo,$path);  //一张一张进行上传
                    if($filepath===false){
                        return false;   //只要一个失败! 就然后全部失败!
                    }else{
                        $filepaths[] = $filepath;
                    }
                }
            }
        return  $filepaths;
    }

    /**
     * 得到错误信息
     * @return mixed
     */
    public function getError(){
        return $this->error;
    }


    public function __set($name, $value)
    {
        if(in_array($name,['max_size','allow_types'])){
            $this->$name = $value;
        }
    }

    public function __get($name)
    {
        if($name=='error'){
            return $this->$name;
        }
    }

}