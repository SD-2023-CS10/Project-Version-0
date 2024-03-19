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
            <a href="settings.html" onclick="closeSB()" class="bar-item button padding"><i class="fa fa-solid fa-gear"></i> SETTINGS</a>
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
                <button class="tablinks" onclick="opentab(event, 'System/Devices')">System/Devices</button>
                <button class="tablinks" onclick="opentab(event, 'Server')">Server</button>
                <button class="tablinks" onclick="opentab(event, 'ePHI')">ePHI</button>
                <button class="tablinks" onclick="opentab(event, 'Authentication')">Authentication</button>
                <button class="tablinks" onclick="opentab(event, 'Asset Information')">Asset
                    Information</button>
            </div>

            <!-- System Devices -->
            <div id="System/Devices" class="tabcontent">

                <!-- Table Filter Input -->
                <div class="inputbar">
                    <input type="text" placeholder="Search Filter..">
                </div>

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
                                        Inv_Item.os, Inv_Item.os_version, Vender.poc,
                                        Vender.email, Inv_Item.auto_log_off_freq,
                                        Vender.baa, Vender.date
                                FROM Inv_Item LEFT JOIN Vender
                                    ON Inv_Item.vender = Vender.email
                                WHERE Inv_Item.client =  '$CLIENT';";

                            $st = $cn ->stmt_init ();
                            $st ->prepare($q);

                            // execute the statement and bind the result (to vars)
                            $st ->execute ();
                            $st ->bind_result($item_id, $name, $type, $version, $os,
                                            $os_version, $vpoc, $vemail,
                                            $auto_log_off_freq, $baa, $date);

                            // output result
                            echo "<thead>";
                                echo "<td>Item ID</td>";
                                echo "<td>Name</td>";
                                echo "<td>Type of Application/Device</td>";
                                echo "<td>APPLICATION Version in Place</td>";
                                echo "<td>Operating System & Version</td>";
                                echo "<td>VENDOR POC</td>";
                                echo "<td>POC E-mail</td>";
                                echo "<td>AUTOMATIC LOG-OFF FREQUENCY</td>";
                                echo "<td>BAA?</td>";
                                echo "<td>DATE BAA SIGNED</td>";
                                echo "<td>Delete</td>";
                            echo "</thead>";

                            while ($st -> fetch()) {
                                echo "<tr>";
                                    echo "<td>" . $item_id . "</td>";
                                    echo "<td id='device' contenteditable='true'>" . $name . "</td>";
                                    echo "<td id='device' contenteditable='true'>" . $type . "</td>";
                                    echo "<td id='device' contenteditable='true'>" . $version . "</td>";
                                    echo "<td id='device' contenteditable='true'>" . $os . " " . $os_version . "</td>";
                                    echo "<td id='device' contenteditable='true'>" . $vpoc . "</td>";
                                    echo "<td id='device' contenteditable='true'>" . $vemail . "</td>";
                                    echo "<td id='device' contenteditable='true'>" . $auto_log_off_freq . "</td>";
                                    echo "<td id='device' contenteditable='true'>" . $baa . "</td>";
                                    echo "<td id='device' contenteditable='true'>" . $date . "</td>";
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
                            <input type="text" name="userDevice" id="userDevice" placeholder="Device Name" />
                            <input type="submit" value="Add" />
                        </form>
                    </div>

                </font>

                <script>
                    function updateDatabase(element) {
                        var table = document.getElementById("deviceTable");
                        var rowIndex = element.parentNode.rowIndex;
                        var cellIndex = element.cellIndex;
                        var columnName = table.rows[0].cells[cellIndex].innerText;
                        var item_id = table.rows[rowIndex].cells[0].innerText;
                        var cellValue = element.innerText;
                        
                        var tableId = element.parentNode.parentNode.parentNode.id; // Get the ID of the table
                        var tableName = (tableId === "deviceTable") ? "device" : "server"; // Determine table context
                        // Make an AJAX call to update the record in the database
                        var xhr = new XMLHttpRequest();
                        xhr.open("POST", "updateDevice.php", true);
                        xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                        xhr.send("item_id=" + item_id + "&rowIndex=" + rowIndex + "&columnName=" + columnName + "&cellValue=" + cellValue);
                    }

                    function deleteRow(rowId) {
                        var table = document.getElementById("deviceTable");
                        var item_id = table.rows[rowId].cells[0].innerText;

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
                            button.innerHTML = "Delete";
                            button.onclick = function () {
                                deleteRow(this.parentNode.parentNode.rowIndex);
                            };
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

                <!-- Table Filter Input -->
                <div class="inputbar">
                        <input type="text" placeholder="Search Filter..">
                </div>

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
                            $q = "SELECT i.item_id, s.name, s.ip_address, l.cloud_prem,
                                        l.details, l.protection
                                FROM Inv_Item as i LEFT JOIN Server as s
                                    ON i.server = s.id
                                    LEFT JOIN Location as l ON l.id = s.location_id
                                WHERE i.client = '$CLIENT';";

                            $st = $cn ->stmt_init ();
                            $st ->prepare($q);

                            // execute the statement and bind the result (to vars)
                            $st ->execute ();
                            $st ->bind_result($id, $name, $addr, $cp, $details, $protection);

                            // output result
                            echo "<thead>";
                                echo "<td>Item ID</td>";
                                echo "<td>SERVER NAME</td>";
                                echo "<td>SERVER IP ADDRESS</td>";
                                echo "<td>Cloud or On Premise?</td>";
                                echo "<td>Location</td>";
                                echo "<td>How is the Location Protected?</td>";
                            echo "</thead>";

                            while ($st -> fetch()) {
                                echo "<tr>";    
                                    echo "<td>" . $id . "</td>";
                                    echo "<td id='server' contenteditable='true'>" . $name . "</td>";
                                    echo "<td id='server' contenteditable='true'>" . $addr . "</td>";
                                    echo "<td id='server' contenteditable='true'>" . $cp . "</td>";
                                    echo "<td id='server' contenteditable='true'>" . $details . "</td>";
                                    echo "<td id='server' contenteditable='true'>" . $protection . "</td>";
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
                            <input type="text" name="userDevice" id="userDevice" placeholder="Device Name" />
                            <input type="submit" value="Add" />
                        </form>
                    </div>

                </font>
            </div>

            <!-- ePHI -->
            <div id="ePHI" class="tabcontent">

                <!-- Table Filter Input -->
                <div class="inputbar">
                    <input type="text" placeholder="Search Filter..">
                </div>

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
                            $q = "SELECT i.item_id, i.ephi, i.ephi_encrypted, i.ephi_encr_method, i.ephi_encr_tested, i.interfaces_with
                                    FROM Inv_Item as i
                                WHERE i.client = '$CLIENT';";

                            $st = $cn ->stmt_init ();
                            $st ->prepare($q);

                            // execute the statement and bind the result (to vars)
                            $st ->execute ();
                            $st ->bind_result($item_id, $ephi, $encr, $meth, $test, $inter);

                            // output result
                            echo "<thead>";
                                echo "<td>Item ID</td>";
                                echo "<td>ePHI YES/NO</td>";
                                echo "<td>ENCRYPTED? YES/NO</td>";
                                echo "<td>IF YES, ENCRYPTION METHOD</td>";
                                echo "<td>IF YES, ENCRYPTION TESTED?</td>";
                                echo "<td>APPLICATIONS INTERFACED WITH</td>";
                            echo "</thead>";

                            while ($st -> fetch()) {
                                echo "<tr>";
                                    echo "<td>" . $item_id . "</td>";
                                    echo "<td contenteditable='true'>" . $ephi . "</td>";
                                    echo "<td contenteditable='true'>" . $encr . "</td>";
                                    echo "<td contenteditable='true'>" . $meth . "</td>";
                                    echo "<td contenteditable='true'>" . $test . "</td>";
                                    echo "<td contenteditable='true'>" . $inter . "</td>";
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
                            <input type="text" name="userDevice" id="userDevice" placeholder="Device Name" />
                            <input type="submit" value="Add" />
                        </form>
                    </div>

                </font>
            </div>

            <!-- Authentication Information -->
            <div id="Authentication" class="tabcontent">

                <!-- Table Filter Input -->
                <div class="inputbar">
                    <input type="text" placeholder="Search Filter..">
                </div>

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
                            $q = "SELECT i.item_id, i.user_auth_method, i.app_auth_method, i.psw_min_len, i.psw_change_freq
                                    FROM Inv_Item as i
                                WHERE i.client = '$CLIENT';"; 

                            $st = $cn ->stmt_init ();
                            $st ->prepare($q);

                            // execute the statement and bind the result (to vars)
                            $st ->execute ();
                            $st ->bind_result($id, $user, $app, $min, $freq);

                            // output result
                            echo "<thead>";
                                echo "<td>Item ID</td>";
                                echo "<td>USER AUTHENTICATION METHOD</td>";
                                echo "<td>APPLICATION AUTHENTICATION METHOD</td>";
                                echo "<td>Minimum Password Length (as applicable)</td>";
                                echo "<td>PASSWORD CHANGE FREQUENCY (as applicable)</td>";
                            echo "</thead>";

                            while ($st -> fetch()) {
                                echo "<tr>";
                                    echo "<td>" . $id . "</td>";
                                    echo "<td id='authen' contenteditable='true'>" . $user . "</td>";
                                    echo "<td id='authen' contenteditable='true'>" . $app . "</td>";
                                    echo "<td id='authen' contenteditable='true'>" . $min . "</td>";
                                    echo "<td id='authen' contenteditable='true'>" . $freq . "</td>";
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
                            <input type="text" name="userDevice" id="userDevice" placeholder="Device Name" />
                            <input type="submit" value="Add" />
                        </form>
                    </div>

                </font>
            </div>

            <!-- Asset Information -->
            <div id="Asset Information" class="tabcontent">

                <!-- Table Filter Input -->
                <div class="inputbar">
                    <input type="text" placeholder="Search Filter..">
                </div>

                <!-- Device table with database connections -->
                <font size="4" face="Courier New">
                    <table BORDER=1 width="100%" id="assetTable">
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
                            $q = "SELECT i.item_id, i.dept, i.space, i.date_last_ordered, i.vender,
                                    i.purchase_price, i.warranty_expires, i.item_condition,
                                    i.quantity, i.assset_value, i.model_num, i.notes, i.link
                                    FROM Inv_Item as i
                                WHERE i.client = '$CLIENT';";

                            $st = $cn ->stmt_init ();
                            $st ->prepare($q);

                            // execute the statement and bind the result (to vars)
                            $st ->execute ();
                            $st ->bind_result($id, $dept, $space, $dlo, $vender, $price, $warr, $cond, $quant, $value, $model_num, $notes, $link);

                            // output result
                            echo "<thead>";
                                echo "<td>Item ID</td>";
                                echo "<td>DEPARTMENT</td>";
                                echo "<td>SPACE (LOCATION)</td>";
                                echo "<td>DATE OF LAST ORDER</td>";
                                echo "<td>VENDER</td>";
                                echo "<td>PURCHASE PRICE PER ITEM</td>";
                                echo "<td>WARRANTY EXPIRY DATE</td>";
                                echo "<td>CONDITION</td>";
                                echo "<td>QUANTITY</td>";
                                echo "<td>ASSET VALUE</td>";
                                echo "<td>TOTAL VALUE</td>";
                                echo "<td>MODEL</td>";
                                echo "<td>VENDOR NO.</td>";
                                echo "<td>REMARKS</td>";
                                echo "<td>PHOTOGRAPH/LINK</td>";
                            echo "</thead>";

                            while ($st -> fetch()) {
                                echo "<tr>";
                                    echo "<td>" . $id . "</td>";
                                    echo "<td contenteditable='true'>" . $dept . "</td>";
                                    echo "<td contenteditable='true'>" . $space . "</td>";
                                    echo "<td contenteditable='true'>" . $dlo . "</td>";
                                    echo "<td contenteditable='true'>" . $vender . "</td>";
                                    echo "<td contenteditable='true'>" . $price . "</td>";
                                    echo "<td contenteditable='true'>" . $warr . "</td>";
                                    echo "<td contenteditable='true'>" . $cond . "</td>";
                                    echo "<td contenteditable='true'>" . $quant . "</td>";
                                    echo "<td contenteditable='true'>" . $value . "</td>";
                                    echo "<td contenteditable='true'></td>";
                                    echo "<td contenteditable='true'>" . $model_num . "</td>";
                                    echo "<td contenteditable='true'></td>";
                                    echo "<td contenteditable='true'>" . $notes . "</td>";
                                    echo "<td contenteditable='true'>" . $link . "</td>";
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
                            <input type="text" name="userDevice" id="userDevice" placeholder="Device Name" />
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
                        // $username = $_SESSION["username"];
                        $username = "clemak";
                        $pythonScript = realpath(__DIR__) . '/../csv/csv-export.py';
                        $command = "python3 $pythonScript $username";
                        shell_exec($command);
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