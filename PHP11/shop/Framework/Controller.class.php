<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/21
 * Time: 9:50
 */
class Controller
{
    private $data = [];  //就是用来存储需要在页面上使用到的数据


    protected function display($template){
        /*$rows = $this->data['rows'];
        $name = $this->data['name'];
        $sex = $this->data['sex'];
        $age = $this->data['age'];*/
        extract($this->data);  //将关联数组中的内容变成变量.. 使用关联数组的键作为变量的名字,使用关联数组的值作为变量的值.
        require CURRENT_VIEW_PATH.$template.'.html';   //在视图页面中不能够使用到另外一个方法中的局部变量
        exit;
    }

    /**
     * 该方法主要是在子类中使用. 向data属性上放key===value
     * @param $key
     * @param $value
     */
    protected function assign($key,$value=''){
        if(is_array($key)){  //如果键是一个数组, 那么将数组合并到$data中. 直接在页面上使用数组中的键取出值.
            $this->data = array_merge($this->data,$key); // $student = ['name'=>'张三','age'=>29,'sex'=>'男'];
        }else{
            $this->data[$key] = $value;
        }
    }


    /**
     * 跳转
     * @param 跳转的url $url
     * @param string|提示的信息 $msg
     * @param int|等待时间 $times   0代表立即跳转
     */
    protected function redirect($url,$msg='',$times=0){
        if(headers_sent()){
            //header已经发送
            if($times){
                echo "<h1>{$msg}</h1>";
                $times = $times*1000;
            }
            echo <<<JS
              <script type='text/javascript'>
                window.setTimeout(function(){
                     window.location.href = "{$url}";
                },$times);
              <script>
JS;
        }else{
            //header没有发送
            /*    if($times){
                      //等待跳转
                    echo "<h1>{$msg}</h1>";
                    header("Refresh: {$times};$url");
                }else{
                       //立即跳转
                    header("Location: $url");
                }*/
            echo "<h1>{$msg}</h1>";
            header("Refresh: {$times};$url");
        }
        exit;
    }

}