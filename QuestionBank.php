<?php
    include_once 'db-connect.php';
    class QuestionBank
    {
        private $db;

        //初始化数据库链接
        public function __construct()
        {
            $this->db = new DbConnect();
        }

        //使用Qid获取题目内容, 返回一系列（Qid, Qcontent）的mysqli_result对象
        public function getQuestionContent($Qid)
        {
            $json = array();
            $query = "select * from question_content where Qid = '$Qid'";
            if(mysqli_num_rows($result = mysqli_query($this->db->getDb(), $query))) {
                $questionContent = mysqli_fetch_assoc($result);
                $json['success'] = 1;
                $json['message'] = "Request Done";
                $json['chapter'] = $questionContent['chapterID'];
                $json['content'] = $questionContent['Qcontent'].' '.$questionContent['Qoptions'];
                $json['answer'] = $questionContent['Qanswer'];
                $json['analysis'] = $questionContent['Qanalysis'];
            }
            else{
                $json['success'] = 0;
                $json['message'] = "Invalid Qid";
            }
            return $json;
        }

        public function getBasicQuestion($Qid)
        {
            $array = array();
            $query = "select BQid from weakness_basic_question_relation inner join question_weakness_relation on (weakness_basic_question_relation.Wid = question_weakness_relation.Wid) where Qid = '$Qid' group by BQid";
            $result = mysqli_query($this->db->getDb(), $query);
            while($rows = mysqli_fetch_array($result)) {
                $array[] = (int)$rows['BQid'];
            }
            return $array;
        }

        public function getBasicQuestionContent($BQid)
        {
            $json = array();
            $query = "select BQcontent, BQoptions, BQanswer from basic_question_content where BQid = '$BQid'";
            if(mysqli_num_rows($result = mysqli_query($this->db->getDb(), $query))) {
                $questionContent = mysqli_fetch_assoc($result);
                $json['success'] = 1;
                $json['message'] = "Request Done";
                $json['content'] = $questionContent['BQcontent'].' '.$questionContent['BQoptions'];
                $json['answer'] = $questionContent['BQanswer'];
            }
            else{
                $json['success'] = 0;
                $json['message'] = "Invalid BQid";
            }
            return $json;
        }

        public function startChapter($username, $ChapterID){
            $json = array();
            $query = "select chapter".$ChapterID." from user_chapter_state where Uname = '$username'";
            if(mysqli_num_rows($result = mysqli_query($this->db->getDb(), $query))) {
                $questionContent = mysqli_fetch_assoc($result);
                $json['success'] = 1;
                $json['message'] = "Request Done";
                $json['Qid'] = $questionContent["chapter".$ChapterID];
            }
            else{
                $json['success'] = 0;
                $json['message'] = "Invalid username or ChapterID";
            }
            return $json;
        }

        public function getWeaknessContent($Wid)
        {
            $json = array();
            $query = "select Wcontent from weakness_content where Wid = '$Wid'";
            if(mysqli_num_rows($result = mysqli_query($this->db->getDb(), $query))) {
                $questionContent = mysqli_fetch_assoc($result);
                $json['success'] = 1;
                $json['message'] = "Request Done";
                $json['content'] = $questionContent['Wcontent'];
            }
            else{
                $json['success'] = 0;
                $json['message'] = "Invalid Wid";
            }
            return $json;
        }

        //使用Qid获取具有相同薄弱点的Qid的合集, 返回一系列（Wid, Qid）的数组
        public function getNextQustion($Qid)
        {
            $array = array();
            //获取薄弱点对应的所有题号
            $query = "select t1.Qid from question_weakness_relation as t1 inner join question_weakness_relation as t2 on (t1.Wid = t2.Wid and t1.Qid != t2.Qid) where t2.Qid = '$Qid' group by t1.Qid";
            $result = mysqli_query($this->db->getDb(), $query);
            while($rows = mysqli_fetch_array($result)) {
                $array[] = (int)$rows['Qid'];
            }
            return $array;
        }

        //插入一条薄弱点记录，返回插入结果。
        public function takeMistakeNote($Uname, $Qid)
        {
            $json = array();
            $query = "select Wid from question_weakness_relation where Qid = '$Qid'";
            if(mysqli_num_rows($result = mysqli_query($this->db->getDb(), $query))){
                while($rows = mysqli_fetch_array($result)) {
                    $Wid = $rows['Wid'];
                    $query = "insert into user_weakness_relation values ('$Uname', '$Wid')";
                    if(mysqli_query($this->db->getDb(), $query)){
                        $json['success'] = 1;
                        $json['message'] = "Request Done";
                    }
                    else{
                        $json['success'] = 0;
                        $json['message'] = "Fail to Update User's Weakness";
                    }
                }
            }
            else{
                $json['success'] = 0;
                $json['message'] = "Invalid Qid or No Wid Found";
            }
            return $json;
        }
        public function updateUserState($username, $Qid){
            $json = array();
            $query = "select chapterID from question_content where Qid = '$Qid'";
            if(mysqli_num_rows($result = mysqli_query($this->db->getDb(), $query))){
                $query = "update user_chapter_state set chapter".mysqli_fetch_assoc($result)['chapterID']." = '$Qid' where Uname = '$username'";
                if(mysqli_query($this->db->getDb(), $query)){
                    $json['success'] = 1;
                    $json['message'] = "Update Success";
                }
                else{
                    $json['success'] = 0;
                    $json['message'] = "Failed to Update User State";
                }
            }
            else{
                $json['success'] = 0;
                $json['message'] = "Invalid Qid";
            }
            return $json;
        }
    }