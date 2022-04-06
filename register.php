<?php  
require 'config/config.php';
require 'includes/handlers/register_handler.php';
?>

<html>
<head>
	<title></title>
	<link rel="stylesheet" type="text/css" href="assets/css/style.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
	<script src="assets/js/register.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
	<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
	<link rel="stylesheet" type="text/css" href="assets/css/bootstrap.css">
</head>
<body>
<div class="wrapper">
<div class="row g-4">
<div class="col-lg-6 col-12">
<div class="column">
    <ul class="nav nav-tabs" id="myTab" role="tablist">
      <li class="nav-item" role="presentation">
        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#register_div" type="button" role="tab" aria-controls="register_div" aria-selected="true">Register</button>
      </li>
      <li class="nav-item" role="presentation">
        <button id="commenttab" class="nav-link active" data-bs-toggle="tab" data-bs-target="#login_div" type="button" role="tab" aria-controls="login_div" aria-selected="false">Login</button>
      </li>
    </ul>

    <div class="tab-content">

      <div role="tabpanel" class="tab-pane" id="register_div">
            <form action="register.php" method="POST">
                <input class="form-control" type="text" name="registration_username" placeholder="Username" value="<?php 
                if(isset($_SESSION['usernametemp'])) {
                    echo $_SESSION['usernametemp'];
                } 
                ?>" required>
                <input class="form-control" type="password" name="registration_password" placeholder="Password" required>
                <input class="form-control" type="password" name="registration_password2" placeholder="Password Confirmation" required>
                <input class="form-control" type="email" name="registration_email" placeholder="Email" value="<?php 
                if(isset($_SESSION['email'])) {
                    echo $_SESSION['email'];
                } 
                ?>" required>
                <?php
                    foreach($errors as $error){
                        echo $error;
                    }
                ?>
                <br>
                <input class="btn btn-primary" type="submit" name="register_button" value="Register">
            </form>
      </div>


      <div role="tabpanel" class="tab-pane active" id="login_div">
            <form action="register.php" method="POST">
                <input class="form-control" type="text" name="login_username" placeholder="Username" value="<?php 
                if(isset($_SESSION['usernametemp'])) {
                    echo $_SESSION['usernametemp'];
                } 
                ?>" required>
                <input class="form-control" type="password" name="login_password" placeholder="Password" required>
                <?php
                    foreach($errors as $error){
                        echo $error;
                    }
                ?>
                <br>
                <input class="btn btn-primary" type="submit" name="login_button" value="Login">
            </form>
      </div>


    </div>
</div></div></div></div>
</body>
</html>