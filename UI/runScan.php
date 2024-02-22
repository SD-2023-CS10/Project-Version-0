<?php

    // Parse config.ini
    $config = parse_ini_file("./config.ini");
    $username = $config["username"];
    echo $username;

    // Check if the username parameter is set
    if($username) {        
        // Download needed dependencies
        downlaodDependencies();

        // Execute the runScan function with the provided username
        $result = runScan($username);
        echo $result;
    } else {
        // If username parameter is not set, output an error message
        echo "Error: Username parameter is missing: " + $username;
    }

    // Downloads dependencies
    function downlaodDependencies() {
        // Navigate from /UI, up to parent dir then to crawler file
        $depScriptRelativePath = '/../crawler/dependency-checks.php';
        $cwd = getcwd(); // Get the current working directory
        $depScriptAbsolutePath = realpath($cwd . '/' . $depScriptRelativePath);
        if ($depScriptAbsolutePath) {
            // Open a pipe to the Python script so we can capture output as it is printed from crawl
            $descriptorspec = array(
                0 => array("pipe", "r"),  // stdin
                1 => array("pipe", "w"),  // stdout
                2 => array("pipe", "w")   // stderr
            );
            $process = proc_open("php $depScriptAbsolutePath", $descriptorspec, $pipes);

            // Check if the process was successfully created
            if (is_resource($process)) {
                // Read from the pipe and display the output dynamically
                while (!feof($pipes[1])) {
                    if (!feof($pipes[1])) {
                        $output = fgets($pipes[1]);
                        if ($output !== false) {
                            echo $output;
                            ob_start(); // use ob_start instead of ob_flush bc of warnings
                            flush();
                        }
                    }
                }
                fclose($pipes[1]);

                // Close the process and get the return value
                $return_value = proc_close($process);

                echo "Script finished with status: $return_value\n";
            } else {
                echo "Error: Failed to launch PHP script.\n";
            }
        } else {
            echo "Error: PHP script not found.\n";
        }  
    }
    

    // Launches crawler/crawl-device.py with username as argument from Project-Version-0 folder
    function runScan($username) {
        // Navigate from /UI, up to parent dir then to crawler file
        $crawlerScriptRelativePath = '/../crawler/printhi.py';
        // $crawlerScriptRelativePath = '/crawler/crawl-device.py';

        $cwd = getcwd(); // Get the current working directory
        $crawlerScriptAbsolutePath = realpath($cwd . '/' . $crawlerScriptRelativePath);
        
        // Check if the script exists
        if ($crawlerScriptAbsolutePath) {
            // Open a pipe to the Python script so we can capture output as it is printed from crawl
            $descriptorspec = array(
                0 => array("pipe", "r"),  // stdin
                1 => array("pipe", "w"),  // stdout
                2 => array("pipe", "w")   // stderr
            );
            $process = proc_open("python3 $crawlerScriptAbsolutePath $username", $descriptorspec, $pipes);

            // Check if the process was successfully created
            if (is_resource($process)) {
                // Read from the pipe and display the output dynamically
                while (!feof($pipes[1])) {
                    if (!feof($pipes[1])) {
                        $output = fgets($pipes[1]);
                        if ($output !== false) {
                            echo $output;
                            ob_start(); // use ob_start instead of ob_flush bc of warnings
                            flush();
                        }
                    }
                }
                fclose($pipes[1]);

                // Close the process and get the return value
                $return_value = proc_close($process);

                echo "Script finished with status: $return_value\n";
            } else {
                echo "Error: Failed to launch Python script.\n";
            }
        } else {
            echo "Error: Python script not found.\n";
        }  
    }
?>