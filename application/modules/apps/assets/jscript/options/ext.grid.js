var newObject = function() {
    var internalId = null;
    var url = globals.module_url + 'options/get_options_properties/';
    load_props(url, internalId);
}



var mygrid = Ext.create('Ext.grid.Panel', {
    //  var mygrid=Ext.create('Ext.ux.LiveSearchGridPanel',{
    title: 'Options',
    store: Ext.data.StoreManager.lookup('optionsDefault'),
    columns: [{
        text: 'Value',
        dataIndex: 'value',
        editor: {
            allowBlank: false
        }
    }, {
        text: 'Text',
        dataIndex: 'text',
        flex: 1,
        editor: {
            allowBlank: false
        }
    }, {
        text: 'idrel',
        dataIndex: 'idrel',
        editor: {
            allowBlank: true
        }
    }],
    viewConfig: {
        //autoScroll:true,
        stripeRows: true,
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