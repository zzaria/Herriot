<?php
class Post
{
	private $con;

	public function __construct($con)
	{
		$this->con = $con;
	}
	public function getAuthor($post){
		return mysqli_fetch_array(mysqli_query($this->con, "SELECT author FROM posts WHERE id=$post"))['author'];
	}

	public function submitPost($body, $problem, $author, $parent)
	{
		$body = strip_tags($body, "<img><video><h1><h2><h3><h4><h5><h6><b><a>"); //removes html tags 
		//$body = mysqli_real_escape_string($this->con, $body);
		$body = str_replace('\r\n', "\n", $body);
		$body = nl2br($body);
		$check_empty = preg_replace('/\s+/', '', $body); //Deltes all spaces 
		if ($check_empty == "")
			return;

		//Current date and time
		$date_added = date("Y-m-d H:i:s");

		//insert post 
		$sql = mysqli_prepare($this->con, "INSERT INTO posts VALUES (NULL, ?,?,?,?, 0, 0, ?)");
		mysqli_stmt_bind_param($sql, "siisi", $body, $author, $problem, $date_added, $parent);
		mysqli_stmt_execute($sql);
		$query = mysqli_stmt_get_result($sql);
		//$query = mysqli_query($this->con, "INSERT INTO posts VALUES (NULL, '$body', '$author', '$problem', '$date_added', 0, 0, $parent)");

		$returned_id = mysqli_insert_id($this->con);

		//Insert notification
		if ($parent != 0) {
			$parentAuthor = $this->getAuthor($parent);
			if ($author != $parentAuthor) {
				$notification = new Notification($this->con);
				$notification->insertNotification($author, $parentAuthor, "post.php?id=" . $parent, "comment");
			}
		}
		return $returned_id;
	}
	public function editPost($id,$body)
	{
		$body = strip_tags($body, "<img><video><h1><h2><h3><h4><h5><h6><b><a>"); //removes html tags 
		//$body = mysqli_real_escape_string($this->con, $body);
		$body = str_replace('\r\n', "\n", $body);
		$body = nl2br($body);

		//insert post 
		$sql = mysqli_prepare($this->con, "UPDATE posts SET body=? WHERE id=?");
		mysqli_stmt_bind_param($sql, "si", $body,$id);
		mysqli_stmt_execute($sql);
		$query = mysqli_stmt_get_result($sql);
		//$query = mysqli_query($this->con, "INSERT INTO posts VALUES (NULL, '$body', '$author', '$problem', '$date_added', 0, 0, $parent)");
	}
	function time_elapsed_string($datetime, $full = false) {
		$now = new DateTime;
		$ago = new DateTime($datetime);
		$diff = $now->diff($ago);
	
		$diff->w = floor($diff->d / 7);
		$diff->d -= $diff->w * 7;
	
		$string = array(
			'y' => 'year',
			'm' => 'month',
			'w' => 'week',
			'd' => 'day',
			'h' => 'hour',
			'i' => 'minute',
			's' => 'second',
		);
		foreach ($string as $k => &$v) {
			if ($diff->$k) {
				$v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
			} else {
				unset($string[$k]);
			}
		}
	
		if (!$full) $string = array_slice($string, 0, 1);
		return $string ? implode(', ', $string) . ' ago' : 'just now';
	}
	public function loadPosts($data, $parent = 0, $getchildren = True)
	{

		$start = (int)$data['amount'] * ($data['page'] - 1);
		$amount = (int)$data['amount'];
		$problem = (int)$data['problem'];
		$user = (int)$data['user'];


		$ret = ""; //String to return 
		$data_query = mysqli_query($this->con, "SELECT posts.id,posts.body,posts.author,posts.problem,posts.date_added,posts.likes,votes.value,users.username,users.profile_pic,users.power FROM posts
		LEFT JOIN (SELECT value,parent FROM votes WHERE type=2 AND user=$user) votes ON posts.id=votes.parent
		INNER JOIN users ON users.id=posts.author
		WHERE deleted=0 AND problem=$problem AND posts.parent=$parent
		ORDER BY posts.id DESC LIMIT $start, $amount");

		$no_more_posts = true;
		$problem_obj = new Problem($this->con);

		while ($row = mysqli_fetch_array($data_query)) {
			$no_more_posts = false;
			$id = $row['id'];
			$body = $row['body'];
			$author = $row['author'];
			$time_message = $this->time_elapsed_string($row['date_added']);

			if ($user == $author || $data['perms'] >= Constants::ADMIN_PERMS)
				$delete_button = "<span class='float-end cursor-pointer' onclick='deletePost($id,1)'>X</span>";
			else
				$delete_button = "";
			if($data['perms']>=Constants::ADMIN_PERMS)
				$edit_post="ondblclick='editPost($id)'";
			else
				$edit_post="";

			//todo: remove this query, join tables above
			//$user_details_query = mysqli_query($this->con, "SELECT username, profile_pic,power FROM users WHERE id='$author'");
			//$user_row = mysqli_fetch_array($user_details_query);
			$username = $row['username'];
			$profile_pic = $row['profile_pic'];
			$post_link = "<a href=\"post.php?id=$id\">$id</a>";

			if ($getchildren) {
				$children = "
				<form id='commentForm{$id}' class='comment_form' method='POST' enctype='multipart/form-data'>
					<textarea class='form-control' name='post_text' id='post_text'></textarea>
					<input type='hidden' name='problem' value={$problem}>
					<input type='submit' name='post' id='post_button' value='Post'>
					<input type='hidden' name='parent' value='{$id}'>
					<hr>
				</form>" .
					$this->loadPosts(array("amount" => 10000, "page" => 1, "problem" => $problem, "user" => $user, "perms" => $data['perms']), $id);
			} else
				$children = "";
			$ratingCircle=$problem_obj->ratingCircle($row['power']," $username");
			$ret .= "<div class='status_post' id='comment-$id'>
						<div class='post_profile_pic'>
							<img src='$profile_pic' width='50'>
						</div>

						<div class='posted_by' style='color:#ACACAC;'>
							<a href='profile.php?user=$author'> $ratingCircle </a> $time_message &nbsp;&nbsp;&nbsp;&nbsp;$post_link
							$delete_button
						</div>
						<div class='post_body' id='comment-body-$id' $edit_post>
							$body
						</div>

						<div class='newsfeedPostOptions'>
							<span onclick=\"replyPost($id)\">Reply</span> &nbsp;&nbsp;&nbsp;
							<iframe src='like.php?post=$id&user=$user&likes={$row['likes']}&current={$row['value']}' scrolling='no'></iframe>
						</div>
						<div class=\"mt-3 ms-5\">
							$children
						</div>
					</div>";
			if ($parent == 0) {
				$ret .= "<hr>";
			}


?>
			<script>
				$(document).ready(function() {

					$('#post<?php echo $id; ?>').on('click', function() {
						bootbox.confirm("Maybe Later", function(result) {

							$.post("includes/handlers/delete_post.php?post_id=<?php echo $id; ?>", {
								result: result
							});

							if (result)
								location.reload();

						});
					});


				});
			</script>
		<?php

		} //End while loop
		if ($parent != 0)
			return $ret;
		if ($no_more_posts)
			$ret .= "<input type='hidden' class='noMorePosts' value='true'><p style='text-align: centre;' class='noMorePostsText'> No more posts to show! </p>";
		else
			$ret .= "<input type='hidden' class='nextPage' value='" . ($data['page'] + 1) . "'>
			<input type='hidden' class='noMorePosts' value='false'>";
		echo $ret;
	}

	public function getSinglePost($post_id, $user, $perms=0)
	{
		$post_id = (int)$post_id;
		$user = (int)$user;
		mysqli_query($this->con, "UPDATE notifications SET opened=1 WHERE user_to='$user' AND link LIKE '%=$post_id'");

		$ret = ""; //String to return 
		if($perms<1)
			$data_query = mysqli_query($this->con, "SELECT * FROM posts WHERE deleted=0 AND id='$post_id'");
		else
			$data_query = mysqli_query($this->con, "SELECT * FROM posts WHERE id='$post_id'");

		if (mysqli_num_rows($data_query) > 0) {
			$row = mysqli_fetch_array($data_query);
			$id = $row['id'];
			$body = $row['body'];
			$author = $row['author'];
			$time_message = $this->time_elapsed_string($row['date_added']);
			$problem = $row['problem'];

			//Prepare user_to string so it can be included even if not posted to a user
			$problem_obj = new Problem($this->con);
			$problemName = $problem_obj->getName($problem);
			if ($problem == 0)
				$problemName = "to <a href=\"index.php\">$problemName</a>";
			else
				$problemName = "to <a href=\"$problem\">$problemName</a>";

			if ($user == $author)
				$delete_button = ""; //"<button class='delete_button btn-danger' id='post$id'>X</button>";
			else
				$delete_button = "";

			$user_details_query = mysqli_query($this->con, "SELECT username, profile_pic,perms,power FROM users WHERE id='$author'");
			$user_row = mysqli_fetch_array($user_details_query);
			$username = $user_row['username'];
			$profile_pic = $user_row['profile_pic'];
			$children = $this->loadPosts(array("amount" => 10000, "page" => 1, "problem" => $problem, "user" => $user, "perms" => $user_row['perms']), $id);
			$ratingCircle=$problem_obj->ratingCircle($user_row['power']," $username");
			
			if($row['deleted']==1)
				$ret.="<button class='btn btn-primary' onclick='deletePost($id,0)'>Restore Post</button>";
			$ret .= "<div class='status_post' id='comment-$id'>
						<div class='post_profile_pic'>
							<img src='$profile_pic' width='50'>
						</div>

						<div class='posted_by' style='color:#ACACAC;'>
							<a href='$author'>$ratingCircle </a> $problemName &nbsp;&nbsp;&nbsp;&nbsp;$time_message
							$delete_button
							</div>
							<div class='post_body'>
								$body
							</div>

							<div class='newsfeedPostOptions'>
								<span onclick=\"$('#commentForm{$id}').show();\">Reply</span> &nbsp;&nbsp;&nbsp;
								<iframe src='like.php?post=$id&user=$user&likes={$row['likes']}' scrolling='no'></iframe>
							</div>
							<div class=\"mt-3 ms-5\">
								<form id='commentForm{$id}' class='comment_form' method='POST' enctype='multipart/form-data'>
									<textarea class='form-control' name='post_text' id='post_text'></textarea>
									<input type='hidden' name='problem' value={$problem}>
									<input type='submit' name='post' id='post_button' value='Post'>
									<input type='hidden' name='parent' value='{$id}'>
									<hr>
								</form>
								$children
							</div>
						</div><hr>";


		?>
<?php
		} else {
			echo "<p>No post found. If you clicked a link, it may be broken.</p>";
			return;
		}

		echo $ret;
	}
	function recentActions()
	{
		$query = mysqli_query($this->con, "SELECT posts.id,posts.body,posts.problem,posts.author,problems.name,username
		FROM posts LEFT JOIN problems ON posts.problem=problems.id
		INNER JOIN users ON posts.author=users.id
		WHERE posts.deleted=0 AND posts.parent=0 AND (problems.deleted=0 OR posts.problem=0) ORDER BY posts.date_added DESC LIMIT 9");

		foreach ($query as $row) {
			$username = $row['username'];
			$problemname = $row['name'];
			if($row['problem']==0)
				$problemname="Announcements";
			$word = $row['body'];
			$word = strip_tags($word);
			if (strlen($word) > 65) $word = substr($word, 0, 65) . "...";
			if (strlen($username) > 29) $username = substr($username, 0, 29) . "...";
			if (strlen($problemname) > 29) $problemname = substr($problemname, 0, 29) . "...";
			$word = "<a href='{$row['problem']}&postId={$row['id']}'>{$word}</a>";
			$username = "<a href='profile.php?user={$row['author']}'>{$username}</a>";
			echo "<div style'padding: 1px'>";
			echo $username . " in " . $problemname . " - " . $word;
			echo "<br></div><br>";
		}
	}
}

?>
