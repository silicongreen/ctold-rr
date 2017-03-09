<?php

class Settings {

    public static $sync_off = false; 
    public static $school_card_time_zone = array(2=>"UTC",3=>"UTC",246=>"UTC");
    public static $domain_name = 'http://www.champs21.com/';
    public static $classtune_domain_name = 'http://www.classtune.com/';
    public static $image_path = 'http://www.champs21.com/';
    public static $url_array = array("http://www.champs21.com/", "http://champs21.com/", "http://stage.champs21.com");
    public static $real_path = '/home/champs21/public_html/website/';
    public static $main_path = "../../website/";
    public static $paid_image_path = "../../../../classtune/public_html/classtune/public/";
    public static $notice_attachment_path = "../../../../classtune/public_html/classtune/public/uploads/news/attachments/";
    public static $acacal_attachment_path = "../../../../classtune/public_html/classtune/public/uploads/acacals/attachments/";
    public static $upload_image_in_classtune = TRUE; 
    public static $inner_post_to_show = 15;
    public static $api_llicence_key = "fa@#25896321";
    public static $count_update_by = 3;
    public static $school_category_id = 58;
    public static $wow_login = false;
    public static $client_id = "champs21$#@!";
    public static $client_secret = "champs21!@#$";
    public static $endPoint = "plus.champs21.com";
    public static $HomeworkText = "New Homework";
    public static $AssignmentText = "New Assignment";
    public static $ClassworkText = "New Classwork";
    
    public static $education_changes_life = 59;
    public static $notification_url = "http://www.champs21.com/front/ajax/send_paid_notification";
    public static $mail_url = "http://www.champs21.com/front/ajax/send_email_to_user_api";
    public static $free_domain_string = array("free");
    public static $card_attendence_school = [2,3,246];
    
    
    public static $version_update = array(
      "version"         => 11,
      "toast_update"    => FALSE,
      "must_update"     => TRUE
    );
    
    //spelling bee config
    public static $mobile_operator = array("17", "16", "15", "11", "18");
    public static $method = array("c", "p", "s", "m", "d");
    public static $operator = array("m", "p");
    public static $encoded_left = TRUE;
    public static $encoded_right = TRUE;
    public static $encoded_method = TRUE;
    public static $encoded_operator = TRUE;
    public static $encoded_send_id = TRUE;
    public static $check_service = false;
    public static $check_id = 259;
    public static $spellingbeeConfig = false;
    public static $alwaysAgreementCheck = TRUE;
    public static $checkPointSize = 20;
    public static $dailyWord = 20;
    public static $easyWord = 50;
    public static $normalWord = 50;
    public static $hardWord = 20;
    public static $checkpointValue = 20;
    public static $school_join_approved = array(
        1 => false,
        2 => false,
        3 => false,
        4 => false
    );
    public static $post_type = 4;
    public static $allclass = 10;
    public static $school_candle_publish = array(
        1 => false,
        2 => false,
        3 => true,
        4 => false
    );
    public static $news_in_index = array(
        'show_old_news' => TRUE,
        'days_to_retrieve_news' => "-180 days"
    );
    public static $reset_password = array(
        'token_salt' => TRUE,
        'expire_time_limit' => 1800
    );
    public static $ar_weekdays = array(
        '0' => 'sunday',
        '1' => 'monday',
        '2' => 'tuesday',
        '3' => 'wednesday',
        '4' => 'thursday',
        '5' => 'friday',
        '6' => 'saturday',
    );
    public static $ar_default_folder = array(
        '0' => 'unread',
        '1' => 'articles',
        '2' => 'recipes',
        '3' => 'resources'
    );
    public static $ar_weekdays_key = array(
        'sunday' => '0',
        'monday' => '1',
        'tuesday' => '2',
        'wednesday' => '3',
        'thursday' => '4',
        'friday' => '5',
        'saturday' => '6',
    );
    public static $ar_notice_type = array(
        '1' => 'general',
        '2' => 'circular',
        '3' => 'announcement',
        '4' => 'event',
    );
    public static $ar_notice_acknowledge_status = array(
        '0' => 'Not Acknowledged',
        '1' => 'Acknowledged',
    );
    public static $ar_event_status = array(
        '0' => 'Not Going',
        '1' => 'Join In',
    );
    public static $ar_club_status = array(
        '0' => 'Applied To Join',
        '1' => 'Successfully Joined',
    );
    public static $ar_notice_acknowledge_by = array(
        '0' => 'Students',
        '1' => 'Guardians',
    );
    public static $ar_exam_category = array(
        '1' => 'Class Test',
        '2' => 'Project',
        '3' => 'Term',
    );
    public static $ar_event_origins = array(
        '0' => array(
            'name' => 'Exam',
            'condition' => "t.origin_type = 'Exam' AND t.is_holiday != '1'",
            'operator' => "AND",
        ),
        '1' => array(
            'name' => 'Events',
            'condition' => "(t.origin_id IS NULL OR t.origin_id = '') AND (t.origin_type IS NULL OR t.origin_type = '') AND (t.is_holiday != '1')",
            'operator' => "AND",
        ),
        '2' => array(
            'name' => 'Holidays',
            'condition' => "t.is_holiday = '1' ",
            'operator' => "AND",
        ),
        '3' => array(
            'name' => 'Others',
            'condition' => "t.is_holiday != '1' AND t.is_exam != '1' ",
            'operator' => "AND",
        ),
    );
    public static $assessment_config = array(
        'types' => array(
            1 => 'Assessment',
            2 => 'Quiz',
        ),
        'update_played' => array(
            'before_start' => TRUE,
            'after_finish' => FALSE,
        )
    );
    public static $change_name_cm = Array
        (
        'calender GetAttendence' => "attendence index",
        'calender Academic' => "calender index",
        'calender AddAttendenceSingle' => "add attendence",
        'calender AddAttendence' => "add attendence",
        'calender StudentAttendenceReport' => "attendence report",
        'calender GetBatchStudentAttendence' => "attendence index",
        'calender GetBatchStudentAttendence' => "attendence index",
        'calender GetStudentInfo' => "student info",
        'calender GetBatch' => "batch info",
        'dashboard GetHome' => "dashboard index",
        'event ReadReminder' => "notice index",
        'event GetuserReminder' => "notice show",
        'event Fees' => "Fees index",
        'event StudentLeaves' => "Leave index",
        'event TeacherLeaves' => "Leave index",
        'event LeaveType' => "Leave index",
        'event ReportManagerTeacher' => "Leave index",
        'event StudentLeavesParent' => "Leave index",
        'event AddLeaveStudent' => "Add Leave",
        'event AddLeaveTeacher' => "Add index",
        'event AddMeetingParent' => "Add Meeting",
        'event MeetingStatus' => "Meeting index",
        'event MeetingRequestSingle' => "Meeting index",
        'event GetSingleEvent' => "Event index",
        'event index' => "Event index",
        'homework AssessmentScore' => "Assessment Mark",
        'homework SaveAssessment' => "Save Assessment",
        'homework GetAssessment' => "Assessment index",
        'homework Assessment' => "Assessment index",
        'homework SingleTeacher' => "homework index",
        'homework SingleHomework' => "homework show",
        'homework index' => "homework index",
        'homework HomeworkStatus' => "homework index",
        'homework TeacherHomework' => "homework index",
        'homework AddHomework' => "Add Homework",
        'homework Done' => "Done Homework",
        'homework TeacherQuiz' => "Quiz index",
        'notice GetSingleNotice' => "notice index",
        'notice GetNotice' => "notice index",
        'notice index' => "notice index",
        'notice Acknowledge' => "notice Acknowledge",
        'report ProgressAll' => "Exam Report Prograss",
        'report Progress' => "Exam Report Prograss",
        'report GetExamReport' => "Exam Report",
        'report ClassTestReport' => "Class Test Report",
        'routine GetDateRoutine' => "Routine index",
        'routine Teacher' => "Routine index",
        'routine index' => "Routine index",
        'routine TeacherExam' => "Exam Routine",
        'routine Exam' => "Exam Routine",
        'routine AllExam' => "Exam Routine",
        'syllabus index' => "Syllabus index",
        'syllabus Terms' => "Syllabus index",
        'syllabus Single' => "Syllabus Show",
        'syllabus lessonplans' => "Lessonplans index",
        'syllabus lessonplanedit' => "Lessonplans Edit",
        'syllabus addlessonplan' => "Lessonplans Add",
        'syllabus Singlelessonplans' => "Lessonplans Show",
        'syllabus AddLessonPlan' => "Add LessonPlan",
        'syllabus AssignLesson' => "Assign LessonPlan",
        'task index' => "Task index",
        'task Details' => "Task index",
        'transport index' => "Transport index",
        'syllabus lessonplandelete' => "Lessonplans Remove",
        'syllabus getsubject' => "Lessonplans Index",
        'syllabus lsubjects' => "Lessonplans Index",
        'syllabus lessonplansstd' => "Lessonplans Index"
    );
    
