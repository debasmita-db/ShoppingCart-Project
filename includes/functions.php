<?php

function dbConnect($type) {
	if ($type == 'query') {
		$user = 'gotheelsquery';
		$pwd = 'gotheels';
	} elseif ($type == 'admin') {
		$user = 'gotheelsadmin';
		$pwd = 'gotheels';
	} else {
		exit('Unrecognized connection type');
	}
	//connection code goes here
	$conn = new mysqli('localhost', 'root','','gotheels') or die ('Cannot open database');
	//echo "database connected<br>";  //for troubleshooting
	return $conn;
}

function connect() {
    return new PDO('mysql:host=localhost;dbname=gotheels', 'root', '', array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
}



function dbClose($conn) {
	mysqli_close($conn);
}

function setCopyright($startYear) {
			ini_set('date.timezone', 'America/Los_Angeles');
			$thisYear = date('Y');
			
			if ($startYear == $thisYear) {
				echo $startYear;
			} else {
				echo "$startYear - $thisYear";
			}
}


function listCategories() {
			//connect to db
			$conn = dbConnect('query');
			
			//we need to set up a query to get the category information
			//create SQL from  two tables - products and category - via the lookup table
			$sql = "SELECT * FROM category";
				
			//submit the SQL query to the database and get the result
			$result = $conn->query($sql) or die(mysqli_error());	

			//open list HTML

			
			//loop through and display categories
			while ($row = $result->fetch_assoc()) {
				
				echo '<li><a href="categories.php?category_id='.$row['category_id'] .' "> ' . $row['category_name'] . '</a></li>';						
			}
			
			//close list HTML
			$result->free_result();
			dbClose($conn);
}


##################################################################
#Function to select random featured products based on # passed
##################################################################

//$number passed in call, indicates how many to get
	function featuredLolcat($number) { 
			//connect to db
			$conn = dbConnect('query');
			
			//get product info from  product and image tables
						   
			$sqlRandom = "SELECT * FROM product
						  LEFT JOIN image
						  ON product.product_id = image.product_id
						  ORDER BY RAND()
						  LIMIT $number";

			//submit the SQL query to the database and get the result
			$result = $conn->query($sqlRandom) or die(mysqli_error());	

			//loop through and display categories
			while ($row = $result->fetch_assoc()) {
				//loop through the results of the product query and display product info.
				//Plus build the link dynamically to a detail page
				echo '<div class="feature">';
				echo '<p><a href="product_details.php?product_id='.$row['product_id'] .' "> ' . $row['product_name'] . '</a></p><br/>';
				echo '<a href="product_details.php?product_id='.$row['product_id'] .' "><img src="images/' . $row['thumb_filename'] .'" /></a>';
				echo '</div>';
			}
			
			$result->free_result();
			dbClose($conn); 
			
}





function nukeMagicQuotes() {
  if (get_magic_quotes_gpc()) {
    function stripslashes_deep($value) {
      $value = is_array($value) ? array_map('stripslashes_deep', $value) : stripslashes($value);
      return $value;
      }
    $_POST = array_map('stripslashes_deep', $_POST);
    $_GET = array_map('stripslashes_deep', $_GET);
    $_COOKIE = array_map('stripslashes_deep', $_COOKIE);
    }
  }
  
##################################################################
//pagination
##################################################################


function create_navbar($start_number, $items_per_page, $count) {
// Creates a navigation bar
	$current_page = $_SERVER["PHP_SELF"];
	if (($start_number < 0) || (! is_numeric($start_number))) {
		$start_number = 0;
	}
	$navbar = "";
	$prev_navbar = "";
	$next_navbar = "";
	if ($count > $items_per_page) {
		$nav_count = 0;
		$page_count = 1;
		$nav_passed = false;
		while ($nav_count < $count) {
			// Are we at the current page position?
			if (($start_number <= $nav_count) && ($nav_passed != true)) {
				$navbar .= "<b><a href=\"$current_page?start=$nav_count\">[$page_count] </a></b>";
				$nav_passed = true;
				// Do we need a "prev" button?
				if ($start_number != 0) {
					$prevnumber = $nav_count - $items_per_page;
					if ($prevnumber < 1) {
						$prevnumber = 0;
					}
					$prev_navbar = "<a href=\"$current_page?start=$prevnumber\"> &lt;&lt;Prev - </a>";
				}
				$nextnumber = $items_per_page + $nav_count;
				// Do we need a "next" button?
				if ($nextnumber < $count) {
					$next_navbar = "<a href=\"
					$current_page?start=$nextnumber\"> - Next&gt;&gt; </a><br>";
				}
			} else {
				// Print normally.
				$navbar .= "<a href=\"$current_page?start=$nav_count\">[$page_count] </a>";
			}
			$nav_count += $items_per_page;
			$page_count++;
		}
		$navbar = $prev_navbar . $navbar . $next_navbar;
		return $navbar;
	}
}  


?>