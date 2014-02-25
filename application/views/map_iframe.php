<!DOCTYPE html>
<html>
  <head>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
    <style type="text/css">
      html { height: 100% }
      body { height: 100%; margin: 0; padding: 0 }
      #map_canvas { height: 100% }
    </style>
    <script type="text/javascript"
      src="http://maps.googleapis.com/maps/api/js?key=AIzaSyAh1jOLTGGolug3Ao8u9CaLvYCOuPDYQaI&sensor=true">
    </script>
    <script type="text/javascript" src = "<?php echo ASSEST_URL?>js/map.js">
    </script>
  </head>
  <body onload="initialize()">
    <div id="map_canvas" ></div>
  </body>
</html>