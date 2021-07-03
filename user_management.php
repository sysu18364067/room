<?PHP
    require_once 'user.php';
    $username = "";
    $password = "";
    $email = "";

    //获取post的变量
    if(isset($_POST['username'])){
        $username = $_POST['username'];
    }

    if(isset($_POST['password'])){
        $password = $_POST['password'];
    }

    if(isset($_POST['email'])){
        $email = $_POST['email'];
    }

    $userObject = new User();

    //从submit项中获取操作注册或登入
    if($_POST['type'] == "signup"){
        $hashed_password = md5($password);
        $json_registration = $userObject->createNewRegisterUser($username, $hashed_password, $email);
        echo json_encode($json_registration);
    }
    else if($_POST['type'] == "login"){
        $hashed_password = md5($password);
        $json_array = $userObject->loginUsers($username, $hashed_password);
        if($json_array['success'] == 1) {
            $json_array['Ostates'] = $userObject->getOrnamentOwned($username);
            $path  = "./user/record/".$_POST['username'].".txt";
            $json_array["online_record"] = (int)file_exists($path);
        }
        echo json_encode($json_array);
    }
    else{
        $json = array();
        $json['success'] = 0;
        $json['message'] = "Invalid Type";
    }