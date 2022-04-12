<?php  
include("../../config/config.php");
include("../classes/Tag.php");
include("../current_user.php");
include("../classes/Constants.php");

$tag0=(int)$_REQUEST['tag'];
$tag=abs($tag0);
$sql=mysqli_prepare($con,"SELECT * FROM tagowners WHERE tag=? AND owner=? AND type=0");
mysqli_stmt_bind_param($sql,"si",$tag,$curUID);
mysqli_stmt_execute($sql);
$access_query=mysqli_stmt_get_result($sql);
if(mysqli_num_rows($access_query) == 0 && $user['perms']<Constants::EDITOR_PERMS) {
  echo "Tag unavailable";
  exit;
}

$tags = new Tag($con);
$tags->addProblemTag($_REQUEST['problem'],$tag);
if($tag0<0)
  $tags->calcPoints($curUID);
?>