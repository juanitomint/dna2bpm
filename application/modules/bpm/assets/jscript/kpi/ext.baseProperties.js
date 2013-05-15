try{
    
    
    var PropertiesSave=Ext.create('Ext.Action',
    {
        text: 'Save',
        iconCls:'icon icon-save',
        handler:function(){
            var url=globals.module_url+'kpi/save_properties/'+Ext.getCmp('propsGrid').store.data.get('idkpi').data.value;
            save_props(url);
        }
    });

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
    var readonly=Ext.create('Ext.form.Text',{
        readOnly:true,
        iconCls:"icon icon-lock"
    });
    var checkRender=function(value){
        if(value){
            rtn='<div class="text-center"><img class="x-grid-checkcolumn x-grid-checkcolumn-checked" src="data:image/gif;base64,R0lGODlhAQABAID/AMDAwAAAACH5BAEAAAAALAAAAAABAAEAAAICRAEAOw=="></img></div>';
        } else {
            rtn='<div class="text-center"><input type="checkbox" readonly="readonly"/></div>';
        }
        return rtn;
    }
    comboFilter = new Ext.form.ComboBox({
        name            : 'filter',
        allowBlank     : false,
        //store: Ext.getStore('controls')
        store: Ext.getStore('filterStore'),
        displayField: 'name',
        valueField: 'filter',
        queryMode: 'local'
    //,triggerAction: 'all'
    });
    comboType = new Ext.form.ComboBox({
        name            : 'type',
        allowBlank     : false,
        //store: Ext.getStore('controls')
        store: Ext.getStore('typeStore'),
        displayField: 'title',
        valueField: 'type',
        queryMode: 'local'
    //,triggerAction: 'all'
    });

    ///---add some flavor to propertyGrid

    config={
        id:'propsGrid',
        source: {},
        sortableColumns:true,
        disabled:true,
        sourceConfig:{
            resourceId:{
                //editor:readonly,
                //type:'boolean'
            },
            hidden: {
                displayName:'<i class="icon icon-eye-close"></i> Hidden',
                editor:new Ext.form.Checkbox(),
                renderer:checkRender,
                type:'boolean'
                
            },
            locked:{
                displayName:'<i class="icon icon-lock"></i> Locked',
                renderer:checkRender,
                editor:new Ext.form.Checkbox(),
                type:'boolean'
            },
            'title':{
                displayName:'Title'
            },
            
            
            'idkpi':{
                editor:readonly
            },
            'idwf':{
                editor:readonly
            },
            
            'filter':{ 
                displayName:'<i class="icon icon-filter"></i> Filter',
                editor:comboFilter,
                renderer:function(value){
                    if(value){
                        var filter=Ext.getStore('filterStore');
                        return filter.getAt(filter.find('filter',value)).data.name;
                    } else {
                        return value;
                    }
                }  
            },
            type:{ 
                displayName:'Type',
                editor: comboType,
                renderer:function(value){
                    if(value){
                        var types=Ext.getStore('typeStore');
                        return types.getAt(types.find('type',value)).data.title;
                    } else {
                        return value;
                    }
                } 
            },
            'idu':{
                displayName:'<i class="icon icon-user"></i>  User',
                renderer:function(value){
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
            },
            help:{
                displayName:'<i class="icon icon-info-sign"></i> Help Text',
                renderer:clickToHTML
            } 
            ,
            desc: {
                displayName:'Description',
                renderer:clickToHTML
            }
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
            PropertiesSave
            ,{
                xtype: 'button', 
                text: 'Refresh',
                iconCls:'icon icon-repeat',
                handler:function(me){
                    if(mygrid.selModel.getSelection()[0]){
                        
                        load_props(propsGrid.url,propsGrid.idkpi,true);                              
                    } else {
                        propsGrid.setSource({});  
                    }
            
                }
            }
            ,{
                xtype: 'button', 
                text: 'Preview',
                iconCls:'icon icon-desktop',
                handler:function(me){
                    load_props(propsGrid.url,propsGrid.idkpi,true);                              
            
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
    txt="There was an error on this page: ext.baseProperties.js\n\n";
    txt+=e.name + "\n" + e.message+"\n\n";
    txt+="Click OK to continue.\n\n";
    alert(txt);
}
