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