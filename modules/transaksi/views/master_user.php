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
	
    Ext.define('mdl_user', {
        extend: 'Ext.data.Model',
        fields: ['id','username','password','kel_user','nipp','nama','lokasi','keterangan','ip','active','job','tag_change_passwd'],
        idProperty: 'id'
    });
	
    var store_user = Ext.create('Ext.data.Store', {
        pageSize: 50,
        model: 'mdl_user',
        remoteSort: true,
        proxy: {
            url: '<?=base_url()?>index.php/transaksi/Master/get_user',
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
	
	var APcellEditing_m_user = Ext.create('Ext.grid.plugin.RowEditing', {
		//clicksToEdit: 1,
		clicksToMoveEditor: 1,
		autoCancel: false,
		listeners : {
			'edit' : function() {						
				var editedRecords = grid_m_user.getView().getSelectionModel().getSelection();
				Ext.Ajax.request({
					url: '<?=base_url();?>index.php/transaksi/Master/simpan_master_data/tbl_device/id/imei',
					method: 'POST',
					params: {
						'id': editedRecords[0].data.id,
						'username': editedRecords[0].data.username,
						'password': editedRecords[0].data.password,
						'ip': editedRecords[0].data.ip,
						'job': editedRecords[0].data.job,
						'nama': editedRecords[0].data.nama,
						'nipp': editedRecords[0].data.nipp,
						'active': editedRecords[0].data.active,
					},								
					success: function(response) {
						var text = response.responseText;
						Ext.MessageBox.alert('Status', response.responseText, function(btn,txt){
							if(btn == 'ok')
							{
								store_user.load();
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

var grid_m_user = Ext.create('Ext.grid.Panel', {
	store: store_user,
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
	plugins: [APcellEditing_m_user],
	columns:[
		{xtype: 'rownumberer', width: 35, sortable: false},
		{text: "id",dataIndex: 'id',sortable: false,},				
		{text: "username",dataIndex: 'username',flex: 1,sortable: false, editor: {xtype: 'textfield',allowBlank:false}},
		{text: "password",dataIndex: 'password',flex: 1,sortable: false, editor: {inputType: 'password',allowBlank:false}},
		{text: "kel_user",dataIndex: 'kel_user',flex: 1,sortable: false, editor: {xtype: 'textfield',allowBlank:false}},
		{text: "nipp",dataIndex: 'nipp',flex: 1,sortable: false, editor: {xtype: 'textfield',allowBlank:false}},
		{text: "nama",dataIndex: 'nama',flex: 1,sortable: false, editor: {xtype: 'textfield',allowBlank:false}},
		{text: "lokasi",dataIndex: 'lokasi',flex: 1,sortable: false, editor: {xtype: 'textfield',allowBlank:false}},
		{text: "keterangan",dataIndex: 'keterangan',flex: 1,sortable: false, editor: {xtype: 'textfield',allowBlank:false}},
		{text: "active",dataIndex: 'active',flex: 1,sortable: false, editor: {xtype: 'textfield',allowBlank:false}},
		{text: "ip",dataIndex: 'ip',flex: 1,sortable: false, editor: {xtype: 'textfield',allowBlank:false}},
		{text: "job",dataIndex: 'job',flex: 1,sortable: false, editor: {xtype: 'textfield',allowBlank:false}},
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
				var r = Ext.create('mdl_user', {
					username : '[NAMA USER]',
				});
				store_user.insert(0, r);
				APcellEditing_m_user.startEdit(0, 0);									
			}
		},
		{
			text:'Delete',
			iconCls: 'icon-del',
			handler: function() {          
				var records = grid_m_user.getView().getSelectionModel().getSelection(), id=[];
				Ext.Array.each(records, function(rec){
					id.push(rec.get('id'));
				});
				if(id != '')
				{
				Ext.MessageBox.confirm('Hapus', 'Apakah anda akan menghapus item ini (' + id.join(',') + ') ?',
				function(resbtn){
					if(resbtn == 'yes')
					{
						Ext.Ajax.request({
							url: '<?=base_url();?>index.php/transaksi/Master/master_delet/an_users/id',
							method: 'POST',											
							params: {												
								'id' : id.join(','),
							},								
							success: function(response) {
								Ext.MessageBox.alert('OK', response.responseText, function()
								{
									store_user.load();
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
				store_user.load();
			}
		},'->',
		{
			xtype: 'searchfield',
			remoteFilter: true,
			store: store_user,
			id: 'searchField',
			emptyText: 'IMEI / Nama Perangkat',
			width: '30%',
		},		
		]
	}],
   bbar: Ext.create('Ext.PagingToolbar',{
		store: store_user,
		displayInfo: true,
		displayMsg: 'Displaying Data : {0} - {1} of {2}',
		emptyMsg: "No Display Data"
	}),	
	listeners:{
		beforerender:function(){
			store_user.load();
		}
	}			
});
	
    Ext.create('Ext.container.Viewport', {
        layout: 'fit',
        items: [grid_m_user]
    });
	

});	

	</script>
</head>
<body>
    <div id="topic-grid"></div>	
</body>
</html>