
function gridClick (view,record,item,index,e,options ){
    form=Ext.getCmp('userForm');
    if (record) {
        //---load record into form
        form.loadRecord(record);
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

var checkDisabled = Ext.create('Ext.ux.CheckColumn',{
    xtype: 'checkcolumn',
    header: 'Disabled',
    dataIndex: 'disabled',
    width: 60,
    editor: {
        xtype: 'checkbox',
        cls: 'x-grid-checkheader-editor'
    }
}
);

//var sm = Ext.create('Ext.selection.CheckboxModel');
//var sm = Ext.create('Ext.selection.Model',{mode:'MULTI'});
//sm={};
//var mygrid=Ext.create('Ext.ux.LiveSearchGridPanel',
var mygrid=Ext.create('Ext.grid.Panel',
{
    title:'Users',
    columnLines: true,
    autoScroll: true,
    stripeRows: true,
    id:'userGrid',
    indexes:['idu','nick'],
    //store:dgstore,    
    store: Ext.data.StoreManager.lookup('UserStore'),    
    //selModel:sm, //---uncomment for allow select
    columns: [
    //Ext.create('Ext.grid.RowNumberer'),
    
    {
        text: "ID",
        width:90,
        dataIndex: 'idu',
        hidden:true,
        sortable: true
           
    },
    {
        text: "Nick",
        flex: true,
        dataIndex: 'nick',
        sortable: true
    },
    {
        text: "Name",
        flex: true,
        dataIndex: 'name',
        sortable: true
    },
    {
        text: "Last Name",
        dataIndex: 'lastname',
        sortable: true
    },
    {
        text: "email",
        dataIndex: 'email',
        flex:true,
        sortable: true
    }
    ,checkLock
    ,checkDisabled
    ],
        
    viewConfig: {
        
        listeners: {
            itemclick: gridClick
        }//---listeners
    },
        
    bbar: Ext.create('Ext.PagingToolbar', {
        store: Ext.data.StoreManager.lookup('UserStore'),
        displayInfo: true,
        displayMsg: 'Displaying users {0} - {1} of {2}',
        emptyMsg: "No users to display",
        renderTo: mygrid
            
    }),
    tbar: [
                    
    {
        xtype: 'button', 
        text: 'Save Changes',
        icon:globals.base_url+'css/ext_icons/save.gif',
        handler:function(){
            mygrid.store.update();      
        }
    }
               
    ,
    {
        width: 400,
        fieldLabel: 'Search',
        labelWidth: 50,
        xtype: 'searchfield',
        store: Ext.data.StoreManager.lookup('UserStore')
    }
    ]
        


});

