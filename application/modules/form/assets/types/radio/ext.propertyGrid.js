//----  This file describes common properties 
//      that apply to all options driven widgets
//
//----create model 4 options
Ext.define('Options', {
    extend: 'Ext.data.Model',
    fields: ['idop','title']
});
//----create model 4 Default options
Ext.define('Option', {
    extend: 'Ext.data.Model',
    fields: ['value','text']
});
//----create datastore 4 options if not exists
//var optionsStore = 
if(!Ext.getStore('optionsStore')){
    
    Ext.create('Ext.data.Store', {
        id:'optionsStore',
        autoLoad: false,
        model: 'Options',
        proxy: {
            type: 'ajax',
            url: globals.module_url+'get_options',  // url that will load data with respect to start and limit params
            noCache: false,
            reader: {
                type: 'json',
                root: 'rows',
                totalProperty: 'totalCount'
            }
            
        }
        ,
        listeners:{
            load:function(){
                
            }
        }
    });
}
//---set synchronous loading on this one to avoid problems with rendering
Ext.apply(Ext.data.Connection.prototype, {
    async: false
});
Ext.getStore('optionsStore').load();
//---restore async property to the default value
Ext.apply(Ext.data.Connection.prototype, {
    async: true
});
//----Create store for default options if not exist
if(!Ext.getStore('optionsDefault')){
    optionsDefault = Ext.create('Ext.data.Store', {
        id:'optionsDefault',
        autoLoad: false,
        model: 'Option',
        proxy: {
            type: 'ajax',
            url: globals.module_url+'get_option',  // url that will load data with respect to start and limit params
            noCache: false,
            reader: {
                type: 'json',
                root: 'rows',
                totalProperty: 'totalCount'
            }
        }
    });
}
//-----Create combo 4 options
var comboOptions= new Ext.form.ComboBox({
    name            : 'idop',
    allowBlank     : false,
    store: Ext.getStore('optionsStore'),
    displayField: 'title' ,
    valueField: 'idop',
    queryMode: 'local'
});
//-----Create combo 4 Default
var comboDefault= new Ext.form.ComboBox({
    name            : 'default',
    allowBlank     : false,
    store:Ext.getStore('optionsDefault') ,
    displayField: 'text',
    valueField: 'value',
    queryMode: 'local'
});

var rendererOptions=function(value){
    if(value){
        var optionsStore=Ext.getStore('optionsStore');
        var optionsDefault=Ext.getStore('optionsDefault');
        //---load default options
        optionsDefault.load({
            params:{
                'idop':value
            }
            ,//---fix asyncloading problem
            callback: function(){
                propsGrid.setProperty('default',propsGrid.store.getProperty('default').data.value);
            }    
        });                
        //---return idop+text
        //wait to optionsStore to finish
        //---Safe renderer: if still loading return plain value
        return value+' :: '+optionsStore.findRecord('idop',value,0,false,true,true).data.title;
        if(!optionsStore.loading){
        } else {
            return value;
        }
    } else {
        return value;
    }
};
var rendererDefault=function(value) {
    //return value+text
    if(value){
        var optionsDefault=Ext.getStore('optionsDefault') ;
        //---safe return value if still loading
        if(!optionsDefault.loading){
            return (optionsDefault.findRecord('value',value))?value+' :: '+optionsDefault.findRecord('value',value).data.text:value;
        } else {
            return value;
        }
    } else {
        return value;
    }
            
};
//----Extra propertyNames
config.propertyNames['idop']='Options Src';
config.propertyNames['default']='Option default';
config.propertyNames['default']='Option default';

//----Extra editors
config.customEditors['idop']=comboOptions;
config.customEditors['default']=comboDefault;
//----Extra Renders
config.customRenderers['idop']=rendererOptions;
config.customRenderers['default']=rendererDefault;