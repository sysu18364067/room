<?php
    include_once 'db-connect.php';

    /***********************************************************************************************
     *类名 : User
     *类描述 : 用户类，用于进行以用户为主要对象的操作，主要包括用户注册、登录、购买、积分修改等。
     *类成员 :
         $db : 数据库链接
         $db_table : 主用户表
     *作者 : Luo Ganyuan
     *类创建日期 : 2021.5.7
     *类修改日期 : 2021.5.21
     *修改人 ：Luo Ganyuan
     *修改原因 : 增加了对于购买行为的支持
     *版本 : 1.2
     *历史版本 : 1.0(2021.5.7) 1.1(2021.5.14)
     ***********************************************************************************************/
    class User
    {
        private $db;

        private $db_table = "user";

        //调用DbConnect对象完成数据库链接初始化jkhiu
        public function __construct()
        {
            $this->db = new DbConnect();
        }

        /***********************************************************************************************
         *函数名 : createNewRegisterUser
         *函数功能描述 : 创建新的注册用户。
         *函数参数 : $username--用户名, $password--用户密码, $email--用户邮箱
         *函数返回值 :
             array{
                 'success' : 0--创建新用户失败, 1--创建新用户成功
                 ‘message’ : 错误提示信息
             }
         *作者 : Luo Ganyuan
         *函数创建日期 : 2021.5.7
         *函数修改日期 : 尚未修改
         *修改人 ：尚未修改
         *修改原因 : 尚未修改
         *版本 : 1.0
         *历史版本 : 无
         ***********************************************************************************************/
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
                    $inserted_1 = mysqli_query($this->db->getDb(), $query);
                    $query = "insert into user_chapter_state (Uname) values ('$username')";
                    $inserted_2 = mysqli_query($this->db->getDb(), $query);
                    //插入完成，返回注册成功
                    if ($inserted_1 == 1 && $inserted_2 == 1) {
                        $json['success'] = 1;
                        $json['message'] = "Successfully registered";
                    //插入失败，返回注册失败
                    } else if(inserted_1 == 0) {
                        $query = "delete from user_chapter_state where Uname = '$username'";
                        mysqli_query($this->db->getDb(), $query);
                        $json['success'] = 0;
                        $json['message'] = "Registering Failed. Fail to insert user.";
                    } else {
                        $query = "delete from " . $this->db_table . " where Uname = '$username'";
                        mysqli_query($this->db->getDb(), $query);
                        $json['success'] = 0;
                        $json['message'] = "Registering Failed. Fail to init user chapter state.";
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

        /***********************************************************************************************
         *函数名 : isEmailUsernameExist
         *函数功能描述 : 检查用户注册表中，用户名或邮箱是否已被注册。
         *函数参数 : $username--用户名, $email--用户邮箱
         *函数返回值 : 0--用户已存在, 1--用户不存在
         *作者 : Luo Ganyuan
         *函数创建日期 : 2021.5.7
         *函数修改日期 : 2021.5.21
         *修改人 ：Luo Ganyuan
         *修改原因 : 优化SQL语句，提升查询效率
         *版本 : 1.1
         *历史版本 : 1.0
         ***********************************************************************************************/
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

        /***********************************************************************************************
         *函数名 : isValidEmail
         *函数功能描述 : 判定邮箱是否有效
         *函数参数 : $email--用户邮箱
         *函数返回值 : 0--邮箱无效, 1--邮箱有效
         *作者 : Luo Ganyuan
         *函数创建日期 : 2021.5.7
         *函数修改日期 : 尚未修改
         *修改人 ：尚未修改
         *修改原因 : 尚未修改
         *版本 : 1.0
         *历史版本 : 无
         ***********************************************************************************************/
        public function isValidEmail($email)
        {
            return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
        }

        /***********************************************************************************************
         *函数名 : loginUsers
         *函数功能描述 : 进行一次用户登入操作
         *函数参数 : $username--用户名, $password--用户密码
         *函数返回值 :
             array{
                 'success' : 0--登入失败, 1--登入成功
                 ‘message’ : 错误提示信息
             }
         *作者 : Luo Ganyuan
         *函数创建日期 : 2021.5.7
         *函数修改日期 : 尚未修改
         *修改人 ：尚未修改
         *修改原因 : 尚未修改
         *版本 : 1.0
         *历史版本 : 无
         ***********************************************************************************************/
        public function loginUsers($username, $password)
        {

            $json = array();
            $query = "select * from " . $this->db_table . " inner join user_chapter_fin_state on (". $this->db_table .".Uname = user_chapter_fin_state.Uname) where user.Uname = '$username' AND user.Passwd = '$password'";

            if (mysqli_num_rows($result = mysqli_query($this->db->getDb(), $query))>0) {
                $json['success'] = 1;
                $json['message'] = "Successfully log in";
                $row = mysqli_fetch_assoc($result);
                $json['PassScore'] = (int)$row['PassScore'];
                $json['TimerScore'] = (int)$row['TimerScore'];
                $array = array();
                for($i = 1; $i <=12; $i++){
                    $array[] = (int)$row["chapter".$i];
                }
                $json['Fin'] = $array;
            } else {
                $json['success'] = 0;
                $json['message'] = "Incorrect username or password";
            }
            return $json;
        }

        /***********************************************************************************************
         *函数名 : getOrnamentOwned
         *函数功能描述 : 查询对应用户拥有的饰品ID，并以array数组形式返回。
         *函数参数 : $username--用户名
         *函数返回值 : $array--包含用户拥有饰品ID的数组
         *作者 : Luo Ganyuan
         *函数创建日期 : 2021.5.14
         *函数修改日期 : 2021.5.21
         *修改人 ：Luo Ganyuan
         *修改原因 : 使用inner join联合查询提高效率
         *版本 : 1.1
         *历史版本 : 1.0
         ***********************************************************************************************/
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

        /***********************************************************************************************
         *函数名 : UpdateTimerScore
         *函数功能描述 : 向数据库提交一次TimerScore更新请求。
         *函数参数 : $username--用户名, $score--分数修改数值。
         *函数返回值 :
             array{
                 'success' : 0--更新失败, 1--更新成功
                 ‘message’ : 错误提示信息
             }
         *作者 : Luo Ganyuan
         *函数创建日期 : 2021.5.21
         *函数修改日期 : 尚未修改
         *修改人 ：尚未修改
         *修改原因 : 尚未修改
         *版本 : 1.0
         *历史版本 : 尚未修改
         ***********************************************************************************************/
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

        /***********************************************************************************************
         *函数名 : UpdatePassScore
         *函数功能描述 : 向数据库提交一次PassScore更新请求。
         *函数参数 : $username--用户名, $score--分数修改数值。
         *函数返回值 :
             array{
                 'success' : 0--更新失败, 1--更新成功
                 ‘message’ : 错误提示信息
             }
         *作者 : Luo Ganyuan
         *函数创建日期 : 2021.5.21
         *函数修改日期 : 尚未修改
         *修改人 ：尚未修改
         *修改原因 : 尚未修改
         *版本 : 1.0
         *历史版本 : 尚未修改
         ***********************************************************************************************/
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

        /***********************************************************************************************
         *函数名 : purchase
         *函数功能描述 : 进行一次饰品购买操作，扣除用户相应积分并添加用户购买记录。
         *函数参数 : $username--用户名, $ornamentName--饰品名称
         *函数返回值 :
             array{
                 'success' : 0--更新失败, 1--更新成功
                 ‘message’ : 错误提示信息
             }
         *作者 : Luo Ganyuan
         *函数创建日期 : 2021.5.21
         *函数修改日期 : 尚未修改
         *修改人 ：尚未修改
         *修改原因 : 尚未修改
         *版本 : 1.0
         *历史版本 : 尚未修改
         ***********************************************************************************************/
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