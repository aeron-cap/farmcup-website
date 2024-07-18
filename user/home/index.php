<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
if(isset($_SESSION['id']) && isset($_SESSION['username'])) {
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta http-equiv="refresh" content="30">
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="icon" href="favicon.ico" type="image/x-icon">
        <title> Farm Cup Control System </title>
        
        <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
        
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Product+Sans:wght@300;400;500;600;700&display=swap">
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;700&display=swap">
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/material-components-web/10.0.0/material-components-web.min.css">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" >
        <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
        
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
        
        <link rel="stylesheet" href="./style.css">
        
        <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    </head>

    <body>
        <style>
          .carousel-indicators li {
            border-radius: 50%;
            width: 0.5rem;
            height: 0.5rem; 
            margin-right: 5px;
            background-color: grey;
          }
          .carousel-indicators .active {
            background-color: #222831; /* Background color for active indicator */
            box-shadow: none;
            padding-left: none;
            border-radius: none!important;
          }
        </style>
        <?php
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        require_once('../../dbconnection/config.php');
        $conn = new mysqli($db_host, $db_user, $db_password, $db_name);

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $sql = "SELECT * FROM sensordata ORDER BY time_stamp DESC LIMIT 2";
        $result = $conn->query($sql);

        $data = array();
        while($row = $result->fetch_assoc()) {
            $data[] = $row;
        }

        if (count($data) >= 2) {
            // Extract data from the last two rows
            $lastRow = $data[0];
            $secondLastRow = $data[1];
        
            // Extract values from the last row
            $moisture_last = $lastRow["moisture"];
            $tds_last = $lastRow["tds"];
            $pH_last = $lastRow["pH"];
            $EC_last = $lastRow["EC"];
            $ambient_light_last = $lastRow["ambient_light"];
            $temperature_last = $lastRow["temperature"];
            $humidity_last = $lastRow["humidity"];
            $waterlevel_last = round(((25.4 - $lastRow["waterlevel"])/23.40)*100);
        
            // Extract values from the second last row
            $moisture_second_last = $secondLastRow["moisture"];
            $tds_second_last = $secondLastRow["tds"];
            $pH_second_last = $secondLastRow["pH"];
            $EC_second_last = $secondLastRow["EC"];
            $ambient_light_second_last = $secondLastRow["ambient_light"];
            $temperature_second_last = $secondLastRow["temperature"];
            $humidity_second_last = $secondLastRow["humidity"];
            $waterlevel_second_last = round(((25.4 - $secondLastRow["waterlevel"])/23.40)*100);
        
            // Compute percent change for each parameter
            $percent_change_moisture = ($moisture_second_last != 0) ? (($moisture_last - $moisture_second_last) / $moisture_second_last) * 100 : 0;
            $percent_change_tds = ($tds_second_last != 0) ? (($tds_last - $tds_second_last) / $tds_second_last) * 100 : 0;
            $percent_change_pH = ($pH_second_last != 0) ? (($pH_last - $pH_second_last) / $pH_second_last) * 100 : 0;
            $percent_change_EC = ($EC_second_last != 0) ? (($EC_last - $EC_second_last) / $EC_second_last) * 100 : 0;
            $percent_change_ambient_light = ($ambient_light_second_last != 0) ? (($ambient_light_last - $ambient_light_second_last) / $ambient_light_second_last) * 100 : 0;
            $percent_change_temperature = ($temperature_second_last != 0) ? (($temperature_last - $temperature_second_last) / $temperature_second_last) * 100 : 0;
            $percent_change_humidity = ($humidity_second_last != 0) ? (($humidity_last - $humidity_second_last) / $humidity_second_last) * 100 : 0;
            $percent_change_waterlevel = ($waterlevel_second_last != 0) ? (($waterlevel_last - $waterlevel_second_last) / $waterlevel_second_last) * 100 : 0;

        
            // Determine the direction of change for each parameter
            function determineArrow($percent_change) {
                if ($percent_change > 0) {
                    return "increase";
                } elseif ($percent_change < 0) {
                    return "decrease";
                } else {
                    return "no-change";
                }
            }
        
            // Assign the arrow direction for each parameter
            $arrow_moisture = determineArrow($percent_change_moisture);
            $arrow_tds = determineArrow($percent_change_tds);
            $arrow_pH = determineArrow($percent_change_pH);
            $arrow_EC = determineArrow($percent_change_EC);
            $arrow_ambient_light = determineArrow($percent_change_ambient_light);
            $arrow_temperature = determineArrow($percent_change_temperature);
            $arrow_humidity = determineArrow($percent_change_humidity);
            $arrow_waterlevel = determineArrow($percent_change_waterlevel);
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
        
        // ----------------- SQL Request for ALL DATA -------------------------------------
        $sqlDisplay = "SELECT * FROM sensordata ORDER BY time_stamp DESC LIMIT 50";
        $resultDisplay = $conn->query($sqlDisplay);
        
        $timeStampArray = array();
        $moistureArray = array();
        $tdsArray = array();
        $phArray = array();
        $ecArray = array();
        $ambientLightArray = array();
        $temperatureArray = array();
        $humidityArray = array();
        $waterLevelArray = array();
        
        if ($resultDisplay->num_rows > 0) {
            while($row = $resultDisplay->fetch_assoc()) {
                $timeStampArray[] = $row['time_stamp'];
                $moistureArray[] = $row['moisture'];
                $tdsArray[] = $row['tds'];
                $phArray[] = $row['pH'];
                $ecArray[] = $row['EC'];
                $ambientLightArray[] = $row['ambient_light'];
                $temperatureArray[] = $row['temperature'];
                $humidityArray[] = $row['humidity'];
                $waterLevelArray[] = round(((25.4-$row['waterlevel'])/23.40)*100);
            }
        } else {
            echo "0 results";
        }

        
        $conn->close();
        ?>
      <div class="container-fluid full-height">
        <div class="row full-height">
            <!-- Navbar -->
          <div class="col-md-2 d-md-block collapse" id='menuCollapse' >
              <aside class="layout-menu menu-vertical menu bg-menu-theme" data-bg-class="bg bg-menu-theme" style='background: linear-gradient(to bottom, #DDEEDF 5%, #f9faf6 95%);'>
                <!-------========== FARM CUP LOGO ============------>
                <div class = 'app-brand-demo'>
                  <a href='index.php'>
                    <img src="././assets/farm-cup.svg" alt="Farm Cup Logo">
                  </a>
                </div>
                <!-------------------------------------------------->
                <hr class="rounded">
                <!------------------** MENU **---------------------->
                <ul  class='menu-inner py-1 ps'>
                  <li class='menu-item active'>
                      <a href= '../home/index.php'>
                        <div class='menu-text'><i class='bx bx-user'></i> Home</div>
                      </a>
                  </li>

                  <li class='menu-item'>
                    <a href='../control/parameters.php' id='editParameters' class="menu-link">
                      <div class='menu-text'>Edit Parameters</div>
                    </a>
                  </li>

                  <hr class='rounded'>

                  <!---------- COCOPEAT MOITSURE -------->
                  <li class='menu-item '>
                      <a href= '../parameters/cocopeatMoisture.php'>
                        <div class='menu-text'>Cocopeat Moisture</div>
                      </a>
                  </li>

                  <!--------- TOTAL DISSOLVED SOLIDS ---->
                  <li class='menu-item '>
                      <a href= '../parameters/tds.php'>
                        <div class='menu-text'>Total Dissolved Solids</div>
                      </a>
                  </li>

                  <!--------- PH LEVEL ---->
                  <li class='menu-item'>
                      <a href= '../parameters/pHLevel.php'>
                        <div class='menu-text'>pH Level</div>
                      </a>
                  </li>

                  <!--- ELECTRICAL CONDUCTIVITY --->
                  <li class='menu-item '>
                      <a href= '../parameters/ecLevel.php'>
                        <div class='menu-text'>Electrical Conductivity</div>
                      </a>
                  </li>

                  <!--- AMBIENT LIGHT --->
                  <li class='menu-item '>
                      <a href= '../parameters/ambientLight.php'>
                        <div class='menu-text'>Ambient Light</div>
                      </a>
                  </li>

                  <!---- AMBIENT HUMIDITY ---->

                  <li class='menu-item '>
                      <a href= '../parameters/ambientHumidity.php'>
                        <div class='menu-text'>Ambient Humidity</div>
                      </a>
                  </li>

                  <!----- AMBIENT TEMPERATURE ------>
                  <li class='menu-item '>
                      <a href= '../parameters/ambientTemperature.php'>
                        <div class='menu-text'>Ambient Temperature</div>
                      </a>
                  </li>

                  <!-----WATER LEVEL ----->
                  <li class='menu-item'>
                      <a href= '../parameters/waterLevel.php'>
                        <div class='menu-text'>Water Level</div>
                      </a>
                  </li>
                </ul>

                <hr class='rounded'>

                <!----SWITCH----->
                <div class="custom-control custom-switch">
                  <input type="checkbox" class="custom-control-input" id="manualControlSwitch" <?php echo ($toggleStatus == 1) ? 'checked' : ''; ?>>
                  <label class="custom-control-label" for="manualControlSwitch">Manual Control Mode</label>
                </div>
                <!---=====================================** ------------------- **=======================================---->
              </aside>
          </div>

          <!-- Content Section -->
          <div class="col-md-10">
            <div class="layout-page">
              <div class='md-block d-md-none'>
                <div class='col-md-12 d-flex'>
                  <button class="toggle-button btn btn-primary d-md-none" id="menuToggleBtn" data-toggle="collapse" href="#menuCollapse" >
                    =
                  </button>
                  <div class='col-md-12 d-flex justify-content-center'>
                    <h1 class='parameter-name-title'>Dashboard</h1>
                  </div>
                </div>
              </div>

              <!-------------------------------- CONTENT NG WEBSITE ---------------------------------------->
              <div class='page-header d-md-block collapse'>
                  <h1 class='parameter-name-title'>Dashboard</h1>
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
                
              <div class="top-grid">
                <div class='row'>
                  <div class='col-12'>
                    <div class="card-top w-100">
                      <div id="graphCarousel" class="carousel slide" data-interval="false">
                        <div class="carousel-inner" >
                          <div class="carousel-item active">
                              <h6 style="text-align: center; margin: 0.5rem">Coco Peat Moisture</h6>
                            <div class="graph-container">
                              <canvas id="moistureGraph"></canvas>
                            </div>
                          </div>
                          <div class="carousel-item">
                              <h6 style="text-align: center; margin: 0.5rem">Total Dissolved Solids</h6>
                            <div class="graph-container">
                              <canvas id="tdsGraph"></canvas>
                            </div>
                          </div>
                          <div class="carousel-item">
                              <h6 style="text-align: center; margin: 0.5rem">pH Level</h6>
                            <div class="graph-container">
                              <canvas id="pHLevelGraph"></canvas>
                            </div>
                          </div>
                          <div class="carousel-item">
                              <h6 style="text-align: center; margin: 0.5rem">Electrical Conductivity</h6>
                            <div class="graph-container">
                              <canvas id="ecLevelGraph"></canvas>
                            </div>
                          </div>
                          <div class="carousel-item">
                              <h6 style="text-align: center; margin: 0.5rem">Ambient Light</h6>
                            <div class="graph-container">
                              <canvas id="lightGraph"></canvas>
                            </div>
                          </div>
                          <div class="carousel-item">
                              <h6 style="text-align: center; margin: 0.5rem">Ambient Humidity</h6>
                            <div class="graph-container">
                              <canvas id="humidityGraph"></canvas>
                            </div>
                          </div>
                          <div class="carousel-item">
                              <h6 style="text-align: center; margin: 0.5rem">Ambient Temperature</h6>
                            <div class="graph-container">
                              <canvas id="temperatureGraph"></canvas>
                            </div>
                          </div>
                          <div class="carousel-item">
                              <h6 style="text-align: center; margin: 0.5rem">Water Level</h6>
                            <div class="graph-container">
                              <canvas id="waterLevelGraph"></canvas>
                            </div>
                          </div>                          
                        </div>
                        <a class="carousel-control-prev" href="#graphCarousel" role="button" data-slide="prev">
                          <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                          <span class="sr-only">Previous</span>
                        </a>
                        <a class="carousel-control-next" href="#graphCarousel" role="button" data-slide="next">
                          <span class="carousel-control-next-icon" aria-hidden="true"></span>
                          <span class="sr-only">Next</span>
                        </a>
                        <ol class="carousel-indicators">
                            <li data-target="#graphCarousel" data-slide-to="0" class="active"></li>
                            <li data-target="#graphCarousel" data-slide-to="1"></li>
                            <li data-target="#graphCarousel" data-slide-to="2"></li>
                            <li data-target="#graphCarousel" data-slide-to="3"></li>
                            <li data-target="#graphCarousel" data-slide-to="4"></li>
                            <li data-target="#graphCarousel" data-slide-to="5"></li>
                            <li data-target="#graphCarousel" data-slide-to="6"></li>
                            <li data-target="#graphCarousel" data-slide-to="7"></li>
                          </ol>
                      </div>

                    </div>
                  </div>
                </div>
              </div>

              <div class='parameters-grid'>
                <div class='row'>

                    <div class='col-sm-3 card-column'>
                        <div class="card w-100"  style='background: linear-gradient(to bottom, #DDDDDD 5%, #f9faf6 95%);'>
                          <div class="card-body">
                            <a class='link-to' href = '../parameters/cocopeatMoisture.php' data-toggle='tooltip' data-placement='auto right' title='Coco peat retains water well, helping to keep the plants hydrated without drowning them.'>
                            <div class="row card-title" style="padding-left: 1rem; padding-right: 1rem">
                              <div class="col-9">
                                <h5 class='card-title'>Coco Peat Moisture</h5>
                              </div>
                              <div class='col-3 text-right tooltip-logo'>
                                <i class='fa fa-info-circle'></i>
                              </div>
                            </div>
                            <div class = 'col-12'>
                              <p class="card-text"><?php echo $moisture_last; ?></p>
                            </div>
                            <div class = 'col-6'>
                              <div class="stat">
                                <div class="stat-help-text">
                                    <span class="stat-arrow <?php echo $arrow_moisture; ?>"></span>
                                    <?php echo round($percent_change_moisture, 2); ?>%
                                </div>
                              </div>
                            </div>
                            </a>
                          </div>
                        </div>
                    </div>

                    <div class='col-sm-3 card-column'>
                        <div class="card w-100"  style='background: linear-gradient(to bottom, #FFAAA6 5%, #f9faf6 95%);'>
                          <div class="card-body">
                            <a class='link-to' href = '../parameters/tds.php' data-toggle='tooltip' data-placement='auto right' title='It measures the concentration of nutrients in the water, ensuring plants get the right amount of food for healthy growth.'>
                            <div class="row card-title" style="padding-left: 1rem; padding-right: 1rem">
                              <div class="col-9">
                                <h5 class='card-title'>Total Dissolved Solids</h5>
                              </div>
                              <div class='col-3 text-right tooltip-logo'>
                                <i class='fa fa-info-circle'></i>
                              </div>
                            </div>
                            <div class = 'col-12'>
                              <p class="card-text"><?php echo $tds_last; ?> ppm</p>
                            </div>
                            <div class = 'col-12'>
                              <div class="stat">
                                <div class="stat-help-text">
                                    <span class="stat-arrow <?php echo $arrow_tds; ?>"></span>
                                    <?php echo round($percent_change_tds, 2); ?>%
                                </div>
                              </div>
                            </div>
                            </a>
                          </div>
                        </div>
                    </div>

                    <div class='col-sm-3 card-column'>
                        <div class="card w-100"  style='background: linear-gradient(to bottom, #FDD998 5%, #f9faf6 95%);'>
                          <div class="card-body">
                            <a class='link-to' href = '../parameters/pHLevel.php' data-toggle='tooltip' data-placement='auto right' title='The pH level refers to how acidic or alkaline the water is, crucial for ensuring plants can absorb nutrients effectively for optimal growth.'>
                            <div class="row card-title" style="padding-left: 1rem; padding-right: 1rem">
                              <div class="col-9">
                                <h5 class='card-title'>pH Level</h5>
                              </div>
                              <div class='col-3 text-right tooltip-logo'>
                                <i class='fa fa-info-circle'></i>
                              </div>
                            </div>
                            <div class = 'col-12'>
                              <p class="card-text"><?php echo $pH_last; ?></p>
                            </div>
                            <div class = 'col-12'>
                              <div class="stat">
                                <div class="stat-help-text">
                                    <span class="stat-arrow <?php echo $arrow_pH; ?>"></span>
                                    <?php echo round($percent_change_pH, 2); ?>%
                                </div>
                              </div>
                            </div>
                            </a>
                          </div>
                        </div>
                    </div>

                    <div class='col-sm-3 card-column'>
                        <div class="card w-100"  style='background: linear-gradient(to bottom, #FFF798 5%, #f9faf6 95%);'>
                          <div class="card-body">
                            <a class='link-to' href='../parameters/ambientHumidity.php' data-toggle='tooltip' data-placement='auto right' title='It measures the concentration of dissolved salts in the water, indicating the nutrient strength available.'>
                            <div class="row card-title" style="padding-left: 1rem; padding-right: 1rem">
                              <div class="col-9">
                                <h5 class='card-title'>Electrical Conductivity</h5>
                              </div>
                              <div class='col-3 text-right tooltip-logo'>
                                <i class='fa fa-info-circle'></i>
                              </div>
                            </div>
                            <div class = 'col-12'>
                              <p class="card-text" ><?php echo $EC_last; ?> ms/cm</p>
                            </div>
                            <div class = 'col-12'>
                              <div class="stat">
                                <div class="stat-help-text">
                                    <span class="stat-arrow <?php echo $arrow_EC; ?>"></span>
                                    <?php echo round($percent_change_EC, 2); ?>%
                                </div>
                              </div>
                            </div>
                            </a>
                          </div>
                        </div>
                    </div>

                    <div class='col-sm-3 card-column'>
                        <div class="card w-100"  style='background: linear-gradient(to bottom, #94DAFF 5%, #f9faf6 95%);'>
                          <div class="card-body">
                            <a class='link-to' href = '../parameters/ambientLight.php' data-toggle='tooltip' data-placement='auto right' title='It refers to the natural or artificial light present in the growing environment, which is essential for photosynthesis '>
                            <div class="row card-title" style="padding-left: 1rem; padding-right: 1rem">
                              <div class="col-9">
                                <h5 class='card-title'>Ambient Light</h5>
                              </div>
                              <div class='col-3 text-right tooltip-logo'>
                                <i class='fa fa-info-circle'></i>
                              </div>
                            </div>
                            <div class = 'col-12'>
                              <p class="card-text"><?php echo $ambient_light_last; ?> lux</p>
                            </div>
                            <div class = 'col-12'>
                              <div class="stat">
                                <div class="stat-help-text">
                                  <span class="stat-arrow <?php echo $arrow_ambient_light; ?>"></span>
                                  <?php echo round($percent_change_ambient_light, 2); ?>%
                                </div>
                              </div>
                            </div>
                            </a>
                          </div>
                        </div>
                    </div>

                    <div class='col-sm-3 card-column'>
                        <div class="card w-100"  style='background: linear-gradient(to bottom, #B9BBDF 5%, #f9faf6 95%);'>
                          <div class="card-body">
                            <a class='link-to' href='../parameters/ambientHumidity.php' data-toggle='tooltip' data-placement='auto right' title='Humidity refers to the amount of water vapor present in the air.'>
                            <div class="row card-title" style="padding-left: 1rem; padding-right: 1rem">
                              <div class="col-9">
                                <h5 class='card-title'>Ambient Humidity</h5>
                              </div>
                              <div class='col-3 text-right tooltip-logo'>
                                <i class='fa fa-info-circle'></i>
                              </div>
                            </div>
                            <div class = 'col-12'>
                              <p class="card-text"><?php echo $humidity_last; ?>%</p>
                            </div>
                            <div class = 'col-12'>
                              <div class="stat">
                                <div class="stat-help-text">
                                  <span class="stat-arrow <?php echo $arrow_humidity; ?>"></span>
                                  <?php echo round($percent_change_humidity, 2); ?>%
                                </div>
                              </div>
                            </div>
                            </a>
                          </div>
                        </div>
                    </div>

                    <div class='col-sm-3 card-column'>
                        <div class="card w-100"  style='background: linear-gradient(to bottom, #FFC0D9 5%, #f9faf6 95%);'>
                          <div class="card-body">
                            <a class='link-to' href = '../parameters/ambientTemperature.php' data-toggle='tooltip' data-placement='auto right' title='It pertains to the surrounding air temperature in the growing environment, affecting plant metabolism, nutrient uptake, and overall growth rates.'>
                            <div class="row card-title" style="padding-left: 1rem; padding-right: 1rem">
                              <div class="col-9">
                                <h5 class='card-title'>Ambient Temperature</h5>
                              </div>
                              <div class='col-3 text-right tooltip-logo'>
                                <i class='fa fa-info-circle'></i>
                              </div>
                            </div>
                            <div class = 'col-12'>
                              <p class="card-text"><?php echo $temperature_last; ?>&deg;C</p>
                            </div>
                            <div class = 'col-12'>
                              <div class="stat">
                                <div class="stat-help-text">
                                    <span class="stat-arrow <?php echo $arrow_temperature; ?>"></span>
                                    <?php echo round($percent_change_temperature, 2); ?>%
                                </div>
                              </div>
                            </div>
                            </a>
                          </div>
                        </div>
                    </div>

                    <div class='col-sm-3 card-column'>
                        <div class="card w-100"  style='background: linear-gradient(to bottom, #DFD3C3 5%, #f9faf6 95%);'>
                          <div class="card-body">
                            <a class='link-to' href = '../parameters/waterLevel.php' data-toggle='tooltip' data-placement='auto right' title='This indicates how much nutrient solution is present in the container, vital for providing a consistent supply of water.'>
                            <div class="row card-title" style="padding-left: 1rem; padding-right: 1rem">
                              <div class="col-9">
                                <h5 class='card-title'>Water Level</h5>
                              </div>
                              <div class='col-3 text-right tooltip-logo'>
                                <i class='fa fa-info-circle'></i>
                              </div>
                            </div>
                            <div class = 'col-12'>
                              <p class="card-text"><?php echo $waterlevel_last; ?> %</p>
                            </div>
                            <div class = 'col-12'>
                              <div class="stat">
                                <div class="stat-help-text">
                                    <span class="stat-arrow <?php echo $arrow_waterlevel; ?>"></span>
                                    <?php echo round($percent_change_waterlevel, 2); ?>%
                                </div>
                              </div>
                            </div>
                            </a>
                          </div>
                        </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
      </div>
      </div>


    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

      <!--------- DATE AND TIME SCRIPT ----------->
      <script src='scripts/dateAndTime.js'></script>

      <!------------- TOGGLE MANUAL MODE --------->
      <script src='scripts/toggleSwitch.js'></script>

  </body>
  
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
              text: 'Manual control mode activated. Parameters are now in manual control.',
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
          console.log('Toggle state changed. New state: OFF');
        }
      });
    </script>

    
    
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>


    <script>
        var options = {
            scales: {
                x: {
                    display: true,
                    ticks: {
                        font: {
                            family: 'Product Sans',
                            size: 12
                        },
                        display: false
                    },
                    grid: {
                        display: true
                    }
                },
                y: {
                    display: true,
                    grid: {
                        display: false
                    },
                    beginAtZero: true,
                    ticks: {
                        font: {
                            family: 'Product Sans',
                            size: 12
                        },
                        display: true
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            },
            elements: {
                point: {
                    radius: 0
                }
            }
        };
        
        
        var border = {
            borderColor: 'rgba(12, 128, 46, 1)',
            borderWidth: 2,
            fill: true,
            tension: 0.2
        };
        
        function createGradient(ctx) {
            var gradient = ctx.createLinearGradient(0, 0, 0, 400);
            gradient.addColorStop(0, 'rgba(12, 128, 46, 0.4)');
            gradient.addColorStop(1, 'rgba(12, 128, 46, 0)');
            return gradient;
        }
                
    
        var ctx1 = document.getElementById('moistureGraph').getContext('2d');
        var ctx2 = document.getElementById('tdsGraph').getContext('2d');
        var ctx3 = document.getElementById('pHLevelGraph').getContext('2d');
        var ctx4 = document.getElementById('ecLevelGraph').getContext('2d');
        var ctx5 = document.getElementById('lightGraph').getContext('2d');
        var ctx6 = document.getElementById('humidityGraph').getContext('2d');
        var ctx7 = document.getElementById('temperatureGraph').getContext('2d');
        var ctx8 = document.getElementById('waterLevelGraph').getContext('2d');
    
    
        // Cocopeat Moisture Graph
        var moistureData = {
            labels: <?php echo json_encode($timeStampArray); ?>,
            datasets: [{
                label: "Coco Peat Moisture",
                data: <?php echo json_encode($moistureArray); ?>,
                backgroundColor: createGradient(ctx1),
                ...border
                
            }]
        };
        
        var moistureGraph = new Chart(ctx1, {
            type: 'line',
            data: moistureData,
            options: options
        });

        var tdsData = {
            labels: <?php echo json_encode($timeStampArray); ?>,
            datasets: [{
                label: "TDS",
                data: <?php echo json_encode($tdsArray); ?>,
                backgroundColor: createGradient(ctx2),
                ...border
            }]
        };
        
        var tdsGraph = new Chart(ctx2, {
            type: 'line',
            data: tdsData,
            options: options
        });

        var pHLevelData = {
            labels: <?php echo json_encode($timeStampArray); ?>,
            datasets: [{
                label: "pH Level",
                data: <?php echo json_encode($phArray); ?>,
                backgroundColor: createGradient(ctx3),
                ...border
            }]
        };
        
        var pHLevelGraph = new Chart(ctx3, {
            type: 'line',
            data: pHLevelData,
            options: options
        });
        
        var ecLevelData = {
            labels: <?php echo json_encode($timeStampArray); ?>,
            datasets: [{
                label: "Electrical Conductivity",
                data: <?php echo json_encode($ecArray); ?>,
                backgroundColor: createGradient(ctx4),
                ...border
            }]
        };
        
        var ecLevelGraph = new Chart(ctx4, {
            type: 'line',
            data: ecLevelData,
            options: options
        });
        
        var ambientLightData = {
            labels: <?php echo json_encode($timeStampArray); ?>,
            datasets: [{
                label: "Ambient Light",
                data: <?php echo json_encode($ambientLightArray); ?>,
                backgroundColor: createGradient(ctx5),
                ...border
            }]
        };
        
        var lightGraph = new Chart(ctx5, {
            type: 'line',
            data: ambientLightData,
            options: options
        });
        
        var humidityData = {
            labels: <?php echo json_encode($timeStampArray); ?>,
            datasets: [{
                label: "Ambient Humidity",
                data: <?php echo json_encode($humidityArray); ?>,
                backgroundColor: createGradient(ctx6),
                ...border
            }]
        };
        
        var humidityGraph = new Chart(ctx6, {
            type: 'line',
            data: humidityData,
            options: options
        });
        
        var temperatureData = {
            labels: <?php echo json_encode($timeStampArray); ?>,
            datasets: [{
                label: "Ambient Temperature",
                data: <?php echo json_encode($temperatureArray); ?>,
                backgroundColor: createGradient(ctx7),
                ...border
              
            }]
        };
        
        var temperatureGraph = new Chart(ctx7, {
            type: 'line',
            data: temperatureData,
            options: options
        });
        
        var waterLevelData = {
            labels: <?php echo json_encode($timeStampArray); ?>,
            datasets: [{
                label: "Water Level",
                data: <?php echo json_encode($waterLevelArray); ?>,
                backgroundColor: createGradient(ctx8),
                ...border
            }]
        };
        
        var waterLevelGraph = new Chart(ctx8, {
            type: 'line',
            data: waterLevelData,
            options: options
        });
</script>

<?php
} else {
    header("Location: ../../default.php");
    exit();
}
?>
</html>