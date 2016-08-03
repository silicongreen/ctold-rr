<?php

/**
 * Service Class for Zend_AMF
 */
class Service
{


    public function logonCheck()
    {
        $objfreeuser = new Freeusers();
        $cookies = Yii::app()->request->cookies;
        if (isset($cookies['champs_session']))
        {
            $user_cookie = $cookies['champs_session']->value;
        }
        $data = $objfreeuser->getFreeuserByCookie(func_get_arg(0));
        return ( is_int($data)) ? TRUE : FALSE;
    }

    public function getWebScores($iLimit = 10)
    {
        $objfreeuser = new Freeusers();
        $data = $objfreeuser->getFreeuserByCookie(func_get_arg(1));

        if ($data!==FALSE)
        {
            $user_id = $data;
        }
        else
        {
            return FALSE;
        }
        $objUser = new Freeusers();
        $user_data = $objUser->findByPk($user_id);

        if ($user_data)
        {
            $highscore = new Highscore();
            $arUserScores = $highscore->getLeaderBoard($iLimit, $user_data->division, $user_data->tds_country_id);
            return $arUserScores;
        }
        else
        {
            return FALSE;
        }
    }

    public function getMode()
    {
        
       
        $arUserMode = array();
        $arUserMode['cPlayMode'] = 'p';
        $arUserMode['bIsNew'] = 0;
        $arUserMode['user_checkpoint'] = 0;
        $arUserMode['user_checkpoint_score'] = 0;
        $arUserMode['word_count'] = 0;
        $arUserMode['remaining_word'] = 0;
        $arUserMode['current_level'] = 0;
        $arUserMode['level_status'] = (object) NULL;
        $arUserMode['user_id_tokens'] = (object) NULL;
        
        $arUserMode['spellingbeeConfig'] = Settings::$spellingbeeConfig;
        $arUserMode['alwaysAgreementCheck'] = Settings::$alwaysAgreementCheck;
        $arUserMode['checkPointSize'] = Settings::$checkPointSize;
        $arUserMode['dailyWord'] = Settings::$dailyWord;
        $arUserMode['easyWord'] = Settings::$easyWord;
        $arUserMode['normalWord'] = Settings::$normalWord;
        $arUserMode['hardWord'] = Settings::$hardWord;
        $arUserMode['arg'] = func_get_arg(0);

        $objfreeuser = new Freeusers();
        $data = $objfreeuser->getFreeuserByCookie(func_get_arg(0));
      
        
        $arUserMode['arg'] = $data;
        
        if ($data == FALSE)
        {
            $data = 3920;
        }
        
        if ($data !== FALSE)
        {
            $iUserId = $data;
            $cache_name_agreement = "YII-SPELLINGBEE-USERAGREMENT";
            $response = Settings::getSpellingBeeCache($cache_name_agreement);
            if ($response !== false)
            {

                if (in_array($data, $response))
                {
                    $arUserMode['bIsNew'] = 1;
                }
            }
            
            $cache_name_old_userdata = "YII-SPELLINGBEE-USERDATA";
            $cache_name_userdata = "YII-SPELLINGBEE-USERDATA-" . $iUserId;
            $response = Settings::getSpellingBeeCache($cache_name_userdata);
            
            if(isset($response) && isset($response))
            {
                if(isset($response['current_score'])) {
                    $current_score = (int)$response['current_score'];
                    $rem = (int)$current_score % (int)Settings::$checkPointSize;
                    $check_point_score = $current_score - $rem;
                    $arUserMode['user_checkpoint_score'] = $check_point_score;
                    $arUserMode['user_checkpoint'] = floor($check_point_score / (int)Settings::$checkPointSize);
                } else {
                    $arUserMode['user_checkpoint'] = 0;
                    $arUserMode['user_checkpoint_score'] = 0;
                }
                
                if(isset($response['remaining_word']))
                {
                    $arUserMode['remaining_word'] = $response['remaining_word'];
                }
                if(isset($response['current_level']))
                {
                    $arUserMode['current_level'] = $response['current_level'];
                }
                if(isset($response['total_time']))
                {
                    $arUserMode['total_time'] = $response['total_time'];
                }
                
            }
            
            $cache_name_old_userword = "YII-SPELLINGBEE-USERWORD";
            $cache_name_userword = "YII-SPELLINGBEE-USERWORD-" . $iUserId;
            $response = Settings::getSpellingBeeCache($cache_name_userword);
            
            if(isset($response) && isset($response) && isset($response['words']))
            {
                $arUserMode['word_count'] = count($response['words']);
            }
            $cache_name_old_status = "YII-SPELLINGBEE-LEVEL-STATUS";
            $cache_name_status = "YII-SPELLINGBEE-LEVEL-STATUS-" . $iUserId;
            $responsecache = Settings::getSpellingBeeCache($cache_name_status);
            
            for($i =0; $i<4; $i++)
            {
                $level_name = "level".$i;
                $arUserMode['level_status']->$level_name = 0;
                if(isset($responsecache) && isset($responsecache) && isset($responsecache[$i]))
                {
                    $arUserMode['level_status']->$level_name = 1;
                }
                
            }

            $arUserMode['cPlayMode'] = 'c';
            $arUserMode['user_id_tokens'] = Settings::createUserToken($data);
            
            Settings::clearCurrentWord($iUserId);
            
            
            
            
        }

        return (object) $arUserMode;
    }

