<?php
    session_start();
    $errors = []; 
    if ($_POST){ 
        if (empty($_POST['username'])){
            $errors[]='The "Username" field is required';
        }
        if (empty($_POST['password'])){
            $errors[]='The "Password" field is required';
        }
        $checkIfUserExists = false;
        if (count($errors) === 0) {
            $person = [
                'username' => $_POST['username'],
                'password' => $_POST['password'],
            ];
            $raw_persons = file_get_contents('data/admin.json');
            $persons = json_decode($raw_persons, TRUE);
            foreach ($persons as $per) {
                if (strcmp($per['username'],$person['username'])==0){
                    if (strcmp($per['password'],$person['password'])==0){
                        $checkIfUserExists = true;                       
                    }
                } 
            }
            if ($checkIfUserExists == true){
                $_SESSION['username'] = $_POST['username'];
                header("Location: controlpanel.php");
                exit();
            } else {
                $errors[]='Username or password is wrong ! Please try again !';
            }
        }
    }
	function debug_to_console($data) {
		$output = $data;
		if (is_array($output))
			$output = implode(',', $output);
	
		echo "<script>console.log('Debug Objects: " . $output . "' );</script>";
	}
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<title>Login Page</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="icon" type="image/png" href="images/icons/favicon.ico"/>
	<link rel="stylesheet" type="text/css" href="vendor/bootstrap/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="fonts/font-awesome-4.7.0/css/font-awesome.min.css">
	<link rel="stylesheet" type="text/css" href="fonts/iconic/css/material-design-iconic-font.min.css">
	<link rel="stylesheet" type="text/css" href="vendor/animate/animate.css">
	<link rel="stylesheet" type="text/css" href="vendor/css-hamburgers/hamburgers.min.css">
	<link rel="stylesheet" type="text/css" href="vendor/animsition/css/animsition.min.css">
	<link rel="stylesheet" type="text/css" href="vendor/select2/select2.min.css">
	<link rel="stylesheet" type="text/css" href="vendor/daterangepicker/daterangepicker.css">
	<link rel="stylesheet" type="text/css" href="css/util.css">
	<link rel="stylesheet" type="text/css" href="css/main.css">
</head>
<body>
    <?php if (count($errors)>0): ?>
        <ul>
            <?php foreach ($errors as $error): ?>
            <li><?= $error ?></li> 
            <?php endforeach ?>
        </ul>
    <?php endif ?>
	<div class="container-login100" style="background-image: url('img/bg.jpg');">
		<div class="wrap-login100 p-l-55 p-r-55 p-t-80 p-b-30">
			<form class="login100-form validate-form" method="POST">
				<span class="login100-form-title p-b-37">
					Welcome to your SmartHouse!
				</span>

				<div class="wrap-input100 validate-input m-b-20" data-validate="Enter username or email">
					<input class="input100" type="text" name="username" id="username" placeholder="username" value="<?php if (count($errors)>0){echo $_POST['username'];} ?>">
					<span class="focus-input100"></span>
				</div>

				<div class="wrap-input100 validate-input m-b-25" data-validate = "Enter password">
					<input class="input100" type="password" name="password" id="password" placeholder="password" value="<?php if (count($errors)>0){echo $_POST['password'];} ?>">
					<span class="focus-input100"></span>
				</div>
				<div class="container-login100-form-btn">
					<button type="submit" class="login100-form-btn">
                        Login
					</button>
				</div>
			</form>
		</div>
	</div>

<!--===============================================================================================-->
	<script src="vendor/jquery/jquery-3.2.1.min.js"></script>
	<script src="vendor/animsition/js/animsition.min.js"></script>
	<script src="vendor/bootstrap/js/popper.js"></script>
	<script src="vendor/bootstrap/js/bootstrap.min.js"></script>
	<script src="vendor/select2/select2.min.js"></script>
	<script src="vendor/daterangepicker/moment.min.js"></script>
	<script src="vendor/daterangepicker/daterangepicker.js"></script>
	<script src="vendor/countdowntime/countdowntime.js"></script>
	<script src="js/main.js"></script>
</body>
</html>