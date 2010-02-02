<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Contains a class for managing Amazon Elastic Compute Cloud (EC2) instances
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
require_once 'Services/Amazon/EC2/AbstractInstanceManager.php';

/**
 * Instance class.
 */
require_once 'Services/Amazon/EC2/Instance.php';

/**
 * Instance runner class.
 */
require_once 'Services/Amazon/EC2/InstanceRunner.php';

/**
 * Manages instances on Amazon EC2
 *
 * This class contains methods to get details about current instances, run new
 * instances or terminate existing instances.
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
class Services_Amazon_EC2_InstanceManager extends
    Services_Amazon_EC2_AbstractInstanceManager
{
    // {{{ getRunner()

    /**
     * @param Services_Amazon_EC2_Image|string $image
     *
     * @return Services_Amazon_EC2_InstanceRunner
     */
    public function getRunner($image)
    {
        return new Services_Amazon_EC2_InstanceRunner(
            $image,
            $this->account,
            null,
            $this->request
        );
    }

    // }}}
    // {{{ describeInstances()

    /**
     * @param array|string|Services_Amazon_EC2_Instance $instance
     *
     * @return array an array of {@link Services_Amazon_EC2_Instance} objects.
     */
    public function describeInstances($instance = null)
    {
        $params = array();
        $params['Action'] = 'DescribeInstances';

        $instances = $instance;

        if ($instance === null) {
            $instances = array();
        }

        if (!is_array($instances)) {
            $instances = array($instances);
        }

        $count = 1;
        foreach ($instances as $instance) {
            if (   !($instance instanceof Services_Amazon_EC2_Instance)
                && !is_string($instance)
            ) {
                throw new InvalidArgumentException('Instances must be ' .
                    'specified either as Services_Amazon_EC2_Instance ' .
                    'objects or instance identifier strings.');
            }

            $params['InstanceId.' . $count] = strval($instance);
            $count++;
        }

        $response  = $this->sendRequest($params);


        $xpath     = $response->getXPath();
        $nodes     = $xpath->query('//ec2:reservationSet/ec2:item');

        foreach ($nodes as $reservationInfoNode) {
            $instances = array_merge(
                $instances,
                $this->parseReservationInfoType(
                    $reservationInfoNode,
                    $xpath
                )
            );
        }

        return $instances;
    }

    // }}}
    // {{{ terminateInstances()

    /**
     * @param array|string|Services_Amazon_EC2_Instance $instance
     *
     * @return array an array of instances and state changes. The array is of
     *               the form:
     *               <code>
     *               <?php
     *               array(
     *                   $instanceId1 => array(
     *                       'shutdownState' => $shutdownState1
     *                       'previousState' => $previousState1,
     *                   ),
     *                   $instanceId2 => array(
     *                        // etc ...
     *                   )
     *               );
     *               ?>
     *               </code>
     */
    public function terminateInstances($instance)
    {
        $params = array();
        $params['Action'] = 'TerminateInstances';

        $instances = $instance;
        if (!is_array($instances)) {
            $instances = array($instances);
        }

        if (count($instances) === 0) {
            throw new InvalidArgumentException('At least one instance must ' .
                'be specified.');
        }

        $count = 1;
        foreach ($instances as $instance) {
            if (   !($instance instanceof Services_Amazon_EC2_Instance)
                && !is_string($instance)
            ) {
                throw new InvalidArgumentException('Instances must be ' .
                    'specified either as Services_Amazon_EC2_Instance ' .
                    'objects or instance identifier strings.');
            }

            $params['InstanceId.' . $count] = strval($instance);
            $count++;
        }

        $response = $this->sendRequest($params);

        // parse response
        $xpath     = $response->getXPath();
        $nodes     = $xpath->query('//ec2:instancesSet/ec2:item');
        $instances = array();
        foreach ($nodes as $node) {
            $instanceId = $xpath->evaluate(
                'string(ec2:instanceId/text())',
                $node
            );

            $shutdownState = $xpath->evaluate(
                'string(ec2:shutdownState/ec2:name/text())',
                $node
            );

            $previousState = $xpath->evaluate(
                'string(ec2:previousState/ec2:name/text())',
                $node
            );

            $instances[$instanceId] = array(
                'shutdownState' => $shutdownState,
                'previousState' => $previousState
            );
        }

        return $instances;
    }

    // }}}
}

?>
