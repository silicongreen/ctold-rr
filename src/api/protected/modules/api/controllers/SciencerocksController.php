<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class SciencerocksController extends Controller {

    /**
     * @return array action filters
     */
    public function filters() {
        return array(
            'accessControl', // perform access control for CRUD operations
            'postOnly + delete', // we only allow deletion via POST request
        );
    }

    /**
     * Specifies the access control rules.
     * This method is used by the 'accessControl' filter.
     * @return array access control rules
     */
    public function accessRules() {
        return array(
            array('allow', // allow authenticated user to perform 'create' and 'update' actions
                'actions' => array('index', 'getlevel', 'getscoreboard','gethighscore', 'getepisode', 'getquestion', 'download', 'sharedaildose', 'savescore', 'getdailydose', 'getdailydosehistory', 'getanchorquestion', 'ask'),
                'users' => array('*'),
            ),
            array('deny', // deny all users
                'users' => array('*'),
            ),
        );
    }

    public function actionGetHighScore() {
        $level_id = Yii::app()->request->getPost('level_id');
        if ($level_id) {
            $total_score = new TdsScienceRocksHighscore();
            
            $question =new TdsScienceRocksQuestion();
                    
            $question_mark = $question->getTotalQuesTionAndMark($level_id);
            $total_mark = 0;
            $total_question = 0;
            
            if($question_mark)
            {
                $total_mark = $question_mark->total_mark;
                $total_question = $question_mark->total_question;
            }
            
            $response['data']['score'] = $total_score->getHighscore($level_id);
            $response['data']['total_mark'] = $total_mark;
            $response['data']['total_question'] = $total_question;
            $response['status']['code'] = 200;
            $response['status']['msg'] = "Success";
        } else {
            $response['status']['code'] = 400;
            $response['status']['msg'] = "Bad Request";
        }
        echo CJSON::encode($response);
        Yii::app()->end();
    }

    public function actionGetScoreboard() {
        $total_score = new TdsScienceRocksTotalScore();

        $response['data']['list'] = $total_score->getLeaderBoard();
        $response['status']['code'] = 200;
        $response['status']['msg'] = "Success";
        echo CJSON::encode($response);
        Yii::app()->end();
    }

    public function actionShareDaildose() {
        $id = $_GET['id'];
        if ($id) {
            $dailydose = new TdsDailydose();
            $dailydoseobj = $dailydose->findByPk($id);
            if ($dailydoseobj->content) {
                if (!$dailydoseobj->share_content) {
                    $dailydoseobj->share_content = $dailydoseobj->content;
                }
            }
            header('Content-Type: text/html; charset=utf-8');
            echo $dailydoseobj->share_content;
        }
    }

    public function actionDownload() {

        $id = $_GET['id'];
        if ($id) {
            $dailydose = new TdsDailydose();
            $dailydoseobj = $dailydose->findByPk($id);
            if ($dailydoseobj->content) {
                if (!$dailydoseobj->share_content) {
                    $dailydoseobj->share_content = $dailydoseobj->content;
                }
                $image_dailydose = Settings::content_single_images($dailydoseobj->share_content);
                $image_path = str_replace(Settings::$image_path, "", $image_dailydose);
                $image_path = str_replace("http://champs21.com/", "", $image_path);

                $image_main_path = Settings::$main_path . $image_path;

                $size = getimagesize($image_main_path);

                $fileParts = pathinfo($image_main_path);

                header("Content-Disposition: attachment; filename=" . $fileParts['filename'] . "." . $fileParts['extension']);
                header("Content-Type: {$size['mime']}");
                header("Content-Length: " . filesize($image_main_path));
                readfile($image_main_path);
            }
        }
    }

    public function actionask() {
        $name = Yii::app()->request->getPost('name');
        $question = Yii::app()->request->getPost('question');
        $date = date("Y-m-d");
        if ($name && $question) {
            $asktheanchor = new TdsAskTheAnchor();
            $asktheanchor->name = $name;
            $asktheanchor->question = $question;
            $asktheanchor->date = $date;
            $asktheanchor->save();
            $response['data']['msg'] = "Successfully Saved";
            $response['status']['code'] = 200;
            $response['status']['msg'] = "Success";
        } else {
            $response['status']['code'] = 400;
            $response['status']['msg'] = "Bad Request";
        }
        echo CJSON::encode($response);
        Yii::app()->end();
    }

    public function actiongetEpisode() {
        $page_number = Yii::app()->request->getPost('page_number');
        $page_size = Yii::app()->request->getPost('page_size');
        if (empty($page_number)) {
            $page_number = 1;
        }
        if (empty($page_size)) {
            $page_size = 10;
        }
        $episode = new TdsScienceRocksWinner();
        $response['data']['total'] = $episode->getEpisodeCount();
        $has_next = false;
        if ($response['data']['total'] > $page_number * $page_size) {
            $has_next = true;
        }
        $response['data']['has_next'] = $has_next;

        $response['data']['episodes'] = $episode->getEpisode($page_number, $page_size);
        $response['status']['code'] = 200;
        $response['status']['msg'] = "Success";
        echo CJSON::encode($response);
        Yii::app()->end();
    }

    public function actiongetAnchorQuestion() {
        $page_number = Yii::app()->request->getPost('page_number');
        $page_size = Yii::app()->request->getPost('page_size');
        if (empty($page_number)) {
            $page_number = 1;
        }
        if (empty($page_size)) {
            $page_size = 10;
        }
        $asktheanchor = new TdsAskTheAnchor();
        $response['data']['total'] = $asktheanchor->getQuestionCount();
        $has_next = false;
        if ($response['data']['total'] > $page_number * $page_size) {
            $has_next = true;
        }
        $response['data']['has_next'] = $has_next;

        $response['data']['asktheanchor'] = $asktheanchor->getQuestion($page_number, $page_size);
        $response['status']['code'] = 200;
        $response['status']['msg'] = "Success";
        echo CJSON::encode($response);
        Yii::app()->end();
    }

    public function actiongetDailyDose() {
        $dailydose = new TdsDailydose();
        $response['data']['dailydose'] = $dailydose->getdailydose();
        $response['status']['code'] = 200;
        $response['status']['msg'] = "Success";
        echo CJSON::encode($response);
        Yii::app()->end();
    }

    public function actiongetDailyDoseHistory() {
        $page_number = Yii::app()->request->getPost('page_number');
        $page_size = Yii::app()->request->getPost('page_size');
        if (empty($page_number)) {
            $page_number = 1;
        }
        if (empty($page_size)) {
            $page_size = 10;
        }
        $dailydose = new TdsDailydose();
        $response['data']['total'] = $dailydose->getdailydoseCount();
        $has_next = false;
        if ($response['data']['total'] > $page_number * $page_size) {
            $has_next = true;
        }
        $response['data']['has_next'] = $has_next;

        $response['data']['dailydose'] = $dailydose->getdailydoseAll($page_number, $page_size);
        $response['status']['code'] = 200;
        $response['status']['msg'] = "Success";
        echo CJSON::encode($response);
        Yii::app()->end();
    }

    public function actionsavescore() {
        $score = Yii::app()->request->getPost('score');
        $time = Yii::app()->request->getPost('time');
        $level_id = Yii::app()->request->getPost('level_id');
        $name = Yii::app()->request->getPost('name');
        $phone = Yii::app()->request->getPost('phone');
        $profile_image = Yii::app()->request->getPost('profile_image');
        $email = Yii::app()->request->getPost('email');
        $auth_id = Yii::app()->request->getPost('auth_id');


        if ($score !== false && $time && $level_id && $name && $email && $auth_id) {
            $userobj = new TdsScienceRocksUser();
            $user_id = $userobj->getUserId($auth_id, $name, $email, $phone, $profile_image);
            if ($user_id) {
                $allscore = new TdsScienceRocksScores();
                $allscore->user_id = $user_id;
                $allscore->level_id = $level_id;
                $allscore->score = $score;
                $allscore->time = $time;
                $allscore->date = date("Y-m-d H:i:s");
                $allscore->save();
                if ($allscore->id) {
                    $highscore = new TdsScienceRocksHighscore();
                    $return = $highscore->savescoreAndReturn($user_id, $score, $time, $level_id);

                    if ($return) {
                        $total_score = new TdsScienceRocksTotalScore();
                        $returntotal = $total_score->savescoreAndReturn($user_id, $return[1], $time, $return[0]->total_time);
                        $response['data']['bestscore'] = $return[0];
                        $response['status']['code'] = 200;
                        $response['status']['msg'] = "Success";
                    } else {
                        $response['status']['code'] = 400;
                        $response['status']['msg'] = "Bad Request";
                    }
                } else {
                    $response['status']['code'] = 400;
                    $response['status']['msg'] = "Bad Request";
                }
            } else {
                $response['status']['code'] = 400;
                $response['status']['msg'] = "Bad Request";
            }
        } else {
            $response['status']['code'] = 400;
            $response['status']['msg'] = "Bad Request";
        }
        echo CJSON::encode($response);
        Yii::app()->end();
    }

    public function actionindex() {
        $category = new TdsScienceRocksCategory();

        $response['data']['topics'] = $category->getCategory();
        $response['status']['code'] = 200;
        $response['status']['msg'] = "Success";
        echo CJSON::encode($response);
        Yii::app()->end();
    }

    public function actiongetLevel() {
        $category_id = Yii::app()->request->getPost('topic_id');
        if ($category_id) {
            $topics = new TdsScienceRocksTopics();

            $response['data']['level'] = $topics->getTopics($category_id);
            $response['status']['code'] = 200;
            $response['status']['msg'] = "Success";
        } else {
            $response['status']['code'] = 400;
            $response['status']['msg'] = "Bad Request";
        }
        echo CJSON::encode($response);
        Yii::app()->end();
    }

    public function actiongetQuestion() {
        $level_id = Yii::app()->request->getPost('level_id');
        if ($level_id) {
            $question = new TdsScienceRocksQuestion();

            $response['data']['questions'] = $question->getQuesTionAndAnswer($level_id);
            $response['status']['code'] = 200;
            $response['status']['msg'] = "Success";
        } else {
            $response['status']['code'] = 400;
            $response['status']['msg'] = "Bad Request";
        }
        echo CJSON::encode($response);
        Yii::app()->end();
    }

}
