var second=null;
var third=null;
var gridIndex=0;
var HSPEED=1300;
function playShape(result){
    if(result=='yes'){
        resourceId=this.get('resourceId');
        gridSel=mygrid.selModel.selected.items[0];
        idcase=gridSel.get('id');
        url=globals.base_url+'bpm/engine/run_post/model/'+globals.idwf+'/'+idcase+'/'+resourceId;
        //console.log(url);
        window.location=url;
    }
}
function highlightToken (view,record,item,index,e,options ){
    //console.log(view,record,record.internalId,item,index,e,options );
    //console.log(record.internalId);
    
    if(third){
        stroke_width=2;
        color='black';
    //paint(third,color,stroke_width)
    }
    if(second){
        stroke_width=3;
        color='green';
        paint(second,color,stroke_width)
        third=second;
    }
    resourceId=record.data.resourceId;
    lockedBy=record.data.lockedBy;
    
    isrun=record.data.run;
    stroke_width=4;
    color='orange';
    paint(resourceId,color,stroke_width);
    //---set badge
    
    add_badge(resourceId,isrun,'info');
    
    //---set lock
    if(lockedBy){
        add_lock(resourceId,'','important');
    }
    if(Ext.getCmp('follow_token').checked){
        follow(resourceId);
    } 
    second=resourceId;
}

var TokensShowExtras=Ext.create('Ext.form.field.Checkbox', {
    labelAlign:'right',
    checked   : true,
    boxLabel  : 'Show Extras',
    name      : 'show_extras',
    inputValue: '1',
    id        : 'show_extras',
    listeners:{
        change:function( me, newValue, thumb, eOpts ){
            Ext.dom.Element.select('.extras').setVisible(newValue);
        }
    }
});
var TokensFolow=Ext.create('Ext.form.field.Checkbox', {
    labelAlign:'right',
    checked   : true,
    boxLabel  : 'Follow Token',
    name      : 'follow',
    inputValue: '1',
    id        : 'follow_token'
});
var TokensTimeSlider=Ext.create('Ext.slider.Single', {
    boxLabel: 'Speed',
    width: 200,
    value: 300,
    increment: 10,
    minValue: 100,
    maxValue: 1000,
    listeners:{
        change:function( me, newValue, thumb, eOpts ){
            if(playTokens){
                playTokens.interval=me.getValue();
            }
        }
    }
});
var runner = new Ext.util.TaskRunner();
var playTokens = runner.newTask({
    run: play,
    interval: TokensTimeSlider.getValue()
});
function start_play(){
    playTokens.start();
    
}
function stop(){
    playTokens.stop();
}
function play(){
    store=tokenGrid.store;
    if(gridIndex==store.count()){
        playTokens.stop();
        load_data_callback=null;
    } else {   
        tokenGrid.selModel.select(gridIndex);
        record=store.getAt(gridIndex);
        highlightToken (null,record);
        gridIndex++;
    }
    
}
function tokens_paint_all(){
    TokensFolow.setValue(0);
    store=tokenGrid.store;
    store.each(function(record){
        highlightToken (null,record);
    });
}
function step_forward(){
    store=tokenGrid.store;
    if(gridIndex<store.count()){
        record=store.getAt(gridIndex);
        tokenGrid.selModel.select(gridIndex);
        highlightToken (null,record);
        gridIndex++;
    }
    
}
function step_backward(){
    store=tokenGrid.store;
    if(gridIndex>0){
        gridIndex--;
        record=store.getAt(gridIndex);
        tokenGrid.selModel.select(gridIndex);
        highlightToken (null,record);
    }
    
}
function tokens_reload(){
    load_data_callback=null;
    load_model(globals.idwf);
    gridIndex=0;
}


