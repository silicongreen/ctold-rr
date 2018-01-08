<?php

    /**
 * Converts LOCAL time to a GMT value when given a timezone and a timestamp
 *
 * Takes a Unix timestamp (in LOCAL TIME) as input, and returns
 * at the GMT value based on the timezone and DST setting
 * submitted
 *
 * @access    public
 * @param    integer Unix timestamp
 * @param    string    timezone
 * @param    bool    whether DST is active
 * @return    integer
 */    
function convert_to_unix_timestamp($pDate = "",$rtnCurrentDate = false)
{            
    if($pDate != "" && $rtnCurrentDate == false)
    {
        $date = new DateTime($pDate);
        return $date->getTimestamp();
    }
    else if($rtnCurrentDate == true)
    {
        $date = new DateTime(date('d M y'));
        return $date->getTimestamp();
    }
    else{return 0;}
}
?>
