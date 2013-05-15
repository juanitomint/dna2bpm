/**
 * @class Ext.app.PortalPanel
 * @extends Ext.Panel
 * A {@link Ext.Panel Panel} class used for providing drag-drop-enabled portal layouts.
 */
Ext.define('Ext.app.DropPanel', {
    extend: 'Ext.panel.Panel',
    alias: 'widget.droppanel',
    requires: [
        'Ext.layout.component.Body'
    ],
    cls: 'x-droppanel',
    bodyCls: 'x-droppanel',
    defaultType: 'droppanel',
// private
    initEvents : function(){
        this.callParent();
        this.dd = Ext.create('Ext.app.grid2formDDZone', this, this.dropConfig);
    }
});