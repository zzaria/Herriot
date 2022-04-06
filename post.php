<?php  
include("includes/header.php");

if(isset($_GET['id'])) {
	$id = $_GET['id'];
}
else {
	$id = 0;
}

include("includes/handlers/add_post.php");
?>
<div class="row">
<div class="col-4"></div>
<div class="col-8">
	<div class="column">

		<div class="posts_area">

			<?php 
				$post = new Post($con);
				$post->getSinglePost($id,$curUID,$user['perms']);
			?>

		</div>

	</div>
</div></div>

<script>
window.history.replaceState( null, null, window.location.href );
function replyPost(id){
	$('#commentForm'+id).show();
}
</script>