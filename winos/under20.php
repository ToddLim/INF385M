<!doctype html>
<html lang="en">
<script src="https://www.w3schools.com/lib/w3.js"></script>
<head>
	<meta charset="utf-8">
	<title>Top Wines - Winos</title>  <!--Top Wines Page-->
	<!--We are using w3school's CSS styling-->
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

/* Header Image*/
.bgimg-1 {
    background-image: url("/winos/images/top_12.jpg");
    min-height: 30%;
}

</style>

</head>
<body>

<!-- Navbar (sit on top) -->
<div w3-include-html="wino_header.html"></div>

<!-- Header image with Logo Text -->
<div class="bgimg-1 w3-display-container w3-opacity-min" id="home">
  <div class="w3-display-middle" style="white-space:nowrap;">
    <span class="w3-center w3-padding-large w3-black w3-xlarge w3-wide w3-animate-opacity">TOP WINES UNDER $20</span>
  </div>
</div>
<!--Container for content in rest of page under header image-->
<div class="w3-container w3-padding-16" id="result">

<!-- Page Description -->
<h4>Our 4 and 5 Star Rated Wines, All under $20</h4>

<?php

// ini_set('display_errors', 1); //Enable error display

// Set variables for database connections
$host = "localhost";
$un = "winos";
$pw = "drinkdrankdrunk";
$db = "winos";
$link = mysqli_connect($host, $un, $pw, $db);

//SQL query - find top 12 rated bottles under $20
//First order by rating, high to low. Then price, low to height
//Join tables in order to retrieve all displayed information
$under20query = "SELECT bottle.bottleID AS bottleID, bottle.name AS name, bottle.rating AS rating,
 								 bottle.pictureLink AS pictureLink, bottle.price AS price, bottle.abv AS abv,
								 type.name AS type, bottle.vintage AS vintage, vineyard.name AS vineyard
								 FROM bottle, type, vineyard
								 WHERE price < 20
								 AND type.typeID = bottle.typeID
								 AND vineyard.vineyardID = bottle.vineyardID
								 ORDER BY rating DESC, price ASC
								 LIMIT 12";

$under20Result = mysqli_query($link,$under20query);

//Error handling to make sure there is a result to display
if(($under20Result) && ($under20Result->num_rows !==0)) {
		print "<div class=\"w3-row-padding\">";  //create space between cards
	  //Loop through results
		while($under20ResultRow = mysqli_fetch_array($under20Result)){
			//create link to bottle page for more details
			$url = "bottle.php?bottleID=".$under20ResultRow[bottleID];

		//show the results in a card
		print "<div class=\"w3-col s12 m6 l4 w3-padding-16\"><div class=\"w3-card-4 w3-dark-grey\"><div class=\"w3-container\">";
		print "<h3><a href = \"$url\">$under20ResultRow[name]</a></h3>";
		print "<h4>($under20ResultRow[vintage]) | $under20ResultRow[type]</h4>";
		print "<img src=\"\winos\images\\$under20ResultRow[pictureLink]\" alt=\"$under20ResultRow[name]\" class=\"w3-left w3-padding-small\" style=\"height:100%;max-height:150px\">";
		print "<p> FROM: $under20ResultRow[vineyard]";
		print "<br> RATING: $under20ResultRow[rating]";
		print "<br> PRICE: $$under20ResultRow[price]";
		print "<div class=\"w3-container\"><br></div></div></div></div>";
	}
	print "</div>"; // Close the card
}

//Add link to browse page
print "<p>Go find something else interesting in <a href=\"http://hornet.ischool.utexas.edu/winos/full.php\">our collection.</a></p>";

mysqli_close($link);

?>
</div>
</body>

<!-- Footer -->
<div w3-include-html="wino_footer.html"></div>

<script src="wino_script_src.js">
</script>

</html>
