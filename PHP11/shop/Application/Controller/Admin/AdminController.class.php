<?php


class AdminController extends Controller
{
    public function index(){
        $adminModel = new AdminModel();
        $row = $adminModel ->getAll();
        $this->assign('row',$row);
        $this->display('index');
    }

    /**
     *
     */
    public function add(){
        if($_SERVER['REQUEST_METHOD']=='POST'){
        $data = $_POST;
        $adminModel = new AdminModel();
        $adminModel->insertData($data);
        //显示页面
        $this->redirect('index.php?p=Admin&c=Admin&a=index','添加成功',1);
    }else{
            $this->display('add');
        }
    }
    public function delete(){
        $id = $_GET['id'];
        $adminModel = new AdminModel();
        $adminModel->deleteByPk($id);
        $this->redirect('index.php?p=Admin&c=Admin&a=index','删除成功',1);
    }
}