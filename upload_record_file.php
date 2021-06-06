<?php
$json = array();
if(isset($_POST['username'])){
    $target_path  = "./user/record/".$_POST['username'].".txt";//接收文件目录
    if(move_uploaded_file($_FILES['record']['tmp_name'], $target_path)) {
        $json['success'] = 1;
        $json['message'] = "The file".  basename($_FILES['record']['name']). " has been uploaded";
    }
    else {
        echo $_FILES["file"]["name"];
        $json['success'] = 0;
        $json['message'] = "Upload Failed";
    }
}
else{
    $json['success'] = 0;
    $json['message'] = "Empty Username";
}
echo json_encode($json);