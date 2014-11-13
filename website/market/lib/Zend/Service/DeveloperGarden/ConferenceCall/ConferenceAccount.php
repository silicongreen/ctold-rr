<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Service
 * @subpackage DeveloperGarden
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: ConferenceAccount.php 24593 2012-01-05 20:35:02Z matthew $
 */

/**
 * @category   Zend
 * @package    Zend_Service
 * @subpackage DeveloperGarden
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @author     Marco Kaiser
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Service_DeveloperGarden_ConferenceCall_ConferenceAccount
{
    /**
     * type of billing
     *
     * @var string
     */
    public $billingtype = null;

    /**
     * account id
     *
     * @var integer
     */
    public $account = null;

    /**
     * @return integer
     */
    public function getAccount()
    {
        return $this->account;
    }

    /**
     * @return string
     */
    public function getBillingType()
    {
        return $this->billingtype;
    }
}
