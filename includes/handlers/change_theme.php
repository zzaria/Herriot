<?php  
include("../../config/config.php");
include("../current_user.php");

$theme=(int)$_REQUEST['theme'];
mysqli_query($con,"UPDATE users SET theme=$theme WHERE id=$curUID");
?>