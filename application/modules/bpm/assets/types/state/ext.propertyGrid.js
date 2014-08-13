//----Extra
config.sourceConfig['list_records'] =
        {
            displayName: 'Records per Page',
        };
config.sourceConfig['list_fields'] =
        {
            displayName: 'List Fields',
            editor: jsonEditor
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

/*
 var statuses = Ext.create('Ext.data.Store', {
 fields: ['name'],
 data: [
 {"name": ""},
 {"name": "finished"},
 {"name": "manual"},
 {"name": "user"},
 {"name": "pending"},
 {"name": "waiting"}
 //...
 ]
 });
 
 // Create the combo box, attached to the states data store
 ;
 config.sourceConfig['status'] = {
 displayName: 'Status',
 editor: Ext.create('Ext.form.ComboBox', {
 store: statuses,
 queryMode: 'local',
 displayField: 'name',
 valueField: 'name',
 })
 } */   