function tokens_load_history(idwf,idcase){
    idwf=(idwf)?idwf:globals.idwf;
    idcase=(idcase)?idcase:globals.idcase;
    TokensFolow.setValue(1);
    //----set url for tokens proxy
    url=globals.module_url+'case_manager/tokens/history/'+idwf+'/'+idcase;
    tokenStore=Ext.getStore('tokenStore')
    tokenStore.proxy.url=url;
    tokenStore.load();
    //---set callback function fro load_model->load_data
    load_data_callback=start_play;
    load_model(idwf);
    Ext.getCmp('ModelPanelTbar').enable();
    gridIndex=0;
}
function tokens_load_status(idwf,idcase){
    idwf=(idwf)?idwf:globals.idwf;
    idcase=(idcase)?idcase:globals.idcase;
    //----set url for tokens proxy
    url=globals.module_url+'case_manager/tokens/status/'+idwf+'/'+idcase;
    tokenStore=Ext.getStore('tokenStore')
    tokenStore.proxy.url=url;
    tokenStore.load();
    //---set callback function fro load_model->load_data
    load_data_callback=tokens_paint_all;
    load_model(idwf);
    Ext.getCmp('ModelPanelTbar').enable();
    gridIndex=0;
}
var TokensStatus=Ext.create('Ext.Action',
{
    text: 'Status',
    iconCls:'icon icon-info-sign icon2x',
    toggleGroup:'filter',
    handler:function(){
        tokens_load_status();
    }
});
var TokensHistory=Ext.create('Ext.Action',
{
    text: 'History',
    iconCls:'icon icon-time icon2x',
    toggleGroup:'filter',
    handler:function(){
        tokens_load_history();
    }
});
var TokensReload=Ext.create('Ext.Action',
{
    text: 'Reload',
    iconCls:'icon icon-refresh icon2x',
    handler:tokens_reload
});
var TokensPlay=Ext.create('Ext.Action',
{
    text: '',
    iconCls:'icon icon-play icon2x',
    handler:start_play
});
var TokensStepForward=Ext.create('Ext.Action',
{
    text: '',
    iconCls:'icon icon-step-forward icon2x',
    handler:step_forward
});
var TokensStepBackward=Ext.create('Ext.Action',
{
    text: '',
    iconCls:'icon icon-step-backward icon2x',
    handler:step_backward
});
var TokensStop=Ext.create('Ext.Action',
{
    text: '',
    iconCls:'icon icon-stop icon2x',
    handler:stop
});
var TokensPaintAll=Ext.create('Ext.Action',
{
    text: 'Paint All',
    iconCls:'icon icon-circle icon2x',
    handler:tokens_paint_all
});
var tokenGrid=Ext.create('Ext.grid.Panel',
{
    columnLines: false,
    autoScroll: true,
    stripeRows: true,
    id:'tokenGrid',
    indexes:['name','desc'],
    //store:dgstore,    
    store: Ext.getStore('tokenStore'),
    columns: [
    Ext.create('Ext.grid.RowNumberer'),
    {
        menuDisabled: true,
        sortable: false,
        xtype: 'actioncolumn',
        width: 50,
        text:'play',
        items: [{
            text:'play',
            icon:globals.module_url+'assets/images/play.png',
            handler: function(grid, rowIndex, colIndex) {
                var rec = tokenstore.getAt(rowIndex);
                Ext.Msg.confirm('Confirm', 'Are you sure you want to Play: '+rec.get('type')+'?',playShape,rec);
                            
            }
        }]
    },
    {
        text: "Icon",
        flex:1,
        dataIndex: 'icon',
        sortable: true
           
    }
    ,
    {
        text: "Name",
        flex:1,
        dataIndex: 'name',
        sortable: true
           
    }
    ,
    {
        text: "Type",
        flex:1,
        dataIndex: 'type',
        sortable: true
           
    }
    ,
    {
        text: "resourceId",
        flex:1,
        dataIndex: 'resourceId',
        editor: {
            allowBlank: false
        },
        sortable: true
           
    }
    ,
    {
        text: "status",
        flex:1,
        dataIndex: 'status',
        sortable: true
           
    }    
    ],
    
    listeners: {
        //itemclick: highlightToken,
        selectionchange: function(me, selected, eOpts ){
            if(selected[0]){
                highlightToken(me, selected[0], eOpts );
                gridIndex=selected[0].index;
            }
        
        }
    },
    plugins: [{
        ptype: 'cellediting', 
        clicksToEdit: 1
    }]
    
   
});
