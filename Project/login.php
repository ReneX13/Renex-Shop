
<?php 
// invoke autoload to get access to the propel generated models
require_once '../vendor/autoload.php';

// require the config file that propel init created with your db connection information
require_once '../generated-conf/config.php';



$flag1 = false;
$flag2 = false;
$flag3 = false;

session_start();
if(!empty($_SESSION)){
	header("Location: index.php");
}

if(!empty($_POST)){
	
	$u = UsersQuery::create()->findOneByEmail($_POST["email"]);
	if(empty($u)){
		$flag1 = true;	
	}else{
		if($u->login($_POST['password'])){
			$flag3 = true;
			//echo "Successfull!";
			session_start();
			$_SESSION['id'] = $u->getId();
			$_SESSION['cart'] = array();
			$cart = CartQuery::create()->filterByUserId($u->getId());
			foreach($cart as $c){
				array_push($_SESSION['cart'], array('id'=>$c->getProductId(), 'amount'=>$c->getAmount())); 
			}
			header("Location: index.php");
		}else{
			$flag2 = true;
		}
	}
}
else{

}


?>

<html>
<head>
<title>Renex</title>
	<meta charset="utf-8">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
	
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">

	<meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<style>
.custom_inputs{
        margin-bottom: 20px;
}

</style>
<body>
	<div class="container text-center" >
		<div class="jumbotron">
		  <h3>Renex</h3>    
		</div>
	

	<form class="form-signin" method = "POST">
        	<h2 class="form-signin-heading">Please sign in</h2>
        	
		<?php
                        if($flag1){
                        echo "<div class='alert alert-danger'>
                                <strong>Info!</strong> Username Incorrect!
                              </div>";
			}
                ?>
		<label for="inputEmail" class="sr-only">Email address</label>
       		<input type="email" name ="email" id="inputEmail" class="form-control custom_inputs" placeholder="Email address" required="" autofocus="">
        	

 		<?php
                       if($flag2){
                        echo "<div class='alert alert-danger'>
                                <strong>Info!</strong> Password Incorrect!
                              </div>";
			}
                ?>
		<label for="inputPassword" class="sr-only">Password</label>
        	<input type="password" name = "password" id="inputPassword" class="form-control custom_inputs" placeholder="Password" required="">
       
        	<button class="btn btn-lg btn-primary btn-block" type="submit">Sign in</button>
	 </form>
	 <form class="form-signin" data-toggle="validator" method = "POST" action = "index.php" >

        	<button class="btn btn-lg btn-primary btn-block" type="submit">Cancel</button>
         </form>
	 <small>Don't have an account,  <br> <a href="register.php">Sign Up</a>!</small>
		<?php
                        if($flag3){
                        echo "<div class='alert alert-success'>
                                <strong>Info!</strong> Successful Login!
                              </div>";
			}
                ?>

	</div>
<?php
//$password = "horriblepassword";

// Calculate a bcrypt hash from a password
//$hash = PHPassLib\Hash\BCrypt::hash($password);

///echo "First hash is: ".$hash."<br/>";
?>
</body>
</html>
