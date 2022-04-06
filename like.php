<!DOCTYPE html>
<html>
<head>
	<title></title>
	<link rel="stylesheet" type="text/css" href="assets/css/style.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
</head>
<body>

	<style type="text/css">
	* {
		font-family: Arial, Helvetica, Sans-serif;
	}
	body {
		background-image:none;
		background-color: transparent;
		margin:0;
	}

	</style>
	<?php  
		require 'config/config.php';
		include("includes/classes/Vote.php");
		$vote=new Vote($con);
		$user=$_REQUEST['user'];
		$post=$_REQUEST['post'];
		$numLikes=$_REQUEST['likes'];
		if(isset($_REQUEST['current']))
			$current=$_REQUEST['current'];
		else
			$current=$vote->getVote($post,2,$user);
		if($current==NULL||$current=="none")
			$current=0;
	?>
	<div>
		<input type="submit" class="comment_like" value="<?php echo $current==1? "unlike": "like"?>" onclick="voteComment(<?php echo $current==1? 0:1?>)">
		<input type="submit" class="comment_like" value="<?php echo $current==-1? "undislike": "dislike"?>" onclick="voteComment(<?php echo $current==-1? 0:-1?>)">
		<div class="like_value">
			<?php echo $numLikes ?> Likes
		</div>
	</div>
	<script>
function voteComment(value){
	$.ajax({
		url: "includes/handlers/add_vote.php",
		type: "POST",
		data: {id:<?php echo $post?>,value:value, type:2},
		cache:false,

		success: function(response) {
			window.location="like.php?post=<?php echo $post?>&user=<?php echo $user?>&likes="+response+"&current="+value;
		}
	});
}
	</script>

</body>
</html>