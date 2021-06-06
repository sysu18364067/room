<?php
    require_once 'user.php';

    $userObject = new User();

    //请求不完整或分数栏非数字，返回请求失败
    if(!isset($_POST['username']) || !isset($_POST['score']) || !isset($_POST['type']) || !is_numeric($_POST['score'])){
        $json['success'] = 0;
        $json['message'] = "Invalid Scoreboard Request";
        echo json_encode($json);
    }
    //更改计时器得分请求
    else if($_POST['type'] == "TimerScore"){
        $json = $userObject->UpdateTimerScore($_POST['username'], $_POST['score']);
        echo json_encode($json);
    }
    /*
    //该接口已被弃用
    else if($_POST['type'] == "PassScore"){
        $json = $userObject->UpdatePassScore($_POST['username'], $_POST['score']);
        echo json_encode($json);
    }
    */
    //无效的请求类型
    else{
        $json['success'] = 0;
        $json['message'] = "Invalid Scoreboard Request(Wrong type)";
        echo json_encode($json);
    }