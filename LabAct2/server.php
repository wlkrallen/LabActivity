<?php
session_start();

$username = "";
$email    = "";
$errors = array(); 

//db connect
$db = mysqli_connect('localhost', 'root', '', 'registration');


if (isset($_POST['reg_user'])) {
  $username = mysqli_real_escape_string($db, $_POST['username']);
  $email = mysqli_real_escape_string($db, $_POST['email']);
  $password_1 = mysqli_real_escape_string($db, $_POST['password_1']);
  $password_2 = mysqli_real_escape_string($db, $_POST['password_2']);
  $uppercase = preg_match('@[A-Z]@', $password_1);
  $uppercase = preg_match('@[A-Z]@', $password_2);
  $lowercase = preg_match('@[a-z]@', $password_1 );
  $lowercase = preg_match('@[a-z]@', $password_2 );  
  $number    = preg_match('@[0-9]@', $password_1);  
  $number    = preg_match('@[0-9]@', $password_2);  
  $specialChars = preg_match('@[^\w]@', $password_1);
  $specialChars = preg_match('@[^\w]@', $password_2); 
    
//form conditions
  if (empty($username)) { array_push($errors, "Username is required"); }
  if (empty($email)) { array_push($errors, "Email is required"); }
  if (empty($password_1)) { array_push($errors, "Password is required"); }
  if ($password_1 != $password_2) {
	array_push($errors, "The two passwords do not match"); }
  if(strlen($password_1) & ($password_2) > 8) { array_push($errors, "Password must be minimum of 8 characters"); }
  if (!$uppercase) { array_push ($errors, "Password should have at least One(1) Upper case letter"); }
  if (!$lowercase) { array_push ($errors, "Password must have atleast One(1) Lowercase"); }
  if (!$number) { array_push ($errors, "Password must have atleast One(1) Number"); } 
  if (!$specialChars)   { array_push ($errors, "Password must have atleast One(1) Special Characters"); }
    
// checking DB for existing Username or email
  $user_check_query = "SELECT * FROM users WHERE username='$username' OR email='$email' LIMIT 1";
  $result = mysqli_query($db, $user_check_query);
  $user = mysqli_fetch_assoc($result);
  
  if ($user) { 
    if ($user['username'] === $username) {
      array_push($errors, "Username already exists");
    }

    if ($user['email'] === $email) {
      array_push($errors, "email already exists");
    }
  }

//register user if no errors in the form
  if (count($errors) == 0) {
  	$password = md5($password_1); //encrypt
   
  	$query = "INSERT INTO users (username, email, password) 
  			  VALUES('$username', '$email', '$password')";
  	mysqli_query($db, $query);
  	$_SESSION['username'] = $username;
  	$_SESSION['success'] = "You are now logged in";
  	header('location: index.php');
  }
}

//log in
if (isset($_POST['login_user'])) {
  $username = mysqli_real_escape_string($db, $_POST['username']);
  $password = mysqli_real_escape_string($db, $_POST['password']);

  if (empty($username)) {
  	array_push($errors, "Username is required");
  }
  if (empty($password)) {
  	array_push($errors, "Password is required");
  }

  if (count($errors) == 0) {
  	$password = md5($password);
  	$query = "SELECT * FROM users WHERE username='$username' AND password='$password'";
  	$results = mysqli_query($db, $query);
  	if (mysqli_num_rows($results) == 1) {
  	  $_SESSION['username'] = $username;
  	  $_SESSION['success'] = "You are now logged in";
  	  header('location: index.php');
  	}else {
  		array_push($errors, "Wrong username/password combination");
  	}
  }
}




?>