//----Extra
config.sourceConfig.timelimit =
        {
            displayName: '<i class="icon icon-time"></i><strong> Time Limit</strong>'
        };
//----Extra
var outtypes = Ext.create('Ext.data.Store', {
    fields: ['value', 'name'],
    data: [
        {"value": "out_time", "name": "Cases out of time"},
        {"value": "on_time", "name": "Cases on time"},
    ]
});

config.sourceConfig['list_type'] =
        {
            displayName: 'List Type',
            defaultValue: 'out_time',
            editor: new Ext.form.ComboBox({
                name: 'list_type',
                allowBlank: false,
                store: outtypes,
                displayField: 'name',
                valueField: 'value',
                queryMode: 'local'
                        //,triggerAction: 'all'
            }),
            renderer: function(value) {
                    if (value) {
                        return outtypes.getAt(outtypes.find('value', value)).data.name;
                    } else {
                        return value;
                    }
                }
        };
 
config.sourceConfig['list_records'] =
        {
            displayName: 'Records per Page',
        };
config.sourceConfig['list_fields'] =
        {
            displayName: 'List Fields'
        };
config.sourceConfig['list_detail'] =
        {
            displayName: 'List Detail'
        };
config.sourceConfig['list_detail_template'] =
        {
            displayName: 'List Detail Template'
        };
config.sourceConfig['list_template'] =
        {
            displayName: 'List Template'
        };
//config.sourceConfig['list_type'] =
//        {
//            displayName: 'List Type'
//        };
