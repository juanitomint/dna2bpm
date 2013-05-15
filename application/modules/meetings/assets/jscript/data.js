/*
 *                      BUSINESS
 */
Ext.define('Business', {
    extend: 'Ext.data.Model',
    fields: [
    'id',
    'name',
    'cuit',
    'products',
    {
        name: 'accredited', 
        type: 'bool'
    }
    
    ]
});


Ext.create('Ext.data.Store', {
    id:'BusinessStore',
    model: 'Business',
    autoLoad: false,
    proxy: {
        type: 'ajax',
        noCache: false,//---get rid of the ?dc=.... in urls
        method:'post',
        api: {
            create  : globals.base_url+'meetings/admin/business/create',
            read    : globals.base_url+'meetings/admin/business/read',
            update  : globals.base_url+'meetings/admin/business/update',
            destroy : globals.base_url+'meetings/admin/business/destroy'
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
        property: 'idBusiness',
        direction: 'ASC'
    }]
    ,
    listeners:{
        load: onBusinessStoreLoad
    }
    
});

function onBusinessStoreLoad(){
    //---select first Business in list.
    dataview.selModel.select(0)
}
/*
 *                      USERS
 */
Ext.define('User', {
    extend: 'Ext.data.Model',
    fields: [
    'idu',
    'nick',
    'name',
    'lastname',
    'email',
    'phone',
    'idnumber',
    'Business',
    {
        name: 'locked', 
        type: 'bool'
    },
    {
        name: 'disabled', 
        type: 'bool'
    }
    ]
});

   
Ext.create('Ext.data.Store', {
    id:'UserStore',
    model: 'User',
    pageSize: 50,
    autoLoad: false,
    proxy: {
        type: 'ajax',
        noCache: false,//---get rid of the ?dc=.... in urls
        method:'post',
        api: {
            create  : globals.base_url+'user/admin/user/create',
            read    : globals.base_url+'user/admin/user/read',
            update  : globals.base_url+'user/admin/user/update',
            destroy : globals.base_url+'user/admin/user/destroy'
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
        property: 'idu',
        direction: 'ASC'
    }]
});
/*
 *    Empty store for user Businesss
 */

Ext.create('Ext.data.Store', {
    id:'UserBusinessStore',
    model: 'Business',
    sorters: [{
        property: 'name',
        direction: 'ASC'
    }]
});
/*
 *      Tree Store
 */

function onTreeStoreLoad(){
    //tree.load_checked(dataview.selModel.getLastSelected().data.idBusiness);
}
Ext.create('Ext.data.TreeStore', {
    id:"TreeStore",
    autoLoad: false,
    proxy: {
        type: 'ajax',
        noCache: false,//---get rid of the ?dc=.... in urls
        url: globals.base_url+'user/rbac_admin/repository/read'
    },
    sorters: [{
        property: 'leaf',
        direction: 'ASC'
    }, {
        property: 'text',
        direction: 'ASC'
    }],
    listeners:{
        load: onTreeStoreLoad
    }
        
});
