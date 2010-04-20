<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * This is the package.xml generator for Services_Amazon_EC2
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
 * @link      http://pear.php.net/package/Services_Amazon_EC2
 */

require_once 'PEAR/PackageFileManager2.php';
PEAR::setErrorHandling(PEAR_ERROR_DIE);

$api_version     = '0.0.2';
$api_state       = 'alpha';

$release_version = '0.0.3';
$release_state   = 'alpha';
$release_notes   = "Alpha release contains instance-related methods only.\n" .
    "This release indexes results of describeInstances() and runInstances() " .
    "by the instance id.";

$description =
    "This package provides an object-oriented interface to the Amazon " .
    "Elastic Compute Cloud (EC2). Included are client libraries that " .
    "implement the EC2 API. You will need a set of web-service keys " .
    "from Amazon Web Services that have EC2 enabled. You can sign up for an " .
    "account at: " .
    "https://aws-portal.amazon.com/gp/aws/developer/registration/index.html." .
    "\n\n" .
    "Note: Although this package has no cost, Amazon's Web services are not " .
    "free to use. You will be billed by Amazon for your use of EC2." .
    "\n\n" .
    "This package requires PHP 5.2.1. On Red Hat flavored distributions, the " .
    "'php-xml' package must also be installed.";

$package = new PEAR_PackageFileManager2();

$package->setOptions(
    array(
        'filelistgenerator'       => 'file',
        'simpleoutput'            => true,
        'baseinstalldir'          => '/',
        'packagedirectory'        => './',
        'dir_roles'               => array(
            'Services'            => 'php',
            'Services/Amazon'     => 'php',
            'Services/Amazon/EC2' => 'php',
            'tests'               => 'test'
        ),
        'ignore'                  => array(
            'package.php',
            '*.tgz'
        )
    )
);

$package->setPackage('Services_Amazon_EC2');
$package->setSummary('PHP API for Amazon EC2 (Elastic Compute Cloud)');
$package->setDescription($description);
$package->setChannel('pear.silverorange.com');
$package->setPackageType('php');
$package->setLicense(
    'Apache License 2.0',
    'http://www.apache.org/licenses/LICENSE-2.0'
);

$package->setNotes($release_notes);
$package->setReleaseVersion($release_version);
$package->setReleaseStability($release_state);
$package->setAPIVersion($api_version);
$package->setAPIStability($api_state);

$package->addMaintainer(
    'lead',
    'gauthierm',
    'Mike Gauthier',
    'mike@silverorange.com'
);

$package->addReplacement(
    'Services/Amazon/EC2.php',
    'package-info',
    '@api-version@',
    'api-version'
);

$package->addReplacement(
    'Services/Amazon/EC2.php',
    'package-info',
    '@name@',
    'name'
);

$package->setPhpDep('5.2.1');

$package->addPackageDepWithChannel(
    'required',
    'PEAR',
    'pear.php.net'
);

$package->addPackageDepWithChannel(
    'required',
    'Crypt_HMAC2',
    'pear.php.net',
    '0.2.1'
);

$package->addPackageDepWithChannel(
    'required',
    'Net_URL2',
    'pear.php.net',
    '0.2.0'
);

$package->addPackageDepWithChannel(
    'required',
    'HTTP_Request2',
    'pear.php.net',
    '0.1.0'
);

$package->setPearInstallerDep('1.7.0');
$package->generateContents();
$package->addRelease();

if (   isset($_GET['make'])
    || (isset($_SERVER['argv']) && @$_SERVER['argv'][1] == 'make')
) {
    $package->writePackageFile();
} else {
    $package->debugPackageFile();
}

?>
