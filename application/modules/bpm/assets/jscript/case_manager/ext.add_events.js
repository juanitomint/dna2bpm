/*
 *  Add events for overlays
 */
var add_events = function(shapes) {
    for (j in shapes) {
        var shape = shapes[j];
        xBound = 0;
        yBound = 0;
        //console.log(shape.stencil.id,exclude_shape.indexOf(shape.stencil.id)==-1);
        xBound = shape.bounds.upperLeft.x + offset_x; //( -1 to fix nested objects
        yBound = shape.bounds.upperLeft.y + offset_y;
        width = shape.bounds.lowerRight.x - shape.bounds.upperLeft.x;
        height = shape.bounds.lowerRight.y - shape.bounds.upperLeft.y
        if (exclude_shape.indexOf(shape.stencil.id) == -1) { //---if not excluded
            switch (shape.stencil.id) {
                case 'Lane':
                    width = 24;
                    break;

            }
            add = new Array();
            add.push('<span class="resourceId">resourceId:<br>' + shape.resourceId + '</span>');
            if (shape.properties.tasktype) {
                switch (shape.properties.tasktype) {
                    case 'Script':
                        add.push((globals.idcase) ? '<a target="_blank" class="btn btn-small btn-info" href="' + globals.base_url + 'bpm/test/test_task/' + globals.idwf + '/' + globals.idcase + '/' + shape.resourceId + '"><i class="fa fa-flask fa-white"></i> Test Script</button>' : '');
                        break;

                    case 'Send':
                        add.push((globals.idcase) ? '<a target="_blank" class="btn btn-small btn-info" href="' + globals.base_url + 'bpm/test/send/' + globals.idwf + '/' + globals.idcase + '/' + shape.resourceId + '"><i class="fa fa-envelope fa-white"></i> Test Msg</button>' : '');
                        break;

                }
            }

            Ext.core.DomHelper.append('svg-box', {
                tag: 'div',
                cls: 'model_overlay',
                id: 'overlay' + shape.resourceId,
                style: ' border-radius: 3px;\n\
                        position:absolute;\n\
                        left:' + xBound + 'px;\n\
                        top:' + yBound + 'px;\n\
                        width:' + width + 'px;\n\
                        height:' + height + 'px;\n\
\n\
'
            });
            config = {
                id: 'toolTip' + shape.resourceId,
                target: 'overlay' + shape.resourceId,
                anchor: 'bottom',
                dismissDelay: 0,
                minWidth: 320,
                //anchorOffset: 85, // center the anchor on the tooltip
                html: add.join('<br/>')
            };
            tooltips.push(
                Ext.create('Ext.tip.ToolTip', config)
            );

            div = Ext.get('overlay' + shape.resourceId);
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
            div.on('click', function(event, target, options) {
                resourceId = target.id.replace('overlay', '');
                tip = Ext.getCmp('toolTip' + resourceId);
                if (tip.isVisible()) {
                    Ext.getCmp('toolTip' + resourceId).autoHide = true;
                }
                else {
                    Ext.getCmp('toolTip' + resourceId).show();
                    Ext.getCmp('toolTip' + resourceId).autoHide = false;
                }
                //console.log(resourceId);
                tokenGrid.selModel.select(tokenGrid.store.find('resourceId', resourceId));
            });
        }
        flat[shape.resourceId] = shape;
        if (shape.childShapes.length) {
            var offset_x_orig = offset_x;
            var offset_y_orig = offset_y;
            offset_x = xBound;
            offset_y = yBound;
            add_events(shape.childShapes);
            //---restore original valoues of offset
            offset_x = offset_x_orig;
            offset_y = offset_y_orig;

        }
    }
}