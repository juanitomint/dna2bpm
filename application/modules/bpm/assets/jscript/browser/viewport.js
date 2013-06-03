Ext.onReady(function(){
    //---define components
    var left=Ext.create('Ext.Panel',
    {
        region: 'west',
        id: 'leftPanel', // see Ext.getCmp() below
        title: 'Model Tree',
        //                            title: 'West',
        split: true,
        width: 360,
        minWidth: 300,
        maxWidth: 700,
        collapsible: true,
        animCollapse: true,
        margins: '0 0 0 0',
        layout: 'fit',
        items:[tree]
    }
    );
    var center=Ext.create('Ext.Panel',
    { 
        region:'center',
        id: 'centerPanel',
        layout: 'fit',
        margins: '0 0 0 0',
        autoScroll:true,
        items: [center_panel]
    });
    //---Create Application
    Ext.application({
        name: 'Model Browser',
        launch: function() {
            Ext.create('Ext.container.Viewport', {
                layout:'border',
                items:[ 
                /*
                {
                    region:'north',
                    title:'<h3 class="hidden-tablet hidden-phone"><i class="icon icon-bpm"></i> BPM Browser</h3>',
                    cls:'page_header',
                    collapsible:true
                },
                */
                left,
                center
                ],
                listeners: {

                    afterrender: function(){
                        //---Load Data

                        //Ext.data.StoreManager.lookup('GroupStore').load(); 
                        //Ext.data.StoreManager.lookup('UserStore').load(); 
                        Ext.data.StoreManager.lookup('TreeStore').load(); 


                    }
                }
            });

        },
        onLaunch: function(){
        }
    });
    //---remove the loader
    Ext.get('loading').remove();
    Ext.fly('loading-mask').remove();


});