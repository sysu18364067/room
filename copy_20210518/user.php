<?php
    include_once 'db-connect.php';

    class User
    {

        private $db;

        private $db_table = "user";

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
                    //完成，返回注册成功
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
                    //错误的email输入
                    $json['success'] = 0;
                    $json['message'] = "Unvalid email Address";
                }

            }
            return $json;
        }

        //查询邮箱是否已被注册、用户名是否已被注册
        public function isEmailUsernameExist($username, $email)
        {

            $query_user = "select * from " . $this->db_table . " where Uname = '$username'";
            $query_email = "select * from " . $this->db_table . " where email = '$email'";

            $result_user = mysqli_query($this->db->getDb(), $query_user);
            $result_email = mysqli_query($this->db->getDb(), $query_email);

            if (mysqli_num_rows($result_user) > 0 || mysqli_num_rows($result_email) > 0) {
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

            $canUserLogin = $this->isLoginExist($username, $password);

            if ($canUserLogin) {

                $json['success'] = 1;
                $json['message'] = "Successfully log in";

            } else {
                $json['success'] = 0;
                $json['message'] = "Incorrect username or password";
            }
            return $json;
        }

        //核对数据库是否有用户名密码对应的表项
        public function isLoginExist($username, $password)
        {
            $query = "select * from " . $this->db_table . " where Uname = '$username' AND Passwd = '$password'";
            $result = mysqli_query($this->db->getDb(), $query);
            if (mysqli_num_rows($result) > 0) {
                mysqli_close($this->db->getDb());
                return true;
            }
            else {
                mysqli_close($this->db->getDb());
                return false;
            }
        }
    }
?>