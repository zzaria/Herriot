<?php 
include("includes/header.php");
include("includes/handlers/settings_handler.php");
?>
<div class="row">
<div class="col-md-4 col-0"></div>
<div class="col-md-8 col-12">
<div class="column">

	<?php
	echo "<img src='" . $user['profile_pic'] ."' class='circle_profile_pic'>";
	?>
	<h1 class="h4">Account Settings</h1>

	<?php
	$user_data_query = mysqli_query($con, "SELECT username, email FROM users WHERE id={$curUID}");
	$row = mysqli_fetch_array($user_data_query);
	$username = $row['username'];
	$email = $row['email'];
	?>

	<form action="settings.php" method="POST">
		Email: <input type="text" name="email" value="<?php echo $email; ?>" class="form-outline"><br>
		Username: <input type="text" name="username" value="<?php echo $username; ?>" class="form-outline"><br>
		Profile Picture: <input type="text" name="profile_pic" value="<?php echo $user['profile_pic']; ?>" class="form-outline"><br>

		<?php echo $message; ?>

		<input type="submit" name="update_details" id="save_details" value="Update Details" class="btn btn-info"><br>
	</form>

	<h1 class="h4">Change Password</h1>
	<form action="settings.php" method="POST">
		Old Password: <input type="password" name="old_password" class="form-outline"><br>
		New Password: <input type="password" name="new_password_1" class="form-outline"><br>
		Repeat New Password: <input type="password" name="new_password_2" class="form-outline"><br>

		<?php echo $password_message; ?>

		<input type="submit" name="update_password" id="save_details" value="Update Password" class="btn btn-info"><br>
	</form>

	<h1 class="h4">Get Editing Permissions</h1>
	<form action="editor.php">
		<input type="submit" class="btn btn-success" value="Apply">
	</form>
	<h1 class="h4">Theme/Background</h1>
	<div class="btn-group" role="group" aria-label="Theme">
		<input type="radio" class="btn-check" name="theme" id="btnradio1" value="0" onchange="updTheme(this.value)">
		<label class="btn btn-outline-primary" for="btnradio1">Default</label>
		<input type="radio" class="btn-check" name="theme" id="btnradio2" value="1" onchange="updTheme(this.value)">
		<label class="btn btn-outline-primary" for="btnradio2">Theme 1</label>
		<input type="radio" class="btn-check" name="theme" id="btnradio3" value="2" onchange="updTheme(this.value)">
		<label class="btn btn-outline-primary" for="btnradio3">Theme 2</label>
		<input type="radio" class="btn-check" name="theme" id="btnradio4" value="3" onchange="updTheme(this.value)">
		<label class="btn btn-outline-primary" for="btnradio4">Theme 3</label>
		<input type="radio" class="btn-check" name="theme" id="btnradio5" value="4" onchange="updTheme(this.value)">
		<label class="btn btn-outline-primary" for="btnradio5">Theme 4</label>
		<input type="radio" class="btn-check" name="theme" id="btnradio6" value="5" onchange="updTheme(this.value)">
		<label class="btn btn-outline-primary" for="btnradio6">Theme 5</label>
		<input type="radio" class="btn-check" name="theme" id="btnradio7" value="6" onchange="updTheme(this.value)">
		<label class="btn btn-outline-primary" for="btnradio7">Theme 6</label>
		<input type="radio" class="btn-check" name="theme" id="btnradio8" value="7" onchange="updTheme(this.value)">
		<label class="btn btn-outline-primary" for="btnradio8">Theme 7</label>
	</div>
	<div class="btn-group" role="group" aria-label="Theme">
		<input type="radio" class="btn-check" name="theme" id="btnradio2" value="100" onchange="updTheme(this.value)">
		<label class="btn btn-outline-primary" for="btnradio2">Classic</label>
		<input type="radio" class="btn-check" name="theme" id="btnradio9" value="101" onchange="updTheme(this.value)">
		<label class="btn btn-outline-primary" for="btnradio9">Light 1</label>
		<input type="radio" class="btn-check" name="theme" id="btnradio10" value="102" onchange="updTheme(this.value)">
		<label class="btn btn-outline-primary" for="btnradio10">Light 2</label>
	</div>

</div>
</div></div>
</div>

<script>
if(<?php echo $clearPost? 'true':'false' ?>){
	window.history.replaceState( null, null, window.location.href );
}
function updTheme(theme){
	$.ajax({
		url: "includes/handlers/change_theme.php",
		type: "POST",
		data: {theme:theme},
		cache:false,

		success: function(response) {
			location.reload();
		}
	});
	
}
</script>
</body>
</html>