var user_selector=Ext.create('Ext.panel.Panel', 
{
    height:300,
    items :[{
        xtype: 'toolbar',
        items: [
        //---autocomplete from groups
            
        {
            xtype: 'combo',
            id:'groupCombo',
            store: Ext.data.StoreManager.lookup('GroupStore'),
            displayField: 'name',
            typeAhead: true,
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
                    groupField={};
                    groupField.value=propsGrid.getProperty('groups');

                    if(groupField.value){
                        groups=groupField.value.split(',');                  
                    } else {
                        groups=new Array();
                    }
                    if(groups.indexOf(combo.value.toString())==-1){
                        groups.push(combo.value);
                        propsGrid.setProperty('groups',groups.join(','));
                    }
                    renderGroups();
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
        })
        ]
    }
    //----container 4 grid
        
       
    ]
} //---end fieldset
);