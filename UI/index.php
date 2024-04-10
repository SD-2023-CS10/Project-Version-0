<!--
 * File Name: index.php
 * 
 * Description:
 * This is the main entry point of the application. It initializes the application
 * environment, loads the necessary resources, and routes the request to the appropriate
 * controller. This file also handles basic configuration settings and global declarations.
 * 
 * @package MedcurityNetworkScanner
 * @authors Jack Nealon (jnealon0805@gmail.com)
 * @license 
 * @version 1.0.0
 * @link 
 * @since 
 * 
 * Usage:
 * This file should be placed in the root directory of the application. It can be directly
 * accessed via the URL [Your Application's URL]. No modifications are necessary for basic
 * operation, but customization can be done by editing the configuration settings within.
 * 
 * Modifications:
 * [Date] - [Your Name] - Version [New Version Number] - [Description of Changes]
 * 
 * Notes:
 * - Additional notes or special instructions can be added here.
 * - Remember to update the version number and modification log with each change.
 * 
 * TODO:
 * - List any pending tasks or improvements that are planned for future updates.
 * 
 -->
<?php
    session_start();
?>

<!DOCTYPE html>
<html>

<!--
    Header Information for Inventory Page

    Title: Inventory
    Description: This is the head section for the Inventory management system page.
-->
<head>
    <title>Inventory</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="styling\homepage.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Raleway">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        body, 
        h1,
        h2, 
        h3, 
        h4, 
        h5,
        h6 {
            font-family: "Raleway", sans-serif
        }
    </style>
</head>

<!-- Background container --> 
<body class="light-grey content" style="max-width:1600px">

    <!-- Sidebar/menu -->
    <nav class="sidebar collapse blue animate-left" style="z-index:3;width:300px;" id="mySidebar"><br>
        
        <!-- Logo and sidebar title -->
        <div class="container">
            <a href="index.php" onclick="closeSB()" class="hide-large right xxlarge padding hover-grey"
                title="close menu">
                <i class="fa fa-remove"></i>
            </a>
            <img src="resources\logo.png" alt="logo" style="width: 250px;"></img>
            <h4><b>Network Inventory</b></h4>
        </div>
        <div class="section bottombar"></div>

        <!-- Sidebar redirection tabs -->
        <div class="bar-block">
            <a href="index.php" onclick="closeSB()" class="bar-item button padding grey black-text"><i class="fa fa-solid fa-folder"></i> HOME</a> 
            <!-- <a href="network.html" onclick="closeSB()" class="bar-item button padding"><i class="fa fa-solid fa-wifi"></i> NETWORK</a> -->
            <a href="settings.php" onclick="closeSB()" class="bar-item button padding"><i class="fa fa-solid fa-gear"></i> SETTINGS</a>
            <form action="index.php" method="POST">
                <button href="" name="DOWNLOAD" value="True" onclick="csv_launch()" class="bar-item button padding"><i class="fa fa-solid fa-download"></i> DOWNLOAD</button>
            </form>
            <a href="" id="runScanButton" id="scanOutput" class="bar-item button padding"><i class="fa fa-solid fa-download"></i> RUN SCAN</a>

        </div>

    </nav>

    <!-- Overlay effect when opening sidebar on small screens -->
    <div class="overlay hide-large animate-opatab" onclick="openSB()" style="cursor:pointer" title="open side menu"
        id="myOverlay">
    </div>

    <!-- PAGE CONTENT! -->
    <div class="main" style="margin-left:300px">

        <!-- Client Program Header -->
        <header id="Med INC System Inventory">
            <span class="button hide-large xxlarge hover-text-grey" onclick="openSB()"><i class="fa fa-bars"></i></span>
            <div class="container">
                <h1>
                    <b>Med INC System Inventory</b>
                </h1>
        <!-- Left open to give rest of page indentation off sidebar -->

        <!--
            Primary Content Container

            Title: Homepage
            Description: Holds the tables to display shown as Tabs in the GUI
        -->
        <body>
            <h2>Homepage</h2>
            <div class="tab">
                <button class="tablinks active" onclick="opentab(event, 'System/Devices')">System/Devices</button>
                <button class="tablinks" onclick="opentab(event, 'Server')">Server</button>
                <button class="tablinks" onclick="opentab(event, 'ePHI')">ePHI</button>
                <button class="tablinks" onclick="opentab(event, 'Authentication')">Authentication</button>
            </div>

            <!-- System Devices -->
            <div id="System/Devices" class="tabcontent" style="display: block;">

                <!-- Device table with database connections -->
                <font size="4" face="Courier New">
                    <table BORDER=1 width="100%" id="deviceTable">
                        <?php
                            $CLIENT = "Med INC";

                            // connection params
                            $config = parse_ini_file("./config.ini");
                            $server = $config["servername"];
                            $username = $config["username"];
                            $password = $config["password"];
                            $database = "gu_devices";

                            // connect to db
                            $cn = mysqli_connect($server , $username , $password , $database );

                            // check connection
                            if (!$cn) {
                                die("Connection failed: " . mysqli_connect_error ());
                            }

                            // set up the prepared statement
                            $q = "SELECT Inv_Item.item_id, Inv_Item.name, Inv_Item.type, Inv_Item.version,
                                        Inv_Item.os, Inv_Item.os_version, Inv_Item.auto_log_off_freq
                                FROM Inv_Item WHERE Inv_Item.client =  '$CLIENT'";

                            $st = $cn ->stmt_init ();
                            $st ->prepare($q);

                            // execute the statement and bind the result (to vars)
                            $st ->execute ();
                            $st ->bind_result($item_id, $name, $type, $version, $os,
                                            $os_version, $auto_log_off_freq);

                            // output result
                            echo "<thead>";
                                echo "<td>Item ID</td>";
                                echo "<td>Name</td>";
                                echo "<td>Type of Application/Device</td>";
                                echo "<td>Application Version in Place</td>";
                                echo "<td>Operating System </td>";
                                echo "<td>OS Version</td>";
                                echo "<td>Automatic Logoff Frequency(Min)</td>";
                                echo "<td>Delete</td>";
                            echo "</thead>";

                            while ($st -> fetch()) {
                                echo "<tr>";
                                    echo "<td>" . $item_id . "</td>";
                                    echo "<td id='Inv_Item.name.item_id' contenteditable='true'>" . $name . "</td>";
                                    echo "<td id='Inv_Item.type.item_id' contenteditable='true'>" . $type . "</td>";
                                    echo "<td id='Inv_Item.version.item_id' contenteditable='true'>" . $version . "</td>";
                                    echo "<td id='Inv_Item.os.item_id' contenteditable='true'>" . $os . "</td>";
                                    echo "<td id='Inv_Item.os_version.item_id' contenteditable='true'>" . $os_version . "</td>";
                                    echo "<td id='Inv_Item.auto_log_off_freq.item_id' contenteditable='true'>" . $auto_log_off_freq . "</td>";
                                echo "</tr>";
                            }
                            // clean up
                            $st ->close ();
                            $cn ->close ();
                        ?>
                    </table>

                    <!-- Add device input -->
                    <div class="inputbar">
                        <form action="addDevice.php" method="post">
                            <input type="submit" value="Add" class="margin-top: 15px" />
                            <input type="text" name="userDevice" id="userDevice" placeholder="Insert Device Name" />
                        </form>
                    </div>

                </font>

                <script>
                    function updateDatabase(element) {
                        var table = document.getElementById("deviceTable");
                        var rowIndex = element.parentNode.rowIndex;
                        var cellIndex = element.cellIndex;
                        var item_id = table.rows[rowIndex].cells[0].innerText;
                        var cellValue = element.innerText;
                        // Retrieve the cell's id
                        var cellId = element.id;

                        // Determine which PHP file to call based on the first element of cellId
                        var phpFile = "";
                        var cellIdParts = cellId.split(".");
                        console.log("cellIdParts:", cellIdParts);
                        if (cellIdParts[0] === "Server" || cellIdParts[0] === "Location") {
                            phpFile = "updateServer.php";
                        } else if (cellIdParts[0] === "Inv_Item") {
                            phpFile = "updateDevice.php";
                        }

                        if (phpFile !== "") {
                            var xhr = new XMLHttpRequest();
                            xhr.open("POST", phpFile, true);
                            xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                            xhr.send("&item_id=" + item_id + "&rowIndex=" + rowIndex + "&cellValue=" + cellValue + "&cellId=" + cellId);
                        } else {
                            console.error("Invalid cellId format.");
                        }
                    }

                    function deleteRow(rowId) {
                        var table = document.getElementById("deviceTable");
                        var item_id = table.rows[rowId].cells[0].innerText;

                        // Confirm user will delete entire item
                        var confirmed = window.confirm("Are you sure you want to delete this item? This action will delete the entire item and cannot be undone.");
                        if (!confirmed) {
                            return; // If user cancels, exit the function
                        }

                        // Run php script to delete item
                        var xhr = new XMLHttpRequest();
                        xhr.open("POST", "deleteDevice.php", true);
                        xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                        xhr.onreadystatechange = function () {
                            if (xhr.readyState == 4 && xhr.status == 200) {
                                table.deleteRow(rowId);
                            }
                        };
                        xhr.send("item_id=" + item_id);
                    }

                    function addDeleteButton() {
                        var table = document.getElementById("deviceTable");
                        var rows = table.getElementsByTagName("tr");

                        for (var i = 1; i < rows.length; i++) {
                            var cell = rows[i].insertCell(-1);
                            var button = document.createElement("button");
                            button.innerHTML = "&#128465;";
                            button.onclick = function () {
                                deleteRow(this.parentNode.parentNode.rowIndex);
                            };
                            button.style.backgroundColor = "red";
                            button.style.color = "white";
                            button.style.transition = "background-color 0.3s";
                            button.addEventListener("mouseenter", function() {
                                this.style.backgroundColor = "darkred";
                            });
                            button.addEventListener("mouseleave", function() {
                                this.style.backgroundColor = "red";
                            });
                            button.style.display = "block";
                            button.style.margin = "auto";
                            cell.style.textAlign = "center";
                            cell.appendChild(button);
                        }
                    }

                    window.onload = function () {
                        addDeleteButton();
                        var editableCells = document.querySelectorAll("td[contenteditable='true']");
                        editableCells.forEach(function (cell) {
                            cell.addEventListener("input", function () {
                                updateDatabase(cell);
                            });
                        });
                    };
                </script>
            </div>

            <!-- Server Information -->
            <div id="Server" class="tabcontent">

                <!-- Device table with database connections -->
                <font size="4" face="Courier New">
                    <table BORDER=1 width="100%" id="serverTable">
                        <?php
                            $CLIENT = "Med INC";

                            // connection params
                            $config = parse_ini_file("./config.ini");
                            $server = $config["servername"];
                            $username = $config["username"];
                            $password = $config["password"];
                            $database = "gu_devices";

                            // connect to db
                            $cn = mysqli_connect($server , $username , $password , $database );

                            // check connection
                            if (!$cn) {
                                die("Connection failed: " . mysqli_connect_error ());
                            }

                            // set up the prepared statement
                            $q = "SELECT i.item_id, i.name, i.type, s.name, s.ip_address, l.cloud_prem,
                                        l.details
                                FROM Inv_Item as i LEFT JOIN Server as s
                                    ON i.server = s.id
                                    LEFT JOIN Location as l ON l.id = s.location_id
                                WHERE i.client = '$CLIENT';";

                            $st = $cn ->stmt_init ();
                            $st ->prepare($q);

                            // execute the statement and bind the result (to vars)
                            $st ->execute ();
                            $st ->bind_result($id, $name, $type, $servName, $addr, $cp, $details);

                            // output result
                            echo "<thead>";
                                echo "<td>Item ID</td>";
                                echo "<td>Name</td>";
                                echo "<td>Type of Application/Device</td>";
                                echo "<td>Server Name</td>";
                                echo "<td>Server IP Address (Decimal)</td>";
                                echo "<td>Cloud or On-Premise?</td>";
                                echo "<td>Location</td>";
                            echo "</thead>";

                            while ($st -> fetch()) {
                                echo "<tr>";    
                                    echo "<td>" . $id . "</td>";
                                    echo "<td id='Inv_Item.name.item_id' contenteditable='true'>" . $name . "</td>";
                                    echo "<td id='Inv_Item.type.item_id' contenteditable='true'>" . $type . "</td>";
                                    echo "<td id='Server.name' contenteditable='true'>" . $servName . "</td>";
                                    echo "<td id='Server.ip_address' contenteditable='true'>" . $addr . "</td>";
                                    echo "<td id='Location.cloud_prem' contenteditable='true'>" . $cp . "</td>";
                                    echo "<td id='Location.details' contenteditable='true'>" . $details . "</td>";
                                echo "</tr>";
                            }

                            // clean up
                            $st ->close ();
                            $cn ->close ();
                        ?>
                    </table>

                    <!-- Add device input -->
                    <div class="inputbar">
                        <form action="addDevice.php" method="post">
                            <input type="text" name="userDevice" id="userDevice" placeholder="Insert New Device" />
                            <input type="submit" value="Add" class="padding-8" />
                        </form>
                    </div>

                </font>
            </div>

            <!-- ePHI -->
            <div id="ePHI" class="tabcontent">

                <!-- Device table with database connections -->
                <font size="4" face="Courier New">
                    <table BORDER=1 width="100%" id="ephiTable">
                        <?php
                            $CLIENT = "Med INC";

                            // connection params
                            $config = parse_ini_file("./config.ini");
                            $server = $config["servername"];
                            $username = $config["username"];
                            $password = $config["password"];
                            $database = "gu_devices";

                            // connect to db
                            $cn = mysqli_connect($server , $username , $password , $database );

                            // check connection
                            if (!$cn) {
                                die("Connection failed: " . mysqli_connect_error ());
                            }

                            // set up the prepared statement
                            $q = "SELECT i.item_id, i.name, i.type, i.ephi, i.ephi_encrypted, i.ephi_encr_method, i.ephi_encr_tested, i.interfaces_with
                                    FROM Inv_Item as i
                                WHERE i.client = '$CLIENT';";

                            $st = $cn ->stmt_init ();
                            $st ->prepare($q);

                            // execute the statement and bind the result (to vars)
                            $st ->execute ();
                            $st ->bind_result($item_id, $name, $type, $ephi, $encr, $meth, $test, $inter);

                            // output result
                            echo "<thead>";
                                echo "<td>Item ID</td>";
                                echo "<td>Name</td>";
                                echo "<td>Type of Application/Device</td>";
                                echo "<td>EPHI? Yes/No</td>";
                                echo "<td>Encrypted? Yes/No</td>";
                                echo "<td>If Yes, Encryption Method</td>";
                                echo "<td>If Yes, Encryption Tested?</td>";
                                echo "<td>Applications Interfaced With</td>";
                            echo "</thead>";

                            while ($st -> fetch()) {
                                echo "<tr>";
                                    echo "<td>" . $item_id . "</td>";
                                    echo "<td id='Inv_Item.name.item_id' contenteditable='true'>" . $name . "</td>";
                                    echo "<td id='Inv_Item.type.item_id' contenteditable='true'>" . $type . "</td>";
                                    echo "<td id='Inv_Item.ephi'contenteditable='true'>" . $ephi . "</td>";
                                    echo "<td id='Inv_Item.ephi_encrypted'contenteditable='true'>" . $encr . "</td>";
                                    echo "<td id='Inv_Item.ephi_encr_method'contenteditable='true'>" . $meth . "</td>";
                                    echo "<td id='Inv_Item.ephi_encr_tested'contenteditable='true'>" . $test . "</td>";
                                    echo "<td id='Inv_Item.interfaces_with'contenteditable='true'>" . $inter . "</td>";
                                echo "</tr>";
                            }

                            // clean up
                            $st ->close ();
                            $cn ->close ();
                        ?>
                    </table>

                    <!-- Add device input -->
                    <div class="inputbar">
                        <form action="addDevice.php" method="post">
                            <input type="text" name="userDevice" id="userDevice" placeholder="Insert Device Name" />
                            <input type="submit" value="Add" />
                        </form>
                    </div>

                </font>
            </div>

            <!-- Authentication Information -->
            <div id="Authentication" class="tabcontent">

                <!-- Device table with database connections -->
                <font size="4" face="Courier New">
                    <table BORDER=1 width="100%" id="authenticationTable">
                        <?php
                            $CLIENT = "Med INC";

                            // connection params
                            $config = parse_ini_file("./config.ini");
                            $server = $config["servername"];
                            $username = $config["username"];
                            $password = $config["password"];
                            $database = "gu_devices";

                            // connect to db
                            $cn = mysqli_connect($server , $username , $password , $database );

                            // check connection
                            if (!$cn) {
                                die("Connection failed: " . mysqli_connect_error ());
                            }

                            // set up the prepared statement
                            $q = "SELECT i.item_id, i.name, i.type, i.user_auth_method, i.app_auth_method, i.psw_min_len, i.psw_change_freq
                                    FROM Inv_Item as i
                                WHERE i.client = '$CLIENT';"; 

                            $st = $cn ->stmt_init ();
                            $st ->prepare($q);

                            // execute the statement and bind the result (to vars)
                            $st ->execute ();
                            $st ->bind_result($id, $name, $type, $user, $app, $min, $freq);

                            // output result
                            echo "<thead>";
                                echo "<td>Item ID</td>";
                                echo "<td>Name</td>";
                                echo "<td>Type of Application/Device</td>";
                                echo "<td>User Authention Method</td>";
                                echo "<td>Application Authentication Method</td>";
                                echo "<td>Minimum Password Length</td>";
                                echo "<td>Password Change Frequency (days)</td>";
                            echo "</thead>";

                            while ($st -> fetch()) {
                                echo "<tr>";
                                    echo "<td>" . $id . "</td>";
                                    echo "<td id='Inv_Item.name.item_id' contenteditable='true'>" . $name . "</td>";
                                    echo "<td id='Inv_Item.type.item_id' contenteditable='true'>" . $type . "</td>";
                                    echo "<td id='Inv_Item.user_auth_method.' contenteditable='true'>" . $user . "</td>";
                                    echo "<td id='Inv_Item.app_auth_method' contenteditable='true'>" . $app . "</td>";
                                    echo "<td id='Inv_Item.psw_min_len' contenteditable='true'>" . $min . "</td>";
                                    echo "<td id='Inv_Item.psw_change_freq' contenteditable='true'>" . $freq . "</td>";
                                echo "</tr>";
                            }

                            // clean up
                            $st ->close ();
                            $cn ->close ();
                        ?>
                    </table>

                    <!-- Add device input -->
                    <div class="inputbar">
                        <form action="addDevice.php" method="post">
                            <input type="text" name="userDevice" id="userDevice" placeholder="Insert Device Name" />
                            <input type="submit" value="Add" />
                        </form>
                    </div>

                </font>
            </div>

        </body>
        <!-- End page content -->

        <script>
            function opentab(evt, tabName) {
                var i, tabcontent, tablinks;
                tabcontent = document.getElementsByClassName("tabcontent");
                for (i = 0; i < tabcontent.length; i++) {
                    tabcontent[i].style.display = "none";
                }
                tablinks = document.getElementsByClassName("tablinks");
                for (i = 0; i < tablinks.length; i++) {
                    tablinks[i].className = tablinks[i].className.replace(" active", "");
                }
                document.getElementById(tabName).style.display = "block";
                evt.currentTarget.className += " active";
            }
        </script>

        <script>
            // Animate the bar moving accross the screen
            var i = 0;
            function move() {
                if (i == 0) {
                    i = 1;
                    var elem = document.getElementById("visBar");
                    var width = 10;
                    var id = setInterval(frame, 10);
                    function frame() {
                        if (width >= 100) {
                            clearInterval(id);
                            i = 0;
                        } else {
                            width++;
                            elem.style.width = width + "%";
                            elem.innerHTML = width  + "%";
                        }
                    }
                }
            }
        </script>

        
        <script>
            function csv_launch() {
                closeSB();
                location.reload();
                <?php
                    if (isset($_POST["DOWNLOAD"]) && $_POST["DOWNLOAD"] == "True")
                    {
                        // $user = $_SESSION["username"];
                        $user = "testadmin";

                        // connection params
                        $config = parse_ini_file("./config.ini");
                        $server = $config["servername"];
                        $username = $config["username"];
                        $password = $config["password"];
                        $database = "gu_devices";

                        // connect to db
                        $cn = mysqli_connect($server , $username , $password , $database );

                        // check connection
                        if (!$cn) {
                            die("Connection failed: " . mysqli_connect_error ());
                        }

                        // set up the prepared statement
                        $q = "SELECT client FROM User WHERE user_name = '$user';";

                        $st = $cn ->stmt_init ();
                        $st ->prepare($q);

                        // execute the statement and bind the result (to vars)
                        $st ->execute ();
                        $st ->bind_result($c);

                        $st -> fetch();
                        // if ($c == "Medcurity") {
                        if ($c == "admin") {
                            $py = '/../csv/admin-export.py';
                        }
                        else {
                            $py = '/../csv/csv-export.py';
                        }
                
                        $pythonScript = realpath(__DIR__) . $py;
                        $command = "python3 $pythonScript $user";
                        shell_exec($command);
                        $_POST["DOWNLOAD"] == "False";
                    }
                ?>
            }
        </script>

        <script>
            // Script to open and closeSB sidebar
            function openSB() {
                document.getElementById("mySidebar").style.display = "block";
                document.getElementById("myOverlay").style.display = "block";
            }

            function closeSB() {
                document.getElementById("mySidebar").style.display = "none";
                document.getElementById("myOverlay").style.display = "none";
            }
        </script>

        <!-- Run Scan Button launches crawler and dependency scripts -->
        <script>
            document.getElementById("runScanButton").addEventListener("click", function() {
                console.log("Launching scan...");
                var xhr = new XMLHttpRequest();
                xhr.open("POST", "runScan.php", true);
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                xhr.onreadystatechange = function() {
                    if (xhr.readyState == 4 && xhr.status == 200) {
                        // Capture output
                        document.getElementById("scanOutput").innerHTML = xhr.responseText;
                        console.log(xhr.responseText);
                    }
                };
                // Send the POST data
                xhr.send();
            });
        </script>
    </div>
</body>

</html>