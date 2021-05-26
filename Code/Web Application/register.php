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
        if (empty($_POST['re-password'])){
            $errors[]='The "Re-enter Password" field is required';
        } 
        if (strcmp($_POST['password'],$_POST['re-password']) !== 0){
            $errors[]='Password should match';
        }
        if (count($errors) === 0) {
            $person = [
                'username' => $_POST['username'],
                'password' => $_POST['password'],
            ];
            $raw_persons = file_get_contents('data/admin.json');
            $persons = json_decode($raw_persons, TRUE);            
            $persons[] = $person;
            file_put_contents('data/admin.json', json_encode($persons));
            header("Location: index.php");
            exit();
            
        } 
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Register Page</title>
    <link rel="stylesheet" href="fonts/material-icon/css/material-design-iconic-font.min.css">
    <link rel="stylesheet" href="css/style2.css">
</head>
<body>
    <?php if (count($errors)>0): ?>
        <ul>
            <?php foreach ($errors as $error): ?>
            <li><?= $error ?></li> 
            <?php endforeach ?>
        </ul>
    <?php endif ?>
    <section class="signup">
        <div class="container">
            <div class="signup-content">

            
                <div class="signup-form">
                    <h2 class="form-title">Sign up</h2>
                    <form method="POST" class="register-form" id="register-form">
                        <div class="form-group">
                            <label for="name"><i class="zmdi zmdi-account material-icons-name"></i></label>
                            <input type="text" name="username" id="username" placeholder="Username" value="<?php if (count($errors)>0){echo $_POST['username'];} ?>">
                        </div>
                        <div class="form-group">
                            <label for="pass"><i class="zmdi zmdi-lock"></i></label>
                            <input type="password" name="password" id="password" placeholder="Password" value="<?php if (count($errors)>0){echo $_POST['password'];} ?>">
                        </div>
                        <div class="form-group">
                            <label for="re-pass"><i class="zmdi zmdi-lock-outline"></i></label>
                            <input type="password" name="re-password" id="re_pass" placeholder="Repeat your password" value="<?php if (count($errors)>0){echo $_POST['re-password'];} ?>">
                        </div>                        
                        <div class="form-group form-button">
                            <input type="submit" name="signup" id="signup" class="form-submit" value="Register"/>
                        </div>
                    </form>
                </div>
                <div class="signup-image">
                    <figure><img src="img/signup-image.jpg" alt="sing up image"></figure>
                    <a href="index.php" class="signup-image-link">I am already member</a>
                </div>
            </div>
        </div>
    </section>

</div>
<script src="vendor/jquery/jquery.min.js"></script>
<script src="js/main.js"></script>
</html>