    public static $_ar_language = array( 'en' => 'ENG', 'bn' => 'BAN',);
    
    public static function getProfileImagePaidPath($free_user_id)
    {
        $profile_image = "";
        $free = new Freeusers();
        $userinfo = $free->findByPk($free_user_id);
        
        if($userinfo && $userinfo->paid_id && $userinfo->paid_username && $userinfo->paid_password && self::$upload_image_in_classtune)
        {
            
            $puser = new Users();
            $puserinfo = $puser->getUser($userinfo->paid_username);
            if($puserinfo)
            {
                
                if($puserinfo->student)
                {
                    $stdObj = new Students();
                    $std_info = $stdObj->getStudentByUserId($puserinfo->id);
                    if($std_info)
                    {
                         $profile_image = self::getImageUploadPath($std_info,"students");    
                        
                    }
                } 
                else if($puserinfo->employee)
                {
                    
                    $empObj = new Employees();
                    $emp_info = $empObj->getEmployeeByUserId($puserinfo->id);
                  
                    if($emp_info)
                    {
                        $profile_image = self::getImageUploadPath($emp_info,"employees"); 
                    }
                }    
            }
        }    
       
        return $profile_image;
        
    }
    public static function getImageUploadPath($obj, $type = "students")
    {
        
        $image_url = self::$paid_image_path."uploads/000/000/".self::numberFormat($obj->school_id)."/".$type."/photos/".$obj->id."/original/";
        @mkdir($image_url, 0777,true);
        return $image_url;
        
    }

    public static function getBingTokens($grantType, $scopeUrl, $clientID, $clientSecret, $authUrl) {
        try {
            //Initialize the Curl Session.
            $ch = curl_init();
            //Create the request Array.
            $paramArr = array(
                'grant_type' => $grantType,
                'scope' => $scopeUrl,
                'client_id' => $clientID,
                'client_secret' => $clientSecret
            );

            //Create an Http Query.//
            $paramArr = http_build_query($paramArr);
            //Set the Curl URL.
            curl_setopt($ch, CURLOPT_URL, $authUrl);
            //Set HTTP POST Request.
            curl_setopt($ch, CURLOPT_POST, TRUE);
            //Set data to POST in HTTP "POST" Operation.
            curl_setopt($ch, CURLOPT_POSTFIELDS, $paramArr);
            //CURLOPT_RETURNTRANSFER- TRUE to return the transfer as a string of the return value of curl_exec().
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            //CURLOPT_SSL_VERIFYPEER- Set FALSE to stop cURL from verifying the peer's certificate.
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            //Execute the  cURL session.
            $strResponse = curl_exec($ch);

            //Get the Error Code returned by Curl.
            $curlErrno = curl_errno($ch);
            if ($curlErrno) {
                $curlError = curl_error($ch);
                throw new Exception($curlError);
            }
            //Close the Curl Session.
            curl_close($ch);
            //Decode the returned JSON string.
            $objResponse = json_decode($strResponse);
            if (isset($objResponse->error)) {
                throw new Exception($objResponse->error_description);
            }
            return $objResponse->access_token;
        } catch (Exception $e) {
            echo "Exception-" . $e->getMessage();
        }
    }
    
    public static function can_play_spelling_bee($freeuserObj)
    {
        $response = 0;
                        
        if($freeuserObj->mobile_no && $freeuserObj->email)
        {
            $response = 1;
        }
        return $response;
    }        

    public static function download_bing_audio($strWord) {
        $sound_status = 0;
        $clientID = '00000000480E8A3E';
        $clientSecret = 'VvjLerJ7T8v5X4g+9r4WKWOG3Ih0yNEwz2tpveoYmsw=';
        $authUrl = 'https://datamarket.accesscontrol.windows.net/v2/OAuth2-13/';
        $scopeUrl = 'http://api.microsofttranslator.com';
        $grantType = 'client_credentials';
        $accessToken = self::getBingTokens($grantType, $scopeUrl, $clientID, $clientSecret, $authUrl);
        if ($accessToken) {
            try {
                $strLang = 'en';
                $strAuthHeader = "Authorization: Bearer " . $accessToken;
                $strParams = "text=" . urlencode($strWord) . "&language=" . $strLang . "&format=audio/mp3";
                $strURL = "http://api.microsofttranslator.com/V2/Http.svc/Speak?" . $strParams;
                $strResponse = curlRequest($strURL, $strAuthHeader);
                return $strResponse;
            } catch (Exception $e) {
                return false;
            }
        } else {
            return false;
        }
        return false;
    }

    public static function downloadMP3($strWord) {

        $strDestination = self::$main_path . "upload/spellingbee";
        if (!is_dir($strDestination)) {
            @mkdir($strDestination, 0777, true);
        }
        $strMusicFile = $strDestination . "/" . strtolower(trim($strWord)) . ".mp3";
        $sound_status = 1;
        @unlink($strMusicFile);
        if (!is_file($strMusicFile)) {
            $objCURL = curl_init("http://translate.google.com/translate_tts?q=" . str_replace(" ", "+", strtolower(trim($strWord))) . "&tl=en");
            $fp = fopen($strMusicFile, "w+");

            curl_setopt($objCURL, CURLOPT_FILE, $fp);
            curl_setopt($objCURL, CURLOPT_HEADER, 0);

            curl_exec($objCURL);
            $curl_status = curl_getinfo($objCURL);
            if ($curl_status['http_code'] == 200) {
                $sound_status = 1;
                curl_close($objCURL);
                fclose($fp);
                if (filesize($strMusicFile) < 500) {
                    $sound_status = 0;
                }
            } else {
                $sound_status = 0;
                curl_close($objCURL);
                fclose($fp);
                @unlink($strMusicFile);
            }
        }
        if ($sound_status == 0) {
            @unlink($strMusicFile);
            $file_response = self::download_bing_audio($strWord);
            if ($file_response) {
                $sound_status = 1;
                file_put_contents($strMusicFile, $file_response);
            }
        }

        return $sound_status;
    }

