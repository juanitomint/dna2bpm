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
                height:300,
                items:[tokenGrid]
            }
            ,
            {
                //title: '<i class="icon icon-bpm"></i> Model Panel / Picker',
                title:'<h4><i class="icon icon-info-sign"></i> Token Viewer</h4>',
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
                    TokensStatus,
                    TokensHistory,
                    ]
                }
            }
            ]
        }
        );

        //---CREATE TOKENS VIEWPORT  
        
        Ext.create('Ext.Viewport', {
            layout:'border',
            items:[
            center,
            ]
            ,
            listeners:{
                render: function(){
                },
                afterRender: function(){
                    remove_loaders();
                    // load_data_callback=function(){center.setLoading(false);}
                    // load_model(globals.idwf);
                    tokens_load_status(globals.idwf,globals.idcase,tokens_paint_all);
                }
                    
            }
        });
    }
    
});

