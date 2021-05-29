<?php
    include_once 'db-connect.php';

    class User
    {

        private $db;

        private $db_table = "users";

        public function __construct()
        {
            $this->db = new DbConnect();
        }

        //创建一个新的注册用户
        public function createNewRegisterUser($username, $password, $email)
        {
            //从用户表中查询用户是否已存在
            $isExisting = $this->isEmailUsernameExist($username, $email);

            if ($isExisting) {

                $json['success'] = 0;
                $json['message'] = "username or email already exists";
            } else {

                $isValid = $this->isValidEmail($email);

                if ($isValid) {
                    $query = "insert into " . $this->db_table . " (username, password, email, created_at, updated_at) values ('$username', '$password', '$email', NOW(), NOW())";

                    $inserted = mysqli_query($this->db->getDb(), $query);

                    if ($inserted == 1) {

                        $json['success'] = 1;
                        $json['message'] = "Successfully registered";

                    } else {

                        $json['success'] = 0;
                        $json['message'] = "Unknown error in registering.";

                    }

                    mysqli_close($this->db->getDb());
                } else {
                    $json['success'] = 0;
                    $json['message'] = "Unvalid email Address";
                }

            }

            return $json;

        }

        public function isEmailUsernameExist($username, $email)
        {

            $query = "select * from " . $this->db_table . " where username = '$username' AND email = '$email'";

            $result = mysqli_query($this->db->getDb(), $query);

            if (mysqli_num_rows($result) > 0) {

                mysqli_close($this->db->getDb());

                return true;

            }

            return false;

        }

        public function isValidEmail($email)
        {
            return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
        }

        public function loginUsers($username, $password)
        {

            $json = array();

            $canUserLogin = $this->isLoginExist($username, $password);

            if ($canUserLogin) {

                $json['success'] = 1;
                $json['message'] = "Successfully logged in";

            } else {
                $json['success'] = 0;
                $json['message'] = "Incorrect details";
            }
            return $json;
        }

        public function isLoginExist($username, $password)
        {

            $query = "select * from " . $this->db_table . " where username = '$username' AND password = '$password' Limit 1";
            $result = mysqli_query($this->db->getDb(), $query);
            if (mysqli_num_rows($result) > 0) {
                mysqli_close($this->db->getDb());

                return true;
            }
            mysqli_close($this->db->getDb());

            return false;
        }
    }
?>