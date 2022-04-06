<?php  
include("../../config/config.php");
include("../classes/Tag.php");
include("../classes/Notification.php");
include("../current_user.php");

$sql=mysqli_prepare($con,"SELECT * FROM tagowners WHERE tag=? AND owner=?");
mysqli_stmt_bind_param($sql,"si",$_REQUEST['tag'],$curUID);
mysqli_stmt_execute($sql);
$access_query=mysqli_stmt_get_result($sql);
//$access_query= mysqli_query($con, "SELECT * FROM tagowners WHERE tag={$_REQUEST['tag']} AND owner={$curUID}");
if(mysqli_num_rows($access_query) == 0 && $user['perms']<1) {
    echo "Tag unavailable";
    exit;
}

$tags = new Tag($con);
switch($_REQUEST['action']){
    case 'share':
        $sql=mysqli_prepare($con, "SELECT id FROM users WHERE username=?");
        mysqli_stmt_bind_param($sql,"s",$_REQUEST['name']);
        mysqli_stmt_execute($sql);
        $user2=mysqli_fetch_array(mysqli_stmt_get_result($sql))['id'];
        //$user2=mysqli_fetch_array(mysqli_query($con,"SELECT id FROM users WHERE username='{$_REQUEST['name']}'"))['id'];
        $tags->shareTag($_REQUEST['tag'],$user2);
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
        if(!in_array($_REQUEST['field'],array("name","background","thumbnail","banner")))
            exit;
        $tags->editTag($_REQUEST['tag'],$_REQUEST['field'],htmlspecialchars($_REQUEST['value']));
        break;
    case 'reorder':
        $tags->reorderTag($_REQUEST['tag'],$_REQUEST['newOrder']);
        break;
}

if($user['perms']<1) {
    echo "No perms";
    exit;
}


switch($_REQUEST['action']){
    case 'public':
        $tags->shareTag($_REQUEST['tag'],0);
        break;
    case 'unpublic':
        $tags->unshareTag($_REQUEST['tag'],0);
        break;
}

if($user['perms']<2){
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