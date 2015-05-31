try{

//---PROPERTY GRID+
var comboType = new Ext.form.ComboBox({
    name            : 'type',
    allowBlank     : false,
    //store: Ext.getStore('controls')
    store: controls,
    displayField: 'title',
    valueField: 'type',
    queryMode: 'local'
//,triggerAction: 'all'
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


///---add some flavor to propertyGrid

config={
    id:'propsGrid',
    source: {},
    sortableColumns:false,
    disabled:true,
    propertyNames: {
        'required': 'Required',
        'hidden': 'Hidden',
        'locked':'Locked',
        'type':'Type',
        'title':'Title',
        'desc':'Description',
        'help':'Help Text'      
    },
    customEditors: {
        'type': comboType,
        'desc':desc,
        'help':help,
        'cname':cname,
        'idframe':idframe,
        'required'  : required,
        'hidden'  : hidden,
        'locked'  : locked
    }
    ,
    customRenderers: {
    /*    'required': showCheck
        ,
    /    'hidden': showCheck
        ,
    /    'locked': showCheck
        ,
        */
        'type': function(value){
            if(value){
                return controls.getAt(controls.find('type',value)).data.name;
            } else {
                return value;
            }
        }        
        
    }
    
    ////////////////////////////////////////////////////////////////////////////
    //////////////////////   LISTENERS    /////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////
    
    ,
    listeners: {
        propertychange: function(source,recordId,value,oldValue,options){
            //console.log('source',source,'recordId','recordId',this.activeRecord,value,oldValue,options);            
            ds=mygrid.store.data.getAt(mygrid.store.data.keys.indexOf(this.activeRecord));
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
};
//------------------------------------------------------------------------------
//-------here the custom config-------------------------------------------------
//------------------------------------------------------------------------------
//{customProps}
//------------------------------------------------------------------------------
propsGrid = Ext.create('Ext.grid.property.Grid', config);
}
catch(e)
  {
  txt="There was an error on this page.\n\n";
  txt+=e.name + "\n" + e.message+"\n\n";
  txt+="Click OK to continue.\n\n";
  alert(txt);
  }