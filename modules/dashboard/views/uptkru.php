<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Dashboard</title>
<script type="text/javascript" src="<?=base_url()?>resources/ext4/examples/shared/include-ext.js"></script>
<style>
.icon-print-preview { background-image:url(<?=base_url(); ?>assets/images/txt.png) !important; }
.icon-print { background-image:url(<?=base_url(); ?>assets/images/print.png) !important; }
.icon-reload { background-image:url(<?=base_url(); ?>assets/images/reload.png) !important; }
.icon-print-pdf { background-image:url(<?=base_url(); ?>assets/images/pdf.png) !important; }
.icon-print-xls { background-image:url(<?=base_url(); ?>assets/images/xls.png) !important; }
.tabs { background-image:url(<?=base_url(); ?>assets/images/tabs.gif ) !important; }
.icon-add { background-image:url(<?=base_url(); ?>assets/images/add.png) !important; }
.icon-table { background-image:url(<?=base_url(); ?>assets/images/table.png) !important; }
.icon-del { background-image:url(<?=base_url(); ?>assets/images/delete.png) !important; }
</style>

<script type="text/javascript">
Ext.Loader.setConfig({enabled: true});

Ext.Loader.setPath('Ext.ux', '<?=base_url()?>resources/ext4/examples/ux/');
Ext.require([
	'*',
	'Ext.chart.*',
    'Ext.data.*',
    'Ext.util.*',
    'Ext.toolbar.Paging',
    'Ext.ModelManager',	
    'Ext.menu.*',
    'Ext.tip.QuickTipManager',
]);

