//---ACTIONS 4 Context
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
                        iconCls: 'dot-green',
                        checked: n.data.checked
                    };
                n.appendChild(node);
                n.set('leaf', false);
                n.set('iconCls', '');
                tree.store.sync();
            });
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
                            url: globals.module_url + 'rbac_admin/repository/delete',
                            method: 'POST',
                            // define a handler for request success
                            params: {
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
//---Refresh Tree
var reloadTree = Ext.create('Ext.Action', {
    text: 'Reload',
    iconCls: 'x-tbar-loading',
    handler: function() {
        Ext.data.StoreManager.lookup('TreeStore').load();
        tree.uncheck_all()
    }
});
//---Saves checked nodes
var saveTree = Ext.create('Ext.Action', {
    iconCls: 'icon-save',
    text: 'Save',

    handler: function(widget, event) {
        var records = tree.getView().getChecked(),
            paths = new Array();

        Ext.Array.each(records, function(rec) {
            paths.push(rec.data.id);
        });


        tree.setLoading('Saving');
        Ext.Ajax.request({
            // the url to the remote source
            url: globals.module_url + 'rbac_admin/repository/save',
            method: 'POST',
            // define a handler for request success
            params: {
                //---get the active group
                'idgroup': dataview.selModel.getLastSelected().data.idgroup,
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
    }
});
//---4 context menu
var contextMenu = Ext.create('Ext.menu.Menu', {
    title: 'Path Menu',
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

//---TREE
var tree = Ext.create('Ext.tree.Panel', {
    id: 'PermTree',
    title: 'Permissions',
    store: Ext.data.StoreManager.lookup('TreeStore'),
    rootVisible: true,
    useArrows: true,
    frame: false,
    animate: false,
    stateful: true,
    lines: true,
    layout: 'fit',
    uncheck_all: uncheck_all,
    load_checked: load_checked,
    columns: [{
        xtype: 'treecolumn', //this is so we know which column will show the tree
        text: 'Folders',
        flex: 2,
        sortable: true,
        dataIndex: 'text'
    }, {
        text: 'Path',
        flex: 1,
        dataIndex: 'id',
        sortable: true
    }],
    listeners: {
        checkchange: checkchange,
        itemcontextmenu: function(view, rec, node, index, e) {
            e.stopEvent();
            contextMenu.showAt(e.getXY());
            return false;
        }

    },

    dockedItems: [{
        xtype: 'toolbar',
        items: [


            {
                xtype: 'button',
                enableToggle: true,
                text: '[+]',
                //iconCls:'x-tree-icon x-tree-icon-parent',
                toggleHandler: function(button, state) {
                        if (state) {
                            button.setText('[-]');
                            //button.setIconCls('x-grid-tree-node-expanded  x-tree-icon-parent');
                            tree.expandAll();
                        }
                        else {
                            button.setText('[+]');
                            //button.setIconCls('x-tree-icon x-tree-icon-parent');
                            tree.collapseAll();
                        }
                    }
                    //cls:'x-tree-icon x-tree-icon-parent'

            },
            saveTree,
            reloadTree,
            addPath,
            removePath
        ]
    }]
});