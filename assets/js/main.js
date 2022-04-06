function getDropdownData(user, type) {

	if($(".dropdown_data_window").css("height") == "0px") {


		if(type == 'notification') {
			$("span").remove("#unread_notification");
		}

		var ajaxreq = $.ajax({
			url: "includes/handlers/load_notifications.php",
			type: "POST",
			cache: false,

			success: function(response) {
				$(".dropdown_data_window").html(response);
				$(".dropdown_data_window").css({"padding" : "0px", "height": "280px"});
				$("#dropdown_data_type").val(type);
			}

		});

	}
	else {
		$(".dropdown_data_window").html("");
		$(".dropdown_data_window").css({"padding" : "0px", "height": "0px", "border" : "none"});
	}
}
function deletePost(id,value=1){
	$.ajax({
	  url: "includes/handlers/delete_post.php",
	  type: "POST",
	  data: {id:id,delete:value},
	  cache:false,
  
	  success: function(response) {
		$("#comment-"+id).remove();
	  }
	});
}

let editingPost=false;
function editPost(id){
    if(editingPost)
      return;
	  editingPost=true;
    let post=$('#comment-body-'+id);
	let content=post.html().trim().replaceAll("<br>","");
    post.html('<textarea class="form-control" id="newfield">'+content+'</textarea>');
    $('#newfield').focus();
    $('#newfield').blur(function(){
      let newContent=$('#newfield').val();
      $.ajax({
          url: "includes/handlers/edit_post.php",
          type: "POST",
          data: {id:id,value:newContent},
          cache:false,

          success: function(response) {
            editingPost=false;
            post.html(newContent.replaceAll("\n","<br>\n"));
          }
      });
    });
  }


