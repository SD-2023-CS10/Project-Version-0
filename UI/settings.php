<!--
 * File Name: settings.html
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
<style>
  body,h1,h2,h3,h4,h5,h6 {font-family: "Raleway", sans-serif}
</style>
</head>

<body class="light-grey content" style="max-width:1600px">

  <!-- Sidebar/menu -->
  <nav class="sidebar collapse blue animate-left" style="z-index:3;width:300px;" id="mySidebar"><br>
    <div class="container">
      <a href="settings.html" onclick="closeSB()" class="hide-large right jumbo padding hover-grey" title="close menu">
        <i class="fa fa-remove"></i>
      </a>
      <img src="resources\logo.png" alt="logo" style="width: 250px;">
      <h4><b>Network Inventory</b></h4>
    </div>
    <div class="section bottombar"></div>
    <div class="bar-block">
      <a href="index.php" onclick="closeSB()" class="bar-item button padding"><i class="fa fa-solid fa-folder"></i> HOME</a>
      <a href="settings.html" onclick="closeSB()" class="bar-item button padding grey text-black"><i
          class="fa fa-solid fa-gear"></i> SETTINGS</a>
      <a href="" onclick="closeSB()" class="bar-item button padding"><i class="fa fa-solid fa-download"></i> DOWNLOAD</a>
      <a href="" onclick="closeSB()" class="bar-item button padding"><i class="fa fa-solid fa-play"></i> RUN SCAN</a>
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
      <div class="container">
        <h1><b>MedCorp System Inventory</b></h1>
      </div>
    </header>

    <!-- First Grid-->
    <body>
      <div class="container margin-left">
        <div class="list-topics-content row-padding">
          <div class="single-list-topics-content">
            <h2><a href="#" onclick="showTab('profile')">Profile</a></h2>
          </div>
          <div class="single-list-topics-content">
            <h2><a href="#" onclick="showTab('accessibility')">Accessibility</a></h2>
          </div>
          <div class="single-list-topics-content">
            <h2><a href="#" onclick="showTab('network')">Network</a></h2>
          </div>
          <div class="single-list-topics-content">
            <h2><a href="#" onclick="showTab('contact')">Contact</a></h2>
          </div>
        </div>
      </div><!--/.container-->


      <div id="profile" class="tab-content row-padding" style="display: none;">
        <button onclick="showList()">Back</button>
        <h2>Profile Settings</h2>
        <p>In Development</p>
      </div>
      <div id="accessibility" class="tab-content row-padding" style="display: none;">
        <button onclick="showList()">Back</button>
        <h2>Accessibility Settings</h2>
        <p>In Development</p>
      </div>
      <div id="network" class="tab-content row-padding" style="display: none;">
        <button onclick="showList()">Back</button>
        <h2>Network Settings</h2>
        <p>In Development</p>
      </div>
      <div id="contact" class="tab-content row-padding" style="display: none;">
        <button onclick="showList()">Back</button>
        <h2>Help Menu</h2>
        <p>Medcurity Contact Representitive: (###)-###-####</p>
        <p>About Application: Link in Development</p>
        <p>Help Manuals: <?php include 'README.md';?></p>
        <p>Troubleshooting Guide: In Developemnt</p>
      </div>
    </body>

    <script>
      // Script to open and close sidebar
      function openSB() {
        document.getElementById("mySidebar").style.display = "block";
        document.getElementById("myOverlay").style.display = "block";
      }

      function closeSB() {
        document.getElementById("mySidebar").style.display = "none";
        document.getElementById("myOverlay").style.display = "none";
      }

      // Function to show tab content and hide list
      function showTab(tabId) {
        document.querySelector('.list-topics-content').style.display = 'none';
        document.getElementById(tabId).style.display = 'block';
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