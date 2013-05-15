// do the Ext.Ajax.request
function load_props(url,id,nocache){
    propsGrid = Ext.getCmp('propsGrid');
    if(propsGrid){
        //---TODO check cached sources
        //---set actual id
        propsGrid.activeRecord=id;
        //console.log('serving:'+id);
        //---set actual url
        propsGrid.url=url;
        if(pgridCache[id] && !nocache){
            //---if already loaded just takeit from local cache
            propsGrid.setSource(pgridCache[id]);
        } else {       
            propsGrid.setLoading(true);
            Ext.Ajax.request({
                // the url to the remote source
                url: url,
                method: 'POST',
                // define a handler for request success
                success: function(response, options){
                    var propsGrid = Ext.getCmp('propsGrid');
                    propsGrid.setSource(Ext.JSON.decode(response.responseText));
                    //---send result to the cache
                    pgridCache[id]=propsGrid.getSource();
                    propsGrid.setLoading(false);
                },
                // NO errors ! ;)
                failure: function(response,options){
                    alert('Error Loading:'+response.err);
                    propsGrid.setLoading(false);
                
                }
            });
        }
        //----enable CodeEditor btn
        Ext.getCmp('propsGrid').enable(true);
        
    }
}
function save_props(url){
    var propsGrid = Ext.getCmp('propsGrid');
    if(propsGrid){
        //---TODO check cached sources
        //--get actual id
        id=propsGrid.activeRecord;
        if(pgridCache[id]){
            var data=pgridCache[id];
            
            //---if already loaded just takeit from local cache    
            propsGrid.setLoading(true);
            Ext.Ajax.request({
                // the url to the remote source
                url: url,
                method: 'POST',
                // define a handler for request success
                params:pgridCache[id],
                success: function(response, options){
                    var propsGrid = Ext.getCmp('propsGrid');
                    var app=Ext.JSON.decode(response.responseText);
                    //---update pgrid 
                    propsGrid.id=app.id;
                    delete app.id;
                    //----set source with returned data
                    propsGrid.setSource(app);
                    //----update gridview
                    //----update Grid whith data
                    //---convert source data to propper Form model 
                    mygrid.store.data.items[mygrid.store.data.keys.indexOf(id)]=Ext.ModelManager.create(app,'AppsModel');
                    mygrid.getView().refresh(true);
                    //---update cache
                    pgridCache[id]=propsGrid.getSource();
                    pgridCache[app.idform]=propsGrid.getSource();
                    propsGrid.setLoading(false);
                },
                // NO errors ! ;)
                failure: function(response,options){
                    alert('Error Loading:'+response.err);
                    propsGrid.setLoading(false);
                
                }
            });
        }        
        
    }
}