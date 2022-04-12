<?php 
include("includes/header.php");

if($user['perms']<Constants::ADMIN_PERMS) {
	exit();
}
?>
<div class="row">
<div class="col-lg-4 col-0"></div>
<div class="col-lg-8 col-12">
<div class="column">
	<div class="input-group">
		<input type="text" class="form-control" id="addAdmin" placeholder="User Id">
		<input type="submit" class="btn btn-outline-light" data-toggle="modal" value="Add Administrator" onclick="addAdmin()">
	</div>
</div>
</div></div>
</div>

<script>
function addAdmin(){
	let user=$('#addAdmin').val();
    $.ajax({
        url: "includes/handlers/add_editor.php",
        type: "POST",
        data: {user:user,method:"addAdmin"},
        cache:false,
        success: function(response) {
			console.log(response);
        }
    });
}
</script>
</body>
</html>