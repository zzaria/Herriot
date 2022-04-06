<?php
include("includes/header.php");
include("includes/classes/Tag.php");
//header("Location: index.php"); exit;

if (isset($_REQUEST['user'])) {
  $userID = (int)$_GET['user'];
  $user_details_query = mysqli_query($con, "SELECT * FROM users WHERE id=$userID");

  if (mysqli_num_rows($user_details_query) == 0) {
    echo "User $userID does not exist";
    exit();
  }

  $user_array = mysqli_fetch_array($user_details_query);
  $username = $user_array['username'];
}
$tag = new Tag($con);
$problem = new Problem($con);
$ret = $tag->calcPoints($userID);
$count = $ret[0];
$points = $ret[1];
$points2 = $ret[2];
$level = floor($points2**(1/2));
$levelProgress=$points2-$level**2;
$levelAmount=$level*2+1;
$levelPercentage=$levelProgress/$levelAmount*100;
?>

<div class="row g-2">
  <div class="col-md-3 col-0"></div>
  <div class="col-md-8 col-12">
    <div class="dark_column column">
      <div class="row">
        <div class="col-8">
          <h1 class="display-2"><?php echo $username ?></h1>
          <dl class="row">
            <dt class="col-4">Total Power</dt>
            <dd class="col-8 text-end"><?php echo $problem->ratingCircle($points, $points) ?></dd>
            <dt class="col-4">ACs</dt>
            <dd class="col-8 text-end"><?php echo $count ?></dd>
            <dt class="col-4">Level</dt>
            <dd class="col-8 text-end"><?php echo $level?></dd>
            <div class="col-12"><div class=" progress bg-dark">
              <div class="progress-bar progress-bar-striped progress-bar-animated overflow-visible" style="width:<?php echo $levelPercentage?>%">
                <?php echo "$levelProgress/$levelAmount"?>
              </div>
            </div> </div>
          </dl>
        </div>
        <div class="col-4">
          <?php
          echo "<img src='" . $user_array['profile_pic'] . "' class='circle_profile_pic'>";
          ?>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-3 col-0"></div>
  <div class="col-md-8 col-12">
    <div class="column dark_column">
      <div class="row">
        <h2 class="text-center">Tags</h2>
        <?php $tag->getTags(-1, $userID, 4) ?>
      </div>
    </div>
  </div>
</div>
</div>
</div>

<script>
  window.history.replaceState(null, null, window.location.href);
</script>
</body>

</html>