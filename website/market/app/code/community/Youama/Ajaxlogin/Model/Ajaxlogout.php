<?php

/**
 * YouAMA.com
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA that is bundled with this package
 * on http://youama.com/freemodule-license.txt.
 *
 *******************************************************************************
 *                          MAGENTO EDITION USAGE NOTICE
 *******************************************************************************
 * This package designed for Magento Community edition. Developer(s) of
 * YouAMA.com does not guarantee correct work of this extension on any other
 * Magento edition except Magento Community edition. YouAMA.com does not
 * provide extension support in case of incorrect edition usage.
 *******************************************************************************
 *                                  DISCLAIMER
 *******************************************************************************
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future.
 *******************************************************************************
 * @category   Youama
 * @package    Youama_Ajaxlogin
 * @copyright  Copyright (c) 2012-2014 YouAMA.com (http://www.youama.com)
 * @license    http://youama.com/freemodule-license.txt
 */

/**
 * Login user.
 * Class Youama_Ajaxlogin_Model_Ajaxlogin
 * @author doveid
 */
class Youama_Ajaxlogin_Model_Ajaxlogout extends Youama_Ajaxlogin_Model_Validator
{
    /**
     * Init.
     */
    public function _construct() 
    {
        parent::_construct();
        
        
		//$this->setEmail($_POST['email']);
        //$this->setSinglePassword($_POST['password']);

        // Start login process.
        if ($this->_result == '') {
            $this->_logoutUser();
        }
    }

    /**
     * Try login user.
     */
    protected function _logoutUser() {

		$session = Mage::getSingleton('customer/session');

        try {
            
			if($session->isLoggedIn()){
				$session->unsetAll();     
			}
			
			$arr['name'] = "DONE";			
			
            $this->_result .= $_GET['callback']. '(' . json_encode($arr) . ');';
        } catch(Exception $ex) {
            $this->_result .= 'LIKHON-ERROR,';
        }
    }

    /**
     * String result for Javascript.
     * @return string
     */
    public function getResult()
    {
        return $this->_result;
    }
}
