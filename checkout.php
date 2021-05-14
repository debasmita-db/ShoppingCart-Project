<?php
ob_start();
session_start();
include("includes/functions.php");
if(isset($_POST['reg'])) {
if(isset($_POST['fname']) && isset($_POST['lname']) && isset($_POST['email']) && isset($_POST['password']) ) {
	$return_url 	= base64_decode($_POST["return_url"]);
	if(strlen($_POST['password']) < 6) {	
		echo '<div class="password_error">Password must be at least 6 characters</div>';
	include('register.php');
	} elseif(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)){
		include('header.inc');
		echo "Email must be valid.";
		include('footer.inc');
	} else {
		$conn = dbConnect('query');	
		$user_fname = $conn->real_escape_string($_POST['fname']);
		$user_lname = $conn->real_escape_string($_POST['lname']);
		$user_email = $conn->real_escape_string($_POST['email']);
		$user_password = $conn->real_escape_string(md5($_POST['password']));//md5 encrypt password
		
		//echo "$user_fname $user_lname $user_email $user_password"."<br/>";
		
		
		//check if user already register. if not insert info into database
		$query = $conn->query("SELECT email FROM customer WHERE email = '". $user_email ."'"); // note the way $user_email in the query exactly the same
		
		$result = $query->num_rows;
		
		if( $result > 0 ) {// if there is no duplicate			
		}
		else {	//if there is duplicate	
			//$conn = dbConnect('query');
			$values = "INSERT INTO customer (customer_fname, customer_lname, email, password) VALUES ('$user_fname', '$user_lname', '$user_email', '$user_password')";
			//echo $values;
			$insert = $conn->query($values) or die('insert failed to execute');
			$loggedin = array('loggedinemail'=>$user_email, 'loggedinfname'=>$user_fname, 'loggedinlname'=>$user_lname,'loggedinpass'=>$user_password);
			if($insert) {				
				$_SESSION['loggedin'] = $loggedin;
				//print_r($_SESSION['loggedin']);	
				//echo 'session is set';			
			}
			//echo $insert; //check if insert	
			//echo "Please <a href='login.php'>Login</a>";			
		}
	}
	}
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8" />
<title>GOT HEELS <?php  //echo $sectionName; ?></title>
<link href="heels_style.css" rel="stylesheet" type="text/css" />
<link href='http://fonts.googleapis.com/css?family=Lato:400,300' rel='stylesheet' type='text/css'>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script type="text/javascript" src="js/jquery.min.js"></script>
<script type="text/javascript" src="js/search.js"></script>

<script>
$(document).ready(function() {
	
	// Expand Panel
	$("#open").click(function(){
		$("div#panel").slideDown("slow");
	
	});	
	
	// Collapse Panel
	$("#close").click(function(){
		$("div#panel").slideUp("slow");	
	});		
	
	// Switch buttons from "Log In | Register" to "Close Panel" on click
	$("#toggle a").click(function () {
		$("#toggle a").toggle();
	});
	
	$('#cart_slide').click(function () {        
		$('#cart_slide_down').slideToggle(500);			
	});	
		
});
</script>

<!--[if IE 5]>
<style type="text/css"> 
/* IE 5 does not use the standard box model, so the column widths are overidden to render the page correctly. */
#outerWrapper #contentWrapper #navBar {
  width: 180px;
}
#outerWrapper #contentWrapper #rightColumn1 {
  width: 220px;
}
</style>
<![endif]-->
<!--[if IE]>
<style type="text/css"> 
/* The proprietary zoom property gives IE the hasLayout property which addresses several bugs. */
#outerWrapper #contentWrapper, #outerWrapper #contentWrapper #content {
  zoom: 1;
}
</style>
<![endif]-->
<!--[if lt IE 7]>
<style type="text/css"> 
img, div {
  behavior: url("images/iepngfix.htc");
}
</style>
<![endif]-->

</head>

<body>

