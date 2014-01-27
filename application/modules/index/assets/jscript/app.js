Ext.onReady(function() {
    //---define components
//    var left = Ext.create('Ext.Panel',
//            {
//                region: 'west',
//                id: 'leftPanel', // see Ext.getCmp() below
//                title: 'Groups',
//                //                            title: 'West',
//                split: true,
//                width: 260,
//                minWidth: 200,
//                maxWidth: 700,
//                collapsible: true,
//                animCollapse: true,
//                margins: '0 0 0 0',
//                layout: 'fit'
//                        ,
//                items: [dataview]
//            }
//    );
    //----right
    var right = Ext.create('Ext.Panel',
            {
                region: 'east',
                id: 'rightPanel', // see Ext.getCmp() below
                title: 'Item Details',
                split: true,
                width: 370,
                minWidth: 0,
                maxWidth: 450,
                collapsible: true,
                margins: '0 0 0 0',
                layout: 'fit'
                        ,
                items: [propsGrid]
            }
    );
    var center = Ext.create('Ext.Panel',
            {
                region: 'center',
                id: 'centerPanel',
                layout: 'fit',
                margins: '0 0 0 0'
                        ,
                items: [
                    
                    tree
                ]
                
            });
    //---Create Application
    Ext.application({
        name: 'ProcessBrowser',
        launch: function() {
            Ext.create('Ext.container.Viewport', {
                layout: 'border',
                items: [center, right,
                    {
                        region: 'north',
                        title: '<h3><i class="icon icon-list"></i> Menú Manager</h3>',
                        cls: 'page_header'
                    },
                ],
                listeners: {
                    afterrender: function() {
                        //Ext.data.StoreManager.lookup('TreeStore').load();


                    }
                }
            });

        },
        onLaunch: function() {
        }
    });
    //---remove the loader
    Ext.get('loading').remove();
    Ext.fly('loading-mask').remove();


});