<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Contains class definition for Amazon Web Services account
 *
 * PHP version 5
 *
 * LICENSE:
 *
 * Copyright 2008 Mike Brittain, 2009 silverorange
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
 * @author    Mike Brittain <mike@mikebrittain.com>
 * @author    Michael Gauthier <mike@silverorange.com>
 * @copyright 2008 Mike Brittain, 2009 silverorange
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache License 2.0
 * @version   CVS: $Id:$
 * @link      http://pear.php.net/package/Services_Amazon_EC2
 * @link      http://aws.amazon.com/ec2/
 * @link      http://s3.amazonaws.com/awsdocs/EC2/2008-12-01/ec2-dg-2008-12-01.pdf
 */

/**
 * Amazon Web Services account
 *
 * @category  Services
 * @package   Services_Amazon_EC2
 * @author    Mike Brittain <mike@mikebrittain.com>
 * @author    Michael Gauthier <mike@silverorange.com>
 * @copyright 2008 Mike Brittain, 2009 silverorange
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache License 2.0
 * @link      http://pear.php.net/package/Services_Amazon_EC2
 * @link      http://aws.amazon.com/ec2/
 * @link      http://s3.amazonaws.com/awsdocs/EC2/2008-12-01/ec2-dg-2008-12-01.pdf
 */
class Services_Amazon_EC2_Account
{
    // {{{ private properties

    /**
     * The Amazon Web Services access key id
     *
     * This is a 20-character hexadecimal string.
     *
     * @var string
     *
     * @see Services_Amazon_EC2_Account::getAccessKey()
     */
    private $_awsAccessKey = '';

    /**
     * The Amazon Web Services secret access key
     *
     * This key should not be shared. It is used to sign requests.
     *
     * @var string
     *
     * @see Services_Amazon_EC2_Account::getSecretKey()
     */
    private $_awsSecretAccessKey = '';

    // }}}
    // {{{ __construct()

    /**
     * Creates a new account object used to authenticate actions for
     * Amazon Web Services
     *
     * @param string $accessKey       a 20-character hexadecimal string
     *                                containing the access key id of this
     *                                account.
     * @param string $secretAccessKey the secret access key of this account.
     */
    public function __construct($accessKey, $secretAccessKey)
    {
        $this->_awsAccessKey       = $accessKey;
        $this->_awsSecretAccessKey = $secretAccessKey;
    }

    // }}}
    // {{{ getAccessKey()

    /**
     * Gets the Amazon Web Services access key id of this account
     *
     * @return string a 20-character hexadecimal string containing the access
     *                key id of this account.
     */
    public function getAccessKey()
    {
        return $this->_awsAccessKey;
    }

    // }}}
    // {{{ getSecretKey()

    /**
     * Gets the Amazon Web Services secret access key of this account
     *
     * The secret access key is used to sign requests for Amazon Web Services.
     *
     * @return string a string containing the secret access key of this account.
     */
    public function getSecretAccessKey()
    {
        return $this->_awsSecretAccessKey;
    }

    // }}}
}

?>
