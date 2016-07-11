try {
    var PropertiesSave = Ext.create('Ext.Action',
            {
                text: 'Save',
                iconCls: 'icon icon-save',
                handler: function() {
                    //var url = globals.module_url + 'kpi/save_properties/' + Ext.getCmp('propsGrid').store.data.get('idkpi').data.value;
                    var url = globals.module_url + 'kpi/save_properties/';
                    save_props(url);
                }
            });

    var PropertiesDownload = Ext.create('Ext.Action',
            {
                text: 'Download',
                iconCls: 'icon icon-save',
                handler: function() {
                    var url = globals.module_url + 'kpi/download/' + Ext.getCmp('propsGrid').store.data.get('idkpi').data.value;
                    window.location = url;
                }
            });

    //---PROPERTY GRID
    function showCheck(v) {
        if (v) {
            str = "<div align=center><input type='checkbox' checked='checked' DISABLED/></div>";

        } else {
            str = "<div align=center><input type='checkbox' DISABLED/></div>";
        }
        return str;
    }
    function clickToHTML(v) {
        return Ext.util.Format.stripTags(v);

    }
    //---define custom editors for grid
    var hidden = new Ext.form.Checkbox();
    var locked = new Ext.form.Checkbox();
    var desc = Ext.create('Ext.form.TextArea', {});
    var help = Ext.create('Ext.form.TextArea', {});
    var readonly = Ext.create('Ext.form.Text', {
        readOnly: true,
        iconCls: "icon icon-lock"
    });

    var jsonEditor = Ext.create('Ext.form.Text', {
        //readOnly: true,
        iconCls: "icon icon-lock",
        listeners: {
            click: {
                element: 'el', //bind to the underlying el property on the panel
                fn: function() {
                    Ext.create('Ext.window.Window', {
                        title: 'Query Editor',
                        height: 350,
                        width: 600,
                        layout: 'fit',
                        editorId: Ext.getCmp(this.parent().id).editorId,
                        items: {// Let's put an empty grid in just to illustrate fit layout
                            xtype: 'panel',
                            border: false,
                            html: '<div id="jsoneditor"></div>' // One header just for show. There's no data,
                        },
                        listeners: {
                            show: function() {
                                var options = {
                                    mode: 'tree',
                                    modes: ['code', 'form', 'text', 'tree', 'view'], // allowed modes
                                    error: function(err) {
                                        alert(err.toString());
                                    }
                                };
                                var fval = propsGrid.store.getRec(this.editorId).data.value;
                                if (fval) {
                                    json = Ext.JSON.decode(fval);
                                } else {
                                    json = {};
                                }
                                var container = document.getElementById('jsoneditor');
                                globals.jsonEd = new JSONEditor(container, options, json);
                            }
                        },
                        close: function() {
                            propsGrid.store.setValue(this.editorId, Ext.JSON.encode(globals.jsonEd.get()));
                            this.destroy();
                        }
                    }).show();
                }
            },
            startedit: {
                element: 'el', //bind to the underlying el property on the panel
                fn: function() {

                }
            }
        }
    });


    var checkRender = function(value) {
        if (value) {
            rtn = '<div class="text-center"><img class="x-grid-checkcolumn x-grid-checkcolumn-checked" src="data:image/gif;base64,R0lGODlhAQABAID/AMDAwAAAACH5BAEAAAAALAAAAAABAAEAAAICRAEAOw=="></img></div>';
        } else {
            rtn = '<div class="text-center"><input type="checkbox" readonly="readonly"/></div>';
        }
        return rtn;
    }
    var comboFilter = new Ext.form.ComboBox({
        name: 'filter',
        allowBlank: false,
        //store: Ext.getStore('controls')
        store: Ext.getStore('filterStore'),
        displayField: 'name',
        valueField: 'filter',
        queryMode: 'local'
                //,triggerAction: 'all'
    });
    var comboType = new Ext.form.ComboBox({
        name: 'type',
        allowBlank: false,
        //store: Ext.getStore('controls')
        store: Ext.getStore('typeStore'),
        displayField: 'title',
        valueField: 'type',
        queryMode: 'local'
                //,triggerAction: 'all'
    });

    var wtypes = Ext.create('Ext.data.Store', {
        fields: ['value', 'name'],
        data: [
            {"value": "tiles", "name": "Tile"},
            {"value": "widgets", "name": "Widget"},
        ]
    });

    var comboWidget = new Ext.form.ComboBox({
        name: 'widget_type',
        allowBlank: false,
        //store: Ext.getStore('controls')
        store: wtypes,
        displayField: 'name',
        valueField: 'value',
        queryMode: 'local'
    });
// ComboBox with a custom item template
    var comboIcon = Ext.create('Ext.form.field.ComboBox', {
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
    ///---add some flavor to propertyGrid

    config = {
        id: 'propsGrid',
        source: {},
        sortableColumns: false,
        disabled: true,
        sourceConfig: {
            resourceId: {
                //editor:readonly,
                //type:'boolean'
            },
            hidden: {
                displayName: '<i class="icon icon-eye-close"></i> Hidden',
                editor: new Ext.form.Checkbox(),
                renderer: checkRender,
                type: 'boolean'

            },
            locked: {
                displayName: '<i class="icon icon-lock"></i> Locked',
                renderer: checkRender,
                editor: new Ext.form.Checkbox(),
                type: 'boolean'
            },
            'title': {
                displayName: 'Title'
            },
            'idkpi': {
                editor: readonly
            },
            'idwf': {
                editor: readonly
            },
            'filter_extra': {
                displayName: '<i class="icon icon-filter"></i> Filter Complex',
                value: '',
                editor: jsonEditor
            },
            'sort_by': {
                displayName: 'Sort By',
                value: '',
                editor: jsonEditor
            },
            'filter': {
                displayName: '<i class="icon icon-filter"></i> Filter',
                editor: comboFilter,
                renderer: function(value) {
                    if (value) {
                        var filter = Ext.getStore('filterStore');
                        return filter.getAt(filter.find('filter', value)).data.name;
                    } else {
                        return value;
                    }
                }
            },
            'type': {
                displayName: 'Type',
                editor: comboType,
                renderer: function(value) {
                    if (value) {
                        var types = Ext.getStore('typeStore');
                        return types.getAt(types.find('type', value)).data.title;
                    } else {
                        return value;
                    }
                }
            },
            'icon': {
                displayName: 'Icon',
                editor: comboIcon,
            },
            'widget_type': {
                displayName: 'Widget Type',
                editor: comboWidget,
                renderer: function(value) {
                    if (value) {
                        return wtypes.getAt(wtypes.find('value', value)).data.name;
                    } else {
                        return value;
                    }
                }
            },
            'idu': {
                displayName: '<i class="icon icon-user"></i>  User',
                renderer: function(value) {
                    if (value) {
                        Ext.apply(Ext.data.Connection.prototype, {
                            async: false
                        });

                        //---load default options
                        user.load({
                            async: false,
                            params: {
                                'idu': value
                            }
                        });
                        Ext.apply(Ext.data.Connection.prototype, {
                            async: true
                        });
                        //---return idop+text
                        thisuser = user.findRecord('idu', value);
                        return value + ' :: ' + thisuser.data.name + ' ' + thisuser.data.lastname;
                    } else {
                        return value;
                    }
                }
            },
            help: {
                displayName: '<i class="icon icon-info-sign"></i> Help Text',
                renderer: clickToHTML
            }
            ,
            desc: {
                displayName: 'Description',
                renderer: clickToHTML
            }
        }


        ////////////////////////////////////////////////////////////////////////////
        //////////////////////   LISTENERS    /////////////////////////////////////
        ////////////////////////////////////////////////////////////////////////////

        ,
        listeners: {
            propertychange: function(source, recordId, value, oldValue, options) {
                //console.log('source',source,'recordId','recordId',this.activeRecord,value,oldValue,options);            
                var ds = mygrid.store.data.getAt(mygrid.store.data.keys.indexOf(this.activeRecord));
                //---change data on mygrid
                if (ds)
                    ds.data[recordId] = value;
                //---update cache
                pgridCache[this.activeRecord] = this.getSource();
                //---finally refresh the grid
                mygrid.getView().refresh(true);
            }
        },
        ////////////////////////////////////////////////////////////////////////////
        //////////////////////   DOCKERS    ////////////////////////////////////////
        ////////////////////////////////////////////////////////////////////////////
        tbar: {
            id: 'propsGridTbar',
            items: [
                PropertiesSave
                        , {
                            xtype: 'button',
                            text: 'Refresh',
                            iconCls: 'icon icon-repeat',
                            handler: function(me) {
                                if (mygrid.selModel.getSelection()[0]) {

                                    load_props(propsGrid.url, propsGrid.idkpi, true);
                                } else {
                                    propsGrid.setSource({});
                                }

                            }
                        }
                , {
                    xtype: 'button',
                    text: 'Preview',
                    iconCls: 'icon icon-desktop',
                    handler: function(me) {
                        //load_props(propsGrid.url, propsGrid.idkpi, true);

                    }
                }
                , PropertiesDownload
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
catch (e)
{
    txt = "There was an error on this page: ext.baseProperties.js\n\n";
    txt += e.name + "\n" + e.message + "\n\n";
    txt += "Click OK to continue.\n\n";
    alert(txt);
}
