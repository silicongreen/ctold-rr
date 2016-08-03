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
 * @package    Zend_Gdata
 * @subpackage App
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Element.php 24593 2012-01-05 20:35:02Z matthew $
 */

/**
 * @see Zend_Gdata_App_Extension
 */
#require_once 'Zend/Gdata/App/Extension.php';

/**
 * Class that represents elements which were not handled by other parsing
 * code in the library.
 *
 * @category   Zend
 * @package    Zend_Gdata
 * @subpackage App
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Gdata_App_Extension_Element extends Zend_Gdata_App_Extension
{

    public function __construct($rootElement=null, $rootNamespace=null, $rootNamespaceURI=null, $text=null){
        parent::__construct();
        $this->_rootElement = $rootElement;
        $this->_rootNamespace = $rootNamespace;
        $this->_rootNamespaceURI = $rootNamespaceURI;
        $this->_text = $text;
    }

    public function transferFromDOM($node)
    {
        parent::transferFromDOM($node);
        $this->_rootNamespace = null;
        $this->_rootNamespaceURI = $node->namespaceURI;
        $this->_rootElement = $node->localName;
    }

}
