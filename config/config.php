<?php
ob_start(); //Turns on output buffering 
session_start();

$timezone = date_default_timezone_set("Europe/London");


$con = mysqli_init();

//**/mysqli_real_connect($con,"localhost","root","","herriot");/*
mysqli_real_connect($con,"remotemysql.com","AmqHpz3bzP","dLoDquV3iV","AmqHpz3bzP");//*/
mysqli_set_charset($con, 'utf8mb4');

if(mysqli_connect_errno()) 
{
	echo "Failed to connect database: " .mysqli_connect_errno()." - ". mysqli_connect_error();
	die;
}

?>