var remove_loaders = function() {
    Ext.get('loading').remove();
    Ext.fly('loading-mask').remove();
}

var comboOptions = new Ext.form.ComboBox({
    name: 'idop',
    allowBlank: false,
    store: Ext.getStore('optionsStore'),
    displayField: 'title',
    valueField: 'idop',
    queryMode: 'local'
});

Ext.application({
    name: 'Options Editor',
    newObject: newObject,
    init: function() {

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
                items: [
                    comboOptions, {
                        xtype: 'panel',
                        title: 'Inner Panel One',
                        // width: 550,
                        items: [mygrid],
                    }
                ]
            }]
        });

        var right = Ext.create('Ext.Panel', {
            id: 'rightPanel',
            region: 'east',
            animCollapse: true,
            collapsible: true,
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
                    title: 'option properties',
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
