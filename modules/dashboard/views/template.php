<!DOCTYPE html>
<head>
<title>Aplikasi Pendataan Rumah Tidak Laya Huni (RUTILAHU)</title>
<link rel="icon" href="<?=base_url()?>/assets/images/logo_sm_2_mr_1.png" type="image/png">
<meta HTTP-EQUIV="CACHE-CONTROL" CONTENT="NO-CACHE">
<meta HTTP-EQUIV="PRAGMA" CONTENT="NO-CACHE">
<!-- <script type="text/javascript" src="<?=base_url()?>resources/ext4/bootstrap.min.js"></script> -->
<script type="text/javascript" src="<?=base_url()?>resources/ext4/examples/shared/include-ext.js"></script>
<style>
    /*
    .x-body,.x-grid-cell,.x-tree-node-text,.x-accordion-hd .x-panel-header-text-container,
    .x-panel-header-text-container-default,.x-tab-default .x-tab-inner
    {font-family: calibri;font-size: 16px}
    */
</style>
<script type="text/javascript">
    
    Ext.require(['*']);

    Ext.onReady(function() {
        Ext.QuickTips.init();

        Ext.state.Manager.setProvider(Ext.create('Ext.state.CookieProvider'));
                
        var tabs = Ext.create('Ext.tab.Panel', {
            region: 'center', 
            id:'content-tab-panel',
            deferredRender: false,
            activeTab: 0,    
            margins: '1 0 0 0',
            items: [{
                contentEl: 'centerContent',
                title: 'HOME',
                id:'db',
                //closable: true,
                autoScroll: false,
                flex:1,
                html: '  <iframe src="<?=base_url()?>index.php/dashboard/Uptkru"'+
                      '           name="mainframe" width="100%" height="100%"'+
                      '           style="height: 100%; border: 0">'+
                      '  </iframe>'
            }]
        })
     
        <?php foreach ($menu_root as $mr): ?>
                
                // DATA TREE MENU
                var store<?=$mr->id?> = Ext.create('Ext.data.TreeStore', {
                    root: {
                        expanded: true
                    },
                    proxy: {
                        type: 'ajax',
                        url: '<?=base_url()?>index.php/dashboard/Mainindex/ShowMenu/<?=$mr->id?>'
                    }
                });
                
                // TREE MENU
                var treePanel<?=$mr->id?> = Ext.create('Ext.tree.Panel', {
                        id: 'tree-panel<?=$mr->id?>',
                        title: '<?=$mr->title?>',
                        region:'north',
                        split: true,
                        height: 300,
                        minSize: 150,
                        rootVisible: false,
                        autoScroll: true,
                        store: store<?=$mr->id?>,
                        listeners: {
                            itemclick: function(view,rec,item,index,eventObj) {  
                                //alert(rec.get('id'));
                                if(rec.get('id'))
                                {    
                                    var tabExist = false;
                                    var i=0;
                                    for(i=0; i < tabs.items.items.length; i++){
                                        if(tabs.items.items[i].id == rec.get('id')){
                                            tabExist = true;
                                            break;
                                        }
                                    }

                                    if(tabExist==false){
                                        tabs.add({
                                            title: rec.get('text'),
                                            id: rec.get('id'),
                                            flex:1,
                                            html: '<div id="dd" style="height:100%; width:100%; background:#ccc">'+
                                                  '  <iframe src="<?=base_url()?>index.php/dashboard/Mainindex/ShowTab/'+rec.get('id')+'"'+
                                                  '           width="100%" height="100%"'+
                                                  '           style="height: 100%; border: 0">'+
                                                  '    <p>Your browser does not support iframes.</p>'+
                                                  '  </iframe>'+     
                                                  '</div>',
                                            closable: true,
                                            id:rec.getId()
                                        }).show();
                                        //alert($('#dd').height());
                                    }
                                    else {
                                        tabs.setActiveTab(i);
                                    }
                                }
                            }
                        }
                });

            
                
                
        <?php endforeach; ?>        


        var viewport = Ext.create('Ext.Viewport', {
            id: 'border-example',
            layout: 'border',
            items: [tabs,
            Ext.create('Ext.Component', {
                region: 'north',
                height: 40,
                xtype:'panel',
                autoLoad:{url:'<?=base_url()?>index.php/dashboard/Mainindex/HeadTemplate',params:'foo=bar&wtf=2'}
            }), {
                region: 'south',
                height: 20,
                margins: '0 0 0 0',
                xtype:'panel',
                autoLoad:{url:'<?=base_url()?>index.php/dashboard/Mainindex/FooterTemplate',params:'foo=bar&wtf=2'}
            }, {
                region: 'west',
                stateId: 'navigation-panel',
                id: 'menu-sipoka', 
                title: 'Menu Utama',
                split: true,
                width: 250,
                minWidth: 175,
                maxWidth: 400,
                collapsible: true,
                animCollapse: true,
                margins: '1 0 0 0',
                layout: 'accordion',
                items: [ 
                <?php 
                   foreach ($menu_root as $mr){
                      echo "treePanel".$mr->id.","; 
                   }
                ?>
                ]
                
                
            }]
        });
        
    });
    </script>
</head>
<body>
    <div id="props-panel" class="x-hide-display" style="width:200px;height:200px;overflow:hidden;"></div>
    <div id="centerContent">
        <iframe src="" name="mainframe" width="100%"
                style="height: 100%; border: 0">
          <p>Your browser does not support iframes.</p>
        </iframe> 
    </div>
</body>
</html>