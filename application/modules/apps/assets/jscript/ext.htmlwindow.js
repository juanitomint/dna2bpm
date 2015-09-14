Ext.define('Ext.ux.htmlwindow', {
    extend:'Ext.window.Window',
    title: 'HtmlFieldEditor:'+this.id,
    closeAction:'hide',
    layout: 'fit',
    renderTo:Ext.getBody(),
    items: {  // Let's put the html editor
        xtype: 'htmleditor',
        value:this.value
    },
    listeners:{
        activate:function(w,o){
            this.setWidth(500);
        }
    },    

    //----bind fields methods
    reset: function(){
        this.items.items[0].setValue('');
        this.show();
    },
    setValue: function(value){
        this.items.items[0].setValue(value);
    },
    getValue: function(){
        this.hide();
        return this.items.items[0].getValue();
        
    },
    isValid:function(){
        return true;
    }
    
});

