//---allow for dynamic loading
Ext.Loader.setConfig({
    enabled: true
}); 
//---Remove trail dc=.... from requests
Ext.Loader.setConfig({
    disableCaching : false
});
Ext.Ajax.setConfig({
    disableCaching : false
});
//---set ux path
Ext.Loader.setPath('Ext.ux', globals.base_url+'jscript/ext/src/ux');
//--- this is 4 CodeIgniter smart urls
Ext.apply(Ext.data.AjaxProxy.prototype.actionMethods, {
    read: 'POST'
});          

//----define cache object
pgridCache={};

Ext.override(Ext.grid.PropertyGrid, {
    getProperty: function(prop){
        return this.store.getProperty(prop).data.value;
    }
});
/*
 * Modified scrollIntoView function with non visible highlight
 */
Ext.override(Ext.dom.Element,{
    scrollIntoView: function(container, hscroll, animate) {
        var me = this,
        dom = me.dom,
        offsets = me.getOffsetsTo(container = Ext.getDom(container) || Ext.getBody().dom),
        // el's box
        left = offsets[0] + container.scrollLeft,
        top = offsets[1] + container.scrollTop,
        bottom = top + dom.offsetHeight,
        right = left + dom.offsetWidth,
        // ct's box
        ctClientHeight = container.clientHeight,
        ctScrollTop = parseInt(container.scrollTop, 10),
        ctScrollLeft = parseInt(container.scrollLeft, 10),
        ctBottom = ctScrollTop + ctClientHeight,
        ctRight = ctScrollLeft + container.clientWidth,
        newPos;

        // Highlight upon end of scroll
        if (animate) {
            animate = Ext.apply({
                listeners: {
                    afteranimate: function() {
                        me.scrollChildFly.attach(dom).highlight("ffff9c", {
                            attr: "boder-color", //can be any valid CSS property (attribute) that supports a color value
                            endColor: "ffffff",
                            easing: 'easeIn',
                            duration: 0
                        }    
                    );
                    }
                }
            }, animate);
        }

        if (dom.offsetHeight > ctClientHeight || top < ctScrollTop) {
            newPos = top;
        } else if (bottom > ctBottom) {
            newPos = bottom - ctClientHeight;
        }
        if (newPos != null) {
            me.scrollChildFly.attach(container).scrollTo('top', newPos, animate);
        }

        if (hscroll !== false) {
            newPos = null;
            if (dom.offsetWidth > container.clientWidth || left < ctScrollLeft) {
                newPos = left;
            } else if (right > ctRight) {
                newPos = right - container.clientWidth;
            }
            if (newPos != null) {
                me.scrollChildFly.attach(container).scrollTo('left', newPos, animate);
            }
        }
        return me;
    }
})