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
        if (isset($cookies['c21_session']))
        {
            $user_cookie = $cookies['c21_session']->value;
        }
        $data = $objfreeuser->getFreeuserByCookie();
        return ( is_int($data)) ? TRUE : FALSE;
    }

    public function getWebScores($iLimit = 10)
    {
        $objfreeuser = new Freeusers();
        $data = $objfreeuser->getFreeuserByCookie();

        if (isset($data) && is_int($data))
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
            $arUserScores = $highscore->getLeaderBoard($iLimit, $user_data->district, $user_data->tds_country_id);
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
        $arUserMode['user_id_tokens'] = (object) NULL;
        $arUserMode['level_status'] = (object) NULL;

        $objfreeuser = new Freeusers();
        $data = $objfreeuser->getFreeuserByCookie();
      

        if ($data && is_int($data))
        {
            $iUserId = $data;
            $cache_name = "YII-SPELLINGBEE-USERAGREMENT";
            $response = Settings::getSpellingBeeCache($cache_name);
            if ($response !== false)
            {

                if (in_array($data, $response))
                {
                    $arUserMode['bIsNew'] = 1;
                }
            }
            
            $cache_name = "YII-SPELLINGBEE-USERDATA";
            $response = Settings::getSpellingBeeCache($cache_name);
            if (isset($response[$iUserId]) && isset($response[$iUserId]['user_checkpoint']))
            {
                $arUserMode['user_checkpoint'] = $response[$iUserId]['user_checkpoint'];
            }

            $arUserMode['cPlayMode'] = 'c';
            $arUserMode['user_id_tokens'] = Settings::createUserToken($data);
            
            
            for($i =0; $i<4; $i++)
            {
                $arUserMode['level_status']->$i = 1;
                if(isset($levelstatus) && isset($levelstatus[$iUserId]) && isset($levelstatus[$iUserId][$i]))
                {
                    $arUserMode['level_status']->$i = 0;
                }
                
            }
            
            Settings::clearCurrentWord($iUserId);
            
            
            
            
        }

        return (object) $arUserMode;
    }

    public function saveUserAgreeMent()
    {
        $arUserMode = array();
        $arUserMode['bTerms'] = FALSE;

        $objfreeuser = new Freeusers();
        $data = $objfreeuser->getFreeuserByCookie();

        if ($data && is_int($data))
        {
            $user_array = array();
            $cache_name = "YII-SPELLINGBEE-USERAGREMENT";
            $response = Settings::getSpellingBeeCache($cache_name);
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
            Settings::setSpellingBeeCache($cache_name, $user_array);

            $arUserMode['bTerms'] = TRUE;
        }
        return (object) $arUserMode;
    }
    public function saveCheckPoint($objParams)
    {
        $words_id = $objParams->words;
      
        $checkpoint = $objParams->checkpoint;
        $objfreeuser = new Freeusers();
        $data = $objfreeuser->getFreeuserByCookie();
       
        $valid_user = FALSE;
        if ($data && is_int($data) && $words_id && $checkpoint)
        {
            
            $autorize_check = Settings::authorizeUserCheck($objParams->left, $objParams->right, $objParams->method, $objParams->operator, $objParams->send_id, $data);
            $autorize_check = TRUE;
            if ($autorize_check)
            {
                $valid_user = TRUE;
            }
     
        }
        
        if($valid_user)
        {
            
            $word_id_array = explode(",", $words_id);
            $cache_name = "YII-SPELLINGBEE-USERWORD";
            $response = Settings::getSpellingBeeCache($cache_name);
           
            foreach($word_id_array as $value)
            {
                $response[$data]['words'][] = $value;
                
            }
            
            Settings::setSpellingBeeCache($cache_name, $response);
            
            $cache_name = "YII-SPELLINGBEE-USERDATA";
            $check = Settings::getSpellingBeeCache($cache_name);
            $check[$data]['user_checkpoint'] = $checkpoint;
            Settings::setSpellingBeeCache($cache_name, $check);
            
            Settings::clearCurrentWord($iUserId);
            
            return TRUE;
            
            
        }
        return FALSE;
        
         
        
        
    }

    public function getWords($objParams)
    {
       
        $objfreeuser = new Freeusers();
        $data = $objfreeuser->getFreeuserByCookie();
        $valid_user = FALSE;
        if ($data && is_int($data))
        {

            $valid_user = TRUE;
     
        }
        $arWords = "";
        if($valid_user)
        {
            $iUserId = $data;
            $iLevelId = $objParams->level;
            $user_word_played = array();
            $cache_name = "YII-SPELLINGBEE-USERWORD";
            $response = Settings::getSpellingBeeCache($cache_name);
           
    
            if (isset($response[$iUserId]) && isset($response[$iUserId]) && isset($response[$iUserId]['words']))
            {
                $user_word_played = $response[$iUserId]['words'];
            }
            
          
            $spbobj = new Spellingbee();
            $arWords = $spbobj->getWordsByLevel($iLevelId, $objParams->size,$user_word_played,$iUserId);
          
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
        $data = $objfreeuser->getFreeuserByCookie();
        $valid_user = FALSE;
        if ($data && is_int($data))
        {
            $autorize_check = Settings::authorizeUserCheck($objParams->left, $objParams->right, $objParams->method, $objParams->operator, $objParams->send_id, $data);
           
            if ($autorize_check)
            {
                $valid_user = TRUE;
            }
        }

        $arUserData = array();
        $arUserData['rank'] = "UnRanked";
        $arUserData['highestScore'] = 0;
       
        if ($valid_user)
        {
           
            $iUserId = $data;
            $objUser = new Freeusers();
            $user_data = $objUser->findByPk($iUserId);

            if ($user_data)
            {
                Settings::clearCurrentWord($iUserId);
                $cache_name = "YII-SPELLINGBEE-USERDATA";
                $response = Settings::getSpellingBeeCache($cache_name);

                $current_score = 0;
                $current_time = 0;

                $highscore = new Highscore();
                $prev_id = 0;
                $play_total_time = 0;
               
                if ($response !== FALSE)
                {
                    
                    if (isset($response[$iUserId]) && isset($response[$iUserId]['current_score']) && isset($response[$iUserId]['current_time']) && isset($response[$iUserId]['prev_id']) && isset($response[$iUserId]['play_total_time']))
                    {
                        
                        $current_score = $response[$iUserId]['current_score'];
                        $current_time = $response[$iUserId]['current_time'];
                        $prev_id = $response[$iUserId]['prev_id'];
                        $play_total_time = $response[$iUserId]['play_total_time'] = $response[$iUserId]['play_total_time'] + $objParams->total_time;
                        Settings::setSpellingBeeCache($cache_name, $response);
                    }
                    else
                    {
                        $user_score_data = $highscore->getUserScore($iUserId);
                        if ($user_score_data)
                        {
                            $response[$iUserId]['current_score'] = $current_score = $user_score_data->score;
                            $response[$iUserId]['current_time'] = $current_time = $user_score_data->test_time;
                            $response[$iUserId]['prev_id'] = $prev_id = $user_score_data->id;
                            $response[$iUserId]['play_total_time'] = $play_total_time = $user_score_data->play_total_time + $objParams->total_time;

                            Settings::setSpellingBeeCache($cache_name, $response);
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
                        $response[$iUserId]['current_score'] = $current_score = $user_score_data->score;
                        $response[$iUserId]['current_time'] = $current_time = $user_score_data->test_time;
                        $response[$iUserId]['prev_id'] = $prev_id = $user_score_data->id;
                        $response[$iUserId]['play_total_time'] = $play_total_time = $user_score_data->play_total_time + $objParams->total_time;
                        Settings::setSpellingBeeCache($cache_name, $response);
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
                    $highscore->save();
                   
                    
                    $response[$iUserId]['current_score'] = $score_for_rank;
                    $response[$iUserId]['current_time'] = $time_for_rank;
                    $response[$iUserId]['prev_id'] = $highscore->id;
                    $response[$iUserId]['play_total_time'] = $play_total_time;
                    Settings::setSpellingBeeCache($cache_name, $response);
                }
                else
                {
                    $score_for_rank = $current_score;
                    $time_for_rank = $current_time;
                }
                $arUserData['highestScore'] = $score_for_rank;
                $arUserData['rank'] = $highscore->getUserRank($score_for_rank, $time_for_rank,$user_data->tds_country_id, strtolower($user_data->division));
            }
        }

        return (object) $arUserData;
    }  

}
