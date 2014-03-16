//---ACTIONS 4 Context
var FolderAdd = Ext.create('Ext.Action', {
    iconCls: 'icon-add-folder',
    text: 'Add Folder',
    handler: function(widget, event) {
        var n = tree.getSelectionModel().getSelection()[0];
        if (!n.isLeaf()) {
            Ext.MessageBox.prompt('Model', 'Please Folder name:', function(btn,text){
                if(btn=='ok' && text){
                    
                    node={
                        id: text,
                        text    : text +' <span class="text-new">[new]</span>',
                        leaf    : false,
                        loaded: true
                    };
                    n.appendChild(node);
                    n.set('leaf',false);
                    n.set('iconCls','folder');
                }
            }
            );
        } else {
            //---show message
            Ext.MessageBox.alert('Error!', "'Can't add a Folder here");
        }
    }
});
function get_path(n){
    parents=new Array(n.data.id);
    while(n=n.parentNode){
        parents.unshift(n.data.id)
    }
    root=parents.splice(0,1);
    return parents.join('/');
}
var ModelAdd = Ext.create('Ext.Action', {
    iconCls: ' icon-plus-sign',
    text: 'Add Model',
    handler: function(widget, event) {
        var n = tree.getSelectionModel().getSelection()[0];
        if(n){
            
            if (!n.isLeaf()) {
                Ext.MessageBox.prompt('Model', 'Please enter Model id:', function(btn,text){
                    if(btn=='ok' && text){
                    
                        node={
                            id: n.data.id+'/'+text,
                            text    : text +' <span class="text-new">[new]</span>',
                            leaf    : true,
                            iconCls : 'dot-gray',
                            checked : n.data.checked
                        };
                        tree.setLoading('saving...');
                        Ext.Ajax.request({
                            // the url to the remote source\/test-call-activity
                            url: globals.module_url+'/repository/add/model',
                            method: 'POST',
                            params:{
                            
                                'idwf':text,
                                'folder':get_path(n)
                            },
                            // define a handler for request success
                            success: function(response, options){
                                tree.setLoading(false);
                                n.appendChild(node);                 
                            },
                            // NO errors ! ;)
                            failure: function(response,options){
                                alert('Error Loading:'+response.err);
                                tree.setLoading(false);
                
                            }
                        });
                    }
                });
            } else {
                //---show message
                Ext.MessageBox.alert('Error!', "'Can't add a model here");
            }
        } else {
            Ext.MessageBox.alert('Error!', "'Nothing selected");
        }
    }
});
var ModelRemove = Ext.create('Ext.Action', {
    iconCls: 'icon-minus-sign',
    text: 'Remove Model',
    handler: function(widget, event) {
        var n = tree.getSelectionModel().getSelection()[0];
        if (n && n.isLeaf()) {
            Ext.MessageBox.show({
                title: 'What, really remove model?',
                msg: 'Are you sure?',
                buttons: Ext.MessageBox.YESNO,
                buttonText:{ 
                    yes: "Definitely!", 
                    no: "No chance!" 
                },
                fn: function(btn){
                    if(btn=='yes'){
                        m=n.parentNode;
                        path=n.data.id;
                        n.remove();
                        //---remove from repo
                        tree.setLoading('Saving');
                        Ext.Ajax.request({
                            // the url to the remote source
                            url: globals.module_url+'repository/delete/model',
                            method: 'POST',
                            params:{
                                //---get the active node
                                'idwf':n.data.id
                            },
                            // define a handler for request success
                            success: function(response, options){
                                tree.setLoading(false);
                            },
                            // NO errors ! ;)
                            failure: function(response,options){
                                alert('Error Loading:'+response.err);
                                tree.setLoading(false);
                
                            }
                        });
                        
                    }
                }
            });
            
        }
    }
});
//---Refresh Tree
var reloadTree = Ext.create('Ext.Action', {
    text:'Reload',
    iconCls:'icon-refresh',
    handler:function(){
        Ext.data.StoreManager.lookup('TreeStore').reload();
        //tree.load_checked(dataview.selModel.getLastSelected().data.idgroup);
    }
});

//---4 context menu
var contextMenu = Ext.create('Ext.menu.Menu', {
    title:'Path Menu',
    items: [
    FolderAdd,ModelAdd,ModelRemove
    ]
});
    
