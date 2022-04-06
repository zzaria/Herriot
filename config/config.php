<?php
ob_start(); //Turns on output buffering 
session_start();

$timezone = date_default_timezone_set("Europe/London");


$con = mysqli_init();

//**/mysqli_real_connect($con,"localhost","root","","herriot");/*
mysqli_real_connect($con,$_ENV["HERRIOT_HOSTNAME"],$_ENV["HERRIOT_USERNAME"],$_ENV["HERRIOT_PASSWORD"],$_ENV["HERRIOT_HOSTNAME"]);//*/
mysqli_set_charset($con, 'utf8mb4');

if(mysqli_connect_errno()) 
{
	echo "Failed to connect database: " .mysqli_connect_errno()." - ". mysqli_connect_error();
	die;
}

?>