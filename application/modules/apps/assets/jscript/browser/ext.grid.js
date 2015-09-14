function gridClick (view,record,item,index,e,options ){
    //console.log(view,record,record.internalId,item,index,e,options );
    //console.log(record.internalId);
    var thisid=(record.data.idapp)?record.data.idapp:'';    
    var internalId=record.internalId;   
    propsGrid.idapp=thisid
    var url=globals.module_url+'get_app_properties/'+thisid;
    load_props(url,internalId);
}

var newObject = function() {
    var internalId=null;
    var url=globals.module_url+'get_app_properties/';
    load_props(url,internalId);
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
    indexes:['title','idapp'],
    store:dgstore,    
    columns: [
    Ext.create('Ext.grid.RowNumberer'),
    {
        text: "IDApp",
        width:90,
        dataIndex: 'idapp',
        sortable: true
           
    },
    {
        text: "Title",
        width:320,
        dataIndex: 'title',
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
            ptype: 'gridviewdragdrop'
            ,
            ddGroup:'frames'
        },
        listeners: {
            
            beforedrop: function(node,data,overModel,position,dropFunction,options ){
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

            drop: function(node, data, dropRec, dropPosition) {
                //console.log(data.records[0].data);
                var dropOn = dropRec ? ' ' + dropPosition + ' ' + dropRec.get('title') : ' on empty view';
                Ext.example.msg("Drag", 'Dropped ' + data.records[0].get('title') +'\n'+ dropOn);
            }
            ,
            itemclick: gridClick
        }
    },
    ////////////////////////////////////////////////////////////////////////////
    //////////////////////   DOCKERS    ////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////
    tbar: [
    {
        xtype: 'button', 
        text: 'Save App',
        icon:globals.base_url+'css/ext_icons/save.gif',
        tooltip:'Save changes',
        handler:function(){
            mygrid.store.sync();
        //mygrid.store.read();
        }
    }
    ,{
        xtype: 'button', 
        text: 'Reload',
        icon:globals.base_url+'css/ext_icons/refresh.gif',
        tooltip:'Reload from db an discard changes',
        handler:function(){    
            mygrid.store.read();
        }
    }
    ,{
        xtype: 'button', 
        text: 'Preview',
        icon:globals.base_url+'css/ext_icons/preview.gif',
        tooltip:'Preview Form',
        handler:function(){
            url=globals.base_url+'dna2/render/go/'+idobj;
            window.open(url);
        }
    }
    ,{
        xtype: 'button', 
        text: 'New Object',
        icon:globals.base_url+'css/ext_icons/new_tab.gif',
        tooltip:'Preview Form',
        handler:newObject
    }
    ]
    

});
dgstore.load();