    public function saveUserAgreeMent()
    {
        $arUserMode = array();
        $arUserMode['bTerms'] = FALSE;

        $objfreeuser = new Freeusers();
        $data = $objfreeuser->getFreeuserByCookie(func_get_arg(0));

        if ($data!==FALSE)
        {
            $user_array = array();
            $cache_name_agreement = "YII-SPELLINGBEE-USERAGREMENT";
            $response = Settings::getSpellingBeeCache($cache_name_agreement);
            if ($response === false)
            {
                array_push($user_array, $data);
            }
            else
            {
                $user_array = $response;
                if (!in_array($data, $response))
                {
                    array_push($user_array, $data);
                }
            }
            Settings::setSpellingBeeCache($cache_name_agreement, $user_array);

            $arUserMode['bTerms'] = TRUE;
        }
        return (object) $arUserMode;
    }
    public function saveCheckPoint($objParams)
    {
          
        $words_id = $objParams->words;
        
//        if(isset($objParams->old_words))
//        $old_words_id = $objParams->old_words;
        
        $total_time = $objParams->total_time;
        $current_level = $objParams->current_level;
        $remaining_word = $objParams->remaining_word;
      
        $checkpoint = $objParams->checkpoint;
        $objfreeuser = new Freeusers();
        $data = $objfreeuser->getFreeuserByCookie(func_get_arg(1));
         
       
        $valid_user = FALSE;
        if ($data!==FALSE && $words_id && $checkpoint)
        {
            
            $autorize_check = Settings::authorizeUserCheck($objParams->left, $objParams->right, $objParams->method, $objParams->operator, $objParams->send_id, $data);
            #$autorize_check = TRUE;
            if ($autorize_check)
            {
                $valid_user = TRUE;
            }
     
        }
      
        
        if($valid_user)
        {
            $cache_name_userword = "YII-SPELLINGBEE-USERWORD-" . $data;
            $response = Settings::getSpellingBeeCache($cache_name_userword);
            
            $cache_name_userword_played = "YII-SPELLINGBEE-USERWORD-PLAYED-" . $data;
            $response_played = Settings::getSpellingBeeCache($cache_name_userword_played);
            
            $cache_name_userdata = "YII-SPELLINGBEE-USERDATA-" . $data;
            $check = Settings::getSpellingBeeCache($cache_name_userdata);
            $score_count = 0;
            
            if(isset($check))
            {
                if(isset($check['current_score'])) {
                    $current_score = (int)$check['current_score'];
                    $rem = (int)$current_score % (int)Settings::$checkPointSize;
                    $check_point_score = $current_score - $rem;
                    $check['user_checkpoint_score'] = $check_point_score;
                } else {
                    $check['user_checkpoint_score'] = 0;
                }
                if(isset($check['user_checkpoint_score']))
                {
                    $score_count = $check['user_checkpoint_score'];
                }

                if(isset($check['total_time']))
                {
                    $total_time = $total_time+$check['total_time'];
                } 
            }
            
            $word_id_array = explode(",", $words_id);
            if(count($word_id_array) > 0)
            {
                foreach($word_id_array as $value)
                {
                    if($value!="" && $value != '0')
                    {
                        if(!in_array($value, $response_played['words'])) {
                            $response_played['words'][] = $value;
                            $response['words'][] = $value;
                        }
                        $score_count++;
                    }
                }
            }
            
            
            Settings::setSpellingBeeCache($cache_name_userword, $response);
            Settings::setSpellingBeeCache($cache_name_userword_played, $response_played);
            
            $highscore = new Highscore();
            $user_score_data = $highscore->getUserScore($data);
            
            $score_count = (int)$objParams->checkpoint * (int)Settings::$checkPointSize;
            
            $current_score = $score_count;
            
            if ( empty($user_score_data) || ($current_score > $user_score_data->score) )
            {
                if(!empty($user_score_data)) {
                    $highscore = $highscore->findByPk($user_score_data->id);
                }
                $objUser = new Freeusers();
                $user_data = $objUser->findByPk($data);
                
                $highscore->userid = $data;
                $highscore->test_time = $total_time;
                $highscore->enddate = time();
                $highscore->score = (int) $current_score;
                $highscore->is_cheat = 0;
                if (empty($user_score_data)) {
                    $play_total_time = (int)$total_time;
                } else {
                    $play_total_time = (int)$user_score_data->test_time + (int)$total_time;
                }
                $highscore->play_total_time = $play_total_time;
                $highscore->spell_year = date('Y');
                $highscore->division = strtolower($user_data->division);
                $highscore->country = $user_data->tds_country_id;
                $highscore->save();

                $check['current_score'] = $current_score;
                $check['current_time'] = $total_time;
                $check['prev_id'] = (empty($user_score_data)) ? 0 : $user_score_data->id;
                $check['play_total_time'] = $play_total_time;
            }
            
            $check['total_time'] = $total_time;
            $check['remaining_word'] = $remaining_word;
            $check['current_level'] = $current_level;
            $check['user_checkpoint_score'] = $score_count;
            $check['user_checkpoint'] = $checkpoint;
            Settings::setSpellingBeeCache($cache_name_userdata, $check);
            
            Settings::clearCurrentWord($data);
            
            return TRUE;
        }
        return FALSE;
    }

