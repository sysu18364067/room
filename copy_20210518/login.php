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
    if($_POST['submit'] == "signup"){
        $hashed_password = md5($password);
        $json_registration = $userObject->createNewRegisterUser($username, $hashed_password, $email);
        echo json_encode($json_registration);
    }


    if($_POST['submit'] == "login"){
        $hashed_password = md5($password);
        $json_array = $userObject->loginUsers($username, $hashed_password);
        echo json_encode($json_array);
    }
?>