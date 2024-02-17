<?php

    function launchPythonScript($username) {
        // Check if pip dependencies are installed, if not, install them
        $dependencies = array(
            'nmap3',
            'ipaddress',
            'requests',
            'netifaces',
            'pysnmp',
            'scapy'
        );

        foreach ($dependencies as $dependency) {
            if (!isDependencyInstalled($dependency)) {
                echo "Installing $dependency..." . PHP_EOL;
                shell_exec("pip install $dependency");
            }
        }

        // Launch Python script with argument
        $pythonScript = realpath(__DIR__ . '/../crawler/crawl-device.py');
        $command = "python $pythonScript $username";
        echo "Launching Python script: $command" . PHP_EOL;
        shell_exec($command);
    }

    function isDependencyInstalled($dependency) {
        $output = shell_exec("pip show $dependency");
        return strpos($output, 'Name: ' . $dependency) !== false;
    }

    // Use UI-inputted username to replace "my_username"
    $username = "my_username";
    launchPythonScript($username);

?>
