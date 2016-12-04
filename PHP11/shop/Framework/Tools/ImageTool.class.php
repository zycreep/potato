<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/28
 * Time: 10:31
 */
class ImageTool
{
    private $error;

    private $createFuns = [
        "image/png"=>"imagecreatefrompng",
        "image/jpeg"=>"imagecreatefromjpeg",
        "image/gif"=>"imagecreatefromgif",
    ];
    private $outFuns = [
        "image/png"=>"imagepng",
        "image/jpeg"=>"imagejpeg",
        "image/gif"=>"imagegif",
    ];

    /**
     * 根据原图片的路径生成缩略图
     * @param $src_path         相对于Uploads下面的图片
     * @param $max_width         目标图片的宽
     * @param $max_height        目标图片的高
     * @param  $type    1: 补白    2: 裁剪
     * @return 目标图片的路径  相对于Uploads下面的图片
     */
   public function thumb($src_path,$max_width,$max_height,$type=1){
         //>>1.  判断原图片是否存在
            $src_path = UPLOADS_PATH.$src_path;  //绝对路径
            if(!is_file($src_path)){
                 $this->error = "原图片不存在!";
                 return false;
            }
       //>>2. 打开原图片
            $imagesize = getimagesize($src_path);
            list($src_width,$src_height) = $imagesize;  //原图片的宽和高
            $mimeType = $imagesize['mime']; //得到原图片的mime类型

            $createFun = $this->createFuns[$mimeType];  //可变函数
            $src_img = $createFun($src_path);


       //>>3.创建目标图片
            $thumb_img = imagecreatetruecolor($max_width,$max_height);
            switch($type){
                case 1:   //补白
                    $white = imagecolorallocate($thumb_img,255,255,255);
                    imagefill($thumb_img,0,0,$white);
                    break;
            }

       //>>4. 计算缩放大小
            $scale = max($src_width/$max_width,$src_height/$max_height);
            $width = $src_width/$scale;
            $height = $src_height/$scale;
            imagecopyresampled($thumb_img,$src_img,($max_width-$width)/2,($max_height-$height)/2,0,0,$width,$height,$src_width,$src_height);

       //>>5.将缩略图保存
           $pathinfo  = pathinfo($src_path);
           $thumb_path =   $pathinfo['dirname'].'/'.$pathinfo['filename']."_{$max_width}x{$max_height}.".$pathinfo['extension'];
           $outFun = $this->outFuns[$mimeType];  //根据mime类型的名字得到函数的名字
           $outFun($thumb_img,$thumb_path);  //调用输出函数

       return substr($thumb_path,strlen(UPLOADS_PATH));
   }


    public function getError(){
        return $this->error;
    }

}