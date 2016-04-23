Ext.define('Options', {
    extend: 'Ext.data.Model',
    fields: ['idop','title']
});
Ext.define('Option', {
    extend: 'Ext.data.Model',
    fields: ['value','text','idrel']
})
//----create datastore 4 options
var optionsStore = Ext.create('Ext.data.Store', {
    id:'optionsStore',
    autoLoad: true,
    model: 'Options',
    proxy: {
        type: 'ajax',
        url: globals.module_url+'options/get_options',  // url that will load data with respect to start and limit params
        noCache: false,
        reader: {
            type: 'json',
            root: 'rows',
            totalProperty: 'totalCount'
        }
    }
});

var optionsDefault = Ext.create('Ext.data.Store', {
    id:'optionsDefault',
    autoLoad: false,
    model: 'Option',
    proxy: {
        type: 'ajax',
        url: globals.module_url+'options/get_option',  // url that will load data with respect to start and limit params
        noCache: false,
        // extraParams:{idop:39}, //for default parameters to load
        reader: {
            type: 'json',
            root: 'rows',
            totalProperty: 'totalCount'
        }
    }
});