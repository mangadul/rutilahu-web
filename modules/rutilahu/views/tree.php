<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title>Peta Kepri</title>        
        <script type="text/javascript" src="<?=base_url()?>assets/js/ext-3.4.1/adapter/ext/ext-base.js"></script>
        <script type="text/javascript" src="<?=base_url()?>assets/js/ext-3.4.1/ext-all.js"></script>
        <link rel="stylesheet" type="text/css" href="<?=base_url()?>assets/js/ext-3.4.1/resources/css/ext-all.css" />
        <link rel="stylesheet" type="text/css" href="<?=base_url()?>assets/js/ext-3.4.1/examples/shared/examples.css" />
        <script src="<?=base_url()?>assets/js/openlayers/lib/OpenLayers.js"></script>
        <script src="<?=base_url()?>assets/js/openlayers/lib/OpenLayers/Control/LayerSwitcherGroups.js"></script>		
        <script type="text/javascript" src="<?=base_url()?>assets/js/geoext/lib/GeoExt.js"></script>
		<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false&v=3.2"></script>
		<script type="text/javascript" src="<?=base_url()?>assets/js/jquery.min.js"></script>	
		<!-- 
		<link rel="stylesheet" type="text/css" href="<?=base_url()?>resources/ext4/examples/view/data-view.css" />			
		-->
        <link rel="stylesheet" type="text/css" href="<?=base_url()?>assets/js/ext-3.4.1/examples/organizer/organizer.css" />		
<style>
.x-grid3-row-body p {
    margin:5px 5px 10px 5px !important;
}
.image {
	position: relative;
	height: auto;
	min-height: 100% !important;
}
#images-view .x-panel-body{
    background: white;
    font: 11px Arial, Helvetica, sans-serif;
}
#images-view .thumb{
    background: #dddddd;
    padding: 3px;
    padding-bottom: 0;
}

.x-quirks #images-view .thumb {
    padding-bottom: 3px;
}

#images-view .thumb img{
    height: 60px;
    width: 80px;
}
#images-view .thumb-wrap{
    float: left;
    margin: 4px;
    margin-right: 0;
    padding: 5px;
}
#images-view .thumb-wrap span {
    
    display: block;
    overflow: hidden;
    text-align: center;
    width: 86px; /* for ie to ensure that the text is centered */
}

#images-view .x-item-over{
    border:1px solid #dddddd;
    background: #efefef url(over.gif) repeat-x left top;
    padding: 4px;
}

#images-view .x-item-selected{
    background: #eff5fb url(selected.gif) no-repeat right bottom;
    border:1px solid #99bbe8;
    padding: 4px;
}
#images-view .x-item-selected .thumb{
    background:transparent;
}

#images-view .loading-indicator {
    font-size:11px;
    background-image:url('../../resources/themes/images/default/grid/loading.gif');
    background-repeat: no-repeat;
    background-position: left;
    padding-left:20px;
    margin:10px;
}

.x-view-selector {
    position:absolute;
    left:0;
    top:0;
    width:0;
    border:1px dotted;
    opacity: .5;
    -moz-opacity: .5;
    filter:alpha(opacity=50);
    zoom:1;
    background-color:#c3daf9;
    border-color:#3399bb;
}.ext-strict .ext-ie .x-tree .x-panel-bwrap{
    position:relative;
    overflow:hidden;
}
</style>
        <script type="text/javascript">

var mapPanel, tree, controls=[];
var ctrl, toolbarItems = [], action, actions = {};

