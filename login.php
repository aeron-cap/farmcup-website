<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    require_once('./dbconnection/config.php');

    $conn = new mysqli($db_host, $db_user, $db_password, $db_name);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $user = $_POST["userName"];
    $pass = $_POST["passWord"];

    // Authenticate Login Credentials
    $sql = "SELECT * FROM userdata WHERE username = '$user' AND password = '$pass'";
    $result = $conn->query($sql);

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();

        $_SESSION['id'] = $row['id'];
        $_SESSION['username'] = $row['username'];

        // Redirect to the home page
        header('Location: user/home/index.php');
        exit;
    } else {
        echo '<script>
                swal({
                    title: "Error!",
                    text: "Invalid username or password.",
                    icon: "error",
                    button: {
                        text: "Try Again",
                        className: "btn btn-primary btn-center", 
                        value: true,
                        closeModal: true,
                    },
                    buttonsStyling: false, 
                    customClass: {
                        confirmButton: "btn-center", 
                        popup: "custom-popup" 
                    },
                    closeOnClickOutside: false, 
                    closeOnEsc: false
                }).then(function() {

                });
            </script>';
    }

    $conn->close();
}
