function gridClick (view,record,item,index,e,options ){
    thisCase=record.data.id;
    url=globals.module_url+'case_manager/tokens/status/'+globals.idwf+'/'+thisCase;    
    tokenStore=Ext.getStore('tokenStore')
    tokenStore.proxy.url=url;
    tokenStore.load({
        scope: this,
        callback:tokens_paint_all    
    });
    load_model(globals.idwf);
    Ext.getCmp('ModelPanelTbar').enable();
    gridIndex=0;
}

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
    },
    listeners:{
        checkchange: function (me, rowIndex, checked, eOpts ){
            mygrid.store.sync();
        }
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
    indexes:['checkdate'],
    store:dgstore,    
    columns: [
    {
        menuDisabled: true,
        sortable: false,
        xtype: 'actioncolumn',
        width: 50,
        items: [{
            icon   : globals.module_url+'assets/images/delete.png',  // Use a URL in the icon config
            tooltip: 'Remove case from DB',
            handler: function(grid, rowIndex, colIndex) {
                var rec = dgstore.getAt(rowIndex);
                Ext.Msg.confirm('Confirm', 'Are you sure you want to remove: '+rec.get('id')+'?',confirm,rec);
                            
            }
        }]
    },
    Ext.create('Ext.grid.RowNumberer'),
    {
        flex:1,
        text: "Date",
        dataIndex: 'date',
        sortable: true
           
    },
    {
        flex:1,
        text: "ID",
        dataIndex: 'id',
        sortable: true
           
    }
    ,
    {
        flex:1,
        text: "User",
        dataIndex: 'user',
        sortable: true
    }
    ,
    {
        flex:1,
        text: "Status",
        dataIndex: 'status',
        sortable: true,
        renderer: function(value){
            switch(value){
                case 'open':
                    stClass='label-warning';
                    break;
                case 'closed':
                    stClass='label-success';
                    break;
                case 'locked':
                    stClass='';
                    
                    break;
                default:
                    stClass='label-important';
                    break;
                
            }
                
            value='<span class="label '+stClass+'">'+value+'</span>'
            return value;
        }
    }
    ,checkLock
    ],
    stripeRows       : true,
    ////////////////////////////////////////////////////////////////////////////
    //////////////////////   LISTENERS  ////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////
    listeners: {
        /*
        selectionchange: function( me, selected, eOpts ){
            load_model(globals.idwf);
        },*/
        itemclick: gridClick
    },
    
    ////////////////////////////////////////////////////////////////////////////
    //////////////////////   DOCKERS    ////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////
    tbar: [
    {
        fieldLabel: 'Search',
        labelWidth: 50,
        xtype: 'searchfield',
        store: Ext.data.StoreManager.lookup('caseStore')
    },
    {
        xtype: 'button', 
        text: '<i class="icon-repeat icon-2x"></i>',
        handler:function(){    
            mygrid.store.read();
        }
    }               
   
    ]    

});


//otherstore.load();
//dgstore.load();