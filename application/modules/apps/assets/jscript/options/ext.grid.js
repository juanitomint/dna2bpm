function gridClick (view,record,item,index,e,options ){
    //console.log(view,record,record.internalId,item,index,e,options );
    //console.log(record.internalId);
    var thisid=(record.data.idop)?record.data.idop:'';
    var internalId=record.internalId;
    propsGrid.idop=thisid
    var url=globals.module_url+'options/get_options_properties/'+thisid;
    load_props(url,internalId);
}

var newObject = function() {
    var internalId=null;
    var url=globals.module_url+'options/get_options_properties/';
    load_props(url,internalId);
}




//var sm = Ext.create('Ext.selection.CheckboxModel');
//--uncoment this when check bug it's fixed
//sm={};
// var mygrid=Ext.create('Ext.ux.LiveSearchGridPanel',
// // var mygrid=Ext.create('Ext.grid.Panel',
// {
//     columnLines: true,

//     id:'centerGrid',
//     // indexes:['value','text'],
//     store:optionsDefault,
//     columns: [
//     Ext.create('Ext.grid.RowNumberer'),
//     {
//         text: "idop",
//         width:90,
//         dataIndex: 'idop',
//         sortable: true

//     },
//     {
//         text: "Title",
//         width:320,
//         dataIndex: 'title',
//         sortable: true
//     }
//     ],
//     stripeRows       : true,
//     viewConfig: {
//         //autoScroll:true,
//         //        stripeRows: true,
//         plugins: {
//             // ptype: 'gridviewdragdrop'
//             // ,
//             // ddGroup:'frames'
//         },
//         listeners: {
//             itemclick: gridClick
//         }
//     },
//     ////////////////////////////////////////////////////////////////////////////
//     //////////////////////   DOCKERS    ////////////////////////////////////////
//     ////////////////////////////////////////////////////////////////////////////



// });
// optionsStore.load();


var mygrid=Ext.create('Ext.grid.Panel', {
//  var mygrid=Ext.create('Ext.ux.LiveSearchGridPanel',{
    title: 'Options',
    store: Ext.data.StoreManager.lookup('optionsDefault'),
    columns: [
        { text: 'Value',  dataIndex: 'value' },
        { text: 'Text', dataIndex: 'text', flex: 1 },
        { text: 'idrel', dataIndex: 'idrel' }
    ],
     viewConfig: {
        //autoScroll:true,
        stripeRows: true,
        listeners: {
            itemclick: gridClick
        }
    },
});