<?php
include("../../config/config.php");
include("../classes/Notification.php");
include("../current_user.php");

$limit = 7; //Number of messages to load

$notification = new Notification($con);
echo $notification->getNotifications($_REQUEST, $curUID);

?>