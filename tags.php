<?php 
include("includes/header.php");
?>

<div class="row">
<div class="col-lg-4 col-0"></div>
<div class="col-lg-8 col-12">
	<div class="column">
		<table class="table">
			<thead>
				<tr>
					<th>Personal Tags</th>
				</tr>
			</thead>
			<tbody class="personal_tags"></tbody>
		</table>
		<input type="text" onchange="newTag(this.value)" placeholder="Create new tag" class="form-control">
		<table class="table">
			<thead>
				<tr>
					<th>Shared Tags</th>
				</tr>
			</thead>
			<tbody class="shared_tags"></tbody>
		</table>
		<table class="table">
			<thead>
				<tr>
					<th>Public Tags</th>
				</tr>
			</thead>
			<tbody class="public_tags"></tbody>
		</table>

	</div>
</div></div>

<script>
function loadTags(){
	$.ajax({
		url: "includes/handlers/load_tags.php",
		type: "POST",
		data: {problem:-1,owner:"public",type:3,spoiler:0},
		cache:false,

		success: function(response) {
			$(".public_tags").html(response);
		}
	});
	$.ajax({
		url: "includes/handlers/load_tags.php",
		type: "POST",
		data: {problem:-1,owner:"personal",type:5},
		cache:false,

		success: function(response) {
			console.log(response);
			$(".shared_tags").html(response);
		}
	});
	$.ajax({
		url: "includes/handlers/load_tags.php",
		type: "POST",
		data: {problem:-1,owner:"personal",type:3},
		cache:false,

		success: function(response) {
			$(".personal_tags").html(response);
		}
	});
}
function newTag(name){
	if(name=="")
		return;
	if(name.length>64){
		window.alert("Name must be fewer than 64 characters");
		return;
	}
	let user=<?php echo $curUID?>;
	$.ajax({
		url: "includes/handlers/create_tag.php",
		type: "POST",
		data: {name:name,user:user},
		cache:false,

		success: function(response) {
			//console.log(response);
			loadTags();
		}
	});
}

$(function(){
	loadTags();
});

</script>




	</div>
</body>
</html>