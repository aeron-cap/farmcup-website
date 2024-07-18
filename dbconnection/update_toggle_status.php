<?php

include_once("config.php");

// Check if toggleStatus is set and not empty
if (isset($_POST['toggleStatus'])) {
    // Get toggleStatus value
    $toggleStatus = $_POST['toggleStatus'];

    try {
        // Create a new PDO instance
        $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_password);
        
        // Set the PDO error mode to exception
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Prepare and execute the update statement
        $stmt = $pdo->prepare("UPDATE toggle_switch SET toggle = :toggleStatus, time_stamp = CURRENT_TIMESTAMP WHERE id = 1");
        $stmt->bindParam(':toggleStatus', $toggleStatus, PDO::PARAM_INT);
        $stmt->execute();

        echo "Toggle status updated successfully";
    } catch(PDOException $e) {
        echo "Error updating toggle status: " . $e->getMessage();
    }

    // Close connection
    $pdo = null;
} else {
    echo "Toggle status is not set";
}
?>
