<script>
    $(document).ready(function() {
        $('a[href="' + this.location.pathname + '"]').parent().addClass('active');
    });
</script>

    <div class="navbar navbar-inverse navbar-fixed-top">
      <div class="navbar-inner">
        <div class="container">
          <button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="brand" href="#" data-toggle="pill">Live Map</a>
          <div class="nav-collapse collapse">
            <ul class="nav">
							<li><a href="index.php" data-toggle="pill">Event Map</a></li>
							<li><a href="heatmap.php" data-toggle="pill">AC Heat Map</a></li>
							<li><a href="wifi.php" data-toggle="pill">WiFi Usage Map</a></li>
            </ul>
          </div><!--/.nav-collapse -->
        </div>
      </div>
    </div>