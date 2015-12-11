<!DOCTYPE html>
<html lang="en">
    <head>
        <meta http-equiv="content-type" content="text/html; charset=UTF-8"> 
        <meta charset="utf-8">
        <title>Data Lokasi Penerima</title>
        <meta name="generator" content="Bootply" />
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
        <meta name="description" content="Peta Penerima" />
	<link rel="stylesheet" type="text/css" href="<?=base_url()?>assets/css/bootstrap.min.css" />		
		
	<link rel="stylesheet" type="text/css" href="<?=base_url()?>resources/ext4/examples/shared/example.css" />		
	<link rel="stylesheet" type="text/css" href="<?=base_url()?>resources/ext4/examples/view/data-view.css" />	
    <style type="text/css">
      html { height: 100% }
      body { height: 100%; margin: 0; padding: 0 }
      #map-canvas { height: 100% }
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
	<script type="text/javascript" src="<?=base_url()?>resources/ext4/examples/shared/include-ext.js"></script>  	
	<script type="text/javascript" src="http://maps.google.com/maps/api/js"></script>
	<script type="text/javascript" src="<?=base_url()?>assets/js/jquery.min.js"></script>
	<script type="text/javascript" src="<?=base_url()?>assets/js/bootstrap.min.js"></script>  	
	<script type="text/javascript" src="<?=base_url()?>assets/js/jquery.ui.map.js"></script>
	<script type="text/javascript">
		
Ext.Loader.setConfig({enabled: true});
Ext.Loader.setPath('Ext.ux.DataView', '<?=base_url()?>resources/ext4/examples/ux/DataView/');
Ext.Loader.setPath('Ext.ux', '<?=base_url()?>resources/ext4/examples/ux');
Ext.require([
    'Ext.tip.QuickTipManager',
    'Ext.container.Viewport',
    'Ext.layout.*',
    'Ext.form.Panel',
    'Ext.form.Label',
    'Ext.grid.*',
    'Ext.data.*',
    'Ext.tree.*',
    'Ext.selection.*',
    'Ext.tab.Panel',
	'Ext.ux.GMapPanel',
    'Ext.util.*',
    'Ext.view.View',
    'Ext.menu.*',
    'Ext.form.field.ComboBox',
    'Ext.layout.container.Table',
    'Ext.container.ButtonGroup',	
    'Ext.ux.DataView.DragSelector',
    'Ext.ux.DataView.LabelEditor'	
]);

