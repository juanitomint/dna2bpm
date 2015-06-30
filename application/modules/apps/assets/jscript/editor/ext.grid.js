var newObject = function() {
    Ext.create('Ext.window.Window', {
        title: "New Object",
        id: 'newObjWindow',
        height: 400,
        width: 300,
        layout: 'fit',
        items: {
            xtype: 'form',
            bodyPadding: 8,
            url: globals.module_url + 'forms/create/' + globals.idapp,
            items: [{
                    xtype: 'textfield',
                    name: 'title',
                    fieldLabel: 'Title',
                    allowBlank: false
                }, //---4 types
                {
                    xtype: 'fieldset',
                    flex: 1,
                    title: 'Type',
                    defaultType: 'radio', // each item will be a radio button
                    layout: 'anchor',
                    defaults: {
                        anchor: '100%',
                        hideEmptyLabel: false
                    },
                    items: [{
                        name: 'type',
                        inputValue: 'D',
                        fieldLabel: '',
                        boxLabel: 'D :: Object Definition'
                    }, {
                        name: 'type',
                        inputValue: 'V',
                        fieldLabel: '',
                        boxLabel: 'V :: View'
                    }, 
                    {
                        name: 'type',
                        inputValue: 'Q',
                        fieldLabel: '',
                        boxLabel: 'Q :: Search Form'
                    },
                    {
                        name: 'type',
                        inputValue: 'L',
                        fieldLabel: '',
                        boxLabel: 'L :: List View'
                    },
                    {
                        name: 'type',
                        inputValue: 'P',
                        fieldLabel: '',
                        boxLabel: 'P :: Printable Form'
                    },
                    {
                        name: 'type',
                        inputValue: 'X',
                        fieldLabel: '',
                        boxLabel: 'X :: Export View'
                    }
                    ]
                }
            ],
            buttons: [{
                text: 'Submit',
                handler: function() {
                    // The getForm() method returns the Ext.form.Basic instance:
                    var form = this.up('form').getForm();
                    if (form.isValid()) {
                        // Submit the Ajax request and handle the response
                        form.submit({
                            success: function(form, action) {
                                //Ext.Msg.alert('Success', action.result.msg);
                                //---refresh grid view.
                                mygrid.store.read();
                                //---select recently created

                                //---close window.
                                Ext.getCmp('newObjWindow').close();
                            },
                            failure: function(form, action) {
                                Ext.Msg.alert('Failed', action.result.msg);
                            }
                        });
                    }
                }
            }]

        }
    }).show();
}

function gridClick(view, record, item, index, e, options) {
    //console.log(view,record,record.internalId,item,index,e,options );
    //console.log(record.internalId);
    var thisid = (record.data.idform) ? record.data.idform : '';
    var type = record.data.type;
    var internalId = record.internalId;
    Ext.getCmp('rightPanel').setLoading(true);

    //------LOAD propGrid Template
    Ext.Ajax.request({
        // the url to the remote source
        url: globals.module_url + 'get_form_template/' + type,
        method: 'POST',
        // define a handler for request success
        success: function(response, options) {
            Ext.getCmp('rightPanel').setLoading(false);
            Ext.getCmp('propsGridTbar').enable();
            panel = Ext.getCmp('rightPanel');
            panel.remove('propsGrid', true);
            eval(response.responseText);
            panel.add(propsGrid);

            propsGrid.idform = thisid
            var url = globals.module_url + 'get_form_properties/' + type + thisid;
            //--------------------------------------------------------------
            //--------LOAD PROPERTIES---------------------------------------
            //--------------------------------------------------------------
            load_props(url, internalId);
        },
        // NO errors ! ;)
        failure: function(response, options) {
            alert('Error Loading:' + response.err);
            propsGrid.setLoading(false);

        }
    }); //-- Ext.Ajax.request

}

//---4 in place locking editor
var checkLock = Ext.create('Ext.ux.CheckColumn', {
    xtype: 'checkcolumn',
    header: 'Locked',
    dataIndex: 'locked',
    width: 60,
    editor: {
        xtype: 'checkbox',
        cls: 'x-grid-checkheader-editor'
    }
});

var checkHidden = Ext.create('Ext.ux.CheckColumn', {
    xtype: 'checkcolumn',
    header: 'Hidden',
    dataIndex: 'hidden',
    width: 60,
    editor: {
        xtype: 'checkbox',
        cls: 'x-grid-checkheader-editor'
    }
});

