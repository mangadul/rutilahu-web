<html>
    <head>
        <title>Peta</title>
	<link rel="stylesheet" type="text/css" href="<?=base_url()?>assets/js/ext-3.4.1/resources/css/ext-all.css" />
	<!--
		<link rel="stylesheet" type="text/css" href="<?=base_url()?>assets/js/ext-3.4.1/examples/shared/examples.css" /> 
	-->
	<script type="text/javascript" src="<?=base_url()?>assets/js/ext-3.4.1/adapter/ext/ext-base.js"></script>
	<script type="text/javascript" src="<?=base_url()?>assets/js/ext-3.4.1/ext-all.js"></script>
    <style type="text/css">
      html { height: 100% }
      body { height: 100%; margin: 0; padding: 0 }
      #mappanel { 
		position: absolute;
		z-index:1;
		left: 0;
		border: 1px solid black;
		height: 100%;
		width:100%;
		bottom: auto;
		height: expression(document.documentElement.clientHeight - 150 +"px");
	  }
	.image {
	position: relative;
	height: auto;
	min-height: 100% !important;
	}	  
	#container {
		height:40px;
		z-index: 0;
		position: absolute;
		right: 0px;
		top: 0;
		left: 0;		
		border:1px solid #c3daf9;
		position: absolute;
	}		
    </style>		
		<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false&v=3.2"></script>		
	<!-- 
	<script type="text/javascript" src="http://maps.google.com/maps/api/js"></script>
	<script type="text/javascript" src="<?=base_url()?>assets/js/bootstrap.min.js"></script>  	
	<script type="text/javascript" src="http://extjs.cachefly.net/ext-3.4.0/adapter/ext/ext-base.js"></script>
	<script type="text/javascript" src="http://extjs.cachefly.net/ext-3.4.0/ext-all.js"></script>
	<link rel="stylesheet" type="text/css" href="http://extjs.cachefly.net/ext-3.4.0/resources/css/ext-all.css" />
	<link rel="stylesheet" type="text/css" href="http://extjs.cachefly.net/ext-3.4.0/examples/shared/examples.css" />
	<script src="//code.jquery.com/jquery-1.11.3.min.js"></script>
	-->
		
        <script src="<?=base_url()?>assets/js/openlayers/lib/OpenLayers.js"></script>
        <script type="text/javascript" src="<?=base_url()?>assets/js/geoext/lib/GeoExt.js"></script>
	<script type="text/javascript" src="<?=base_url()?>assets/js/jquery.min.js"></script>

        <script type="text/javascript">
		var mapPanel, peta, controls = [];
