<?php  
include("../../config/config.php");
include("../classes/Tag.php");
include("../classes/Notification.php");
include("../current_user.php");
include("../classes/Constants.php");

$sql=mysqli_prepare($con,"SELECT * FROM tagowners WHERE tag=? AND owner=? AND type=0");
mysqli_stmt_bind_param($sql,"si",$_REQUEST['tag'],$curUID);
mysqli_stmt_execute($sql);
$access_query=mysqli_stmt_get_result($sql);
//$access_query= mysqli_query($con, "SELECT * FROM tagowners WHERE tag={$_REQUEST['tag']} AND owner={$curUID}");
if(mysqli_num_rows($access_query) == 0 && $user['perms']<Constants::EDITOR_PERMS) {
    echo "Tag unavailable";
    exit;
}

$tags = new Tag($con);
switch($_REQUEST['action']){
    case 'share':
        $user2=$_REQUEST['user'];
        $value=$_REQUEST['value'];
        if($value==-1){
            $tags->unshareTag($_REQUEST['tag'],$user2,1);
            break;
        }
        //$user2=mysqli_fetch_array(mysqli_query($con,"SELECT id FROM users WHERE username='{$_REQUEST['name']}'"))['id'];
        $tags->shareTag($_REQUEST['tag'],$user2,$value);
        if($curUID!=$user2){
            $notifications=new Notification($con);
            $notifications->insertNotification($curUID,$user2,"tag.php?id=".$_REQUEST['tag'],"sharetag");
        }
        break;
    case 'leave':
        $tags->unshareTag($_REQUEST['tag'],$_REQUEST['user']);
        break;
    case 'copy':
        $tags->copyTag($_REQUEST['tag'],$_REQUEST['user']);
        break;
    case 'edit':
        if(!in_array($_REQUEST['field'],array("name","background","thumbnail","banner","spoiler")))
            exit;
        $tags->editTag($_REQUEST['tag'],$_REQUEST['field'],htmlspecialchars($_REQUEST['value']));
        break;
    case 'reorder':
        $tags->reorderTag($_REQUEST['tag'],$_REQUEST['newOrder']);
        break;
}

if($user['perms']<Constants::EDITOR_PERMS) {
    echo "No perms";
    exit;
}


switch($_REQUEST['action']){
    case 'public':
        $tags->shareTag($_REQUEST['tag'],0,0);
        break;
    case 'unpublic':
        $tags->unshareTag($_REQUEST['tag'],0);
        break;
}

if($user['perms']<Constants::ADMIN_PERMS){
    echo "No perms";
    exit;
}

switch($_REQUEST['action']){
    case 'delete':
        $tags->deleteTag($_REQUEST['tag']);
        break;
    case 'merge':
        $tags->mergeTag($_REQUEST['tag'],$_REQUEST['value']);
        echo 'a';
        break;
}

?>