<?php  
include("../../config/config.php");
include("../classes/Constants.php");
include("../classes/User.php");
include("../classes/Problem.php");
include("../classes/Post.php");

$problems = new Post($con);
$problems->loadPosts($_REQUEST);
?>