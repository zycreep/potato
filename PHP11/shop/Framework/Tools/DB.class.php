<?php

/**
 * 专业操作数据
 */
class  DB
{
    private $host;  //主机
    private $user;//用户名
    private $password;//密码
    private $dbname;//数据库名字
    private $port;//端口
    private $charset;

    private $link;//数据库链接资源

    private static $instance; //静态属性保存一份的对象.

    /**
     * DB constructor.
     * @param $params  DB类中需要的数据
     */
    private function __construct($params)
    {
        $this->host = isset($params['host'])?$params['host']:"127.0.0.1";
        $this->user = isset($params['user'])?$params['user']:"root";
        $this->password = $params['password'];
        $this->dbname = $params['dbname'];
        $this->port = isset($params['port'])?$params['port']:3306;
        $this->charset = isset($params['charset'])?$params['charset']:"utf8";

        $this->connect(); //必须优先调用connect()
        $this->setCharset();
    }

    /**
     * 根据链接信息获取一个独一无二的对象
     * @param $params
     * @return DB
     */
    public static  function getInstance($params){
            if(!self::$instance instanceof self){
                    self::$instance =  new self($params);
            }
         return self::$instance;
    }

    private function __clone()
    {
    }

    /**
     * 连接数据库
     */
    private function connect()
    {
        $this->link = mysqli_connect($this->host, $this->user, $this->password, $this->dbname, $this->port);  //链接错误时返回false
        if ($this->link === false) {  //链接失败. 就退出
            exit("连接失败!错误信息:" . mysqli_connect_error());
        }
    }

    /**
     * 设置编码
     */
    private function setCharset()
    {
        $sql = "set names " . $this->charset;
        $result = mysqli_query($this->link, $sql);
        if ($result === false) {  //链接失败. 就退出
            exit("执行SQL失败!错误信息:" . mysqli_error($this->link));
        }
    }

    /**
     * 执行sql语句
     * @param $sql
     */
    public function query($sql)
    {
        $result = mysqli_query($this->link, $sql);
        if ($result === false) {  //链接失败. 就退出
            echo "执行SQL失败!<br/>";
            echo "SQL:{$sql}!<br/>";
            exit("执行SQL失败!错误信息:" . mysqli_error($this->link));
        }
        return $result;
    }

    /**
     * 执行sql语句,返回所有的数据,该数据以二维数组的形式存在
     * @param $sql
     * @return  二维数组
     */
    public function fetchAll($sql){
        //>>1.执行sql语句,得到查询结果
        //>>1.1 找一个具备执行sql得到结果的能力对象,让它帮我执行sql语句
        $result = $this->query($sql);
        //>>2.从结果集中取出每一行数据并且放到二维数组中.
        /*  $rows = [];
             while($row = mysqli_fetch_assoc($result)){
                 $rows[] = $row;   //每取出一行数据放在外面的数组中.
             }*/
        $rows = mysqli_fetch_all($result,MYSQLI_ASSOC);  //一次性从结果中取出所有的数据,并且以关联数组返回.
        //>>3. 返回二维数组
        return $rows;
    }

    /**
     * 根据sql查询出一行数据
     * @param $sql
     * @return 一维数组
     */
    public function fetchRow($sql){
        //>>1. 执行sql语句,得到多行数据
        $rows = $this->fetchAll($sql);
        //>>2. 从多行数据中解析出一行
        return empty($rows)?null:$rows[0];
    }

    /**
     * 执行sql语句,得到唯一结果值.
     * @param $sql
     */
    public function fetchColumn($sql){
        /**
        //>>1.执行sql语句,得到结果集
        $result  = $this->query($sql);
        //>>2.从结果集中获取第一行
        $row =  mysqli_fetch_row($result);
        //>>3.从第一行中取出第一列的值
        return empty($row)?null:$row[0];
         **/

        //>>1. 执行sql语句,得到第一行的值
        $row = $this->fetchRow($sql);

        //>>2. 从第一行中得到第一列的值
        return empty($row)?null:array_values($row)[0];  //因为row是一个关联数组,需要先从关联数组中取出所有的值. 再从所有的值中取出第一个值.

    }

    public function __destruct()
    {
        //mysqli_close($this->link);
    }

    /**
     * 该方法在对象序列化时被调用.
     * @return array
     */
    public function __sleep()
    {
        //该数组中指定的属性才能够被序列化
        return ["host",'user','password','dbname','port','charset'];
    }

    /**
     * 该方法在对象被反序列化时调用
     */
    public function __wakeup()
    {
        //用来重新初始化属性
        $this->connect();
        $this->setCharset();
    }

    /**
     * 得到最后生成id值
     * @return int|string
     */
    public function last_insert_id(){
        return mysqli_insert_id($this->link);
    }
}