    public static function retriveWord($left, $right, $operator, $word, $top, $bottom) {

        $left_decrepted = self::decreptmobilestyle($left);
        if ($left_decrepted == 0) {
            return false;
        }
        $word_without_left = substr($word, $left_decrepted);

        $right_decrepted = self::decreptmobilestyle($right);
        if ($right_decrepted == 0) {
            return false;
        }

        $right_position = strlen($word_without_left) - $right_decrepted;
        $word_without_lr = substr($word_without_left, 0, $right_position);




        if ((strlen($word_without_lr) % 2) != 0) {
            return false;
        }
        $array_string = str_split($word_without_lr, 2);


        $main_array = array();

        $i = 2;
        foreach ($array_string as $value) {
            if (($i % 2) == 0) {
                if ($operator == "pm") {
                    $main_array[] = $value - $left_decrepted;
                } else if ($operator == "am") {
                    $main_array[] = $value + $left_decrepted;
                }
            } else {
                if ($operator == "pm") {
                    $main_array[] = $value + $right_decrepted;
                } else if ($operator == "am") {
                    $main_array[] = $value - $right_decrepted;
                }
            }
            $i++;
        }



        $character_array = self::createCharacterArray($top, $bottom);


        $return_string = "";

        if (!$main_array) {
            return false;
        } else {
            foreach ($main_array as $value) {
                if (isset($character_array[$value])) {
                    $return_string.=$character_array[$value];
                } else {
                    return false;
                    break;
                }
            }
        }
        return $return_string;
    }

    public static function createCharacterArray($top, $bottom) {
        $character_array = array();

        $characters = 'abcdefghijklmnopqrstuvwxyz';
        $charactersLength = strlen($characters);

        for ($i = 0; $i < $charactersLength; $i++) {
            if ($i > 0) {
                $top = $top + $bottom;
            }
            $character_array[$top] = $characters[$i];
        }



        return $character_array;
    }

    public static function decreptmobilestyle($string) {
        $mobile_keyboard = array(2 => "abc", 3 => "def", 4 => "ghi", 5 => "jkl", 6 => "mno", 7 => "pqrs", 8 => "tuv", 9 => "wxyz");
        $convertvalue = 0;
        foreach ($mobile_keyboard as $key => $value) {
            if (strpos($value, $string) !== FALSE) {
                $convertvalue = $key;
                break;
            }
        }
        return $convertvalue;
    }

    public static function getSessionId() {
        if (self::$check_service) {
            return self::$check_id;
        } else {
            $id = Yii::app()->user->free_id;
            if ($id) {
                return (int) $id;
            }
        }
        return false;
    }
    public static function getProfileImagePaid($user_id)
    {
        $profile_image = "";
        $puser = new Users();
        $puserinfo = $puser->findByPk($user_id);
        if($puserinfo)
        {

            if($puserinfo->student)
            {
                $stdObj = new Students();
                $std_info = $stdObj->getStudentByUserId($puserinfo->id);
                if($std_info && $std_info->photo_file_name)
                {
                     $profile_image = self::getImageUrlEmSt($std_info,"students");    

                }
            } 
            else if($puserinfo->employee || $puserinfo->admin)
            {

                $empObj = new Employees();
                $emp_info = $empObj->getEmployeeByUserId($puserinfo->id);

                if($emp_info && $emp_info->photo_file_name)
                {

                    $profile_image = self::getImageUrlEmSt($emp_info,"employees"); 
                }
            }    
        }
        return $profile_image;   
    }
    public static function getProfileImage($free_user_id)
    {
        $profile_image = "";
        $free = new Freeusers();
        $userinfo = $free->findByPk($free_user_id);
        
        if($userinfo && $userinfo->paid_id && $userinfo->paid_username && $userinfo->paid_password)
        {
            
            $puser = new Users();
            $puserinfo = $puser->getUser($userinfo->paid_username);
            if($puserinfo)
            {
                
                if($puserinfo->student)
                {
                    $stdObj = new Students();
                    $std_info = $stdObj->getStudentByUserId($puserinfo->id);
                    if($std_info && $std_info->photo_file_name)
                    {
                         $profile_image = self::getImageUrlEmSt($std_info,"students");    
                        
                    }
                } 
                else if($puserinfo->employee || $puserinfo->admin)
                {
                    
                    $empObj = new Employees();
                    $emp_info = $empObj->getEmployeeByUserId($puserinfo->id);
                  
                    if($emp_info && $emp_info->photo_file_name)
                    {
                        
                        $profile_image = self::getImageUrlEmSt($emp_info,"employees"); 
                    }
                }    
            }
        }    
        
        if(!$profile_image && $userinfo && $userinfo->profile_image)
        {
            
           $profile_image = $userinfo->profile_image;
           
        }
        return $profile_image;
        
    }
    public static function getImageUrlEmSt($obj, $type = "students")
    {
        $profile_image = "";
        $sd = new SchoolDomains();
        $domains = $sd->getSchoolDomainBySchoolId($obj->school_id);
        if($domains)
        {
            $image_url = "http://".$domains->domain."/uploads/000/000/".self::numberFormat($obj->school_id)."/".$type."/photos/".$obj->id."/original/".$obj->photo_file_name;
//            list($width, $height, $type, $attr) = getimagesize($image_url);
//            if(isset($width) && $width)
//            {
                $profile_image = $image_url;
//            }
//            else
//            {
//                $profile_image = "http://".$domains->domain."/uploads/000/000/".self::numberFormat($obj->school_id)."/".$type."/photos/".self::numberFormat($obj->id)."/original/".$obj->photo_file_name;
//            }    
        }
        return $profile_image;
    }        

    public static function numberFormat($i)
    {
        $formated_i = "";
        if($i<10)
        {
            $formated_i = "00".$i;
        } 
        else if($i<100)
        {
            $formated_i = "0".$i;
        }  
        else
        {
            $formated_i = $i;
        }   
        return $formated_i;
    }        
    

    public static function setSpellTvCache($cache_name, $response) {
        $cachefile = new CFileCache();
        $cachefile->cachePath = "protected/runtime/cache/spelltv";
        if (!is_dir($cachefile->cachePath)) {
            mkdir($cachefile->cachePath, 0777);
        }
        $cachefile->set($cache_name, $response, 31536000);
    }

    public static function getSpellTvCache($cache_name) {
        $cachefile = new CFileCache();
        $cachefile->cachePath = "protected/runtime/cache/spelltv";
        if (!is_dir($cachefile->cachePath)) {
            mkdir($cachefile->cachePath, 0777);
        }
        $response = $cachefile->get($cache_name);
        return $response;
    }

    public static function setSpellingBeeCache($cache_name, $response) {
        $cachefile = new CFileCache();
        $cachefile->cachePath = "protected/runtime/cache/spellingbee";
        if (!is_dir($cachefile->cachePath)) {
            mkdir($cachefile->cachePath, 0777);
        }
        $cachefile->set($cache_name, $response, 31536000);
    }

    public static function getSpellingBeeCache($cache_name) {
        $cachefile = new CFileCache();
        $cachefile->cachePath = "protected/runtime/cache/spellingbee";
        if (!is_dir($cachefile->cachePath)) {
            mkdir($cachefile->cachePath, 0777);
        }
        $response = $cachefile->get($cache_name);
        return $response;
    }

    public static function clearCurrentWord($iUserId) {
        $cache_name_word = "YII-SPELLINGBEE-CURRENTUSERWORD-" . $iUserId;
        $responseword = array();
        Settings::setSpellingBeeCache($cache_name_word, $responseword);
    }

    public static function createUserToken($user_id) {
        $leftstring = rand(1000, 1000000);
        $rightstring = rand(100, 100000);



        $leftvalue = strlen($leftstring);
        $rightvalue = strlen($rightstring);




        $method_main = self::$method[array_rand(self::$method)];



        $operator_main = self::$operator[array_rand(self::$operator)];

        $encoded_method = self::createMethodEncoded($method_main);




        $encripted_user_id = self::createEncriptedUserID($encoded_method, $operator_main, $user_id, $leftvalue, $rightvalue);

        $user_id_created = $leftstring . $encripted_user_id . $rightstring;



        $return1 = $return = array("left" => $leftvalue, "right" => $rightvalue, "method" => $encoded_method, "operator" => $operator_main, "user_id_token" => $user_id_created);

        $cache_name = "USER_TOKEN_CACHE";
        $response[$user_id][] = $user_id_created;
        self::setSpellingBeeCache($cache_name, $response);

        if (self::$encoded_right) {
            $return['right'] = base64_encode($return['right']);
        }

        if (self::$encoded_left) {
            $return['left'] = base64_encode($return['left']);
        }

        if (self::$encoded_send_id) {
            $return['user_id_token'] = base64_encode($return['user_id_token']);
        }

        if (self::$encoded_method) {
            $return['method'] = base64_encode($return['method']);
        }

        if (self::$encoded_operator) {
            $return['operator'] = base64_encode($return['operator']);
        }

        return (object) $return;
    }

