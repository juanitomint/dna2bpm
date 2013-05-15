/*

This file is part of Ext JS 4

Copyright (c) 2011 Sencha Inc

Contact:  http://www.sencha.com/contact

Commercial Usage
Licensees holding valid commercial licenses may use this file in accordance with the Commercial Software License Agreement provided with the Software or, alternatively, in accordance with the terms contained in a written agreement between you and Sencha.

If you are unsure which license is appropriate for your use, please contact the sales department at http://www.sencha.com/contact.

 */
/**
 * @class Ext.ux.LiveFilterGridPanel
 * @extends Ext.grid.Panel
 * <p>A GridPanel class with live search support.</p>
 * @author Nicolas Ferrero & Juan Borda
 */
Ext.define('Ext.ux.LiveFilterGridPanel', {
    extend: 'Ext.grid.Panel',
    requires: [
    'Ext.toolbar.TextItem',
    'Ext.form.field.Checkbox',
    'Ext.form.field.Text',
    'Ext.ux.statusbar.StatusBar'
    ],
    
    /**
     * @private
     * search value initialization
     */
    searchValue: null,
    
    /**
     * @private
     * The row indexes where matching strings are found. (used by previous and next buttons)
     */
    indexes: [],
    
    /**
     * @private
     * The row index of the first search, it could change if next or previous buttons are used.
     */
    currentIndex: null,
    
    /**
     * @private
     * The generated regular expression used for searching.
     */
    searchRegExp: null,
    
    /**
     * @private
     * Case sensitive mode.
     */
    caseSensitive: false,
    
    /**
     * @private
     * Regular expression mode.
     */
    regExpMode: false,
    
    /**
     * @cfg {String} matchCls
     * The matched string css classe.
     */
    matchCls: 'x-livesearch-match',
    
    defaultStatusText: 'Nothing Found',
    
    // Component initialization override: adds the top and bottom toolbars and setup headers renderer.
    initComponent: function() {
        var me = this;
        me.tbar = [me.indexes.join(',')+':',
            {
            xtype: 'textfield',
            name: 'searchField',
            hideLabel: true,
            width: 200,
            listeners: {
                     change: {
                         fn: me.onTextFieldChange,
                         scope: this,
                         buffer: 100
                     }
                 }
        }
        ,{
            xtype: 'button', 
            text: 'Filter',
            handler: me.onTextFieldChange,
            icon:globals.base_url+'jscript/ext/src/ux/filter/filter.gif',
            scope: me
        }
        ,{
            xtype: 'button', 
            text: 'Clear Filter',
            icon:globals.base_url+'jscript/ext/src/ux/filter/filter_delete.gif',
            handler: function(){
                me.store.clearFilter();
            },
            scope: me
        }
        ];

        me.bbar = Ext.create('Ext.ux.StatusBar', {
            defaultText: me.defaultStatusText,
            name: 'searchStatusBar'
        });
        
        me.callParent(arguments);
    },
    
    // afterRender override: it adds textfield and statusbar reference and start monitoring keydown events in textfield input 
    afterRender: function() {
        var me = this;
        me.callParent(arguments);
        me.textField = me.down('textfield[name=searchField]');
        me.statusBar = me.down('statusbar[name=searchStatusBar]');
    },
    // detects html tag
    tagsRe: /<[^>]*>/gm,
    
    // DEL ASCII code
    tagsProtect: '\x0f',
    
    // detects regexp reserved word
    regExpProtect: /\\|\/|\+|\\|\.|\[|\]|\{|\}|\?|\$|\*|\^|\|/gm,
    
    /**
 * In normal mode it returns the value with protected regexp characters.
 * In regular expression mode it returns the raw value except if the regexp is invalid.
 * @return {String} The value to process or null if the textfield value is blank or invalid.
 * @private
 */
    getSearchValue: function() {
        var me = this,
        value = me.textField.getValue();
            
        if (value === '') {
            return null;
        }
        if (!me.regExpMode) {
            value = value.replace(me.regExpProtect, function(m) {
                return '\\' + m;
            });
        } else {
            try {
                new RegExp(value);
            } catch (error) {
                me.statusBar.setStatus({
                    text: error.message,
                    iconCls: 'x-status-error'
                });
                return null;
            }
            // this is stupid
            if (value === '^' || value === '$') {
                return null;
            }
        }
        
        var length = value.length,
        resultArray = [me.tagsProtect + '*'],
        i = 0,
        c;
            
        for(; i < length; i++) {
            c = value.charAt(i);
            resultArray.push(c);
            if (c !== '\\') {
                resultArray.push(me.tagsProtect + '*');
            } 
        }
        return resultArray.join('');
    },
    
    /**
 * Finds all strings that matches the searched value in each grid cells.
 * @private
 */
    onTextFieldChange: function() {
        var me = this,
        count = 0;

        me.view.refresh();
        // reset the statusbar
        me.statusBar.setStatus({
            text: me.defaultStatusText,
            iconCls: ''
        });

        me.searchValue = me.getSearchValue();
         
        //me.indexes = [];
        var indexes=me.indexes;
        me.currentIndex = null;
        //---clear previous filter
        me.store.clearFilter();
        if (me.searchValue !== null) {
            me.searchRegExp = new RegExp(me.searchValue, 'g' + (me.caseSensitive ? '' : 'i'));
            var regexp=me.searchRegExp;
            //console.log(me.index,me.searchRegExp);
            me.store.filterBy(
                function(obj){
                    var test=false;
                    //console.log(obj,indexes);
                    for(idx in indexes){
                        index=indexes[idx];
                        test=test || regexp.test(obj.data[index]);
                    //console.log('testing',index,obj.data[index],regexp.toString(),test);
                    }
                    
                    //console.log('Result:',test);
                    return test;
                });
            //me.store.filter(me.index,me.searchRegExp);
            // results found
            me.statusBar.setStatus({
                text: me.store.count() + ' matche(s) found.',
                iconCls: 'x-status-valid'
            });
                    }

        // no results found
        if (me.currentIndex === null) {
            me.getSelectionModel().deselectAll();
        }

        // force textfield focus
        me.textField.focus();
    },
       
 /**
 * Switch to case sensitive mode.
 * @private
 */    
    caseSensitiveToggle: function(checkbox, checked) {
        this.caseSensitive = checked;
        this.onTextFieldChange();
    },
    
    /**
 * Switch to regular expression mode
 * @private
 */
    regExpToggle: function(checkbox, checked) {
        this.regExpMode = checked;
        this.onTextFieldChange();
    }
});
