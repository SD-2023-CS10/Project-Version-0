<?php
    // Launch the crawler from a shell command
    function launch_crawler($username) {
        // Launch Python script with argument
        $pythonScript = realpath(__DIR__ . '/../crawler/crawl-device.py');
        $command = "python3 $pythonScript $username";
        echo "Launching Python script: $command" . PHP_EOL;
        shell_exec($command);
    }

    // Installs dependencies if necessary
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

    // Use UI-inputted username to replace "my_username"
    // $username = "my_username";
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
    // launch_crawler($username);
?>
