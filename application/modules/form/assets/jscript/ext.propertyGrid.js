//---PROPERTY GRID+
var comboType = new Ext.form.ComboBox({
    name            : 'type',
    allowBlank     : false,
    store: Ext.getStore('controls')
    ,
    displayField: 'name'
    ,
    valueField: 'type'

    //    store           : [
    //        ['text','Text Box']
    //        ,['textarea','Textarea']
    //        ,['radio','Radio Buttons']
    //        ,['combo','Radio Buttons']
    //    ],
    //typeAhead    : true,
    ,
    queryMode: 'local'
//,triggerAction: 'all'
});
//----create model 4 options
Ext.define('Options', {
    extend: 'Ext.data.Model',
    fields: ['idop','title']
});
Ext.define('Option', {
    extend: 'Ext.data.Model',
    fields: ['value','text']
});
//----create datastore 4 options
var optionsStore = Ext.create('Ext.data.Store', {
    id:'optionsStore',
    autoLoad: true,
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
});
var optionsDefault = Ext.create('Ext.data.Store', {
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
    store: Ext.getStore('optionsDefault'),
    displayField: 'text',
    valueField: 'value',
    queryMode: 'local'
});
function showCheck(v){
    if (v) {
        str="<input type='checkbox' checked='checked' DISABLED/>";
                
    } else {
        str="<input type='checkbox' DISABLED/>";
    }
    return str;
}
//---define custom editors for grid
var required= new Ext.form.Checkbox({});
var hidden = new Ext.form.Checkbox();
var locked = new Ext.form.Checkbox();
var desc=Ext.create('Ext.form.TextArea', {});
var help=Ext.create('Ext.form.TextArea', {});
var cname=Ext.create('Ext.form.Text',{
    disabled:true,
    cls:"locked"
});
var idframe=Ext.create('Ext.form.Text',{
    disabled:true,
    cls:"locked"
});
editPHP=function (me,options ){
    var name=me.title+'Code';
    obj=me.getEl().select('textarea').elements[0];
    editAreaLoader.init({
        id :obj.id,
        syntax: "php",
        start_highlight: true
    });
};

///---add some flavor to propertyGrid
Ext.override(Ext.grid.PropertyGrid, {
    getProperty: function(prop){
        return propsGrid.store.getProperty(prop).data.value;
    }
});
var propsGrid = Ext.create('Ext.grid.property.Grid', {
    id:'propsGrid'
    //,layout:'fit'
    //    ,
    //    title:"Props"
    ,
    source: {}
    ,
    sortableColumns:false
    ,
    disabled:true,
    propertyNames: {
        'required': 'Required',
        'hidden': 'Hidden',
        'locked':'Locked',
        'type':'Type',
        'title':'Title',
        'desc':'Description',
        'help':'Help Text',
        'idop':'Options Src',
        'default':'Option default',
        'removed':'Removed Options'
    }
    ,
    customEditors: {
        'type': comboType,
        'desc':desc,
        'help':help,
        'idop':comboOptions,
        'default':comboDefault,
        'cname':cname,
        'idframe':idframe,
        'required'  : required,
        'hidden'  : hidden,
        'locked'  : locked
    }
    ,
    customRenderers: {
        'required': showCheck
        ,
        'hidden': showCheck
        ,
        'locked': showCheck
        ,
        'type': function(value){
            if(value){
                return controls.getAt(controls.find('type',value)).data.name;
            } else {
                return value;
            }
        }
        ,
        'idop': function(value){
            if(value){
                //---load default options
                optionsDefault.load({
                    async:false,
                    params:{
                        'idop':value
                    }
                    ,//---fix asyncloading problem
                    callback: function(){
                        propsGrid.setProperty('default',propsGrid.store.getProperty('default').data.value);
                    }    
                });                
                //---return idop+text
                return value+' :: '+optionsStore.findRecord('idop',value,0,false,true,true).data.title;
            } else {
                return value;
            }
        }
        ,
        'default': function(value){
            //return value+text
            if(value){                
                return (optionsDefault.findRecord('value',value))?value+' :: '+optionsDefault.findRecord('value',value).data.text:value;
            } else {
                return value;
            }
            
        }
    }
    ////////////////////////////////////////////////////////////////////////////
    //////////////////////   LISTENERS    /////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////
    
    ,
    listeners:{
        propertychange: function(source,recordId,value,oldValue,options){
            //console.log('source',source,'recordId','recordId',this.activeRecord,value,oldValue,options);            
            var ds=mygrid.store.data.getAt(mygrid.store.data.keys.indexOf(this.activeRecord));
            //---change data on mygrid
            if(ds)
                ds.data[recordId]=value;
            //---update cache
            pgridCache[this.activeRecord]=this.getSource();
            //---finally refresh the grid
            mygrid.getView().refresh(true);
        }
    },
    ////////////////////////////////////////////////////////////////////////////
    //////////////////////   DOCKERS    ////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////
    tbar:{
        id:'propsGridTbar', 
        items:[
                                

        {
            xtype: 'button', 
            text: 'Save',
            icon:globals.base_url+'css/ext_icons/save.gif',
            handler:function(){
                var url=globals.module_url+'save_frame_properties/'+globals.idobj;
                save_props(url);
            }
        }
        ,{
            xtype: 'button', 
            text: 'Refresh',
            icon:globals.base_url+'css/ext_icons/table_refresh.png',
            handler:function(me){
                load_props(propsGrid.url,propsGrid.idframe,true);                              
            
            }
        }
        ,{
            xtype: 'button', 
            text: 'Preview',
            icon:globals.base_url+'css/ext_icons/preview.gif',
            handler:function(me){
                load_props(propsGrid.url,propsGrid.idframe,true);                              
            
            }
        }
        ,{
            xtype: 'button',
            //text: " <span class='hasPHP'> PHP </span>",
            icon:globals.base_url+'css/ext_icons/php.png',
            id:'codeBtnPHP',
            tooltip:'Server Side Hooks',
            handler: function(){
                var ref=Ext.getCmp('propsGrid').store.data.get('cname').data.value;
                createCodeWindow('Server Side Script Hooks for:'+ref,hooksPHP,this.id,globals.module_url+'code',ref);
            }
        },
        {
            xtype: 'button',
            //text: "<span class='hasJS'> JS </span>",
            icon:globals.base_url+'css/ext_icons/js.png',
            id:'codeBtnJS',
            tooltip:'Client Side Hooks',
            handler: function(){
                var ref=propsGrid.store.data.get('cname').data.value;
                createCodeWindow('Client Side Scripts Hooks for:'+ref,hooksJS,this.id,globals.module_url+'code', ref);
            }
        }
        ]
    }
});



