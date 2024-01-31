<!DOCTYPE html>
<html>

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

<body class="light-grey content" style="max-width:1600px">

    <!-- Sidebar/menu -->
    <nav class="sidebar collapse blue animate-left" style="z-index:3;width:300px;" id="mySidebar"><br>
        <div class="container">
            <a href="index.php" onclick="w3_close()" class="hide-large right jumbo padding hover-grey"
                title="close menu">
                <i class="fa fa-remove"></i>
            </a>
            <img src="resources\logo.png" alt="logo" style="width: 250px;">
            </a>
            <h4><b>Network Inventory</b></h4>
        </div>
        <div class="section bottombar"></div>
        <div class="bar-block">
            <a href="index.php" onclick="w3_close()" class="bar-item button padding grey text-black"><i
                    class="fa fa-solid fa-house-user"></i> HOME</a>
            <a href="network.html" onclick="w3_close()" class="bar-item button padding"><i
                    class="fa fa-solid fa-wifi"></i> NETWORK</a>
            <a href="settings.html" onclick="w3_close()" class="bar-item button padding"><i
                    class="fa fa-solid fa-gear"></i> SETTINGS</a>
            <a href="download.html" onclick="w3_close()" class="bar-item button padding"><i
                    class="fa fa-solid fa-download"></i> DOWNLOAD</a>
            <a href="download.html" onclick="w3_close()" class="bar-item button padding"><i
                    class="fa fa-solid fa-download"></i> RUN SCAN</a>
        </div>
    </nav>

    <!-- Overlay effect when opening sidebar on small screens -->
    <div class="overlay hide-large animate-opatab" onclick="w3_close()" style="cursor:pointer" title="close side menu"
        id="myOverlay"></div>

    <!-- PAGE CONTENT! -->
    <div class="main" style="margin-left:300px">

        <!-- Header -->
        <header id="MedCorp System Inventory">
            <span class="button hide-large xxlarge hover-text-grey" onclick="w3_open()"><i
                    class="fa fa-bars"></i></span>
            <div class="container">
                <h1><b>MedCorp System Inventory</b><button class="button margin-right right green large">SAVE <i
                            class="fa fa-solid fa-upload large"></i></button></h1>

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
                        <div class="inputbar">
                            <input type="text" placeholder="Search Filter..">
                        </div>
                        <font size="4" face="Courier New">
                            <table BORDER=1 width="100%">
                                <?php
                                    $CLIENT = "MedCorp";

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
                                    $q = "SELECT Inv_Item.name, Inv_Item.type, Inv_Item.version,
                                                Inv_Item.os, Inv_Item.os_version, Vender.poc,
                                                Vender.email, Inv_Item.auto_log_off_freq,
                                                Vender.baa, Vender.date
                                        FROM Inv_Item LEFT JOIN Vender
                                            ON Inv_Item.vender = Vender.email;";
                                        //   <!-- WHERE client = " $CLIENT";";  -->

                                    $st = $cn ->stmt_init ();
                                    $st ->prepare($q);

                                    // execute the statement and bind the result (to vars)
                                    $st ->execute ();
                                    $st ->bind_result($name, $type, $version, $os,
                                                    $os_version, $vpoc, $vemail,
                                                    $auto_log_off_freq, $baa, $date);

                                    // output result
                                    echo "<thead>";
                                        echo "<td>Name</td>";
                                        echo "<td>Type of Application/Device</td>";
                                        echo "<td>APPLICATION Version in Place</td>";
                                        echo "<td>Operating System & Version</td>";
                                        echo "<td>VENDOR POC</td>";
                                        echo "<td>POC E-mail</td>";
                                        echo "<td>AUTOMATIC LOG-OFF FREQUENCY</td>";
                                        echo "<td>BAA?</td>";
                                        echo "<td>DATE BAA SIGNED</td>";
                                    echo "</thead>";

                                    while ($st -> fetch()) {
                                        echo "<tr>";
                                            echo "<td contenteditable='true'>" . $name . "</td>";
                                            echo "<td contenteditable='true'>" . $type . "</td>";
                                            echo "<td contenteditable='true'>" . $version . "</td>";
                                            echo "<td contenteditable='true'>" . $os . " " . $os_version . "</td>";
                                            echo "<td contenteditable='true'>" . $vpoc . "</td>";
                                            echo "<td contenteditable='true'>" . $vemail . "</td>";
                                            echo "<td contenteditable='true'>" . $auto_log_off_freq . "</td>";
                                            echo "<td contenteditable='true'>" . $baa . "</td>";
                                            echo "<td contenteditable='true'>" . $date . "</td>";
                                        echo "</tr>";
                                    }

                                    // clean up
                                    $st ->close ();
                                    $cn ->close ();
                                ?>
                            </table>
                        </font>
                    </div>

                    <!-- Server Information -->
                    <div id="Server" class="tabcontent">
                        <div class="inputbar">
                            <input type="text" placeholder="Search Filter..">
                        </div>
                        <font size="4" face="Courier New">
                            <table BORDER=1 width="100%">
                                <?php
                                    $CLIENT = "MedCorp";

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
                                    $q = "SELECT s.name, s.ip_address, l.cloud_prem,
                                                l.details, l.protection
                                        FROM Inv_Item as i LEFT JOIN Server as s
                                            ON i.server = s.id
                                            LEFT JOIN Location as l ON l.id = s.location_id;";
                                        //   <!-- WHERE client = " $CLIENT";";  -->

                                    $st = $cn ->stmt_init ();
                                    $st ->prepare($q);

                                    // execute the statement and bind the result (to vars)
                                    $st ->execute ();
                                    $st ->bind_result($name, $addr, $cp, $details, $protection);

                                    // output result
                                    echo "<thead>";
                                        echo "<td>SERVER NAME</td>";
                                        echo "<td>SERVER IP ADDRESS</td>";
                                        echo "<td>Cloud or On Premise?</td>";
                                        echo "<td>Location</td>";
                                        echo "<td>How is the Location Protected?</td>";
                                    echo "</thead>";

                                    while ($st -> fetch()) {
                                        echo "<tr>";
                                            echo "<td contenteditable='true'>" . $name . "</td>";
                                            echo "<td contenteditable='true'>" . $addr . "</td>";
                                            echo "<td contenteditable='true'>" . $cp . "</td>";
                                            echo "<td contenteditable='true'>" . $details . "</td>";
                                            echo "<td contenteditable='true'>" . $protection . "</td>";
                                        echo "</tr>";
                                    }

                                    // clean up
                                    $st ->close ();
                                    $cn ->close ();
                                ?>
                            </table>
                        </font>
                    </div>

                    <!-- ePHI -->
                    <div id="ePHI" class="tabcontent">
                        <div class="inputbar">
                            <input type="text" placeholder="Search Filter..">
                        </div>
                        <font size="4" face="Courier New">
                            <table BORDER=1 width="100%">
                                <?php
                                    $CLIENT = "MedCorp";

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
                                    $q = "SELECT i.ephi, i.ephi_encrypted, i.ephi_encr_method, i.ephi_encr_tested, i.interfaces_with
                                          FROM Inv_Item as i;";
                                        //   <!-- WHERE client = " $CLIENT";";  -->

                                    $st = $cn ->stmt_init ();
                                    $st ->prepare($q);

                                    // execute the statement and bind the result (to vars)
                                    $st ->execute ();
                                    $st ->bind_result($ephi, $encr, $meth, $test, $inter);

                                    // output result
                                    echo "<thead>";
                                        echo "<td>ePHI YES/NO</td>";
                                        echo "<td>ENCRYPTED? YES/NO</td>";
                                        echo "<td>IF YES, ENCRYPTION METHOD</td>";
                                        echo "<td>IF YES, ENCRYPTION TESTED?</td>";
                                        echo "<td>APPLICATIONS INTERFACED WITH</td>";
                                    echo "</thead>";

                                    while ($st -> fetch()) {
                                        echo "<tr>";
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
                        </font>
                    </div>

                    <!-- Authentication Information -->
                    <div id="Authentication" class="tabcontent">
                        <div class="inputbar">
                            <input type="text" placeholder="Search Filter..">
                        </div>
                        <font size="4" face="Courier New">
                            <table BORDER=1 width="100%">
                                <?php
                                    $CLIENT = "MedCorp";

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
                                    $q = "SELECT i.user_auth_method, i.app_auth_method, i.psw_min_len, i.psw_change_freq
                                          FROM Inv_Item as i;";
                                        //   <!-- WHERE client = " $CLIENT";";  -->

                                    $st = $cn ->stmt_init ();
                                    $st ->prepare($q);

                                    // execute the statement and bind the result (to vars)
                                    $st ->execute ();
                                    $st ->bind_result($user, $app, $min, $freq);

                                    // output result
                                    echo "<thead>";
                                        echo "<td>USER AUTHENTICATION METHOD</td>";
                                        echo "<td>APPLICATION AUTHENTICATION METHOD</td>";
                                        echo "<td>Minimum Password Length (as applicable)</td>";
                                        echo "<td>PASSWORD CHANGE FREQUENCY (as applicable)</td>";
                                    echo "</thead>";

                                    while ($st -> fetch()) {
                                        echo "<tr>";
                                            echo "<td contenteditable='true'>" . $user . "</td>";
                                            echo "<td contenteditable='true'>" . $app . "</td>";
                                            echo "<td contenteditable='true'>" . $min . "</td>";
                                            echo "<td contenteditable='true'>" . $freq . "</td>";
                                        echo "</tr>";
                                    }

                                    // clean up
                                    $st ->close ();
                                    $cn ->close ();
                                ?>
                            </table>
                        </font>
                    </div>

                    <!-- Asset Information -->
                    <div id="Asset Information" class="tabcontent">
                        <div class="inputbar">
                            <input type="text" placeholder="Search Filter..">
                        </div>
                        <font size="4" face="Courier New">
                            <table BORDER=1 width="100%">
                                <?php
                                    $CLIENT = "MedCorp";

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
                                    $q = "SELECT i.dept, i.space, i.date_last_ordered, i.vender,
                                            i.purchase_price, i.warranty_expires, i.item_condition,
                                            i.quantity, i.assset_value, i.model_num, i.notes, i.link
                                          FROM Inv_Item as i;";
                                        //   <!-- WHERE client = " $CLIENT";";  -->

                                    $st = $cn ->stmt_init ();
                                    $st ->prepare($q);

                                    // execute the statement and bind the result (to vars)
                                    $st ->execute ();
                                    $st ->bind_result($dept, $space, $dlo, $vender, $price, $warr, $cond, $quant, $value, $model_num, $notes, $link);

                                    // output result
                                    echo "<thead>";
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
                        </font>
                    </div>

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

                </body>
                <div class="container">
                    <div class="button left xxlarge">
                        <i class="fa fa-solid fa-plus left"></i>
                    </div>
                    <div class="inputbar">
                        <input class="left" type="text" placeholder="Add Device..">
                    </div>
                </div>
            </div>

            <div class="container padding-large" style="margin-bottom:32px">
                <div class="row-padding" style="margin:0 -16px">
                </div>
            </div>

            <!-- Footer -->
            <footer class="container padding-64 dark-grey bottom">

                <div class="third">
                </div>

                <div class="third">
                </div>

                <div class="third">
                </div>

            </footer>

            <div class="black center padding-24 bottom">

                <!-- End page content -->
            </div>

            <script>
                // Script to open and close sidebar
                function w3_open() {
                    document.getElementById("mySidebar").style.display = "block";
                    document.getElementById("myOverlay").style.display = "block";
                }

                function w3_close() {
                    document.getElementById("mySidebar").style.display = "none";
                    document.getElementById("myOverlay").style.display = "none";
                }
            </script>

</body>

</html>