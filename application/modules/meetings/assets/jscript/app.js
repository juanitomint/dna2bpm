Ext.onReady(function(){
                //---define components
                var left=Ext.create('Ext.Panel',
                {
                    region: 'west',
                    id: 'leftPanel', // see Ext.getCmp() below
                    title: 'Actions',
                    //                            title: 'West',
                    split: true,
                    width: 260,
                    minWidth: 200,
                    maxWidth: 700,
                    collapsible: true,
                    animCollapse: true,
                    margins: '0 0 0 0',
                    layout: 'fit'
                    ,items:[]
                }
            );
                //----right
                var right=Ext.create('Ext.Panel',
                {
                    region: 'east',
                    id: 'rightPanel', // see Ext.getCmp() below
                    title:'Business Attending',
                    split: true,
                    width: 370,
                    minWidth: 0,
                    maxWidth: 450,
                    collapsible: true,
                    margins: '0 0 0 0',
                    layout: 'fit'
                    ,items:[businessform]
                }
            );
                var center=Ext.create('Ext.Panel',
                { 
                    region:'center',
                    id: 'centerPanel',
                    layout: 'fit',
                    margins: '0 0 0 0'
                    ,items: {
                        xtype: 'tabpanel',
                        items:[mygrid,tree]
                    }
                });
                //---Create Application
                Ext.application({
                    name: 'ProcessBrowser',
                    launch: function() {
                        Ext.create('Ext.container.Viewport', {
                            layout:'border',
                            items:[ left,center,right,
                                { 
                                    
                                    region:'north',
                                    html: '<div id="app-logo"></div><h1>RBAC Manager</h1>',
                                    height: 50
                                }
                            ],
                            listeners: {
                                
                                afterrender: function(){
                                    //---Load Data
                                   
                                    Ext.data.StoreManager.lookup('GroupStore').load(); 
                                    //Ext.data.StoreManager.lookup('UserStore').load(); 
                                    Ext.data.StoreManager.lookup('TreeStore').load(); 
                                   
                                
                                }
                            }
                        });
                        
                    },
                    onLaunch: function(){
                    }
                });
                //---remove the loader
                Ext.get('loading').remove();
                Ext.fly('loading-mask').remove();
                
                
            });