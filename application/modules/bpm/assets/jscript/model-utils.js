var model,thiscase,offset_x,offset_y;

var flat={};
function loadSync(url,callback){
    var rtnData={};
    $.ajax({
        url : url,
        async:false,
        type: 'GET',
        dataType:'json',
        success:function(data){
            rtnData=data;
            if(typeof callback=="function")
                callback(data);
        }
    });
    return rtnData;
}//end loadDone
function overlay(model){
    //---get first position parameters
    svg_box=$('#svg-box');
    obj=$('#svg-box .stencils:first');
    xy=obj.attr('transform').replace('translate(','').replace(')','').split(',');
    offset_x=parseInt( xy[0])+svg_box.offset().left;
    offset_y=parseInt(xy[1])+svg_box.offset().top;
    //console.log('translate',offset_x,offset_y);
    container='svg-box';
    add_shapes(model.childShapes,container);
    return true;
}
function add_shapes(shapes,container){
    for (j in shapes){
        shape=shapes[j];
        if(shape.stencil.id!='SequenceFlow'){
            me=$('#'+shape.resourceId);
            div='<div id="div_'+shape.resourceId+'" class="overlay" style="visibility:visible;position:relative;" type="'+shape.stencil.id+'" resourceId="'+shape.resourceId+'"/>';
            parent=(shape.parent);
            flat[shape.resourceId]=shape;
            $('#'+container).append(div);
            m3=$('#div_'+shape.resourceId);
            //add Functions
            m3.mouseover(MOUSE_OVER);
            m3.mouseout(MOUSE_OUT);
            m3.click(LAYER_CLICK);

            xBound=shape.bounds.upperLeft.x+offset_x-1; //( -1 to fix nested objects
            yBound=shape.bounds.upperLeft.y+offset_y-2;
            //console.log(shape.resourceId,shape.stencil.id,shape.bounds.upperLeft.x,shape.bounds.upperLeft.y,xBound,yBound);
            //---set div offset
            m3.offset({
                left:xBound,
                top:yBound
            });
            //---add id text 4 debug only
            //            m3.html('<span>xb'+shape.bounds.upperLeft.x+' yb'+shape.bounds.upperLeft.y+'   '+' L:'+xBound+' Y:'+yBound+' Ox'+offset_x+' Oy'+offset_y+'</span>');
            //            m3.css('border','2px dotted grey');
            //---set size
            m3.width(shape.bounds.lowerRight.x-shape.bounds.upperLeft.x);
            m3.height(shape.bounds.lowerRight.y-shape.bounds.upperLeft.y);

            //--add child divs calling recursively
            if(shape.childShapes.length){
                //console.log('recalling for:'+shape.stencil.id);
                //---save offset status
                offset_x_orig=offset_x;
                offset_y_orig=offset_y;
                offset_x=xBound;
                offset_y=yBound;
                //if(shape.stencil.id!='Lane') //---don't dive intor lanes 4 debug only
                add_shapes(shape.childShapes,'div_'+shape.resourceId);
                //---restore original valoues of offset
                offset_x=offset_x_orig;
                offset_y=offset_y_orig;
            ///restore ofsets

            }
        }
    }
}

function add_popup(){
    $('.overlay').RemoveBubblePopup();
    $('.overlay').CreateBubblePopup();
    $('.overlay[type="Pool"]').RemoveBubblePopup();
    $('.overlay[type="Lane"]').RemoveBubblePopup();
    if(typeof(SOUP_EXTRA)=='undefined'){
     //console.log('ma perche???',typeof(SOUP_EXTRA));
     SOUP_EXTRA={};
    }

    $('.overlay').each(function(){
        var resourceId=$(this).attr('resourceId');
        var shape=flat[resourceId];
        var token={};
        for(j in thiscase.tokens){
            if(thiscase.tokens[j].resourceId==resourceId)
                token=thiscase.tokens[j];
        }
        if((Array('Pool','Lane').indexOf(shape.stencil.id)==-1)) {
            //$(this).addClass('popup');

            var align='middle';
            //if(shape.stencil.id=='lene')
            //  align='top';
            popup_text=POPUP_TEXT(token,shape);
            $(this).SetBubblePopupOptions(
                $.extend({
                alwaysvisible:false,
                distance:'10px',
                //innerHtml: $(this).attr('type')+'<br/><img src="'+base_url+'css/ajax/loading.gif" style="border:0px; vertical-align:middle; margin-right:10px; display:inline;" />loading!',
                innerHtml: popup_text,
                innerHtmlStyle: {
                    color:'#FFFFFF',
                    'text-align':'center'
                },
                position: 'top',
                align: align,
                manageMouseEvents:false,
                //mouseOut: 'show',
                themeName: 	popup_style,
                themePath: 	base_url+'jscript/jquery/plugins/popup-bubble/jquerybubblepopup-theme'
            },SOUP_EXTRA));
        //console.log('Adding popup 4:',shape.stencil.id,resourceId,popup_text);
        }
    // $('.overlay[type="Pool"]').
    });
//$('.overlay').ShowAllBubblePopups();

}

function paint(resourceId,color,stroke_width){
    shape=flat[resourceId];
    stroke_with=(stroke_width==null)? 2:stroke_width;
    switch(shape.stencil.id){
        case "Task":
            $('#'+shape.resourceId+' .stencils .me [id*="_frame"]').attr('style','').attr('stroke-width',stroke_with).attr('stroke',color);
            break;
        case "Exclusive_Databased_Gateway":
            $('#'+shape.resourceId+' .stencils .me [id*="_frame"]').attr('style','').attr('stroke-width',stroke_with).attr('stroke',color);
            break;
        case "SequenceFlow":
            $('#'+shape.resourceId+' .stencils .me path').attr('stroke-width',stroke_with).attr('stroke',color);
            break;
        default:
            $('#'+shape.resourceId+' .stencils .me [id*="_frame"]').attr('stroke-width',stroke_with).attr('stroke',color);
            break;
    }

}
function paintme(){
    token=thiscase.tokens[i];
    //console.log(i,token.resourceId,token.type,token.status);
    status={
        'finished':'seagreen',
        'stoped':'red',
        'pending':'orange'
    }
    color=(status[token.status])? status[token.status]:'orange';
    switch(token.type){
        case "Task":
            $('#'+token.resourceId+' .stencils .me [id*="_frame"]').attr('style','').attr('stroke-width','3').attr('stroke',color);
            break;
        case "Exclusive_Databased_Gateway":
            $('#'+token.resourceId+' .stencils .me [id*="_frame"]').attr('style','').attr('stroke-width','3').attr('stroke',color);
            break;
        case "SequenceFlow":
            $('#'+token.resourceId+' .stencils .me path').attr('stroke-width','4').attr('stroke',color);
            $('#'+token.resourceId+'end path').attr('stroke-width','3').attr('stroke',color).attr('fill',color);

            break;
        default:
            $('#'+token.resourceId+' .stencils .me [id*="_frame"]').attr('stroke-width','3').attr('stroke',color);
            break;
    }
    i++;
    if(i >= thiscase.tokens.length){
        //console.log('Abort...');
        window.clearInterval(intervalID);

    }
}

