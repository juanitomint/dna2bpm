var GroupRename = Ext.create('Ext.Action', {
    iconCls: '',
    text: 'Rename Group',
    handler: function(widget, event) {
        n=dataview.selModel.getSelection()[0];
        if (n) {
            dataview.editingPlugin.startEdit(n,1);
        }
    }
});
var GroupAdd = Ext.create('Ext.Action', {
    iconCls: '',
    text: 'Add Group',
    handler: function(widget, event) {
        Ext.MessageBox.prompt('Model', 'Please Folder name:', function(btn,text){
            if(btn=='ok' && text){
                    
                dataview.store.add({
                    'name':text
                });
                dataview.store.sync();
            }
        }
        );
    }
});
var GroupRemove = Ext.create('Ext.Action', {
    iconCls: 'icon-minus-sign',
    text: 'Remove Group',
    handler: function(widget, event) {
        n=dataview.selModel.getSelection()[0];
        if (n) {
            Ext.MessageBox.show({
                title: 'What, really remove Group?',
                msg: 'Are you sure?',
                buttons: Ext.MessageBox.YESNO,
                buttonText:{ 
                    yes: "Definitely!", 
                    no: "No chance!" 
                },
                fn: function(btn){
                    if(btn=='yes'){
                        dataview.store.remove(n);
                        dataview.store.sync();
                        onGroupStoreLoad();
                    }
                }
            });
        } else {
            //---show message
            Ext.MessageBox.alert('Error!', "Select a Group to remove");
        }
        
    }
});

//---4 context menu
var GroupContextMenu = Ext.create('Ext.menu.Menu', {
    title:'Group Menu',
    items: [
    GroupAdd,
    GroupRemove,
    GroupRename
    ]
});

var onSelectionChange=function(item){
    n=dataview.selModel.getSelection()[0];
    if(n){
        mygrid.store.load({
            params:{
                idgroup:item.getLastSelected().data.idgroup
            }
        });
        if(tree){
            tree.setLoading('wait...');
            tree.uncheck_all()
            tree.load_checked(dataview.selModel.getLastSelected().data.idgroup);
        }
    }
}
var dataview=Ext.create('Ext.grid.Panel',
{
    columnLines: false,
    autoScroll: true,
    stripeRows: true,
    id:'groupGrid',
    indexes:['idgroup','name'],
    //store:dgstore,    
    store: Ext.data.StoreManager.lookup('GroupStore'),
    selModel: {
        mode: 'SINGLE',
        listeners: {
            scope: this,
            selectionchange: onSelectionChange
        }
    },
    columns: [
    //Ext.create('Ext.grid.RowNumberer'),
    
    {
        text: "IDGroup",
        width:90,
        dataIndex: 'idu',
        hidden:true,
        sortable: true
           
    },
    {
        text: "Name",
        xtype: 'templatecolumn',
        tpl:'{name} ({idgroup})',
        flex: true,
        dataIndex: 'name',
        sortable: true,
        editor: {
            allowBlank: false
        }
    }]
    ,
    viewConfig: {
        copy:true,
        plugins: {
            ptype: 'gridviewdragdrop',
            enableDrop: false,
            ddGroup:'user'
        }
    },
    plugins:[
    Ext.create('Ext.grid.plugin.CellEditing', {
        clicksToEdit: 2
    })  
    ],
    listeners:{
              
        itemcontextmenu: function(view, rec, node, index, e) {
            e.stopEvent();
            GroupContextMenu.showAt(e.getXY());
            return false;
        }  
    },
    ////////////////////////////////////////////////////////////////////////////
    //////////////////////     DOCKERS     /////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////
    tbar:{
        items:[
        GroupAdd,
        GroupRemove
        ]
    }
});

dataview.on('edit', function(editor, e) {
    // Sync changes as they occur
    dataview.store.sync();
});