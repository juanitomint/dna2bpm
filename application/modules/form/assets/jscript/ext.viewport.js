var remove_loaders=function(){
    Ext.get('loading').remove();
    Ext.fly('loading-mask').remove();
}

//----onReady
            Ext.application({
                name: 'formEditor',
                init: function(){
                    //Ext.get('loading').remove();
                    //Ext.fly('loading-mask').remove();
                    
                    //propsGrid.loader.load();
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
                                title: "<img align='top' src='"+globals.base_url+"css/ext_icons/preview-hide.gif'/> All available Frames",
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
