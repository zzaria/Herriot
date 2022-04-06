<?php
class Notification {
	private $con;

	public function __construct($con, $user=NULL){
		$this->con = $con;
	}

	public function getUnreadNumber($user) {
		$query = mysqli_query($this->con, "SELECT * FROM notifications WHERE viewed='no' AND user_to='$user'");
		return mysqli_num_rows($query);
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
	public function getNotifications($data,$user) {
		$ret = "";
		mysqli_query($this->con, "UPDATE notifications SET viewed=1 WHERE user_to=$user");

		$query = mysqli_query($this->con, "SELECT * FROM notifications WHERE user_to=$user ORDER BY id DESC");

		if(mysqli_num_rows($query) == 0) {
			echo "You have no notifications!";
			return;
		}

		while($row = mysqli_fetch_array($query)) {
			$user_from = $row['user_from'];
			$time_message = $this->time_elapsed_string($row['datetime']);
			$opened = $row['opened'];
			$style = ($opened == 0) ? "background-color: rgb(200, 150, 120,0.2);" : "";

			if($user_from==0){
				$ret.="<a href='" . $row['link'] . "'> 
						<div class='resultDisplay resultDisplayNotification' style='" . $style . "'>
							<p class='timestamp_smaller' id='grey'>" . $time_message . "</p>" . $row['message'] . "
						</div>
					</a>";
				continue;
			}

			$user_data_query = mysqli_query($this->con, "SELECT * FROM users WHERE id=$user_from");
			$user_data = mysqli_fetch_array($user_data_query);

			$ret .= "<a href='" . $row['link'] . "'> 
									<div class='resultDisplay resultDisplayNotification' style='" . $style . "'>
										<div>
											<img src='" . $user_data['profile_pic'] . "'>
										</div>
										<p class='timestamp_smaller' id='grey'>" . $time_message . "</p>" . $row['message'] . "
									</div>
								</a>";
		}
		return $ret;
	}

	public function insertNotification($user_from, $user_to, $link, $type) {
		if($user_from!=0)
			$username = mysqli_fetch_array(mysqli_query($this->con,"SELECT username FROM users WHERE id=$user_from"))['username'];

		$date_time = date("Y-m-d H:i:s");

		switch($type) {
			case 'comment':
				$message = $username . " commented on your post";
				break;
			case 'like':
				$message = $username . " liked your post";
				break;
			case 'sharetag':
				$message = $username . " shared a tag with you";
				break;
			default:
				$message = $type;
		}

		mysqli_query($this->con, "INSERT INTO notifications VALUES (NULL, $user_to, $user_from, '$message', '$link', '$date_time', 0,0)");
	}

}

?>