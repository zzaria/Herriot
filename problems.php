<?php 
include("includes/header.php");
$page=isset($_REQUEST['page'])? $_REQUEST['page']:1;
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
		<button class="btn btn-dark" onclick="window.location.search=''">Reset</button>
	</div>
	</div>
	<div class="col-md-9 col-12">
	<div class="column">
		<input class="btn btn-light" id="add_problem" type="submit" onclick="addProblem()" value="Add Problem">
		<span class="text-muted float-end " id="pcount"><span class="placeholder" style="width:100px"></span></span>
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
			<tbody class="problems">
			</tbody>
		</table>
        <div class="loading spinner-border"></div>
		<nav>
			<ul class="pagination" id="problemPages"></ul>
		</nav>
	</div>
</div></div>
</div>
<script>
function addProblem(){
	$.ajax({
		url: "includes/handlers/edit_problem.php",
		type: "POST",
		cache:false,
		data:{action:'createNew'},
		success: function(response) {
			window.location=response;
		}
	});
}

let amount=50;
let query={amount:amount,mindifficulty:0,maxdifficulty:4000,minquality:0,maxquality:5,tag:-1,search:"",sort:0,order:0};
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
function loadProblems() {
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
			$("#pcount").text("Count: "+count);
			pageMax=Math.ceil(count/amount);
			let params={};
			Object.assign(params,query);
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

function reload(){
	$(".problems").html('');
	loadProblems(); //Load first posts
	loadCount();
	$('#sort'+query['sort']).find("span").text(query['order']==1? '\u25BE':'\u25B4');
}

$(function(){
	if(<?php echo $user['perms']<Constants::EDITOR_PERMS? "true":"false"?>)
		$('#add_problem').hide();
	reload();
});

</script>




	</div>
</body>
</html>