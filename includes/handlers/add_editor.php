<?php  
include("../../config/config.php");
include("../current_user.php");
include("../classes/Notification.php");
include("../classes/Constants.php");
$verifyString="herriot-user-".md5("herriot-user-".$curUID);
switch($_REQUEST['method']) {
    case 'codeforces':
        $url="https://codeforces.com/api/user.info?handles=".$_REQUEST['username'];
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        $data=json_decode($output,true);
        $maxrating=$data['result'][0]['maxRating'];
        $firstname=$data['result'][0]['firstName'];
        if($firstname==$verifyString&&$maxrating>=Constants::EDITOR_RATING_CF){
            mysqli_query($con,"UPDATE users SET perms=1 WHERE id=$curUID AND perms=0");
            $notifs=New Notification($con);
            $notifs->insertNotification(0,$curUID,"editor.php","You can now edit problems");
            echo 'success';
            exit;
        }
        echo 'failed';
        break;
    case 'manual':
        $pendingRequests=mysqli_query($con,"SELECT * FROM editor_waitlist WHERE user=$curUID AND closed=0");
        if(mysqli_num_rows($pendingRequests)<2){
            $message=htmlspecialchars($_REQUEST['message']);
            $sql=mysqli_prepare($con,"INSERT INTO editor_waitlist VALUES (NULL,?,?,0)");
            mysqli_stmt_bind_param($sql,"is",$curUID,$message);
            mysqli_stmt_execute($sql);
            $query=mysqli_stmt_get_result($sql);
        }
        echo 'success';
        break;
    case 'manualAccept':
        if($user['perms']<Constants::ADMIN_PERMS){
            echo 'failed';
            exit;
        }
        $_REQUEST['id']=(int)$_REQUEST['id'];
        $_REQUEST['user']=(int)$_REQUEST['user'];
        mysqli_query($con,"UPDATE editor_waitlist SET closed=1 WHERE id={$_REQUEST['id']}");
        mysqli_query($con,"UPDATE users SET perms=1 WHERE id={$_REQUEST['user']} AND perms=0");
        $notifs=New Notification($con);
        $notifs->insertNotification(0,$_REQUEST['user'],"editor.php","You can now edit problems");
        echo 'success';
        break;
    case 'addAdmin':
        if($user['perms']<Constants::ADMIN_PERMS){
            echo 'failed';
            exit;
        }
        $user=(int)$_REQUEST['user'];
        $val=Constants::ADMIN_PERMS;
        mysqli_query($con,"UPDATE users SET perms={$val} WHERE id={$user}");
        echo 'success';
        break;
} 
?>