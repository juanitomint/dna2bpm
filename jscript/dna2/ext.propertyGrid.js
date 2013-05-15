//---PROPERTY GRID
var propsGrid = Ext.create('Ext.grid.property.Grid', {
    id:'propsGrid',
    width:"auto",
    propertyNames: {
        tested: 'QA',
        borderWidth: 'Border Width'
    },
    loader: {
        url: 'http://localhost/beta/ci/jscript/dna2/testgrid.json',
        renderer: 'data',
        params: {
            userId: 1
        },
        success: function(dataloader,response,options){
            //dataloader.getTarget() <--this get the propertyGrid
            dataloader.getTarget().setSource(Ext.JSON.decode(response.responseText));
            dataloader.getTarget().setLoading(false);


        },
        // NO errors ! ;)
        failure: function(response,options){
        //TODO handle error
        },
        listeners: {
            beforeload: function(dataloader,options){
                dataloader.getTarget().setLoading(true);
                return true;
            }
        }

    },
    source: {}
});