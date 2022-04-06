<?php 
include("includes/header.php");

if(isset($_GET['id'])) {
	$id = (int)$_GET['id'];
  if($id==0){
    header("Location: index.php");
    exit;
  }
	$problem_query = mysqli_query($con, "SELECT * FROM problems WHERE id=$id");
  if(mysqli_num_rows($problem_query) == 0) {
    echo "Problem not found";
    exit();
  }

	$problem = mysqli_fetch_array($problem_query);
  if($problem['deleted']==1&&$user['perms']<1){
    echo "Problem not found";
    exit();
  }
}
else{
  echo "Problem not found";
  exit();
}

include("includes/handlers/add_post.php");
include("includes/classes/Vote.php");
$vote=new Vote($con);
$problems=new Problem($con);
 ?>

 	<style type="text/css">
	 	.wrapper {
      width:80%;
	 	}
    .user_details{
      float:right;
    }
 	</style>

<div class="row g-4">
<div class="col-md-6 col-12">
	<div class="column">
    <ul class="nav nav-tabs" role="tablist">
      <li class="nav-item" role="presentation">
        <div class="problem-tab active" data-bs-toggle="tab" data-bs-target="#info_div" type="button" role="tab" aria-controls="info_div" aria-selected="true">Summary</div>
      </li>
      <li class="nav-item" role="presentation">
        <div id="commenttab" class="problem-tab" data-bs-toggle="tab" data-bs-target="#comments_div" type="button" role="tab" aria-controls="comments_div" aria-selected="false">Comments</div>
      </li>
      <li class="nav-item">
        <a href="problems.php"><div class="problem-tab">Back to Problem List</div></a>
      </li>
    </ul>

    <div class="tab-content">
      <div role="tabpanel" class="tab-pane active" id="info_div">
        <div class="problem_info">
          <div><div class="row g-2">
            <div class="col-12">
              <img class="img-fluid" src="<?php echo $problem['banner']?>">
              <img class="img-thumbnail problem-thumbnail" src="<?php echo $problem['thumbnail']?>">
              <div ondblclick="editProblem('name')">
                <h1 class="display-4" id="name"><?php echo $problem['name']; ?></h1>
              </div>
            </div>
            <div class="col-12" ondblclick="editProblem('statement_links')"><div class="problem-subcolumn">
              <h2>statement</h2>
              <div id="statement_links">
                <?php echo $problem['statement_links']; ?>
              </div>
            </div></div>
            <div class="col-6" ondblclick="editProblem('judge_links')"><div class="problem-subcolumn">
              <h2>judge</h2>
              <div id="judge_links">
                <?php echo $problem['judge_links']; ?>
              </div>
            </div></div>
            <div class="col-6" ondblclick="editProblem('editorial_links')"> <div class="problem-subcolumn">
              <h2>editorial</h2>
              <div id="editorial_links">
                <?php echo $problem['editorial_links']; ?>
              </div>
            </div></div>
            <div class="col-6" ondblclick="editProblem('solution_links')"> <div class="problem-subcolumn">
              <h2>code</h2>
              <div id="solution_links">
                <?php echo $problem['solution_links']; ?>
              </div>
            </div></div>
            <div class="col-6" ondblclick="editProblem('data_links')"> <div class="problem-subcolumn">
              <h2>test data</h2>
              <div id="data_links">
                <?php echo $problem['data_links']; ?>
              </div>
            </div></div>
            <div class="col-12 editonly"> <div class="problem-subcolumn">
            <button class="btn btn-dark float-end" data-bs-toggle="popover" data-bs-trigger="focus" title="Edit Problem" data-bs-content="Double click on a section to edit it.">Edit Problem</button>  
            <div ondblclick="editProblem('thumbnail')">Change Thumbnail: <span id="thumbnail"><?php echo $problem['thumbnail']?></span></div>
            <div ondblclick="editProblem('banner')">Change Banner: <span id="banner"><?php echo $problem['banner']?></span></div>
            </div></div>
            <input class="btn-check editonly" id="delete_problem" type="checkbox" <?php if($problem['deleted']) echo "checked"?> onchange="deleteProblem(this.checked)">
            <label class="btn btn-danger editonly" for="delete_problem"><?php echo $problem['deleted']==0? "Delete": "Restore"?> Problem</label>
          </div></div>
        </div>
      </div>


      <div role="tabpanel" class="tab-pane" id="comments_div">
        <form class="post_form" method="POST" enctype="multipart/form-data">
          <textarea class="form-control" name="post_text" id="post_text" placeholder="Add comment"></textarea>
          <input type="hidden" name="problem" value=<?php echo $problem['id'] ?>>
          <input type="hidden" name="parent" value="0">
          <input type="submit" name="post" id="post_button" value="Post">
          <hr>
        </form>

        <div class="posts_area"></div>   
        <div id="loading" class="spinner-grow"></div>
      </div>


    </div>


	</div>
