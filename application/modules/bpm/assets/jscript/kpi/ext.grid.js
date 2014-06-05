function gridClick (view,record,item,index,e,options ){
    //console.log(view,record,record.internalId,item,index,e,options );
    //console.log(record.internalId);
    var thisid=(record.data.idkpi!=="")?record.data.idkpi:'';
    var type=record.data.type;
    var internalId=record.internalId;
    Ext.getCmp('rightPanel').setLoading(true);
   
    //------LOAD propGrid Template
    Ext.Ajax.request({
        // the url to the remote source
        url: globals.module_url+'kpi/get_template/'+type,
        method: 'POST',
        // define a handler for request success
        success: function(response, options){
            Ext.getCmp('rightPanel').setLoading(false);
            //Ext.getCmp('propsGridTbar').enable();
            panel=Ext.getCmp('rightPanel');
            panel.remove('propsGrid',true);
            eval(response.responseText);
            panel.add(propsGrid);
                                                
            propsGrid.idkpi=thisid
            //---allow natural order
            propsGrid.store.sorters.items=[];
            var url=globals.module_url+'kpi/get_properties/'+thisid;
            //--------------------------------------------------------------
            //--------LOAD PROPERTIES---------------------------------------
            //--------------------------------------------------------------
            load_props(url,internalId);
        },
        // NO errors ! ;)
        failure: function(response,options){
            alert('Error Loading:'+response.err);
            propsGrid.setLoading(false);
                
        }
    });//-- Ext.Ajax.request
    
}
KpiSave=Ext.create('Ext.Action',
{
        text: 'Save',
        iconCls:'icon icon-save',
        tooltip:'Save changes',
        handler:function(){
            mygrid.store.sync({callback:mygrid.store.read});
            
        }
    }
);
KpiReload=Ext.create('Ext.Action',
{
        text: 'Reload',
        iconCls:'icon icon-repeat',
        handler:function(){    
            mygrid.store.read();
        }
    }    
);
KpiPreview=Ext.create('Ext.Action',
{
        text: 'Preview',
        iconCls:'icon icon-desktop',
        tooltip:'Preview',
        handler:function(){
            url=globals.module_url+'kpi/test_render/'+globals.idwf;
            window.open(url);
        }
    }    
);
function confirm(result){
    if(result=='yes'){
        dgstore.remove(this);
        dgstore.sync();
    }
}
//---4 in place locking editor
var checkLock = Ext.create('Ext.ux.CheckColumn',{
    xtype: 'checkcolumn',
    header: 'Locked',
    dataIndex: 'locked',
    width: 60,
    editor: {
        xtype: 'checkbox',
        cls: 'x-grid-checkheader-editor'
    }
}
);

var checkHidden = Ext.create('Ext.ux.CheckColumn',{
    xtype: 'checkcolumn',
    header: 'Hidden',
    dataIndex: 'hidden',
    width: 60,
    editor: {
        xtype: 'checkbox',
        cls: 'x-grid-checkheader-editor'
    }
}
);

//var sm = Ext.create('Ext.selection.CheckboxModel');
//--uncoment this when check bug it's fixed
//sm={};
//var mygrid=Ext.create('Ext.ux.LiveSearchGridPanel',
var mygrid=Ext.create('Ext.grid.Panel',
{
    columnLines: true,
 
    id:'centerGrid',
    indexes:['title','idframe'],
    store:dgstore,    
    columns: [
    {
        menuDisabled: true,
        sortable: false,
        xtype: 'actioncolumn',
        width: 50,
        items: [{
            icon   : globals.module_url+'assets/images/delete.png',  // Use a URL in the icon config
            tooltip: 'Remove user from group',
            handler: function(grid, rowIndex, colIndex) {
                var rec = dgstore.getAt(rowIndex);
                Ext.Msg.confirm('Confirm', 'Are you sure you want to remove: '+rec.get('title')+'?',confirm,rec);
                            
            }
        }]
    },
    Ext.create('Ext.grid.RowNumberer'),
    {
        text: "ID",
        width:90,
        dataIndex: 'idkpi',
        sortable: true
           
    },
    {
        text: "Title",
        width:320,
        dataIndex: 'title',
        sortable: true
    }
    ,
    {
        text: "Type",
        dataIndex: 'type',
        sortable: true
    }
    ,checkLock
    ,checkHidden            
    ],
    stripeRows       : true,
    viewConfig: {
        //autoScroll:true,
        //        stripeRows: true,
        plugins: {
            ptype: 'gridviewdragdrop',
            ddGroup:'type'
        },
        listeners: {
            /*
            beforedrop:  function(node, data, overModel, dropPosition, dropHandlers){
             
                //console.log(node,data,overModel,position,dropFunction,options );
                var me=this;
                if(data.copy){
                    //---get the index within the grid
                    var index=node.viewIndex;
                    if (position !== 'before') {
                        index++;
                    } 
                    //---make a copy of the item
                    var itemadd=data.records[0].copy(Ext.id());//---take one item only 
                    this.store.insert(index,itemadd);                
                    //---TODO load pgrid with propper type; if new
                    //return 0;
                    return false;
                } else {
                    return 0;
                }
            },
                */

            drop: function(node, data, dropRec, dropPosition) {
                //---emulate grid click over new row
                gridClick(null,mygrid.selModel.getSelection()[0]);
               
            }
            ,
            itemclick: gridClick
        }
    },
    
    ////////////////////////////////////////////////////////////////////////////
    //////////////////////   DOCKERS    ////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////
    tbar: [
    KpiSave,
    KpiReload,
    KpiPreview
    ]
    

});


//otherstore.load();
//dgstore.load();