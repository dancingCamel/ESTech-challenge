<?php

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

	include("./config.php");

    $conn = new mysqli($dbHost, $dbUsername, $dbPassword, $dbName);

    if ($conn->connect_error) {
        die("Connection failed: " . $db->connect_error);
    }

    echo "Connection opened successfully <br>";

    $path = "./import.csv";

    $csvFile = fopen($path, "r") or die("Unable to open file ".$path);

    echo "File opened: " .$path. "<br>";
    
    // skip the first line as it's a title row
    fgetcsv($csvFile);
    
    while(($line = fgetcsv($csvFile)) !== FALSE){
        // Get row data
        $sku   = $line[0];
        $account_ref  = $line[1];
        $user_ref  = $line[2];
        $quantity = $line[3];
        $value = $line[3];

        if ($user_ref == ""){
            $query = "INSERT INTO prices (product_id, account_id, quantity, value, created_at) VALUES ((SELECT id FROM products WHERE sku = '".$sku."' LIMIT 1), (SELECT id FROM accounts WHERE external_reference = '".$account_ref."' LIMIT 1), '" . $quantity . "', '" . $value . "', NOW())";
        } else {
            $query = "INSERT INTO prices (product_id, account_id, user_id, quantity, value, created_at) VALUES ((SELECT id FROM products WHERE sku = '".$sku."' LIMIT 1), (SELECT id FROM accounts WHERE external_reference = '".$account_ref."' LIMIT 1), (SELECT id FROM users WHERE external_reference = '".$user_ref."' LIMIT 1), '" . $quantity . "', '" . $value . "', NOW())";

        }
        
        $result = $conn->query($query);
        
        if (!$result) {

            echo "Insert Failed.<br>";
            echo mysqli_errno($conn);
            echo "<br>";
            echo mysqli_error($conn);
            fclose($csvFile);
            mysqli_close($conn);
            exit;
    
        }
        
    }

    echo "Inserted Successfully";

    fclose($csvFile);

    mysqli_close($conn);

?>