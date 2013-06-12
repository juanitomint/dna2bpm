Ext.application({
    name: 'AppEditor',
    init: function(){
        
    },
    launch:function(){
        var remove_loaders=function(){
            Ext.get('loading').remove();
            Ext.fly('loading-mask').remove();
        }        
        var modelPanel= Ext.create('Ext.Panel', {
            id:'modelPanel',
            autoScroll:true,
            listeners:{
        //  render: load_model
        }
        });
        
        var left = Ext.create('Ext.Panel',
        {
            id:'leftPanel',
            title: 'Types',
            region:'west',
            xtype: 'panel',
            width: 300,
            collapsible:true,
            layout: 'fit',
            split: true,
            animCollapse: true,
            items:[dataview]
        });
        var center = Ext.create('Ext.Panel', 
        {
            region:'center',
            margins:'0 0 0 0',
            layout:'border',
            items: [
            {
                region:'center',
                layout:'fit',
                items:[mygrid]
            }
            ,
            {
                title: '<i class="icon icon-bpm"></i> Model Panel / Picker',
                region:'south',
                layout:'fit',
                collapsible: true,
                collapsed:true,
                animCollapse: false,
                resizable:true
                ,
                split: true,
                height:500,
                items:[modelPanel]
            }
            ]
        }
        );

        var right=Ext.create('Ext.Panel',
        {
            id:'rightPanel',
            region: 'east',
            title:'Properties',
            animCollapse: true,
            collapsible: true,
            animCollapse: false,
            split: true,
            width: 400, // give east and west regions a width
            minWidth: 300,
            maxWidth: 700,
            margins: '0 0 0 0',
            layout: 'fit',
            items: [
            ///-------Pgrid
            propsGrid
            ///-------Pgrid

            ]
                   
        }
        );
          
        //---CREATE VIEWPORT  
        Ext.create('Ext.Viewport', {
            layout:'border',
            items:[
            /*
            {
                region:'north',
                title:'<h3><i class="icon icon-dashboard"></i> Key Process/Performance Indicators Editor</h3>',
                cls:'page_header'
            },
            */
            center,
            left,
            right
            ]
            ,
            listeners:{
                render: function(){
                },
                afterRender: function(){
                    remove_loaders();
                    load_model(globals.idwf);

                }
                    
            }
        });
    }
    
});

