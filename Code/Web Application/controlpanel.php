<?php
	session_start();
    if (!isset($_SESSION['username'])){
		header("Location: index.php");
		exit();
	}
    function getStatus($item) 
    {
      $url = "http://192.168.1.110:8080/rest/items/" . $item;
      $options = array(
        'http' => array(
            'header'  => "Content-type: text/plain\r\n",
            'method'  => 'GET',
        ),
      );
      $context  = stream_context_create($options);
      $json = file_get_contents($url, false, $context);
      $state = json_decode($json);
      $result = $state->{'state'};
      return  $result;
    }
    $SESSION['Button_Light'] = getStatus("Button_Light");
    $SESSION['Button_TV'] = getStatus("Button_TV");
    $SESSION['Humidity'] = getStatus("Humidity");
    $SESSION['RGBColor'] = getStatus("RGBColor");
    $SESSION['MotionDetector'] = getStatus("MotionDetector");
    
    if ($SESSION['MotionDetector'] == '1'){
        $motionStatus = "YES";       
    }else {
        $motionStatus= "NO";
    }
    if ($SESSION['RGBColor'] == '#000000'){
        $RGBStatus = "OFF";
    } else {
        $RGBStatus = "ON - ".$SESSION['RGBColor'];
    }
    // function sendCommand($item, $data) {
    //     $url = "http://192.168.1.110:8080/rest/items/" . $item;
      
    //     $options = array(
    //       'http' => array(
    //           'header'  => "Content-type: text/plain\r\n",
    //           'method'  => 'POST',
    //           'content' => $data  
    //       ),
    //     );
      
    //     $context  = stream_context_create($options);
    //     $result = file_get_contents($url, true, $context);
    //     console_log($result);
    //     return $result;
    // }
    
?>
<script>
function RedirectToCamera() {
  location.href = "http://192.168.1.118";
}
function Switch(items, command){
    var url = 'http://192.168.1.110:8080/rest/items/'.concat(items);
    try{
    fetch(url , { method: 'POST', mode:'no-cors', body: command });
    
    location.href = "http://192.168.1.110/controlpanel.php";
    
    } catch(err){
        console.log("Error");
    }
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
        <?php if ($_SESSION['username'] = 'admin') : ?>
            <a class="navbar-brand col-md-3 col-lg-2 mr-0 px-1" href="setting.php">Manage Access</a>
        <?php endif ?>
        <a class="navbar-brand col-md-3 col-lg-2 mr-0 px-1" href="logout.php">Log Out</a>
    </nav>
    <div class="container-fluid">
        <p style="color: black; font-size: 20px ">Welcome <?= $_SESSION['username'] ?>!</p>
        <div id="status" class="alert">
            Is somebody at home right now?  <?php echo $motionStatus; ?>          
        </div>
        <!-- <div class="card mb-2">
            <div class="row">
                <div class="col-6 col-md-4">
                    <div class="card-body">
                        <div class="card-title">
                            Light:                            
                        </div>
                        <span id="label1" class="badge w-100">
                        <form method="post">
                        <input type="submit" onclick="Switch('Button_Light','ON')" value ="ON">
                        <input type="submit" onclick="Switch('Button_Light','OFF')" value ="OFF">
                        </form>
                    </div>
                </div>
                <div class="col-6 col-md-4">
                    <div class="card-body">
                        <div class="card-title">
                            TV
                        </div>
                        <span id="label2" class="badge w-100">
                        <form method="post">
                        <input type="submit" onclick="Switch('Button_TV','ON')" value ="ON">
                        <input type="submit" onclick="Switch('Button_TV','OFF')" value ="OFF">
                        </form>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="card-body">
                        <div class="card-title">
                            Room temperature
                        </div>
                        <span id="basementTempLabel" class="badge w-100" style="font-size: 20px"><?php echo $SESSION['Humidity'] ?>%</span>
                    </div>
                </div>
            </div>
        </div> -->
        <div class="row">
            <!-- <div class="col-12 col-md-7 mb-2">
                <div class="card">
                    <div class="card-body">
                        <div class="card-title">
                            Living room temperature
                            <span id="livingTempLabel" class="text-muted float-right badge">Unknown</span>
                        </div>
                    </div>
                    <canvas id="tempChart" width="300" height="120"></canvas>
                </div>
            </div> -->
            <div class="col-12 col-md-5">
                <table class="table table-bordered table-sm table-striped mt-2">                    
                    <tbody>
                        <tr>
                            <th>Room Humidity</th>
                            <td><small id="RoomTempLabel" style="font-size: 20px "><?php echo $SESSION['Humidity'] ?>%</small></td>
                        </tr>
                        <tr>
                            <th>Room Temperature</th>
                            <td><small id="livingTempSensor"style="font-size: 20px ">27 °C</small></td>
                        </tr>
                        <tr>
                            <th>Light RGB</th>
                            <td><small id="value1"style="font-size: 20px "><?php echo $RGBStatus; ?></small></td>
                        </tr>
						<tr>
                            <th>Light</th>
                            <td><small id="value2"style="font-size: 20px "><?php echo $SESSION['Button_Light'] ?></small></td>
                        </tr>
                        <tr>
                            <th>TV</th>
                            <td><small id="value2"style="font-size: 20px "><?php echo $SESSION['Button_TV'] ?></small></td>
                        </tr>
						<tr>
							<th>Some more demo devices</th>
						</tr>
                        <tr>
                            <th>Garage Door</th>
                            <td><small id="value2"style="font-size: 20px ">CLOSED</small></td>
                        </tr>
                        <tr>
                            <th>Front Door</th>
                            <td><small id="value2"style="font-size: 20px ">OPEN</small></td>
                        </tr>
                        <tr>
                            <th>Waterpump</th>
                            <td><small id="value2"style="font-size: 20px ">RUNNING</small></td>
                        </tr>
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
