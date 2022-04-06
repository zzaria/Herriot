<?php 
include("config/config.php");
include("includes/current_user.php");
if($user['perms']<2){
	die;
}
$query = mysqli_query($con,"SELECT name FROM problems WHERE deleted=0");
foreach($query as $key=>$row){
	$id=strstr($row['name'], ' ', true);
	$current[$id]=1;
}

$url="https://codeforces.com/api/problemset.problems";
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$output = curl_exec($ch);
$data=json_decode($output,true);
$data=$data['result']['problems'];
foreach($data as $key=>$row){
	$contest=$row['contestId'];
	$index=$row['index'];
	$id="CF".$contest.strtolower($index);
	if(!isset($current[$id])){
		$name=$id." ".$row['name'];
		$location="https://codeforces.com/contest/{$contest}/problem/{$index}";
		$link="<a href='$location'>$location</a>";
		$location="https://codeforces.com/contest/{$contest}/status/{$index}";
		$code="<a href='$location'>$location</a>";
		$name=mysqli_real_escape_string($con,$name);
		$link=mysqli_real_escape_string($con,$link);
		$code=mysqli_real_escape_string($con,$code);
		$rating=0;
		if(isset($row['rating']))
			$rating=$row['rating'];
		$a=mysqli_query($con, "INSERT INTO problems VALUES (NULL,'$name','','','',$rating,0,'$link','$link','$link','$code','',1,1,0,1,0,0)");
		echo (int)$a." ".$id." ".$name;
		echo "<br>";
	}}
?>
