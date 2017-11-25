<!doctype html>
<html lang="en">
<script src="https://www.w3schools.com/lib/w3.js"></script>
<head>
	<meta charset="utf-8">
	<title>Dinner - Winos</title>  <!--bottle page-->
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

/* Header image (Logo. Full height) */
.bgimg-1 {
    background-image: url("/winos/images/dinner.jpg");
    min-height: 30%;
}

</style>

</head>
<body>

<!-- Navbar (sit on top) -->
<div w3-include-html="wino_header.html"></div>

<!-- Header Image with Logo Text -->
<div class="bgimg-1 w3-display-container w3-opacity-min" id="home">
  <div class="w3-display-middle" style="white-space:nowrap;">
    <span class="w3-center w3-padding-large w3-black w3-xlarge w3-wide w3-animate-opacity">WHAT'S FOR DINNER?</span>
  </div>
</div>

<div class="w3-container w3-padding-16" id="result">
<!-- Instructions for filling out search box -->
<h4>Tell us what you're eating in <b>one</b> word!</h4>
<h5>(If you say too much, we won't listen.)</h5>
<br/>
<!-- Search box for What's for Dinner?" -->
<form action='dinner.php' method='GET'>
Search: <input type='text' size='40' name='search'>
<input type='submit' value='submit' name='submit'>
</form>

<?php
// ini_set('display_errors', 1); //Enable error display

// Set up database variables and open database connection
$host = "localhost";
$un = "winos";
$pw = "drinkdrankdrunk";
$db = "winos";
$link = mysqli_connect($host, $un, $pw, $db);

// If a search query has been submitted...
if (isset($_GET['search'])) {
	// ...determine if search terms match anything in bottle's enjoyWith field 
	$search = $_GET['search'];
	$cleanSearch = preg_replace("/[^ 0-9a-zA-Z]+/", " ", $search);  // sanitize user submission
	$searchQuery = "SELECT bottle.bottleID AS bottleID, bottle.name AS bottleName, bottle.vintage AS bottleVintage,
						bottle.rating AS bottleRating, vineyard.vineyardID AS vineyardID, vineyard.name AS vineyardName, 
						type.typeID AS typeID, type.name AS typeName, bottle.pictureLink AS bottlePictureLink, 
						bottle.price AS bottlePrice, bottle.abv AS bottleABV, bottle.enjoyWith AS bottleEnjoyWith,
						bottle.tastingNotes AS bottleTastingNotes
					FROM bottle, vineyard, type
					WHERE bottle.vineyardID = vineyard.vineyardID AND bottle.typeID = type.typeID AND bottle.enjoyWith LIKE '%$cleanSearch%'
					ORDER BY rating DESC";
	$searchResult = mysqli_query($link, $searchQuery);
		

	// If results are found...
	if(($searchResult) && ($searchResult->num_rows !==0)) {
		// ...display results
		print "<br/><h3>Bottles that pair with your request:</h3>";
		print "<div class=\"w3-row-padding\">";  // Set up bottles to show in cards
		// While there are bottles found...
		while($searchResultRow = mysqli_fetch_array($searchResult)){
			// ...show the results in a card
			print "<div class=\"w3-col s12 m6 l4 w3-padding-16\"><div class=\"w3-card-4 w3-dark-grey\"><div class=\"w3-container\">";
			$url = "bottle.php?bottleID=".$searchResultRow['bottleID'];
			print "<h3><a href = \"$url\">$searchResultRow[bottleName]</a></h3>";
			print "<h4>($searchResultRow[bottleVintage]) | $searchResultRow[typeName]</h4>";
			print "<img src=\"\winos\images\\$searchResultRow[bottlePictureLink]\" alt=\"$searchResultRow[bottleName]\" class=\"w3-left w3-padding-small\" style=\"height:100%;max-height:250px\">";
			print "<p> PAIRS WITH: $searchResultRow[bottleEnjoyWith]";
			print "<br> RATING: $searchResultRow[bottleRating]";
			print "<br> PRICE: $$searchResultRow[bottlePrice]";
			print "<br><br> TASTING NOTES: $searchResultRow[bottleTastingNotes]";
			print "<div class=\"w3-container\"><br></div></div></div></div>";
		}
		print "</div>"; // Close the card
	} else { // Otherwise, if no bottles found, print generic message
		print "<p> No selections are currently available. Maybe you ought to rethink what's for dinner?</p><br/>";
	}

// Close the database link	
mysqli_close($link);
}
?>
</div>
</body>

<!-- Footer -->
<div w3-include-html="wino_footer.html"></div>

<script src="wino_script_src.js">
</script>

</html>
