
var groupingFeature = Ext.create('Ext.grid.feature.Grouping',{
    groupHeaderTpl: '{name} <br/>({rows.length} Item{[values.rows.length > 1 ? "s" : ""]})',
    tdCls:'wrap-text'
});


var dataview=Ext.create('Ext.grid.Panel',
{
    columnLines: false,
    autoScroll: true,
    stripeRows: true,
    id:'typesGrid',
    indexes:['name','desc'],
    cls:'wrap-text',
    //store:dgstore,    
    store: Ext.getStore('typeStore'),
    columns: [
    //Ext.create('Ext.grid.RowNumberer'),
    {
        text: "Desc",
        flex:1,
        tdCls:'wrap-text',
        dataIndex: 'title',
        sortable: true
           
    }
    
    ]
    ,
    features: [groupingFeature],
    viewConfig: {
        copy:true,
        plugins: {
            ptype: 'gridviewdragdrop',
            enableDrop: false,
            ddGroup:'type'
        }
    }
   
});
