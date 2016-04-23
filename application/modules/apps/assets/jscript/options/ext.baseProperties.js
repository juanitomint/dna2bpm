//----define cache object
pgridCache={};
if(!Ext.getStore('optionsStore')){

    Ext.create('Ext.data.Store', {
        id:'optionsStore',
        autoLoad: false,
        model: 'Options',
        proxy: {
            type: 'ajax',
            url: globals.module_url+'form/get_options',  // url that will load data with respect to start and limit params
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


    var hidden = new Ext.form.Checkbox();
    var locked = new Ext.form.Checkbox();
    var desc=Ext.create('Ext.form.TextArea', {});
    var help=Ext.create('Ext.form.TextArea', {});
    var idop=Ext.create('Ext.form.Text',{
        disabled:true,
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
            'idop':'idop',
            'title': 'Title',
            'idrel': 'ID-Rel',
            'idu':'Owner'
        }
        ,
        customEditors:{
            idop:idop
            
        }
        ,
        customRenderers: {
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
                    var ref=Ext.getCmp('propsGrid').store.data.get('idop').data.value;
                    window.open(globals.module_url+'editor/'+ref);

                }
            },
            {
                xtype: 'button',
                text: 'Save',
                icon:globals.base_url+'css/ext_icons/save.gif',
                handler:function(me){
                    var url=globals.module_url+'options/save_options_properties/'+Ext.getCmp('propsGrid').store.data.get('idop').data.value;
                    save_props(url);
                }
            }
            ,{
                xtype: 'button',
                text: 'Refresh',
                icon:globals.base_url+'css/ext_icons/table_refresh.png',
                handler:function(me){
                    load_props(propsGrid.url,propsGrid.idop,true);

                }
            }
            ,{
                xtype: 'button',
                text: 'Preview',
                icon:globals.base_url+'css/ext_icons/preview.gif',
                handler:function(me){
                    load_props(propsGrid.url,propsGrid.idop,true);

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
