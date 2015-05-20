//----Define the data model 4 forms
Ext.define('FormsModel', {
    extend:"Ext.data.Model",
    fields:['idform', 'title','type','hidden','locked']
});

Ext.define('AppsModel', {
    extend:"Ext.data.Model",
    fields:['idapp', 'title','type','hidden','locked','icon']
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
        "name":"Print Out"
    },

    {
        "ftype":"Q",
        "name":"Search Form"
    },

    {
        "ftype":"P",
        "name":"Printable Form"
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
function renderGroups(){
    store=Ext.data.StoreManager.lookup('GroupStore');
    grid=Ext.getCmp('UserGroupGrid');
    grid.store.removeAll();
    groupField={};
    groupField.value=propsGrid.getProperty('groups');
    if(groupField.value){
        groups=groupField.value.split(',');  
        for(i in groups){
            value=groups[i];
            record=store.getAt(store.find('idgroup',value));
            if(record){
                grid.store.add(record);
            }
        //html+=record.data.name+'<br/>';
        }
    }
    
}

var dgstore = Ext.create('Ext.data.Store', {
    id:'viewStore',
    autoLoad: false,
    model: 'AppsModel',
    proxy: {
        type: 'ajax',
        api: {
            read    : globals.module_url+'action/read/',
            create  : globals.module_url+'action/create/',
            update  : globals.module_url+'action/update/',
            destroy : globals.module_url+'action/destroy/'        
        },
        url:globals.module_url+'action/read/'+globals.idapp,
        noCache: false,
        reader: {
            type: 'json',
            root: 'rows',
            totalProperty: 'totalCount'
        }
        
    },
    listeners:{
        load: renderGroups
    }
});
/*
 *                      GROUPS
 */

Ext.define('Group', {
    extend: 'Ext.data.Model',
    fields: [
    'idgroup',
    'name',
    'desc',
    {
        name: 'disabled', 
        type: 'bool'
    },
    'users'
    ]
});

/*
 *    Empty store for user groups
 */


Ext.create('Ext.data.Store', {
    id:'UserGroupStore',
    model: 'Group',
    sorters: [{
        property: 'name',
        direction: 'ASC'
    }]
});

Ext.create('Ext.data.Store', {
    id:'GroupStore',
    model: 'Group',
    autoLoad: false,
    proxy: {
        type: 'ajax',
        noCache: false,//---get rid of the ?dc=.... in urls
        method:'post',
        api: {
            create  : globals.base_url+'user/admin/group/create',
            read    : globals.base_url+'user/admin/group/read',
            update  : globals.base_url+'user/admin/group/update',
            destroy : globals.base_url+'user/admin/group/destroy'
        },
        reader: {
            type: 'json',
            root: 'rows',
            totalProperty: 'totalCount'
        },
        writer: {
            type: 'json'
        }
    },
    sorters: [{
        property: 'idgroup',
        direction: 'ASC'
    }]
    ,
    listeners:{
        //load: onGroupStoreLoad
    }
    
});