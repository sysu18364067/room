<?php
    include_once 'db-connect.php';

    //用户类，主要用于用户表相关操作
    class User
    {
        private $db;

        //操作表名称
        private $db_table = "user";

        //初始化数据库连接
        public function __construct()
        {
            $this->db = new DbConnect();
        }

        //创建一个新的注册用户
        public function createNewRegisterUser($username, $password, $email)
        {
            //从用户表中查询用户是否已存在
            $isExisting = $this->isEmailUsernameExist($username, $email);

            //用户已存在，返回注册失败
            if ($isExisting) {
                $json['success'] = 0;
                $json['message'] = "username or email already exists";
            } else {
                //用户未存在，判断email是否正确
                $isValid = $this->isValidEmail($email);
                //email正确，尝试向数据库插入新用户项
                if ($isValid) {
                    $query = "insert into " . $this->db_table . " (Uname, Passwd, email) values ('$username', '$password', '$email')";
                    $inserted = mysqli_query($this->db->getDb(), $query);
                    //插入完成，返回注册成功
                    if ($inserted == 1) {

                        $json['success'] = 1;
                        $json['message'] = "Successfully registered";
                    //插入失败，返回注册失败
                    } else {

                        $json['success'] = 0;
                        $json['message'] = "Unknown error in registering. Fail to insert user.";

                    }
                    mysqli_close($this->db->getDb());
                } else {
                    //异常处理：错误的email输入
                    $json['success'] = 0;
                    $json['message'] = "Unvalid email Address";
                }

            }
            return $json;
        }

        //查询邮箱是否已被注册、用户名是否已被注册
        public function isEmailUsernameExist($username, $email)
        {
            $query = "select * from " . $this->db_table . " where Uname = '$username' or email = '$email'";
            $result = mysqli_query($this->db->getDb(), $query);
            //查询到重复的用户名或email
            if (mysqli_num_rows($result) > 0) {
                mysqli_close($this->db->getDb());
                return true;
            }
            return false;
        }

        //查询邮箱地址是否有效
        public function isValidEmail($email)
        {
            return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
        }

        //用户登入函数
        public function loginUsers($username, $password)
        {

            $json = array();
            $query = "select TimerScore, PassScore from " . $this->db_table . " where Uname = '$username' AND Passwd = '$password'";

            if (mysqli_num_rows($result = mysqli_query($this->db->getDb(), $query))>0) {
                $json['success'] = 1;
                $json['message'] = "Successfully log in";
                $json['PassScore'] = (int)mysqli_fetch_assoc($result)['PassScore'];
                mysqli_data_seek($result, 0);
                $json['TimerScore'] = (int)mysqli_fetch_assoc($result)['TimerScore'];
            } else {
                $json['success'] = 0;
                $json['message'] = "Incorrect username or password";
            }
            return $json;
        }

        //获取拥有的家具
        public function getOrnamentOwned($username)
        {
            $array = array();
            $query = "select Oid from user_ornaments_relation inner join ornament_name_id on (user_ornaments_relation.Oname = ornament_name_id.Oname) where Uname = '$username' group by Oid";
            $result = mysqli_query($this->db->getDb(), $query);
            while($rows = mysqli_fetch_array($result)) {
                $array[] = (int)$rows['Oid'];
            }
            return $array;
        }

        //更新计时器积分
        public function UpdateTimerScore($username, $score)
        {
            //查询用户对应的计时器积分
            $query = "select TimerScore from " . $this->db_table . " where Uname = '$username'";
            //找到条目，计算用户更新积分
            if(mysqli_num_rows($result = mysqli_query($this->db->getDb(), $query))>0){
                $score = $score + mysqli_fetch_assoc($result)['TimerScore'];
            }
            //找不到条目，该用户无效
            else{
                $json['success'] = 0;
                $json['message'] = "Invalid Username";
                mysqli_close($this->db->getDb());
                return $json;
            }
            //分数不足，更新失败
            if($score < 0){
                $json['success'] = 0;
                $json['message'] = "Insufficient TimerScore";
            }
            else if ($score >= 2147483647){
                $json['success'] = 0;
                $json['message'] = "TimerScore Overflow";
            }
            else {
                //更新积分
                $query = "update " . $this->db_table . " set TimerScore = '$score' where Uname = '$username'";
                //更新成功，返回成功信息
                if (mysqli_query($this->db->getDb(), $query)) {
                    $json['success'] = 1;
                    $json['message'] = "Update TimerScore Success";
                    //更新失败，返回失败信息
                } else {
                    $json['success'] = 0;
                    $json['message'] = "Fail to Update TimerScore Due to Unknown Problem";
                }
            }
            return $json;
        }

        //更新通关积分
        public function UpdatePassScore($username, $score)
        {
            //查询用户对应的通关积分
            $query = "select PassScore from " . $this->db_table . " where Uname = '$username'";
            //找到条目，计算用户更新积分
            if(mysqli_num_rows($result = mysqli_query($this->db->getDb(), $query))>0){
                $score = $score + mysqli_fetch_assoc($result)['PassScore'];
            }
            //找不到条目，该用户无效
            else{
                $json['success'] = 0;
                $json['message'] = "Invalid Username";
                mysqli_close($this->db->getDb());
                return $json;
            }
            //分数不足，更新失败
            if($score < 0){
                $json['success'] = 0;
                $json['message'] = "Insufficient PassScore";
            }
            else if ($score >= 2147483647){
                $json['success'] = 0;
                $json['message'] = "PassScore Overflow";
            }
            else {
                //更新积分
                $query = "update " . $this->db_table . " set PassScore = '$score' where Uname = '$username'";
                if (mysqli_query($this->db->getDb(), $query)) {
                    $json['success'] = 1;
                    $json['message'] = "Update PassScore Success";
                    //更新失败，返回失败信息
                } else {
                    $json['success'] = 0;
                    $json['message'] = "Fail to Update PassScore Due to Unknown Problem";
                }
            }
            mysqli_close($this->db->getDb());
            return $json;
        }

        //定义用户购买行为
        public function purchase($username, $ornamentName){
            //查询用户是否已经购买该饰品
            $query = "select Uname from user_ornaments_relation where Oname = '$ornamentName' and Uname = '$username'";
            //如果已购买，返回购买已完成
            if(mysqli_num_rows($result = mysqli_query($this->db->getDb(), $query))>0){
                $json['success'] = 0;
                $json['message'] = "Ornament Had Been Purchased Yet";
                mysqli_close($this->db->getDb());
                return $json;
            }
            //更新用户购买信息表，更新用户TimerScore
            $query = "select Oprice from ornament where Oname = '$ornamentName'";
            if(mysqli_num_rows($result = mysqli_query($this->db->getDb(), $query))>0){
                $json = $this->UpdateTimerScore($username, -mysqli_fetch_assoc($result)['Oprice']);
                if($json['success'] == 1){
                    $query = "insert into user_ornaments_relation (Uname, Oname) values ('$username', '$ornamentName')";
                    //尝试更新购买信息表
                    if(mysqli_query($this->db->getDb(), $query)){
                        $json['success'] = 1;
                        $json['message'] = "Purchase Success";
                    }
                    //如果向user_ornaments_relation表插入失败，撤销积分更改。
                    else{
                        $this->UpdateTimerScore($username, mysqli_fetch_assoc($result)['Oprice']);
                        $json['success'] = 0;
                        $json['message'] = "Purchase Failed(Invalid Username)";
                    }
                }
            }
            //无效的饰品名
            else{
                $json['success'] = 0;
                $json['message'] = "Invalid Ornament";
            }
            mysqli_close($this->db->getDb());
            return $json;
        }
    }