/*
            var vectors1 = new OpenLayers.Layer.Vector("Vector Layer 1", {
                renderers: renderer,
                styleMap: new OpenLayers.StyleMap({
                    "default": new OpenLayers.Style(OpenLayers.Util.applyDefaults({
                        externalGraphic: "../img/marker-green.png",
                        graphicOpacity: 1,
                        rotation: -45,
                        pointRadius: 10
                    }, OpenLayers.Feature.Vector.style["default"])),
                    "select": new OpenLayers.Style({
                        externalGraphic: "../img/marker-blue.png"
                    })
                })
            });
            var vectors2 = new OpenLayers.Layer.Vector("Vector Layer 2", {
                renderers: renderer,
                styleMap: new OpenLayers.StyleMap({
                    "default": new OpenLayers.Style(OpenLayers.Util.applyDefaults({
                        fillColor: "red",
                        strokeColor: "gray",
                        graphicName: "square",
                        rotation: 45,
                        pointRadius: 15
                    }, OpenLayers.Feature.Vector.style["default"])),
                    "select": new OpenLayers.Style(OpenLayers.Util.applyDefaults({
                        graphicName: "square",
                        rotation: 45,
                        pointRadius: 15
                    }, OpenLayers.Feature.Vector.style["select"]))
                })
            });
			
         selectControl = new OpenLayers.Control.SelectFeature(
                [vectors1, vectors2],
                {
                    clickout: true, toggle: false,
                    multiple: false, hover: false,
                    toggleKey: "ctrlKey", // ctrl key removes from selection
                    multipleKey: "shiftKey" // shift key adds to selection
                }
            );		

        map.addControl(selectControl);
            selectControl.activate();		
*/
        Ext.onReady(function() {
            Ext.state.Manager.setProvider(new Ext.state.CookieProvider());
            var map = new OpenLayers.Map();
            var layer = new OpenLayers.Layer.Google(
                    'Google Streets',
                    {numZoomLevels: 20},
                    {visibility: true}
            );

			var osm = new OpenLayers.Layer.OSM("OpenStreet Map");
			
			var google_hybrid = new OpenLayers.Layer.Google(
				"Google Hybrid",
				{type: google.maps.MapTypeId.HYBRID}
			);
			 
			var google_physical = new OpenLayers.Layer.Google(
				"Google Physical",
				{type: google.maps.MapTypeId.PHYSICAL}
			);
			 
			var google_satellite = new OpenLayers.Layer.Google(
				"Google Satellite",
				{type: google.maps.MapTypeId.SATELLITE}
			);
			 
			var google_streets = new OpenLayers.Layer.Google(
				"Google Streets",
				{type: google.maps.MapTypeId.STREETS}
			);
			 
			var google_terrain = new OpenLayers.Layer.Google(
				"Google Terrain",
				{type: google.maps.MapTypeId.TERRAIN}
			);

		   var drawCircleInMeter = function(map, radius) {
			   console.log(map.getView());
				var view = map.getView();
				var projection = view.getProjection();
				var resolutionAtEquator = view.getResolution();
				var center = new OpenLayers.LonLat(1.0898176,104.4803663); //map.getView().getCenter();
				var pointResolution = projection.getPointResolution(resolutionAtEquator, center);
				var resolutionFactor = resolutionAtEquator/pointResolution;
				var radius = (radius / ol.proj.METERS_PER_UNIT.m) * resolutionFactor;
				var circle = new ol.geom.Circle(center, radius);
				var circleFeature = new ol.Feature(circle);
				// Source and vector layer
				var vectorSource = new ol.source.Vector({
					projection: 'EPSG:4326'
				});
				vectorSource.addFeature(circleFeature);
				var vectorLayer = new ol.layer.Vector({
					source: vectorSource
				});
				map.addLayer(vectorLayer);
			}
			
			drawCircleInMeter(map, 5000);
			drawCircleInMeter(map, 10000);
			drawCircleInMeter(map, 20000);
			drawCircleInMeter(map, 50000);
			
			var titikListener = {
				featureclick: function(e) {
					log(e.object.name + " says: " + e.feature.id + " clicked.");
					Ext.MessageBox.alert('Status', 'Changes saved successfully. '+e.feature.id);					
					return false;
				},
				nofeatureclick: function(e) {
					log(e.object.name + " says: No feature clicked.");
				}
			};
			
			var pointLayer = new OpenLayers.Layer.Vector("Titik Lokasi Penerima Bantuan", {
					projection: "EPSG:4326",
					eventListeners: titikListener
				}
			);
			
			$.getJSON('<?=base_url()?>index.php/rutilahu/Main/titik_peta', function(data) {
			  $.each(data.markers, function(i, marker) {
				var pointFeatures = [];
				var px = marker.longitude;
				var py = marker.latitude;
				// Create a lonlat instance and transform it to the map projection.
				var lonlat = new OpenLayers.LonLat(px, py);
				lonlat.transform(new OpenLayers.Projection("EPSG:4326"), new OpenLayers.Projection("EPSG:900913"));
				var pointGeometry = new OpenLayers.Geometry.Point(lonlat.lon, lonlat.lat);
				var pointFeature = new OpenLayers.Feature.Vector(pointGeometry, null, {
					pointRadius: 16,
					fillOpacity: 0.7,
					externalGraphic: '<?=base_url()?>assets/images/home5.png',
				});
				pointFeatures.push(pointFeature);
				pointLayer.addFeatures(pointFeatures);
			  });
			});
			
			var layerList = [
			{
				nodeType: "gx_overlaylayercontainer",
				text: "Overlay Layers",
				expanded: true
			}];
			
			var tree = new Ext.tree.TreePanel({
				title: "Tree Layer",
				loader: new Ext.tree.TreeLoader({
					applyLoader: false
				}),
				root: {
					children: layerList
				},
				rootVisible: false
			});
			
			//map.addLayer(tree);
			
			map.addLayer(pointLayer);
			map.addLayers([google_hybrid, google_physical, google_satellite, google_streets, google_terrain, osm]);			
            
			//map.addLayers([layer]);
            
			map.addControls(controls);
			
            mapPanel = new GeoExt.MapPanel({
                title: "Peta Kepulauan Riau",
                renderTo: "mappanel", //mappanel
                stateId: "mappanel",
                height: "100%",
                width: "100%",
                map: map,
                center: new OpenLayers.LonLat(1.0898176,104.4803663),
                zoom: 7,
                getState: function() {
                    var state = GeoExt.MapPanel.prototype.getState.apply(this);
                    state.width = this.getSize().width;
                    state.height = this.getSize().height;
                    return state;
                },
                applyState: function(state) {
                    GeoExt.MapPanel.prototype.applyState.apply(this, arguments);
                    this.width = state.width;
                    this.height = state.height;
                }
            });						
        });

        controls.push(
                new OpenLayers.Control.Navigation(),
                new OpenLayers.Control.Attribution(),
                new OpenLayers.Control.PanPanel(),
                new OpenLayers.Control.ZoomPanel(),
				new OpenLayers.Control.LayerSwitcher(),
				new OpenLayers.Control.OverviewMap(),
				new OpenLayers.Control.MousePosition(),
				new OpenLayers.Control.ScaleLine(),
				new OpenLayers.Control.KeyboardDefaults()
            );

        // functions for resizing the map panel
        function mapSizeUp() {
            var size = mapPanel.getSize();
            size.width += 40;
            size.height += 40;
            mapPanel.setSize(size);
        }
        function mapSizeDown() {
            var size = mapPanel.getSize();
            size.width -= 40;
            size.height -= 40;
            mapPanel.setSize(size);
        }		
		
		function log(msg) {
			if (!log.timer) {
				result.innerHTML = "";
				log.timer = window.setTimeout(function() {delete log.timer;}, 100);
			}
			result.innerHTML += msg + "<br>";
		}					
		</script>
    </head>
<body>
<div id="mappanel"></div>
<div id="result"></div>
</body>		
</html>
<!-- 
https://thatgeospatialblog.wordpress.com/2014/05/30/workshop-2-adding-base-layers-google-maps-yahoo-maps-bing-maps/
-->