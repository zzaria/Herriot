<?php 
include("includes/header.php");
//header("Location: problems.php"); exit;
$post=new Post($con);
$problem=new Problem($con);
include("includes/handlers/add_post.php");
?>

<div class="row">
	<div class="col-6"><div class="column">
		<h1>Announcements</h1>
		<div>
			<form id="announcementform" method='POST' enctype='multipart/form-data'>
				<textarea class='form-control' name='post_text' id='post_text'></textarea>
				<input type='hidden' name='problem' value="0">
				<input type='submit' name='post' id='post_button' value='Post'>
				<input type='hidden' name='parent' value='0'>
				<hr>
			</form>
			<?php $post->loadPosts(array("amount"=>10,"page"=>1,"problem"=>0,"user"=>$curUID,"perms"=>$user['perms']),0,false)?>
		</div>
	</div></div>
	<div class="col-6"><div class="row">
		<div class="col-12"><div class="side_column column">
			<h1>Featured</h1>
		</div></div>
		<?php $problem->loadProblems(array("amount"=>100,"page"=>1,"mindifficulty"=>0,"maxdifficulty"=>4000,"minquality"=>0,"maxquality"=>5,"search"=>"","tag"=>138,"sort"=>0,"order"=>0),0,1) ?>
		<div class="col-12"><div class="side_column column">
			<h1>New</h1>
		</div></div>
		<?php $problem->loadProblems(array("amount"=>4,"page"=>1,"mindifficulty"=>0,"maxdifficulty"=>4000,"minquality"=>0,"maxquality"=>5,"search"=>"","tag"=>-1,"sort"=>0,"order"=>1),0,1) ?>
		<div class="col-12"><div class="side_column column">
			<h1>Recommended</h1>
		</div></div>
		<?php $problem->loadProblems(array("amount"=>8,"page"=>1,"mindifficulty"=>0,"maxdifficulty"=>4000,"minquality"=>0,"maxquality"=>5,"search"=>"","tag"=>-1,"sort"=>4,"order"=>0),0,1) ?>
		<div class="col-6"><div class="side_column column">
			<h1>Recent Actions</h1>
			<div>
				<?php 
				echo $post->recentActions();
				?>
			</div>
		</div></div>
		<div class="col-6"><div class="side_column column">
			<a href="profile.php?user=<?php echo $curUID; ?>">  <img class="small_profile_pic" src="<?php echo $user['profile_pic']; ?>"> </a>

			<div class="user_details_left_right">
				<a href="profile.php?user=<?php echo $curUID; ?>">
				<?php echo $user['username'];?>
				</a>
				<br>
				<?php echo $problem->ratingCircle($user['power'], $user['power']) ?>
			</div>
		</div></div>
	</div></div>
</div>



<script>
window.history.replaceState( null, null, window.location.href );
if(<?php echo $user['perms']<Constants::ADMIN_PERMS? "true":"false"?>){
	$('#announcementform').hide();
}

function replyPost(id){
	location.href="post.php?id="+id;
}

if(window.innerWidth<1600){
	$(".wrapper").css('width','100%');
}
</script>
</div>
</body>
</html>