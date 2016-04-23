function comboSelect(combo, records, eOpts) {
    var value = combo.value;
    optionsDefault.load({
        params: {
            idop: value
        }
    });
    propsGrid.idop=value;
    var url=globals.module_url+'options/get_options_properties/'+value;
    load_props(url,value);
}

var comboOptions = new Ext.form.ComboBox({
    name: 'idop',
    allowBlank: false,
    store: Ext.getStore('optionsStore'),
    displayField: 'title',
    valueField: 'idop',
    queryMode: 'local',

    listeners: {
        'select': comboSelect
    }
});