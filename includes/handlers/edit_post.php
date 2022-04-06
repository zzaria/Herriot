<?php 
require '../../config/config.php';
include("../current_user.php");
include("../classes/Post.php");

$id=(int)$_REQUEST['id'];
if($user['perms']<2)
	exit;
$post=new Post($con);
$post->editPost($id,$_REQUEST['value']);



?>