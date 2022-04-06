<?php  
include("../../config/config.php");
include("../classes/User.php");

$users = new User($con);
$users->loadUsers($_REQUEST);
?>