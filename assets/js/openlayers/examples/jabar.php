<!DOCTYPE html>
<html>
  <head>
    <title>OpenLayers ArcGIS Cache Example (MapServer Access)</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <link rel="stylesheet" href="../theme/default/style.css" type="text/css">
    <link rel="stylesheet" href="style.css" type="text/css">
    <script src="http://maps.google.com/maps/api/js?v=3&amp;sensor=false"></script>
    <script src="../lib/OpenLayers.js"></script>
    <script src="../lib/OpenLayers/Layer/ArcGISCache.js" type="text/javascript"></script>
    <script type="text/javascript">
        var map, 
            cacheLayer,
            testLayer,
            //This layer requires meta data about the ArcGIS service.  Typically you should use a 
            //JSONP call to get this dynamically.  For this example, we are just going to hard-code
            //an example that we got from here (yes, it's very big):
            //    http://services.arcgisonline.com/ArcGIS/rest/services/World_Street_Map/MapServer?f=json&pretty=true
            layerInfo = {
  "serviceDescription" : "Peta Jawa Barat", 
  "mapName" : "Layers", 
  "description" : "", 
  "copyrightText" : "", 
  "layers" : [
    {
      "id" : 0, 
      "name" : "Anotasi", 
      "parentLayerId" : -1, 
      "defaultVisibility" : true, 
      "subLayerIds" : [1, 3, 5, 7, 9, 11, 13, 15, 17, 19, 21, 23]
    }, 
    {
      "id" : 1, 
      "name" : "Gunung_Anno", 
      "parentLayerId" : 0, 
      "defaultVisibility" : true, 
      "subLayerIds" : [2]
    }, 
    {
      "id" : 2, 
      "name" : "Default", 
      "parentLayerId" : 1, 
      "defaultVisibility" : true, 
      "subLayerIds" : null
    }, 
    {
      "id" : 3, 
      "name" : "IbuKotaKab_Anno", 
      "parentLayerId" : 0, 
      "defaultVisibility" : true, 
      "subLayerIds" : [4]
    }, 
    {
      "id" : 4, 
      "name" : "Default", 
      "parentLayerId" : 3, 
      "defaultVisibility" : true, 
      "subLayerIds" : null
    }, 
    {
      "id" : 5, 
      "name" : "IbuKotaKec_Anno", 
      "parentLayerId" : 0, 
      "defaultVisibility" : true, 
      "subLayerIds" : [6]
    }, 
    {
      "id" : 6, 
      "name" : "Default", 
      "parentLayerId" : 5, 
      "defaultVisibility" : true, 
      "subLayerIds" : null
    }, 
    {
      "id" : 7, 
      "name" : "IbuKotaProv_Anno", 
      "parentLayerId" : 0, 
      "defaultVisibility" : true, 
      "subLayerIds" : [8]
    }, 
    {
      "id" : 8, 
      "name" : "Default", 
      "parentLayerId" : 7, 
      "defaultVisibility" : true, 
      "subLayerIds" : null
    }, 
    {
      "id" : 9, 
      "name" : "KotaLain_Anno", 
      "parentLayerId" : 0, 
      "defaultVisibility" : true, 
      "subLayerIds" : [10]
    }, 
    {
      "id" : 10, 
      "name" : "Default", 
      "parentLayerId" : 9, 
      "defaultVisibility" : true, 
      "subLayerIds" : null
    }, 
    {
      "id" : 11, 
      "name" : "Laut_Anno", 
      "parentLayerId" : 0, 
      "defaultVisibility" : true, 
      "subLayerIds" : [12]
    }, 
    {
      "id" : 12, 
      "name" : "Default", 
      "parentLayerId" : 11, 
      "defaultVisibility" : true, 
      "subLayerIds" : null
    }, 
    {
      "id" : 13, 
      "name" : "ProvArea_Anno", 
      "parentLayerId" : 0, 
      "defaultVisibility" : true, 
      "subLayerIds" : [14]
    }, 
    {
      "id" : 14, 
      "name" : "Default", 
      "parentLayerId" : 13, 
      "defaultVisibility" : true, 
      "subLayerIds" : null
    }, 
    {
      "id" : 15, 
      "name" : "Pulau_Anno", 
      "parentLayerId" : 0, 
      "defaultVisibility" : true, 
      "subLayerIds" : [16]
    }, 
    {
      "id" : 16, 
      "name" : "Default", 
      "parentLayerId" : 15, 
      "defaultVisibility" : true, 
      "subLayerIds" : null
    }, 
    {
      "id" : 17, 
      "name" : "SungaiAnno", 
      "parentLayerId" : 0, 
      "defaultVisibility" : true, 
      "subLayerIds" : [18]
    }, 
    {
      "id" : 18, 
      "name" : "Default", 
      "parentLayerId" : 17, 
      "defaultVisibility" : true, 
      "subLayerIds" : null
    }, 
    {
      "id" : 19, 
      "name" : "Tanjung_Anno", 
      "parentLayerId" : 0, 
      "defaultVisibility" : true, 
      "subLayerIds" : [20]
    }, 
    {
      "id" : 20, 
      "name" : "Default", 
      "parentLayerId" : 19, 
      "defaultVisibility" : true, 
      "subLayerIds" : null
    }, 
    {
      "id" : 21, 
      "name" : "Teluk_Anno", 
      "parentLayerId" : 0, 
      "defaultVisibility" : true, 
      "subLayerIds" : [22]
    }, 
    {
      "id" : 22, 
      "name" : "Default", 
      "parentLayerId" : 21, 
      "defaultVisibility" : true, 
      "subLayerIds" : null
    }, 
    {
      "id" : 23, 
      "name" : "DanauAnno", 
      "parentLayerId" : 0, 
      "defaultVisibility" : true, 
      "subLayerIds" : [24]
    }, 
    {
      "id" : 24, 
      "name" : "Default", 
      "parentLayerId" : 23, 
      "defaultVisibility" : true, 
      "subLayerIds" : null
    }, 
    {
      "id" : 25, 
      "name" : "Jembatan", 
      "parentLayerId" : -1, 
      "defaultVisibility" : true, 
      "subLayerIds" : null
    }, 
    {
      "id" : 26, 
      "name" : "Titik Tinggi", 
      "parentLayerId" : -1, 
      "defaultVisibility" : true, 
      "subLayerIds" : null
    }, 
    {
      "id" : 27, 
      "name" : "Kota Lain", 
      "parentLayerId" : -1, 
      "defaultVisibility" : false, 
      "subLayerIds" : null
    }, 
    {
      "id" : 28, 
      "name" : "Ibukota Kecamatan", 
      "parentLayerId" : -1, 
      "defaultVisibility" : true, 
      "subLayerIds" : null
    }, 
    {
      "id" : 29, 
      "name" : "Ibukota Kabupaten", 
      "parentLayerId" : -1, 
      "defaultVisibility" : true, 
      "subLayerIds" : null
    }, 
    {
      "id" : 30, 
      "name" : "Ibukota Provinsi", 
      "parentLayerId" : -1, 
      "defaultVisibility" : true, 
      "subLayerIds" : null
    }, 
    {
      "id" : 31, 
      "name" : "Terumbu Karang", 
      "parentLayerId" : -1, 
      "defaultVisibility" : true, 
      "subLayerIds" : null
    }, 
    {
      "id" : 32, 
      "name" : "Jalan", 
      "parentLayerId" : -1, 
      "defaultVisibility" : true, 
      "subLayerIds" : null
    }, 
    {
      "id" : 33, 
      "name" : "Jalan Kereta Api", 
      "parentLayerId" : -1, 
      "defaultVisibility" : true, 
      "subLayerIds" : null
    }, 
    {
      "id" : 34, 
      "name" : "Sungai", 
      "parentLayerId" : -1, 
      "defaultVisibility" : true, 
      "subLayerIds" : null
    }, 
    {
      "id" : 35, 
      "name" : "Garis Pantai", 
      "parentLayerId" : -1, 
      "defaultVisibility" : true, 
      "subLayerIds" : null
    }, 
    {
      "id" : 36, 
      "name" : "Kontur", 
      "parentLayerId" : -1, 
      "defaultVisibility" : false, 
      "subLayerIds" : null
    }, 
    {
      "id" : 37, 
      "name" : "Desa", 
      "parentLayerId" : -1, 
      "defaultVisibility" : true, 
      "subLayerIds" : null
    }, 
    {
      "id" : 38, 
      "name" : "Kecamatan", 
      "parentLayerId" : -1, 
      "defaultVisibility" : true, 
      "subLayerIds" : null
    }, 
    {
      "id" : 39, 
      "name" : "Kabupaten", 
      "parentLayerId" : -1, 
      "defaultVisibility" : true, 
      "subLayerIds" : null
    }, 
    {
      "id" : 40, 
      "name" : "Provinsi", 
      "parentLayerId" : -1, 
      "defaultVisibility" : true, 
      "subLayerIds" : null
    }, 
    {
      "id" : 41, 
      "name" : "Danau", 
      "parentLayerId" : -1, 
      "defaultVisibility" : true, 
      "subLayerIds" : null
    }, 
    {
      "id" : 42, 
      "name" : "Terumbu Karang", 
      "parentLayerId" : -1, 
      "defaultVisibility" : true, 
      "subLayerIds" : null
    }, 
    {
      "id" : 43, 
      "name" : "Tutupan Lahan", 
      "parentLayerId" : -1, 
      "defaultVisibility" : true, 
      "subLayerIds" : null
    }, 
    {
      "id" : 44, 
      "name" : "Stasiun Kereta Api", 
      "parentLayerId" : -1, 
      "defaultVisibility" : true, 
      "subLayerIds" : null
    }, 
    {
      "id" : 45, 
      "name" : "Bandar Udara", 
      "parentLayerId" : -1, 
      "defaultVisibility" : true, 
      "subLayerIds" : null
    }, 
    {
      "id" : 46, 
      "name" : "Kontur", 
      "parentLayerId" : -1, 
      "defaultVisibility" : true, 
      "subLayerIds" : null
    }, 
    {
      "id" : 47, 
      "name" : "Desa", 
      "parentLayerId" : -1, 
      "defaultVisibility" : true, 
      "subLayerIds" : null
    }, 
    {
      "id" : 48, 
      "name" : "Kecamatan", 
      "parentLayerId" : -1, 
      "defaultVisibility" : true, 
      "subLayerIds" : null
    }, 
    {
      "id" : 49, 
      "name" : "Kabupaten", 
      "parentLayerId" : -1, 
      "defaultVisibility" : true, 
      "subLayerIds" : null
    }, 
    {
      "id" : 50, 
      "name" : "Provinsi", 
      "parentLayerId" : -1, 
      "defaultVisibility" : true, 
      "subLayerIds" : null
    }
  ], 
  "spatialReference" : {
    "wkt" : "GEOGCS[\"WGS84 Lat/Long\u0027s, Degrees, -180 ==\u003e +180\",DATUM[\"D_WGS84\",SPHEROID[\"World Geodetic System of 1984\",6378137.0,298.257222932867]],PRIMEM[\"Greenwich\",0.0],UNIT[\"degree\",0.0174532925199433]]"
  }, 
  "singleFusedMapCache" : false, 
  "initialExtent" : {
    "xmin" : 106.169254520354, 
    "ymin" : -7.78639720883322, 
    "xmax" : 108.848864050127, 
    "ymax" : -5.88451076158012, 
    "spatialReference" : {
      "wkt" : "GEOGCS[\"WGS84 Lat/Long\u0027s, Degrees, -180 ==\u003e +180\",DATUM[\"D_WGS84\",SPHEROID[\"World Geodetic System of 1984\",6378137.0,298.257222932867]],PRIMEM[\"Greenwich\",0.0],UNIT[\"degree\",0.0174532925199433]]"
    }
  }, 
  "fullExtent" : {
    "xmin" : 106.067479313965, 
    "ymin" : -7.78639720883322, 
    "xmax" : 108.950639256517, 
    "ymax" : -5.88451076158012, 
    "spatialReference" : {
      "wkt" : "GEOGCS[\"WGS84 Lat/Long\u0027s, Degrees, -180 ==\u003e +180\",DATUM[\"D_WGS84\",SPHEROID[\"World Geodetic System of 1984\",6378137.0,298.257222932867]],PRIMEM[\"Greenwich\",0.0],UNIT[\"degree\",0.0174532925199433]]"
    }
  }, 
  "units" : "esriDecimalDegrees", 
  "documentInfo" : {
    "Title" : "SINGAPURA", 
    "Author" : "Abi Rahman", 
    "Comments" : "", 
    "Subject" : "", 
    "Category" : "", 
    "Keywords" : ""
  }
};

        function init(){
            //The max extent for spherical mercator
            var maxExtent = new OpenLayers.Bounds(106.067479313965,-7.78639720883322,108.950639256517,-5.88451076158012);
            
            //Max extent from layerInfo above            
            var layerMaxExtent = new OpenLayers.Bounds(
                layerInfo.fullExtent.xmin, 
                layerInfo.fullExtent.ymin, 
                layerInfo.fullExtent.xmax, 
                layerInfo.fullExtent.ymax  
            );
            
            var resolutions = [];
            for (var i=0; i<layerInfo.tileInfo.lods.length; i++) {
                resolutions.push(layerInfo.tileInfo.lods[i].resolution);
            }
            
            map = new OpenLayers.Map('map', {
                maxExtent: maxExtent,
                StartBounds: layerMaxExtent,
                units: 'esriDecimalDegrees', //(layerInfo.units == "esriFeet") ? 'ft' : 'm',
                resolutions: resolutions,
                tileSize: new OpenLayers.Size(layerInfo.tileInfo.width, layerInfo.tileInfo.height),                
                projection: 'EPSG:' + layerInfo.spatialReference.wkid
            });
            
            
            
            cacheLayer = new OpenLayers.Layer.ArcGISCache( "AGSCache",
                    "http://geoservice.bakosurtanal.go.id/ArcGIS/rest/services/JABAR/MapServer", {
                        isBaseLayer: true,
                        //From layerInfo above                        
                        resolutions: resolutions,                        
                        tileSize: new OpenLayers.Size(layerInfo.tileInfo.cols, layerInfo.tileInfo.rows),                        
                        tileOrigin: new OpenLayers.LonLat(layerInfo.tileInfo.origin.x , layerInfo.tileInfo.origin.y),                        
                        maxExtent: layerMaxExtent,                        
                        projection: 'EPSG:' + layerInfo.spatialReference.wkid
                    });

            
            // create Google Mercator layers
            testLayer = new OpenLayers.Layer.Google(
                "Google Streets",
                {'sphericalMercator': true}
            );
            
            map.addLayers([testLayer, cacheLayer]);
            
            map.addControl(new OpenLayers.Control.LayerSwitcher());
            map.addControl( new OpenLayers.Control.MousePosition() );
            
            map.zoomToExtent(new OpenLayers.Bounds(106.067479313965,-7.78639720883322,108.950639256517,-5.88451076158012));
        }
    </script>
  </head>
  <body onload="init()">
      <h1 id="title">OpenLayers ArcGIS Cache Example (MapServer Access)</h1>

    <div id="tags">
        arcgis, arcgiscache, cache, tms
    </div>

    <p id="shortdesc">
        Demonstrates the basic initialization of the ArcGIS Cache layer using a prebuilt configuration, and standard tile access.
    </p>

    <div id="map" class="smallmap"></div>

    <div id="docs">
        <p>This example demonstrates using the ArcGISCache layer for 
        accessing ESRI's ArcGIS Server (AGS) Map Cache tiles through 
        an AGS MapServer.  Toggle the visibility of the AGS layer to
        demonstrate how the two maps are lined up correctly.</p>
        
         <h2>Notes on this layer</h2>
        <p>A few attempts have been made at this kind of layer before. See 
        <a href="http://trac.osgeo.org/openlayers/ticket/1967">here</a> and 
        <a href="http://trac.osgeo.org/openlayers/browser/sandbox/tschaub/arcgiscache/lib/OpenLayers/Layer/ArcGISCache.js">here</a>.
        A problem the users encounter is that the tiles seem to "jump around".
        This is due to the fact that the max extent for the cached layer actually
        changes at each zoom level due to the way these caches are constructed.
        We have attempted to use the resolutions, tile size, and tile origin
        from the cache meta data to make the appropriate changes to the max extent
        of the tile to compensate for this behavior.</p>
        You will need to know:
        <ul>
            <li>Max Extent: The max extent of the layer</li>
            <li>Resolutions: An array of resolutions, one for each zoom level</li>
            <li>Tile Origin: The location of the tile origin for the cache in the upper left.</li>
            <li>Tile Size: The size of each tile in the cache. Commonly 256 x 256</li>
        </ul>
        <p>It's important that you set the correct values in your layer, and these
        values will differ from layer to layer. You can find these values for your 
        layer in a metadata page in ArcGIS Server. 
        (ie. <a href="http://services.arcgisonline.com/ArcGIS/rest/services/World_Street_Map/MapServer">http://services.arcgisonline.com/ArcGIS/rest/services/World_Street_Map/MapServer</a>)</p>
        <ul>
            <li>Max Extent: Full Extent</li>
            <li>Resolutions: Tile Info -> Levels of Detail -> Resolution</li>
            <li>Tile Origin: Origin -> X,Y</li>
            <li>Tile Size: Tile Info -> Height,Width</li>
        </ul>
        
        <h2> Other Examples </h2>
        <p>This is one of three examples for this layer.  You can also configure this
        layer to use <a href="arcgiscache_direct.html">prebuilt tiles in a file store
         (not a live server).</a> It is also  possible to let this
          <a href="arcgiscache_jsonp.html">layer 'auto-configure' itself using the
          capabilities json object from the server itself when using a live ArcGIS server.</a>
        </p>
    </div>
  </body>
</html>
