<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Upload Data</title>
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
	
    Ext.define('mdl_penerima', {
        extend: 'Ext.data.Model',
        fields: ['id_penerima', 'kabupaten', 'tahun_terima', 'kecamatan', 'desa',
			'no_urut', 'namalengkap', 'jalan_desa', 'rt', 'rw', 'ktp', 'kk',
			'tempat_lahir','tgl_lahir','jenis_kelamin'],
        idProperty: 'id_penerima'
    });
	
    var store_penerima = Ext.create('Ext.data.Store', {
        pageSize: 50,
        model: 'mdl_penerima',
        remoteSort: true,
        proxy: {
            url: '<?=base_url()?>index.php/rutilahu/Main/get_data_upload',
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
	
var grid_m_penerima = Ext.create('Ext.grid.Panel', {
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
	columns:[
		{xtype: 'rownumberer', width: 35, sortable: false},
		{text: "id_penerima",dataIndex: 'id_penerima',width: 50,sortable: false,},				
		{text: "tahun_terima",dataIndex: 'tahun_terima',width: 50,sortable: false},
		{text: "namalengkap",dataIndex: 'namalengkap',width: 200,sortable: false},
		{text: "jenis_kelamin",dataIndex: 'jenis_kelamin',width: 70,sortable: false},
		{text: "rt",dataIndex: 'rt',flex: 1,sortable: false},
		{text: "rw",dataIndex: 'rw',flex: 1,sortable: false},
		{text: "jalan_desa",dataIndex: 'jalan_desa',width: 150,sortable: false},
		{text: "desa",dataIndex: 'desa',width: 200,sortable: false,},
		{text: "kecamatan",dataIndex: 'kecamatan',width: 200,sortable: false,},
		{text: "kabupaten",dataIndex: 'kabupaten',width: 200,sortable: false,},
		{text: "provinsi",dataIndex: 'provinsi',width: 250,sortable: false},
		{text: "ktp",dataIndex: 'ktp',width: 200,sortable: false},
		{text: "kk",dataIndex: 'kk',width: 200,sortable: false},
		{text: "tempat_lahir",dataIndex: 'tempat_lahir',flex: 1,sortable: false},
		{text: "tgl_lahir",dataIndex: 'tgl_lahir',flex: 1,sortable: false},
		{text: "kode_desa",dataIndex: 'kode_desa',width: 50,sortable: false},
		{text: "kode_kec",dataIndex: 'kode_kec',width: 50,sortable: false},
		{text: "kode_kab",dataIndex: 'kode_kab',width: 50,sortable: false},
		{text: "kode_prov",dataIndex: 'kode_prov',width: 50,sortable: false},
	],
	dockedItems: [
	{
		xtype: 'toolbar',
		dock: 'top',
		items: 
		[
		{
			text:'Posting Data',
			iconCls: 'icon-table',
			handler: function(){    
				Ext.Ajax.request({
					url: '<?=base_url();?>index.php/rutilahu/Main/posting_data_survey',
					method: 'POST',
					params: {
						'proses_posting_survey' : 1,
					},								
					success: function(response) {
						var text = response.responseText;
						if(parseInt(text.search('duplikat'))>0)
						{							
							Ext.MessageBox.confirm('Data Duplikat', response.responseText, function(btn,txt){
								if(btn == 'yes')
								{
									Ext.Ajax.request({
										url: '<?=base_url();?>index.php/rutilahu/Main/posting_data_duplikat',
										method: 'POST',
										params: {
											'proses_duplikat_data' : 1,
										},
										success: function(response) {
											Ext.MessageBox.alert('Status', response.responseText, function(btn,txt){
												if(btn == 'ok')
												{
													store_penerima.load();
												}
											});							
										}
									});
								}
							});							
						} else 
						{
							Ext.MessageBox.alert('Status', response.responseText, function(btn,txt){
								if(btn == 'ok')
								{
									store_penerima.load();
								}
							});							
						}
					},
					failure: function(response) {
						Ext.MessageBox.alert('Failure', 'Insert Data Error due to connection problem, or duplicate entries!');
					}
				});			
			}
		},
		{
			text:'Refresh',
			iconCls: 'icon-reload',
			handler: function(){          
				store_penerima.load();
			}
		},
		{
			text:'Kosongkan Data',
			iconCls: 'icon-del',
			handler: function(){    
				Ext.MessageBox.confirm('Konfirmasi penghapusan', 'Apakah anda akan menghapus data?', function(btn){
					if(btn == 'yes')
					{
						Ext.Ajax.request({
							url: '<?=base_url();?>index.php/rutilahu/Main/kosongkan_tbl_temp_upload',
							method: 'POST',
							params: {
								'proses' : 1,
							},								
							success: function(response) {
								Ext.MessageBox.alert('Status', response.responseText, function(btn,txt){
									if(btn == 'ok')
									{
										store_penerima.load();
									}
								});							
							}
						});						
					}					
				});
			}
		},'->',
		{
			xtype: 'searchfield',
			remoteFilter: true,
			store: store_penerima,
			id: 'searchField',
			emptyText: 'Nama Lengkap, KTP, KK, Jalan, Desa/Kelurahan, Kecamatan, Kabupaten',
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
	
        var frmUpload = Ext.widget({
            xtype: 'form',
            layout: 'form',
            url: '<?php echo base_url(); ?>index.php/rutilahu/Main/upload_rutilahu',
            frame: false,
            bodyPadding: '5 5 0',
            width: 350,
            fieldDefaults: {
                msgTarget: 'side',
                labelWidth: 75
            },
            items: [
                {
                    xtype: 'hidden',
                    name: 'id_proyek',
                    value: '1'
                },
                {
                    xtype: 'filefield',
                    emptyText: 'silahkan pilih file...',
                    fieldLabel: 'File',
                    name: 'upload_data',
                    buttonText: 'pilih file',
                    allowBlank: false
                },              
            ],

            buttons: [{
                text: 'Upload',
                handler: function(){            
                    var form = this.up('form').getForm();
                    if(form.isValid()){
                        form.submit({
                            enctype: 'multipart/form-data',
                            waitMsg: 'Upload Data hasil Survey ...',
                            success: function(fp, o) {
                                Ext.MessageBox.alert('Status','Upload file "'+ o.result.file + '" berhasil.', function()
                                {
                                    store_penerima.load();
                                });
                            },
                            failure: function(fp, o){                               
                                Ext.MessageBox.alert('Error','GAGAL Upload file "'+ o.result.file + '", pesan: '+o.result.message);
                            }
                        });
                    }
                }
            },
            {
                text: 'Cancel',
                handler: function() {
                }
            }]
        });
		
	new Ext.create('Ext.Viewport', {
		layout: 'border',
		items: [
		{
			region: 'north',
			stateId: 'navigation-panel',
			id: 'uploaddata', 
			title: 'Upload Data',
			height: '20%',
			margins: '1 0 0 0',
			layout: 'fit',
			split: true,
			items: [frmUpload]
		},		
		{
			region: 'south',
			stateId: 'navigation-panel',
			id: 'data-penerima', 
			title: 'Data Penerima',
			height: '80%',
			margins: '1 0 0 0',
			layout: 'fit',
			items: [grid_m_penerima],
		}]
	});

});	

	</script>
</head>
<body>
    <div id="upload"></div>	
</body>
</html>