    public function getWords($objParams)
    {
       
        $objfreeuser = new Freeusers();
        $data = $objfreeuser->getFreeuserByCookie(func_get_arg(1));
        $valid_user = FALSE;
        $year = 0;
        if ($data!==FALSE)
        {

            $valid_user = TRUE;
     
        }
        $arWords = "";
        
        if(!$valid_user) {
            $year = 2012;
        }
        
        $valid_user = TRUE;
        if($valid_user)
        {
            $iUserId = $data;
            $iLevelId = $objParams->level;
            $user_word_played = array();
            $cache_name_userword = "YII-SPELLINGBEE-USERWORD-" . $iUserId;
            $response = Settings::getSpellingBeeCache($cache_name_userword);
           
    
            if (isset($response) && isset($response['words']))
            {
                $user_word_played = $response['words'];
            }
            
          
            $spbobj = new Spellingbee();
            $arWords = $spbobj->getWordsByLevel($iLevelId, $objParams->size,$user_word_played,$iUserId, $year);
          
            $words_array['words'] = array();
            $words_array['word_complete'] = $arWords['word_complete'];
            $words_array['level'] = $arWords['level'];
            
            if(count($arWords['words'])>0)
            {
               $i = 0;
                foreach($arWords['words'] as $value)
                {
                    foreach($value as $key=>$v)
                    {                      
                       $words_array['words'][$i][$key] =$v; 

                    } 
                    $i++;
                 } 
                 $words_array['words'] = (object)$words_array['words'];
            }  
           
            return (object)$words_array;
           
        }
        

    }

    

    public function getXML()
    {
        
    }

    public function getBalance()
    {
        $objConn = Champs21_Db_Connection::factory()->getMasterConnection();
        $objSpellbeeDao = Champs21_Model_Dao_Factory::getInstance()->setModule('spellingbee')->getSpellingbeeDao();
        $objSpellbeeDao->setDbConnection($objConn);
        $arBalance = $objSpellbeeDao->getSpellbeeBalance();
        return $arBalance;
    }

