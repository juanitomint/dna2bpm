var onSelectionChange=function(item){
    mygrid.store.load({
        params:{
            idgroup:item.getLastSelected().data.idgroup
        }
    });
    if(tree){
        tree.setLoading('wait...');
        tree.uncheck_all()
        tree.load_checked(dataview.selModel.getLastSelected().data.idgroup);
    }
};
var dataview=Ext.create('Ext.grid.Panel',
{
    columnLines: false,
    autoScroll: true,
    stripeRows: true,
    id:'groupGrid',
    indexes:['idgroup','name'],
    //store:dgstore,    
    store: Ext.data.StoreManager.lookup('GroupStore'),
    selModel: {
        mode: 'SINGLE',
        listeners: {
            scope: this,
            selectionchange: onSelectionChange
        }
    },
    columns: [
    //Ext.create('Ext.grid.RowNumberer'),
    
    {
        text: "IDGroup",
        width:90,
        dataIndex: 'idu',
        hidden:true,
        sortable: true
           
    },
    {
        text: "Name",
        xtype: 'templatecolumn',
        tpl:'{name} ({idgroup})',
        flex: true,
        dataIndex: 'name',
        sortable: true
    }]
    ,
    viewConfig: {
        copy:true,
        plugins: {
            ptype: 'gridviewdragdrop',
            enableDrop: false,
            ddGroup:'user'
        }
    }
});