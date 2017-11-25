<!doctype html>
<html lang="en">
<script src="https://www.w3schools.com/lib/w3.js"></script>
<head>
	<meta charset="utf-8">
	<title>Full Collection - Winos</title>  <!--full collection page-->
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
    background-image: url("/winos/images/full.jpg");
    min-height: 30%;
}
</style>

</head>
<body>

<!-- Navbar (sit on top) -->
<div w3-include-html="wino_header.html"></div>

<!--Header image and page title-->
<div class="bgimg-1 w3-display-container w3-opacity-min" id="home">
  <div class="w3-display-middle" style="white-space:nowrap;">
    <span class="w3-center w3-padding-large w3-black w3-xlarge w3-wide w3-animate-opacity">HAVE A LOOK AROUND</span>
  </div>
</div>

<!--Container for content in rest of page under header image-->
<div class="w3-container w3-padding-16" id="result">

<!-- Page Description -->
<h3>All wines in our collection, A-Z</h3>

<?php
// ini_set('display_errors', 1); //Enable error display

//Connect to the database
$host = "localhost";
$un = "winos";
$pw = "drinkdrankdrunk";
$db = "winos";
$link = mysqli_connect($host, $un, $pw, $db);

//Set max number of bottle results on a single page
$pageLimit = 21;

//See if we are on a specific page
if(isset($_GET['page'])) {
	//Get page info and sanitize
	$page = $_GET['page'];
	$cleanPage = preg_replace ("/[^ 0-9a-zA-Z]+/", "", $page);
	//If we are not on the first page, set the location of the first database result to display
	//This is one more than the number of pages into the results times the number of results on a page
	$pageOffset = (($cleanPage - 1) * $pageLimit) + 1;
	//Even if page = 1, cleanPage will be 1 and pageOffset will be 1
}
else {
	//If no page is set, make it the first page in the results with the first result to display
	$cleanPage = 1;
	$pageOffset = 1;
}

//Make query to get list of bottles
//Join tables to get all the details to display
//Put in alphabetical order
//Get back only the number of results for a page, at the correct starting point
$bottleQuery = "SELECT bottle.bottleID AS bottleID, bottle.name AS bottleName,
	bottle.pictureLink AS bottlePictureLink, bottle.vintage AS bottleVintage, type.name AS typeTypeName,
	bottle.price AS price, bottle.rating AS bottleRating,
	vineyard.name AS vineyardName
	FROM bottle, type, region, vineyard
	WHERE bottle.typeID = type.typeID
	AND bottle.regionID = region.regionID
	AND bottle.vineyardID = vineyard.vineyardID
	ORDER BY bottleName
	LIMIT $pageLimit OFFSET $pageOffset";

//Get bottle data
$bottleResult = mysqli_query($link, $bottleQuery);

//Check that there is a result before displaying
if(($bottleResult->num_rows !==0)) {
	print "<div class=\"w3-row-padding\">";  // Create space between cards in a row-padding
	//Loop through the results and create a card to display the results of each
	while($bottleResultRow = mysqli_fetch_array($bottleResult)){
		//Create URL to link to bottle page for more details
		$url = "bottle.php?bottleID=".$bottleResultRow['bottleID'];
		//create the card containers and properties, then print data
		print "<div class=\"w3-col s12 m6 l4 w3-padding-16\"><div class=\"w3-card-4 w3-dark-grey\"><div class=\"w3-container\">";
		print "<h3><a href = \"$url\">$bottleResultRow[bottleName]</a></h3>";
		print "<h4>($bottleResultRow[bottleVintage]) | $bottleResultRow[typeTypeName]</h4>";
		print "<img src=\"\winos\images\\$bottleResultRow[bottlePictureLink]\" class=\"w3-left w3-padding-small\" style=\"height:100%;max-height:150px\">";
		print "<p> FROM: $bottleResultRow[vineyardName]</p>";
		print "<p> RATING: $bottleResultRow[bottleRating]</p>";
		print "<p> PRICE: $$bottleResultRow[price]</p></div>";
		print "<div class=\"w3-container\"><br></div></div></div>";  //close containers
	}
	print "</div>";  //close spacing property
}
else { //Print an error message if no results came back
	print "<p>Whoops!  Something's wrong. </p><br/>";
}

//Pagination
//Get the total number of rows in the table to determine number of pages needed
$numRowsQuery = "SELECT COUNT(bottleID) AS count FROM bottle";
$numRowsResult = mysqli_query($link, $numRowsQuery);
$numRowsResultRow = mysqli_fetch_array($numRowsResult);
//Round up the number of results divided by the number of results on a page
//Always take the ceiling so that even the last, partial page will be available
$numPages = ceil($numRowsResultRow[count]/$pageLimit);

//Create the container for the pagination buttons and center it
print "<div class=\"w3-center\"><div class=\"w3-bar\">";

//Create and define links for buttons
//The 'one page back' button will need to be set based on what the current page is
if ($cleanPage >1){  //if not on first page, subtract a page number
	print "<a href=\"full.php?page=".($cleanPage-1)."\" class=\"w3-button\">&laquo;</a>";
}
else {  //if on first page, make it the first page
	print "<a href=\"full.php\" class=\"w3-button\">&laquo;</a>";
}
//create and set links for specific page buttons
//loop through the number of pages to create a button for each
//set the link on the button to the page number of that button
for ($i = 1; $i<=$numPages; $i++) {
	if ($i == $cleanPage){  //if the current index is also the current page, highlight it
		print "<a href=\"full.php?page=".$i."\" class=\"w3-button w3-red\">$i</a>";
	}
	else {
		print "<a href=\"full.php?page=".$i."\" class=\"w3-button\">$i</a>";
	}
}
//The 'one page forward' button will need to be set based on what the current page is
if ($cleanPage < $numPages){ //if not on last page, add a page number
	print "<a href=\"full.php?page=".($cleanPage+1)."\" class=\"w3-button\">&raquo;</a>";
}
else {  //if on last page, make it the last page
	print "<a href=\"full.php?page=".$numPages."\" class=\"w3-button\">&raquo;</a>";
}
print "</div></div>"; //close pagination containers
//Done with pagination!

//Cleanup
mysqli_close($link);

?>
</div>

</body>

<!-- Footer -->
<div w3-include-html="wino_footer.html"></div>

<script src="wino_script_src.js">
</script>

</html>
