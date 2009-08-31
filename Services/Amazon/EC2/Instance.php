<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Contains a class representing a server instance on the Amazon Elastic
 * Compute Cloud (EC2)
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
 * Exception classes.
 */
require_once 'Services/Amazon/EC2/Exceptions.php';

/**
 * Base class.
 */
require_once 'Services/Amazon/EC2.php';

/**
 * Base class.
 */
require_once 'Services/Amazon/EC2/InstanceManager.php';

/**
 * An instance on Amazon EC2
 *
 * This class contains methods to get details about an instance or to terminate
 * an instance.
 *
 * @category  Services
 * @package   Services_Amazon_EC2
 * @author    Michael Gauthier <mike@silverorange.com>
 * @copyright 2009 silverorange
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache License 2.0
 * @link      http://pear.php.net/package/Services_Amazon_EC2
 * @link      http://aws.amazon.com/ec2/
 * @link      http://s3.amazonaws.com/awsdocs/EC2/2008-12-01/ec2-dg-2008-12-01.pdf
 */
class Services_Amazon_EC2_Instance extends Services_Amazon_EC2
{
    // {{{ class constants

    /**
     * Server to query for instance metadata and user-data.
     */
    const METADATA_SERVER = '169.254.169.254';

    const STATE_RUNNING        = 'running';
    const STATE_PENDING        = 'pending';
    const STATE_SHUTTING_DOWN  = 'shutting-down';
    const STATE_TERMINATED     = 'terminated';

    const TYPE_SMALL           = 'm1.small';
    const TYPE_LARGE           = 'm1.large';
    const TYPE_EXTRA_LARGE     = 'm1.xlarge';
    const TYPE_CPU_MEDIUM      = 'c1.medium';
    const TYPE_CPU_EXTRA_LARGE = 'c1.xlarge';


    // }}}
    // {{{ protected properties

    /**
     * @var string
     */
    protected $id = '';

    /**
     * @var string
     */
    protected $type = self::TYPE_SMALL;

    /**
     * @var Services_Amazon_EC2_Image
     */
    protected $image = null;

    /**
     * @var string
     */
    protected $kernelId = '';

    /**
     * @var string
     */
    protected $ramDiskId = '';

    /**
     * @var string
     */
    protected $state = self::STATE_PENDING;

    /**
     * @var string
     */
    protected $dnsName = '';

    /**
     * @var string
     */
    protected $privateDnsName = '';

    /**
     * @var string
     */
    protected $keyName = '';

    /**
     * @var DateTime
     */
    protected $launchTime = null;

    /**
     * @var DateTime
     */
    protected $launchIndex = 0;

    /**
     * @var string
     */
    protected $placement = '';

    /**
     * @var array
     */
    protected $productCodes = array();

    /**
     * @var array
     */
    protected $groups = array();

    /**
     * @var string
     */
    protected $ownerId = '';

    /**
     * @var string
     */
    protected $reservationId = '';

    // }}}
    // {{{ __construct()

    /**
     * Creates an EC2 instance object
     *
     * @param string|array                       $id              the instance
     *        identifier of this instance, or an array containing the data
     *        for this instance.
     *
     * @param Services_Amazon_EC2_Account|string $accessKey       either a
     *        {@link Services_Amazon_EC2_Account} object or a string containing
     *        the AWS access key for an account.
     *
     * @param string                             $secretAccessKey if the second
     *        parameter is an account object, this parameter is ignored.
     *        Otherwise, this parameter is required and is the secret access
     *        key for the AWS account.
     *
     * @param HTTP_Request2                      $request         optional. The
     *        HTTP request object to use. If not specified, a HTTP request
     *        object is created automatically.
     */

    public function __construct($instanceId, $accessKey, $secretAccessKey = '',
        HTTP_Request2 $request = null
    ) {
        parent::__construct($accessKey, $secretAccessKey, $request);

        if (is_array($instanceId)) {
            $this->initializeFromData($instanceId);
        } else {
            $this->instanceId = strval($instanceId);
        }
    }

    // }}}
    // {{{ terminate()

    /**
     * Stops this instance
     *
     * @return
     */
    public function terminate()
    {
        $manager = new Services_Amazon_EC2_InstanceManager(
            $this->account,
            null,
            $this->request
        );

        return $manager->terminateInstances($this);
    }

