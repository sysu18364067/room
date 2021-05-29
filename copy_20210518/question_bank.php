<?php
    include_once 'db-connect.php';
    class Question_bank
    {
        private $db;

        public function __construct()
        {
            $this->db = new DbConnect();
        }

        public function getQuestionContent($Qid){
            $query = "select * from question_content where Qid = '$Qid'";
            $result = mysqli_query($this->db->getDb(), $query);
            return $result;
        }

        public function getNextQustion($Qid){
            $query = "select * from question_relation where Qid = '$Qid'";
            $result = mysqli_query($this->db->getDb(), $query);
            return $result;
        }

        public function getBasicQustionID($Qid){
            $query = "select * from question_basic_relation where Qid = '$Qid'";
            $result = mysqli_query($this->db->getDb(), $query);
            return $result;
        }

        public function getBasicQustion($BQid){
            $query = "select * from question_content where Qid = '$BQid'";
            $result = mysqli_query($this->db->getDb(), $query);
            return $result;
        }
    }
?>