<?php  
$message="";
$clearPost=false;
if(isset($_POST['update_details'])) {
	$clearPost=true;
	$email = $_POST['email'];
	$email=strtolower(strip_tags($email));
	$username = $_POST['username'];
	$username=trim(strtolower(strip_tags($username)));
	$profile_pic = $_POST['profile_pic'];
	$profile_pic=htmlspecialchars($profile_pic);

    $sql=mysqli_prepare($con, "SELECT id FROM users WHERE email=?");
    mysqli_stmt_bind_param($sql,"s",$email);
    mysqli_stmt_execute($sql);
    $same_email=mysqli_stmt_get_result($sql);
    $sql=mysqli_prepare($con, "SELECT id FROM users WHERE username=?");
    mysqli_stmt_bind_param($sql,"s",$username);
    mysqli_stmt_execute($sql);
    $same_username=mysqli_stmt_get_result($sql);

	if(!filter_var($email, FILTER_VALIDATE_EMAIL))
		$message.="Invalid email format";
	if(strlen($username)>100)
		$message.="Username must be 100 characters or less";
	else if(strlen($username)==0)
		$message.="Invalid Username";
	$check = mysqli_fetch_array($same_email);
	if($check&&$check['id'] != $curUID)
		$message.= "That email is already in use!<br><br>";
	$check = mysqli_fetch_array($same_username);
	if($check&&$check['id'] != $curUID)
		$message.= "That username is already in use!<br><br>";
	if($message==""){
		$message = "Details updated!<br><br>";
		//$query = mysqli_query($con, "UPDATE users SET username='$username', profile_pic='$profile_pic', email='$email' WHERE username='$userLoggedIn'");
		$sql=mysqli_prepare($con, "UPDATE users SET username=?, profile_pic=?, email=? WHERE id=?");
		mysqli_stmt_bind_param($sql,"sssi",$username,$profile_pic,$email,$curUID);
		mysqli_stmt_execute($sql);
		$same_emails=mysqli_stmt_get_result($sql);
	}
}

//**************************************************
$password_message="";
if(isset($_POST['update_password'])) {
	$clearPost=true;
	$old_password = $_POST['old_password'];
	$new_password_1 = $_POST['new_password_1'];
	$new_password_2 = $_POST['new_password_2'];

	$password_query = mysqli_query($con, "SELECT password FROM users WHERE id='$curUID'");
	$row = mysqli_fetch_array($password_query);
	$db_password = $row['password'];

	if(!password_verify($old_password,$db_password))
		$password_message = "The old password is incorrect! <br><br>";
	else if($new_password_1 != $new_password_2)
		$password_message = "Your two new passwords need to match!<br><br>";
	else{
		$password=password_hash($new_password_1,PASSWORD_DEFAULT);
		$password_query = mysqli_query($con, "UPDATE users SET password='$password' WHERE id='$curUID'");
		$password_message = "Password has been changed!<br><br>";
	}
}

if(isset($_POST['close_account'])) {
	header("Location: close_account.php");
	exit;
}


?>