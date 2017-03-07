<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class Sms {
    public static $sms_attendence_school = [2,296,319];
    public static $sms_all_guardian = [319];
    public static $sms_subject_attendence_school = [2,319];
    //296
    
    public static $sms_school_host = array(2=>"host_ssd", 296=>"host_ssd", 319=>"host_ssd");
    public static $sms_school_param = array(2=>"param_ssd", 296=>"param_ssd", 319=>"param_ssd");
    public static $sms_school_hosts_return = array(2=>"host_return_ssd", 296=>"host_return_ssd", 319=>"host_return_ssd");
    
    
   
  
    public static $param_ssd = array("msisdn","sms");
    public static $host_ssd = "http://sms.sslwireless.com/pushapi/dynamic/server.php?user=classtune&pass=ssl@123&sid=ClassTune";
    public static $host_return_ssd = "Success";
    public static function send_sms($sms_data,$school_id,$msg_id)
    {
        if(in_array($school_id,self::$sms_attendence_school)) 
        {
            $sms_hosts_name = self::$sms_school_host[$school_id];
            $sms_hosts = self::$$sms_hosts_name;
            $sms_parmas_name = self::$sms_school_param[$school_id];
            $sms_params = self::$$sms_parmas_name;
            
            $sms_gateway_response = self::$sms_school_hosts_return[$school_id];
            $sms_response = self::$$sms_gateway_response;
            
            $params_string = "";
            foreach($sms_params as $key=>$value)
            {
                $params_string = $params_string."&".$value."=".$sms_data[$key];
            } 
            $full_url_sms = $sms_hosts.$params_string;
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $full_url_sms);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET"); 
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $result = curl_exec($ch);
            
            
            
            $sms_log = new SmsLogs();
            $sms_log->mobile = $sms_data[0];
            $sms_log->sms_message_id = $msg_id;
            $sms_log->gateway_response = $sms_response;
            $sms_log->created_at = date("Y-m-d H:i:s");
            $sms_log->updated_at = date("Y-m-d H:i:s");
            $sms_log->school_id = $school_id;
            $sms_log->save();
            
          
            $configobj = new Configurations();
            $config_id = $configobj->getConfigId("TotalSmsCount",$school_id);

            if($config_id)
            {
                $configmain = $configobj->findByPk($config_id);
                $configmain->config_value = $configmain->config_value+1;
                $configmain->save();
            }
            
            
        }
    } 
    public static function send_sms_ssl($sms_numbers,$sms_msg_array,$school_id)
    {
        
        $sms_params = array();
        if($sms_numbers && in_array($school_id,self::$sms_attendence_school))
        {
            $configobj = new Configurations();
            $config_id = $configobj->getConfigId("TotalSmsCount",$school_id);

            if($config_id)
            {
                $configmain = $configobj->findByPk($config_id);
                $configmain->config_value = $configmain->config_value+count($sms_numbers);
            }
            foreach($sms_numbers as $key=>$value)
            {
                $sms_msg = new SmsMessages();
                $sms_msg->body = str_replace(" ","+", $sms_msg_array[$key]);
                $sms_msg->created_at = date("Y-m-d H:i:s");
                $sms_msg->updated_at = date("Y-m-d H:i:s");
                $sms_msg->school_id = $school_id;
                $sms_msg->save();
                if($sms_msg)
                {
                    $sms_log = new SmsLogs();
                    $sms_log->mobile = $value;
                    $sms_log->sms_message_id = $sms_msg->id;
                    $sms_log->gateway_response = "Success";
                    $sms_log->created_at = date("Y-m-d H:i:s");
                    $sms_log->updated_at = date("Y-m-d H:i:s");
                    $sms_log->school_id = $school_id;
                    $sms_log->save(); 
                }
                $sms_params[] = "sms[".$key."][0]= ".$value."&sms[".$key."][1]=".urlencode($sms_msg_array[$key])."&sms[".$key."][2]=123456789"; 
            }
            if($sms_params)
            {
                $user = "classtune";
                $pass = "ssl@123";
                $sid = "ClassTune";
                $url="http://sms.sslwireless.com/pushapi/dynamic/server.php";
                $param="user=$user&pass=$pass";
                $param = $param."&".implode("&",$sms_params)."&sid=".$sid;
                $crl = curl_init();
                curl_setopt($crl,CURLOPT_SSL_VERIFYPEER,FALSE);
                curl_setopt($crl,CURLOPT_SSL_VERIFYHOST,2);
                curl_setopt($crl,CURLOPT_URL,$url);
                curl_setopt($crl,CURLOPT_HEADER,0);
                curl_setopt($crl,CURLOPT_RETURNTRANSFER,1);
                curl_setopt($crl,CURLOPT_POST,1);
                curl_setopt($crl,CURLOPT_POSTFIELDS,$param);
                $response = curl_exec($crl);
                curl_close($crl);
            }
            
            $configobj = new Configurations();
            $config_id = $configobj->getConfigId("TotalSmsCount",$school_id);
            if($config_id)
            {
                $configmain = $configobj->findByPk($config_id);
                $configmain->config_value = $configmain->config_value+count($sms_numbers);
                $configmain->save();
            }

        }
    }        
    
}