</div>
<div class="col-md-4 col-12">
<div class="row g-4">
<div class="col-6">
    <div class="row g-4">
      <div class="col-12"><div class="side_column column">
            <div ondblclick="editProblem('quality')">
              <h1 class="inline">Quality:</h1>
              <span id="quality"><?php echo $problem['quality']; ?></span>
            </div>
            <div>
              <label for="quality_vote">Vote:</label>
              <select id="quality_vote">
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
                <option value="5">5</option>
              </select>
            </div>
            <input type="checkbox" id="quality_lock" <?php if($problem['quality_lock']) echo "checked"?>>
        </div></div>
      <div class="col-12"><div class="side_column column">
        <div ondblclick="editProblem('difficulty')">
          <h1 class="inline">Difficulty</h1>
          <span>
            <?php echo $problems->ratingCircle($problem['difficulty'],"<span id=\"difficulty\">{$problem['difficulty']}</span>")?>  
          </span>
        </div>
        <label class="form-label" for="difficulty_vote">Vote: <span class="form-text" id="difficulty_vote_label">None</span></label>
        
        <button type="button" class="btn btn-outline-danger btn-sm" data-mdb-ripple-color="dark" onclick="voteDifficulty('')">
          X
        </button>
        <input class="" id="difficulty_vote" type="range" min="0" max="4000" step="100" onchange ="voteDifficulty(this.value)" <?php if($user['perms']<Constants::EDITOR_PERMS) echo "disabled"?>>
        <input type="checkbox" id="difficulty_lock" <?php if($problem['difficulty_lock']) echo "checked"?>>
      </div></div>
    </div>
</div>
<div class="col-6"><div class="side_column column h-100">
  <a href="profile.php?user=<?php echo $curUID; ?>">  <img class="small_profile_pic" src="<?php echo $user['profile_pic']; ?>"> </a>

  <div class="user_details_left_right">
    <a href="profile.php?user=<?php echo $curUID; ?>">
    <?php echo $user['username'];?>
    </a>
    <br>
    <?php echo $problems->ratingCircle($user['power'], $user['power']) ?>
  </div>
</div></div>
<div class="col-12"><div class="side_column column">
  <h1>Tags</h1>
  <span class="public_tags"><span class="placeholder placeholder-wave w-100"></span></span>
  <span class="editonly" id="newPublicTagDiv">
    <button class='square_button btn-success' onclick="showTagSelect('newPublicTag')">+</button>
    <select id="newPublicTag" placeholder="new tag" onchange="addTag(this.value)"></select>
  </span>
  <h1>Personal tags</h1>
  <span class="user_tags"><span class="placeholder placeholder-wave w-100"></span></span>
  <span id="newUserTagDiv">
    <button class='square_button btn-success' onclick="showTagSelect('newUserTag')">+</button>
    <select id="newUserTag" placeholder="new tag" onchange="addTag(this.value)"></select> 
  </span>
</div></div>
<div class="col-12">
	<div class="side_column column">

		<h1>Recent Actions</h1>

		<div>
			<?php 
      $post=new Post($con);
      echo $post->recentActions();
			?>
		</div>
	</div>
</div></div>
</div></div>
</div>

  <script>
    
