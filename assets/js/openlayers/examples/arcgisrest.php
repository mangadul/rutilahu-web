<!DOCTYPE html>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <link rel="stylesheet" href="../theme/default/style.css" type="text/css">
    <link rel="stylesheet" href="style.css" type="text/css">
    <script src="../lib/OpenLayers.js"></script>
    <script type="text/javascript">
        var map;
        var layer;

        function init(){
            var mapOptions = {
                maxExtent: new OpenLayers.Bounds(106.169254520354, -8.17525875009317, 108.848864050127, -5.49564922032017),
                maxResolution: 0.25,
                projection: "EPSG:4326"};
            map = new OpenLayers.Map( 'map', mapOptions );
            layer = new OpenLayers.Layer.ArcGIS93Rest( "Jawa barat",
                    "http://geoservice.bakosurtanal.go.id/ArcGIS/rest/services/JABAR/MapServer/export", 
                    {layers: "show:0,1,2,3,4,5,6,7,8,9,10"});
            map.addLayer(layer);

            map.addControl( new OpenLayers.Control.MousePosition() );
            
            map.setCenter(new OpenLayers.LonLat(106.067479313965,-7.78639720883322), 0);
        }
        
        function enableFilter() {
            layer.setLayerFilter(2, "STATE_NAME LIKE '%" + document.getElementById('filterValueField').value + "%'");
            layer.redraw();
        }
        function disableFilter() {
            layer.setLayerFilter(2, null);
            layer.redraw();
        }
        function updateButton() {
            document.getElementById('filterButton').value = "Show '" +
                document.getElementById('filterValueField').value + "' States";
        }
    </script>
  </head>
  <body onload="init()">
    <h1 id="title">ArcGIS Server 9.3 Rest API Example</h1>

    <div id="tags">
        ESRI, ArcGIS, REST, filter
    </div>
    <p id="shortdesc">
        Shows the basic use of openlayers using an ArcGIS Server 9.3 Rest API layer
    </p>

    <div id="map" class="smallmap"></div>

    <div id="docs">
        This is an example of how to add an ArcGIS Server 9.3 Rest API layer to the OpenLayers window.
    </div>
    <input id="filterValueField" type="textfield" value="A"/>
    <input id="filterButton" type="button" onclick="enableFilter();" value="Filter States"/>
    <input type="button" onclick="disableFilter();" value="Show All States"/>
    <br>
    (Filter is case sensitive.)
  </body>
</html>




