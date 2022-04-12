<?php  
require 'config/config.php';
include("includes/classes/Constants.php");
include("includes/classes/User.php");
include("includes/classes/Problem.php");
include("includes/classes/Post.php");
include("includes/classes/Notification.php");
include("includes/current_user.php");

$problemobj=new Problem($con);
$theme=$user['theme'];
?>
<!DOCTYPE html>
<html>
<head>
	<title>Herriot!</title>
	<link rel="icon" type="image/x-icon" href="assets/images/favicon.ico">

	<!-- Libraries -->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>

	<link href="https://cdn.jsdelivr.net/gh/hung1001/font-awesome-pro-v6@18657a9/css/all.min.css" rel="stylesheet" type="text/css" />

	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
	<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
	<link rel="stylesheet" type="text/css" href="assets/css/bootstrap.css">
	
	<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
	<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

	<script src="assets/js/jquery.barrating.min.js"></script>
	<link rel="stylesheet" type="text/css" href="assets/css/fontawesome-stars.css">
	<link rel="stylesheet" type="text/css" href="assets/css/fontawesome-stars-o.css">
	<link rel="stylesheet" type="text/css" href="assets/css/bars-pill.css">

	<script src="assets/js/lc_switch.min.js"></script>
	
	<script src="assets/js/html5sortable.min.js"></script>

	<script type="text/javascript" src="assets/js/jquery.scrollTo.min.js"></script>

	<script src="assets/js/jQueryColorPicker.min.js "></script>

	<!-- Javascript -->
	<script src="assets/js/main.js"></script>
	
	<!-- CSS -->
	<link rel="stylesheet" type="text/css" href="assets/css/style<?php if($theme>=100) echo '-light'?>.css">
	<?php if($theme>=100) echo '<link rel="stylesheet" type="text/css" href="assets/css/style-'.$theme.'.css">'?>
	<!--<link rel="icon" href="/assets/images/icon.png" type="image/icon">-->
</head>
<body>

	<nav class="navbar navbar-expand-lg navbar-dark fixed-top navbar1 p-1 bg-navbar">
		<div class="navbar-brand ms-2 fs-2">
			<a class="logo" href="index.php"><span class="fs-1 fw-bold logo-hover-first">⟁</span>ん<span class="logo-hover-black">Σ</span>尺я<span class="logo-hover-black">𝒾Ꭷ</span>₮</a>
		</div>
		<ul class="navbar-nav">
			<li class="nav-item"><a class="nav-link" href="problems.php">All Problems</a></li>
			<li class="nav-item"><a class="nav-link" href="tags.php">Tags</a></li>
			<li class="nav-item"><a class="nav-link" href="users.php">Users</a></li>
			<li class="nav-item"><a class="nav-link" href="editor.php">Add Problems</a></li>
			<li class="nav-item"><a class="nav-link" href="about.php">About</a></li>
		</ul>
		<nav class="navbar2 position-absolute end-0 me-2">
			<?php
				//Unread notifications 
				$notifications = new Notification($con);
				$num_notifications = $notifications->getUnreadNumber($curUID);
				if($user['perms']>=Constants::ADMIN_PERMS)
					echo '
					<a href="admin.php">
						<i class="fa-regular fa-alicorn"></i>
					</a>
					'
			?>

			<a href="profile.php?user=<?php echo $curUID; ?>">
				<?php echo $problemobj->ratingCircle($user['power'],$user['username']); ?>
			</a>
			
			<a href="blank.php">
				<i class="fa-solid fa-citrus-slice"></i>
			</a>
			<a href="index.php">
				<i class="fa fa-home fa-lg"></i>
			</a>
			<a class="position-relative" href="javascript:void(0);" onclick="getDropdownData('<?php echo $userLoggedIn; ?>', 'notification')">
				<i class="fa fa-tree fa-lg"></i>
				<?php
				if($num_notifications > 0)
				 echo '<span class="notification_badge" id="unread_notification">' . $num_notifications . '</span>';
				?>
			</a>
			<a href="settings.php">
				<i class="fa fa-cog fa-lg"></i>
			</a>
			<a href="includes/handlers/logout.php">
				<i class="fa fa-sign-out fa-lg"></i>
			</a>

		</nav>
	</nav>
		<div class="dropdown_wrapper"><div class="dropdown_data_window scroller" style="height:0px; border:none;"></div></div>
		<input type="hidden" id="dropdown_data_type" value="">
	</div>


	<script>
		$(function(){
			$('.navbar1 li a').filter(function(){return this.href === location.href;}).addClass('active');
			let theme=<?php echo $user['theme']?>;
			let root = document.documentElement;
			let width=window.innerWidth;
			if(width<1000){
				$(".wrapper").css('width','100%');
				$(".wrapper").css('top','180px');
				$(".navbar2").css('position','relative');
				$(".row").removeClass('g-5');
				$(".row").removeClass('g-4');
				$(".row").addClass('g-0');
				$(".dropdown_wrapper").css('top','140px');
			}
			switch(theme){
				case 2:
					$('body').css('background-image','url(assets/images/backgrounds/a.jpg)');
					break;
				case 3:
					$('body').css('background-image','url(assets/images/backgrounds/b.jpg)');
					break;
				case 4:
					$('body').css('background-image','url(assets/images/backgrounds/c.jpg)');
					break;
				case 5:
					$('body').css('background-image','url(assets/images/backgrounds/d.jpg)');
					break;
				case 6:
					$('body').css('background-image','url(assets/images/backgrounds/f.jpg)');
					break;
				case 7:
					$('body').css('background-image','url(assets/images/backgrounds/g.jpg)');
					break;
				case 100:
					$('.logo').text('Herriot');
					$('table').addClass('table-bordered');
					break;
				case 102:
					$('body').css('background-image','url(assets/images/backgrounds/a.jpg)');
					break;

			}

		});
	</script>

	<div class="wrapper">