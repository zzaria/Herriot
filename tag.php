<?php 
include("includes/header.php");

include("includes/classes/Tag.php");
if(isset($_REQUEST['id'])) {
	$id = (int)$_REQUEST['id'];
	$access_query= mysqli_query($con, "SELECT * FROM tagowners WHERE tag=$id AND (owner={$curUID} OR owner=0)");
	if(mysqli_num_rows($access_query) == 0 && $user['perms']<Constants::EDITOR_PERMS) {
		echo "Tag unavailable";
		exit();
	}

	$query = mysqli_query($con, "SELECT * FROM tags WHERE id=$id");
	$tag = mysqli_fetch_array($query);
	$page=isset($_REQUEST['page'])? $_REQUEST['page']:1;
}
else{
  echo "Tag unavailable";
  exit();
}
$tags=new Tag($con);
?>
<div class="row g-5">
<div class="col-md-3 col-12">
	<div class="search column">
		<div class="form-floating" >
			<input name="search" class="form-control" id="search" type="text" onkeyup="updateSearch(this.name,this.value)" placeholder="search" value="<?php echo isset($_REQUEST['search'])? $_REQUEST['search'] :''?>">
			<label for="search">Search</label>
		</div>
		<div>
			<div class="form-check form-check-inline">
				<label class="form-label" for="editorial">Editorial</label>
				<input name="editorial" class="form-check-input" id="editorial" type="checkbox" onchange="updateSearch(this.name,this.checked)" <?php if(isset($_REQUEST['editorial'])) echo "checked"?>>
			</div>
			<div class="form-check form-check-inline">
				<label class="form-label" for="code">Code</label>
				<input name="code" class="form-check-input" id="code" type="checkbox" onchange="updateSearch(this.name,this.checked)" <?php if(isset($_REQUEST['code'])) echo "checked"?>>
			</div>
			<div class="form-check form-check-inline">
				<label class="form-label" for="data">Data</label>
				<input name="data" class="form-check-input" id="data" type="checkbox" onchange="updateSearch(this.name,this.checked)" <?php if(isset($_REQUEST['data'])) echo "checked"?>>
			</div>
		</div>
		<div class="sliders">
			<label class="form-label" for="mindifficulty">Min Difficulty <span class="form-text" id="mindifficultylabel">0</span></label>
			<input name="mindifficulty" class="form-range" id="mindifficulty" type="range" min="0" max="4000" step="100" onchange ="updateSearch(this.name,this.value)" value=<?php echo isset($_REQUEST['mindifficulty'])? $_REQUEST['mindifficulty'] :0?>>
			<label class="form-label" for="maxdifficulty">Max Difficulty <span class="form-text" id="maxdifficultylabel">4000</span></label>
			<input name="maxdifficulty" class="form-range" id="maxdifficulty" type="range" min="0" max="4000" step="100" onchange="updateSearch(this.name,this.value)" value=<?php echo isset($_REQUEST['maxdifficulty'])? $_REQUEST['maxdifficulty'] :5000?>>
			<label class="form-label" for="minquality">Min Quality <span class="form-text" id="minqualitylabel">0</span></label>
			<input name="minquality" class="form-range" id="minquality" type="range" min="0" max="5" onchange="updateSearch(this.name,this.value)" value=<?php echo isset($_REQUEST['minquality'])? $_REQUEST['minquality'] :0?>>
			<label class="form-label" for="maxquality">Max Quality <span class="form-text" id="maxqualitylabel">5</span></label>
			<input name="maxquality" class="form-range" id="maxquality" type="range" min="0" max="5" onchange="updateSearch(this.name,this.value)" value=<?php echo isset($_REQUEST['maxquality'])? $_REQUEST['maxquality'] :5?>>
		</div>
		<input class="btn btn-primary" type="submit" value="Search" onclick="window.location.search=$.param(query)">
	</div>
