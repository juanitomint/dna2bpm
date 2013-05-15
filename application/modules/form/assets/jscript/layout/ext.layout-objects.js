/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

Ext.define('Form.layout.PanelH', {
    extend: 'Ext.panel.Panel',
    constructor: function (config) {
        config.layout='hbox';
        config.bodyPadding=5;
        config.frame=true;
        config.items=[
        {
            bodyPadding:5,
            flex:1,
            html:'panel 3', 
            region: 'north',
            frame:true,
            resizable:true
        },
        {
            bodyPadding:5,
            flex:1,
            html:'panel 4', 
            region: 'center',
            frame:true
        },
        {
            xtype:'button',
            text:'<i class="icon-plus-sign"></i> Add Box',
            handler:function (me,e){
                console.log(me);
            }
        }
        ];
        this.callParent(arguments); // calls Ext.panel.Panel's constructor   
    }
     
});
