//---ACTIONS 4 Context
function itemclick(me, record, item, index, e, eOpts) {
    id = record.data.id;
    load_props(globals.module_url + 'admin/get_properties', id, true);
}
var addPath = Ext.create('Ext.Action', {
    iconCls: 'icon-add',
    text: 'Add Path',
    handler: function(widget, event) {
        var n = tree.getSelectionModel().getSelection()[0];
        if (n) {
            Ext.MessageBox.prompt('Path', 'Please enter obj name or funcion:', function(btn, text) {
                if (btn == 'ok' && text)
                    node = {
                        id: n.data.id + '/' + text,
                        text: text,
                        leaf: true,
                        priority: 10,
                    };
                n.appendChild(node);
                n.set('leaf', false);
                n.set('iconCls', '');
                tree.store.sync();
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
                        path = n.data.id;
                        n.remove();
                        //---remove from repo
                        tree.setLoading('Saving');
                        Ext.Ajax.request({
                            // the url to the remote source
                            url: globals.module_url + 'admin/repository/delete',
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
//---Saves checked nodes
var saveTree = Ext.create('Ext.Action', {
    iconCls: 'icon-save',
    text: 'Save',
    handler: function(widget, event) {
        tree.sync();
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

//---load checked nodes
function load_checked() {
    idgroup = dataview.selModel.getLastSelected().data.idgroup;
    tree.uncheck_all();
    paths = [];
    tree.setLoading('loading Checked');
    Ext.Ajax.request({
        // the url to the remote source
        url: globals.module_url + 'rbac_admin/getpaths',
        method: 'POST',
        params: {
            //---get the active group
            'idgroup': idgroup
        },
        // define a handler for request success
        success: function(response, options) {
            obj = Ext.decode(response.responseText);
            obj.paths.forEach(function(path) {
                if (path.length) {
                    n = tree.store.tree.getNodeById(path);
                    if (n)
                        n.set('checked', true);
                }
            });
            tree.setLoading(false);
        },
        // NO errors ! ;)
        failure: function(response, options) {
            alert('Error Loading:' + response.err);
            tree.setLoading(false);

        }
    });
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
            dataIndex: 'id'
        }
        , {
            text: 'priority',
            flex: 1,
            sortable: true,
            dataIndex: 'priority'
        }

    ],
    listeners: {
        checkchange: checkchange,
        itemclick: itemclick,
        itemcontextmenu: function(view, rec, node, index, e) {
            e.stopEvent();
            contextMenu.showAt(e.getXY());
            return false;
        }

    },
    dockedItems: [{
            xtype: 'toolbar', items: [
                saveTree,
                reloadTree,
                addPath,
                removePath
            ]
        }]
});