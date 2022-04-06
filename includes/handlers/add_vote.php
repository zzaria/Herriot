<?php  
include("../../config/config.php");
include("../classes/Vote.php");
include("../classes/Notification.php");
include("../classes/Constants.php");
include("../current_user.php");

$votes = new Vote($con);
$value=$_REQUEST['value'];
$type=$_REQUEST['type'];
$id=(int)$_REQUEST['id'];
switch($type){
    case 0:
        if($user['perms']<Constants::EDITOR_PERMS)
            exit;
        if($value=="")
            $votes->deleteVote($id,$type,$curUID);
        else if(1<=$value&&$value<=5)
            $votes->addVote($id,$type,$curUID,$value);
        else
            exit;
        $problem=mysqli_fetch_array(mysqli_query($con, "SELECT * FROM problems WHERE deleted=0 AND id={$id}"));
        if($problem['quality_lock']==0){
            $val=round($votes->getQuality($id),2);
            echo $val;
            mysqli_query($con,"UPDATE problems SET quality={$val} WHERE id={$id}"); 
        }
        else{
            echo $problem['quality'];
        }
        break;
    case 1:
        if($user['perms']<Constants::EDITOR_PERMS)
            exit;
        if($value=="")
            $votes->deleteVote($id,$type,$curUID);
        else if(0<=$value&&$value<=4000)
            $votes->addVote($id,$type,$curUID,$value);
        else
            exit;
        $problem=mysqli_fetch_array(mysqli_query($con, "SELECT * FROM problems WHERE deleted=0 AND id={$id}"));
        if($problem['difficulty_lock']==0){
            $val=$votes->getDifficulty($id);
            echo $val;
            mysqli_query($con,"UPDATE problems SET difficulty={$val} WHERE id={$id}"); 
        }
        else{
            echo $problem['difficulty'];
        }
        break;
    case 2:
        if($value<-1||$value>1)
            exit;
        $votes->addVote($id,$type,$curUID,$value);
        $val=$votes->getLikes($id);
        echo $val;
        mysqli_query($con,"UPDATE posts SET likes={$val} WHERE id={$id}");
        $parentAuthor=mysqli_fetch_array(mysqli_query($con,"SELECT author FROM posts WHERE id={$id}"))['author'];
        if($value==1&&$curUID!=$parentAuthor){
            $notifications=new Notification($con);
            $notifications->insertNotification($curUID,$parentAuthor,"post.php?id=".$id,"like");
        }
        break;
}
?>