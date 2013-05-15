//----define cache object
pgridCache={};
//////////////////////////////////////////////////////////////////////////////
//////////////////////   MODEL    ////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////
Ext.define('FramesModel', {
    extend: 'Ext.data.Model',
    fields:['idframe', 'title','type','group','locked','hidden','required'],
    proxy: {
        type: 'ajax',
        api: {
            create  : globals.module_url+'frames/create/'+globals.idobj,
            read    : globals.module_url+'frames/read/'+globals.idobj,
            update  : globals.module_url+'frames/update/'+globals.idobj,
            destroy : globals.module_url+'frames/destroy/'+globals.idobj
        },
        noCache: false,
        writer:{
            type: 'json',
            allowSingle:false
        },
        reader: {
            type: 'json',
            root: 'rows',
            totalProperty: 'totalCount'
        }
        
    }
});

////////////////////////////////////////////////////////////////////////////
//////////////////////   4 DATA GRID    ////////////////////////////////////
////////////////////////////////////////////////////////////////////////////
var controls = Ext.create('Ext.data.Store', {
    storeId: 'controls',
    model: 'FramesModel',
    //sorters: 'name',
    groupField: 'group',
    data: [
    {
        name: "Text Field",
        qtip : "A Text Field where u can write",
        type:'text',
        group:'Common',
        idobj:'???',
        title:'Text Field'
        
    }
    ,{
        name: "Text Area",
        qtip : "A text area",
        type:'textarea',
        group:'Common',
        idobj:'???',
        title:'Text Area'
    }
    ,{
        name: "Date",
        qtip : "date field w/pick-up icon",
        type:'date',
        group:'Common',
        idobj:'???',
        title:'Date Field'
    }
    ,{
        name: "Date Time",
        qtip : "date Time field w/pick-up icon",
        type:'datetime',
        group:'Common',
        idobj:'???',
        title:'Date Time Field'
    }
    ,{
        name: "Time",
        qtip : "Time field",
        type:'time',
        group:'Common',
        idobj:'???',
        title:' New Time Field'
    }
    ,{
        name: "Money",
        qtip : "Money formated field",
        type:'money',
        group:'Common',
        idobj:'???',
        title:'Money Field'
    }
    ,{
        name: "Numeric",
        qtip : "Only Numbers",
        type:'numeric',
        group:'Common',
        idobj:'???',
        title:'Numeric Field'
    }
    ,{
        name: "Label",
        qtip : "Simple Text",
        type:'label',
        group:'Common',
        idobj:'???',
        title:'Label'
    }
    //------options
    ,{
        name: "Combo Box",
        qtip : "A combo box",
        type:'combo',
        group:'Options',
        idobj:'???',
        title:'Combo'
    }
    ,{
        name: "DB Combo Box",
        qtip : "A combo box populated from db-query",
        type:'combodb',
        group:'Options',
        idobj:'???',
        title:'DB combo'
    }
    ,{
        name: "Option List",
        qtip : "A list of options (only one can be selected)",
        type:'radio',
        group:'Options',
        idobj:'???',
        title:' Options'
    }
    ,{
        name: "Check Box",
        qtip : "Multiple select Check Box set from saved options",
        type:'checklist',
        group:'Options',
        idobj:'???',
        title:' Check Box'
    }
    //-------Relations
    ,{
        name: "Sub-Form",
        qtip : "a Sub form widget with Create,Update and Unlink control",
        type:'subform',
        group:'Relations',
        idobj:'???',
        title:' SubForm'
    }
    ,{
        name: "Sub-Form Parent",
        qtip : "a Table wich show all parent where this form is referenced",
        type:'subformparent',
        group:'Relations',
        idobj:'???',
        title:' SubFrom Parent'
    }
    //-----Special Widgets
    ,{
        name: "Time Stamp",
        qtip : "a time stamp mark",
        type:'timestamp',
        group:'Special',
        idobj:'???',
        title:' Time Stamp'
    }
    ,{
        name: "Signature",
        qtip : "a user Signature",
        type:'signature',
        group:'Special',
        idobj:'???',
        title:' Signature'
    }
    ,{
        name: "Fixed Table",
        qtip : "a Table with fixed rows and col",
        type:'table',
        group:'Special',
        idobj:'???',
        title:' Fixed Table'
    }
    ,{
        name: "HTML Editor",
        qtip : "a text Area with HTML capabilities",
        type:'html',
        group:'Special',
        idobj:'???',
        title:' HTML'
    }
    ]
});

var dgstore = Ext.create('Ext.data.Store', {
    id:'viewStore',
    autoLoad: false,
    model: 'FramesModel'
  
});
var otherstore= Ext.create('Ext.data.Store', {
    id:'viewStoreAll',
    autoLoad: true,
    model: 'FramesModel',
    groupField: 'group',
    proxy: {
        type: 'ajax',
        //url: base_url+'jscript/form/grid.json',  // url that will load data with respect to start and limit params
        url: globals.module_url+'get_all_frames/'+globals.idobj,  // url that will load data with respect to start and limit params
        noCache: false,
        params: {
        //userId: 1
        },
        reader: {
            type: 'json',
            root: 'rows',
            totalProperty: 'totalCount'
        },
        writer: {
            allowSingle : false
        }
    }
});