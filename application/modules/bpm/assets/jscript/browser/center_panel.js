/*
 * Center panel
 * 
 */

var ModelEdit = Ext.create('Ext.Action', {
    text:'Edit',
    iconCls:'fa fa-edit',
    handler:function (){
        TreeDblClick(tree,null);
    }
});

var ModelImport= Ext.create('Ext.Action', {
    text:'Import',
    iconCls:'fa fa-download',
    handler:function (){
        Ext.create('Ext.window.Window', {
            title: '<h5><i class="fa fa-try"></i> Import Model</h5>',
            id:Ext.id(),
            height: 200,
            width: 500,
            layout: 'fit',
            items:[
            Ext.create('Ext.form.Panel', {
                width: 400,
                bodyPadding: 10,
                
                items: [{
                    xtype: 'fileuploadfield',
                    name: 'file',
                    fieldLabel: 'file',
                    labelWidth: 50,
                    msgTarget: 'side',
                    allowBlank: false,
                    anchor: '100%'
                }],

                buttons: [{
                    text: 'Upload',
                    handler: function() {
                        var form = this.up('form').getForm();
                        if(form.isValid()){
                            form.submit({
                                url: globals.module_url+'repository/import/model',
                                waitMsg: 'Uploading your file...',
                                success: function(fp, o) {
                                    Ext.Msg.alert('Status',o.result.msg);
                                    tree.store.reload();
                                }
                                ,
                                failure: function(form, action) {
                                    switch (action.failureType) {
                                        case Ext.form.action.Action.CLIENT_INVALID:
                                            Ext.Msg.alert('Failure', 'Form fields may not be submitted with invalid values');
                                            break;
                                        case Ext.form.action.Action.CONNECT_FAILURE:
                                            Ext.Msg.alert('Failure', 'Ajax communication failed');
                                            break;
                                        case Ext.form.action.Action.SERVER_INVALID:
                                            Ext.Msg.alert('Failure', action.result.msg);
                                    }
                                }
                            });//----end form submit
                        }
                    }
                }]
            })//----formpanel
            ]//end items
        }).show();//--end create
    }//---end handler
});

var ModelExport= Ext.create('Ext.Action', {
    text:'Export',
    iconCls:'fa fa-upload',
    handler:function (){
        var body = Ext.getBody(),
        n=tree.getSelectionModel().getSelection()[0];
        //---only do something if its leaf=model
        if(n && n.isLeaf()){
            frame = body.createChild({
                src:globals.base_url+'bpm/assets/files/images/zip/'+n.data.id+'.zip',
                tag:'iframe',
                cls:'x-hidden',
                id:'hiddenform-iframe',
                name:'iframe'
            });
        } else {
            //---show message
            Ext.MessageBox.alert('Error!', "Select a model to export");
        }
        
    }
});

var ModelCloudExport= Ext.create('Ext.Action', {
    text:'Export',
    iconCls:'fa fa-cloud-upload',
    handler:function (){
        
    }
});

var ModelCloudImport= Ext.create('Ext.Action', {
    text:'Import',
    iconCls:'fa fa-cloud-download',
    handler:function (){}
});

var ModelStatus= Ext.create('Ext.Action', {
    text:'Status',
    iconCls:'fa fa-heart',
    handler:function (){
          n=tree.getSelectionModel().getSelection()[0];
        //---only do something if its leaf=model
        if(n && n.isLeaf()){
            url= globals.module_url+'tokens/status/'+n.data.id;
            window.open(url);
        }
    }
});

var ModelView = Ext.create('Ext.Action', {
    text:'View',
    iconCls:'fa fa-file',
    handler:function(){
        n=tree.getSelectionModel().getSelection()[0];
        //---only do something if its leaf=model
        
        if(n && n.isLeaf()){
            url= globals.module_url+'repository/view/model/'+n.data.id;
            url= globals.base_url+'bpm/assets/files/images/svg/'+n.data.id+'.svg';
            window.open(url);
        }
    }
});
var ModelDump = Ext.create('Ext.Action', {
    text:'Dump',
    iconCls:'fa fa-arrow-circle-down',
    handler:function(){
        n=tree.getSelectionModel().getSelection()[0];
        //---only do something if its leaf=model
        if(n && n.isLeaf()){
            url= globals.module_url+'repository/json_view/model/'+n.data.id;
            window.open(url);
        }
    }
});
var ModelRun = Ext.create('Ext.Action', {
    text:'Run',
    iconCls:'fa fa-play',
    handler:function(){
        n=tree.getSelectionModel().getSelection()[0];
        //---only do something if its leaf=model
        if(n && n.isLeaf()){
            url=globals.module_url+'engine/newcase/model/'+n.data.id;
            window.open(url);
        }
    }
});

var ModelRunManual = Ext.create('Ext.Action', {
    text:'Run Manual',
    iconCls:'fa fa-hand-o-right',
    handler:function(){
        n=tree.getSelectionModel().getSelection()[0];
        //---only do something if its leaf=model
        if(n && n.isLeaf()){
            url= globals.module_url+'engine/newcase/model/'+n.data.id+'/manual';
            window.open(url);
        }
    }
});
var ModelManager = Ext.create('Ext.Action', {
    text:'Manager',
    iconCls:'fa fa-bar-chart-o',
    handler:function(){
        n=tree.getSelectionModel().getSelection()[0];
        //---only do something if its leaf=model
        if(n && n.isLeaf()){
            url= globals.module_url+'case_manager/browse/model/'+n.data.id;
            window.open(url);
        }
    }
});

var ModelKPI = Ext.create('Ext.Action', {
    text:'KPI',
    iconCls:'fa fa-dashboard',
    handler:function(){
        n=tree.getSelectionModel().getSelection()[0];
        //---only do something if its leaf=model
        if(n && n.isLeaf()){
            url= globals.module_url+'kpi/editor/model/'+n.data.id;
            window.open(url);
        }
    }
});


var toolBar=Ext.create('Ext.toolbar.Toolbar', {
    disabled: false,
    items: [
    ModelManager,
    ModelEdit,
    ModelRun,
    ModelKPI,
    ModelView,
    ModelDump,
    ModelRunManual,
    ModelImport,
    ModelExport,
    ModelStatus
    /*    ModelCloudImport,
    ModelCloudExport
         */
    ]
});

var modelPanel= Ext.create('Ext.Panel', {
    id:'modelPanel',
    autoScroll:true,
    listeners:{
//  render: load_model
}
});
        
var center_panel=Ext.create('Ext.panel.Panel', {
    title: '<i class="fa fa-bpm"></i> Model Explorer',    
    id:"center_panel",
    tbar:toolBar,
    border:2,
    layout: "fit",
    autoScroll:true,
    items: [modelPanel]
    
    
});