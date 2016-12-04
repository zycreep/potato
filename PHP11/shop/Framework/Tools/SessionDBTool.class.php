<?php

/**
 *
 * 该类中封装了Session入口的行为代码
 */
class SessionDBTool
{
    private $db;

    public function __construct()
    {

        //>>3.强制关闭session
        session_write_close();
        //>>1.告诉PHP,重写session存储机制的代码是当前对象中的方法
        session_set_save_handler(
            array($this, "open"),
            array($this, "close"),
            array($this, "read"),
            array($this, "write"),
            array($this, "destroy"),
            array($this, "gc")
        );
        //>>2.开启session机制
        @session_start();
    }

    /**
     * 当session机制被打开的时候调用.
     * @param $savePath
     * @param $sessionName
     */
    public function open($savePath, $sessionName)
    {
        //打开数据库链接
        $this->db = DB::getInstance($GLOBALS['config']['db']);
        return true;
    }

    /**
     * 当session数据写到数据空间之后被调用.
     */
    public function close()
    {
    }

    /**
     * @param $sessionId  PHPSESSID的值, 可以根据该值找到session数据.
     * @return  str:''    如果有数据时一个序列化的数据, 如果没有数据是一个''
     */
    public function read($sessionId)
    {
        $sql = "select sess_data from session where sess_id = '{$sessionId}' limit 1";
        $sess_data = $this->db->fetchColumn($sql);
        return empty($sess_data) ? '' : $sess_data;
    }

    /**
     * 根据$sessionId找到session数据, 然后将$data中的数据覆盖到session数据中
     * @param $sessionId
     * @param $data   已经被序列化了
     */
    public function write($sessionId, $data)
    {
        $sql = "insert into session values('{$sessionId}','{$data}',unix_timestamp()) on duplicate key update sess_data = '{$data}',last_modified=unix_timestamp()";
        $this->db->query($sql);
    }

    /**
     * 当session_destroy方法执行时,该函数才执行.
     * 根据$sessionId的值删除session数据
     * @param $sessionId
     */
    public function destroy($sessionId)
    {
        $sql = "delete from session where sess_id = '{$sessionId}'";
        return $this->db->query($sql);
    }

    /**
     * 垃圾回收机制启动时,删除垃圾数据
     * @param $lifetime ---最大的时间间隔--php.ini中配置的session.gc_maxlifetime
     */
    public function gc($lifetime)
    {
        $sql = "delete from session where last_modified + {$lifetime} < unix_timestamp()";
        return $this->db->query($sql);
    }


}