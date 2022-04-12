<?php 
require '../../config/config.php';
include("../current_user.php");
include("../classes/Post.php");
include("../classes/Constants.php");

$id=(int)$_REQUEST['id'];
if($user['perms']<Constants::ADMIN_PERMS)
	exit;
$post=new Post($con);
$post->editPost($id,$_REQUEST['value']);



?>