</div>
<div class="col-md-9 col-12">
<div class="column">
	<?php
	if(str_starts_with($tag['banner'],"f")){
		$banner=substr($tag['banner'],1);
		echo "<div class='tag-fullbanner'><img src='{$banner}'></div>
		
		<span>{$tag['name']}: <span class='pcount'><span class='placeholder' style='width:100px'></span></span></span>
		<span class='float-end'>{$tags->isPublic($id)}</span>
		";
	}
	else{
		if($tag['banner']=="")
			$background="background-color: ".$tag['background'];
		else
			$background="background-image: url({$tag['banner']})";
		echo "
		<div class='tag-header' style='$background'>
			<img class='tag-thumbnail' src='{$tag['thumbnail']}'>
			<span class='tag-header2'>
				<h1>{$tag['name']}</h1>
				<p>{$tags->isPublic($id)}</p>
				<span class='pcount'><span class='placeholder' style='width:100px'></span></span>
			</span>
		</div>";
	}
	?>
	<table class="table problemlist fw-light">
		<thead>
			<tr>
				<th onclick="sortBy(0)" id="sort0"># <span></span></th>
				<th onclick="sortBy(3)" id="sort3">Name <span></span></th>
				<th onclick="sortBy(1)" id="sort1">Difficulty <span></span></th>
				<th onclick="sortBy(2)" id="sort2">Quality <span></span></th>
			</tr>
		</thead>
		<tbody class="loading placeholder-glow">
			<tr>
				<td><span class="placeholder w-100"></span></td>
				<td><span class="placeholder w-100"></span></td>
				<td><span class="placeholder w-100"></span></td>
				<td><span class="placeholder w-100"></span></td>
			</tr>
			<tr>
				<td><span class="placeholder w-100"></span></td>
			</tr>
		</tbody>
		<tbody class="problems sortable"></tbody>
	</table>
    <div class="loading spinner-border"></div>
	<nav>
		<ul class="pagination" id="problemPages"></ul>
	</nav>
	<div id="edit_options">
		<span class="text-muted" id="owners"><?php echo $tags->getOwners($id) ?></span>
		<input type="submit" class="btn btn-dark float-end" id="editTagButton" data-toggle="modal" class="">
		<div class="input-group">
			<input type="submit" class="btn btn-outline-light" data-toggle="modal" onclick="editTag()" value="Go">
			<select id="editTagOption">
				<option value="name" class="dropdown-item">Rename</option>
				<option value="thumbnail" class="dropdown-item">Thumbnail</option>
				<option value="banner" class="dropdown-item">Banner</option>
			</select>
			<input type="text" class="form-control" id="editTagValue">
		</div>
		<div class="input-group">
			<input type="submit" class="btn btn-outline-light" data-toggle="modal" onclick="editTag('background')" value="Tag Color">
			<input type="text" class="form-control" id="background" value="<?php echo $tag['background']?>">
		</div>
		<div class="input-group">
			<span class="input-group-text">Invite Collaborator</span>
			<select type="text" id="newowner" multiple="multiple"></select>
		</div>
		<div class="input-group">
			<span class="input-group-text">Share Tag</span>
			<select type="text" id="newviewer" multiple="multiple"></select>
		</div>
		<div>
			Shared with:
			<span id="viewers"><?php echo $tags->getViewers($id) ?></span>
		</div>
		<div class="form-check form-switch">
			<input class="form-check-input" type="checkbox" role="switch" id="spoiler" <?php echo $tag['spoiler']? "checked":""?> onchange="editTag('spoiler',Number(this.checked))">
			<label class="form-check-label" for="spoiler">Spoiler</label>
		</div>
		<input type="submit" class="btn btn-danger" data-toggle="modal" onclick="leaveTag()" value="Leave Tag">
		<input type="submit" class="btn btn-secondary" data-toggle="modal" onclick="copyTag()" value="Copy Tag">
		<div id="admin_options" class="float-end">
			<input type="submit" class="btn btn-info" data-toggle="modal" onclick="makePublic()" value="Make Public">
			<input type="submit" class="btn btn-warning" data-toggle="modal" onclick="makePrivate()" value="Make Private">
		</div>
	</div>
</div></div>
</div>

