<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class SpelltvController extends Controller
{

    /**
     * @return array action filters
     */
    public function filters()
    {
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
    public function accessRules()
    {
        return array(
            array('allow', // allow authenticated user to perform 'create' and 'update' actions
                'actions' => array('index','getleaderboard','savespellingtv'),
                'users' => array('*'),
            ),
            array('deny', // deny all users
                'users' => array('*'),
            ),
        );
    }
    public function actionindex()
    {
        //need to build
         $user_id = Yii::app()->request->getPost('free_id');
         if($user_id)
         { 
            
            $highscore = new Spelltvhighscore();
            $current_score = 0;
            $cache_name_userdata = "YII-SPELLTV-USERDATA-" . $user_id;
            $response = Settings::getSpellTvCache($cache_name_userdata);
            if (isset($response) && isset($response['current_score']))
            {
                $current_score = $response['current_score'];
            }
            else
            {
                $user_score_data = $highscore->getUserScore($user_id);
                if ($user_score_data)
                {
                    $current_score = $user_score_data->score;
                }
            }

            $checkpoint = floor($current_score/Settings::$checkpointValue)*Settings::$checkpointValue;



            $rresponse['data']['user_checkpoint'] = $checkpoint;
            $rresponse['status']['code'] = 200;
            $rresponse['status']['msg'] = "Success";
               
           
        }
        else
        {
            $rresponse['status']['code'] = 400;
            $rresponse['status']['msg'] = "Bad Request";
        }
        echo CJSON::encode($rresponse);
        Yii::app()->end();
    }
    public function actiongetLeaderboard()
    {
        $limit = Yii::app()->request->getPost('limit');
        $user_id = Yii::app()->request->getPost('free_id');
        
        if(!$limit)
        {
            $limit = 30;
        } 
//        if($user_id)
//        {
            $user_division = "";
            $user_division_main = "";
            $current_score = "N/A";
            $highscore = new Spelltvhighscore();
            if($user_id)
            {
                $objUser = new Freeusers();
                $user_data = $objUser->findByPk($user_id);
                
                if($user_data->division)
                {
                    $user_division = $user_data->division;
                }
                else
                {
                    $user_division = "Dhaka";
                }    
                $country = $user_data->tds_country_id;
            
            
                $user_division = ucfirst($user_division);
                
                $current_score = 0;
                $current_time = 0;
                $cache_name_userdata = "YII-SPELLTV-USERDATA-" . $user_id;
                $response = Settings::getSpellTvCache($cache_name_userdata);
                if (isset($response) && isset($response['current_score']))
                {
                    $current_score = $response['current_score'];
                    $current_time = $response['current_time'];
                }
                else
                {
                    $user_score_data = $highscore->getUserScore($user_id);
                    if ($user_score_data)
                    {
                        $current_score = $user_score_data->score;
                        $current_time = $user_score_data->test_time;
                    }
                }




                $rresponse['data']['rank'] = $highscore->getUserRank($current_score, $current_time);
            }
            else
            {
                $rresponse['data']['rank'] = "N/A";
            }    
            
            $arUserScores = $highscore->getLeaderBoard($limit);
            $rresponse['data']['leaderboard'] = (array)$arUserScores;
            $rresponse['data']['division'] = $user_division;
            $rresponse['data']['best_score'] = $current_score;
            $rresponse['status']['code'] = 200;
            $rresponse['status']['msg'] = "Success";
           
//        }
//        else
//        {
//            $rresponse['status']['code'] = 400;
//            $rresponse['status']['msg'] = "Bad Request";
//        }
        echo CJSON::encode($rresponse);
        Yii::app()->end();
    }
    
    public function actionsaveSpellingTv()
    {
        
        if(Yii::app()->request->getPost('left') && Yii::app()->request->getPost('right') && Yii::app()->request->getPost('method')
                 && Yii::app()->request->getPost('operator') && Yii::app()->request->getPost('send_id') )
        {
            if(Yii::app()->request->getPost('total_time'))
            {
                $total_time = Yii::app()->request->getPost('total_time');
            }
            else
            {
                $total_time = 0;
            }  
            if(Yii::app()->request->getPost('score'))
            {
                $score = Yii::app()->request->getPost('score');
            }
            else
            {
                $score = 0;
            }
            if(Yii::app()->request->getPost('is_cheater'))
            {
                $cheat = 1;
            }
            else
            {
                $cheat = 0;
            }
         
            $objParams = (object) null;
            $objParams->left = Yii::app()->request->getPost('left');
            $objParams->right = Yii::app()->request->getPost('right');
            $objParams->method = Yii::app()->request->getPost('method');
            $objParams->operator = Yii::app()->request->getPost('operator');
            $objParams->send_id = Yii::app()->request->getPost('send_id');

            $objParams->total_time = $total_time;
            $objParams->score = $score;
            $objParams->isCheater = $cheat;

            $arUserData = array();
            $arUserData['rank'] = "UnRanked";
            $arUserData['highestScore'] = 0;
            $data = Yii::app()->request->getPost('free_id');
            $data = (int)$data;
            
            
            $valid_user = Settings::authorizeUserCheckSpellTv($objParams->left, $objParams->right, $objParams->method, $objParams->operator, $objParams->send_id, $data);
            
            if ($valid_user && !Settings::dateOverSpellTv())
            {
                $iUserId = $data;
                $objUser = new Freeusers();
                $user_data = $objUser->findByPk($iUserId);

                if ($user_data)
                {
                    $cache_name_userdata = "YII-SPELLTV-USERDATA-" . $iUserId;
                    $response = Settings::getSpellTvCache($cache_name_userdata);

                    $current_score = 0;
                    $current_time = 0;

                    $highscore = new Spelltvhighscore();
                    $prev_id = 0;
                    $play_total_time = 0;
                    if ($response !== FALSE)
                    {

                        if (isset($response) && isset($response['current_score']) && isset($response['current_time']) && isset($response['prev_id']) && isset($response['play_total_time']))
                        {

                            $current_score = $response['current_score'];
                            $current_time = $response['current_time'];
                            $prev_id = $response['prev_id'];
                            $play_total_time = $response['play_total_time'] = $response['play_total_time'] + $objParams->total_time;
                            Settings::setSpellTvCache($cache_name_userdata, $response);
                        }
                        else
                        {
                            $user_score_data = $highscore->getUserScore($iUserId);
                            if ($user_score_data)
                            {
                                $response['current_score'] = $current_score = $user_score_data->score;
                                $response['current_time'] = $current_time = $user_score_data->test_time;
                                $response['prev_id'] = $prev_id = $user_score_data->id;
                                $response['play_total_time'] = $play_total_time = $user_score_data->play_total_time + $objParams->total_time;

                                Settings::setSpellTvCache($cache_name_userdata, $response);
                            }
                            else
                            {
                                $play_total_time = $objParams->total_time;
                            }    
                        }
                    }
                    else
                    {
                        $response = array();

                        $user_score_data = $highscore->getUserScore($iUserId);
                        if ($user_score_data)
                        {
                            $response['current_score'] = $current_score = $user_score_data->score;
                            $response['current_time'] = $current_time = $user_score_data->test_time;
                            $response['prev_id'] = $prev_id = $user_score_data->id;
                            $response['play_total_time'] = $play_total_time = $user_score_data->play_total_time + $objParams->total_time;
                            Settings::setSpellTvCache($cache_name_userdata, $response);
                        }
                        else
                        {
                            $play_total_time = $objParams->total_time;
                        }
                    }

                    if  ( $play_total_time > $current_score && ( $objParams->score > $current_score || ($objParams->score == $current_score && $objParams->total_time < $current_time) ))
                    {

                        $score_for_rank = $objParams->score;
                        $time_for_rank = $objParams->total_time;

                        if ($prev_id)
                        {
                            $highscore = $highscore->findByPk($prev_id);
                        }
                        $highscore->userid = $iUserId;
                        $highscore->test_time = $objParams->total_time;
                        $highscore->enddate = time();
                        $highscore->score = (int) $objParams->score;
                        $highscore->is_cheat = $objParams->isCheater;
                        $highscore->play_total_time = $play_total_time;
                        $highscore->spell_year = date('Y');
                        $highscore->division = strtolower($user_data->division);
                        $highscore->country = $user_data->tds_country_id;
                        $highscore->from_mobile = 1;
                        
                        $highscore->save();

                        $response['current_score'] = $score_for_rank;
                        $response['current_time'] = $time_for_rank;
                        $response['prev_id'] = $highscore->id;
                        $response['play_total_time'] = $play_total_time;
                        Settings::setSpellTvCache($cache_name_userdata, $response);
                    }
                    else
                    {
                        $score_for_rank = $current_score;
                        $time_for_rank = $current_time;
                    }
                    $arUserData['highestScore'] = $score_for_rank;
                    $arUserData['rank'] = $highscore->getUserRank($score_for_rank, $time_for_rank);
                    
                    
                    
                    $rresponse['data'] = $arUserData;
                    $rresponse['status']['code'] = 200;
                    $rresponse['status']['msg'] = "Success";
                }
                else
                {
                    $rresponse['status']['code'] = 400;
                    $rresponse['status']['msg'] = "Bad Request";
                }    
            }
            else
            {
                $rresponse['status']['code'] = 400;
                $rresponse['status']['msg'] = "Bad Request";
            }    
        }
        else
        {
            $rresponse['status']['code'] = 400;
            $rresponse['status']['msg'] = "Bad Request";
        }
        echo CJSON::encode($rresponse);
        Yii::app()->end();
    }
}

