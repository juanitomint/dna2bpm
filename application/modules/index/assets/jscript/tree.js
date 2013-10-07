//---ACTIONS 4 Context

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

    tree.uncheck_all();
    paths = [];
    tree.setLoading('loading Checked');
    Ext.Ajax.request({
        // the url to the remote source
        url: globals.module_url + 'admin/getpaths',
        method: 'POST',
        params: {
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
        },
        {
            text: 'Path',
            flex: 1,
            dataIndex: 'id',
            sortable: true
        }
    ],
    listeners: {
        checkchange: checkchange,
    },
    dockedItems: [{
            xtype: 'toolbar', items: [
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
                        } else {
                            button.setText('[+]');
                            //button.setIconCls('x-tree-icon x-tree-icon-parent');
                            tree.collapseAll();
                        }
                    }
                    //cls:'x-tree-icon x-tree-icon-parent'

                }
                ,
                reloadTree
            ]
        }]
});