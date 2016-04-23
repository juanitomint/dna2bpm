var newObject = function() {
    var internalId = null;
    var url = globals.module_url + 'options/get_options_properties/';
    load_props(url, internalId);
}



var mygrid = Ext.create('Ext.grid.Panel', {
    //  var mygrid=Ext.create('Ext.ux.LiveSearchGridPanel',{
    // title: 'Options',
    store: Ext.data.StoreManager.lookup('optionsDefault'),
    columns: [
        Ext.create('Ext.grid.RowNumberer', {
            text: '#',
            flex: 1,
            align:'center'
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
});