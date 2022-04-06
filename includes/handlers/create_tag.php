<?php  
include("../../config/config.php");
include("../classes/Tag.php");

$tags = new Tag($con);
$tags->createTag(htmlspecialchars($_REQUEST['name']),$_REQUEST['user']);
?>