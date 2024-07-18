<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
if(isset($_SESSION['id']) && isset($_SESSION['username'])) {
?>

<!DOCTYPE html>
<html lang="en">
  <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <link rel="icon" href="./home/favicon.ico" type="image/x-icon">
      <title> Device Control | Farm Cup Control System </title>

      <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
      <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Google+Sans&display=swap">

      <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Product+Sans:wght@300;400;500;600;700&display=swap">
      <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;700&display=swap">
      <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap">
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/material-components-web/10.0.0/material-components-web.min.css">
      <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" >
      <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

      <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
      <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
      
      <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
      <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
      <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
      <script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>

      <link rel="stylesheet" href="./style.css">
      
      <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

      <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  </head>

  <body>
      <?php
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        require_once('../../dbconnection/config.php');
        $conn = new mysqli($db_host, $db_user, $db_password, $db_name);

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $sql = "SELECT * FROM sensordata ORDER BY time_stamp DESC LIMIT 1";
        $result = $conn->query($sql);

        $data = array();
        while($row = $result->fetch_assoc()) {
            $data[] = $row;
        }

        if (count($data) === 1) {
            // Extract data from the last two rows
            $lastRow = $data[0];
        
            // Extract values from the last row
            $moisture_last = $lastRow["moisture"];
            $tds_last = $lastRow["tds"];
            $pH_last = $lastRow["pH"];
            $EC_last = $lastRow["EC"];
            $ambient_light_last = $lastRow["ambient_light"];
            $temperature_last = $lastRow["temperature"];
            $humidity_last = $lastRow["humidity"];
            $waterlevel_last = $lastRow["waterlevel"];
        
        } else {
            // Handle the case when there are less than two rows in the table
            // You can set default values or display an error message
        }
        
        // -------------------- SQL Request for TOGGLE STATUS -----------------------------
        $sqlToggleStatus = "SELECT toggle FROM toggle_switch";
        $resultToggleStatus = $conn->query($sqlToggleStatus);
        if ($resultToggleStatus->num_rows > 0) {
            while ($row = $resultToggleStatus->fetch_assoc()) {
                $toggleStatus = $row["toggle"];
            }
        } else {
            $toggleStatus = 0;
        }
        
        $conn->close();
        ?>
     
  <div class="container-fluid full-height">
      <div class="row full-height">
          <!-- Navbar -->
          <div class="col-md-2 d-md-block collapse" id='menuCollapse'>
              <aside class="layout-menu menu-vertical menu bg-menu-theme" data-bg-class="bg bg-menu-theme" style='background: linear-gradient(to bottom, #DDEEDF 5%, #f9faf6 95%);'>
                <div class = 'app-brand-demo'>
                  <a href='index.php'>
                    <img src="././assets/farm-cup.svg" alt="Farm Cup Logo">
                  </a>
                </div>

                <hr class="rounded">

                <ul  class='menu-inner py-1 ps'>
                  <li class='menu-item '>
                      <a href= '../home/index.php'>
                        <div class='menu-text'><i class='bx bx-user'></i> Home</div>
                      </a>
                  </li>
              
                  <li class='menu-item hidden active' id='hiddenMenuItem'>
                    <a href= '#' id=editParameters disabled>
                      <div class='menu-text'>Edit Parameters</div>
                    </a>
                  </li>

                  <hr class='rounded'>

                  <li class='menu-item '>
                      <a href= '../parameters/cocopeatMoisture.php'>
                        <div class='menu-text'>Cocopeat Moisture</div>
                      </a>
                  </li>

                  <li class='menu-item '>
                      <a href= '../parameters/tds.php'>
                        <div class='menu-text'>Total Dissolved Solids</div>
                      </a>
                  </li>

                  <li class='menu-item '>
                      <a href= '../parameters/pHLevel.php'>
                        <div class='menu-text'>pH Level</div>
                      </a>
                  </li>

                  <li class='menu-item '>
                      <a href= '../parameters/ecLevel.php'>
                        <div class='menu-text'>Electrical Conductivity</div>
                      </a>
                  </li>

                  <li class='menu-item '>
                      <a href= '../parameters/ambientLight.php'>
                        <div class='menu-text'>Ambient Light</div>
                      </a>
                  </li>

                  <li class='menu-item '>
                      <a href= '../parameters/ambientHumidity.php'>
                        <div class='menu-text'>Ambient Humidity</div>
                      </a>
                  </li>

                  <li class='menu-item '>
                      <a href= '../parameters/ambientTemperature.php'>
                        <div class='menu-text'>Ambient Temperature</div>
                      </a>
                  </li>

                  <li class='menu-item'>
                      <a href= '../parameters/waterLevel.php'>
                        <div class='menu-text'>Water Level</div>
                      </a>
                  </li>
                </ul>

                <hr class='rounded'>

                <div class="custom-control custom-switch">
                  <input type="checkbox" class="custom-control-input" id="manualControlSwitch" checked>
                  <label class="custom-control-label" for="manualControlSwitch">Manual Control Mode</label>
                </div>
              </aside>
          </div>

        <!-- Content Section -->
        <div class="col-md-10">
          <div class="layout-page">
            <div class='md-block d-md-none top-nav'>
                <div class='col-md-12 d-flex' id='button-toggle'>
                <button class="toggle-button btn btn-primary d-md-none" id="menuToggleBtn" data-toggle="collapse" href="#menuCollapse" >
                  <i class="fas fa-bars"></i> 
                </button>
                <div class='col-md-12 d-flex'>
                  <h1 class='parameter-name-title-sm'>Device Control</h1>
                </div>
              </div>
            </div>

            <!-------------------------------- CONTENT NG WEBSITE ---------------------------------------->
                              
            <div class='page-header d-md-block collapse'>
              <div class='nav-link'>
                <p class='link-top'>
                    <a href="../home/index.php">Home</a> &gt; 
                    <a href="#">Device Control</a>
                </p>
              </div>
                <h1 class='parameter-name-title'>Device Control</h1>
            </div>


            <div class='subhead container-fluid'>
              <div class = 'row'>
                <div class = 'live-date col-6'>
                  <text class='live-date-text'>Date, Month DD, YYYY</text>
                  
                </div>
                <div class = 'live-clock col-6 text-right'>
                  <text class='live-clock-text'>
                    00:00:00
                  </text>
                </div>
              </div>
            </div>

            <div class="col-12 top-grid">
              <div class='row'>
                <!---------------------------------------------- MOTHER CONTAINER ----------------------------------------------------->
                
                <div class="container">
                  <h3>Control Water Parameters </h3>
                  <hr class= 'rounded'>
                  <div class="row">

                    <div class="col-lg-6 col-md-4 mb-4">
                      <div class="card h-100">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            
                            <div class='col-7'>
                              <h5 class="card-title mb-0" style='font-weight: 700; color:green'>pH Up Pump (1)</h5> <!--- RELAY 1 --->
                            </div>
                            <div class='col-5' style='background-color: #BCEFBC; border-radius: 1rem;'>
                              <h5 class="card-title mb-0" style='text-align: center; font-weight: 500; font-size: 0.9rem; padding:0.2rem'>Current pH: <?php echo $pH_last; ?></h5>
                            </div>
                        </div>
                        
                        
                        <div class="card-body">
                            <div class='col-12'>
                                <h6 id="relay1_duration">Duration: <b>0 seconds</b></h6>
                                <input type="range" class="form-range relay-slider" id="relay1_slider" min="0" max="60" value="0">
                            </div>
                        </div>
                      </div>
                    </div>

                    <div class="col-lg-6 col-md-6 mb-4">
                      <div class="card h-100">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            
                            <div class='col-7'>
                                <h5 class="card-title mb-0" style='font-weight: 700; color:green'>pH Down Pump (2)</h5> <!--- RELAY 2 --->
                            </div>
                            <div class='col-5' style='background-color: #BCEFBC; border-radius: 1rem;'>
                              <h5 class="card-title mb-0" style='text-align: center; font-weight: 500; font-size: 0.9rem; padding:0.2rem'>Current pH: <?php echo $pH_last; ?></h5>
                            </div>
                        </div>
                        
                        
                        <div class="card-body">
                            <div class='col-12'>
                                <h6 id="relay2_duration">Duration: <b>0 seconds</b></h6>
                                <input type="range" class="form-range relay-slider" id="relay2_slider" min="0" max="60" value="0">
                            </div>
                        </div>
                      </div>
                    </div>

                    <div class="col-lg-12 col-md-6 mb-4">
                      <div class="card h-100">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            
                            <div class='col-8'>
                                <h5 class="card-title mb-0" style='font-weight: 700; color:green'>Snap Solution (3)</h5>
                            </div>
                            <div class='col-4'>
                                <div class='row'>
                                    <div class='col-lg-6' style='background-color: #BCEFBC; border-radius: 1rem;'>
                                        <h5 class="card-title mb-0" style='text-align: center; font-weight: 500; font-size: 0.9rem; padding:0.2rem'>Current EC: <?php echo $EC_last; ?> ms/cm</h5>
                                    </div>
                                    
                                    <div class='col-lg-6' style='background-color: #BCEFBC; border-radius: 1rem;'>
                                        <h5 class="card-title mb-0" style='text-align: center; font-weight: 500; font-size: 0.9rem; padding:0.2rem'>Current TDS: <?php echo $tds_last; ?> ppm</h5>
                                    </div>
                                </div>
                            </div>
                            
                        </div>
                        
                        <div class="card-body">
                            <div class='col-12'>
                                <h6 id="relay3_duration">Duration: <b>0 seconds</b></h6>
                                <input type="range" class="form-range relay-slider" id="relay3_slider" min="0" max="60" value="0">
                            </div>
                        </div>
                      </div>
                    </div>

                    <h3>Control Water Pumps</h3>
                    <hr class='rounded'>

                    <div class="col-lg-6 col-md-6 mb-4">
                        <div class="card h-100">
                            <div class="card-body d-flex justify-content-between align-items-center">
                                <div class='col-7'>
                                    <h5 class="card-title mb-0 mt-0" style='font-weight: 700; color:green'>Water to System (7)</h5>
                                </div>
    
                                <div class='col-5' style='background-color: #BCEFBC; border-radius: 1rem;'>
                                    <h5 class="card-title mb-0 mt-0" style='text-align: center; font-weight: 500; font-size: 0.9rem; padding:0.2rem'>Current Moisture: <?php echo $moisture_last; ?></h5>
                                </div>
                            </div>
                        
                        <div class="card-body">
                            <div class='col-12'>
                                <h6 id="relay7_duration">Duration: <b>0 seconds</b></h6>
                                <input type="range" class="form-range relay-slider" id="relay7_slider" min="0" max="60" value="0">
                                
                            </div>
                        </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-6 col-md-6 mb-4">
                          <div class="card h-100">
                            <div class="card-body d-flex justify-content-between align-items-center">
                                <div class='col-7'>
                                    <h5 class="card-title mb-0" style='font-weight: 700; color:green'>Refill Water Reservior (6)</h5>
                                </div>
                                
                                <div class='col-5' style='background-color: #BCEFBC; border-radius: 1rem;'>
                                    <h5 class="card-title mb-0" style='text-align: center; font-weight: 500; font-size: 0.9rem; padding:0.2rem'>Current Water Level: <?php echo $waterlevel_last; ?> cm</h5>
                                </div>
                            </div>

                        <div class="card-body">
                            <div class='col-12'>
                                <h6 id="relay6_duration">Duration: <b>0 seconds</b></h6>
                                <input type="range" class="form-range relay-slider" id="relay6_slider" min="0" max="60" value="0">
                            </div>
                        </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-12 col-md-6 mb-4">
                          <div class="card h-100">
                            <div class="card-body d-flex justify-content-between align-items-center">
                                <div class='col-8'>
                                    <h5 class="card-title mb-0" style='font-weight: 700; color:green'>Mixing Water Pump (5)</h5>
                                </div>
                                
                                <div class='col-4' style='background-color: #BCEFBC; border-radius: 1rem;'>
                                    <!--<h5 class="card-title mb-0" style='text-align: center; font-weight: 500; font-size: 0.9rem; padding:0.2rem'>Current Water Level: 5.5</h5>-->
                                </div>
                            
                            </div>
                        
                        <div class="card-body">
                            <div class='col-12'>
                                <h6 id="relay5_duration">Duration: <b>0 seconds</b></h6>
                                <input type="range" class="form-range relay-slider" id="relay5_slider" min="0" max="60" value="0">
                            </div>
                        </div>
                        </div>
                    </div>

                    
                    <h3>Control Peripherals</h3>
                    <hr class='rounded'>

                    <div class="col-lg-6 col-md-6 mb-4">
                          <div class="card h-100">
                            <div class="card-body d-flex justify-content-between align-items-center">
                                <div class='col-3'>
                                    <h5 class="card-title mb-0" style='font-weight: 700; color:green'>Fans (4)</h5>
                                </div>
                                
                                <div class='col-9'>
                                    <div class='row'>
                                    <div class='col-lg-6' style='background-color: #BCEFBC; border-radius: 1rem;'>
                                        <h5 class="card-title mb-0" style='text-align: center; font-weight: 500; font-size: 0.9rem; padding:0.2rem'>Current Temperature: <?php echo $temperature_last; ?>&deg;C</h5>
                                    </div>
                                    
                                    <div class='col-lg-6' style='background-color: #BCEFBC; border-radius: 1rem;'>
                                        <h5 class="card-title mb-0" style='text-align: center; font-weight: 500; font-size: 0.9rem; padding:0.2rem'>Current Humidity: <?php echo $humidity_last; ?>%</h5>
                                    </div>
                                    </div>
                            </div>
                             
                            </div>
                        <div class="card-body">
                            <div class='col-12'>
                                <h6 id="relay4_duration">Duration: <b>0 seconds</b></h6>
                                <input type="range" class="form-range relay-slider" id="relay4_slider" min="0" max="60" value="0">
                            </div>
                        </div>
                          </div>
                    </div>

                    <div class="col-lg-6 col-md-6 mb-4">
                          <div class="card h-100">
                            <div class="card-body d-flex justify-content-between align-items-center">
                                <div class='col-6'>
                                    <h5 class="card-title mb-0" style='font-weight: 700; color:green'>Growlights (8)</h5>
                                </div>
                                
                                <div class='col-6' style='background-color: #BCEFBC; border-radius: 1rem;'>
                                    <h5 class="card-title mb-0" style='text-align: center; font-weight: 500; font-size: 0.9rem; padding:0.2rem'>Current Light Level: <?php echo $ambient_light_last; ?> lux</h5>
                                </div>
                                
                            
                            </div>
                        <div class="card-body">
                            <div class='col-12'>
                                <h6 id="relay8_duration">Duration: <b>0 seconds</b></h6>
                                <input type="range" class="form-range relay-slider" id="relay8_slider" min="0" max="60" value="0">
                            </div>
                        </div>
                          </div>
                    </div>

                    <div class="col-lg-12 col-md-6 col-sm-6 col-12 mb-4">
                      <button id="submit-btn" type="submit" class="btn btn-success btn-block">Save Changes</button>
                    </div>

                </div>
                </div>
            </div>
            </div>
          </div>
        </div>
      </div>
  </div>
  
  
    <script>
      const numRelays = 8;
      for (let i = 1; i <= numRelays; i++) {
        const slider = document.getElementById(`relay${i}_slider`);
        const duration = document.getElementById(`relay${i}_duration`);
        // const valueDisplay = document.getElementById(`relay${i}_value`);
        
        slider.addEventListener('input', function() {
          const value = this.value;
        //   valueDisplay.textContent = this.value;
          duration.textContent = `Duration: ${this.value} seconds`;
        });
      }
      
      document.getElementById('submit-btn').addEventListener('click', function(event) {
        event.preventDefault(); // Prevent form submission (if used within a form)
        const data = {};
        for (let i = 1; i <= numRelays; i++) {
          const sliderValue = document.getElementById(`relay${i}_slider`).value*1000;
        //   const durationValue = document.getElementById(`relay${i}_duration`).value;
          data[`relay${i}_duration`] = sliderValue;
        }
        console.log('DATA: ',data); // You can replace console.log with your save function here
        
        
        const xhr = new XMLHttpRequest();
        xhr.open("POST", "../../dbconnection/post_manual_control.php", true);
        xhr.setRequestHeader("Content-Type", "application/json");
        xhr.onreadystatechange = function() {
            if (xhr.readyState === XMLHttpRequest.DONE) {
                if (xhr.status === 200) {
                    console.log("Data saved successfully");
                    swal("Success!", "Data saved successfully!", "success");
                    // Clear text fields
                    for (let i = 1; i <= numRelays; i++) {
                        document.getElementById(`relay${i}_duration`).value = "";
                    }
                } else {
                    console.error("Error saving data: " + xhr.responseText);
                    swal("Error!", "Error saving data: " + xhr.responseText, "error");
                }
            }
        };
        xhr.send(JSON.stringify(data));
      });
      
      
      
      
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap5-toggle@5.0.4/js/bootstrap5-toggle.ecmas.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <script src='scripts/dateAndTime.js'></script>
  
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

    
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
        const manualControlSwitch = document.getElementById('manualControlSwitch');
        const editParametersLink = document.getElementById('editParameters');
        
        function toggleLinkState() {
            const toggleStatus = manualControlSwitch.checked ? 1 : 0;
            updateToggleSwitchStatus(toggleStatus);
            
            console.log('toggle status: ', toggleStatus);
            
            if (manualControlSwitch.checked) {
                
                editParametersLink.classList.remove('disabled');
                editParametersLink.removeAttribute('disabled');
                editParametersLink.removeEventListener('click', preventDefaultClick);
                
            } else {
                
                
                editParametersLink.classList.add('disabled');
                editParametersLink.setAttribute('disabled', 'disabled');
                editParametersLink.addEventListener('click', preventDefaultClick);
                editParametersLink.removeEventListener('contextmenu', preventContextMenu);
            }
            }
            
            function preventDefaultClick(event) {
                event.preventDefault();
            }
            
            function preventContextMenu(event) {
                event.preventDefault();
            }
            
            function updateToggleSwitchStatus(status) {
                
            const xhr = new XMLHttpRequest();
            xhr.open('POST', '../../dbconnection/update_toggle_status.php');
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onload = function() {
                if (xhr.status === 200) {
                    console.log(xhr.responseText);
                } else {
                    console.error('Request failed. Status:', xhr.status);
                }
            };
            xhr.send('toggleStatus=' + status);
        }
            
            manualControlSwitch.addEventListener('change', toggleLinkState);
            toggleLinkState();
            });
    </script> 
    
    <script>
      var checkbox = document.getElementById('manualControlSwitch');
    
      checkbox.addEventListener('change', function() {
        if (this.checked) {
            Swal.fire({
              icon: 'success',
              title: 'Manual Control Mode',
              text: 'Manual control mode activated. Parameters is now in manual control.',
              timer: 2000,
              showConfirmButton: false
            });
        } else {

            Swal.fire({
              icon: 'success',
              title: 'Automated Mode',
              text: 'Manual control disabled. Parameters is now back to automated control.',
              timer: 2000,
              showConfirmButton: false
            });
            window.location.href = '../home/index.php';
          console.log('Toggle state changed. New state: OFF');
        }
      });
    </script>
    
    

  </body>
  <?php
} else {
    header("Location: ../../default.php");
    exit();
}
?>
</html>