var pgridCache = {};
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
/*
 *      Tree Store
 */
Ext.define('TreeModel', {
    extend: 'Ext.data.Model',
    fields: ['priority', 'title', 'type', 'group', 'locked', 'hidden', 'required']
});
function onTreeStoreLoad() {
    //tree.load_checked();
}
Ext.define('MenuItem', {
    extend: 'Ext.data.Model',
    fields: [
        'title',
        'target',
        'text',
        'cls',
        'iconCls',
        'priority',
        'info',
    ]
}
);
Ext.create('Ext.data.TreeStore', {
    id: "TreeStore",
    autoLoad: false,
    allowSingle: false,
    model: MenuItem,
    proxy: {
        type: 'ajax',
        noCache: false, //---get rid of the ?dc=.... in urls
        api: {
            create: globals.module_url + 'admin/repository/'+globals.repoId+'/create',
            read: globals.module_url + 'admin/repository/'+globals.repoId+'/read',
            update: globals.module_url + 'admin/repository/'+globals.repoId+'/update',
            destroy: globals.module_url + 'admin/repository/'+globals.repoId+'/destroy'
        },
        writer: {
            type: 'json',
            allowSingle: false
        }
    },
    sorters: [
        {
            property: 'priority',
            direction: 'ASC'
        },
        {
            property: 'leaf',
            direction: 'ASC'
        },
        {
            property: 'text',
            direction: 'ASC'
        }
    ],
    listeners: {
        load: onTreeStoreLoad
    }

});

