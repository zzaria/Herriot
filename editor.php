<?php 
include("includes/header.php");
$verifyString="herriot-user-".md5("herriot-user-".$curUID);
?>
<div class="row">
<div class="col-lg-4 col-0"></div>
<div class="col-lg-8 col-12">
<div class="column">
	<div id="alert">
		<div class="alert alert-success alert-dismissible fade show" role="alert" id="success_alert">
			Success
			<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
		</div>
		<div class="alert alert-danger alert-dismissible fade show" role="alert" id="failed_alert">
			Failed
			<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
		</div>
	</div>
	<h1 class="display-2">Become an Editor</h1>
	<p class="lead">You can edit problems if you have one of the criteria below.</p>
	<p>Currently, the options available are:</p>
	<ul>
		<li>Have at least <?php echo Constants::EDITOR_RATING_CF?> max rating on codeforces</li>
	</ul>
	
	<h1 class="h4">Register with codeforces</h1>
	<div>
		<div class="form-text">
			Set your First Name (english) to 
			<span class="toast position-absolute" data-bs-delay="500" style="width:auto; top:200px; right:30%">
				<span class="toast-body">
				Copied
				</span>
			</span>
			<code onclick="copyCode(this)" data-bs-toggle="tooltip" data-bs-original-title="Click to Copy"><?php echo $verifyString ?></code> at 
			<a href="https://codeforces.com/settings/social">https://codeforces.com/settings/social</a>.
			You need at least <?php echo Constants::EDITOR_RATING_CF?> rating.
		</div>
		<div class="input-group">
			<input type="text" class="form-control" id="cfname" name="cfname" placeholder="Codeforces Username">
			<input type="submit" class="btn btn-outline-light" data-toggle="modal" value="Check" onclick="checkCf()">
		</div>
	</div>
	<h1 class="h4">Apply Manually</h1>
	<div>
		<div class="form-text">Add your name to the waiting list, if an admin knows you they can add you.</div>
		<textarea id="message" placeholder="message (optional)" class="form-control"></textarea>
		<div><input type="submit" class="btn btn-primary" value="Apply" onclick="manualApply()"></div>
		<table class="table" id="waitlist">
			<thead>
				<tr>
					<th>Name</th>
					<th>Message</th>
				</tr>
			</thead>
			<tbody>
<?php
if($user['perms']>=Constants::ADMIN_PERMS){
	$waitlist=mysqli_query($con,"SELECT username,editor_waitlist.id,users.id user,message
	FROM editor_waitlist INNER JOIN users ON editor_waitlist.user=users.id WHERE closed=0");
	while($row = mysqli_fetch_array($waitlist)){
		$name=$row['username'];
		echo "<tr><td><a href='profile.php?user={$row['user']}'>{$name}</a></td>
			<td>{$row['message']}</td>
			<td onclick='accept({$row['id']},{$row['user']})'>Accept</td>
			<td onclick='accept({$row['id']},0)'>Close</td></tr>";
	}
}

?>
			</tbody>
		</table>
		</div>
	</div>
</div>
</div></div>
</div>

<script>
function copyCode(e){
	navigator.clipboard.writeText(e.textContent);
	$('.toast').toast('show');
	//$('.toast').attr('style','display: inline !important;');
}
let successAlert,failedAlert;
$(function(){
	var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
	var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
	return new bootstrap.Tooltip(tooltipTriggerEl)
	})

	successAlert=$('#success_alert')[0].outerHTML;
	failedAlert=$('#failed_alert')[0].outerHTML;
	$('#alert').empty();
	if(<?php echo $user['perms']<Constants::ADMIN_PERMS? "true":"false"?>)
		$('#waitlist').hide();
});

function checkCf(){
	let username=$('#cfname').val();
    $.ajax({
        url: "includes/handlers/add_editor.php",
        type: "POST",
        data: {username:username,method:"codeforces"},
        cache:false,

        success: function(response) {
			if(response=="success"){
				$('#alert').html(successAlert);
			}
			else{
				$('#alert').html(failedAlert);
			}
        }
    });
}
function manualApply(){
	let message=$('#message').val();
    $.ajax({
        url: "includes/handlers/add_editor.php",
        type: "POST",
        data: {message:message,method:"manual"},
        cache:false,
        success: function(response) {
			$('#message').val('');
			$('#alert').html(successAlert);
        }
    });
}
function accept(id,user){
    $.ajax({
        url: "includes/handlers/add_editor.php",
        type: "POST",
        data: {id:id,user:user,method:"manualAccept"},
        cache:false,
        success: function(response) {
			location.reload();
        }
    });
}
</script>
</body>
</html>