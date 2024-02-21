<?php

    // Launches the crawler from Project-Version-0 folder
    // Creates a pipe to receive terminal output dynamically
    function launch_crawler($username) {
        // Define the relative path to the crawler script
        $crawlerScriptRelativePath = 'crawler/printhi.py';

        // Get the absolute path to the crawler script
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

                echo "Script exited with status: $return_value\n";
            } else {
                echo "Error: Failed to launch Python script.\n";
            }
        } else {
            echo "Error: Python script not found.\n";
        }  
    }

    // Installs missing dependencies as necessary
    function check_dependencies() {
        // Check if pip dependencies are installed, if not, install them
        $dependencies = array(
            'python3-nmap',
            'ipaddress',
            'requests',
            'netifaces',
            'pysnmp',
            'scapy'
        );

        $missing_dependencies = array();

        foreach ($dependencies as $dependency) {
            if (!is_dependency_installed($dependency)) {
                $missing_dependencies[] = $dependency;
                echo "Installing $dependency..." . PHP_EOL;
                exec("pip3 install $dependency", $output, $return_var);
                if ($return_var !== 0) {
                    echo "Failed to install $dependency\n";
                    return false;
                }
                echo "$dependency installed successfully.\n";
            }
        }
    }

    // Checks dependency installation with python3 and pip3
    function is_dependency_installed($dependency) {
        if ($dependency === "python3-nmap") {
            // Handle 'python3-nmap' separately
            $command = "pip3 show python3-nmap";
            exec($command, $output, $return_var);
        } else {
            $command = "python3 -c \"import $dependency\"";
            exec($command, $output, $return_var);
        }
        return $return_var === 0;
    }    

    
    // Main Program Driver
    $missing_dependencies = check_dependencies();
    if (!empty($missing_dependencies)) {
        echo "Missing dependencies found:\n";
        foreach ($missing_dependencies as $dependency) {
            echo "- $dependency\n";
        }

        echo "\nAttempting to install missing dependencies...\n";
        if (install_dependencies($missing_dependencies)) {
            echo "\nAll dependencies installed successfully.\n";
        } else {
            echo "\nFailed to install some dependencies. Please check and try again.\n";
        }
    } else {
        echo "All dependencies are already installed.\n";
    }

    $username = "unique_username_from_UI";
    launch_crawler($username);
?>
