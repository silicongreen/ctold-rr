<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Settings
 *
 * @author ahuffas
 */
class Settings extends DataMapper 
{
    var $table = "settings";
    
    var $validation = array(
        'value' => array(
            'label' => 'Layout',
            'rules' => array('required', 'trim'),
        )
    );
        
}

?>
