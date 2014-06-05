// do the Ext.Ajax.request
function load_props(url, id, nocache) {
    propsGrid = Ext.getCmp('propsGrid');
    if (propsGrid) {
//---TODO check cached sources
//---set actual id
        propsGrid.activeRecord = id;
        //console.log('serving:'+id);
        //---set actual url
        propsGrid.url = url;
        if (pgridCache[id] && !nocache) {
//---if already loaded just takeit from local cache
            propsGrid.setSource(pgridCache[id]);
        } else {
            propsGrid.setLoading(true);
            //---select the record 
            //mygrid.selModel.select(id);
            Ext.Ajax.request({
// the url to the remote source
                url: url,
                method: 'POST',
                // define a handler for request success
                params: {
                    id: id,
                    repoId:globals.repoId
                },
                success: function(response, options) {
                    var propsGrid = Ext.getCmp('propsGrid');
                    propsGrid.setSource(Ext.JSON.decode(response.responseText));
                    //---send result to the cache
                    pgridCache[id] = propsGrid.getSource();
                    propsGrid.setLoading(false);
                },
                // NO errors ! ;)
                failure: function(response, options) {
                    alert('Error Loading:' + response.err);
                    propsGrid.setLoading(false);
                }
            });
        }
//----enable CodeEditor btn
        Ext.getCmp('propsGrid').enable(true);
    }
}
function save_props(url) {
    if (propsGrid) {
//---TODO check cached sources
//--get actual id
        id = propsGrid.activeRecord;
        //---if already loaded just takeit from local cache    
        propsGrid.setLoading(true);
        data = propsGrid.getSource();
        console.log
        Ext.Ajax.request({
// the url to the remote source
            url: url,
            method: 'POST',
            // define a handler for request success
            jsonData: {
                repoId:globals.repoId,
                path:id,
                data:data
            },
            success: function(response, options) {
                var propsGrid = Ext.getCmp('propsGrid');
                var kpi = Ext.JSON.decode(response.responseText);
                //----set source with returned data
                propsGrid.setSource(kpi);
                //---update tree
                tree.store.read();
                propsGrid.setLoading(false);
            },
            // NO errors ! ;)
            failure: function(response, options) {
                alert('Error Loading:' + response.err);
                propsGrid.setLoading(false);
            }
        });
    }
}