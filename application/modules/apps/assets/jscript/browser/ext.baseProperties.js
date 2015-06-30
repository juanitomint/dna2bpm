//----define cache object
pgridCache={};

Ext.override(Ext.grid.PropertyGrid, {
    getProperty: function(prop){
        if(propsGrid.store.getProperty('groups')!=null)
            return propsGrid.store.getProperty(prop).data.value;
    }
});
try{


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
    // Define the model for a State
    Ext.define('Icons', {
        extend: 'Ext.data.Model',
        fields: ['icon']
        
    });
    // ComboBox with a custom item template
    var icon_combo = Ext.create('Ext.form.field.ComboBox', {
        displayField: 'icon',
        store: Ext.create('Ext.data.Store', {
            autoDestroy: true,
            model: 'Icons',
            data: icons
        }),
        queryMode: 'local',

        listConfig: {
            getInnerTpl: function() {
                return '<h5><i class="icon icon-2x {icon}"></i> {icon}</h5>';
            }
        }
    });
    var hidden = new Ext.form.Checkbox();
    var locked = new Ext.form.Checkbox();
    var desc=Ext.create('Ext.form.TextArea', {});
    var help=Ext.create('Ext.form.TextArea', {});
    var idapp=Ext.create('Ext.form.Text',{
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
        layout:'fit',
        source: {},
        sortableColumns:false,
        disabled:true,
        propertyNames: {
            'required': 'Required',
            'container': 'Container',
            'hidden': 'Hidden',
            'locked':'Locked',
            'type':'Type',
            'title':'Title',
            'desc':'Description',
            'help':'Help Text',
            'ident':'Entity',
            'idu':'Owner'
        },
        
        customEditors: {
            'idapp':idapp,
            'icon':icon_combo
        }
        ,
        customRenderers: {           
            //            'hidden': showCheck
            //            ,
            //            'locked': showCheck
            //            ,
            'icon': function (value){
                if(value){
                    return '<i class="icon '+value+'"></i> '+value;
                } else {
                    return value;
                }
            },
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
                    var ref=Ext.getCmp('propsGrid').store.data.get('idapp').data.value;
                    window.open(globals.module_url+'editor/'+ref);
            
                }
            },                   
            {
                xtype: 'button', 
                text: 'Save',
                icon:globals.base_url+'css/ext_icons/save.gif',
                handler:function(me){
                    var url=globals.module_url+'save_app_properties/'+Ext.getCmp('propsGrid').store.data.get('idapp').data.value;
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

}
catch(e)
{
    txt="There was an error on this page: ext.form.baseProperties.js\n\n";
    txt+=e.name + "\n" + e.message+"\n\n";
    txt+="Click OK to continue.\n\n";
    alert(txt);
}
