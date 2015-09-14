//----Define the data model 4 forms
Ext.define('FormsModel', {
    extend:"Ext.data.Model",
    fields:['idform','idobj', 'title','type','hidden','locked']
});

//----4 propgrid (this are 4 fixed combos)
Ext.create('Ext.data.Store', {
    id:'Entities',
    autoLoad: true,
    fields: ['ident', 'name'],
    proxy: {
                    
        type: 'ajax',
        url : globals.module_url+'get_entities',  // url that will load data with respect to start and limit params,
        reader: {
            type: 'json',
            root: 'entities'
        }
    }
});
            
Ext.create('Ext.data.Store', {
    id:'typeStore',
    fields: ['ftype', 'name'],
    data : [
    {
        "ftype":"V",
        "name":"View"
    },

    {
        "ftype":"D",
        "name":"Definition"
    },

    {
        "ftype":"L",
        "name":"List View"
    },

    {
        "ftype":"Q",
        "name":"Search Form"
    },

    {
        "ftype":"P",
        "name":"Printable Form"
    },
    {
        "ftype":"H",
        "name":"Header View (deprecated)"
    },
    {
        "ftype":"E",
        "name":"Statistics (deprecated)"
    },
    {
        "ftype":"X",
        "name":"Export View"
    }
    ]
});
           
var user=Ext.create('Ext.data.Store', {
    id:'owner',
    autoLoad: false,
    fields: ['idu', 'name','lastname','nick'],
    proxy: {
        type: 'ajax',
        url: globals.base_url+'user/util/get_user',  // url that will load data with respect to start and limit params
        noCache: false,
        reader: {
            type: 'json',
            root: 'rows',
            totalProperty: 'totalCount'
        }
    }
});

//--4 Data Grid
var dgstore = Ext.create('Ext.data.Store', {
    id:'viewStore',
    autoLoad: false,
    model: 'FormsModel',
    proxy: {
        type: 'ajax',
        api: {
            create  : globals.module_url+'forms/create/'+globals.idapp,
            read    : globals.module_url+'forms/read/'+globals.idapp,
            update  : globals.module_url+'forms/update/'+globals.idapp,
            destroy : globals.module_url+'forms/destroy/'+globals.idapp        
        },
        url: globals.module_url+'get_forms/read/'+globals.idapp,  // url that will load data with respect to start and limit params
        noCache: false,
        reader: {
            type: 'json',
            root: 'rows',
            totalProperty: 'totalCount'
        },
        writer:{
            type: 'json',
            allowSingle:false
        }
    }
});

//--4 Data View
var otherstore = Ext.create('Ext.data.Store', {
    id:'otherStore',
    autoLoad: true,
    model: 'FormsModel',
    groupField: 'type',
    proxy: {
        type: 'ajax',
        url: globals.module_url+'get_all_objs',  // url that will load data with respect to start and limit params
        noCache: false,
        reader: {
            type: 'json',
            root: 'rows',
            totalProperty: 'totalCount'
        }
        
    }
});