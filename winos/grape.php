<!doctype html>
<html lang="en">
<script src="https://www.w3schools.com/lib/w3.js"></script>
<head>
	<meta charset="utf-8">
	<title>Grapes</title>  <!--grapes page-->
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

/* Header Image */
.bgimg-1 {
    background-image: url("/winos/images/grape_page.jpg");
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
    <span class="w3-center w3-padding-large w3-black w3-xlarge w3-wide w3-animate-opacity">VARIETALS</span>
  </div>
</div>

<div class="w3-container w3-padding-16" id="result">

<!-- Page Description -->
<h6>The term <b>varietal</b> refers to the main type of grape that makes up a particular wine.
	Sometimes a wine is referred to by it's varietal, rather than it's region.
<br>Check out the varietals featured in our collection:</h6>

<?php

//ini_set('display_errors', 1); //Enable error display

//Set variables for database connections
$host = "localhost";
$un = "winos";
$pw = "drinkdrankdrunk";
$db = "winos";
$link = mysqli_connect($host, $un, $pw, $db);

//set self-referential form
print "<form action=\"grape.php\" method=\"GET\">";
//Populate dropdown menu for possible grapes to view from database query
print "<select name=\"grapeID\"><option value=\"Choose\"> Please choose your grape</option>";
$listresult = mysqli_query($link, "SELECT grapeID, name FROM grape ORDER BY name");
	while ($row = mysqli_fetch_array($listresult)) {
		print "<option value='$row[grapeID]'>$row[name]</option>";
	}
print "</select>";
print "<input type=\"submit\" value=\"Explore\">";
print "</form>";

//if a grape is requested, print grape info; otherwise print generic message
if(isset($_GET['grapeID'])) {
	//Get grape info
	$grape = $_GET['grapeID'];
	$cleanGrapeID = preg_replace("/[^ 0-9a-zA-Z]+/", "", $grape); //sanitize
	$grapeQuery = "SELECT grapeID, name AS grapeName, description AS grapeDescription, wikilink AS grapeWikilink
								 FROM grape
								 WHERE grapeID = $cleanGrapeID";
	$grapeResult = mysqli_query($link, $grapeQuery);
	$grapeResultRow = mysqli_fetch_array($grapeResult);

	//Print our grape info (Name which links to wikipage, description), ignoring null values
	if ($grapeResultRow['grapeWikilink'] != 'null') {print "<h1>Varietal: <a href=\"$grapeResultRow[grapeWikilink]\">$grapeResultRow[grapeName]</a></h1>";}
		else {print "<p><h1>Varietal: $grapeResultRow[grapeName]</h1></p>";}
	if ($grapeResultRow['grapeDescription'] != 'null') {print "<p>$grapeResultRow[grapeDescription]</p>";}
		else {print "<p></p>";}

//Select bottle information for each grape
//Join tables in order to retrieve all displayed information
$grapeBottleQuery = "SELECT bottle.bottleID AS bottleID, bottle.name AS bottleName, bottle.pictureLink AS bottlePictureLink,
				bottle.vintage AS bottleVintage, bottle.abv AS bottleABV, bottle.price AS bottlePrice, bottle.rating AS bottleRating,
				vineyard.name AS vineyardName, type.name AS type
				FROM grape, bottle, bottleGrape, vineyard, type
				WHERE grape.grapeID = bottleGrape.grapeID
				AND vineyard.vineyardID = bottle.vineyardID
				AND type.typeID = bottle.typeID
				AND bottle.bottleID = bottleGrape.bottleID AND grape.grapeID = $cleanGrapeID
				ORDER BY bottleName";
$grapeBottleResult = mysqli_query($link, $grapeBottleQuery);

print "<br/><h3>Bottles that contain this grape:</h3>";

//Error handling to make sure there is a result to display
if(($grapeBottleResult) && ($grapeBottleResult->num_rows !==0)) {
	print "<div class=\"w3-row-padding\">";  //Create space between cards
	//loop through results
	while ($grapeBottleResultRow = mysqli_fetch_array($grapeBottleResult)) {
		//create link to bottle page for more details
		$url = "bottle.php?bottleID=".$grapeBottleResultRow['bottleID'];

		//show the results in a card
		print "<div class=\"w3-col s12 m6 l4 w3-padding-16\"><div class=\"w3-card-4 w3-dark-grey\"><div class=\"w3-container\">";
		print "<h3><a href = \"$url\">$grapeBottleResultRow[bottleName]</a></h3>";
		print "<h4>($grapeBottleResultRow[bottleVintage]) | $grapeBottleResultRow[type]</h4>";
		print "<img src=\"\winos\images\\$grapeBottleResultRow[bottlePictureLink]\" alt=\"$grapeBottleResultRow[bottleName]\" class=\"w3-left w3-padding-small\" style=\"height:100%;max-height:150px\">";
		print "<p> FROM: $grapeBottleResultRow[vineyardName]";
		print "<br> RATING: $grapeBottleResultRow[bottleRating]";
		print "<br> PRICE: $$grapeBottleResultRow[bottlePrice]";
		print "<div class=\"w3-container\"><br></div></div></div></div>";
	}
	print "</div>"; // Close the card
	}
	else { // Print a generic message if no bottles available for the grape
		print "<p> No selections are currently available.</p><br/>";
  	}
}
else {  // If grapeID not set, print a generic message
	print "<p>Choose a varietal to see more! </p><br/>";
}

mysqli_close($link);
?>

</body>

<!-- Footer -->
<div w3-include-html="wino_footer.html"></div>

<script src="wino_script_src.js">
</script>

</html>