Ext.onReady(function() {
	
    var required = '<span style="color:red;font-weight:bold" data-qtip="Required">*</span>';
				
	var peta = new OpenLayers.Map({
		projection: new OpenLayers.Projection("EPSG:4326"), 
		maxExtent: new OpenLayers.Bounds(102.53579, 1.58407, 107.76528, -0.06368),		// 94.23065, 6.59913, 112.38006,-5.55038
		allOverlays: false,
		center: new OpenLayers.LonLat(104.4803663, 1.0898176),
		numZoomLevels: 20,
		maxResolution: 'auto',		
		units: 'm',
		displayProjection: new OpenLayers.Projection("EPSG:4326"),		
		controls: [ 
			new OpenLayers.Control.PanZoom(), 
			new OpenLayers.Control.Navigation(),
			new OpenLayers.Control.Attribution(),
			new OpenLayers.Control.LayerSwitcher(),
			new OpenLayers.Control.OverviewMap(),
			new OpenLayers.Control.MousePosition(),
			new OpenLayers.Control.ScaleLine(),
			new OpenLayers.Control.KeyboardDefaults()
		]		
	});
				
   var vector = new OpenLayers.Layer.Vector("vector", {projection: "EPSG:4326", units: 'm'});
   	peta.addLayer(vector);
	vector.setVisibility(false);
	
    var store_datasurvey = new Ext.data.JsonStore({
        root: 'data',
        remoteSort: true,
        fields: ['id_penerima', 'tahun_terima', 'provinsi', 'kabupaten', 'kecamatan', 'desa', 'no_urut',
	'namalengkap', 'jalan_desa', 'rt', 'rw', 'ktp', 'kk', 'latitude', 'longitude', 'img_foto_penerima', 
	'img_tampak_samping_1', 'img_tampak_samping_2', 'img_tampak_belakang', 'img_tampak_dapur', 
	'img_tampak_jamban', 'img_tampak_sumber_air', 'keterangan', 'kode_desa', 'kode_kec', 'kode_kab', 
	'is_catat', 'tgl_update', 'tempat_lahir', 'tgl_lahir', 'jenis_kelamin', 'devid', 'img_tampak_depan_rumah'],
		idProperty: 'id_penerima',
        proxy: new Ext.data.ScriptTagProxy({
            url: '<?=base_url()?>index.php/rutilahu/Main/get_detail_penerima'
        })
    });
		
	var store_photo = new Ext.data.JsonStore({
		url: '<?=base_url()?>index.php/rutilahu/Main/get_photo_penerima',
		autoLoad: true,
		root: 'images',
		id:'name',
		fields:['name', 'url',]
    });
	
    var viewPhoto = new Ext.DataView({
        itemSelector: 'div.thumb-wrap',
        style:'overflow:auto',
		singleSelect: true,		
        store: store_photo,
		//selModel: new Ext.grid.RowSelectionModel(),		
        tpl: new Ext.XTemplate(
            '<tpl for=".">',
            '<div class="thumb-wrap" id="{name}">',
            '<div class="thumb"><img src="{url}" class="thumb-img"></div>',
            '<span>{shortName}</span></div>',
            '</tpl>'
        ),
		prepareData: function(data) {
			Ext.apply(data, {
				shortName: Ext.util.Format.ellipsis(data.name, 15),
			});
			return data;
		},
		listeners: {
			'selectionchange': {fn: function() {
				var selNode = viewPhoto.getSelectedNodes()[0];
				//fwinImg(selNode);
				//console.log(selNode);
			}, scope:this, buffer:100},	
		}		
    });	
	/* end data view */
		
	function fwinImg(img)
	{
		var winimg = new Ext.Window({
			title: 'Lihat Gambar',
			modal: true,	
			resizable: false,
			closeAction: 'hide',
			closable: true,					
			width:600,
			height:600,
			border: false,
			x: 0,
			y: 0,			
			items: new Ext.Panel({
			  scroll : "vertical",
			  title   : "Foto",
			  html   : img			  
			}),
		});
		winimg.on('show', function(win) {
			win.center();
			win.doLayout();						
		});					
		winimg.show();		
	}
	
    var frmDetail = new Ext.FormPanel({	
        layout: 'form',
		autoScroll: true,
        id: 'frmDetail',
        frame: false,
        bodyPadding: '5 5 0',
        width: 350,
        fieldDefaults: {
            msgTarget: 'side',
            labelWidth: 100
        },
        defaultType: 'textfield',
        items: [
		{
			xtype: 'fieldset',
			title: 'Data Diri',
			defaults: {
				anchor: '100%',
				layout: {
					type: 'hbox',
				}
			},
			items: [		
			{
				fieldLabel: 'ID Penerima',
				xtype     : 'textfield',
				name: 'id_penerima',
				readOnly: true,
			},		
			{
				fieldLabel: 'Nama Lengkap',
				xtype     : 'textfield',
				readOnly: true,
				name: 'namalengkap',
			},
			{
				fieldLabel: 'KTP',
				xtype     : 'textfield',
				readOnly: true,
				name: 'ktp',
			},
			{
				fieldLabel: 'KK',
				xtype     : 'textfield',
				readOnly: true,
				name: 'kk',
			},
			{
				fieldLabel: 'Tahun Terima',
				xtype     : 'textfield',
				name: 'tahun_terima',
				readOnly: true,
			},
			]
		},
		{
			xtype: 'fieldset',
			title: 'Alamat',
			defaults: {
				anchor: '100%',
				layout: {
					type: 'hbox',
				}
			},
			items: [
				{
					xtype     : 'textfield',
					readOnly: true,
					name      : 'jalan_desa',
					fieldLabel: 'Jalan Desa',
				},
				{
					xtype     : 'textfield',
					name      : 'rt',
					fieldLabel: 'Rt',
					readOnly: true,
					
				},
				{
					xtype     : 'textfield',
					name      : 'rw',
					fieldLabel: 'Rw',
					readOnly: true,
				},				
				{
					xtype     : 'textfield',
					name      : 'desa',
					fieldLabel: 'Desa',
					readOnly: true,
				},				
				{
					xtype     : 'textfield',
					name      : 'kecamatan',
					fieldLabel: 'Kecamatan',
					readOnly: true,
				},
				{
					xtype     : 'textfield',
					name      : 'kabupaten',
					fieldLabel: 'Kabupaten',
					readOnly: true,
				}				
			]
		},
		{
			xtype: 'fieldset',
			title: 'Titik GPS',
			defaults: {
				anchor: '100%',
				layout: {
					type: 'hbox',
				}
			},
			items: [
				{
					xtype     : 'textfield',
					readOnly: true,
					name      : 'longitude',
					fieldLabel: 'Latitude',
				},
				{
					xtype     : 'textfield',
					readOnly: true,
					name      : 'latitude',
					fieldLabel: 'Longitude',
				},
			]
		},
		{
			xtype: 'fieldset',
			title: 'Keterangan',
			defaults: {
				anchor: '100%',
				layout: {
					type: 'hbox',
				}
			},
			items: [		
				{
					xtype     : 'textfield',
					fieldLabel: 'Tgl Catat',
					readOnly: true,
					name: 'tgl_update'
				}, 		
				{
					fieldLabel: 'Keterangan',
					xtype     : 'textfield',
					name: 'keterangan'
				}, 
				{
					fieldLabel: 'IMEI Perangkat',
					xtype     : 'textfield',
					readOnly: true,
					name: 'devid'
				}, 
			]
		}
		],
    });
	
	var tabDetail = new Ext.TabPanel({	
        activeTab: 0,
        width: '100%',
        height: '100%',
        plain: false,
		frame: false,
		//layout: 'fit',
        defaults :{
            autoScroll: true,
            bodyPadding: 1
        },
        items: [
			{
                title: 'Data Penerima',
				layout: 'fit',
				autoScroll: true,
				items: [frmDetail],
                listeners: {
                    activate: function(tab) {
                        setTimeout(function() {							
							Ext.MessageBox.show({
							   title: 'Silahkan tunggu',
							   msg: 'Sedang mengambil data...',
							   progressText: 'Loading...',
							   width:300,
							   progress:true,
							   closable:true,
							});						
							frmDetail.getForm().load({
								url: '<?=base_url();?>index.php/rutilahu/Main/get_detail_penerimas',
								failure: function(form, action) {
									Ext.Msg.alert("Load failed", "Gagal mengambil data. Ada masalah dg koneksi jaringan. Silahkan dicoba kembali.");
								},
								success: function(r){
									Ext.MessageBox.hide();
								}
							});
						}, 1);
                    }
                }
            },
			{
                title: 'Foto',
                listeners: {
                    activate: function(tab){
                        setTimeout(function() {							
							Ext.MessageBox.show({
							   title: 'Silahkan tunggu',
							   msg: 'Sedang memperkecil dan memuat foto...',
							   progressText: 'Loading...',
							   width:300,
							   progress:true,
							   closable:true,
							});
							store_photo.load({
								callback: function(records, operation, success) {
									if(success)
									{
										Ext.MessageBox.hide();
									}
								}
							});							
                        }, 1);
                    }
                },
                items: [viewPhoto]
            },
        ]
    });
		
	var winDetail = new Ext.Window({
		title: 'Detail Penerima Bantuan',
		closeAction: 'hide',
		closable: true,					
		width: 600,
		height: 400,
		layout: 'fit',
		modal: true,	
		items: [
			{
				layout: 'fit',
				items: [tabDetail],
			}
		],
	});
		
			var titikListener = {
				featureselected:function(evt){
					var feature = evt.feature;
					console.log("fitur dipilih: "+feature);
				},	
				featureclick: function(e) {
					//console.log(e.feature);
					winDetail.setTitle('Detail Penerima Bantuan :: '+e.feature.id+' :: '+e.feature.nama);
					winDetail.on('show', function(win) {
						frmDetail.getForm().load({
							url: '<?=base_url();?>index.php/rutilahu/Main/get_detail_penerima',
							method: 'POST',
							params: {
								'idpenerima':e.feature.id,
							},
							failure: function(form, action) {
								Ext.Msg.alert("Load failed", "Gagal mengambil data. Cek koneksi jaringan anda. Silahkan coba kembali.");
							},
							success: function(){								
								Ext.MessageBox.show({
									   title: 'Silahkan tunggu',
									   msg: 'Sedang memperkecil dan memuat foto...',
									   progressText: 'Loading...',
									   width:300,
									   progress:true,
									   closable:true,
								   });												
								store_photo.load({
									callback: function(records, operation, success) {
										if(success)
										{
											Ext.MessageBox.hide();
										}
									}
								});
							}							
						});
					});						
					winDetail.doLayout();							
					winDetail.show();							
				},
				nofeatureclick: function(e) {
				}
			};
			
			this.pj_epsg_900913 = new OpenLayers.Projection("EPSG:900913");
			this.pj_epsg_4326 = new OpenLayers.Projection("EPSG:4326");
	
			var osm = new OpenLayers.Layer.OSM("OpenStreet Map");			
			
			var google_hybrid = new OpenLayers.Layer.Google(
				"Google Hybrid",
				{type: google.maps.MapTypeId.HYBRID, visibility: true}
			);

			peta.addLayer(google_hybrid);
			peta.setCenter(new OpenLayers.LonLat(104.4803663, 1.0898176).transform(
			this.pj_epsg_4326,
			this.pj_epsg_900913), 10);
			
			var google_roadmap = new OpenLayers.Layer.Google(
				"Google Roadmap",
				{type: google.maps.MapTypeId.ROADMAP, visibility: true}
			);
			
			var google_physical = new OpenLayers.Layer.Google(
				"Google Physical",
				{type: google.maps.MapTypeId.PHYSICAL}
			);
			 
			var google_satellite = new OpenLayers.Layer.Google(
				"Google Satellite",
				{type: google.maps.MapTypeId.SATELLITE}
			);
						 								   
			var google_terrain = new OpenLayers.Layer.Google(
				"Google Terrain",
				{type: google.maps.MapTypeId.TERRAIN}
			);
		
		var lonlat_ = [104.4803663, 1.0898176];
		var zpj_epsg_900913 = new OpenLayers.Projection("EPSG:900913");
		var zpj_epsg_4326 = new OpenLayers.Projection("EPSG:4326");
		
		$.getJSON('<?=base_url()?>index.php/rutilahu/Main/get_titiktengah_peta', function(tengah) {
			var tlong = tengah[0].titik_tengah_long;
			var tlat = tengah[0].titik_tengah_lat;
			$.getJSON('<?=base_url()?>index.php/rutilahu/Main/get_zonasi_peta', function(zona) {
			  $.each(zona.data, function(i, zone) {	
				var strLayerZona = "LayerZona"+zone.id_zona;	
				var geomLayer = "GeomZona"+zone.id_zona;	
				var czona = "cZona"+zone.id_zona;	
				var cfzona = "CFZona"+zone.id_zona;	
				var strZone = zone.zona + ' - '+zone.ket;
				strLayerZona = new OpenLayers.Layer.Vector(strZone);
				geomLayer = OpenLayers.Geometry.Polygon.createRegularPolygon(
					new OpenLayers.Geometry.Point(tlong, tlat).transform(
					zpj_epsg_4326,
					zpj_epsg_900913),
					zone.jarak_meter,
					30);	
				cfzona = [];	
				czona = new OpenLayers.Feature.Vector(geomLayer);
				cfzona.push(czona);
				strLayerZona.removeAllFeatures();
				strLayerZona.addFeatures(cfzona);	
				strLayerZona.group = 'Zonasi (dari titik tengah)';	
				peta.addLayer(strLayerZona);
				strLayerZona.setVisibility(false);
				});
				peta.addControl(new OpenLayers.Control.LayerSwitcherGroups());
			}); 
		});		

		/* tahun */
		//var gTahunLayer = [];
		$.getJSON('<?=base_url()?>index.php/rutilahu/Main/get_tahun_peta', function(dtahun) {
			
			//var renderer = OpenLayers.Layer.Vector.prototype.renderers;
			
		  $.each(dtahun.data, function(i, thn) {	
			var strLayer = "LayerTahun"+thn;
			strLayer = new OpenLayers.Layer.Vector("Tahun "+thn, {
					projection: "EPSG:4326",
					eventListeners: titikListener,
				}
			);  	
			Ext.MessageBox.show({
			   title: 'Silahkan tunggu',
			   msg: 'Sedang mengambil data...',
			   progressText: 'Loading...',
			   width:300,
			   progress:true,
			   closable:true,
			});									
						
			$.getJSON('<?=base_url()?>index.php/rutilahu/Main/get_data_tahun', {tahun: thn}, function(tdata) {
			  $.each(tdata.data, function(j, t) {
				var tlokasi = [], f_id=[], f_nama=[];
				var px = t.latitude;
				var py = t.longitude;
				f_id = t.id_penerima;
				f_nama = t.namalengkap;
				var lonlat = new OpenLayers.LonLat(px, py);					
				lonlat.transform(new OpenLayers.Projection("EPSG:4326"), new OpenLayers.Projection("EPSG:900913"));
				var pG = new OpenLayers.Geometry.Point(lonlat.lon, lonlat.lat);
				var icon = '<?=base_url()?>assets/images/markers/home%s.png';
				var iconImg = icon.replace(/%s/g, i);	
				var pF = new OpenLayers.Feature.Vector(pG, null, {
					pointRadius: 18,
					fillOpacity: 1,
					externalGraphic: iconImg,
					label: t.id_penerima,
					fontSize: "8px",
					labelAlign: "cm",
					strokeColor: "#00FF00",
					strokeOpacity: 1,
					strokeWidth: 3,
					fillColor: "#FF5500",
					pointerEvents: "visiblePainted",
					fontWeight: "bold"
				});
				pF.attributes = {
					name: t.id_penerima,
					favColor: 'blue',
					//align: 'lb',
					align: "cm",
					xOffset: 50,
					yOffset: -15					
				};				
				pF.id = f_id;
				pF.nama = f_nama;
				tlokasi.push(pF);
				strLayer.addFeatures(tlokasi);
			  });
			})
			  .done(function() {
				Ext.MessageBox.hide();
			  })
			  .fail(function() {
				console.log( "error" );
				Ext.MessageBox.hide();
				Ext.MessageBox.alert("Status", "Gagal memuat data. Kesalahan koneksi jaringan.");				
			  });			
			strLayer.group = 'Tahun';
			peta.addLayer(strLayer);
			peta.setLayerIndex(strLayer, 99999);
			strLayer.setVisibility(true);
		  }); 
		peta.addControl(new OpenLayers.Control.LayerSwitcherGroups());
		}); 
		/* end tahun */
		
		/*
		this.pilih = new OpenLayers.Control.SelectFeature(
			[],
			{
				'hover':true,
				'callbacks': {
				
			}
		});
		*/
		
		OpenLayers.Handler.Feature.prototype.activate = function() {
			var activated = false;
			if (OpenLayers.Handler.prototype.activate.apply(this, arguments)) {
				//this.moveLayerToTop();
				this.map.events.on({
					"removelayer": this.handleMapEvents,
					"changelayer": this.handleMapEvents,
					scope: this
				});
				activated = true;
			}
			return activated;
		};
		
		action = new GeoExt.Action({
        control: new OpenLayers.Control.ZoomToMaxExtent(),
        map: peta,
        text: "max extent",
        tooltip: "zoom to max extent"
    });
    actions["max_extent"] = action;
    toolbarItems.push(action);
    toolbarItems.push("-");

    action = new GeoExt.Action({
        text: "nav",
        control: new OpenLayers.Control.Navigation(),
        map: peta,
        toggleGroup: "draw",
        allowDepress: false,
        pressed: true,
        tooltip: "navigate",
        group: "draw",
        checked: true
    });
    actions["nav"] = action;
    toolbarItems.push(action);

    action = new GeoExt.Action({
        text: "draw poly",
        control: new OpenLayers.Control.DrawFeature(
            vector, OpenLayers.Handler.Polygon
        ),
        map: peta,
        toggleGroup: "draw",
        allowDepress: false,
        tooltip: "draw polygon",
        group: "draw"
    });
    actions["draw_poly"] = action;
    toolbarItems.push(action);

    action = new GeoExt.Action({
        text: "draw line",
        control: new OpenLayers.Control.DrawFeature(
            vector, OpenLayers.Handler.Path
        ),
        map: peta,
        toggleGroup: "draw",
        allowDepress: false,
        tooltip: "draw line",
        group: "draw"
    });
    actions["draw_line"] = action;
    toolbarItems.push(action);
    toolbarItems.push("-");

    action = new GeoExt.Action({
        text: "select",
        control: new OpenLayers.Control.SelectFeature(vector, {
            type: OpenLayers.Control.TYPE_TOGGLE,
            hover: true
        }),
        map: peta,
        enableToggle: true,
        tooltip: "select feature"
    });
    actions["select"] = action;
    toolbarItems.push(action);
    toolbarItems.push("-");
	
    toolbarItems.push({
        text: "menu",
        menu: new Ext.menu.Menu({
            items: [
                actions["max_extent"],
                new Ext.menu.CheckItem(actions["nav"]),
                new Ext.menu.CheckItem(actions["draw_poly"]),
                new Ext.menu.CheckItem(actions["draw_line"]),
                new Ext.menu.CheckItem(actions["select"]),
            ]
        })
    });
			
	slonLat = new OpenLayers.LonLat(104.4803663, 1.0898176).transform(new OpenLayers.Projection("EPSG:4326"), peta.getProjectionObject());	
    mapPanel = new GeoExt.MapPanel({
        border: true,
        region: "center",
        map: peta,
        center: slonLat,
        zoom: 10,
        layers: [google_roadmap, google_physical, google_satellite, google_terrain, osm], //pointLayer, 
		tbar: toolbarItems
    });
	
    var LayerNodeUI = Ext.extend(GeoExt.tree.LayerNodeUI, new GeoExt.tree.TreeNodeUIEventMixin());
        
    var treeConfig = [
		{
			nodeType: "gx_baselayercontainer"
		}, 
		{
			nodeType: "gx_overlaylayercontainer",
			expanded: true,
			loader: {
				baseAttrs: {
					radioGroup: "foo",
					uiProvider: "layernodeui"
				}
			}
		},
	];
    treeConfig = new OpenLayers.Format.JSON().write(treeConfig, true);
    // create the tree with the configuration from above
    tree = new Ext.tree.TreePanel({
        border: true,
        region: "west",
        title: "Layers",
        width: 200,
        split: true,
        collapsible: true,
        collapseMode: "mini",
        autoScroll: true,
        plugins: [
            new GeoExt.plugins.TreeNodeRadioButton({
                listeners: {
                    "radiochange": function(node) {
						Ext.MessageBox.alert("Status", node.text + " is now the active layer.");
                    }
                }
            })
        ],
        loader: new Ext.tree.TreeLoader({
            applyLoader: false,
            uiProviders: {
                "layernodeui": LayerNodeUI
            },
        }),
        root: {
            nodeType: "async",
            children: Ext.decode(treeConfig)			
        },
        listeners: {
            "radiochange": function(node){
                alert(node.layer.name + " is now the the active layer.");
            }
        },
        rootVisible: false,
        lines: false,
    });

    var treeConfigWin = new Ext.Window({
        layout: "fit",
        hideBorders: true,
        closeAction: "hide",
        width: 300,
        height: 400,
        title: "Tree Configuration",
        items: [{
            xtype: "form",
            //layout: "fit",
            items: [{
                id: "treeconfig",
                xtype: "textarea"
            }],
            buttons: [{
                text: "Save",
                handler: function() {
                    var value = Ext.getCmp("treeconfig").getValue()
                    try {
                        var root = tree.getRootNode();
                        root.attributes.children = Ext.decode(value);
                        tree.getLoader().load(root);
                    } catch(e) {
                        alert("Invalid JSON");
                        return;
                    }
                    treeConfig = value;
                    treeConfigWin.hide();
                }
            }, {
                text: "Cancel",
                handler: function() {
                    treeConfigWin.hide();
                }
            }]
        }]
    });
    	
    new Ext.Viewport({
        layout: "fit",
        hideBorders: true,
        items: {
            layout: "border",
            deferredRender: false,
            items: [mapPanel, tree]
        }
    });
});		
		</script>

    </head>
    <body>
        <div id="desc"></div>
    </body>
</html>