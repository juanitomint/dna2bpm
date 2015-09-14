//----define cache object
pgridCache={};

Ext.override(Ext.grid.PropertyGrid, {
    getProperty: function(prop){
        return propsGrid.store.getProperty(prop).data.value;
    }
});
// try{


    //---PROPERTY GRID
    function showCheck(v){
        if (v) {
            str="<div align=center><input type='checkbox' checked='checked' DISABLED/></div>";
                
        } else {
            str="<div align=center><input type='checkbox' DISABLED/></div>";
        }
        return str;
    }
    function clickToHTML(v){
        return Ext.util.Format.stripTags(v);
        
    }
    //---define custom editors for grid
    var hidden = new Ext.form.Checkbox();
    var locked = new Ext.form.Checkbox();
    var desc=Ext.create('Ext.form.TextArea', {});
    var help=Ext.create('Ext.form.TextArea', {});
    var idform=Ext.create('Ext.form.Text',{
        readOnly:true,
        cls:"locked"
    });
    var idobj=Ext.create('Ext.form.Text',{
        readOnly:true,
        cls:"locked"
    });
    var idu=Ext.create('Ext.form.Text',{
        readOnly:true,
        cls:"locked"
    });


    ///---add some flavor to propertyGrid

    config={
        id:'propsGrid',
        source: {},
        sortableColumns:false,
        disabled:true,
        propertyNames: {
            'title':'Title',
            'required': 'Required',
            'container': 'Container',
            'hidden': 'Hidden',
            'locked':'Locked',
            'type':'Type',
            'desc':'Description',
            'help':'Help Text',
            'ident':'Entity',
            'idu':'Owner'
        },
        customEditors: {},
        /*
            'type': Ext.create('Ext.form.ComboBox', {
                store: Ext.getStore('typeStore'),
                queryMode: 'local',
                valueField: 'ftype',
                displayField: 'name'
            }),
            'desc':Ext.create('Ext.ux.htmlwindow', {}),
            'help':Ext.create('Ext.ux.htmlwindow', {}),
            'ident': Ext.create('Ext.form.ComboBox',{
                allowBlank      : false,
                valueField      : 'ident',
                displayField    : 'name',
                store           : Ext.getStore('Entities'),
                queryMode       : 'local'
            //,triggerAction: 'all'
            }),
            'idobj':idobj,
            'idform':idform,
            'idu':idu,
            'hidden'  : hidden,
            'locked'  : locked
        }
        ,*/
        customRenderers: {           
            //            'hidden': showCheck
            //            ,
            //            'locked': showCheck
            //            ,
            'type': function(value){
                if(value){
                    var types=Ext.getStore('typeStore');
                    return types.getAt(types.find('ftype',value)).data.name;
                } else {
                    return value;
                }
            }        
            ,
            'ident': function(value){
                
                if(value){
                    var entities=Ext.getStore('Entities');
                    return entities.getAt(entities.find('ident',value)).data.name;
                } else {
                    return value;
                }
            }
            //WIP
            ,
            'idu':function(value){
                if(value){
                    Ext.apply(Ext.data.Connection.prototype, {
                        async: false
                    });
                    
                    //---load default options
                    user.load({
                        async:false,
                        params:{
                            'idu':value
                        }
                    });  
                    Ext.apply(Ext.data.Connection.prototype, {
                        async: true
                    });
                    //---return idop+text
                    thisuser=user.findRecord('idu',value);
                    return value+' :: '+thisuser.data.name+' '+thisuser.data.lastname;
                } else {
                    return value;
                }
            }
            ,
            help: clickToHTML
            ,
            desc: clickToHTML
    
        
        }
    
        ////////////////////////////////////////////////////////////////////////////
        //////////////////////   LISTENERS    /////////////////////////////////////
        ////////////////////////////////////////////////////////////////////////////
    
        ,
        listeners: {
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
                text: 'Edit',
                icon:globals.base_url+'css/ext_icons/edit.png',
                handler:function(me){
                    var ref=Ext.getCmp('propsGrid').store.data.get('idobj').data.value;
                    window.open(globals.base_url+'form/editor/'+ref);
            
                }
            },                   
            {
                xtype: 'button', 
                text: 'Save',
                icon:globals.base_url+'css/ext_icons/save.gif',
                handler:function(me){
                    var url=globals.module_url+'save_form_properties/'+Ext.getCmp('propsGrid').store.data.get('idobj').data.value;
                    save_props(url);
                }
            }
            ,{
                xtype: 'button', 
                text: 'Refresh',
                icon:globals.base_url+'css/ext_icons/table_refresh.png',
                handler:function(me){
                    load_props(propsGrid.url,propsGrid.idapp,true);                              
            
                }
            }
            ,{
                xtype: 'button', 
                text: 'Preview',
                icon:globals.base_url+'css/ext_icons/preview.gif',
                handler:function(me){
                    load_props(propsGrid.url,propsGrid.idapp,true);                              
            
                }
            }
            
            ,{
                xtype: 'button',
                //text: " <span class='hasPHP'> PHP </span>",
                icon:globals.base_url+'css/ext_icons/php.png',
                id:'codeBtnPHP',
                tooltip:'Server Side Hooks',
                handler: function(){
                    var ref=Ext.getCmp('propsGrid').store.data.get('idobj').data.value;
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
                    var ref=Ext.getCmp('propsGrid').store.data.get('idobj').data.value;
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
    var propsGrid = Ext.create('Ext.grid.property.Grid', config);
//var propsGrid = Ext.create('Ext.ux.propertyGrid', config);

// }
// catch(e)
// {
//     txt="There was an error on this page: ext.form.baseProperties.js\n\n";
//     txt+=e.name + "\n" + e.message+"\n\n";
//     txt+="Click OK to continue.\n\n";
//     alert(txt);
// }
