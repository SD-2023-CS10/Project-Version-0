<?php
    // session_start();
    // $username = $_SESSION["username"];
    $username = "bhuyck";
    $pythonScript = realpath(__DIR__ . '/../csv/csv-export.py');
    $command = "python3 $pythonScript $username";
    shell_exec($command);
?>