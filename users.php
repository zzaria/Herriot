<?php 
include("includes/header.php");
?>

<div class="row">
<div class="col-lg-4 col-0"></div>
<div class="col-lg-8 col-12">
	<div class="column">
		<input name="search" class="form-control" id="search" type="text" placeholder="search" onkeyup="updateSearch(this.name,this.value)">
		<table class="table">
			<thead>
				<tr>
					<th onclick="sortBy(0)">#</th>
					<th onclick="sortBy(1)">Users</th>
					<th onclick="sortBy(2)">Power</th>
					<th onclick="sortBy(3)">Level</th>
					<th onclick="sortBy(4)">Solved</th>
				</tr>
			</thead>
			<tbody class="users"></tbody>
		</table>
        <div id="loading" class="spinner-border"></div>
		<p id="pbottom">End</p>
	</div>
</div></div>
<script>

let amount=100;
let query={amount:amount,search:"",sort:0,option:0};
function sortBy(sort){
	updateSearch('sort',sort);
}
function updateSearch(key,value){
	query[key]=value;
	reload();
}

let inProgress = false;
function loadUsers() {
	if(inProgress) { //If it is already in the process of loading some posts, just return
		return;
	}
	
	inProgress = true;
	$('#loading').show();

	let page = $('.users').find('.nextPage').val() || 1; //If .nextPage couldn't be found, it must not be on the page yet (it must be the first time loading posts), so use the value '1'
	query['page']=page;
	$.ajax({
		url: "includes/handlers/load_users.php",
		type: "POST",
		data: query,
		cache:false,

		success: function(response) {
			$('.users').find('.nextPage').remove(); //Removes current .nextpage 
			$('.users').find('.noMorePosts').remove(); //Removes current .nextpage 

			$('#loading').hide();
			$(".users").append(response);

			inProgress = false;
		}
	});
}

function reload(){
	$(".users").html("");
	loadUsers(); //Load first posts
}

$(function(){

	reload();
    $(window).scroll(function() {
    	let bottomElement = $("#pbottom");
    	let noMorePosts = $('.users').find('.noMorePosts').val();

        // isElementInViewport uses getBoundingClientRect(), which requires the HTML DOM object, not the jQuery object. The jQuery equivalent is using [0] as shown below.
        if (isElementInView(bottomElement[0]) && noMorePosts == 'false') {
            loadUsers();
        }
    });

    //Check if the element is in view
    function isElementInView (el) {
        let rect = el.getBoundingClientRect();

        return (
            rect.top >= 0 &&
            rect.left >= 0 &&
            rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) && //* or $(window).height()
            rect.right <= (window.innerWidth || document.documentElement.clientWidth) //* or $(window).width()
        );
    }
});

</script>




	</div>
</body>
</html>