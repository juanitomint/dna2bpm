function gridClick(view, record, item, index, e, options) {
    thisCase = record.data.id;
    url = globals.module_url + 'case_manager/tokens/status/' + globals.idwf + '/' + thisCase;
    globals.idcase=thisCase;
    tokenStore = Ext.getStore('tokenStore')
    tokenStore.proxy.url = url;
    load_data_callback = tokens_paint_all;
    first = false;
//    tokenStore.load(
//            {
//                scope: this,
//                callback: load_model(globals.idwf)
//            }
//    );
    load_data_callback = function() {
        tokenStore.load(
                {
                    scope: this,
                    callback: tokens_paint_all
                }
        );
    }
    load_model(globals.idwf);
    Ext.getCmp('ModelPanelTbar').enable();
    gridIndex = 0;
}



function confirm(result) {
    if (result == 'yes') {
        gridSel = mygrid.selModel.selected.items[0];
        dgstore.remove(gridSel);
        dgstore.sync();
    }
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
    },
    listeners: {
        checkchange: function(me, rowIndex, checked, eOpts) {
            mygrid.store.sync();
        }
    }

}
);

var checkHidden = Ext.create('Ext.ux.CheckColumn', {
    xtype: 'checkcolumn',
    header: 'Hidden',
    dataIndex: 'hidden',
    width: 60,
    editor: {
        xtype: 'checkbox',
        cls: 'x-grid-checkheader-editor'
    }
}
);

