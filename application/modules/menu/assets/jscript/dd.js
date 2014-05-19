

    var formPanelDropTarget = Ext.create('Ext.dd.DropTarget', formPanelDropTargetEl, {
        ddGroup: 'GridExample',
        notifyEnter: function(ddSource, e, data) {

            //Add some flare to invite drop.
            formPanel.body.stopAnimation();
            formPanel.body.highlight();
        },
        notifyDrop  : function(ddSource, e, data){

            // Reference the record (single selection) for readability
            var selectedRecord = ddSource.dragData.records[0];

            // Load the record into the form
            formPanel.getForm().loadRecord(selectedRecord);

            // Delete record from the source store.  not really required.
            ddSource.view.store.remove(selectedRecord);

            return true;
        }
    });