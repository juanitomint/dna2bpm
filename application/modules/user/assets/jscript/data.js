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


Ext.create('Ext.data.Store', {
    id: 'GroupStore',
    model: 'Group',
    autoLoad: false,
    proxy: {
        type: 'ajax',
        noCache: false, //---get rid of the ?dc=.... in urls
        method: 'post',
        api: {
            create: globals.module_url + 'admin/group/create',
            read: globals.module_url + 'admin/group/read',
            update: globals.module_url + 'admin/group/update',
            destroy: globals.module_url + 'admin/group/destroy'
        },
        reader: {
            type: 'json',
            root: 'rows',
            totalProperty: 'totalCount'
        },
        writer: {
            type: 'json',
            allowSingle: false
        }
    },
    sorters: [{
            property: 'idgroup',
            direction: 'ASC'
        }]
            ,
    listeners: {
        load: onGroupStoreLoad
    }

});

function onGroupStoreLoad() {
    //---select first group in list.
    dataview.selModel.select(0)
}
/*
 *                      USERS
 */
Ext.define('User', {
    extend: 'Ext.data.Model',
    fields: [
        '_id',
        'idu',
        'nick',
        'name',
        'lastname',
        'email',
        'phone',
        'idnumber',
        'group',
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
    id: 'UserStore',
    model: 'User',
    pageSize: 50,
    autoLoad: false,
    proxy: {
        type: 'ajax',
        noCache: false, //---get rid of the ?dc=.... in urls
        method: 'post',
        api: {
            create: globals.module_url + 'admin/user/create',
            read: globals.module_url + 'admin/user/read',
            update: globals.module_url + 'admin/user/update',
            destroy: globals.module_url + 'admin/user/destroy'
        },
        reader: {
            type: 'json',
            root: 'rows',
            totalProperty: 'totalCount'
        },
        writer: {
            type: 'json',
            allowSingle: false
        }
    },
    sorters: [{
            property: 'idu',
            direction: 'ASC'
        }]
});
/*
 *    Empty store for user groups
 */

Ext.create('Ext.data.Store', {
    id: 'UserGroupStore',
    model: 'Group',
    sorters: [{
            property: 'name',
            direction: 'ASC'
        }]
});
/*
 *      Tree Store
 */

function onTreeStoreLoad() {
    tree.load_checked();
}
Ext.create('Ext.data.TreeStore', {
    id: "TreeStore",
    autoLoad: false,
    allowSingle: false,
    proxy: {
        type: 'ajax',
        noCache: false, //---get rid of the ?dc=.... in urls
        api: {
            create: globals.module_url + 'rbac_admin/repository/create',
            read: globals.module_url + 'rbac_admin/repository/read',
            update: globals.module_url + 'rbac_admin/repository/update',
            destroy: globals.module_url + 'rbac_admin/repository/destroy'
        }
    },
    sorters: [{
            property: 'leaf',
            direction: 'ASC'
        }, {
            property: 'text',
            direction: 'ASC'
        }],
    listeners: {
        load: onTreeStoreLoad
    }

});
