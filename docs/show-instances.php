<?php
require_once 'Services/Amazon/EC2/InstanceManager.php';

$access_key_id     = 'YOUR KEY';
$access_key_secret = 'YOUR SECRET';

$manager = new Services_Amazon_EC2_InstanceManager(
    $access_key_id,
    $access_key_secret
);

$instances = $manager->describeInstances();

foreach ($instances as $instance) {
    echo $instance->getDnsName();
}