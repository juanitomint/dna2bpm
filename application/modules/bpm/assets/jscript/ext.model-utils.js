var model_data={};
var model_svg={};
var flat={};
var first=false;
var exclude_shape=new Array('SequenceFlow','Pool','MessageFlow');
var exclude_paint=new Array('Pool','Lane');
var TOKEN_SCROLL=true;
var load_data_callback=null;
/*
var offset_x=0;
var offset_y=0;
 */


function load_data(idwf){
    //--load model data
    if(!first){
        
        url=globals.base_url+'bpm/repository/load/model/'+idwf
        Ext.Ajax.request({
            // the url to the remote source
            url:url,
            disableCaching : false,
            method: 'GET',
            failure: function(response,options){
                alert('Error Loading:'+response.err);
            },
            success:function(response,options){
                var model_data=Ext.JSON.decode(response.responseText);
            
                center_panel=Ext.getCmp('modelPanel');
                svg_box=center_panel.body.dom;
                var model_svg=Ext.get(center_panel.body.dom.children[1].id);
            
                
                st1=model_svg.select('.stencils:first');
                obj=st1.elements[0];
                    
                xy=obj.getAttribute('transform').replace('translate(','').replace(')','').split(',');
                offset_x=parseInt( xy[0]);
                offset_y=parseInt(xy[1]);
                
                    
                /*
                    stencils=model_svg.select('.stencils')
                    stencils.each(function(item){
                        item.on('click',function(event,target,options){
                            event.stopEvent();
                            console.log(event,target,options);
                        });
                        
                    });
                 */
                add_events(model_data.childShapes);
                if(load_data_callback){
                    load_data_callback();
                } 
            }
        });
        first=true;    
    }
}
//---some functions
function load_model(idwf){
    first=false;
    //---only do something if its leaf=model
    center_panel=Ext.getCmp('modelPanel');
        
    options={
        url:globals.module_url+'repository/svg/'+idwf,
        success:function(){
            load_data(idwf);
            
        }
    }
    center_panel.body.load(options);
    
     
            
    
};
function add_lock(resourceId,data,style){
    if(style==null) style='default';
    div=Ext.get('overlay'+resourceId);
    if(div){
        div.update('<span class="extras bpm_lock badge badge-'+style+'"><i class="icon icon-lock"></i>'+data+'</span>');
    }
}
function add_badge(resourceId,data,style){
    if(style==null) style='default';
    div=Ext.get('overlay'+resourceId);
    if(div){
        div.update('<span class="extras bpm_badge badge badge-'+style+'">'+data+'</span>');
    }
}
function follow(resourceId){
    if(TOKEN_SCROLL){
        div=Ext.get('overlay'+resourceId);
        if(div){
            div.scrollIntoView(modelPanel.body,true,true);
        }
    }
}
function paint(resourceId,color,stroke_width){
    shape=flat[resourceId];
    stroke_with=(stroke_width==null)? 2:stroke_width;
    if(exclude_paint.indexOf(shape.stencil.id)==-1){//--don't paint excluded
        switch(shape.stencil.id){
            case "Task":
                $('#'+shape.resourceId+' .stencils .me [id*="_frame"]').attr('style','').attr('stroke-width',stroke_with).attr('stroke',color);
                break;
            case "Exclusive_Databased_Gateway":
                $('#'+shape.resourceId+' .stencils .me [id*="_frame"]').attr('style','').attr('stroke-width',stroke_with).attr('stroke',color);
                break;
            case "SequenceFlow":
                $('#'+shape.resourceId+' .stencils .me path').attr('stroke-width',stroke_with).attr('stroke',color);
                //---paint end point
                $('#'+shape.resourceId+'end path').attr('stroke',color).attr('fill',color);
                break;
            /*
        case 'EndNoneEvent':
            $('#'+shape.resourceId+'.stencils .me circle').attr('stroke-width',stroke_with).attr('stroke',color);
             $('#'+shape.resourceId+' .stencils .me [id*="_frame"]').attr('stroke-width',stroke_with).attr('stroke',color);
            break;
                     */
            default:
                $('#'+shape.resourceId+' .stencils .me [id*="_frame"]').attr('stroke-width',stroke_with).attr('stroke',color);
                break;
        }
    }
};
