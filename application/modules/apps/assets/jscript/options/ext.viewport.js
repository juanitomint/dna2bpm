var remove_loaders = function() {
    Ext.get('loading').remove();
    Ext.fly('loading-mask').remove();
}

var newOption = function() {
    var url = globals.module_url + 'options/get_options_properties/';
    load_props(url);
    optionsDefault.removeAll();
    optionsDefault.insert(0, {});
}
var NewOption = Ext.create('Ext.Action', {
    text: 'New',
    iconCls: 'fa fa-plus-square',
    handler: newOption
});

Ext.application({
    name: 'Options Editor',
    newObject: newObject,
    init: function() {

        newOption();
    },
    launch: function() {
        var center = Ext.create('Ext.Panel', {
            region: 'center',
            margins: '0 0 0 0',
            layout: 'border',
            //title: "<img align='top' src='"+globals.base_url+"css/ext_icons/details.gif'/> Form Frames",
            title: "Options",
            items: [{
                region: 'center',
                // layout:'vbox',
                items: [{
                    xtype: 'panel',
                    layout: 'fit',
                    items: [
                        comboOptions,{}
                    ],
                    tbar: {
                        items: [
                            NewOption
                        ]
                    }
                }, {
                    xtype: 'panel',
                    // width: 550,
                    items: [mygrid],
                }]
            }]
        });

        var right = Ext.create('Ext.Panel', {
            id: 'rightPanel',
            region: 'east',
            collapsible: false,
            animCollapse: false,
            split: true,
            width: 400, // give east and west regions a width
            minWidth: 300,
            maxWidth: 400,
            margins: '0 0 0 0',
            align: 'stretch',
            pack: 'start',
            items: [

                ///-------Pgrid
                {
                    xtype: 'panel',
                    title: 'Option properties',
                    flex: 1,
                    items: [

                        propsGrid
                    ]
                }
            ]

        });

        //---CREATE VIEWPORT
        Ext.create('Ext.Viewport', {
            layout: 'border',
            items: [
                center,
                right
            ],
            listeners: {
                render: remove_loaders
            }
        });
    }

});
