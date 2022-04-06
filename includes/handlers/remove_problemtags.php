<?php  
include("../../config/config.php");
include("../classes/Tag.php");
include("../current_user.php");

$tag0=(int)$_REQUEST['tag'];
$tag=abs($tag0);
$access_query= mysqli_query($con, "SELECT * FROM tagowners WHERE tag={$tag} AND owner={$curUID}");
if(mysqli_num_rows($access_query) == 0 && $user['perms']<1) {
  echo "Tag unavailable";
  exit;
}

$tags = new Tag($con);
$tags->removeProblemTag($_REQUEST['problem'],$tag);
if($tag0<0)
  $tags->calcPoints($curUID);
?>