<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * This file contains various exception classes used by the Services_Amazon_EC2
 * package
 *
 * PHP version 5
 *
 * LICENSE:
 *
 * Copyright 2009 silverorange
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * @category  Services
 * @package   Services_Amazon_EC2
 * @author    Michael Gauthier <mike@silverorange.com>
 * @copyright 2009 silverorange
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache License 2.0
 * @version   CVS: $Id:$
 * @link      http://pear.php.net/package/Services_Amazon_EC2
 * @link      http://aws.amazon.com/ec2/
 * @link      http://s3.amazonaws.com/awsdocs/EC2/2008-12-01/ec2-dg-2008-12-01.pdf
 */

/**
 * PEAR exception base class
 */
require_once 'PEAR/Exception.php';

// {{{ class Services_Amazon_EC2_Exception

/**
 * Abstract base class for exceptions thrown by the Services_Amazon_EC2 package
 *
 * @category  Services
 * @package   Services_Amazon_EC2
 * @author    Michael Gauthier <mike@silverorange.com>
 * @copyright 2009 silverorange
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache License 2.0
 * @link      http://pear.php.net/package/Services_Amazon_EC2
 */
abstract class Services_Amazon_EC2_Exception extends PEAR_Exception
{
}

// }}}
// {{{ class Services_Amazon_EC2_HttpException

/**
 * Exception thrown when there is a HTTP communication error in the
 * Services_Amazon_EC2 package
 *
 * @category  Services
 * @package   Services_Amazon_EC2
 * @author    Michael Gauthier <mike@silverorange.com>
 * @copyright 2009 silverorange
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache License 2.0
 * @link      http://pear.php.net/package/Services_Amazon_EC2
 */
class Services_Amazon_EC2_HttpException extends Services_Amazon_EC2_Exception
{
}

// }}}
// {{{ class Services_Amazon_EC2_ErrorException

/**
 * Exception thrown when one or more errors are returned by Amazon
 *
 * The Amazon error code may be retrived using
 * {@link Services_Amazon_EC2_ErrorException::getCode()} and the error message
 * may bre retrieved using
 * {@link Services_Amazon_EC2_ErrorException::getMessage()}.
 *
 * See the Amazon EC2 Developer's Guide for a full list of error codes.
 *
 * @category  Services
 * @package   Services_Amazon_EC2
 * @author    Michael Gauthier <mike@silverorange.com>
 * @copyright 2009 silverorange
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache License 2.0
 * @link      http://pear.php.net/package/Services_Amazon_EC2
 */
class Services_Amazon_EC2_ErrorException extends Services_Amazon_EC2_Exception
{
    // {{{ private properties

    /**
     * The Amazon EC2 error code
     *
     * @var string
     */
    private $_error = '';

    // }}}
    // {{{ __construct()

    /**
     * Creates a new error exception
     *
     * @param string  $message an error message.
     * @param integer $code    a user-defined error code.
     * @param string  $error   the Amazon EC2 error code.
     */
    public function __construct($message, $code, $error = '')
    {
        parent::__construct($message, $code);
        $this->_error = $error;
    }

    // }}}
    // {{{ getName()

    /**
     * Gets the Amazon EC2 error code
     *
     * @return string the Amazon EC2 error code.
     */
    public function getError()
    {
        return $this->_error;
    }

    // }}}
}

// }}}

?>
