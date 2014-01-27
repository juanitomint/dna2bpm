//---PROPERTY GRID+


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
var readOnly=Ext.create('Ext.form.Text',{
    disabled:true,
    cls:"locked"
});
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
        'title' :'Title', //----Section/module
        'target' :'Target',
        'text' :'Text',
        'cls' :'Class',
        'iconCls' :'IconClass',
        'priority' :'Priority',
        'info' :'Info', 
    }
    ,
    customEditors: {
    id:readOnly
    }
    ,
    customRenderers: {}
    ////////////////////////////////////////////////////////////////////////////
    //////////////////////   LISTENERS    /////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////
    
    ,
    listeners:{
        propertychange: function(source,recordId,value,oldValue,options){
            //console.log('source',source,'recordId','recordId',this.activeRecord,value,oldValue,options);            
            //---update cache
            //pgridCache[this.activeRecord]=this.getSource();
            //---finally refresh the grid
            thisData=this.getSource();
            node=tree.store.getNodeById(this.activeRecord)
            node.data['data']=thisData;
            node.data['text']=thisData.text;
            tree.getView().refresh(true);
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
                var url=globals.module_url+'admin/save_properties';
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
                createCodeWindow('Server Side Script Hooks for:'+ref,hooksPHP,this.id,globals.base_url+'dna2/form/code',ref);
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
                createCodeWindow('Client Side Scripts Hooks for:'+ref,hooksJS,this.id,globals.base_url+'dna2/form/code', ref);
            }
        }
        ]
    }
});



