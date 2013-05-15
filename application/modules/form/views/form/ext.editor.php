<html>
    <head>
        <title>DNA2 Form Editor</title>
        <link rel="stylesheet" type="text/css" href="{base_url}jscript/ext/resources/css/ext-all-gray.css" />
        <link rel="stylesheet" type="text/css" href="{base_url}jscript/ext/src/ux/statusbar/css/statusbar.css" />
        <link rel="stylesheet" type="text/css" href="{base_url}jscript/ext/src/ux/css/CheckHeader.css" />
        <style type="text/css">
            p {
                margin:5px;
            }
            .views {
                background-image:url(../shared/icons/fam/folder_wrench.png);
            }
            .nav {
                background-image:url(../shared/icons/fam/folder_go.png);
            }
            .info {
                background-image:url(../shared/icons/fam/information.png);
            }
            .new{color:red;}
            .hasPHP{
                border:1px dotted blue;
                background-color: #acc5ff;
                color:blue;
                padding: 1px;

            }
            .hasJS{
                border:1px dotted green;
                background-color:#afffac;
                color: green;
                padding: 1px;

            }
            #loading-mask{
                background-color:white;
                height:100%;
                position:absolute;
                left:0;
                top:0;
                width:100%;
                z-index:20000;
            }
            #loading .loading-indicator{
                background:white;
                color:#444;
                font:bold 13px Helvetica, Arial, sans-serif;
                height:auto;
                margin:0;
                padding:10px;
            }
            #loading{
                height:auto;
                position:absolute;
                left:45%;
                top:30%;
                padding:2px;
                z-index:20001;
            }
            #loading a {
                color:#225588;
            }
            #loading .loading-indicator{
                background:white;
                color:#444;
                font:bold 13px Helvetica, Arial, sans-serif;
                height:auto;
                margin:0;
                padding:10px;
            }
            #loading-msg {
                font-size: 10px;
                font-weight: normal;
            }
            .locked {
                background-image: url("{module_url}assets/images/icon_padlock.gif") !important;
                background-repeat: no-repeat;
                padding-left: 17px;
            }
            .add24 {
                background-image: url("{base_url}css/Icons/24x24/Document 2.gif") !important;
            }
            .edit{
                background-image: url("{base_url}css/Icons/16x16/Document 2 Edit 2.gif") !important;
            }
            .open{
                background-image: url("{base_url}css/Icons/16x16/Document 2.gif") !important;
            }
            .newDoc{
                background-image: url("{base_url}css/Icons/16x16/Document 2 New.gif") !important;
            }
            .import{
                background-image: url("{base_url}css/Icons/16x16/Document 2 Back.gif") !important;
            }
            .x-toolbar-vertical .x-btn-inner{
                text-align: left;
            }
            #msg-div {
                position:absolute;
                left:35%;
                top:10px;
                width:300px;
                z-index:20000;
            }
            #msg-div .msg {
                border-radius: 8px;
                -moz-border-radius: 8px;
                background: #F6F6F6;
                border: 2px solid #ccc;
                margin-top: 2px;
                padding: 10px 15px;
                color: #555;
            }
            #msg-div .msg h3 {
                margin: 0 0 8px;
                font-weight: bold;
                font-size: 15px;
            }
            #msg-div .msg p {
                margin: 0;
            }
        </style>
    </head>
    <body>
        <div id="loading-mask" style=""></div>
        <div id="loading">
            <div class="loading-indicator">
                <img src="{base_url}css/ajax/loader18.gif" style="margin-right:8px;float:left;vertical-align:top;"/>
                <div style="float: left;">
                    DNA&sup2; Form Editor<br/>
                    <span id="loading-msg">
                        Loading styles and images...
                    </span>
                </div>
            </div>
        </div>
        <script type="text/javascript">document.getElementById('loading-msg').innerHTML += '<br/>Loading Core API...';</script>
        <script type="text/javascript" src="{base_url}jscript/ext/bootstrap.js"></script>
        <script language="javascript" type="text/javascript" src="{base_url}jscript/editarea/edit_area/edit_area_full.js"></script>
        <script type="text/javascript">
            //-----declare global vars
            var base_url='{base_url}';
            var module_url='{module_url}';
            var idapp='{idapp}';
            var idobj='{idobj}';
            var imin=idobj;
            var pgridCache = new Array();
            var pgridTypeCache=new Array();
            var optionsCache=new Array;
            var optionsStore;
            var optionsDefault;
            Ext.Loader.setConfig({enabled: true}); 
            Ext.Loader.setPath('Ext.ux', '{base_url}jscript/ext/src/ux');
            //--- this is 4 CodeIgniter smart urls
            Ext.apply(Ext.data.AjaxProxy.prototype.actionMethods, {
                read: 'POST'
            });
            ///-------------utility function to POST to an url
            function postAjax(url,params,callback){
                var rtn='';
                Ext.Ajax.request({
                    // the url to the remote source
                    url: url,
                    method: 'POST',
                    params: params,
                    
                    // define a handler for request success
                    success:callback,
                    // NO errors ! ;)
                    failure: function(response,options){
                        alert("ERROR!\n"+response.request.options.url+"\n"+response.status+" "+response.statusText);
                        //TODO handle error
                    }
                });
            }
            //---add some flavor to Property grid
            Ext.override(Ext.grid.PropertyGrid, {
                getProperty: function(prop){
                    return propsGrid.store.getProperty(prop).data.value;
                }
            });
            
            
        </script>
        <script type="text/javascript">document.getElementById('loading-msg').innerHTML += '<br/>Loading Data Components ';</script>
        <script type="text/javascript" src="{module_url}assets/jscript/ext.data.js"></script>
        <script type="text/javascript">document.getElementById('loading-msg').innerHTML += '<br/>Loading Component Tree...';</script>
        <script type="text/javascript" src="{module_url}assets/jscript/ext.components.grid.js"></script>
        <script type="text/javascript">document.getElementById('loading-msg').innerHTML += '<br/>Loading propertyGrid...';</script>
        <script type="text/javascript" src="{module_url}assets/jscript/ext.baseProperties.js"></script>