Ext.onReady(function(){
		
    Ext.tip.QuickTipManager.init();
		
	var store1 = new Ext.data.JsonStore({
		fields : ['kecamatan', 'totdata'],
		autoLoad:true, 
		proxy: {
			type: 'ajax',
			url: '<?=base_url()?>index.php/rutilahu/Main/get_chart1',
			reader: {
				type: 'json',
				root: ''
			}
		}
    });

	var store2 = new Ext.data.JsonStore({
		fields : ['kabupaten', 'totdata'],
		autoLoad:true, 
		proxy: {
			type: 'ajax',
			url: '<?=base_url()?>index.php/rutilahu/Main/get_chart2',
			reader: {
				type: 'json',
				root: ''
			}
		}
    });
	

	var store3 = new Ext.data.JsonStore({
		fields : ['kabupaten', 'tahun_terima', 'totdata'],
		autoLoad:true, 
		proxy: {
			type: 'ajax',
			url: '<?=base_url()?>index.php/rutilahu/Main/get_chart3',
			reader: {
				type: 'json',
				root: ''
			}
		}
    });
	
    var chart1 = Ext.create('Ext.chart.Chart', {
            animate: true,
            shadow: true,
            store: store1,
            axes: [{
                type: 'Numeric',
                position: 'left',
                fields: ['totdata'],
                title: 'Jml Penerima',
                grid: true,
                minimum: 0,
                //maximum: 1000000
            }, {
                type: 'Category',
                position: 'bottom',
                fields: ['kecamatan'],
                title: 'Kecamatan',
                label: {
                    rotate: {
                        degrees: 270
                    }
                }
            }],
            series: [{
                type: 'column',
                axis: 'left',
                gutter: 80,
                xField: 'kecamatan',
                yField: ['totdata'],
                tips: {
                    trackMouse: true,
                    width: 200,
                    height: 38,
                    renderer: function(storeItem, item) {
                        this.setTitle(storeItem.get('kecamatan'));
                        this.update(storeItem.get('totdata'));
                    }
                },
                style: {
                    fill: '#38B8BF'
                }
            }]
        });

    var chart2 = Ext.create('Ext.chart.Chart', {
            animate: true,
            shadow: true,
            store: store2,
			/*
            legend: {
              position: 'right'  
            },
			*/
            axes: [{
                type: 'Numeric',
                position: 'bottom',
                fields: ['totdata'],
                minimum: 0,
                label: {
                    renderer: Ext.util.Format.numberRenderer('0,0')
                },
                grid: true,
                title: 'Jml Penerima Bantuan RTLH'
            }, {
                type: 'Category',
                position: 'left',
                fields: ['kabupaten'],
                title: 'Kabupaten'
            }],
            series: [{
                type: 'bar',
                axis: 'bottom',
                xField: 'kabupaten',
                yField: ['totdata'],
                tips: {
                    trackMouse: true,
                    width: 200,
                    height: 38,
                    renderer: function(storeItem, item) {
                        this.setTitle(storeItem.get('kabupaten'));
                        this.update(storeItem.get('totdata'));
                    }
                },
                style: {
                    fill: '#ccffcc'
                }				
            }]			
        });
		
    var chart3 = Ext.create('Ext.chart.Chart', {
		style: 'background:#fff',
		animate: true,
		shadow: true,
		store: store3,
		axes: [{
			type: 'Numeric',
			position: 'bottom',
			fields: ['totdata'],
			minimum: 0,
			label: {
				renderer: Ext.util.Format.numberRenderer('0,0')
			},
			grid: true,
			title: 'Jumlah Penerima Bantuan'
		}, {
			type: 'Category',
			position: 'left',
			fields: ['kabupaten'],
			title: 'Kabupaten'
		}],
		series: [{
			type: 'bar',
			axis: 'bottom',
			xField: 'totdata',
			yField: ['totdata'],
			tips: {
				trackMouse: true,
				width: 250,
				height: 38,
				renderer: function(storeItem, item) {
					this.setTitle(storeItem.get('kabupaten') + ' / '+ storeItem.get('tahun_terima'));
					this.update(storeItem.get('totdata'));
				}
			},
			style: {
				fill: '#ccffcc'
			}
		}]
	});
		
    var panel1 = Ext.create('widget.panel', {
        renderTo: Ext.getBody(),
		height: '100%',
        layout: 'fit',
        tbar: [{
            text: 'Export Grafik',
            handler: function() {
                Ext.MessageBox.confirm('Download', 'Grafik akan disimpan sbg gambar, pastikan anda terhubung dg internet.', function(choice){
                    if(choice == 'yes'){
                        chart1.save({
                            type: 'image/png'
                        });
                    }
                });
            }
        }, {
            text: 'Reload Data',
            handler: function() {
				store1.load();
            }
        }],
        items: [chart1]
    });

    var panel2 = Ext.create('widget.panel', {
        renderTo: Ext.getBody(),
		height: '100%',
        layout: 'fit',
        tbar: [{
            text: 'Export Grafik',
            handler: function() {
                Ext.MessageBox.confirm('Download', 'Grafik akan disimpan sbg gambar, pastikan anda terhubung dg internet.', function(choice){
                    if(choice == 'yes'){
                        chart2.save({
                            type: 'image/png'
                        });
                    }
                });
            }
        }, {
            text: 'Reload Data',
            handler: function() {
				store2.load();
            }
        }],
        items: [chart2]
    });

    var panel3 = Ext.create('widget.panel', {
        renderTo: Ext.getBody(),
		height: '100%',
        layout: 'fit',
        tbar: [{
            text: 'Export Grafik',
            handler: function() {
                Ext.MessageBox.confirm('Download', 'Grafik akan disimpan sbg gambar, pastikan anda terhubung dg internet.', function(choice){
                    if(choice == 'yes'){
                        chart2.save({
                            type: 'image/png'
                        });
                    }
                });
            }
        }, {
            text: 'Reload Data',
            handler: function() {
				store3.load();
            }
        }],
        items: [chart3]
    });
	
	new Ext.create('Ext.Viewport', {
		layout: 'border',
		items: [
		{
			region: 'north',
			stateId: 'navigation-panel',
			id: 'ctop', 
			title: 'Jumlah Penerima Bantuan RTLH - per Kecamatan',
			height: '70%',
			margins: '1 0 0 0',
			layout: 'fit',
			split: true,
			items: [panel1]
		},		
		{
			region: 'south',
			stateId: 'navigation-panel',
			id: 'cbot', 
			//title: 'Grafik Bawah',
			height: '50%',
			margins: '1 0 0 0',
			layout: 'border',
			items: [
			{
				region: 'west',
				stateId: 'navigation-panel',
				id: 'ckiri', 
				title: 'Grafik Jumlah Penerima Bantuan RTLH Per Kabupaten',
				height: '100%',
				width: '50%',
				split: true,				
				margins: '1 0 0 0',
				layout: 'fit',
				items: [panel2]
			},
			{
				region: 'east',
				stateId: 'navigation-panel',
				id: 'ckanan', 
				title: 'Grafik Jumlah Penerima Bantuan RTLH per Tahun',
				width: '50%',
				height: '100%',
				margins: '1 0 0 0',
				layout: 'fit',		
				items: [panel3]
			}
			],
		}]
	});

});	

	</script>
</head>
<body>
    <div id="chart"></div>	
</body>
</html>