<?php  
include("../../config/config.php");
include("../classes/Tag.php");
include("../current_user.php");

include("../../assets/php/color-converter/Color.php");

$tags = new Tag($con);
$owner=$_REQUEST['owner'];
if($owner=="public"){
    $tags->getTags($_REQUEST['problem'],0,$_REQUEST['type'],$_REQUEST['spoiler']);
}
else if($owner=="personal"){
    $spoiler=0;
    if(isset($_REQUEST['spoiler']))
        $spoiler=$_REQUEST['spoiler'];
    $tags->getTags($_REQUEST['problem'],$curUID,$_REQUEST['type'],$spoiler);
}
?>