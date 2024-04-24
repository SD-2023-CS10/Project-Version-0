<!--
 * File Name: settings.php
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
<head>
<title>Inventory</title>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="styling\homepage.css">
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Raleway">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<link href="styling\bootstrap.min.css" rel="stylesheet">

<style>
  body,h1,h2,h3,h4,h5,h6 {font-family: "Raleway", sans-serif}
</style>
</head>

<body class="light-grey content" style="max-width:1600px">

  <!-- Sidebar/menu -->
  <nav class="sidebar collapse blue animate-left" style="z-index:3;width:300px;" id="mySidebar"><br>
    <div class="container">
      <a href="settings.php" onclick="closeSB()" class="hide-large right jumbo padding hover-grey" title="close menu">
        <i class="fa fa-remove"></i>
      </a>
      <img src="resources\logo.png" alt="logo" style="width: 250px;">
      <h4><b>Network Inventory</b></h4>
    </div>
    <div class="section bottombar"></div>
    <div class="bar-block">
      <a href="index.php" onclick="closeSB()" class="bar-item button padding"><i class="fa fa-solid fa-folder"></i> HOME</a>
      <a href="settings.php" onclick="closeSB()" class="bar-item button padding grey text-black"><i
          class="fa fa-solid fa-gear"></i> SETTINGS</a>
        <form action="index.php" method="POST">
            <button href="index.php" name="DOWNLOAD" value="True" onclick="csv_launch()" class="bar-item button padding"><i class="fa fa-solid fa-download"></i> DOWNLOAD</button>
        </form>
        <a href="index.php" id="runScanButton" id="scanOutput" class="bar-item button padding"><i class="fa fa-solid fa-download"></i> RUN SCAN</a>

    </div>
  </nav>

  <!-- Overlay effect when opening sidebar on small screens -->
  <div class="overlay hide-large animate-opacity" onclick="closeSB()" style="cursor:pointer" title="close side menu"
    id="myOverlay"></div>

  <!-- PAGE CONTENT! -->
  <div class="main" style="margin-left:300px">

    <!-- Header -->
    <header id="MedCorp System Inventory">
      <span class="button hide-large xxlarge hover-text-grey" onclick="openSB()"><i class="fa fa-bars"></i></span>
      <div class="container padding-16">
        <h1><b>Med INC System Inventory</b></h1>
      </div>
    </header>

    <div class="container-xxl py-5">
    <div class="container">
        <div class="text-center mx-auto mb-5 wow fadeInUp" data-wow-delay="0.1s" style="max-width: 600px;">
            <h1 class="mb-3">Settings</h1>
        </div>
        
        <div class="row g-4">
            <div class="col-lg-3 col-sm-6 wow fadeInUp" data-wow-delay="0.1s">
                <a class="cat-item d-block bg-primary text-center rounded p-3" onclick="openTab(event, 'profile')">
                    <div class="rounded p-4">
                        <h2>Profile</h2>
                    </div>
                </a>
            </div>

            <div class="col-lg-3 col-sm-6 wow fadeInUp" data-wow-delay="0.3s">
                <a class="cat-item d-block bg-primary text-center rounded p-3" onclick="openTab(event, 'network')">
                    <div class="rounded p-4">
                        <h2>Network</h2>
                    </div>
                </a>
            </div>

            <div class="col-lg-3 col-sm-6 wow fadeInUp" data-wow-delay="0.5s">
                <a class="cat-item d-block bg-primary text-center rounded p-3" onclick="openTab(event, 'accessibility')">
                    <div class="rounded p-4">
                        <h3>Accessibility</h3>
                    </div>
                </a>
            </div>

            <div class="col-lg-3 col-sm-6 wow fadeInUp" data-wow-delay="0.7s">
                <a class="cat-item d-block bg-primary text-center rounded p-3" onclick="openTab(event, 'contact')">
                    <div class="rounded p-4">
                        <h2>Contact/Help</h2>
                    </div>
                </a>
            </div>
        </div>

        <div id="profile" class="tab-content row-padding padding-16" style="display: none;">
            <!-- Profile Content -->
            <h2>Profile Settings</h2>
            <font size="4" face="sans-serif">
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
                        $q = "SELECT `User`.`client`,
                        `User`.`user_name`
                        FROM `gu_devices`.`User` 
                        WHERE `User`.`client` = '$CLIENT'";

                        $st = $cn ->stmt_init ();
                        $st ->prepare($q);

                        // execute the statement and bind the result (to vars)
                        $st ->execute ();
                        $st ->bind_result($client, $username);

                        // output result
                        echo "<thead>";
                            echo "<td>Username</td>";
                            echo "<td>Client</td>";
                        echo "</thead>";

                        while ($st -> fetch()) {
                        echo "<tr>";
                            echo "<td id='User.user_name'>" . $username . "</td>";
                            echo "<td id='User.client'>" . $client . "</td>";
                        echo "</tr>";
                        }
                        // clean up
                        $st ->close ();
                        $cn ->close ();
                    ?>
                </table>
            </font>
        </div>
        <div id="network" class="tab-content row-padding padding-16" style="display: none;">
            <!-- Network Content -->
            <h2>Network Settings</h2>
            <p>Out Of Scope</p>
        </div>
        <div id="accessibility" class="tab-content row-padding padding-16" style="display: none;">
            <!-- Accessibility Content -->
            <h2>Accessibility Settings</h2>
            <p>In Development</p>
        </div>
        <div id="contact" class="tab-content row-padding padding-16" style="display: none;">
            <!-- Contact Content -->
            <h2>Help Menu</h2>
            <p>Medcurity Phone Contact:  (509) - 867 - 3645</p>
            <p>Medcurity Email Contact:  support@medcurity.com </p>
            <p>About Application: <a href="/UI/INSTRUCTIONS.md">INSTRUCTIONS.md</a></p>
            <!-- <p>Help Manuals: <?php include 'README.md';?></p> -->
            <p>Help Manual: <a href="/UI/README.md">README.md</a></p>
        </div>
    </div>
</div>

<script>
    function openTab(evt, tabName) {
        var i, tabcontent, tablinks;
        tabcontent = document.getElementsByClassName("tab-content");
        for (i = 0; i < tabcontent.length; i++) {
            tabcontent[i].style.display = "none";
        }
        document.getElementById(tabName).style.display = "block";
    }

    function showMenu() {
        var tabcontent = document.getElementsByClassName("tab-content");
        for (var i = 0; i < tabcontent.length; i++) {
            tabcontent[i].style.display = "none";
        }
    }
      // Script to open and close sidebar
        function openSB() {
        document.getElementById("mySidebar").style.display = "block";
        document.getElementById("myOverlay").style.display = "block";
        }

        function closeSB() {
        document.getElementById("mySidebar").style.display = "none";
        document.getElementById("myOverlay").style.display = "none";
        }

        // Function to show list and hide tab content
        function showList() {
        document.querySelector('.list-topics-content').style.display = 'block';
        document.querySelectorAll('.tab-content').forEach(tab => {
            tab.style.display = 'none';
        });
    }

    </script>

</body>


</div>
</html>