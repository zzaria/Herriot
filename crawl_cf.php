<?php 
include("config/config.php");
include("includes/current_user.php");
include("includes/classes/Constants.php");
if($user['perms']<Constants::ADMIN_PERMS){
	die;
}
$query = mysqli_query($con,"SELECT name,id FROM problems WHERE deleted=0");
foreach($query as $key=>$row){
	$id=strstr($row['name'], ' ', true);
	$current[$id]=$row['id'];
}

$url="https://codeforces.com/api/problemset.problems";
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$output = curl_exec($ch);
$data=json_decode($output,true);
$data=$data['result']['problems'];
$query=[];
$cnt=0;
foreach($data as $key=>$row){
	$contest=$row['contestId'];
	$index=$row['index'];
	$id="CF".str_pad($contest,4,"0",STR_PAD_LEFT).strtolower($index);
	$name=$id." ".$row['name'];
	if(!isset($current[$id])){
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
		$query[]="(NULL,'$name','','','',$rating,0,'$link','$link','$link','$code','',1,1,0,1,0,0)";
		$cnt++;
		if($cnt%10==0){
			$query="INSERT INTO problems VALUES ".implode(',',$query);
			$a=mysqli_query($con, $query);
			echo $query." ";
			$query=[];
			echo "<br>";
			$cnt=0;
		}
	}
}
if($cnt%10!=0){
	$query="INSERT INTO problems VALUES ".implode(',',$query);
	$a=mysqli_query($con, $query);
	echo $query." ";
	$query=[];
	echo "<br>";
}
//$a=mysqli_query($con, $query);
echo "Done ".$cnt;
?>
