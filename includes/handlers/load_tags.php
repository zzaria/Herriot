<?php  
include("../../config/config.php");
include("../classes/Tag.php");
include("../current_user.php");

$tags = new Tag($con);
$owner=$_REQUEST['owner'];
if($owner=="public"){
    $tags->getTags($_REQUEST['problem'],0,$_REQUEST['type']);
}
else if($owner=="personal"){
    $tags->getTags($_REQUEST['problem'],$curUID,$_REQUEST['type']);
}
?>