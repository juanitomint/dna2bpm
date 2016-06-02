//----Define the data model 4 forms
Ext.define('kpiModel', {
    extend: "Ext.data.Model",
    fields: ['idkpi', 'idwf', 'title', 'type', 'hidden', 'locked', 'category']
});
Ext.define('Icons', {
    extend: 'Ext.data.Model',
    fields: ['icon']

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
    id: 'filterStore',
    fields: ['filter', 'name'],
    data: [
        {
            'filter': 'user',
            'name': 'By User'
        },
        {
            'filter': 'group',
            'name': 'By Group'
        },
        {
            'filter': 'idwf',
            'name': 'By Model'
        },
        {
            'filter': 'owner',
            'name': 'By Case Owner'
        },
    ]
});
Ext.create('Ext.data.Store', {
    id: 'typeStore',
    model: 'kpiModel',
    groupField: 'category',
    data: [
        {
            category: "Business Process - Key Performance Indicators",
            type: "state",
            idwf: globals.idwf,
            title: "Cases are at this point"
        },
        {
            category: "Business Process - Key Performance Indicators",
            type: "count_cases",
            idwf: globals.idwf,
            title: "Cases that have passed this point"
        },
        {
            category: "Business Process - Key Performance Indicators",
            type: "time_avg",
            idwf: globals.idwf,
            title: "Average time to complete task"
        },
        {
            category: "Business Process - Key Performance Indicators",
            type: "time_avg_all",
            idwf: globals.idwf,
            title: "Average process age"
        },
        {
            category: "Service Level Agreement (SLA) - Key Performance Indicators",
            title: "Percentage of service requests resolved within an agreed-upon/acceptable period of time",
            idwf: globals.idwf,
            type: "sla_time"
        }
        ,
        {
            category: "Goals",
            title: "Ammount of cases by filter(model,group,user)",
            idwf: globals.idwf,
            type: "goal_ammt"
        }
        ,
        {
            category: "Goals",
            title: "Ammount of cases by filter(model,group,user) by date",
            idwf: globals.idwf,
            type: "goal_ammt_time"
        }

    ]
});

var user = Ext.create('Ext.data.Store', {
    id: 'owner',
    autoLoad: false,
    fields: ['idu', 'name', 'lastname', 'nick'],
    proxy: {
        type: 'ajax',
        url: globals.base_url + 'user/util/get_user', // url that will load data with respect to start and limit params
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
    id: 'viewStore',
    autoLoad: true,
    model: 'kpiModel',
    proxy: {
        type: 'ajax',
        api: {
            create: globals.module_url + 'kpi/data/create/' + globals.idwf,
            read: globals.module_url + 'kpi/data/read/' + globals.idwf,
            update: globals.module_url + 'kpi/data/update/' + globals.idwf,
            destroy: globals.module_url + 'kpi/data/destroy/' + globals.idwf
        },
        url: globals.module_url + 'kpi/data/read/' + globals.idwf, // url that will load data with respect to start and limit params
        noCache: false,
        reader: {
            type: 'json',
            root: 'rows',
            totalProperty: 'totalCount'
        },
        writer: {
            type: 'json',
            allowSingle: false
        }

    }
});