    // }}}
    // {{{ __toString()

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getId();
    }

    // }}}

    // accessors
    // {{{ getId()

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    // }}}
    // {{{ getType()

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    // }}}
    // {{{ getImage()

    /**
     * @return Services_Amazon_EC2_Image
     */
    public function getImage()
    {
        return $this->image;
    }

    // }}}
    // {{{ getKernelId()

    /**
     * @return string
     */
    public function getKernelId()
    {
        return $this->kernelId;
    }

    // }}}
    // {{{ getRamDiskId()

    /**
     * @return string
     */
    public function getRamDiskId()
    {
        return $this->ramDiskId;
    }

    // }}}
    // {{{ getState()

    /**
     * @return string
     */
    public function getState()
    {
        return $this->state;
    }

    // }}}
    // {{{ getDnsName()

    /**
     * @return string
     */
    public function getDnsName()
    {
        return $this->dnsName;
    }

    // }}}
    // {{{ getPrivateDnsName()

    /**
     * @return string
     */
    public function getPrivateDnsName()
    {
        return $this->privateDnsName;
    }

    // }}}
    // {{{ getKeyName()

    /**
     * @return string the key name, or an empty string if no key-pair was
     *                specified when this instance was launched.
     */
    public function getKeyName()
    {
        return $this->keyName;
    }

    // }}}
    // {{{ getLaunchTime()

    /**
     * @return DateTime
     */
    public function getLaunchTime()
    {
        return $this->launchTime;
    }

    // }}}
    // {{{ getLaunchIndex()

    /**
     * @return integer
     */
    public function getLaunchIndex()
    {
        return $this->launchIndex;
    }

    // }}}
    // {{{ getPlacementAvailabilityZone()

    /**
     * @return string
     */
    public function getPlacementAvailabilityZone()
    {
        return $this->placement;
    }

    // }}}
    // {{{ getProductCodes()

    /**
     * @return array
     */
    public function getProductCodes()
    {
        return $this->productCodes;
    }

    // }}}
    // {{{ getSecurityGroups()

    /**
     * Gets the security groups of this instance
     *
     * @return array an array of Services_Amazon_EC2_SecurityGroup objects.
     */
    public function getSecurityGroups()
    {
        return $this->groups;
    }

    // }}}
    // {{{ getOwnerId()

    /**
     * Gets the owner identifier of this instance
     *
     * @return string the owner identifier of this instance.
     */
    public function getOwnerId()
    {
        return $this->ownerId;
    }

    // }}}
    // {{{ getReservationId()

    /**
     * Gets the reservation identifier of this instance
     *
     * Instances are grouped into reservations. Each reservation represents a
     * single call to <kbd>RunInstances</kbd> during which one or more
     * instances were created.
     *
     * @return string the reservation identifier of this instance.
     */
    public function getReservationId()
    {
        return $this->reservationId;
    }

    // }}}

    // initialization
    // {{{ initializeFromData()

    protected function initializeFromData(array $data)
    {
        $stringFields = array(
            'id',
            'type',
            'kernelId',
            'ramDiskId',
            'state',
            'dnsName',
            'privateDnsName',
            'keyName',
            'placement',
            'ownerId',
            'reservationId'
        );

        foreach ($stringFields as $fieldName) {
            if (array_key_exists($fieldName, $data)) {
                $this->$fieldName = strval($data[$fieldName]);
            }
        }

        $arrayFields = array(
            'productCodes',
            'securityGroups'
        );

        foreach ($arrayFields as $fieldName) {
            if (array_key_exists($fieldName, $data)) {
                if (!is_array($data[$fieldName])) {
                    throw new InvalidArgumentException('The field "' .
                        $fieldName . '" must be an array.');
                }
                $this->$fieldName = $data[$fieldName];
            }
        }

        if (array_key_exists('image', $data)) {
            // TODO: instantiate an image object
            $this->image = $data['image'];
        }

        if (array_key_exists('launchIndex', $data)) {
            $this->launchIndex = intval($data['launchIndex']);
        }

        if (array_key_exists('launchTime', $data)) {
            $this->launchTime = new DateTime($data['launchTime']);
        }
    }

    // }}}
    // {{{ initializeFromId()

    /**
     * Initializes this instance from an instance identifier
     *
     * This internally calls <kbd>DescribeInstances</kbd> to set the instance
     * properties.
     *
     * @param string $id
     */
    protected function initializeFromId($id)
    {
        $manager = new Services_Amazon_EC2_InstanceManager(
            $this->account,
            null,
            $this->request
        );

        $instances = $manager->describeInstances($id);

        if (count($instances) === 1) {
            $instance = reset($instances);
            $fields = array(
                'id',
                'image',
                'type',
                'kernelId',
                'ramDiskId',
                'state',
                'dnsName',
                'privateDnsName',
                'keyName',
                'launchIndex',
                'launchTime',
                'placement',
                'ownerId',
                'reservationId',
                'productCodes',
                'securityGroups'
            );

            foreach ($fields as $fieldName) {
                $this->$fieldName = $instance->$fieldName;
            }
        }
    }

    // }}}
}

?>
