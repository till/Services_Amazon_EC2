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
 * Amazon Web Services EC2 availability zones and regions.
 *
 * @category  Services
 * @package   Services_Amazon_EC2
 * @author    Till Klampaeckel <till@php.net>
 * @copyright 2008 Mike Brittain, 2009 silverorange
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache License 2.0
 * @link      http://pear.php.net/package/Services_Amazon_EC2
 * @link      http://aws.amazon.com/ec2/
 * @link      http://s3.amazonaws.com/awsdocs/EC2/2008-12-01/ec2-dg-2008-12-01.pdf
 */
class Services_Amazon_EC2_Zones
{
    protected $availabilityZones = array(
        'us-east' => array('us-east-1a', 'us-east-1b', 'us-east-1c', 'us-east-1d'),
        'us-west' => array('us-west-1a', 'us-west-1b'),
        'eu-west' => array('eu-west-1a', 'eu-west-1b'),
    );

    /**
     * Creates a new account object used to authenticate actions for
     * Amazon Web Services
     *
     * @return Services_Amazon_EC2_Zones
     */
    public function __construct()
    {
    }

    /**
     * Return the regions only.
     *
     * @return array
     */
    public function getRegions()
    {
        return array_keys($this->availabilityZones);
    }

    /**
     * Get all zones, by region.
     *
     * @return array
     * @todo   Use an API call instead.
     */
    public function getZones()
    {
        $keep = array();
        foreach ($this->availabilityZones as $region => $zones) {
            $keep = array_merge($keep, $zones);
        }
        return $keep;
    }
}
?>