<script>
$(function(){
	reload();
	$.ajax({
		url: "includes/handlers/edit_tag.php",
		type: "POST",
		data: {tag:<?php echo $id ?>,action:'none'},
		cache:false,

		success: function(response) {
			editTagStop();
			if(response=="Tag unavailable"){
				$('#edit_options').hide();
				return;
			}
			if(response=="No perms"){
				$('#admin_options').hide();
				return;
			}
		}
	});
	$('#background').colorPicker();
	
	$('#newowner').select2({
		selectionCssClass:"form-control",
		width:"20%",
		ajax: {
			url: "includes/handlers/load_users.php",
			dataType: 'json',
			data: function(params){
				return {
					option:1,
					amount:100,
					page:1,
					search:params.term?? "",
					sort:0,
					order:0,
				}
			},
			processResults: function(data){
				return {
					results: data,
				};
			}
			// Additional AJAX parameters go here; see the end of this chapter for the full code of this example
		}
	});
	$('#newviewer').on('select2:select', function (e) {
		shareTag(e.params.data['id'],1);
	});
	$('#newviewer').select2({
		selectionCssClass:"form-control",
		width:"20%",
		ajax: {
			url: "includes/handlers/load_users.php",
			dataType: 'json',
			data: function(params){
				return {
					option:1,
					amount:100,
					page:1,
					search:params.term?? "",
					sort:0,
					order:0,
				}
			},
			processResults: function(data){
				return {
					results: data,
				};
			}
			// Additional AJAX parameters go here; see the end of this chapter for the full code of this example
		}
	});
	$('#newowner').on('select2:select', function (e) {
		shareTag(e.params.data['id'],0);
	});
	$(".select2-selection").css('background-color','rgba(255,255,255,0.15)');
	$(".select2-selection").css('border-bottom-left-radius','0');
	$(".select2-selection").css('border-top-left-radius','0');
});
function editTagStart(){
	query={...query0};
	query['amount']=1000000;
	reload(true);
	$('#editTagButton').val("Done");
	$('#editTagButton').attr('onclick',"reload()");
}
function editTagStop(){
	$('.square_button').hide();
	$('#editTagButton').val("Edit Tag");
	$('#editTagButton').attr('onclick',"editTagStart()");
	sortable('.sortable', 'destroy');
}
function copyTag(){
$.ajax({
	url: "includes/handlers/edit_tag.php",
	type: "POST",
	data: {tag:<?php echo $id ?>,user:<?php echo $curUID ?>,action:'copy'},
	cache:false,

	success: function(response) {
	}
});
}
function leaveTag(){
$.ajax({
	url: "includes/handlers/edit_tag.php",
	type: "POST",
	data: {tag:<?php echo $id ?>,user:<?php echo $curUID ?>,action:'leave'},
	cache:false,

	success: function(response) {
		window.location='tags.php';
	}
});
}
function editTag(field=null,value=null){
	if(field===null){
		field=$('#editTagOption').val();
		value=$('#editTagValue').val();
	}
	else if(value===null){
		value=$('#'+field).val();
	}
	if(field==="name"&&value.length>64){
		window.alert("Name must be fewer than 64 characters");
		return;
	}
	$.ajax({
		url: "includes/handlers/edit_tag.php",
		type: "POST",
		data: {tag:<?php echo $id ?>,value:value,field:field,action:'edit'},
		cache:false,
		success: function(response) {
			//console.log(response);
			location.reload();
		}
	});
}
function shareTag(user,value){
$.ajax({
	url: "includes/handlers/edit_tag.php",
	type: "POST",
	data: {tag:<?php echo $id ?>,user:user,value:value,action:'share'},
	cache:false,

	success: function(response) {
		location.reload();
	}
});

}
function deleteTagProblem(problem){
	$.ajax({
        url: "includes/handlers/remove_problemtags.php",
        type: "POST",
        data: {tag:<?php echo $id ?>,problem:problem},
		cache:false,

		success: function(response) {
			reload(true);
		}
	});
}

