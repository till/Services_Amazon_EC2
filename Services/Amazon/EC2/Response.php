<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Contains a high-level Amazon Elastic Compute Cloud (EC2) response class
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
 * HTTP response class.
 */
require_once 'HTTP/Request2/Response.php';

/**
 * Amazon Elastic Compute Cloud (EC2) response class
 *
 * This class provides high-level methods for using an Amazon EC2 HTTP response.
 * Detailed response parsing may be accomplished with the XPath object provided
 * by this class.
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
class Services_Amazon_EC2_Response
{
    // {{{ class constants

    /**
     * XML namespace used for EC2 responses.
     */
    const XML_NAMESPACE = 'http://ec2.amazonaws.com/doc/2008-12-01/';

    // }}}
    // {{{ private properties

    /**
     * The original HTTP response
     *
     * This contains the response body and headers.
     *
     * @var Services_Amazon_EC2_HttpResponse
     */
    private $_httpResponse = null;

    /**
     * The response document object
     *
     * @var DOMDocument
     */
    private $_document = null;

    /**
     * The response XPath
     *
     * @var DOMXPath
     */
    private $_xpath = null;

    /**
     * Last error code
     *
     * @var integer
     */
    private $_errorCode = 0;

    /**
     * Last error message
     *
     * @var string
     */
    private $_errorMessage = '';

    // }}}
    // {{{ __construct()

    /**
     * Creates a new high-level EC2 response object
     *
     * @param HTTP_Request2_Response $httpResponse the HTTP response.
     */
    public function __construct(HTTP_Request2_Response $httpResponse)
    {
        $this->_httpResponse = $httpResponse;
    }

    // }}}
    // {{{ getXPath()

    /**
     * Gets the XPath object for this response
     *
     * @return DOMXPath the XPath object for response.
     */
    public function getXPath()
    {
        if ($this->_xpath === null) {
            $document = $this->getDocument();
            if ($document === false) {
                $this->_xpath = false;
            } else {
                $this->_xpath = new DOMXPath($document);
                $this->_xpath->registerNamespace(
                    'ec2',
                    self::XML_NAMESPACE
                );
            }
        }

        return $this->_xpath;
    }

    // }}}
    // {{{ getDocument()

    /**
     * Gets the document object for this response
     *
     * @return DOMDocument the DOM Document for this response.
     */
    public function getDocument()
    {
        try {
            $body = $this->_httpResponse->getBody();
        } catch (HTTP_Request2_Exception $e) {
            $body = false;
        }

        if ($this->_document === null) {
            if ($body !== false) {
                // turn off libxml error handling
                $errors = libxml_use_internal_errors();

                $this->_document = new DOMDocument();
                if (!$this->_document->loadXML($body)) {
                    $this->_document = false;
                }

                // reset libxml error handling
                libxml_clear_errors();
                libxml_use_internal_errors($errors);
            } else {
                $this->_document = false;
            }
        }

        return $this->_document;
    }

    // }}}
}

?>
