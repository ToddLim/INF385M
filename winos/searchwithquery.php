<!doctype html>
<html lang="en">
<script src="https://www.w3schools.com/lib/w3.js"></script>
<head>
	<meta charset="utf-8">
	<title>Search - Winos</title>  <!--our search page-->
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
    background-image: url("/winos/images/search.jpg");
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
    <span class="w3-center w3-padding-large w3-black w3-xlarge w3-wide w3-animate-opacity">FIND A WINE</span>
  </div>
</div>

<div class="w3-container w3-padding-16" id="result">


<?php
// ini_set('display_errors', 1); //Enable error display

// Database variables and open database connection
$host = "localhost";
$un = "winos";
$pw = "drinkdrankdrunk";
$db = "winos";
$link = mysqli_connect($host, $un, $pw, $db);

//Put TYPE, CONTINENT, PRICE, selections and SUBMIT buttons in a container for formatting purposes 
print "<div class=\"w3-row\">";

// Begin form for search selections
print "<form action=\"searchwithquery.php\" method=\"GET\">";

// TYPE checkboxes populted form database
print "<div class=\"w3-container w3-third\">TYPE<br>"; //TYPE will take up one third of the row
$listTypeResult = mysqli_query($link, "SELECT typeID, name FROM type ORDER BY name");
while ($typeRow = mysqli_fetch_array($listTypeResult)) {
	print "<input name=\"$typeRow[name]\" value=\"$typeRow[typeID]\" class=\"w3-check\" type=\"checkbox\"><label> $typeRow[name]</label><br>";
}
print "</div>";

// CONTINENT checkboxes populated from database
print "<div class=\"w3-container w3-third\">CONTINENT<br/>";  //CONTINENT will take up one third of the row
$listContinentResult = mysqli_query($link, "SELECT continentID, name FROM continent ORDER BY name");
while ($continentRow = mysqli_fetch_array($listContinentResult)) {
	print "<div><input name=\"$continentRow[name]\" value=\"$continentRow[continentID]\" class=\"w3-check\" type=\"checkbox\"><label> $continentRow[name]</label></div>";
}
print "</div>";

// PRICE selection boxes
print "<div class=\"w3-container w3-third\">PRICE<br/>";
print "Min:     <input type=\"text\" name=\"priceMin\"><br>";
print "Max: <input type=\"text\" name=\"priceMax\"><br><br>";
print "</div></div>";

// Submit and reset buttons
print "<div class=\"w3-container w3-center\">";
print "<input type=\"submit\" name=\"submit\" value=\"Find a wine\">";
print "<input type=\"reset\" value=\"Reset Form\"></div>";

//Close the selection form
print "</form>";

//Close the TYPE, CONTINENT, PRICE, SUBMIT container
print "</div>";



