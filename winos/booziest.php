<!doctype html>
<html lang="en">
<script src="https://www.w3schools.com/lib/w3.js"></script>
<head>
	<meta charset="utf-8">
	<title>Booziest - Winos</title>  <!--bottle page-->
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
    background-image: url("/winos/images/booziest.jpg");
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
    <span class="w3-center w3-padding-large w3-black w3-xlarge w3-wide w3-animate-opacity">MOST BUZZ FOR YOUR BUCK!</span>
  </div>
</div>

<div class="w3-container w3-padding-16" id="result">

<!-- Page Description -->
<h4>Highest alcohol% by volume (ABV), per dollar spent</h4>

<?php
// ini_set('display_errors', 1); //Enable error display

// Set database variables and open database connection
$host = "localhost";
$un = "winos";
$pw = "drinkdrankdrunk";
$db = "winos";
$link = mysqli_connect($host, $un, $pw, $db);

//Get info on 12 bottles from SQL based on which abv/price is greatest
$booziestQuery = "SELECT bottle.bottleID AS ID, bottle.name AS bottleName, bottle.rating AS bottleRating, bottle.pictureLink as picLink,
						bottle.price as bottlePrice, bottle.abv as bottleABV, bottle.vintage as bottleVintage, type.name as typeName
					FROM bottle, type
					WHERE bottle.typeID = type.typeID
					ORDER BY bottleABV/bottlePrice DESC
					LIMIT 12";
$booziestResult = mysqli_query($link,$booziestQuery);

// If results are returned from the query...
if(($booziestResult) && ($booziestResult->num_rows !==0)) {
	// ...display the results
	print "<div class=\"w3-row-padding\">";  // Set up cards for displaying results
	$count = 0;  // Initialize variable to enumerate the top results
	// While there are still more bottles to display...
	while($bottleRow = mysqli_fetch_array($booziestResult)){
		// ...display them in a card
		$count += 1;  // Update enumeration variable
		print "<div class=\"w3-col s12 m6 l4 w3-padding-16\"><div class=\"w3-card-4 w3-dark-grey\"><div class=\"w3-container\">";
		$url = "bottle.php?bottleID=".$bottleRow[ID];
		print "<h3>#$count  <a href = \"$url\">$bottleRow[bottleName]</a></h3>";
		print "<h4>($bottleRow[bottleVintage]) | $bottleRow[typeName]</h4>";
		print "<img src=\"\winos\images\\$bottleRow[picLink]\" class=\"w3-left w3-padding-small\" style=\"height:100%;max-height:150px;width:auto;\">";
		print "<p> RATING: $bottleRow[bottleRating]";
		print "<br> PRICE: $$bottleRow[bottlePrice]";
		print "<br> ABV: $bottleRow[bottleABV]%";
		$boozeRatio = round($bottleRow[bottleABV]/$bottleRow[bottlePrice], 2);
		print "<br> ABV to dollar ratio: ".$boozeRatio." percent/dollar</p></div>";
		print "<div class=\"w3-container\"><br></div></div></div>";
	}
	print "</div>";
} else {  // If no results found, say so
	print "<p>No selections are currently available.  Perhaps sober up with some water?</p>";
}

print "<h3>Go find something else interesting in <a href=\"http://hornet.ischool.utexas.edu/winos/full.php\">our collection.</a></h3>";

// Close the database connection
mysqli_close($link);
?>
</div>
</body>

<!-- Footer -->
<div w3-include-html="wino_footer.html"></div>

<script src="wino_script_src.js">
</script>

</html>
