<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Contains a base class for Amazon EC2 instance managers
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
 * Instance class.
 */
require_once 'Services/Amazon/EC2/Instance.php';

/**
 * Abstract base class for managing instances on Amazon EC2
 *
 * This class contains common construction methods for instance-related methods.
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
abstract class Services_Amazon_EC2_AbstractInstanceManager extends
    Services_Amazon_EC2
{
    // {{{ parseReservationInfoType()

    /**
     * @param Services_Amazon_EC2_Response $response
     *
     * @return array an array of {@link Services_Amazon_EC2_Instance} objects.
     */
    protected function parseReservationInfoType(
        DOMElement $reservationInfoNode, DOMXPath $xpath
    ) {
        $instances = array();

        $reservationId = $xpath->evaluate(
            'string(ec2:reservationId/text())',
            $reservationInfoNode
        );

        $ownerId = $xpath->evaluate(
            'string(ec2:ownerId/text())',
            $reservationInfoNode
        );

        // get security groups
        $securityGroupNodes = $xpath->query(
            'ec2:groupSet/ec2:item',
            $reservationInfoNode
        );

        $securityGroups = array();
        foreach ($securityGroupNodes as $securityGroupNode) {
            $securityGroups[] = $xpath->evaluate(
                'string(ec2:groupId/text())',
                $securityGroupNode
            );
        }

        // get instances
        $instanceNodes = $xpath->query(
            'ec2:instancesSet/ec2:item',
            $reservationInfoNode
        );

        foreach ($instanceNodes as $instanceNode) {
            $data = array();

            // reservation-specific data
            $data['ownerId']        = $ownerId;
            $data['reservationId']  = $reservationId;
            $data['securityGroups'] = $securityGroups;

            // instance-specific data
            $data['id'] = $xpath->evaluate(
                'string(ec2:instanceId/text())',
                $instanceNode
            );

            $data['type'] = $xpath->evaluate(
                'string(ec2:instanceType/text())',
                $instanceNode
            );

            $data['image'] = $xpath->evaluate(
                'string(ec2:imageId/text())',
                $instanceNode
            );

            $data['kernelId'] = $xpath->evaluate(
                'string(ec2:kernelId/text())',
                $instanceNode
            );

            $data['ramDiskId'] = $xpath->evaluate(
                'string(ec2:ramDiskId/text())',
                $instanceNode
            );

            $data['state'] = $xpath->evaluate(
                'string(ec2:instanceState/ec2:name/text())',
                $instanceNode
            );

            $data['dnsName'] = $xpath->evaluate(
                'string(ec2:dnsName/text())',
                $instanceNode
            );

            $data['privateDnsName'] = $xpath->evaluate(
                'string(ec2:privateDnsName/text())',
                $instanceNode
            );

            $data['keyName'] = $xpath->evaluate(
                'string(ec2:keyName/text())',
                $instanceNode
            );

            $data['launchTime'] = $xpath->evaluate(
                'string(ec2:launchTime/text())',
                $instanceNode
            );

            $data['launchIndex'] = $xpath->evaluate(
                'string(ec2:amiLaunchIndex/text())',
                $instanceNode
            );

            $data['placement'] = $xpath->evaluate(
                'string(ec2:placement/ec2:availabilityZone/text())',
                $instanceNode
            );

            $data['productCodes'] = array();
            $productCodeNodes = $xpath->query(
                'ec2:productCodes/ec2:item',
                $instanceNode
            );

            foreach ($productCodeNodes as $productCodeNode) {
                $data['productCodes'][] = $xpath->evaluate(
                    'string(ec2:productCode/text())',
                    $productCodeNode
                );
            }

            $instances[$data['id']] = new Services_Amazon_EC2_Instance(
                $data,
                $this->account,
                null,
                $this->request
            );
        }

        return $instances;
    }

    // }}}
}

?>
