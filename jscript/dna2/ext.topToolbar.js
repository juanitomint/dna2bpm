function viewClick(){
    c=Ext.getCmp('centerPanel')
    c.header.body.dom.textContent='Views';
    c.removeAll();
    var store = Ext.create('Ext.data.Store', {
        id:'viewStore',
        autoLoad: true,
        fields:['idobj', 'tags', 'title','createdby'],
        proxy: {
            type: 'ajax',
            url: base_url+'jscript/dna2/grid.json',  // url that will load data with respect to start and limit params
            noCache: false,
            params: {
                userId: 1
            },
            reader: {
                type: 'json',
                root: 'rows',
                totalProperty: 'totalCount'
            }
        }
    });
    var sm = Ext.create('Ext.selection.CheckboxModel');
    c.add(Ext.create('Ext.ux.LiveSearchGridPanel',
    {
        title:'pipoka',
        //-----try ad as panel
        id:'centerPanel',
        region:'center',
        title:'Objects',
        layout:'fit',
        //-----try ad as panel
        
        columnLines: true,
        id:'centerGrid',
        store:store,
        indexes:['title','createdby'],
        viewConfig: {
            stripeRows: true
        },
        selModel: sm,    
        columns: [
        {
            text: "ID",
            width:90,
            dataIndex: 'idobj',
            sortable: true
           
        },

        {
            text: "Tags",
            width: 90,
            dataIndex: 'tags',
            sortable: false
        },
        {
            text: "Title",
            flex:1,
            dataIndex: 'title',
            sortable: true
        },

        {
            text: "CreatedBy",
            width: 115, 
            dataIndex: 'createdby',
            sortable: true
        }
            
        ],
        dockedItems: [{
            xtype: 'pagingtoolbar',
            store: store,   // same store GridPanel is using
            dock: 'bottom',
            displayInfo: true
        }]

    })
    );
                    
}
var topToolbar=Ext.create(Ext.Panel, {
    xtype: 'buttongroup',
    title: '',
    region: 'north',
    height: 32, // give north and south regions a height
    bodyStyle: 'padding:10px',
    tbar: [
    {
        text: 'Views',
        iconCls: 'add24',
        handler: viewClick
                                    
    },
    {
        text: 'Forms',
        iconCls: 'add24',
    },
    {
        text: 'Option',
        iconCls: 'add24',
    },
    {
        text: 'Groups',
        iconCls: 'add24'
    },
    {
        text: 'Users',
        iconCls: 'add24',
        menu: [{
            text: 'Paste Menu Item'
        }]
    },
    '-',
    {
        text: 'Applications',
        iconCls: 'add24'
    }
    ]
});