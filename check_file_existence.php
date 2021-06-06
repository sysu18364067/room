<?php
$json = array();
if(isset($_POST['username'])){
    $path  = "./user/record/".$_POST['username'].".txt";//接收文件目录
    if(file_exists($path)) {
        $json['success'] = 1;
        $json['message'] = "The file exists";
    }
    else {
        $json['success'] = 0;
        $json['message'] = "No File Found";
    }
}
else{
    $json['success'] = 0;
    $json['message'] = 'Empty Username';
}
echo json_encode($json);