<!--        <script type="text/javascript" src="{base_url}jscript/form/ext.propertyGrid1.js"></script>-->
        <script type="text/javascript">document.getElementById('loading-msg').innerHTML += '<br/>Loading properties Loader...';</script>
        <script type="text/javascript" src="{module_url}assets/jscript/ext.load_props.js"></script>
        <script type="text/javascript">document.getElementById('loading-msg').innerHTML += '<br/>Loading Form Grid...';</script>
        <script type="text/javascript" src="{module_url}assets/jscript/ext.grid.js"></script>
        <script type="text/javascript">document.getElementById('loading-msg').innerHTML += '<br/>Loading Code Editor...';</script>
        <script type="text/javascript" src="{module_url}assets/jscript/ext.code_editor.js"></script>
        <script type="text/javascript">document.getElementById('loading-msg').innerHTML += '<br/>Building User Interface...';</script>
        <script type="text/javascript">
            
            Ext.require(['*']);
            var remove_loaders=function(){
                Ext.get('loading').remove();
                Ext.fly('loading-mask').remove();
            }
            //----extended classes-& Functions----------------------------------           
            //----onReady
            Ext.application({
                name: 'formEditor',
                init: function(){

                }                
                ,launch: 
                    function() {
                    Ext.example = function(){
                        var msgCt;

                        function createBox(t, s){
                            // return ['<div class="msg">',
                            //         '<div class="x-box-tl"><div class="x-box-tr"><div class="x-box-tc"></div></div></div>',
                            //         '<div class="x-box-ml"><div class="x-box-mr"><div class="x-box-mc"><h3>', t, '</h3>', s, '</div></div></div>',
                            //         '<div class="x-box-bl"><div class="x-box-br"><div class="x-box-bc"></div></div></div>',
                            //         '</div>'].join('');
                            return '<div class="msg"><h3>' + t + '</h3><p>' + s + '</p></div>';
                        }
                        return {
                            msg : function(title, format){
                                if(!msgCt){
                                    msgCt = Ext.core.DomHelper.insertFirst(document.body, {id:'msg-div'}, true);
                                }
                                var s = Ext.String.format.apply(String, Array.prototype.slice.call(arguments, 1));
                                var m = Ext.core.DomHelper.append(msgCt, createBox(title, s), true);
                                m.hide();
                                m.slideIn('t').ghost("t", { delay: 3000, remove: true});
                            },

                            init : function(){
                                //            var t = Ext.get('exttheme');
                                //            if(!t){ // run locally?
                                //                return;
                                //            }
                                //            var theme = Cookies.get('exttheme') || 'aero';
                                //            if(theme){
                                //                t.dom.value = theme;
                                //                Ext.getBody().addClass('x-'+theme);
                                //            }
                                //            t.on('change', function(){
                                //                Cookies.set('exttheme', t.getValue());
                                //                setTimeout(function(){
                                //                    window.location.reload();
                                //                }, 250);
                                //            });
                                //
                                //            var lb = Ext.get('lib-bar');
                                //            if(lb){
                                //                lb.show();
                                //            }
                            }
                        };
                    }();
                    var bogusAction = Ext.create('Ext.Action', {
                        text: 'Action 1',
                        iconCls: 'icon-add',
                        handler: function(button){
                            Ext.example.msg('Click', 'You clicked on "Action 1".');
                        }
                    });
                

                    // NOTE: This is an example showing simple state management. During development,
                    // it is generally best to disable state management as dynamically-generated ids
                    // can change across page loads, leading to unpredictable results.  The developer
                    // should ensure that stable state ids are set for stateful components in real apps.
                    //Ext.state.Manager.setProvider(Ext.create('Ext.state.CookieProvider'));

                
                
                    ////////////////////////////////////////////////////////////////
                    ///////////////////      TEST Proyectos DNA²DATA /////////////////////////////
                    ////////////////////////////////////////////////////////////////
                    var empty= Ext.create('Ext.Panel', {
                        cls:'empty',
                        bodyStyle:'background:#f1f1f1',
                        html:'<br/><br/>&lt;empty center panel&gt;'
                    });
                    var center = Ext.create('Ext.Panel', 
                    {
                        region:'center',
                        margins:'0 0 0 0',
                        layout:'border',
                        items: [
                            {
                                region:'center',
                                layout:'fit',
                                items:[mygrid]
                            }
                            ,{
                                region:'south',
                                layout:'fit',
                                title: "<img align='top' src='"+base_url+"css/ext_icons/preview-hide.gif'/> All available Frames",
                                collapsible: true,
                                collapsed:true,
                                animCollapse: false,
                                resizable:false,
                                split: true,
                                height:301,
                                items:[othergrid]
                            }
                        ]
                    }
                );
                    var top=Ext.create('Ext.Panel',{
                        region:'north',
                        title:'Editing: {form title} :: {form idobj}',
                        layout: 'fit'
                    });
                    var right=Ext.create('Ext.Panel',
                    {
                        id:'rightPanel',
                        region: 'east',
                        title:'Properties',
                        animCollapse: true,
                        collapsible: true,
                        animCollapse: false,
                        split: true,
                        width: 400, // give east and west regions a width
                        minWidth: 300,
                        maxWidth: 400,
                        margins: '0 0 0 0',
                        layout: 'fit',
                        items: [
                            ///-------Pgrid
                            propsGrid
                            ///-------Pgrid

                        ]
                   
                    }
                );
                    var south=Ext.create('Ext.Panel',
                    {
                        region: 'south',
                        split: true,
                        height: 100,
                        minSize: 100,
                        maxSize: 200,
                        collapsible: true,
                        collapsed: true,
                        title: 'South',
                        margins: '0 0 0 0'
                    }
                );
                    var left=Ext.create('Ext.Panel',
                    {
                        region: 'west',
                        id: 'leftPanel', // see Ext.getCmp() below
                        title: 'Form widgets',
                        //                            title: 'West',
                        split: true,
                        width: 200,
                        minWidth: 200,
                        maxWidth: 200,
                        collapsible: true,
                        animCollapse: true,
                        margins: '0 0 0 0',
                        layout: 'fit',
                        items:[westCmp]
                    }
                );
                    
                    ////////////////////////////////////////////////////////////////
                    ///////////////////    VIEWPORT    /////////////////////////////
                    ////////////////////////////////////////////////////////////////
                    Ext.create('Ext.Viewport', {
                        layout:'border',
                        items:[top,center, left,right]
                        ,listeners:{
                            render: remove_loaders
                        }
                    });
                    
                }
            
            });
        
        </script>

    </body>
</html>