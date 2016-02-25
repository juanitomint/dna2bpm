var modelPanel = Ext.create('Ext.panel.Panel', {
    id: 'modelPanel',
    autoScroll: true,
    listeners: {
        //  render: load_model
    }
});
var caseFreeze = Ext.create('Ext.Action', {
    text: 'Freeze',
    iconCls: 'fa fa-upload',
    handler: function() {
        Ext.Ajax.request({
            // the url to the remote source\/test-call-activity
            url: globals.module_url + 'case_manager/freeze',
            method: 'POST',
            params: {

                'idwf': globals.idwf,
                'idcase': globals.idcase
            },
            // define a handler for request success
            success: function(response, options) {

                var o = JSON.parse(response.responseText);
                Ext.Msg.alert('Status', o.msg);
            },
            // NO errors ! ;)
            failure: function(response, options) {
                alert('Error Loading:' + response.err);
                tree.setLoading(false);

            }
        });
    }
});
var caseUnFreeze = Ext.create('Ext.Action', {
    text: 'UnFreeze',
    iconCls: 'fa fa-download',
    handler: function() {
        Ext.Ajax.request({
            // the url to the remote source\/test-call-activity
            url: globals.module_url + 'case_manager/unfreeze',
            method: 'POST',
            params: {

                'idwf': globals.idwf,
                'idcase': globals.idcase
            },
            // define a handler for request success
            success: function(response, options) {

                var o = JSON.parse(response.responseText);
                if (o.ok) {
                    load_data_callback = function() {
                        tokenStore.load({
                            scope: this,
                            callback: tokens_paint_all
                        });
                    }
                    load_model(globals.idwf);
                    Ext.Msg.alert('Status', o.msg);
                } else {
                    Ext.Msg.alert('Status', o.msg);
                }
            },
            // NO errors ! ;)
            failure: function(response, options) {
                alert('Error Loading:' + response.err);
                tree.setLoading(false);

            }
        });
    }
});
Ext.application({
    name: 'AppEditor',
    init: function() {

    },
    launch: function() {
        var remove_loaders = function() {

            Ext.get('loading').remove();
            Ext.fly('loading-mask').remove();
            dgstore.load();

        }

        //----collapse cases panel
        if (globals.idcase) {
            cases_collapsed = true;
        }
        else {
            cases_collapsed = false;
        }
        var center = Ext.create('Ext.Panel', {
            region: 'center',
            margins: '0 0 0 0',
            layout: 'border',
            items: [{
                region: 'south',
                layout: 'fit',
                title: '<i class="icon icon-time" ></i> Token History',
                collapsible: true,
                collapsed: true,
                resizable: true,
                animCollapse: false,
                height: 300,
                items: [tokenGrid]
            }, {
                //title: '<i class="icon icon-bpm"></i> Model Panel / Picker',
                title: '<i class="icon icon-dashboard"></i> Case Manager',
                id: 'ModelPanel',
                region: 'center',
                layout: 'fit',
                collapsible: true,
                collapsed: false,
                animCollapse: false,
                resizable: true,
                split: true,
                items: [modelPanel],
                tbar: {
                    id: 'ModelPanelTbar',
                    disabled: true,
                    items: [
                        TokensPlay,
                        TokensStop,
                        TokensStepBackward,
                        TokensStepForward,
                        TokensTimeSlider,
                        TokensFolow,
                        TokensShowExtras,
                        TokensReload,
                        TokensPaintAll,
                        caseFreeze,
                        caseUnFreeze
                    ]
                }
            }]
        });

        var right = Ext.create('Ext.Panel', {
            id: 'rightPanel',
            region: 'east',
            title: 'Cases',
            animCollapse: true,
            collapsible: true,
            collapsed: cases_collapsed,
            animCollapse: false,
            split: true,
            width: 400, // give east and west regions a width
            minWidth: 300,
            maxWidth: 700,
            margins: '0 0 0 0',
            layout: 'fit',
            items: [mygrid]

        });

        //---CREATE VIEWPORT

        Ext.create('Ext.Viewport', {
            layout: 'border',
            items: [
                center,
                right
            ],
            listeners: {
                render: function() {},
                afterRender: function() {
                    remove_loaders();
                    load_model(globals.idwf);

                }

            }
        });
    }

});