    public function saveSpellingBee($objParams)
    {

        $objfreeuser = new Freeusers();
        $data = $objfreeuser->getFreeuserByCookie(func_get_arg(1));
        $valid_user = FALSE;
        if ($data!==FALSE)
        {
            $autorize_check = Settings::authorizeUserCheck($objParams->left, $objParams->right, $objParams->method, $objParams->operator, $objParams->send_id, $data);
           
            if ($autorize_check)
            {
                $valid_user = TRUE;
                
                $iUserId = $data;
                $objUser = new Freeusers();
                $user_data = $objUser->findByPk($iUserId);
            }
        }

        $arUserData = array();
        $arUserData['rank'] = "UnRanked";
        $arUserData['highestScore'] = 0;
        
        if (!empty($user_data) && $user_data->user_type != 2) {
            $valid_user = false;
        }
        
        if (!empty($user_data) && $valid_user)
        {
           
            if ($user_data)
            {
                $user_word_played = array();
                $iScore = (int) $objParams->score;
                
                $cache_name_userdata = "YII-SPELLINGBEE-USERDATA-" . $iUserId;
                $response_check = Settings::getSpellingBeeCache($cache_name_userdata);

                if(isset($response_check) && isset($response_check))
                {
                    if(isset($response_check['current_score'])) {
                        $current_score = (int)$response_check['current_score'];
                        $rem = (int)$current_score % (int)Settings::$checkPointSize;
                        $check_point_score = $current_score - $rem;
                        
                    } else {
                        $check_point_score = 0;
                    }

                }
                
                $iScore = $iScore - $check_point_score;
                if($iScore>Settings::$checkPointSize)
                {
                    return NULL;
                }
                
                $cache_name_word = "YII-SPELLINGBEE-CURRENTUSERWORD-" . $iUserId;
                $responseword = Settings::getSpellingBeeCache($cache_name_word);
                if(isset($responseword) && isset($responseword['words'])) {
                    $i = 0;
                    foreach ($responseword['words'] as $words) {
                        $user_word_played[] = $words;
                        $i++;
                        if($i > $iScore) {
                            break;
                        }
                    }
                }
                
                Settings::clearCurrentWord($iUserId);
                
                $cache_name_userword = "YII-SPELLINGBEE-USERWORD-" . $iUserId;
                $response = Settings::getSpellingBeeCache($cache_name_userword);
                
                $current_words = array('words' => $user_word_played);
                
                if(isset($response) && isset($response['words'])) {
                    foreach ($user_word_played as $word) {
                        $response['words'][] = $word;
                    }
                } else {
                    $response = $current_words;
                }
                Settings::setSpellingBeeCache($cache_name_userword, $response);
                
                $cache_name_userword_played = "YII-SPELLINGBEE-USERWORD-PLAYED-" . $iUserId;
                $response = Settings::getSpellingBeeCache($cache_name_userword_played);
                
                if(isset($response) && isset($response['words'])) {
                    foreach ($user_word_played as $word) {
                        $response['words'][] = $word;
                    }
                } else {
                    $response = $current_words;
                }
                Settings::setSpellingBeeCache($cache_name_userword_played, $response);
                
                $cache_name_old_userdata = "YII-SPELLINGBEE-USERDATA";
                $cache_name_userdata = "YII-SPELLINGBEE-USERDATA-" . $iUserId;
                $response = Settings::getSpellingBeeCache($cache_name_userdata);

                $current_score = 0;
                $current_time = 0;

                $highscore = new Highscore();
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
                        Settings::setSpellingBeeCache($cache_name_userdata, $response);
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

                            Settings::setSpellingBeeCache($cache_name_userdata, $response);
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
                        Settings::setSpellingBeeCache($cache_name_userdata, $response);
                    }
                    else
                    {
                        $play_total_time = $objParams->total_time;
                    }
                }

                if ($objParams->score > $current_score || ($objParams->score == $current_score && $objParams->total_time < $current_time))
                {
                   
                    $score_for_rank = $objParams->score;
                    $time_for_rank = $objParams->total_time;

//                    if ($prev_id)
//                    {
//                        $highscore = $highscore->findByAttributes(array('userid' => $iUserId));
                        $highscore = $highscore->getUserScore($iUserId,true);
//                    }
                    if(empty($highscore)) {
                        $highscore = new Highscore();
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
                    $highscore->from_web = 1;
                    $highscore->save();
                   
                    
                    $response['current_score'] = $score_for_rank;
                    $response['current_time'] = $time_for_rank;
                    $response['prev_id'] = $highscore->id;
                    $response['play_total_time'] = $play_total_time;
                    Settings::setSpellingBeeCache($cache_name_userdata, $response);
                }
                else
                {
                    $score_for_rank = $current_score;
                    $time_for_rank = $current_time;
                }
                $arUserData['highestScore'] = $score_for_rank;
                $arUserData['rank'] = $highscore->getUserRank($score_for_rank, $time_for_rank,$user_data->tds_country_id, strtolower($user_data->division));
            }
        } else {
            $arUserData['highestScore'] = 'N/A';
            $arUserData['rank'] = 'N/A';
        }

        return (object) $arUserData;
    }  

}
