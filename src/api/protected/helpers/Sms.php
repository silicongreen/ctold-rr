<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class Sms {
    public static $sms_attendence_school = [2,296];
    
    public static $sms_school_host = array(2=>"host_ssd", 296=>"host_ssd");
    public static $sms_school_param = array(2=>"param_ssd", 296=>"param_ssd");
    public static $sms_school_hosts_return = array(2=>"host_return_ssd", 296=>"host_return_ssd");
    
    
   
  
    public static $param_ssd = array("msisdn","text");
    public static $host_ssd = "http://103.239.252.108/api/send.php?username=robi&password=robi018&sender=ClassTune";
    public static $host_return_ssd = "Successfully inserted to smsoutbox";
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
            $sms_log->gateway_response = $result;
            $sms_log->created_at = date("Y-m-d H:i:s");
            $sms_log->updated_at = date("Y-m-d H:i:s");
            $sms_log->school_id = $school_id;
            $sms_log->save();
            
            if($result == $sms_response)
            {
                $configobj = new Configurations();
                $config_id = $configobj->getConfigId("TotalSmsCount",$school_id);
                
                if($config_id)
                {
                    $configmain = $configobj->findByPk($config_id);
                    $configmain->config_value = $configmain->config_value+1;
                }
            }
            
        }
    }        
    
}

