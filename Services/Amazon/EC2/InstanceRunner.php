<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Contains a class for launching Amazon Elastic Compute Cloud (EC2) instances
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
 * @see       Services_Amazon_EC2_InstanceManager
 * @see       Services_Amazon_EC2_Instance
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
 * Instance class.
 */
require_once 'Services/Amazon/EC2/Instance.php';

/**
 * Launches instances on Amazon EC2
 *
 * This class contains methods to setup and launch Amazon machine image
 * instances.
 *
 * @category  Services
 * @package   Services_Amazon_EC2
 * @author    Michael Gauthier <mike@silverorange.com>
 * @copyright 2009 silverorange
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache License 2.0
 * @link      http://pear.php.net/package/Services_Amazon_EC2
 * @link      http://aws.amazon.com/ec2/
 * @link      http://s3.amazonaws.com/awsdocs/EC2/2008-12-01/ec2-dg-2008-12-01.pdf
 * @see       Services_Amazon_EC2_InstanceManager
 * @see       Services_Amazon_EC2_Instance
 */
class Services_Amazon_EC2_InstanceRunner extends
    Services_Amazon_EC2_AbstractInstanceManager
{
    // {{{ protected properties

    /**
     * @var array
     */
    protected $parameters = array(
        'Action'   => 'RunInstances',
        'MinCount' => 1,
        'MaxCount' => 1
    );

    // }}}
    // {{{ __construct()

    /**
     * Creates an EC2 instance object
     *
     * @param Services_Amazon_EC2_Image|string                    either a
     *        {@link Services_Amazon_EC2_Image} object or a string containing
     *        the Amazon Machine Image identifier to use for launched instances.
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

    public function __construct($image, $accessKey, $secretAccessKey = '',
        HTTP_Request2 $request = null
    ) {
        parent::__construct($accessKey, $secretAccessKey, $request);
        $this->setImage($image);
    }

    // }}}
    // {{{ runInstances()

    /**
     * Launches instances using the current configuration
     *
     * @return array an array of {@link Services_Amazon_EC2_Instance} objects
     *               for the freshly launched instances.
     */
    public function runInstances()
    {
        $params   = $this->getParameters();
        $response = $this->sendRequest($params);

        $xpath = $response->getXPath();
        $node  = $xpath->query('//ec2:RunInstancesResponse')->item(0);
        return $this->parseReservationInfoType($node, $xpath);
    }

    // }}}
    // {{{ setNumber()

    /**
     * Sets the number of instances to launch
     *
     * @param integer $min the minimum number of instances to launch. If this
     *                     number of instances can not be launched, the
     *                     {@link Services_Amazon_EC2_InstanceRunner::runInstances()}
     *                     method will throw an exception.
     * @param integer $max optional. The maximum number of instances to launch.
     *                     If not specified, defaults to <kbd>$min</kbd>.
     *
     * @return Services_Amazon_EC2_InstanceRunner
     */
    public function setNumber($min, $max = 0)
    {
        $min = intval($min);
        $max = intval($max);

        if ($min === 0 && $max === 0) {
            throw new InvalidArgumentException('Can not launch zero instances');
        }

        $this->parameters['MinCount'] = $min;
        $this->parameters['MaxCount'] = max($max, $min);

        return $this;
    }

    // }}}
    // {{{ setKeyName()

    /**
     * Note: Running public instances without specifying a key-pair will make
     * login impossible.
     *
     * @param string $keyName the name of the key-pair with which to launch the
     *                        instances.
     *
     * @return Services_Amazon_EC2_InstanceRunner
     */
    public function setKeyName($keyPairName)
    {
        $this->parameters['KeyName'] = strval($keyName);

        return $this;
    }

    // }}}
    // {{{ setSecurityGroups()

    /**
     * @param array $groups the security groups to associate with launched
     *                      instances. The array keys should be numeric and
     *                      correspond to launched instances counting from 1.
     *                      The security group for the first launched instance
     *                      will be the security group with the array key `1',
     *                      etc. The array may be sparsely indexed if no
     *                      security group is required for a particular launched
     *                      instance.
     *
     * @return Services_Amazon_EC2_InstanceRunner
     */
    public function setSecurityGroups(array $groups)
    {
        foreach ($groups as $key => $group) {
            if (!ctype_digit($key)) {
                throw new InvalidArgumentException(
                    'Security group array keys must be numeric. Key "' . $key .
                    '" is not allowed.');
            }

            if ($key == 0) {
                throw new InvalidArgumentException(
                    'Security group array keys start at 1. Can not specifiy ' .
                    'a group for "0".');
            }

            $this->parameters['SecurityGroup.' . $key] = strval($group);
        }
        return $this;
    }

    // }}}
    // {{{ setUserData()

    /**
     * For example, this data could contain configuration information that is
     * applied after instances are launched. Limited to 16000 bytes.
     *
     * @param string $data user-specified data that is retrievable by launched
     *                     instances. Limited to 16000 bytes.
     *
     * @return Services_Amazon_EC2_InstanceRunner
     */
    public function setUserData($data)
    {
        $data = trim(strval($data));
        if ($data == '') {
            return $this;
        }

        if (   extension_loaded('mbstring')
            && (ini_get('mbstring.func_overload') & 0x02) === 0x02
        ) {
            $length = mb_strlen($data, '8bit');
        } else {
            $length = strlen($data);
        }

        if ($length > 16000) {
            throw new InvalidArgumentException('User-data can not be more ' .
                'than 16000 bytes in length.');
        }

        $this->parameters['UserData'] = base64_encode($data);

        return $this;
    }

    // }}}
    // {{{ setType()

    /**
     * The default type used if no type is set is
     * {@link Services_Amazon_EC2_Instance::TYPE_SMALL}.
     *
     * @param string $type the type of instance to start. Should be one of the
     *                     constants defined in
     *                     {@link Services_Amazon_EC2_Instance}.
     *
     * @return Services_Amazon_EC2_InstanceRunner
     * @throws InvalidArgumentException In case an invalid type was specified.
     */
    public function setType($type)
    {
        static $validTypes = array(
            Services_Amazon_EC2_Instance::TYPE_SMALL,
            Services_Amazon_EC2_Instance::TYPE_LARGE,
            Services_Amazon_EC2_Instance::TYPE_EXTRA_LARGE,
            Services_Amazon_EC2_Instance::TYPE_CPU_MEDIUM,
            Services_Amazon_EC2_Instance::TYPE_CPU_EXTRA_LARGE
        );

        if (!in_array($type, $validTypes)) {
            throw new InvalidArgumentException('Invalid type specified. ' .
                'Must be one of the type constants defined in ' .
                'Services_Amazon_EC2_Instance.');
        }

        $this->parameters['InstanceType'] = $type;

        return $this;
    }

    // }}}
    // {{{ setPlacementAvailabilityZone()

    /**
     * @param string $zone the availability zone in which to launch the
     *                     instances.
     *
     * @return Services_Amazon_EC2_InstanceRunner
     */
    public function setPlacementAvailabilityZone($zone)
    {
        $zone = strval($zone);

        $zones = new Services_Amazon_EC2_Zones;
        if (!in_array($zone, $zones->getZones())) {
            throw new InvalidArgumentException('Invalid zone specified. ' .
                'See Services_Amazon_EC2_Zones');
        } 

        $this->parameters['Placement.AvailabilityZone'] = $zone;

        return $this;
    }

    // }}}
    // {{{ setKernelId()

    /**
     * @param string $kernelId the kernel identifier with which to launch the
     *                         instances.
     *
     * @return Services_Amazon_EC2_InstanceRunner
     */
    public function setKernelId($kernelId)
    {
        $this->parameters['KernelId'] = strval($kernelId);

        return $this;
    }

    // }}}
    // {{{ setRamDiskId()

    /**
     * @param string $ramDiskId the RAM-disk identifier with which to launch
     *                          the instances.
     *
     * @return void
     */
    public function setRamDiskId($ramDiskId)
    {
        $this->parameters['RamDiskId'] = strval($ramDiskId);
    }

    // }}}
    // {{{ setBlockDeviceMappings()

    /**
     *
     * Example:
     * <code>
     * <?php
     * // Maps devide 'sdb' to virtual name 'instancestore0' for first launched
     * // instance and device 'sdc' to virtual name 'instancestore1' for third
     * // launched instace.
     * $runner->setBlockDeviceMappings(
     *     array(
     *         1 => array('sdb' => 'instancestore0'),
     *         3 => array('sdc' => 'instancestore1')
     *     )
     * );
     * ?>
     * </code>
     *
     * @param array $mappings the block device mappings to associate with
     *                        launched instances. The array keys should be
     *                        numeric and correspond to launched instances
     *                        counting from 1. The block device mapping for the
     *                        first launched instance will be the mapping with
     *                        the array key `1', etc. The array may be sparsely
     *                        indexed if no block device mapping is required
     *                        for a particular launched instance. Array values
     *                        are single-element arrays where the array key
     *                        is the device name and the array value is the
     *                        mapped virtual name.
     *
     * @return Services_Amazon_EC2_InstanceRunner
     * @throws InvalidArgumentException On non-numeric array keys.
     * @throws InvalidArgumentException If the $mappings array doesn't start at 0.
     * @throws InvalidArgumentException If the value is not a single element array.
     */
    public function setBlockDeviceMappings(array $mappings)
    {
        foreach ($mappings as $key => $mapping) {
            if (!ctype_digit($key)) {
                throw new InvalidArgumentException(
                    'Block devide mapping array keys must be numeric. ' .
                    'Key "' . $key . '" is not allowed.');
            }

            if ($key == 0) {
                throw new InvalidArgumentException(
                    'Block device mapping array keys start at 1. Can not ' .
                    'specifiy a block device mapping for "0".');
            }

            if (!is_array($mapping) || count($mapping) !== 1) {
                throw new InvalidArgumentException(
                    'Mapping for instance "' . $key . '" must be a single ' .
                    'element array.');
            }

            $this->parameters['BlockDeviceMapping.' . $key . '.VirtualName'] =
                strval(key($mapping));

            $this->parameters['BlockDeviceMapping.' . $key . '.DeviceName'] =
                strval(reset($mapping));
        }

        return $this;
    }

    // }}}
    // {{{ setImage()

    /**
     * @param string|Services_Amazon_EC2_Image $image
     *
     * @return Services_Amazon_EC2_InstanceRunner
     * @throws InvalidArgumentException On wrong $image argument.
     */
    protected function setImage($image)
    {
        if (   !($image instanceof Services_Amazon_EC2_Image)
            && !is_string($image)
        ) {
            throw new InvalidArgumentException('The image must be either a ' .
                'Services_Amazon_EC2_Image object or an image identifier '.
                'string.');
        }

        $this->parameters['ImageId'] = strval($image);

        return $this;
    }

    // }}}
    // {{{ getParameters()

    protected function getParameters()
    {
        $parameters = $this->parameters;
        return $parameters;
    }

    // }}}
}

?>
