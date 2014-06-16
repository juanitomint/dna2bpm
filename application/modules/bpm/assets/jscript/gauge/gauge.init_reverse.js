/* 
 * Jquery Gauge wrapper
 * 
 */
$(document).ready(function(){
    $('.gauge_reverse').each(function(index,element){
        me=$(element);
        data=me.data();
        max=data.max;
        div=max/3;
        majorTicks=new Array();
        //----make major ticks
        for(var i=0;i<=4;i++){
            if(max<10){
                majorTicks.push(parseFloat(Math.round(max/4 *i* 100) / 100).toFixed(1));
            } else {
                majorTicks.push(Math.round(max/4*i));
            }
        }
        data.renderTo='canvas-'+me.attr('id');
        data.glow=false;
        data.minValue=0;
        data.maxValue=max;
        data.greenTo=div;
        /*data.units='Days';*/
        data.valueFormat={
            int:3,
            dec:0
        };
        data.animation  = {
            delay : 25,
            duration: 500,
            fn : 'elastic'
        };
        data.majorTicks=majorTicks;
        data.highlights = [{
            from  : 0,
            to    : div,
            color : 'LightSalmon'
        }, {
            from  : div,
            to    : 2*div,
            color : 'Khaki'
        }, {
            from  : 2*div,
            to    : 3*div,
            color : 'PaleGreen'
        }];
        //---set colors
        /*
        data.colors = {
            needle : {
                start : 'lightgreen', 
                end : 'navy'
            },
            plate : 'lightyellow',
            title : 'green',
            units : 'lightgreen',
            majorTicks : 'darkgreen',
            minorTicks : 'lightgreen',
            numbers : 'darkgreen'
        }
*/
        
        data.width=(data.width!=null)? data.width:200;
        data.height=(data.height!=null)? data.height:200;
        //---get value from html
        if(me.html()){
            data.value=parseInt(me.html());
            me.html('');
        } 
            
        canvas='<canvas id="canvas-'+me.attr('id')+'" width="'+data.width+'" height="'+data.height+'"></canvas>';
        me.append(canvas);
        var mygauge=new Gauge(data);
        mygauge.setValue(data.value);
        
        mygauge.onready=function(){
            value=this.getValue();
            this.setValue(0);
            this.setValue(value);
        };
        mygauge.draw();
    });
});
