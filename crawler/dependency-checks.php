<?php

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
        echo "\nAll dependencies are already installed.\n";
    }
    
?>
