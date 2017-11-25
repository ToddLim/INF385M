<!doctype html>
<html lang="en">
<script src="https://www.w3schools.com/lib/w3.js"></script>
<head>
	<meta charset="utf-8">
	<title>Bottle - Winos</title>  <!--bottle page-->
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
    background-image: url("/winos/images/bottle_page.jpg");
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
    <span class="w3-center w3-padding-large w3-black w3-xlarge w3-wide w3-animate-opacity">ABOUT YOUR BOTTLE</span>
  </div>
</div>

<!--Container for content in rest of page under header image-->
<div class="w3-container w3-padding-16" id="result">
<?php
//ini_set('display_errors', 1); //Enable error display

//Connect to database and get details if we have a bottle to display, otherwise show an error message
if (isset($_GET['bottleID'])) {
	//Get bottle from URL
	$bottle = $_GET['bottleID'];
	//Sanitize
	$cleanBottleID = preg_replace ("/[^ 0-9a-zA-Z]+/", "", $bottle);
	
	//Connect to database
	$host = "localhost";
	$un = "winos";
	$pw = "drinkdrankdrunk";
	$db = "winos";
	$link = mysqli_connect($host, $un, $pw, $db);
	
	//Create query to get bottle details
	//Join tables to get all details to display
	//Get details only for the bottleID selected in URL
	$bottleDetailQuery = "SELECT bottle.name AS bottleName, bottle.rating AS bottleRating, bottle.price AS bottlePrice, 
	bottle.vintage AS bottleVintage, bottle.tastingNotes AS bottleTastingNotes,
	bottle.enjoyWith AS bottleEnjoyWith, bottle.abv AS bottleABV, bottle.pictureLink AS bottlePictureLink, region.country AS regionCountry, 
	region.regionID AS regionRegionID, region.name AS regionName, type.name AS typeName,
	vineyard.name AS vineyardName, vineyard.vineyardID AS vineyardVineyardID
	FROM bottle, region, type, vineyard
	WHERE bottle.typeID = type.typeID
	AND bottle.regionID = region.regionID
	AND bottle.vineyardID = vineyard.vineyardID
	AND bottleID = $cleanBottleID";
	$bottleDetailResult = mysqli_query($link, $bottleDetailQuery);
	
	//Only print details if bottleId exists in database
	if(mysqli_num_rows($bottleDetailResult) != 0) {
	
		$bottleDetailResultRow = mysqli_fetch_array($bottleDetailResult);

		//Create query to get the grapes for a particular bottle
		//Join with junction table
		//Get only grapes for the bottleID selected in the URL
		$grapeDetailQuery = "SELECT bottle.name AS bottleName, grape.name AS grapeName, grape.grapeID AS grapeGrapeID, bottleGrape.grapePercent AS grapePercent
		FROM bottle, grape, bottleGrape
		WHERE bottle.bottleID = bottleGrape.bottleID
		AND grape.grapeID = bottleGrape.grapeID
		AND bottle.bottleId = $cleanBottleID";
		$grapeDetailResult = mysqli_query($link, $grapeDetailQuery);

		//create URLs and picture links for linking in result printout
		$bottleUrl = "bottle.php?bottleID=".$cleanBottleID;
		$vineyardUrl = "vineyard.php?vineyardID=".$bottleDetailResultRow['vineyardVineyardID'];
		$regionUrl = "region.php?regionID=".$bottleDetailResultRow['regionRegionID'];
		$imageFilePath = "/winos/images/".$bottleDetailResultRow['bottlePictureLink'];
		$ratingPicture = $bottleDetailResultRow['bottleRating'];
		$ratingFilePath = "/winos/images/".$ratingPicture.".png";
		
		//----START DISPLAY----
		print "<h1>$bottleDetailResultRow[bottleName] ($bottleDetailResultRow[bottleVintage])</h1>";
		
		//Create columns and containers for image to display below bottle name
		print "<div class=\"w3-col s12 m4 l4\"><div class=\"w3-container\"><img src=\"$imageFilePath\" style = \"width:100%\"></div></div>";

		//Create column and container next to image, display other bottle detail info
		print "<div class=\"w3-col s12 m8 l8\"><div class=\"w3-container\">";
		print "<h4><a href = \"$vineyardUrl\">$bottleDetailResultRow[vineyardName]</a></h4>";
		print "<div class=\"w3-container\"><img src=\"$ratingFilePath\" style = \"width:10%\"></div>";  //show rating as a star picture
		print "<p>Price: $$bottleDetailResultRow[bottlePrice]</p>";
		
		//----WHAT'S IN THE BOTTLE----
		print "<br><h4>What's in the Bottle?</h4>";
		//print grape info, loop through grape results
		while($grapeDetailResultRow = mysqli_fetch_array($grapeDetailResult)) {
			//create URL to link to more details on the grape
			$grapeUrl = "grape.php?grapeID=".$grapeDetailResultRow['grapeGrapeID'];
			
			//check for nulls and clean up display if exist
			if ($grapeDetailResultRow['grapePercent'] == 0) { 
				print "<a href = \"$grapeUrl\">$grapeDetailResultRow[grapeName]</a>: Percent Unknown <br />"; 
			} 
			else {
				print "<a href = \"$grapeUrl\">$grapeDetailResultRow[grapeName]</a>: $grapeDetailResultRow[grapePercent]% <br />"; 
			}
		}  //done with grapes
		print "<p>From: <a href = \"$regionUrl\">$bottleDetailResultRow[regionName]</a>, $bottleDetailResultRow[regionCountry]</p>";
		//take out null values if they exist in bottleABV
		if ($bottleDetailResultRow['bottleABV'] == 0) { 
		   print "<p>ABV: Unknown</p>";
		} 
		else { 
			  print "<p>ABV: $bottleDetailResultRow[bottleABV]%</p>"; 
		}		

		//----HOW TO ENJOY----
		print "<br><h4>How to Enjoy This $bottleDetailResultRow[typeName] Wine</h4>";
		print "<h5>Tasting Notes:</h5> <p>$bottleDetailResultRow[bottleTastingNotes]</p>";
		print "<h5>Pairings:</h5> <p>$bottleDetailResultRow[bottleEnjoyWith]</p>";
		print "</div></div>";  //close other detail containers
		
		//DONE WITH PRINTING RESULTS
	}
	else {  //if bottle id is invalid, print error
       print "Bottle not found";
    }
    
	//Link back to collection, especially if error is thrown
	print "<br><div class=\"w3-container 3-padding-8\">Go find something else interesting in <a href=\"http://hornet.ischool.utexas.edu/winos/full.php\">our collection.</a></div>";
	
	// Comments section
	// Comment section header
	print "<br/><br/><div class=\"w3-container\"><div class=\"w3-gray\"><h5> Comments </h5></div>"; // Comment section header
	
	// Create query to get comments
	// Join tables to get comments only for selected bottleID 
	$commentListQuery = "SELECT comment.user AS commentUser, comment.comment AS commentComment
						FROM comment, bottle
						WHERE comment.bottleID = bottle.bottleID AND bottle.bottleID = $cleanBottleID";
	$commentListResult = mysqli_query($link, $commentListQuery);
	
	//Loop through results and display
	while($commentListResultRow = mysqli_fetch_array($commentListResult)) {
		print "<div class=\"w3-border\"><p>$commentListResultRow[commentUser] said: $commentListResultRow[commentComment]</p></div>";
	}
	print "</div>";  // Closes the w3-container <div>
	
	// Validate form data for new comment
	if (isset($_GET['Submit'])) {  	// If comment form has been submitted
		$problem = FALSE;
		if (!empty($_GET['user']) && !empty($_GET['comment'])) {  // And if both 'user' and 'comment' fields contain data, clean the data
			$user = $_GET['user'];
			$cleanUser = preg_replace ("/[^ 0-9a-zA-Z]+/", "", $user);
			$comment = $_GET['comment'];
			$cleanComment = preg_replace ("/[^ 0-9a-zA-Z]+/", "", $comment);
		} else {  // Otherwise if 'user' and/or 'comment' is empty, print error message
			print "<p style=\"color: red;\">Please submit both your name and a comment.</p>";
			$problem = TRUE;
		}
	
		if (!$problem) {  // If no problem with 'user' or 'comment' then define SQL query and insert 'user' and 'comment' into the database
			$addComment = "INSERT INTO comment (user, comment, bottleID) VALUES ('$cleanUser', '$cleanComment', '$cleanBottleID')";
			if (@mysqli_query($link, $addComment)) {
				print "<p>Comment added.</p>";
			} else { // If problem with inserting data, print an error message
				print "<p>Comment could not be added.</p>";
			}
		}
	}
	
	// Display the comment form
	print "<br/><br/><div class=\"w3-container\"><h5>Please leave a comment about this wine</h5>";
	print "<form action=\"bottle.php\" method=\"GET\">";
	print "<p>Your name: <input type=\"text\" name=\"user\" size=\"40\"></p>";
	print "<p>Comment: <textarea name=\"comment\"></textarea></p>";
	print "<input type=\"hidden\" value=\"$cleanBottleID\" name=\"bottleID\">";
	print "<input type=\"Submit\" name=\"Submit\" value=\"Submit\"></form></div><br/>";
		
	//close connection
	mysqli_close($link);
}
else { //if no bottle id
	print "<h1>Whoops!</h1>";
	print "<p>Go find something interesting in <a href=\"http://hornet.ischool.utexas.edu/winos/full.php\">our collection.</a></p>";
}

?>
</div>
</body>

<!-- Footer -->
<div w3-include-html="wino_footer.html"></div>

<script src="wino_script_src.js">
</script>

</html>
