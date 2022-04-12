<?php 
include("includes/header.php");
?>
<div class="row">
<div class="col-lg-4 col-0"></div>
<div class="col-lg-8 col-12">
<div class="column">
	<h1 class="display-1">About</h1>
	<div class="accordion" id="accordionExample">
		<div class="accordion-item">
			<h2 class="accordion-header" id="headingOne">
			<button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
				Intro
			</button>
			</h2>
			<div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
			<div class="accordion-body">
				<p><strong>This is an index/checklist for all problems.</strong> There weren’t that many problems before, but now there
				are tens of thousands, spread across hundreds of judges. Here everything is together in one place.</p>

				<p>Many judges don’t have difficulty ratings, or have weird rating scales, but here everything is on the same system. With
				the quality rating, you can filter out all the boring problems. For OI problems, you don’t have to go searching on the
				official website from 1996 (which is often down) for the editorial or test data. If someone has an alternate or better
				solution, stronger test data, better problem statement, it can get updated here. Also, you can create your own playlists
				of problems for various purposes, such as a certain topic, contest, group of contests, or author.</p>
			</div>
			</div>
		</div>
		<div class="accordion-item">
			<h2 class="accordion-header" id="headingTwo">
			<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
				Mechanics
			</button>
			</h2>
			<div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#accordionExample">
			<div class="accordion-body">
				<p>Each problem has a <strong>difficulty</strong> and <strong>quality</strong>. The difficulty is the same scale as codeforces
				but extended. You should rate average problems 3 stars, 4 stars for ones you like and 2 stars for ones you dislike.</p>

				<p>After you solve a problem, you can check it off by giving it your personal <code>solved</code> tag. You can also make
				your own <a href="tags.php">custom tags</a> and share them with others. As you solve problems, you'll gain experience
				and level up.</p>
				
				<p>Go to your <a href="settings.php">settings</a> to change your profile picture or theme.</p>
			</div>
			</div>
		</div>
		<div class="accordion-item">
			<h2 class="accordion-header" id="headingThree">
			<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
				Contributing
			</button>
			</h2>
			<div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#accordionExample">
			<div class="accordion-body">
				<p>Most of the problem information is crowdsourced. The easiest way to contribute is to rate problems for difficulty and
				quality after you solve them. You can also help fill in a problem's information and add new problems by applying to become
				an <a href="editor.php">editor</a>. You'll need to meet one of the verification criteria or have an administrator personally
				accept your request, although more options and lower requirements might be added later.</p>

				<p>Another way to help is to write some crawlers or scripts to automate some of the problem and tagging process.</p>
			</div>
			</div>
		</div>
		<div class="accordion-item">
			<h2 class="accordion-header" id="headingFour">
			<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
				FAQ
			</button>
			</h2>
			<div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour" data-bs-parent="#accordionExample">
			<div class="accordion-body">
				<blockquote class="blockquote">
					<p>> <small class="text-muted">Why are the problems empty?</small></p>
				</blockquote>
				<p>Editors need to fill out the information. See the contributing section above to help out.</p>
				<blockquote class="blockquote">
					<p>> <small class="text-muted">How is your total power determined?</small></p>
				</blockquote>
				<p>
					Your power is a rating for the difficulty of your practice, calculated as an
					exponential average of the problems you solve. It's similar to the osu or dmoj
					performance point systems.
				</p>
			</div>
			</div>
		</div>
	</div>
</div>
</div></div>
</div>

<script>
</script>
</body>
</html>