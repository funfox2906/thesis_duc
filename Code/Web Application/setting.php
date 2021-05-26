<?php
	session_start();
	if (!isset($_SESSION['username'])){
		header("Location: index.php");
		exit();
	}
?>
<script>
function RedirectToCamera() {
  location.href = "http://192.168.1.118";
}
</script>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="A web interface for controling home">
    <title>Home</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    
    <script src="js/weather.js"></script>
    <link rel="stylesheet" href="fonts/Rimouski.css">
    <link rel="stylesheet" href="css/weather.css">
</head>
<body>
    <div class="topcorner">
	</div>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-2">
        <a class="navbar-brand col-md-3 col-lg-2 mr-0 px-3" href="controlpanel.php">Home Panel</a>
        <a class="navbar-brand col-md-3 col-lg-2 mr-0 px-1" href="#" onclick="RedirectToCamera()">Camera View</a>
        <a class="navbar-brand col-md-3 col-lg-2 mr-0 px-1" href="control.php" >Control Panel</a>
        <a class="navbar-brand col-md-3 col-lg-2 mr-0 px-1" href="setting.php">Manage Access</a>
        <a class="navbar-brand col-md-3 col-lg-2 mr-0 px-1" href="logout.php">Log Out</a>
    </nav>
    <div class="container-fluid">
        <p style="color: black; font-size: 20px ">Welcome <?= $_SESSION['username'] ?>!</p>
        <a class="navbar-brand col-md-3 col-lg-2 mr-0 px-3" href="register.php">Register new member</a>
        <div id="status" class="alert">
        <p>List of existed members</p>
        </div>
        <div class="row">           
            <div class="col-12 col-md-5">
                <table class="table table-bordered table-sm table-striped mt-2" id="existedmember">                    
                    <tbody>                           
                        <script>
                            fetch('data/admin.json')
                                .then(function (response){
                                    return response.json();
                                })
                                .then(function (data){
                                    appendData(data);    
                                })
                                .catch(function(err){
                                    console.log('error: '+err);
                                });
                            function appendData(data){
                                var mainContainer = document.getElementById("existedmember");
                                for (var i=0;i<data.length;i++){
                                    var tr = document.createElement("tr");
                                    var th = document.createElement("th");
                                    th.innerHTML= data[i].username;
                                    tr.appendChild(th);
                                    var td = document.createElement("td");
                                    td.innerHTML=data[i].password;
                                    tr.appendChild(td);
                                    var btn = document.createElement("button");
                                    btn.innerHTML='x';
                                    tr.appendChild(btn);
                                    mainContainer.appendChild(tr);                                    
                                }
                            } 
                        </script>           
                    </tbody>
                </table>
            </div>
        </div>
        <hr>

        <footer class="text-center mt-10">
            <small>
               <p>Copyright by Le Thanh Duc - YZ49Y8</p>
            </small>
        </footer>
    </div>
</body>
</html>
