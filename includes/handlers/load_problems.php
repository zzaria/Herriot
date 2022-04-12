<?php  
include("../../config/config.php");
include("../classes/Problem.php");
include("../current_user.php");
include("../../assets/php/color-converter/Color.php");

$problems = new Problem($con);
$problems->loadProblems($_REQUEST,$_SESSION['curUID']);
?>