//var sm = Ext.create('Ext.selection.CheckboxModel');
//--uncoment this when check bug it's fixed
//sm={};
//var mygrid=Ext.create('Ext.ux.LiveSearchGridPanel',
var mygrid = Ext.create('Ext.grid.Panel', {
    columnLines: true,

    id: 'centerGrid',
    indexes: ['title', 'idframe'],
    store: dgstore,
    columns: [
        Ext.create('Ext.grid.RowNumberer'), {
            text: "ID",
            width: 90,
            dataIndex: 'idform',
            sortable: true

        }, {
            text: "Title",
            width: 320,
            dataIndex: 'title',
            sortable: true
        }, {
            text: "Type",
            dataIndex: 'type',
            sortable: true
        },
        checkLock, checkHidden
    ],
    stripeRows: true,
    viewConfig: {
        //autoScroll:true,
        //        stripeRows: true,
        plugins: {
            ptype: 'gridviewdragdrop',
            ddGroup: 'objects'
        },
        listeners: {

            beforedrop: function(node, data, overModel, position, dropFunction, options) {
                //console.log(node,data,overModel,position,dropFunction,options );
                var me = this;
                if (data.copy) {
                    //---get the index within the grid
                    var index = node.viewIndex;
                    if (position !== 'before') {
                        index++;
                    }
                    //---make a copy of the item
                    var itemadd = data.records[0].copy(Ext.id()); //---take one item only 
                    this.store.insert(index, itemadd);
                    //---TODO load pgrid with propper type; if new
                    //return 0;
                    return false;
                }
                else {
                    return 0;
                }
            },

            drop: function(node, data, dropRec, dropPosition) {
                //console.log(data.records[0].data);
                var dropOn = dropRec ? ' ' + dropPosition + ' ' + dropRec.get('title') : ' on empty view';
                //---mark dirty so gets saved
                data.records[0].dirty = true;

            },
            itemclick: gridClick
        }
    },

    ////////////////////////////////////////////////////////////////////////////
    //////////////////////   DOCKERS    ////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////
    tbar: [{
        xtype: 'button',
        text: 'Save App',
        icon: globals.base_url + 'css/ext_icons/save.gif',
        tooltip: 'Save changes',
        handler: function() {
            Ext.Object.each(mygrid.store.data.items, function(index) {
                mygrid.store.data.items[index].dirty = true
            });
            mygrid.store.sync();
            //mygrid.store.read();
        }
    }, {
        xtype: 'button',
        text: 'Reload',
        icon: globals.base_url + 'css/ext_icons/refresh.gif',
        tooltip: 'Reload from db an discard changes',
        handler: function() {
            mygrid.store.read();
        }
    }, {
        xtype: 'button',
        text: 'Preview',
        icon: globals.base_url + 'css/ext_icons/preview.gif',
        tooltip: 'Preview Form',
        handler: function() {
            url = globals.base_url + 'dna2/render/go/' + idobj;
            window.open(url);
        }
    }, {
        xtype: 'button',
        text: 'New Object',
        icon: globals.base_url + 'css/ext_icons/new_tab.gif',
        tooltip: 'Preview Form',
        handler: newObject
    }]


});

////////////////////////////////////////////////////////////////////////////////
//////////////////////// BEGIN ALL Objs GRID////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
var groupingOther = Ext.create('Ext.grid.feature.Grouping', {
    groupHeaderTpl: '{name} ({rows.length})', //print the number of items in the group
    startCollapsed: false // start all groups collapsed
});

//var othergrid=Ext.create('Ext.grid.Panel', {
var othergrid = Ext.create('Ext.ux.LiveFilterGridPanel', {
    //title:'All Similar Frames Available',
    columnLines: true,
    stripeRows: true,
    id: 'otherGrid',
    indexes: ['title', 'idobj'],
    store: otherstore,
    features: [groupingOther],

    //---start model
    //    selModel: sm,    
    columns: [
        //Ext.create('Ext.grid.RowNumberer'),
        {
            text: "idobj",
            width: 90,
            dataIndex: 'idobj',
            sortable: true

        }, {
            text: "Title",
            width: 320,
            dataIndex: 'title',
            sortable: true
        }, {
            text: "Type",
            dataIndex: 'type',
            sortable: true
        }

    ],
    viewConfig: {
        autoScroll: false,
        stripeRows: true,
        plugins: {
            ptype: 'gridviewdragdrop',
            ddGroup: 'objects'
        },
        listeners: {
            itemclick: gridClick
        }
    }


});

//otherstore.load();
dgstore.load();