<?php
    require_once 'user.php';

    $userObject = new User();

    //请求不完整或分数栏非数字，返回请求失败
    if(!isset($_POST['username']) || !isset($_POST['type'])){
        $json['success'] = 0;
        $json['message'] = "Invalid Purchase Request";
        echo json_encode($json);
    }
    //更改计时器得分请求
    else if($_POST['type'] == "purchase"){
        $json = $userObject->purchase($_POST['username'], $_POST['ornamentName']);
        echo json_encode($json);
    }
    //更改通关得分请求（该接口已被弃用）
//    else if($_POST['type'] == "upgrade"){
//        $json = $userObject->UpdatePassScore($_POST['username'], 1);
//        echo json_encode($json);
//    }
    //无效的请求类型
    else{
        $json['success'] = 0;
        $json['message'] = "Invalid type";
        return json_encode($json);
    }