function makePublic(){
	$.ajax({
		url: "includes/handlers/edit_tag.php",
		type: "POST",
		data: {tag:<?php echo $id ?>,action:'public'},
		cache:false,

		success: function(response) {
			location.reload();
		}
	});
}
function makePrivate(){
	$.ajax({
		url: "includes/handlers/edit_tag.php",
		type: "POST",
		data: {tag:<?php echo $id ?>,action:'unpublic'},
		cache:false,

		success: function(response) {
			location.reload();
		}
	});
}


let amount=50;
let query0={amount:amount,mindifficulty:0,maxdifficulty:4000,minquality:0,maxquality:5,tag:<?php echo $id ?>,id:<?php echo $id ?>,search:"",sort:0,order:0};
let query={...query0};
<?php
	foreach($_REQUEST as $key=>$value){
		echo "query['$key']='$value';";
	}
?>

let searchDelay;
function updateSearch(key,value){
	if(query[key]===value)
		return;
	query[key]=value;
	if(key=='mindifficulty'||key=='maxdifficulty'||key=='minquality'||key=='maxquality'){
		$('#'+key+'label').text(value);
	}
	clearTimeout(searchDelay);
	searchDelay=setTimeout(()=>{
		reload();
	},200);
}
function sortBy(num){
	$('#sort'+query['sort']).find("span").text('');
	updateSearch('sort',num);
	updateSearch('order',1-query['order']);
}

let inProgress = 0;
function loadProblems(edit=false) {
	let id=++inProgress;
	$('.loading').show();

	let page = <?php echo $page?>;
	query['page']=page;
	$.ajax({
		url: "includes/handlers/load_problems.php",
		type: "POST",
		data: query,
		cache:false,

		success: function(response) {
			if(id!=inProgress)
				return;
			$('.problems').find('.nextPage').remove(); //Removes current .nextpage 
			$('.problems').find('.noMorePosts').remove(); //Removes current .nextpage 

			$('.loading').hide();
			$(".problems").append(response);
			if(edit){
				$('.square_button').show();
				sortable('.sortable',{
					forcePlaceholderSize: true,
					placeholderClass: 'ph-class',
				});
				sortable('.sortable')[0].addEventListener('sortupdate', function(e) {
					newOrder=e.detail.destination.items.map(row => row.id);
					$.ajax({
						url: "includes/handlers/edit_tag.php",
						type: "POST",
						data: {tag:<?php echo $id ?>,user:<?php echo $curUID ?>,newOrder:newOrder,action:'reorder'},
						cache:false,
						success: function(response) {
						}
					});
				});
			}
			else
				editTagStop();
		}
	});
}
function loadCount(){
	$.ajax({
		url: "includes/handlers/count_problems.php",
		type: "POST",
		data: query,
		cache:false,

		success: function(count) {
			$(".pcount").text(count+" Problems");
			pageMax=Math.ceil(count/amount);
			let params={};
			Object.assign(params,query);
			params['amount']=amount;
			params['page']=1;
			url=window.location.href.split("?")[0];
			let pageVals=[-4096,-1024,-256,-64,-16,-4,-3,-2,-1,0,1,2,3,4,16,64,256,1024,4096],cur=<?php echo $page?>;
			let pages=`<li class="page-item"><a class="page-link" href="${url}?${$.param(params)}">First</a></li>`;
			pageVals.forEach(pageVal =>{
				let page=cur+pageVal;
				params['page']=page;
				if(page==cur)
					pages+=`<li class="page-item active"><a class="page-link" href="${url}?${$.param(params)}">${page}</a></li>`;
				else if(1<=page&&page<=pageMax){
					pages+=`<li class="page-item"><a class="page-link" href="${url}?${$.param(params)}">${page}</a></li>`;
				}
			})
			params['page']=pageMax;
			pages+=`<li class="page-item"><a class="page-link" href="${url}?${$.param(params)}">Last</a></li>`;
			$('#problemPages').html(pages);
		}
	});
}

function reload(edit=false){
	$(".problems").html("");
	loadProblems(edit); //Load first posts
	loadCount();
	$('#sort'+query['sort']).find("span").text(query['order']==1? '\u25BE':'\u25B4');
}
</script>




	</div>
</body>
</html>