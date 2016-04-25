var newObject = function() {
    var internalId = null;
    var url = globals.module_url + 'options/get_options_properties/';
    load_props(url, internalId);
}

function confirm(result) {
    if (result == 'yes') {
        optionsDefault.remove(this);
        // dgstore.sync();
    }
}


var mygrid = Ext.create('Ext.grid.Panel', {
    //  var mygrid=Ext.create('Ext.ux.LiveSearchGridPanel',{
    // title: 'Options',
    store: Ext.data.StoreManager.lookup('optionsDefault'),
    columns: [{
            menuDisabled: true,
            sortable: false,
            xtype: 'actioncolumn',
            width: 50,
            items: [{
                icon: globals.module_url + 'assets/images/delete.png', // Use a URL in the icon config
                tooltip: 'Remove Option',
                handler: function(grid, rowIndex, colIndex) {
                    store = Ext.data.StoreManager.lookup('optionsDefault');
                    var rec = store.getAt(rowIndex);
                    Ext.Msg.confirm('Confirm', 'Are you sure you want to remove: ' + rec.get('text') + '?', confirm, rec);

                }
            }]
        },
        Ext.create('Ext.grid.RowNumberer', {
            text: '#',
            flex: 1,
            align: 'center'
        }), {
            text: 'Value',
            flex: 1,
            align: 'center',
            dataIndex: 'value',
            editor: {
                allowBlank: false
            }
        }, {
            text: 'Text',
            dataIndex: 'text',
            flex: 6,
            editor: {
                allowBlank: false
            }
        }, {
            text: 'idrel',
            dataIndex: 'idrel',
            flex: 1,
            editor: {
                allowBlank: true
            }
        }
    ],
    viewConfig: {
        //autoScroll:true,
        stripeRows: true,

        plugins: {
            ptype: 'gridviewdragdrop',
            dragText: 'Drag and drop to reorganize'
        },
        listeners: {
            // itemclick: gridClick
        }
    },
    plugins: [
        Ext.create('Ext.grid.plugin.CellEditing', {
            clicksToEdit: 1
        })
    ],
    bbar: {
        items: [{
            xtype: 'button',
            text: 'Add Option',
            icon: globals.module_url + 'assets/images/add.png',
            handler: function(me) {
                optionsDefault.insert(optionsDefault.count(),{});

            }
        }, ]

    }
});