/*
 *  Add events for overlays
 */
//Exclude lane 4
var add_events=function(shapes){
    for (j in shapes){
        shape=shapes[j];
        xBound=0;
        yBound=0;
        //console.log(shape.stencil.id,exclude_shape.indexOf(shape.stencil.id)==-1);
        xBound=shape.bounds.upperLeft.x+offset_x; //( -1 to fix nested objects
        yBound=shape.bounds.upperLeft.y+offset_y;                        
        width=shape.bounds.lowerRight.x-shape.bounds.upperLeft.x;
        height=shape.bounds.lowerRight.y-shape.bounds.upperLeft.y
        if(exclude_shape.indexOf(shape.stencil.id)==-1){//---if not excluded
            switch(shape.stencil.id){
                case 'Lane':
                    width=24;
                    break;
                 
            }
            Ext.core.DomHelper.append( 'svg-box',
            {
                tag:'div',
                cls:'model_overlay',
                id: 'overlay'+shape.resourceId,
                //html:shape.properties.documentation,
                style:' border-radius: 3px;\n\
                        position:absolute;\n\
                        left:'+xBound+'px;\n\
                        top:'+yBound+'px;\n\
                        width:'+width+'px;\n\
                        height:'+height+'px;\n\
\n\
'
            }
            );
            
            documentation=(shape.properties.documentation!=null) ? shape.properties.documentation:'';
            config={
                id:'toolTip'+shape.resourceId,
                target: 'overlay'+shape.resourceId,
                anchor: 'bottom',
                dismissDelay : 0,
                minWidth:320,
                //anchorOffset: 85, // center the anchor on the tooltip
                html: "<span class='resourceId'>resourceId:<br/>"+shape.resourceId+"</span><br/>Doc:"+documentation
            };
                
            Ext.create('Ext.tip.ToolTip', config);
            
            
            div=Ext.get('overlay'+shape.resourceId);
        
            div.on('click',function(event,target,options){
                resourceId=target.id.replace('overlay','');
                Ext.getCmp('toolTip'+resourceId).show();
                Ext.getCmp('toolTip'+resourceId).autoHide=false;
                
                if (window.clipboardData  && clipboardData.setData){
                    window.clipboardData.setData('text', resourceId);
                }
            });
                 
        }
        if(shape.childShapes.length){
            offset_x_orig=offset_x;
            offset_y_orig=offset_y;
            offset_x=xBound;
            offset_y=yBound;
                
            add_events(shape.childShapes);
            //---restore original valoues of offset
            offset_x=offset_x_orig;
            offset_y=offset_y_orig;
        }
    }
}