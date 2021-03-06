<?php 
include("config/config.php");
include("includes/current_user.php");
include("includes/classes/Constants.php");
include("includes/classes/Tag.php");
if($user['perms']<Constants::ADMIN_PERMS){
	die;
}
$tags=new Tag($con);
$query = mysqli_query($con,"SELECT name FROM problems WHERE deleted=0");
foreach($query as $key=>$row){
	$id=strstr($row['name'], ' ', true);
	$current[$id]=1;
}

$url="https://raw.githubusercontent.com/CodeTiger927/USACO-Rating/main/backend/problems.txt";
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$output = curl_exec($ch);
$data=explode("\n",$output);
foreach($data as $key=>$row){
	$info=explode("|",$row);
	$name="USACO";
	$info[0]=explode(" ",$info[0]);
	$name.=$info[0][0];
	$index=($key%3+1);
	$link2="http://usaco.org/index.php?page=".strtolower($info[0][1]).substr($info[0][0],-2)."results";
	$link2="<a href='$link2'>$link2</a>";
	switch($info[0][1]){
		case "Jan":
			$name.="01";
			break;
		case "Feb":
			$name.="02";
			break;
		case "Open":
			$name.="03";
			break;
		case "Dec":
			$name.="12";
			break;
	}
	switch($info[3]){
		case 0:
			$name.="p";
			$tag=865;
			break;
		case 1:
			$name.="g";
			$tag=864;
			break;
		case 2:
			$name.="s";
			$tag=863;
			break;
		case 3:
			$name.="b";
			$tag=862;
			break;
	}
	$name.=$index." ".$info[1];
	$link=$info[2];
	$link="<a href='$link'>$link</a>";
	//echo $name," ",$link," ",$link2,"<br>";
	continue;
	$sql = $con->prepare("SELECT id FROM problems WHERE name LIKE ?");
	$sql->bind_param("s",$name);
	$sql->execute();
	$id=mysqli_fetch_array($sql->get_result())['id'];
	echo $name." ".$id." ".$tag."<br>";
	//break;
	$tags->addProblemTag($id,$tag);	
	//$sql = mysqli_prepare($con, "INSERT INTO problems VALUES (NULL,?,'','','',0,0,?,?,?,?,?,1,1,1,0,0,0)");
	//mysqli_stmt_bind_param($sql, "ssssss",$name,$link,$link,$link2,$link2,$link2);
	//mysqli_stmt_execute($sql);
}
echo "Done";
?>