if(window.innerWidth<1800){
	$('.col-md-6').addClass('col-md-7');
  $('.col-md-4').addClass('col-md-5');
}
  function showTagSelect(id){
    $('#'+id+'Div').find('.select2').show();
    $('#'+id).select2('open');
  }
  $(function(){
	  window.history.replaceState( null, null, window.location.href );
    loadTags();
    $('#newUserTag').select2({
      width:'50%',
    });
    $('#newUserTag').on('select2:closing',function(e){
          $('.select2').hide();
    });
    $('#newPublicTag').select2({
      width:'50%',
    });
    $('#newPublicTag').on('select2:closing',function(e){
          $('.select2').hide();
    });
    $('.select2').hide();
    $('#quality_vote').barrating({
      theme: 'fontawesome-stars-o',
      allowEmpty: true,
      deselectable: true,
      initialRating: <?php
                      $a=$vote->getVote($problem['id'],0,$curUID);
                      echo $a=="none"? 5:$a;
                    ?>,
      readonly: <?php echo $user['perms']<Constants::EDITOR_PERMS? "true":"false"?>,
      onSelect:function(value){
        $.ajax({
            url: "includes/handlers/add_vote.php",
            type: "POST",
            data: {id:<?php echo $problem['id'] ?>,value:value, type:0},
            cache:false,

            success: function(response) {
              $('#quality').text(response);
            }
        });
      }
    });
    $('.br-theme-fontawesome-stars-o').css('display','inline-block');
    let difficulty_vote="<?php echo $vote->getVote($problem['id'],1,$curUID)?>";
    if(difficulty_vote!="none"){
      $('#difficulty_vote_label').text(difficulty_vote);
      $('#difficulty_vote').val(difficulty_vote);
    }
    lc_switch('#quality_lock',{
      on_txt:'🔒',
      off_txt:'OFF'
    });
    $('#quality_lock').on('lcs-statuschange',function(e){
      lock("quality_lock",e.currentTarget.checked);
    });
    lc_switch('#difficulty_lock',{
      on_txt:'🔒',
      off_txt:'OFF'
    });
    $('#difficulty_lock').on('lcs-statuschange',function(e){
      lock("difficulty_lock",e.currentTarget.checked);
    });

    
    if(<?php echo $user['perms']?> < 1){
      $('.editonly').hide();
      $('.lcs_wrap').hide();
    }
    thumbnail="<?php echo $problem['thumbnail']?>";
    if(thumbnail==="")
      $('.problem-thumbnail').hide();
    var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'))
    var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
      return new bootstrap.Popover(popoverTriggerEl)
    })


  });
  function lock(name,value){
    $.ajax({
        url: "includes/handlers/edit_problem.php",
        type: "POST",
        data: {id:<?php echo $problem['id'] ?>,value:Number(value), field:name,action:"changeField"},
        cache:false,
        success: function(response) {
          //console.log(response);
        }
    });
  }
  function voteDifficulty(value){
    $('#difficulty_vote_label').text(value);
    $.ajax({
        url: "includes/handlers/add_vote.php",
        type: "POST",
        data: {id:<?php echo $problem['id'] ?>,value:value, type:1},
        cache:false,

        success: function(response) {
          $('#difficulty').html(response);
          //location.reload();
        }
    });
  }
  let editingProblem=false;
  function editProblem(fieldname){
    if(<?php echo $user['perms']?> < 1)
      return;
    if(editingProblem)
      return;
    editingProblem=true;
    let field=$('#'+fieldname);
	  let content=field.html().trim().replaceAll("<br>","");
    field.html('<textarea class="form-control" id="newfield">'+content+'</textarea>');
    $('#newfield').focus();
    $('#newfield').blur(function(){
      let newContent=$('#newfield').val();
      if(fieldname=="name"&&(1>newContent.length||newContent.length>255)){
        window.alert('Name must have fewer than 256 characters');
        return;
      }
      $.ajax({
          url: "includes/handlers/edit_problem.php",
          type: "POST",
          data: {id:<?php echo $problem['id'] ?>,value:newContent, field:fieldname,action:"changeField"},
          cache:false,

          success: function(response) {
            console.log(response);
            editingProblem=false;
            field.html(newContent.replaceAll("\n","<br>\n"));
          }
      });
    });
  }
  function deleteProblem(value){
    value=value|0;
    $.ajax({
        url: "includes/handlers/edit_problem.php",
        type: "POST",
        data: {id:<?php echo $problem['id'] ?>,action:"changeField",field:"deleted",value:value},
        cache:false,

        success: function(response) {
          location.reload();
          //console.log(response);
          //window.location="problems.php";
        }
    });

  }
  function loadTags(){
    $.ajax({
        url: "includes/handlers/load_tags.php",
        type: "POST",
        data: {problem:<?php echo $problem['id'] ?>,owner:"public",type:<?php echo $user['perms']<1? 2:0 ?>},
        cache:false,

        success: function(response) {
            $(".public_tags").html(response);
        }
    });
    $.ajax({
        url: "includes/handlers/load_tags.php",
        type: "POST",
        data: {problem:<?php echo $problem['id'] ?>,owner:"personal",type:0},
        cache:false,

        success: function(response) {
            $(".user_tags").html(response);
        }
    });
    $.ajax({
        url: "includes/handlers/load_tags.php",
        type: "POST",
        data: {problem:<?php echo $problem['id'] ?>,owner:"personal",type:1},
        cache:false,

        success: function(response) {
            $("#newUserTag").html(response);
        }
    });
    
    $.ajax({
        url: "includes/handlers/load_tags.php",
        type: "POST",
        data: {problem:<?php echo $problem['id'] ?>,owner:"public",type:1},
        cache:false,

        success: function(response) {
            $("#newPublicTag").html(response);
        }
    });
  }
  function addTag(tag){
    $.ajax({
        url: "includes/handlers/add_problemtags.php",
        type: "POST",
        data: {tag:tag,problem:<?php echo $problem['id'] ?>},
        cache:false,

        success: function(response) {
          loadTags();
          //$('#newUserTag').val("");
          //$('#newUserTag').hide();
          $('.select2').hide();
        }
    });
    
  }
  function removeTag(tag){
    $.ajax({
        url: "includes/handlers/remove_problemtags.php",
        type: "POST",
        data: {tag:tag,problem:<?php echo $problem['id'] ?>},
        cache:false,

        success: function(response) {
          loadTags();
        }
    });

  }


