<?php  

if (isset($_SESSION['curUID'])) {
	$curUID=$_SESSION['curUID'];
	$user_details_query = mysqli_query($con, "SELECT * FROM users WHERE id='$curUID'");
	$user = mysqli_fetch_array($user_details_query);
	$userLoggedIn = $user['username'];
}
else {
	header("Location: register.php");
	exit;
}

?>