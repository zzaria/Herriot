<?php 
require '../../config/config.php';
include("../current_user.php");
include("../classes/Constants.php");

$id=(int)$_REQUEST['id'];
$delete=(int)$_REQUEST['delete'];
$author=mysqli_fetch_array(mysqli_query($con,"SELECT author FROM posts WHERE id=$id"))['author'];
if($author!=$curUID and $user['perms']<Constants::ADMIN_PERMS)
	exit;

mysqli_query($con, "UPDATE posts SET deleted=$delete WHERE id=$id");

?>