<?php
// Test if we're using this in the app or not
//  index.php?menu=false
if (isset($_GET['menu'])) {
  if($_GET['menu'] != 'false') {
}
?>

<nav class="navbar navbar-inverse navbar-static-top" role="navigation">
  <div class="container">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href="#">Live Map</a>
    </div>

    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
      <ul class="nav nav-pills">
        <li><a href="index.php" class="navitem">Event Map</a></li>
        <li><a href="heatmap.php" class="navitem">AC Heat Map</a></li>
        <li><a href="wifi.php" class="navitem">WiFi Usage Map</a></li>
      </ul>
    </div>
  </div>
</nav>

<?php
}
?>