<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Master Data Penerima</title>
<script type="text/javascript" src="<?=base_url()?>resources/ext4/examples/shared/include-ext.js"></script>
<style>
.icon-print-preview { background-image:url(<?=base_url(); ?>assets/images/txt.png) !important; }
.icon-print { background-image:url(<?=base_url(); ?>assets/images/print.png) !important; }
.icon-reload { background-image:url(<?=base_url(); ?>assets/images/reload.png) !important; }
.icon-print-pdf { background-image:url(<?=base_url(); ?>assets/images/pdf.png) !important; }
.icon-print-xls { background-image:url(<?=base_url(); ?>assets/images/xls.png) !important; }
.tabs { background-image:url(<?=base_url(); ?>assets/images/tabs.gif ) !important; }
.icon-add { background-image:url(<?=base_url(); ?>assets/images/add.png) !important; }
.icon-del { background-image:url(<?=base_url(); ?>assets/images/delete.png) !important; }
</style>

<script type="text/javascript">
Ext.Loader.setConfig({enabled: true});

Ext.Loader.setPath('Ext.ux', '<?=base_url()?>resources/ext4/examples/ux/');
Ext.require([
    '*',
    'Ext.grid.*',
    'Ext.data.*',
    'Ext.util.*',
    'Ext.toolbar.Paging',
    'Ext.ux.PreviewPlugin',
    'Ext.ux.DataTip',	
    'Ext.ModelManager',	
	'Ext.ux.form.SearchField',
    'Ext.menu.*',
    'Ext.tip.QuickTipManager',
    'Ext.container.ButtonGroup'
]);

