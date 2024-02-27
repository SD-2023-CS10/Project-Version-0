<?php

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // $username = $_SESSION["username"];
            $username = "clemak";
            echo $username;
            $cwd = getcwd(); 

            // Dependency Checks
            $dependencyScript = '/../crawler/dependency-checks.php';
            $depAbsolutePath = realpath($cwd . '/' . $dependencyScript);
            if ($depAbsolutePath) {
                // Open a pipe to the Python script so we can capture output as it is printed from crawl
                $descriptorspec = array(
                    0 => array("pipe", "r"),  // stdin
                    1 => array("pipe", "w"),  // stdout
                    2 => array("pipe", "w")   // stderr
                );
                $process = proc_open("php $depAbsolutePath", $descriptorspec, $pipes);
    
                // Check if the process was successfully created
                if (is_resource($process)) {
                    // Read from the pipe and display the output dynamically
                    $output = stream_get_contents($pipes[1]);
                    fclose($pipes[1]);
                    echo "$output";
                    flush();
    
                    // Close the process and get the return value
                    $return_value = proc_close($process);
                } else {
                    echo "Error: Failed to launch PHP script.";
                }
            } else {
                echo "Error: PHP script not found.";
            } 

            // Run Crawl
            $crawlScript = '/../crawler/crawl-device.py';
            $crawlAbsolutePath = realpath($cwd . '/' . $crawlScript);
            // Check if the script exists
            if ($crawlAbsolutePath) {
                // Open a pipe to the Python script so we can capture output as it is printed from crawl
                $descriptorspec = array(
                    0 => array("pipe", "r"),  // stdin
                    1 => array("pipe", "w"),  // stdout
                    2 => array("pipe", "w")   // stderr
                );
                $process = proc_open("python3 $crawlAbsolutePath $username", $descriptorspec, $pipes);

                // Check if the script exists
                if ($crawlAbsolutePath) {
                    // Read from the pipe and display the output dynamically
                    while (($output = fgets($pipes[1])) !== false) {
                        echo $output;
                        flush(); // Flush the output buffer to make sure it's sent to the browser immediately
                    }
                    fclose($pipes[1]); // Close the pipe when done reading from it

                    // Close the process and get the return value
                    $return_value = proc_close($process);
                } else {
                    echo "Error: Failed to launch Python script.";
                }
            } else {
                echo "Error: Python script not found.";
            }   
            // Redirect to prevent resubmission
            header("Location: index.php");
            exit();  
    }
?>