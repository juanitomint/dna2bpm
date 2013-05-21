Ext.application({
    name: 'FormGenias',
    init: function() {

    },
    launch: function() {
        var remove_loaders = function() {

            Ext.get('loading').remove();
            Ext.fly('loading-mask').remove();
        }

        var title = (navigator.onLine) ? "Informaci&oacute;n del Servidor" : "Informaci&oacute;n Local";

        center = Ext.create('Ext.panel.Panel', {
            
            region: 'center',
            layout: {
                type: 'hbox',
                pack: 'start',
                align: 'stretch'
            },
            items: [
                {
                    itemId: 'form',
                    xtype: 'writerform',
                    flex: 1,
                    //manageHeight: true,
                    //margins: '0 0 10 0',
                    listeners: {
                        create: function(form, data) {
                            store.insert(0, data);
                        }
                    }
                }
                ,
                {
                    itemId: 'grid',
                    xtype: 'writergrid',
                    title: title,
                    flex: 2,
                    store: store,
                    listeners: {
                        selectionchange: function(selModel, selected) {
                            center.child('#form').setActiveRecord(selected[0] || null);
                        }
                    }
                }]
        });


        //---CREATE VIEWPORT  

        Ext.create('Ext.Viewport', {
            layout: 'border',
            items: [center],
            listeners: {
                render: function() {
                },
                afterRender: function() {
                    remove_loaders();
                }

            }
        });
    }

});

