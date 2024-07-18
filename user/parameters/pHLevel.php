<?php

session_start();
date_default_timezone_set('Asia/Manila');
if (isset($_SESSION['id']) && isset($_SESSION['username'])) {
?>



  <!DOCTYPE html>
  <html lang="en">

  <head>
    <meta charset="UTF-8">
    <meta http-equiv="refresh" content="30">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../../user/home/favicon.ico" type="image/x-icon">
    <title> pH Level | Farm Cup Control System </title>

    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;700&display=swap">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Product+Sans:wght@300;400;500;600;700&display=swap">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/material-components-web/10.0.0/material-components-web.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KyZXEAg3QhqLMpG8r+Knujsl5+zr/AdmQKsD/8+71zBap2tcUnjddEc1z0xg2I1Q" crossorigin="anonymous">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">


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



    $sqlLatest = "SELECT pH, time_stamp FROM sensordata ORDER BY time_stamp DESC LIMIT 1;";
    $resultLatest = $conn->query($sqlLatest);
    while ($row = $resultLatest->fetch_assoc()) {
      $pHLatest = $row["pH"];
      $timestampLatest = $row["time_stamp"];

      $timestampLatestManila = new DateTime($timestampLatest, new DateTimeZone('UTC'));
      $timestampLatestManila->setTimezone(new DateTimeZone('Asia/Manila'));
      $formattedTimestamp = $timestampLatestManila->format("F d, Y | g:i A");
    }

    $sql = "SELECT time_stamp, pH FROM sensordata ORDER BY time_stamp";
    $result = $conn->query($sql);

    $dateLabels = array();
    $timeLabels = array();
    $pHData = array();


    while ($row = $result->fetch_assoc()) {
      $timestampManila = new DateTime($row["time_stamp"], new DateTimeZone('UTC'));
      $timestampManila->setTimezone(new DateTimeZone('Asia/Manila'));

      $date = $timestampManila->format("F d, Y");
      $time = $timestampManila->format("g:i A");

      $dateLabels[] = $date;
      $timeLabels[] = $time;
      $pHData[] = $row["pH"];
    }

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
        <div class="col-md-2 d-md-block collapse" id='menuCollapse'>
          <aside class="layout-menu menu-vertical menu bg-menu-theme" data-bg-class="bg bg-menu-theme" style='background: linear-gradient(to bottom, #DDEEDF 5%, #f9faf6 95%);'>

            <div class='app-brand-demo'>
              <a href='../home/index.php'>
                <img src="././assets/farm-cup.svg" alt="Farm Cup Logo">
              </a>
            </div>

            <hr class="rounded">

            <ul class='menu-inner py-1 ps'>
              <li class='menu-item '>
                <a href='../home/index.php'>
                  <div class='menu-text'><i class='bx bx-user'></i> Home</div>
                </a>
              </li>

              <li class='menu-item'>
                <a href='../control/parameters.php' id='editParameters' class="menu-link">
                  <div class='menu-text'>Edit Parameters</div>
                </a>
              </li>

              <hr class='rounded'>


              <li class='menu-item '>
                <a href='../parameters/cocopeatMoisture.php'>
                  <div class='menu-text'>Cocopeat Moisture</div>
                </a>
              </li>

              <li class='menu-item '>
                <a href='../parameters/tds.php'>
                  <div class='menu-text'>Total Dissolved Solids</div>
                </a>
              </li>

              <li class='menu-item active'>
                <a href='../parameters/pHLevel.php'>
                  <div class='menu-text'>pH Level</div>
                </a>
              </li>

              <li class='menu-item '>
                <a href='../parameters/ecLevel.php'>
                  <div class='menu-text'>Electrical Conductivity</div>
                </a>
              </li>

              <li class='menu-item '>
                <a href='../parameters/ambientLight.php'>
                  <div class='menu-text'>Ambient Light</div>
                </a>
              </li>

              <li class='menu-item '>
                <a href='../parameters/ambientHumidity.php'>
                  <div class='menu-text'>Ambient Humidity</div>
                </a>
              </li>

              <li class='menu-item '>
                <a href='../parameters/ambientTemperature.php'>
                  <div class='menu-text'>Ambient Temperature</div>
                </a>
              </li>

              <li class='menu-item '>
                <a href='../parameters/waterLevel.php'>
                  <div class='menu-text'>Water Level</div>
                </a>
              </li>
            </ul>

            <hr class='rounded'>

            <div class="custom-control custom-switch">
              <input type="checkbox" class="custom-control-input" id="manualControlSwitch" <?php echo ($toggleStatus == 1) ? 'checked' : ''; ?>>
              <label class="custom-control-label" for="manualControlSwitch">Manual Control Mode</label>
            </div>

          </aside>
        </div>

        <div class="col-md-10">
          <div class="layout-page">
            <div class='md-block d-md-none top-nav'>
              <div class='col-md-12 d-flex' id='button-toggle'>
                <button class="toggle-button btn btn-primary d-md-none" id="menuToggleBtn" data-toggle="collapse" href="#menuCollapse">
                  <i class="fas fa-bars"></i>
                </button>
                <div class='col-md-12 d-flex'>
                  <h1 class='parameter-name-title-sm'>pH Level</h1>
                </div>
              </div>
            </div>

            <div class='page-header d-md-block collapse'>
              <div class='nav-link'>
                <p class='link-top'>
                  <a href="../home/index.php">Home</a> &gt;
                  <a href="#">pH Level</a>
                </p>
              </div>
              <h1 class='parameter-name-title'>pH Level</h1>
            </div>

            <div class='subhead container-fluid'>
              <div class='row'>
                <div class='live-date col-6'>
                  <text class='live-date-text'>Date, Month DD, YYYY</text>

                </div>
                <div class='live-clock col-6 text-right'>
                  <text class='live-clock-text'>
                    00:00:00
                  </text>
                </div>
              </div>
            </div>

            <div class='container'>
              <div class='row'>
                <div class='latest-data-container col-md-8 col-12'>
                  <h2 class='parameter-name'>Updated Reading</h2>
                  <text class='parameter-last-value'><?php echo $pHLatest; ?></text>
                  <p class='parameter-last-updated'>Last Updated: <?php echo $formattedTimestamp; ?></p>
                </div>
                <div class='latest-data-container col-md-4 col-12' style='background-color: #EBEDF0'>
                  pH level affects <b>how well plants can absorb nutrients.</b> If the pH is too high or too low, it can make it hard for plants to get the nutrients they need, leading to stunted growth or nutrient deficiencies.
                </div>
              </div>
            </div>

            <div class='filter-head-container'>
              <button id="toggle-filter-btn-a" class='btn btn-secondary'>
                Toggle Filter
                <i class="fas fa-filter"></i>
              </button>
            </div>

            <div class='filter-container' id="filter-container-a">
              <h5 class='f-title'>Filter Data</h5>
              <hr class='rounded'>

              <div class="form-row">
                <div class="col-md-6">
                  <label for="start-date">Start Date:</label>
                  <input type="date" class="form-control" id="start-date" style='border-radius: 2rem'>
                </div>
                <div class="col-md-6">
                  <label for="end-date">End Date:</label>
                  <input type="date" class="form-control" id="end-date" style='border-radius: 2rem'>
                </div>
              </div>

              <div class="form-row">
                <div class="col-md-6">
                  <label for="start-time">Start Time:</label>
                  <input type="time" class="form-control" id="start-time" style='border-radius: 2rem'>
                </div>
                <div class="col-md-6">
                  <label for="end-time">End Time:</label>
                  <input type="time" class="form-control" id="end-time" style='border-radius: 2rem'>
                </div>
              </div>

              <div class="row justify-content-end" style='padding-top: 1rem'>
                <div class='col-12 d-flex justify-content-end'>
                  <button id="reset" class="btn btn-outline-danger" style='margin-right: 1rem; border-color: transparent; border-radius: 2rem'>Reset</button>
                  <button id="displayLastHourBtn" class="btn btn-outline-secondary" style='margin-right: 1rem; border-color: transparent; border-radius: 2rem'>Display Last Hour Data</button>
                  <button class="btn btn-success" id="apply-filter-btn" style=' border-color: transparent; border-radius: 2rem'>Apply Filter</button>
                </div>
              </div>


            </div>


            <div class="top-grid">
              <div class='row'>

                <div class='col-md-6 col-12'>
                  <div class='data-header'>
                    pH Level Graph
                  </div>

                  <div class="card-top w-100">
                    <div class="top-card">
                      <canvas id="phChart" width="800" height="400" style='padding: 1rem'></canvas>
                    </div>
                  </div>
                </div>

                <div class='col-md-6 col-12'>

                  <div class='data-header'>
                    pH Level Table
                  </div>

                  <div class="card-top w-100">
                    <div class="top-card table-container">
                      <table class='parameters' id='dataTable'>
                        <thead>
                          <tr>
                            <th>Value</th>
                            <th>Time</th>
                            <th>Date</th>
                          </tr>
                        </thead>
                        <tbody>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>


    <!--------------------------------------- *** SCRIPTS *** -------------------------------------------------->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://unpkg.com/boxicons@2.1.4/dist/boxicons.js"></script>
    <!-------------------------------------- *** LIVE CLOCK *** ------------------------------------------------>
    <script src='scripts/dateAndTime.js'></script>

    <script>
      document.getElementById('reset').addEventListener('click', function() {
        location.reload();
      });
    </script>

    <script>
      var pHData = <?php echo json_encode(array_slice($pHData, -50)); ?>;
      var dateLabels = <?php echo json_encode(array_slice($dateLabels, -50)); ?>;
      var timeLabels = <?php echo json_encode(array_slice($timeLabels, -50)); ?>;

      function populateTable() {
        var tableBody = document.getElementById("dataTable").getElementsByTagName('tbody')[0];

        tableBody.innerHTML = '';

        for (var i = pHData.length - 1; i >= 0; i--) {
          var row = tableBody.insertRow();
          var valueCell = row.insertCell(0);
          var timeCell = row.insertCell(1);
          var dateCell = row.insertCell(2);

          valueCell.innerHTML = pHData[i];
          timeCell.innerHTML = timeLabels[i];
          dateCell.innerHTML = dateLabels[i];

          if (i === pHData.length - 1) {
            valueCell.classList.add("bold-cell");
            timeCell.classList.add("bold-cell");
            dateCell.classList.add("bold-cell");
          }
        }
      }

      window.onload = function() {
        populateTable();
      };
    </script>

    <script>
      document.getElementById('displayLast30Btn').addEventListener('click', function() {
        var pHData = <?php echo json_encode($pHData); ?>;
        var dateLabels = <?php echo json_encode($dateLabels); ?>;
        var timeLabels = <?php echo json_encode($timeLabels); ?>;

        updateChart(timeLabels, pHData);
        updateTable(dateLabels, timeLabels, pHData);
      });

      function updateChart(timeLabels, pHData) {
        // Update the chart with the new data
        phChart.data.labels = timeLabels;
        phChart.data.datasets[0].data = pHData;
        phChart.update();
      }

      function updateTable(dateLabels, timeLabels, pHData) {
        const tableBody = document.querySelector('.parameters tbody');
        tableBody.innerHTML = '';

        for (let i = 0; i < timeLabels.length; i++) {
          const row = `<tr>
                                <td>${pHData[i]}</td>
                                <td>${timeLabels[i]}</td>
                                <td>${dateLabels[i]}</td>
                            </tr>`;
          tableBody.innerHTML += row;
        }
      }
    </script>

    <script>
      document.getElementById('displayLastHourBtn').addEventListener('click', function() {
        const last30TimeLabels = timeLabels.slice(-30);
        const last30pHData = pHData.slice(-30);
        const last30DateLabels = dateLabels.slice(-30);

        updateChart(last30TimeLabels, last30pHData);
        updateTable(last30DateLabels, last30TimeLabels, last30pHData);

        Swal.fire({
          icon: 'success',
          title: 'Success',
          text: 'Showing the data from the last hour',
          timer: 4000,
          showConfirmButton: false
        });
      });

      function updateChart(timeLabels, pHData) {
        // Update the chart with the new data
        phChart.data.labels = timeLabels;
        phChart.data.datasets[0].data = pHData;
        phChart.update();
      }

      function updateTable(dateLabels, timeLabels, pHData) {
        const tableBody = document.querySelector('.parameters tbody');
        tableBody.innerHTML = '';

        for (let i = 0; i < timeLabels.length; i++) {
          const row = `<tr>
                                <td>${pHData[i]}</td>
                                <td>${timeLabels[i]}</td>
                                <td>${dateLabels[i]}</td>
                            </tr>`;
          tableBody.innerHTML += row;
        }
      }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
      var timeLabels = <?php echo json_encode(array_slice($timeLabels, -50)); ?>;
      var pHData = <?php echo json_encode(array_slice($pHData, -50)); ?>;

      var ctx = document.getElementById('phChart').getContext('2d');

      var options = {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
          y: {
            min: 4,
            max: 10,
            grid: {
              display: true
            },
            ticks: {
              font: {
                family: 'Product Sans',
                size: 15
              },
            }
          },
          x: {
            grid: {
              display: false
            },
            ticks: {
              display: false
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
            radius: 1
          }
        }
      };

      var borders = {
        borderColor: 'rgba(12, 128, 46, 1)',
        borderWidth: 2,
        fill: true,
        tension: 0.25

      }

      var gradient = ctx.createLinearGradient(0, 0, 0, 400);
      gradient.addColorStop(0, 'rgba(12, 128, 46, 0.4)');
      gradient.addColorStop(1, 'rgba(12, 128, 46, 0)');

      var phChart = new Chart(ctx, {
        type: 'line',
        data: {
          labels: timeLabels,
          datasets: [{
            label: '',
            data: pHData,
            backgroundColor: gradient,
            ...borders

          }]
        },
        options: options
      });
    </script>

    <script>
      function toggleFilterContainerA() {
        const filterContainer = document.getElementById('filter-container-a');
        filterContainer.classList.toggle('visible');
      }

      document.getElementById('toggle-filter-btn-a').addEventListener('click', toggleFilterContainerA);
    </script>

    <script>
      const originalDateLabels = <?php echo json_encode($dateLabels); ?>;
      const originalTimeLabels = <?php echo json_encode($timeLabels); ?>;
      const originalpHData = <?php echo json_encode($pHData); ?>;

      document.getElementById('apply-filter-btn').addEventListener('click', function() {
        const startDate = document.getElementById('start-date').value;
        const endDate = document.getElementById('end-date').value;
        const startTime = document.getElementById('start-time').value;
        const endTime = document.getElementById('end-time').value;

        const filteredData = filterData(startDate, endDate, startTime, endTime);

        updateChart(filteredData.timeLabels, filteredData.pHData);
        updateTable(filteredData.dateLabels, filteredData.timeLabels, filteredData.pHData);

        console.log('clicked');
        console.log('Filtered Data: ', filteredData);


      });

      function filterData(startDate, endDate, startTime, endTime) {
        const startDateTime = startDate ? new Date(`${startDate}T${startTime || '00:00'}`) : new Date();
        const endDateTime = endDate ? new Date(`${endDate}T${endTime || '23:59'}`) : new Date();


        if (!endDate) {
          endDateTime.setDate(endDateTime.getDate() - 1);
        }

        const filteredDateLabels = [];
        const filteredTimeLabels = [];
        const filteredpHData = [];

        for (let i = 0; i < originalDateLabels.length; i++) {
          const dateTime = new Date(originalDateLabels[i] + ' ' + originalTimeLabels[i]);

          // Check if the data falls within the selected range
          if (dateTime >= startDateTime && dateTime <= endDateTime) {
            filteredDateLabels.push(originalDateLabels[i]);
            filteredTimeLabels.push(originalTimeLabels[i]);
            filteredpHData.push(originalpHData[i]);
          }
        }

        Swal.fire({
          icon: 'success',
          title: 'Success',
          text: 'Filtered Data Updated',
          timer: 4000,
          showConfirmButton: false
        });

        return {
          dateLabels: filteredDateLabels,
          timeLabels: filteredTimeLabels,
          pHData: filteredpHData,


        };
      }

      function updateChart(timeLabels, pHData) {
        // Your chart update code here
        console.log('Updating chart with filtered data');
        phChart.data.labels = timeLabels;
        phChart.data.datasets[0].data = pHData;
        phChart.update();


      }

      function updateTable(dateLabels, timeLabels, pHData) {
        const tableBody = document.querySelector('.parameters tbody');
        console.log('Updating table with filtered data');
        tableBody.innerHTML = '';

        for (let i = 0; i < timeLabels.length; i++) {
          const row = `<tr>
                                <td>${pHData[i]}</td>
                                <td>${timeLabels[i]}</td>
                                <td>${dateLabels[i]}</td>
                            </tr>`;
          tableBody.innerHTML += row;
        }
      }
    </script>

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