<?php

/**
 * Created by PhpStorm.
 * User: WJZ
 * Date: 2016/12/4
 * Time: 16:48
 */
class NewsController extends Controller
{
    public function index(){
        $news=new NewsModel();
        $rows=$news->getAll();
        //var_dump($rows);die;
        $this->assign('rows',$rows);
        $this->display('index');
    }
    public function add(){
        if($_SERVER['REQUEST_METHOD']=="POST"){
            $data=$_POST;
            $data['add_time']=time();
            $news=new NewsModel();
            $news->insertData($data);
            $this->redirect('index.php?p=Admin&c=News&a=index');
        }else{
           $this->display('add');
        }


    }
    public function edit(){

    }
    public function delete(){
        $id=$_GET['id'];
        $news=new NewsModel();
        $news->deleteByPk($id);
        $this->redirect('index.php?p=Admin&c=News&a=index');
    }

}