$(function(){
  let problem = '<?php echo $_REQUEST['id']; ?>';
  let inProgress = false;

  loadPosts(); //Load first posts

  postId=<?php echo $postId ?>;
  if(postId!=-1){
    $('#commenttab').trigger("click");
    setTimeout(function() {
      $.scrollTo($('#comment-'+postId).offset().top-100,500);
    }, 100);
  }

    $(window).scroll(function() {
      let bottomElement = $(".posts_area").last();
      let noMorePosts = $('.posts_area').find('.noMorePosts').val();

        // isElementInViewport uses getBoundingClientRect(), which requires the HTML DOM object, not the jQuery object. The jQuery equivalent is using [0] as shown below.
        if (isElementInView(bottomElement[0]) && noMorePosts == 'false') {
            loadPosts();
        }
    });

    function loadPosts() {
      if(inProgress) { //If it is already in the process of loading some posts, just return
        return;
      }
      
      inProgress = true;
      $('#loading').show();

      let page = $('.posts_area').find('.nextPage').val() || 1; //If .nextPage couldn't be found, it must not be on the page yet (it must be the first time loading posts), so use the value '1'
      let amount=100;

      $.ajax({
        url: "includes/handlers/load_posts.php",
        type: "POST",
        data: {amount:amount,page:page,problem:problem,user:<?php echo $curUID ?>,perms:<?php echo $user['perms']?>},
        cache:false,

        success: function(response) {
          $('.posts_area').find('.nextPage').remove(); //Removes current .nextpage 
          $('.posts_area').find('.noMorePosts').remove(); //Removes current .nextpage 
          $('.posts_area').find('.noMorePostsText').remove(); //Removes current .nextpage 

          $('#loading').hide();
          $(".posts_area").append(response);
          inProgress = false;
        }
      });
    }

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
function replyPost(id){
	$('#commentForm'+id).show();
}


</script>
</div>
</body>
</html>