<div id="outerWrapper">
    <div id="loginNav">
    	<?php include("includes/loginnav.php"); ?>
    </div>
    <div class="header_bg">
    <div id="header" class="clearfix">
    	<div class="pagelogo">
            <a href="index.php"><div class="logo"></div></a>
            
        </div>
        <div class="promotion">
            FREE SHIPPING AND FREE RETURN <br />
            <div id="search_form">

			<form>                
                <div class="input_container">
                    <input type="text" id="item_search_id" onkeyup="autocomplet()" placeholder="Search..">
                    <ul id="item_list_id"></ul>
                </div>
                <!--<input type="submit" value="Search" name="search" />-->
            </form>
                
            </div>
        </div>
        <div id="navBar">
            <?php include("includes/navbar.php"); ?>
        </div>
    </div>
    </div>
        
    <div id="contentWrapper">        
        <div id="navigation">
			<?php include("includes/nav.php"); ?>
        </div>
        
        <div id="content">
            <?php echo $message; ?>
            <!-- Login navifation : only display if user not logged in-->
            
            
            <!-- Shopping cart display, view cart -->			
            <div id="checkout_info"> 
                    <h2>Your Shopping Cart</h2>    
                    <form name="checkout" id="checkout" method="post" action="paypal/process.php">
                    	<div id="cart_paypal">      
                            <div class="cart">                         
                                <?php
                                if(isset($_SESSION["products"]))
                                {
                                    $total = 0;
                                    echo '<form method="post" action="paypal-checkout/process.php">';
                                    echo '<ol>';
									$cart_items = 0;
                                    foreach ($_SESSION["products"] as $cart_itm)
                                    {
                                        echo '<li class="cart-itm">';
                                       
                                        echo '<div class="cart-item-image"><img src="images/'.$cart_itm["thumb"].'" alt=""/></div>';
                                        echo '<div class="cart-item-content">';
                                            echo '<h3>'.$cart_itm["name"].'</h3>';
                                            echo '<div class="cart-item-box">';
                                                echo '<p>Size : '.$cart_itm["size"].'<br>';
                                                echo 'Qty : '.$cart_itm["qty"].'</p>';			
                                        echo '<div class="p-price">'.$currency.$cart_itm["price"].'</div>';
                                        echo '<div class="remove-itm"><a href="cart_update.php?removep='.$cart_itm["size"].'&item='.$cart_itm["name"].'&return_url='.$current_url.'">&times;<br><span>Remove</span></a></div>';
                                       
                                        echo '</div>';
                                        echo '</div>';
                                        echo '</li>';
                                        $subtotal = ($cart_itm["price"]*$cart_itm["qty"]);
                                        $total = ($total + $subtotal);
										echo '<input type="hidden" name="item_price['.$cart_items.']" value="'.$cart_itm["price"].'" />';	
										echo '<input type="hidden" name="item_name['.$cart_items.']" value="'.$cart_itm["name"].'" />';
										echo '<input type="hidden" name="item_id['.$cart_items.']" value="'.$cart_itm["product_id"].'" />';
										echo '<input type="hidden" name="item_thumb['.$cart_items.']" value="'.$cart_itm["thumb"].'" />';									
										echo '<input type="hidden" name="item_size['.$cart_items.']" value="'.$cart_itm["size"].'" />';
										echo '<input type="hidden" name="item_qty['.$cart_items.']" value="'.$cart_itm["qty"].'" />';									
										$cart_items ++;
                                    }
                                    echo '</ol>';
                                    echo '<strong>Total : '.$currency.$total.'</strong>';
                                    echo '<span class="check-out-txt"> <a href="view_cart.php">Edit</a></span>';
                                    echo '<span class="empty-cart"><a href="cart_update.php?emptycart=1&return_url='.$current_url.'">Empty Cart</a></span>';
									
									

                                }else{
                                    echo 'Your Cart is empty';
                                }
                                ?>
                            </div> <!-- end shopping-cart -->
						</div>
                                       
                        </div> 
                   </form>
			</div>
            

        
    
            <div id="footer">
                <?php include("includes/footer.php"); ?>
            </div>
		</div>
	</div>
</div>

</body>
</html>