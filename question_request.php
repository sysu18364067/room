<?php
require_once 'QuestionBank.php';

    $questionObject = new QuestionBank;

    $json = array();
    if(!isset($_POST['type'])){
        $json['success'] = 0;
        $json['message'] = "Invalid Question Request(NoneType)";
        echo json_encode($json);
    }
    else {
        switch ($_POST['type']) {
            case 'startChapter':
                if(isset($_POST['ChapterID']) && isset($_POST['username'])){
                    $json = $questionObject->startChapter($_POST['username'], $_POST['ChapterID']);
                }
                else{
                    $json['success'] = 0;
                    $json['message'] = "Invalid Request(Empty ChapterID or username)";
                }
                break;
            case 'QuestionContent':
                if (isset($_POST['Qid']) && isset($_POST['username'])) {
                    $json = $questionObject->updateUserState($_POST['username'], $_POST['Qid']);
                    if($json['success'] = 0){
                        echo json_encode($json);
                    }
                    else {
                        $json = $questionObject->getQuestionContent($_POST['Qid']);
                    }
                }
                else {
                    $json['success'] = 0;
                    $json['message'] = "Invalid Question Request(Unknown Qid or username)";
                }
                break;
            case 'updateUserState':
                if(isset($_POST['Qid']) && isset($_POST['username'])){
                    $json = $questionObject->updateUserState($_POST['username'], $_POST['Qid']);
                }
                else{
                    $json['success'] = 0;
                    $json['message'] = "Invalid Update Request(No Qid or Username)";
                }
                break;
            case 'QuestionResult':
                if (isset($_POST['Qid']) && isset($_POST['correct'])) {
                    if($_POST['correct'] == 0){
                        if(isset($_POST['username'])){
                            $json = $questionObject->takeMistakeNote($_POST['username'], $_POST['Qid']);
                            $json['BQid'] = $questionObject->getBasicQuestion($_POST['Qid']);
                        }
                        else{
                            $json['success'] = 0;
                            $json['message'] = "Invalid Request(Empty Username)";
                        }
                    }
                    else if($_POST['correct'] == 1){
                        $json['success'] = 1;
                        $json['message'] = "Request Down";
                        $json['Qid'] = $questionObject->getNextQustion($_POST['Qid']);
                    }
                }
                else {
                    $json['success'] = 0;
                    $json['message'] = "Invalid Question Request(Invalid Qid or Empty Correct Tag)";
                }
                break;
            case 'BasicQuestionContent':
                if (isset($_POST['Qid'])) {
                    $json = $questionObject->getBasicQuestionContent($_POST['Qid']);
                }
                else {
                    $json['success'] = 0;
                    $json['message'] = "Invalid Question Request(Unknown BQid)";
                }
                break;
            case 'WeaknessContent':
                if (isset($_POST['Wid'])) {
                    $json = $questionObject->getWeaknessContent($_POST['Wid']);
                }
                else {
                    $json['success'] = 0;
                    $json['message'] = "Invalid Weakness Request(Unknown Wid)";
                }
                break;
            default:
                $json['success'] = 0;
                $json['message'] = "Invalid Request(Invalid Type)";
            }
        echo json_encode($json);
        }
