<?php

/**
 * Created by PhpStorm.
 * User: GE60 2PL
 * Date: 2016/12/4
 * Time: 17:13
 */
class BrandController extends Controller
{
    public function index(){
        $brandModel=new BrandModel();
        $rows=$brandModel->getAll();
        $this->assign("rows",$rows);
        $this->display("index");
    }
    public function add(){
        if($_SERVER['REQUEST_METHOD']=='POST'){
            $data=$_POST;
            $brandModel=new BrandModel();
            $brandModel->insertData($data);
            $this->redirect('index.php?p=admin&c=brand&a=index');
        }
        $this->display('add');
    }
    public function delete(){
        $id=$_GET['id'];
        $brandModel=new BrandModel();
        $brandModel->deleteByPk($id);
        $this->redirect('index.php?p=admin&c=brand&a=index');
    }
    public function edit(){
        if($_SERVER['REQUEST_METHOD']=='POST'){
            $data=$_POST;
            $brandModel=new BrandModel();
            $result=$brandModel->updateData($data);
            if($result!==false) {
                $this->redirect("index.php?p=admin&c=brand&a=index");
            }
            }else{
                $id=$_GET['id'];
                $brandModel=new BrandModel();
                $row=$brandModel->getByPk($id);
                $this->assign($row);
                $this->display("edit");
            }
        }
    }
