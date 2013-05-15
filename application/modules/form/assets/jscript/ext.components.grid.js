var groupingFeature = Ext.create('Ext.grid.feature.Grouping',{
    groupHeaderTpl: '{name} ({rows.length} Item{[values.rows.length > 1 ? "s" : ""]})'
});
var westCmp=Ext.create('Ext.grid.Panel', {
    id:'componentGrid',
    store: controls,
    width: 600,
    layout:'fit',
    //title: 'Controls',
    features: [groupingFeature],
    //----set drop features
    viewConfig: {
        copy:true,
        plugins: {
            ptype: 'gridviewdragdrop',
            enableDrop: false,
            ddGroup:'frames'     
        }
    },
    
    columns: [{
        text: 'Name',
        flex: 1,
        dataIndex: 'title'
    }]

});
