<?php

/**
 * 验证码工具类
 */
class CaptchaTool
{
    /**
     * 生成随机码
     * @param $num
     */
    private static function makeCode($num){
        $chars = "123456789ABCDEFJHGKLMNPQRSTUVWXYZ"; //链接起来
        $chars = str_shuffle($chars); //打乱
        $random_code = substr($chars,0,6);
        return $random_code;
    }

    /**
     * 生成一个指定长度的验证码
     * @param int $num
     */
    public static function generate($num=6){
         //>>1.生成一个随机码
            $random_code = self::makeCode($num);
         //>>2.将随机码保存到session
            new SessionDBTool();
            $_SESSION['random_code'] = $random_code;
         //>>3.将随机码写到图片上
                //>>3.1 获取图片的大小
                $imagePath = TOOLS_PATH."captcha/captcha_bg" . mt_rand(1, 5) . ".jpg";
                list($width, $height) = getimagesize($imagePath);  //得到图片的大小

                //>>3.2 得到图片资源
                $image = imagecreatefromjpeg($imagePath);
                //>>3.3 先画出一个图片的外边
                $white = imagecolorallocate($image,255, 255, 255);
                imagerectangle($image,0,0,$width-1,$height-1,$white);

                 //>>3.4. 将随机码写到图片上
                $black = imagecolorallocate($image,0, 0, 0);
                imagestring($image,5,$width/3,$height/8,$random_code,mt_rand(0,1)?$white:$black);

                 //>>3.5  混淆验证码
                       /* //>>a. 画点
                        for($i = 0; $i<200; ++$i){
                            $color = imagecolorallocate($image,mt_rand(0,255),mt_rand(0,255) ,mt_rand(0,255));
                            imagesetpixel($image ,mt_rand(1,$width-2) , mt_rand(1,$height-2) , $color);
                        }

                        //>>b. 画线
                        for($i = 0; $i<5; ++$i){
                            $color = imagecolorallocate($image,mt_rand(0,255),mt_rand(0,255) ,mt_rand(0,255));
                            imageline ($image ,mt_rand(1,$width-2) , mt_rand(1,$height-2) ,mt_rand(1,$width-2) , mt_rand(1,$height-2),$color);
                        }*/



                 //>>3.6. 将图片发送给浏览器
                header('Content-Type: image/jpeg;charset=utf-8');
                imagejpeg($image);
                //>>3.7. 释放图片资源
                imagedestroy($image);
    }

    /**
     * 对用户输入的验证码进行验证
     * @param $captcha  用户输入的验证码
     * @return bool
     */
    public static function check($captcha){
        //>>1.用户输入的验证码和session中的验证码功能进行对比
        new SessionDBTool();
        return strtolower($_SESSION['random_code']) == strtolower($captcha); //不区分大小写进行比对
    }
}