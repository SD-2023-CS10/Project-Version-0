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
  body,h1,h2,h3,h4,h5,h6 {font-family: "Raleway", sans-serif}
</style>
</head>
<body class="light-grey content" style="max-width:1600px">

<!-- Sidebar/menu -->
<nav class="sidebar collapse blue animate-left" style="z-index:3;width:300px;" id="mySidebar"><br>
  <div class="container">
    <a href="index.html" onclick="w3_close()" class="hide-large right jumbo padding hover-grey" title="close menu">
      <i class="fa fa-remove"></i>
    </a>
        <img src="resources\logo.png" alt="logo" style="width: 250px;">
     </a>
    <h4><b>Network Inventory</b></h4>
  </div>
  <div class="section bottombar"></div>
  <div class="bar-block">
    <a href="index.html" onclick="w3_close()" class="bar-item button padding grey text-black"><i class="fa fa-solid fa-house-user"></i> HOME</a> 
    <a href="network.html" onclick="w3_close()" class="bar-item button padding"><i class="fa fa-solid fa-wifi"></i> NETWORK</a> 
    <a href="settings.html" onclick="w3_close()" class="bar-item button padding"><i class="fa fa-solid fa-gear"></i>  SETTINGS</a>
    <a href="download.html" onclick="w3_close()" class="bar-item button padding"><i class="fa fa-solid fa-download"></i>  DOWNLOAD</a>
  </div>
</nav>

<!-- Overlay effect when opening sidebar on small screens -->
<div class="overlay hide-large animate-opacity" onclick="w3_close()" style="cursor:pointer" title="close side menu" id="myOverlay"></div>

<!-- PAGE CONTENT! -->
<div class="main" style="margin-left:300px">

  <!-- Header -->
  <header id="MedCorp System Inventory">
    <span class="button hide-large xxlarge hover-text-grey" onclick="w3_open()"><i class="fa fa-bars"></i></span>
    <div class="container">
      <h1><b>MedCorp System Inventory</b><button class="button margin-right right green large">SAVE <i class="fa fa-solid fa-upload large"></i></button></h1>
      <div class="section bottombar padding-16">
        <a href="index.html"><button class="button black">System/Devices</button></a>
        <a href="server-tab.html"><button class="button white"></i>Server Info</button></a>
        <a href="ephi-tab.html"><button class="button white hide-small"></i>ePHI</button></a>
        <a href="authentication-tab.html"><button class="button white hide-small"></i>Authentication Methods</button></a>
        <a href="asset-info-tab.html"><button class="button white hide-small"></i>Asset Information</button></a>
      </div>
      <div class="inputbar">
        <input type="text" placeholder="Search Filter..">
      </div>
    </div>
  </header>

  <!-- First Grid-->
  <div class="row-padding">
    <div class="container left">
      <h4><b>System/Devices</b></h4>
      <font size="5" face = "Courier New">
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
      <hr>
    </div>
    <div class="container">
      <div class = "button left xxlarge">
        <i class="fa fa-solid fa-plus left"></i>
      </div>
      <div class="inputbar">
        <input class= "left" type="text" placeholder="Add Device..">
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