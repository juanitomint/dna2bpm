var modelPanel= Ext.create('Ext.panel.Panel', {
    id:'modelPanel',
    autoScroll:true,
    listeners:{
//  render: load_model
}
});
        
Ext.application({
    name: 'AppEditor',
    init: function(){
        
    },
    launch:function(){
        var remove_loaders=function(){
            
            Ext.get('loading').remove();
            Ext.fly('loading-mask').remove();
            dgstore.load();
            
        }        
        
       //----collapse cases panel
      if(globals.idcase){
            cases_collapsed=true;
        } else {
            cases_collapsed=false;
        }
        var center = Ext.create('Ext.Panel', 
        {
            region:'center',
            margins:'0 0 0 0',
            layout:'border',
            items: [
            {
                region:'south',
                layout:'fit',
                title:'<i class="icon icon-time" ></i> Token History',
                collapsible: true,
                collapsed:true,
                resizable:true,
                animCollapse: false,
                height:300,
                items:[tokenGrid]
            }
            ,
            {
                //title: '<i class="icon icon-bpm"></i> Model Panel / Picker',
                title:'<h4><i class="icon icon-dashboard"></i> Case Manager</h4>',
                id:'ModelPanel',
                region:'center',
                layout:'fit',
                collapsible: true,
                collapsed:false,
                animCollapse: false,
                resizable:true,
                split: true,
                items:[modelPanel],
                tbar:{
                    id:'ModelPanelTbar',
                    disabled:true,
                    items:[
                    TokensPlay,
                    TokensStop,
                    TokensStepBackward,
                    TokensStepForward,
                    TokensTimeSlider,
                    TokensFolow,
                    TokensShowExtras,
                    TokensReload,
                    TokensPaintAll
                    ]
                }
            }
            ]
        }
        );

        var right=Ext.create('Ext.Panel',
        {
            id:'rightPanel',
            region: 'east',
            title:'Cases',
            animCollapse: true,
            collapsible:true,
            collapsed: cases_collapsed,
            animCollapse: false,
            split: true,
            width: 400, // give east and west regions a width
            minWidth: 300,
            maxWidth: 700,
            margins: '0 0 0 0',
            layout: 'fit',
            items: [mygrid]
                   
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

