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
	$SESSION['ESP_Light'] = getStatus("ESP_Light");
	
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

    
	// if ($_POST && !empty($_SESSION['user'])){ 
	// 	$_SESSION['date'] = $_POST['date'];
	// 	$_SESSION['time_slot'] = $_POST['time_slot'];
	// 	$_SESSION['free_slot'] = $_POST['free_slot'];
	// 	$_SESSION['total_slot'] = $_POST['total_slot'];
	// 	$data = file_get_contents('data/users.json');
	// 	$json_arr = json_decode($data, true);
	// 	$arr_index = array();
	// 	foreach ($json_arr as $key => $value)
	// 	{
	// 		if ($value['email'] == $_SESSION['user'])
	// 		{
	// 			$arr_index[] = $key;
	// 		}
	// 	}

    
?>
<script>

function RedirectToCamera() {
  location.href = "http://192.168.1.118";
}
function Switch(items, command){
    var url = 'http://192.168.1.110:8080/rest/items/'.concat(items);
    try{
    fetch(url , { method: 'POST', mode:'no-cors', body: command });
    
    location.href = "http://192.168.1.110/control.php";
    
    } catch(err){
        console.log("Error");
    }
}
function showHEX(){
    var colorwheel = document.getElementById("colorwheel");
    var HEX = colorwheel.value;
    Switch("RGBColor",HEX);
    
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

</head>
<body>
    <div class="topcorner">
	</div>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-2">
        <a class="navbar-brand col-md-3 col-lg-2 mr-0 px-3" href="controlpanel.php">Home Panel</a>
        <a class="navbar-brand col-md-3 col-lg-2 mr-0 px-1" href="#" onclick="RedirectToCamera()">Camera View</a>
        <a class="navbar-brand col-md-3 col-lg-2 mr-0 px-1" href="#" onclick="control.php">Control Panel</a>
        <a class="navbar-brand col-md-3 col-lg-2 mr-0 px-1" href="setting.php">Manage Access</a>
        <a class="navbar-brand col-md-3 col-lg-2 mr-0 px-1" href="logout.php">Log Out</a>
    </nav>
    <div class="container-fluid">
        <p style="color: black; font-size: 20px ">Welcome <?= $_SESSION['username'] ?>!</p>
        <div id="status" class="alert">
        </div>
        <div class="card mb-2">
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
                    <div class="card-body">
                        <div class="card-title">
                            Light RGB
                        </div>
                        <span id="label2" class="badge w-100">
                        <form method="post">
                            <input id="colorwheel" type="color" value="<?php echo $SESSION['RGBColor']?>" onchange="showHEX()">                            
                            <input type="submit" onclick="Switch('RGBColor','#000000')" value ="OFF">
                        </form>
                        </span>
                    </div>
					<div class="card-body">
                        <div class="card-title">
							Untested part
                        </div>                        
                    </div>
					<div class="card-body">
                        <div class="card-title">
							TV - Volumn Up
                        </div>
                        <span id="label2" class="badge w-100">
                        <form method="post">
                        <input type="submit" onclick="Switch('Button_TV','ON')" value ="ON">
                        <input type="submit" onclick="Switch('Button_TV','OFF')" value ="OFF">
                        </form>
                    </div>
                    <div class="card-body">
                        <div class="card-title">
                            TV - Volumn Down
                        </div>
                        <span id="label2" class="badge w-100">
                        <form method="post">
                        <input type="submit" onclick="Switch('Button_TV','ON')" value ="ON">
                        <input type="submit" onclick="Switch('Button_TV','OFF')" value ="OFF">
                        </form>
                    </div>
                    <div class="card-body">
                        <div class="card-title">
                            Water Pump
                        </div>
                        <span id="label2" class="badge w-100">
                        <form method="post">
                        <input type="submit" onclick="Switch('Button_TV','ON')" value ="RUNNING">
                        <input type="submit" onclick="Switch('Button_TV','OFF')" value ="STOP">
                        </form>
                    </div>
                </div>
                <div class="col-6 col-md-4">
                    
                </div>
                <div class="col-12 col-md-4">
                    <div class="card-body">
                        <div class="card-title">
                            Room Humidity
                        </div>
                        <span id="basementTempLabel" class="badge w-100" style="font-size: 20px"><?php echo $SESSION['Humidity'] ?>%</span>
                    </div>
                    <div class="card-body">
                        <div class="card-title">
                            Room Temperature
                        </div>
                        <span id="basementTempLabel" class="badge w-100" style="font-size: 20px">27 °C</span>
                    </div>
					<div class="card-body">
                        <div class="card-title">
                            Motion Detector
                        </div>
                        <span id="basementTempLabel" class="badge w-100" style="font-size: 20px"><?php echo ($SESSION['MotionDetector'] != 0) ? 'ON' : 'OFF' ?></span>
                    </div>
                </div>
            </div>
        </div>
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
