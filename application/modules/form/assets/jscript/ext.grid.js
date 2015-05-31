dropNode=function(node, data, dropRec, dropPosition) {
    //console.log(data.records[0].data);
    data.records[0].dirty=true;
                
//var dropOn = dropRec ? ' ' + dropPosition + ' ' + dropRec.get('title') : ' on empty view';
//Ext.example.msg("Drag", 'Dropped ' + data.records[0].get('title') +'\n'+ dropOn);
}
            
function gridClick (view,record,item,index,e,options ){
    //console.log(view,record,record.internalId,item,index,e,options );
    //console.log(record.internalId);
    var thisidframe=(record.data.idframe)?record.data.idframe:'';
    var type=record.data.type;
    var internalId=record.internalId;
    Ext.getCmp('rightPanel').setLoading(true);
    //------LOAD propGrid Template
    Ext.Ajax.request({
        // the url to the remote source
        url: globals.module_url+'get_form_template/'+type,
        method: 'POST',
        // define a handler for request success
        success: function(response, options){
            Ext.getCmp('rightPanel').setLoading(false);
            panel=Ext.getCmp('rightPanel');
            panel.remove('propsGrid',true);
            eval(response.responseText);
            panel.add(propsGrid);
                                                
            propsGrid.idframe=thisidframe
            var url=globals.module_url+'get_properties/'+type+'/'+globals.idobj+'/';
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
function GridMarkDirty(){
    mygrid.store.data.items.forEach(function(row){
        row.dirty=true;
    });
    return;
};
var GridSave=Ext.create('Ext.Action', 
{
    xtype: 'button', 
    text: 'Save',
    icon:globals.base_url+'css/ext_icons/save.gif',
    tooltip:'Save changes',
    handler:function(){
        mygrid.setLoading('Saving...');
        GridMarkDirty();
        mygrid.store.sync({
            callback : function (){
                mygrid.setLoading(false);
            }
        });
    }        
});

var GridReload=Ext.create('Ext.Action', 
{
    text: 'Reload',
    icon:globals.base_url+'css/ext_icons/refresh.gif',
    tooltip:'Reload from db an discard changes',
    handler:function(){    
        mygrid.store.read();
    }
});

var GridPreview=Ext.create('Ext.Action',
{
    text: 'Preview',
    icon:globals.base_url+'css/ext_icons/preview.gif',
    tooltip:'Preview Form',
    handler:function(){
        url=globals.base_url+'dna2/render/go/'+globals.idobj;
        window.open(url);
    }
});

var GridEditPHP=Ext.create('Ext.Action',
{
    icon:globals.base_url+'css/ext_icons/php.png',
    id:'ObjCodeBtnPHP',
    tooltip:'Server Side Hooks',
    handler: function(){
        
        createCodeWindow('Server Side Script Hooks for:'+globals.idobj,{
            'edit':'php',
            'view':'php',
            'process':'php',
            'print':'php'
        },this.id,globals.module_url+'code',globals.idobj);
    }
});
    
    
var GridEditJS=Ext.create('Ext.Action',
{
    icon:globals.base_url+'css/ext_icons/js.png',
    id:'ObjCodeBtnJS',
    tooltip:'Client Side Hooks',
    handler: function(){
        createCodeWindow('Client Side Scripts Hooks for:'+globals.idobj,hooksJS,this.id,globals.module_url+'code',globals.idobj);
    }
});


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
var checkRequired = Ext.create('Ext.ux.CheckColumn',{
    xtype: 'checkcolumn',
    header: 'Required',
    dataIndex: 'required',
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
sm={};
//var mygrid=Ext.create('Ext.ux.LiveSearchGridPanel',
var mygrid=Ext.create('Ext.grid.Panel',
{
    columnLines: true,
    title:'Form Frames',
    id:'centerGrid',
    indexes:['title','idframe'],
    //store:dgstore,    
    store:dgstore,    
    columns: [
    Ext.create('Ext.grid.RowNumberer'),
    {
        text: "ID",
        width:90,
        dataIndex: 'idframe',
        sortable: true
           
    },
    {
        text: "Title",
        width:320,
        dataIndex: 'title',
        sortable: true
    },
    {
        text: "Type",
        dataIndex: 'type',
        sortable: true
    }
    ,checkLock
    ,checkHidden
    ,checkRequired
    ],
    stripeRows       : true,
    viewConfig: {
        
        //        stripeRows: true,
        plugins: {
            ptype: 'gridviewdragdrop'
            ,
            ddGroup:'frames'
        },
        listeners: {
            /* 
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
*/
            drop: dropNode
            ,
            itemclick: gridClick
        }
    },
    ////////////////////////////////////////////////////////////////////////////
    //////////////////////   DOCKERS    ////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////
    tbar: [
    GridSave,
    GridPreview,
    GridReload,
    GridEditPHP,
    GridEditJS 
   
    ]

});

////////////////////////////////////////////////////////////////////////////////
//////////////////////// BEGIN ALL FRAMES GRID////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
var groupingOther = Ext.create('Ext.grid.feature.Grouping',{
    groupHeaderTpl: '{name} ({rows.length})', //print the number of items in the group
    startCollapsed: false // start all groups collapsed
});

//var othergrid=Ext.create('Ext.grid.Panel', {
var othergrid=Ext.create('Ext.ux.LiveFilterGridPanel',{
    //title:'All Similar Frames Available',
    columnLines: true,
    stripeRows : true,
    id:'otherGrid',
    indexes:['title','idframe'],
    store:otherstore,
    features: [groupingOther],
    
    //---start model
    //    selModel: sm,    
    columns: [
    //Ext.create('Ext.grid.RowNumberer'),
    {
        text: "ID",
        width:90,
        dataIndex: 'idframe',
        sortable: true
           
    },
    {
        text: "Title",
        width:320,
        dataIndex: 'title',
        sortable: true
    },
    {
        text: "Type",
        dataIndex: 'type',
        sortable: true
    }
            
    ],
    viewConfig: {
        autoScroll:false,
        stripeRows: true,
        plugins: {
            ptype: 'gridviewdragdrop'
            ,
            ddGroup:'frames'
        },
        listeners: {
            itemclick: gridClick
        }
    }
    

});
//otherstore.load();
dgstore.load();