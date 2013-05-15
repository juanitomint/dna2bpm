var remove_loaders=function(){
    Ext.get('loading').remove();
    Ext.fly('loading-mask').remove();
}



Ext.application({
    name: 'AppEditor',
    newObject: newObject,
    init: function(){
        
    },
    launch:function(){
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
                title: "<img align='top' src='"+globals.base_url+"css/ext_icons/preview-hide.gif'/> All available Objects",
                region:'south',
                layout:'fit',
                collapsible: true,
                collapsed:true,
                animCollapse: false,
                resizable:false,
                split: true,
                height:301,
                items:[othergrid]
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
            maxWidth: 400,
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
            center,
            right
            ]
            ,
            listeners:{
                render: remove_loaders
            }
        });
    }
    
});

