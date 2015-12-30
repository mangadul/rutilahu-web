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
	
    Ext.define('mdl_zona', {
        extend: 'Ext.data.Model',
        fields: ['id_zona','zona','jarak_meter','ket'],
        idProperty: 'id_zona'
    });
	
    var store_zona = Ext.create('Ext.data.Store', {
        pageSize: 50,
        model: 'mdl_zona',
        remoteSort: true,
        proxy: {
            url: '<?=base_url()?>index.php/transaksi/Master/get_zona',
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
            property: 'id_zona',
            direction: 'DESC'
        }]
    });
	
	var APcellEditing_m_zona = Ext.create('Ext.grid.plugin.RowEditing', {
		//clicksToEdit: 1,
		clicksToMoveEditor: 1,
		autoCancel: false,
		listeners : {
			'edit' : function() {						
				var editedRecords = grid_m_zona.getView().getSelectionModel().getSelection();
				Ext.Ajax.request({
					url: '<?=base_url();?>index.php/transaksi/Master/simpan_master_data/tbl_peta_zonasi/id_zona/zona',
					method: 'POST',
					params: {
						'id_zona': editedRecords[0].data.id_zona,
						'zona': editedRecords[0].data.zona,
						'jarak_meter': editedRecords[0].data.jarak_meter,
						'ket': editedRecords[0].data.ket,
					},								
					success: function(response) {
						var text = response.responseText;
						Ext.MessageBox.alert('Status', response.responseText, function(btn,txt){
							if(btn == 'ok')
							{
								store_zona.load();
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

var grid_m_zona = Ext.create('Ext.grid.Panel', {
	store: store_zona,
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
	plugins: [APcellEditing_m_zona],
	columns:[
		{xtype: 'rownumberer', width: 35, sortable: false},
		{text: "id_zona",dataIndex: 'id_zona',sortable: false,},				
		{text: "zona",dataIndex: 'zona',flex: 1,sortable: false, editor: {xtype: 'textfield',allowBlank:false}},
		{text: "jarak_meter",dataIndex: 'jarak_meter',flex: 1,sortable: false, editor: {xtype: 'textfield',allowBlank:false}},
		{text: "ket",dataIndex: 'ket',flex: 1,sortable: false, editor: {xtype: 'textfield',allowBlank:false}},
	],
	dockedItems: [
	{
		xtype: 'toolbar',
		dock: 'top',
		items: 
		[
		{
			text:'Tambah Data',
			iconCls: 'icon-add',
			handler: function(){          
				var r = Ext.create('mdl_zona', {
					zona : '[ZONA]',
				});
				store_zona.insert(0, r);
				APcellEditing_m_zona.startEdit(0, 0);									
			}
		},
		{
			text:'Delete',
			iconCls: 'icon-del',
			handler: function() {          
				var records = grid_m_zona.getView().getSelectionModel().getSelection(), id=[];
				Ext.Array.each(records, function(rec){
					id.push(rec.get('id_zona'));
				});
				if(id != '')
				{
				Ext.MessageBox.confirm('Hapus', 'Apakah anda akan menghapus item ini (' + id.join(',') + ') ?',
				function(resbtn){
					if(resbtn == 'yes')
					{
						Ext.Ajax.request({
							url: '<?=base_url();?>index.php/transaksi/Master/master_delet/tbl_peta_zonasi/id_zona',
							method: 'POST',											
							params: {												
								'id' : id.join(','),
							},								
							success: function(response) {
								Ext.MessageBox.alert('OK', response.responseText, function()
								{
									store_zona.load();
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
		{
			text:'Refresh',
			iconCls: 'icon-reload',
			handler: function(){          
				store_zona.load();
			}
		},'->',
		{
			xtype: 'searchfield',
			remoteFilter: true,
			store: store_zona,
			id: 'searchField',
			emptyText: 'Zona / Ket / Jarak',
			width: '30%',
		},		
		]
	}],
   bbar: Ext.create('Ext.PagingToolbar',{
		store: store_zona,
		displayInfo: true,
		displayMsg: 'Displaying Data : {0} - {1} of {2}',
		emptyMsg: "No Display Data"
	}),	
	listeners:{
		beforerender:function(){
			store_zona.load();
		}
	}			
});
	
    Ext.create('Ext.container.Viewport', {
        layout: 'fit',
        items: [grid_m_zona]
    });
	

});	

	</script>
</head>
<body>
    <div id="topic-grid"></div>	
</body>
</html>