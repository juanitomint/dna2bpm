//----Define the data model 4 cases
Ext.define('caseModel', {
    extend:"Ext.data.Model",
    fields:[
        'id',
        'idwf',
        'iduser',
        'user',
        'date',
        'status',
        'locked'
    ]
});
//----Define the data model 4 tokens
Ext.define('tokenModel', {
    extend:"Ext.data.Model",
    fields:[
        'icon',
        'title',
        'resourceId',
        'idwf',
        'iduser',
        'user',
        'date',
        'type',
        'status',
        'lockedBy',
        'run'
    ]
});

/*
             * Business Process – Key Performance Indicators

    KPI: Percentage of processes where completion falls within +/- 5% of the estimated completion
    KPI: Average process overdue time
    KPI: Percentage of overdue processes
    KPI: Average process age
    KPI: Percentage of processes where the actual number assigned resources is less than planned number of assigned resources
    KPI: Sum of costs of “killed” / stopped active processes
    KPI: Average time to complete task
             */
            
Ext.create('Ext.data.Store', {
    id:'filterStore',
    fields: ['filter','name'],
    data : [
    {
        'filter':'user',
        'name':'By User'
    },

    {
        'filter':'group',
        'name':'By Group'
    },

    {
        'filter':'idwf',
        'name':'By Model'
    },
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
    id:'caseStore',
    autoLoad: false,
    model: 'caseModel',
    pageSize: 20,
    proxy: {
        type: 'ajax',
        api: {
            create  : globals.module_url+'case_manager/data/create/'+globals.idwf,
            read    : globals.module_url+'case_manager/data/read/'+globals.idwf,
            update  : globals.module_url+'case_manager/data/update/'+globals.idwf,
            destroy : globals.module_url+'case_manager/data/destroy/'+globals.idwf        
        },
        url: globals.module_url+'case_manager/data/read/'+globals.idwf,  // url that will load data with respect to start and limit params
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
    ,
    listeners:{
        load: function(me){
            if(globals.idcase){
           // mygrid.selModel().selected(mygrid.store.find('id',globals.idcase));
            }
        }
    }
});

//--4 token Grid
var tokenstore = Ext.create('Ext.data.Store', {
    id:'tokenStore',
    autoLoad: false,
    model: 'tokenModel',
    proxy: {
        type: 'ajax',
        url: globals.module_url+'case_manager/tokens/read/'+globals.idwf,  // url that will load data with respect to start and limit params
        noCache: false,
        timeout: 120*1000,//---set timeout to 120 seconds
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