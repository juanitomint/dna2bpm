// do the Ext.Ajax.request
function load_props(url){
    var propsGrid = Ext.getCmp('propsGrid');
    if(propsGrid){
        propsGrid.setLoading(true);
        Ext.Ajax.request({
            // the url to the remote source
            url: url,
            // define a handler for request success
            success:
            function(response, options){
                var propsGrid = Ext.getCmp('propsGrid');
                propsGrid.setSource(Ext.JSON.decode(response.responseText));
            },
            // NO errors ! ;)
            failure: function(response,options){
            //TODO handle error
            }
        });
        propsGrid.setLoading(false);
    }
}
//// custom Editors for property grid
/*
Ext.onReady(function(){
    var comboCategory = new Ext.form.ComboBox({
    fieldLabel    : 'Category',
    name        : 'category',
           allowBlank     : false,
           store        : ['Business', 'Personal'],
           typeAhead    : true,
    mode        : 'local',
    triggerAction    : 'all',
    emptyText    :'-- Select category --',
    selectOnFocus    :true
   });

   var active = new Ext.form.Checkbox({
     name        : 'active',
     fieldLabel : 'Active',
     checked    : true,
     inputValue : '1'
   });

   var propsGrid = new Ext.grid.PropertyGrid({
        el:'props-grid',
        nameText: 'Properties Grid',
        width:300,
        autoHeight:true,
        viewConfig : {
            forceFit:true,
            scrollOffset:2 // the grid will never have scrollbars
        },
    customEditors: {
            'Category': new Ext.grid.GridEditor(comboCategory),
        'Active'  : new Ext.grid.GridEditor(active)
        }
    });

    propsGrid.render();

    propsGrid.setSource({
        "(name)": "Properties Grid11",
        "grouping": false,
        "autoFitColumns": true,
        "productionQuality": false,
        "created": new Date(Date.parse('10/15/2006')),
        "tested": false,
        "version": 0.01,
        "borderWidth": 1,
        "Category": 'Personal',
    "Active" : true
    });
});
*/