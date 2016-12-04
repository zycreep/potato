<?php

/**
 * 基础模型类专门比所有的模型类继承
 */
abstract  class Model
{
    /**
     * 该属性可以被子类重写,指定表名
     * @var string
     */
    protected $table_name = '';

    protected $db;

    private $fields = [];//存储当前模型对应表的字段信息
    /**
     * 存储错误信息
     * @var
     */
    protected $error;

    public function __construct()
    {
        //>>1.加载执行sql的DB类并且创建
        $this->db = DB::getInstance($GLOBALS['config']['db']);
        $this->initFields();
    }

    /**
     * 查询出当前子类对象对应表的字段信息
     *
     * array("pk"=>"id","name","url","logo","intro")
     */
    private function initFields(){
        $sql = "desc {$this->table()}";
        $rows = $this->db->fetchAll($sql);
        foreach($rows as $row){
            if($row['Key']=='PRI'){
                 //主键
                 $this->fields['pk'] = $row['Field'];  //主键的话需要通过pk键指向.
            }else{
                 $this->fields[] =$row['Field'];  //列表直接放在数组中
            }
        }
    }


    /**
     * 获取错误信息
     * @return mixed
     */
    public function getError(){
        return $this->error;
    }

    /**
     * 拼接处真实的表名
     */
    private function table(){
        if(empty($this->table_name)){
            //从子类的名字上得到表名
            $class_name = get_class($this);//得到类名
            //$last_pos = strrpos($class_name, "Model");  //Model中类名中的位置
            $class_name = substr($class_name,0,-5);//得到类名
            $this->table_name = strtolower($class_name);  //将类名变成小写
        }
        return  '`'.$GLOBALS['config']['db']['prefix'].$this->table_name.'`';
    }


    /**
     *根据主键删除一行数据
     * @param $pk  主键的值
     */
    public function deleteByPk($pk){
        $sql = "delete from {$this->table()} where {$this->fields['pk']} = {$pk}";
        $this->db->query($sql);
    }

    /**
     * 根据条件查询一部分数据
     */
    public function getAll($condition=''){
        $sql = "select * from {$this->table()}";
        if(!empty($condition)){
            $sql.=" where ".$condition;
        }
        return $this->db->fetchAll($sql);
    }

    /**
     * 根据主键得到一行数据
     * @param $id
     */
    public function getByPk($pk){
        $sql = "select * from {$this->table()} where `{$this->fields['pk']}` = {$pk} limit 1";
        return $this->db->fetchRow($sql);
    }


    /**
     * 根据数据:
     * $data = ["id"=>1,"name"=>'魅族',"url"=>"www.meizu.com","logo"=>"xxxx.jpg","intro"=>"美足手机!"];
     * 拼装出
     *  update 表  set id = 1 ,name = ''... where id = 1;
     *
     * @param $new_data  必须包含主键的值
     */
    public function updateData($new_data){
        //根据表中的列明删除$data中和表不相关的数据
        $this->ignoreErrorField($new_data);


        $sql = "update {$this->table()} set ";
        $fieldValues = [];
        foreach($new_data as $key=>$value){
            $fieldValues[] = "`{$key}`='{$value}'";  //将字段和值放在数组中
        }
        $sql .= implode(",",$fieldValues)." where {$this->fields['pk']} = {$new_data["{$this->fields['pk']}"]}";
        $this->db->query($sql);
    }


    /**
     * 删除$data中非法的数据
     * @param $data
     */
    private function ignoreErrorField(&$data){
        foreach($data as $k=>$v){
            if(!in_array($k,$this->fields)){  //$data中的键不在fields中,删除$data中键对应的值.
                unset($data[$k]);
            }
        }
    }


    /**
     * 根据数据:
     * $data = ["name"=>'魅族',"url"=>"www.meizu.com","logo"=>"xxxx.jpg","intro"=>"美足手机!"];
     *
     *
     * 拼装出:
     * //insert into brand values();
     * insert into brand set `name`='魅族',`url`='www.meizu.com',`logo`='xxxx.jpg',intro='美足手机!'
     *
     *
     * 根据用户传入的数据进行拼装
     * @param $data  大大的前提:  必须是一个关联数组, 键必须和数据库中的字段一一对应.
     * @return 保存在数据库中数据对应的id
     */
    public function insertData($data){
        //根据表中的列明删除$data中和表不相关的数据
        $this->ignoreErrorField($data);

        $sql = "insert into {$this->table()} set  ";

        $fieldValues = [];
        foreach($data as $key=>$value){
            $fieldValues[] = "`{$key}`='{$value}'";  //将字段和值放在数组中
        }
        $sql.=implode(",",$fieldValues);  //将字段和值的字符串连接起来变成:    `name`='魅族',`url`='www.meizu.com',`logo`='xxxx.jpg',intro='美足手机!'

        $this->db->query($sql);

        return  $this->db->last_insert_id();
    }


    /**
     * 根据条件得到一行中的一个字段的值
     * @param $field
     * @param $condition
     */
    public function getColumn($field,$condition){
        $sql = "select {$field} from {$this->table()} where {$condition} limit 1";
        return $this->db->fetchColumn($sql);
    }

    /**
     * 根据条件得到一行的值
     * @param $condition
     */
    public function getRow($condition){
        $sql = "select * from {$this->table()} where {$condition} limit 1";
        return $this->db->fetchRow($sql);
    }


    /**
     * 根据条件统计总条数
     * @param $condition
     */
    public function getCount($condition=''){
        $sql = "select count(*) from {$this->table()}";
        if(!empty($condition)){
            $sql.=" where ".$condition;
        }
        return $this->db->fetchColumn($sql);
    }
}