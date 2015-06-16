///-------------utility function to POST to an url
function postAjax(url, params, callback) {
    var rtn = '';
    Ext.Ajax.request({
        // the url to the remote source
        url: url,
        method: 'POST',
        params: params,

        // define a handler for request success
        success: callback,
        // NO errors ! ;)
        failure: function(response, options) {
            alert("ERROR!\n" + response.request.options.url + "\n" + response.status + " " + response.statusText);
            //TODO handle error
        }
    });
}
var ace_config = {
    fontSize: 16
}

var hooksPHP = {
    'edit': 'php',
    'view': 'php',
    'process': 'php',
    'print': 'php',
    'list': 'php',
    'subform': 'php',
    'export': 'php'
};
var hooksJS = {
    'edit': 'javascript',
    'view': 'javascript',
    'print': 'javascript'
};


var codeEditorPHP;
var codeEditorJS;

function createCodeWindow(title, hooks, caller, url, ref) {
    var window = Ext.getCmp('codeEditor');
    var tabs = [];
    //---TODO bind to load and save
    Ext.iterate(hooks, function(name, mode) {

        tabs.push(
            Ext.create('Ext.panel.Panel', {
                mode: mode,
                title: name,
                layout: 'fit',
                caller: caller,
                ref: ref,
                html: "<textarea name='edit' id='" + ref + name + "Code' class='codeEditor' style='width:100%;height:100%;borer: 1x dashed grey'></textarea>",
                listeners: {
                    activate: function(me, options) {
                        if (!me.codeActive) {
                            var obj = me.getEl().select('textarea').elements[0];
                            var url = me.up('window').proc;
                            //---define Script lang
                            var lang = me.mode;
                            //---define reference 4 process
                            //var ref=propsGrid.store.data.get('cname').data.value;
                            var params = {
                                'action': 'load',
                                'id': ref,
                                'context': me.title,
                                'lang': lang
                            };
                            //console.log('initiating editor:' + obj.id);

                            /* ACE */
                            $('#' + obj.id).ace(
                            $.extend(ace_config,    {
                                lang: options.mode
                            })
                            );
                            me.setLoading('Loading...');
                            postAjax(url, params,
                                function(response, options) {
                                    var result = Ext.JSON.decode(response.responseText);
                                    //---check 4 error
                                    if (result.ok) {
                                        var editor = $('#' + obj.id).data('ace').editor.ace;
                                        editor.setValue(result.code, 1);


                                    }
                                    else {

                                    }
                                    me.setLoading(false);
                                }
                            );
                            //---end post

                            me.codeActive = true;
                        }
                    }

                }
            })
        );
    });
    Ext.create('Ext.window.Window', {
        title: title,
        id: Ext.id(),
        height: 500,
        width: 700,
        proc: url,
        //closeAction:'close',
        layout: 'fit',
        tbar: [{
                xtype: 'button',
                text: 'Save',
                icon: globals.base_url + 'css/ext_icons/save.gif',
                tooltip: 'Save changes',
                handler: function(me) {
                    //---get the active window
                    var win = me.up('window');
                    //---get the url to process
                    var url = win.proc;
                    //---get the active tab (go up 1 level and sown to tabpanel
                    var tab = me.up('window').child('tabpanel').getActiveTab();
                    //---get textarea object
                    var obj = tab.getEl().select('textarea').elements[0];
                    //---define Script lang
                    var lang = tab.mode;
                    var context = tab.title;
                    //---find textarea
                    var textArea = tab.getEl().select('textarea').elements[0]
                    var code = editAreaLoader.getValue(obj.id);
                    var params = {
                        'action': 'save',
                        'id': tab.ref,
                        'context': context,
                        'lang': lang,
                        'code': code
                    };
                    var result = '';
                    postAjax(url, params,
                        function(response, options) {
                            var statusBar = me.up('window').child('statusbar');
                            statusBar.showBusy();
                            result = Ext.JSON.decode(response.responseText);

                            if (result.ok) {
                                statusBar.setStatus({
                                    text: 'Save ok!',
                                    iconCls: 'x-status-valid'
                                });
                            }
                            else {
                                statusBar.setStatus({
                                    text: 'Error saving:' + response.err,
                                    iconCls: 'x-status-error'
                                });
                            }
                        });
                    //---end postAjax

                }
            }, {
                xtype: 'button',
                text: 'Reload',
                icon: globals.base_url + 'css/ext_icons/refresh.gif',
                tooltip: 'Reload from db an discard changes',
                handler: function(me) {
                    //---get the active window
                    var win = me.up('window');
                    //---get the url to process
                    var url = win.proc;
                    //---get the active tab (go up 1 level and sown to tabpanel
                    var tab = me.up('window').child('tabpanel').getActiveTab();
                    //---get textarea object
                    var obj = tab.getEl().select('textarea').elements[0];
                    //---define Script lang
                    var lang = tab.mode;
                    var context = tab.title;
                    var params = {
                        'action': 'load',
                        'id': tab.ref,
                        'context': context,
                        'lang': lang
                    };
                    //---todo ask 4 confirmation
                    Ext.MessageBox.confirm('Confirm', 'Are you sure you want to discard your chages? (if any)', function(btn) {
                        if (btn == 'yes') {
                            postAjax(url, params,
                                function(response, options) {
                                    var result = Ext.JSON.decode(response.responseText);
                                    //---check 4 error
                                    if (result.ok) {
                                        editAreaLoader.setValue(obj.id, result.code);
                                        var statusBar = me.up('window').child('statusbar');
                                        statusBar.setStatus({
                                            text: 'Reload ok!',
                                            iconCls: 'x-status-valid'
                                        });
                                    }
                                    else {}
                                });
                        } //---end if
                    });

                }
            }

        ],
        items: {
            xtype: 'tabpanel',
            items: tabs
        },
        bbar: Ext.create('Ext.ux.statusbar.StatusBar', {
            defaultText: 'ready',
            statusAlign: 'right', // the magic config
            items: []
        })

    }).show(caller, function() {
        //initialize Activetab code editor
        var tab = this.child('tabpanel').getActiveTab();
        var obj = tab.getEl().select('textarea').elements[0];
        /* ACE */
        $('#' + obj.id).ace(
            $.extend(ace_config, {
                lang: tab.initialConfig.mode
            })
        );
        /* EditArea */
        // editAreaLoader.init({
        //     id :obj.id,
        //     syntax: tab.initialConfig.mode,
        //     start_highlight: true
        // });
    });
    ///-----end window
    ////---end if

    /*else {
            win.show(caller, function() {
                
                });
        }*/

}