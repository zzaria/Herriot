<?php  
include("../../config/config.php");
include("../classes/Problem.php");
include("../current_user.php");

$problems = new Problem($con);
$problems->countProblems($_REQUEST,$curUID);
?>