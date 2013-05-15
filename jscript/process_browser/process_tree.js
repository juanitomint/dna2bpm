var tree_store = Ext.create('Ext.data.TreeStore', {
    root: {
        expanded: true
    },
    proxy: {
        type: 'ajax',
        api: {
            create  : base_url+'bpm/browser/tree/create',
            read    : base_url+'bpm/browser/get_tree2/read',
            update  : base_url+'bpm/browser/tree/update',
            destroy : base_url+'bpm/browser/tree/destroy'
        },
        writer: {
            type: 'json'
        }
    },
    folderSort: true,
    sorters: [{
        property: 'text',
        direction: 'ASC'
    }]
});

/////////////// FIX ERROR IN IDEXOF
Ext.override(Ext.data.AbstractStore,{
    indexOf: Ext.emptyFn
});  
//---add dbl click functionalities
var onTreeNodeDblClick= function(n) {
    treeEditor.editNode = n;
    treeEditor.startEdit(n.ui.textNode);
}
//---Wht to do when finished.
var onTreeEditComplete=function(treeEditor, o, n) {
//o - oldValue
//n - newValue
}
//---Click!
var onTreeNodeClick= function(View, record, item, index, e,eOpts ){
    console.log(index,record);
}
//---Drop!
var onTreeNodeDrop=function (node, data, view, ddel, item) {
    folder=view.data.id;                       
}
var cellEditor=Ext.create('Ext.grid.plugin.CellEditing', {
                clicksToEdit: 2
            });
//---Create the Tree
var tree = Ext.create('Ext.tree.Panel', {
    store: tree_store,    
    layout:'fit',
    title: '',
    rootVisible: true,
    animCollapse : false,
    animate:false,
    lines:true,
    rootText:'Home',
    root: {
        text: "Home",
        expanded:true
    },
    useArrows: true,
    viewConfig: {
        toggleOnDblClick: false,
        plugins: {
            ptype: 'treeviewdragdrop'
        },
        
        listeners : {
            drop: onTreeNodeDrop
            //itemclick: onTreeNodeClick
        }
                        
    },
    ///-----PlugIns
    plugins:[
            cellEditor
        ],
    ///-----Dockers
    dockedItems: [{
        xtype: 'toolbar',
        items: [
        {
            text: '[+]',
            handler: function(){
                tree.expandAll();
            }
        }
        ,{
            text: '[-]',
            handler: function(){
                tree.collapseAll();
            }
        }
        ,{
            text: '+Folder',
            handler: function(){
                tree.collapseAll();
            }
        }
        ]
    }]
});