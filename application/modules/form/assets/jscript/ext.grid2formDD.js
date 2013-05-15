function initDD(){
    return;
    /****
     * Setup Drop Targets
     ***/

    // This will make sure we only drop to the view container
    var formPanelDropTargetEl =  formPanel.body.dom;

    var overrides={       
        ddGroup: 'GridGroup',
        overClass:'ddOver'
    /*
        notifyEnter: function(ddSource, e, data) {

            //Add some flare to invite drop.
            //console.log(this.el);
        
            this.el.stopAnimation();
            this.el.highlight();
             
        },*/
        
    }
    //var formPanelDropTarget = Ext.create('Ext.dd.DropTarget', formPanelDropTargetEl,overrides);
    var formPanelDropTarget = Ext.create('Ext.app.grid2formDDZone', formPanelDropTargetEl,overrides);
}
Ext.define('Ext.app.grid2panelDDZone', {
    extend: 'Ext.dd.DropTarget',
    ddGroup: 'GridGroup',
    overClass:'ddOver',
   
    notifyDrop  : function(ddSource, e, data){

        // Reference the record (single selection) for readability
        var selectedRecord = ddSource.dragData.records[0];

        // Load the record into the form
        
        //wht 2do!?

        // Delete record from the source store.  not really required.
        //ddSource.view.store.remove(selectedRecord);
        console.log(selectedRecord);
        return this.callParent(arguments);
        
    }
});
Ext.define('Ext.app.grid2formDDZone', {
    extend: 'Ext.dd.DropTarget',
    ddGroup: 'GridGroup',
    overClass:'ddForm',
    notifyOver: function(dd, e, data) {
        var xy = e.getXY(),
        pos=0,
        h = 0,
        match = false,overSelf = false;
        
        len = formPanel.items.length;
        for (len; pos < len; pos++) {
            overItem = over.items.items[pos];
            h = overItem.el.getHeight();
            if (h === 0) {
                overSelf = true;
            } else if ((overItem.el.getY() + (h)) > xy[1]) {
                match = true;
                //console.log (pos);
                break;
            }
        }
        return true;
    },
    notifyDrop  : function(ddSource, e, data){

        // Reference the record (single selection) for readability
        var selectedRecord = ddSource.dragData.records[0];

        // Load the record into the form
        
        //wht 2do!?

        // Delete record from the source store.  not really required.
        //ddSource.view.store.remove(selectedRecord);
        console.log(selectedRecord);
        return this.callParent(arguments);
        
    }
});

Ext.define('Ext.app.DDform', {
    extend: 'Ext.form.Panel',
    alias: 'widget.DDform',
    bodyPadding: 5,
    collapsible:true,    
    tools:[{
        type:'refresh',
        tooltip: 'Refresh form Data',
        // hidden:true,
        handler: function(event, toolEl, panel){
        // refresh logic
        }
    },
    {
        type:'help',
        tooltip: 'Get Help',
        handler: function(event, toolEl, panel){
        // show help here
        }
    }],
    // private
    initEvents : function(){
        //this.callParent();
        console.log('initEvents');
        this.dd = Ext.create('Ext.app.grid2panelDDZone', this.body.dom);
    },
    // private
    beforeDestroy : function() {
        if (this.dd) {
            this.dd.unreg();
        }
        this.callParent();
    }
    
});

Ext.define('Ext.app.DDpanel', {
    extend: 'Ext.panel.Panel',
    alias: 'widget.DDpanel',
    bodyPadding: 5,
    collapsible:true,    
    tools:[{
        type:'refresh',
        tooltip: 'Refresh form Data',
        // hidden:true,
        handler: function(event, toolEl, panel){
        // refresh logic
        }
    },
    {
        type:'help',
        tooltip: 'Get Help',
        handler: function(event, toolEl, panel){
        // show help here
        }
    }],
    // private
    initEvents : function(){
        //this.callParent();
        console.log('initEvents');
        this.dd = Ext.create('Ext.app.grid2formDDZone', this.body.dom);
    },
    // private
    beforeDestroy : function() {
        if (this.dd) {
            this.dd.unreg();
        }
        this.callParent();
    }
    
});