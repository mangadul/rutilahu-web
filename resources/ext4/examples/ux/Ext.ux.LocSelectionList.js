Ext.ns('Ext.ux');

Ext.define('Ext.ux.LocSelectionList', {
	extend: 'Ext.DataView',
	alias: 'locselectionlist',    
		listItemTpl: '<tpl for="."><div class="ux-locitem-selector"><div>{title}</div></div></tpl>',
		initComponent: function(){				
				this.liTpl = new Ext.XTemplate(this.listItemTpl);				
				Ext.applyIf(this, {
						store: new Ext.data.JsonStore({
				fields: ['lat','lng','title'],
						root: 'Data',
				data: {
					Data: this.locations
				}
			}),
			tpl: this.liTpl,
			multiSelect: false,
			itemSelector: 'div.ux-locitem-selector',
			overClass:'ux-locitem-selector-over',
			emptyText: '',
			deferEmptyText: false
				});
				
				this.on('click', function(t,i){
						var map = Ext.getCmp(this.mapTargetCmpId);
			var rec = t.store.getAt(i);
						var point = map.fixLatLng(new google.maps.LatLng(rec.data.lat, rec.data.lng));
						map.getMap().setCenter(point);
		}, this);
				
				Ext.ux.LocSelectionList.superclass.initComponent.call(this);
				
		}			
});