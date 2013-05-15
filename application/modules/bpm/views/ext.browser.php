<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>Process Browser</title>
        <link rel="stylesheet" type="text/css" href="{base_url}jscript/ext/resources/css/ext-all-gray.css" />
        <link rel="stylesheet" type="text/css" href="{base_url}css/load_mask.css" />
    </head>
    <body>
        <div id="loading-mask" style=""></div>
        <div id="loading">
            <div class="loading-indicator">
                <img src="{base_url}css/ajax/loader18.gif" style="margin-right:8px;float:left;vertical-align:top;"/>
                <div style="float: left;">
                    Process Browser<br/>
                    <span id="loading-msg">
                        Loading styles and images...
                    </span>
                </div>
            </div>
        </div>
        <script type="text/javascript">
            //-----declare global vars
            var base_url='{base_url}';
        </script>
        <script type="text/javascript">document.getElementById('loading-msg').innerHTML += '<br/>Loading Core API...';</script>
        <script type="text/javascript" src="{base_url}jscript/ext/bootstrap.js"></script>
        <script type="text/javascript" src="{base_url}jscript/process_browser/process_tree.js"></script>

        <script type="text/javascript">

            Ext.Loader.setConfig({enabled: true}); 
            Ext.Loader.setPath('Ext.ux', '{base_url}jscript/ext/src/ux');
            //--- this is 4 CodeIgniter smart urls
            Ext.apply(Ext.data.AjaxProxy.prototype.actionMethods, {
                read: 'POST'
            });
            Ext.require([
                'Ext.grid.*',
                'Ext.data.*',
                'Ext.util.*',
                'Ext.Action',
                'Ext.tab.*',
                'Ext.button.*',
                'Ext.form.*',
                'Ext.layout.container.Card',
                'Ext.layout.container.Border',
                'Ext.ux.PreviewPlugin',
                'Ext.tree.*',
                'Ext.data.*',
                'Ext.tip.*'
            ]);
            Ext.onReady(function(){
                //---define components
                 
                      
                var left=Ext.create('Ext.Panel',
                {
                    region: 'west',
                    id: 'leftPanel', // see Ext.getCmp() below
                    title: 'Models',
                    //                            title: 'West',
                    split: true,
                    width: 300,
                    minWidth: 300,
                    maxWidth: 700,
                    collapsible: true,
                    animCollapse: true,
                    margins: '0 0 0 0',
                    layout: 'fit',
                    items:[tree]
                }
            );
                //----right
                var right=Ext.create('Ext.Panel',
                {
                    region: 'east',
                    id: 'rightPanel', // see Ext.getCmp() below
                    title: 'Properties',
                    //                            title: 'West',
                    split: true,
                    width: 300,
                    minWidth: 0,
                    maxWidth: 300,
                    collapsible: true,
                    margins: '0 0 0 0',
                    layout: 'fit'
                    //items:[westCmp]
                }
            );
                //---Create Application
                Ext.application({
                    name: 'ProcessBrowser',
                    launch: function() {
                        Ext.create('Ext.container.Viewport', {
                            layout:'border',
                            items:[ left,
                                
                                { 
                                    title:'Center',
                                    region:'center',
                                    html: 'My App1'
                                },
                                right
                            ]
                        });
                    }
                });
                //---remove the loader
                Ext.get('loading').remove();
                Ext.fly('loading-mask').remove();
            });
        </script>
    </body>
</html>