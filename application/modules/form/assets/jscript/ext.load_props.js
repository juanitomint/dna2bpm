// do the Ext.Ajax.request
function load_props(url,id,nocache){
    var propsGrid = Ext.getCmp('propsGrid');
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
                url: url+propsGrid.idframe,
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
                    var frame=Ext.JSON.decode(response.responseText);
                    //---update pgrid 
                    propsGrid.idframe=frame.idframe;
                    //----set source with returned data
                    propsGrid.setSource(frame);
                    //----update gridview
                    //----update Grid whith data
                    row=mygrid.getSelectionModel().selected.items[0];
                    if(row){
                        row.dirty=true;
                        //--update only present data.
                        dgstore.model.prototype.fields.keys.forEach(
                            function(key){
                                row.data[key]=frame[key];
                            });
                    }
                    mygrid.getView().refresh(true);
                    //---update cache
                    pgridCache[id]=propsGrid.getSource();
                    pgridCache[frame.idframe]=propsGrid.getSource();
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