Ext.onReady(function(){ 
		Ext.tip.QuickTipManager.init();
    var required = '<span style="color:red;font-weight:bold" data-qtip="Required">*</span>';
			
    Ext.define('mdl_datasurvey', {
        extend: 'Ext.data.Model',			
        fields: ['id_penerima', 'tahun_terima', 'provinsi', 'kabupaten', 'kecamatan', 'desa', 'no_urut',
	'namalengkap', 'jalan_desa', 'rt', 'rw', 'ktp', 'kk', 'latitude', 'longitude', 'img_foto_penerima', 
	'img_tampak_samping_1', 'img_tampak_samping_2', 'img_tampak_belakang', 'img_tampak_dapur', 
	'img_tampak_jamban', 'img_tampak_sumber_air', 'keterangan', 'kode_desa', 'kode_kec', 'kode_kab', 
	'is_catat', 'tgl_update', 'tempat_lahir', 'tgl_lahir', 'jenis_kelamin', 'devid', 'img_tampak_depan_rumah'],
        idProperty: 'id_penerima'
    });
		
    var store_datasurvey = Ext.create('Ext.data.Store', {
        pageSize: 50,
        model: 'mdl_datasurvey',
        remoteSort: true,
        proxy: {
            url: '<?=base_url()?>index.php/rutilahu/Main/get_detail_penerima',
            simpleSortMode: true,
			type: 'ajax',
			reader: {
				type: 'json',
				root: 'data'
			}			
        },
		baseParams: {
			limit: 100,
		},		
        sorters: [{
            property: 'id_penerima',
            direction: 'DESC'
        }],
		autoLoad: false		
    });
			
	/* data view */	
    ImageModel = Ext.define('ImageModel', {
        extend: 'Ext.data.Model',
        fields: [
           {name: 'name'},
           {name: 'url'}
        ]
    });

    var store_photo = Ext.create('Ext.data.Store', {
        model: 'ImageModel',
        proxy: {
            type: 'ajax',
            url: '<?=base_url()?>index.php/rutilahu/Main/get_photo_penerima',
            reader: {
                type: 'json',
                root: 'images'
            }
        }
    });
	
    //store_photo.load();

    var viewPhoto = Ext.create('Ext.Panel', {
        id: 'images-view',
        frame: false,
        width: '100%',
		height:'100%',
        items: Ext.create('Ext.view.View', {
            store: store_photo,
            tpl: [
                '<tpl for=".">',
                    '<div class="thumb-wrap" id="{name:stripTags}">',
                        '<div class="thumb"><img src="{url}" title="{name:htmlEncode}"></div>',
                        '<span class="x-editable">{shortName:htmlEncode}</span>',
                    '</div>',
                '</tpl>',
                '<div class="x-clear"></div>'
            ],
            multiSelect: false,
            height: '100%',
            trackOver: true,
            overItemCls: 'x-item-over',
            itemSelector: 'div.thumb-wrap',
            emptyText: 'No images to display',
            plugins: [
                Ext.create('Ext.ux.DataView.DragSelector', {}),
                Ext.create('Ext.ux.DataView.LabelEditor', {dataIndex: 'name'})
            ],
            prepareData: function(data) {
                Ext.apply(data, {
                    shortName: Ext.util.Format.ellipsis(data.name, 15),
					/*
                    sizeString: Ext.util.Format.fileSize(data.size),
                    dateString: Ext.util.Format.date(data.lastmod, "m/d/Y g:i a")
					*/
                });
                return data;
            },
            listeners: {
				selectionchange: function(dv, sel) {
					//var url = sel.get("url");				
					//console.log(url);
				//selectionchange: function(selModel, selection, eOpts) {
					//var node = selection[0];
					//fwinImg(node.get('url'));
                }
            }
        })
    });	
	/* end data view */
	
	function fwinImg(img)
	{
		var winimg = Ext.create('Ext.window.Window', {
			title: 'Lihat Gambar',
			modal: true,	
			resizable: false,
			closeAction: 'hide',
			closable: true,					
			width:300,
			height:400,
			border: false,
			x: 0,
			y: 0,
			items: 
			{
				xtype: 'image',
				width: 300,
				height: 400,
				src: img
			}
		});
		winimg.on('show', function(win) {
			win.center();
			win.doLayout();						
		});					
		//winimg.center();
		winimg.show();		
	}
	
    var frmDetail = Ext.widget({
        xtype: 'form',
        layout: 'form',
		autoScroll: true,
        id: 'frmDetail',
        //url: 'save-form.php',
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
            fieldLabel: 'ID Penerima',
            name: 'id_penerima',
			readOnly: true,
        },		
		{
            fieldLabel: 'Nama Lengkap',
			readOnly: true,
            name: 'namalengkap',
        },
		{
            fieldLabel: 'KTP',
			readOnly: true,
            name: 'ktp',
        },
		{
            fieldLabel: 'KK',
			readOnly: true,
            name: 'kk',
        },
		{
            fieldLabel: 'Tahun Terima',
            name: 'tahun_terima',
			readOnly: true,
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
            fieldLabel: 'Tgl Catat',
			readOnly: true,
            name: 'tgl_update'
        }, 		
		{
            fieldLabel: 'Keterangan',
            name: 'keterangan'
        }, 
		{
            fieldLabel: 'IMEI Perangkat',
			readOnly: true,
            name: 'devid'
        }, 
		],
		/*
        buttons: [{
            text: 'Save'
        },{
            text: 'Cancel'
        }],
		listeners: {
			afterrender: function(win) {
				win.down('form').loadRecord(selectedItem);
			}
		}		
		*/		
    });
	
    var tabDetail = Ext.widget('tabpanel', {
        activeTab: 0,
        width: '100%',
        height: '100%',
        plain: false,
		frame: false,
		layout: 'fit',
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
						frmDetail.load({
							url: '<?=base_url();?>index.php/rutilahu/Main/get_detail_penerima',
							failure: function(form, action) {
								Ext.Msg.alert("Load failed", action.result.errorMessage);
							}
						});											
                    }
                }
            },
			{
                title: 'Foto',
                listeners: {
                    activate: function(tab){
                        setTimeout(function() {
							store_photo.load();
                        }, 5);
                    }
                },
                items: [viewPhoto]
            },
        ]
    });
				
		var winDetail = Ext.widget('window', {
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
		
		$('#map-canvas').gmap({'center': '3.9126021,107.8492258,10' }).bind('init', function() { 
			$.getJSON( '<?=base_url()?>index.php/rutilahu/Main/titik_peta', function(data) { 
				$.each( data.markers, function(i, marker) {
					$('#map-canvas').gmap('addMarker', { 
						'position': new google.maps.LatLng(marker.latitude, marker.longitude), 
						'bounds': true 
					}).click(function() {
						winDetail.setTitle('Detail Penerima Bantuan :: '+marker.id_penerima+' :: '+marker.nama_penerima);
						winDetail.on('show', function(win) {
							Ext.Ajax.request({
								url: '<?=base_url();?>index.php/rutilahu/Main/set_detail_penerima',
								method: 'POST',
								params: {
									'idpenerima':marker.id_penerima,
								},
								success: function(r) {
									if(r.statusText == 'OK')
									{
										/*
										setTimeout(function() {
										}, 5);
										*/
										frmDetail.getForm().load({
											url: '<?=base_url();?>index.php/rutilahu/Main/get_detail_penerima',
											failure: function(form, action) {
												Ext.Msg.alert("Load failed", action.result.errorMessage);
											}
										});											
										store_photo.load();
									}
								},
								failure: function() {
									Ext.Msg.alert("ERROR", "Error due to connection problem!");
								}
							});			   
						});						
						//viewPhoto.doLayout();
						winDetail.doLayout();							
						winDetail.show();		
					});
				});
			});
		});	   		
					
	var options = [];
	$('.dropdown-menu a').on('click', function( event ) {
	   var $target = $(event.currentTarget),
		   val = $target.attr('data-value'),
		   $inp = $target.find('input'),
		   idx;	  
	   if ( ( idx = options.indexOf( val ) ) > -1 ) {
		  options.splice( idx, 1 );
		  setTimeout( function() { $inp.prop( 'checked', false ) }, 0);
	   } else {
		  options.push( val );
		  setTimeout( function() { $inp.prop( 'checked', true ) }, 0);
	   }
	   $( event.target ).blur();      
	   console.log( options );
	   return false;
	});
		
});	   	
	</script>
  </head>
  <body>
        <nav class="navbar navbar-default navbar-fixed-top" role="navigation">
      <div class="container-fluid">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="<?php echo base_url();?>index.php/rutilahu/Main/peta">Peta Sebaran Penerima Bantuan</a>
        </div>

        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
          <ul class="nav navbar-nav">
            <!-- <li class="active"><a href="#">Link</a></li> -->
          </ul>
		  <!--
          <form class="navbar-form navbar-left" role="search">
            <div class="form-group">
              <input type="text" class="form-control" placeholder="Search">
            </div>
            <button type="submit" class="btn btn-default">Cari</button>
          </form>
		  -->
          <ul class="nav navbar-nav navbar-right">
            <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown">Setting Peta <b class="caret"></b></a>
              <ul class="dropdown-menu">
                <li><a href="#">Filter Peta</a></li>
                <li class="divider"></li>
				  <li><a href="#" class="small" data-value="option1" tabIndex="-1"><input type="checkbox"/>&nbsp;Option 1</a></li>
				  <li><a href="#" class="small" data-value="option2" tabIndex="-1"><input type="checkbox"/>&nbsp;Option 2</a></li>
				  <li><a href="#" class="small" data-value="option3" tabIndex="-1"><input type="checkbox"/>&nbsp;Option 3</a></li>
				  <li><a href="#" class="small" data-value="option4" tabIndex="-1"><input type="checkbox"/>&nbsp;Option 4</a></li>
				  <li><a href="#" class="small" data-value="option5" tabIndex="-1"><input type="checkbox"/>&nbsp;Option 5</a></li>
				  <li><a href="#" class="small" data-value="option6" tabIndex="-1"><input type="checkbox"/>&nbsp;Option 6</a></li>				
              </ul>
            </li>
          </ul>
        </div>
      </div>
    </nav>  
    <div id="map-canvas"></div>  
  </body>
</html>