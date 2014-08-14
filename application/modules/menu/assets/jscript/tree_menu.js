var priority = 0;
//---ACTIONS 4 Context
function recalculate_tree() {
    priority = 0;
    tree.getRootNode().cascade(function(rec) {
        if (!rec.isRoot()) {
            //---fix node paths
            rec.set('path', rec.getPath());
            rec.set('priority', 10 * priority++);
        }
    });
}
function itemclick(me, record, item, index, e, eOpts) {
    id = record.data.id;
    //load_props(globals.module_url + 'admin/get_properties', id, true);
    //propsGrid.setSource(record.data);
    node = Ext.create(MItem_clear, record.data);
    propsGrid.setSource(node.data);
    propsGrid.setLoading(false);
    if (id != 'root') {
        propsGrid.enable();
    } else {
        propsGrid.disable();
        propsGrid.setLoading("Can't touch Root node");
    }

}
var addPath = Ext.create('Ext.Action', {
    iconCls: 'icon-add',
    text: 'Add Path',
    handler: function(widget, event) {
        var n = tree.getSelectionModel().getSelection()[0];
        n.set('expanded', true);
        n.set('leaf', false);
        if (n) {
            Ext.MessageBox.prompt('MenuItem', 'Please enter obj name or funcion:<br/>You can use comma separated for multiple add<br/>ie: pico,nano,micro,mili', function(btn, text) {
                if (btn == 'ok' && text)
                    text_arr = text.split(',');
                for (i in text_arr) {
                    node = {
                        id: Ext.id(null, text_arr[i] + '_'),
                        text: text_arr[i],
                        leaf: true,
                        //expanded: true,
                        //children: []
                    };
                    ;
                    new_node = n.appendChild(node);
                    recalculate_tree();
                    //tree.store.sync();
                }
            }
            );
        }
    }
});
var removePath = Ext.create('Ext.Action', {
    iconCls: 'icon-delete',
    text: 'Remove Path',
    handler: function(widget, event) {
        var n = tree.getSelectionModel().getSelection()[0];
        if (n) {
            Ext.MessageBox.show({
                title: 'What, really remove path?',
                msg: 'Are you sure?',
                buttons: Ext.MessageBox.YESNO,
                buttonText: {
                    yes: "Definitely!",
                    no: "No chance!"
                },
                fn: function(btn) {
                    if (btn == 'yes') {
                        m = n.parentNode;
                        path = n.data.path;
                        n.remove();
                        //---remove from repo
                        tree.setLoading('Saving');
                        Ext.Ajax.request({
                            // the url to the remote source
                            url: globals.module_url + 'repository/' + globals.repoId + '/delete',
                            method: 'POST',
                            // define a handler for request success
                            params: {
                                //---get the active group
                                'path': path
                            },
                            success: function(response, options) {
                                tree.setLoading(false);
                            },
                            // NO errors ! ;)
                            failure: function(response, options) {
                                alert('Error Loading:' + response.err);
                                tree.setLoading(false);

                            }
                        });

                    }
                }
            });

        }
    }
});
var saveTree = Ext.create('Ext.Action', {
    iconCls: 'icon-save',
    text: 'Save',
    handler: function(widget, event) {

        paths = new Array();

        tree.getRootNode().cascade(function(rec) {
            //---fix node paths
            rec.set('path', rec.getPath());
            rec.setDirty();
        });
        tree.store.sync();
        /*
         Ext.Ajax.request({
         // the url to the remote source
         url: globals.module_url + 'admin/repository/sync',
         method: 'POST',
         // define a handler for request success
         params: {
         //---get the active group
         'paths[]': paths
         },
         success: function(response, options) {
         tree.setLoading(false);
         },
         // NO errors ! ;)
         failure: function(response, options) {
         alert('Error Loading:' + response.err);
         tree.setLoading(false);
         
         }
         });
         */
    }
});
//---4 context menu
var contextMenu = Ext.create('Ext.menu.Menu', {
    title: 'Menu Item',
    items: [
        addPath, removePath
    ]
});

//---set all child checked
function checkchange(node, checked) {
    node.eachChild(function(n) {
        n.set('checked', checked);
        if (n.childNodes.length) {
            checkchange(n, checked);
        }
    });
}
function uncheck_all() {
    root = tree.store.tree.getRootNode();
    root.set('checked', false);
    checkchange(root, false);
}


//---Refresh Tree
var reloadTree = Ext.create('Ext.Action', {
    text: 'Reload',
    iconCls: 'x-tbar-loading',
    handler: function() {
        Ext.data.StoreManager.lookup('TreeStore').load();
    }
});
//---TREE
var tree = Ext.create('Ext.tree.Panel', {
    id: 'MenuTree',
    title: 'Menu',
    xtype: 'tree-grid',
    store: Ext.data.StoreManager.lookup('TreeStore'),
    root: {
        text: "Home",
        iconCls: 'icon-home',
        expanded: true,
        path: '/root'
    },
    rootVisible: true,
    useArrows: true,
    frame: false,
    animate: false,
    stateful: true,
    lines: true,
    uncheck_all: uncheck_all,
    columns: [{
            xtype: 'treecolumn', //this is so we know which column will show the tree
            text: 'Items',
            flex: 2,
            sortable: true,
            dataIndex: 'text'
        }
        , {
            text: 'path',
            flex: 2,
            sortable: true,
            dataIndex: 'path'
        }
        , {
            text: 'priority',
            flex: 1,
            sortable: true,
            dataIndex: 'priority'
        }

    ],
    viewConfig: {
        plugins: {
            ptype: 'treeviewdragdrop'

        },
        listeners: {
            drop: function(dom_node, data, overModel, dropPosition, eOpts) {
                recalculate_tree();
            }
        }

    },
    listeners: {
        checkchange: checkchange,
        itemdblclick: addPath,
        itemclick: itemclick,
        itemcontextmenu: function(view, rec, node, index, e) {
            e.stopEvent();
            contextMenu.showAt(e.getXY());
            return false;
        }

    },
    dockedItems: [{
            xtype: 'toolbar', items: [
                reloadTree,
                addPath,
                removePath,
                saveTree
            ]
        }]
});