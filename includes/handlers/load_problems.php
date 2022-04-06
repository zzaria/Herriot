<?php  
include("../../config/config.php");
include("../classes/Problem.php");
include("../current_user.php");

$problems = new Problem($con);
$problems->loadProblems($_REQUEST,$curUID);
?>