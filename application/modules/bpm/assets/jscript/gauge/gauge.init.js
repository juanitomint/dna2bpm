/* 
 * Jquery Gauge wrapper
 * 
 */
$(document).ready(function(){
    $('.gauge').each(function(index,element){
        me=$(element);
        data=me.data();
        max=data.max;
        div=max/3;
        //---set colors
        data.greenFrom="0";
        data.greenTo=div;
        data.yellowFrom=div;
        data.yellowTo=2*div;
        data.redFrom=2*div;
        data.redTo=max;
        data.majorTicks=5;
        data.majorTickLabel=true;
        data.width=(data.width!=null)? data.width:200;
        data.height=(data.height!=null)? data.height:200;
        data.label=(data.label!=null)? data.label:'';
        //---get value from html
        if(me.html()){
        data.value=parseInt(me.html());
        me.html('');
        
        } 
            
        canvas='<canvas id="canvas-'+me.attr('id')+'" width="'+data.width+'" height="'+data.height+'"></canvas>';
        me.append(canvas);
        new Gauge($('#canvas-'+me.attr('id'))[0],data);
    });
});
