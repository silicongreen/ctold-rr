<?php

class FreeuserController extends Controller
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
                'actions' => array('index','runmusic','getleaderboard','savespellingbee', 'downloadattachment','downloadlessonplan', 'create', 'getcategorypost', 'getsinglenews', 'search', "getkeywordpost"
                    , "gettagpost", "getbylinepost", "getmenu", "getassesment", "addmark", "updateplayed", "assesmenthistory",
                    "getuserinfo", "goodread", "readlater", "goodreadall", "goodreadfolder", "removegoodread"
                    , "schoolsearch", "school", "createschool", "schoolpage", "schoolactivity", "candle"
                    , "garbagecollector", "getschoolteacherbylinepost", "createcachesinglenews", "addwow", "can_share_from_web",
                    'set_preference', 'addcomments', 'getcomments', 'get_preference', 'addgcm', 'getallgcm', 'shareschoolfeed',
                    'getschoolinfo', 'joinschool', 'candleschool', 'leaveschool', 'folderdelete', 'assesmenttopscore', 'relatednews', 'regenspellcache', 'getspellstatus'),
                'users' => array('*'),
            ),
            array('deny', // deny all users
                'users' => array('*'),
            ),
        );
    }
    
    public function actionrunMusic()
    {
      
        $left = Yii::app()->request->getParam('left');
        $right = Yii::app()->request->getParam('right');
        
        $operator = Yii::app()->request->getParam('operator');
        
        $word = Yii::app()->request->getParam('word');
        
        $top = Yii::app()->request->getParam('top');
        $bottom = Yii::app()->request->getParam('bottom');
        
        
        
        $strWord = Settings::retriveWord($left, $right,$operator, $word, $top,$bottom);
       
        
      
        
        if($strWord)
        {
        
            $strMusicFile = Settings::$main_path."upload/spellingbee/".$strWord.".mp3";


            if ( file_exists( $strMusicFile ) && is_file( $strMusicFile ) && is_readable( $strMusicFile ) && filesize($strMusicFile)>500 )
            {
                //do nothing

            }
            else
            {
                Settings::downloadMP3($strWord);
            }
            
          
            header( 'Content-Description: File Transfer' );
//            header( 'Content-Type: audio/mpeg' );
//            header( 'Content-Disposition: attachment; filename=' . basename( $strMusicFile ) );
            header( 'Content-Transfer-Encoding: binary' );
            header( 'Expires: 0' );
            header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' );
            header( 'Pragma: public' );
            header( 'Content-Length: ' . filesize( $strMusicFile ) );
            readfile( $strMusicFile );
            exit;
        }
        
        
    }
    
    public function actiongetLeaderboard()
    {
        $limit = Yii::app()->request->getPost('limit');
        $division = Yii::app()->request->getPost('division');
//        $user_id = Settings::getSessionId();
        
        /** Quick Fix free_id **/
        $user_id = Yii::app()->request->getPost('free_id');
       /** Quick Fix free_id **/
        
        if(!$limit)
        {
            $limit = 30;
        } 
        if($user_id || $division)
        {
            $user_division = "";
            $user_division_main = "";
            if($user_id)
            {
                $objUser = new Freeusers();
                $user_data = $objUser->findByPk($user_id);
                
                if($user_data->division)
                {
                    $user_division = $user_data->division;
                    $user_division_main = $user_data->division;
                }
                $country = $user_data->tds_country_id;
            }
            
            if($division)
            {
                $user_division = $division;
                $country = 14;
            }
            $user_division = strtolower($user_division);
            $highscore = new Highscore();
            $current_score = 0;
            $current_time = 0;
            $cache_name_userdata = "YII-SPELLINGBEE-USERDATA-" . $user_id;
            $response = Settings::getSpellingBeeCache($cache_name_userdata);
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
            
            if($user_division_main=="rajshashi")
            {
                $user_division_main = "rajshahi";
            }  
            if($user_division=="rajshashi")
            {
                $user_division = "rajshahi";
            }
            
            
            $rresponse['data']['rank'] = $highscore->getUserRank($current_score, $current_time,$country, $user_division_main);
            
            
            
            
            $arUserScores = $highscore->getLeaderBoard($limit, $user_division, $country);
            $rresponse['data']['leaderboard'] = (array)$arUserScores;
            $rresponse['data']['division'] = $user_division_main;
            $rresponse['data']['best_score'] = $current_score;
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
    
    public function actionsaveSpellingBee()
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

            
            #$data = Yii::app()->user->free_id;
//            $data = Settings::getSessionId();
//            $valid_user = FALSE;
//            if ($data && is_int($data))
//            {
//                $autorize_check = Settings::authorizeUserCheck($objParams->left, $objParams->right, $objParams->method, $objParams->operator, $objParams->send_id, $data);
//
//                if ($autorize_check)
//                {
//                    $valid_user = TRUE;
//                }
//            }

            $arUserData = array();
            $arUserData['rank'] = "UnRanked";
            $arUserData['highestScore'] = 0;
            
            /** Quick Fix free_id **/
            $data = Yii::app()->request->getPost('free_id');
            $data = (int)$data;
            $valid_user = TRUE;
            /** Quick Fix free_id **/
            
            if ($valid_user)
            {
                $iUserId = $data;
                $objUser = new Freeusers();
                $user_data = $objUser->findByPk($iUserId);

                if ($user_data)
                {
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
                        Settings::setSpellingBeeCache($cache_name_userdata, $response);
                    }
                    else
                    {
                        $score_for_rank = $current_score;
                        $time_for_rank = $current_time;
                    }
                    $arUserData['highestScore'] = $score_for_rank;
                    $arUserData['rank'] = $highscore->getUserRank($score_for_rank, $time_for_rank,$user_data->tds_country_id, strtolower($user_data->division));
                    
                    
                    
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
    
    public function actionDownloadLessonplan()
    {

        $id = $_GET['id'];
        if ($id)
        {
            $lessonplan = new Lessonplan();
            $lessonplantobj = $lessonplan->findByPk($id);
            if ($lessonplantobj->attachment_file_name)
            {
                $attachment_datetime_chunk = explode(" ", $lessonplantobj->updated_at);

                $attachment_date_chunk = explode("-", $attachment_datetime_chunk[0]);
                $attachment_time_chunk = explode(":", $attachment_datetime_chunk[1]);

                $attachment_extra = $attachment_date_chunk[0] . $attachment_date_chunk[1] . $attachment_date_chunk[2];
                $attachment_extra.= $attachment_time_chunk[0] . $attachment_date_chunk[1] . $attachment_time_chunk[2];

                $url = Settings::$paid_image_path ."uploads/lessonplans/attachments/" . $id . "/original/" . str_replace(" ", "+", $lessonplantobj->attachment_file_name) . "?" . $attachment_extra;

                header("Content-Disposition: attachment; filename=" . $lessonplantobj->attachment_file_name);
                header("Content-Type: {$lessonplantobj->attachment_content_type}");
                header("Content-Length: " . $lessonplantobj->attachment_file_size);
                readfile($url);
            }
        }
    }

    public function actionDownloadAttachment()
    {

        $id = $_GET['id'];
        if ($id)
        {
            $assignment = new Assignments();
            $assignmentobj = $assignment->findByPk($id);
            if ($assignmentobj->attachment_file_name)
            {
                $attachment_datetime_chunk = explode(" ", $assignmentobj->updated_at);

                $attachment_date_chunk = explode("-", $attachment_datetime_chunk[0]);
                $attachment_time_chunk = explode(":", $attachment_datetime_chunk[1]);

                $attachment_extra = $attachment_date_chunk[0] . $attachment_date_chunk[1] . $attachment_date_chunk[2];
                $attachment_extra.= $attachment_time_chunk[0] . $attachment_date_chunk[1] . $attachment_time_chunk[2];
                
                $url = Settings::$paid_image_path ."uploads/assignments/attachments/" . $id . "/original/" . urlencode(str_replace(" ", "+", $assignmentobj->attachment_file_name)) . "?" . $attachment_extra;              

                header("Content-Disposition: attachment; filename=" . $assignmentobj->attachment_file_name);
                header("Content-Type: {$assignmentobj->attachment_content_type}");
                header("Content-Length: " . $assignmentobj->attachment_file_size);
                readfile($url);
            }
        }
    }

    public function actionShareSchoolFeed()
    {
        $id = Yii::app()->request->getPost('id');
        $user_id = Yii::app()->request->getPost('user_id');
        if (!$id || !$user_id)
        {
            $response['status']['code'] = 400;
            $response['status']['msg'] = "Bad Request";
        }
        else
        {
            $schooluser = new SchoolUser();
            $user_schools = $schooluser->userSchool($user_id);
            if (isset($user_schools[0]['school_id']))
            {
                $school_id = $user_schools[0]['school_id'];
                $objpost = new PostSchoolShare();
                $already_share = $objpost->getSchoolSharePost($school_id, $id);

                if ($already_share)
                {

                    $response['status']['code'] = 404;
                    $response['status']['msg'] = "ALREADY_SHARE";
                }
                else
                {
                    $objpostmain = new Post();

                    $postData = $objpostmain->findByPk($id);

                    if ($postData)
                    {
                        if ($postData->school_id)
                        {
                            $response['status']['code'] = 400;
                            $response['status']['msg'] = "Bad Request";
                        }
                        else
                        {
                            $objpost->post_id = $id;
                            $objpost->school_id = $school_id;
                            $objpost->user_id = $user_id;
                            $objpost->created_date = date("Y-m-d H:i:s");
                            $objpost->save();
                            $response['status']['code'] = 200;
                            $response['status']['msg'] = "Successfully Saved";
                        }
                    }
                    else
                    {
                        $response['status']['code'] = 400;
                        $response['status']['msg'] = "Bad Request";
                    }
                }
            }
            else
            {
                $response['status']['code'] = 400;
                $response['status']['msg'] = "Bad Request";
            }
        }
        echo CJSON::encode($response);
        Yii::app()->end();
    }

    public function actionRelatednews()
    {
        $id = Yii::app()->request->getPost('id');
        
        if (!$id)
        {
            $response['status']['code'] = 400;
            $response['status']['msg'] = "Bad Request";
        }
        else
        {

            $objrelated = new RelatedNews();
            $rnews = $objrelated->getRelatedNews($id);
            $post_data = array();
            $i = 0;
            foreach ($rnews as $value)
            {
                $post_data[$i] = $this->getSingleNewsFromCache($value['id']);
                $i++;
            }
            
            $response['data']['post'] = $post_data;
            $response['status']['code'] = 200;
            $response['status']['msg'] = "success";
        }
        echo CJSON::encode($response);
        Yii::app()->end();
    }

    public function actionFolderDelete()
    {
        $user_id = Yii::app()->request->getPost('user_id');
        $folder_name = Yii::app()->request->getPost('folder_name');
        if (!$user_id || !$folder_name)
        {
            $response['status']['code'] = 400;
            $response['status']['msg'] = "Bad Request";
        }
        else
        {
            $folders = Settings::$ar_default_folder;
            $fobj = new UserFolder();
            $return = $fobj->removeFolder($folder_name, $user_id, $folders);
            if ($return)
            {
                $goodread = new UserGoodRead();
                $goodread->deleteAll("folder_id=:folder_id", array(':folder_id' => $return));
                $response['status']['code'] = 200;
                $response['status']['msg'] = "success";
            }
            else
            {
                $response['status']['code'] = 404;
                $response['status']['msg'] = "Cant delete this folder";
            }
        }
        echo CJSON::encode($response);
        Yii::app()->end();
    }

    public function actionAssesmentTopScore()
    {

        $id = Yii::app()->request->getPost('id');
        $limit = Yii::app()->request->getPost('limit');
        $type = Yii::app()->request->getPost('type');

        if (!$id)
        {
            $response['status']['code'] = 400;
            $response['status']['msg'] = "Bad Request";
        }
        else
        {

            $objcmark = new Cmark();
            if (!$limit)
            {
                $limit = 100;
            }
            
            if ($type == 2)
            {
                $objassessment = $objcmark->getTopMark($id, $limit, 0, $type);
                $response['data']['assesment'] = $objassessment;

                $assessment_school_mark = new AssesmentSchoolMark();
                $response['data']['school_score_board'] = $assessment_school_mark->getSchoolTopMark($id, 100);
                
            } else {
                
                $objassessment = $objcmark->getTopMark($id, $limit);
                $response['data']['assesment'] = $objassessment;                
            }

            $response['status']['code'] = 200;
            $response['status']['msg'] = "success";
        }
        echo CJSON::encode($response);
        Yii::app()->end();
    }

    public function actionAssesmentHistory()
    {

        $user_id = Yii::app()->request->getPost('user_id');
        if (!$user_id)
        {
            $response['status']['code'] = 400;
            $response['status']['msg'] = "Bad Request";
        }
        else
        {

            $objcmark = new Cmark();
            $objassessment = $objcmark->getUserMark($user_id);
            $response['data']['assesment'] = $objassessment;
            $response['status']['code'] = 200;
            $response['status']['msg'] = "success";
        }
        echo CJSON::encode($response);
        Yii::app()->end();
    }

    public function actionUpdatePlayed()
    {
        $assessment_id = Yii::app()->request->getPost('assessment_id');
        if (!$assessment_id)
        {
            $response['status']['code'] = 400;
            $response['status']['msg'] = "Bad Request";
        }
        else
        {

            $assesmentObj = new Cassignments();
            $objassessment = $assesmentObj->findByPk($assessment_id);
            $objassessment->played = $objassessment->played + 1;
            $objassessment->save();
            $response['status']['code'] = 200;
            $response['status']['msg'] = "success";
        }
        echo CJSON::encode($response);
        Yii::app()->end();
    }

    public function actionAddMark()
    {
        $assessment_id = Yii::app()->request->getPost('assessment_id');
        $user_id = Yii::app()->request->getPost('user_id');
        $mark = Yii::app()->request->getPost('mark');
        $time_taken = Yii::app()->request->getPost('time_taken');
        $avg_time = Yii::app()->request->getPost('avg_time');
        if (!$assessment_id || (!$mark && $mark !== 0) || !$user_id)
        {
            $response['status']['code'] = 400;
            $response['status']['msg'] = "Bad Request";
        }
        else
        {

            $assesmentObj = new Cassignments();

            $objassessment = $assesmentObj->findByPk($assessment_id);

            if ($user_id)
            {
                $objcmark = new Cmark();
                $objassessment = $objcmark->getUserMarkAssessment($user_id, $assessment_id);
                $can_play = true;
                if (isset($objassessment->created_date))
                {
                    $last_played = $objassessment->created_date;
                    $can_play_date = date("Y-m-d H:i:s", strtotime("-1 Day"));
                    if ($objassessment->created_date > $can_play_date)
                    {
                        $can_play = false;
                    }
                }
                if ($can_play)
                {
                    $add = false;
                    $new = false;
                    if ($objassessment)
                    {
                        $marksobj = $objcmark->findByPk($objassessment->id);
                        if ($objassessment->mark < $mark)
                        {
                            $marksobj->delete();
                            $add = true;
                        }
                        else if ($objassessment->mark == $mark &&
                                ($objassessment->time_taken > $time_taken ||
                                ($objassessment->time_taken == $time_taken && $objassessment->avg_time_per_ques > $avg_time))
                        )
                        {
                            $marksobj->delete();
                            $add = true;
                        }
                        else
                        {
                            $marksobj->created_date = date("Y-m-d H:i:s");
                            $marksobj->no_played = $marksobj->no_played + 1;
                            $marksobj->save();
                        }
                    }
                    else
                    {
                        $add = true;
                        $new = true;
                    }
                    if ($add)
                    {
                        $objcmark->mark = $mark;
                        $objcmark->user_id = $user_id;
                        $objcmark->created_date = date("Y-m-d H:i:s");
                        if ($time_taken)
                        {
                            $objcmark->time_taken = $time_taken;
                        }
                        if ($avg_time)
                        {
                            $objcmark->avg_time_per_ques = $avg_time;
                        }
                        if ($new)
                        {
                            $objcmark->no_played = 1;
                        }
                        else
                        {
                            $objcmark->no_played = $marksobj->no_played + 1;
                        }
                        $objcmark->assessment_id = $assessment_id;
                        $objcmark->save();
                    }
                    $response['data']['last_played'] = date("Y-m-d H:i:s");
                    $response['status']['code'] = 200;
                    $response['status']['msg'] = "success";
                }
                else
                {
                    $response['data']['last_played'] = $last_played;
                    $response['status']['code'] = 404;
                    $response['status']['msg'] = "Can-play-now";
                }
            }
            else
            {
                $response['status']['code'] = 400;
                $response['status']['msg'] = "Bad Request";
            }
        }
        echo CJSON::encode($response);
        Yii::app()->end();
    }

    public function actionGetAssesment()
    {
        $assesment_id = Yii::app()->request->getPost('assesment_id');
        $webview = Yii::app()->request->getPost('webview');
        $user_id = Yii::app()->request->getPost('user_id');
        $limit = Yii::app()->request->getPost('limit');
        $type = Yii::app()->request->getPost('type');
        $level = Yii::app()->request->getPost('level');

        if (!$assesment_id)
        {
            $response['status']['code'] = 400;
            $response['status']['msg'] = "Bad Request";
        }
        else
        {

            if ($webview == 1)
            {
                $webview = TRUE;
            }
            else
            {
                $webview = FALSE;
            }

            $assesmentObj = new Cassignments();
            $cmark = new Cmark();

            if (!$limit)
            {
                if ($webview)
                {
                    $limit = 10;
                }
                else
                {
                    $limit = 3;
                }
            }
            $last_played = "";
            $can_play = true;
            $total_score = 0;

            $user_score_board = array();
            if ($user_id)
            {
                $objcmark = new Cmark();
                $objassessment = $objcmark->getUserMarkAssessment($user_id, $assesment_id, $type);

                if (is_array($objassessment))
                {

                    $i = 1;
                    foreach ($objassessment as $assessment)
                    {
                        $user_score_board[$assessment->level]['user_id'] = $assessment->user_id;
                        $user_score_board[$assessment->level]['mark'] = $assessment->mark;
                        $total_score += $assessment->mark;
                        $i++;
                    }
                }

                if (isset($objassessment->created_date))
                {
                    $last_played = $objassessment->created_date;

                    $can_play_date = date("Y-m-d H:i:s", strtotime("-1 Day"));
                    if ($objassessment->created_date > $can_play_date)
                    {
                        $can_play = false;
                    }
                }
            }

            $response['data']['current_date'] = date("Y-m-d H:i:s");
            $response['data']['last_played'] = $last_played;
            $response['data']['can_play'] = $can_play;
            
            if ($type == 2)
            {
                $response['data']['score_board'] = $cmark->getTopMark($assesment_id, $limit, 0, $type);

                $assessment_school_mark = new AssesmentSchoolMark();
                $response['data']['school_score_board'] = $assessment_school_mark->getSchoolTopMark($assesment_id, 100);
                
            } else {
                
                $response['data']['score_board'] = $cmark->getTopMark($assesment_id, $limit);
            }
            
            $response['data']['higistmark'] = $cmark->assessmentHighistMark($assesment_id);
            $response['data']['assesment'] = $assesmentObj->getAssessment($assesment_id, $webview, $type, $level);
            $response['data']['assesment']['levels'] = $assesmentObj->getAssessmentLevels($assesment_id);
            $response['data']['assesment']['user_score_board'] = $user_score_board;
            $response['data']['assesment']['total_score'] = $total_score;
            $response['data']['assesment']['higistmark'] = $response['data']['higistmark'];
            $response['status']['code'] = 200;
            $response['status']['msg'] = "success";
        }
        echo CJSON::encode($response);
        Yii::app()->end();
    }

    public function actionAddWow()
    {
        $post_id = Yii::app()->request->getPost('post_id');
        $user_id = Yii::app()->request->getPost('user_id');
        if (!$post_id || (!$user_id && Settings::$wow_login == true))
        {
            $response['status']['code'] = 400;
            $response['status']['msg'] = "Bad Request";
        }
        else
        {
            $objwow = new Wow();
            if (Settings::$wow_login == false || !$objwow->wowexists($post_id, $user_id))
            {
                if (Settings::$wow_login == true)
                {
                    $objwow->post_id = $post_id;
                    $objwow->user_id = $user_id;
                    $objwow->save();
                }

                $postModel = new Post();
                $postobj = $postModel->findByPk($post_id);
                $postobj->wow_count = $postobj->wow_count + 1;
                $postobj->save();

                $cache_name = "YII-SINGLE-POST-CACHE-" . $post_id;
                $cache_data = Yii::app()->cache->get($cache_name);
                if ($cache_data !== false)
                {
                    $cache_data['wow_count'] = $cache_data['wow_count'] + 1;
                    $singlepost = $cache_data;
                    Yii::app()->cache->set($cache_name, $singlepost, 5184000);
                }


                $response['data']['wow_count'] = $postobj->wow_count;
                $response['status']['code'] = 200;
                $response['status']['msg'] = "success";
            }
            else
            {
                $postModel = new Post();
                $postobj = $postModel->findByPk($post_id);
                $response['data']['wow_count'] = $postobj->wow_count;
                $response['status']['code'] = 200;
                $response['status']['msg'] = "success";
            }
        }
        echo CJSON::encode($response);
        Yii::app()->end();
    }

    public function actionLeaveSchool()
    {
        $school_id = Yii::app()->request->getPost('school_id');
        $user_id = Yii::app()->request->getPost('user_id');
        if (!$school_id || !$user_id)
        {
            $response['status']['code'] = 400;
            $response['status']['msg'] = "Bad Request";
        }
        else
        {
            $schooluser = new SchoolUser();
            $userschool = $schooluser->userSchoolSingle($user_id, $school_id);
            if ($userschool)
            {
                $schooluser->deleteByPk($userschool->id);
            }
            $freeuserObj = new Freeusers();
            $user_info = $freeuserObj->getUserInfo($user_id);

            $response['data']['userinfo'] = $user_info;
            $response['status']['code'] = 200;
            $response['status']['msg'] = "success";
        }
        echo CJSON::encode($response);
        Yii::app()->end();
    }

    public function actionJoinSchool()
    {
        $school_id = Yii::app()->request->getPost('school_id');
        $user_id = Yii::app()->request->getPost('user_id');
        $type = Yii::app()->request->getPost('type');
        $information = Yii::app()->request->getPost('information');
        $grade = Yii::app()->request->getPost('grade');

        if (!$school_id || !$user_id || !$type || !$grade || !$information || !isset(Settings::$school_join_approved[$type]))
        {
            $response['status']['code'] = 400;
            $response['status']['msg'] = "Bad Request";
        }
        else
        {

            $is_approved = 0;

            if (Settings::$school_join_approved[$type] === false)
            {
                $is_approved = 1;
            }
            $school_join = array();

            $schooluser = new SchoolUser();
            $school_user = $schooluser->userSchool($user_id, $school_id);
            if (count($school_user) > 0)
            {
                foreach ($school_user as $value)
                {
                    $school_join[$value['school_id']] = $value['status'];
                }
            }

            if (isset($school_join[$school_id]))
            {
                //do nothing
            }
            else
            {
                $schooluser->user_id = $user_id;
                $schooluser->school_id = $school_id;
                $schooluser->is_approved = $is_approved;
                $schooluser->type = $type;
                $schooluser->grade = $grade;
                $schooluser->information = $information;
                $schooluser->save();
            }

            $freeuserObj = new Freeusers();
            $user_info = $freeuserObj->getUserInfo($user_id);




            $response['data']['userinfo'] = $user_info;


            $response['status']['code'] = 200;
            $response['status']['msg'] = "SUCCESSFULLY-SAVED";
        }
        echo CJSON::encode($response);
        Yii::app()->end();
    }

    public function actionGetAllGcm()
    {
        $cache_name = "YII-RESPONSE-GCM";
        $request_llicence = Yii::app()->request->getPost('request_llicence');
        if (Settings::$api_llicence_key == $request_llicence)
        {
            $response = Yii::app()->cache->get($cache_name);
            if ($response === false)
            {

                $gcmobj = new Gcm();
                $response = $gcmobj->getAllGcm();
                Yii::app()->cache->set($cache_name, $response);
            }
            echo CJSON::encode($response);
            Yii::app()->end();
        }
    }

    public function actionAddGcm()
    {
        $gcm_id = Yii::app()->request->getPost('gcm_id');
        $device_id = Yii::app()->request->getPost('device_id');
        if ($gcm_id)
        {
            $gcmobj = new Gcm();


            $gcm_added = $gcmobj->getGcm($gcm_id);

            if (!$gcm_added)
            {
                if ($device_id)
                {
                    $gcm_device = $gcmobj->getGcmDeviceId($device_id);
                    if ($gcm_device)
                    {
                        $pobj = $gcmobj->findByPk($gcm_device);
                        $pobj->delete();
                    }
                }
                $gcmobj->gcm_id = $gcm_id;
                $gcmobj->device_id = $device_id;
                $gcmobj->save();
                $cache_name = "YII-RESPONSE-GCM";
                Yii::app()->cache->delete($cache_name);
            }
            $response['data']['id'] = $gcm_id;
            $response['status']['code'] = 200;
            $response['status']['msg'] = "SUCCESFULLY_SAVED";
        }
        else
        {
            $response['data']['id'] = 0;
            $response['status']['code'] = 400;
            $response['status']['msg'] = "Bad Request";
        }
        echo CJSON::encode($response);
        Yii::app()->end();
    }

    public function actionCreateSchool()
    {
        $user_id = Yii::app()->request->getPost('user_id');
        $school_name = Yii::app()->request->getPost('school_name');
        $contact = Yii::app()->request->getPost('contact');
        $about = Yii::app()->request->getPost('about');
        $address = Yii::app()->request->getPost('address');
        $zip_code = Yii::app()->request->getPost('zip_code');
        if (!$user_id || !$school_name || !$contact || !$address || !$about)
        {
            $response['status']['code'] = 400;
            $response['status']['msg'] = "Bad Request";
        }
        else
        {
            $userCretedSchool = new UserCreatedSchool();
            $userCretedSchool->freeuser_id = $user_id;
            $userCretedSchool->school_name = $school_name;
            $userCretedSchool->contact = $contact;
            $userCretedSchool->about = $about;
            $userCretedSchool->address = $address;
            $userCretedSchool->zip_code = $zip_code;
            if (!empty($_FILES['picture']['name']))
            {
                $main_dir = 'upload/user_submitted_image/';
                $uploads_dir = Settings::$main_path . 'upload/user_submitted_image/';
                $tmp_name = $_FILES["picture"]["tmp_name"];
                $name = "school_picture_" . time() . "_" . str_replace(" ", "-", $_FILES["picture"]["name"]);

                move_uploaded_file($tmp_name, "$uploads_dir/$name");
                $userCretedSchool->picture = $main_dir . $name;
            }
            if (!empty($_FILES['logo']['name']))
            {
                $main_dir = 'upload/user_submitted_image/';
                $uploads_dir = Settings::$main_path . 'upload/user_submitted_image/';
                $tmp_name = $_FILES["logo"]["tmp_name"];
                $name = "school_logo_" . time() . "_" . str_replace(" ", "-", $_FILES["logo"]["name"]);

                move_uploaded_file($tmp_name, "$uploads_dir/$name");
                $userCretedSchool->logo = $main_dir . $name;
            }

            $userCretedSchool->save();

            $response['status']['code'] = 200;
            $response['status']['msg'] = "Successfully Saved";
        }
        echo CJSON::encode($response);
        Yii::app()->end();
    }

    public function actionCandleSchool()
    {
        $username = Yii::app()->request->getPost('username');
        $headline = Yii::app()->request->getPost('headline');
        $content = Yii::app()->request->getPost('content');
        $category_id = Settings::$school_category_id;
        $show_comment_to_all = Yii::app()->request->getPost('show_comment_to_all');
        $can_comment = Yii::app()->request->getPost('can_comment');
        $school_id = Yii::app()->request->getPost('school_id');
        $user_id = Yii::app()->request->getPost('user_id');
        $mobile_num = Yii::app()->request->getPost('mobile_num');
        $candle_type = Yii::app()->request->getPost('candle_type');

        if (!$username || !$headline || !$content || !$category_id || !$school_id || !$user_id)
        {
            $response['status']['code'] = 400;
            $response['status']['msg'] = "Bad Request";
        }
        else
        {
            $user_school = new SchoolUser();
            $userschool = $user_school->userSchoolSingle($user_id, $school_id);
            if (isset($userschool->is_approved) && $userschool->is_approved == 1)
            {

                $postobj = new Post();
                $postobj->headline = $headline;
                $postobj->content = $content;
                $objfreeuser = new Freeusers();
                $freeobj = $objfreeuser->findByPk($user_id);
                if ($mobile_num)
                {
                    $postobj->mobile_num = $mobile_num;

                    if (!$freeobj->mobile_no)
                    {
                        $freeobj->mobile_no = $mobile_num;
                        $freeobj->save();
                    }
                }
                if ($freeobj->profile_image)
                {
                    $postobj->author_image_post = $freeobj->profile_image;
                }
                $postobj->published_date = date("Y-m-d H:i:s");
                $postobj->status = 1;
                if (Settings::$school_candle_publish[$userschool->type] === true)
                {
                    $postobj->status = 5;
                }
                if ($show_comment_to_all)
                {
                    $postobj->show_comment_to_all = $show_comment_to_all;
                }

                if ($can_comment)
                {
                    $postobj->can_comment = $can_comment;
                }

                $postobj->user_id = $user_id;

                $postobj->type = "Print";
                $postobj->user_type = 2;
                $postobj->language = "en";
                $postobj->school_id = $school_id;

                $objbyline = new Bylines();
                $postobj->byline_id = $objbyline->generate_byline_id($username);

                if (!empty($_FILES['leadimage']['name']))
                {
                    $main_dir = 'upload/user_submitted_image/';
                    $uploads_dir = Settings::$main_path . 'upload/user_submitted_image/';
                    $tmp_name = $_FILES["leadimage"]["tmp_name"];
                    $name = "image_" . time() . "_" . str_replace(" ", "-", $_FILES["leadimage"]["name"]);

                    move_uploaded_file($tmp_name, "$uploads_dir/$name");
                    $postobj->lead_material = $main_dir . $name;
                }
                $postobj->save();

                if (!empty($_FILES['attach_file']['name']))
                {
                    $main_dir = 'upload/user_submitted_image/';
                    $uploads_dir = Settings::$main_path . 'upload/user_submitted_image/';
                    $tmp_name = $_FILES["attach_file"]["tmp_name"];
                    $name = "file_" . time() . "_" . str_replace(" ", "-", $_FILES["attach_file"]["name"]);

                    move_uploaded_file($tmp_name, "$uploads_dir/$name");

                    $postAttachmentObj = new PostAttachment();

                    $postAttachmentObj->file_name = $main_dir . $name;
                    $postAttachmentObj->post_id = $postobj->id;
                    $postAttachmentObj->show = 1;
                    $postAttachmentObj->save();
                }

                $objpostcategory = new PostCategory();

                $objpostcategory->post_id = $postobj->id;
                $objpostcategory->category_id = $category_id;

                $objpostcategory->save();



                for ($i = 1; $i <= Settings::$allclass; $i++)
                {
                    $objpostclass = new PostClass();
                    $objpostclass->post_id = $postobj->id;
                    $objpostclass->class_id = $i;
                    $objpostclass->save();
                }


                if (Yii::app()->request->getPost('type'))
                {
                    foreach (Yii::app()->request->getPost('type') as $value)
                    {
                        $objposttype = new PostType();
                        $objposttype->post_id = $postobj->id;
                        $objposttype->type_id = $value;
                        $objposttype->save();
                    }
                }
                else
                {
                    for ($i = 1; $i <= 4; $i++)
                    {
                        $objposttype = new PostType();
                        $objposttype->post_id = $postobj->id;
                        $objposttype->type_id = $i;
                        $objposttype->save();
                    }
                }

                $response['status']['code'] = 200;
                $response['status']['msg'] = "Successfully Saved";
            }
            else
            {
                $response['status']['code'] = 400;
                $response['status']['msg'] = "Bad Request";
            }
        }
        echo CJSON::encode($response);
        Yii::app()->end();
    }

    public function actionCandle()
    {
        $username = Yii::app()->request->getPost('username');
        $headline = Yii::app()->request->getPost('headline');
        $content = Yii::app()->request->getPost('content');
        $mobile_num = Yii::app()->request->getPost('mobile_num');
        $category_id = Yii::app()->request->getPost('category_id');
        $user_id = Yii::app()->request->getPost('user_id');
        if (!$username || !$headline || !$content || !$category_id || !$user_id)
        {
            $response['status']['code'] = 400;
            $response['status']['msg'] = "Bad Request";
        }
        else
        {
            $postobj = new Post();
            $postobj->headline = $headline;
            $postobj->content = $content;
            $postobj->published_date = date("Y-m-d H:i:s");
            $postobj->status = 1;
            $postobj->type = "Print";
            $postobj->user_type = 2;
            $postobj->language = "en";

            $postobj->user_id = $user_id;

            $objfreeuser = new Freeusers();
            $freeobj = $objfreeuser->findByPk($user_id);
            if ($mobile_num)
            {
                $postobj->mobile_num = $mobile_num;

                if (!$freeobj->mobile_no)
                {
                    $freeobj->mobile_no = $mobile_num;
                    $freeobj->save();
                }
            }
            if ($freeobj->profile_image)
            {
                $postobj->author_image_post = $freeobj->profile_image;
            }

            $objbyline = new Bylines();
            $postobj->byline_id = $objbyline->generate_byline_id($username);

            if (!empty($_FILES['leadimage']['name']))
            {
                $main_dir = 'upload/user_submitted_image/';
                $uploads_dir = Settings::$main_path . 'upload/user_submitted_image/';
                $tmp_name = $_FILES["leadimage"]["tmp_name"];
                $name = "image_" . time() . "_" . str_replace(" ", "-", $_FILES["leadimage"]["name"]);

                move_uploaded_file($tmp_name, "$uploads_dir/$name");
                $postobj->lead_material = $main_dir . $name;
            }
            $postobj->save();

            if (!empty($_FILES['attach_file']['name']))
            {
                $main_dir = 'upload/user_submitted_image/';
                $uploads_dir = Settings::$main_path . 'upload/user_submitted_image/';
                $tmp_name = $_FILES["attach_file"]["tmp_name"];
                $name = "file_" . time() . "_" . str_replace(" ", "-", $_FILES["attach_file"]["name"]);

                move_uploaded_file($tmp_name, "$uploads_dir/$name");

                $postAttachmentObj = new PostAttachment();

                $postAttachmentObj->file_name = $main_dir . $name;
                $postAttachmentObj->post_id = $postobj->id;
                $postAttachmentObj->show = 1;
                $postAttachmentObj->save();
            }

            $objpostcategory = new PostCategory();

            $objpostcategory->post_id = $postobj->id;
            $objpostcategory->category_id = $category_id;

            $objpostcategory->save();

            for ($i = 1; $i <= Settings::$allclass; $i++)
            {
                $objpostclass = new PostClass();
                $objpostclass->post_id = $postobj->id;
                $objpostclass->class_id = $i;
                $objpostclass->save();
            }

            if (Yii::app()->request->getPost('type'))
            {
                foreach (Yii::app()->request->getPost('type') as $value)
                {
                    $objposttype = new PostType();
                    $objposttype->post_id = $postobj->id;
                    $objposttype->type_id = $value;
                    $objposttype->save();
                }
            }

            $response['status']['code'] = 200;
            $response['status']['msg'] = "Successfully Saved";
        }
        echo CJSON::encode($response);
        Yii::app()->end();
    }

    public function actionSchoolActivity()
    {
        $school_id = Yii::app()->request->getPost('school_id');
        $page_size = Yii::app()->request->getPost('page_size');
        $page_number = Yii::app()->request->getPost('page_number');
        if (empty($page_number))
        {
            $page_number = 1;
        }
        if (empty($page_size))
        {
            $page_size = 10;
        }
        if (!$school_id)
        {
            $response['status']['code'] = 400;
            $response['status']['msg'] = "Bad Request";
        }
        else
        {

            $schoolActivity = new SchoolActivities();

            $response['data']['total'] = $schoolActivity->getActivityTotal($school_id);
            $has_next = false;
            if ($response['data']['total'] > $page_number * $page_size)
            {
                $has_next = true;
            }
            $response['data']['has_next'] = $has_next;

            $response['data']['activity'] = $schoolActivity->getActivity($school_id, $page_size, $page_number);

            $response['status']['code'] = 200;
            $response['status']['msg'] = "DATA_FOUND";
        }
        echo CJSON::encode($response);
        Yii::app()->end();
    }

    public function actionSchoolPage()
    {
        $school_id = Yii::app()->request->getPost('school_id');
        $page_id = Yii::app()->request->getPost('page_id');
        $menu_id = Yii::app()->request->getPost('menu_id');



        if (!$school_id || !$page_id || !$menu_id)
        {
            $response['status']['code'] = 400;
            $response['status']['msg'] = "Bad Request";
        }
        else
        {
            $school_page = new SchoolPage();

            $response['data']['page_details'] = $school_page->pageDetails($page_id);
            $response['data']['activity'] = array();
            if ($menu_id == 1)
            {
                $schoolActivity = new SchoolActivities();
                $response['data']['activity'] = $schoolActivity->getActivity($school_id);
            }

            $response['status']['code'] = 200;
            $response['status']['msg'] = "DATA_FOUND";
        }
        echo CJSON::encode($response);
        Yii::app()->end();
    }

    public function actionGetSchoolInfo()
    {
        $user_id = Yii::app()->request->getPost('user_id');
        $school_id = Yii::app()->request->getPost('school_id');
        if (!$user_id)
        {
            $user_id = 0;
        }
        if (!$school_id)
        {
            $response['status']['code'] = 400;
            $response['status']['msg'] = "Bad Request";
        }
        else
        {
            $schoolobj = new School();
            $response['data']['schools'] = $schoolobj->getSchoolInfo($school_id, $user_id);
            $schoolActivity = new SchoolActivities();
            $response['data']['activity'] = $schoolActivity->getActivity($school_id);

            $response['status']['code'] = 200;
            $response['status']['msg'] = "DATA_FOUND";
        }
        echo CJSON::encode($response);
        Yii::app()->end();
    }

    public function actionSchool()
    {

        $page_size = Yii::app()->request->getPost('page_size');
        $page_number = Yii::app()->request->getPost('page_number');
        $user_id = Yii::app()->request->getPost('user_id');
        $myschool = Yii::app()->request->getPost('myschool');
        $userschool = false;
        if ($myschool == 1)
        {
            $userschool = true;
        }
        if (!$user_id)
        {
            $user_id = 0;
        }
        if (empty($page_number))
        {
            $page_number = 1;
        }
        if (empty($page_size))
        {
            $page_size = 10;
        }


        $schoolobj = new School();

        if ($user_id && $userschool)
        {
            $response['data']['total'] = $schoolobj->getSchoolTotal($user_id, $userschool);
        }
        else
        {
            $response['data']['total'] = $schoolobj->getSchoolTotal();
        }
        $has_next = false;
        if ($response['data']['total'] > $page_number * $page_size)
        {
            $has_next = true;
        }
        $response['data']['has_next'] = $has_next;

        $response['data']['schools'] = $schoolobj->Schools($page_size, $page_number, $user_id, $userschool);

        $response['status']['code'] = 200;
        $response['status']['msg'] = "DATA_FOUND";

        echo CJSON::encode($response);
        Yii::app()->end();
    }

    public function actionSchoolSearch()
    {
        $name = Yii::app()->request->getPost('name');
        $division = Yii::app()->request->getPost('division');
        $medium = Yii::app()->request->getPost('medium');
        $location = Yii::app()->request->getPost('location');
        if (!$name && !$division && !$location && !$medium)
        {
            $response['data']['schools'] = array();
            $response['status']['code'] = 200;
            $response['status']['msg'] = "NOT_FOUND";
        }
        else
        {
            $schoolobj = new School();

            $response['data']['schools'] = $schoolobj->getSchhool($name, $division, $medium, $location);

            $response['status']['code'] = 200;
            $response['status']['msg'] = "DATA_FOUND";
        }

        echo CJSON::encode($response);
        Yii::app()->end();
    }

    public function actionGetMenu()
    {
        $user_id = Yii::app()->request->getPost('user_id');
        $categoryObj = new Categories();
        if (!$user_id)
            $user_id = 0;

        $response['data']['menu'] = $categoryObj->getParentCategory($user_id);
        $response['status']['code'] = 200;
        $response['status']['msg'] = "DATA_FOUND";

        echo CJSON::encode($response);
        Yii::app()->end();
    }

    public function actionRemoveGoodRead()
    {
        $user_id = Yii::app()->request->getPost('user_id');
        $post_id = Yii::app()->request->getPost('post_id');
        $folder_name = Yii::app()->request->getPost('folder_name');
        $folder_id = Yii::app()->request->getPost('folder_id');

        if (!$user_id || (!$folder_name && !$folder_id) || !$post_id)
        {
            $response['status']['code'] = 400;
            $response['status']['msg'] = "Bad Request";
        }
        else
        {
            $obj_good_read = new UserGoodRead();
            $obj_good_read->removeGoodRead($post_id, $user_id, $folder_name, $folder_id);
            $response['status']['code'] = 200;
            $response['status']['msg'] = "DATA_FOUND";
        }
        echo CJSON::encode($response);
        Yii::app()->end();
    }

    public function actionGoodReadFolder()
    {
        $user_id = Yii::app()->request->getPost('user_id');
        $folderObj = new UserFolder();
        if (!$user_id)
        {
            $response['status']['code'] = 400;
            $response['status']['msg'] = "Bad Request";
        }
        else
        {
            $response['data']['folder'] = $folderObj->getAllFolder($user_id);
            $response['status']['code'] = 200;
            $response['status']['msg'] = "DATA_FOUND";
        }
        echo CJSON::encode($response);
        Yii::app()->end();
    }

    public function actionGoodReadAll()
    {
        $user_id = Yii::app()->request->getPost('user_id');
        $folderObj = new UserFolder();
        if (!$user_id)
        {
            $response['status']['code'] = 400;
            $response['status']['msg'] = "Bad Request";
        }
        else
        {
            $response['data']['post'] = $folderObj->getPost($user_id);
            $response['status']['code'] = 200;
            $response['status']['msg'] = "DATA_FOUND";
        }
        if (isset($response['data']['post']) && count($response['data']['post']) > 0)
        {

            $post_data = array();
            $j = 0;
            foreach ($response['data']['post'] as $value)
            {
                $i = 0;
                foreach ($value['post'] as $postvalue)
                {

                    $response['data']['post'][$j]['post'][$i] = $this->getSingleNewsFromCache($postvalue['id']);
                    $response['data']['post'][$j]['post'][$i]['can_wow'] = 1;
                    $response['data']['post'][$j]['post'][$i]['can_share'] = $this->can_share($postvalue['id'], $user_id);

                    $i++;
                }
                $j++;
            }
        }
        echo CJSON::encode($response);
        Yii::app()->end();
    }

    public function actionGoodRead()
    {
        $user_id = Yii::app()->request->getPost('user_id');
        $post_id = Yii::app()->request->getPost('post_id');
        $folder_id = Yii::app()->request->getPost('folder_id');

        $folderObj = new UserFolder();
        $goodreadObj = new UserGoodRead();




        if (!$folder_id && Yii::app()->request->getPost('folder_name'))
        {
            if (!$folder = $folderObj->getFolder(Yii::app()->request->getPost('folder_name'), $user_id))
            {
                if (!$folderObj->getFolder("unread", $user_id))
                {
                    $folderObj->title = "unread";
                    $folderObj->user_id = $user_id;
                    $folderObj->visible = 0;
                    $folderObj->save();
                }
                $folderObj->title = Yii::app()->request->getPost('folder_name');
                $folderObj->user_id = $user_id;
                $folderObj->save();
                $folder_id = $folderObj->id;
            }
            else
            {
                $folder_id = $folder->id;
            }
        }
        if (!$folder_id || !$user_id || !$post_id || Yii::app()->request->getPost('folder_name') == "unread" || Yii::app()->request->getPost('folder_name') == "Unread")
        {
            $response['status']['code'] = 400;
            $response['status']['msg'] = "Bad Request";
        }

        if ($folder_id && !$goodreadObj->getGoodRead($folder_id, $post_id))
        {
            $goodreadObj->folder_id = $folder_id;
            $goodreadObj->user_id = $user_id;
            $goodreadObj->post_id = $post_id;
            $goodreadObj->save();

            $response['data']['folder_id'] = $folder_id;
            $response['status']['code'] = 200;
            $response['status']['msg'] = "DATA_FOUND";
        }
        else if ($folder_id)
        {
            $response['data']['folder_id'] = $folder_id;
            $response['status']['code'] = 200;
            $response['status']['msg'] = "DATA_FOUND";
        }


        echo CJSON::encode($response);
        Yii::app()->end();
    }

    public function actionReadlater()
    {
        $user_id = Yii::app()->request->getPost('user_id');
        $post_id = Yii::app()->request->getPost('post_id');

        if (!$user_id || !$post_id)
        {
            $response['status']['code'] = 400;
            $response['status']['msg'] = "Bad Request";
        }
        else
        {
            $folderObj = new UserFolder();
            $goodreadObj = new UserGoodRead();

            if (!$folder = $folderObj->getFolder("unread", $user_id))
            {
                $folderObj->title = "unread";
                $folderObj->user_id = $user_id;
                $folderObj->visible = 0;
                $folderObj->save();

                $folder_id = $folderObj->id;
            }
            else
            {
                $folder_id = $folder->id;
            }

            if (!$goodreadObj->getGoodRead($folder_id, $post_id))
            {
                $goodreadObj->folder_id = $folder_id;
                $goodreadObj->user_id = $user_id;
                $goodreadObj->post_id = $post_id;
                $goodreadObj->save();
            }

            $response['status']['code'] = 200;
            $response['status']['msg'] = "DATA_FOUND";
        }

        echo CJSON::encode($response);
        Yii::app()->end();
    }

    public function actionGetBylinePost()
    {
        $page_number = Yii::app()->request->getPost('page_number');
        $page_size = Yii::app()->request->getPost('page_size');
        $id = Yii::app()->request->getPost('id');
        if (empty($page_number))
        {
            $page_number = 1;
        }
        if (empty($page_size))
        {
            $page_size = 10;
        }

        $user_id = Yii::app()->request->getPost('user_id');
        if (!$user_id)
        {
            $user_type = 1;
        }
        else
        {
            $freeuserObj = new Freeusers();
            $user_info = $freeuserObj->getUserInfo($user_id);
            $user_type = $user_info['user_type'];
        }


        if ($id)
        {
            $postbylineObj = new Post();
            $post = $postbylineObj->getPosts($id, $user_type, $page_number, $page_size);

            $response['data']['total'] = $postbylineObj->getPostTotal($id, $user_type);
            $has_next = false;
            if ($response['data']['total'] > $page_number * $page_size)
            {
                $has_next = true;
            }
            $response['data']['has_next'] = $has_next;

            $response['data']['post'] = $post;
            $response['status']['code'] = 200;
            $response['status']['msg'] = "DATA_FOUND";
        }
        else
        {
            $response['status']['code'] = 400;
            $response['status']['msg'] = "Bad Request";
        }

        echo CJSON::encode($response);
        Yii::app()->end();
    }

    public function actionGetKeywordPost()
    {
        $page_number = Yii::app()->request->getPost('page_number');
        $page_size = Yii::app()->request->getPost('page_size');
        $id = Yii::app()->request->getPost('id');
        if (empty($page_number))
        {
            $page_number = 1;
        }
        if (empty($page_size))
        {
            $page_size = 10;
        }

        $user_id = Yii::app()->request->getPost('user_id');
        if (!$user_id)
        {
            $user_type = 1;
        }
        else
        {
            $freeuserObj = new Freeusers();
            $user_info = $freeuserObj->getUserInfo($user_id);
            $user_type = $user_info['user_type'];
        }

        if ($id)
        {
            $postKeywordObj = new PostKeyword();
            $post = $postKeywordObj->getPost($id, $user_type, $page_number, $page_size);

            $response['data']['total'] = $postKeywordObj->getPostTotal($id, $user_type);
            $has_next = false;
            if ($response['data']['total'] > $page_number * $page_size)
            {
                $has_next = true;
            }
            $response['data']['has_next'] = $has_next;

            $response['data']['post'] = $post;
            $response['status']['code'] = 200;
            $response['status']['msg'] = "DATA_FOUND";
        }
        else
        {
            $response['status']['code'] = 400;
            $response['status']['msg'] = "Bad Request";
        }
        echo CJSON::encode($response);
        Yii::app()->end();
    }

    public function actionGetTagPost()
    {
        $page_number = Yii::app()->request->getPost('page_number');
        $page_size = Yii::app()->request->getPost('page_size');
        $id = Yii::app()->request->getPost('id');
        if (empty($page_number))
        {
            $page_number = 1;
        }
        if (empty($page_size))
        {
            $page_size = 10;
        }

        $user_id = Yii::app()->request->getPost('user_id');
        if (!$user_id)
        {
            $user_type = 1;
        }
        else
        {
            $freeuserObj = new Freeusers();
            $user_info = $freeuserObj->getUserInfo($user_id);
            $user_type = $user_info['user_type'];
        }

        if ($id)
        {
            $postTagObj = new PostTags();
            $post = $postTagObj->getPost($id, $user_type, $page_number, $page_size);

            $response['data']['total'] = $postTagObj->getPostTotal($id, $user_type);
            $has_next = false;
            if ($response['data']['total'] > $page_number * $page_size)
            {
                $has_next = true;
            }
            $response['data']['has_next'] = $has_next;

            $response['data']['post'] = $post;
            $response['status']['code'] = 200;
            $response['status']['msg'] = "DATA_FOUND";
        }
        else
        {
            $response['status']['code'] = 400;
            $response['status']['msg'] = "Bad Request";
        }

        echo CJSON::encode($response);
        Yii::app()->end();
    }

    public function actionSearch()
    {
        $term = Yii::app()->request->getPost('term');
        if ($term)
        {
            $postObj = new Post();
            $response['data']['post'] = $postObj->getSearchPost($term);
            $response['status']['code'] = 200;
            $response['status']['msg'] = "DATA_FOUND";
        }
        else
        {
            $response['data']['post'] = array();

            $response['data']['categories'] = array();

            $response['data']['tags'] = array();

            $response['data']['keywords'] = array();

            $response['data']['authors'] = array();

            $response['status']['code'] = 200;
            $response['status']['msg'] = "DATA_FOUND";
        }

        //echo json_encode($response, JSON_HEX_QUOT | JSON_HEX_TAG);
        echo CJSON::encode($response);
        Yii::app()->end();
    }

    public function actionCreateCacheSingleNews()
    {
        $id = Yii::app()->request->getPost('id');
        $user_view_count = Yii::app()->request->getPost('user_view_count');
        $view_count = Yii::app()->request->getPost('view_count');
        $delete_cache = Yii::app()->request->getPost('delete_cache');

        $cache_name = "YII-SINGLE-POST-CACHE-" . $id;
        $cache_data = Yii::app()->cache->get($cache_name);
        if ($cache_data !== false && $delete_cache == "yes")
        {
            $postModel = new Post();
            Yii::app()->cache->delete($cache_name);
            $singlepost = $postModel->getSinglePost($id);
        }
        else if ($cache_data !== false && $delete_cache == "no" && $user_view_count && $view_count)
        {
            $postModel = new Post();
            $postobj = $postModel->findByPk($id);
            $cache_data['seen'] = $postobj->view_count;
            $cache_data['view_count'] = $postobj->view_count;
            $cache_data['user_view_count'] = $postobj->user_view_count;
            $singlepost = $cache_data;
        }
        else if ($cache_data !== false)
        {
            $postModel = new Post();
            Yii::app()->cache->delete($cache_name);
            $singlepost = $postModel->getSinglePost($id);
        }
        else
        {
            $postModel = new Post();
            $singlepost = $postModel->getSinglePost($id);
        }

        Yii::app()->cache->set($cache_name, $singlepost, 5184000);
    }

    private function getSingleNewsFromCache($id)
    {
        $cache_name = "YII-SINGLE-POST-CACHE-" . $id;
        if (!$singlepost = Yii::app()->cache->get($cache_name))
        {
            $postModel = new Post();
            $singlepost = $postModel->getSinglePost($id);
            Yii::app()->cache->set($cache_name, $singlepost, 5184000);
        }
        else
        {
            $datestring = Settings::get_post_time($singlepost['published_date']);
            $singlepost['current_date'] = date("Y-m-d H:i:s");
            $singlepost['published_date_string'] = $datestring;
        }    
        return $singlepost;
    }

    public function actionGetComments()
    {
        $post_id = Yii::app()->request->getPost('post_id');
        $user_id = Yii::app()->request->getPost('user_id');
        $page_number = Yii::app()->request->getPost('page_number');
        $page_size = Yii::app()->request->getPost('page_size');
        if (empty($page_number))
        {
            $page_number = 1;
        }
        if (empty($page_size))
        {
            $page_size = 9;
        }


        if ($post_id)
        {
            $post_value = $this->getSingleNewsFromCache($post_id);
            if ($post_value['can_comment'] == 1)
            {
                if ($post_value['show_comment_to_all'] || ($user_id && $user_id = $post_value['user_id']))
                {
                    $coments_obj_for_all = new Postcomments();
                    if (($user_id && $user_id = $post_value['user_id']))
                    {
                        $comments_total = $coments_obj_for_all->getCommentsTotal($post_id, true);
                        $comments_data = $coments_obj_for_all->getCommentsPost($post_id, $page_number, $page_size, true);
                    }
                    else
                    {
                        $comments_total = $coments_obj_for_all->getCommentsTotal($post_id);
                        $comments_data = $coments_obj_for_all->getCommentsPost($post_id, $page_number, $page_size);
                    }
                }


                $response['data']['total'] = $comments_total;
                $has_next = false;
                if ($response['data']['total'] > $page_number * $page_size)
                {
                    $has_next = true;
                }

                $response['data']['has_next'] = $has_next;

                $response['data']['comments'] = $comments_data;
                $response['status']['code'] = 200;
                $response['status']['msg'] = "Success";
            }
            else
            {
                $response['data']['total'] = 0;
                $response['data']['has_next'] = false;
                $response['data']['comments'] = array();
                $response['status']['code'] = 200;
                $response['status']['msg'] = "Success";
            }
        }
        else
        {
            $response['status']['code'] = 400;
            $response['status']['msg'] = "BAD_REQUEST";
        }
        echo CJSON::encode($response);
        Yii::app()->end();
    }

    public function actionAddComments()
    {
        $post_id = Yii::app()->request->getPost('post_id');
        $user_id = Yii::app()->request->getPost('user_id');
        //$title = Yii::app()->request->getPost('title');
        $details = Yii::app()->request->getPost('details');

        if ($post_id && $user_id && $details)
        {
            $post_value = $this->getSingleNewsFromCache($post_id);
            if ($post_value['can_comment'] == 1)
            {
                $coments_obj = new Postcomments();
                $coments_obj->post_id = $post_id;
                $coments_obj->user_id = $user_id;
                //$coments_obj->title = $title;
                $coments_obj->details = $details;
                $coments_obj->save();
                if ($post_value['show_comment_to_all'] || ($user_id = $post_value['user_id']))
                {
                    $coments_obj_for_all = new Postcomments();
                    if (($user_id && $user_id = $post_value['user_id']))
                    {
                        $comments_data = $coments_obj_for_all->getCommentsTotal($post_id, true);
                    }
                    else
                    {
                        $comments_data = $coments_obj_for_all->getCommentsTotal($post_id);
                    }
                }
                $response['data']['total'] = $comments_data;
                $response['status']['code'] = 200;
                $response['status']['msg'] = "Success";
            }
            else
            {
                $response['status']['code'] = 400;
                $response['status']['msg'] = "BAD_REQUEST";
            }
        }
        else
        {
            $response['status']['code'] = 400;
            $response['status']['msg'] = "BAD_REQUEST";
        }
        echo CJSON::encode($response);
        Yii::app()->end();
    }

    public function actionGetSingleNews()
    {
        $id = Yii::app()->request->getPost('id');
        $user_id = Yii::app()->request->getPost('user_id');
        if ($id)
        {
            //update view count
            $postModel = new Post();
            $postobj = $postModel->findByPk($id);
            $postobj->user_view_count = $postobj->user_view_count + Settings::$count_update_by;
            $postobj->view_count = $postobj->view_count + Settings::$count_update_by;
            $postobj->save();

            //CREATE CACHE FOR SINGLE NEWS
            $cache_name = "YII-SINGLE-POST-CACHE-" . $id;

            $cache_data = Yii::app()->cache->get($cache_name);
            if ($cache_data !== false)
            {

                $cache_data['seen'] = $postobj->view_count;
                $cache_data['view_count'] = $postobj->view_count;
                $cache_data['user_view_count'] = $postobj->user_view_count;
                $singlepost = $cache_data;
            }
            else
            {
                $singlepost = $postModel->getSinglePost($id);
            }



            Yii::app()->cache->set($cache_name, $singlepost, 5184000);

            $comments_data = "0";
            if ($singlepost['can_comment'] == 1)
            {
                if ($singlepost['show_comment_to_all'] || ($user_id && $user_id = $singlepost['user_id']))
                {
                    $coments_obj = new Postcomments();
                    if (($user_id && $user_id = $singlepost['user_id']))
                    {
                        $comments_data = $coments_obj->getCommentsTotal($id, true);
                    }
                    else
                    {
                        $comments_data = $coments_obj->getCommentsTotal($id);
                    }
                }
            }
            //CREATE CACHE FOR SINGLE NEWS


            $singlepost['content'] = $singlepost['mobile_content'];
            $singlepost['post_type'] = $singlepost['post_type_mobile'];

            unset($singlepost['mobile_content']);
            unset($singlepost['post_type_mobile']);



            $user_id = Yii::app()->request->getPost('user_id');
            if (!$user_id)
            {
                $user_type = 1;
            }
            else
            {
                $freeuserObj = new Freeusers();
                $user_info = $freeuserObj->getUserInfo($user_id);
                $user_type = $user_info['user_type'];
            }
            $main_id = Yii::app()->request->getPost('main_id');

            $good_read = "";

            if ($user_id)
            {
                $goodreadObj = new UserGoodRead();
                $goodreadObj->removeGoodRead($id, $user_id);

                $all_good_read_folder = $goodreadObj->getGoodReadUser($id, $user_id);

                if ($all_good_read_folder)
                {

                    $good_read = $all_good_read_folder->folder_id;
                }
            }



            $category_id = Yii::app()->request->getPost('category_id');

            if (!$category_id)
            {
                if (!$main_id)
                    $category_id = $postModel->getCategoryId($id);
                else
                    $category_id = $postModel->getCategoryId($main_id);
            }

            $postcategoryObj = new PostCategory();
            //$allpostid = $postcategoryObj->getPostAll($category_id, $user_type);








            if (!$main_id)
            {
                $next_id = $postcategoryObj->nextpreviousid($category_id, $user_type, $id, $singlepost['published_date'], $singlepost['inner_priority']);

                $previous_id = $postcategoryObj->nextpreviousid($category_id, $user_type, $id, $singlepost['published_date'], $singlepost['inner_priority'], "previous");

                if ($next_id == $previous_id)
                {
                    $next_id = $postcategoryObj->nextpreviousid($category_id, $user_type, $next_id, $singlepost['published_date'], $singlepost['inner_priority'], "next", $id);
                }
            }
            else
            {
                $next_id = $postcategoryObj->nextpreviousid($category_id, $user_type, $main_id, $singlepost['published_date'], $singlepost['inner_priority']);

                $previous_id = $postcategoryObj->nextpreviousid($category_id, $user_type, $main_id, $singlepost['published_date'], $singlepost['inner_priority'], "previous");

                if ($next_id == $previous_id)
                {
                    $next_id = $postcategoryObj->nextpreviousid($category_id, $user_type, $next_id, $singlepost['published_date'], $singlepost['inner_priority'], "next", $id);
                }
            }



            $categoryModel = new Categories();

            $can_wow = 1;
            if ($user_id && Settings::$wow_login == true)
            {
                $obj_wow = new Wow();
                $wowexists = $obj_wow->wowexists($id, $user_id);
                if ($wowexists)
                {
                    $can_wow = 0;
                }
            }
            //$subcategory = $categoryModel->getSubcategory($category_id);
            //$response['data']['subcategory'] = $subcategory;
            //$response['data']['allpostid'] = $allpostid;
            $response['data']['good_read'] = $good_read;
            $response['data']['previous_id'] = $previous_id;
            $response['data']['next_id'] = $next_id;
            $response['data']['can_wow'] = $can_wow;

            if ($main_id)
            {
                $response['data']['language'] = $postModel->getLanguage($main_id);
            }
            else
            {
                $response['data']['language'] = $postModel->getLanguage($id);
            }

            if ($main_id)
            {
                $response['data']['main_id'] = $main_id;
            }
            else
            {
                $response['data']['main_id'] = $id;
            }


            $response['data']['post'] = $singlepost;
            $response['data']['post']['comments_total'] = $comments_data;
            $post_data['data']['post']['can_share'] = $this->can_share($id, $user_id);
            //$response['data']['comments_total']  = $comments_data;
            $response['status']['code'] = 200;
            $response['status']['msg'] = "DATA_FOUND";
        }
        else
        {
            $response['status']['code'] = 400;
            $response['status']['msg'] = "BAD_REQUEST";
        }

        //echo json_encode($response, JSON_HEX_QUOT | JSON_HEX_TAG);
        echo CJSON::encode($response);
        Yii::app()->end();
    }

    private function createAllCache($cache_name)
    {
        $cache_all = "CACHE-KEYS";
        $cache_values = Yii::app()->cache->get($cache_all);
        $new_cache = array();
        if ($cache_values === false)
        {
            $new_cache[] = $cache_name;
            Yii::app()->cache->set($cache_all, $new_cache);
        }
        else if (!in_array($cache_name, $cache_values))
        {
            array_push($cache_values, $cache_name);
            Yii::app()->cache->set($cache_all, $cache_values);
        }
    }

    private function check_news_updated($update_date, $post_type = 0, $category_id = 0)
    {
        $objsortnews = new SortPostChange();

        return $objsortnews->checkNewsUpdated($update_date, $post_type, $category_id);
    }

    public function actionIndex()
    {
        $website_only = 0;
        
        $page_number = Yii::app()->request->getPost('page_number');
        $total_showed = Yii::app()->request->getPost('total_showed');
        $page_size = Yii::app()->request->getPost('page_size');
        $user_id = Yii::app()->request->getPost('user_id');
        $user_type_set = Yii::app()->request->getPost('user_type');
        $website_only = Yii::app()->request->getPost('website_only');

        $check_news_update = Yii::app()->request->getPost('check_news_update');
        $last_api_call = Yii::app()->request->getPost('last_api_call');


        $already_showed = Yii::app()->request->getPost('already_showed');
        $from_main_site = Yii::app()->request->getPost('from_main_site');
        $callded_for_cache = Yii::app()->request->getPost('callded_for_cache');
        
        $lang = array_search(Yii::app()->request->getPost('lang'), Settings::$_ar_language);
        
        $content_showed_for_caching = "top";
        if ($already_showed)
            $content_showed_for_caching = md5(str_replace(",", "-", $already_showed));

        $category_filter = "none";
        $category_not_to_show = false;
        if (!$user_id)
        {
            $user_type = 1;
        }
        else
        {
            $freeuserObj = new Freeusers();
            $user_info = $freeuserObj->getUserInfo($user_id);
            $user_type = $user_info['user_type'];

            $user_pref = FreeUserPreference::model()->findByAttributes(array('free_user_id' => $user_id));
            if ($user_pref)
            {
                if ($user_pref->category_ids)
                {
                    $category_not_to_show = $user_pref->category_ids;
                    $category_filter = md5(str_replace(",", "-", $user_pref->category_ids));
                }
            }
        }
        if (empty($page_number))
        {
            $page_number = 1;
        }
        if (empty($page_size))
        {
            $page_size = 9;
        }

        if ($user_type_set && $user_type_set > 0 && $user_type_set < 5)
        {
            $user_type = $user_type_set;
        }

        if (empty($total_showed))
        {
            $total_showed = ($page_number - 1) * $page_size;
        }
        //check news update

        if ($check_news_update && $last_api_call)
        {
            $news_update = $this->check_news_updated($last_api_call, $user_type);
            if (!$news_update)
            {
                $response['status']['code'] = 401;
                $response['status']['msg'] = "NO_NEWS_UPDATE_FOUND";
                echo CJSON::encode($response);
                Yii::app()->end();
                exit;
            }
        }


        $cache_name = "YII-RESPONSE-HOME-" . $total_showed . "-" . $page_size . "-" . $content_showed_for_caching . "-" . $category_filter . "-" . $user_type;
        
        if(!empty($lang)) {
            $cache_name .= '-'.$lang;
        }
        
        $this->createAllCache($cache_name);
        $response = Yii::app()->cache->get($cache_name);

        if ($response === false)
        {


            $homepageObj = new HomepageData();

            if ($already_showed)
            {
                $homepage_post = $homepageObj->getHomePagePost($website_only, $user_type, $page_number,
                        $page_size, false, $already_showed, $from_main_site, $category_not_to_show,
                        $lang);
            }
            else
            {
                $homepage_post = $homepageObj->getHomePagePost($website_only, $user_type, $page_number,
                        $page_size, false, false, $from_main_site, $category_not_to_show,
                        $lang);
            }

            $response['data']['total'] = $homepageObj->getPostTotal($user_type, false, $category_not_to_show, $lang);
            $has_next = false;
            if ($response['data']['total'] > $page_number * $page_size)
            {
                $has_next = true;
            }

            $response['data']['has_next'] = $has_next;



            $response['data']['post'] = $homepage_post;
            $response['status']['code'] = 200;
            $response['status']['msg'] = "DATA_FOUND";


            Yii::app()->cache->set($cache_name, $response, 86400);
        }
        if ($page_number == 1)
        {
            $pinpostobj = new Pinpost();
            $all_pinpost = $pinpostobj->getPinPost(0, $website_only);
            $new_post = array();
            $i = 0;
            
            if(!empty($response['data']['post'])) {
                foreach ($response['data']['post'] as $value)
                {
                    for ($k = $i; $k < 10; $k++)
                    {
                        if (isset($all_pinpost[$k + 1]))
                        {
                            $new_post[]['id'] = $all_pinpost[$k + 1];
                            if ($k > $i)
                            {
                                $i = $k;
                            }
                        }
                        else
                        {
                            break;
                        }
                    }
                    if (!in_array($value['id'], $all_pinpost))
                    {
                        $new_post[]['id'] = $value['id'];
                    }
                    $i++;
                }
            } else {
                foreach ($all_pinpost as $value) {
                    $new_post[]['id'] = $value;
                }
            }
            
            $response['data']['post'] = $new_post;
        }

        if (isset($response['data']['post']) && count($response['data']['post']) > 0)
        {
            $wow = array();
            if ($user_id)
            {
                $obj_wow = new Wow();
                $wow = $obj_wow->userwow($user_id);
            }

            $post_data = array();
            $i = 0;
            foreach ($response['data']['post'] as $value)
            {
                $post_data[$i] = $this->getSingleNewsFromCache($value['id']);
                $post_data[$i]['can_wow'] = 1;
                $post_data[$i]['can_share'] = $this->can_share($value['id'], $user_id);
                if (in_array($value['id'], $wow) && Settings::$wow_login == true)
                {
                    $post_data[$i]['can_wow'] = 0;
                }
                $i++;
            }
            $response['data']['post'] = $post_data;
        }
        $response['data']['api_call_time'] = date("Y-m-d H:i:s");
        if (!$callded_for_cache)
            echo CJSON::encode($response);
        Yii::app()->end();
    }

    function actioncan_share_from_web($id = "", $user_id = "")
    {
        $user_id = Yii::app()->request->getPost('user_id');
        $id = Yii::app()->request->getPost('id');
        if (!$id || !$user_id)
        {
            echo 0;
        }
        else
        {
            $schooluser = new SchoolUser();
            $user_schools = $schooluser->userSchool($user_id);
            if (isset($user_schools[0]['school_id']))
            {
                $school_id = $user_schools[0]['school_id'];
                $objpost = new PostSchoolShare();
                $already_share = $objpost->getSchoolSharePost($school_id, $id);

                if ($already_share)
                {

                    echo 0;
                }
                else
                {
                    $objpostmain = new Post();

                    $postData = $objpostmain->findByPk($id);

                    if ($postData)
                    {
                        if ($postData->school_id)
                        {
                            echo 0;
                        }
                        else
                        {
                            echo 1;
                        }
                    }
                    else
                    {
                        echo 0;
                    }
                }
            }
            else
            {
                echo 0;
            }
        }
    }

    private function can_share($id = "", $user_id = "")
    {
        if (!$id || !$user_id)
        {
            return 0;
        }
        else
        {
            $schooluser = new SchoolUser();
            $user_schools = $schooluser->userSchool($user_id);
            if (isset($user_schools[0]['school_id']))
            {
                $school_id = $user_schools[0]['school_id'];
                $objpost = new PostSchoolShare();
                $already_share = $objpost->getSchoolSharePost($school_id, $id);

                if ($already_share)
                {

                    return 0;
                }
                else
                {
                    $objpostmain = new Post();

                    $postData = $objpostmain->findByPk($id);

                    if ($postData)
                    {
                        if ($postData->school_id)
                        {
                            return 0;
                        }
                        else
                        {
                            return 1;
                        }
                    }
                    else
                    {
                        return 0;
                    }
                }
            }
            else
            {
                return 0;
            }
        }
    }

    public function actionGetSchoolTeacherBylinePost()
    {
        $page_number = Yii::app()->request->getPost('page_number');
        $page_size = Yii::app()->request->getPost('page_size');
        $id = Yii::app()->request->getPost('id');
        $target = Yii::app()->request->getPost('target');
        $user_id = Yii::app()->request->getPost('user_id');


        if ($target && $id)
        {
            if (empty($page_number))
            {
                $page_number = 1;
            }
            if (empty($page_size))
            {
                $page_size = 10;
            }

            $user_id = Yii::app()->request->getPost('user_id');
            if (!$user_id)
            {
                $user_type = 1;
            }
            else
            {
                $freeuserObj = new Freeusers();
                $user_info = $freeuserObj->getUserInfo($user_id);
                $user_type = $user_info['user_type'];
            }



            $postObj = new Post();
            $post = $postObj->getPosts($id, $user_type, $target, $page_number, $page_size);

            $response['data']['total'] = $postObj->getPostTotal($id, $user_type, $target);
            $has_next = false;
            if ($response['data']['total'] > $page_number * $page_size)
            {
                $has_next = true;
            }
            $response['data']['has_next'] = $has_next;
            $response['data']['post'] = $post;
            $response['status']['code'] = 200;
            $response['status']['msg'] = "DATA_FOUND";

            if (isset($response['data']['post']) && count($response['data']['post']) > 0)
            {

                $wow = array();
                if ($user_id)
                {
                    $obj_wow = new Wow();
                    $wow = $obj_wow->userwow($user_id);
                }

                $post_data = array();
                $i = 0;
                foreach ($response['data']['post'] as $value)
                {
                    $post_data[$i] = $this->getSingleNewsFromCache($value['id']);
                    $post_data[$i]['can_wow'] = 1;
                    $post_data[$i]['can_share'] = 0;
                    $shared_user_name = "";
                    $shared_user_image = "";

                    if (isset($value['postSchool'][0]['freeUser']->profile_image))
                    {
                        $shared_user_image = $value['postSchool'][0]['freeUser']->profile_image;
                    }
                    if (isset($value['postSchool'][0]['freeUser']->first_name) && $value['postSchool'][0]['freeUser']->first_name)
                    {
                        $shared_user_name .= $value['postSchool'][0]['freeUser']->first_name . " ";
                    }
                    if (isset($value['postSchool'][0]['freeUser']->middle_name) && $value['postSchool'][0]['freeUser']->middle_name)
                    {
                        $shared_user_name .= $value['postSchool'][0]['freeUser']->middle_name . " ";
                    }
                    if (isset($value['postSchool'][0]['freeUser']->last_name) && $value['postSchool'][0]['freeUser']->last_name)
                    {
                        $shared_user_name .= $value['postSchool'][0]['freeUser']->last_name;
                    }
                    if (!$shared_user_name)
                    {
                        if (isset($value['postSchool'][0]['freeUser']->email))
                            $shared_user_name = $value['postSchool'][0]['freeUser']->email;
                    }

                    $post_data[$i]['shared_user_name'] = $shared_user_name;
                    $post_data[$i]['shared_user_image'] = $shared_user_image;

                    if (in_array($value['id'], $wow) && Settings::$wow_login == true)
                    {
                        $post_data[$i]['can_wow'] = 0;
                    }
                    $i++;
                }
                $response['data']['post'] = $post_data;
            }
        }
        else
        {
            $response['status']['code'] = 400;
            $response['status']['msg'] = "Bad Request";
        }
        echo CJSON::encode($response);
        Yii::app()->end();
    }

    public function actionGetcategorypost()
    {
        $page_number = Yii::app()->request->getPost('page_number');
        $total_showed = Yii::app()->request->getPost('total_showed');
        $page_size = Yii::app()->request->getPost('page_size');
        $category_id = Yii::app()->request->getPost('category_id');
        $subcategory_id = Yii::app()->request->getPost('subcategory_id');
        $popular_sort = Yii::app()->request->getPost('popular_sort');
        $fetaured = Yii::app()->request->getPost('fetaured');
        $game_type = Yii::app()->request->getPost('game_type');
        $user_type_set = Yii::app()->request->getPost('user_type');
        $callded_for_cache = Yii::app()->request->getPost('callded_for_cache');

        $last_api_call = Yii::app()->request->getPost('last_api_call');

        $check_news_update = Yii::app()->request->getPost('check_news_update');
        
        $already_showed = Yii::app()->request->getPost('already_showed');
        $lang = array_search(Yii::app()->request->getPost('lang'), Settings::$_ar_language);
        
        $news_category = $category_id;
        if ($subcategory_id)
        {
            $news_category = $subcategory_id;
        }

        $extra = "";
        if ($popular_sort)
        {
            $extra.= "-popular";
        }
        if ($game_type)
        {
            $extra.= "-" . $game_type;
        }
        if ($fetaured == 1)
        {
            $extra.= "-featured";
        }
        else if ($fetaured == 2)
        {
            $extra.= "-notfeatured";
        }
        if (empty($page_number))
        {
            $page_number = 1;
        }
        if (empty($page_size))
        {
            $page_size = 10;
        }

        $user_id = Yii::app()->request->getPost('user_id');
        if (!$user_id)
        {
            $user_type = 1;
        }
        else
        {
            $freeuserObj = new Freeusers();
            $user_info = $freeuserObj->getUserInfo($user_id);
            $user_type = $user_info['user_type'];
        }

        if ($user_type_set && $user_type_set > 0 && $user_type_set < 5)
        {
            $user_type = $user_type_set;
        }

        if (empty($total_showed))
        {
            $total_showed = ($page_number - 1) * $page_size;
        }

        if ($check_news_update && $last_api_call)
        {
            $news_update = $this->check_news_updated($last_api_call, 0, $news_category);
            if (!$news_update)
            {
                $response['status']['code'] = 401;
                $response['status']['msg'] = "NO_NEWS_UPDATE_FOUND";
                echo CJSON::encode($response);
                Yii::app()->end();
                exit;
            }
        }

        $cache_name = "YII-RESPONSE-CATEGORY-" . $news_category . "-" . $total_showed . "-" . $page_size . "-" . $user_type . $extra;
        
        if(!empty($lang)) {
            $cache_name .= '-'.$lang;
        }
        
        $this->createAllCache($cache_name);
        $response = Yii::app()->cache->get($cache_name);
        if ($response === false)
        {

            $postcategoryObj = new PostCategory();
            if ($already_showed)
            {
                $post = $postcategoryObj->getPost($news_category, $user_type, $total_showed,
                        $page_size, $popular_sort, $game_type, $fetaured, $already_showed, $lang);
            }
            else
            {
                $post = $postcategoryObj->getPost($news_category, $user_type, $total_showed,
                        $page_size, $popular_sort, $game_type, $fetaured, false, $lang);
            }
            
            
            if ($already_showed)
            {
                $response['data']['total'] = $postcategoryObj->getPostTotal($news_category, $user_type, $already_showed, $lang);
            }
            else
            {
                $response['data']['total'] = $postcategoryObj->getPostTotal($news_category, $user_type, FALSE, $lang);
            }
            $has_next = false;
            if ($response['data']['total'] > $page_number * $page_size)
            {
                $has_next = true;
            }

            $categoryObj = new Categories();

            $response['data']['has_next'] = $has_next;


            $response['data']['subcategory'] = $categoryObj->getSubcategory($category_id);

            $response['data']['post'] = $post;
            $response['status']['code'] = 200;
            $response['status']['msg'] = "DATA_FOUND";
            Yii::app()->cache->set($cache_name, $response, 86400);
        }
        $selected_post = array();
        $pinpostobj = new Pinpost();
        $all_pinpost = $pinpostobj->getPinPost($news_category);
        if ($page_number == 1)
        {
            $new_post = array();
            $i = 0;
            
            if(!empty($response['data']['post'])) {
                foreach ($response['data']['post'] as $value)
                {
                    for ($k = $i; $k < 10; $k++)
                    {
                        if (isset($all_pinpost[$k + 1]))
                        {
                            $new_post[]['id'] = $all_pinpost[$k + 1];
                            if ($k > $i)
                            {
                                $i = $k;
                            }
                        }
                        else
                        {
                            break;
                        }
                    }


                    if (!in_array($value['id'], $all_pinpost))
                    {
                        $new_post[]['id'] = $value['id'];
                    }
                    $i++;
                }
            } else {
                foreach ($all_pinpost as $value) {
                    $new_post[]['id'] = $value;
                }
            }
            
            $response['data']['post'] = $new_post;
        }
        else
        {
            $new_post = array();
            $i = 0;
            foreach ($response['data']['post'] as $value)
            {
                if (!in_array($value['id'], $all_pinpost))
                {
                    $new_post[]['id'] = $value['id'];
                }
                $i++;
            }
            $response['data']['post'] = $new_post;
        }

        $obj_selected = new Selectedpost();
        $selected_post = $obj_selected->getSelectedPost($news_category);
        $response['data']['selected_post'] = array();

        if ($selected_post)
        {
            $selected_post_data = array();
            $j = 0;
            foreach ($selected_post as $value)
            {
                $selected_post_data[$j] = $this->getSingleNewsFromCache($value);
                $selected_post_data[$j]['can_wow'] = 1;
                $selected_post_data[$j]['can_share'] = $this->can_share($value, $user_id);
//                if(in_array($value, $wow)  && Settings::$wow_login==true)
//                {
//                   $selected_post_data[$j]['can_wow'] = 0; 
//                } 
                $j++;
            }
            $response['data']['selected_post'] = $selected_post_data;
        }

        if (isset($response['data']['post']) && count($response['data']['post']) > 0)
        {

            $wow = array();
            if ($user_id)
            {
                $obj_wow = new Wow();
                $wow = $obj_wow->userwow($user_id);
            }

            $post_data = array();
            $i = 0;
            foreach ($response['data']['post'] as $value)
            {
                $post_data[$i] = $this->getSingleNewsFromCache($value['id']);
                $post_data[$i]['can_wow'] = 1;
                $post_data[$i]['can_share'] = $this->can_share($value['id'], $user_id);
                if (in_array($value['id'], $wow) && Settings::$wow_login == true)
                {
                    $post_data[$i]['can_wow'] = 0;
                }
                $i++;
            }
            $response['data']['post'] = $post_data;
        }

        $response['data']['api_call_time'] = date("Y-m-d H:i:s");

        if (!$callded_for_cache)
            echo CJSON::encode($response);
        Yii::app()->end();
    }

    private function encrypt($field, $salt)
    {
        return hash('sha512', $salt . $field);
    }

    public function actionGetuserinfo()
    {
        if (isset($_POST) && !empty($_POST))
        {
            $user_id = Yii::app()->request->getPost('user_id');
            if ($user_id)
            {
                $freeuserObj = new Freeusers();
                if ($freeuserObj->getUserInfo($user_id))
                { 
                    $countryObj = new Countries();
                    $response['data'] = $freeuserObj->getPaidUserInfo();
                    $response['data']['countries'] = $countryObj->getCountryies();
                    $response['data']['user'] = $freeuserObj->getUserInfo($user_id);
                    
                    $response['status']['code'] = 200;
                    $response['status']['msg'] = "DATA_FOUND";
                }
                else
                {
                    $response['status']['code'] = 400;
                    $response['status']['msg'] = "Bad Request";
                }
            }
            else
            {
                $response['status']['code'] = 400;
                $response['status']['msg'] = "Bad Request";
            }
        }
        else
        {
            $response['status']['code'] = 400;
            $response['status']['msg'] = "Bad Request";
        }
        echo CJSON::encode($response);
        Yii::app()->end();
    }

    public function actionCreate()
    {
        if (isset($_POST) && !empty($_POST))
        {
            $countryObj = new Countries();
            $email = Yii::app()->request->getPost('email');
            $password = Yii::app()->request->getPost('password');
            $user_id = Yii::app()->request->getPost('user_id');
            $fb_profile_id = Yii::app()->request->getPost('fb_profile_id');
            $gl_profile_id = Yii::app()->request->getPost('gl_profile_id');
            $first_name = Yii::app()->request->getPost('first_name');

            $response = array();

            $freeuserObj = new Freeusers();
            if ($fb_profile_id)
            {
                
                if ($user = $freeuserObj->getFreeuserFb($fb_profile_id))
                {
                    $folderObj = new UserFolder();

                    $folderObj->createGoodReadFolder($user->id);
                    
                    Yii::app()->user->setState("free_id",$user->id);

                    $response['data'] = $freeuserObj->getPaidUserInfo($freeuserObj);
                    
                   
                    $response['data']['can_play_spellingbee'] = Settings::can_play_spelling_bee($user);
                    
                    
                    $response['data']['free_id'] = $user->id;
                    $response['data']['user'] = $freeuserObj->getUserInfo($user->id);
                    $response['status']['code'] = 200;
                    $response['data']['is_register'] = true;
                    $response['data']['is_login'] = true;
                    $response['status']['msg'] = "Successfully Saved";
                }
                else if (!Yii::app()->request->getPost('user_type'))
                {
                    $response['status']['code'] = 200;
                    $response['data']['is_register'] = false;
                    $response['data']['is_login'] = false;
                    $response['status']['msg'] = "Successfully Saved";
                }
                else
                {
                    $freeuserObj->fb_profile_id = $fb_profile_id;
                    $freeuserObj->first_name = $first_name;
                    $freeuserObj->email = $email;
                    $freeuserObj->last_name = Yii::app()->request->getPost('last_name');
                    $freeuserObj->middle_name = Yii::app()->request->getPost('middle_name');
                    $freeuserObj->gender = Yii::app()->request->getPost('gender');
                    $freeuserObj->nick_name = Yii::app()->request->getPost('nick_name');
                    $freeuserObj->user_type = Yii::app()->request->getPost('user_type');
                    $freeuserObj->medium = Yii::app()->request->getPost('medium');
                    $freeuserObj->tds_country_id = Yii::app()->request->getPost('country');
                    $freeuserObj->district = Yii::app()->request->getPost('district');
                    $freeuserObj->division = Yii::app()->request->getPost('division');
                    $freeuserObj->grade_ids = Yii::app()->request->getPost('grade_ids');
                    $freeuserObj->mobile_no = Yii::app()->request->getPost('mobile_no');
                    $freeuserObj->dob = Yii::app()->request->getPost('dob');
                    $freeuserObj->school_name = Yii::app()->request->getPost('school_name');
                    $freeuserObj->location = Yii::app()->request->getPost('location');
                    $freeuserObj->teaching_for = Yii::app()->request->getPost('teaching_for');
                    $freeuserObj->occupation = Yii::app()->request->getPost('occupation');
                    $freeuserObj->profile_image = Yii::app()->request->getPost('profile_image');
                    
                    if (Yii::app()->request->getPost('is_joined_spellbee'))
                        $freeuserObj->is_joined_spellbee = Yii::app()->request->getPost('is_joined_spellbee');
                    
                    $freeuserObj->save();

                    $folderObj = new UserFolder();

                    $folderObj->createGoodReadFolder($freeuserObj->id);

                    $this->sendRegistrationMail($freeuserObj);
                    
                    Yii::app()->user->setState("free_id",$freeuserObj->id);

                    $response['data'] = $freeuserObj->getPaidUserInfo($freeuserObj);
                    
                    
                    
                    $response['data']['can_play_spellingbee'] = Settings::can_play_spelling_bee($freeuserObj);
                   
                    
                    
                    $response['data']['free_id'] = $freeuserObj->id;
                    $response['data']['user'] = $freeuserObj->getUserInfo($freeuserObj->id);
                    $response['data']['countries'] = $countryObj->getCountryies();
                    $response['status']['code'] = 200;
                    $response['data']['is_register'] = true;
                    $response['data']['is_login'] = false;
                    $response['status']['msg'] = "Successfully Saved";
                }
            }
            else if ($gl_profile_id)
            {
                
                if ($user = $freeuserObj->getFreeuserGmail($gl_profile_id))
                {
                    $folderObj = new UserFolder();
                    Yii::app()->user->setState("free_id",$user->id);

                    $folderObj->createGoodReadFolder($user->id);
                    $response['data'] = $freeuserObj->getPaidUserInfo($freeuserObj);
                    
                    $response['data']['can_play_spellingbee'] = Settings::can_play_spelling_bee($user);
                        
                   
                    
                    
                    $response['data']['free_id'] = $user->id;
                    $response['data']['user'] = $freeuserObj->getUserInfo($user->id);
                    $response['status']['code'] = 200;
                    $response['data']['is_register'] = true;
                    $response['data']['is_login'] = true;
                    $response['status']['msg'] = "Successfully Saved";
                }
                else if (!Yii::app()->request->getPost('user_type'))
                {
                    $response['status']['code'] = 200;
                    $response['data']['is_register'] = false;
                    $response['data']['is_login'] = false;
                    $response['status']['msg'] = "Successfully Saved";
                }
                else
                {
                    $freeuserObj->gl_profile_id = $gl_profile_id;
                    $freeuserObj->first_name = $first_name;
                    $freeuserObj->email = $email;
                    $freeuserObj->last_name = Yii::app()->request->getPost('last_name');
                    $freeuserObj->middle_name = Yii::app()->request->getPost('middle_name');
                    $freeuserObj->gender = Yii::app()->request->getPost('gender');
                    $freeuserObj->nick_name = Yii::app()->request->getPost('nick_name');
                    $freeuserObj->user_type = Yii::app()->request->getPost('user_type');
                    $freeuserObj->medium = Yii::app()->request->getPost('medium');
                    $freeuserObj->tds_country_id = Yii::app()->request->getPost('country');
                    $freeuserObj->district = Yii::app()->request->getPost('district');
                    $freeuserObj->division = Yii::app()->request->getPost('division');
                    $freeuserObj->grade_ids = Yii::app()->request->getPost('grade_ids');
                    $freeuserObj->mobile_no = Yii::app()->request->getPost('mobile_no');
                    $freeuserObj->dob = Yii::app()->request->getPost('dob');
                    $freeuserObj->school_name = Yii::app()->request->getPost('school_name');
                    $freeuserObj->location = Yii::app()->request->getPost('location');
                    $freeuserObj->teaching_for = Yii::app()->request->getPost('teaching_for');
                    $freeuserObj->occupation = Yii::app()->request->getPost('occupation');
                    $freeuserObj->profile_image = Yii::app()->request->getPost('profile_image');
                    
                    if (Yii::app()->request->getPost('is_joined_spellbee'))
                        $freeuserObj->is_joined_spellbee = Yii::app()->request->getPost('is_joined_spellbee');
                    
                    $freeuserObj->save();
                    $folderObj = new UserFolder();

                    $folderObj->createGoodReadFolder($freeuserObj->id);

                    $this->sendRegistrationMail($freeuserObj);
                    
                    Yii::app()->user->setState("free_id",$freeuserObj->id);

                    $response['data'] = $freeuserObj->getPaidUserInfo($freeuserObj);
                    
                    
                    
                    $response['data']['can_play_spellingbee'] = Settings::can_play_spelling_bee($freeuserObj);
                   
                    
                    
                    $response['data']['free_id'] = $freeuserObj->id;
                    $response['data']['user'] = $freeuserObj->getUserInfo($freeuserObj->id);
                    $response['data']['countries'] = $countryObj->getCountryies();
                    $response['status']['code'] = 200;
                    $response['data']['is_register'] = true;
                    $response['data']['is_login'] = false;
                    $response['status']['msg'] = "Successfully Saved";
                }
            }
            else if ($user_id)
            {
                
                $paid_image_name = "";
                $freeuserObj = $freeuserObj->findByPk($user_id);
                $salt_paid = "";
                $pass_paid = "";
                if ($password)
                {
                    if (Yii::app()->request->getPost('previous_password') && $this->encrypt(Yii::app()->request->getPost('previous_password'), $freeuserObj->salt) == $freeuserObj->password)
                    {
                        $freeuserObj->salt = md5(uniqid(rand(), true));
                        $freeuserObj->password = $this->encrypt($password, $freeuserObj->salt);

                        $salt_paid = $freeuserObj->salt;
                        $pass_paid = $password;
                    }
                    else
                    {
                        $response['status']['code'] = 402;
                        $response['status']['msg'] = "Password Missmatch";
                        echo CJSON::encode($response);
                        Yii::app()->end();
                        eit;
                    }
                }
                
                if (isset($_FILES['profile_image']['name']) && !empty($_FILES['profile_image']['name']))
                {
                    $main_dir = Settings::$image_path . 'upload/free_user_profile_images/';
                    $uploads_dir = Settings::$main_path . 'upload/free_user_profile_images/';
                    $tmp_name = $_FILES["profile_image"]["tmp_name"];
                    $name = "file_" . $user_id . "_" . time() . "_" . str_replace(" ", "-", $_FILES["profile_image"]["name"]);

                    move_uploaded_file($tmp_name, "$uploads_dir/$name");
                    $freeuserObj->profile_image = $main_dir . $name;
                    
                    $paid_image_path = Settings::getProfileImagePaidPath($user_id);
                    if($paid_image_path)
                    {
                        copy("$uploads_dir/$name", "$paid_image_path/$name");
                        $paid_image_name = $name;
                        $photo_content_type = "image/jpeg";
                        $photo_file_size = $_FILES['profile_image']["size"];
                    }    
                }


                if (!$password)
                {
                    if ($first_name)
                        $freeuserObj->first_name = $first_name;

                    if (Yii::app()->request->getPost('last_name'))
                        $freeuserObj->last_name = Yii::app()->request->getPost('last_name');

                    if (Yii::app()->request->getPost('middle_name'))
                        $freeuserObj->middle_name = Yii::app()->request->getPost('middle_name');

                    if (Yii::app()->request->getPost('gender'))
                        $freeuserObj->gender = Yii::app()->request->getPost('gender');

                    if (Yii::app()->request->getPost('nick_name'))
                        $freeuserObj->nick_name = Yii::app()->request->getPost('nick_name');

                    if (Yii::app()->request->getPost('user_type'))
                        $freeuserObj->user_type = Yii::app()->request->getPost('user_type');

                    if (Yii::app()->request->getPost('medium'))
                        $freeuserObj->medium = Yii::app()->request->getPost('medium');

                    if (Yii::app()->request->getPost('country'))
                        $freeuserObj->tds_country_id = Yii::app()->request->getPost('country');

                    if (Yii::app()->request->getPost('district'))
                        $freeuserObj->district = Yii::app()->request->getPost('district');
                    
                    if (Yii::app()->request->getPost('division'))
                        $freeuserObj->division = Yii::app()->request->getPost('division');

                    if (Yii::app()->request->getPost('grade_ids'))
                        $freeuserObj->grade_ids = Yii::app()->request->getPost('grade_ids');

                    if (Yii::app()->request->getPost('mobile_no'))
                        $freeuserObj->mobile_no = Yii::app()->request->getPost('mobile_no');

                    if (Yii::app()->request->getPost('dob'))
                        $freeuserObj->dob = Yii::app()->request->getPost('dob');

                    if (Yii::app()->request->getPost('school_name'))
                        $freeuserObj->school_name = Yii::app()->request->getPost('school_name');

                    if (Yii::app()->request->getPost('location'))
                        $freeuserObj->location = Yii::app()->request->getPost('location');

                    if (Yii::app()->request->getPost('teaching_for'))
                        $freeuserObj->teaching_for = Yii::app()->request->getPost('teaching_for');

                    if (Yii::app()->request->getPost('occupation'))
                        $freeuserObj->occupation = Yii::app()->request->getPost('occupation');
                    
                    if (Yii::app()->request->getPost('is_joined_spellbee'))
                        $freeuserObj->is_joined_spellbee = Yii::app()->request->getPost('is_joined_spellbee');
                    
                }

                $freeuserObj->save();

                if ($freeuserObj->paid_id)
                {
                    $freeuserObjnew = $freeuserObj->findByPk($user_id);
                    $userobj = new Users();
                    $user_obj = $userobj->findByPk($freeuserObj->paid_id);
                    if ($user_obj)
                    {
                        $user_obj->first_name = $freeuserObj->first_name;
                        $user_obj->last_name = $freeuserObj->last_name;
                        if ($salt_paid && $pass_paid)
                        {
                            $user_obj->hashed_password = sha1($user_obj->salt . $pass_paid);
                            $freeuserObjnew->paid_password = $pass_paid;
                            
                        }
                        $user_obj->save();
                        if ($user_obj->student == 1)
                        {
                            $freeuserObjnew->user_type = 2;
                            $std_obj = new Students();
                            $std_data = $std_obj->getStudentByUserId($freeuserObj->paid_id);
                            if ($std_data)
                            {
                                $stdobj = $std_obj->findByPk($std_data->id);
                                $stdobj->first_name = $freeuserObj->first_name;
                                $stdobj->last_name = $freeuserObj->last_name;
                                if($paid_image_name)
                                {
                                    $stdobj->photo_file_name = $paid_image_name;
                                    $stdobj->photo_content_type = $photo_content_type;
                                    $stdobj->photo_file_size = $photo_file_size;
                                }
                                
                                $stdobj->save();
                            }
                        }
                        else if ($user_obj->employee == 1)
                        {
                            $freeuserObjnew->user_type = 3;
                            $em_obj = new Employees();
                            $em_data = $em_obj->getEmployeeByUserId($freeuserObj->paid_id);
                            if ($em_data)
                            {
                                $emobj = $em_obj->findByPk($em_data->id);
                                $emobj->first_name = $freeuserObj->first_name;
                                $emobj->last_name = $freeuserObj->last_name;
                                if($paid_image_name)
                                {
                                    $emobj->photo_file_name = $paid_image_name;
                                    $emobj->photo_content_type = $photo_content_type;
                                    $emobj->photo_file_size = $photo_file_size;
                                }
                                $emobj->save();
                            }
                        }
                        else if ($user_obj->parent == 1)
                        {
                            $freeuserObjnew->user_type = 4;
                            $gu_obj = new Guardians();
                            $gu_data = $gu_obj->getGuardianByUserId($freeuserObj->paid_id);
                            if ($gu_data)
                            {
                                $guobj = $gu_obj->findByPk($gu_data->id);
                                $guobj->first_name = $freeuserObj->first_name;
                                $guobj->last_name = $freeuserObj->last_name;
                                if (Yii::app()->request->getPost('relation'))
                                {
                                    $guobj->relation = Yii::app()->request->getPost('relation');
                                }    
                                
                                $guobj->save();
                            }
                        }
                        $freeuserObjnew->save();
                    }
                }

                Yii::app()->user->setState("free_id",$freeuserObj->id);
                $this->sendRegistrationMail($freeuserObj);
                $response['data'] = $freeuserObj->getPaidUserInfo($freeuserObj);
                
                
                
                $response['data']['can_play_spellingbee'] = Settings::can_play_spelling_bee($freeuserObj);
               
                    
                
                
                $response['data']['free_id'] = $freeuserObj->id;
                $response['data']['user'] = $freeuserObj->getUserInfo($freeuserObj->id);
                $response['data']['is_register'] = true;
                $response['data']['is_login'] = true;
                $response['status']['code'] = 200;
                $response['status']['msg'] = "Successfully Saved";
            }
            else if (Yii::app()->request->getPost('user_type') && $email && !$freeuserObj->getFreeuser($email) && $password)
            {
                
                $freeuserObj->salt = md5(uniqid(rand(), true));
                $freeuserObj->password = $this->encrypt($password, $freeuserObj->salt);
                $freeuserObj->email = Yii::app()->request->getPost('email');
                $freeuserObj->user_type = Yii::app()->request->getPost('user_type');

                $freeuserObj->save();
                $folderObj = new UserFolder();

                $folderObj->createGoodReadFolder($freeuserObj->id);
                
                Yii::app()->user->setState("free_id",$freeuserObj->id);

                $response['data'] = $freeuserObj->getPaidUserInfo($freeuserObj);
                
                $response['data']['can_play_spellingbee'] = Settings::can_play_spelling_bee($freeuserObj);
                        
               
                
                $response['data']['free_id'] = $freeuserObj->id;
                $response['data']['countries'] = $countryObj->getCountryies();
                $response['data']['user'] = $freeuserObj->getUserInfo($freeuserObj->id);
                $response['data']['is_register'] = true;
                $response['data']['is_login'] = false;
                $response['status']['code'] = 200;
                $response['status']['msg'] = "Successfully Saved";
            }
            else if ($email && $password && $freeuserObj->getFreeuser($email))
            {
                $response['status']['code'] = 404;
                $response['status']['msg'] = "Username Exists";
            }
            else
            {
                $response['status']['code'] = 400;
                $response['status']['msg'] = "Bad Request";
            }
        }
        else
        {
            $response['status']['code'] = 400;
            $response['status']['msg'] = "Bad Request";
        }
        echo CJSON::encode($response);
        Yii::app()->end();
    }

    private function get_welcome_message($full_name = '', $b_image_mail = false, $b_join_school = false)
    {

        $main_url = Settings::$image_path;
        $message = '<!DOCTYPE HTML>';

        $message .= '<head>';
        $message .= '<meta http-equiv="content-type" content="text/html">';

        if ($b_join_school)
        {
            $message .= '<title>Welcome to Champs21.com</title>';
        }
        else
        {
            $message .= '<title>Join to school</title>';
        }
        $message .= '<body>';

        if (!$b_image_mail)
        {

            if ($b_join_school)
            {

                if (!empty($full_name))
                {
                    $message .= '<p>Hi ' . $full_name . ',</p>';
                }

                $message .= '<p>Your request for joining to the school has been accepted and under processing.</p>';
                $message .= '<p> We&#39;ll inform you as soon as your request is approved.</p>';

                $message .= '<p>Thank you once again for your time and patience.</p>';
                $message .= '<p>Best Regards,</p>';
                $message .= '<p>&nbsp;</p>';
                $message .= '<p>&nbsp;</p>';
                $message .= '<p>Champs21.com</p>';
            }
            else
            {

                $message .= '<div id="header" style="width: 50%; height: 60px; margin: 0 auto; padding: 10px; color: #fff; text-align: center; background-color: #E0E0E0;font-family: Open Sans,Arial,sans-serif;">';
                $message .= '<img height="50" width="220" style="border-width:0" src="' . $main_url . 'styles/layouts/tdsfront/images/logo-new.png" alt="Champs21.com" title="Champs21.com">';
                $message .= '</div>';

                if (!empty($full_name))
                {
                    $message .= '<p>Hi ' . $full_name . ',</p>';
                }

                $message .= '<p>Thank you for joining Champs21.com and welcome to country&#39;s largest portal for Students | Teachers | Parents. I&#39;m writing this mail to Thank You and giving you a little brief on our services and features.</p>';
                $message .= '<p>
                    Champs21.com, the pioneer eLearning program of Bangladesh, has been dedicatedly and very
                    humbly working with the objectives to better prepare our students as the Champions of 21st Century. 
                    The portal offers various educational and non-educational contents on daily basis for every family 
                    that has a school going student.</p>';

                $message .= '<p>
                    <a href="' . $main_url . 'resource-centre" style="color:#000000; text-decoration: underline; font-weight: bold; ">Resource Centre</a> is the most important section where you will find education content not for students 
                    but also teaching and learning resources for teachers and parents on various subjects. All the 
                    education contents are developed by professional pool of teachers from Champs21.com. Please feel 
                    free and <a href="' . $main_url . '" style="color:#000000; text-decoration: underline; ">apply</a>, if you want to join us as a teacher. Education resources uploaded by others are 
                    carefully checked and modified before it is uploaded for our respected users. Please <a href="' . $main_url . '" style="color:#000000; text-decoration: underline; font-weight: bold; ">Candle</a> now if 
                    you want to share any resources with our education community.</p>';

                $message .= '<p>
                    Our non-education contents i.e. Tech News, Sports News, Entertainment, Health & Nutrition, 
                    Literature, Travel, Games and Videos are also very popular among our family members. Our 
                    continued efforts are always there to research and develop contents in order to make them truly 
                    useful for you.</p>';

                $message .= '<p>
                    <a href="' . $main_url . 'schools" style="color:#000000; text-decoration: underline; font-weight: bold; ">Schools</a> section offers and extensive database of schools in the country. This makes your life simpler 
                    to collect information about any particular school. If you are a teacher, create your <a href="' . $main_url . 'schools" style="color:#000000; text-decoration: underline; ">School</a> if it is not 
                    already there.</p>';

                $message .= '<p>
                    <strong>Good Read</strong> allows you to save the articles and create your own library of resources. You can save 
                    your favourite articles and read them again and again at later dates at your convenience.</p>';

                $message .= '<p>
                    Do you think you can contribute to our Students | Teachers | Parents community? <a href="' . $main_url . '" style="color:#000000; text-decoration: underline; font-weight: bold; ">Candle</a> us your 
                    article now and spread light. Other than only education, you can write and Candle on any available 
                    sections of Champs21.com.</p>';

                $message .= '<p>
                    As a registered user, you can now make <strong>preference settings</strong> and get only favourite content feeding 
                    on your home page.</p>';

                $message .= '<p>
                    You are very important to us. So is our every other student, teacher and parent of our beloved 
                    country. If you like our resources, please do <span style="text-decoration: underline; ">spread</span> this message among your near and dear ones.</p>';

                $message .= '<p>Thank you once again for your time and patience.</p>';
                $message .= '<p>Best Regards,</p>';
                $message .= '<p>&nbsp;</p>';
                $message .= '<p>&nbsp;</p>';
                $message .= '<p>Russell T. Ahmed</p>';
                $message .= '<p>Founder &amp; CEO</p>';
            }
        }
        else
        {
            $message .= '<img src="' . $main_url . '/styles/layouts/tdsfront/image/welcome-email.png">';
        }

        $message .= '</body>';
        $message .= '</head>';

        return $message;
    }

    private function sendRegistrationMail($free_user)
    {

        $name = $free_user->first_name . ' ' . $free_user->last_name;

        $mail = new YiiMailer();

        $mail->setFrom("info@champs21.com");
        $mail->setTo($free_user->email);
        $mail->setSubject('Welcome to Champs21.com');
        $mail->setBody($this->get_welcome_message($name, true));
        $mail->send();
    }

    public function actionGarbageCollector()
    {
        $keys_to_match = Yii::app()->request->getPost('keys_to_match');
        $cache_all = "CACHE-KEYS";
        $cache_keys = Yii::app()->cache->get($cache_all);
        if ($cache_keys !== false)
        {
            foreach ($cache_keys as $value)
            {
                if (strpos($value, $keys_to_match) !== false)
                {
                    Yii::app()->cache->delete($value);
                }
            }
        }
    }

    public function actionSet_preference()
    {

        $response = array();
        if (isset($_POST) && !empty($_POST))
        {

            $user_id = Yii::app()->request->getPost('user_id');
            $category_ids = Yii::app()->request->getPost('category_ids');

            if (empty($user_id) || empty($category_ids))
            {

                $response['status']['code'] = 400;
                $response['status']['msg'] = "BAD_REQUEST";

                echo CJSON::encode($response);
                Yii::app()->end();
            }

            $category_ids = trim($category_ids, ',');

            $user_pref_mod = FreeUserPreference::model()->findByAttributes(array('free_user_id' => $user_id));

            if (!empty($user_pref_mod))
            {

                $user_pref_mod->category_ids = $category_ids;

                if ($user_pref_mod->update())
                {

                    $response['data']['preferred_categories'] = $user_pref_mod->category_ids;
                    $response['status']['code'] = 200;
                    $response['status']['msg'] = "PREFERENCE_SAVED";
                }
                else
                {

                    $response['status']['code'] = 400;
                    $response['status']['msg'] = "USER_PREFERENCE_NOT_SAVED";
                }
            }
            else
            {

                $user_pref_mod = new FreeUserPreference;

                $user_pref_mod->free_user_id = $user_id;
                $user_pref_mod->category_ids = $category_ids;

                if ($user_pref_mod->insert())
                {

                    $response['data']['preferred_categories'] = $user_pref_mod->category_ids;
                    $response['status']['code'] = 200;
                    $response['status']['msg'] = "USER_PREFERENCE_SAVED";
                }
                else
                {

                    $response['status']['code'] = 400;
                    $response['status']['msg'] = "BAD_REQUEST";
                }
            }
        }
        else
        {

            $response['status']['code'] = 400;
            $response['status']['msg'] = "BAD_REQUEST";
        }

        echo CJSON::encode($response);
        Yii::app()->end();
    }

    public function actionGet_preference()
    {

        $response = array();
        if (isset($_POST) && !empty($_POST))
        {

            $user_id = Yii::app()->request->getPost('user_id');

            if (empty($user_id))
            {

                $response['status']['code'] = 400;
                $response['status']['msg'] = "BAD_REQUEST";

                echo CJSON::encode($response);
                Yii::app()->end();
            }

            $user_id = Yii::app()->request->getPost('user_id');

            $category_mod = new Categories;
            $all_categoires = $category_mod->all_cats_in_relative_manner();

            $user_pref_mod = FreeUserPreference::model()->findByAttributes(array('free_user_id' => $user_id));

            $response['data']['all_categories'] = $all_categoires;

            $response['data']['preferred_categories'] = (!empty($user_pref_mod)) ? $user_pref_mod->category_ids : '';
            $status = 404;
            if (!empty($user_pref_mod) && !empty($all_categoires))
            {
                $status = 200;
            }
            else if (!empty($all_categoires))
            {
                $status = 202;
            }

            $response['status']['code'] = $status;
            $response['status']['msg'] = (!empty($user_pref_mod)) ? "USER_PREFERENCE_FOUND" : "USER_PREFERENCE_NOT_FOUND";
        }
        else
        {

            $response['status']['code'] = 400;
            $response['status']['msg'] = "BAD_REQUEST";
        }


        echo CJSON::encode($response);
        Yii::app()->end();
    }
    
    public function actionRegenspellcache() {
        
//        $cache_name_old_userdata = 'YII-SPELLINGBEE-USERWORD';
//        $response = Settings::getSpellingBeeCache($cache_name_old_userdata);
//        echo '<pre>';
//        print_r($response);
//        
//        exit;
        
        
        
        $responsesss = array(
            6199
        );
        
        foreach ($responsesss as $userdata) {
            $cache_name_old_user_word = 'YII-SPELLINGBEE-USERWORD-' . $userdata;
            
            $cache_name_user_data = 'YII-SPELLINGBEE-USERDATA-' . $userdata;
            $cache_name_old_user_word_played = 'YII-SPELLINGBEE-USERWORD-PLAYED-' . $userdata;
            $cache_name_old_user_word_current = 'YII-SPELLINGBEE-CURRENTUSERWORD-' . $userdata;
            
            $response_user_data = Settings::getSpellingBeeCache($cache_name_user_data);
            $response_word = Settings::getSpellingBeeCache($cache_name_old_user_word);
            $response_played = Settings::getSpellingBeeCache($cache_name_old_user_word_played);
            $response_current = Settings::getSpellingBeeCache($cache_name_old_user_word_current);
            echo '<pre>';
            echo 'word:';
            print_r($response_word);
            echo '<pre>';
            echo 'unique word:';
            print_r(array_unique($response_word));
            echo '<br />';
            echo 'played:';
            print_r($response_played);
            echo '<br />';
            echo 'current:';
            print_r($response_current);
            echo '<br />';
            echo 'Uesr Data:';
            print_r($response_user_data);
        }
        exit;
        
        $response = array(
            'total_time' => 104533,
            'remaining_word' => 30,
            'current_level' => 1,
            'user_checkpoint_score' => 260,
            'user_checkpoint' => 13,
            'current_score' => 260,
            'current_time' => 3876,
            'prev_id' => 1196,
            'play_total_time' => 104533
        );        
//        $ar_cache_names = array(
//            'YII-SPELLINGBEE-CURRENTUSERWORD'
//        );
//        
//        foreach ($ar_cache_names as $cache_name_old_userdata) {
//            
//            $response = Settings::getSpellingBeeCache($cache_name_old_userdata);
//
//            foreach ($response as $key => $value) {
//                $cache_name = $cache_name_old_userdata . '-' . $key;
//
//                $data = $value;
//
//                $response = Settings::setSpellingBeeCache($cache_name, $data);
//            }
//            echo $cache_name_old_userdata . '-done<br/>';
//        
//        }
//        
//        $cache_name_old_userdata = 'YII-SPELLINGBEE-USERDATA-4994';
//        $response = Settings::setSpellingBeeCache($cache_name_old_userdata, $response);
//        
//        echo 'USERDATA-done';
//        
//        $array_words_played = array();
//        for($i = 0; $i <= 260; $i++ ) {
//            $array_words_played['words'][] = 0;
//        }
//        
//        $cache_name_userword = "YII-SPELLINGBEE-USERWORD-4994";
//        Settings::setSpellingBeeCache($cache_name_userword, $array_words_played);
//        echo '<br />USERWORD-done';
//        
//        $cache_name_userword_played = "YII-SPELLINGBEE-USERWORD-PLAYED-4994";
//        Settings::setSpellingBeeCache($cache_name_userword_played, $array_words_played);
//        echo '<br />PLAYED-done';
//        
        echo '<pre>';
        print_r($response);
        exit;
        
    }
    
    public function actionGetspellstatus() {
        
        $resulsts = Yii::app()->db->createCommand()->select('*')->from(Highscore::model()->tableName())->group('userid')->queryAll();
        $i = 0;
        $total = 0;
        
//        foreach ($resulsts as $userdata) {
//            $cache_name_old_user_word = 'YII-SPELLINGBEE-USERWORD-' . $userdata['id'];
//            $cache_name_old_user_word_played = 'YII-SPELLINGBEE-USERWORD-PLAYED-' . $userdata['id'];
//            $cache_name_old_user_word_current = 'YII-SPELLINGBEE-CURRENTUSERWORD-' . $userdata['id'];
//            $response_word = Settings::getSpellingBeeCache($cache_name_old_user_word);
//            $response_played = Settings::getSpellingBeeCache($cache_name_old_user_word_played);
//            $response_current = Settings::getSpellingBeeCache($cache_name_old_user_word_current);
//            echo '<pre>';
//            echo  $userdata['id'] . ': word:';
//            print_r($response_word);
//            echo '<br />';
//            echo $userdata['id'] . ': played:';
//            print_r($response_played);
//            echo '<br />';
//            echo $userdata['id'] . ': current:';
//            print_r($response_current);
//            echo '<br />';
//        }
//        exit;
        
      
        
         foreach ($resulsts as $rows) {
            $user_words = 'YII-SPELLINGBEE-USERDATA-'. $rows['userid'];
            $response_words = Settings::getSpellingBeeCache($user_words);
            
            if(!isset($response_words['user_checkpoint']) && $response_words['current_score']>=20)
            {
                $i++;
                echo ",";
                echo $rows['userid'];
            }
            
         }
            echo "<br/>";
            echo "<br/>";
            echo $i;
            exit;
            
            $high_score = (int)$rows['score'];
//            echo '<pre>';
//            print_r($response_words);
            
//            $cache_userwords = array();
//            $cache_userwords_played = array();
//            $is_modified = 0;
//            if(isset($response_words) && isset($response_words['words'])) {
//                $inum_cur_words = (int)count($response_words['words']);
//                foreach ($response_words['words'] as $word) {
//                    $cache_userwords[] = $word;
//                }
//                if ($inum_cur_words <= $high_score) {
//                    $diff_score = $high_score - $inum_cur_words;
//                    $diff_score += 20;
//                    for($i = 0; $i < $diff_score; $i++) {
//                        $cache_userwords[] = '0';
//                        $is_modified = 1;
//                    }
//                }
//            } else {
//                $diff_score = $high_score;
//                $diff_score += 20;
//                for($i = 0; $i < $diff_score; $i++) {
//                    $cache_userwords[] = '0';
//                    $is_modified = 1;
//                }
//            }
//            
//            $current_words = array('words' => $cache_userwords);
//            Settings::setSpellingBeeCache($user_words, $current_words);
//            Settings::setSpellingBeeCache('YII-SPELLINGBEE-USERWORD-PLAYED-' . $rows['userid'], $current_words);
//            Yii::app()->db->createCommand()->update(Highscore::model()->tableName(), 
//                                array(
//                                    'is_modified'=> $is_modified
//                                ),
//                                'userid=:uid',
//                                array(':uid'=>$rows['userid'])
//                            );
//            
//            echo $rows['userid'] . ': DONE<br />';
//            
//            $num_words = 0;
//            if(!empty($response_words) && isset($words['words'])) foreach ($response_words as $words) {
//                $num_words += count($words['words']);
//            }
//            
//            $high_score = (int)$rows['score'];
//            $diff_score = count($response_words['words']) - $high_score;
//            
//            echo 'User Id: ' . $rows['userid'] . ' Total Words: ' . count($response_words['words']) . ' High Score: ' . $high_score . ' Total Played: ' . $diff_score . '<br /><br />';
//            $i++;
//            
//            if ($diff_score < 0) {
//                $total += 20;
//            } else {
//                $total += $diff_score;
//            }
//            
//            print_r($response_words);
//        }
//        var_dump($sql);
        
//        echo '<br /><br />' . $total . '====' . $i;
//        exit;
        
    }

}