Ext.onReady(function(){
		
    Ext.tip.QuickTipManager.init();
	
    var required = '<span style="color:red;font-weight:bold" data-qtip="Required">*</span>';

	var store_tahun = Ext.create('Ext.data.Store', {
		pageSize: 200,
		fields: ['tahun', 'id_tahun'],
		remoteSort: true,
		proxy: {
			url: '<?=base_url()?>index.php/rutilahu/Main/get_tahun',
			simpleSortMode: true,
			type: 'ajax',
			reader: {
				type: 'json',
				root: 'data'
			}			
		},
		baseParams: {
			limit: 200,
		},		
		sorters: [{
			direction: 'DESC'
		}],
		autoLoad: true
	});

	var pilih_tahun = Ext.create('Ext.form.ComboBox', {
		flex:1,
		store: store_tahun,
		//queryMode: 'local',	
		minChars: 2,
		triggerAction : 'all',                  
		anchor: '100%',
		displayField: 'tahun',
		valueField: 'id_tahun',
		listeners: {
			'select': function(combo, row, index) {
			}
		},
	});
	
	var store_kab = Ext.create('Ext.data.Store', {
		pageSize: 200,
		fields: ['kode_kab', 'kabupaten'],
		remoteSort: true,
		proxy: {
			url: '<?=base_url()?>index.php/rutilahu/Main/get_kabupaten',
			simpleSortMode: true,
			type: 'ajax',
			reader: {
				type: 'json',
				root: 'data'
			}			
		},
		baseParams: {
			limit: 200,
		},		
		sorters: [{
			direction: 'DESC'
		}],
		autoLoad: true
	});

	var pilih_kab = Ext.create('Ext.form.ComboBox', {
		flex:1,
		store: store_kab,
		//queryMode: 'local',	
		minChars: 2,
		triggerAction : 'all',                  
		anchor: '100%',
		displayField: 'kabupaten',
		valueField: 'kode_kab',
		listeners: {
			'select': function(combo, row, index) {
			}
		},
	});
	
    Ext.define('mdl_penerima', {
        extend: 'Ext.data.Model',			
        fields: ['id_penerima', 'tahun_terima', 'provinsi', 'kabupaten', 'kecamatan', 'desa', 'no_urut',
	'namalengkap', 'jalan_desa', 'rt', 'rw', 'ktp', 'kk', 'latitude', 'longitude', 'img_foto_penerima', 
	'img_tampak_samping_1', 'img_tampak_samping_2', 'img_tampak_belakang', 'img_tampak_dapur', 
	'img_tampak_jamban', 'img_tampak_sumber_air', 'keterangan', 'kode_desa', 'kode_kec', 'kode_kab', 
	'is_catat', 'tgl_update', 'tempat_lahir', 'tgl_lahir', 'jenis_kelamin', 'devid', 'img_tampak_depan_rumah'],
        idProperty: 'id_penerima'
    });
		
    var store_penerima = Ext.create('Ext.data.Store', {
        pageSize: 50,
        model: 'mdl_penerima',
        remoteSort: true,
        proxy: {
            url: '<?=base_url()?>index.php/rutilahu/Main/get_data_penerima',
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
        }]
    });
	
	var APcellEditing_m_penerima = Ext.create('Ext.grid.plugin.RowEditing', {
		//clicksToEdit: 1,
		clicksToMoveEditor: 1,
		autoCancel: false,
		listeners : {
			'edit' : function() {						
				var editedRecords = grid_m_penerima.getView().getSelectionModel().getSelection();
				Ext.Ajax.request({
					url: '<?=base_url();?>index.php/transaksi/Master/simpan_master_data/tbl_temp_penerima/id_penerima/namalengkap',
					method: 'POST',
					params: {
						'id_penerima': editedRecords[0].data.id_penerima,
						'namalengkap': editedRecords[0].data.namalengkap,
						'rt': editedRecords[0].data.rt,
						'rw': editedRecords[0].data.rw,
						'jalan_desa': editedRecords[0].data.jalan_desa,
						'tahun_terima': editedRecords[0].data.tahun_terima,
						'kode_desa': editedRecords[0].data.kode_desa,
						'kode_kec': editedRecords[0].data.kode_kec,
						'kode_kab': editedRecords[0].data.kode_kab,
						'kode_prov': editedRecords[0].data.kode_prov,
						'ktp': editedRecords[0].data.ktp,
						'kk': editedRecords[0].data.kk,
						'tempat_lahir': editedRecords[0].data.tempat_lahir,
						'tgl_lahir': editedRecords[0].data.tgl_lahir,
						'jenis_kelamin': editedRecords[0].data.jenis_kelamin,						
					},								
					success: function(response) {
						var text = response.responseText;
						Ext.MessageBox.alert('Status', response.responseText, function(btn,txt){
							if(btn == 'ok')
							{
								store_penerima.load();
							}
						}
						);
					},
					failure: function(response) {
						Ext.MessageBox.alert('Failure', 'Insert Data Error due to connection problem!');
					}
				});
			}
		}
	});			

var grid_m_penerima = Ext.create('Ext.grid.Panel', {
	title: 'Data Hasil Survey',
	store: store_penerima,
	disableSelection: false,
	loadMask: true,
	selModel: Ext.create('Ext.selection.CheckboxModel', {
		mode: 'MULTI', 
		multiSelect: true,
		keepExisting: true,
	}),
	viewConfig: {
		trackOver: true,
		stripeRows: true,
	},
	plugins: [APcellEditing_m_penerima],	
	columns:[
		{xtype: 'rownumberer', width: 35, sortable: false},
		{text: "id_penerima",dataIndex: 'id_penerima',width: 50,sortable: false,},				
		{text: "tahun_terima",dataIndex: 'tahun_terima',width: 50,sortable: false, editor: {xtype: 'textfield',allowBlank:false}},
		{text: "namalengkap",dataIndex: 'namalengkap',width: 200,sortable: false, editor: {xtype: 'textfield',allowBlank:false}},
		{text: "jenis_kelamin",dataIndex: 'jenis_kelamin',width: 70,sortable: false, editor: {xtype: 'textfield',allowBlank:false}},
		{text: "rt",dataIndex: 'rt',flex: 1,sortable: false, editor: {xtype: 'textfield',allowBlank:false}},
		{text: "rw",dataIndex: 'rw',flex: 1,sortable: false,editor: {xtype: 'textfield',allowBlank:false}},
		{text: "jalan_desa",dataIndex: 'jalan_desa',width: 150,sortable: false,editor: {xtype: 'textfield',allowBlank:false}},
		{text: "desa",dataIndex: 'desa',width: 200,sortable: false,},
		{text: "kecamatan",dataIndex: 'kecamatan',width: 200,sortable: false,},
		{text: "kabupaten",dataIndex: 'kabupaten',width: 200,sortable: false,},
		{text: "provinsi",dataIndex: 'provinsi',width: 250,sortable: false,},
		{text: "ktp",dataIndex: 'ktp',width: 200,sortable: false, editor: {xtype: 'textfield',allowBlank:false}},
		{text: "kk",dataIndex: 'kk',width: 200,sortable: false, editor: {xtype: 'textfield',allowBlank:false}},
		{text: "tempat_lahir",dataIndex: 'tempat_lahir',flex: 1,sortable: false, editor: {xtype: 'textfield',allowBlank:false}},
		{text: "tgl_lahir",dataIndex: 'tgl_lahir',flex: 1,sortable: false, editor: {xtype: 'textfield',allowBlank:false}},
		{text: "kode_desa",dataIndex: 'kode_desa',width: 50,sortable: false, editor: {xtype: 'textfield',allowBlank:false}},
		{text: "kode_kec",dataIndex: 'kode_kec',width: 50,sortable: false, editor: {xtype: 'textfield',allowBlank:false}},
		{text: "kode_kab",dataIndex: 'kode_kab',width: 50,sortable: false, editor: {xtype: 'textfield',allowBlank:false}},
		{text: "kode_prov",dataIndex: 'kode_prov',width: 50,sortable: false, editor: {xtype: 'textfield',allowBlank:false}},
	],
	dockedItems: [
	{
		xtype: 'toolbar',
		dock: 'top',
		items: 
		[
		/*
		{
			text:'Tambah Data',
			iconCls: 'icon-add',
			handler: function(){          
				var r = Ext.create('mdl_penerima', {
					namalengkap : '[NAMALENGKAP]',
					rt : '[RT]',
					rw : '[RW]',
					jalan_desa : '[JALANDESA]',
				});
				store_penerima.insert(0, r);
				APcellEditing_m_penerima.startEdit(0, 0);									
			}
		},
		{
			text:'Delete',
			iconCls: 'icon-del',
			handler: function() {          
				var records = grid_m_penerima.getView().getSelectionModel().getSelection(), id=[];
				Ext.Array.each(records, function(rec){
					id.push(rec.get('id_penerima'));
				});
				if(id != '')
				{
				Ext.MessageBox.confirm('Hapus', 'Apakah anda akan menghapus item ini (' + id.join(',') + ') ?',
				function(resbtn){
					if(resbtn == 'yes')
					{
						Ext.Ajax.request({
							url: '<?=base_url();?>index.php/transaksi/Master/master_del/tbl_temp_penerima/id_penerima',
							method: 'POST',											
							params: {												
								'id_penerima' : id.join(','),
							},								
							success: function(response) {
								Ext.MessageBox.alert('OK', response.responseText, function()
								{
									store_penerima.load();
								});
							},
							failure: function(response) {
								Ext.MessageBox.alert('Failure', 'Insert Data Error due to connection problem, or duplicate entries!');
							}
						});			   	
					} else 
					{
						Ext.MessageBox.alert('Error', 'Silahkan pilih item yang mau dihapus!');
					}																		
				});
				} else 
				{
					Ext.MessageBox.alert('Error', 'Silahkan pilih item yang mau dihapus!');
				}
			}
		},
		*/		
		{
			text:'Refresh',
			iconCls: 'icon-reload',
			handler: function(){          
				store_penerima.load();
			}
		},'-','Kabupaten',
			pilih_kab,
		'Tahun',pilih_tahun,
		'->',		
		{
			xtype: 'searchfield',
			remoteFilter: true,
			store: store_penerima,
			id: 'searchField',
			emptyText: 'Nama Lengkap, KTP, KK, Jalan, Tahun Terima, Desa/Kelurahan, Kecamatan, Kabupaten',
			width: '50%',
		},		
		]
	}],
   bbar: Ext.create('Ext.PagingToolbar',{
		store: store_penerima,
		displayInfo: true,
		displayMsg: 'Displaying Data : {0} - {1} of {2}',
		emptyMsg: "No Display Data"
	}),	
	listeners:{
		beforerender:function(){
			store_penerima.load();
		}
	}			
});
	
    Ext.create('Ext.container.Viewport', {
        layout: 'fit',
        items: [grid_m_penerima]
    });
	

});	

	</script>
</head>
<body>
    <div id="penerima"></div>	
</body>
</html>