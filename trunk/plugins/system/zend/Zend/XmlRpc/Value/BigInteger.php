<?php defined( '_JEXEC') or die( 'Restricted Access' );
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
 * @package    Zend_XmlRpc
 * @subpackage Value
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: BigInteger.php 23775 2011-03-01 17:25:24Z ralph $
 */


/**
 * Zend_XmlRpc_Value_Integer
 */
require_once 'Zend/XmlRpc/Value/Integer.php';


/**
 * @category   Zend
 * @package    Zend_XmlRpc
 * @subpackage Value
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_XmlRpc_Value_BigInteger extends Zend_XmlRpc_Value_Integer
{
    /**
     * @var Zend_Crypt_Math_BigInteger
     */
    protected $_integer;

    /**
     * @param mixed $value
     */
    public function __construct($value)
    {
        require_once 'Zend/Crypt/Math/BigInteger.php';
        $this->_integer = new Zend_Crypt_Math_BigInteger();
        $this->_value = $this->_integer->init($this->_value);

        $this->_type = self::XMLRPC_TYPE_I8;
    }

    /**
     * Return bigint value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->_integer;
    }
}
