<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Master Desa</title>
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
	
    Ext.define('mdl_peta', {
        extend: 'Ext.data.Model',
        fields: ['titik_tengah_long', 'titik_tengah_lat'],
        //idProperty: 'id_device'
    });
	
    var store_tt = Ext.create('Ext.data.Store', {
        pageSize: 50,
        model: 'mdl_peta',
        remoteSort: true,
        proxy: {
            url: '<?=base_url()?>index.php/transaksi/Master/get_titiktengah',
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
            property: 'id_kelurahan',
            direction: 'DESC'
        }]
    });
	
	var APcellEditing_m_dev = Ext.create('Ext.grid.plugin.RowEditing', {
		//clicksToEdit: 1,
		clicksToMoveEditor: 1,
		autoCancel: false,
		listeners : {
			'edit' : function() {						
				var editedRecords = grid_m_tt.getView().getSelectionModel().getSelection();
				Ext.Ajax.request({
					url: '<?=base_url();?>index.php/transaksi/Master/simpan_master_data/tbl_peta_titik_tengah/titik_tengah_lat',
					method: 'POST',
					params: {
						'titik_tengah_lat': editedRecords[0].data.titik_tengah_lat,
						'titik_tengah_long': editedRecords[0].data.titik_tengah_long,
					},								
					success: function(response) {
						var text = response.responseText;
						Ext.MessageBox.alert('Status', response.responseText, function(btn,txt){
							if(btn == 'ok')
							{
								store_tt.load();
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

var grid_m_tt = Ext.create('Ext.grid.Panel', {
	store: store_tt,
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
	plugins: [APcellEditing_m_dev],
	columns:[
		{xtype: 'rownumberer', width: 35, sortable: false},
		{text: "Latitude",dataIndex: 'titik_tengah_lat',flex: 1, sortable: false,editor: {xtype: 'textfield',allowBlank:false}},				
		{text: "Longitude",dataIndex: 'titik_tengah_long',flex: 1,sortable: false, editor: {xtype: 'textfield',allowBlank:false}},
	],
	dockedItems: [
	{
		xtype: 'toolbar',
		dock: 'top',
		items: 
		[
		{
			text:'Refresh',
			iconCls: 'icon-reload',
			handler: function(){          
				store_tt.load();
			}
		}
		]
	}],
   bbar: Ext.create('Ext.PagingToolbar',{
		store: store_tt,
		displayInfo: true,
		displayMsg: 'Displaying Data : {0} - {1} of {2}',
		emptyMsg: "No Display Data"
	}),	
	listeners:{
		beforerender:function(){
			store_tt.load();
		}
	}			
});
	
    Ext.create('Ext.container.Viewport', {
        layout: 'fit',
        items: [grid_m_tt]
    });
	

});	

	</script>
</head>
<body>
    <div id="topic-grid"></div>	
</body>
</html>