//---set all child checked
function checkchange (node, checked) {
    node.eachChild(function(n) {
        n.set('checked',checked);
        if(n.childNodes.length){
            checkchange(n,checked);
        }
    });
}
function uncheck_all(){
    root=tree.store.tree.getRootNode();
    root.set('checked',false);
    checkchange(root,false);
}
/* 
 * Click Function
 * 
 * Load Model Data in center panel
 * 
 * @todo repeated clicks comes cached
 */
var TreeClick=function(widget,event){
    n=widget.getSelectionModel().getSelection()[0];
    //---only do something if its leaf=model
    if(n && n.isLeaf()){
        
        //url='http://localhost/dna2.gitorious/bpm/repository/load/model/ksemilla-listados/json';
        options={
            
        'url': globals.module_url+'admin/get_model/'+n.data.id
        }
        //center_panel.body.load(options);
        //---prevent not loading
        var first=false;
        load_model(n.data.id);

    }
}
/*
 * Double click function
 * 
 * Opens model editor in a new window
 *  
 */
var TreeDblClick=function(widget,event){
    n=widget.getSelectionModel().getSelection()[0];
    //---only do something if its leaf=model
    if(n && n.isLeaf()){
        strUrl=globals.base_url+'jscript/bpm/editor.xhtml#model/'+n.data.id.split('/').pop();
        window.open(strUrl);
    }
}
/*
 * function move node
 * 
 * moves a node from one point to another
 * 
 */

function move_node(n,path){
    tree.setLoading('Saving data...');
    Ext.Ajax.request({
        // the url to the remote source
        url: globals.module_url+'/repository/update_folder',
        method: 'POST',
        params:{
                            
            'idwf':n.data.id,
            'folder':path
        },
        // define a handler for request success
        success: function(response, options){
            tree.setLoading(false);
        },
        // NO errors ! ;)
        failure: function(response,options){
            alert('Error Loading:'+response.err);
            tree.setLoading(false);
                
        }
    });
}

function move_folder(folder,path){
    folder.eachChild(function(n) {
        if(n.isLeaf()){
            move_node(n,path);
        } else {
            path=get_path(n);
            move_folder(n,path);
        }
    });
}
//---TREE
var tree=Ext.create('Ext.tree.Panel', {
    id:'ModelTree',
    store: Ext.data.StoreManager.lookup('TreeStore'),
    //deferRowRender:true,
    animate: false,
    rootVisible: true,
    useArrows: true,
    //layout:'fit',
    //sortOnDrop:true,
    uncheck_all:uncheck_all,
    /*
     *  VIEWCONFIG
     */
    
    viewConfig: {
        plugins: {
            ptype: 'treeviewdragdrop'
        },
        listeners:{
            beforedrop:function ( node, data, overModel, dropPosition, dropHandler, eOpts ){
                if(overModel.isLeaf()){
                    return false
                }
            },
            drop: function ( node, data, overModel, dropPosition, eOpts) {
                //console.log(data.records[0].data);
                s=1;
                n=data.records[0];                 
                if(n.isLeaf()){
                    //---DROPED a NODE
                    move_node(n,get_path(overModel));
                } else{
                    //---DROPED a FOLDER
                    move_folder(n,get_path(overModel));
                    
                }
                
            }
        }
            
    },
    /*
     * LISTENERS
     */
    listeners:{
        checkchange:checkchange,
        itemcontextmenu: function(view, rec, node, index, e) {
            e.stopEvent();
            contextMenu.showAt(e.getXY());
            return false;
        },
        
        itemclick: TreeClick,
        itemdblclick: TreeDblClick
            
    },

    dockedItems: [{
        xtype: 'toolbar',
        items: [
            
        
        {
            xtype:'button',
            enableToggle: true,
            text:'[+]',
            //iconCls:'x-tree-icon x-tree-icon-parent',
            toggleHandler: function(button,state){
                if(state){
                    button.setText('[-]');
                    //button.setIconCls('x-grid-tree-node-expanded  x-tree-icon-parent');
                    tree.expandAll();
                }else{
                    button.setText('[+]');
                    //button.setIconCls('x-tree-icon x-tree-icon-parent');
                    tree.collapseAll();
                }
            }
        //cls:'x-tree-icon x-tree-icon-parent'
            
        }
        ,
        //saveTree,
        reloadTree,
        ModelAdd,
        ModelRemove
        ]
    }]
});