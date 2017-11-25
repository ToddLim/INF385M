<!doctype html>
<html lang="en">
<script src="https://www.w3schools.com/lib/w3.js"></script>
<head>
	<meta charset="utf-8">
	<title>Vineyards - Winos</title>  <!--vineyards page-->
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
info{font-size: 1.5em;
}

/* Header image */
.bgimg-1 {
    background-image: url("/winos/images/vineyard.jpg");
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
    <span class="w3-center w3-padding-large w3-black w3-xlarge w3-wide w3-animate-opacity">VINEYARDS</span>
  </div>
</div>

<div class="w3-container w3-padding-16" id="result">

<!-- Page Description -->
<h6>A vineyard may grow the grapes used to produce the wine, or may simply bottle the wines.
	Many can be booked for tours and wine-tastings.<br>Check out the vineyards featured in our collection:</h6>

<?php

// ini_set('display_errors', 1); //Enable error display

// Set variables for database connections
$host = "localhost";
$un = "winos";
$pw = "drinkdrankdrunk";
$db = "winos";
$link = mysqli_connect($host, $un, $pw, $db);

//set self-referential form
print "<form action='vineyard.php' method='GET'>";
//Populate dropdown menu for a list of possible vineyards to view from database query:
print "<select name='vineyardID'><option value='Choose'> Please choose your vineyard</option>";
$listresult = mysqli_query($link, "SELECT vineyardID, name FROM vineyard ORDER BY name");
	while ($row = mysqli_fetch_array($listresult)) {
		print "<option value='$row[vineyardID]'>$row[name]</option>";
	}
print "</select>";
print "<input type='submit' value='Explore'>";
print "</form>";

// If a vineyard is being requested, give vineyard info; otherwise print a generic message
if(isset($_GET['vineyardID'])) {
	//Get vineyard info
	$vineyard = $_GET['vineyardID'];
	$cleanVineyardID = preg_replace ("/[^ 0-9a-zA-Z]+/", "", $vineyard); //sanitize
	$vineyardQuery = "SELECT vineyardID, name AS vineyardName, owner AS vineyardOwner,
					streetAddress AS vineyardStreetAddress, city AS vineyardCity, state AS vineyardState,
					country AS vineyardCountry, regionID AS vineyardRegionID, postalCode AS vineyardPostalCode,
					latLong AS vineyardLatLong, siteLink AS vineyardSiteLink, mapLink AS vineyardMapLink
					FROM vineyard
					WHERE vineyardID = $cleanVineyardID";
	$vineyardResult = mysqli_query($link, $vineyardQuery);
	$vineyardResultRow = mysqli_fetch_array($vineyardResult);

	//Print our vineyard owner, address, and country ignoring null values
	print "<h1>$vineyardResultRow[vineyardName]</h1>";
	//This container displays vineyard details
	print "<div class=\"w3-col s12 m12 l6\"><div class=\"w3-container\">";
	if ($vineyardResultRow[vineyardOwner] != 'null') {print "<info>Owner: $vineyardResultRow[vineyardOwner]<br></info>";}
	if ($vineyardResultRow[vineyardStreetAddress] != 'null') {print "<info>$vineyardResultRow[vineyardStreetAddress]<br></info>";}
	//Nest if/elseif/else print statements so that city, state, and postal print on one line, excluding nulls
	if ($vineyardResultRow[vineyardCity] != 'null') {
		print "<info>$vineyardResultRow[vineyardCity]";
		if ($vineyardResultRow[vineyardState] != 'null') {
			print ", $vineyardResultRow[vineyardState]";
			if ($vineyardResultRow[vineyardPostalCode] != 'null') {
				print " $vineyardResultRow[vineyardPostalCode]<br></info>";
			} else {print "<br></info>";}
		} elseif ($vineyardResultRow[vineyardPostalCode] != 'null') {
				print ", $vineyardResultRow[vineyardPostalCode]<br></info>";
		} else {print "<br></info>";}
	} elseif ($vineyardResultRow[vineyardState] != 'null') {
		print "<info>$vineyardResultRow[vineyardState]";
		if ($vineyardResultRow[vineyardPostalCode] != 'null') {
			print " $vineyardResultRow[vineyardPostalCode]<br></info>";
		} else {print "<br></info>";}
	} elseif ($vineyardResultRow[vineyardPostalCode] != 'null') {
		print "<info>$vineyardResultRow[vineyardPostalCode]<br></info>";
	}
	if ($vineyardResultRow[vineyardCountry] != 'null') {print "<info>$vineyardResultRow[vineyardCountry]<br></info>";}
    if ($vineyardResultRow[vineyardSiteLink] != 'null') {print "<p>Find out more at: <a href=\"$vineyardResultRow[vineyardSiteLink]\">$vineyardResultRow[vineyardSiteLink]</a></p>";}
	print "</div></div>"; //close the vineyard detail container

	//generate google map for each vineyard from the mapLink in database
	print "<div class=\"w3-col s12 m12 l6\"><div class=\"w3-container\">";
	$mapurl = addslashes($vineyardResultRow[vineyardMapLink]); //make url php-friendly
	print "<iframe src=\"$mapurl\" width=\"65%\" height=\"100%\" frameborder=\"0\" style=\"border:0\" allowfullscreen></iframe></p>";
	print "</div></div>"; //close map container

	//Get bottle info for selected vineyard from database
	//Join tables in order to retrieve all displayed information
	$vineyardBottleQuery = "SELECT bottle.bottleID AS bottleID, bottle.name AS bottleName, bottle.pictureLink AS bottlePictureLink,
					bottle.vintage AS bottleVintage, bottle.abv AS bottleABV, bottle.vineyardID AS bottleVineyardID,
					bottle.rating AS bottleRating, bottle.price AS bottlePrice, region.name AS regionName, type.name AS typeName
					FROM vineyard, bottle, region, type
					WHERE vineyard.vineyardID = bottle.vineyardID AND vineyard.vineyardID = $cleanVineyardID AND type.typeID = bottle.typeID AND bottle.regionID = region.regionID
					ORDER BY bottleName";
	$vineyardBottleResult = mysqli_query($link, $vineyardBottleQuery);

	//Create container for bottle display
	print "<br><div class=\"w3-container 3-padding-8\">";
	print "<br/><h3>Bottles in our collection:</h3>";

	//Error handling to make sure there is a result to display
	if(($vineyardBottleResult) && ($vineyardBottleResult->num_rows !==0)) {
		print "<div class=\"w3-row-padding\">";  //create space between cards
		//loop through results
		while($vineyardBottleResultRow = mysqli_fetch_array($vineyardBottleResult)){
			//create link to bottle page for more details
			$url = "bottle.php?bottleID=".$vineyardBottleResultRow[bottleID];

			//show the results in a card
			print "<div class=\"w3-col s12 m6 l4 w3-padding-16\"><div class=\"w3-card-4 w3-dark-grey\"><div class=\"w3-container\">";
			print "<h3><a href = \"$url\">$vineyardBottleResultRow[bottleName]</a></h3>";
			print "<h4>($vineyardBottleResultRow[bottleVintage]) | $vineyardBottleResultRow[typeName]</h4>";
			print "<img src=\"\winos\images\\$vineyardBottleResultRow[bottlePictureLink]\" alt=\"$vineyardBottleResultRow[bottleName]\" class=\"w3-left w3-padding-small\" style=\"height:100%;max-height:150px\">";
			print "<p> RATING: $vineyardBottleResultRow[bottleRating]";
			print "<br> PRICE: $$vineyardBottleResultRow[bottlePrice]";
			print "<br> REGION: $vineyardBottleResultRow[regionName]";
			print "<div class=\"w3-container\"><br></div></div></div></div>";
		}
		print "</div></div>"; // Close the card and container
	}
	else { // Print a generic message if no bottles available for the vineyard
		print "<p> No selections are currently available.</p><br/>";
	}
}
else {  // If vineyardID not set, print a generic message
	print "<p>Choose a vineyard to see more! </p><br/>";
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
