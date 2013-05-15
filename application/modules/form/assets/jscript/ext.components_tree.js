Ext.define('Controls', {
    extend: 'Ext.data.Model',
    fields: ['name']
});
var controls = Ext.create('Ext.data.Store', {
    storeId: 'controls',
    model: 'Controls',
    sorters: ['name'],
    groupField: 'group',
    data: [
    {
        name: "Text Field",
        qtip : "A Text Field where u can write",
        group:'Common'
    }
    ,{
        name: "Text Area",
        qtip : "A text area",
        group:'Common'
    }
    ,{
        name: "Combo Box",
        qtip : "A combo box",
        group:'Options'
    }
    ,{
        name: "Option List",
        qtip : "A list of options (only one can be selected)",
        group:'Options'
    }
    ]
});


var treestore = Ext.create('Ext.data.TreeStore', {
    root: {
        expanded: true,
        text:"",
        user:"",
        status:"",
        children: [
        {
            text : "Forms",
            cls : "folder",
            allowDrag: false,
                                
            children : [{
                leaf: true,
                text : "Form Panel",
                qtip : "A panel containing form elements",
                config : {
                    xtype : "form",
                    title : "Form"
                },
                cls : "file"
            },{
                leaf: true,
                text : "Field Set",
                qtip : "A Fieldset, containing other form elements",
                config : {
                    xtype : "fieldset",
                    title : "Legend",
                    autoHeight : true
                },
                cls : "file"
            },{
                leaf: true,
                text : "Combo Box",
                qtip : "A combo box",
                config : {
                    xtype : "combo",
                    fieldLabel : "Text",
                    name : "combovalue",
                    hiddenName : "combovalue"
                },
                cls : "file"
            },{
                leaf: true,
                text : "Text Field",
                qtip : "A Text Field",
                idobj:'163',
                config : {
                    xtype : "textfield",
                    fieldLabel : "Text",
                    name : "textvalue"
                },
                cls : "file"
            },{
                leaf: true,
                text : "Text Area",
                qtip : "A Text Area",
                idobj:'TA',
                config : {
                    idobj:'TA',
                    xtype : "textarea",
                    fieldLabel : "Text",
                    name : "textarea"
                },
                cls : "file"
            },{
                leaf: true,
                text : "Number Field",
                qtip : "A Text Field where you can only enter numbers",
                config : {
                    xtype : "numberfield",
                    fieldLabel : "Number",
                    name : "numbervalue"
                },
                cls : "file"
            },{
                leaf: true,
                text : "Time Field",
                qtip : "A Text Field where you can only enter a time",
                config : {
                    xtype : "timefield",
                    fieldLabel : "Time",
                    name : "timevalue"
                },
                cls : "file"
            },{
                leaf: true,
                text : "Date Field",
                qtip : "A Text Field where you can only enter a date",
                config : {
                    xtype : "datefield",
                    fieldLabel : "Date",
                    name : "datevalue"
                },
                cls : "file"
            },{
                leaf: true,
                text : "Check Box",
                qtip : "A checkbox",
                config : {
                    xtype : "checkbox",
                    fieldLabel : "Label",
                    boxLabel : "Box label",
                    name : "checkbox",
                    inputValue : "cbvalue"
                },
                cls : "file"
            },{
                leaf: true,
                text : "Radio Box",
                qtip : "A radio form element",
                config : {
                    xtype : "radio",
                    fieldLabel : "Label",
                    boxLabel : "Box label",
                    name : "radio",
                    inputValue : "radiovalue"
                },
                cls : "file"
            },{
                leaf: true,
                text : "Text Label",
                qtip : "A textlabel",
                config : {
                    xtype : "label",
                    text : "Label"
                },
                cls : "file"
            },{
                leaf: true,
                text : "Button",
                qtip : "A button",
                config : {
                    xtype : "button",
                    text : "Ok"
                },
                cls : "file"
            },{
                leaf: true,
                text : "Date Picker",
                gtip : "A date picker",
                config : {
                    xtype : "datepicker"
                }
            },{
                leaf: true,
                text : "Color Pallet",
                gtip : "A color Pallet",
                config : {
                    xtype : "colorpalette"
                }
            }]
        },{
            text : "Panels",
            cls : "folder",
            children : [{
                leaf: true,
                text : "Panel",
                qtip : "A simple panel with default layout",
                config : {
                    xtype : "panel",
                    title : "Panel"
                },
                cls : "file"
            },{
                leaf: true,
                text : "Tab Panel",
                qtip : "A panel with many tabs",
                wizard : 'wizard/tabpanel-wiz.json',
                cls : "file"
            }]
        },{
            text : "Layouts",
            cls : "folder",
            children : [{
                leaf: true,
                text : "Fit Layout",
                qtip : "Layout containing only one element, fitted to container",
                config : {
                    layout : "fit",
                    title : "FitLayout Container"
                },
                cls : "file"
            },{
                leaf: true,
                text : "Card Layout",
                qtip : "Layout containing many elements, only one can be displayed at a time",
                config : {
                    layout : "card",
                    title : "CardLayout Container",
                    activeItem : 0
                },
                cls : "file"
            },{
                leaf: true,
                text : "Anchor Layout",
                qtip : "Layout containing many elements, sized with \"anchor\" percentage values",
                config : {
                    layout : "anchor",
                    title : "AnchorLayout Container"
                },
                cls : "file"
            },{
                leaf: true,
                text : "Absolute Layout",
                qtip : "Layout containing many elements, absolutely positionned with x/y values",
                config : {
                    layout : "absolute",
                    title : "AbsoluteLayout Container"
                },
                cls : "file"
            },{
                leaf: true,
                text : "Accordion Layout",
                qtip : "Layout as accordion",
                wizard: "wizard/accordion-wiz.json",
                cls : "file"
            },{
                leaf: true,
                text : "Column Layout",
                qtip : "Layout of columns",
                wizard : "wizard/column-wiz.json",
                cls : "file"
            },{
                leaf: true,
                text : "Border Layout",
                qtip : "Layout with regions",
                wizard : "wizard/border-wiz.json",
                cls : "file"
            }]
        },{
            text:'Presets',
            cls:'folder'
        }
                            
        ]
    }
});
//var tree= Ext.create('Ext.tree.Panel', {
//    title: 'Objects',
//    ddGroup:'grid2tree',
//    viewConfig: {
//        plugins: {
//            ptype: 'treeviewdragdrop',
//            dragGroup:'frames',
//            appendOnly: true
//
//        }
//    },
//    tools:[{
//        type:'refresh',
//        qtip: 'Refresh form Data',
//        // hidden:true,
//        handler: function(event, toolEl, panel){
//        // refresh logic
//        }
//    },
//    {
//        type:'help',
//        qtip: 'Get Help',
//        handler: function(event, toolEl, panel){
//        // show help here
//        }
//    }],
//    listeners: {
//        beforenodedrop: function(e){
//            console.log('beforenodedrop',e);
//            return false;
//        },
//        drop: function(node, data, dropRec, dropPosition) {
//            console.log('drop',node);
//            var dropOn = dropRec ? ' ' + dropPosition + ' ' + dropRec.get('name') : ' on empty view';
//            Ext.example.msg("Drag from right to left", 'Dropped ' + data.records[0].get('name') + dropOn);
//        }
//    },
//    draggable:true,
//    store: treestore,
//    rootVisible: false
//});

var groupingFeature = Ext.create('Ext.grid.feature.Grouping',{
    groupHeaderTpl: '{name} ({rows.length} Item{[values.rows.length > 1 ? "s" : ""]})'
});
var tree=Ext.create('Ext.grid.Panel', {
    store: controls,
    width: 600,
    height: 400,
    title: 'Controls',
    features: [groupingFeature],
    columns: [{
        text: 'Name',
        flex: 1,
        dataIndex: 'name'
    }]

});