//var sm = Ext.create('Ext.selection.CheckboxModel');
//--uncoment this when check bug it's fixed
//sm={};
//var mygrid=Ext.create('Ext.ux.LiveSearchGridPanel',
var mygrid = Ext.create('Ext.grid.Panel',
        {
            columnLines: true,
            id: 'centerGrid',
            indexes: ['checkdate'],
            store: dgstore,
            columns: [
                {
                    flex: 1,
                    text: "Status",
                    dataIndex: 'status',
                    sortable: true,
                    renderer: function(value) {
                        switch (value) {
                            case 'open':
                                stClass = 'label-warning';
                                break;
                            case 'closed':
                                stClass = 'label-success';
                                break;
                            case 'locked':
                                stClass = '';

                                break;
                            default:
                                stClass = 'label-important';
                                break;

                        }

                        value = '<span class="label ' + stClass + '">' + value + '</span>'
                        return value;
                    }
                },
                {
                    menuDisabled: true,
                    sortable: false,
                    xtype: 'actioncolumn',
                    width: 30,
                    items: [{
                            icon: globals.module_url + 'assets/images/contact-list.png', // Use a URL in the icon config
                            tooltip: 'miniReport',
                            handler: function(grid, rowIndex, colIndex, Item, e) {
                                e.stopEvent();
                                var rec = dgstore.getAt(rowIndex);
                                Ext.create('Ext.window.Window', {
                                    title: 'Mini Report',
                                    height: 400,
                                    width: 300,
                                    layout: 'fit',
                                    autoScroll: true,
                                    loader: {
                                        url: globals.base_url + 'bpm/manager/mini_report/' + globals.idwf + '/' + rec.get('id') + '/html',
                                        autoLoad: true
                                    }
//                                    items: {// Let's put an empty grid in just to illustrate fit layout
//                                        xtype: 'panel',
//                                        border: false,
//                                        html: '<H1>MINI-REPORT:' + rec.get('id') + '</H1>',
//                                    }
                                }).show();
                                return false;
                            }
                        }]
                },
                {
                    menuDisabled: true,
                    sortable: false,
                    xtype: 'actioncolumn',
                    width: 30,
                    items: [{
                            icon: globals.module_url + 'assets/images/comment3_16x16.gif', // Use a URL in the icon config
                            tooltip: 'Process Notifications',
                            handler: function(grid, rowIndex, colIndex, Item, e) {
                                e.stopEvent();
                                var rec = dgstore.getAt(rowIndex);
                                url = globals.base_url + 'bpm/test/send_task/' + globals.idwf + '/' + rec.get('id');
                                window.open(url);
                                return false;
                            }
                        }]
                },
                {
                    menuDisabled: true,
                    sortable: false,
                    xtype: 'actioncolumn',
                    width: 30,
                    items: [{
                            icon: globals.module_url + 'assets/images/database_16x16.gif', // Use a URL in the icon config
                            tooltip: 'Process Data',
                            handler: function(grid, rowIndex, colIndex, Item, e) {
                                e.stopEvent();
                                var rec = dgstore.getAt(rowIndex);
                                url = globals.base_url + 'bpm/admin/get_data/' + globals.idwf + '/' + rec.get('id');
                                Ext.Ajax.request({
                                    url: url,
                                    params: {
                                        id: 1
                                    },
                                    success: function(response) {
                                        var fval = response.responseText;
                                        Ext.create('Ext.window.Window', {
                                            title: 'Query Editor',
                                            height: 650,
                                            width: 500,
                                            layout: 'fit',
                                            items: {// Let's put an empty grid in just to illustrate fit layout
                                                xtype: 'panel',
                                                border: false,
                                                overflowY: 'scroll',
                                                html: '<div id="jsoneditor"></div>' // One header just for show. There's no data,
                                            },
                                            listeners: {
                                                show: function() {
                                                    var options = {
                                                        mode: 'view',
                                                        modes: ['code', 'form', 'text', 'tree', 'view'], // allowed modes
                                                        error: function(err) {
                                                            alert(err.toString());
                                                        }
                                                    };
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
                                                this.destroy();
                                            }
                                        }).show();
                                    }
                                });

                                return false;
                            }
                        }]
                },
                // Ext.create('Ext.grid.RowNumberer'),
                {
                    flex: 1,
                    text: "Date",
                    dataIndex: 'date',
                    sortable: true

                },
                {
                    flex: 2,
                    text: "ID",
                    dataIndex: 'id',
                    sortable: true

                }
                ,
                {
                    flex: 2,
                    text: "User",
                    dataIndex: 'user',
                    sortable: true
                }
                , checkLock,
                {
                    menuDisabled: true,
                    sortable: false,
                    xtype: 'actioncolumn',
                    width: 50,
                    items: [{
                            icon: globals.module_url + 'assets/images/delete.png', // Use a URL in the icon config
                            tooltip: 'Remove case from DB',
                            handler: function(grid, rowIndex, colIndex) {
                                var rec = dgstore.getAt(rowIndex);
                                Ext.Msg.confirm('Confirm', 'Are you sure you want to remove: ' + rec.get('id') + '?', confirm, rec);

                            }
                        }]
                }
            ],
            stripeRows: true,
            ////////////////////////////////////////////////////////////////////////////
            //////////////////////   LISTENERS  ////////////////////////////////////////
            ////////////////////////////////////////////////////////////////////////////
            listeners: {
                /*
                 selectionchange: function( me, selected, eOpts ){
                 load_model(globals.idwf);
                 },*/
                itemclick: gridClick
            },
            ////////////////////////////////////////////////////////////////////////////
            //////////////////////   DOCKERS    ////////////////////////////////////////
            ////////////////////////////////////////////////////////////////////////////
            tbar: [
                {
                    fieldLabel: 'Search',
                    labelWidth: 50,
                    xtype: 'searchfield',
                    store: Ext.data.StoreManager.lookup('caseStore')
                },
                {
                    xtype: 'button',
                    text: '<i class="icon-repeat icon-2x"></i>',
                    handler: function() {
                        mygrid.store.read();
                    }
                }

            ],
            bbar: Ext.create('Ext.PagingToolbar', {
                store: Ext.data.StoreManager.lookup('caseStore'),
                displayInfo: true,
                displayMsg: '{0} - {1} of {2}',
                emptyMsg: "No cases to display",
                renderTo: mygrid

            }),
        });


//otherstore.load();
//dgstore.load();