    public static function createEncriptedUserID($method, $operator, $send_id_without_lr, $left, $right) {
        $send_id_decrepted = 0;
        if (strpos($method, self::$method[0]) !== FALSE) {
            $concated_value = $left . "" . $right;
            $value = (int) $concated_value;
            if ($operator == self::$operator[0]) {
                $send_id_decrepted = $send_id_without_lr + $value;
            } else if ($operator == self::$operator[1]) {
                $send_id_decrepted = $send_id_without_lr - $value;
            }
        } else if (strpos($method, self::$method[1]) !== FALSE) {
            $value = $left + $right;
            if ($operator == self::$operator[0]) {
                $send_id_decrepted = $send_id_without_lr + $value;
            } else if ($operator == self::$operator[1]) {
                $send_id_decrepted = $send_id_without_lr - $value;
            }
        } else if (strpos($method, self::$method[2]) !== FALSE) {

            $value = $left - $right;


            if ($operator == self::$operator[0]) {
                $send_id_decrepted = $send_id_without_lr + $value;
            } else if ($operator == self::$operator[1]) {
                $send_id_decrepted = $send_id_without_lr - $value;
            }
        } else if (strpos($method, self::$method[3]) !== FALSE) {

            $value = $left * $right;

            if ($operator == self::$operator[0]) {
                $send_id_decrepted = $send_id_without_lr + $value;
            } else if ($operator == self::$operator[1]) {
                $send_id_decrepted = $send_id_without_lr - $value;
            }
        } else if (strpos($method, self::$method[4]) !== FALSE) {

            $value = ceil($left / $right);

            if ($operator == self::$operator[0]) {
                $send_id_decrepted = $send_id_without_lr + $value;
            } else if ($operator == self::$operator[1]) {
                $send_id_decrepted = $send_id_without_lr - $value;
            }
        }
        return $send_id_decrepted;
    }

    public static function createMethodEncoded($method_main) {
        $length = rand(2, 5);
        $characters = 'abefghijklnoqrtuvwxyz';
        $charactersLength = strlen($characters);
        $encoded_method = '';
        $randomString1 = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString1 .= $characters[rand(0, $charactersLength - 1)];
        }

        $encoded_method.= $randomString1 . $method_main;


