<!doctype html>
<html lang="en">
<script src="https://www.w3schools.com/lib/w3.js"></script>
<head>
	<meta charset="utf-8">
	<title>Region - Winos</title>  <!--Region page-->
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

/* Header image */
.bgimg-1 {
    background-image: url("/winos/images/region.jpg");
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
    <span class="w3-center w3-padding-large w3-black w3-xlarge w3-wide w3-animate-opacity">REGIONS</span>
  </div>
</div>

<div class="w3-container w3-padding-16" id="result">

<!-- Page Description -->
<h6>Certain regions become well-known due to their ability to produce good wines.
	Factors such as climate and soil affect the type of grapes grown and wine produced.
<br>Check out the regions that produce our wines:</h6>

<?php
// ini_set('display_errors', 1); //Enable error display

// Set variables for database connections
$host = "localhost";
$un = "winos";
$pw = "drinkdrankdrunk";
$db = "winos";
$link = mysqli_connect($host, $un, $pw, $db);

//Set self-referential form
print "<form action='region.php' method='GET'>";
//Populate dropdown menu for a list of possible regions to view from database query
print "<select name='regionID'><option value='Choose'> Please choose your region</option>";
$listresult = mysqli_query($link, "SELECT regionID, name FROM region ORDER BY name");
	while ($row = mysqli_fetch_array($listresult)) {
		print "<option value='$row[regionID]'>$row[name]</option>"; //Passes the regionID, displays the region name in the dropdown
	}
print "</select>";
print "<input type='submit' value='Explore'>";
print "</form>";

// If a region is being requested, give region info; otherwise print a generic message
if(isset($_GET['regionID'])) {
	//Get region info
	$region = $_GET['regionID'];
	$cleanRegionID = preg_replace ("/[^ 0-9a-zA-Z]+/", "", $region); //Sanitize
	$regionQuery = "SELECT regionID, name AS regionName, country AS regionCountry,
					climate AS regionClimate
					FROM region
					WHERE regionID = $cleanRegionID";
	$regionResult = mysqli_query($link, $regionQuery);
	$regionResultRow = mysqli_fetch_array($regionResult);

	//Print our region info after a region has been selected from the dropdown
	print "<h1>$regionResultRow[regionName]</h1>";
	print "<p>Country: $regionResultRow[regionCountry]</p>";
	print "<p>Climate: $regionResultRow[regionClimate]</p>";
	print "<br/><h3>Bottles in our collection:</h3>";

	//Get bottle info for selected region from database
	//Join tables in order to retrieve all displayed information
	$regionBottleQuery = "SELECT bottle.bottleID AS bottleID, bottle.name AS bottleName, bottle.pictureLink AS bottlePictureLink,
					bottle.vintage AS bottleVintage, bottle.abv AS bottleABV, bottle.regionID AS bottleRegionID,
					bottle.rating AS bottleRating, bottle.price AS bottlePrice, vineyard.vineyardID AS vineyardID,
					vineyard.name AS vineyardName, type.typeID AS typeID, type.name AS typeName
					FROM region, bottle, vineyard, type
					WHERE type.typeID = bottle.typeID AND vineyard.vineyardID = bottle.vineyardID AND region.regionID = bottle.regionID AND region.regionID = $cleanRegionID
					ORDER BY bottleName";
	$regionBottleResult = mysqli_query($link, $regionBottleQuery);

	//Error handling to make sure there is a result to display
	if(($regionBottleResult) && ($regionBottleResult->num_rows !==0)) {
	   print "<div class=\"w3-row-padding\">"; //Create space between cards
	   //Loop through results
	   while($bottleRow = mysqli_fetch_array($regionBottleResult)){
	       //Create link to bottle page and vineyard page for more details
		   $url = "bottle.php?bottleID=".$bottleRow[bottleID];
		   $vineyardUrl = "vineyard.php?vineyardID=".$bottleRow['vineyardID'];

		   //Show the results in a card
		   print "<div class=\"w3-col s12 m6 l4 w3-padding-16\"><div class=\"w3-card-4 w3-dark-grey\"><div class=\"w3-container\">";
		   print "<h3><a href = \"$url\">$bottleRow[bottleName]</a></h3>";
		   print "<h4>($bottleRow[bottleVintage]) | $bottleRow[typeName]</h4>";
		   print "<img src=\"\winos\images\\$bottleRow[bottlePictureLink]\" class=\"w3-left w3-padding-small\" style=\"height:100%;max-height:150px\">";
		   print "<p> FROM: <a href = \"$vineyardUrl\">$bottleRow[vineyardName]</a>";
		   print "<br> RATING: $bottleRow[bottleRating]";
		   print "<br> PRICE: $$bottleRow[bottlePrice]</div>";
		   print "<div class=\"w3-container\"><br></div></div></div>";
	   }
	   print "</div>";
      }
   else {
	print "<p>No results found</p>"; // Print a generic message if no bottles available for the vineyard
   }

}
else {
	print "<p>Choose a region to see more! </p><br/>";
}
mysqli_close($link);

?>
</div>
</body>

<!-- Footer -->
<div w3-include-html="wino_footer.html"></div>

<script src="wino_script_src.js">
</script>

</html>
