Array.prototype.remove= function(){
    var what, a= arguments, L= a.length, ax;
    while(L && this.length){
        what= a[--L];
        while((ax= this.indexOf(what))!= -1){
            this.splice(ax, 1);
        }
    }
    return this;
}

function confirm(result){
    if(result=='yes'){
        store=Ext.data.StoreManager.lookup('UserGroupStore');
        groupField=Ext.getCmp('groupField');
        groups=groupField.value.split(',');
        groups.remove(toString(this.data.idgroup));
        groupField.setValue(groups.join(','));
        store.remove(this);
    
    }
}
function renderGroups(groupField){
    store=Ext.data.StoreManager.lookup('GroupStore');
    grid=Ext.getCmp('UserGroupGrid');
    grid.store.removeAll();
    if(groupField.value){
        groups=groupField.getValue().split(',');  
        for(i in groups){
            value=groups[i];
            record=store.getAt(store.find('idgroup',value));
            if(record){
                grid.store.add(record);
            }
        //html+=record.data.name+'<br/>';
        }
    }
    
}
var required = '<span style="color:red;font-weight:bold" data-qtip="Required">*</span>';
var businessform = Ext.create('Ext.form.Panel',{
    id: 'userForm',
    frame: true,
    bodyPadding: '5 5 0',
    fieldDefaults: {
        msgTarget: 'side'
        ,
        labelWidth: 80
    },
    defaults: {
        anchor: '100%'
    },
    actioncomplete:    function(){
        renderGroups(Ext.getCmp('groupsContainer'));
    }
    ,
    items: [
    {
        xtype:'fieldset',
        title: 'User Information',
        defaultType: 'textfield',
        collapsible: true,
        layout: 'anchor',
        defaults: {
            anchor: '100%'
        },
        items :[
        {
            fieldLabel: 'IDU',
            name: 'idu',
            readOnly:true
            
        },
        {
            fieldLabel: 'First Name',
            afterLabelTextTpl: required,
            name: 'name',
            allowBlank:false
        },
        {
            fieldLabel: 'Last Name',
            afterLabelTextTpl: required,
            name: 'lastname',
            allowBlank:false
        },
        {
            fieldLabel: 'Company',
            name: 'company'
        },
        {
            fieldLabel: 'Email',
            afterLabelTextTpl: required,
            name: 'email',
            vtype:'email'
        },
        {
            fieldLabel: 'ID Number',
            afterLabelTextTpl: required,
            name: 'idnumber'
        },
        {
            fieldLabel: 'Phone Number',
            name: 'phone'
        },
        {
            xtype: 'textfield',
            name: 'password',
            inputType: 'password',
            fieldLabel: 'Set Password'
        },
        {
            xtype: 'textfield',
            name: 'password2',
            fieldLabel: 'Repeat Password',
            inputType: 'password',
            validator: function(value) {
                var password = this.previousSibling('[name=password]');
                return (value === password.getValue()) ? true : 'Passwords do not match.'
            }
        }
        ]
    },
    /*{
        xtype:'fieldset',
        title: 'Phone Number',
        collapsible: true,
        defaultType: 'textfield',
        layout: 'anchor',
        defaults: {
            anchor: '100%'
        },
        items :[{
            fieldLabel: 'Home',
            name: 'home'
        },{
            fieldLabel: 'Business',
            name: 'business'
        },{
            fieldLabel: 'Mobile',
            name: 'mobile'
        },{
            fieldLabel: 'Fax',
            name: 'fax'
        }]
    }.*/
    {
        xtype:'fieldset',
        title: 'Groups / Roles',
        collapsible: true,
        defaultType: 'textfield',
        layout: 'anchor',
        defaults: {
            anchor: '100%'
        },
        items :[{
            xtype: 'toolbar',
            items: [
            //---autocomplete from groups
            
            {
                xtype: 'combo',
                id:'groupCombo',
                store: Ext.data.StoreManager.lookup('GroupStore'),
                displayField: 'name',
                typeAhead: false,
                hideLabel: true,
                valueField: 'idgroup',
                //hideTrigger:true,
                anchor: '100%',
                listConfig: {
                    loadingText: 'Searching...',
                    emptyText: 'No matching Groups found.'
                }
            },
            
            {
                xtype: 'button', 
                text: 'Add',
                icon:globals.module_url+'assets/images/add.png',
                handler:function(){
                    combo=Ext.getCmp('groupCombo');
                    if(combo.value){
                        groupField=Ext.getCmp('groupField');  
                        if(groupField.value){
                            groups=groupField.getValue().split(',');  
                                        
                        } else {
                            groups=new Array();
                        }
                        if(groups.indexOf(combo.value)==-1){
                            groups.push(combo.value);
                            groupField.setValue(groups.join(','));
                        }
                    }
                }
            }
                
            ]
        },
        {
            id:'groupField',
            labelAlign: 'top',
            fieldLabel: 'Groups',
            hidden:true,
            name: 'group',
            listeners: {
                change: renderGroups
            }
            
        }
        ,
        //----container 4 grid
        {
            xtype:'container',
            items:[
            Ext.create('Ext.grid.Panel', {
                id:'UserGroupGrid',
                store: Ext.data.StoreManager.lookup('UserGroupStore'),
                title:'Groups / Roles',
                stateful: true,
                collapsible: true,
                multiSelect: true,
                draggable:false,
                stateId: 'stateGrid',
                columns: [
                {
                    menuDisabled: true,
                    sortable: false,
                    xtype: 'actioncolumn',
                    width: 50,
                    items: [{
                        icon   : globals.module_url+'assets/images/delete.png',  // Use a URL in the icon config
                        tooltip: 'Remove user from group',
                        handler: function(grid, rowIndex, colIndex) {
                            store=Ext.data.StoreManager.lookup('UserGroupStore');
                            var rec = store.getAt(rowIndex);
                            Ext.Msg.confirm('Confirm', 'Are you sure you want to remove: '+rec.get('name')+'?',confirm,rec);
                            
                        }
                    }]
                }
                ,
                {
                    text     : 'id',
                    dataIndex: 'idgroup'
                },
                {
                    text     : 'name',
                    flex     : 1,
                    dataIndex: 'name'
                }
                ]//---end columns
                ,    
                viewConfig: {
                    draggable:false,
                    plugins: {
                        ptype: 'gridviewdragdrop'
                        ,
                        ddGroup:'user',
                        copy:true
                    },
                    listeners: {
            
                        beforedrop: function(node,data,overModel,position,dropFunction,options ){
                            //console.log('user Perm',node,data,overModel,position,dropFunction,options );
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
                                return false;
                            } else {
                                return 0;
                            }
                        }
                        ,
                        drop: function(node, data, dropRec, dropPosition) {
                            //console.log(data.records[0].data);
                            var dropOn = dropRec ? ' ' + dropPosition + ' ' + dropRec.get('title') : ' on empty view';
                            console.log("Drag", 'Dropped ' + data.records[0].get('title') +'\n'+ dropOn);
                        }
                    }//---listeners
                }
            })
            ]
        }
        //----container 4 grid
        
       
        ]
    } //---end fieldset    
    ],//----end form items

    buttons: [{
        text: 'Reset',
        handler: function() {
            this.up('form').getForm().reset();
        }
    }, {
        text: 'save',
        formBind: true, //only enabled once the form is valid
        disabled: true,
        handler: function() {
            var form = this.up('form').getForm();
            if (form.isValid()) {
                form.submit({
                    success: function(form, action) {
                        Ext.Msg.alert('Success', action.result.msg);
                    },
                    failure: function(form, action) {
                        Ext.Msg.alert('Failed', action.result.msg);
                    }
                });
            }
        }
    }]
    ,
    url: globals.module_url+'admin/user/update'
});

