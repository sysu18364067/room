<?php
    include_once 'config.php';

    /***********************************************************************************************
     *类名 : DbConnect
     *类功能描述 : 创建主数据库链接，并在链接失败时给出错误提示。
     *类成员 :
         $connect : 数据库链接对象
     *作者 : Luo Ganyuan
     *函数创建日期 : 2021.5.7
     *函数修改日期 : 尚未修改
     *修改人 ：尚未修改
     *修改原因 : 尚未修改
     *版本 : 1.0
     *历史版本 : 无
     ***********************************************************************************************/
    class DbConnect
    {
        private $connect;

        public function __construct()
        {
            $this->connect = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
            if (mysqli_connect_errno($this->connect)) {
                echo "Failed to connect to MySQL Database: " . mysqli_connect_error();
            }
        }

        public function getDb()
        {
            return $this->connect;
        }
    }