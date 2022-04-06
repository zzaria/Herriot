<?php
include("includes/classes/Tag.php");
//Declaring variables
$username = "";
$email = ""; 
$password = ""; 
$password2 = ""; 
$date = ""; 
$errors = array(); 

if(isset($_POST['register_button'])){
	$username=strtolower(strip_tags($_POST['registration_username']));
	$email=strtolower(strip_tags($_POST['registration_email']));
	$password=$_POST['registration_password'];
	$password2=$_POST['registration_password2'];
	$date=date("Y-m-d");
	$_SESSION['usernametemp'] = $username;
	$_SESSION['email'] = $email;

    $sql=mysqli_prepare($con, "SELECT email FROM users WHERE email=?");
    mysqli_stmt_bind_param($sql,"s",$email);
    mysqli_stmt_execute($sql);
    $same_email=mysqli_stmt_get_result($sql);
    $sql=mysqli_prepare($con, "SELECT email FROM users WHERE username=?");
    mysqli_stmt_bind_param($sql,"s",$username);
    mysqli_stmt_execute($sql);
    $same_username=mysqli_stmt_get_result($sql);
	//validation checks
	if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
		array_push($errors, "Invalid email format");
	}
	else if($same_email->num_rows>0){
		array_push($errors, "Email already exists");
	}
	if(strlen($username)>100){
		array_push($errors,"Username must be 100 characters or less");
	}
	else if($same_username->num_rows>0){
		array_push($errors, "Username already exists");
	}
	if(strlen($password)>255){
		array_push($errors,"Password must be 255 characters or less");
	}
	else if($password!=$password2){
		array_push($errors,"Passwords don't match");
	}

	if(empty($errors)){
		$password=password_hash($password,PASSWORD_DEFAULT);

		//Profile picture assignment
		$arrFiles = scandir('assets/images/profile_pics/defaults/');

		$rand = rand(2, count($arrFiles)-1);
		$profile_pic = "assets/images/profile_pics/defaults/".$arrFiles[$rand];
		
		$sql=mysqli_prepare($con, "INSERT INTO users VALUES (NULL, ?,?,?,?,?,0,0,0,0,0)");
		mysqli_stmt_bind_param($sql,"sssss",$username,$email,$password,$date,$profile_pic);
		mysqli_stmt_execute($sql);
		$query=mysqli_stmt_get_result($sql);
		//$query = mysqli_query($con, "INSERT INTO users VALUES (NULL, '$username', '$email', '$password', '$date', '$profile_pic',0)");
		$user=mysqli_insert_id($con);
		array_push($errors,"Account created");
		$_SESSION['email'] = "";
		$_SESSION['curUID'] = $user;
		$tags = new Tag($con);
		$tags->createTag("To do",$user);
		$tags->createTag("In progress",$user,"rgba(255, 238, 186,0.2)");
		$tags->createTag("Solved-$user",$user,"rgba(48,80,80,0.8)");
		$tags->createTag("Skipped",$user,"rgba(184, 218, 255,0.5)");
		$tags->createTag("Favourites",$user,"rgba(255, 105, 180,0.5)");
		header("Location: index.php");
        exit;
	}
}
?>
<?php  

if(isset($_POST['login_button'])) {

	$username = strtolower($_POST['login_username']);
	$password = $_POST['login_password'];
	$_SESSION['usernametemp'] = $username;
	
    $sql=mysqli_prepare($con, "SELECT id,password FROM users WHERE username=?");
    mysqli_stmt_bind_param($sql,"s",$username);
    mysqli_stmt_execute($sql);
    $query=mysqli_stmt_get_result($sql);
	//$query = mysqli_query($con, "SELECT id,password FROM users WHERE username='$username'");
	if($query->num_rows!=1){
		array_push($errors, "Wrong username");
	}
	else{
		$row = mysqli_fetch_array($query);
		if(password_verify($password,$row['password'])){
			$_SESSION['username'] = $username;
			$_SESSION['curUID'] = $row['id'];
			header("Location: index.php");
			exit;
		}
		else {
			array_push($errors, "Wrong password");
		}
	}
}

?>