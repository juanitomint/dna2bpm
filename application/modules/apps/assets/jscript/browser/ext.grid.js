function gridClick (view,record,item,index,e,options ){
    //console.log(view,record,record.internalId,item,index,e,options );
    //console.log(record.internalId);
    var thisid=(record.data.idapp)?record.data.idapp:'';    
    var internalId=record.internalId;   
    propsGrid.idapp=thisid
    var url=globals.module_url+'get_app_properties/'+thisid;
    load_props(url,internalId);
}

var newObject=function(){
    Ext.create('Ext.window.Window', {
        title: "New Object",
        id:'newObjWindow',
        height: 200,
        width: 400,
        layout:'fit',
        items:{
            xtype:'form',
            bodyPadding: 8,
            url: globals.module_url+'forms/'+globals.idapp+'/create',
            items: [{
                xtype: 'textfield',
                name: 'title',
                fieldLabel: 'Title',
                allowBlank: false 
            }
            ,//---4 types
            {
                xtype: 'fieldset',
                flex: 1,
                title: 'Type',
                defaultType: 'radio', // each item will be a radio button
                layout: 'anchor',
                defaults: {
                    anchor: '100%',
                    hideEmptyLabel: false
                }
                ,
                items: [{
                    xtype: 'radiofield',
                    name: 'type',
                    inputValue: 'D',
                    fieldLabel: '',                      
                    boxLabel: 'Object Definition'
                }
                ,{
                    xtype: 'radiofield',
                    name: 'type',
                    inputValue: 'V',
                    fieldLabel: '',                      
                    boxLabel: 'Data View'
                }
                ,{
                    xtype: 'radiofield',
                    name: 'type',
                    inputValue: 'Q',
                    fieldLabel: '',                      
                    boxLabel: 'Search Form'
                }]
            }
            ],
            buttons: [{
                text: 'Submit',
                handler: function() {
                    // The getForm() method returns the Ext.form.Basic instance:
                    var form = this.up('form').getForm();
                    if (form.isValid()) {
                        // Submit the Ajax request and handle the response
                        form.submit({
                            success: function(form, action) {
                                //Ext.Msg.alert('Success', action.result.msg);
                                //---refresh grid view.
                                mygrid.store.read();
                                //---select recently created
                                                    
                                //---close window.
                                Ext.getCmp('newObjWindow').close();
                            },
                            failure: function(form, action) {
                                Ext.Msg.alert('Failed', action.result.msg);
                            }
                        });
                    }
                }
            }]

        }
    }).show();
}



//---4 in place locking editor
var checkLock = Ext.create('Ext.ux.CheckColumn',{
    xtype: 'checkcolumn',
    header: 'Locked',
    dataIndex: 'locked',
    width: 60,
    editor: {
        xtype: 'checkbox',
        cls: 'x-grid-checkheader-editor'
    }
}
);

var checkHidden = Ext.create('Ext.ux.CheckColumn',{
    xtype: 'checkcolumn',
    header: 'Hidden',
    dataIndex: 'hidden',
    width: 60,
    editor: {
        xtype: 'checkbox',
        cls: 'x-grid-checkheader-editor'
    }
}
);

//var sm = Ext.create('Ext.selection.CheckboxModel');
//--uncoment this when check bug it's fixed
//sm={};
//var mygrid=Ext.create('Ext.ux.LiveSearchGridPanel',
var mygrid=Ext.create('Ext.grid.Panel',
{
    columnLines: true,
 
    id:'centerGrid',
    indexes:['title','idapp'],
    store:dgstore,    
    columns: [
    Ext.create('Ext.grid.RowNumberer'),
    {
        text: "IDApp",
        width:90,
        dataIndex: 'idapp',
        sortable: true
           
    },
    {
        text: "Title",
        width:320,
        dataIndex: 'title',
        sortable: true
    }
    ,checkLock
    ,checkHidden       
    ],
    stripeRows       : true,
    viewConfig: {
        //autoScroll:true,
        //        stripeRows: true,
        plugins: {
            ptype: 'gridviewdragdrop'
            ,
            ddGroup:'frames'
        },
        listeners: {
            
            beforedrop: function(node,data,overModel,position,dropFunction,options ){
                //console.log(node,data,overModel,position,dropFunction,options );
                var me=this;
                if(data.copy){
                    //---get the index within the grid
                    var index=node.viewIndex;
                    if (position !== 'before') {
                        index++;
                    } 
                    //---make a copy of the item
                    var itemadd=data.records[0].copy(Ext.id());//---take one item only 
                    this.store.insert(index,itemadd);                
                    //---TODO load pgrid with propper type; if new
                    //return 0;
                    return false;
                } else {
                    return 0;
                }
            },

            drop: function(node, data, dropRec, dropPosition) {
                //console.log(data.records[0].data);
                var dropOn = dropRec ? ' ' + dropPosition + ' ' + dropRec.get('title') : ' on empty view';
                Ext.example.msg("Drag", 'Dropped ' + data.records[0].get('title') +'\n'+ dropOn);
            }
            ,
            itemclick: gridClick
        }
    },
    ////////////////////////////////////////////////////////////////////////////
    //////////////////////   DOCKERS    ////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////
    tbar: [
    {
        xtype: 'button', 
        text: 'Save App',
        icon:globals.base_url+'css/ext_icons/save.gif',
        tooltip:'Save changes',
        handler:function(){
            mygrid.store.sync();
        //mygrid.store.read();
        }
    }
    ,{
        xtype: 'button', 
        text: 'Reload',
        icon:globals.base_url+'css/ext_icons/refresh.gif',
        tooltip:'Reload from db an discard changes',
        handler:function(){    
            mygrid.store.read();
        }
    }
    ,{
        xtype: 'button', 
        text: 'Preview',
        icon:globals.base_url+'css/ext_icons/preview.gif',
        tooltip:'Preview Form',
        handler:function(){
            url=globals.base_url+'dna2/render/go/'+idobj;
            window.open(url);
        }
    }
    ,{
        xtype: 'button', 
        text: 'New Object',
        icon:globals.base_url+'css/ext_icons/new_tab.gif',
        tooltip:'Preview Form',
        handler:newObject
    }
    ]
    

});
dgstore.load();