        $randomString2 = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString2 .= $characters[rand(0, $charactersLength - 1)];
        }


        $encoded_method.=$randomString2;

        return $encoded_method;
    }

    public static function authorizeUserCheckSpellTv($left, $right, $method, $operator, $send_id, $session_id) {

        if (self::$encoded_send_id) {
            $send_id = base64_decode($send_id);
        }

        $cache_name = "USER_TOKEN_CACHE";
        $response = self::getSpellTvCache($cache_name);


        if ($response !== FALSE) {

            if (isset($response[$session_id])) {

                if (in_array($send_id, $response[$session_id])) {

                    return FALSE;
                }
            }
        }


        if (self::$encoded_right) {
            $right = base64_decode($right);
        }

        if (self::$encoded_left) {
            $left = base64_decode($left);
        }

        if (self::$encoded_method) {
            $method = base64_decode($method);
        }

        if (self::$encoded_operator) {
            $operator = base64_decode($operator);
        }

        $left_position = $left - 1;
        $send_id_without_left = substr($send_id, $left);



        $right_position = strlen($send_id_without_left) - $right;
        $send_id_without_lr = substr($send_id_without_left, 0, $right_position);



        if (strpos($method, self::$method[0]) !== FALSE) {
            $concated_value = $left . "" . $right;
            $value = (int) $concated_value;
            if ($operator == self::$operator[0]) {
                $send_id_decrepted = $send_id_without_lr - $value;
            } else if ($operator == self::$operator[1]) {
                $send_id_decrepted = $send_id_without_lr + $value;
            }
        } else if (strpos($method, self::$method[1]) !== FALSE) {
            $value = $left + $right;
            if ($operator == self::$operator[0]) {
                $send_id_decrepted = $send_id_without_lr - $value;
            } else if ($operator == self::$operator[1]) {
                $send_id_decrepted = $send_id_without_lr + $value;
            }
        } else if (strpos($method, self::$method[2]) !== FALSE) {

            $value = $left - $right;


            if ($operator == self::$operator[0]) {
                $send_id_decrepted = $send_id_without_lr - $value;
            } else if ($operator == self::$operator[1]) {
                $send_id_decrepted = $send_id_without_lr + $value;
            }
        } else if (strpos($method, self::$method[3]) !== FALSE) {

            $value = $left * $right;

            if ($operator == self::$operator[0]) {
                $send_id_decrepted = $send_id_without_lr - $value;
            } else if ($operator == self::$operator[1]) {
                $send_id_decrepted = $send_id_without_lr + $value;
            }
        } else if (strpos($method, self::$method[4]) !== FALSE) {

            $value = ceil($left / $right);

            if ($operator == self::$operator[0]) {
                $send_id_decrepted = $send_id_without_lr - $value;
            } else if ($operator == self::$operator[1]) {
                $send_id_decrepted = $send_id_without_lr + $value;
            }
        }

        
        if (isset($send_id_decrepted) && $send_id_decrepted == $session_id) {
            $response[$session_id][] = $send_id;
            self::setSpellTvCache($cache_name, $response);
            return TRUE;
        }
        return FALSE;
    }
    public static function dateOverSpellTv()
    {
        $last_date = "2016-02-10";
        $current_date = date("Y-m-d");
        if( $current_date>$last_date )
        {
            return TRUE;
        } 
        return FALSE;
    }        

    public static function authorizeUserCheck($left, $right, $method, $operator, $send_id, $session_id) {

        if (self::$encoded_send_id) {
            $send_id = base64_decode($send_id);
        }

        $cache_name = "USER_TOKEN_CACHE";
        $response = self::getSpellingBeeCache($cache_name);


        if ($response !== FALSE) {

            if (isset($response[$session_id])) {

                if (in_array($send_id, $response[$session_id])) {

                    return FALSE;
                }
            }
        }


        if (self::$encoded_right) {
            $right = base64_decode($right);
        }

        if (self::$encoded_left) {
            $left = base64_decode($left);
        }

        if (self::$encoded_method) {
            $method = base64_decode($method);
        }

        if (self::$encoded_operator) {
            $operator = base64_decode($operator);
        }

        $left_position = $left - 1;
        $send_id_without_left = substr($send_id, $left);



        $right_position = strlen($send_id_without_left) - $right;
        $send_id_without_lr = substr($send_id_without_left, 0, $right_position);



        if (strpos($method, self::$method[0]) !== FALSE) {
            $concated_value = $left . "" . $right;
            $value = (int) $concated_value;
            if ($operator == self::$operator[0]) {
                $send_id_decrepted = $send_id_without_lr - $value;
            } else if ($operator == self::$operator[1]) {
                $send_id_decrepted = $send_id_without_lr + $value;
            }
        } else if (strpos($method, self::$method[1]) !== FALSE) {
            $value = $left + $right;
            if ($operator == self::$operator[0]) {
                $send_id_decrepted = $send_id_without_lr - $value;
            } else if ($operator == self::$operator[1]) {
                $send_id_decrepted = $send_id_without_lr + $value;
            }
        } else if (strpos($method, self::$method[2]) !== FALSE) {

            $value = $left - $right;


            if ($operator == self::$operator[0]) {
                $send_id_decrepted = $send_id_without_lr - $value;
            } else if ($operator == self::$operator[1]) {
                $send_id_decrepted = $send_id_without_lr + $value;
            }
        } else if (strpos($method, self::$method[3]) !== FALSE) {

            $value = $left * $right;

            if ($operator == self::$operator[0]) {
                $send_id_decrepted = $send_id_without_lr - $value;
            } else if ($operator == self::$operator[1]) {
                $send_id_decrepted = $send_id_without_lr + $value;
            }
        } else if (strpos($method, self::$method[4]) !== FALSE) {

            $value = ceil($left / $right);

            if ($operator == self::$operator[0]) {
                $send_id_decrepted = $send_id_without_lr - $value;
            } else if ($operator == self::$operator[1]) {
                $send_id_decrepted = $send_id_without_lr + $value;
            }
        }


        if (isset($send_id_decrepted) && $send_id_decrepted == $session_id) {
            $response[$session_id][] = $send_id;
            self::setSpellingBeeCache($cache_name, $response);
            return TRUE;
        }
        return FALSE;
    }
    
    public static function sendCurlMail($data) {
        $url = Settings::$mail_url;
        $fields = array(
            'data' => $data
        );

//        $fields_string = "";
//
//        foreach ($fields as $key => $value) {
//            $fields_string .= $key . '=' . $value . '&';
//        }
//
//        rtrim($fields_string, '&');
        
        $fields_string = http_build_query($fields);
        $ch = curl_init();

        //set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, $url);

        curl_setopt($ch, CURLOPT_POST, count($fields));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Accept: application/json',
            'Content-Length: ' . strlen($fields_string)
                )
        );

        $result = curl_exec($ch);

        curl_close($ch);
    }

    public static function sendCurlNotification($user_id, $notification_id) {
        $url = Settings::$notification_url;
        $fields = array(
            'user_id' => $user_id,
            'notification_id' => $notification_id
        );

        $fields_string = "";

        foreach ($fields as $key => $value) {
            $fields_string .= $key . '=' . $value . '&';
        }

        rtrim($fields_string, '&');
        $ch = curl_init();

        //set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, $url);

        curl_setopt($ch, CURLOPT_POST, count($fields));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Accept: application/json',
            'Content-Length: ' . strlen($fields_string)
                )
        );

        $result = curl_exec($ch);

        curl_close($ch);
    }

    public static function getFedenaToken($school_code, $username, $password) {
        $endPoint = "champs21.com";

        $client_id = sha1($school_code . Settings::$client_id);
        $client_secret = sha1($school_code . Settings::$client_secret);
        $redirect_url = 'http://' . $school_code . '.' . self::$endPoint . '/authenticate';
        $url = 'http://' . $school_code . '.' . self::$endPoint . '/oauth/token';
        $data = array(
            "client_id" => $client_id,
            "client_secret" => $client_secret,
            "username" => $username,
            "password" => $password,
            "redirect_uri" => $redirect_url,
            "grant_type" => "password"
        );



        $output = Yii::app()->curl->post($url, $data);

        $joutput = json_decode(($output));

        return $joutput;
    }

    public static function getDataApi($data, $url_end, $type = "get") {
        $url = 'http://' . Yii::app()->user->school_code . '.' . self::$endPoint . "/" . $url_end;

        $headers = array('Content-type' => 'application/x-www-form-urlencoded',
            'Authorization' => 'Token token="' . Yii::app()->user->access_token_user . '"'
        );

        if ($type == "post") {
            $output = Yii::app()->curl->setHeaders($headers)->post($url, $data);
        } else {
            $output = Yii::app()->curl->setHeaders($headers)->get($url, $data);
        }

        $xml = simplexml_load_string($output);
        $json = json_encode($xml);
        $array = json_decode($json, TRUE);
        return $array;
    }

    public static function getCurrentDay($date = '') {

        $date = (!empty($date)) ? $date : \date('Y-m-d', \time());

        $day = strtolower(date('l', strtotime($date)));
        return $day;
    }

    public static function formatTime($time, $b_12_hour = TRUE) {

        return $time = ($b_12_hour) ? date('h:i a', strtotime($time)) : $time;
    }

    public static function get_diff_date($end, $out_in_array = true, $start_date = false) {
        if ($start_date == false) {
            $intervalo = date_diff(date_create($end), date_create());
        } else {
            $intervalo = date_diff(date_create($end), date_create($start_date));
        }
        $out = $intervalo->format("Years:%Y,Months:%M,Days:%d,Hours:%H,Minutes:%i,Seconds:%s");
        if (!$out_in_array)
            return $out;
        $a_out = array();
        $outs = explode(',', $out);
        foreach ($outs as $val) {
            $v = explode(':', $val);
            $a_out[$v[0]] = $v[1];
        }
        return $a_out;
    }

    public static function get_post_time($published_date, $to = 6, $check = true, $start_date = false) {
        $datediff = self::get_diff_date($published_date, true, $start_date);
        $datestring = "";
        $findvalue = false;
        if ($datediff['Years'] > 0 && $to > 0) {
            if ($datediff['Years'] > 1) {
                $datestring.= $datediff['Years'] . " Years";
            } else {
                $datestring.= $datediff['Years'] . " Year";
            }
            $findvalue = true;
        }
        if ($datediff['Months'] > 0 && ($findvalue === false || $check == false) && $to > 1) {
            if ($findvalue) {
                $datestring.= ", ";
            }
            if ($datediff['Months'] > 1) {
                $datestring.= $datediff['Months'] . " Months";
            } else {
                $datestring.= $datediff['Months'] . " Month";
            }

            $findvalue = true;
        }
        if ($datediff['Days'] > 0 && ($findvalue === false || $check == false) && $to > 2) {
            if ($findvalue) {
                $datestring.= ", ";
            }
            if ($datediff['Days'] > 1) {
                $datestring.= $datediff['Days'] . " Days";
            } else {
                $datestring.= $datediff['Days'] . " Day";
            }

            $findvalue = true;
        }
        if ($datediff['Hours'] > 0 && ($findvalue === false || $check == false) && $to > 3) {
            if ($findvalue) {
                $datestring.= ", ";
            }
            if ($datediff['Hours'] > 1) {
                $datestring.= $datediff['Hours'] . " Hours";
            } else {
                $datestring.= $datediff['Hours'] . " Hour";
            }

            $findvalue = true;
        }
        if ($datediff['Minutes'] > 0 && ($findvalue === false || $check == false) && $to > 4) {
            if ($findvalue) {
                $datestring.= ", ";
            }
            if ($datediff['Minutes'] > 1) {
                $datestring.= $datediff['Minutes'] . " Minutes";
            } else {
                $datestring.= $datediff['Minutes'] . " Minute";
            }

            $findvalue = true;
        }
        if ($datediff['Seconds'] > 0 && ($findvalue === false || $check == false) && $to > 5) {
            if ($findvalue) {
                $datestring.= ", ";
            }
            if ($datediff['Seconds'] > 1) {
                $datestring.= $datediff['Seconds'] . " Seconds";
            } else {
                $datestring.= $datediff['Seconds'] . " Second";
            }

            $findvalue = true;
        }


        return $datestring;
    }

    public static function formatDateTime($date_time, $b_12_hour = TRUE) {

        return $time = ($b_12_hour) ? date('Y-m-d h:i a', strtotime($date_time)) : $time;
    }

    public static function get_crop_image($url, $replace_url = "gallery/facebook/") {
        $image = str_replace("gallery/", $replace_url, $url);

        foreach (self::$url_array as $value) {
            $image = str_replace($value, self::$real_path, $image);
        }

        if (!file_exists($image)) {
            return $url;
        }
        return str_replace(self::$real_path, self::$image_path, $image);
    }

    public static function get_mobile_image($url, $replace_url = "gallery/mobile/") {
        $image = str_replace("gallery/", $replace_url, $url);

        foreach (self::$url_array as $value) {
            $image = str_replace($value, self::$real_path, $image);
        }

        if (!file_exists($image)) {
            return $url;
        }
        return str_replace(self::$real_path, self::$image_path, $image);
    }

    public static function sanitize($str, $char = '-') {
        // Lower case the string and remove whitespace from the beginning or end
        $str = trim(strtolower($str));

        // Remove single quotes from the string
        $str = str_replace("'", '', $str);

        $str = str_replace("?", '', $str);
        $str = str_replace("!", '', $str);

        // Every character other than a-z, 0-9 will be replaced with a single dash (-)
        //$str = mb_ereg_replace("/[^a-z0-9]+/", $char, $str);
        $str = str_replace(" ", "-", $str);

        // Remove any beginning or trailing dashes
        $str = trim($str, $char);

        return $str;
    }

    public static function get_simple_post_layout($postValue) {
        $post_type = 0;
        $edu_check = false;
        if (isset($postValue['postCategories']) && count($postValue['postCategories']) > 0) {
            foreach ($postValue['postCategories'] as $value) {
                if (self::$education_changes_life == $value['category']->id) {
                    $edu_check = true;
                    break;
                }
            }
        }


        if ($postValue->school_id > 0) {
            $post_type = 8;
        } else if ($edu_check) {
            $post_type = 9;
        } else if ($postValue->post_layout == 1 && $postValue->inside_image != "" && $postValue->inside_image != null) {
            $post_type = 2;
        } else if ($postValue->post_layout == 2 && $postValue['postGalleries'] && count($postValue['postGalleries']) > 2) {
            $post_type = 3;
        } else if ($postValue->post_layout == 3) {
            $post_type = 1;
        } else if ($postValue->short_title != "") {
            if ($postValue->sort_title_type == 2 && $postValue['postGalleries'] && count($postValue['postGalleries']) > 1) {
                $post_type = 6;
            } else if ($postValue->sort_title_type == 3) {
                $post_type = 7;
            } else if ($postValue->sort_title_type == 4 && isset($postValue['postAuthor']) && $postValue['postAuthor']->image != "") {
                $post_type = 4;
            } else if ($postValue->sort_title_type == 5) {
                $post_type = 5;
            }
        }
        return $post_type;
    }

    public static function get_post_link_url($news) {
        $link_array = array();
        if ($news->post_type == 2) {
            if ($news->lead_link != null && $news->lead_link != "") {
                $link_array['link'] = $news->lead_link;
                $link_array['use_link'] = 1;
            } else {
                $link_array['link'] = "";
                $link_array['use_link'] = 1;
            }
        } else {
            if ($news->lead_link != null && $news->lead_link != "") {
                $link_array['link'] = $news->lead_link;
                $link_array['use_link'] = 1;
            } else {
                $link_array['link'] = self::$image_path . self::sanitize($news->headline) . "-" . $news->id;
                $link_array['use_link'] = 0;
            }
        }
        return $link_array;
    }

    public static function add_caption_and_link($postValue) {

        $all_image = array();
        if ($postValue->lead_material && strlen(trim($postValue->lead_material)) > 0) {
            $all_image[0]['ad_image'] = self::get_mobile_image(self::$image_path . $postValue->lead_material);
            $all_image[0]['ad_image_link'] = $postValue->lead_source;
            $all_image[0]['ad_image_caption'] = $postValue->lead_caption;
        } else {
            $doc = new DOMDocument();
            @$doc->loadHTML($postValue->content);
            $images = $doc->getElementsByTagName('img');
            $i = 0;
            foreach ($images as $image) {
                if (strpos($image->getAttribute('src'), "relatednews.jpg") !== FALSE) {
                    continue;
                } else if (strpos($image->getAttribute('class'), "no_slider") !== FALSE) {
                    continue;
                } else {
                    $all_image[$i]['ad_image'] = self::get_mobile_image($image->getAttribute('src'));
                    $all_image[$i]['ad_image_link'] = $image->getAttribute('longdesc');
                    $all_image[$i]['ad_image_caption'] = $image->getAttribute('title');
                    $i++;
                }
            }
        }
        return $all_image;
    }

    public static function get_embeded_url($content) {
        preg_match('/src="([^"]+)"/', $content, $match);
        $url = $match[1];
        return $url;
    }

    public static function get_solution($content) {
        $value = preg_match_all('/<div(.*?)id=\"solution\-text\"(.*?)>(.*?)<\/div>/s', $content, $estimates);
        $soultion = "";
        if ($value) {
            $soultion = str_replace("<hr />", "", $estimates[count($estimates) - 1][0]);
            $soultion = str_replace("<hr/>", "", $soultion);
            $soultion = str_replace("\n", "", $soultion);
        }

        return $soultion;
    }

    public static function remove_solution_button($content) {
        $value = preg_replace('/<p(.*?)id=\"solution-\-p\"(.*?)>(.*?)<\/p>/s', "", $content);
        return $value;
    }

    public static function getSingleNewsFromCache($id) {
        $cache_name = "YII-SINGLE-POST-CACHE-" . $id;
        if (!$singlepost = Yii::app()->cache->get($cache_name)) {
            $postModel = new Post();
            $singlepost = $postModel->getSinglePost($id);
            Yii::app()->cache->set($cache_name, $singlepost, 5184000);
        } else {
            $datestring = self::get_post_time($singlepost['published_date']);
            $singlepost['current_date'] = date("Y-m-d H:i:s");
            $singlepost['published_date_string'] = $datestring;
        }
        return $singlepost;
    }

    public static function formatData($postValue) {
        $post_array = array();
        if ($postValue) {
            $post_array['title'] = $postValue->headline;

            $post_array['is_spelling_bee'] = $postValue->is_spelling_bee;
            $post_array['is_science_rocks'] = $postValue->is_science_rocks;

            $post_array['related_news_spelling_bee'] = array();

            if ($postValue->is_spelling_bee || $postValue->is_science_rocks) {

                $objrelated = new RelatedNews();
                $rnews = $objrelated->getRelatedNews($postValue->id);
                $post_data = array();
                $i = 0;
                foreach ($rnews as $value) {
                    $post_data[$i] = self::getSingleNewsFromCache($value['id']);
                    $i++;
                }

                $post_array['related_news_spelling_bee'] = $post_data;
            }



            $post_array['post_type'] = $postValue->post_type;

            $post_array['ad_target'] = 1;
            if (isset($postValue->ad_target)) {
                $post_array['ad_target'] = $postValue->ad_target;
            }

            $post_array['category_id_to_use'] = "";
            $post_array['subcategory_id_to_use'] = "";
            $post_array['school_id'] = "";
            $post_array['education_changes_life'] = 0;
            if (isset($postValue->school_id) && $postValue->school_id) {
                $post_array['school_id'] = $postValue->school_id;
            }

            if (isset($postValue->category_id) && $postValue->category_id) {
                $post_array['category_id_to_use'] = $postValue->category_id;
                if (self::$education_changes_life == $postValue->category_id) {
                    $post_array['education_changes_life'] = 1;
                }

                if (isset($postValue->subcategory_id_to_use) && $postValue->subcategory_id_to_use) {
                    $post_array['subcategory_id_to_use'] = $postValue->subcategory_id_to_use;
                }
            }

            //need to change into single news
            $post_array['post_type_mobile'] = $postValue->mobile_view_type;

            $post_array['can_comment'] = $postValue->can_comment;

            $post_array['assessment_id'] = "";

            if ($postValue->assessment_id)
                $post_array['assessment_id'] = $postValue->assessment_id;

            $post_array['force_assessment'] = $postValue->force_assessment;

            $post_array['assessment_title'] = "";

            $post_array['assessment_played'] = 0;

            if (isset($postValue['postAssessment']->title) && $postValue['postAssessment']->title) {
                $post_array['assessment_title'] = $postValue['postAssessment']->title;
                $post_array['assessment_played'] = $postValue['postAssessment']->played;
            }

            $post_array['show_comment_to_all'] = $postValue->show_comment_to_all;

            $post_array['video_file'] = "";

            if ($postValue->video_file)
                $post_array['video_file'] = Settings::$image_path . $postValue->video_file;


            $post_array['sub_head'] = $postValue->sub_head;

            $post_array['wow_count'] = $postValue->wow_count;

            $post_array['school_id'] = $postValue->school_id;

            $post_array['teacher_id'] = $postValue->teacher_id;

            $post_array['candle_type'] = $postValue->candle_type;

            $post_array['subject'] = $postValue->subject;

            $post_array['show_comment_to_all'] = $postValue->show_comment_to_all;

            $post_array['user_id'] = $postValue->user_id;

            $post_array['force_web_view_mobie'] = $postValue->force_web_view_mobie;



            $post_array['related_post_type'] = $postValue->related_post_type;

            $post_array['seen'] = $postValue->view_count;

            $post_array['title_color'] = $postValue->headline_color;

            $post_array['id'] = $postValue->id;

            $post_array['post_layout'] = $postValue->post_layout;

            $post_array['sort_title_type'] = $postValue->sort_title_type;
            $post_array['inside_image'] = "";

            if ($postValue->inside_image)
                $post_array['inside_image'] = Settings::get_mobile_image(Settings::$image_path . $postValue->inside_image);





            $post_array['author'] = "";
            $post_array['designation'] = "";
            if (isset($postValue->author_image_post)) {
                $post_array['author_image'] = $postValue->author_image_post;
            } else {
                $post_array['author_image'] = "";
            }

            if (isset($postValue['freeUser'])) {
                $auther_name = "";
                if (isset($postValue['freeUser']->profile_image)) {
                    $post_array['author_image'] = $postValue['freeUser']->profile_image;
                }
                if (isset($postValue['freeUser']->first_name) && $postValue['freeUser']->first_name) {
                    $auther_name .= $postValue['freeUser']->first_name . " ";
                }
                if (isset($postValue['freeUser']->middle_name) && $postValue['freeUser']->middle_name) {
                    $auther_name .= $postValue['freeUser']->middle_name . " ";
                }
                if (isset($postValue['freeUser']->last_name) && $postValue['freeUser']->last_name) {
                    $auther_name .= $postValue['freeUser']->last_name;
                }
                if (!$auther_name) {
                    if (isset($postValue['freeUser']->email))
                        $auther_name = $postValue['freeUser']->email;
                }
                $post_array['author'] = $auther_name;

                if (isset($postValue['freeUser']->designation) && $postValue['freeUser']->designation) {
                    $post_array['designation'] = $postValue['freeUser']->designation;
                }
            }


            if (isset($postValue['postAuthor'])) {
                $post_array['author'] = $postValue['postAuthor']->title;
                if ($postValue['postAuthor']->image)
                    $post_array['author_image'] = Settings::$image_path . $postValue['postAuthor']->image;

                if (isset($postValue['postAuthor']->designation) && $postValue['postAuthor']->designation) {
                    $post_array['designation'] = $postValue['postAuthor']->designation;
                }
            }

            $post_array['post_id'] = $postValue->id;

            $post_array['headline'] = $postValue->headline;

            $post_array['content'] = $postValue->content;

            $post_array['is_featured'] = $postValue->is_featured;
            $post_array['show_byline_image'] = $postValue->show_byline_image;
            $post_array['headline_color'] = $postValue->headline_color;


            $post_array['short_title'] = $postValue->short_title;
            $post_array['shoulder'] = $postValue->shoulder;
            $post_array['other_language'] = $postValue->other_language;

            $post_array['sub_head'] = $postValue->sub_head;
            $post_array['lead_material'] = $postValue->lead_material;

            $post_array['lead_caption'] = $postValue->lead_caption;
            $post_array['is_breaking'] = $postValue->is_breaking;
            $post_array['breaking_expire'] = $postValue->breaking_expire;
            $post_array['is_exclusive'] = $postValue->is_exclusive;
            $post_array['exclusive_expired'] = $postValue->exclusive_expired;


            $post_array['language'] = $postValue->language;
            $post_array['lead_link'] = $postValue->lead_link;
            $post_array['view_count'] = $postValue->view_count;

            $post_array['user_view_count'] = $postValue->user_view_count;
            $post_array['embedded'] = $postValue->embedded;

            $post_array['embedded_url'] = "";
            if ($postValue->embedded)
                $post_array['embedded_url'] = Settings::get_embeded_url($postValue->embedded);

            $post_array['layout_color'] = $postValue->layout_color;

            $post_array['referance_id'] = $postValue->referance_id;
            $post_array['attach'] = $postValue->attach;
            $post_array['layout'] = $postValue->layout;

            $post_array['crop_images'] = array();
            $post_array['images'] = array();
            $post_array['add_images'] = array();
            $post_array['web_images'] = array();

            $post_array['image_width'] = "";
            $post_array['image_height'] = "";

            if ($postValue['postGalleries']) {
                $j = 0;
                $k = 0;
                foreach ($postValue['postGalleries'] as $value) {
                    if (trim($value['material']->material_url) && $value->type == 2) {
                        $post_array['crop_images'][] = Settings::get_crop_image(Settings::$image_path . $value['material']->material_url);
                        $post_array['images'][] = Settings::get_mobile_image(Settings::$image_path . $value['material']->material_url);

                        $post_array['add_images'][$j]['ad_image'] = Settings::get_mobile_image(Settings::$image_path . $value['material']->material_url);
                        $post_array['add_images'][$j]['ad_image_link'] = $value->source;
                        $post_array['add_images'][$j]['ad_image_caption'] = $value->caption;
                        
                        $post_array['add_images'][$j]['ad_image_category'] = $value->category_id;
                        $post_array['add_images'][$j]['ad_image_subcategory'] = $value->subcategory_id;
                        if ($j == 1) {
                            list($image_width, $image_height, $image_type, $image_attr) = @getimagesize($post_array['images'][$j]);
                            if (isset($image_width) && isset($image_height)) {
                                $post_array['image_width'] = $image_width;
                                $post_array['image_height'] = $image_height;
                            }
                        }
                        $j++;
                    } else if (trim($value['material']->material_url) && $value->type == 1) {

                        $post_array['web_images'][$k]['image'] = Settings::$image_path . $value['material']->material_url;
                        $post_array['web_images'][$k]['source'] = $value->source;
                        $post_array['web_images'][$k]['caption'] = $value->caption;

                        $k++;
                    }
                }
            }

            if (empty($post_array['images'])) {
                if (!empty($post_array['lead_material'])) {
                    $post_array['images'][] = Settings::get_mobile_image(Settings::$image_path . $post_array['lead_material']);
                    $post_array['crop_images'][] = Settings::get_crop_image(Settings::$image_path . $post_array['lead_material']);
                }
            }

            //need to change 2
            if (isset($postValue->mobile_content) && strlen(Settings::substr_with_unicode($postValue->mobile_content, true)) > 0) {
                $post_array['mobile_content'] = Settings::remove_solution_button($postValue->mobile_content);
                $post_array['full_content'] = Settings::substr_with_unicode($postValue->mobile_content, true);
                $post_array['solution'] = Settings::get_solution($postValue->mobile_content);
            } else {
                $post_array['mobile_content'] = Settings::remove_solution_button($postValue->content);
                $post_array['full_content'] = Settings::substr_with_unicode($postValue->content, true);
                $post_array['solution'] = Settings::get_solution($postValue->content);
            }

            $post_array['summary'] = "";


            if ($postValue->summary) {
                $post_array['has_summary'] = 1;
                $post_array['summary'] = $postValue->summary;
            } else {
                $post_array['has_summary'] = 0;
                $post_array['summary'] = Settings::substr_with_unicode($postValue->content);
            }

            $post_array['share_link'] = Settings::get_post_link_url($postValue);
            $post_array['mobile_image'] = "";
            if ($postValue->mobile_image)
                $post_array['mobile_image'] = Settings::$image_path . $postValue->mobile_image;

            $datestring = Settings::get_post_time($postValue->published_date);

            $post_array['published_date'] = $postValue->published_date;
            //$post_array['attachment'] = $postValue->attach_file;
            $post_array['current_date'] = date("Y-m-d H:i:s");
            $post_array['published_date_string'] = $datestring;

            $post_array['category_menu_icon'] = "";
            $post_array['category_icon'] = "http://www.champs21.com/upload/gallery/image/category/news_3.png";


            if (isset($postValue['postCategories'][0]['category']->menu_icon) && $postValue['postCategories'][0]['category']->menu_icon)
                $post_array['category_menu_icon'] = Settings::$image_path . $postValue['postCategories'][0]['category']->menu_icon;

            if (isset($postValue['postCategories'][0]['category']->icon) && $postValue['postCategories'][0]['category']->icon)
                $post_array['category_icon'] = Settings::$image_path . $postValue['postCategories'][0]['category']->icon;

            $post_array['category_name'] = "News and Articles";
            $post_array['category_id'] = 38;
            $post_array['inner_priority'] = 1;



            if (isset($postValue['postCategories'][0]['category']->name)) {
                if (isset($postValue['postCategories'][0]['category']->display_name) && $postValue['postCategories'][0]['category']->display_name != "") {
                    $post_array['category_name'] = $postValue['postCategories'][0]['category']->display_name;
                } else {
                    $post_array['category_name'] = $postValue['postCategories'][0]['category']->name;
                }
            }

            if (isset($postValue['postCategories'][0]['category']->id))
                $post_array['category_id'] = $postValue['postCategories'][0]['category']->id;

            if (isset($postValue['postCategories'][0]['category']->inner_priority))
                $post_array['inner_priority'] = $postValue['postCategories'][0]->inner_priority;

            $post_array['second_category_name'] = "";
            $post_array['second_category_id'] = 0;

            if (isset($postValue['postCategories'][1]['category']->id))
                $post_array['second_category_id'] = $postValue['postCategories'][1]['category']->id;

            if (isset($postValue['postCategories'][1]['category']->name)) {
                if (isset($postValue['postCategories'][1]['category']->display_name) && $postValue['postCategories'][1]['category']->display_name != "") {
                    $post_array['second_category_name'] = $postValue['postCategories'][1]['category']->display_name;
                } else {
                    $post_array['second_category_name'] = $postValue['postCategories'][1]['category']->name;
                }
                $post_array['second_category_id'] = $postValue['postCategories'][1]['category']->id;
            }
            $post_array['tags'] = array();

            $post_array['normal_post_type'] = Settings::get_simple_post_layout($postValue);

            $j = 0;
            if ($postValue['postTags'])
                foreach ($postValue['postTags'] as $value) {
                    $post_array['tags'][$j]['name'] = $value['tag']->tags_name;
                    $post_array['tags'][$j]['id'] = $value['tag']->id;
                    $j++;
                }

            $post_array['attach'] = "";
            $post_array['attach_content'] = "";
            $post_array['attach_download_link'] = "";
            $post_array['attachment'] = array();

            if ($postValue['postAttachment'] && count($postValue['postAttachment']) > 0) {
                $ai = 0;
                foreach ($postValue['postAttachment'] as $avalue) {
                    $post_array['attachment'][$ai]['attach'] = Settings::$image_path . $avalue->file_name;

                    $post_array['attachment'][$ai]['content'] = '<iframe frameborder="0" style="width: 100%; height: 500px;" src="http://docs.google.com/gview?url=' . Settings::$image_path . $avalue->file_name . '&embedded=true"></iframe>';
                    $post_array['attachment'][$ai]['download_link'] = 'http://www.champs21.com/download?f_path=' . $avalue->file_name;

                    $post_array['attachment'][$ai]['caption'] = $avalue->caption;
                    $post_array['attachment'][$ai]['show'] = $avalue->show;
                    $ai++;
                }
            }

            return $post_array;
        } else {
            return false;
        }
    }
    public static function content_single_images($content)
    {
        $doc = new DOMDocument();
        @$doc->loadHTML($content);
        $images = $doc->getElementsByTagName('img');
        $content_image = "";
        foreach ($images as $image) 
        {
            $content_image = $image->getAttribute('src');
            break;
        }
        return $content_image;
    }        

    public static function content_images($content, $first_image = true, $lead_material = false) {
        $doc = new DOMDocument();
        @$doc->loadHTML($content);
        $images = $doc->getElementsByTagName('img');
        $all_image = array();

        if ($lead_material) {
            $all_image[] = self::get_mobile_image(self::$image_path . $lead_material);
        }
        $i = 1;
        foreach ($images as $image) {
            if (strpos($image->getAttribute('src'), "relatednews.jpg") !== FALSE) {
                continue;
            } else if (strpos($image->getAttribute('class'), "no_slider") !== FALSE) {
                continue;
            } else if ($i == 1 && $first_image === false) {
                continue;
            } else {
                $all_image[] = self::get_mobile_image($image->getAttribute('src'));
            }
            $i++;
        }
        return $all_image;
    }

    public static function substr_with_unicode($string, $full_length = false, $length = 400) {
        $string = preg_replace('/<div (.*?)>Source:(.*?)<\/div>/', '', $string);
        $string = preg_replace('/<div class="img_caption" (.*?)>(.*?)<\/div>/', '', $string);

        $string = str_replace("\n", '', trim($string));
        $string = str_replace("&nbsp;", '', $string);
        $string = str_replace("<p></p>", '', $string);

        if ($full_length === false) {

            $main_string = mb_substr(strip_tags(html_entity_decode($string, ENT_QUOTES, 'UTF-8')), 0, $length, 'UTF-8');
            return trim($main_string);
        } else {
            $main_string = strip_tags(html_entity_decode($string, ENT_QUOTES, 'UTF-8'));
            $main_string = mb_substr($main_string, 0, mb_strlen($main_string, 'UTF-8'), 'UTF-8');
            return trim($main_string);
        }
    }

    public static function getProfileModel() {

        $mod_name = 'Employees';

        if (Yii::app()->user->isStudent) {
            $mod_name = 'Students';
        }

        if (Yii::app()->user->isParent) {
            $mod_name = 'Guardians';
        }

        return $mod_name;
    }

    public static function extractIds($array_or_obj, $key = 'id') {

        $ar_ids = array();

        foreach ($array_or_obj as $value) {
            if (is_object($array_or_obj)) {
                $ar_ids[] = $value->$key;
            }

            if (is_array($array_or_obj)) {
                $ar_ids[] = $value[$key];
            }
        }

        return $ar_ids;
    }
    
    public static function getUniqueId($id = 0, $max_length = 12) {

        $id_len = strlen($id);
        $max_len = $max_length - $id_len;

        $unique_id = (string) microtime(true);
        $unique_id = str_replace('.', '', $unique_id);

        $n_unique_id = substr($unique_id, $id_len);
        $n_unique_id_len = strlen($n_unique_id);

        $nn_unique_id = '';
        $nn_unique_id = substr($n_unique_id, -$max_len);

        $ref_num = $id . $nn_unique_id;
        if (strlen($ref_num) < $max_length) {
            for ($i = strlen($ref_num); $i < $max_length; $i++) {
                $num = rand($i, $i * 5);
                $ref_num .= $num;
            }
        }

        return $ref_num;
    }

}