// If form has been submitted...
if(isset($_GET['submit'])) {
	// ...get search results
	
	// We need to dynamically construct a master search query composed of: (1) a WHERE clause composed of three ANDed pieces: 
	//			(a) a TYPE clause (the checked types ORed together), (b) a continent clause (the checked types ORed together),
	//			and (c) a price clause (>PriceMin OR <PriceMax); (2) a start stub, which will be different based on whether a
	//			continent was selected or not; and (3) and end stub
	// The below code could have been written more for efficiency, but was instead written for trying different methods for
	//			eductional purposes
	
	// 1(a). Construct the TYPE portion of the WHERE clause
	// Set initial variables
	$typeClause = "";  // Start the TYPE portion of the WHERE clause empty
	$typeConjunction = "(";  // First part of eventual TYPE portion of WHERE clause
	
	// Loop through each passed $_GET key to see if it is a TYPE, and if so, add it to the TYPE portion of the potiential WHERE clause
	//		This showcases using switch, which is not scalable
	foreach ($_GET as $getKey => $getValue) {
		switch ($getKey) {
			case 'Brut_Sparkling':
			case 'Brut_Sparkling_Rose':
			case 'Fruit':
			case 'Ice':
			case 'Port':
			case 'Red':
			case 'Rose':
			case 'Sweet_Red':
			case 'Sweet_White':
			case 'White':
				if (!empty($getValue)) {
					$cleanGetValue = preg_replace ("/[^ 0-9a-zA-Z]+/", "", $getValue);
					$typeClause .= $typeConjunction."bottle.typeID=$cleanGetValue";
					$typeConjunction = " OR ";
					break;
				}
		}				
	}
	// If TYPE portion of WHERE clause exists, then add the closing parenthesis
	if (!empty($typeClause)) { $typeClause .= ")";}
	
	// 1(b). Construct the CONTINENT portion of the WHERE clause
	// Set initial variables
	$continentClause = "";  // Start the TYPE portion of the WHERE clause empty
	$continentConjunction = "(";  // First part of eventual TYPE portion of WHERE clause
	
	// Get the continent names
	$continentQuery = "SELECT * FROM continent";
	$continentQueryResult = mysqli_query($link, $continentQuery);
	
	// Provided getting CONTINENTS works..
	if(($continentQueryResult) && ($continentQueryResult->num_rows !==0)) {
		// ...then while there are CONTINENTS to be tested...
		while($continentQueryResultRow = mysqli_fetch_array($continentQueryResult)) {
			// ...loop through each passed $_GET key to see if it is a CONTINENT, and if so, add it to the CONTINENT portion of the potiential WHERE clause
			//		This showcases a scalable method
			foreach ($_GET as $getKey => $getValue) {
				$cleanGetKey = preg_replace ("/[^ 0-9a-zA-Z]+/", " ", $getKey);
				$cleanGetValue = preg_replace ("/[^ 0-9a-zA-Z]+/", "", $getValue);
				if(!empty($cleanGetKey) && ($cleanGetKey == $continentQueryResultRow['name'])) {
					$continentClause .= $continentConjunction."(region.continentID=$cleanGetValue AND bottle.regionID=region.regionID)";
					$continentConjunction = " OR ";
				}
			}
		}
		// If CONTINENT portion of WHERE clause exists, then add the closing parenthesis
		if (!empty($continentClause)) {$continentClause .= ")";}
	}

	
	// 1(c).  Construct the PRICE portion of the WHERE clause
	// Set initial variables
	$priceClause = "";
	
	// Loop through each passed $_GET key to see if it is priceMin or priceMax, and if so create a clause to include in eventual query
	foreach($_GET as $getKey => $getValue) {
		$cleanGetKey = preg_replace ("/[^ 0-9a-zA-Z]+/", "", $getKey);
		$cleanGetValue = preg_replace ("/[^ 0-9a-zA-Z]+/", "", $getValue);
		if(($cleanGetKey == 'priceMin') && (!empty($cleanGetValue))) {
			$priceClause = "(bottle.price > $cleanGetValue)";
		}
		if(($cleanGetKey == 'priceMax') && !empty($cleanGetValue) && (!empty($priceClause))) {
			$priceClause .= " AND (bottle.price < $cleanGetValue)";
		} elseif(($cleanGetKey == 'priceMax') && !empty($cleanGetValue) && (empty($priceClause))) {
			$priceClause = "(bottle.price < $cleanGetValue)";
		}
	}
	
	// 2. Set the start stub -- which stub to use depends on whether user checked a continent
	if(!empty($continentClause)) {  // If a continent was selected, include region in the query start stub...
		$searchBottleQueryStub = "SELECT bottle.bottleID AS bottleID, bottle.name AS bottleName, bottle.vintage AS bottleVintage, bottle.abv AS bottleABV, bottle.rating AS bottleRating, 
					bottle.pictureLink AS bottlePictureLink, bottle.price AS bottlePrice, vineyard.name AS vineyardName, type.name AS typeName, vineyard.siteLink AS vineyardURL
					FROM bottle, region, vineyard, type";
	} else {  //...otherwise do not include region in the query
		$searchBottleQueryStub = "SELECT bottle.bottleID AS bottleID, bottle.name AS bottleName, bottle.vintage AS bottleVintage, bottle.abv AS bottleABV, bottle.rating AS bottleRating, 
					bottle.pictureLink AS bottlePictureLink, bottle.price AS bottlePrice, vineyard.name AS vineyardName, type.name AS typeName, vineyard.siteLink AS vineyardURL
					FROM bottle, vineyard, type";
	}
	
	// Build the master search query from our component parts
	// Figure out which clauses are not empty and use them to build the master query
	if (!empty($typeClause)) {
		if(!empty($continentClause)) {
			if(!empty($priceClause)) {
				$masterSearchBottleQuery = $searchBottleQueryStub." WHERE ".$typeClause." AND ".$continentClause." AND ".$priceClause." AND vineyard.vineyardID = bottle.vineyardID AND bottle.typeID = type.typeID";
			} else {
				$masterSearchBottleQuery = $searchBottleQueryStub." WHERE ".$typeClause." AND ".$continentClause." AND vineyard.vineyardID = bottle.vineyardID AND bottle.typeID = type.typeID";
			}
		} elseif(!empty($priceClause)) {
				$masterSearchBottleQuery = $searchBottleQueryStub." WHERE ".$typeClause." AND ".$priceClause." AND vineyard.vineyardID = bottle.vineyardID AND bottle.typeID = type.typeID";
			} else {
				$masterSearchBottleQuery = $searchBottleQueryStub." WHERE ".$typeClause." AND vineyard.vineyardID = bottle.vineyardID AND bottle.typeID = type.typeID";
			}
	} elseif(!empty($continentClause)) {
		if(!empty($priceClause)) {
			$masterSearchBottleQuery = $searchBottleQueryStub." WHERE ".$continentClause." AND ".$priceClause." AND vineyard.vineyardID = bottle.vineyardID AND bottle.typeID = type.typeID";
			} else {
				$masterSearchBottleQuery = $searchBottleQueryStub." WHERE ".$continentClause." AND vineyard.vineyardID = bottle.vineyardID AND bottle.typeID = type.typeID";
			}
	} elseif(!empty($priceClause)) {
		$masterSearchBottleQuery = $searchBottleQueryStub." WHERE ".$priceClause." AND vineyard.vineyardID = bottle.vineyardID AND bottle.typeID = type.typeID";
	} else {
		$masterSearchBottleQuery = "";  // If no search criteria are selected, set up query to later print instructions for user
	}

	// 3.  Add in the end stub of the query
	$masterSearchBottleQuery .= " ORDER BY bottleName";
	
	// Show the query
	print $masterSearchBottleQuery;
	
	// Show results
	print "<br/><h3>Bottles in our collection:</h3>";
	$masterSearchBottleResult = mysqli_query($link, $masterSearchBottleQuery);
	// If results, create wrapping cards to display the search results
	if(($masterSearchBottleResult) && ($masterSearchBottleResult->num_rows !==0)) {
		print "<div class=\"w3-row-padding\">";  // Set up results to show in cards
		while($masterSearchBottleResultRow = mysqli_fetch_array($masterSearchBottleResult)){
			$url = "bottle.php?bottleID=".$masterSearchBottleResultRow['bottleID'];
		
			//show the results in a card
			print "<div class=\"w3-col s12 m6 l4 w3-padding-16\"><div class=\"w3-card-4 w3-dark-grey\"><div class=\"w3-container\">";
			print "<h3><a href = \"$url\">$masterSearchBottleResultRow[bottleName]</a></h3>";
			print "<h4>($masterSearchBottleResultRow[bottleVintage]) | $masterSearchBottleResultRow[typeName]</h4>";
			print "<img src=\"\winos\images\\$masterSearchBottleResultRow[bottlePictureLink]\" alt=\"$masterSearchBottleResultRow[bottleName]\" class=\"w3-left w3-padding-small\" style=\"height:100%;max-height:150px\">";
			print "<p> FROM: <a href=\"$masterSearchBottleResultRow[vineyardURL]\">$masterSearchBottleResultRow[vineyardName]</a>";
			print "<p> PRICE: $$masterSearchBottleResultRow[bottlePrice]</p>";
			print "<p> RATING: $masterSearchBottleResultRow[bottleRating]</p>";
			print "<div class=\"w3-container\"><br></div></div></div></div>";
		}
		print "</div>"; // Close the card container
	// If no search criteria were selected, print instructions for user
	} elseif ($masterSearchBottleQuery = ' ORDER BY bottleName') {
		print "Please select search criteria.";
	} else {
	// Otherwise show no results
		print "<p> No selections match your criteria.</p><br/>";
	}
}

// Close the database link
mysqli_close($link);

?>
</div>
</body>

<!-- Footer -->
<div w3-include-html="wino_footer.html"></div> 

<script src="wino_script_src.js">
</script>

</html>