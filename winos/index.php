<!doctype html>
<html lang="en">
<script src="https://www.w3schools.com/lib/w3.js"></script>
<head>
	<meta charset="utf-8">
	<title>Winos</title>  <!--Our home page-->
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Lato">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<style>
body,h1,h2,h3,h4,h5,h6 {font-family: "Lato", sans-serif;}
body, html {
    height: 100%;
    color: #777;
    line-height: 1.8;
}

/* Create a Parallax Effect */
.bgimg-1, .bgimg-2 {
    background-attachment: fixed;
    background-position: center;
    background-repeat: no-repeat;
    background-size: cover;
}

/* First image (Logo. Full height) */
.bgimg-1 {
    background-image: url("/winos/images/vin6.jpg");
    min-height: 100%;
}

/* Second image (Portfolio) */
.bgimg-2 {
    background-image: url("/winos/images/about_us.jpg");
    min-height: 400px;
}

.w3-wide {letter-spacing: 10px;}
.w3-hover-opacity {cursor: pointer;}

}
</style>	
</head>

<body>
<!-- Navbar (sit on top) -->
<div w3-include-html="wino_header.html"></div> 

<!-- First Parallax Image with Logo Text -->
<div class="bgimg-1 w3-display-container w3-opacity-min" id="home">
  <div class="w3-display-middle" style="white-space:nowrap;">
    <span class="w3-center w3-padding-large w3-black w3-xlarge w3-wide w3-animate-opacity">WINOS <span class="w3-hide-small">WINOS</span> WINOS</span>
  </div>
  <div class="w3-display-bottommiddle" style="white-space:nowrap;">
	<span class="w3-center w3-padding-small w3-black w3-small w3-animate-opacity">Curious? <em>We dare you to scroll</em></span>
  </div>
</div>

<!-- Container 2 (Quicksearch) -->
<!-- Create buttons with quick links -->
<div class="w3-content w3-container w3-padding-64" id="quick">
	<h3 class="w3-center">NO TIME TO THINK, GRAB SOMETHING FAST.</h3>
	<!-- What's for Dinner? Button -->
	<div class="w3-row-padding w3-grayscale">
	  <div class="w3-third w3-padding-large">
		<img src="/winos/images/vin7.jpg" alt="Dinner" style="width:100%">
		<h3>What's for dinner?</h3>
		<p>Let us help you pair a wine.</p>
		<p><a href="dinner.php" class="w3-button w3-light-grey w3-block">I'm hungry already...</a></p>
	  </div>
	  <!-- Top Wines Button -->
	  <div class="w3-third w3-padding-large">
		<img src="/winos/images/vin1.jpg" alt="Cheap" style="width:100%">
		<h3>Top Wines Under $20</h3>
		<p>Great wines on a budget.</p>
		<p><a href="under20.php" class="w3-button w3-light-grey w3-block">Let's Go!</a></p>
	  </div>
	  <!-- Most Bang for Your Buck Button -->
	  <div class="w3-third w3-padding-large">
		<img src="/winos/images/vin2.jpg" alt="Booziest" style="width:100%">
		<h3>Most buzz for your buck!</h3>
		<p>Need we explain?</p>
		<p><a href="booziest.php" class="w3-button w3-light-grey w3-block">What are we waiting for?</a></p>
	  <!-- Close Quicksearch container -->
	  </div>

	</div>
</div>

<!-- Second Parallax Image with Quicksearch Text -->
<div class="bgimg-2 w3-display-container w3-opacity-min">
  <div class="w3-display-middle">
    <span class="w3-xxlarge w3-text-white w3-wide">MAKE YOURSELF AT HOME</span>
  </div>
</div>

<!-- Container (About Section) -->
<div class="w3-content w3-container w3-padding-64" id="about">
  <h3 class="w3-center">WHO ARE WE, YOU ASK?</h3>
  <p class="w3-center"><em>We drink wine.</em></p>
  <p class="w3-center">And we thought we'd share it with you by building this page to show off our affection for wine and our mad php/sql skills.
  <br> <br> Grab some cheese and crackers, you might work up an appetite.</p>
<!-- Close About Section container -->
</div> 
</body>
<!-- Footer -->
<div w3-include-html="wino_footer.html"></div> 

<script src="wino_script_src.js">
</script>

</html>