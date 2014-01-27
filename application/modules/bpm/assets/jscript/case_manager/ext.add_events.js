/*
 *  Add events for overlays
 */
var add_events=function(shapes){
    for (j in shapes){
        var shape=shapes[j];
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
                //html:shape.properties.name,
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
            div=Ext.get('overlay'+shape.resourceId);
            /*
            div.on('mouseover',function(event,target,options){
                this.dom.style.setProperty('background-color','green');
                this.dom.style.setProperty('opacity','0.5');
            });
            div.on('mouseout',function(event,target,options){
                this.dom.style.removeProperty('background-color');
                this.dom.style.removeProperty('opacity');
            });
            */
            div.on('click',function(event,target,options){
                resourceId=target.id.replace('overlay','');
                //console.log(resourceId);
                tokenGrid.selModel.select(tokenGrid.store.find('resourceId',resourceId));
            });
        }
        flat[shape.resourceId]=shape;
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