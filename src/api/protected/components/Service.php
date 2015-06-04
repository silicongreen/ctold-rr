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

//    public function checkUser()
//    {
//        $autorize_check = Settings::authorizeUserCheck("Mw==", "NA==", "a2xnZGp2YQ==", "cA==", "MjQ1MjU4OTU3NA==", 259);
//
//        return $autorize_check;
//    }

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
        $arUserMode['user_id_tokens'] = (object) NULL;

        $objfreeuser = new Freeusers();
        $data = $objfreeuser->getFreeuserByCookie();
      

        if ($data && is_int($data))
        {
            $cache_name = "YII-SPELLINGBEE-USERAGREMENT";
            $response = Yii::app()->cache->get($cache_name);
            if ($response !== false)
            {

                if (in_array($data, $response))
                {
                    $arUserMode['bIsNew'] = 1;
                }
            }

            $arUserMode['cPlayMode'] = 'c';
            $arUserMode['user_id_tokens'] = Settings::createUserToken($data);
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
            $response = Yii::app()->cache->get($cache_name);
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
            Yii::app()->cache->set($cache_name, $user_array, 3986400);

            $arUserMode['bTerms'] = TRUE;
        }
        return (object) $arUserMode;
    }

    public function getWords($objParams)
    {
        $iYear = 2014; // deault year for spellbee word
        $iUserID = Champs21_Utility_Session::getValue('userInfo', 'userid');
        $objConn = Champs21_Db_Connection::factory()->getSlaveConnection();
        $objSpellbeeDao = Champs21_Model_Dao_Factory::getInstance()->setModule('spellingbee')->getSpellingbeeDao();
        $objSpellbeeDao->setDbConnection($objConn);
        $iLevelId = $objParams->level;
        if (is_int($iUserID) && $iUserID == 496)
        {
            $iLevelId = 2; //0 = DS, 1 = Easy, 2 = Mid, 3 = Hard
            $iYear = 2014;
        }
        elseif (is_int($iUserID) && $iUserID == 10840)
        {
            $iYear = 2014;
        }

        $arWords = $objSpellbeeDao->getWordsByLevel($iLevelId, $objParams->size, $iYear);
        return (!is_array($arWords) ) ? 'no_data' : $arWords;
    }

    public function getWordsForMobile($level, $size, $strExcludeIds = '')
    {
        $strExcludeIds = substr($strExcludeIds, 0, -1);
        $objViewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
        if (null === $objViewRenderer->view)
        {
            $objViewRenderer->initView();
        }

        $objView = $objViewRenderer->view;
        $objConn = Champs21_Db_Connection::factory()->getMasterConnection();
        $objSpellbeeDao = Champs21_Model_Dao_Factory::getInstance()->setModule('spellingbee')->getSpellingbeeDao();
        $objSpellbeeDao->setDbConnection($objConn);
        $arWords = $objSpellbeeDao->getWordsForMobile($objView->APP_URL, $level, $size, $strExcludeIds);
        header('content-type: text/xml');
        $strSpellBeeXML = '<spellbee_xml>';
        if ($arWords === FALSE)
        {
            $strSpellBeeXML .= '<spellbee>';
            $strSpellBeeXML .= '<id></id><bangla_meaning></bangla_meaning><definition></definition><word>finish play</word><sentence></sentence><wtype></wtype><level></level><voice></voice><source></source><enabled></enabled><sound_url></sound_url>';
            $strSpellBeeXML .= '</spellbee>';
            $strSpellBeeXML .= '</spellbee_xml>';
            print $strSpellBeeXML;
            exit;
        }
        foreach ($arWords as $arWord)
        {
            $strSpellBeeXML .= '<spellbee>';
            foreach ($arWord as $key => $value)
            {
                if ($key == "sentence")
                    $strSpellBeeXML .= '<' . $key . '>' . str_replace($arWord['word'], "******", $value) . '</' . $key . '>';
                else
                    $strSpellBeeXML .= '<' . $key . '>' . $value . '</' . $key . '>';
            }
            $strSpellBeeXML .= '</spellbee>';
        }
        $strSpellBeeXML .= '</spellbee_xml>';
        print $strSpellBeeXML;
        exit;
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
                $cache_name = "YII-SPELLINGBEE-USERDATA";
                $response = Yii::app()->cache->get($cache_name);

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
                        Yii::app()->cache->set($cache_name, $response, 3986400);
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

                            Yii::app()->cache->set($cache_name, $response, 3986400);
                        }
                        else
                        {
                            $play_total_time = $user_score_data->test_time;
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
                        Yii::app()->cache->set($cache_name, $response, 3986400);
                    }
                    else
                    {
                        $play_total_time = $user_score_data->test_time;
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
                    $highscore->division = strtolower($user_data->district);
                    $highscore->country = $user_data->tds_country_id;
                    $highscore->save();
                    
                    $response[$iUserId]['current_score'] = $score_for_rank;
                    $response[$iUserId]['current_time'] = $time_for_rank;
                    $response[$iUserId]['prev_id'] = $highscore->id;
                    $response[$iUserId]['play_total_time'] = $play_total_time;
                    Yii::app()->cache->set($cache_name, $response, 3986400);
                }
                else
                {
                    $score_for_rank = $current_score;
                    $time_for_rank = $current_time;
                }
                $arUserData['highestScore'] = $score_for_rank;
                $arUserData['rank'] = $highscore->getUserRank($score_for_rank, $time_for_rank,$user_data->tds_country_id, strtolower($user_data->district));
            }
        }

        return (object) $arUserData;
    }

    public function clearHistory()
    {
        $objConfig = Champs21_Module_Config::getConfig('core');
        $strDestination = CHAMPS21_ROOT_DIR . DS . $objConfig->upload_dir->upload_dir;
        $strPath = implode(DS, array("userspelling", "front"));
        Champs21_Utility_File::createDirs($strDestination, $strPath);
        $strDestinationDir = Champs21_Utility_File::makePath($strDestination . DS . $strPath);
        $iUserID = Champs21_Utility_Session::getValue('userInfo', 'userid');
        if (empty($iUserID))
            $iUserID = session_id();
        $strFileName = $strDestinationDir . DS . $iUserID . "_spellconfig.txt";

        if (file_exists($strFileName))
        {
            unlink($strFileName);
        }

        $strFileName = $strDestinationDir . DS . $iUserID . "_spellids.txt";

        if (file_exists($strFileName))
            unlink($strFileName);

        $iCurTime = time();

        $cacheDir = CHAMPS21_VAR_DIR . DS . 'cache' . DS . "object" . DS . "usertime";
        $cachePath = $cacheDir . DS . "spelling_" . $iUserID . ".obj";
        if ($fp = fopen($cachePath, 'wb'))
        {
            fwrite($fp, $iCurTime);
            fclose($fp);
        }

//            $objConn = Champs21_Db_Connection::factory()->getMasterConnection();
//            $objSpellingbeeDao = Champs21_Model_Dao_Factory::getInstance()->setModule( 'spellingbee' )->getSpellingbeeDao();
//            $objSpellingbeeDao->setDbConnection( $objConn );
//            $bIsScoreSave = $objSpellingbeeDao->saveSbGameStartTime( $iUserID, $iCurTime );
    }

    public function getScores($user_id)
    {
        $objConn = Champs21_Db_Connection::factory()->getMasterConnection();
        $objSpellbeeDao = Champs21_Model_Dao_Factory::getInstance()->setModule('spellingbee')->getSpellingbeeDao();
        $objSpellbeeDao->setDbConnection($objConn);

        $objUserInfo = $objSpellbeeDao->getUserInfo($user_id);
        if (!$objUserInfo)
        {
            header('content-type: text/xml');
            $strSpellBeeXML = '<spellbee_xml>';
            $strSpellBeeXML .= '<scores>';
            $strSpellBeeXML .= '<error>541</error>';
            $strSpellBeeXML .= '<message>User not found</message>';
            $strSpellBeeXML .= '</scores>';
            $strSpellBeeXML .= '</spellbee_xml>';
            echo $strSpellBeeXML;
            exit;
        }
        else
        {
            if ($objUserInfo->division_id == 0)
            {
                header('content-type: text/xml');
                $strSpellBeeXML = '<spellbee_xml>';
                $strSpellBeeXML .= '<scores>';
                $strSpellBeeXML .= '<error>542</error>';
                $strSpellBeeXML .= '<message>Division or District is not set for this user</message>';
                $strSpellBeeXML .= '</scores>';
                $strSpellBeeXML .= '</spellbee_xml>';
                echo $strSpellBeeXML;
                exit;
            }
            else
            {
                $objScores = $objSpellbeeDao->getLeaderBoard($objUserInfo->division_id, $objUserInfo->district_id);
                header('content-type: text/xml');
                $strSpellBeeXML = '<spellbee_xml>';
                if ($objScores !== FALSE)
                {
                    foreach ($objScores as $objScore)
                    {
                        $strSpellBeeXML .= '<scores>';
                        $strSpellBeeXML .= '<id>' . $objScore->id . '</id>';
                        $strSpellBeeXML .= '<userid>' . $objScore->userid . '</userid>';
                        $strSpellBeeXML .= '<user_name>' . $objScore->user_name . '</user_name>';
                        //$strSpellBeeXML .= '<district_name>' . $objScore->district_name . '</district_name>';

                        if ($objUserInfo->division_id != -1)
                            $strSpellBeeXML .= '<division_name>' . $objScore->division_name . '</division_name>';
                        else
                            $strSpellBeeXML .= '<division_name>' . $objScore->country_name . '</division_name>';

                        $strSpellBeeXML .= '<high_score>' . $objScore->score . '</high_score>';
                        $strSpellBeeXML .= '<time>' . $objScore->test_time . '</time>';
                        $strSpellBeeXML .= '</scores>';
                    }
                }
                $strSpellBeeXML .= '</spellbee_xml>';
                echo $strSpellBeeXML;
                exit;
            }
        }
    }

    public function getLoginUser($user_name, $password, $code, $test)
    {
        $objConn = Champs21_Db_Connection::factory()->getMasterConnection();
        $objSpellbeeDao = Champs21_Model_Dao_Factory::getInstance()->setModule('spellingbee')->getSpellingbeeDao();
        $objSpellbeeDao->setDbConnection($objConn);

        $arUserInfo = $objSpellbeeDao->authenticateUser($user_name, md5($password), $code, $test);
        if (is_null($arUserInfo))
        {
            header('content-type: text/xml');
            $strSpellBeeXML = '<spellbee_xml>';
            $strSpellBeeXML .= '<user>';
            $strSpellBeeXML .= '<error>404</error>';
            $strSpellBeeXML .= '<message>User not found</message>';
            $strSpellBeeXML .= '</user>';
            $strSpellBeeXML .= '</spellbee_xml>';
            echo $strSpellBeeXML;
            exit;
        }
        else if ($arUserInfo == -1)
        {
            header('content-type: text/xml');
            $strSpellBeeXML = '<spellbee_xml>';
            $strSpellBeeXML .= '<user>';
            $strSpellBeeXML .= '<error>503</error>';
            $strSpellBeeXML .= '<message>Your Subscription period has been over</message>';
            $strSpellBeeXML .= '</user>';
            $strSpellBeeXML .= '</spellbee_xml>';
            echo $strSpellBeeXML;
            exit;
        }
        else if ($arUserInfo == -2)
        {
            header('content-type: text/xml');
            $strSpellBeeXML = '<spellbee_xml>';
            $strSpellBeeXML .= '<user>';
            $strSpellBeeXML .= '<error>507</error>';
            $strSpellBeeXML .= '<message>This Subscription Code is already used by another user</message>';
            $strSpellBeeXML .= '</user>';
            $strSpellBeeXML .= '</spellbee_xml>';
            echo $strSpellBeeXML;
            exit;
        }
        else if ($arUserInfo == -3)
        {
            header('content-type: text/xml');
            $strSpellBeeXML = '<spellbee_xml>';
            $strSpellBeeXML .= '<user>';
            $strSpellBeeXML .= '<error>503</error>';
            $strSpellBeeXML .= '<message>Yor are not eligible to play spell bee</message>';
            $strSpellBeeXML .= '</user>';
            $strSpellBeeXML .= '</spellbee_xml>';
            echo $strSpellBeeXML;
            exit;
        }
        else
        {
            if ($arUserInfo['is_code_for_user'] == 0)
            {
                header('content-type: text/xml');
                $strSpellBeeXML = '<spellbee_xml>';
                $strSpellBeeXML .= '<user>';
                $strSpellBeeXML .= '<error>505</error>';
                $strSpellBeeXML .= '<message>The code is not match to your code.\n\n To resolve this error,\n 1. Go to your Application settings, uninstall the Spellbee application.\n2. Redownload it from http://www.champs21.com/sb/mobile\n3. Install the application\n4. Reinsert your mobile no and code again and play</message>';
                $strSpellBeeXML .= '</user>';
                $strSpellBeeXML .= '</spellbee_xml>';
                echo $strSpellBeeXML;
                exit;
            }
            if ($arUserInfo['update_require'] == 1)
                $objSpellbeeDao->updateCodeData($code, $arUserInfo);
            header('content-type: text/xml');
            $strSpellBeeXML = '<spellbee_xml>';
            $strSpellBeeXML .= '<user>';
            $strSpellBeeXML .= '<error>0</error>';
            $strSpellBeeXML .= '<userid>' . $arUserInfo['userid'] . '</userid>';
            $strSpellBeeXML .= '<user_name>' . $arUserInfo['user_name'] . '</user_name>';
            $strSpellBeeXML .= '<smc_id>' . $arUserInfo['smc_id'] . '</smc_id>';
            if ($arUserInfo['division_id'] == 0)
            {
                $strSpellBeeXML .= '<is_spellbee>0</is_spellbee>';
            }
            else
            {
                $strSpellBeeXML .= '<is_spellbee>1</is_spellbee>';
            }
            $strSpellBeeXML .= '</user>';
            $strSpellBeeXML .= '</spellbee_xml>';
            echo $strSpellBeeXML;
            exit;
        }
    }

    public function setAccessforMobile($code, $mobileno)
    {
        $objConn = Champs21_Db_Connection::factory()->getMasterConnection();
        $objSpellbeeDao = Champs21_Model_Dao_Factory::getInstance()->setModule('spellingbee')->getSpellingbeeDao();
        $objSpellbeeDao->setDbConnection($objConn);

        $bCodeInfo = $objSpellbeeDao->verifyCode($code, $mobileno);
        if ($bCodeInfo == "faliure")
        {
            header('content-type: text/xml');
            $strSpellBeeXML = '<spellbee_xml>';
            $strSpellBeeXML .= '<code_verify>';
            $strSpellBeeXML .= '<error>404</error>';
            $strSpellBeeXML .= '<message>Code and/or Mobile no not found</message>';
            $strSpellBeeXML .= '</code_verify>';
            $strSpellBeeXML .= '</spellbee_xml>';
            echo $strSpellBeeXML;
            exit;
        }
        elseif ($bCodeInfo == "expire")
        {
            header('content-type: text/xml');
            $strSpellBeeXML = '<spellbee_xml>';
            $strSpellBeeXML .= '<code_verify>';
            $strSpellBeeXML .= '<error>503</error>';
            $strSpellBeeXML .= '<message>The Code you entered is already expired</message>';
            $strSpellBeeXML .= '</code_verify>';
            $strSpellBeeXML .= '</spellbee_xml>';
            echo $strSpellBeeXML;
            exit;
        }
        else
        {
            header('content-type: text/xml');
            $strSpellBeeXML = '<spellbee_xml>';
            $strSpellBeeXML .= '<code_verify>';
            $strSpellBeeXML .= '<error>0</error>';
            $strSpellBeeXML .= '<code>' . $code . '</code>';
            $strSpellBeeXML .= '<message></message>';
            $strSpellBeeXML .= '</code_verify>';
            $strSpellBeeXML .= '</spellbee_xml>';
            echo $strSpellBeeXML;
            exit;
        }
    }

    public function isUserCodeEnabled($user_id, $code = '')
    {
        $objConn = Champs21_Db_Connection::factory()->getMasterConnection();
        $objSpellbeeDao = Champs21_Model_Dao_Factory::getInstance()->setModule('spellingbee')->getSpellingbeeDao();
        $objSpellbeeDao->setDbConnection($objConn);

        $arCodeInfo = $objSpellbeeDao->isUserCodeEnabled($user_id, $code);
        if (!$arCodeInfo)
        {
            header('content-type: text/xml');
            $strSpellBeeXML = '<spellbee_xml>';
            $strSpellBeeXML .= '<code_verify>';
            $strSpellBeeXML .= '<found>0</found>';
            $strSpellBeeXML .= '</code_verify>';
            $strSpellBeeXML .= '</spellbee_xml>';
            echo $strSpellBeeXML;
            exit;
        }
        else
        {
            header('content-type: text/xml');
            $strSpellBeeXML = '<spellbee_xml>';
            $strSpellBeeXML .= '<code_verify>';
            $strSpellBeeXML .= '<found>1</found>';
            if ($arCodeInfo['is_code_used'] == 1)
            {
                $strSpellBeeXML .= '<code_used>1</code_used>';
                if ($arCodeInfo['is_valid'] > time())
                {
                    $strSpellBeeXML .= '<valid>1</valid>';
                }
                else
                {
                    $strSpellBeeXML .= '<valid>0</valid>';
                }
            }
            else
            {
                $strSpellBeeXML .= '<code_used>0</code_used>';
                $strSpellBeeXML .= '<valid>1</valid>';
            }
            $strSpellBeeXML .= '</code_verify>';
            $strSpellBeeXML .= '</spellbee_xml>';
            echo $strSpellBeeXML;
            exit;
        }
    }

    public function updateUserInfo($user_id, $division_id, $code, $test, $district_id = 0)
    {
        $objConn = Champs21_Db_Connection::factory()->getMasterConnection();
        $objSpellbeeDao = Champs21_Model_Dao_Factory::getInstance()->setModule('spellingbee')->getSpellingbeeDao();
        $objSpellbeeDao->setDbConnection($objConn);

        $objUserInfo = $objSpellbeeDao->updateUserLocation($user_id, $division_id, $district_id);
        if (!$objUserInfo)
        {
            header('content-type: text/xml');
            $strSpellBeeXML = '<spellbee_xml>';
            $strSpellBeeXML .= '<update_user>';
            $strSpellBeeXML .= '<error>505</error>';
            $strSpellBeeXML .= '<message>An Error Occur while updating user info</message>';
            $strSpellBeeXML .= '</update_user>';
            $strSpellBeeXML .= '</spellbee_xml>';
            echo $strSpellBeeXML;
            exit;
        }
        else
        {
            $objSpellbeeDao->updateSpellAppDevision($code, $division_id);
            header('content-type: text/xml');
            $strSpellBeeXML = '<spellbee_xml>';
            $strSpellBeeXML .= '<update_user>';
            $strSpellBeeXML .= '<error>0</error>';
            $strSpellBeeXML .= '<message>Successful</message>';
            $strSpellBeeXML .= '</update_user>';
            $strSpellBeeXML .= '</spellbee_xml>';
            echo $strSpellBeeXML;
            exit;
        }
    }

    public function getDivisions()
    {
        $objConn = Champs21_Db_Connection::factory()->getMasterConnection();
        $objSpellbeeDao = Champs21_Model_Dao_Factory::getInstance()->setModule('spellingbee')->getSpellingbeeDao();
        $objSpellbeeDao->setDbConnection($objConn);

        $objDivisions = $objSpellbeeDao->getDivisions();
        if (!$objDivisions)
        {
            header('content-type: text/xml');
            $strSpellBeeXML = '<spellbee_xml>';
            $strSpellBeeXML .= '<divisions>';
            $strSpellBeeXML .= '<error>404</error>';
            $strSpellBeeXML .= '<id>-1</id>';
            $strSpellBeeXML .= '<name>not found</name>';
            $strSpellBeeXML .= '</divisions>';
            $strSpellBeeXML .= '</spellbee_xml>';
            echo $strSpellBeeXML;
            exit;
        }
        else
        {
            header('content-type: text/xml');
            $strSpellBeeXML = '<spellbee_xml>';
            foreach ($objDivisions as $objDivision)
            {
                $strSpellBeeXML .= '<divisions>';
                $strSpellBeeXML .= '<error>0</error>';
                $strSpellBeeXML .= '<id>' . $objDivision->id . '</id>';
                $strSpellBeeXML .= '<name>' . $objDivision->name . '</name>';
                $strSpellBeeXML .= '</divisions>';
            }
            $strSpellBeeXML .= '</spellbee_xml>';
            echo $strSpellBeeXML;
            exit;
        }
    }

    public function getDistricts($iDivisionId)
    {
        $objConn = Champs21_Db_Connection::factory()->getMasterConnection();
        $objSpellbeeDao = Champs21_Model_Dao_Factory::getInstance()->setModule('spellingbee')->getSpellingbeeDao();
        $objSpellbeeDao->setDbConnection($objConn);

        $objDistricts = $objSpellbeeDao->getDistricts($iDivisionId);
        if (!$objDistricts)
        {
            header('content-type: text/xml');
            $strSpellBeeXML = '<spellbee_xml>';
            $strSpellBeeXML .= '<districts>';
            $strSpellBeeXML .= '<error>404</error>';
            $strSpellBeeXML .= '<id>-1</id>';
            $strSpellBeeXML .= '<name>not found</name>';
            $strSpellBeeXML .= '</districts>';
            $strSpellBeeXML .= '</spellbee_xml>';
            echo $strSpellBeeXML;
            exit;
        }
        else
        {
            header('content-type: text/xml');
            $strSpellBeeXML = '<spellbee_xml>';
            foreach ($objDistricts as $objDistrict)
            {
                $strSpellBeeXML .= '<districts>';
                $strSpellBeeXML .= '<error>0</error>';
                $strSpellBeeXML .= '<id>' . $objDistrict->id . '</id>';
                $strSpellBeeXML .= '<name>' . $objDistrict->name . '</name>';
                $strSpellBeeXML .= '</districts>';
            }
            $strSpellBeeXML .= '</spellbee_xml>';
            echo $strSpellBeeXML;
            exit;
        }
    }

    public function getMediums()
    {
        $objConn = Champs21_Db_Connection::factory()->getMasterConnection();
        $objMediumDao = Champs21_Model_Dao_Factory::getInstance()->setModule('adminmedium')->getMediumDao();
        $objMediumDao->setDbConnection($objConn);

        $objMediums = $objMediumDao->getAllMedium(20, 0, 'id', 'asc');
        $arMediums = $objMediums->ConvertArray();
        if (count($arMediums) == 0)
        {
            header('content-type: text/xml');
            $strSpellBeeXML = '<spellbee_xml>';
            $strSpellBeeXML .= '<mediums>';
            $strSpellBeeXML .= '<error>404</error>';
            $strSpellBeeXML .= '<id>-1</id>';
            $strSpellBeeXML .= '<name>not found</name>';
            $strSpellBeeXML .= '</mediums>';
            $strSpellBeeXML .= '</spellbee_xml>';
            echo $strSpellBeeXML;
            exit;
        }
        else
        {
            header('content-type: text/xml');
            $strSpellBeeXML = '<spellbee_xml>';
            foreach ($arMediums as $arMedium)
            {
                $strSpellBeeXML .= '<mediums>';
                $strSpellBeeXML .= '<error>0</error>';
                $strSpellBeeXML .= '<id>' . $arMedium['id'] . '</id>';
                $strSpellBeeXML .= '<name>' . $arMedium['medium_name'] . '</name>';
                $strSpellBeeXML .= '</mediums>';
            }
            $strSpellBeeXML .= '</spellbee_xml>';
            echo $strSpellBeeXML;
            exit;
        }
    }

    public function getClasses()
    {
        $objConn = Champs21_Db_Connection::factory()->getMasterConnection();
        $objClassDao = Champs21_Model_Dao_Factory::getInstance()->setModule('adminclass')->getClassDao();
        $objClassDao->setDbConnection($objConn);

        $objClasses = $objClassDao->getAllClass(20, 0, 'id', 'asc');
        $arClasses = $objClasses->ConvertArray();
        if (count($arClasses) == 0)
        {
            header('content-type: text/xml');
            $strSpellBeeXML = '<spellbee_xml>';
            $strSpellBeeXML .= '<classes>';
            $strSpellBeeXML .= '<error>404</error>';
            $strSpellBeeXML .= '<id>-1</id>';
            $strSpellBeeXML .= '<name>not found</name>';
            $strSpellBeeXML .= '</classes>';
            $strSpellBeeXML .= '</spellbee_xml>';
            echo $strSpellBeeXML;
            exit;
        }
        else
        {
            header('content-type: text/xml');
            $strSpellBeeXML = '<spellbee_xml>';
            foreach ($arClasses as $arClass)
            {
                if ($arClass['id'] >= 4)
                {
                    $strSpellBeeXML .= '<classes>';
                    $strSpellBeeXML .= '<error>0</error>';
                    $strSpellBeeXML .= '<id>' . $arClass['id'] . '</id>';
                    $strSpellBeeXML .= '<name>' . str_replace("&", "|", $arClass['class_name']) . '</name>';
                    $strSpellBeeXML .= '</classes>';
                }
            }
            $strSpellBeeXML .= '</spellbee_xml>';
            echo $strSpellBeeXML;
            exit;
        }
    }

    public function registerUser($user_name, $password, $class_id, $medium_id, $division_id, $code, $test, $district_id = 0)
    {
        $objConn = Champs21_Db_Connection::factory()->getMasterConnection();
        $objSpellbeeDao = Champs21_Model_Dao_Factory::getInstance()->setModule('spellingbee')->getSpellingbeeDao();
        $objSpellbeeDao->setDbConnection($objConn);

        $bUserExists = $objSpellbeeDao->isUserExists($user_name);
        if ($bUserExists)
        {
            header('content-type: text/xml');
            $strSpellBeeXML = '<spellbee_xml>';
            $strSpellBeeXML .= '<user>';
            $strSpellBeeXML .= '<error>801</error>';
            $strSpellBeeXML .= '<message>User already Exists</message>';
            $strSpellBeeXML .= '</user>';
            $strSpellBeeXML .= '</spellbee_xml>';
            echo $strSpellBeeXML;
            exit;
        }

        $objMediumDao = Champs21_Model_Dao_Factory::getInstance()->setModule('adminmedium')->getMediumDao();
        $objMediumDao->setDbConnection($objConn);
        $iSMCID = $objMediumDao->getSMCID($medium_id, $class_id);

        $arUser = array(
            "user_name" => $user_name,
            "password" => $password,
            "smc_id" => $iSMCID,
            "district_id" => $district_id,
            "division_id" => $division_id
        );

        $objUser = (object) $arUser;
        $iUserId = $objSpellbeeDao->insertUserFromMobile($objUser);
        if (!$iUserId)
        {
            header('content-type: text/xml');
            $strSpellBeeXML = '<spellbee_xml>';
            $strSpellBeeXML .= '<user>';
            $strSpellBeeXML .= '<error>404</error>';
            $strSpellBeeXML .= '<message>An error occur while inserting User in the database</message>';
            $strSpellBeeXML .= '</user>';
            $strSpellBeeXML .= '</spellbee_xml>';
            echo $strSpellBeeXML;
            exit;
        }
        else
        {
            $iValidCode = $objSpellbeeDao->isValidSBCode($code, $test);
            if ($iValidCode === FALSE)
            {
                header('content-type: text/xml');
                $strSpellBeeXML = '<spellbee_xml>';
                $strSpellBeeXML .= '<user>';
                $strSpellBeeXML .= '<error>503</error>';
                $strSpellBeeXML .= '<message>Your Subscription is over</message>';
                $strSpellBeeXML .= '</user>';
                $strSpellBeeXML .= '</spellbee_xml>';
                echo $strSpellBeeXML;
                exit;
            }
            if ($iValidCode > 0 && $iUserId != $iValidCode)
            {
                header('content-type: text/xml');
                $strSpellBeeXML = '<spellbee_xml>';
                $strSpellBeeXML .= '<user>';
                $strSpellBeeXML .= '<error>505</error>';
                $strSpellBeeXML .= '<message>This Code you tried is already used by another user</message>';
                $strSpellBeeXML .= '</user>';
                $strSpellBeeXML .= '</spellbee_xml>';
                echo $strSpellBeeXML;
                exit;
            }
            else if ($iValidCode == 0)
            {
                $arUserInfo = array();
                $arUserInfo['userid'] = $iUserId;
                $arUserInfo['smc_id'] = $iSMCID;
                $arUserInfo['division_id'] = $division_id;
                $arUserInfo['user_name'] = $user_name;
                $arUserInfo['user_fullname'] = "";
                $objSpellbeeDao->updateCodeData($code, $arUserInfo);
            }
            else
            {
                if ($iValidCode != $iUserId)
                {
                    header('content-type: text/xml');
                    $strSpellBeeXML = '<spellbee_xml>';
                    $strSpellBeeXML .= '<user>';
                    $strSpellBeeXML .= '<error>505</error>';
                    $strSpellBeeXML .= '<message>The code is not match to your code. To resolve this error, 1. Go to your Application settings, uninstall the Spellbee application. 2. Redownload it from http://www.champs21.com/sb/mobile 3. Install the application4. Reinsert your mobile no and code again and play</message>';
                    $strSpellBeeXML .= '</user>';
                    $strSpellBeeXML .= '</spellbee_xml>';
                    echo $strSpellBeeXML;
                    exit;
                }
            }
            header('content-type: text/xml');
            $strSpellBeeXML = '<spellbee_xml>';
            $strSpellBeeXML .= '<user>';
            $strSpellBeeXML .= '<error>0</error>';
            $strSpellBeeXML .= '<userid>' . $iUserId . '</userid>';
            $strSpellBeeXML .= '<is_spellbee>1</is_spellbee>';
            $strSpellBeeXML .= '</user>';
            $strSpellBeeXML .= '</spellbee_xml>';
            echo $strSpellBeeXML;
            exit;
        }
    }

    public function saveSpellingBeeForMobile($user_id, $total_time = 0, $score)
    {
        $iUserId = $user_id;
        $objConn = Champs21_Db_Connection::factory()->getMasterConnection();
        $objSpellbeeDao = Champs21_Model_Dao_Factory::getInstance()->setModule('spellingbee')->getSpellingbeeDao();
        $objSpellbeeDao->setDbConnection($objConn);

        $arSaveScoreData = array();
        $arSaveScoreData['userid'] = $iUserId;
        $arSaveScoreData['test_time'] = $total_time;
        $arSaveScoreData['enddate'] = time();
        $arSaveScoreData['score'] = (int) $score;

        $objSaveScoreData = (object) $arSaveScoreData;
        $objSpellbeeDao->saveResult($objSaveScoreData);

        $iUserHighestScore = $objSpellbeeDao->getUserHighestScore($iUserId);
        if (!$iUserHighestScore)
        {
            $objSpellbeeDao->insertUserHighestScore($objSaveScoreData);
        }
        else if ($arSaveScoreData['score'] > $iUserHighestScore)
        {
            $objSpellbeeDao->updateUserHighestScore($objSaveScoreData);
        }
        $iRank = $objSpellbeeDao->getUserRank($objSaveScoreData->score);

        header('content-type: text/xml');
        $strSpellBeeXML = '<spellbee_xml>';
        $strSpellBeeXML .= '<save_score>';
        $strSpellBeeXML .= '<error>0</error>';
        $strSpellBeeXML .= '<message>success</message>';
        $strSpellBeeXML .= '</save_score>';
        $strSpellBeeXML .= '</spellbee_xml>';
        echo $strSpellBeeXML;
        exit;
    }

    public function subscribeSB($MOBILENO, $KEYWORD)
    {
        $objConfig = Champs21_Module_Config::getConfig('spellingbee');
        $strAPPKey = $objConfig->APP_KEY->key;
        $arKeys = explode(" ", $KEYWORD);
        if (count($arKeys) != 2)
        {
            self::_handleUndefinedKey($MOBILENO, $KEYWORD);
        }
        else
        {
            if (strtoupper(trim($arKeys[1])) != strtoupper($strAPPKey))
            {
                self::_handleUndefinedKey($MOBILENO, $KEYWORD);
            }
            else
            {
                $arStoreKeys[0] = $objConfig->KEYWORD->keyword1;
                $arStoreKeys[1] = $objConfig->KEYWORD->keyword2;
                $arStoreKeys[2] = $objConfig->KEYWORD->keyword3;
                $arStoreKeys[3] = $objConfig->KEYWORD->keyword4;
                if (!in_array(strtoupper($arKeys[0]), $arStoreKeys))
                {
                    self::_handleUndefinedKey($MOBILENO, $KEYWORD);
                }
                else
                {
                    if (strcasecmp($arKeys[0], $arStoreKeys[0]) === 0)
                        self::_registerSBUser($objConfig->VALIDITY->period, $MOBILENO, $KEYWORD);
                    else if (strcasecmp($arKeys[0], $arStoreKeys[1]) === 0)
                        self::_upgradeSBUser($objConfig->VALIDITY->period, $MOBILENO, $KEYWORD);
                    else if (strcasecmp($arKeys[0], $arStoreKeys[2]) === 0)
                        self::_stopSBUser($MOBILENO, $KEYWORD);
                    else
                        self::_handleUndefinedKey($MOBILENO, $KEYWORD);
                }
            }
        }
    }

    function refilSB($MOBILENO, $KEYWORD)
    {
        $objConfig = Champs21_Module_Config::getConfig('spellingbee');
        $strPeriod = $objConfig->VALIDITY->period;
        $MOBILENO = '88' . substr($MOBILENO, strlen($MOBILENO) - 11, 11);
        $objConn = Champs21_Db_Connection::factory()->getMasterConnection();
        $objSpellbeeDao = Champs21_Model_Dao_Factory::getInstance()->setModule('spellingbee')->getSpellingbeeDao();
        $objSpellbeeDao->setDbConnection($objConn);
        $objUserSBInfo = $objSpellbeeDao->getUserSpellInfo($MOBILENO);

        if ($objUserSBInfo !== FALSE)
        {
            if ($strPeriod == "week")
            {
                $iValidity = strtotime("+2 days", $objUserSBInfo->validity);
            }
            else
                $iValidity = strtotime("+1 month", $objUserSBInfo->validity);

            $iAffected = $objSpellbeeDao->upgradeSBUser($iValidity, $MOBILENO);

            //header( 'content-type: text/xml' );
            $strSpellBeeXML = "<CHAMPS21><MT><MOBILENO>" . trim($MOBILENO) . "</MOBILENO><KEYWORD>" . trim($KEYWORD) . "</KEYWORD>";
            if ($iAffected > 0)
            {
                $arSubscriptionInfo = $objSpellbeeDao->getUserSpellInfo($MOBILENO);
                $strSpellBeeXML .= "<STATUS>SUCCESS</STATUS>";
                //$strSpellBeeXML .= "<URL>http://www.champs21.com/spellingbee/mobile</URL>";
                if ($arSubscriptionInfo !== FALSE)
                {
                    $strActivated = ( $arSubscriptionInfo->is_code_used == 1 ) ? "Activated" : $arSubscriptionInfo->code;
                    $strSpellBeeXML .= "<CODE>" . trim($strActivated) . "</CODE>";
                    $strSpellBeeXML .= "<STIME>" . date("H:i", $arSubscriptionInfo->start_date) . "</STIME>";
                    $strSpellBeeXML .= "<SDATE>" . date("d/m/Y", $arSubscriptionInfo->start_date) . "</SDATE>";
                    $strSpellBeeXML .= "<ETIME>" . date("H:i", $arSubscriptionInfo->validity) . "</ETIME>";
                    $strSpellBeeXML .= "<EDATE>" . date("d/m/Y", $arSubscriptionInfo->validity) . "</EDATE>";
                    $strSpellBeeXML .= "<MESSAGE>NA</MESSAGE>";
                }
                else
                {
                    $strSpellBeeXML .= "<CODE>NA</CODE>";
                    $strSpellBeeXML .= "<STIME>NA</STIME>";
                    $strSpellBeeXML .= "<SDATE>NA</SDATE>";
                    $strSpellBeeXML .= "<ETIME>NA</ETIME>";
                    $strSpellBeeXML .= "<EDATE>NA</EDATE>";
                    $strSpellBeeXML .= "<MESSAGE>ERROR</MESSAGE>";
                }
            }
            else
            {
                $strSpellBeeXML .= "<STATUS>ERROR</STATUS>";
                $strSpellBeeXML .= "<URL>http://www.champs21.com/spellingbee/mobile</URL>";
                $arSubscriptionInfo = $objSpellbeeDao->getUserSpellInfo($MOBILENO);
                if ($arSubscriptionInfo !== FALSE)
                {
                    if ($arSubscriptionInfo->enabled == 0)
                    {
                        $strSpellBeeXML .= "<CODE>NA</CODE>";
                        $strSpellBeeXML .= "<STIME>NA</STIME>";
                        $strSpellBeeXML .= "<SDATE>NA</SDATE>";
                        $strSpellBeeXML .= "<ETIME>NA</ETIME>";
                        $strSpellBeeXML .= "<EDATE>NA</EDATE>";
                        $strSpellBeeXML .= "<MESSAGE>UNSUBSCRIBE_USER</MESSAGE>";
                    }
                    else
                    {
                        $strSpellBeeXML .= "<CODE>NA</CODE>";
                        $strSpellBeeXML .= "<STIME>NA</STIME>";
                        $strSpellBeeXML .= "<SDATE>NA</SDATE>";
                        $strSpellBeeXML .= "<ETIME>NA</ETIME>";
                        $strSpellBeeXML .= "<EDATE>NA</EDATE>";
                        $strSpellBeeXML .= "<MESSAGE>ERROR</MESSAGE>";
                    }
                }
                else
                {
                    $strSpellBeeXML .= "<CODE>NA</CODE>";
                    $strSpellBeeXML .= "<STIME>NA</STIME>";
                    $strSpellBeeXML .= "<SDATE>NA</SDATE>";
                    $strSpellBeeXML .= "<ETIME>NA</ETIME>";
                    $strSpellBeeXML .= "<EDATE>NA</EDATE>";
                    $strSpellBeeXML .= "<MESSAGE>ERROR</MESSAGE>";
                }
            }
            $strSpellBeeXML .= "</MT></CHAMPS21>";
            $objXML = simplexml_load_string($strSpellBeeXML);
            echo $objXML->asXML();
            exit;
        }
        else
        {
            header('content-type: text/xml');
            $strSpellBeeXML = "<CHAMPS21><MT><MOBILENO>" . trim($MOBILENO) . "</MOBILENO><KEYWORD>" . trim($KEYWORD) . "</KEYWORD>";
            $strSpellBeeXML .= "<STATUS>ERROR</STATUS>";
            $strSpellBeeXML .= "<URL>http://www.champs21.com/spellingbee/mobile</URL>";
            $strSpellBeeXML .= "<CODE>NA</CODE>";
            $strSpellBeeXML .= "<STIME>NA</STIME>";
            $strSpellBeeXML .= "<SDATE>NA</SDATE>";
            $strSpellBeeXML .= "<ETIME>NA</ETIME>";
            $strSpellBeeXML .= "<EDATE>NA</EDATE>";
            $strSpellBeeXML .= "<MESSAGE>USER_NOT_EXISTS</MESSAGE>";
            $strSpellBeeXML .= "</MT></CHAMPS21>";
            echo $strSpellBeeXML;
            exit;
        }
    }

    function refilAgainSB($MOBILENO, $KEYWORD)
    {
        $objConfig = Champs21_Module_Config::getConfig('spellingbee');
        $strPeriod = $objConfig->VALIDITY->period;
        $MOBILENO = '88' . substr($MOBILENO, strlen($MOBILENO) - 11, 11);
        $objConn = Champs21_Db_Connection::factory()->getMasterConnection();
        $objSpellbeeDao = Champs21_Model_Dao_Factory::getInstance()->setModule('spellingbee')->getSpellingbeeDao();
        $objSpellbeeDao->setDbConnection($objConn);
        $objUserSBInfo = $objSpellbeeDao->getUserSpellInfo($MOBILENO);

        if ($objUserSBInfo !== FALSE)
        {
            header('content-type: text/xml');
            $strSpellBeeXML = "<CHAMPS21><MT><MOBILENO>" . trim($MOBILENO) . "</MOBILENO><KEYWORD>" . trim($KEYWORD) . "</KEYWORD>";
            $strSpellBeeXML .= "<STATUS>SUCCESS</STATUS>";
            $strSpellBeeXML .= "<URL>http://www.champs21.com/spellingbee/mobile</URL>";
            $strSpellBeeXML .= "<CODE>" . $objUserSBInfo->code . "</CODE>";
            $strSpellBeeXML .= "<STIME>" . date("H:i", $objUserSBInfo->start_date) . "</STIME>";
            $strSpellBeeXML .= "<SDATE>" . date("d/m/Y", $objUserSBInfo->start_date) . "</SDATE>";
            $strSpellBeeXML .= "<ETIME>" . date("H:i", $objUserSBInfo->validity) . "</ETIME>";
            $strSpellBeeXML .= "<EDATE>" . date("d/m/Y", $objUserSBInfo->validity) . "</EDATE>";
            $strSpellBeeXML .= "<MESSAGE>NA</MESSAGE>";
            $strSpellBeeXML .= "</MT></CHAMPS21>";
            echo $strSpellBeeXML;
            exit;
        }
        else
        {
            header('content-type: text/xml');
            $strSpellBeeXML = "<CHAMPS21><MT><MOBILENO>" . trim($MOBILENO) . "</MOBILENO><KEYWORD>" . trim($KEYWORD) . "</KEYWORD>";
            $strSpellBeeXML .= "<STATUS>ERROR</STATUS>";
            $strSpellBeeXML .= "<URL>http://www.champs21.com/spellingbee/mobile</URL>";
            $strSpellBeeXML .= "<CODE>NA</CODE>";
            $strSpellBeeXML .= "<STIME>NA</STIME>";
            $strSpellBeeXML .= "<SDATE>NA</SDATE>";
            $strSpellBeeXML .= "<ETIME>NA</ETIME>";
            $strSpellBeeXML .= "<EDATE>NA</EDATE>";
            $strSpellBeeXML .= "<MESSAGE>USER_NOT_EXISTS</MESSAGE>";
            $strSpellBeeXML .= "</MT></CHAMPS21>";
            echo $strSpellBeeXML;
            exit;
        }
    }

    function notrefilSB($MOBILENO, $KEYWORD)
    {
        $objConfig = Champs21_Module_Config::getConfig('spellingbee');
        $strPeriod = $objConfig->VALIDITY->period;
        $MOBILENO = '88' . substr($MOBILENO, strlen($MOBILENO) - 11, 11);
        $objConn = Champs21_Db_Connection::factory()->getMasterConnection();
        $objSpellbeeDao = Champs21_Model_Dao_Factory::getInstance()->setModule('spellingbee')->getSpellingbeeDao();
        $objSpellbeeDao->setDbConnection($objConn);
        $objUserSBInfo = $objSpellbeeDao->getUserSpellInfo($MOBILENO);

        header('content-type: text/xml');
        $strSpellBeeXML = "<CHAMPS21><MT><MOBILENO>" . trim($MOBILENO) . "</MOBILENO><KEYWORD>" . trim($KEYWORD) . "</KEYWORD>";
        $strSpellBeeXML .= "<STATUS>ERROR</STATUS>";
        $strSpellBeeXML .= "<URL>http://www.champs21.com/spellingbee/mobile</URL>";
        $strSpellBeeXML .= "<CODE>NA</CODE>";
        $strSpellBeeXML .= "<STIME>NA</STIME>";
        $strSpellBeeXML .= "<SDATE>NA</SDATE>";
        $strSpellBeeXML .= "<ETIME>NA</ETIME>";
        $strSpellBeeXML .= "<EDATE>NA</EDATE>";
        $strSpellBeeXML .= "<MESSAGE>NA</MESSAGE>";
        $strSpellBeeXML .= "</MT></CHAMPS21>";
        echo $strSpellBeeXML;
        exit;
    }

    function unsubscribeSB($MOBILENO, $KEYWORD)
    {
        $objConn = Champs21_Db_Connection::factory()->getMasterConnection();
        $objSpellbeeDao = Champs21_Model_Dao_Factory::getInstance()->setModule('spellingbee')->getSpellingbeeDao();
        $objSpellbeeDao->setDbConnection($objConn);

        $MOBILENO = '88' . substr($MOBILENO, strlen($MOBILENO) - 11, 11);
        $iAffected = $objUserSBInfo = $objSpellbeeDao->removeSubscription($MOBILENO);
        header('content-type: text/xml');
        //$strSpellBeeXML = "<CHAMPS21><MT><MOBILENO>" . trim($MOBILENO) . "</MOBILENO><KEYWORD>" . trim($KEYWORD) . "</KEYWORD>";
        $strSpellBeeXML = "<CHAMPS21><MT>";
        if ($iAffected > 0)
        {
            $strSpellBeeXML .= "<STATUS>SUCCESS</STATUS>";
            $strSpellBeeXML .= "<MESSAGE>NA</MESSAGE>";
        }
        else
        {
            $strSpellBeeXML .= "<STATUS>ERROR</STATUS>";
            $objUserSBInfo = $objSpellbeeDao->getUserSpellInfo($MOBILENO);
            if ($objUserSBInfo == FALSE)
            {
                $strSpellBeeXML .= "<MESSAGE>NO_SUBSCRIBER</MESSAGE>";
            }
            else
            {
                $strSpellBeeXML .= "<MESSAGE>NA</MESSAGE>";
            }
        }
        $strSpellBeeXML .= "</MT></CHAMPS21>";
        echo $strSpellBeeXML;
        exit;
    }

    public function resultSB($MOBILENO, $KEYWORD, $USERNAME)
    {
        $objConn = Champs21_Db_Connection::factory()->getMasterConnection();
        $objSpellbeeDao = Champs21_Model_Dao_Factory::getInstance()->setModule('spellingbee')->getSpellingbeeDao();
        $objSpellbeeDao->setDbConnection($objConn);

        $objUserInfo = $objSpellbeeDao->getUserInfoByUserName($USERNAME);
        header('content-type: text/xml');
        if (isset($objUserInfo->userid) && $objUserInfo->userid > 0)
        {
            $iUserId = $objUserInfo->userid;
            $iDivisionId = $objUserInfo->division_id;
            $strDivisionName = $objUserInfo->division_name;
            if ($objUserInfo->smc_id < 4)
            {
                $strSpellBeeXML = "<CHAMPS21><RESULT>";
                $strSpellBeeXML .= "<STATUS>SUCCESS</STATUS>";

                $strSpellBeeXML .= "<CURRENTSCORE></CURRENTSCORE>";
                $strSpellBeeXML .= "<HIGHESTSCORE></HIGHESTSCORE>";
                $strSpellBeeXML .= "<RANK></RANK>";
                $strSpellBeeXML .= "<DIVISION>" . $strDivisionName . "</DIVISION>";

                $strSpellBeeXML .= "<MESSAGE>NOT_ELIGIBLE</MESSAGE>";
                $strSpellBeeXML .= "</RESULT></CHAMPS21>";
                echo $strSpellBeeXML;
                exit;
            }

            $objUserHighestScore = $objSpellbeeDao->getUserHighestScore($iUserId);
            if (isset($objUserHighestScore->highest_score))
                $iUserHighestScore = $objUserHighestScore->highest_score;
            else
                $iUserHighestScore = 0;
            if ($iUserHighestScore > 0)
            {
                $iUserCurrentScore = $objSpellbeeDao->getUserCurrentScore($iUserId);
                $iRank = $objSpellbeeDao->getUserRank($iUserHighestScore, $iDivisionId, $iUserId);

                $strSpellBeeXML = "<CHAMPS21><RESULT>";
                $strSpellBeeXML .= "<STATUS>SUCCESS</STATUS>";

                $strSpellBeeXML .= "<CURRENTSCORE>" . $iUserCurrentScore . "</CURRENTSCORE>";
                $strSpellBeeXML .= "<HIGHESTSCORE>" . $iUserHighestScore . "</HIGHESTSCORE>";
                $strSpellBeeXML .= "<RANK>" . $iRank . "</RANK>";
                $strSpellBeeXML .= "<DIVISION>" . $strDivisionName . "</DIVISION>";

                $strSpellBeeXML .= "<MESSAGE>NA</MESSAGE>";
                $strSpellBeeXML .= "</RESULT></CHAMPS21>";
                echo $strSpellBeeXML;
                exit;
            }
            else
            {
                $strSpellBeeXML = "<CHAMPS21><RESULT>";
                $strSpellBeeXML .= "<STATUS>SUCCESS</STATUS>";

                $strSpellBeeXML .= "<CURRENTSCORE></CURRENTSCORE>";
                $strSpellBeeXML .= "<HIGHESTSCORE></HIGHESTSCORE>";
                $strSpellBeeXML .= "<RANK></RANK>";
                $strSpellBeeXML .= "<DIVISION>" . $strDivisionName . "</DIVISION>";

                $strSpellBeeXML .= "<MESSAGE>NOT_PARTICIPATE</MESSAGE>";
                $strSpellBeeXML .= "</RESULT></CHAMPS21>";
                echo $strSpellBeeXML;
                exit;
            }
        }
        else
        {
            $strSpellBeeXML = "<CHAMPS21><RESULT>";
            $strSpellBeeXML .= "<STATUS>ERROR</STATUS>";
            $strSpellBeeXML .= "<CURRENTSCORE></CURRENTSCORE>";
            $strSpellBeeXML .= "<HIGHESTSCORE></HIGHESTSCORE>";
            $strSpellBeeXML .= "<RANK></RANK>";
            $strSpellBeeXML .= "<DIVISION></DIVISION>";
            $strSpellBeeXML .= "<MESSAGE>NO_USER</MESSAGE>";
            $strSpellBeeXML .= "</RESULT></CHAMPS21>";
            echo $strSpellBeeXML;
            exit;
        }
    }

    function _registerSBUser($strPeriod, $MOBILENO, $KEYWORD)
    {
        $objConn = Champs21_Db_Connection::factory()->getMasterConnection();
        $objSpellbeeDao = Champs21_Model_Dao_Factory::getInstance()->setModule('spellingbee')->getSpellingbeeDao();
        $objSpellbeeDao->setDbConnection($objConn);

        $strTime = md5(microtime());
        $strCode = substr($strTime, 0, 6);
        $MOBILENO = '88' . substr($MOBILENO, strlen($MOBILENO) - 11, 11);
        $arUserSBInfo = array();
        $arUserSBInfo['start_date'] = time();
        if ($strPeriod == "week")
            $arUserSBInfo['validity'] = strtotime("+2 days");
        else
            $arUserSBInfo['validity'] = strtotime("+1 month");
        $arUserSBInfo['mobile_no'] = $MOBILENO;
        $arUserSBInfo['code'] = $strCode;
        $objUserSBInfo = (object) $arUserSBInfo;
        $iAffected = $objSpellbeeDao->insertUserSpellInfo($objUserSBInfo);

        if ($iAffected > 0)
        {
            //header( 'content-type: text/xml' );
            $strSpellBeeXML = "<CHAMPS21><MT><MOBILENO>" . trim($MOBILENO) . "</MOBILENO><KEYWORD>" . trim($KEYWORD) . "</KEYWORD>";
            $strSpellBeeXML .= "<STATUS>SUCCESS</STATUS>";
            $strSpellBeeXML .= "<URL>http://www.champs21.com/spellingbee/mobile</URL>";
            $strSpellBeeXML .= "<CODE>" . trim($strCode) . "</CODE>";
            $strSpellBeeXML .= "<STIME>" . date("H:i", $arUserSBInfo['start_date']) . "</STIME>";
            $strSpellBeeXML .= "<SDATE>" . date("d/m/Y", $arUserSBInfo['start_date']) . "</SDATE>";
            $strSpellBeeXML .= "<ETIME>" . date("H:i", $arUserSBInfo['validity']) . "</ETIME>";
            $strSpellBeeXML .= "<EDATE>" . date("d/m/Y", $arUserSBInfo['validity']) . "</EDATE>";
            $strSpellBeeXML .= "<MESSAGE>NA</MESSAGE>";
            $strSpellBeeXML .= "</MT></CHAMPS21>";
            $objXML = simplexml_load_string($strSpellBeeXML);
            echo $objXML->asXML();
            exit;
        }
        else
        {
            $objUserSBInfo = $objSpellbeeDao->getUserSpellInfo($MOBILENO);
            if ($objUserSBInfo !== FALSE)
            {
                $this->refilAgainSB($MOBILENO, $KEYWORD);
            }
            else
            {
                header('content-type: text/xml');
                $strSpellBeeXML = "<CHAMPS21><MT><MOBILENO>" . trim($MOBILENO) . "</MOBILENO><KEYWORD>" . trim($KEYWORD) . "</KEYWORD>";
                $strSpellBeeXML .= "<STATUS>ERROR</STATUS>";
                $strSpellBeeXML .= "<URL></URL>";
                $strSpellBeeXML .= "<CODE></CODE>";
                $strSpellBeeXML .= "<STIME></STIME>";
                $strSpellBeeXML .= "<SDATE></SDATE>";
                $strSpellBeeXML .= "<ETIME></ETIME>";
                $strSpellBeeXML .= "<EDATE></EDATE>";
                $strSpellBeeXML .= "<MESSAGE>NA</MESSAGE>";
                $strSpellBeeXML .= "</MT></CHAMPS21>";
                echo $strSpellBeeXML;
                exit;
            }
        }
    }

    function _upgradeSBUser($strPeriod, $MOBILENO, $KEYWORD)
    {
        $objConn = Champs21_Db_Connection::factory()->getMasterConnection();
        $objSpellbeeDao = Champs21_Model_Dao_Factory::getInstance()->setModule('spellingbee')->getSpellingbeeDao();
        $objSpellbeeDao->setDbConnection($objConn);
        $MOBILENO = '88' . substr($MOBILENO, strlen($MOBILENO) - 11, 11);
        $objUserSBInfo = $objSpellbeeDao->getUserSpellInfo($MOBILENO);
        if ($objUserSBInfo !== FALSE)
        {
            if ($strPeriod == "week")
            {
                $iValidity = strtotime("+2 days", $objUserSBInfo->validity);
            }
            else
                $iValidity = strtotime("+1 month", $objUserSBInfo->validity);

            $iAffected = $objSpellbeeDao->upgradeSBUser($iValidity, $MOBILENO);

            header('content-type: text/xml');
            $strSpellBeeXML = "<CHAMPS21><MT><MOBILENO>" . trim($MOBILENO) . "</MOBILENO><KEYWORD>" . trim($KEYWORD) . "</KEYWORD>";
            if ($iAffected > 0)
            {
                $arSubscriptionInfo = $objSpellbeeDao->getUserSpellInfo($MOBILENO);
                $strSpellBeeXML .= "<STATUS>SUCCESS</STATUS>";
                $strSpellBeeXML .= "<URL>http://www.champs21.com/spellingbee/mobile</URL>";
                if ($arSubscriptionInfo !== FALSE)
                {
                    $strActivated = ( $arSubscriptionInfo->is_code_used == 1 ) ? "Activated" : $arSubscriptionInfo->code;
                    $strSpellBeeXML .= "<CODE>" . trim($strActivated) . "</CODE>";
                    $strSpellBeeXML .= "<STIME>" . date("H:i", $arSubscriptionInfo->start_date) . "</STIME>";
                    $strSpellBeeXML .= "<SDATE>" . date("d/m/Y", $arSubscriptionInfo->start_date) . "</SDATE>";
                    $strSpellBeeXML .= "<ETIME>" . date("H:i", $arSubscriptionInfo->validity) . "</ETIME>";
                    $strSpellBeeXML .= "<EDATE>" . date("d/m/Y", $arSubscriptionInfo->validity) . "</EDATE>";
                    $strSpellBeeXML .= "<MESSAGE>NA</MESSAGE>";
                }
                else
                {
                    $strSpellBeeXML .= "<CODE>NA</CODE>";
                    $strSpellBeeXML .= "<STIME>NA</STIME>";
                    $strSpellBeeXML .= "<SDATE>NA</SDATE>";
                    $strSpellBeeXML .= "<ETIME>NA</ETIME>";
                    $strSpellBeeXML .= "<EDATE>NA</EDATE>";
                    $strSpellBeeXML .= "<MESSAGE>NA</MESSAGE>";
                }
            }
            else
            {
                $strSpellBeeXML .= "<STATUS>ERROR</STATUS>";
                $strSpellBeeXML .= "<URL>http://www.champs21.com/spellingbee/mobile</URL>";
                $arSubscriptionInfo = $objSpellbeeDao->getUserSpellInfo($MOBILENO);
                if ($arSubscriptionInfo !== FALSE)
                {
                    if ($arSubscriptionInfo->enabled == 0)
                    {
                        $strSpellBeeXML .= "<CODE>NA</CODE>";
                        $strSpellBeeXML .= "<STIME>NA</STIME>";
                        $strSpellBeeXML .= "<SDATE>NA</SDATE>";
                        $strSpellBeeXML .= "<ETIME>NA</ETIME>";
                        $strSpellBeeXML .= "<EDATE>NA</EDATE>";
                        $strSpellBeeXML .= "<MESSAGE>UNSUBSCRIBE_USER</MESSAGE>";
                    }
                    else
                    {
                        $strSpellBeeXML .= "<CODE>NA</CODE>";
                        $strSpellBeeXML .= "<STIME>NA</STIME>";
                        $strSpellBeeXML .= "<SDATE>NA</SDATE>";
                        $strSpellBeeXML .= "<ETIME>NA</ETIME>";
                        $strSpellBeeXML .= "<EDATE>NA</EDATE>";
                        $strSpellBeeXML .= "<MESSAGE>NA</MESSAGE>";
                    }
                }
                else
                {
                    $strSpellBeeXML .= "<CODE>NA</CODE>";
                    $strSpellBeeXML .= "<STIME>NA</STIME>";
                    $strSpellBeeXML .= "<SDATE>NA</SDATE>";
                    $strSpellBeeXML .= "<ETIME>NA</ETIME>";
                    $strSpellBeeXML .= "<EDATE>NA</EDATE>";
                    $strSpellBeeXML .= "<MESSAGE>NA</MESSAGE>";
                }
            }
            $strSpellBeeXML .= "</MT></CHAMPS21>";
            echo $strSpellBeeXML;
            exit;
        }
        else
        {
            header('content-type: text/xml');
            $strSpellBeeXML = "<CHAMPS21><MT><MOBILENO>" . trim($MOBILENO) . "</MOBILENO><KEYWORD>" . trim($KEYWORD) . "</KEYWORD>";
            $strSpellBeeXML .= "<STATUS>ERROR</STATUS>";
            $strSpellBeeXML .= "<URL>http://www.champs21.com/spellingbee/mobile</URL>";
            $strSpellBeeXML .= "<CODE>NA</CODE>";
            $strSpellBeeXML .= "<STIME>NA</STIME>";
            $strSpellBeeXML .= "<SDATE>NA</SDATE>";
            $strSpellBeeXML .= "<ETIME>NA</ETIME>";
            $strSpellBeeXML .= "<EDATE>NA</EDATE>";
            $strSpellBeeXML .= "<MESSAGE>USER_NOT_EXISTS</MESSAGE>";
            $strSpellBeeXML .= "</MT></CHAMPS21>";
            echo $strSpellBeeXML;
            exit;
        }
    }

    function _stopSBUser($MOBILENO, $KEYWORD)
    {
        $objConn = Champs21_Db_Connection::factory()->getMasterConnection();
        $objSpellbeeDao = Champs21_Model_Dao_Factory::getInstance()->setModule('spellingbee')->getSpellingbeeDao();
        $objSpellbeeDao->setDbConnection($objConn);
        $MOBILENO = '88' . substr($MOBILENO, strlen($MOBILENO) - 11, 11);
        $iAffected = $objUserSBInfo = $objSpellbeeDao->stopSubscription($MOBILENO);


        header('content-type: text/xml');
        $strSpellBeeXML = "<CHAMPS21><MT><MOBILENO>" . trim($MOBILENO) . "</MOBILENO><KEYWORD>" . trim($KEYWORD) . "</KEYWORD>";
        if ($iAffected > 0)
        {
            $strSpellBeeXML .= "<STATUS>SUCCESS</STATUS>";
            $strSpellBeeXML .= "<MESSAGE>NA</MESSAGE>";
        }
        else
        {
            $strSpellBeeXML .= "<STATUS>ERROR</STATUS>";
            $objUserSBInfo = $objSpellbeeDao->getUserSpellInfo($MOBILENO);
            if ($objUserSBInfo == FALSE)
            {
                $strSpellBeeXML .= "<MESSAGE>USER_NOT_EXISTS</MESSAGE>";
            }
            else
            {
                $strSpellBeeXML .= "<MESSAGE>NA</MESSAGE>";
            }
        }
        $strSpellBeeXML .= "</MT></CHAMPS21>";
        echo $strSpellBeeXML;
        exit;
    }

    function _handleUndefinedKey($MOBILENO, $KEYWORD)
    {
        header('content-type: text/xml');
        $strSpellBeeXML = "<CHAMPS21><MT><MOBILENO>" . trim($MOBILENO) . "</MOBILENO><KEYWORD>" . trim($KEYWORD) . "</KEYWORD>";
        $strSpellBeeXML .= "<STATUS>FAILURE</STATUS>";
        $strSpellBeeXML .= "<MESSAGE>UNDEFINED_KEY</MESSAGE>";
        $strSpellBeeXML .= "</MT></CHAMPS21>";
        echo $strSpellBeeXML;
        exit;
    }

    public function getConfig()
    {
        $objConfig = Champs21_Module_Config::getConfig('core');
        $strDestination = CHAMPS21_ROOT_DIR . DS . 'var/mobile/spellingbee/';
        $strSpellBeeXML = simplexml_load_file($strDestination . 'project.xml');
        header('content-type: text/xml');
        echo $strSpellBeeXML->asXML();
        exit;
    }

    public function saveFBSpellingBee($objParams)
    {
        $arUserData = array();
        $arUserData['rank'] = "UnRanked";
        $arUserData['highestScore'] = 0;

        $objConfig = Champs21_Module_Config::getConfig('fboauth');
        $arConfig = $objConfig->facebook->toArray();
        try
        {
            $objFacebook = new Champs21_Facebook_Facebook($arConfig);
            $iFbId = $objFacebook->getUser();
            $arUserData['fb_id'] = $iFbId;
            if ($iFbId)
            {

                try
                {
                    //$arUserData['track'] = "Heres";
                    $arUserProfile = $objFacebook->api('/' . $iFbId);
                    //$arUserData['track1'] = "Heres1";
                    if (!is_array($arUserProfile) && count($arUserProfile) <= 0)
                        return (object) $arUserProfile;

                    $iFBUserName = $arUserProfile['name'];
                    $iFBUserId = $arUserProfile['id'];
                    $objConn = Champs21_Db_Connection::factory()->getMasterConnection();
                    $objSpellbeeDao = Champs21_Model_Dao_Factory::getInstance()->setModule('spellingbee')->getSpellingbeeDao();
                    $objSpellbeeDao->setDbConnection($objConn);

                    if (strlen(trim($iFBUserId)) > 0)
                    {
                        $iCurScore = (int) $objParams->score;
                        $arSaveScoreData = array();
                        $arSaveScoreData['fb_id'] = $iFBUserId;
                        $arSaveScoreData['test_time'] = $objParams->total_time; //strtotime( $objParams->total_time );
                        $arSaveScoreData['enddate'] = time();
                        $arSaveScoreData['score'] = $iCurScore;
                        $arSaveScoreData['isCheat'] = $objParams->isCheater;
                        $arSaveScoreData['spell_year'] = date('Y');
                        $arSaveScoreData['fb_user_name'] = $iFBUserName;
                        $arSaveScoreData['gptTime'] = $objParams->total_time;

                        $iUserHighestScore = $iCurScore;

                        $objSaveScoreData = (object) $arSaveScoreData;

                        $objSpellbeeDao->saveFBResult($objSaveScoreData);

                        $objUserHighestScore = $objSpellbeeDao->getFBUserHighestScore($iFBUserId);

                        if (!$objUserHighestScore)
                        {
                            $objSpellbeeDao->insertFBUserHighestScore($objSaveScoreData);
                        }
                        else if ($arSaveScoreData['score'] > $objUserHighestScore->highest_score)
                        {
                            $objSpellbeeDao->updateFBUserHighestScore($objSaveScoreData);
                        }
                        else if ($arSaveScoreData['test_time'] < $objUserHighestScore->total_time && $arSaveScoreData['score'] == $objUserHighestScore->highest_score)
                        {
                            $objSpellbeeDao->updateFBUserHighestScore($objSaveScoreData);
                        }
                        else
                        {
                            $iUserHighestScore = $objUserHighestScore->highest_score;
                        }


                        $arUserData['highestScore'] = $iUserHighestScore;

                        if ($arUserData['highestScore'] > 0)
                        {
                            $arUserData['rank'] = $objSpellbeeDao->getFBUserRank($arUserData['highestScore']);
                        }
                    }
                }
                catch (Facebook_Api_Exception $e)
                {
                    $arUserData['traaaaa'] = "aaaaa";
                    $arUserData['msgss'] = $e->getMessage();
                    return $arUserData;
                    $iFbId = null;
                }
            }
            else
            {
                $arUserData['msgss'] = 'Fb AUth Failed';
                return $arUserData;
            }
        }
        catch (Exception $e)
        {
            return (object) $arUserData;
        }



        return (object) $arUserData;
    }

    public function getFBWebScores($iLimit = 10)
    {

        $objConn = Champs21_Db_Connection::factory()->getSlaveConnection();
        $objSpellbeeDao = Champs21_Model_Dao_Factory::getInstance()->setModule('spellingbee')->getSpellingbeeDao();
        $objSpellbeeDao->setDbConnection($objConn);

        $objScores = $objSpellbeeDao->getFBLeaderBoard($iLimit);
        $arUserScores = array();
        if (count($objScores) > 0)
        {
            foreach ($objScores as $objScore)
            {
                $arUser = array();

                $arUser['user_fullname'] = ( $objScore->fb_user_name == '') ? 'No Name' : $objScore->fb_user_name;

                $arUser['high_score'] = $objScore->score;
                $arUser['time'] = $objScore->test_time;

                array_push($arUserScores, (object) $arUser);
            }
        }
        return $arUserScores;
    }

}
