<?php
    include_once 'db-connect.php';
    class QuestionBank
    {
        private $db;

        private $db_table = "question_content";
        //初始化数据库链接
        public function __construct()
        {
            $this->db = new DbConnect();
        }

        //使用Qid获取题目内容, 返回一系列（Qid, Qcontent）的mysqli_result对象
        public function getQuestionContent($Qid)
        {
            $query = "select * from question_content where Qid = '$Qid'";
            return mysqli_query($this->db->getDb(), $query);
        }

        //从题号获取其薄弱点ID, 返回一系列（Qid, Qcontent）的mysqli_result对象
        public function getWeaknessID($Qid)
        {
            $query = "select * from question_weakness_relation where Qid = '$Qid'";
            return mysqli_query($this->db->getDb(), $query);
        }

        //从基础题获取内容, 返回一系列（BQid, BQcontent）的mysqli_result对象
        public function Content($BQid)
        {
            $query = "select * from basic_question_content where BQid = '$BQid'";
            return mysqli_query($this->db->getDb(), $query);
        }

        //使用Qid获取具有相同薄弱点的Qid的合集, 返回一系列（Wid, Qid）的mysqli_result对象
        public function getNextQustion($Qid)
        {
            $questionWeakness = getWeaknessID($Qid);
            //获取题目得薄弱点集合, 转换为查询字符串
            $widSet = implode(',', array($questionWeakness['Wid']));
            //获取薄弱点对应的所有题号
            $query = "select * from question_weakness_relation where Wid in (". $widSet. ")";
            return mysqli_query($this->db->getDb(), $query);
        }

        //插入一条薄弱点记录，返回插入结果。
        public function takeMistakeNote($Uid, $Qid)
        {
            $query = "insert into user_weakness_relation (Uid, Wid) values ('$Uid', '$Qid')";
            return mysqli_query($this->db->getDb(), $query);
        }



    }