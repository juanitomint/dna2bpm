/*
Copyright(c) 2011 Sencha Inc.
licensing@sencha.com
*/
Ext.define("Ext.layout.container.boxOverflow.None",{
    alternateClassName:"Ext.layout.boxOverflow.None",
    constructor:function(b,a){
        this.layout=b;
        Ext.apply(this,a||{})
        },
    handleOverflow:Ext.emptyFn,
    clearOverflow:Ext.emptyFn,
    onRemove:Ext.emptyFn,
    getItem:function(a){
        return this.layout.owner.getComponent(a)
        },
    onRemove:Ext.emptyFn
    });
Ext.define("Ext.layout.Layout",{
    isLayout:true,
    initialized:false,
    statics:{
        create:function(b,c){
            var a;
            if(b instanceof Ext.layout.Layout){
                return Ext.createByAlias("layout."+b)
                }else{
                if(!b||typeof b==="string"){
                    a=b||c;
                    b={}
                }else{
                a=b.type
                }
                return Ext.createByAlias("layout."+a,b||{})
            }
        }
},
constructor:function(a){
    this.id=Ext.id(null,this.type+"-");
    Ext.apply(this,a)
    },
layout:function(){
    var a=this;
    a.layoutBusy=true;
    a.initLayout();
    if(a.beforeLayout.apply(a,arguments)!==false){
        a.layoutCancelled=false;
        a.onLayout.apply(a,arguments);
        a.childrenChanged=false;
        a.owner.needsLayout=false;
        a.layoutBusy=false;
        a.afterLayout.apply(a,arguments)
        }else{
        a.layoutCancelled=true
        }
        a.layoutBusy=false;
    a.doOwnerCtLayouts()
    },
beforeLayout:function(){
    this.renderChildren();
    return true
    },
renderChildren:function(){
    var a=this;
    a.renderItems(a.getLayoutItems(),a.getRenderTarget())
    },
renderItems:function(a,e){
    var d=a.length,b=0,c;
    for(;b<d;b++){
        c=a[b];
        if(c&&!c.rendered){
            this.renderItem(c,e,b)
            }else{
            if(!this.isValidParent(c,e,b)){
                this.moveItem(c,e,b)
                }
            }
    }
},
isValidParent:function(b,c,a){
    var d=b.el?b.el.dom:Ext.getDom(b);
    if(d&&c&&c.dom){
        if(Ext.isNumber(a)&&d!==c.dom.childNodes[a]){
            return false
            }
            return(d.parentNode==(c.dom||c))
        }
        return false
    },
renderItem:function(c,d,a){
    var b=this;
    if(!c.rendered){
        if(b.itemCls){
            c.addCls(b.itemCls)
            }
            if(b.owner.itemCls){
            c.addCls(b.owner.itemCls)
            }
            c.render(d,a);
        b.configureItem(c);
        b.childrenChanged=true
        }
    },
moveItem:function(b,c,a){
    c=c.dom||c;
    if(typeof a=="number"){
        a=c.childNodes[a]
        }
        c.insertBefore(b.el.dom,a||null);
    b.container=Ext.get(c);
    this.configureItem(b);
    this.childrenChanged=true
    },
initLayout:function(){
    if(!this.initialized&&!Ext.isEmpty(this.targetCls)){
        this.getTarget().addCls(this.targetCls)
        }
        this.initialized=true
    },
setOwner:function(a){
    this.owner=a
    },
getLayoutItems:function(){
    return[]
    },
configureItem:Ext.emptyFn,
onLayout:Ext.emptyFn,
afterLayout:Ext.emptyFn,
onRemove:Ext.emptyFn,
onDestroy:Ext.emptyFn,
doOwnerCtLayouts:Ext.emptyFn,
afterRemove:function(d){
    var c=this,b=d.el,a=c.owner;
    if(d.rendered){
        if(c.itemCls){
            b.removeCls(c.itemCls)
            }
            if(a.itemCls){
            b.removeCls(a.itemCls)
            }
        }
    delete d.layoutManagedWidth;
delete d.layoutManagedHeight
},
destroy:function(){
    if(!Ext.isEmpty(this.targetCls)){
        var a=this.getTarget();
        if(a){
            a.removeCls(this.targetCls)
            }
        }
    this.onDestroy()
}
});
Ext.define("Ext.util.Observable",{
    requires:["Ext.util.Event"],
    statics:{
        releaseCapture:function(a){
            a.fireEvent=this.prototype.fireEvent
            },
        capture:function(c,b,a){
            c.fireEvent=Ext.Function.createInterceptor(c.fireEvent,b,a)
            },
        observe:function(a,b){
            if(a){
                if(!a.isObservable){
                    Ext.applyIf(a,new this());
                    this.capture(a.prototype,a.fireEvent,a)
                    }
                    if(Ext.isObject(b)){
                    a.on(b)
                    }
                    return a
                }
            }
    },
isObservable:true,
constructor:function(a){
    var b=this;
    Ext.apply(b,a);
    if(b.listeners){
        b.on(b.listeners);
        delete b.listeners
        }
        b.events=b.events||{};
    
    if(b.bubbleEvents){
        b.enableBubble(b.bubbleEvents)
        }
    },
eventOptionsRe:/^(?:scope|delay|buffer|single|stopEvent|preventDefault|stopPropagation|normalized|args|delegate|element|vertical|horizontal)$/,
addManagedListener:function(h,d,f,e,c){
    var g=this,a=g.managedListeners=g.managedListeners||[],b;
    if(typeof d!=="string"){
        c=d;
        for(d in c){
            if(c.hasOwnProperty(d)){
                b=c[d];
                if(!g.eventOptionsRe.test(d)){
                    g.addManagedListener(h,d,b.fn||b,b.scope||c.scope,b.fn?b:c)
                    }
                }
        }
        }else{
    a.push({
        item:h,
        ename:d,
        fn:f,
        scope:e,
        options:c
    });
    h.on(d,f,e,c)
    }
},
removeManagedListener:function(h,c,f,j){
    var e=this,k,b,g,a,d;
    if(typeof c!=="string"){
        k=c;
        for(c in k){
            if(k.hasOwnProperty(c)){
                b=k[c];
                if(!e.eventOptionsRe.test(c)){
                    e.removeManagedListener(h,c,b.fn||b,b.scope||k.scope)
                    }
                }
        }
        }
    g=e.managedListeners?e.managedListeners.slice():[];
for(d=0,a=g.length;d<a;d++){
    e.removeManagedListenerItem(false,g[d],h,c,f,j)
    }
},
fireEvent:function(){
    var g=this,c=Ext.Array.toArray(arguments),d=c[0].toLowerCase(),b=true,f=g.events[d],a=g.eventQueue,e;
    if(g.eventsSuspended===true){
        if(a){
            a.push(c)
            }
        }else{
    if(f&&f!==true){
        if(f.bubble){
            if(f.fire.apply(f,c.slice(1))===false){
                return false
                }
                e=g.getBubbleTarget&&g.getBubbleTarget();
            if(e&&e.isObservable){
                if(!e.events[d]||e.events[d]===true||!e.events[d].bubble){
                    e.enableBubble(d)
                    }
                    return e.fireEvent.apply(e,c)
                }
            }else{
        c.shift();
        b=f.fire.apply(f,c)
        }
    }
}
return b
},
addListener:function(c,e,d,b){
    var g=this,a,f;
    if(typeof c!=="string"){
        b=c;
        for(c in b){
            if(b.hasOwnProperty(c)){
                a=b[c];
                if(!g.eventOptionsRe.test(c)){
                    g.addListener(c,a.fn||a,a.scope||b.scope,a.fn?a:b)
                    }
                }
        }
        }else{
    c=c.toLowerCase();
    g.events[c]=g.events[c]||true;
    f=g.events[c]||true;
    if(Ext.isBoolean(f)){
        g.events[c]=f=new Ext.util.Event(g,c)
        }
        f.addListener(e,d,Ext.isObject(b)?b:{})
    }
},
removeListener:function(c,e,d){
    var g=this,b,f,a;
    if(typeof c!=="string"){
        a=c;
        for(c in a){
            if(a.hasOwnProperty(c)){
                b=a[c];
                if(!g.eventOptionsRe.test(c)){
                    g.removeListener(c,b.fn||b,b.scope||a.scope)
                    }
                }
        }
        }else{
    c=c.toLowerCase();
    f=g.events[c];
    if(f&&f.isEvent){
        f.removeListener(e,d)
        }
    }
},
clearListeners:function(){
    var b=this.events,c,a;
    for(a in b){
        if(b.hasOwnProperty(a)){
            c=b[a];
            if(c.isEvent){
                c.clearListeners()
                }
            }
    }
    this.clearManagedListeners()
},
clearManagedListeners:function(){
    var b=this.managedListeners||[],c=0,a=b.length;
    for(;c<a;c++){
        this.removeManagedListenerItem(true,b[c])
        }
        this.managedListeners=[]
    },
removeManagedListenerItem:function(b,a,f,c,e,d){
    if(b||(a.item===f&&a.ename===c&&(!e||a.fn===e)&&(!d||a.scope===d))){
        a.item.un(a.ename,a.fn,a.scope);
        if(!b){
            Ext.Array.remove(this.managedListeners,a)
            }
        }
},
addEvents:function(e){
    var d=this,b,a,c;
    d.events=d.events||{};
    
    if(Ext.isString(e)){
        b=arguments;
        c=b.length;
        while(c--){
            d.events[b[c]]=d.events[b[c]]||true
            }
        }else{
    Ext.applyIf(d.events,e)
    }
},
hasListener:function(a){
    var b=this.events[a.toLowerCase()];
    return b&&b.isEvent===true&&b.listeners.length>0
    },
suspendEvents:function(a){
    this.eventsSuspended=true;
    if(a&&!this.eventQueue){
        this.eventQueue=[]
        }
    },
resumeEvents:function(){
    var a=this,b=a.eventQueue||[];
    a.eventsSuspended=false;
    delete a.eventQueue;
    Ext.each(b,function(c){
        a.fireEvent.apply(a,c)
        })
    },
relayEvents:function(c,e,h){
    h=h||"";
    var g=this,a=e.length,d=0,f,b;
    for(;d<a;d++){
        f=e[d].substr(h.length);
        b=h+f;
        g.events[b]=g.events[b]||true;
        c.on(f,g.createRelayer(b))
        }
    },
createRelayer:function(a){
    var b=this;
    return function(){
        return b.fireEvent.apply(b,[a].concat(Array.prototype.slice.call(arguments,0,-1)))
        }
    },
enableBubble:function(a){
    var b=this;
    if(!Ext.isEmpty(a)){
        a=Ext.isArray(a)?a:Ext.Array.toArray(arguments);
        Ext.each(a,function(c){
            c=c.toLowerCase();
            var d=b.events[c]||true;
            if(Ext.isBoolean(d)){
                d=new Ext.util.Event(b,c);
                b.events[c]=d
                }
                d.bubble=true
            })
        }
    }
},function(){
    this.createAlias({
        on:"addListener",
        un:"removeListener",
        mon:"addManagedListener",
        mun:"removeManagedListener"
    });
    this.observeClass=this.observe;
    Ext.apply(Ext.util.Observable.prototype,function(){
        function a(i){
            var h=(this.methodEvents=this.methodEvents||{})[i],d,c,f,g=this;
            if(!h){
                this.methodEvents[i]=h={};
                
                h.originalFn=this[i];
                h.methodName=i;
                h.before=[];
                h.after=[];
                var b=function(k,j,e){
                    if((c=k.apply(j||g,e))!==undefined){
                        if(typeof c=="object"){
                            if(c.returnValue!==undefined){
                                d=c.returnValue
                                }else{
                                d=c
                                }
                                f=!!c.cancel
                            }else{
                            if(c===false){
                                f=true
                                }else{
                                d=c
                                }
                            }
                    }
            };
        
    this[i]=function(){
        var k=Array.prototype.slice.call(arguments,0),j,l,e;
        d=c=undefined;
        f=false;
        for(l=0,e=h.before.length;l<e;l++){
            j=h.before[l];
            b(j.fn,j.scope,k);
            if(f){
                return d
                }
            }
        if((c=h.originalFn.apply(g,k))!==undefined){
        d=c
        }
        for(l=0,e=h.after.length;l<e;l++){
        j=h.after[l];
        b(j.fn,j.scope,k);
        if(f){
            return d
            }
        }
    return d
    }
}
return h
}
return{
    beforeMethod:function(d,c,b){
        a.call(this,d).before.push({
            fn:c,
            scope:b
        })
        },
    afterMethod:function(d,c,b){
        a.call(this,d).after.push({
            fn:c,
            scope:b
        })
        },
    removeMethodListener:function(h,f,d){
        var g=this.getMethodEvent(h),c,b;
        for(c=0,b=g.before.length;c<b;c++){
            if(g.before[c].fn==f&&g.before[c].scope==d){
                Ext.Array.erase(g.before,c,1);
                return
            }
        }
        for(c=0,b=g.after.length;c<b;c++){
        if(g.after[c].fn==f&&g.after[c].scope==d){
            Ext.Array.erase(g.after,c,1);
            return
        }
    }
    },
toggleEventLogging:function(b){
    Ext.util.Observable[b?"capture":"releaseCapture"](this,function(c){
        if(Ext.isDefined(Ext.global.console)){
            Ext.global.console.log(c,arguments)
            }
        })
}
}
}())
});
Ext.define("Ext.util.ClickRepeater",{
    extend:"Ext.util.Observable",
    constructor:function(b,a){
        this.el=Ext.get(b);
        this.el.unselectable();
        Ext.apply(this,a);
        this.addEvents("mousedown","click","mouseup");
        if(!this.disabled){
            this.disabled=true;
            this.enable()
            }
            if(this.handler){
            this.on("click",this.handler,this.scope||this)
            }
            this.callParent()
        },
    interval:20,
    delay:250,
    preventDefault:true,
    stopDefault:false,
    timer:0,
    enable:function(){
        if(this.disabled){
            this.el.on("mousedown",this.handleMouseDown,this);
            if(Ext.isIE){
                this.el.on("dblclick",this.handleDblClick,this)
                }
                if(this.preventDefault||this.stopDefault){
                this.el.on("click",this.eventOptions,this)
                }
            }
        this.disabled=false
    },
disable:function(a){
    if(a||!this.disabled){
        clearTimeout(this.timer);
        if(this.pressedCls){
            this.el.removeCls(this.pressedCls)
            }
            Ext.getDoc().un("mouseup",this.handleMouseUp,this);
        this.el.removeAllListeners()
        }
        this.disabled=true
    },
setDisabled:function(a){
    this[a?"disable":"enable"]()
    },
eventOptions:function(a){
    if(this.preventDefault){
        a.preventDefault()
        }
        if(this.stopDefault){
        a.stopEvent()
        }
    },
destroy:function(){
    this.disable(true);
    Ext.destroy(this.el);
    this.clearListeners()
    },
handleDblClick:function(a){
    clearTimeout(this.timer);
    this.el.blur();
    this.fireEvent("mousedown",this,a);
    this.fireEvent("click",this,a)
    },
handleMouseDown:function(a){
    clearTimeout(this.timer);
    this.el.blur();
    if(this.pressedCls){
        this.el.addCls(this.pressedCls)
        }
        this.mousedownTime=new Date();
    Ext.getDoc().on("mouseup",this.handleMouseUp,this);
    this.el.on("mouseout",this.handleMouseOut,this);
    this.fireEvent("mousedown",this,a);
    this.fireEvent("click",this,a);
    if(this.accelerate){
        this.delay=400
        }
        a=new Ext.EventObjectImpl(a);
    this.timer=Ext.defer(this.click,this.delay||this.interval,this,[a])
    },
click:function(a){
    this.fireEvent("click",this,a);
    this.timer=Ext.defer(this.click,this.accelerate?this.easeOutExpo(Ext.Date.getElapsed(this.mousedownTime),400,-390,12000):this.interval,this,[a])
    },
easeOutExpo:function(e,a,g,f){
    return(e==f)?a+g:g*(-Math.pow(2,-10*e/f)+1)+a
    },
handleMouseOut:function(){
    clearTimeout(this.timer);
    if(this.pressedCls){
        this.el.removeCls(this.pressedCls)
        }
        this.el.on("mouseover",this.handleMouseReturn,this)
    },
handleMouseReturn:function(){
    this.el.un("mouseover",this.handleMouseReturn,this);
    if(this.pressedCls){
        this.el.addCls(this.pressedCls)
        }
        this.click()
    },
handleMouseUp:function(a){
    clearTimeout(this.timer);
    this.el.un("mouseover",this.handleMouseReturn,this);
    this.el.un("mouseout",this.handleMouseOut,this);
    Ext.getDoc().un("mouseup",this.handleMouseUp,this);
    if(this.pressedCls){
        this.el.removeCls(this.pressedCls)
        }
        this.fireEvent("mouseup",this,a)
    }
});
Ext.define("Ext.fx.target.Target",{
    isAnimTarget:true,
    constructor:function(a){
        this.target=a;
        this.id=this.getId()
        },
    getId:function(){
        return this.target.id
        }
    });
Ext.define("Ext.fx.CubicBezier",{
    singleton:true,
    cubicBezierAtTime:function(n,d,b,m,l,h){
        var i=3*d,k=3*(m-d)-i,a=1-i-k,g=3*b,j=3*(l-b)-g,o=1-g-j;
        function f(p){
            return((a*p+k)*p+i)*p
            }
            function c(p,r){
            var q=e(p,r);
            return((o*q+j)*q+g)*q
            }
            function e(p,w){
            var v,u,s,q,t,r;
            for(s=p,r=0;r<8;r++){
                q=f(s)-p;
                if(Math.abs(q)<w){
                    return s
                    }
                    t=(3*a*s+2*k)*s+i;
                if(Math.abs(t)<0.000001){
                    break
                }
                s=s-q/t
                }
                v=0;
            u=1;
            s=p;
            if(s<v){
                return v
                }
                if(s>u){
                return u
                }while(v<u){
                q=f(s);
                if(Math.abs(q-p)<w){
                    return s
                    }
                    if(p>q){
                    v=s
                    }else{
                    u=s
                    }
                    s=(u-v)/2+v
                }
                return s
            }
            return c(n,1/(200*h))
        },
    cubicBezier:function(b,e,a,c){
        var d=function(f){
            return Ext.fx.CubicBezier.cubicBezierAtTime(f,b,e,a,c,1)
            };
            
        d.toCSS3=function(){
            return"cubic-bezier("+[b,e,a,c].join(",")+")"
            };
            
        d.reverse=function(){
            return Ext.fx.CubicBezier.cubicBezier(1-a,1-c,1-b,1-e)
            };
            
        return d
        }
    });
Ext.define("Ext.util.TextMetrics",{
    statics:{
        shared:null,
        measure:function(a,d,e){
            var b=this,c=b.shared;
            if(!c){
                c=b.shared=new b(a,e)
                }
                c.bind(a);
            c.setFixedWidth(e||"auto");
            return c.getSize(d)
            },
        destroy:function(){
            var a=this;
            Ext.destroy(a.shared);
            a.shared=null
            }
        },
constructor:function(a,c){
    var b=this.measure=Ext.getBody().createChild({
        cls:"x-textmetrics"
    });
    this.el=Ext.get(a);
    b.position("absolute");
    b.setLeftTop(-1000,-1000);
    b.hide();
    if(c){
        b.setWidth(c)
        }
    },
getSize:function(c){
    var b=this.measure,a;
    b.update(c);
    a=b.getSize();
    b.update("");
    return a
    },
bind:function(a){
    var b=this;
    b.el=Ext.get(a);
    b.measure.setStyle(b.el.getStyles("font-size","font-style","font-weight","font-family","line-height","text-transform","letter-spacing"))
    },
setFixedWidth:function(a){
    this.measure.setWidth(a)
    },
getWidth:function(a){
    this.measure.dom.style.width="auto";
    return this.getSize(a).width
    },
getHeight:function(a){
    return this.getSize(a).height
    },
destroy:function(){
    var a=this;
    a.measure.remove();
    delete a.el;
    delete a.measure
    }
},function(){
    Ext.core.Element.addMethods({
        getTextWidth:function(c,b,a){
            return Ext.Number.constrain(Ext.util.TextMetrics.measure(this.dom,Ext.value(c,this.dom.innerHTML,true)).width,b||0,a||1000000)
            }
        })
});
Ext.define("Ext.util.KeyMap",{
    alternateClassName:"Ext.KeyMap",
    constructor:function(b,d,a){
        var c=this;
        Ext.apply(c,{
            el:Ext.get(b),
            eventName:a||c.eventName,
            bindings:[]
        });
        if(d){
            c.addBinding(d)
            }
            c.enable()
        },
    eventName:"keydown",
    addBinding:function(g){
        if(Ext.isArray(g)){
            Ext.each(g,this.addBinding,this);
            return
        }
        var f=g.key,h=false,d,e,b,c,a;
        if(Ext.isString(f)){
            e=[];
            b=f.toLowerCase();
            for(c=0,a=b.length;c<a;++c){
                e.push(b.charCodeAt(c))
                }
                f=e;
            h=true
            }
            if(!Ext.isArray(f)){
            f=[f]
            }
            if(!h){
            for(c=0,a=f.length;c<a;++c){
                d=f[c];
                if(Ext.isString(d)){
                    f[c]=d.toLowerCase().charCodeAt(0)
                    }
                }
            }
        this.bindings.push(Ext.apply({
    keyCode:f
},g))
},
handleKeyDown:function(c){
    if(this.enabled){
        var d=this.bindings,b=0,a=d.length;
        c=this.processEvent(c);
        for(;b<a;++b){
            this.processBinding(d[b],c)
            }
        }
    },
processEvent:function(a){
    return a
    },
processBinding:function(f,a){
    if(this.checkModifiers(f,a)){
        var g=a.getKey(),j=f.fn||f.handler,k=f.scope||this,h=f.keyCode,b=f.defaultEventAction,c,e,d=new Ext.EventObjectImpl(a);
        for(c=0,e=h.length;c<e;++c){
            if(g===h[c]){
                if(j.call(k,g,a)!==true&&b){
                    d[b]()
                    }
                    break
            }
        }
        }
},
checkModifiers:function(h,f){
    var d=["shift","ctrl","alt"],c=0,a=d.length,g,b;
    for(;c<a;++c){
        b=d[c];
        g=h[b];
        if(!(g===undefined||(g===f[b+"Key"]))){
            return false
            }
        }
    return true
},
on:function(b,d,c){
    var g,a,e,f;
    if(Ext.isObject(b)&&!Ext.isArray(b)){
        g=b.key;
        a=b.shift;
        e=b.ctrl;
        f=b.alt
        }else{
        g=b
        }
        this.addBinding({
        key:g,
        shift:a,
        ctrl:e,
        alt:f,
        fn:d,
        scope:c
    })
    },
isEnabled:function(){
    return this.enabled
    },
enable:function(){
    if(!this.enabled){
        this.el.on(this.eventName,this.handleKeyDown,this);
        this.enabled=true
        }
    },
disable:function(){
    if(this.enabled){
        this.el.removeListener(this.eventName,this.handleKeyDown,this);
        this.enabled=false
        }
    },
setDisabled:function(a){
    if(a){
        this.disable()
        }else{
        this.enable()
        }
    },
destroy:function(b){
    var a=this;
    a.bindings=[];
    a.disable();
    if(b===true){
        a.el.remove()
        }
        delete a.el
    }
});
Ext.define("Ext.util.Floating",{
    uses:["Ext.Layer","Ext.window.Window"],
    focusOnToFront:true,
    shadow:"sides",
    constructor:function(a){
        this.floating=true;
        this.el=Ext.create("Ext.Layer",Ext.apply({},a,{
            hideMode:this.hideMode,
            hidden:this.hidden,
            shadow:Ext.isDefined(this.shadow)?this.shadow:"sides",
            shadowOffset:this.shadowOffset,
            constrain:false,
            shim:this.shim===false?false:undefined
            }),this.el)
        },
    onFloatRender:function(){
        var a=this;
        a.zIndexParent=a.getZIndexParent();
        a.setFloatParent(a.ownerCt);
        delete a.ownerCt;
        if(a.zIndexParent){
            a.zIndexParent.registerFloatingItem(a)
            }else{
            Ext.WindowManager.register(a)
            }
        },
setFloatParent:function(b){
    var a=this;
    if(a.floatParent){
        a.mun(a.floatParent,{
            hide:a.onFloatParentHide,
            show:a.onFloatParentShow,
            scope:a
        })
        }
        a.floatParent=b;
    if(b){
        a.mon(a.floatParent,{
            hide:a.onFloatParentHide,
            show:a.onFloatParentShow,
            scope:a
        })
        }
        if((a.constrain||a.constrainHeader)&&!a.constrainTo){
        a.constrainTo=b?b.getTargetEl():a.container
        }
    },
onFloatParentHide:function(){
    if(this.hideOnParentHide!==false){
        this.showOnParentShow=this.isVisible();
        this.hide()
        }
    },
onFloatParentShow:function(){
    if(this.showOnParentShow){
        delete this.showOnParentShow;
        this.show()
        }
    },
getZIndexParent:function(){
    var a=this.ownerCt,b;
    if(a){
        while(a){
            b=a;
            a=a.ownerCt
            }
            if(b.floating){
            return b
            }
        }
},
setZIndex:function(a){
    var b=this;
    this.el.setZIndex(a);
    a+=10;
    if(b.floatingItems){
        a=Math.floor(b.floatingItems.setBase(a)/100)*100+10000
        }
        return a
    },
doConstrain:function(b){
    var c=this,a=c.getConstrainVector(b),d;
    if(a){
        d=c.getPosition();
        d[0]+=a[0];
        d[1]+=a[1];
        c.setPosition(d)
        }
    },
getConstrainVector:function(b){
    var c=this,a;
    if(c.constrain||c.constrainHeader){
        a=c.constrainHeader?c.header.el:c.el;
        b=b||(c.floatParent&&c.floatParent.getTargetEl())||c.container;
        return a.getConstrainVector(b)
        }
    },
alignTo:function(b,a,c){
    if(b.isComponent){
        b=b.getEl()
        }
        var d=this.el.getAlignToXY(b,a,c);
    this.setPagePosition(d);
    return this
    },
toFront:function(b){
    var a=this;
    if(a.zIndexParent){
        a.zIndexParent.toFront(true)
        }
        if(a.zIndexManager.bringToFront(a)){
        if(!Ext.isDefined(b)){
            b=!a.focusOnToFront
            }
            if(!b){
            a.focus(false,true)
            }
        }
    return a
},
setActive:function(a,b){
    if(a){
        if((this instanceof Ext.window.Window)&&!this.maximized){
            this.el.enableShadow(true)
            }
            this.fireEvent("activate",this)
        }else{
        if((this instanceof Ext.window.Window)&&(b instanceof Ext.window.Window)){
            this.el.disableShadow()
            }
            this.fireEvent("deactivate",this)
        }
    },
toBack:function(){
    this.zIndexManager.sendToBack(this);
    return this
    },
center:function(){
    var a=this.el.getAlignToXY(this.container,"c-c");
    this.setPagePosition(a);
    return this
    },
syncShadow:function(){
    if(this.floating){
        this.el.sync(true)
        }
    },
fitContainer:function(){
    var c=this.floatParent,a=c?c.getTargetEl():this.container,b=a.getViewSize(false);
    this.setSize(b)
    }
});
Ext.define("Ext.Template",{
    requires:["Ext.core.DomHelper","Ext.util.Format"],
    statics:{
        from:function(b,a){
            b=Ext.getDom(b);
            return new this(b.value||b.innerHTML,a||"")
            }
        },
constructor:function(d){
    var f=this,b=arguments,a=[],c=0,e=b.length,g;
    f.initialConfig={};
    
    if(e>1){
        for(;c<e;c++){
            g=b[c];
            if(typeof g=="object"){
                Ext.apply(f.initialConfig,g);
                Ext.apply(f,g)
                }else{
                a.push(g)
                }
            }
        d=a.join("")
    }else{
    if(Ext.isArray(d)){
        a.push(d.join(""))
        }else{
        a.push(d)
        }
    }
f.html=a.join("");
    if(f.compiled){
    f.compile()
    }
},
isTemplate:true,
disableFormats:false,
re:/\{([\w\-]+)(?:\:([\w\.]*)(?:\((.*?)?\))?)?\}/g,
applyTemplate:function(a){
    var f=this,c=f.disableFormats!==true,e=Ext.util.Format,b=f;
    if(f.compiled){
        return f.compiled(a)
        }
        function d(g,i,j,h){
        if(j&&c){
            if(h){
                h=[a[i]].concat(Ext.functionFactory("return ["+h+"];")())
                }else{
                h=[a[i]]
                }
                if(j.substr(0,5)=="this."){
                return b[j.substr(5)].apply(b,h)
                }else{
                return e[j].apply(e,h)
                }
            }else{
        return a[i]!==undefined?a[i]:""
        }
    }
return f.html.replace(f.re,d)
},
set:function(a,c){
    var b=this;
    b.html=a;
    b.compiled=null;
    return c?b.compile():b
    },
compileARe:/\\/g,
compileBRe:/(\r\n|\n)/g,
compileCRe:/'/g,
compile:function(){
    var me=this,fm=Ext.util.Format,useFormat=me.disableFormats!==true,body,bodyReturn;
    function fn(m,name,format,args){
        if(format&&useFormat){
            args=args?","+args:"";
            if(format.substr(0,5)!="this."){
                format="fm."+format+"("
                }else{
                format="this."+format.substr(5)+"("
                }
            }else{
        args="";
        format="(values['"+name+"'] == undefined ? '' : "
        }
        return"',"+format+"values['"+name+"']"+args+") ,'"
    }
    bodyReturn=me.html.replace(me.compileARe,"\\\\").replace(me.compileBRe,"\\n").replace(me.compileCRe,"\\'").replace(me.re,fn);
body="this.compiled = function(values){ return ['"+bodyReturn+"'].join('');};";
eval(body);
return me
},
insertFirst:function(b,a,c){
    return this.doInsert("afterBegin",b,a,c)
    },
insertBefore:function(b,a,c){
    return this.doInsert("beforeBegin",b,a,c)
    },
insertAfter:function(b,a,c){
    return this.doInsert("afterEnd",b,a,c)
    },
append:function(b,a,c){
    return this.doInsert("beforeEnd",b,a,c)
    },
doInsert:function(c,e,b,a){
    e=Ext.getDom(e);
    var d=Ext.core.DomHelper.insertHtml(c,e,this.applyTemplate(b));
    return a?Ext.get(d,true):d
    },
overwrite:function(b,a,c){
    b=Ext.getDom(b);
    b.innerHTML=this.applyTemplate(a);
    return c?Ext.get(b.firstChild,true):b.firstChild
    }
},function(){
    this.createAlias("apply","applyTemplate")
    });
Ext.define("Ext.util.Memento",function(){
    function d(g,f,h){
        g[h]=f[h]
        }
        function c(g,f,h){
        delete g[h]
    }
    function e(h,g,i){
        var f=h[i];
        if(f||h.hasOwnProperty(i)){
            a(g,i,f)
            }
        }
    function a(g,h,f){
    if(Ext.isDefined(f)){
        g[h]=f
        }else{
        delete g[h]
    }
}
function b(f,i,h,g){
    if(i){
        if(Ext.isArray(g)){
            Ext.each(g,function(j){
                f(i,h,j)
                })
            }else{
            f(i,h,g)
            }
        }
}
return{
    data:null,
    target:null,
    constructor:function(g,f){
        if(g){
            this.target=g;
            if(f){
                this.capture(f)
                }
            }
    },
capture:function(f,g){
    b(d,this.data||(this.data={}),g||this.target,f)
    },
remove:function(f){
    b(c,this.data,null,f)
    },
restore:function(g,f,h){
    b(e,this.data,h||this.target,g);
    if(f!==false){
        this.remove(g)
        }
    },
restoreAll:function(f,i){
    var h=this,g=i||this.target;
    Ext.Object.each(h.data,function(k,j){
        a(g,k,j)
        });
    if(f!==false){
        delete h.data
        }
    }
}
}());
Ext.define("Ext.dd.DragTracker",{
    uses:["Ext.util.Region"],
    mixins:{
        observable:"Ext.util.Observable"
    },
    active:false,
    trackOver:false,
    tolerance:5,
    autoStart:false,
    constructor:function(a){
        Ext.apply(this,a);
        this.addEvents("mouseover","mouseout","mousedown","mouseup","mousemove","beforedragstart","dragstart","dragend","drag");
        this.dragRegion=Ext.create("Ext.util.Region",0,0,0,0);
        if(this.el){
            this.initEl(this.el)
            }
            this.mixins.observable.constructor.call(this);
        if(this.disabled){
            this.disable()
            }
        },
initEl:function(a){
    this.el=Ext.get(a);
    this.handle=Ext.get(this.delegate);
    this.delegate=this.handle?undefined:this.delegate;
    if(!this.handle){
        this.handle=this.el
        }
        this.mon(this.handle,{
        mousedown:this.onMouseDown,
        delegate:this.delegate,
        scope:this
    });
    if(this.trackOver||this.overCls){
        this.mon(this.handle,{
            mouseover:this.onMouseOver,
            mouseout:this.onMouseOut,
            delegate:this.delegate,
            scope:this
        })
        }
    },
disable:function(){
    this.disabled=true
    },
enable:function(){
    this.disabled=false
    },
destroy:function(){
    this.clearListeners();
    delete this.el
    },
onMouseOver:function(c,b){
    var a=this;
    if(!a.disabled){
        if(Ext.EventManager.contains(c)||a.delegate){
            a.mouseIsOut=false;
            if(a.overCls){
                a.el.addCls(a.overCls)
                }
                a.fireEvent("mouseover",a,c,a.delegate?c.getTarget(a.delegate,b):a.handle)
            }
        }
},
onMouseOut:function(a){
    if(this.mouseIsDown){
        this.mouseIsOut=true
        }else{
        if(this.overCls){
            this.el.removeCls(this.overCls)
            }
            this.fireEvent("mouseout",this,a)
        }
    },
onMouseDown:function(b,a){
    if(this.disabled||b.dragTracked){
        return
    }
    this.dragTarget=this.delegate?a:this.handle.dom;
    this.startXY=this.lastXY=b.getXY();
    this.startRegion=Ext.fly(this.dragTarget).getRegion();
    if(this.fireEvent("mousedown",this,b)===false||this.fireEvent("beforedragstart",this,b)===false||this.onBeforeStart(b)===false){
        return
    }
    this.mouseIsDown=true;
    b.dragTracked=true;
    if(this.preventDefault!==false){
        b.preventDefault()
        }
        Ext.getDoc().on({
        scope:this,
        mouseup:this.onMouseUp,
        mousemove:this.onMouseMove,
        selectstart:this.stopSelect
        });
    if(this.autoStart){
        this.timer=Ext.defer(this.triggerStart,this.autoStart===true?1000:this.autoStart,this,[b])
        }
    },
onMouseMove:function(d,c){
    if(this.active&&Ext.isIE&&!d.browserEvent.button){
        d.preventDefault();
        this.onMouseUp(d);
        return
    }
    d.preventDefault();
    var b=d.getXY(),a=this.startXY;
    this.lastXY=b;
    if(!this.active){
        if(Math.max(Math.abs(a[0]-b[0]),Math.abs(a[1]-b[1]))>this.tolerance){
            this.triggerStart(d)
            }else{
            return
        }
    }
    if(this.fireEvent("mousemove",this,d)===false){
    this.onMouseUp(d)
    }else{
    this.onDrag(d);
    this.fireEvent("drag",this,d)
    }
},
onMouseUp:function(a){
    this.mouseIsDown=false;
    if(this.mouseIsOut){
        this.mouseIsOut=false;
        this.onMouseOut(a)
        }
        a.preventDefault();
    this.fireEvent("mouseup",this,a);
    this.endDrag(a)
    },
endDrag:function(c){
    var b=Ext.getDoc(),a=this.active;
    b.un("mousemove",this.onMouseMove,this);
    b.un("mouseup",this.onMouseUp,this);
    b.un("selectstart",this.stopSelect,this);
    this.clearStart();
    this.active=false;
    if(a){
        this.onEnd(c);
        this.fireEvent("dragend",this,c)
        }
        delete this._constrainRegion;
    delete Ext.EventObject.dragTracked
    },
triggerStart:function(a){
    this.clearStart();
    this.active=true;
    this.onStart(a);
    this.fireEvent("dragstart",this,a)
    },
clearStart:function(){
    if(this.timer){
        clearTimeout(this.timer);
        delete this.timer
        }
    },
stopSelect:function(a){
    a.stopEvent();
    return false
    },
onBeforeStart:function(a){},
onStart:function(a){},
onDrag:function(a){},
onEnd:function(a){},
getDragTarget:function(){
    return this.dragTarget
    },
getDragCt:function(){
    return this.el
    },
getConstrainRegion:function(){
    if(this.constrainTo){
        if(this.constrainTo instanceof Ext.util.Region){
            return this.constrainTo
            }
            if(!this._constrainRegion){
            this._constrainRegion=Ext.fly(this.constrainTo).getViewRegion()
            }
        }else{
    if(!this._constrainRegion){
        this._constrainRegion=this.getDragCt().getViewRegion()
        }
    }
return this._constrainRegion
},
getXY:function(a){
    return a?this.constrainModes[a](this,this.lastXY):this.lastXY
    },
getOffset:function(c){
    var b=this.getXY(c),a=this.startXY;
    return[b[0]-a[0],b[1]-a[1]]
    },
constrainModes:{
    point:function(b,d){
        var c=b.dragRegion,a=b.getConstrainRegion();
        if(!a){
            return d
            }
            c.x=c.left=c[0]=c.right=d[0];
        c.y=c.top=c[1]=c.bottom=d[1];
        c.constrainTo(a);
        return[c.left,c.top]
        },
    dragTarget:function(c,f){
        var b=c.startXY,e=c.startRegion.copy(),a=c.getConstrainRegion(),d;
        if(!a){
            return f
            }
            e.translateBy(f[0]-b[0],f[1]-b[1]);
        if(e.right>a.right){
            f[0]+=d=(a.right-e.right);
            e.left+=d
            }
            if(e.left<a.left){
            f[0]+=(a.left-e.left)
            }
            if(e.bottom>a.bottom){
            f[1]+=d=(a.bottom-e.bottom);
            e.top+=d
            }
            if(e.top<a.top){
            f[1]+=(a.top-e.top)
            }
            return f
        }
    }
});
Ext.define("Ext.util.Offset",{
    statics:{
        fromObject:function(a){
            return new this(a.x,a.y)
            }
        },
constructor:function(a,b){
    this.x=(a!=null&&!isNaN(a))?a:0;
    this.y=(b!=null&&!isNaN(b))?b:0;
    return this
    },
copy:function(){
    return new Ext.util.Offset(this.x,this.y)
    },
copyFrom:function(a){
    this.x=a.x;
    this.y=a.y
    },
toString:function(){
    return"Offset["+this.x+","+this.y+"]"
    },
equals:function(a){
    return(this.x==a.x&&this.y==a.y)
    },
round:function(b){
    if(!isNaN(b)){
        var a=Math.pow(10,b);
        this.x=Math.round(this.x*a)/a;
        this.y=Math.round(this.y*a)/a
        }else{
        this.x=Math.round(this.x);
        this.y=Math.round(this.y)
        }
    },
isZero:function(){
    return this.x==0&&this.y==0
    }
});
Ext.define("Ext.layout.component.Component",{
    extend:"Ext.layout.Layout",
    type:"component",
    monitorChildren:true,
    initLayout:function(){
        var c=this,a=c.owner,b=a.el;
        if(!c.initialized){
            if(a.frameSize){
                c.frameSize=a.frameSize
                }else{
                a.frameSize=c.frameSize={
                    top:0,
                    left:0,
                    bottom:0,
                    right:0
                }
            }
        }
    c.callParent(arguments)
    },
beforeLayout:function(b,j,k,h){
    this.callParent(arguments);
    var g=this,c=g.owner,d=c.ownerCt,f=c.layout,e=c.isVisible(true),a=c.el.child,i;
    g.previousComponentSize=g.lastComponentSize;
    if(!k&&((!Ext.isNumber(b)&&c.isFixedWidth())||(!Ext.isNumber(j)&&c.isFixedHeight()))&&h!==d){
        g.doContainerLayout();
        return false
        }
        if(!e&&(c.hiddenAncestor||c.floating)){
        if(c.hiddenAncestor){
            i=c.hiddenAncestor.layoutOnShow;
            i.remove(c);
            i.add(c)
            }
            c.needsLayout={
            width:b,
            height:j,
            isSetSize:false
        }
    }
    if(e&&this.needsLayout(b,j)){
    return c.beforeComponentLayout(b,j,k,h)
    }else{
    return false
    }
},
needsLayout:function(d,a){
    var e=this,c,b;
    e.lastComponentSize=e.lastComponentSize||{
        width:-Infinity,
        height:-Infinity
        };
        
    c=!Ext.isDefined(d)||e.lastComponentSize.width!==d;
    b=!Ext.isDefined(a)||e.lastComponentSize.height!==a;
    return !e.isSizing&&(e.childrenChanged||c||b)
    },
setElementSize:function(c,b,a){
    if(b!==undefined&&a!==undefined){
        c.setSize(b,a)
        }else{
        if(a!==undefined){
            c.setHeight(a)
            }else{
            if(b!==undefined){
                c.setWidth(b)
                }
            }
    }
},
getTarget:function(){
    return this.owner.el
    },
getRenderTarget:function(){
    return this.owner.el
    },
setTargetSize:function(d,a){
    var e=this;
    e.setElementSize(e.owner.el,d,a);
    if(e.owner.frameBody){
        var g=e.getTargetInfo(),f=g.padding,c=g.border,b=e.frameSize;
        e.setElementSize(e.owner.frameBody,Ext.isNumber(d)?(d-b.left-b.right-f.left-f.right-c.left-c.right):d,Ext.isNumber(a)?(a-b.top-b.bottom-f.top-f.bottom-c.top-c.bottom):a)
        }
        e.autoSized={
        width:!Ext.isNumber(d),
        height:!Ext.isNumber(a)
        };
        
    e.lastComponentSize={
        width:d,
        height:a
    }
},
getTargetInfo:function(){
    if(!this.targetInfo){
        var b=this.getTarget(),a=this.owner.getTargetEl();
        this.targetInfo={
            padding:{
                top:b.getPadding("t"),
                right:b.getPadding("r"),
                bottom:b.getPadding("b"),
                left:b.getPadding("l")
                },
            border:{
                top:b.getBorderWidth("t"),
                right:b.getBorderWidth("r"),
                bottom:b.getBorderWidth("b"),
                left:b.getBorderWidth("l")
                },
            bodyMargin:{
                top:a.getMargin("t"),
                right:a.getMargin("r"),
                bottom:a.getMargin("b"),
                left:a.getMargin("l")
                }
            }
    }
return this.targetInfo
},
doOwnerCtLayouts:function(){
    var b=this.owner,e=b.ownerCt,c,h,d=this.lastComponentSize,g=this.previousComponentSize,a=(g&&d&&d.width)?d.width!==g.width:true,f=(g&&d&&d.height)?d.height!==g.height:true;
    if(!e||(!a&&!f)){
        return
    }
    c=e.componentLayout;
    h=e.layout;
    if(!b.floating&&c&&c.monitorChildren&&!c.layoutBusy){
        if(!e.suspendLayout&&h&&!h.layoutBusy){
            if(((a&&!e.isFixedWidth())||(f&&!e.isFixedHeight()))){
                this.isSizing=true;
                e.doComponentLayout();
                this.isSizing=false
                }else{
                if(h.bindToOwnerCtContainer===true){
                    h.layout()
                    }
                }
        }
}
},
doContainerLayout:function(){
    var e=this,a=e.owner,c=a.ownerCt,d=a.layout,b;
    if(!a.suspendLayout&&d&&d.isLayout&&!d.layoutBusy&&!d.isAutoDock){
        d.layout()
        }
        if(c&&c.componentLayout){
        b=c.componentLayout;
        if(!a.floating&&b.monitorChildren&&!b.layoutBusy){
            b.childrenChanged=true
            }
        }
},
afterLayout:function(c,a,b,d){
    this.doContainerLayout();
    this.owner.afterComponentLayout(c,a,b,d)
    }
});
Ext.define("Ext.chart.Callout",{
    constructor:function(a){
        if(a.callouts){
            a.callouts.styles=Ext.applyIf(a.callouts.styles||{},{
                color:"#000",
                font:"11px Helvetica, sans-serif"
            });
            this.callouts=Ext.apply(this.callouts||{},a.callouts);
            this.calloutsArray=[]
            }
        },
renderCallouts:function(){
    if(!this.callouts){
        return
    }
    var u=this,l=u.items,a=u.chart.animate,t=u.callouts,g=t.styles,e=u.calloutsArray,b=u.chart.store,r=b.getCount(),d=l.length/r,k=[],q,c,o,m;
    for(q=0,c=0;q<r;q++){
        for(o=0;o<d;o++){
            var s=l[c],f=e[c],h=b.getAt(q),n;
            n=t.filter(h);
            if(!n&&!f){
                c++;
                continue
            }
            if(!f){
                e[c]=f=u.onCreateCallout(h,s,q,n,o,c)
                }
                for(m in f){
                if(f[m]&&f[m].setAttributes){
                    f[m].setAttributes(g,true)
                    }
                }
            if(!n){
            for(m in f){
                if(f[m]){
                    if(f[m].setAttributes){
                        f[m].setAttributes({
                            hidden:true
                        },true)
                        }else{
                        if(f[m].setVisible){
                            f[m].setVisible(false)
                            }
                        }
                }
            }
        }
        t.renderer(f,h);
    u.onPlaceCallout(f,h,s,q,n,a,o,c,k);
    k.push(f);
    c++
    }
}
this.hideCallouts(c)
},
onCreateCallout:function(f,m,e,h){
    var j=this,k=j.calloutsGroup,d=j.callouts,n=d.styles,c=n.width,l=n.height,g=j.chart,b=g.surface,a={
        lines:false
    };
    
    a.lines=b.add(Ext.apply({},{
        type:"path",
        path:"M0,0",
        stroke:j.getLegendColor()||"#555"
        },n));
    if(d.items){
        a.panel=Ext.create("widget.panel",{
            style:"position: absolute;",
            width:c,
            height:l,
            items:d.items,
            renderTo:g.el
            })
        }
        return a
    },
hideCallouts:function(b){
    var d=this.calloutsArray,a=d.length,e,c;
    while(a-->b){
        e=d[a];
        for(c in e){
            if(e[c]){
                e[c].hide(true)
                }
            }
        }
    }
});
Ext.define("Ext.chart.Shape",{
    singleton:true,
    circle:function(a,b){
        return a.add(Ext.apply({
            type:"circle",
            x:b.x,
            y:b.y,
            stroke:null,
            radius:b.radius
            },b))
        },
    line:function(a,b){
        return a.add(Ext.apply({
            type:"rect",
            x:b.x-b.radius,
            y:b.y-b.radius,
            height:2*b.radius,
            width:2*b.radius/5
            },b))
        },
    square:function(a,b){
        return a.add(Ext.applyIf({
            type:"rect",
            x:b.x-b.radius,
            y:b.y-b.radius,
            height:2*b.radius,
            width:2*b.radius,
            radius:null
        },b))
        },
    triangle:function(a,b){
        b.radius*=1.75;
        return a.add(Ext.apply({
            type:"path",
            stroke:null,
            path:"M".concat(b.x,",",b.y,"m0-",b.radius*0.58,"l",b.radius*0.5,",",b.radius*0.87,"-",b.radius,",0z")
            },b))
        },
    diamond:function(a,c){
        var b=c.radius;
        b*=1.5;
        return a.add(Ext.apply({
            type:"path",
            stroke:null,
            path:["M",c.x,c.y-b,"l",b,b,-b,b,-b,-b,b,-b,"z"]
            },c))
        },
    cross:function(a,c){
        var b=c.radius;
        b=b/1.7;
        return a.add(Ext.apply({
            type:"path",
            stroke:null,
            path:"M".concat(c.x-b,",",c.y,"l",[-b,-b,b,-b,b,b,b,-b,b,b,-b,b,b,b,-b,b,-b,-b,-b,b,-b,-b,"z"])
            },c))
        },
    plus:function(a,c){
        var b=c.radius/1.3;
        return a.add(Ext.apply({
            type:"path",
            stroke:null,
            path:"M".concat(c.x-b/2,",",c.y-b/2,"l",[0,-b,b,0,0,b,b,0,0,b,-b,0,0,b,-b,0,0,-b,-b,0,0,-b,"z"])
            },c))
        },
    arrow:function(a,c){
        var b=c.radius;
        return a.add(Ext.apply({
            type:"path",
            path:"M".concat(c.x-b*0.7,",",c.y-b*0.4,"l",[b*0.6,0,0,-b*0.4,b,b*0.8,-b,b*0.8,0,-b*0.4,-b*0.6,0],"z")
            },c))
        },
    drop:function(b,a,f,e,c,d){
        c=c||30;
        d=d||0;
        b.add({
            type:"path",
            path:["M",a,f,"l",c,0,"A",c*0.4,c*0.4,0,1,0,a+c*0.7,f-c*0.7,"z"],
            fill:"#000",
            stroke:"none",
            rotate:{
                degrees:22.5-d,
                x:a,
                y:f
            }
        });
    d=(d+90)*Math.PI/180;
    b.add({
        type:"text",
        x:a+c*Math.sin(d)-10,
        y:f+c*Math.cos(d)+5,
        text:e,
        "font-size":c*12/40,
        stroke:"none",
        fill:"#fff"
    })
    }
});
Ext.define("Ext.ZIndexManager",{
    alternateClassName:"Ext.WindowGroup",
    statics:{
        zBase:9000
    },
    constructor:function(a){
        var b=this;
        b.list={};
        
        b.zIndexStack=[];
        b.front=null;
        if(a){
            if(a.isContainer){
                a.on("resize",b._onContainerResize,b);
                b.zseed=Ext.Number.from(a.getEl().getStyle("zIndex"),b.getNextZSeed());
                b.targetEl=a.getTargetEl();
                b.container=a
                }else{
                Ext.EventManager.onWindowResize(b._onContainerResize,b);
                b.zseed=b.getNextZSeed();
                b.targetEl=Ext.get(a)
                }
            }else{
        Ext.EventManager.onWindowResize(b._onContainerResize,b);
        b.zseed=b.getNextZSeed();
        Ext.onDocumentReady(function(){
            b.targetEl=Ext.getBody()
            })
        }
    },
getNextZSeed:function(){
    return(Ext.ZIndexManager.zBase+=10000)
    },
setBase:function(a){
    this.zseed=a;
    return this.assignZIndices()
    },
assignZIndices:function(){
    var c=this.zIndexStack,b=c.length,e=0,f=this.zseed,d;
    for(;e<b;e++){
        d=c[e];
        if(d&&!d.hidden){
            f=d.setZIndex(f)
            }
        }
    this._activateLast();
    return f
    },
_setActiveChild:function(a){
    if(a!=this.front){
        if(this.front){
            this.front.setActive(false,a)
            }
            this.front=a;
        if(a){
            a.setActive(true);
            if(a.modal){
                this._showModalMask(a.el.getStyle("zIndex")-4)
                }
            }
    }
},
_activateLast:function(a){
    var b,d=false,c;
    for(c=this.zIndexStack.length-1;c>=0;--c){
        b=this.zIndexStack[c];
        if(!b.hidden){
            if(!d){
                this._setActiveChild(b);
                d=true
                }
                if(b.modal){
                this._showModalMask(b.el.getStyle("zIndex")-4);
                return
            }
        }
    }
    this._hideModalMask();
if(!d){
    this._setActiveChild(null)
    }
},
_showModalMask:function(a){
    if(!this.mask){
        this.mask=this.targetEl.createChild({
            cls:Ext.baseCSSPrefix+"mask"
            });
        this.mask.setVisibilityMode(Ext.core.Element.DISPLAY);
        this.mask.on("click",this._onMaskClick,this)
        }
        Ext.getBody().addCls(Ext.baseCSSPrefix+"body-masked");
    this.mask.setSize(this.targetEl.getViewSize(true));
    this.mask.setStyle("zIndex",a);
    this.mask.show()
    },
_hideModalMask:function(){
    if(this.mask){
        Ext.getBody().removeCls(Ext.baseCSSPrefix+"body-masked");
        this.mask.hide()
        }
    },
_onMaskClick:function(){
    if(this.front){
        this.front.focus()
        }
    },
_onContainerResize:function(){
    if(this.mask&&this.mask.isVisible()){
        this.mask.setSize(this.targetEl.getViewSize(true))
        }
    },
register:function(a){
    if(a.zIndexManager){
        a.zIndexManager.unregister(a)
        }
        a.zIndexManager=this;
    this.list[a.id]=a;
    this.zIndexStack.push(a);
    a.on("hide",this._activateLast,this)
    },
unregister:function(a){
    delete a.zIndexManager;
    if(this.list&&this.list[a.id]){
        delete this.list[a.id];
        a.un("hide",this._activateLast);
        Ext.Array.remove(this.zIndexStack,a);
        this._activateLast(a)
        }
    },
get:function(a){
    return typeof a=="object"?a:this.list[a]
    },
bringToFront:function(a){
    a=this.get(a);
    if(a!=this.front){
        Ext.Array.remove(this.zIndexStack,a);
        this.zIndexStack.push(a);
        this.assignZIndices();
        return true
        }
        if(a.modal){
        Ext.getBody().addCls(Ext.baseCSSPrefix+"body-masked");
        this.mask.setSize(Ext.core.Element.getViewWidth(true),Ext.core.Element.getViewHeight(true));
        this.mask.show()
        }
        return false
    },
sendToBack:function(a){
    a=this.get(a);
    Ext.Array.remove(this.zIndexStack,a);
    this.zIndexStack.unshift(a);
    this.assignZIndices();
    return a
    },
hideAll:function(){
    for(var a in this.list){
        if(this.list[a].isComponent&&this.list[a].isVisible()){
            this.list[a].hide()
            }
        }
    },
hide:function(){
    var b=0,c=this.zIndexStack.length,a;
    this.tempHidden=[];
    for(;b<c;b++){
        a=this.zIndexStack[b];
        if(a.isVisible()){
            this.tempHidden.push(a);
            a.hide()
            }
        }
    },
show:function(){
    var c=0,d=this.tempHidden.length,b,a,e;
    for(;c<d;c++){
        b=this.tempHidden[c];
        a=b.x;
        e=b.y;
        b.show();
        b.setPosition(a,e)
        }
        delete this.tempHidden
    },
getActive:function(){
    return this.front
    },
getBy:function(e,d){
    var f=[],c=0,a=this.zIndexStack.length,b;
    for(;c<a;c++){
        b=this.zIndexStack[c];
        if(e.call(d||b,b)!==false){
            f.push(b)
            }
        }
    return f
},
each:function(c,b){
    var a;
    for(var d in this.list){
        a=this.list[d];
        if(a.isComponent&&c.call(b||a,a)===false){
            return
        }
    }
    },
eachBottomUp:function(e,d){
    var b,a=this.zIndexStack,c,f;
    for(c=0,f=a.length;c<f;c++){
        b=a[c];
        if(b.isComponent&&e.call(d||b,b)===false){
            return
        }
    }
    },
eachTopDown:function(e,d){
    var b,a=this.zIndexStack,c;
    for(c=a.length;c-->0;){
        b=a[c];
        if(b.isComponent&&e.call(d||b,b)===false){
            return
        }
    }
    },
destroy:function(){
    delete this.zIndexStack;
    delete this.list;
    delete this.container;
    delete this.targetEl
    }
},function(){
    Ext.WindowManager=Ext.WindowMgr=new this()
    });
Ext.define("Ext.draw.Color",{
    colorToHexRe:/(.*?)rgb\((\d+),\s*(\d+),\s*(\d+)\)/,
    rgbRe:/\s*rgb\s*\(\s*([0-9]+)\s*,\s*([0-9]+)\s*,\s*([0-9]+)\s*\)\s*/,
    hexRe:/\s*#([0-9a-fA-F][0-9a-fA-F]?)([0-9a-fA-F][0-9a-fA-F]?)([0-9a-fA-F][0-9a-fA-F]?)\s*/,
    lightnessFactor:0.2,
    constructor:function(d,c,a){
        var b=this,e=Ext.Number.constrain;
        b.r=e(d,0,255);
        b.g=e(c,0,255);
        b.b=e(a,0,255)
        },
    getRed:function(){
        return this.r
        },
    getGreen:function(){
        return this.g
        },
    getBlue:function(){
        return this.b
        },
    getRGB:function(){
        var a=this;
        return[a.r,a.g,a.b]
        },
    getHSL:function(){
        var i=this,a=i.r/255,f=i.g/255,j=i.b/255,k=Math.max(a,f,j),d=Math.min(a,f,j),m=k-d,e,n=0,c=0.5*(k+d);
        if(d!=k){
            n=(c<0.5)?m/(k+d):m/(2-k-d);
            if(a==k){
                e=60*(f-j)/m
                }else{
                if(f==k){
                    e=120+60*(j-a)/m
                    }else{
                    e=240+60*(a-f)/m
                    }
                }
            if(e<0){
            e+=360
            }
            if(e>=360){
            e-=360
            }
        }
    return[e,n,c]
},
getLighter:function(b){
    var a=this.getHSL();
    b=b||this.lightnessFactor;
    a[2]=Ext.Number.constrain(a[2]+b,0,1);
    return this.fromHSL(a[0],a[1],a[2])
    },
getDarker:function(a){
    a=a||this.lightnessFactor;
    return this.getLighter(-a)
    },
toString:function(){
    var f=this,c=Math.round,e=c(f.r).toString(16),d=c(f.g).toString(16),a=c(f.b).toString(16);
    e=(e.length==1)?"0"+e:e;
    d=(d.length==1)?"0"+d:d;
    a=(a.length==1)?"0"+a:a;
    return["#",e,d,a].join("")
    },
toHex:function(b){
    if(Ext.isArray(b)){
        b=b[0]
        }
        if(!Ext.isString(b)){
        return""
        }
        if(b.substr(0,1)==="#"){
        return b
        }
        var e=this.colorToHexRe.exec(b);
    if(Ext.isArray(e)){
        var f=parseInt(e[2],10),d=parseInt(e[3],10),a=parseInt(e[4],10),c=a|(d<<8)|(f<<16);
        return e[1]+"#"+("000000"+c.toString(16)).slice(-6)
        }else{
        return""
        }
    },
fromString:function(h){
    var c,e,d,a,f=parseInt;
    if((h.length==4||h.length==7)&&h.substr(0,1)==="#"){
        c=h.match(this.hexRe);
        if(c){
            e=f(c[1],16)>>0;
            d=f(c[2],16)>>0;
            a=f(c[3],16)>>0;
            if(h.length==4){
                e+=(e*16);
                d+=(d*16);
                a+=(a*16)
                }
            }
    }else{
    c=h.match(this.rgbRe);
    if(c){
        e=c[1];
        d=c[2];
        a=c[3]
        }
    }
return(typeof e=="undefined")?undefined:Ext.create("Ext.draw.Color",e,d,a)
},
getGrayscale:function(){
    return this.r*0.3+this.g*0.59+this.b*0.11
    },
fromHSL:function(f,n,d){
    var a,b,c,e,j=[],k=Math.abs,g=Math.floor;
    if(n==0||f==null){
        j=[d,d,d]
        }else{
        f/=60;
        a=n*(1-k(2*d-1));
        b=a*(1-k(f-2*g(f/2)-1));
        c=d-a/2;
        switch(g(f)){
            case 0:
                j=[a,b,0];
                break;
            case 1:
                j=[b,a,0];
                break;
            case 2:
                j=[0,a,b];
                break;
            case 3:
                j=[0,b,a];
                break;
            case 4:
                j=[b,0,a];
                break;
            case 5:
                j=[a,0,b];
                break
                }
                j=[j[0]+c,j[1]+c,j[2]+c]
        }
        return Ext.create("Ext.draw.Color",j[0]*255,j[1]*255,j[2]*255)
    }
},function(){
    var a=this.prototype;
    this.addStatics({
        fromHSL:function(){
            return a.fromHSL.apply(a,arguments)
            },
        fromString:function(){
            return a.fromString.apply(a,arguments)
            },
        toHex:function(){
            return a.toHex.apply(a,arguments)
            }
        })
});
Ext.define("Ext.fx.target.Sprite",{
    extend:"Ext.fx.target.Target",
    type:"draw",
    getFromPrim:function(b,a){
        var c;
        if(a=="translate"){
            c={
                x:b.attr.translation.x||0,
                y:b.attr.translation.y||0
                }
            }else{
        if(a=="rotate"){
            c={
                degrees:b.attr.rotation.degrees||0,
                x:b.attr.rotation.x,
                y:b.attr.rotation.y
                }
            }else{
        c=b.attr[a]
        }
    }
return c
},
getAttr:function(a,b){
    return[[this.target,b!=undefined?b:this.getFromPrim(this.target,a)]]
    },
setAttr:function(m){
    var g=m.length,k=[],q,f,p,e,b,o,n,d,c,l,h,a;
    for(d=0;d<g;d++){
        q=m[d].attrs;
        for(f in q){
            p=q[f];
            a=p.length;
            for(c=0;c<a;c++){
                b=p[c][0];
                e=p[c][1];
                if(f==="translate"){
                    n={
                        x:e.x,
                        y:e.y
                        }
                    }else{
                if(f==="rotate"){
                    l=e.x;
                    if(isNaN(l)){
                        l=null
                        }
                        h=e.y;
                    if(isNaN(h)){
                        h=null
                        }
                        n={
                        degrees:e.degrees,
                        x:l,
                        y:h
                    }
                }else{
                if(f==="width"||f==="height"||f==="x"||f==="y"){
                    n=parseFloat(e)
                    }else{
                    n=e
                    }
                }
            }
        o=Ext.Array.indexOf(k,b);
            if(o==-1){
            k.push([b,{}]);
            o=k.length-1
            }
            k[o][1][f]=n
        }
        }
    }
g=k.length;
for(d=0;d<g;d++){
    b=k[d];
    b[0].setAttributes(b[1])
    }
    this.target.redraw()
}
});
Ext.define("Ext.fx.target.CompositeSprite",{
    extend:"Ext.fx.target.Sprite",
    getAttr:function(a,d){
        var b=[],c=this.target;
        c.each(function(e){
            b.push([e,d!=undefined?d:this.getFromPrim(e,a)])
            },this);
        return b
        }
    });
Ext.define("Ext.util.Animate",{
    uses:["Ext.fx.Manager","Ext.fx.Anim"],
    animate:function(a){
        var b=this;
        if(Ext.fx.Manager.hasFxBlock(b.id)){
            return b
            }
            Ext.fx.Manager.queueFx(Ext.create("Ext.fx.Anim",b.anim(a)));
        return this
        },
    anim:function(a){
        if(!Ext.isObject(a)){
            return(a)?{}:false
            }
            var b=this;
        if(a.stopAnimation){
            b.stopAnimation()
            }
            Ext.applyIf(a,Ext.fx.Manager.getFxDefaults(b.id));
        return Ext.apply({
            target:b,
            paused:true
        },a)
        },
    stopFx:Ext.Function.alias(Ext.util.Animate,"stopAnimation"),
    stopAnimation:function(){
        Ext.fx.Manager.stopAnimation(this.id);
        return this
        },
    syncFx:function(){
        Ext.fx.Manager.setFxDefaults(this.id,{
            concurrent:true
        });
        return this
        },
    sequenceFx:function(){
        Ext.fx.Manager.setFxDefaults(this.id,{
            concurrent:false
        });
        return this
        },
    hasActiveFx:Ext.Function.alias(Ext.util.Animate,"getActiveAnimation"),
    getActiveAnimation:function(){
        return Ext.fx.Manager.getActiveAnimation(this.id)
        }
    },function(){
    Ext.applyIf(Ext.core.Element.prototype,this.prototype);
    Ext.CompositeElementLite.importElementMethods()
    });
Ext.define("Ext.ComponentQuery",{
    singleton:true,
    uses:["Ext.ComponentManager"]
    },function(){
    var g=this,j=["var r = [],","i = 0,","it = items,","l = it.length,","c;","for (; i < l; i++) {","c = it[i];","if (c.{0}) {","r.push(c);","}","}","return r;"].join(""),e=function(o,n){
        return n.method.apply(this,[o].concat(n.args))
        },a=function(p,t){
        var n=[],q=0,s=p.length,r,o=t!==">";
        for(;q<s;q++){
            r=p[q];
            if(r.getRefItems){
                n=n.concat(r.getRefItems(o))
                }
            }
        return n
    },f=function(o){
    var n=[],p=0,r=o.length,q;
    for(;p<r;p++){
        q=o[p];
        while(!!(q=(q.ownerCt||q.floatParent))){
            n.push(q)
            }
        }
    return n
},l=function(o,t,s){
    if(t==="*"){
        return o.slice()
        }else{
        var n=[],p=0,r=o.length,q;
        for(;p<r;p++){
            q=o[p];
            if(q.isXType(t,s)){
                n.push(q)
                }
            }
        return n
    }
},i=function(o,r){
    var t=Ext.Array,n=[],p=0,s=o.length,q;
    for(;p<s;p++){
        q=o[p];
        if(q.el?q.el.hasCls(r):t.contains(q.initCls(),r)){
            n.push(q)
            }
        }
    return n
},m=function(p,u,o,t){
    var n=[],q=0,s=p.length,r;
    for(;q<s;q++){
        r=p[q];
        if(!t?!!r[u]:(String(r[u])===t)){
            n.push(r)
            }
        }
    return n
},d=function(o,s){
    var n=[],p=0,r=o.length,q;
    for(;p<r;p++){
        q=o[p];
        if(q.getItemId()===s){
            n.push(q)
            }
        }
    return n
},k=function(n,o,p){
    return g.pseudos[o](n,p)
    },h=/^(\s?([>\^])\s?|\s|$)/,c=/^(#)?([\w\-]+|\*)(?:\((true|false)\))?/,b=[{
    re:/^\.([\w\-]+)(?:\((true|false)\))?/,
    method:l
},{
    re:/^(?:[\[](?:@)?([\w\-]+)\s?(?:(=|.=)\s?['"]?(.*?)["']?)?[\]])/,
    method:m
},{
    re:/^#([\w\-]+)/,
    method:d
},{
    re:/^\:([\w\-]+)(?:\(((?:\{[^\}]+\})|(?:(?!\{)[^\s>\/]*?(?!\})))\))?/,
    method:k
},{
    re:/^(?:\{([^\}]+)\})/,
    method:j
}];
g.Query=Ext.extend(Object,{
    constructor:function(n){
        n=n||{};
        
        Ext.apply(this,n)
        },
    execute:function(o){
        var q=this.operations,r=0,s=q.length,p,n;
        if(!o){
            n=Ext.ComponentManager.all.getArray()
            }else{
            if(Ext.isArray(o)){
                n=o
                }
            }
        for(;r<s;r++){
        p=q[r];
        if(p.mode==="^"){
            n=f(n||[o])
            }else{
            if(p.mode){
                n=a(n||[o],p.mode)
                }else{
                n=e(n||a([o]),p)
                }
            }
        if(r===s-1){
        return n
        }
    }
    return[]
},
is:function(p){
    var o=this.operations,s=Ext.isArray(p)?p:[p],n=s.length,t=o[o.length-1],r,q;
    s=e(s,t);
    if(s.length===n){
        if(o.length>1){
            for(q=0,r=s.length;q<r;q++){
                if(Ext.Array.indexOf(this.execute(),s[q])===-1){
                    return false
                    }
                }
            }
        return true
}
return false
}
});
Ext.apply(this,{
    cache:{},
    pseudos:{
        not:function(t,n){
            var u=Ext.ComponentQuery,r=0,s=t.length,q=[],p=-1,o;
            for(;r<s;++r){
                o=t[r];
                if(!u.is(o,n)){
                    q[++p]=o
                    }
                }
            return q
        }
    },
query:function(o,v){
    var w=o.split(","),n=w.length,p=0,q=[],x=[],u={},s,r,t;
    for(;p<n;p++){
        o=Ext.String.trim(w[p]);
        s=this.cache[o];
        if(!s){
            this.cache[o]=s=this.parse(o)
            }
            q=q.concat(s.execute(v))
        }
        if(n>1){
        r=q.length;
        for(p=0;p<r;p++){
            t=q[p];
            if(!u[t.id]){
                x.push(t);
                u[t.id]=true
                }
            }
        q=x
    }
    return q
},
is:function(o,n){
    if(!n){
        return true
        }
        var p=this.cache[n];
    if(!p){
        this.cache[n]=p=this.parse(n)
        }
        return p.is(o)
    },
parse:function(q){
    var o=[],p=b.length,u,r,v,w,x,s,t,n;
    while(q&&u!==q){
        u=q;
        r=q.match(c);
        if(r){
            v=r[1];
            if(v==="#"){
                o.push({
                    method:d,
                    args:[Ext.String.trim(r[2])]
                    })
                }else{
                if(v==="."){
                    o.push({
                        method:i,
                        args:[Ext.String.trim(r[2])]
                        })
                    }else{
                    o.push({
                        method:l,
                        args:[Ext.String.trim(r[2]),Boolean(r[3])]
                        })
                    }
                }
            q=q.replace(r[0],"")
        }while(!(w=q.match(h))){
        for(s=0;q&&s<p;s++){
            t=b[s];
            x=q.match(t.re);
            n=t.method;
            if(x){
                o.push({
                    method:Ext.isString(t.method)?Ext.functionFactory("items",Ext.String.format.apply(Ext.String,[n].concat(x.slice(1)))):t.method,
                    args:x.slice(1)
                    });
                q=q.replace(x[0],"");
                break
            }
        }
        }
    if(w[1]){
    o.push({
        mode:w[2]||w[1]
        });
    q=q.replace(w[0],"")
    }
}
return new g.Query({
    operations:o
})
}
})
});
Ext.define("Ext.layout.component.Auto",{
    alias:"layout.autocomponent",
    extend:"Ext.layout.component.Component",
    type:"autocomponent",
    onLayout:function(b,a){
        this.setTargetSize(b,a)
        }
    });
Ext.define("Ext.panel.Proxy",{
    alternateClassName:"Ext.dd.PanelProxy",
    constructor:function(a,b){
        this.panel=a;
        this.id=this.panel.id+"-ddproxy";
        Ext.apply(this,b)
        },
    insertProxy:true,
    setStatus:Ext.emptyFn,
    reset:Ext.emptyFn,
    update:Ext.emptyFn,
    stop:Ext.emptyFn,
    sync:Ext.emptyFn,
    getEl:function(){
        return this.ghost.el
        },
    getGhost:function(){
        return this.ghost
        },
    getProxy:function(){
        return this.proxy
        },
    hide:function(){
        if(this.ghost){
            if(this.proxy){
                this.proxy.remove();
                delete this.proxy
                }
                this.panel.unghost(null,false);
            delete this.ghost
            }
        },
show:function(){
    if(!this.ghost){
        var a=this.panel.getSize();
        this.panel.el.setVisibilityMode(Ext.core.Element.DISPLAY);
        this.ghost=this.panel.ghost();
        if(this.insertProxy){
            this.proxy=this.panel.el.insertSibling({
                cls:Ext.baseCSSPrefix+"panel-dd-spacer"
                });
            this.proxy.setSize(a)
            }
        }
},
repair:function(b,c,a){
    this.hide();
    if(typeof c=="function"){
        c.call(a||this)
        }
    },
moveProxy:function(a,b){
    if(this.proxy){
        a.insertBefore(this.proxy.dom,b)
        }
    }
});
Ext.define("Ext.layout.component.AbstractDock",{
    extend:"Ext.layout.component.Component",
    type:"dock",
    autoSizing:true,
    beforeLayout:function(){
        var a=this.callParent(arguments);
        if(a!==false&&(!this.initializedBorders||this.childrenChanged)&&(!this.owner.border||this.owner.manageBodyBorders)){
            this.handleItemBorders();
            this.initializedBorders=true
            }
            return a
        },
    handleItemBorders:function(){
        var a=this.owner,e=a.body,l=this.getLayoutItems(),g={
            top:[],
            right:[],
            bottom:[],
            left:[]
        },b=this.borders,d={
            top:"bottom",
            right:"left",
            bottom:"top",
            left:"right"
        },c,h,k,j,f;
        for(c=0,h=l.length;c<h;c++){
            k=l[c];
            j=k.dock;
            if(k.ignoreBorderManagement){
                continue
            }
            if(!g[j].satisfied){
                g[j].push(k);
                g[j].satisfied=true
                }
                if(!g.top.satisfied&&d[j]!=="top"){
                g.top.push(k)
                }
                if(!g.right.satisfied&&d[j]!=="right"){
                g.right.push(k)
                }
                if(!g.bottom.satisfied&&d[j]!=="bottom"){
                g.bottom.push(k)
                }
                if(!g.left.satisfied&&d[j]!=="left"){
                g.left.push(k)
                }
            }
        if(b){
        for(f in b){
            if(b.hasOwnProperty(f)){
                h=b[f].length;
                if(!a.manageBodyBorders){
                    for(c=0;c<h;c++){
                        b[f][c].removeCls(Ext.baseCSSPrefix+"docked-noborder-"+f)
                        }
                        if(!b[f].satisfied&&!a.bodyBorder){
                        e.removeCls(Ext.baseCSSPrefix+"docked-noborder-"+f)
                        }
                    }else{
                if(b[f].satisfied){
                    e.setStyle("border-"+f+"-width","")
                    }
                }
        }
        }
}
for(f in g){
    if(g.hasOwnProperty(f)){
        h=g[f].length;
        if(!a.manageBodyBorders){
            for(c=0;c<h;c++){
                g[f][c].addCls(Ext.baseCSSPrefix+"docked-noborder-"+f)
                }
                if((!g[f].satisfied&&!a.bodyBorder)||a.bodyBorder===false){
                e.addCls(Ext.baseCSSPrefix+"docked-noborder-"+f)
                }
            }else{
        if(g[f].satisfied){
            e.setStyle("border-"+f+"-width","1px")
            }
        }
}
}
this.borders=g
},
onLayout:function(a,m){
    var i=this,b=i.owner,g=b.body,f=b.layout,h=i.getTarget(),k=false,l=false,j,e,d;
    var c=i.info={
        boxes:[],
        size:{
            width:a,
            height:m
        },
        bodyBox:{}
};

delete f.isAutoDock;
Ext.applyIf(c,i.getTargetInfo());
if(b&&b.ownerCt&&b.ownerCt.layout&&b.ownerCt.layout.isLayout){
    if(!Ext.isNumber(b.height)||!Ext.isNumber(b.width)){
        b.ownerCt.layout.bindToOwnerCtComponent=true
        }else{
        b.ownerCt.layout.bindToOwnerCtComponent=false
        }
    }
if(m===undefined||m===null||a===undefined||a===null){
    j=c.padding;
    e=c.border;
    d=i.frameSize;
    if((m===undefined||m===null)&&(a===undefined||a===null)){
        l=true;
        k=true;
        i.setTargetSize(null);
        i.setBodyBox({
            width:null,
            height:null
        })
        }else{
        if(m===undefined||m===null){
            l=true;
            i.setTargetSize(a);
            i.setBodyBox({
                width:a-j.left-e.left-j.right-e.right-d.left-d.right,
                height:null
            })
            }else{
            k=true;
            i.setTargetSize(null,m);
            i.setBodyBox({
                width:null,
                height:m-j.top-j.bottom-e.top-e.bottom-d.top-d.bottom
                })
            }
        }
    if(f&&f.isLayout){
    f.bindToOwnerCtComponent=true;
    f.isAutoDock=f.autoSize!==true;
    f.layout();
    c.autoSizedCtLayout=f.autoSize===true
    }
    i.dockItems(k,l);
i.setTargetSize(c.size.width,c.size.height)
}else{
    i.setTargetSize(a,m);
    i.dockItems()
    }
    i.callParent(arguments)
},
dockItems:function(g,a){
    this.calculateDockBoxes(g,a);
    var f=this.info,c=f.boxes,e=c.length,d,b;
    for(b=0;b<e;b++){
        d=c[b];
        d.item.setPosition(d.x,d.y);
        if((g||a)&&d.layout&&d.layout.isLayout){
            d.layout.bindToOwnerCtComponent=true
            }
        }
    if(!f.autoSizedCtLayout){
    if(g){
        f.bodyBox.width=null
        }
        if(a){
        f.bodyBox.height=null
        }
    }
this.setBodyBox(f.bodyBox)
},
calculateDockBoxes:function(n,o){
    var k=this,g=k.getTarget(),j=k.getLayoutItems(),a=k.owner,q=a.body,b=k.info,r=b.size,h=j.length,m=b.padding,d=b.border,c=k.frameSize,p,e,f,l;
    if(o){
        r.height=q.getHeight()+m.top+d.top+m.bottom+d.bottom+c.top+c.bottom
        }else{
        r.height=g.getHeight()
        }
        if(n){
        r.width=q.getWidth()+m.left+d.left+m.right+d.right+c.left+c.right
        }else{
        r.width=g.getWidth()
        }
        b.bodyBox={
        x:m.left+c.left,
        y:m.top+c.top,
        width:r.width-m.left-d.left-m.right-d.right-c.left-c.right,
        height:r.height-d.top-m.top-d.bottom-m.bottom-c.top-c.bottom
        };
        
    for(e=0;e<h;e++){
        p=j[e];
        f=k.initBox(p);
        if(o===true){
            f=k.adjustAutoBox(f,e)
            }else{
            f=k.adjustSizedBox(f,e)
            }
            b.boxes.push(f)
        }
    },
adjustSizedBox:function(e,d){
    var a=this.info.bodyBox,b=this.frameSize,g=this.info,f=g.padding,h=e.type,c=g.border;
    switch(h){
        case"top":
            e.y=a.y;
            break;
        case"left":
            e.x=a.x;
            break;
        case"bottom":
            e.y=(a.y+a.height)-e.height;
            break;
        case"right":
            e.x=(a.x+a.width)-e.width;
            break
            }
            if(e.ignoreFrame){
        if(h=="bottom"){
            e.y+=(b.bottom+f.bottom+c.bottom)
            }else{
            e.y-=(b.top+f.top+c.top)
            }
            if(h=="right"){
            e.x+=(b.right+f.right+c.right)
            }else{
            e.x-=(b.left+f.left+c.left)
            }
        }
    if(!e.overlay){
    switch(h){
        case"top":
            a.y+=e.height;
            a.height-=e.height;
            break;
        case"left":
            a.x+=e.width;
            a.width-=e.width;
            break;
        case"bottom":
            a.height-=e.height;
            break;
        case"right":
            a.width-=e.width;
            break
            }
        }
return e
},
adjustAutoBox:function(h,l){
    var b=this.info,a=this.owner,m=b.bodyBox,q=b.size,j=b.boxes,f=j.length,o=h.type,e=this.frameSize,p=b.padding,d=b.border,c=b.autoSizedCtLayout,n=(f<l)?f:l,g,k;
    if(o=="top"||o=="bottom"){
        for(g=0;g<n;g++){
            k=j[g];
            if(k.stretched&&k.type=="left"||k.type=="right"){
                k.height+=h.height
                }else{
                if(k.type=="bottom"){
                    k.y+=h.height
                    }
                }
        }
    }
switch(o){
    case"top":
        h.y=m.y;
        if(!h.overlay){
        m.y+=h.height;
        if(a.isFixedHeight()){
            m.height-=h.height
            }else{
            q.height+=h.height
            }
        }
    break;
case"bottom":
    if(!h.overlay){
    if(a.isFixedHeight()){
        m.height-=h.height
        }else{
        q.height+=h.height
        }
    }
h.y=(m.y+m.height);
break;
case"left":
    h.x=m.x;
    if(!h.overlay){
    m.x+=h.width;
    if(a.isFixedWidth()){
        m.width-=h.width
        }else{
        q.width+=h.width
        }
    }
break;
case"right":
    if(!h.overlay){
    if(a.isFixedWidth()){
        m.width-=h.width
        }else{
        q.width+=h.width
        }
    }
h.x=(m.x+m.width);
break
}
if(h.ignoreFrame){
    if(o=="bottom"){
        h.y+=(e.bottom+p.bottom+d.bottom)
        }else{
        h.y-=(e.top+p.top+d.top)
        }
        if(o=="right"){
        h.x+=(e.right+p.right+d.right)
        }else{
        h.x-=(e.left+p.left+d.left)
        }
    }
return h
},
initBox:function(j){
    var h=this,g=h.info.bodyBox,a=(j.dock=="top"||j.dock=="bottom"),b=h.owner,e=h.frameSize,c=h.info,i=c.padding,d=c.border,f={
        item:j,
        overlay:j.overlay,
        type:j.dock,
        offsets:Ext.core.Element.parseBox(j.offsets||{}),
        ignoreFrame:j.ignoreParentFrame
        };
        
    if(j.stretch!==false){
        f.stretched=true;
        if(a){
            f.x=g.x+f.offsets.left;
            f.width=g.width-(f.offsets.left+f.offsets.right);
            if(f.ignoreFrame){
                f.width+=(e.left+e.right+d.left+d.right+i.left+i.right)
                }
                j.setCalculatedSize(f.width-j.el.getMargin("lr"),undefined,b)
            }else{
            f.y=g.y+f.offsets.top;
            f.height=g.height-(f.offsets.bottom+f.offsets.top);
            if(f.ignoreFrame){
                f.height+=(e.top+e.bottom+d.top+d.bottom+i.top+i.bottom)
                }
                j.setCalculatedSize(undefined,f.height-j.el.getMargin("tb"),b);
            if(!Ext.supports.ComputedStyle){
                j.el.repaint()
                }
            }
    }else{
    j.doComponentLayout();
    f.width=j.getWidth()-(f.offsets.left+f.offsets.right);
    f.height=j.getHeight()-(f.offsets.bottom+f.offsets.top);
    f.y+=f.offsets.top;
    if(a){
        f.x=(j.align=="right")?g.width-f.width:g.x;
        f.x+=f.offsets.left
        }
    }
if(f.width==undefined){
    f.width=j.getWidth()+j.el.getMargin("lr")
    }
    if(f.height==undefined){
    f.height=j.getHeight()+j.el.getMargin("tb")
    }
    return f
},
getLayoutItems:function(){
    var c=this.owner.getDockedItems(),d=c.length,b=0,a=[];
    for(;b<d;b++){
        if(c[b].isVisible(true)){
            a.push(c[b])
            }
        }
    return a
},
renderItems:function(g,e){
    var a=e.dom.childNodes,d=a.length,f=g.length,k=0,c,b,h,l;
    for(c=0;c<d;c++){
        h=Ext.get(a[c]);
        for(b=0;b<f;b++){
            l=g[b];
            if(l.rendered&&(h.id==l.el.id||h.down("#"+l.el.id))){
                break
            }
        }
        if(b===f){
        k++
    }
    }
    for(c=0,b=0;c<f;c++,b++){
    l=g[c];
    if(c===b&&(l.dock==="right"||l.dock==="bottom")){
        b+=k
        }
        if(l&&!l.rendered){
        this.renderItem(l,e,b)
        }else{
        if(!this.isValidParent(l,e,b)){
            this.moveItem(l,e,b)
            }
        }
}
},
setBodyBox:function(f){
    var h=this,a=h.owner,g=a.body,b=h.info,e=b.bodyMargin,i=b.padding,d=b.border,c=h.frameSize;
    if(a.collapsed){
        return
    }
    if(Ext.isNumber(f.width)){
        f.width-=e.left+e.right
        }
        if(Ext.isNumber(f.height)){
        f.height-=e.top+e.bottom
        }
        h.setElementSize(g,f.width,f.height);
    if(Ext.isNumber(f.x)){
        g.setLeft(f.x-i.left-c.left)
        }
        if(Ext.isNumber(f.y)){
        g.setTop(f.y-i.top-c.top)
        }
    },
configureItem:function(a,b){
    this.callParent(arguments);
    if(a.dock=="top"||a.dock=="bottom"){
        a.layoutManagedWidth=1;
        a.layoutManagedHeight=2
        }else{
        a.layoutManagedWidth=2;
        a.layoutManagedHeight=1
        }
        a.addCls(Ext.baseCSSPrefix+"docked");
    a.addClsWithUI("docked-"+a.dock)
    },
afterRemove:function(a){
    this.callParent(arguments);
    if(this.itemCls){
        a.el.removeCls(this.itemCls+"-"+a.dock)
        }
        var b=a.el.dom;
    if(!a.destroying&&b){
        b.parentNode.removeChild(b)
        }
        this.childrenChanged=true
    }
});
Ext.define("Ext.chart.Mask",{
    constructor:function(a){
        var b=this;
        b.addEvents("select");
        if(a){
            Ext.apply(b,a)
            }
            if(b.mask){
            b.on("afterrender",function(){
                var c=Ext.create("Ext.chart.MaskLayer",{
                    renderTo:b.el
                    });
                c.el.on({
                    mousemove:function(f){
                        b.onMouseMove(f)
                        },
                    mouseup:function(f){
                        b.resized(f)
                        }
                    });
            var d=Ext.create("Ext.resizer.Resizer",{
                el:c.el,
                handles:"all",
                pinned:true
            });
            d.on({
                resize:function(f){
                    b.resized(f)
                    }
                });
            c.initDraggable();
            b.maskType=b.mask;
            b.mask=c;
            b.maskSprite=b.surface.add({
                type:"path",
                path:["M",0,0],
                zIndex:1001,
                opacity:0.7,
                hidden:true,
                stroke:"#444"
            })
            },b,{
            single:true
        })
    }
},
resized:function(d){
    var f=this,k=f.bbox||f.chartBBox,i=k.x,h=k.y,a=k.width,l=k.height,c=f.mask.getBox(true),g=Math.max,b=Math.min,m=c.x-i,j=c.y-h;
    m=g(m,i);
    j=g(j,h);
    m=b(m,a);
    j=b(j,l);
    c.x=m;
    c.y=j;
    f.fireEvent("select",f,c)
    },
onMouseUp:function(c){
    var a=this,d=a.bbox||a.chartBBox,b=a.maskSelection;
    a.maskMouseDown=false;
    a.mouseDown=false;
    if(a.mouseMoved){
        a.onMouseMove(c);
        a.mouseMoved=false;
        a.fireEvent("select",a,{
            x:b.x-d.x,
            y:b.y-d.y,
            width:b.width,
            height:b.height
            })
        }
    },
onMouseDown:function(b){
    var a=this;
    a.mouseDown=true;
    a.mouseMoved=false;
    a.maskMouseDown={
        x:b.getPageX()-a.el.getX(),
        y:b.getPageY()-a.el.getY()
        }
    },
onMouseMove:function(s){
    var t=this,n=t.maskType,a=t.bbox||t.chartBBox,h=a.x,g=a.y,b=Math,p=b.floor,i=b.abs,m=b.min,o=b.max,j=p(g+a.height),l=p(h+a.width),d=s.getPageX(),c=s.getPageY(),r=d-t.el.getX(),q=c-t.el.getY(),f=t.maskMouseDown,k;
    t.mouseMoved=t.mouseDown;
    r=o(r,h);
    q=o(q,g);
    r=m(r,l);
    q=m(q,j);
    if(f&&t.mouseDown){
        if(n=="horizontal"){
            q=g;
            f.y=j;
            c=t.el.getY()+a.height+t.insetPadding
            }else{
            if(n=="vertical"){
                r=h;
                f.x=l
                }
            }
        l=f.x-r;
    j=f.y-q;
    k=["M",r,q,"l",l,0,0,j,-l,0,"z"];
    t.maskSelection={
        x:l>0?r:r+l,
        y:j>0?q:q+j,
        width:i(l),
        height:i(j)
        };
        
    t.mask.updateBox({
        x:d-i(l),
        y:c-i(j),
        width:i(l),
        height:i(j)
        });
    t.mask.show();
    t.maskSprite.setAttributes({
        hidden:true
    },true)
    }else{
    if(n=="horizontal"){
        k=["M",r,g,"L",r,j]
        }else{
        if(n=="vertical"){
            k=["M",h,q,"L",l,q]
            }else{
            k=["M",r,g,"L",r,j,"M",h,q,"L",l,q]
            }
        }
    t.maskSprite.setAttributes({
    path:k,
    fill:t.maskMouseDown?t.maskSprite.stroke:false,
    "stroke-width":n===true?1:3,
    hidden:false
},true)
}
},
onMouseLeave:function(b){
    var a=this;
    a.mouseMoved=false;
    a.mouseDown=false;
    a.maskMouseDown=false;
    a.mask.hide();
    a.maskSprite.hide(true)
    }
});
Ext.define("Ext.chart.Navigation",{
    constructor:function(){
        this.originalStore=this.store
        },
    setZoom:function(g){
        var f=this,j=f.substore||f.store,k=f.chartBBox,e=j.getCount(),h=(g.x/k.width*e)>>0,i=Math.ceil(((g.x+g.width)/k.width*e)),b,d=[],a,l=[],c;
        j.each(function(n,m){
            if(m<h||m>i){
                return
            }
            c={};
            
            if(!d.length){
                n.fields.each(function(o){
                    d.push(o.name)
                    });
                b=d.length
                }
                for(m=0;m<b;m++){
                a=d[m];
                c[a]=n.get(a)
                }
                l.push(c)
            });
        f.store=f.substore=Ext.create("Ext.data.JsonStore",{
            fields:d,
            data:l
        });
        f.redraw(true)
        },
    restoreZoom:function(){
        this.store=this.substore=this.originalStore;
        this.redraw(true)
        }
    });
Ext.define("Ext.util.HashMap",{
    mixins:{
        observable:"Ext.util.Observable"
    },
    constructor:function(a){
        a=a||{};
        
        var c=this,b=a.keyFn;
        c.addEvents("add","clear","remove","replace");
        c.mixins.observable.constructor.call(c,a);
        c.clear(true);
        if(b){
            c.getKey=b
            }
        },
getCount:function(){
    return this.length
    },
getData:function(a,b){
    if(b===undefined){
        b=a;
        a=this.getKey(b)
        }
        return[a,b]
    },
getKey:function(a){
    return a.id
    },
add:function(a,d){
    var b=this,c;
    if(arguments.length===1){
        d=a;
        a=b.getKey(d)
        }
        if(b.containsKey(a)){
        b.replace(a,d)
        }
        c=b.getData(a,d);
    a=c[0];
    d=c[1];
    b.map[a]=d;
    ++b.length;
    b.fireEvent("add",b,a,d);
    return d
    },
replace:function(b,d){
    var c=this,e=c.map,a;
    if(!c.containsKey(b)){
        c.add(b,d)
        }
        a=e[b];
    e[b]=d;
    c.fireEvent("replace",c,b,d,a);
    return d
    },
remove:function(b){
    var a=this.findKey(b);
    if(a!==undefined){
        return this.removeAtKey(a)
        }
        return false
    },
removeAtKey:function(a){
    var b=this,c;
    if(b.containsKey(a)){
        c=b.map[a];
        delete b.map[a];
        --b.length;
        b.fireEvent("remove",b,a,c);
        return true
        }
        return false
    },
get:function(a){
    return this.map[a]
    },
clear:function(a){
    var b=this;
    b.map={};
    
    b.length=0;
    if(a!==true){
        b.fireEvent("clear",b)
        }
        return b
    },
containsKey:function(a){
    return this.map[a]!==undefined
    },
contains:function(a){
    return this.containsKey(this.findKey(a))
    },
getKeys:function(){
    return this.getArray(true)
    },
getValues:function(){
    return this.getArray(false)
    },
getArray:function(d){
    var a=[],b,c=this.map;
    for(b in c){
        if(c.hasOwnProperty(b)){
            a.push(d?b:c[b])
            }
        }
    return a
},
each:function(d,c){
    var a=Ext.apply({},this.map),b,e=this.length;
    c=c||this;
    for(b in a){
        if(a.hasOwnProperty(b)){
            if(d.call(c,b,a[b],e)===false){
                break
            }
        }
    }
    return this
},
clone:function(){
    var c=new this.self(),b=this.map,a;
    c.suspendEvents();
    for(a in b){
        if(b.hasOwnProperty(a)){
            c.add(a,b[a])
            }
        }
    c.resumeEvents();
return c
},
findKey:function(b){
    var a,c=this.map;
    for(a in c){
        if(c.hasOwnProperty(a)&&c[a]===b){
            return a
            }
        }
    return undefined
}
});
Ext.define("Ext.AbstractManager",{
    requires:["Ext.util.HashMap"],
    typeName:"type",
    constructor:function(a){
        Ext.apply(this,a||{});
        this.all=Ext.create("Ext.util.HashMap");
        this.types={}
    },
get:function(a){
    return this.all.get(a)
    },
register:function(a){
    this.all.add(a)
    },
unregister:function(a){
    this.all.remove(a)
    },
registerType:function(b,a){
    this.types[b]=a;
    a[this.typeName]=b
    },
isRegistered:function(a){
    return this.types[a]!==undefined
    },
create:function(a,d){
    var b=a[this.typeName]||a.type||d,c=this.types[b];
    return new c(a)
    },
onAvailable:function(e,c,b){
    var a=this.all,d;
    if(a.containsKey(e)){
        d=a.get(e);
        c.call(b||d,d)
        }else{
        a.on("add",function(h,f,g){
            if(f==e){
                c.call(b||g,g);
                a.un("add",c,b)
                }
            })
    }
},
each:function(b,a){
    this.all.each(b,a||this)
    },
getCount:function(){
    return this.all.getCount()
    }
});
Ext.define("Ext.ElementLoader",{
    mixins:{
        observable:"Ext.util.Observable"
    },
    uses:["Ext.data.Connection","Ext.Ajax"],
    statics:{
        Renderer:{
            Html:function(a,b,c){
                a.getTarget().update(b.responseText,c.scripts===true);
                return true
                }
            }
    },
url:null,
params:null,
baseParams:null,
autoLoad:false,
target:null,
loadMask:false,
ajaxOptions:null,
scripts:false,
isLoader:true,
constructor:function(b){
    var c=this,a;
    b=b||{};
    
    Ext.apply(c,b);
    c.setTarget(c.target);
    c.addEvents("beforeload","exception","load");
    c.mixins.observable.constructor.call(c);
    if(c.autoLoad){
        a=c.autoLoad;
        if(a===true){
            a={}
        }
        c.load(a)
    }
},
setTarget:function(b){
    var a=this;
    b=Ext.get(b);
    if(a.target&&a.target!=b){
        a.abort()
        }
        a.target=b
    },
getTarget:function(){
    return this.target||null
    },
abort:function(){
    var a=this.active;
    if(a!==undefined){
        Ext.Ajax.abort(a.request);
        if(a.mask){
            this.removeMask()
            }
            delete this.active
        }
    },
removeMask:function(){
    this.target.unmask()
    },
addMask:function(a){
    this.target.mask(a===true?null:a)
    },
load:function(h){
    h=Ext.apply({},h);
    var e=this,d=e.target,i=Ext.isDefined(h.loadMask)?h.loadMask:e.loadMask,b=Ext.apply({},h.params),a=Ext.apply({},h.ajaxOptions),f=h.callback||e.callback,g=h.scope||e.scope||e,c;
    Ext.applyIf(a,e.ajaxOptions);
    Ext.applyIf(h,a);
    Ext.applyIf(b,e.params);
    Ext.apply(b,e.baseParams);
    Ext.applyIf(h,{
        url:e.url
        });
    Ext.apply(h,{
        scope:e,
        params:b,
        callback:e.onComplete
        });
    if(e.fireEvent("beforeload",e,h)===false){
        return
    }
    if(i){
        e.addMask(i)
        }
        c=Ext.Ajax.request(h);
    e.active={
        request:c,
        options:h,
        mask:i,
        scope:g,
        callback:f,
        success:h.success||e.success,
        failure:h.failure||e.failure,
        renderer:h.renderer||e.renderer,
        scripts:Ext.isDefined(h.scripts)?h.scripts:e.scripts
        };
        
    e.setOptions(e.active,h)
    },
setOptions:Ext.emptyFn,
onComplete:function(b,g,a){
    var d=this,f=d.active,c=f.scope,e=d.getRenderer(f.renderer);
    if(g){
        g=e.call(d,d,a,f)
        }
        if(g){
        Ext.callback(f.success,c,[d,a,b]);
        d.fireEvent("load",d,a,b)
        }else{
        Ext.callback(f.failure,c,[d,a,b]);
        d.fireEvent("exception",d,a,b)
        }
        Ext.callback(f.callback,c,[d,g,a,b]);
    if(f.mask){
        d.removeMask()
        }
        delete d.active
    },
getRenderer:function(a){
    if(Ext.isFunction(a)){
        return a
        }
        return this.statics().Renderer.Html
    },
startAutoRefresh:function(a,b){
    var c=this;
    c.stopAutoRefresh();
    c.autoRefresh=setInterval(function(){
        c.load(b)
        },a)
    },
stopAutoRefresh:function(){
    clearInterval(this.autoRefresh);
    delete this.autoRefresh
    },
isAutoRefreshing:function(){
    return Ext.isDefined(this.autoRefresh)
    },
destroy:function(){
    var a=this;
    a.stopAutoRefresh();
    delete a.target;
    a.abort();
    a.clearListeners()
    }
});
Ext.define("Ext.dd.StatusProxy",{
    animRepair:false,
    constructor:function(a){
        Ext.apply(this,a);
        this.id=this.id||Ext.id();
        this.proxy=Ext.createWidget("component",{
            floating:true,
            id:this.id,
            html:'<div class="'+Ext.baseCSSPrefix+'dd-drop-icon"></div><div class="'+Ext.baseCSSPrefix+'dd-drag-ghost"></div>',
            cls:Ext.baseCSSPrefix+"dd-drag-proxy "+this.dropNotAllowed,
            shadow:!a||a.shadow!==false,
            renderTo:document.body
            });
        this.el=this.proxy.el;
        this.el.show();
        this.el.setVisibilityMode(Ext.core.Element.VISIBILITY);
        this.el.hide();
        this.ghost=Ext.get(this.el.dom.childNodes[1]);
        this.dropStatus=this.dropNotAllowed
        },
    dropAllowed:Ext.baseCSSPrefix+"dd-drop-ok",
    dropNotAllowed:Ext.baseCSSPrefix+"dd-drop-nodrop",
    setStatus:function(a){
        a=a||this.dropNotAllowed;
        if(this.dropStatus!=a){
            this.el.replaceCls(this.dropStatus,a);
            this.dropStatus=a
            }
        },
reset:function(a){
    this.el.dom.className=Ext.baseCSSPrefix+"dd-drag-proxy "+this.dropNotAllowed;
    this.dropStatus=this.dropNotAllowed;
    if(a){
        this.ghost.update("")
        }
    },
update:function(a){
    if(typeof a=="string"){
        this.ghost.update(a)
        }else{
        this.ghost.update("");
        a.style.margin="0";
        this.ghost.dom.appendChild(a)
        }
        var b=this.ghost.dom.firstChild;
    if(b){
        Ext.fly(b).setStyle("float","none")
        }
    },
getEl:function(){
    return this.el
    },
getGhost:function(){
    return this.ghost
    },
hide:function(a){
    this.proxy.hide();
    if(a){
        this.reset(true)
        }
    },
stop:function(){
    if(this.anim&&this.anim.isAnimated&&this.anim.isAnimated()){
        this.anim.stop()
        }
    },
show:function(){
    this.proxy.show();
    this.proxy.toFront()
    },
sync:function(){
    this.proxy.el.sync()
    },
repair:function(b,c,a){
    this.callback=c;
    this.scope=a;
    if(b&&this.animRepair!==false){
        this.el.addCls(Ext.baseCSSPrefix+"dd-drag-repair");
        this.el.hideUnders(true);
        this.anim=this.el.animate({
            duration:this.repairDuration||500,
            easing:"ease-out",
            to:{
                x:b[0],
                y:b[1]
                },
            stopAnimation:true,
            callback:this.afterRepair,
            scope:this
        })
        }else{
        this.afterRepair()
        }
    },
afterRepair:function(){
    this.hide(true);
    if(typeof this.callback=="function"){
        this.callback.call(this.scope||this)
        }
        this.callback=null;
    this.scope=null
    },
destroy:function(){
    Ext.destroy(this.ghost,this.proxy,this.el)
    }
});
Ext.define("Ext.data.Operation",{
    synchronous:true,
    action:undefined,
    filters:undefined,
    sorters:undefined,
    group:undefined,
    start:undefined,
    limit:undefined,
    batch:undefined,
    started:false,
    running:false,
    complete:false,
    success:undefined,
    exception:false,
    error:undefined,
    constructor:function(a){
        Ext.apply(this,a||{})
        },
    setStarted:function(){
        this.started=true;
        this.running=true
        },
    setCompleted:function(){
        this.complete=true;
        this.running=false
        },
    setSuccessful:function(){
        this.success=true
        },
    setException:function(a){
        this.exception=true;
        this.success=false;
        this.running=false;
        this.error=a
        },
    hasException:function(){
        return this.exception===true
        },
    getError:function(){
        return this.error
        },
    getRecords:function(){
        var a=this.getResultSet();
        return(a===undefined?this.records:a.records)
        },
    getResultSet:function(){
        return this.resultSet
        },
    isStarted:function(){
        return this.started===true
        },
    isRunning:function(){
        return this.running===true
        },
    isComplete:function(){
        return this.complete===true
        },
    wasSuccessful:function(){
        return this.isComplete()&&this.success===true
        },
    setBatch:function(a){
        this.batch=a
        },
    allowWrite:function(){
        return this.action!="read"
        }
    });
Ext.define("Ext.util.Filter",{
    anyMatch:false,
    exactMatch:false,
    caseSensitive:false,
    constructor:function(a){
        Ext.apply(this,a);
        this.filter=this.filter||this.filterFn;
        if(this.filter==undefined){
            if(this.property==undefined||this.value==undefined){}else{
                this.filter=this.createFilterFn()
                }
                this.filterFn=this.filter
            }
        },
createFilterFn:function(){
    var a=this,c=a.createValueMatcher(),b=a.property;
    return function(d){
        return c.test(a.getRoot.call(a,d)[b])
        }
    },
getRoot:function(a){
    return this.root==undefined?a:a[this.root]
    },
createValueMatcher:function(){
    var d=this,e=d.value,f=d.anyMatch,c=d.exactMatch,a=d.caseSensitive,b=Ext.String.escapeRegex;
    if(!e.exec){
        e=String(e);
        if(f===true){
            e=b(e)
            }else{
            e="^"+b(e);
            if(c===true){
                e+="$"
                }
            }
        e=new RegExp(e,a?"":"i")
    }
    return e
}
});
Ext.define("Ext.data.Association",{
    primaryKey:"id",
    defaultReaderType:"json",
    statics:{
        create:function(a){
            if(!a.isAssociation){
                if(Ext.isString(a)){
                    a={
                        type:a
                    }
                }
                switch(a.type){
                case"belongsTo":
                    return Ext.create("Ext.data.BelongsToAssociation",a);
                case"hasMany":
                    return Ext.create("Ext.data.HasManyAssociation",a);default:
            }
        }
        return a
    }
},
constructor:function(b){
    Ext.apply(this,b);
    var c=Ext.ModelManager.types,d=b.ownerModel,f=b.associatedModel,e=c[d],g=c[f],a;
    this.ownerModel=e;
    this.associatedModel=g;
    Ext.applyIf(this,{
        ownerName:d,
        associatedName:f
    })
    },
getReader:function(){
    var c=this,a=c.reader,b=c.associatedModel;
    if(a){
        if(Ext.isString(a)){
            a={
                type:a
            }
        }
        if(a.isReader){
        a.setModel(b)
        }else{
        Ext.applyIf(a,{
            model:b,
            type:c.defaultReaderType
            })
        }
        c.reader=Ext.createByAlias("reader."+a.type,a)
    }
    return c.reader||null
}
});
Ext.define("Ext.data.validations",{
    singleton:true,
    presenceMessage:"must be present",
    lengthMessage:"is the wrong length",
    formatMessage:"is the wrong format",
    inclusionMessage:"is not included in the list of acceptable values",
    exclusionMessage:"is not an acceptable value",
    presence:function(a,b){
        if(b===undefined){
            b=a
            }
            return !!b
        },
    length:function(b,e){
        if(e===undefined){
            return false
            }
            var d=e.length,c=b.min,a=b.max;
        if((c&&d<c)||(a&&d>a)){
            return false
            }else{
            return true
            }
        },
format:function(a,b){
    return !!(a.matcher&&a.matcher.test(b))
    },
inclusion:function(a,b){
    return a.list&&Ext.Array.indexOf(a.list,b)!=-1
    },
exclusion:function(a,b){
    return a.list&&Ext.Array.indexOf(a.list,b)==-1
    }
});
Ext.define("Ext.util.Sorter",{
    direction:"ASC",
    constructor:function(a){
        var b=this;
        Ext.apply(b,a);
        b.updateSortFunction()
        },
    createSortFunction:function(b){
        var c=this,d=c.property,e=c.direction||"ASC",a=e.toUpperCase()=="DESC"?-1:1;
        return function(g,f){
            return a*b.call(c,g,f)
            }
        },
defaultSorterFn:function(d,c){
    var b=this,a=b.transform,f=b.getRoot(d)[b.property],e=b.getRoot(c)[b.property];
    if(a){
        f=a(f);
        e=a(e)
        }
        return f>e?1:(f<e?-1:0)
    },
getRoot:function(a){
    return this.root===undefined?a:a[this.root]
    },
setDirection:function(b){
    var a=this;
    a.direction=b;
    a.updateSortFunction()
    },
toggle:function(){
    var a=this;
    a.direction=Ext.String.toggle(a.direction,"ASC","DESC");
    a.updateSortFunction()
    },
updateSortFunction:function(a){
    var b=this;
    a=a||b.sorterFn||b.defaultSorterFn;
    b.sort=b.createSortFunction(a)
    }
});
Ext.define("Ext.layout.component.Draw",{
    alias:"layout.draw",
    extend:"Ext.layout.component.Auto",
    type:"draw",
    onLayout:function(b,a){
        this.owner.surface.setSize(b,a);
        this.callParent(arguments)
        }
    });
Ext.define("Ext.state.Provider",{
    mixins:{
        observable:"Ext.util.Observable"
    },
    prefix:"ext-",
    constructor:function(a){
        a=a||{};
        
        var b=this;
        Ext.apply(b,a);
        b.addEvents("statechange");
        b.state={};
        
        b.mixins.observable.constructor.call(b)
        },
    get:function(b,a){
        return typeof this.state[b]=="undefined"?a:this.state[b]
        },
    clear:function(a){
        var b=this;
        delete b.state[a];
        b.fireEvent("statechange",b,a,null)
        },
    set:function(a,c){
        var b=this;
        b.state[a]=c;
        b.fireEvent("statechange",b,a,c)
        },
    decodeValue:function(f){
        var d=this,c=/^(a|n|d|b|s|o|e)\:(.*)$/,e=c.exec(unescape(f)),b,a,f,g;
        if(!e||!e[1]){
            return
        }
        a=e[1];
        f=e[2];
        switch(a){
            case"e":
                return null;
            case"n":
                return parseFloat(f);
            case"d":
                return new Date(Date.parse(f));
            case"b":
                return(f=="1");
            case"a":
                b=[];
                if(f!=""){
                Ext.each(f.split("^"),function(h){
                    b.push(d.decodeValue(h))
                    },d)
                }
                return b;
            case"o":
                b={};
                
                if(f!=""){
                Ext.each(f.split("^"),function(h){
                    g=h.split("=");
                    b[g[0]]=d.decodeValue(g[1])
                    },d)
                }
                return b;
            default:
                return f
                }
            },
encodeValue:function(e){
    var f="",d=0,b,a,c;
    if(e==null){
        return"e:1"
        }else{
        if(typeof e=="number"){
            b="n:"+e
            }else{
            if(typeof e=="boolean"){
                b="b:"+(e?"1":"0")
                }else{
                if(Ext.isDate(e)){
                    b="d:"+e.toGMTString()
                    }else{
                    if(Ext.isArray(e)){
                        for(a=e.length;d<a;d++){
                            f+=this.encodeValue(e[d]);
                            if(d!=a-1){
                                f+="^"
                                }
                            }
                        b="a:"+f
                    }else{
                    if(typeof e=="object"){
                        for(c in e){
                            if(typeof e[c]!="function"&&e[c]!==undefined){
                                f+=c+"="+this.encodeValue(e[c])+"^"
                                }
                            }
                        b="o:"+f.substring(0,f.length-1)
                    }else{
                    b="s:"+e
                    }
                }
        }
}
}
}
return escape(b)
}
});
Ext.define("Ext.util.KeyNav",{
    alternateClassName:"Ext.KeyNav",
    requires:["Ext.util.KeyMap"],
    statics:{
        keyOptions:{
            left:37,
            right:39,
            up:38,
            down:40,
            space:32,
            pageUp:33,
            pageDown:34,
            del:46,
            backspace:8,
            home:36,
            end:35,
            enter:13,
            esc:27,
            tab:9
        }
    },
constructor:function(b,a){
    this.setConfig(b,a||{})
    },
setConfig:function(d,a){
    if(this.map){
        this.map.destroy()
        }
        var f=Ext.create("Ext.util.KeyMap",d,null,this.getKeyEvent("forceKeyDown" in a?a.forceKeyDown:this.forceKeyDown)),e=Ext.util.KeyNav.keyOptions,c=a.scope||this,b;
    this.map=f;
    for(b in e){
        if(e.hasOwnProperty(b)){
            if(a[b]){
                f.addBinding({
                    scope:c,
                    key:e[b],
                    handler:Ext.Function.bind(this.handleEvent,c,[a[b]],true),
                    defaultEventAction:a.defaultEventAction||this.defaultEventAction
                    })
                }
            }
    }
    f.disable();
    if(!a.disabled){
    f.enable()
    }
},
handleEvent:function(c,b,a){
    return a.call(this,b)
    },
disabled:false,
defaultEventAction:"stopEvent",
forceKeyDown:false,
destroy:function(a){
    this.map.destroy(a);
    delete this.map
    },
enable:function(){
    this.map.enable();
    this.disabled=false
    },
disable:function(){
    this.map.disable();
    this.disabled=true
    },
setDisabled:function(a){
    this.map.setDisabled(a);
    this.disabled=a
    },
getKeyEvent:function(a){
    return(a||Ext.EventManager.useKeyDown)?"keydown":"keypress"
    }
});
Ext.define("Ext.data.SortTypes",{
    singleton:true,
    none:function(a){
        return a
        },
    stripTagsRE:/<\/?[^>]+>/gi,
    asText:function(a){
        return String(a).replace(this.stripTagsRE,"")
        },
    asUCText:function(a){
        return String(a).toUpperCase().replace(this.stripTagsRE,"")
        },
    asUCString:function(a){
        return String(a).toUpperCase()
        },
    asDate:function(a){
        if(!a){
            return 0
            }
            if(Ext.isDate(a)){
            return a.getTime()
            }
            return Date.parse(String(a))
        },
    asFloat:function(a){
        var b=parseFloat(String(a).replace(/,/g,""));
        return isNaN(b)?0:b
        },
    asInt:function(a){
        var b=parseInt(String(a).replace(/,/g,""),10);
        return isNaN(b)?0:b
        }
    });
Ext.define("Ext.data.Connection",{
    mixins:{
        observable:"Ext.util.Observable"
    },
    statics:{
        requestId:0
    },
    url:null,
    async:true,
    method:null,
    username:"",
    password:"",
    disableCaching:true,
    disableCachingParam:"_dc",
    timeout:30000,
    useDefaultHeader:true,
    defaultPostHeader:"application/x-www-form-urlencoded; charset=UTF-8",
    useDefaultXhrHeader:true,
    defaultXhrHeader:"XMLHttpRequest",
    constructor:function(a){
        a=a||{};
        
        Ext.apply(this,a);
        this.addEvents("beforerequest","requestcomplete","requestexception");
        this.requests={};
        
        this.mixins.observable.constructor.call(this)
        },
    request:function(j){
        j=j||{};
        
        var f=this,i=j.scope||window,e=j.username||f.username,g=j.password||f.password||"",b,c,d,a,h;
        if(f.fireEvent("beforerequest",f,j)!==false){
            c=f.setOptions(j,i);
            if(this.isFormUpload(j)===true){
                this.upload(j.form,c.url,c.data,j);
                return null
                }
                if(j.autoAbort===true||f.autoAbort){
                f.abort()
                }
                h=this.getXhrInstance();
            b=j.async!==false?(j.async||f.async):false;
            if(e){
                h.open(c.method,c.url,b,e,g)
                }else{
                h.open(c.method,c.url,b)
                }
                a=f.setupHeaders(h,j,c.data,c.params);
            d={
                id:++Ext.data.Connection.requestId,
                xhr:h,
                headers:a,
                options:j,
                async:b,
                timeout:setTimeout(function(){
                    d.timedout=true;
                    f.abort(d)
                    },j.timeout||f.timeout)
                };
                
            f.requests[d.id]=d;
            if(b){
                h.onreadystatechange=Ext.Function.bind(f.onStateChange,f,[d])
                }
                h.send(c.data);
            if(!b){
                return this.onComplete(d)
                }
                return d
            }else{
            Ext.callback(j.callback,j.scope,[j,undefined,undefined]);
            return null
            }
        },
upload:function(d,b,h,j){
    d=Ext.getDom(d);
    j=j||{};
    
    var c=Ext.id(),f=document.createElement("iframe"),i=[],g="multipart/form-data",e={
        target:d.target,
        method:d.method,
        encoding:d.encoding,
        enctype:d.enctype,
        action:d.action
        },a;
    Ext.fly(f).set({
        id:c,
        name:c,
        cls:Ext.baseCSSPrefix+"hide-display",
        src:Ext.SSL_SECURE_URL
        });
    document.body.appendChild(f);
    if(document.frames){
        document.frames[c].name=c
        }
        Ext.fly(d).set({
        target:c,
        method:"POST",
        enctype:g,
        encoding:g,
        action:b||e.action
        });
    if(h){
        Ext.iterate(Ext.Object.fromQueryString(h),function(k,l){
            a=document.createElement("input");
            Ext.fly(a).set({
                type:"hidden",
                value:l,
                name:k
            });
            d.appendChild(a);
            i.push(a)
            })
        }
        Ext.fly(f).on("load",Ext.Function.bind(this.onUploadComplete,this,[f,j]),null,{
        single:true
    });
    d.submit();
    Ext.fly(d).set(e);
    Ext.each(i,function(k){
        Ext.removeNode(k)
        })
    },
onUploadComplete:function(h,b){
    var c=this,a={
        responseText:"",
        responseXML:null
    },g,f;
    try{
        g=h.contentWindow.document||h.contentDocument||window.frames[id].document;
        if(g){
            if(g.body){
                if(/textarea/i.test((f=g.body.firstChild||{}).tagName)){
                    a.responseText=f.value
                    }else{
                    a.responseText=g.body.innerHTML
                    }
                }
            a.responseXML=g.XMLDocument||g
        }
    }catch(d){}
    c.fireEvent("requestcomplete",c,a,b);
    Ext.callback(b.success,b.scope,[a,b]);
    Ext.callback(b.callback,b.scope,[b,true,a]);
    setTimeout(function(){
    Ext.removeNode(h)
    },100)
},
isFormUpload:function(a){
    var b=this.getForm(a);
    if(b){
        return(a.isUpload||(/multipart\/form-data/i).test(b.getAttribute("enctype")))
        }
        return false
    },
getForm:function(a){
    return Ext.getDom(a.form)||null
    },
setOptions:function(k,j){
    var h=this,e=k.params||{},g=h.extraParams,d=k.urlParams,c=k.url||h.url,i=k.jsonData,b,a,f;
    if(Ext.isFunction(e)){
        e=e.call(j,k)
        }
        if(Ext.isFunction(c)){
        c=c.call(j,k)
        }
        c=this.setupUrl(k,c);
    f=k.rawData||k.xmlData||i||null;
    if(i&&!Ext.isPrimitive(i)){
        f=Ext.encode(f)
        }
        if(Ext.isObject(e)){
        e=Ext.Object.toQueryString(e)
        }
        if(Ext.isObject(g)){
        g=Ext.Object.toQueryString(g)
        }
        e=e+((g)?((e)?"&":"")+g:"");
    d=Ext.isObject(d)?Ext.Object.toQueryString(d):d;
    e=this.setupParams(k,e);
    b=(k.method||h.method||((e||f)?"POST":"GET")).toUpperCase();
    this.setupMethod(k,b);
    a=k.disableCaching!==false?(k.disableCaching||h.disableCaching):false;
    if(b==="GET"&&a){
        c=Ext.urlAppend(c,(k.disableCachingParam||h.disableCachingParam)+"="+(new Date().getTime()))
        }
        if((b=="GET"||f)&&e){
        c=Ext.urlAppend(c,e);
        e=null
        }
        if(d){
        c=Ext.urlAppend(c,d)
        }
        return{
        url:c,
        method:b,
        data:f||e||null
        }
    },
setupUrl:function(b,a){
    var c=this.getForm(b);
    if(c){
        a=a||c.action
        }
        return a
    },
setupParams:function(a,d){
    var c=this.getForm(a),b;
    if(c&&!this.isFormUpload(a)){
        b=Ext.core.Element.serializeForm(c);
        d=d?(d+"&"+b):b
        }
        return d
    },
setupMethod:function(a,b){
    if(this.isFormUpload(a)){
        return"POST"
        }
        return b
    },
setupHeaders:function(l,m,d,c){
    var h=this,b=Ext.apply({},m.headers||{},h.defaultHeaders||{}),k=h.defaultPostHeader,i=m.jsonData,a=m.xmlData,j,f;
    if(!b["Content-Type"]&&(d||c)){
        if(d){
            if(m.rawData){
                k="text/plain"
                }else{
                if(a&&Ext.isDefined(a)){
                    k="text/xml"
                    }else{
                    if(i&&Ext.isDefined(i)){
                        k="application/json"
                        }
                    }
            }
    }
b["Content-Type"]=k
}
if(h.useDefaultXhrHeader&&!b["X-Requested-With"]){
    b["X-Requested-With"]=h.defaultXhrHeader
    }
    try{
    for(j in b){
        if(b.hasOwnProperty(j)){
            f=b[j];
            l.setRequestHeader(j,f)
            }
        }
    }catch(g){
    h.fireEvent("exception",j,f)
    }
    return b
},
getXhrInstance:(function(){
    var b=[function(){
        return new XMLHttpRequest()
        },function(){
        return new ActiveXObject("MSXML2.XMLHTTP.3.0")
        },function(){
        return new ActiveXObject("MSXML2.XMLHTTP")
        },function(){
        return new ActiveXObject("Microsoft.XMLHTTP")
        }],c=0,a=b.length,f;
    for(;c<a;++c){
        try{
            f=b[c];
            f();
            break
        }catch(d){}
    }
    return f
})(),
isLoading:function(a){
    if(!(a&&a.xhr)){
        return false
        }
        var b=a.xhr.readyState;
    return !(b===0||b==4)
    },
abort:function(b){
    var a=this,d=a.requests,c;
    if(b&&a.isLoading(b)){
        b.xhr.onreadystatechange=null;
        b.xhr.abort();
        a.clearTimeout(b);
        if(!b.timedout){
            b.aborted=true
            }
            a.onComplete(b);
        a.cleanup(b)
        }else{
        if(!b){
            for(c in d){
                if(d.hasOwnProperty(c)){
                    a.abort(d[c])
                    }
                }
            }
        }
},
onStateChange:function(a){
    if(a.xhr.readyState==4){
        this.clearTimeout(a);
        this.onComplete(a);
        this.cleanup(a)
        }
    },
clearTimeout:function(a){
    clearTimeout(a.timeout);
    delete a.timeout
    },
cleanup:function(a){
    a.xhr=null;
    delete a.xhr
    },
onComplete:function(f){
    var d=this,c=f.options,a,h,b;
    try{
        a=d.parseStatus(f.xhr.status)
        }catch(g){
        a={
            success:false,
            isException:false
        }
    }
    h=a.success;
if(h){
    b=d.createResponse(f);
    d.fireEvent("requestcomplete",d,b,c);
    Ext.callback(c.success,c.scope,[b,c])
    }else{
    if(a.isException||f.aborted||f.timedout){
        b=d.createException(f)
        }else{
        b=d.createResponse(f)
        }
        d.fireEvent("requestexception",d,b,c);
    Ext.callback(c.failure,c.scope,[b,c])
    }
    Ext.callback(c.callback,c.scope,[c,h,b]);
delete d.requests[f.id];
return b
},
parseStatus:function(a){
    a=a==1223?204:a;
    var c=(a>=200&&a<300)||a==304,b=false;
    if(!c){
        switch(a){
            case 12002:case 12029:case 12030:case 12031:case 12152:case 13030:
                b=true;
                break
                }
            }
    return{
    success:c,
    isException:b
}
},
createResponse:function(c){
    var h=c.xhr,a={},i=h.getAllResponseHeaders().replace(/\r\n/g,"\n").split("\n"),d=i.length,j,e,g,f,b;
    while(d--){
        j=i[d];
        e=j.indexOf(":");
        if(e>=0){
            g=j.substr(0,e).toLowerCase();
            if(j.charAt(e+1)==" "){
                ++e
                }
                a[g]=j.substr(e+1)
            }
        }
    c.xhr=null;
delete c.xhr;
b={
    request:c,
    requestId:c.id,
    status:h.status,
    statusText:h.statusText,
    getResponseHeader:function(k){
        return a[k.toLowerCase()]
        },
    getAllResponseHeaders:function(){
        return a
        },
    responseText:h.responseText,
    responseXML:h.responseXML
    };
    
h=null;
return b
},
createException:function(a){
    return{
        request:a,
        requestId:a.id,
        status:a.aborted?-1:0,
        statusText:a.aborted?"transaction aborted":"communication failure",
        aborted:a.aborted,
        timedout:a.timedout
        }
    }
});
Ext.define("Ext.data.writer.Writer",{
    alias:"writer.base",
    alternateClassName:["Ext.data.DataWriter","Ext.data.Writer"],
    writeAllFields:true,
    nameProperty:"name",
    constructor:function(a){
        Ext.apply(this,a)
        },
    write:function(e){
        var c=e.operation,b=c.records||[],a=b.length,d=0,f=[];
        for(;d<a;d++){
            f.push(this.getRecordData(b[d]))
            }
            return this.writeRecords(e,f)
        },
    getRecordData:function(e){
        var i=e.phantom===true,b=this.writeAllFields||i,c=this.nameProperty,f=e.fields,d={},h,a,g,j;
        if(b){
            f.each(function(k){
                if(k.persist){
                    a=k[c]||k.name;
                    d[a]=e.get(k.name)
                    }
                })
        }else{
        h=e.getChanges();
        for(j in h){
            if(h.hasOwnProperty(j)){
                g=f.get(j);
                a=g[c]||g.name;
                d[a]=h[j]
                }
            }
        if(!i){
        d[e.idProperty]=e.getId()
        }
    }
return d
}
});
Ext.define("Ext.data.ResultSet",{
    loaded:true,
    count:0,
    total:0,
    success:false,
    constructor:function(a){
        Ext.apply(this,a);
        this.totalRecords=this.total;
        if(a.count===undefined){
            this.count=this.records.length
            }
        }
});
Ext.define("Ext.layout.container.AbstractContainer",{
    extend:"Ext.layout.Layout",
    type:"container",
    bindToOwnerCtComponent:false,
    bindToOwnerCtContainer:false,
    setItemSize:function(c,b,a){
        if(Ext.isObject(b)){
            a=b.height;
            b=b.width
            }
            c.setCalculatedSize(b,a,this.owner)
        },
    getLayoutItems:function(){
        return this.owner&&this.owner.items&&this.owner.items.items||[]
        },
    afterLayout:function(){
        this.owner.afterLayout(this)
        },
    getTarget:function(){
        return this.owner.getTargetEl()
        },
    getRenderTarget:function(){
        return this.owner.getTargetEl()
        }
    });
Ext.define("Ext.layout.container.Container",{
    extend:"Ext.layout.container.AbstractContainer",
    alternateClassName:"Ext.layout.ContainerLayout",
    layoutItem:function(b,a){
        if(a){
            b.doComponentLayout(a.width,a.height)
            }else{
            b.doComponentLayout()
            }
        },
getLayoutTargetSize:function(){
    var b=this.getTarget(),a;
    if(b){
        a=b.getViewSize();
        if(Ext.isIE&&a.width==0){
            a=b.getStyleSize()
            }
            a.width-=b.getPadding("lr");
        a.height-=b.getPadding("tb")
        }
        return a
    },
beforeLayout:function(){
    if(this.owner.beforeLayout(arguments)!==false){
        return this.callParent(arguments)
        }else{
        return false
        }
    },
getRenderedItems:function(){
    var e=this,g=e.getTarget(),a=e.getLayoutItems(),d=a.length,f=[],b,c;
    for(b=0;b<d;b++){
        c=a[b];
        if(c.rendered&&e.isValidParent(c,g,b)){
            f.push(c)
            }
        }
    return f
},
getVisibleItems:function(){
    var f=this.getTarget(),b=this.getLayoutItems(),e=b.length,a=[],c,d;
    for(c=0;c<e;c++){
        d=b[c];
        if(d.rendered&&this.isValidParent(d,f,c)&&d.hidden!==true){
            a.push(d)
            }
        }
    return a
}
});
Ext.define("Ext.layout.container.AbstractFit",{
    extend:"Ext.layout.container.Container",
    itemCls:Ext.baseCSSPrefix+"fit-item",
    targetCls:Ext.baseCSSPrefix+"layout-fit",
    type:"fit"
});
Ext.define("Ext.layout.container.Auto",{
    alias:["layout.auto","layout.autocontainer"],
    extend:"Ext.layout.container.Container",
    type:"autocontainer",
    bindToOwnerCtComponent:true,
    onLayout:function(a,f){
        var e=this,b=e.getLayoutItems(),d=b.length,c;
        if(d){
            if(!e.clearEl){
                e.clearEl=e.getRenderTarget().createChild({
                    cls:Ext.baseCSSPrefix+"clear",
                    role:"presentation"
                })
                }
                for(c=0;c<d;c++){
                e.setItemSize(b[c])
                }
            }
        },
configureItem:function(a){
    this.callParent(arguments);
    a.layoutManagedHeight=2;
    a.layoutManagedWidth=2
    }
});
Ext.define("Ext.layout.container.Anchor",{
    alias:"layout.anchor",
    extend:"Ext.layout.container.Container",
    alternateClassName:"Ext.layout.AnchorLayout",
    type:"anchor",
    defaultAnchor:"100%",
    parseAnchorRE:/^(r|right|b|bottom)$/i,
    onLayout:function(){
        this.callParent(arguments);
        var r=this,l=r.getLayoutTargetSize(),a=r.owner,t=r.getTarget(),s=l.width,j=l.height,m=t.getStyle("overflow"),k=r.getVisibleItems(a),p=k.length,e=[],g,n,h,f,c,d,o,b,q;
        if(s<20&&j<20){
            return
        }
        if(!r.clearEl){
            r.clearEl=t.createChild({
                cls:Ext.baseCSSPrefix+"clear",
                role:"presentation"
            })
            }
            if(!Ext.supports.RightMargin){
            q=Ext.core.Element.getRightMarginFixCleaner(t);
            t.addCls(Ext.baseCSSPrefix+"inline-children")
            }
            for(o=0;o<p;o++){
            h=k[o];
            b=h.el;
            f=h.anchorSpec;
            if(f){
                if(f.right){
                    c=r.adjustWidthAnchor(f.right(s)-b.getMargin("lr"),h)
                    }else{
                    c=undefined
                    }
                    if(f.bottom){
                    d=r.adjustHeightAnchor(f.bottom(j)-b.getMargin("tb"),h)
                    }else{
                    d=undefined
                    }
                    e.push({
                    component:h,
                    anchor:true,
                    width:c||undefined,
                    height:d||undefined
                    })
                }else{
                e.push({
                    component:h,
                    anchor:false
                })
                }
            }
        if(!Ext.supports.RightMargin){
        t.removeCls(Ext.baseCSSPrefix+"inline-children");
        q()
        }
        for(o=0;o<p;o++){
        g=e[o];
        r.setItemSize(g.component,g.width,g.height)
        }
        if(m&&m!="hidden"&&!r.adjustmentPass){
        n=r.getLayoutTargetSize();
        if(n.width!=l.width||n.height!=l.height){
            r.adjustmentPass=true;
            r.onLayout()
            }
        }
    delete r.adjustmentPass
},
parseAnchor:function(c,f,b){
    if(c&&c!="none"){
        var d;
        if(this.parseAnchorRE.test(c)){
            var e=b-f;
            return function(a){
                return a-e
                }
            }else{
        if(c.indexOf("%")!=-1){
            d=parseFloat(c.replace("%",""))*0.01;
            return function(a){
                return Math.floor(a*d)
                }
            }else{
        c=parseInt(c,10);
        if(!isNaN(c)){
            return function(a){
                return a+c
                }
            }
    }
}
}
return null
},
adjustWidthAnchor:function(b,a){
    return b
    },
adjustHeightAnchor:function(b,a){
    return b
    },
configureItem:function(f){
    var e=this,a=e.owner,d=f.anchor,b,h,c,g;
    if(!f.anchor&&f.items&&!Ext.isNumber(f.width)&&!(Ext.isIE6&&Ext.isStrict)){
        f.anchor=d=e.defaultAnchor
        }
        if(a.anchorSize){
        if(typeof a.anchorSize=="number"){
            c=a.anchorSize
            }else{
            c=a.anchorSize.width;
            g=a.anchorSize.height
            }
        }else{
    c=a.initialConfig.width;
    g=a.initialConfig.height
    }
    if(d){
    b=d.split(" ");
    f.anchorSpec=h={
        right:e.parseAnchor(b[0],f.initialConfig.width,c),
        bottom:e.parseAnchor(b[1],f.initialConfig.height,g)
        };
        
    if(h.right){
        f.layoutManagedWidth=1
        }else{
        f.layoutManagedWidth=2
        }
        if(h.bottom){
        f.layoutManagedHeight=1
        }else{
        f.layoutManagedHeight=2
        }
    }else{
    f.layoutManagedWidth=2;
    f.layoutManagedHeight=2
    }
    this.callParent(arguments)
}
});
Ext.define("Ext.layout.container.CheckboxGroup",{
    extend:"Ext.layout.container.Container",
    alias:["layout.checkboxgroup"],
    onLayout:function(){
        var b=this.getColCount(),c=this.getShadowCt(),a=this.owner,f=a.items,h=c.items,g=f.length,j=0,d,e;
        h.each(function(i){
            i.items.clear()
            });
        while(h.length>b){
            c.remove(h.last())
            }while(h.length<b){
            c.add({
                xtype:"container",
                cls:a.groupCls,
                flex:1
            })
            }
            if(a.vertical){
            e=Math.ceil(g/b);
            for(d=0;d<g;d++){
                if(d>0&&d%e===0){
                    j++
                }
                h.getAt(j).items.add(f.getAt(d))
                }
            }else{
        for(d=0;d<g;d++){
            j=d%b;
            h.getAt(j).items.add(f.getAt(d))
            }
        }
        if(!c.rendered){
    c.render(this.getRenderTarget())
    }else{
    h.each(function(i){
        var k=i.getLayout();
        k.renderItems(k.getLayoutItems(),k.getRenderTarget())
        })
    }
    c.doComponentLayout()
    },
renderItems:Ext.emptyFn,
getShadowCt:function(){
    var h=this,d=h.shadowCt,a,g,j,c,f,b,e;
    if(!d){
        a=h.owner;
        c=a.columns;
        f=Ext.isArray(c);
        b=h.getColCount();
        g=[];
        for(e=0;e<b;e++){
            j={
                xtype:"container",
                cls:a.groupCls
                };
                
            if(f){
                if(c[e]<1){
                    j.flex=c[e]
                    }else{
                    j.width=c[e]
                    }
                }else{
            j.flex=1
            }
            g.push(j)
            }
            d=h.shadowCt=Ext.createWidget("container",{
        layout:"hbox",
        items:g,
        ownerCt:a
    })
    }
    return d
},
getColCount:function(){
    var a=this.owner,b=a.columns;
    return Ext.isArray(b)?b.length:(Ext.isNumber(b)?b:a.items.length)
    }
});
Ext.define("Ext.layout.container.Column",{
    extend:"Ext.layout.container.Auto",
    alias:["layout.column"],
    alternateClassName:"Ext.layout.ColumnLayout",
    type:"column",
    itemCls:Ext.baseCSSPrefix+"column",
    targetCls:Ext.baseCSSPrefix+"column-layout-ct",
    scrollOffset:0,
    bindToOwnerCtComponent:false,
    getRenderTarget:function(){
        if(!this.innerCt){
            this.innerCt=this.getTarget().createChild({
                cls:Ext.baseCSSPrefix+"column-inner"
                });
            this.clearEl=this.innerCt.createChild({
                cls:Ext.baseCSSPrefix+"clear",
                role:"presentation"
            })
            }
            return this.innerCt
        },
    onLayout:function(){
        var g=this,e=g.getTarget(),f=g.getLayoutItems(),d=f.length,j,c,k=[],b,l,h,a;
        l=g.getLayoutTargetSize();
        if(l.width<d*10){
            return
        }
        if(g.adjustmentPass){
            if(Ext.isIE6||Ext.isIE7||Ext.isIEQuirks){
                l.width=g.adjustedWidth
                }
            }else{
        c=e.getStyle("overflow");
        if(c&&c!="hidden"){
            g.autoScroll=true;
            if(!(Ext.isIE6||Ext.isIE7||Ext.isIEQuirks)){
                e.setStyle("overflow","hidden");
                l=g.getLayoutTargetSize()
                }
            }
    }
h=l.width-g.scrollOffset;
g.innerCt.setWidth(h);
    for(c=0;c<d;c++){
    j=f[c];
    b=k[c]=j.getEl().getMargin("lr");
    if(!j.columnWidth){
        h-=(j.getWidth()+b)
        }
    }
h=h<0?0:h;
for(c=0;c<d;c++){
    j=f[c];
    if(j.columnWidth){
        a=Math.floor(j.columnWidth*h)-k[c];
        g.setItemSize(j,a,j.height)
        }else{
        g.layoutItem(j)
        }
    }
if(!g.adjustmentPass&&g.autoScroll){
    e.setStyle("overflow","auto");
    g.adjustmentPass=(e.dom.scrollHeight>l.height);
    if(Ext.isIE6||Ext.isIE7||Ext.isIEQuirks){
        g.adjustedWidth=l.width-Ext.getScrollBarWidth()
        }else{
        e.setStyle("overflow","auto")
        }
        if(g.adjustmentPass){
        g.onLayout()
        }
    }
delete g.adjustmentPass
},
configureItem:function(a){
    this.callParent(arguments);
    if(a.columnWidth){
        a.layoutManagedWidth=1
        }
    }
});
Ext.define("Ext.layout.container.Fit",{
    extend:"Ext.layout.container.AbstractFit",
    alias:"layout.fit",
    alternateClassName:"Ext.layout.FitLayout",
    onLayout:function(){
        var a=this;
        a.callParent();
        if(a.owner.items.length){
            a.setItemBox(a.owner.items.get(0),a.getLayoutTargetSize())
            }
        },
getTargetBox:function(){
    return this.getLayoutTargetSize()
    },
setItemBox:function(c,b){
    var a=this;
    if(c&&b.height>0){
        if(!a.owner.isFixedWidth()){
            b.width=undefined
            }
            if(!a.owner.isFixedHeight()){
            b.height=undefined
            }
            a.setItemSize(c,b.width,b.height)
        }
    },
configureItem:function(a){
    a.layoutManagedHeight=0;
    a.layoutManagedWidth=0;
    this.callParent(arguments)
    }
});
Ext.define("Ext.layout.container.AbstractCard",{
    extend:"Ext.layout.container.Fit",
    type:"card",
    sizeAllCards:false,
    hideInactive:true,
    deferredRender:false,
    beforeLayout:function(){
        var a=this;
        a.getActiveItem();
        if(a.activeItem&&a.deferredRender){
            a.renderItems([a.activeItem],a.getRenderTarget());
            return true
            }else{
            return this.callParent(arguments)
            }
        },
renderChildren:function(){
    this.getActiveItem();
    this.callParent()
    },
onLayout:function(){
    var f=this,g=f.activeItem,b=f.getVisibleItems(),e=b.length,a=f.getTargetBox(),c,d;
    for(c=0;c<e;c++){
        d=b[c];
        f.setItemBox(d,a)
        }
        if(!f.firstActivated&&g){
        if(g.fireEvent("beforeactivate",g)!==false){
            g.fireEvent("activate",g)
            }
            f.firstActivated=true
        }
    },
isValidParent:function(c,d,a){
    var b=c.el?c.el.dom:Ext.getDom(c);
    return(b&&b.parentNode===(d.dom||d))||false
    },
getActiveItem:function(){
    var a=this;
    if(!a.activeItem&&a.owner){
        a.activeItem=a.parseActiveItem(a.owner.activeItem)
        }
        if(a.activeItem&&a.owner.items.indexOf(a.activeItem)!=-1){
        return a.activeItem
        }
        return null
    },
parseActiveItem:function(a){
    if(a&&a.isComponent){
        return a
        }else{
        if(typeof a=="number"||a===undefined){
            return this.getLayoutItems()[a||0]
            }else{
            return this.owner.getComponent(a)
            }
        }
},
configureItem:function(b,a){
    this.callParent([b,a]);
    if(this.hideInactive&&this.activeItem!==b){
        b.hide()
        }else{
        b.show()
        }
    },
onRemove:function(a){
    if(a===this.activeItem){
        this.activeItem=null;
        if(this.owner.items.getCount()===0){
            this.firstActivated=false
            }
        }
},
getAnimation:function(b,a){
    var c=(b||{}).cardSwitchAnimation;
    if(c===false){
        return false
        }
        return c||a.cardSwitchAnimation
    },
getNext:function(){
    var c=arguments[0];
    var a=this.getLayoutItems(),b=Ext.Array.indexOf(a,this.activeItem);
    return a[b+1]||(c?a[0]:false)
    },
next:function(){
    var b=arguments[0],a=arguments[1];
    return this.setActiveItem(this.getNext(a),b)
    },
getPrev:function(){
    var c=arguments[0];
    var a=this.getLayoutItems(),b=Ext.Array.indexOf(a,this.activeItem);
    return a[b-1]||(c?a[a.length-1]:false)
    },
prev:function(){
    var b=arguments[0],a=arguments[1];
    return this.setActiveItem(this.getPrev(a),b)
    }
});
Ext.define("Ext.layout.container.Card",{
    alias:["layout.card"],
    alternateClassName:"Ext.layout.CardLayout",
    extend:"Ext.layout.container.AbstractCard",
    setActiveItem:function(b){
        var e=this,a=e.owner,d=e.activeItem,c;
        b=e.parseActiveItem(b);
        c=a.items.indexOf(b);
        if(c==-1){
            c=a.items.items.length;
            a.add(b)
            }
            if(b&&d!=b){
            if(!b.rendered){
                e.renderItem(b,e.getRenderTarget(),a.items.length);
                e.configureItem(b,0)
                }
                e.activeItem=b;
            if(b.fireEvent("beforeactivate",b,d)===false){
                return false
                }
                if(d&&d.fireEvent("beforedeactivate",d,b)===false){
                return false
                }
                if(e.sizeAllCards){
                e.onLayout()
                }else{
                e.setItemBox(b,e.getTargetBox())
                }
                e.owner.suspendLayout=true;
            if(d){
                if(e.hideInactive){
                    d.hide()
                    }
                    d.fireEvent("deactivate",d,b)
                }
                e.owner.suspendLayout=false;
            if(b.hidden){
                b.show()
                }else{
                e.onLayout()
                }
                b.fireEvent("activate",b,d);
            return b
            }
            return false
        },
    configureItem:function(a){
        a.layoutManagedHeight=0;
        a.layoutManagedWidth=0;
        this.callParent(arguments)
        }
    });
Ext.define("Ext.layout.container.Table",{
    alias:["layout.table"],
    extend:"Ext.layout.container.Auto",
    alternateClassName:"Ext.layout.TableLayout",
    monitorResize:false,
    type:"table",
    autoSize:true,
    clearEl:true,
    targetCls:Ext.baseCSSPrefix+"table-layout-ct",
    tableCls:Ext.baseCSSPrefix+"table-layout",
    cellCls:Ext.baseCSSPrefix+"table-layout-cell",
    tableAttrs:null,
    renderItems:function(g){
        var e=this.getTable().tBodies[0],n=e.rows,d=0,f=g.length,m,k,c,a,l,j,h,b;
        m=this.calculateCells(g);
        for(;d<f;d++){
            k=m[d];
            c=k.rowIdx;
            a=k.cellIdx;
            l=g[d];
            j=n[c];
            if(!j){
                j=e.insertRow(c);
                if(this.trAttrs){
                    j.set(this.trAttrs)
                    }
                }
            b=h=Ext.get(j.cells[a]||j.insertCell(a));
            if(this.needsDivWrap()){
            b=h.first()||h.createChild({
                tag:"div"
            });
            b.setWidth(null)
            }
            if(!l.rendered){
            this.renderItem(l,b,0)
            }else{
            if(!this.isValidParent(l,b,0)){
                this.moveItem(l,b,0)
                }
            }
        if(this.tdAttrs){
            h.set(this.tdAttrs)
            }
            h.set({
            colSpan:l.colspan||1,
            rowSpan:l.rowspan||1,
            id:l.cellId||"",
            cls:this.cellCls+" "+(l.cellCls||"")
            });
        if(!m[d+1]||m[d+1].rowIdx!==c){
            a++;
            while(j.cells[a]){
                j.deleteCell(a)
                }
            }
    }
c++;
while(e.rows[c]){
    e.deleteRow(c)
    }
},
afterLayout:function(){
    this.callParent();
    if(this.needsDivWrap()){
        Ext.Array.forEach(this.getLayoutItems(),function(a){
            Ext.fly(a.el.dom.parentNode).setWidth(a.getWidth())
            })
        }
    },
calculateCells:function(h){
    var l=[],b=0,d=0,a=0,g=this.columns||Infinity,m=[],e=0,c,f=h.length,k;
    for(;e<f;e++){
        k=h[e];
        while(d>=g||m[d]>0){
            if(d>=g){
                d=0;
                a=0;
                b++;
                for(c=0;c<g;c++){
                    if(m[c]>0){
                        m[c]--
                    }
                }
                }else{
        d++
    }
    }
    l.push({
    rowIdx:b,
    cellIdx:a
});
for(c=k.colspan||1;c;--c){
    m[d]=k.rowspan||1;
    ++d
    }
    ++a
}
return l
},
getTable:function(){
    var a=this.table;
    if(!a){
        a=this.table=this.getTarget().createChild(Ext.apply({
            tag:"table",
            role:"presentation",
            cls:this.tableCls,
            cellspacing:0,
            cn:{
                tag:"tbody"
            }
        },this.tableAttrs),null,true)
    }
    return a
},
needsDivWrap:function(){
    return Ext.isOpera10_5
    }
});
Ext.define("Ext.layout.container.boxOverflow.Scroller",{
    extend:"Ext.layout.container.boxOverflow.None",
    requires:["Ext.util.ClickRepeater","Ext.core.Element"],
    alternateClassName:"Ext.layout.boxOverflow.Scroller",
    mixins:{
        observable:"Ext.util.Observable"
    },
    animateScroll:false,
    scrollIncrement:20,
    wheelIncrement:10,
    scrollRepeatInterval:60,
    scrollDuration:400,
    scrollerCls:Ext.baseCSSPrefix+"box-scroller",
    constructor:function(b,a){
        this.layout=b;
        Ext.apply(this,a||{});
        this.addEvents("scroll")
        },
    initCSSClasses:function(){
        var b=this,a=b.layout;
        if(!b.CSSinitialized){
            b.beforeCtCls=b.beforeCtCls||Ext.baseCSSPrefix+"box-scroller-"+a.parallelBefore;
            b.afterCtCls=b.afterCtCls||Ext.baseCSSPrefix+"box-scroller-"+a.parallelAfter;
            b.beforeScrollerCls=b.beforeScrollerCls||Ext.baseCSSPrefix+a.owner.getXType()+"-scroll-"+a.parallelBefore;
            b.afterScrollerCls=b.afterScrollerCls||Ext.baseCSSPrefix+a.owner.getXType()+"-scroll-"+a.parallelAfter;
            b.CSSinitializes=true
            }
        },
handleOverflow:function(a,f){
    var e=this,d=e.layout,c="get"+d.parallelPrefixCap,b={};
    
    e.initCSSClasses();
    e.callParent(arguments);
    this.createInnerElements();
    this.showScrollers();
    b[d.perpendicularPrefix]=f[d.perpendicularPrefix];
    b[d.parallelPrefix]=f[d.parallelPrefix]-(e.beforeCt[c]()+e.afterCt[c]());
    return{
        targetSize:b
    }
},
createInnerElements:function(){
    var a=this,b=a.layout.getRenderTarget();
    if(!a.beforeCt){
        b.addCls(Ext.baseCSSPrefix+a.layout.direction+"-box-overflow-body");
        a.beforeCt=b.insertSibling({
            cls:Ext.layout.container.Box.prototype.innerCls+" "+a.beforeCtCls
            },"before");
        a.afterCt=b.insertSibling({
            cls:Ext.layout.container.Box.prototype.innerCls+" "+a.afterCtCls
            },"after");
        a.createWheelListener()
        }
    },
createWheelListener:function(){
    this.layout.innerCt.on({
        scope:this,
        mousewheel:function(a){
            a.stopEvent();
            this.scrollBy(a.getWheelDelta()*this.wheelIncrement*-1,false)
            }
        })
},
clearOverflow:function(){
    this.hideScrollers()
    },
showScrollers:function(){
    this.createScrollers();
    this.beforeScroller.show();
    this.afterScroller.show();
    this.updateScrollButtons();
    this.layout.owner.addClsWithUI("scroller")
    },
hideScrollers:function(){
    if(this.beforeScroller!=undefined){
        this.beforeScroller.hide();
        this.afterScroller.hide();
        this.layout.owner.removeClsWithUI("scroller")
        }
    },
createScrollers:function(){
    if(!this.beforeScroller&&!this.afterScroller){
        var a=this.beforeCt.createChild({
            cls:Ext.String.format("{0} {1} ",this.scrollerCls,this.beforeScrollerCls)
            });
        var b=this.afterCt.createChild({
            cls:Ext.String.format("{0} {1}",this.scrollerCls,this.afterScrollerCls)
            });
        a.addClsOnOver(this.beforeScrollerCls+"-hover");
        b.addClsOnOver(this.afterScrollerCls+"-hover");
        a.setVisibilityMode(Ext.core.Element.DISPLAY);
        b.setVisibilityMode(Ext.core.Element.DISPLAY);
        this.beforeRepeater=Ext.create("Ext.util.ClickRepeater",a,{
            interval:this.scrollRepeatInterval,
            handler:this.scrollLeft,
            scope:this
        });
        this.afterRepeater=Ext.create("Ext.util.ClickRepeater",b,{
            interval:this.scrollRepeatInterval,
            handler:this.scrollRight,
            scope:this
        });
        this.beforeScroller=a;
        this.afterScroller=b
        }
    },
destroy:function(){
    Ext.destroy(this.beforeRepeater,this.afterRepeater,this.beforeScroller,this.afterScroller,this.beforeCt,this.afterCt)
    },
scrollBy:function(b,a){
    this.scrollTo(this.getScrollPosition()+b,a)
    },
getScrollAnim:function(){
    return{
        duration:this.scrollDuration,
        callback:this.updateScrollButtons,
        scope:this
    }
},
updateScrollButtons:function(){
    if(this.beforeScroller==undefined||this.afterScroller==undefined){
        return
    }
    var d=this.atExtremeBefore()?"addCls":"removeCls",c=this.atExtremeAfter()?"addCls":"removeCls",a=this.beforeScrollerCls+"-disabled",b=this.afterScrollerCls+"-disabled";
    this.beforeScroller[d](a);
    this.afterScroller[c](b);
    this.scrolling=false
    },
atExtremeBefore:function(){
    return this.getScrollPosition()===0
    },
scrollLeft:function(){
    this.scrollBy(-this.scrollIncrement,false)
    },
scrollRight:function(){
    this.scrollBy(this.scrollIncrement,false)
    },
getScrollPosition:function(){
    var a=this.layout;
    return parseInt(a.innerCt.dom["scroll"+a.parallelBeforeCap],10)||0
    },
getMaxScrollPosition:function(){
    var a=this.layout;
    return a.innerCt.dom["scroll"+a.parallelPrefixCap]-this.layout.innerCt["get"+a.parallelPrefixCap]()
    },
atExtremeAfter:function(){
    return this.getScrollPosition()>=this.getMaxScrollPosition()
    },
scrollTo:function(a,b){
    var f=this,e=f.layout,d=f.getScrollPosition(),c=Ext.Number.constrain(a,0,f.getMaxScrollPosition());
    if(c!=d&&!f.scrolling){
        if(b==undefined){
            b=f.animateScroll
            }
            e.innerCt.scrollTo(e.parallelBefore,c,b?f.getScrollAnim():false);
        if(b){
            f.scrolling=true
            }else{
            f.scrolling=false;
            f.updateScrollButtons()
            }
            f.fireEvent("scroll",f,c,b?f.getScrollAnim():false)
        }
    },
scrollToItem:function(g,b){
    var f=this,e=f.layout,a,d,c;
    g=f.getItem(g);
    if(g!=undefined){
        a=this.getItemVisibility(g);
        if(!a.fullyVisible){
            d=g.getBox(true,true);
            c=d[e.parallelPosition];
            if(a.hiddenEnd){
                c-=(this.layout.innerCt["get"+e.parallelPrefixCap]()-d[e.parallelPrefix])
                }
                this.scrollTo(c,b)
            }
        }
},
getItemVisibility:function(f){
    var e=this,d=e.getItem(f).getBox(true,true),c=e.layout,b=d[c.parallelPosition],g=b+d[c.parallelPrefix],h=e.getScrollPosition(),a=h+c.innerCt["get"+c.parallelPrefixCap]();
    return{
        hiddenStart:b<h,
        hiddenEnd:g>a,
        fullyVisible:b>h&&g<a
        }
    }
});
Ext.define("Ext.fx.target.Element",{
    extend:"Ext.fx.target.Target",
    type:"element",
    getElVal:function(b,a,c){
        if(c==undefined){
            if(a==="x"){
                c=b.getX()
                }else{
                if(a==="y"){
                    c=b.getY()
                    }else{
                    if(a==="scrollTop"){
                        c=b.getScroll().top
                        }else{
                        if(a==="scrollLeft"){
                            c=b.getScroll().left
                            }else{
                            if(a==="height"){
                                c=b.getHeight()
                                }else{
                                if(a==="width"){
                                    c=b.getWidth()
                                    }else{
                                    c=b.getStyle(a)
                                    }
                                }
                        }
                }
        }
}
}
return c
},
getAttr:function(a,c){
    var b=this.target;
    return[[b,this.getElVal(b,a,c)]]
    },
setAttr:function(k){
    var f=this.target,h=k.length,m,g,b,e,c,a,d,l;
    for(e=0;e<h;e++){
        m=k[e].attrs;
        for(g in m){
            if(m.hasOwnProperty(g)){
                a=m[g].length;
                for(c=0;c<a;c++){
                    b=m[g][c];
                    d=b[0];
                    l=b[1];
                    if(g==="x"){
                        d.setX(l)
                        }else{
                        if(g==="y"){
                            d.setY(l)
                            }else{
                            if(g==="scrollTop"){
                                d.scrollTo("top",l)
                                }else{
                                if(g==="scrollLeft"){
                                    d.scrollTo("left",l)
                                    }else{
                                    d.setStyle(g,l)
                                    }
                                }
                        }
                }
            }
        }
    }
}
}
});
Ext.define("Ext.fx.target.Component",{
    extend:"Ext.fx.target.Target",
    type:"component",
    getPropMethod:{
        top:function(){
            return this.getPosition(true)[1]
            },
        left:function(){
            return this.getPosition(true)[0]
            },
        x:function(){
            return this.getPosition()[0]
            },
        y:function(){
            return this.getPosition()[1]
            },
        height:function(){
            return this.getHeight()
            },
        width:function(){
            return this.getWidth()
            },
        opacity:function(){
            return this.el.getStyle("opacity")
            }
        },
compMethod:{
    top:"setPosition",
    left:"setPosition",
    x:"setPagePosition",
    y:"setPagePosition",
    height:"setSize",
    width:"setSize",
    opacity:"setOpacity"
},
getAttr:function(a,b){
    return[[this.target,b!==undefined?b:this.getPropMethod[a].call(this.target)]]
    },
setAttr:function(s,e,b){
    var q=this,l=q.target,p=s.length,u,m,a,f,d,n,k,c,r,t,g;
    for(f=0;f<p;f++){
        u=s[f].attrs;
        for(m in u){
            k=u[m].length;
            n={
                setPosition:{},
                setPagePosition:{},
                setSize:{},
                setOpacity:{}
        };
        
        for(d=0;d<k;d++){
            a=u[m][d];
            n[q.compMethod[m]].target=a[0];
            n[q.compMethod[m]][m]=a[1]
            }
            if(n.setPosition.target){
            a=n.setPosition;
            c=(a.left===undefined)?undefined:parseInt(a.left,10);
            r=(a.top===undefined)?undefined:parseInt(a.top,10);
            a.target.setPosition(c,r)
            }
            if(n.setPagePosition.target){
            a=n.setPagePosition;
            a.target.setPagePosition(a.x,a.y)
            }
            if(n.setSize.target){
            a=n.setSize;
            t=(a.width===undefined)?a.target.getWidth():parseInt(a.width,10);
            g=(a.height===undefined)?a.target.getHeight():parseInt(a.height,10);
            if(b||q.dynamic){
                a.target.componentLayout.childrenChanged=true;
                if(q.layoutAnimation){
                    a.target.setCalculatedSize(t,g)
                    }else{
                    a.target.setSize(t,g)
                    }
                }else{
            a.target.el.setSize(t,g)
            }
        }
        if(n.setOpacity.target){
            a=n.setOpacity;
            a.target.el.setStyle("opacity",a.opacity)
            }
        }
    }
}
});
Ext.define("Ext.XTemplate",{
    extend:"Ext.Template",
    statics:{
        from:function(b,a){
            b=Ext.getDom(b);
            return new this(b.value||b.innerHTML,a||{})
            }
        },
argsRe:/<tpl\b[^>]*>((?:(?=([^<]+))\2|<(?!tpl\b[^>]*>))*?)<\/tpl>/,
nameRe:/^<tpl\b[^>]*?for="(.*?)"/,
ifRe:/^<tpl\b[^>]*?if="(.*?)"/,
execRe:/^<tpl\b[^>]*?exec="(.*?)"/,
constructor:function(){
    this.callParent(arguments);
    var x=this,h=x.html,u=x.argsRe,c=x.nameRe,s=x.ifRe,w=x.execRe,o=0,j=[],n="values",v="parent",k="xindex",l="xcount",d="return ",b="with(values){ ",p,f,t,a,e,g,q,y,r;
    h=["<tpl>",h,"</tpl>"].join("");
    while((p=h.match(u))){
        e=null;
        g=null;
        q=null;
        f=p[0].match(c);
        t=p[0].match(s);
        a=p[0].match(w);
        e=t?t[1]:null;
        if(e){
            g=Ext.functionFactory(n,v,k,l,b+"try{"+d+Ext.String.htmlDecode(e)+";}catch(e){return;}}")
            }
            e=a?a[1]:null;
        if(e){
            q=Ext.functionFactory(n,v,k,l,b+Ext.String.htmlDecode(e)+";}")
            }
            y=f?f[1]:null;
        if(y){
            if(y==="."){
                y=n
                }else{
                if(y===".."){
                    y=v
                    }
                }
            y=Ext.functionFactory(n,v,"try{"+b+d+y+";}}catch(e){return;}")
        }
        j.push({
        id:o,
        target:y,
        exec:q,
        test:g,
        body:p[1]||""
        });
    h=h.replace(p[0],"{xtpl"+o+"}");
    o=o+1
    }
    for(r=j.length-1;r>=0;--r){
    x.compileTpl(j[r])
    }
    x.master=j[j.length-1];
x.tpls=j
},
applySubTemplate:function(g,a,c,e,f){
    var d=this,b=d.tpls[g];
    return b.compiled.call(d,a,c,e,f)
    },
codeRe:/\{\[((?:\\\]|.|\n)*?)\]\}/g,
re:/\{([\w-\.\#]+)(?:\:([\w\.]*)(?:\((.*?)?\))?)?(\s?[\+\-\*\/]\s?[\d\.\+\-\*\/\(\)]+)?\}/g,
compileTpl:function(tpl){
    var fm=Ext.util.Format,me=this,useFormat=me.disableFormats!==true,body,bodyReturn,evaluatedFn;
    function fn(m,name,format,args,math){
        var v;
        if(name.substr(0,4)=="xtpl"){
            return"',this.applySubTemplate("+name.substr(4)+", values, parent, xindex, xcount),'"
            }
            if(name=="."){
            v='Ext.Array.indexOf(["string", "number", "boolean"], typeof values) > -1 || Ext.isDate(values) ? values : ""'
            }else{
            if(name=="#"){
                v="xindex"
                }else{
                if(name.substr(0,7)=="parent."){
                    v=name
                    }else{
                    if(name.indexOf(".")!=-1){
                        v="values."+name
                        }else{
                        v="values['"+name+"']"
                        }
                    }
            }
    }
if(math){
    v="("+v+math+")"
    }
    if(format&&useFormat){
    args=args?","+args:"";
    if(format.substr(0,5)!="this."){
        format="fm."+format+"("
        }else{
        format="this."+format.substr(5)+"("
        }
    }else{
    args="";
    format="("+v+" === undefined ? '' : "
    }
    return"',"+format+v+args+"),'"
}
function codeFn(m,code){
    return"',("+code.replace(me.compileARe,"'")+"),'"
    }
    bodyReturn=tpl.body.replace(me.compileBRe,"\\n").replace(me.compileCRe,"\\'").replace(me.re,fn).replace(me.codeRe,codeFn);
body="evaluatedFn = function(values, parent, xindex, xcount){return ['"+bodyReturn+"'].join('');};";
eval(body);
tpl.compiled=function(values,parent,xindex,xcount){
    var vs,length,buffer,i;
    if(tpl.test&&!tpl.test.call(me,values,parent,xindex,xcount)){
        return""
        }
        vs=tpl.target?tpl.target.call(me,values,parent):values;
    if(!vs){
        return""
        }
        parent=tpl.target?values:parent;
    if(tpl.target&&Ext.isArray(vs)){
        buffer=[];
        length=vs.length;
        if(tpl.exec){
            for(i=0;i<length;i++){
                buffer[buffer.length]=evaluatedFn.call(me,vs[i],parent,i+1,length);
                tpl.exec.call(me,vs[i],parent,i+1,length)
                }
            }else{
        for(i=0;i<length;i++){
            buffer[buffer.length]=evaluatedFn.call(me,vs[i],parent,i+1,length)
            }
        }
        return buffer.join("")
}
if(tpl.exec){
    tpl.exec.call(me,vs,parent,xindex,xcount)
    }
    return evaluatedFn.call(me,vs,parent,xindex,xcount)
};

return this
},
applyTemplate:function(a){
    return this.master.compiled.call(this,a,{},1,1)
    },
compile:function(){
    return this
    }
},function(){
    this.createAlias("apply","applyTemplate")
    });
Ext.define("Ext.util.ComponentDragger",{
    extend:"Ext.dd.DragTracker",
    autoStart:500,
    constructor:function(a,b){
        this.comp=a;
        this.initialConstrainTo=b.constrainTo;
        this.callParent([b])
        },
    onStart:function(c){
        var b=this,a=b.comp;
        this.startPosition=a.getPosition();
        if(a.ghost&&!a.liveDrag){
            b.proxy=a.ghost();
            b.dragTarget=b.proxy.header.el
            }
            if(b.constrain||b.constrainDelegate){
            b.constrainTo=b.calculateConstrainRegion()
            }
        },
calculateConstrainRegion:function(){
    var d=this,a=d.comp,g=d.initialConstrainTo,e,f,b=a.el.shadow?a.el.shadow.offset:0;
    if(!(g instanceof Ext.util.Region)){
        g=Ext.fly(g).getViewRegion()
        }
        if(b){
        g.adjust(0,-b,-b,b)
        }
        if(!d.constrainDelegate){
        e=Ext.fly(d.dragTarget).getRegion();
        f=d.proxy?d.proxy.el.getRegion():a.el.getRegion();
        g.adjust(e.top-f.top,e.right-f.right,e.bottom-f.bottom,e.left-f.left)
        }
        return g
    },
onDrag:function(c){
    var b=this,a=(b.proxy&&!b.comp.liveDrag)?b.proxy:b.comp,d=b.getOffset(b.constrain||b.constrainDelegate?"dragTarget":null);
    a.setPosition(b.startPosition[0]+d[0],b.startPosition[1]+d[1])
    },
onEnd:function(a){
    if(this.proxy&&!this.comp.liveDrag){
        this.comp.unghost()
        }
    }
});
Ext.define("Ext.util.Region",{
    requires:["Ext.util.Offset"],
    statics:{
        getRegion:function(a){
            return Ext.fly(a).getPageBox(true)
            },
        from:function(a){
            return new this(a.top,a.right,a.bottom,a.left)
            }
        },
constructor:function(d,f,a,c){
    var e=this;
    e.y=e.top=e[1]=d;
    e.right=f;
    e.bottom=a;
    e.x=e.left=e[0]=c
    },
contains:function(b){
    var a=this;
    return(b.x>=a.x&&b.right<=a.right&&b.y>=a.y&&b.bottom<=a.bottom)
    },
intersect:function(g){
    var f=this,d=Math.max(f.y,g.y),e=Math.min(f.right,g.right),a=Math.min(f.bottom,g.bottom),c=Math.max(f.x,g.x);
    if(a>d&&e>c){
        return new this.self(d,e,a,c)
        }else{
        return false
        }
    },
union:function(g){
    var f=this,d=Math.min(f.y,g.y),e=Math.max(f.right,g.right),a=Math.max(f.bottom,g.bottom),c=Math.min(f.x,g.x);
    return new this.self(d,e,a,c)
    },
constrainTo:function(b){
    var a=this,c=Ext.Number.constrain;
    a.top=a.y=c(a.top,b.y,b.bottom);
    a.bottom=c(a.bottom,b.y,b.bottom);
    a.left=a.x=c(a.left,b.x,b.right);
    a.right=c(a.right,b.x,b.right);
    return a
    },
adjust:function(d,f,a,c){
    var e=this;
    e.top=e.y+=d;
    e.left=e.x+=c;
    e.right+=f;
    e.bottom+=a;
    return e
    },
getOutOfBoundOffset:function(a,b){
    if(!Ext.isObject(a)){
        if(a=="x"){
            return this.getOutOfBoundOffsetX(b)
            }else{
            return this.getOutOfBoundOffsetY(b)
            }
        }else{
    b=a;
    var c=Ext.create("Ext.util.Offset");
    c.x=this.getOutOfBoundOffsetX(b.x);
    c.y=this.getOutOfBoundOffsetY(b.y);
    return c
    }
},
getOutOfBoundOffsetX:function(a){
    if(a<=this.x){
        return this.x-a
        }else{
        if(a>=this.right){
            return this.right-a
            }
        }
    return 0
},
getOutOfBoundOffsetY:function(a){
    if(a<=this.y){
        return this.y-a
        }else{
        if(a>=this.bottom){
            return this.bottom-a
            }
        }
    return 0
},
isOutOfBound:function(a,b){
    if(!Ext.isObject(a)){
        if(a=="x"){
            return this.isOutOfBoundX(b)
            }else{
            return this.isOutOfBoundY(b)
            }
        }else{
    b=a;
    return(this.isOutOfBoundX(b.x)||this.isOutOfBoundY(b.y))
    }
},
isOutOfBoundX:function(a){
    return(a<this.x||a>this.right)
    },
isOutOfBoundY:function(a){
    return(a<this.y||a>this.bottom)
    },
restrict:function(b,d,a){
    if(Ext.isObject(b)){
        var c;
        a=d;
        d=b;
        if(d.copy){
            c=d.copy()
            }else{
            c={
                x:d.x,
                y:d.y
                }
            }
        c.x=this.restrictX(d.x,a);
    c.y=this.restrictY(d.y,a);
    return c
    }else{
    if(b=="x"){
        return this.restrictX(d,a)
        }else{
        return this.restrictY(d,a)
        }
    }
},
restrictX:function(b,a){
    if(!a){
        a=1
        }
        if(b<=this.x){
        b-=(b-this.x)*a
        }else{
        if(b>=this.right){
            b-=(b-this.right)*a
            }
        }
    return b
},
restrictY:function(b,a){
    if(!a){
        a=1
        }
        if(b<=this.y){
        b-=(b-this.y)*a
        }else{
        if(b>=this.bottom){
            b-=(b-this.bottom)*a
            }
        }
    return b
},
getSize:function(){
    return{
        width:this.right-this.x,
        height:this.bottom-this.y
        }
    },
copy:function(){
    return new this.self(this.y,this.right,this.bottom,this.x)
    },
copyFrom:function(b){
    var a=this;
    a.top=a.y=a[1]=b.y;
    a.right=b.right;
    a.bottom=b.bottom;
    a.left=a.x=a[0]=b.x;
    return this
    },
toString:function(){
    return"Region["+this.top+","+this.right+","+this.bottom+","+this.left+"]"
    },
translateBy:function(a,c){
    if(arguments.length==1){
        c=a.y;
        a=a.x
        }
        var b=this;
    b.top=b.y+=c;
    b.right+=a;
    b.bottom+=c;
    b.left=b.x+=a;
    return b
    },
round:function(){
    var a=this;
    a.top=a.y=Math.round(a.y);
    a.right=Math.round(a.right);
    a.bottom=Math.round(a.bottom);
    a.left=a.x=Math.round(a.x);
    return a
    },
equals:function(a){
    return(this.top==a.top&&this.right==a.right&&this.bottom==a.bottom&&this.left==a.left)
    }
});
Ext.define("Ext.dd.DragDropManager",{
    singleton:true,
    requires:["Ext.util.Region"],
    uses:["Ext.tip.QuickTipManager"],
    alternateClassName:["Ext.dd.DragDropMgr","Ext.dd.DDM"],
    ids:{},
    handleIds:{},
    dragCurrent:null,
    dragOvers:{},
    deltaX:0,
    deltaY:0,
    preventDefault:true,
    stopPropagation:true,
    initialized:false,
    locked:false,
    init:function(){
        this.initialized=true
        },
    POINT:0,
    INTERSECT:1,
    mode:0,
    _execOnAll:function(c,b){
        for(var d in this.ids){
            for(var a in this.ids[d]){
                var e=this.ids[d][a];
                if(!this.isTypeOfDD(e)){
                    continue
                }
                e[c].apply(e,b)
                }
            }
            },
_onLoad:function(){
    this.init();
    var a=Ext.EventManager;
    a.on(document,"mouseup",this.handleMouseUp,this,true);
    a.on(document,"mousemove",this.handleMouseMove,this,true);
    a.on(window,"unload",this._onUnload,this,true);
    a.on(window,"resize",this._onResize,this,true)
    },
_onResize:function(a){
    this._execOnAll("resetConstraints",[])
    },
lock:function(){
    this.locked=true
    },
unlock:function(){
    this.locked=false
    },
isLocked:function(){
    return this.locked
    },
locationCache:{},
useCache:true,
clickPixelThresh:3,
clickTimeThresh:350,
dragThreshMet:false,
clickTimeout:null,
startX:0,
startY:0,
regDragDrop:function(b,a){
    if(!this.initialized){
        this.init()
        }
        if(!this.ids[a]){
        this.ids[a]={}
    }
    this.ids[a][b.id]=b
},
removeDDFromGroup:function(c,a){
    if(!this.ids[a]){
        this.ids[a]={}
    }
    var b=this.ids[a];
if(b&&b[c.id]){
    delete b[c.id]
}
},
_remove:function(b){
    for(var a in b.groups){
        if(a&&this.ids[a]&&this.ids[a][b.id]){
            delete this.ids[a][b.id]
        }
    }
    delete this.handleIds[b.id]
},
regHandle:function(b,a){
    if(!this.handleIds[b]){
        this.handleIds[b]={}
    }
    this.handleIds[b][a]=a
},
isDragDrop:function(a){
    return(this.getDDById(a))?true:false
    },
getRelated:function(f,b){
    var e=[];
    for(var d in f.groups){
        for(var c in this.ids[d]){
            var a=this.ids[d][c];
            if(!this.isTypeOfDD(a)){
                continue
            }
            if(!b||a.isTarget){
                e[e.length]=a
                }
            }
        }
        return e
},
isLegalTarget:function(e,d){
    var b=this.getRelated(e,true);
    for(var c=0,a=b.length;c<a;++c){
        if(b[c].id==d.id){
            return true
            }
        }
    return false
},
isTypeOfDD:function(a){
    return(a&&a.__ygDragDrop)
    },
isHandle:function(b,a){
    return(this.handleIds[b]&&this.handleIds[b][a])
    },
getDDById:function(b){
    for(var a in this.ids){
        if(this.ids[a][b]){
            return this.ids[a][b]
            }
        }
    return null
},
handleMouseDown:function(c,b){
    if(Ext.tip.QuickTipManager){
        Ext.tip.QuickTipManager.ddDisable()
        }
        if(this.dragCurrent){
        this.handleMouseUp(c)
        }
        this.currentTarget=c.getTarget();
    this.dragCurrent=b;
    var a=b.getEl();
    this.startX=c.getPageX();
    this.startY=c.getPageY();
    this.deltaX=this.startX-a.offsetLeft;
    this.deltaY=this.startY-a.offsetTop;
    this.dragThreshMet=false;
    this.clickTimeout=setTimeout(function(){
        var d=Ext.dd.DragDropManager;
        d.startDrag(d.startX,d.startY)
        },this.clickTimeThresh)
    },
startDrag:function(a,b){
    clearTimeout(this.clickTimeout);
    if(this.dragCurrent){
        this.dragCurrent.b4StartDrag(a,b);
        this.dragCurrent.startDrag(a,b)
        }
        this.dragThreshMet=true
    },
handleMouseUp:function(a){
    if(Ext.tip.QuickTipManager){
        Ext.tip.QuickTipManager.ddEnable()
        }
        if(!this.dragCurrent){
        return
    }
    clearTimeout(this.clickTimeout);
    if(this.dragThreshMet){
        this.fireEvents(a,true)
        }else{}
    this.stopDrag(a);
    this.stopEvent(a)
    },
stopEvent:function(a){
    if(this.stopPropagation){
        a.stopPropagation()
        }
        if(this.preventDefault){
        a.preventDefault()
        }
    },
stopDrag:function(a){
    if(this.dragCurrent){
        if(this.dragThreshMet){
            this.dragCurrent.b4EndDrag(a);
            this.dragCurrent.endDrag(a)
            }
            this.dragCurrent.onMouseUp(a)
        }
        this.dragCurrent=null;
    this.dragOvers={}
},
handleMouseMove:function(c){
    if(!this.dragCurrent){
        return true
        }
        if(Ext.isIE&&(c.button!==0&&c.button!==1&&c.button!==2)){
        this.stopEvent(c);
        return this.handleMouseUp(c)
        }
        if(!this.dragThreshMet){
        var b=Math.abs(this.startX-c.getPageX());
        var a=Math.abs(this.startY-c.getPageY());
        if(b>this.clickPixelThresh||a>this.clickPixelThresh){
            this.startDrag(this.startX,this.startY)
            }
        }
    if(this.dragThreshMet){
    this.dragCurrent.b4Drag(c);
    this.dragCurrent.onDrag(c);
    if(!this.dragCurrent.moveOnly){
        this.fireEvents(c,false)
        }
    }
this.stopEvent(c);
return true
},
fireEvents:function(l,m){
    var o=this.dragCurrent;
    if(!o||o.isLocked()){
        return
    }
    var p=l.getPoint();
    var a=[];
    var d=[];
    var j=[];
    var g=[];
    var c=[];
    for(var f in this.dragOvers){
        var b=this.dragOvers[f];
        if(!this.isTypeOfDD(b)){
            continue
        }
        if(!this.isOverTarget(p,b,this.mode)){
            d.push(b)
            }
            a[f]=true;
        delete this.dragOvers[f]
    }
    for(var n in o.groups){
        if("string"!=typeof n){
            continue
        }
        for(f in this.ids[n]){
            var h=this.ids[n][f];
            if(!this.isTypeOfDD(h)){
                continue
            }
            if(h.isTarget&&!h.isLocked()&&((h!=o)||(o.ignoreSelf===false))){
                if(this.isOverTarget(p,h,this.mode)){
                    if(m){
                        g.push(h)
                        }else{
                        if(!a[h.id]){
                            c.push(h)
                            }else{
                            j.push(h)
                            }
                            this.dragOvers[h.id]=h
                        }
                    }
            }
        }
    }
    if(this.mode){
    if(d.length){
        o.b4DragOut(l,d);
        o.onDragOut(l,d)
        }
        if(c.length){
        o.onDragEnter(l,c)
        }
        if(j.length){
        o.b4DragOver(l,j);
        o.onDragOver(l,j)
        }
        if(g.length){
        o.b4DragDrop(l,g);
        o.onDragDrop(l,g)
        }
    }else{
    var k=0;
    for(f=0,k=d.length;f<k;++f){
        o.b4DragOut(l,d[f].id);
        o.onDragOut(l,d[f].id)
        }
        for(f=0,k=c.length;f<k;++f){
        o.onDragEnter(l,c[f].id)
        }
        for(f=0,k=j.length;f<k;++f){
        o.b4DragOver(l,j[f].id);
        o.onDragOver(l,j[f].id)
        }
        for(f=0,k=g.length;f<k;++f){
        o.b4DragDrop(l,g[f].id);
        o.onDragDrop(l,g[f].id)
        }
    }
    if(m&&!g.length){
    o.onInvalidDrop(l)
    }
},
getBestMatch:function(c){
    var e=null;
    var b=c.length;
    if(b==1){
        e=c[0]
        }else{
        for(var d=0;d<b;++d){
            var a=c[d];
            if(a.cursorIsOver){
                e=a;
                break
            }else{
                if(!e||e.overlap.getArea()<a.overlap.getArea()){
                    e=a
                    }
                }
        }
    }
return e
},
refreshCache:function(b){
    for(var a in b){
        if("string"!=typeof a){
            continue
        }
        for(var c in this.ids[a]){
            var d=this.ids[a][c];
            if(this.isTypeOfDD(d)){
                var e=this.getLocation(d);
                if(e){
                    this.locationCache[d.id]=e
                    }else{
                    delete this.locationCache[d.id]
                }
            }
        }
        }
    },
verifyEl:function(b){
    if(b){
        var a;
        if(Ext.isIE){
            try{
                a=b.offsetParent
                }catch(c){}
        }else{
        a=b.offsetParent
        }
        if(a){
        return true
        }
    }
return false
},
getLocation:function(h){
    if(!this.isTypeOfDD(h)){
        return null
        }
        if(h.getRegion){
        return h.getRegion()
        }
        var f=h.getEl(),k,d,c,n,m,o,a,j,g;
    try{
        k=Ext.core.Element.getXY(f)
        }catch(i){}
    if(!k){
        return null
        }
        d=k[0];
    c=d+f.offsetWidth;
    n=k[1];
    m=n+f.offsetHeight;
    o=n-h.padding[0];
    a=c+h.padding[1];
    j=m+h.padding[2];
    g=d-h.padding[3];
    return Ext.create("Ext.util.Region",o,a,j,g)
    },
isOverTarget:function(i,a,c){
    var e=this.locationCache[a.id];
    if(!e||!this.useCache){
        e=this.getLocation(a);
        this.locationCache[a.id]=e
        }
        if(!e){
        return false
        }
        a.cursorIsOver=e.contains(i);
    var h=this.dragCurrent;
    if(!h||!h.getTargetCoord||(!c&&!h.constrainX&&!h.constrainY)){
        return a.cursorIsOver
        }
        a.overlap=null;
    var f=h.getTargetCoord(i.x,i.y);
    var b=h.getDragEl();
    var d=Ext.create("Ext.util.Region",f.y,f.x+b.offsetWidth,f.y+b.offsetHeight,f.x);
    var g=d.intersect(e);
    if(g){
        a.overlap=g;
        return(c)?true:a.cursorIsOver
        }else{
        return false
        }
    },
_onUnload:function(b,a){
    Ext.dd.DragDropManager.unregAll()
    },
unregAll:function(){
    if(this.dragCurrent){
        this.stopDrag();
        this.dragCurrent=null
        }
        this._execOnAll("unreg",[]);
    for(var a in this.elementCache){
        delete this.elementCache[a]
    }
    this.elementCache={};
    
    this.ids={}
},
elementCache:{},
getElWrapper:function(b){
    var a=this.elementCache[b];
    if(!a||!a.el){
        a=this.elementCache[b]=new this.ElementWrapper(Ext.getDom(b))
        }
        return a
    },
getElement:function(a){
    return Ext.getDom(a)
    },
getCss:function(b){
    var a=Ext.getDom(b);
    return(a)?a.style:null
    },
ElementWrapper:function(a){
    this.el=a||null;
    this.id=this.el&&a.id;
    this.css=this.el&&a.style
    },
getPosX:function(a){
    return Ext.core.Element.getX(a)
    },
getPosY:function(a){
    return Ext.core.Element.getY(a)
    },
swapNode:function(c,a){
    if(c.swapNode){
        c.swapNode(a)
        }else{
        var d=a.parentNode;
        var b=a.nextSibling;
        if(b==c){
            d.insertBefore(c,a)
            }else{
            if(a==c.nextSibling){
                d.insertBefore(a,c)
                }else{
                c.parentNode.replaceChild(a,c);
                d.insertBefore(c,b)
                }
            }
    }
},
getScroll:function(){
    var d=window.document,e=d.documentElement,a=d.body,c=0,b=0;
    if(Ext.isGecko4){
        c=window.scrollYOffset;
        b=window.scrollXOffset
        }else{
        if(e&&(e.scrollTop||e.scrollLeft)){
            c=e.scrollTop;
            b=e.scrollLeft
            }else{
            if(a){
                c=a.scrollTop;
                b=a.scrollLeft
                }
            }
    }
return{
    top:c,
    left:b
}
},
getStyle:function(b,a){
    return Ext.fly(b).getStyle(a)
    },
getScrollTop:function(){
    return this.getScroll().top
    },
getScrollLeft:function(){
    return this.getScroll().left
    },
moveToEl:function(a,c){
    var b=Ext.core.Element.getXY(c);
    Ext.core.Element.setXY(a,b)
    },
numericSort:function(d,c){
    return(d-c)
    },
_timeoutCount:0,
_addListeners:function(){
    if(document){
        this._onLoad()
        }else{
        if(this._timeoutCount>2000){}else{
            setTimeout(this._addListeners,10);
            if(document&&document.body){
                this._timeoutCount+=1
                }
            }
    }
},
handleWasClicked:function(a,c){
    if(this.isHandle(c,a.id)){
        return true
        }else{
        var b=a.parentNode;
        while(b){
            if(this.isHandle(c,b.id)){
                return true
                }else{
                b=b.parentNode
                }
            }
    }
return false
}
},function(){
    this._addListeners()
    });
Ext.define("Ext.layout.component.Body",{
    alias:["layout.body"],
    extend:"Ext.layout.component.Component",
    uses:["Ext.layout.container.Container"],
    type:"body",
    onLayout:function(c,b){
        var d=this,a=d.owner;
        d.setTargetSize(c,b);
        d.setBodySize.apply(d,arguments);
        if(a&&a.layout&&a.layout.isLayout){
            if(!Ext.isNumber(a.height)||!Ext.isNumber(a.width)){
                a.layout.bindToOwnerCtComponent=true
                }else{
                a.layout.bindToOwnerCtComponent=false
                }
            }
        d.callParent(arguments)
    },
setBodySize:function(e,b){
    var f=this,a=f.owner,c=a.frameSize,d=Ext.isNumber;
    if(d(e)){
        e-=a.el.getFrameWidth("lr")-c.left-c.right
        }
        if(d(b)){
        b-=a.el.getFrameWidth("tb")-c.top-c.bottom
        }
        f.setElementSize(a.body,e,b)
    }
});
Ext.define("Ext.draw.Draw",{
    singleton:true,
    requires:["Ext.draw.Color"],
    pathToStringRE:/,?([achlmqrstvxz]),?/gi,
    pathCommandRE:/([achlmqstvz])[\s,]*((-?\d*\.?\d*(?:e[-+]?\d+)?\s*,?\s*)+)/ig,
    pathValuesRE:/(-?\d*\.?\d*(?:e[-+]?\d+)?)\s*,?\s*/ig,
    stopsRE:/^(\d+%?)$/,
    radian:Math.PI/180,
    availableAnimAttrs:{
        along:"along",
        blur:null,
        "clip-rect":"csv",
        cx:null,
        cy:null,
        fill:"color",
        "fill-opacity":null,
        "font-size":null,
        height:null,
        opacity:null,
        path:"path",
        r:null,
        rotation:"csv",
        rx:null,
        ry:null,
        scale:"csv",
        stroke:"color",
        "stroke-opacity":null,
        "stroke-width":null,
        translation:"csv",
        width:null,
        x:null,
        y:null
    },
    is:function(b,a){
        a=String(a).toLowerCase();
        return(a=="object"&&b===Object(b))||(a=="undefined"&&typeof b==a)||(a=="null"&&b===null)||(a=="array"&&Array.isArray&&Array.isArray(b))||(Object.prototype.toString.call(b).toLowerCase().slice(8,-1))==a
        },
    ellipsePath:function(b){
        var a=b.attr;
        return Ext.String.format("M{0},{1}A{2},{3},0,1,1,{0},{4}A{2},{3},0,1,1,{0},{1}z",a.x,a.y-a.ry,a.rx,a.ry,a.y+a.ry)
        },
    rectPath:function(b){
        var a=b.attr;
        if(a.radius){
            return Ext.String.format("M{0},{1}l{2},0a{3},{3},0,0,1,{3},{3}l0,{5}a{3},{3},0,0,1,{4},{3}l{6},0a{3},{3},0,0,1,{4},{4}l0,{7}a{3},{3},0,0,1,{3},{4}z",a.x+a.radius,a.y,a.width-a.radius*2,a.radius,-a.radius,a.height-a.radius*2,a.radius*2-a.width,a.radius*2-a.height)
            }else{
            return Ext.String.format("M{0},{1}l{2},0,0,{3},{4},0z",a.x,a.y,a.width,a.height,-a.width)
            }
        },
path2string:function(){
    return this.join(",").replace(Ext.draw.Draw.pathToStringRE,"$1")
    },
pathToString:function(a){
    return a.join(",").replace(Ext.draw.Draw.pathToStringRE,"$1")
    },
parsePathString:function(a){
    if(!a){
        return null
        }
        var d={
        a:7,
        c:6,
        h:1,
        l:2,
        m:2,
        q:4,
        s:4,
        t:2,
        v:1,
        z:0
    },c=[],b=this;
    if(b.is(a,"array")&&b.is(a[0],"array")){
        c=b.pathClone(a)
        }
        if(!c.length){
        String(a).replace(b.pathCommandRE,function(f,e,i){
            var h=[],g=e.toLowerCase();
            i.replace(b.pathValuesRE,function(k,j){
                j&&h.push(+j)
                });
            if(g=="m"&&h.length>2){
                c.push([e].concat(Ext.Array.splice(h,0,2)));
                g="l";
                e=(e=="m")?"l":"L"
                }while(h.length>=d[g]){
                c.push([e].concat(Ext.Array.splice(h,0,d[g])));
                if(!d[g]){
                    break
                }
            }
        })
}
c.toString=b.path2string;
return c
},
mapPath:function(k,f){
    if(!f){
        return k
        }
        var g,e,c,h,a,d,b;
    k=this.path2curve(k);
    for(c=0,h=k.length;c<h;c++){
        b=k[c];
        for(a=1,d=b.length;a<d-1;a+=2){
            g=f.x(b[a],b[a+1]);
            e=f.y(b[a],b[a+1]);
            b[a]=g;
            b[a+1]=e
            }
        }
        return k
},
pathClone:function(f){
    var c=[],a,e,b,d;
    if(!this.is(f,"array")||!this.is(f&&f[0],"array")){
        f=this.parsePathString(f)
        }
        for(b=0,d=f.length;b<d;b++){
        c[b]=[];
        for(a=0,e=f[b].length;a<e;a++){
            c[b][a]=f[b][a]
            }
        }
        c.toString=this.path2string;
return c
},
pathToAbsolute:function(c){
    if(!this.is(c,"array")||!this.is(c&&c[0],"array")){
        c=this.parsePathString(c)
        }
        var h=[],l=0,k=0,n=0,m=0,f=0,g=c.length,b,d,e,a;
    if(g&&c[0][0]=="M"){
        l=+c[0][1];
        k=+c[0][2];
        n=l;
        m=k;
        f++;
        h[0]=["M",l,k]
        }
        for(;f<g;f++){
        b=h[f]=[];
        d=c[f];
        if(d[0]!=d[0].toUpperCase()){
            b[0]=d[0].toUpperCase();
            switch(b[0]){
                case"A":
                    b[1]=d[1];
                    b[2]=d[2];
                    b[3]=d[3];
                    b[4]=d[4];
                    b[5]=d[5];
                    b[6]=+(d[6]+l);
                    b[7]=+(d[7]+k);
                    break;
                case"V":
                    b[1]=+d[1]+k;
                    break;
                case"H":
                    b[1]=+d[1]+l;
                    break;
                case"M":
                    n=+d[1]+l;
                    m=+d[2]+k;
                default:
                    e=1;
                    a=d.length;
                    for(;e<a;e++){
                    b[e]=+d[e]+((e%2)?l:k)
                    }
                }
                }else{
        e=0;
        a=d.length;
        for(;e<a;e++){
            h[f][e]=d[e]
            }
        }
        switch(b[0]){
    case"Z":
        l=n;
        k=m;
        break;
    case"H":
        l=b[1];
        break;
    case"V":
        k=b[1];
        break;
    case"M":
        d=h[f];
        a=d.length;
        n=d[a-2];
        m=d[a-1];
    default:
        d=h[f];
        a=d.length;
        l=d[a-2];
        k=d[a-1]
        }
    }
h.toString=this.path2string;
return h
},
pathToRelative:function(d){
    if(!this.is(d,"array")||!this.is(d&&d[0],"array")){
        d=this.parsePathString(d)
        }
        var m=[],o=0,n=0,s=0,q=0,c=0;
    if(d[0][0]=="M"){
        o=d[0][1];
        n=d[0][2];
        s=o;
        q=n;
        c++;
        m.push(["M",o,n])
        }
        for(var g=c,t=d.length;g<t;g++){
        var a=m[g]=[],p=d[g];
        if(p[0]!=p[0].toLowerCase()){
            a[0]=p[0].toLowerCase();
            switch(a[0]){
                case"a":
                    a[1]=p[1];
                    a[2]=p[2];
                    a[3]=p[3];
                    a[4]=p[4];
                    a[5]=p[5];
                    a[6]=+(p[6]-o).toFixed(3);
                    a[7]=+(p[7]-n).toFixed(3);
                    break;
                case"v":
                    a[1]=+(p[1]-n).toFixed(3);
                    break;
                case"m":
                    s=p[1];
                    q=p[2];
                default:
                    for(var f=1,h=p.length;f<h;f++){
                    a[f]=+(p[f]-((f%2)?o:n)).toFixed(3)
                    }
                }
                }else{
        a=m[g]=[];
        if(p[0]=="m"){
            s=p[1]+o;
            q=p[2]+n
            }
            for(var e=0,b=p.length;e<b;e++){
            m[g][e]=p[e]
            }
        }
        var l=m[g].length;
switch(m[g][0]){
    case"z":
        o=s;
        n=q;
        break;
    case"h":
        o+=+m[g][l-1];
        break;
    case"v":
        n+=+m[g][l-1];
        break;
    default:
        o+=+m[g][l-2];
        n+=+m[g][l-1]
        }
    }
m.toString=this.path2string;
return m
},
path2curve:function(j){
    var d=this,g=d.pathToAbsolute(j),c=g.length,h={
        x:0,
        y:0,
        bx:0,
        by:0,
        X:0,
        Y:0,
        qx:null,
        qy:null
    },b,a,f,e;
    for(b=0;b<c;b++){
        g[b]=d.command2curve(g[b],h);
        if(g[b].length>7){
            g[b].shift();
            e=g[b];
            while(e.length){
                Ext.Array.splice(g,b++,0,["C"].concat(Ext.Array.splice(e,0,6)))
                }
                Ext.Array.erase(g,b,1);
            c=g.length
            }
            a=g[b];
        f=a.length;
        h.x=a[f-2];
        h.y=a[f-1];
        h.bx=parseFloat(a[f-4])||h.x;
        h.by=parseFloat(a[f-3])||h.y
        }
        return g
    },
interpolatePaths:function(q,k){
    var h=this,d=h.pathToAbsolute(q),l=h.pathToAbsolute(k),m={
        x:0,
        y:0,
        bx:0,
        by:0,
        X:0,
        Y:0,
        qx:null,
        qy:null
    },a={
        x:0,
        y:0,
        bx:0,
        by:0,
        X:0,
        Y:0,
        qx:null,
        qy:null
    },b=function(p,r){
        if(p[r].length>7){
            p[r].shift();
            var s=p[r];
            while(s.length){
                Ext.Array.splice(p,r++,0,["C"].concat(Ext.Array.splice(s,0,6)))
                }
                Ext.Array.erase(p,r,1);
            n=Math.max(d.length,l.length||0)
            }
        },c=function(u,t,r,p,s){
    if(u&&t&&u[s][0]=="M"&&t[s][0]!="M"){
        Ext.Array.splice(t,s,0,["M",p.x,p.y]);
        r.bx=0;
        r.by=0;
        r.x=u[s][1];
        r.y=u[s][2];
        n=Math.max(d.length,l.length||0)
        }
    };

for(var g=0,n=Math.max(d.length,l.length||0);g<n;g++){
    d[g]=h.command2curve(d[g],m);
    b(d,g);
    (l[g]=h.command2curve(l[g],a));
    b(l,g);
    c(d,l,m,a,g);
    c(l,d,a,m,g);
    var f=d[g],o=l[g],e=f.length,j=o.length;
    m.x=f[e-2];
    m.y=f[e-1];
    m.bx=parseFloat(f[e-4])||m.x;
    m.by=parseFloat(f[e-3])||m.y;
    a.bx=(parseFloat(o[j-4])||a.x);
    a.by=(parseFloat(o[j-3])||a.y);
    a.x=o[j-2];
    a.y=o[j-1]
    }
    return[d,l]
},
command2curve:function(c,b){
    var a=this;
    if(!c){
        return["C",b.x,b.y,b.x,b.y,b.x,b.y]
        }
        if(c[0]!="T"&&c[0]!="Q"){
        b.qx=b.qy=null
        }
        switch(c[0]){
        case"M":
            b.X=c[1];
            b.Y=c[2];
            break;
        case"A":
            c=["C"].concat(a.arc2curve.apply(a,[b.x,b.y].concat(c.slice(1))));
            break;
        case"S":
            c=["C",b.x+(b.x-(b.bx||b.x)),b.y+(b.y-(b.by||b.y))].concat(c.slice(1));
            break;
        case"T":
            b.qx=b.x+(b.x-(b.qx||b.x));
            b.qy=b.y+(b.y-(b.qy||b.y));
            c=["C"].concat(a.quadratic2curve(b.x,b.y,b.qx,b.qy,c[1],c[2]));
            break;
        case"Q":
            b.qx=c[1];
            b.qy=c[2];
            c=["C"].concat(a.quadratic2curve(b.x,b.y,c[1],c[2],c[3],c[4]));
            break;
        case"L":
            c=["C"].concat(b.x,b.y,c[1],c[2],c[1],c[2]);
            break;
        case"H":
            c=["C"].concat(b.x,b.y,c[1],b.y,c[1],b.y);
            break;
        case"V":
            c=["C"].concat(b.x,b.y,b.x,c[1],b.x,c[1]);
            break;
        case"Z":
            c=["C"].concat(b.x,b.y,b.X,b.Y,b.X,b.Y);
            break
            }
            return c
    },
quadratic2curve:function(b,d,g,e,a,c){
    var f=1/3,h=2/3;
    return[f*b+h*g,f*d+h*e,f*a+h*g,f*c+h*e,a,c]
    },
rotate:function(b,g,a){
    var d=Math.cos(a),c=Math.sin(a),f=b*d-g*c,e=b*c+g*d;
    return{
        x:f,
        y:e
    }
},
arc2curve:function(u,ag,I,G,A,n,g,s,af,B){
    var w=this,e=Math.PI,z=w.radian,F=e*120/180,b=z*(+A||0),N=[],K=Math,U=K.cos,a=K.sin,W=K.sqrt,v=K.abs,o=K.asin,J,c,q,P,O,ab,d,S,V,D,C,m,l,r,j,ae,f,ad,Q,T,R,ac,aa,Z,X,M,Y,L,E,H,p;
    if(!B){
        J=w.rotate(u,ag,-b);
        u=J.x;
        ag=J.y;
        J=w.rotate(s,af,-b);
        s=J.x;
        af=J.y;
        c=U(z*A);
        q=a(z*A);
        P=(u-s)/2;
        O=(ag-af)/2;
        ab=(P*P)/(I*I)+(O*O)/(G*G);
        if(ab>1){
            ab=W(ab);
            I=ab*I;
            G=ab*G
            }
            d=I*I;
        S=G*G;
        V=(n==g?-1:1)*W(v((d*S-d*O*O-S*P*P)/(d*O*O+S*P*P)));
        D=V*I*O/G+(u+s)/2;
        C=V*-G*P/I+(ag+af)/2;
        m=o(((ag-C)/G).toFixed(7));
        l=o(((af-C)/G).toFixed(7));
        m=u<D?e-m:m;
        l=s<D?e-l:l;
        if(m<0){
            m=e*2+m
            }
            if(l<0){
            l=e*2+l
            }
            if(g&&m>l){
            m=m-e*2
            }
            if(!g&&l>m){
            l=l-e*2
            }
        }else{
    m=B[0];
    l=B[1];
    D=B[2];
    C=B[3]
    }
    r=l-m;
if(v(r)>F){
    E=l;
    H=s;
    p=af;
    l=m+F*(g&&l>m?1:-1);
    s=D+I*U(l);
    af=C+G*a(l);
    N=w.arc2curve(s,af,I,G,A,0,g,H,p,[l,E,D,C])
    }
    r=l-m;
j=U(m);
ae=a(m);
f=U(l);
ad=a(l);
Q=K.tan(r/4);
T=4/3*I*Q;
R=4/3*G*Q;
ac=[u,ag];
aa=[u+T*ae,ag-R*j];
Z=[s+T*ad,af-R*f];
X=[s,af];
aa[0]=2*ac[0]-aa[0];
aa[1]=2*ac[1]-aa[1];
if(B){
    return[aa,Z,X].concat(N)
    }else{
    N=[aa,Z,X].concat(N).join().split(",");
    M=[];
    L=N.length;
    for(Y=0;Y<L;Y++){
        M[Y]=Y%2?w.rotate(N[Y-1],N[Y],b).y:w.rotate(N[Y],N[Y+1],b).x
        }
        return M
    }
},
rotateAndTranslatePath:function(h){
    var c=h.rotation.degrees,d=h.rotation.x,b=h.rotation.y,n=h.translation.x,k=h.translation.y,m,f,a,l,e,g=[];
    if(!c&&!n&&!k){
        return this.pathToAbsolute(h.attr.path)
        }
        n=n||0;
    k=k||0;
    m=this.pathToAbsolute(h.attr.path);
    for(f=m.length;f--;){
        a=g[f]=m[f].slice();
        if(a[0]=="A"){
            l=this.rotatePoint(a[6],a[7],c,d,b);
            a[6]=l.x+n;
            a[7]=l.y+k
            }else{
            e=1;
            while(a[e+1]!=null){
                l=this.rotatePoint(a[e],a[e+1],c,d,b);
                a[e]=l.x+n;
                a[e+1]=l.y+k;
                e+=2
                }
            }
    }
    return g
},
rotatePoint:function(b,g,e,a,f){
    if(!e){
        return{
            x:b,
            y:g
        }
    }
    a=a||0;
f=f||0;
b=b-a;
g=g-f;
e=e*this.radian;
var d=Math.cos(e),c=Math.sin(e);
return{
    x:b*d-g*c+a,
    y:b*c+g*d+f
    }
},
pathDimensions:function(l){
    if(!l||!(l+"")){
        return{
            x:0,
            y:0,
            width:0,
            height:0
        }
    }
    l=this.path2curve(l);
var j=0,h=0,d=[],b=[],e=0,g=l.length,c,a,k,f;
for(;e<g;e++){
    c=l[e];
    if(c[0]=="M"){
        j=c[1];
        h=c[2];
        d.push(j);
        b.push(h)
        }else{
        f=this.curveDim(j,h,c[1],c[2],c[3],c[4],c[5],c[6]);
        d=d.concat(f.min.x,f.max.x);
        b=b.concat(f.min.y,f.max.y);
        j=c[5];
        h=c[6]
        }
    }
a=Math.min.apply(0,d);
k=Math.min.apply(0,b);
return{
    x:a,
    y:k,
    path:l,
    width:Math.max.apply(0,d)-a,
    height:Math.max.apply(0,b)-k
    }
},
intersectInside:function(b,c,a){
    return(a[0]-c[0])*(b[1]-c[1])>(a[1]-c[1])*(b[0]-c[0])
    },
intersectIntersection:function(m,l,f,d){
    var c=[],b=f[0]-d[0],a=f[1]-d[1],j=m[0]-l[0],h=m[1]-l[1],k=f[0]*d[1]-f[1]*d[0],i=m[0]*l[1]-m[1]*l[0],g=1/(b*h-a*j);
    c[0]=(k*j-i*b)*g;
    c[1]=(k*h-i*a)*g;
    return c
    },
intersect:function(n,c){
    var m=this,h=0,l=c.length,g=c[l-1],p=n,f,q,k,o,a,b,d;
    for(;h<l;++h){
        f=c[h];
        b=p;
        p=[];
        q=b[b.length-1];
        d=0;
        a=b.length;
        for(;d<a;d++){
            k=b[d];
            if(m.intersectInside(k,g,f)){
                if(!m.intersectInside(q,g,f)){
                    p.push(m.intersectIntersection(q,k,g,f))
                    }
                    p.push(k)
                }else{
                if(m.intersectInside(q,g,f)){
                    p.push(m.intersectIntersection(q,k,g,f))
                    }
                }
            q=k
        }
        g=f
    }
    return p
},
curveDim:function(f,d,h,g,s,r,o,l){
    var q=(s-2*h+f)-(o-2*s+h),n=2*(h-f)-2*(s-h),k=f-h,j=(-n+Math.sqrt(n*n-4*q*k))/2/q,i=(-n-Math.sqrt(n*n-4*q*k))/2/q,m=[d,l],p=[f,o],e;
    if(Math.abs(j)>1000000000000){
        j=0.5
        }
        if(Math.abs(i)>1000000000000){
        i=0.5
        }
        if(j>0&&j<1){
        e=this.findDotAtSegment(f,d,h,g,s,r,o,l,j);
        p.push(e.x);
        m.push(e.y)
        }
        if(i>0&&i<1){
        e=this.findDotAtSegment(f,d,h,g,s,r,o,l,i);
        p.push(e.x);
        m.push(e.y)
        }
        q=(r-2*g+d)-(l-2*r+g);
    n=2*(g-d)-2*(r-g);
    k=d-g;
    j=(-n+Math.sqrt(n*n-4*q*k))/2/q;
    i=(-n-Math.sqrt(n*n-4*q*k))/2/q;
    if(Math.abs(j)>1000000000000){
        j=0.5
        }
        if(Math.abs(i)>1000000000000){
        i=0.5
        }
        if(j>0&&j<1){
        e=this.findDotAtSegment(f,d,h,g,s,r,o,l,j);
        p.push(e.x);
        m.push(e.y)
        }
        if(i>0&&i<1){
        e=this.findDotAtSegment(f,d,h,g,s,r,o,l,i);
        p.push(e.x);
        m.push(e.y)
        }
        return{
        min:{
            x:Math.min.apply(0,p),
            y:Math.min.apply(0,m)
            },
        max:{
            x:Math.max.apply(0,p),
            y:Math.max.apply(0,m)
            }
        }
},
getAnchors:function(e,d,j,i,u,t,p){
    p=p||4;
    var h=Math,o=h.PI,q=o/2,l=h.abs,a=h.sin,b=h.cos,f=h.atan,s,r,g,k,n,m,w,v,c;
    s=(j-e)/p;
    r=(u-j)/p;
    if((i>=d&&i>=t)||(i<=d&&i<=t)){
        g=k=q
        }else{
        g=f((j-e)/l(i-d));
        if(d<i){
            g=o-g
            }
            k=f((u-j)/l(i-t));
        if(t<i){
            k=o-k
            }
        }
    c=q-((g+k)%(o*2))/2;
if(c>q){
    c-=o
    }
    g+=c;
k+=c;
n=j-s*a(g);
m=i+s*b(g);
w=j+r*a(k);
v=i+r*b(k);
if((i>d&&m<d)||(i<d&&m>d)){
    n+=l(d-m)*(n-j)/(m-i);
    m=d
    }
    if((i>t&&v<t)||(i<t&&v>t)){
    w-=l(t-v)*(w-j)/(v-i);
    v=t
    }
    return{
    x1:n,
    y1:m,
    x2:w,
    y2:v
}
},
smooth:function(a,q){
    var p=this.path2curve(a),e=[p[0]],h=p[0][1],g=p[0][2],r,t,u=1,k=p.length,f=1,m=h,l=g,c=0,b=0;
    for(;u<k;u++){
        var z=p[u],w=z.length,v=p[u-1],n=v.length,s=p[u+1],o=s&&s.length;
        if(z[0]=="M"){
            m=z[1];
            l=z[2];
            r=u+1;
            while(p[r][0]!="C"){
                r++
            }
            c=p[r][5];
            b=p[r][6];
            e.push(["M",m,l]);
            f=e.length;
            h=m;
            g=l;
            continue
        }
        if(z[w-2]==m&&z[w-1]==l&&(!s||s[0]=="M")){
            var d=e[f].length;
            t=this.getAnchors(v[n-2],v[n-1],m,l,e[f][d-2],e[f][d-1],q);
            e[f][1]=t.x2;
            e[f][2]=t.y2
            }else{
            if(!s||s[0]=="M"){
                t={
                    x1:z[w-2],
                    y1:z[w-1]
                    }
                }else{
            t=this.getAnchors(v[n-2],v[n-1],z[w-2],z[w-1],s[o-2],s[o-1],q)
            }
        }
    e.push(["C",h,g,t.x1,t.y1,z[w-2],z[w-1]]);
    h=t.x2;
    g=t.y2
    }
    return e
},
findDotAtSegment:function(b,a,d,c,i,h,g,f,j){
    var e=1-j;
    return{
        x:Math.pow(e,3)*b+Math.pow(e,2)*3*j*d+e*3*j*j*i+Math.pow(j,3)*g,
        y:Math.pow(e,3)*a+Math.pow(e,2)*3*j*c+e*3*j*j*h+Math.pow(j,3)*f
        }
    },
snapEnds:function(p,q,d){
    var c=(q-p)/d,a=Math.floor(Math.log(c)/Math.LN10)+1,e=Math.pow(10,a),r,n=Math.round((c%e)*Math.pow(10,2-a)),b=[[0,15],[20,4],[30,2],[40,4],[50,9],[60,4],[70,2],[80,4],[100,15]],g=0,o,j,h,f,k=1000000000,l=b.length;
    r=p=Math.floor(p/e)*e;
    for(h=0;h<l;h++){
        o=b[h][0];
        j=(o-n)<0?1000000:(o-n)/b[h][1];
        if(j<k){
            f=o;
            k=j
            }
        }
    c=Math.floor(c*Math.pow(10,-a))*Math.pow(10,a)+f*Math.pow(10,a-2);
while(r<q){
    r+=c;
    g++
}
q=+r.toFixed(10);
return{
    from:p,
    to:q,
    power:a,
    step:c,
    steps:g
}
},
sorter:function(d,c){
    return d.offset-c.offset
    },
rad:function(a){
    return a%360*Math.PI/180
    },
degrees:function(a){
    return a*180/Math.PI%360
    },
withinBox:function(a,c,b){
    b=b||{};
    
    return(a>=b.x&&a<=(b.x+b.width)&&c>=b.y&&c<=(b.y+b.height))
    },
parseGradient:function(j){
    var e=this,f=j.type||"linear",c=j.angle||0,h=e.radian,k=j.stops,a=[],i,b,g,d;
    if(f=="linear"){
        b=[0,0,Math.cos(c*h),Math.sin(c*h)];
        g=1/(Math.max(Math.abs(b[2]),Math.abs(b[3]))||1);
        b[2]*=g;
        b[3]*=g;
        if(b[2]<0){
            b[0]=-b[2];
            b[2]=0
            }
            if(b[3]<0){
            b[1]=-b[3];
            b[3]=0
            }
        }
    for(i in k){
    if(k.hasOwnProperty(i)&&e.stopsRE.test(i)){
        d={
            offset:parseInt(i,10),
            color:Ext.draw.Color.toHex(k[i].color)||"#ffffff",
            opacity:k[i].opacity||1
            };
            
        a.push(d)
        }
    }
Ext.Array.sort(a,e.sorter);
if(f=="linear"){
    return{
        id:j.id,
        type:f,
        vector:b,
        stops:a
    }
}else{
    return{
        id:j.id,
        type:f,
        centerX:j.centerX,
        centerY:j.centerY,
        focalX:j.focalX,
        focalY:j.focalY,
        radius:j.radius,
        vector:b,
        stops:a
    }
}
}
});
Ext.ns("Ext.fx");
Ext.require("Ext.fx.CubicBezier",function(){
    var e=Math,g=e.PI,d=e.pow,b=e.sin,f=e.sqrt,a=e.abs,c=1.70158;
    Ext.fx.Easing={};
    
    Ext.apply(Ext.fx.Easing,{
        linear:function(h){
            return h
            },
        ease:function(k){
            var h=0.07813-k/2,l=-0.25,m=f(0.0066+h*h),p=m-h,j=d(a(p),1/3)*(p<0?-1:1),o=-m-h,i=d(a(o),1/3)*(o<0?-1:1),r=j+i+0.25;
            return d(1-r,2)*3*r*0.1+(1-r)*3*r*r+r*r*r
            },
        easeIn:function(h){
            return d(h,1.7)
            },
        easeOut:function(h){
            return d(h,0.48)
            },
        easeInOut:function(p){
            var k=0.48-p/1.04,j=f(0.1734+k*k),h=j-k,o=d(a(h),1/3)*(h<0?-1:1),m=-j-k,l=d(a(m),1/3)*(m<0?-1:1),i=o+l+0.5;
            return(1-i)*3*i*i+i*i*i
            },
        backIn:function(h){
            return h*h*((c+1)*h-c)
            },
        backOut:function(h){
            h=h-1;
            return h*h*((c+1)*h+c)+1
            },
        elasticIn:function(j){
            if(j===0||j===1){
                return j
                }
                var i=0.3,h=i/4;
            return d(2,-10*j)*b((j-h)*(2*g)/i)+1
            },
        elasticOut:function(h){
            return 1-Ext.fx.Easing.elasticIn(1-h)
            },
        bounceIn:function(h){
            return 1-Ext.fx.Easing.bounceOut(1-h)
            },
        bounceOut:function(k){
            var i=7.5625,j=2.75,h;
            if(k<(1/j)){
                h=i*k*k
                }else{
                if(k<(2/j)){
                    k-=(1.5/j);
                    h=i*k*k+0.75
                    }else{
                    if(k<(2.5/j)){
                        k-=(2.25/j);
                        h=i*k*k+0.9375
                        }else{
                        k-=(2.625/j);
                        h=i*k*k+0.984375
                        }
                    }
            }
        return h
    }
    });
Ext.apply(Ext.fx.Easing,{
    "back-in":Ext.fx.Easing.backIn,
    "back-out":Ext.fx.Easing.backOut,
    "ease-in":Ext.fx.Easing.easeIn,
    "ease-out":Ext.fx.Easing.easeOut,
    "elastic-in":Ext.fx.Easing.elasticIn,
    "elastic-out":Ext.fx.Easing.elasticIn,
    "bounce-in":Ext.fx.Easing.bounceIn,
    "bounce-out":Ext.fx.Easing.bounceOut,
    "ease-in-out":Ext.fx.Easing.easeInOut
    })
});
Ext.define("Ext.fx.PropertyHandler",{
    requires:["Ext.draw.Draw"],
    statics:{
        defaultHandler:{
            pixelDefaultsRE:/width|height|top$|bottom$|left$|right$/i,
            unitRE:/^(-?\d*\.?\d*){1}(em|ex|px|in|cm|mm|pt|pc|%)*$/,
            scrollRE:/^scroll/i,
            computeDelta:function(i,c,a,f,h){
                a=(typeof a=="number")?a:1;
                var g=this.unitRE,d=g.exec(i),b,e;
                if(d){
                    i=d[1];
                    e=d[2];
                    if(!this.scrollRE.test(h)&&!e&&this.pixelDefaultsRE.test(h)){
                        e="px"
                        }
                    }
                i=+i||0;
            d=g.exec(c);
            if(d){
                c=d[1];
                e=d[2]||e
                }
                c=+c||0;
            b=(f!=null)?f:i;
            return{
                from:i,
                delta:(c-b)*a,
                units:e
            }
        },
    get:function(n,b,a,m,h){
        var l=n.length,d=[],e,g,k,c,f;
        for(e=0;e<l;e++){
            if(m){
                g=m[e][1].from
                }
                if(Ext.isArray(n[e][1])&&Ext.isArray(b)){
                k=[];
                c=0;
                f=n[e][1].length;
                for(;c<f;c++){
                    k.push(this.computeDelta(n[e][1][c],b[c],a,g,h))
                    }
                    d.push([n[e][0],k])
                }else{
                d.push([n[e][0],this.computeDelta(n[e][1],b,a,g,h)])
                }
            }
        return d
    },
set:function(k,f){
    var g=k.length,c=[],d,a,h,e,b;
    for(d=0;d<g;d++){
        a=k[d][1];
        if(Ext.isArray(a)){
            h=[];
            b=0;
            e=a.length;
            for(;b<e;b++){
                h.push(a[b].from+(a[b].delta*f)+(a[b].units||0))
                }
                c.push([k[d][0],h])
            }else{
            c.push([k[d][0],a.from+(a.delta*f)+(a.units||0)])
            }
        }
    return c
}
},
color:{
    rgbRE:/^rgb\(([0-9]+)\s*,\s*([0-9]+)\s*,\s*([0-9]+)\)$/i,
    hexRE:/^#?([0-9A-F]{2})([0-9A-F]{2})([0-9A-F]{2})$/i,
    hex3RE:/^#?([0-9A-F]{1})([0-9A-F]{1})([0-9A-F]{1})$/i,
    parseColor:function(a,d){
        d=(typeof d=="number")?d:1;
        var e,c=false,b;
        Ext.each([this.hexRE,this.rgbRE,this.hex3RE],function(g,f){
            e=(f%2==0)?16:10;
            b=g.exec(a);
            if(b&&b.length==4){
                if(f==2){
                    b[1]+=b[1];
                    b[2]+=b[2];
                    b[3]+=b[3]
                    }
                    c={
                    red:parseInt(b[1],e),
                    green:parseInt(b[2],e),
                    blue:parseInt(b[3],e)
                    };
                    
                return false
                }
            });
    return c||a
    },
computeDelta:function(g,a,e,c){
    g=this.parseColor(g);
    a=this.parseColor(a,e);
    var f=c?c:g,b=typeof f,d=typeof a;
    if(b=="string"||b=="undefined"||d=="string"||d=="undefined"){
        return a||f
        }
        return{
        from:g,
        delta:{
            red:Math.round((a.red-f.red)*e),
            green:Math.round((a.green-f.green)*e),
            blue:Math.round((a.blue-f.blue)*e)
            }
        }
},
get:function(h,a,f,d){
    var g=h.length,c=[],e,b;
    for(e=0;e<g;e++){
        if(d){
            b=d[e][1].from
            }
            c.push([h[e][0],this.computeDelta(h[e][1],a,f,b)])
        }
        return c
    },
set:function(j,e){
    var f=j.length,c=[],d,b,a,g,h;
    for(d=0;d<f;d++){
        b=j[d][1];
        if(b){
            g=b.from;
            h=b.delta;
            b=(typeof b=="object"&&"red" in b)?"rgb("+b.red+", "+b.green+", "+b.blue+")":b;
            b=(typeof b=="object"&&b.length)?b[0]:b;
            if(typeof b=="undefined"){
                return[]
                }
                a=typeof b=="string"?b:"rgb("+[(g.red+Math.round(h.red*e))%256,(g.green+Math.round(h.green*e))%256,(g.blue+Math.round(h.blue*e))%256].join(",")+")";
            c.push([j[d][0],a])
            }
        }
    return c
}
},
object:{
    interpolate:function(d,b){
        b=(typeof b=="number")?b:1;
        var a={},c;
        for(c in d){
            a[c]=parseInt(d[c],10)*b
            }
            return a
        },
    computeDelta:function(g,a,c,b){
        g=this.interpolate(g);
        a=this.interpolate(a,c);
        var f=b?b:g,e={},d;
        for(d in a){
            e[d]=a[d]-f[d]
            }
            return{
            from:g,
            delta:e
        }
    },
get:function(h,a,f,d){
    var g=h.length,c=[],e,b;
    for(e=0;e<g;e++){
        if(d){
            b=d[e][1].from
            }
            c.push([h[e][0],this.computeDelta(h[e][1],a,f,b)])
        }
        return c
    },
set:function(k,f){
    var g=k.length,c=[],e={},d,h,j,b,a;
    for(d=0;d<g;d++){
        b=k[d][1];
        h=b.from;
        j=b.delta;
        for(a in h){
            e[a]=Math.round(h[a]+j[a]*f)
            }
            c.push([k[d][0],e])
        }
        return c
    }
},
path:{
    computeDelta:function(e,a,c,b){
        c=(typeof c=="number")?c:1;
        var d;
        e=+e||0;
        a=+a||0;
        d=(b!=null)?b:e;
        return{
            from:e,
            delta:(a-d)*c
            }
        },
forcePath:function(a){
    if(!Ext.isArray(a)&&!Ext.isArray(a[0])){
        a=Ext.draw.Draw.parsePathString(a)
        }
        return a
    },
get:function(b,h,a,p){
    var c=this.forcePath(h),m=[],r=b.length,d,g,n,f,o,l,e,s,q;
    for(n=0;n<r;n++){
        q=this.forcePath(b[n][1]);
        f=Ext.draw.Draw.interpolatePaths(q,c);
        q=f[0];
        c=f[1];
        d=q.length;
        s=[];
        for(l=0;l<d;l++){
            f=[q[l][0]];
            g=q[l].length;
            for(e=1;e<g;e++){
                o=p&&p[0][1][l][e].from;
                f.push(this.computeDelta(q[l][e],c[l][e],a,o))
                }
                s.push(f)
            }
            m.push([b[n][0],s])
        }
        return m
    },
set:function(o,m){
    var n=o.length,e=[],g,f,d,h,l,c,a,b;
    for(g=0;g<n;g++){
        c=o[g][1];
        h=[];
        a=c.length;
        for(f=0;f<a;f++){
            l=[c[f][0]];
            b=c[f].length;
            for(d=1;d<b;d++){
                l.push(c[f][d].from+c[f][d].delta*m)
                }
                h.push(l.join(","))
            }
            e.push([o[g][0],h.join(",")])
        }
        return e
    }
}
}
},function(){
    Ext.each(["outlineColor","backgroundColor","borderColor","borderTopColor","borderRightColor","borderBottomColor","borderLeftColor","fill","stroke"],function(a){
        this[a]=this.color
        },this)
    });
Ext.define("Ext.layout.component.Button",{
    alias:["layout.button"],
    extend:"Ext.layout.component.Component",
    type:"button",
    cellClsRE:/-btn-(tl|br)\b/,
    htmlRE:/<.*>/,
    beforeLayout:function(){
        return this.callParent(arguments)||this.lastText!==this.owner.text
        },
    onLayout:function(c,n){
        var k=this,g=Ext.isNumber,d=k.owner,m=d.el,h=d.btnEl,e=d.btnInnerEl,f=d.btnIconEl,i=(d.icon||d.iconCls)&&(d.iconAlign=="top"||d.iconAlign=="bottom"),b=d.minWidth,l=d.maxWidth,a,o,j;
        k.getTargetInfo();
        k.callParent(arguments);
        e.unclip();
        k.setTargetSize(c,n);
        if(!g(c)){
            if(d.text&&Ext.isIE7&&Ext.isStrict&&h&&h.getWidth()>20){
                o=k.btnFrameWidth;
                j=Ext.util.TextMetrics.measure(e,d.text);
                m.setWidth(j.width+o+k.adjWidth);
                h.setWidth(j.width+o);
                e.setWidth(j.width+o);
                if(i){
                    f.setWidth(j.width+o)
                    }
                }else{
            m.setWidth(null);
            h.setWidth(null);
            e.setWidth(null);
            f.setWidth(null)
            }
            if(b||l){
            a=m.getWidth();
            if(b&&(a<b)){
                k.setTargetSize(b,n)
                }else{
                if(l&&(a>l)){
                    e.clip();
                    k.setTargetSize(l,n)
                    }
                }
        }
}
this.lastText=d.text
},
setTargetSize:function(a,k){
    var g=this,b=g.owner,e=Ext.isNumber,d=b.btnInnerEl,i=(e(a)?a-g.adjWidth:a),f=(e(k)?k-g.adjHeight:k),c=g.btnFrameHeight,j=b.getText(),h;
    g.callParent(arguments);
    g.setElementSize(b.btnEl,i,f);
    g.setElementSize(d,i,f);
    if(e(f)){
        d.setStyle("line-height",f-c+"px")
        }
        if(j&&this.htmlRE.test(j)){
        d.setStyle("line-height","normal");
        h=Ext.util.TextMetrics.measure(d,j).height;
        d.setStyle("padding-top",g.btnFrameTop+Math.max(d.getHeight()-c-h,0)/2+"px");
        g.setElementSize(d,i,f)
        }
    },
getTargetInfo:function(){
    var e=this,a=e.owner,d=a.el,c=e.frameSize,g=a.frameBody,b=a.btnWrap,f=a.btnInnerEl;
    if(!("adjWidth" in e)){
        Ext.apply(e,{
            adjWidth:c.left+c.right+d.getBorderWidth("lr")+d.getPadding("lr")+b.getPadding("lr")+(g?g.getFrameWidth("lr"):0),
            adjHeight:c.top+c.bottom+d.getBorderWidth("tb")+d.getPadding("tb")+b.getPadding("tb")+(g?g.getFrameWidth("tb"):0),
            btnFrameWidth:f.getFrameWidth("lr"),
            btnFrameHeight:f.getFrameWidth("tb"),
            btnFrameTop:f.getFrameWidth("t")
            })
        }
        return e.callParent()
    }
});
Ext.define("Ext.layout.component.Dock",{
    alias:["layout.dock"],
    extend:"Ext.layout.component.AbstractDock"
});
Ext.define("Ext.dd.ScrollManager",{
    singleton:true,
    requires:["Ext.dd.DragDropManager"],
    constructor:function(){
        var a=Ext.dd.DragDropManager;
        a.fireEvents=Ext.Function.createSequence(a.fireEvents,this.onFire,this);
        a.stopDrag=Ext.Function.createSequence(a.stopDrag,this.onStop,this);
        this.doScroll=Ext.Function.bind(this.doScroll,this);
        this.ddmInstance=a;
        this.els={};
        
        this.dragEl=null;
        this.proc={}
    },
onStop:function(a){
    var b=Ext.dd.ScrollManager;
    b.dragEl=null;
    b.clearProc()
    },
triggerRefresh:function(){
    if(this.ddmInstance.dragCurrent){
        this.ddmInstance.refreshCache(this.ddmInstance.dragCurrent.groups)
        }
    },
doScroll:function(){
    if(this.ddmInstance.dragCurrent){
        var a=this.proc,b=a.el,c=a.el.ddScrollConfig,d=c?c.increment:this.increment;
        if(!this.animate){
            if(b.scroll(a.dir,d)){
                this.triggerRefresh()
                }
            }else{
        b.scroll(a.dir,d,true,this.animDuration,this.triggerRefresh)
        }
    }
},
clearProc:function(){
    var a=this.proc;
    if(a.id){
        clearInterval(a.id)
        }
        a.id=0;
    a.el=null;
    a.dir=""
    },
startProc:function(b,a){
    this.clearProc();
    this.proc.el=b;
    this.proc.dir=a;
    var d=b.ddScrollConfig?b.ddScrollConfig.ddGroup:undefined,c=(b.ddScrollConfig&&b.ddScrollConfig.frequency)?b.ddScrollConfig.frequency:this.frequency;
    if(d===undefined||this.ddmInstance.dragCurrent.ddGroup==d){
        this.proc.id=setInterval(this.doScroll,c)
        }
    },
onFire:function(g,j){
    if(j||!this.ddmInstance.dragCurrent){
        return
    }
    if(!this.dragEl||this.dragEl!=this.ddmInstance.dragCurrent){
        this.dragEl=this.ddmInstance.dragCurrent;
        this.refreshCache()
        }
        var k=g.getXY(),l=g.getPoint(),h=this.proc,f=this.els;
    for(var b in f){
        var d=f[b],a=d._region;
        var i=d.ddScrollConfig?d.ddScrollConfig:this;
        if(a&&a.contains(l)&&d.isScrollable()){
            if(a.bottom-l.y<=i.vthresh){
                if(h.el!=d){
                    this.startProc(d,"down")
                    }
                    return
            }else{
                if(a.right-l.x<=i.hthresh){
                    if(h.el!=d){
                        this.startProc(d,"left")
                        }
                        return
                }else{
                    if(l.y-a.top<=i.vthresh){
                        if(h.el!=d){
                            this.startProc(d,"up")
                            }
                            return
                    }else{
                        if(l.x-a.left<=i.hthresh){
                            if(h.el!=d){
                                this.startProc(d,"right")
                                }
                                return
                        }
                    }
                }
        }
    }
}
this.clearProc()
},
register:function(c){
    if(Ext.isArray(c)){
        for(var b=0,a=c.length;b<a;b++){
            this.register(c[b])
            }
        }else{
    c=Ext.get(c);
    this.els[c.id]=c
    }
},
unregister:function(c){
    if(Ext.isArray(c)){
        for(var b=0,a=c.length;b<a;b++){
            this.unregister(c[b])
            }
        }else{
    c=Ext.get(c);
    delete this.els[c.id]
}
},
vthresh:25,
hthresh:25,
increment:100,
frequency:500,
animate:true,
animDuration:0.4,
ddGroup:undefined,
refreshCache:function(){
    var a=this.els,b;
    for(b in a){
        if(typeof a[b]=="object"){
            a[b]._region=a[b].getRegion()
            }
        }
    }
});
Ext.define("Ext.chart.theme.Theme",{
    requires:["Ext.draw.Color"],
    theme:"Base",
    themeAttrs:false,
    initTheme:function(e){
        var d=this,b=Ext.chart.theme,c,a;
        if(e){
            e=e.split(":");
            for(c in b){
                if(c==e[0]){
                    a=e[1]=="gradients";
                    d.themeAttrs=new b[c]({
                        useGradients:a
                    });
                    if(a){
                        d.gradients=d.themeAttrs.gradients
                        }
                        if(d.themeAttrs.background){
                        d.background=d.themeAttrs.background
                        }
                        return
                }
            }
            }
        }
},function(){
    (function(){
        Ext.chart.theme=function(c,b){
            c=c||{};
            
            var j=0,g,a,h,o,p,e,m,n,k=[],d,f;
            if(c.baseColor){
                d=Ext.draw.Color.fromString(c.baseColor);
                f=d.getHSL()[2];
                if(f<0.15){
                    d=d.getLighter(0.3)
                    }else{
                    if(f<0.3){
                        d=d.getLighter(0.15)
                        }else{
                        if(f>0.85){
                            d=d.getDarker(0.3)
                            }else{
                            if(f>0.7){
                                d=d.getDarker(0.15)
                                }
                            }
                    }
            }
        c.colors=[d.getDarker(0.3).toString(),d.getDarker(0.15).toString(),d.toString(),d.getLighter(0.15).toString(),d.getLighter(0.3).toString()];
        delete c.baseColor
        }
        if(c.colors){
        a=c.colors.slice();
        p=b.markerThemes;
        o=b.seriesThemes;
        g=a.length;
        b.colors=a;
        for(;j<g;j++){
            h=a[j];
            m=p[j]||{};
            
            e=o[j]||{};
            
            m.fill=e.fill=m.stroke=e.stroke=h;
            p[j]=m;
            o[j]=e
            }
            b.markerThemes=p.slice(0,g);
        b.seriesThemes=o.slice(0,g)
        }
        for(n in b){
        if(n in c){
            if(Ext.isObject(c[n])&&Ext.isObject(b[n])){
                Ext.apply(b[n],c[n])
                }else{
                b[n]=c[n]
                }
            }
    }
    if(c.useGradients){
    a=b.colors||(function(){
        var i=[];
        for(j=0,o=b.seriesThemes,g=o.length;j<g;j++){
            i.push(o[j].fill||o[j].stroke)
            }
            return i
        })();
    for(j=0,g=a.length;j<g;j++){
        d=Ext.draw.Color.fromString(a[j]);
        if(d){
            h=d.getDarker(0.1).toString();
            d=d.toString();
            n="theme-"+d.substr(1)+"-"+h.substr(1);
            k.push({
                id:n,
                angle:45,
                stops:{
                    0:{
                        color:d.toString()
                        },
                    100:{
                        color:h.toString()
                        }
                    }
            });
    a[j]="url(#"+n+")"
    }
    }
b.gradients=k;
b.colors=a
}
Ext.apply(this,b)
}
})()
});
Ext.define("Ext.chart.theme.Base",{
    requires:["Ext.chart.theme.Theme"],
    constructor:function(a){
        Ext.chart.theme.call(this,a,{
            background:false,
            axis:{
                stroke:"#444",
                "stroke-width":1
            },
            axisLabelTop:{
                fill:"#444",
                font:"12px Arial, Helvetica, sans-serif",
                spacing:2,
                padding:5,
                renderer:function(b){
                    return b
                    }
                },
        axisLabelRight:{
            fill:"#444",
            font:"12px Arial, Helvetica, sans-serif",
            spacing:2,
            padding:5,
            renderer:function(b){
                return b
                }
            },
        axisLabelBottom:{
            fill:"#444",
            font:"12px Arial, Helvetica, sans-serif",
            spacing:2,
            padding:5,
            renderer:function(b){
                return b
                }
            },
    axisLabelLeft:{
        fill:"#444",
        font:"12px Arial, Helvetica, sans-serif",
        spacing:2,
        padding:5,
        renderer:function(b){
            return b
            }
        },
axisTitleTop:{
    font:"bold 18px Arial",
    fill:"#444"
},
axisTitleRight:{
    font:"bold 18px Arial",
    fill:"#444",
    rotate:{
        x:0,
        y:0,
        degrees:270
    }
},
axisTitleBottom:{
    font:"bold 18px Arial",
    fill:"#444"
},
axisTitleLeft:{
    font:"bold 18px Arial",
    fill:"#444",
    rotate:{
        x:0,
        y:0,
        degrees:270
    }
},
series:{
    "stroke-width":0
},
seriesLabel:{
    font:"12px Arial",
    fill:"#333"
},
marker:{
    stroke:"#555",
    fill:"#000",
    radius:3,
    size:3
},
colors:["#94ae0a","#115fa6","#a61120","#ff8809","#ffd13e","#a61187","#24ad9a","#7c7474","#a66111"],
seriesThemes:[{
    fill:"#115fa6"
},{
    fill:"#94ae0a"
},{
    fill:"#a61120"
},{
    fill:"#ff8809"
},{
    fill:"#ffd13e"
},{
    fill:"#a61187"
},{
    fill:"#24ad9a"
},{
    fill:"#7c7474"
},{
    fill:"#a66111"
}],
markerThemes:[{
    fill:"#115fa6",
    type:"circle"
},{
    fill:"#94ae0a",
    type:"cross"
},{
    fill:"#a61120",
    type:"plus"
}]
})
}
},function(){
    var c=["#b1da5a","#4ce0e7","#e84b67","#da5abd","#4d7fe6","#fec935"],h=["Green","Sky","Red","Purple","Blue","Yellow"],g=0,f=0,b=c.length,a=Ext.chart.theme,d=[["#f0a50a","#c20024","#2044ba","#810065","#7eae29"],["#6d9824","#87146e","#2a9196","#d39006","#1e40ac"],["#fbbc29","#ce2e4e","#7e0062","#158b90","#57880e"],["#ef5773","#fcbd2a","#4f770d","#1d3eaa","#9b001f"],["#7eae29","#fdbe2a","#910019","#27b4bc","#d74dbc"],["#44dce1","#0b2592","#996e05","#7fb325","#b821a1"]],e=d.length;
    for(;g<b;g++){
        a[h[g]]=(function(i){
            return Ext.extend(a.Base,{
                constructor:function(j){
                    a.Base.prototype.constructor.call(this,Ext.apply({
                        baseColor:i
                    },j))
                    }
                })
        })(c[g])
        }
        for(g=0;g<e;g++){
    a["Category"+(g+1)]=(function(i){
        return Ext.extend(a.Base,{
            constructor:function(j){
                a.Base.prototype.constructor.call(this,Ext.apply({
                    colors:i
                },j))
                }
            })
    })(d[g])
    }
});
Ext.define("Ext.chart.Label",{
    requires:["Ext.draw.Color"],
    colorStringRe:/url\s*\(\s*#([^\/)]+)\s*\)/,
    constructor:function(a){
        var b=this;
        b.label=Ext.applyIf(b.label||{},{
            display:"none",
            color:"#000",
            field:"name",
            minMargin:50,
            font:"11px Helvetica, sans-serif",
            orientation:"horizontal",
            renderer:function(c){
                return c
                }
            });
    if(b.label.display!=="none"){
        b.labelsGroup=b.chart.surface.getGroup(b.seriesId+"-labels")
        }
    },
renderLabels:function(){
    var H=this,s=H.chart,v=s.gradients,t=H.items,d=s.animate,F=H.label,z=F.display,x=F.color,a=[].concat(F.field),n=H.labelsGroup,e=H.chart.store,B=e.getCount(),f=(t||0)&&t.length,l=f/B,c=(v||0)&&v.length,J=Ext.draw.Color,b,A,h,g,y,w,r,D,p,E,m,q,u,o,C,G,I;
    if(z=="none"){
        return
    }
    for(A=0,h=0;A<B;A++){
        g=0;
        for(y=0;y<l;y++){
            E=t[h];
            m=n.getAt(h);
            q=e.getAt(A);
            while(this.__excludes&&this.__excludes[g]){
                g++
            }
            if(!E&&m){
                m.hide(true)
                }
                if(E&&a[y]){
                if(!m){
                    m=H.onCreateLabel(q,E,A,z,y,g)
                    }
                    H.onPlaceLabel(m,q,E,A,z,d,y,g);
                if(F.contrast&&E.sprite){
                    u=E.sprite;
                    if(u._endStyle){
                        I=u._endStyle.fill
                        }else{
                        if(u._to){
                            I=u._to.fill
                            }else{
                            I=u.attr.fill
                            }
                        }
                    I=I||u.attr.fill;
                o=J.fromString(I);
                if(I&&!o){
                    I=I.match(H.colorStringRe)[1];
                    for(w=0;w<c;w++){
                        b=v[w];
                        if(b.id==I){
                            p=0;
                            r=0;
                            for(D in b.stops){
                                p++;
                                r+=J.fromString(b.stops[D].color).getGrayscale()
                                }
                                C=(r/p)/255;
                            break
                        }
                    }
                    }else{
            C=o.getGrayscale()/255
            }
            if(m.isOutside){
            C=1
            }
            G=J.fromString(m.attr.color||m.attr.fill).getHSL();
            G[2]=C>0.5?0.2:0.8;
            m.setAttributes({
            fill:String(J.fromHSL.apply({},G))
            },true)
        }
        }
    h++;
    g++
}
}
H.hideLabels(h)
},
hideLabels:function(c){
    var b=this.labelsGroup,a;
    if(b){
        a=b.getCount();
        while(a-->c){
            b.getAt(a).hide(true)
            }
        }
}
});
Ext.define("Ext.fx.Queue",{
    requires:["Ext.util.HashMap"],
    constructor:function(){
        this.targets=Ext.create("Ext.util.HashMap");
        this.fxQueue={}
    },
getFxDefaults:function(a){
    var b=this.targets.get(a);
    if(b){
        return b.fxDefaults
        }
        return{}
},
setFxDefaults:function(a,c){
    var b=this.targets.get(a);
    if(b){
        b.fxDefaults=Ext.apply(b.fxDefaults||{},c)
        }
    },
stopAnimation:function(b){
    var d=this,a=d.getFxQueue(b),c=a.length;
    while(c){
        a[c-1].end();
        c--
    }
},
getActiveAnimation:function(b){
    var a=this.getFxQueue(b);
    return(a&&!!a.length)?a[0]:false
    },
hasFxBlock:function(b){
    var a=this.getFxQueue(b);
    return a&&a[0]&&a[0].block
    },
getFxQueue:function(b){
    if(!b){
        return false
        }
        var c=this,a=c.fxQueue[b],d=c.targets.get(b);
    if(!d){
        return false
        }
        if(!a){
        c.fxQueue[b]=[];
        if(d.type!="element"){
            d.target.on("destroy",function(){
                c.fxQueue[b]=[]
                })
            }
        }
    return c.fxQueue[b]
},
queueFx:function(d){
    var c=this,e=d.target,a,b;
    if(!e){
        return
    }
    a=c.getFxQueue(e.getId());
    b=a.length;
    if(b){
        if(d.concurrent){
            d.paused=false
            }else{
            a[b-1].on("afteranimate",function(){
                d.paused=false
                })
            }
        }else{
    d.paused=false
    }
    d.on("afteranimate",function(){
    Ext.Array.remove(a,d);
    if(d.remove){
        if(e.type=="element"){
            var f=Ext.get(e.id);
            if(f){
                f.remove()
                }
            }
    }
},this);
a.push(d)
}
});
Ext.define("Ext.fx.target.CompositeElement",{
    extend:"Ext.fx.target.Element",
    isComposite:true,
    constructor:function(a){
        a.id=a.id||Ext.id(null,"ext-composite-");
        this.callParent([a])
        },
    getAttr:function(a,d){
        var b=[],c=this.target;
        c.each(function(e){
            b.push([e,this.getElVal(e,a,d)])
            },this);
        return b
        }
    });
Ext.define("Ext.PluginManager",{
    extend:"Ext.AbstractManager",
    alternateClassName:"Ext.PluginMgr",
    singleton:true,
    typeName:"ptype",
    create:function(a,b){
        if(a.init){
            return a
            }else{
            return Ext.createByAlias("plugin."+(a.ptype||b),a)
            }
        },
findByType:function(c,f){
    var e=[],b=this.types;
    for(var a in b){
        if(!b.hasOwnProperty(a)){
            continue
        }
        var d=b[a];
        if(d.type==c&&(!f||(f===true&&d.isDefault))){
            e.push(d)
            }
        }
    return e
}
},function(){
    Ext.preg=function(){
        return Ext.PluginManager.registerType.apply(Ext.PluginManager,arguments)
        }
    });
Ext.define("Ext.ComponentManager",{
    extend:"Ext.AbstractManager",
    alternateClassName:"Ext.ComponentMgr",
    singleton:true,
    typeName:"xtype",
    create:function(b,d){
        if(b instanceof Ext.AbstractComponent){
            return b
            }else{
            if(Ext.isString(b)){
                return Ext.createByAlias("widget."+b)
                }else{
                var c=b.xtype||d,a=b;
                return Ext.createByAlias("widget."+c,a)
                }
            }
    },
registerType:function(b,a){
    this.types[b]=a;
    a[this.typeName]=b;
    a.prototype[this.typeName]=b
    }
});
Ext.define("Ext.ComponentLoader",{
    extend:"Ext.ElementLoader",
    statics:{
        Renderer:{
            Data:function(a,b,d){
                var f=true;
                try{
                    a.getTarget().update(Ext.decode(b.responseText))
                    }catch(c){
                    f=false
                    }
                    return f
                },
            Component:function(a,c,g){
                var h=true,f=a.getTarget(),b=[];
                try{
                    b=Ext.decode(c.responseText)
                    }catch(d){
                    h=false
                    }
                    if(h){
                    if(g.removeAll){
                        f.removeAll()
                        }
                        f.add(b)
                    }
                    return h
                }
            }
    },
target:null,
loadMask:false,
renderer:"html",
setTarget:function(b){
    var a=this;
    if(Ext.isString(b)){
        b=Ext.getCmp(b)
        }
        if(a.target&&a.target!=b){
        a.abort()
        }
        a.target=b
    },
removeMask:function(){
    this.target.setLoading(false)
    },
addMask:function(a){
    this.target.setLoading(a)
    },
setOptions:function(b,a){
    b.removeAll=Ext.isDefined(a.removeAll)?a.removeAll:this.removeAll
    },
getRenderer:function(b){
    if(Ext.isFunction(b)){
        return b
        }
        var a=this.statics().Renderer;
    switch(b){
        case"component":
            return a.Component;
        case"data":
            return a.Data;
        default:
            return Ext.ElementLoader.Renderer.Html
            }
        }
});
Ext.define("Ext.dd.DragDrop",{
    requires:["Ext.dd.DragDropManager"],
    constructor:function(c,a,b){
        if(c){
            this.init(c,a,b)
            }
        },
id:null,
config:null,
dragElId:null,
handleElId:null,
invalidHandleTypes:null,
invalidHandleIds:null,
invalidHandleClasses:null,
startPageX:0,
startPageY:0,
groups:null,
locked:false,
lock:function(){
    this.locked=true
    },
moveOnly:false,
unlock:function(){
    this.locked=false
    },
isTarget:true,
padding:null,
_domRef:null,
__ygDragDrop:true,
constrainX:false,
constrainY:false,
minX:0,
maxX:0,
minY:0,
maxY:0,
maintainOffset:false,
xTicks:null,
yTicks:null,
primaryButtonOnly:true,
available:false,
hasOuterHandles:false,
b4StartDrag:function(a,b){},
    startDrag:function(a,b){},
    b4Drag:function(a){},
    onDrag:function(a){},
    onDragEnter:function(a,b){},
    b4DragOver:function(a){},
    onDragOver:function(a,b){},
    b4DragOut:function(a){},
    onDragOut:function(a,b){},
    b4DragDrop:function(a){},
    onDragDrop:function(a,b){},
    onInvalidDrop:function(a){},
    b4EndDrag:function(a){},
    endDrag:function(a){},
    b4MouseDown:function(a){},
    onMouseDown:function(a){},
    onMouseUp:function(a){},
    onAvailable:function(){},
    defaultPadding:{
    left:0,
    right:0,
    top:0,
    bottom:0
},
constrainTo:function(h,f,m){
    if(Ext.isNumber(f)){
        f={
            left:f,
            right:f,
            top:f,
            bottom:f
        }
    }
    f=f||this.defaultPadding;
var j=Ext.get(this.getEl()).getBox(),a=Ext.get(h),l=a.getScroll(),i,d=a.dom;
    if(d==document.body){
    i={
        x:l.left,
        y:l.top,
        width:Ext.core.Element.getViewWidth(),
        height:Ext.core.Element.getViewHeight()
        }
    }else{
    var k=a.getXY();
    i={
        x:k[0],
        y:k[1],
        width:d.clientWidth,
        height:d.clientHeight
        }
    }
var g=j.y-i.y,e=j.x-i.x;
this.resetConstraints();
this.setXConstraint(e-(f.left||0),i.width-e-j.width-(f.right||0),this.xTickSize);
this.setYConstraint(g-(f.top||0),i.height-g-j.height-(f.bottom||0),this.yTickSize)
},
getEl:function(){
    if(!this._domRef){
        this._domRef=Ext.getDom(this.id)
        }
        return this._domRef
    },
getDragEl:function(){
    return Ext.getDom(this.dragElId)
    },
init:function(c,a,b){
    this.initTarget(c,a,b);
    Ext.EventManager.on(this.id,"mousedown",this.handleMouseDown,this)
    },
initTarget:function(c,a,b){
    this.config=b||{};
    
    this.DDMInstance=Ext.dd.DragDropManager;
    this.groups={};
    
    if(typeof c!=="string"){
        c=Ext.id(c)
        }
        this.id=c;
    this.addToGroup((a)?a:"default");
    this.handleElId=c;
    this.setDragElId(c);
    this.invalidHandleTypes={
        A:"A"
    };
    
    this.invalidHandleIds={};
    
    this.invalidHandleClasses=[];
    this.applyConfig();
    this.handleOnAvailable()
    },
applyConfig:function(){
    this.padding=this.config.padding||[0,0,0,0];
    this.isTarget=(this.config.isTarget!==false);
    this.maintainOffset=(this.config.maintainOffset);
    this.primaryButtonOnly=(this.config.primaryButtonOnly!==false)
    },
handleOnAvailable:function(){
    this.available=true;
    this.resetConstraints();
    this.onAvailable()
    },
setPadding:function(c,a,d,b){
    if(!a&&0!==a){
        this.padding=[c,c,c,c]
        }else{
        if(!d&&0!==d){
            this.padding=[c,a,c,a]
            }else{
            this.padding=[c,a,d,b]
            }
        }
},
setInitPosition:function(d,c){
    var e=this.getEl();
    if(!this.DDMInstance.verifyEl(e)){
        return
    }
    var b=d||0;
    var a=c||0;
    var f=Ext.core.Element.getXY(e);
    this.initPageX=f[0]-b;
    this.initPageY=f[1]-a;
    this.lastPageX=f[0];
    this.lastPageY=f[1];
    this.setStartPosition(f)
    },
setStartPosition:function(b){
    var a=b||Ext.core.Element.getXY(this.getEl());
    this.deltaSetXY=null;
    this.startPageX=a[0];
    this.startPageY=a[1]
    },
addToGroup:function(a){
    this.groups[a]=true;
    this.DDMInstance.regDragDrop(this,a)
    },
removeFromGroup:function(a){
    if(this.groups[a]){
        delete this.groups[a]
    }
    this.DDMInstance.removeDDFromGroup(this,a)
    },
setDragElId:function(a){
    this.dragElId=a
    },
setHandleElId:function(a){
    if(typeof a!=="string"){
        a=Ext.id(a)
        }
        this.handleElId=a;
    this.DDMInstance.regHandle(this.id,a)
    },
setOuterHandleElId:function(a){
    if(typeof a!=="string"){
        a=Ext.id(a)
        }
        Ext.EventManager.on(a,"mousedown",this.handleMouseDown,this);
    this.setHandleElId(a);
    this.hasOuterHandles=true
    },
unreg:function(){
    Ext.EventManager.un(this.id,"mousedown",this.handleMouseDown,this);
    this._domRef=null;
    this.DDMInstance._remove(this)
    },
destroy:function(){
    this.unreg()
    },
isLocked:function(){
    return(this.DDMInstance.isLocked()||this.locked)
    },
handleMouseDown:function(c,b){
    if(this.primaryButtonOnly&&c.button!=0){
        return
    }
    if(this.isLocked()){
        return
    }
    this.DDMInstance.refreshCache(this.groups);
    var a=c.getPoint();
    if(!this.hasOuterHandles&&!this.DDMInstance.isOverTarget(a,this)){}else{
        if(this.clickValidator(c)){
            this.setStartPosition();
            this.b4MouseDown(c);
            this.onMouseDown(c);
            this.DDMInstance.handleMouseDown(c,this);
            this.DDMInstance.stopEvent(c)
            }else{}
}
},
clickValidator:function(b){
    var a=b.getTarget();
    return(this.isValidHandleChild(a)&&(this.id==this.handleElId||this.DDMInstance.handleWasClicked(a,this.id)))
    },
addInvalidHandleType:function(a){
    var b=a.toUpperCase();
    this.invalidHandleTypes[b]=b
    },
addInvalidHandleId:function(a){
    if(typeof a!=="string"){
        a=Ext.id(a)
        }
        this.invalidHandleIds[a]=a
    },
addInvalidHandleClass:function(a){
    this.invalidHandleClasses.push(a)
    },
removeInvalidHandleType:function(a){
    var b=a.toUpperCase();
    delete this.invalidHandleTypes[b]
},
removeInvalidHandleId:function(a){
    if(typeof a!=="string"){
        a=Ext.id(a)
        }
        delete this.invalidHandleIds[a]
},
removeInvalidHandleClass:function(b){
    for(var c=0,a=this.invalidHandleClasses.length;c<a;++c){
        if(this.invalidHandleClasses[c]==b){
            delete this.invalidHandleClasses[c]
        }
    }
    },
isValidHandleChild:function(d){
    var c=true;
    var g;
    try{
        g=d.nodeName.toUpperCase()
        }catch(f){
        g=d.nodeName
        }
        c=c&&!this.invalidHandleTypes[g];
    c=c&&!this.invalidHandleIds[d.id];
    for(var b=0,a=this.invalidHandleClasses.length;c&&b<a;++b){
        c=!Ext.fly(d).hasCls(this.invalidHandleClasses[b])
        }
        return c
    },
setXTicks:function(d,a){
    this.xTicks=[];
    this.xTickSize=a;
    var c={};
    
    for(var b=this.initPageX;b>=this.minX;b=b-a){
        if(!c[b]){
            this.xTicks[this.xTicks.length]=b;
            c[b]=true
            }
        }
    for(b=this.initPageX;b<=this.maxX;b=b+a){
    if(!c[b]){
        this.xTicks[this.xTicks.length]=b;
        c[b]=true
        }
    }
Ext.Array.sort(this.xTicks,this.DDMInstance.numericSort)
},
setYTicks:function(d,a){
    this.yTicks=[];
    this.yTickSize=a;
    var c={};
    
    for(var b=this.initPageY;b>=this.minY;b=b-a){
        if(!c[b]){
            this.yTicks[this.yTicks.length]=b;
            c[b]=true
            }
        }
    for(b=this.initPageY;b<=this.maxY;b=b+a){
    if(!c[b]){
        this.yTicks[this.yTicks.length]=b;
        c[b]=true
        }
    }
Ext.Array.sort(this.yTicks,this.DDMInstance.numericSort)
},
setXConstraint:function(c,b,a){
    this.leftConstraint=c;
    this.rightConstraint=b;
    this.minX=this.initPageX-c;
    this.maxX=this.initPageX+b;
    if(a){
        this.setXTicks(this.initPageX,a)
        }
        this.constrainX=true
    },
clearConstraints:function(){
    this.constrainX=false;
    this.constrainY=false;
    this.clearTicks()
    },
clearTicks:function(){
    this.xTicks=null;
    this.yTicks=null;
    this.xTickSize=0;
    this.yTickSize=0
    },
setYConstraint:function(a,c,b){
    this.topConstraint=a;
    this.bottomConstraint=c;
    this.minY=this.initPageY-a;
    this.maxY=this.initPageY+c;
    if(b){
        this.setYTicks(this.initPageY,b)
        }
        this.constrainY=true
    },
resetConstraints:function(){
    if(this.initPageX||this.initPageX===0){
        var b=(this.maintainOffset)?this.lastPageX-this.initPageX:0;
        var a=(this.maintainOffset)?this.lastPageY-this.initPageY:0;
        this.setInitPosition(b,a)
        }else{
        this.setInitPosition()
        }
        if(this.constrainX){
        this.setXConstraint(this.leftConstraint,this.rightConstraint,this.xTickSize)
        }
        if(this.constrainY){
        this.setYConstraint(this.topConstraint,this.bottomConstraint,this.yTickSize)
        }
    },
getTick:function(g,d){
    if(!d){
        return g
        }else{
        if(d[0]>=g){
            return d[0]
            }else{
            for(var b=0,a=d.length;b<a;++b){
                var c=b+1;
                if(d[c]&&d[c]>=g){
                    var f=g-d[b];
                    var e=d[c]-g;
                    return(e>f)?d[b]:d[c]
                    }
                }
            return d[d.length-1]
        }
    }
},
toString:function(){
    return("DragDrop "+this.id)
    }
});
Ext.define("Ext.dd.DDTarget",{
    extend:"Ext.dd.DragDrop",
    constructor:function(c,a,b){
        if(c){
            this.initTarget(c,a,b)
            }
        },
getDragEl:Ext.emptyFn,
isValidHandleChild:Ext.emptyFn,
startDrag:Ext.emptyFn,
endDrag:Ext.emptyFn,
onDrag:Ext.emptyFn,
onDragDrop:Ext.emptyFn,
onDragEnter:Ext.emptyFn,
onDragOut:Ext.emptyFn,
onDragOver:Ext.emptyFn,
onInvalidDrop:Ext.emptyFn,
onMouseDown:Ext.emptyFn,
onMouseUp:Ext.emptyFn,
setXConstraint:Ext.emptyFn,
setYConstraint:Ext.emptyFn,
resetConstraints:Ext.emptyFn,
clearConstraints:Ext.emptyFn,
clearTicks:Ext.emptyFn,
setInitPosition:Ext.emptyFn,
setDragElId:Ext.emptyFn,
setHandleElId:Ext.emptyFn,
setOuterHandleElId:Ext.emptyFn,
addInvalidHandleClass:Ext.emptyFn,
addInvalidHandleId:Ext.emptyFn,
addInvalidHandleType:Ext.emptyFn,
removeInvalidHandleClass:Ext.emptyFn,
removeInvalidHandleId:Ext.emptyFn,
removeInvalidHandleType:Ext.emptyFn,
toString:function(){
    return("DDTarget "+this.id)
    }
});
Ext.define("Ext.dd.DropTarget",{
    extend:"Ext.dd.DDTarget",
    requires:["Ext.dd.ScrollManager"],
    constructor:function(b,a){
        this.el=Ext.get(b);
        Ext.apply(this,a);
        if(this.containerScroll){
            Ext.dd.ScrollManager.register(this.el)
            }
            this.callParent([this.el.dom,this.ddGroup||this.group,{
            isTarget:true
        }])
        },
    dropAllowed:Ext.baseCSSPrefix+"dd-drop-ok",
    dropNotAllowed:Ext.baseCSSPrefix+"dd-drop-nodrop",
    isTarget:true,
    isNotifyTarget:true,
    notifyEnter:function(a,c,b){
        if(this.overClass){
            this.el.addCls(this.overClass)
            }
            return this.dropAllowed
        },
    notifyOver:function(a,c,b){
        return this.dropAllowed
        },
    notifyOut:function(a,c,b){
        if(this.overClass){
            this.el.removeCls(this.overClass)
            }
        },
notifyDrop:function(a,c,b){
    return false
    },
destroy:function(){
    this.callParent();
    if(this.containerScroll){
        Ext.dd.ScrollManager.unregister(this.el)
        }
    }
});
Ext.define("Ext.app.PortalDropZone",{
    extend:"Ext.dd.DropTarget",
    constructor:function(a,b){
        this.portal=a;
        Ext.dd.ScrollManager.register(a.body);
        Ext.app.PortalDropZone.superclass.constructor.call(this,a.body,b);
        a.body.ddScrollConfig=this.ddScrollConfig
        },
    ddScrollConfig:{
        vthresh:50,
        hthresh:-1,
        animate:true,
        increment:200
    },
    createEvent:function(a,f,d,b,h,g){
        return{
            portal:this.portal,
            panel:d.panel,
            columnIndex:b,
            column:h,
            position:g,
            data:d,
            source:a,
            rawEvent:f,
            status:this.dropAllowed
            }
        },
notifyOver:function(u,t,v){
    var d=t.getXY(),a=this.portal,p=u.proxy;
    if(!this.grid){
        this.grid=this.getGrid()
        }
        var b=a.body.dom.clientWidth;
    if(!this.lastCW){
        this.lastCW=b
        }else{
        if(this.lastCW!=b){
            this.lastCW=b;
            this.grid=this.getGrid()
            }
        }
    var o=0,c=0,n=this.grid.columnX,q=n.length,m=false;
for(q;o<q;o++){
    c=n[o].x+n[o].w;
    if(d[0]<c){
        m=true;
        break
    }
}
if(!m){
    o--
}
var i,g=0,r=0,l=false,k=a.items.getAt(o),s=k.items.items,j=false;
    q=s.length;
    for(q;g<q;g++){
    i=s[g];
    r=i.el.getHeight();
    if(r===0){
        j=true
        }else{
        if((i.el.getY()+(r/2))>d[1]){
            l=true;
            break
        }
    }
}
g=(l&&i?g:k.items.getCount())+(j?-1:0);
var f=this.createEvent(u,t,v,o,k,g);
if(a.fireEvent("validatedrop",f)!==false&&a.fireEvent("beforedragover",f)!==false){
    p.getProxy().setWidth("auto");
    if(i){
        p.moveProxy(i.el.dom.parentNode,l?i.el.dom:null)
        }else{
        p.moveProxy(k.el.dom,null)
        }
        this.lastPos={
        c:k,
        col:o,
        p:j||(l&&i)?g:false
        };
        
    this.scrollPos=a.body.getScroll();
    a.fireEvent("dragover",f);
    return f.status
    }else{
    return f.status
    }
},
notifyOut:function(){
    delete this.grid
    },
notifyDrop:function(l,h,g){
    delete this.grid;
    if(!this.lastPos){
        return
    }
    var j=this.lastPos.c,f=this.lastPos.col,k=this.lastPos.p,a=l.panel,b=this.createEvent(l,h,g,f,j,k!==false?k:j.items.getCount());
    if(this.portal.fireEvent("validatedrop",b)!==false&&this.portal.fireEvent("beforedrop",b)!==false){
        a.el.dom.style.display="";
        if(k!==false){
            j.insert(k,a)
            }else{
            j.add(a)
            }
            l.proxy.hide();
        this.portal.fireEvent("drop",b);
        var m=this.scrollPos.top;
        if(m){
            var i=this.portal.body.dom;
            setTimeout(function(){
                i.scrollTop=m
                },10)
            }
        }
    delete this.lastPos;
return true
},
getGrid:function(){
    var a=this.portal.body.getBox();
    a.columnX=[];
    this.portal.items.each(function(b){
        a.columnX.push({
            x:b.el.getX(),
            w:b.el.getWidth()
            })
        });
    return a
    },
unreg:function(){
    Ext.dd.ScrollManager.unregister(this.portal.body);
    Ext.app.PortalDropZone.superclass.unreg.call(this)
    }
});
Ext.define("Ext.ModelManager",{
    extend:"Ext.AbstractManager",
    alternateClassName:"Ext.ModelMgr",
    requires:["Ext.data.Association"],
    singleton:true,
    typeName:"mtype",
    associationStack:[],
    registerType:function(c,b){
        var d=b.prototype,a;
        if(d&&d.isModel){
            a=b
            }else{
            if(!b.extend){
                b.extend="Ext.data.Model"
                }
                a=Ext.define(c,b)
            }
            this.types[c]=a;
        return a
        },
    onModelDefined:function(c){
        var a=this.associationStack,f=a.length,e=[],b,d,g;
        for(d=0;d<f;d++){
            b=a[d];
            if(b.associatedModel==c.modelName){
                e.push(b)
                }
            }
        for(d=0,f=e.length;d<f;d++){
        g=e[d];
        this.types[g.ownerModel].prototype.associations.add(Ext.data.Association.create(g));
        Ext.Array.remove(a,g)
        }
    },
registerDeferredAssociation:function(a){
    this.associationStack.push(a)
    },
getModel:function(b){
    var a=b;
    if(typeof a=="string"){
        a=this.types[a]
        }
        return a
    },
create:function(c,b,d){
    var a=typeof b=="function"?b:this.types[b||c.name];
    return new a(c,d)
    }
},function(){
    Ext.regModel=function(){
        return this.ModelManager.registerType.apply(this.ModelManager,arguments)
        }
    });
Ext.define("Ext.util.Grouper",{
    extend:"Ext.util.Sorter",
    getGroupString:function(a){
        return a.get(this.property)
        }
    });
Ext.define("Ext.util.AbstractMixedCollection",{
    requires:["Ext.util.Filter"],
    mixins:{
        observable:"Ext.util.Observable"
    },
    constructor:function(b,a){
        var c=this;
        c.items=[];
        c.map={};
        
        c.keys=[];
        c.length=0;
        c.addEvents("clear","add","replace","remove");
        c.allowFunctions=b===true;
        if(a){
            c.getKey=a
            }
            c.mixins.observable.constructor.call(c)
        },
    allowFunctions:false,
    add:function(b,e){
        var d=this,f=e,c=b,a;
        if(arguments.length==1){
            f=c;
            c=d.getKey(f)
            }
            if(typeof c!="undefined"&&c!==null){
            a=d.map[c];
            if(typeof a!="undefined"){
                return d.replace(c,f)
                }
                d.map[c]=f
            }
            d.length++;
        d.items.push(f);
        d.keys.push(c);
        d.fireEvent("add",d.length-1,f,c);
        return f
        },
    getKey:function(a){
        return a.id
        },
    replace:function(c,e){
        var d=this,a,b;
        if(arguments.length==1){
            e=arguments[0];
            c=d.getKey(e)
            }
            a=d.map[c];
        if(typeof c=="undefined"||c===null||typeof a=="undefined"){
            return d.add(c,e)
            }
            b=d.indexOfKey(c);
        d.items[b]=e;
        d.map[c]=e;
        d.fireEvent("replace",c,a,e);
        return e
        },
    addAll:function(f){
        var e=this,d=0,b,a,c;
        if(arguments.length>1||Ext.isArray(f)){
            b=arguments.length>1?arguments:f;
            for(a=b.length;d<a;d++){
                e.add(b[d])
                }
            }else{
        for(c in f){
            if(f.hasOwnProperty(c)){
                if(e.allowFunctions||typeof f[c]!="function"){
                    e.add(c,f[c])
                    }
                }
        }
        }
},
each:function(e,d){
    var b=[].concat(this.items),c=0,a=b.length,f;
    for(;c<a;c++){
        f=b[c];
        if(e.call(d||f,f,c,a)===false){
            break
        }
    }
    },
eachKey:function(e,d){
    var f=this.keys,b=this.items,c=0,a=f.length;
    for(;c<a;c++){
        e.call(d||window,f[c],b[c],c,a)
        }
    },
findBy:function(e,d){
    var f=this.keys,b=this.items,c=0,a=b.length;
    for(;c<a;c++){
        if(e.call(d||window,b[c],f[c])){
            return b[c]
            }
        }
    return null
},
find:function(){
    if(Ext.isDefined(Ext.global.console)){
        Ext.global.console.warn("Ext.util.MixedCollection: find has been deprecated. Use findBy instead.")
        }
        return this.findBy.apply(this,arguments)
    },
insert:function(a,b,e){
    var d=this,c=b,f=e;
    if(arguments.length==2){
        f=c;
        c=d.getKey(f)
        }
        if(d.containsKey(c)){
        d.suspendEvents();
        d.removeAtKey(c);
        d.resumeEvents()
        }
        if(a>=d.length){
        return d.add(c,f)
        }
        d.length++;
    Ext.Array.splice(d.items,a,0,f);
    if(typeof c!="undefined"&&c!==null){
        d.map[c]=f
        }
        Ext.Array.splice(d.keys,a,0,c);
    d.fireEvent("add",a,f,c);
    return f
    },
remove:function(a){
    return this.removeAt(this.indexOf(a))
    },
removeAll:function(a){
    Ext.each(a||[],function(b){
        this.remove(b)
        },this);
    return this
    },
removeAt:function(a){
    var c=this,d,b;
    if(a<c.length&&a>=0){
        c.length--;
        d=c.items[a];
        Ext.Array.erase(c.items,a,1);
        b=c.keys[a];
        if(typeof b!="undefined"){
            delete c.map[b]
        }
        Ext.Array.erase(c.keys,a,1);
        c.fireEvent("remove",d,b);
        return d
        }
        return false
    },
removeAtKey:function(a){
    return this.removeAt(this.indexOfKey(a))
    },
getCount:function(){
    return this.length
    },
indexOf:function(a){
    return Ext.Array.indexOf(this.items,a)
    },
indexOfKey:function(a){
    return Ext.Array.indexOf(this.keys,a)
    },
get:function(b){
    var d=this,a=d.map[b],c=a!==undefined?a:(typeof b=="number")?d.items[b]:undefined;
    return typeof c!="function"||d.allowFunctions?c:null
    },
getAt:function(a){
    return this.items[a]
    },
getByKey:function(a){
    return this.map[a]
    },
contains:function(a){
    return Ext.Array.contains(this.items,a)
    },
containsKey:function(a){
    return typeof this.map[a]!="undefined"
    },
clear:function(){
    var a=this;
    a.length=0;
    a.items=[];
    a.keys=[];
    a.map={};
    
    a.fireEvent("clear")
    },
first:function(){
    return this.items[0]
    },
last:function(){
    return this.items[this.length-1]
    },
sum:function(g,b,h,a){
    var c=this.extractValues(g,b),f=c.length,e=0,d;
    h=h||0;
    a=(a||a===0)?a:f-1;
    for(d=h;d<=a;d++){
        e+=c[d]
        }
        return e
    },
collect:function(j,e,g){
    var k=this.extractValues(j,e),a=k.length,b={},c=[],h,f,d;
    for(d=0;d<a;d++){
        h=k[d];
        f=String(h);
        if((g||!Ext.isEmpty(h))&&!b[f]){
            b[f]=true;
            c.push(h)
            }
        }
    return c
},
extractValues:function(c,a){
    var b=this.items;
    if(a){
        b=Ext.Array.pluck(b,a)
        }
        return Ext.Array.pluck(b,c)
    },
getRange:function(f,a){
    var e=this,c=e.items,b=[],d;
    if(c.length<1){
        return b
        }
        f=f||0;
    a=Math.min(typeof a=="undefined"?e.length-1:a,e.length-1);
    if(f<=a){
        for(d=f;d<=a;d++){
            b[b.length]=c[d]
            }
        }else{
    for(d=f;d>=a;d--){
        b[b.length]=c[d]
        }
    }
    return b
},
filter:function(d,c,f,a){
    var b=[],e;
    if(Ext.isString(d)){
        b.push(Ext.create("Ext.util.Filter",{
            property:d,
            value:c,
            anyMatch:f,
            caseSensitive:a
        }))
        }else{
        if(Ext.isArray(d)||d instanceof Ext.util.Filter){
            b=b.concat(d)
            }
        }
    e=function(g){
    var m=true,n=b.length,h;
    for(h=0;h<n;h++){
        var l=b[h],k=l.filterFn,j=l.scope;
        m=m&&k.call(j,g)
        }
        return m
    };
    
return this.filterBy(e)
},
filterBy:function(e,d){
    var h=this,a=new this.self(),g=h.keys,b=h.items,f=b.length,c;
    a.getKey=h.getKey;
    for(c=0;c<f;c++){
        if(e.call(d||h,b[c],g[c])){
            a.add(g[c],b[c])
            }
        }
    return a
},
findIndex:function(c,b,e,d,a){
    if(Ext.isEmpty(b,false)){
        return -1
        }
        b=this.createValueMatcher(b,d,a);
    return this.findIndexBy(function(f){
        return f&&b.test(f[c])
        },null,e)
    },
findIndexBy:function(e,d,h){
    var g=this,f=g.keys,b=g.items,c=h||0,a=b.length;
    for(;c<a;c++){
        if(e.call(d||g,b[c],f[c])){
            return c
            }
        }
    return -1
},
createValueMatcher:function(c,e,a,b){
    if(!c.exec){
        var d=Ext.String.escapeRegex;
        c=String(c);
        if(e===true){
            c=d(c)
            }else{
            c="^"+d(c);
            if(b===true){
                c+="$"
                }
            }
        c=new RegExp(c,a?"":"i")
    }
    return c
},
clone:function(){
    var e=this,f=new this.self(),d=e.keys,b=e.items,c=0,a=b.length;
    for(;c<a;c++){
        f.add(d[c],b[c])
        }
        f.getKey=e.getKey;
    return f
    }
});
Ext.define("Ext.util.Sortable",{
    isSortable:true,
    defaultSortDirection:"ASC",
    requires:["Ext.util.Sorter"],
    initSortable:function(){
        var a=this,b=a.sorters;
        a.sorters=Ext.create("Ext.util.AbstractMixedCollection",false,function(c){
            return c.id||c.property
            });
        if(b){
            a.sorters.addAll(a.decodeSorters(b))
            }
        },
sort:function(g,f,c,e){
    var d=this,h,b,a;
    if(Ext.isArray(g)){
        e=c;
        c=f;
        a=g
        }else{
        if(Ext.isObject(g)){
            e=c;
            c=f;
            a=[g]
            }else{
            if(Ext.isString(g)){
                h=d.sorters.get(g);
                if(!h){
                    h={
                        property:g,
                        direction:f
                    };
                    
                    a=[h]
                    }else{
                    if(f===undefined){
                        h.toggle()
                        }else{
                        h.setDirection(f)
                        }
                    }
            }
    }
}
if(a&&a.length){
    a=d.decodeSorters(a);
    if(Ext.isString(c)){
        if(c==="prepend"){
            g=d.sorters.clone().items;
            d.sorters.clear();
            d.sorters.addAll(a);
            d.sorters.addAll(g)
            }else{
            d.sorters.addAll(a)
            }
        }else{
    d.sorters.clear();
    d.sorters.addAll(a)
    }
    if(e!==false){
    d.onBeforeSort(a)
    }
}
if(e!==false){
    g=d.sorters.items;
    if(g.length){
        b=function(l,k){
            var j=g[0].sort(l,k),n=g.length,m;
            for(m=1;m<n;m++){
                j=j||g[m].sort.call(this,l,k)
                }
                return j
            };
            
        d.doSort(b)
        }
    }
return g
},
onBeforeSort:Ext.emptyFn,
decodeSorters:function(f){
    if(!Ext.isArray(f)){
        if(f===undefined){
            f=[]
            }else{
            f=[f]
            }
        }
    var d=f.length,g=Ext.util.Sorter,a=this.model?this.model.prototype.fields:null,e,b,c;
for(c=0;c<d;c++){
    b=f[c];
    if(!(b instanceof g)){
        if(Ext.isString(b)){
            b={
                property:b
            }
        }
        Ext.applyIf(b,{
        root:this.sortRoot,
        direction:"ASC"
    });
    if(b.fn){
        b.sorterFn=b.fn
        }
        if(typeof b=="function"){
        b={
            sorterFn:b
        }
    }
    if(a&&!b.transform){
    e=a.get(b.property);
    b.transform=e?e.sortType:undefined
    }
    f[c]=Ext.create("Ext.util.Sorter",b)
    }
}
return f
},
getSorters:function(){
    return this.sorters.items
    }
});
Ext.define("Ext.util.MixedCollection",{
    extend:"Ext.util.AbstractMixedCollection",
    mixins:{
        sortable:"Ext.util.Sortable"
    },
    constructor:function(){
        var a=this;
        a.callParent(arguments);
        a.addEvents("sort");
        a.mixins.sortable.initSortable.call(a)
        },
    doSort:function(a){
        this.sortBy(a)
        },
    _sort:function(k,a,j){
        var h=this,d,e,b=String(a).toUpperCase()=="DESC"?-1:1,g=[],l=h.keys,f=h.items;
        j=j||function(i,c){
            return i-c
            };
            
        for(d=0,e=f.length;d<e;d++){
            g[g.length]={
                key:l[d],
                value:f[d],
                index:d
            }
        }
        Ext.Array.sort(g,function(i,c){
        var m=j(i[k],c[k])*b;
        if(m===0){
            m=(i.index<c.index?-1:1)
            }
            return m
        });
    for(d=0,e=g.length;d<e;d++){
        f[d]=g[d].value;
        l[d]=g[d].key
        }
        h.fireEvent("sort",h)
    },
sortBy:function(c){
    var g=this,b=g.items,f=g.keys,e=b.length,a=[],d;
    for(d=0;d<e;d++){
        a[d]={
            key:f[d],
            value:b[d],
            index:d
        }
    }
    Ext.Array.sort(a,function(i,h){
    var j=c(i.value,h.value);
    if(j===0){
        j=(i.index<h.index?-1:1)
        }
        return j
    });
for(d=0;d<e;d++){
    b[d]=a[d].value;
    f[d]=a[d].key
    }
    g.fireEvent("sort",g,b,f)
    },
reorder:function(d){
    var g=this,b=g.items,c=0,f=b.length,a=[],e=[],h;
    g.suspendEvents();
    for(h in d){
        a[d[h]]=b[h]
        }
        for(c=0;c<f;c++){
        if(d[c]==undefined){
            e.push(b[c])
            }
        }
    for(c=0;c<f;c++){
    if(a[c]==undefined){
        a[c]=e.shift()
        }
    }
g.clear();
g.addAll(a);
g.resumeEvents();
g.fireEvent("sort",g)
},
sortByKey:function(a,b){
    this._sort("key",a,b||function(d,c){
        var f=String(d).toUpperCase(),e=String(c).toUpperCase();
        return f>e?1:(f<e?-1:0)
        })
    }
});
Ext.define("Ext.fx.Manager",{
    singleton:true,
    requires:["Ext.util.MixedCollection","Ext.fx.target.Element","Ext.fx.target.CompositeElement","Ext.fx.target.Sprite","Ext.fx.target.CompositeSprite","Ext.fx.target.Component"],
    mixins:{
        queue:"Ext.fx.Queue"
    },
    constructor:function(){
        this.items=Ext.create("Ext.util.MixedCollection");
        this.mixins.queue.constructor.call(this)
        },
    interval:16,
    forceJS:true,
    createTarget:function(d){
        var b=this,c=!b.forceJS&&Ext.supports.Transitions,a;
        b.useCSS3=c;
        if(Ext.isString(d)){
            d=Ext.get(d)
            }
            if(d&&d.tagName){
            d=Ext.get(d);
            a=Ext.create("Ext.fx.target.Element"+(c?"CSS":""),d);
            b.targets.add(a);
            return a
            }
            if(Ext.isObject(d)){
            if(d.dom){
                a=Ext.create("Ext.fx.target.Element"+(c?"CSS":""),d)
                }else{
                if(d.isComposite){
                    a=Ext.create("Ext.fx.target.CompositeElement"+(c?"CSS":""),d)
                    }else{
                    if(d.isSprite){
                        a=Ext.create("Ext.fx.target.Sprite",d)
                        }else{
                        if(d.isCompositeSprite){
                            a=Ext.create("Ext.fx.target.CompositeSprite",d)
                            }else{
                            if(d.isComponent){
                                a=Ext.create("Ext.fx.target.Component",d)
                                }else{
                                if(d.isAnimTarget){
                                    return d
                                    }else{
                                    return null
                                    }
                                }
                        }
                }
        }
}
b.targets.add(a);
return a
}else{
    return null
    }
},
addAnim:function(c){
    var b=this.items,a=this.task;
    b.add(c);
    if(!a&&b.length){
        a=this.task={
            run:this.runner,
            interval:this.interval,
            scope:this
        };
        
        Ext.TaskManager.start(a)
        }
    },
removeAnim:function(c){
    var b=this.items,a=this.task;
    b.remove(c);
    if(a&&!b.length){
        Ext.TaskManager.stop(a);
        delete this.task
        }
    },
startingFilter:function(a){
    return a.paused===false&&a.running===false&&a.iterations>0
    },
runningFilter:function(a){
    return a.paused===false&&a.running===true&&a.isAnimator!==true
    },
runner:function(){
    var b=this,a=b.items;
    b.targetData={};
    
    b.targetArr={};
    
    b.timestamp=new Date();
    a.filterBy(b.startingFilter).each(b.startAnim,b);
    a.filterBy(b.runningFilter).each(b.runAnim,b);
    b.applyPendingAttrs()
    },
startAnim:function(a){
    a.start(this.timestamp)
    },
runAnim:function(d){
    if(!d){
        return
    }
    var c=this,b=d.target.getId(),f=c.useCSS3&&d.target.type=="element",a=c.timestamp-d.startTime,e,g;
    this.collectTargetData(d,a,f);
    if(f){
        d.target.setAttr(c.targetData[b],true);
        c.targetData[b]=[];
        c.collectTargetData(d,d.duration,f);
        d.paused=true;
        e=d.target.target;
        if(d.target.isComposite){
            e=d.target.target.last()
            }
            g={};
        
        g[Ext.supports.CSS3TransitionEnd]=d.lastFrame;
        g.scope=d;
        g.single=true;
        e.on(g)
        }else{
        if(a>=d.duration){
            c.applyPendingAttrs(true);
            delete c.targetData[b];
            delete c.targetArr[b];
            d.lastFrame()
            }
        }
},
collectTargetData:function(d,a,e){
    var b=d.target.getId(),f=this.targetData[b],c;
    if(!f){
        f=this.targetData[b]=[];
        this.targetArr[b]=d.target
        }
        c={
        duration:d.duration,
        easing:(e&&d.reverse)?d.easingFn.reverse().toCSS3():d.easing,
        attrs:{}
};

Ext.apply(c.attrs,d.runAnim(a));
f.push(c)
},
applyPendingAttrs:function(d){
    var c=this.targetData,b=this.targetArr,a;
    for(a in c){
        if(c.hasOwnProperty(a)){
            b[a].setAttr(c[a],false,d)
            }
        }
    }
});
Ext.define("Ext.fx.Animator",{
    mixins:{
        observable:"Ext.util.Observable"
    },
    requires:["Ext.fx.Manager"],
    isAnimator:true,
    duration:250,
    delay:0,
    delayStart:0,
    dynamic:false,
    easing:"ease",
    running:false,
    paused:false,
    damper:1,
    iterations:1,
    currentIteration:0,
    keyframeStep:0,
    animKeyFramesRE:/^(from|to|\d+%?)$/,
    constructor:function(a){
        var b=this;
        a=Ext.apply(b,a||{});
        b.config=a;
        b.id=Ext.id(null,"ext-animator-");
        b.addEvents("beforeanimate","keyframe","afteranimate");
        b.mixins.observable.constructor.call(b,a);
        b.timeline=[];
        b.createTimeline(b.keyframes);
        if(b.target){
            b.applyAnimator(b.target);
            Ext.fx.Manager.addAnim(b)
            }
        },
sorter:function(d,c){
    return d.pct-c.pct
    },
createTimeline:function(f){
    var j=this,m=[],k=j.to||{},c=j.duration,n,a,e,h,l,b,d,g;
    for(l in f){
        if(f.hasOwnProperty(l)&&j.animKeyFramesRE.test(l)){
            g={
                attrs:Ext.apply(f[l],k)
                };
                
            if(l=="from"){
                l=0
                }else{
                if(l=="to"){
                    l=100
                    }
                }
            g.pct=parseInt(l,10);
        m.push(g)
        }
    }
    Ext.Array.sort(m,j.sorter);
    h=m.length;
    for(e=0;e<h;e++){
    n=(m[e-1])?c*(m[e-1].pct/100):0;
    a=c*(m[e].pct/100);
    j.timeline.push({
        duration:a-n,
        attrs:m[e].attrs
        })
    }
},
applyAnimator:function(e){
    var j=this,k=[],n=j.timeline,f=j.reverse,h=n.length,b,g,a,d,m,l,c;
    if(j.fireEvent("beforeanimate",j)!==false){
        for(c=0;c<h;c++){
            b=n[c];
            m=b.attrs;
            g=m.easing||j.easing;
            a=m.damper||j.damper;
            delete m.easing;
            delete m.damper;
            b=Ext.create("Ext.fx.Anim",{
                target:e,
                easing:g,
                damper:a,
                duration:b.duration,
                paused:true,
                to:m
            });
            k.push(b)
            }
            j.animations=k;
        j.target=b.target;
        for(c=0;c<h-1;c++){
            b=k[c];
            b.nextAnim=k[c+1];
            b.on("afteranimate",function(){
                this.nextAnim.paused=false
                });
            b.on("afteranimate",function(){
                this.fireEvent("keyframe",this,++this.keyframeStep)
                },j)
            }
            k[h-1].on("afteranimate",function(){
            this.lastFrame()
            },j)
        }
    },
start:function(d){
    var e=this,c=e.delay,b=e.delayStart,a;
    if(c){
        if(!b){
            e.delayStart=d;
            return
        }else{
            a=d-b;
            if(a<c){
                return
            }else{
                d=new Date(b.getTime()+c)
                }
            }
    }
if(e.fireEvent("beforeanimate",e)!==false){
    e.startTime=d;
    e.running=true;
    e.animations[e.keyframeStep].paused=false
    }
},
lastFrame:function(){
    var c=this,a=c.iterations,b=c.currentIteration;
    b++;
    if(b<a){
        c.startTime=new Date();
        c.currentIteration=b;
        c.keyframeStep=0;
        c.applyAnimator(c.target);
        c.animations[c.keyframeStep].paused=false
        }else{
        c.currentIteration=0;
        c.end()
        }
    },
end:function(){
    var a=this;
    a.fireEvent("afteranimate",a,a.startTime,new Date()-a.startTime)
    }
});
Ext.define("Ext.fx.Anim",{
    mixins:{
        observable:"Ext.util.Observable"
    },
    requires:["Ext.fx.Manager","Ext.fx.Animator","Ext.fx.Easing","Ext.fx.CubicBezier","Ext.fx.PropertyHandler"],
    isAnimation:true,
    duration:250,
    delay:0,
    delayStart:0,
    dynamic:false,
    easing:"ease",
    damper:1,
    bezierRE:/^(?:cubic-)?bezier\(([^,]+),([^,]+),([^,]+),([^\)]+)\)/,
    reverse:false,
    running:false,
    paused:false,
    iterations:1,
    alternate:false,
    currentIteration:0,
    startTime:0,
    constructor:function(a){
        var b=this;
        a=a||{};
        
        if(a.keyframes){
            return Ext.create("Ext.fx.Animator",a)
            }
            a=Ext.apply(b,a);
        if(b.from===undefined){
            b.from={}
        }
        b.propHandlers={};
    
    b.config=a;
    b.target=Ext.fx.Manager.createTarget(b.target);
    b.easingFn=Ext.fx.Easing[b.easing];
    b.target.dynamic=b.dynamic;
    if(!b.easingFn){
        b.easingFn=String(b.easing).match(b.bezierRE);
        if(b.easingFn&&b.easingFn.length==5){
            var c=b.easingFn;
            b.easingFn=Ext.fx.cubicBezier(+c[1],+c[2],+c[3],+c[4])
            }
        }
    b.id=Ext.id(null,"ext-anim-");
    Ext.fx.Manager.addAnim(b);
    b.addEvents("beforeanimate","afteranimate","lastframe");
    b.mixins.observable.constructor.call(b,a);
    if(a.callback){
    b.on("afteranimate",a.callback,a.scope)
    }
    return b
},
setAttr:function(a,b){
    return Ext.fx.Manager.items.get(this.id).setAttr(this.target,a,b)
    },
initAttrs:function(){
    var e=this,g=e.from,h=e.to,f=e.initialFrom||{},c={},a,b,i,d;
    for(d in h){
        if(h.hasOwnProperty(d)){
            a=e.target.getAttr(d,g[d]);
            b=h[d];
            if(!Ext.fx.PropertyHandler[d]){
                if(Ext.isObject(b)){
                    i=e.propHandlers[d]=Ext.fx.PropertyHandler.object
                    }else{
                    i=e.propHandlers[d]=Ext.fx.PropertyHandler.defaultHandler
                    }
                }else{
            i=e.propHandlers[d]=Ext.fx.PropertyHandler[d]
            }
            c[d]=i.get(a,b,e.damper,f[d],d)
        }
    }
    e.currentAttrs=c
},
start:function(d){
    var e=this,c=e.delay,b=e.delayStart,a;
    if(c){
        if(!b){
            e.delayStart=d;
            return
        }else{
            a=d-b;
            if(a<c){
                return
            }else{
                d=new Date(b.getTime()+c)
                }
            }
    }
if(e.fireEvent("beforeanimate",e)!==false){
    e.startTime=d;
    if(!e.paused&&!e.currentAttrs){
        e.initAttrs()
        }
        e.running=true
    }
},
runAnim:function(k){
    var h=this,j=h.currentAttrs,d=h.duration,c=h.easingFn,b=h.propHandlers,f={},g,i,e,a;
    if(k>=d){
        k=d;
        a=true
        }
        if(h.reverse){
        k=d-k
        }
        for(e in j){
        if(j.hasOwnProperty(e)){
            i=j[e];
            g=a?1:c(k/d);
            f[e]=b[e].set(i,g)
            }
        }
    return f
},
lastFrame:function(){
    var c=this,a=c.iterations,b=c.currentIteration;
    b++;
    if(b<a){
        if(c.alternate){
            c.reverse=!c.reverse
            }
            c.startTime=new Date();
        c.currentIteration=b;
        c.paused=false
        }else{
        c.currentIteration=0;
        c.end();
        c.fireEvent("lastframe",c,c.startTime)
        }
    },
end:function(){
    var a=this;
    a.startTime=0;
    a.paused=false;
    a.running=false;
    Ext.fx.Manager.removeAnim(a);
    a.fireEvent("afteranimate",a,a.startTime)
    }
});
Ext.enableFx=true;
Ext.define("Ext.menu.Manager",{
    singleton:true,
    requires:["Ext.util.MixedCollection","Ext.util.KeyMap"],
    alternateClassName:"Ext.menu.MenuMgr",
    uses:["Ext.menu.Menu"],
    menus:{},
    groups:{},
    attached:false,
    lastShow:new Date(),
    init:function(){
        var a=this;
        a.active=Ext.create("Ext.util.MixedCollection");
        Ext.getDoc().addKeyListener(27,function(){
            if(a.active.length>0){
                a.hideAll()
                }
            },a)
    },
hideAll:function(){
    var a=this.active,b;
    if(a&&a.length>0){
        b=a.clone();
        b.each(function(c){
            c.hide()
            });
        return true
        }
        return false
    },
onHide:function(a){
    var b=this,c=b.active;
    c.remove(a);
    if(c.length<1){
        Ext.getDoc().un("mousedown",b.onMouseDown,b);
        b.attached=false
        }
    },
onShow:function(a){
    var e=this,f=e.active,d=f.last(),c=e.attached,b=a.getEl(),g;
    e.lastShow=new Date();
    f.add(a);
    if(!c){
        Ext.getDoc().on("mousedown",e.onMouseDown,e);
        e.attached=true
        }
        a.toFront()
    },
onBeforeHide:function(a){
    if(a.activeChild){
        a.activeChild.hide()
        }
        if(a.autoHideTimer){
        clearTimeout(a.autoHideTimer);
        delete a.autoHideTimer
        }
    },
onBeforeShow:function(a){
    var c=this.active,b=a.parentMenu;
    c.remove(a);
    if(!b&&!a.allowOtherMenus){
        this.hideAll()
        }else{
        if(b&&b.activeChild&&a!=b.activeChild){
            b.activeChild.hide()
            }
        }
},
onMouseDown:function(d){
    var b=this,c=b.active,a=b.lastShow;
    if(Ext.Date.getElapsed(a)>50&&c.length>0&&!d.getTarget("."+Ext.baseCSSPrefix+"menu")){
        b.hideAll()
        }
    },
register:function(b){
    var a=this;
    if(!a.active){
        a.init()
        }
        if(b.floating){
        a.menus[b.id]=b;
        b.on({
            beforehide:a.onBeforeHide,
            hide:a.onHide,
            beforeshow:a.onBeforeShow,
            show:a.onShow,
            scope:a
        })
        }
    },
get:function(b){
    var a=this.menus;
    if(typeof b=="string"){
        if(!a){
            return null
            }
            return a[b]
        }else{
        if(b.isMenu){
            return b
            }else{
            if(Ext.isArray(b)){
                return Ext.create("Ext.menu.Menu",{
                    items:b
                })
                }else{
                return Ext.ComponentManager.create(b,"menu")
                }
            }
    }
},
unregister:function(d){
    var a=this,b=a.menus,c=a.active;
    delete b[d.id];
    c.remove(d);
    d.un({
        beforehide:a.onBeforeHide,
        hide:a.onHide,
        beforeshow:a.onBeforeShow,
        show:a.onShow,
        scope:a
    })
    },
registerCheckable:function(c){
    var a=this.groups,b=c.group;
    if(b){
        if(!a[b]){
            a[b]=[]
            }
            a[b].push(c)
        }
    },
unregisterCheckable:function(c){
    var a=this.groups,b=c.group;
    if(b){
        Ext.Array.remove(a[b],c)
        }
    },
onCheckChange:function(d,f){
    var a=this.groups,c=d.group,b=0,h,e,g;
    if(c&&f){
        h=a[c];
        e=h.length;
        for(;b<e;b++){
            g=h[b];
            if(g!=d){
                g.setChecked(false)
                }
            }
        }
}
});
Ext.define("Ext.chart.Highlight",{
    requires:["Ext.fx.Anim"],
    highlight:false,
    highlightCfg:null,
    constructor:function(a){
        if(a.highlight){
            if(a.highlight!==true){
                this.highlightCfg=Ext.apply({},a.highlight)
                }else{
                this.highlightCfg={
                    fill:"#fdd",
                    radius:20,
                    lineWidth:5,
                    stroke:"#f55"
                }
            }
        }
},
highlightItem:function(j){
    if(!j){
        return
    }
    var f=this,i=j.sprite,a=f.highlightCfg,d=f.chart.surface,c=f.chart.animate,b,h,g,e;
    if(!f.highlight||!i||i._highlighted){
        return
    }
    if(i._anim){
        i._anim.paused=true
        }
        i._highlighted=true;
    if(!i._defaults){
        i._defaults=Ext.apply({},i.attr);
        h={};
        
        g={};
        
        for(b in a){
            if(!(b in i._defaults)){
                i._defaults[b]=d.availableAttrs[b]
                }
                h[b]=i._defaults[b];
            g[b]=a[b];
            if(Ext.isObject(a[b])){
                h[b]={};
                
                g[b]={};
                
                Ext.apply(i._defaults[b],i.attr[b]);
                Ext.apply(h[b],i._defaults[b]);
                for(e in i._defaults[b]){
                    if(!(e in a[b])){
                        g[b][e]=h[b][e]
                        }else{
                        g[b][e]=a[b][e]
                        }
                    }
                for(e in a[b]){
                if(!(e in g[b])){
                    g[b][e]=a[b][e]
                    }
                }
            }
        }
    i._from=h;
i._to=g;
i._endStyle=g
}
if(c){
    i._anim=Ext.create("Ext.fx.Anim",{
        target:i,
        from:i._from,
        to:i._to,
        duration:150
    })
    }else{
    i.setAttributes(i._to,true)
    }
},
unHighlightItem:function(){
    if(!this.highlight||!this.items){
        return
    }
    var h=this,g=h.items,f=g.length,a=h.highlightCfg,c=h.chart.animate,e=0,d,b,j;
    for(;e<f;e++){
        if(!g[e]){
            continue
        }
        j=g[e].sprite;
        if(j&&j._highlighted){
            if(j._anim){
                j._anim.paused=true
                }
                d={};
            
            for(b in a){
                if(Ext.isObject(j._defaults[b])){
                    d[b]={};
                    
                    Ext.apply(d[b],j._defaults[b])
                    }else{
                    d[b]=j._defaults[b]
                    }
                }
            if(c){
            j._endStyle=d;
            j._anim=Ext.create("Ext.fx.Anim",{
                target:j,
                to:d,
                duration:150
            })
            }else{
            j.setAttributes(d,true)
            }
            delete j._highlighted
        }
    }
},
cleanHighlights:function(){
    if(!this.highlight){
        return
    }
    var d=this.group,c=this.markerGroup,b=0,a;
    for(a=d.getCount();b<a;b++){
        delete d.getAt(b)._defaults
        }
        if(c){
        for(a=c.getCount();b<a;b++){
            delete c.getAt(b)._defaults
            }
        }
    }
});
Ext.define("Ext.data.AbstractStore",{
    requires:["Ext.util.MixedCollection","Ext.data.Operation","Ext.util.Filter"],
    mixins:{
        observable:"Ext.util.Observable",
        sortable:"Ext.util.Sortable"
    },
    statics:{
        create:function(a){
            if(!a.isStore){
                if(!a.type){
                    a.type="store"
                    }
                    a=Ext.createByAlias("store."+a.type,a)
                }
                return a
            }
        },
remoteSort:false,
remoteFilter:false,
autoLoad:false,
autoSync:false,
batchUpdateMode:"operation",
filterOnLoad:true,
sortOnLoad:true,
implicitModel:false,
defaultProxyType:"memory",
isDestroyed:false,
isStore:true,
sortRoot:"data",
constructor:function(a){
    var c=this,b;
    c.addEvents("add","remove","update","datachanged","beforeload","load","beforesync","clear");
    Ext.apply(c,a);
    c.removed=[];
    c.mixins.observable.constructor.apply(c,arguments);
    c.model=Ext.ModelManager.getModel(c.model);
    Ext.applyIf(c,{
        modelDefaults:{}
    });
if(!c.model&&c.fields){
    c.model=Ext.define("Ext.data.Store.ImplicitModel-"+(c.storeId||Ext.id()),{
        extend:"Ext.data.Model",
        fields:c.fields,
        proxy:c.proxy||c.defaultProxyType
        });
    delete c.fields;
    c.implicitModel=true
    }
    c.setProxy(c.proxy||c.model.getProxy());
    if(c.id&&!c.storeId){
    c.storeId=c.id;
    delete c.id
    }
    if(c.storeId){
    Ext.data.StoreManager.register(c)
    }
    c.mixins.sortable.initSortable.call(c);
    b=c.decodeFilters(c.filters);
    c.filters=Ext.create("Ext.util.MixedCollection");
    c.filters.addAll(b)
    },
setProxy:function(a){
    var b=this;
    if(a instanceof Ext.data.proxy.Proxy){
        a.setModel(b.model)
        }else{
        if(Ext.isString(a)){
            a={
                type:a
            }
        }
        Ext.applyIf(a,{
        model:b.model
        });
    a=Ext.createByAlias("proxy."+a.type,a)
    }
    b.proxy=a;
return b.proxy
},
getProxy:function(){
    return this.proxy
    },
create:function(e,c){
    var d=this,a=Ext.ModelManager.create(Ext.applyIf(e,d.modelDefaults),d.model.modelName),b;
    c=c||{};
    
    Ext.applyIf(c,{
        action:"create",
        records:[a]
        });
    b=Ext.create("Ext.data.Operation",c);
    d.proxy.create(b,d.onProxyWrite,d);
    return a
    },
read:function(){
    return this.load.apply(this,arguments)
    },
onProxyRead:Ext.emptyFn,
update:function(b){
    var c=this,a;
    b=b||{};
    
    Ext.applyIf(b,{
        action:"update",
        records:c.getUpdatedRecords()
        });
    a=Ext.create("Ext.data.Operation",b);
    return c.proxy.update(a,c.onProxyWrite,c)
    },
onProxyWrite:function(b){
    var c=this,d=b.wasSuccessful(),a=b.getRecords();
    switch(b.action){
        case"create":
            c.onCreateRecords(a,b,d);
            break;
        case"update":
            c.onUpdateRecords(a,b,d);
            break;
        case"destroy":
            c.onDestroyRecords(a,b,d);
            break
            }
            if(d){
        c.fireEvent("write",c,b);
        c.fireEvent("datachanged",c)
        }
        Ext.callback(b.callback,b.scope||c,[a,b,d])
    },
destroy:function(b){
    var c=this,a;
    b=b||{};
    
    Ext.applyIf(b,{
        action:"destroy",
        records:c.getRemovedRecords()
        });
    a=Ext.create("Ext.data.Operation",b);
    return c.proxy.destroy(a,c.onProxyWrite,c)
    },
onBatchOperationComplete:function(b,a){
    return this.onProxyWrite(a)
    },
onBatchComplete:function(c,a){
    var f=this,b=c.operations,e=b.length,d;
    f.suspendEvents();
    for(d=0;d<e;d++){
        f.onProxyWrite(b[d])
        }
        f.resumeEvents();
    f.fireEvent("datachanged",f)
    },
onBatchException:function(b,a){},
filterNew:function(a){
    return a.phantom===true&&a.isValid()
    },
getNewRecords:function(){
    return[]
    },
getUpdatedRecords:function(){
    return[]
    },
filterUpdated:function(a){
    return a.dirty===true&&a.phantom!==true&&a.isValid()
    },
getRemovedRecords:function(){
    return this.removed
    },
filter:function(a,b){},
decodeFilters:function(e){
    if(!Ext.isArray(e)){
        if(e===undefined){
            e=[]
            }else{
            e=[e]
            }
        }
    var d=e.length,a=Ext.util.Filter,b,c;
for(c=0;c<d;c++){
    b=e[c];
    if(!(b instanceof a)){
        Ext.apply(b,{
            root:"data"
        });
        if(b.fn){
            b.filterFn=b.fn
            }
            if(typeof b=="function"){
            b={
                filterFn:b
            }
        }
        e[c]=new a(b)
    }
}
return e
},
clearFilter:function(a){},
isFiltered:function(){},
filterBy:function(b,a){},
sync:function(){
    var d=this,b={},e=d.getNewRecords(),c=d.getUpdatedRecords(),a=d.getRemovedRecords(),f=false;
    if(e.length>0){
        b.create=e;
        f=true
        }
        if(c.length>0){
        b.update=c;
        f=true
        }
        if(a.length>0){
        b.destroy=a;
        f=true
        }
        if(f&&d.fireEvent("beforesync",b)!==false){
        d.proxy.batch(b,d.getBatchListeners())
        }
    },
getBatchListeners:function(){
    var b=this,a={
        scope:b,
        exception:b.onBatchException
        };
        
    if(b.batchUpdateMode=="operation"){
        a.operationcomplete=b.onBatchOperationComplete
        }else{
        a.complete=b.onBatchComplete
        }
        return a
    },
save:function(){
    return this.sync.apply(this,arguments)
    },
load:function(b){
    var c=this,a;
    b=b||{};
    
    Ext.applyIf(b,{
        action:"read",
        filters:c.filters.items,
        sorters:c.getSorters()
        });
    a=Ext.create("Ext.data.Operation",b);
    if(c.fireEvent("beforeload",c,a)!==false){
        c.loading=true;
        c.proxy.read(a,c.onProxyLoad,c)
        }
        return c
    },
afterEdit:function(a){
    var b=this;
    if(b.autoSync){
        b.sync()
        }
        b.fireEvent("update",b,a,Ext.data.Model.EDIT)
    },
afterReject:function(a){
    this.fireEvent("update",this,a,Ext.data.Model.REJECT)
    },
afterCommit:function(a){
    this.fireEvent("update",this,a,Ext.data.Model.COMMIT)
    },
clearData:Ext.emptyFn,
destroyStore:function(){
    var a=this;
    if(!a.isDestroyed){
        if(a.storeId){
            Ext.data.StoreManager.unregister(a)
            }
            a.clearData();
        a.data=null;
        a.tree=null;
        a.reader=a.writer=null;
        a.clearListeners();
        a.isDestroyed=true;
        if(a.implicitModel){
            Ext.destroy(a.model)
            }
        }
},
doSort:function(a){
    var b=this;
    if(b.remoteSort){
        b.load()
        }else{
        b.data.sortBy(a);
        b.fireEvent("datachanged",b)
        }
    },
getCount:Ext.emptyFn,
getById:Ext.emptyFn,
removeAll:Ext.emptyFn,
isLoading:function(){
    return this.loading
    }
});
Ext.define("Ext.data.StoreManager",{
    extend:"Ext.util.MixedCollection",
    alternateClassName:["Ext.StoreMgr","Ext.data.StoreMgr","Ext.StoreManager"],
    singleton:true,
    uses:["Ext.data.ArrayStore"],
    register:function(){
        for(var a=0,b;(b=arguments[a]);a++){
            this.add(b)
            }
        },
unregister:function(){
    for(var a=0,b;(b=arguments[a]);a++){
        this.remove(this.lookup(b))
        }
    },
lookup:function(c){
    if(Ext.isArray(c)){
        var b=["field1"],e=!Ext.isArray(c[0]),f=c,d,a;
        if(e){
            f=[];
            for(d=0,a=c.length;d<a;++d){
                f.push([c[d]])
                }
            }else{
        for(d=2,a=c[0].length;d<=a;++d){
            b.push("field"+d)
            }
        }
        return Ext.create("Ext.data.ArrayStore",{
    data:f,
    fields:b,
    autoDestroy:true,
    autoCreated:true,
    expanded:e
})
}
if(Ext.isString(c)){
    return this.get(c)
    }else{
    return Ext.data.AbstractStore.create(c)
    }
},
getKey:function(a){
    return a.storeId
    }
},function(){
    Ext.regStore=function(c,b){
        var a;
        if(Ext.isObject(c)){
            b=c
            }else{
            b.storeId=c
            }
            if(b instanceof Ext.data.Store){
            a=b
            }else{
            a=Ext.create("Ext.data.Store",b)
            }
            return Ext.data.StoreManager.register(a)
        };
        
    Ext.getStore=function(a){
        return Ext.data.StoreManager.lookup(a)
        }
    });
Ext.define("Ext.LoadMask",{
    mixins:{
        observable:"Ext.util.Observable"
    },
    requires:["Ext.data.StoreManager"],
    msg:"Loading...",
    msgCls:Ext.baseCSSPrefix+"mask-loading",
    useMsg:true,
    disabled:false,
    constructor:function(b,a){
        var c=this;
        if(b.isComponent){
            c.bindComponent(b)
            }else{
            c.el=Ext.get(b)
            }
            Ext.apply(c,a);
        c.addEvents("beforeshow","show","hide");
        if(c.store){
            c.bindStore(c.store,true)
            }
            c.mixins.observable.constructor.call(c,a)
        },
    bindComponent:function(a){
        var c=this,b={
            resize:c.onComponentResize,
            scope:c
        };
        
        if(a.el){
            c.onComponentRender(a)
            }else{
            b.render={
                fn:c.onComponentRender,
                scope:c,
                single:true
            }
        }
        c.mon(a,b)
    },
onComponentRender:function(a){
    this.el=a.getContentTarget()
    },
onComponentResize:function(b,a,c){
    this.el.isMasked()
    },
bindStore:function(a,b){
    var c=this;
    if(!b&&c.store){
        c.mun(c.store,{
            scope:c,
            beforeload:c.onBeforeLoad,
            load:c.onLoad,
            exception:c.onLoad
            });
        if(!a){
            c.store=null
            }
        }
    if(a){
    a=Ext.data.StoreManager.lookup(a);
    c.mon(a,{
        scope:c,
        beforeload:c.onBeforeLoad,
        load:c.onLoad,
        exception:c.onLoad
        })
    }
    c.store=a;
if(a&&a.isLoading()){
    c.onBeforeLoad()
    }
},
disable:function(){
    var a=this;
    a.disabled=true;
    if(a.loading){
        a.onLoad()
        }
    },
enable:function(){
    this.disabled=false
    },
isDisabled:function(){
    return this.disabled
    },
onLoad:function(){
    var a=this;
    a.loading=false;
    a.el.unmask();
    a.fireEvent("hide",a,a.el,a.store)
    },
onBeforeLoad:function(){
    var a=this;
    if(!a.disabled&&!a.loading&&a.fireEvent("beforeshow",a,a.el,a.store)!==false){
        if(a.useMsg){
            a.el.mask(a.msg,a.msgCls,false)
            }else{
            a.el.mask()
            }
            a.fireEvent("show",a,a.el,a.store);
        a.loading=true
        }
    },
show:function(){
    this.onBeforeLoad()
    },
hide:function(){
    this.onLoad()
    },
destroy:function(){
    this.hide();
    this.clearListeners()
    }
});
Ext.define("Ext.state.Manager",{
    singleton:true,
    requires:["Ext.state.Provider"],
    constructor:function(){
        this.provider=Ext.create("Ext.state.Provider")
        },
    setProvider:function(a){
        this.provider=a
        },
    get:function(b,a){
        return this.provider.get(b,a)
        },
    set:function(a,b){
        this.provider.set(a,b)
        },
    clear:function(a){
        this.provider.clear(a)
        },
    getProvider:function(){
        return this.provider
        }
    });
Ext.define("Ext.state.Stateful",{
    mixins:{
        observable:"Ext.util.Observable"
    },
    requires:["Ext.state.Manager"],
    stateful:true,
    saveDelay:100,
    autoGenIdRe:/^((\w+-)|(ext-comp-))\d{4,}$/i,
    constructor:function(a){
        var b=this;
        a=a||{};
        
        if(Ext.isDefined(a.stateful)){
            b.stateful=a.stateful
            }
            if(Ext.isDefined(a.saveDelay)){
            b.saveDelay=a.saveDelay
            }
            b.stateId=b.stateId||a.stateId;
        if(!b.stateEvents){
            b.stateEvents=[]
            }
            if(a.stateEvents){
            b.stateEvents.concat(a.stateEvents)
            }
            this.addEvents("beforestaterestore","staterestore","beforestatesave","statesave");
        b.mixins.observable.constructor.call(b);
        if(b.stateful!==false){
            b.initStateEvents();
            b.initState()
            }
        },
initStateEvents:function(){
    this.addStateEvents(this.stateEvents)
    },
addStateEvents:function(c){
    if(!Ext.isArray(c)){
        c=[c]
        }
        var d=this,b=0,a=c.length;
    for(;b<a;++b){
        d.on(c[b],d.onStateChange,d)
        }
    },
onStateChange:function(){
    var b=this,a=b.saveDelay;
    if(a>0){
        if(!b.stateTask){
            b.stateTask=Ext.create("Ext.util.DelayedTask",b.saveState,b)
            }
            b.stateTask.delay(b.saveDelay)
        }else{
        b.saveState()
        }
    },
saveState:function(){
    var a=this,c,b;
    if(a.stateful!==false){
        c=a.getStateId();
        if(c){
            b=a.getState();
            if(a.fireEvent("beforestatesave",a,b)!==false){
                Ext.state.Manager.set(c,b);
                a.fireEvent("statesave",a,b)
                }
            }
    }
},
getState:function(){
    return null
    },
applyState:function(a){
    if(a){
        Ext.apply(this,a)
        }
    },
getStateId:function(){
    var a=this,b=a.stateId;
    if(!b){
        b=a.autoGenIdRe.test(String(a.id))?null:a.id
        }
        return b
    },
initState:function(){
    var a=this,c=a.getStateId(),b;
    if(a.stateful!==false){
        if(c){
            b=Ext.state.Manager.get(c);
            if(b){
                b=Ext.apply({},b);
                if(a.fireEvent("beforestaterestore",a,b)!==false){
                    a.applyState(b);
                    a.fireEvent("staterestore",a,b)
                    }
                }
        }
}
},
destroy:function(){
    var a=this.stateTask;
    if(a){
        a.cancel()
        }
        this.clearListeners()
    }
});
Ext.define("Ext.AbstractComponent",{
    mixins:{
        observable:"Ext.util.Observable",
        animate:"Ext.util.Animate",
        state:"Ext.state.Stateful"
    },
    requires:["Ext.PluginManager","Ext.ComponentManager","Ext.core.Element","Ext.core.DomHelper","Ext.XTemplate","Ext.ComponentQuery","Ext.LoadMask","Ext.ComponentLoader","Ext.EventManager","Ext.layout.Layout","Ext.layout.component.Auto"],
    uses:["Ext.ZIndexManager"],
    statics:{
        AUTO_ID:1000
    },
    isComponent:true,
    getAutoId:function(){
        return ++Ext.AbstractComponent.AUTO_ID
        },
    renderTpl:null,
    tplWriteMode:"overwrite",
    baseCls:Ext.baseCSSPrefix+"component",
    disabledCls:Ext.baseCSSPrefix+"item-disabled",
    ui:"default",
    uiCls:[],
    hidden:false,
    disabled:false,
    draggable:false,
    floating:false,
    hideMode:"display",
    styleHtmlContent:false,
    styleHtmlCls:Ext.baseCSSPrefix+"html",
    allowDomMove:true,
    autoShow:false,
    autoRender:false,
    needsLayout:false,
    rendered:false,
    componentLayoutCounter:0,
    weight:0,
    trimRe:/^\s+|\s+$/g,
    spacesRe:/\s+/,
    maskOnDisable:true,
    constructor:function(b){
        var d=this,c,a;
        b=b||{};
        
        d.initialConfig=b;
        Ext.apply(d,b);
        d.addEvents("beforeactivate","activate","beforedeactivate","deactivate","added","disable","enable","beforeshow","show","beforehide","hide","removed","beforerender","render","afterrender","beforedestroy","destroy","resize","move");
        d.getId();
        d.mons=[];
        d.additionalCls=[];
        d.renderData=d.renderData||{};
        
        d.renderSelectors=d.renderSelectors||{};
        
        if(d.plugins){
            d.plugins=[].concat(d.plugins);
            for(c=0,a=d.plugins.length;c<a;c++){
                d.plugins[c]=d.constructPlugin(d.plugins[c])
                }
            }
            d.initComponent();
    Ext.ComponentManager.register(d);
    d.mixins.observable.constructor.call(d);
    d.mixins.state.constructor.call(d,b);
    this.addStateEvents("resize");
    if(d.plugins){
        d.plugins=[].concat(d.plugins);
        for(c=0,a=d.plugins.length;c<a;c++){
            d.plugins[c]=d.initPlugin(d.plugins[c])
            }
        }
        d.loader=d.getLoader();
    if(d.renderTo){
    d.render(d.renderTo)
    }
    if(d.autoShow){
    d.show()
    }
},
initComponent:Ext.emptyFn,
getState:function(){
    var f=this,e=f.ownerCt?(f.shadowOwnerCt||f.ownerCt).getLayout():null,g={
        collapsed:f.collapsed
        },c=f.width,b=f.height,a=f.collapseMemento,d;
    if(f.collapsed&&a){
        if(Ext.isDefined(a.data.width)){
            c=a.width
            }
            if(Ext.isDefined(a.data.height)){
            b=a.height
            }
        }
    if(e&&f.flex){
    g.flex=f.flex;
    if(e.perpendicularPrefix){
        g[e.perpendicularPrefix]=f["get"+e.perpendicularPrefixCap]()
        }else{}
}else{
    if(e&&f.anchor){
        g.anchor=f.anchor;
        d=f.anchor.split(" ").concat(null);
        if(!d[0]){
            if(f.width){
                g.width=c
                }
            }
        if(!d[1]){
        if(f.height){
            g.height=b
            }
        }
}else{
    if(f.width){
        g.width=c
        }
        if(f.height){
        g.height=b
        }
    }
}
if(g.width==f.initialConfig.width){
    delete g.width
    }
    if(g.height==f.initialConfig.height){
    delete g.height
    }
    if(e&&e.align&&(e.align.indexOf("stretch")!==-1)){
    delete g[e.perpendicularPrefix]
}
return g
},
show:Ext.emptyFn,
animate:function(b){
    var f=this,j;
    b=b||{};
    
    j=b.to||{};
    
    if(Ext.fx.Manager.hasFxBlock(f.id)){
        return f
        }
        if(!b.dynamic&&(j.height||j.width)){
        var e=f.getWidth(),k=e,d=f.getHeight(),c=d,a=false;
        if(j.height&&j.height>d){
            c=j.height;
            a=true
            }
            if(j.width&&j.width>e){
            k=j.width;
            a=true
            }
            if(a){
            var i=!Ext.isNumber(f.width),g=!Ext.isNumber(f.height);
            f.componentLayout.childrenChanged=true;
            f.setSize(k,c,f.ownerCt);
            f.el.setSize(e,d);
            if(i){
                delete f.width
                }
                if(g){
                delete f.height
                }
            }
    }
return f.mixins.animate.animate.apply(f,arguments)
},
findLayoutController:function(){
    return this.findParentBy(function(a){
        return !a.ownerCt||(a.layout.layoutBusy&&!a.ownerCt.layout.layoutBusy)
        })
    },
onShow:function(){
    var a=this.needsLayout;
    if(Ext.isObject(a)){
        this.doComponentLayout(a.width,a.height,a.isSetSize,a.ownerCt)
        }
    },
constructPlugin:function(a){
    if(a.ptype&&typeof a.init!="function"){
        a.cmp=this;
        a=Ext.PluginManager.create(a)
        }else{
        if(typeof a=="string"){
            a=Ext.PluginManager.create({
                ptype:a,
                cmp:this
            })
            }
        }
    return a
},
initPlugin:function(a){
    a.init(this);
    return a
    },
doAutoRender:function(){
    var a=this;
    if(a.floating){
        a.render(document.body)
        }else{
        a.render(Ext.isBoolean(a.autoRender)?Ext.getBody():a.autoRender)
        }
    },
render:function(b,a){
    var c=this;
    if(!c.rendered&&c.fireEvent("beforerender",c)!==false){
        if(c.el){
            c.el=Ext.get(c.el)
            }
            if(c.floating){
            c.onFloatRender()
            }
            b=c.initContainer(b);
        c.onRender(b,a);
        c.el.setVisibilityMode(Ext.core.Element[c.hideMode.toUpperCase()]);
        if(c.overCls){
            c.el.hover(c.addOverCls,c.removeOverCls,c)
            }
            c.fireEvent("render",c);
        c.initContent();
        c.afterRender(b);
        c.fireEvent("afterrender",c);
        c.initEvents();
        if(c.hidden){
            c.el.hide()
            }
            if(c.disabled){
            c.disable(true)
            }
        }
    return c
},
onRender:function(b,a){
    var f=this,d=f.el,e=f.initStyles(),h,g,c;
    a=f.getInsertPosition(a);
    if(!d){
        if(a){
            d=Ext.core.DomHelper.insertBefore(a,f.getElConfig(),true)
            }else{
            d=Ext.core.DomHelper.append(b,f.getElConfig(),true)
            }
        }else{
    if(f.allowDomMove!==false){
        if(a){
            b.dom.insertBefore(d.dom,a)
            }else{
            b.dom.appendChild(d.dom)
            }
        }
}
if(Ext.scopeResetCSS&&!f.ownerCt){
    if(d.dom==Ext.getBody().dom){
        d.parent().addCls(Ext.baseCSSPrefix+"reset")
        }else{
        f.resetEl=d.wrap({
            cls:Ext.baseCSSPrefix+"reset"
            })
        }
    }
f.setUI(f.ui);
d.addCls(f.initCls());
d.setStyle(e);
f.el=d;
f.initFrame();
h=f.initRenderTpl();
if(h){
    g=f.initRenderData();
    h.append(f.getTargetEl(),g)
    }
    f.applyRenderSelectors();
f.rendered=true
},
afterRender:function(){
    var a=this,c,b;
    a.getComponentLayout();
    if(!a.ownerCt||(a.height||a.width)){
        a.setSize(a.width,a.height)
        }else{
        a.renderChildren()
        }
        if(a.floating&&(a.x===undefined||a.y===undefined)){
        if(a.floatParent){
            b=a.el.getAlignToXY(a.floatParent.getTargetEl(),"c-c");
            c=a.floatParent.getTargetEl().translatePoints(b[0],b[1])
            }else{
            b=a.el.getAlignToXY(a.container,"c-c");
            c=a.container.translatePoints(b[0],b[1])
            }
            a.x=a.x===undefined?c.left:a.x;
        a.y=a.y===undefined?c.top:a.y
        }
        if(Ext.isDefined(a.x)||Ext.isDefined(a.y)){
        a.setPosition(a.x,a.y)
        }
        if(a.styleHtmlContent){
        a.getTargetEl().addCls(a.styleHtmlCls)
        }
    },
renderChildren:function(){
    var b=this,a=b.getComponentLayout();
    b.suspendLayout=true;
    a.renderChildren();
    delete b.suspendLayout
    },
frameCls:Ext.baseCSSPrefix+"frame",
frameElementCls:{
    tl:[],
    tc:[],
    tr:[],
    ml:[],
    mc:[],
    mr:[],
    bl:[],
    bc:[],
    br:[]
},
frameTpl:['<tpl if="top">','<tpl if="left"><div class="{frameCls}-tl {baseCls}-tl {baseCls}-{ui}-tl<tpl if="uiCls"><tpl for="uiCls"> {parent.baseCls}-{parent.ui}-{.}-tl</tpl></tpl>" style="background-position: {tl}; padding-left: {frameWidth}px" role="presentation"></tpl>','<tpl if="right"><div class="{frameCls}-tr {baseCls}-tr {baseCls}-{ui}-tr<tpl if="uiCls"><tpl for="uiCls"> {parent.baseCls}-{parent.ui}-{.}-tr</tpl></tpl>" style="background-position: {tr}; padding-right: {frameWidth}px" role="presentation"></tpl>','<div class="{frameCls}-tc {baseCls}-tc {baseCls}-{ui}-tc<tpl if="uiCls"><tpl for="uiCls"> {parent.baseCls}-{parent.ui}-{.}-tc</tpl></tpl>" style="background-position: {tc}; height: {frameWidth}px" role="presentation"></div>','<tpl if="right"></div></tpl>','<tpl if="left"></div></tpl>',"</tpl>",'<tpl if="left"><div class="{frameCls}-ml {baseCls}-ml {baseCls}-{ui}-ml<tpl if="uiCls"><tpl for="uiCls"> {parent.baseCls}-{parent.ui}-{.}-ml</tpl></tpl>" style="background-position: {ml}; padding-left: {frameWidth}px" role="presentation"></tpl>','<tpl if="right"><div class="{frameCls}-mr {baseCls}-mr {baseCls}-{ui}-mr<tpl if="uiCls"><tpl for="uiCls"> {parent.baseCls}-{parent.ui}-{.}-mr</tpl></tpl>" style="background-position: {mr}; padding-right: {frameWidth}px" role="presentation"></tpl>','<div class="{frameCls}-mc {baseCls}-mc {baseCls}-{ui}-mc<tpl if="uiCls"><tpl for="uiCls"> {parent.baseCls}-{parent.ui}-{.}-mc</tpl></tpl>" role="presentation"></div>','<tpl if="right"></div></tpl>','<tpl if="left"></div></tpl>','<tpl if="bottom">','<tpl if="left"><div class="{frameCls}-bl {baseCls}-bl {baseCls}-{ui}-bl<tpl if="uiCls"><tpl for="uiCls"> {parent.baseCls}-{parent.ui}-{.}-bl</tpl></tpl>" style="background-position: {bl}; padding-left: {frameWidth}px" role="presentation"></tpl>','<tpl if="right"><div class="{frameCls}-br {baseCls}-br {baseCls}-{ui}-br<tpl if="uiCls"><tpl for="uiCls"> {parent.baseCls}-{parent.ui}-{.}-br</tpl></tpl>" style="background-position: {br}; padding-right: {frameWidth}px" role="presentation"></tpl>','<div class="{frameCls}-bc {baseCls}-bc {baseCls}-{ui}-bc<tpl if="uiCls"><tpl for="uiCls"> {parent.baseCls}-{parent.ui}-{.}-bc</tpl></tpl>" style="background-position: {bc}; height: {frameWidth}px" role="presentation"></div>','<tpl if="right"></div></tpl>','<tpl if="left"></div></tpl>',"</tpl>"],
frameTableTpl:["<table><tbody>",'<tpl if="top">',"<tr>",'<tpl if="left"><td class="{frameCls}-tl {baseCls}-tl {baseCls}-{ui}-tl<tpl if="uiCls"><tpl for="uiCls"> {parent.baseCls}-{parent.ui}-{.}-tl</tpl></tpl>" style="background-position: {tl}; padding-left:{frameWidth}px" role="presentation"></td></tpl>','<td class="{frameCls}-tc {baseCls}-tc {baseCls}-{ui}-tc<tpl if="uiCls"><tpl for="uiCls"> {parent.baseCls}-{parent.ui}-{.}-tc</tpl></tpl>" style="background-position: {tc}; height: {frameWidth}px" role="presentation"></td>','<tpl if="right"><td class="{frameCls}-tr {baseCls}-tr {baseCls}-{ui}-tr<tpl if="uiCls"><tpl for="uiCls"> {parent.baseCls}-{parent.ui}-{.}-tr</tpl></tpl>" style="background-position: {tr}; padding-left: {frameWidth}px" role="presentation"></td></tpl>',"</tr>","</tpl>","<tr>",'<tpl if="left"><td class="{frameCls}-ml {baseCls}-ml {baseCls}-{ui}-ml<tpl if="uiCls"><tpl for="uiCls"> {parent.baseCls}-{parent.ui}-{.}-ml</tpl></tpl>" style="background-position: {ml}; padding-left: {frameWidth}px" role="presentation"></td></tpl>','<td class="{frameCls}-mc {baseCls}-mc {baseCls}-{ui}-mc<tpl if="uiCls"><tpl for="uiCls"> {parent.baseCls}-{parent.ui}-{.}-mc</tpl></tpl>" style="background-position: 0 0;" role="presentation"></td>','<tpl if="right"><td class="{frameCls}-mr {baseCls}-mr {baseCls}-{ui}-mr<tpl if="uiCls"><tpl for="uiCls"> {parent.baseCls}-{parent.ui}-{.}-mr</tpl></tpl>" style="background-position: {mr}; padding-left: {frameWidth}px" role="presentation"></td></tpl>',"</tr>",'<tpl if="bottom">',"<tr>",'<tpl if="left"><td class="{frameCls}-bl {baseCls}-bl {baseCls}-{ui}-bl<tpl if="uiCls"><tpl for="uiCls"> {parent.baseCls}-{parent.ui}-{.}-bl</tpl></tpl>" style="background-position: {bl}; padding-left: {frameWidth}px" role="presentation"></td></tpl>','<td class="{frameCls}-bc {baseCls}-bc {baseCls}-{ui}-bc<tpl if="uiCls"><tpl for="uiCls"> {parent.baseCls}-{parent.ui}-{.}-bc</tpl></tpl>" style="background-position: {bc}; height: {frameWidth}px" role="presentation"></td>','<tpl if="right"><td class="{frameCls}-br {baseCls}-br {baseCls}-{ui}-br<tpl if="uiCls"><tpl for="uiCls"> {parent.baseCls}-{parent.ui}-{.}-br</tpl></tpl>" style="background-position: {br}; padding-left: {frameWidth}px" role="presentation"></td></tpl>',"</tr>","</tpl>","</tbody></table>"],
initFrame:function(){
    if(Ext.supports.CSS3BorderRadius){
        return false
        }
        var d=this,c=d.getFrameInfo(),b=c.width,a=d.getFrameTpl(c.table);
    if(d.frame){
        a.insertFirst(d.el,Ext.apply({},{
            ui:d.ui,
            uiCls:d.uiCls,
            frameCls:d.frameCls,
            baseCls:d.baseCls,
            frameWidth:b,
            top:!!c.top,
            left:!!c.left,
            right:!!c.right,
            bottom:!!c.bottom
            },d.getFramePositions(c)));
        d.frameBody=d.el.down("."+d.frameCls+"-mc");
        Ext.apply(d.renderSelectors,{
            frameTL:"."+d.baseCls+"-tl",
            frameTC:"."+d.baseCls+"-tc",
            frameTR:"."+d.baseCls+"-tr",
            frameML:"."+d.baseCls+"-ml",
            frameMC:"."+d.baseCls+"-mc",
            frameMR:"."+d.baseCls+"-mr",
            frameBL:"."+d.baseCls+"-bl",
            frameBC:"."+d.baseCls+"-bc",
            frameBR:"."+d.baseCls+"-br"
            })
        }
    },
updateFrame:function(){
    if(Ext.supports.CSS3BorderRadius){
        return false
        }
        var e=this,g=this.frameSize&&this.frameSize.table,f=this.frameTL,d=this.frameBL,c=this.frameML,a=this.frameMC,b;
    this.initFrame();
    if(a){
        if(e.frame){
            delete e.frameTL;
            delete e.frameTC;
            delete e.frameTR;
            delete e.frameML;
            delete e.frameMC;
            delete e.frameMR;
            delete e.frameBL;
            delete e.frameBC;
            delete e.frameBR;
            this.applyRenderSelectors();
            b=this.frameMC.dom.className;
            a.insertAfter(this.frameMC);
            this.frameMC.remove();
            this.frameBody=this.frameMC=a;
            a.dom.className=b;
            if(g){
                e.el.query("> table")[1].remove()
                }else{
                if(f){
                    f.remove()
                    }
                    if(d){
                    d.remove()
                    }
                    c.remove()
                }
            }else{}
}else{
    if(e.frame){
        this.applyRenderSelectors()
        }
    }
},
getFrameInfo:function(){
    if(Ext.supports.CSS3BorderRadius){
        return false
        }
        var c=this,f=c.el.getStyle("background-position-x"),e=c.el.getStyle("background-position-y"),d,b=false,a;
    if(!f&&!e){
        d=c.el.getStyle("background-position").split(" ");
        f=d[0];
        e=d[1]
        }
        if(parseInt(f,10)>=1000000&&parseInt(e,10)>=1000000){
        a=Math.max;
        b={
            table:f.substr(0,3)=="110",
            vertical:e.substr(0,3)=="110",
            top:a(f.substr(3,2),f.substr(5,2)),
            right:a(f.substr(5,2),e.substr(3,2)),
            bottom:a(e.substr(3,2),e.substr(5,2)),
            left:a(e.substr(5,2),f.substr(3,2))
            };
            
        b.width=a(b.top,b.right,b.bottom,b.left);
        c.el.setStyle("background-image","none")
        }
        if(c.frame===true&&!b){}
    c.frame=c.frame||!!b;
    c.frameSize=b||false;
    return b
    },
getFramePositions:function(e){
    var g=this,h=e.width,i=g.dock,d,b,f,c,a;
    if(e.vertical){
        b="0 -"+(h*0)+"px";
        f="0 -"+(h*1)+"px";
        if(i&&i=="right"){
            b="right -"+(h*0)+"px";
            f="right -"+(h*1)+"px"
            }
            d={
            tl:"0 -"+(h*0)+"px",
            tr:"0 -"+(h*1)+"px",
            bl:"0 -"+(h*2)+"px",
            br:"0 -"+(h*3)+"px",
            ml:"-"+(h*1)+"px 0",
            mr:"right 0",
            tc:b,
            bc:f
        }
    }else{
    c="-"+(h*0)+"px 0";
    a="right 0";
    if(i&&i=="bottom"){
        c="left bottom";
        a="right bottom"
        }
        d={
        tl:"0 -"+(h*2)+"px",
        tr:"right -"+(h*3)+"px",
        bl:"0 -"+(h*4)+"px",
        br:"right -"+(h*5)+"px",
        ml:c,
        mr:a,
        tc:"0 -"+(h*0)+"px",
        bc:"0 -"+(h*1)+"px"
        }
    }
return d
},
getFrameTpl:function(a){
    return a?this.getTpl("frameTableTpl"):this.getTpl("frameTpl")
    },
initCls:function(){
    var b=this,a=[];
    a.push(b.baseCls);
    if(Ext.isDefined(b.cmpCls)){
        if(Ext.isDefined(Ext.global.console)){
            Ext.global.console.warn("Ext.Component: cmpCls has been deprecated. Please use componentCls.")
            }
            b.componentCls=b.cmpCls;
        delete b.cmpCls
        }
        if(b.componentCls){
        a.push(b.componentCls)
        }else{
        b.componentCls=b.baseCls
        }
        if(b.cls){
        a.push(b.cls);
        delete b.cls
        }
        return a.concat(b.additionalCls)
    },
setUI:function(f){
    var e=this,b=Ext.Array.clone(e.uiCls),g=[],d=[],a,c;
    for(c=0;c<b.length;c++){
        a=b[c];
        d=d.concat(e.removeClsWithUI(a,true));
        g.push(a)
        }
        if(d.length){
        e.removeCls(d)
        }
        e.removeUIFromElement();
    e.ui=f;
    e.addUIToElement();
    d=[];
    for(c=0;c<g.length;c++){
        a=g[c];
        d=d.concat(e.addClsWithUI(a,true))
        }
        if(d.length){
        e.addCls(d)
        }
    },
addClsWithUI:function(a,e){
    var d=this,c=[],b;
    if(!Ext.isArray(a)){
        a=[a]
        }
        for(b=0;b<a.length;b++){
        if(a[b]&&!d.hasUICls(a[b])){
            d.uiCls=Ext.Array.clone(d.uiCls);
            d.uiCls.push(a[b]);
            c=c.concat(d.addUIClsToElement(a[b]))
            }
        }
    if(e!==true){
    d.addCls(c)
    }
    return c
},
removeClsWithUI:function(a,e){
    var d=this,c=[],b;
    if(!Ext.isArray(a)){
        a=[a]
        }
        for(b=0;b<a.length;b++){
        if(a[b]&&d.hasUICls(a[b])){
            d.uiCls=Ext.Array.remove(d.uiCls,a[b]);
            c=c.concat(d.removeUIClsFromElement(a[b]))
            }
        }
    if(e!==true){
    d.removeCls(c)
    }
    return c
},
hasUICls:function(a){
    var b=this,c=b.uiCls||[];
    return Ext.Array.contains(c,a)
    },
addUIClsToElement:function(k,a){
    var g=this,l=[],h=g.frameElementCls;
    l.push(Ext.baseCSSPrefix+k);
    l.push(g.baseCls+"-"+k);
    l.push(g.baseCls+"-"+g.ui+"-"+k);
    if(!a&&g.frame&&!Ext.supports.CSS3BorderRadius){
        var e=["tl","tc","tr","ml","mc","mr","bl","bc","br"],c,f,d,b;
        for(f=0;f<e.length;f++){
            b=g["frame"+e[f].toUpperCase()];
            c=[g.baseCls+"-"+g.ui+"-"+e[f],g.baseCls+"-"+g.ui+"-"+k+"-"+e[f]];
            if(b&&b.dom){
                b.addCls(c)
                }else{
                for(d=0;d<c.length;d++){
                    if(Ext.Array.indexOf(h[e[f]],c[d])==-1){
                        h[e[f]].push(c[d])
                        }
                    }
                }
        }
}
g.frameElementCls=h;
return l
},
removeUIClsFromElement:function(b,h){
    var g=this,a=[],f=g.frameElementCls;
    a.push(Ext.baseCSSPrefix+b);
    a.push(g.baseCls+"-"+b);
    a.push(g.baseCls+"-"+g.ui+"-"+b);
    if(!h&&g.frame&&!Ext.supports.CSS3BorderRadius){
        var d=["tl","tc","tr","ml","mc","mr","bl","bc","br"],c,e;
        b=g.baseCls+"-"+g.ui+"-"+b+"-"+d[c];
        for(c=0;c<d.length;c++){
            e=g["frame"+d[c].toUpperCase()];
            if(e&&e.dom){
                e.removeCls(b)
                }else{
                Ext.Array.remove(f[d[c]],b)
                }
            }
        }
    g.frameElementCls=f;
return a
},
addUIToElement:function(g){
    var f=this,e=f.frameElementCls;
    f.addCls(f.baseCls+"-"+f.ui);
    if(f.frame&&!Ext.supports.CSS3BorderRadius){
        var c=["tl","tc","tr","ml","mc","mr","bl","bc","br"],b,d,a;
        for(b=0;b<c.length;b++){
            d=f["frame"+c[b].toUpperCase()];
            a=f.baseCls+"-"+f.ui+"-"+c[b];
            if(d){
                d.addCls(a)
                }else{
                if(!Ext.Array.contains(e[c[b]],a)){
                    e[c[b]].push(a)
                    }
                }
        }
    }
},
removeUIFromElement:function(){
    var g=this,f=g.frameElementCls;
    g.removeCls(g.baseCls+"-"+g.ui);
    if(g.frame&&!Ext.supports.CSS3BorderRadius){
        var d=["tl","tc","tr","ml","mc","mr","bl","bc","br"],c,b,e,a;
        for(c=0;c<d.length;c++){
            e=g["frame"+d[c].toUpperCase()];
            a=g.baseCls+"-"+g.ui+"-"+d[c];
            if(e){
                e.removeCls(a)
                }else{
                Ext.Array.remove(f[d[c]],a)
                }
            }
        }
},
getElConfig:function(){
    var a=this.autoEl||{
        tag:"div"
    };
    
    a.id=this.id;
    return a
    },
getInsertPosition:function(a){
    if(a!==undefined){
        if(Ext.isNumber(a)){
            a=this.container.dom.childNodes[a]
            }else{
            a=Ext.getDom(a)
            }
        }
    return a
},
initContainer:function(a){
    var b=this;
    if(!a&&b.el){
        a=b.el.dom.parentNode;
        b.allowDomMove=false
        }
        b.container=Ext.get(a);
    if(b.ctCls){
        b.container.addCls(b.ctCls)
        }
        return b.container
    },
initRenderData:function(){
    var a=this;
    return Ext.applyIf(a.renderData,{
        ui:a.ui,
        uiCls:a.uiCls,
        baseCls:a.baseCls,
        componentCls:a.componentCls,
        frame:a.frame
        })
    },
getTpl:function(c){
    var e=this,b=e.self.prototype,d,a;
    if(e.hasOwnProperty(c)){
        a=e[c];
        if(a&&!(a instanceof Ext.XTemplate)){
            e[c]=Ext.ClassManager.dynInstantiate("Ext.XTemplate",a)
            }
            return e[c]
        }
        if(!(b[c] instanceof Ext.XTemplate)){
        d=b;
        do{
            if(d.hasOwnProperty(c)){
                a=d[c];
                if(a&&!(a instanceof Ext.XTemplate)){
                    d[c]=Ext.ClassManager.dynInstantiate("Ext.XTemplate",a);
                    break
                }
            }
            d=d.superclass
        }while(d)
}
return b[c]
},
initRenderTpl:function(){
    return this.getTpl("renderTpl")
    },
initStyles:function(){
    var b={},c=this,a=Ext.core.Element;
    if(Ext.isString(c.style)){
        b=a.parseStyles(c.style)
        }else{
        b=Ext.apply({},c.style)
        }
        if(c.padding!==undefined){
        b.padding=a.unitizeBox((c.padding===true)?5:c.padding)
        }
        if(c.margin!==undefined){
        b.margin=a.unitizeBox((c.margin===true)?5:c.margin)
        }
        delete c.style;
    return b
    },
initContent:function(){
    var b=this,d=b.getTargetEl(),a,c;
    if(b.html){
        d.update(Ext.core.DomHelper.markup(b.html));
        delete b.html
        }
        if(b.contentEl){
        a=Ext.get(b.contentEl);
        c=Ext.baseCSSPrefix;
        a.removeCls([c+"hidden",c+"hide-display",c+"hide-offsets",c+"hide-nosize"]);
        d.appendChild(a.dom)
        }
        if(b.tpl){
        if(!b.tpl.isTemplate){
            b.tpl=Ext.create("Ext.XTemplate",b.tpl)
            }
            if(b.data){
            b.tpl[b.tplWriteMode](d,b.data);
            delete b.data
            }
        }
},
initEvents:function(){
    var c=this,e=c.afterRenderEvents,b,d,a=function(f){
        c.mon(b,f)
        };
        
    if(e){
        for(d in e){
            if(e.hasOwnProperty(d)){
                b=c[d];
                if(b&&b.on){
                    Ext.each(e[d],a)
                    }
                }
        }
        }
},
applyRenderSelectors:function(){
    var b=this.renderSelectors||{},c=this.el.dom,a;
    for(a in b){
        if(b.hasOwnProperty(a)&&b[a]){
            this[a]=Ext.get(Ext.DomQuery.selectNode(b[a],c))
            }
        }
    },
is:function(a){
    return Ext.ComponentQuery.is(this,a)
    },
up:function(b){
    var a=this.ownerCt;
    if(b){
        for(;a;a=a.ownerCt){
            if(Ext.ComponentQuery.is(a,b)){
                return a
                }
            }
        }
    return a
},
nextSibling:function(b){
    var f=this.ownerCt,d,e,a,g;
    if(f){
        d=f.items;
        a=d.indexOf(this)+1;
        if(a){
            if(b){
                for(e=d.getCount();a<e;a++){
                    if((g=d.getAt(a)).is(b)){
                        return g
                        }
                    }
                }else{
        if(a<d.getCount()){
            return d.getAt(a)
            }
        }
}
}
return null
},
previousSibling:function(b){
    var e=this.ownerCt,d,a,f;
    if(e){
        d=e.items;
        a=d.indexOf(this);
        if(a!=-1){
            if(b){
                for(--a;a>=0;a--){
                    if((f=d.getAt(a)).is(b)){
                        return f
                        }
                    }
                }else{
        if(a){
            return d.getAt(--a)
            }
        }
}
}
return null
},
previousNode:function(c,d){
    var g=this,b,f,a,e;
    if(d&&g.is(c)){
        return g
        }
        b=this.prev(c);
    if(b){
        return b
        }
        if(g.ownerCt){
        for(f=g.ownerCt.items.items,e=Ext.Array.indexOf(f,g)-1;e>-1;e--){
            if(f[e].query){
                b=f[e].query(c);
                b=b[b.length-1];
                if(b){
                    return b
                    }
                }
        }
        return g.ownerCt.previousNode(c,true)
}
},
nextNode:function(c,d){
    var g=this,b,f,a,e;
    if(d&&g.is(c)){
        return g
        }
        b=this.next(c);
    if(b){
        return b
        }
        if(g.ownerCt){
        for(f=g.ownerCt.items,e=f.indexOf(g)+1,f=f.items,a=f.length;e<a;e++){
            if(f[e].down){
                b=f[e].down(c);
                if(b){
                    return b
                    }
                }
        }
        return g.ownerCt.nextNode(c)
}
},
getId:function(){
    return this.id||(this.id="ext-comp-"+(this.getAutoId()))
    },
getItemId:function(){
    return this.itemId||this.id
    },
getEl:function(){
    return this.el
    },
getTargetEl:function(){
    return this.frameBody||this.el
    },
isXType:function(b,a){
    if(Ext.isFunction(b)){
        b=b.xtype
        }else{
        if(Ext.isObject(b)){
            b=b.statics().xtype
            }
        }
    return !a?("/"+this.getXTypes()+"/").indexOf("/"+b+"/")!=-1:this.self.xtype==b
},
getXTypes:function(){
    var b=this.self,c=[],a=this,d;
    if(!b.xtypes){
        while(a&&Ext.getClass(a)){
            d=Ext.getClass(a).xtype;
            if(d!==undefined){
                c.unshift(d)
                }
                a=a.superclass
            }
            b.xtypeChain=c;
        b.xtypes=c.join("/")
        }
        return b.xtypes
    },
update:function(b,c,a){
    var d=this;
    if(d.tpl&&!Ext.isString(b)){
        d.data=b;
        if(d.rendered){
            d.tpl[d.tplWriteMode](d.getTargetEl(),b||{})
            }
        }else{
    d.html=Ext.isObject(b)?Ext.core.DomHelper.markup(b):b;
    if(d.rendered){
        d.getTargetEl().update(d.html,c,a)
        }
    }
if(d.rendered){
    d.doComponentLayout()
    }
},
setVisible:function(a){
    return this[a?"show":"hide"]()
    },
isVisible:function(a){
    var c=this,e=c,d=!c.hidden,b=c.ownerCt;
    c.hiddenAncestor=false;
    if(c.destroyed){
        return false
        }
        if(a&&d&&c.rendered&&b){
        while(b){
            if(b.hidden||(b.collapsed&&!(b.getDockedItems&&Ext.Array.contains(b.getDockedItems(),e)))){
                c.hiddenAncestor=b;
                d=false;
                break
            }
            e=b;
            b=b.ownerCt
            }
        }
    return d
},
enable:function(a){
    var b=this;
    if(b.rendered){
        b.el.removeCls(b.disabledCls);
        b.el.dom.disabled=false;
        b.onEnable()
        }
        b.disabled=false;
    if(a!==true){
        b.fireEvent("enable",b)
        }
        return b
    },
disable:function(a){
    var b=this;
    if(b.rendered){
        b.el.addCls(b.disabledCls);
        b.el.dom.disabled=true;
        b.onDisable()
        }
        b.disabled=true;
    if(a!==true){
        b.fireEvent("disable",b)
        }
        return b
    },
onEnable:function(){
    if(this.maskOnDisable){
        this.el.unmask()
        }
    },
onDisable:function(){
    if(this.maskOnDisable){
        this.el.mask()
        }
    },
isDisabled:function(){
    return this.disabled
    },
setDisabled:function(a){
    return this[a?"disable":"enable"]()
    },
isHidden:function(){
    return this.hidden
    },
addCls:function(a){
    var b=this;
    if(!a){
        return b
        }
        if(!Ext.isArray(a)){
        a=a.replace(b.trimRe,"").split(b.spacesRe)
        }
        if(b.rendered){
        b.el.addCls(a)
        }else{
        b.additionalCls=Ext.Array.unique(b.additionalCls.concat(a))
        }
        return b
    },
addClass:function(){
    return this.addCls.apply(this,arguments)
    },
removeCls:function(a){
    var b=this;
    if(!a){
        return b
        }
        if(!Ext.isArray(a)){
        a=a.replace(b.trimRe,"").split(b.spacesRe)
        }
        if(b.rendered){
        b.el.removeCls(a)
        }else{
        if(b.additionalCls.length){
            Ext.each(a,function(c){
                Ext.Array.remove(b.additionalCls,c)
                })
            }
        }
    return b
},
addOverCls:function(){
    var a=this;
    if(!a.disabled){
        a.el.addCls(a.overCls)
        }
    },
removeOverCls:function(){
    this.el.removeCls(this.overCls)
    },
addListener:function(b,f,e,a){
    var g=this,d,c;
    if(Ext.isString(b)&&(Ext.isObject(f)||a&&a.element)){
        if(a.element){
            d=f;
            f={};
            
            f[b]=d;
            b=a.element;
            if(e){
                f.scope=e
                }
                for(c in a){
                if(a.hasOwnProperty(c)){
                    if(g.eventOptionsRe.test(c)){
                        f[c]=a[c]
                        }
                    }
            }
            }
        if(g[b]&&g[b].on){
    g.mon(g[b],f)
    }else{
    g.afterRenderEvents=g.afterRenderEvents||{};
    
    if(!g.afterRenderEvents[b]){
        g.afterRenderEvents[b]=[]
        }
        g.afterRenderEvents[b].push(f)
    }
}
return g.mixins.observable.addListener.apply(g,arguments)
},
removeManagedListenerItem:function(b,a,h,d,f,e){
    var g=this,c=a.options?a.options.element:null;
    if(c){
        c=g[c];
        if(c&&c.un){
            if(b||(a.item===h&&a.ename===d&&(!f||a.fn===f)&&(!e||a.scope===e))){
                c.un(a.ename,a.fn,a.scope);
                if(!b){
                    Ext.Array.remove(g.managedListeners,a)
                    }
                }
        }
}else{
    return g.mixins.observable.removeManagedListenerItem.apply(g,arguments)
    }
},
getBubbleTarget:function(){
    return this.ownerCt
    },
isFloating:function(){
    return this.floating
    },
isDraggable:function(){
    return !!this.draggable
    },
isDroppable:function(){
    return !!this.droppable
    },
onAdded:function(a,b){
    this.ownerCt=a;
    this.fireEvent("added",this,a,b)
    },
onRemoved:function(){
    var a=this;
    a.fireEvent("removed",a,a.ownerCt);
    delete a.ownerCt
    },
beforeDestroy:Ext.emptyFn,
onResize:Ext.emptyFn,
setSize:function(b,a){
    var c=this,d;
    if(Ext.isObject(b)){
        a=b.height;
        b=b.width
        }
        if(Ext.isNumber(b)){
        b=Ext.Number.constrain(b,c.minWidth,c.maxWidth)
        }
        if(Ext.isNumber(a)){
        a=Ext.Number.constrain(a,c.minHeight,c.maxHeight)
        }
        if(!c.rendered||!c.isVisible()){
        if(c.hiddenAncestor){
            d=c.hiddenAncestor.layoutOnShow;
            d.remove(c);
            d.add(c)
            }
            c.needsLayout={
            width:b,
            height:a,
            isSetSize:true
        };
        
        if(!c.rendered){
            c.width=(b!==undefined)?b:c.width;
            c.height=(a!==undefined)?a:c.height
            }
            return c
        }
        c.doComponentLayout(b,a,true);
    return c
    },
isFixedWidth:function(){
    var b=this,a=b.layoutManagedWidth;
    if(Ext.isDefined(b.width)||a==1){
        return true
        }
        if(a==2){
        return false
        }
        return(b.ownerCt&&b.ownerCt.isFixedWidth())
    },
isFixedHeight:function(){
    var a=this,b=a.layoutManagedHeight;
    if(Ext.isDefined(a.height)||b==1){
        return true
        }
        if(b==2){
        return false
        }
        return(a.ownerCt&&a.ownerCt.isFixedHeight())
    },
setCalculatedSize:function(b,a,e){
    var c=this,d;
    if(Ext.isObject(b)){
        e=b.ownerCt;
        a=b.height;
        b=b.width
        }
        if(Ext.isNumber(b)){
        b=Ext.Number.constrain(b,c.minWidth,c.maxWidth)
        }
        if(Ext.isNumber(a)){
        a=Ext.Number.constrain(a,c.minHeight,c.maxHeight)
        }
        if(!c.rendered||!c.isVisible()){
        if(c.hiddenAncestor){
            d=c.hiddenAncestor.layoutOnShow;
            d.remove(c);
            d.add(c)
            }
            c.needsLayout={
            width:b,
            height:a,
            isSetSize:false,
            ownerCt:e
        };
        
        return c
        }
        c.doComponentLayout(b,a,false,e);
    return c
    },
doComponentLayout:function(e,b,c,g){
    var f=this,d=f.getComponentLayout(),a=d.lastComponentSize||{
        width:undefined,
        height:undefined
    };
    
    if(f.rendered&&d){
        if(!Ext.isDefined(e)){
            if(f.isFixedWidth()){
                e=Ext.isDefined(f.width)?f.width:a.width
                }
            }
        if(!Ext.isDefined(b)){
        if(f.isFixedHeight()){
            b=Ext.isDefined(f.height)?f.height:a.height
            }
        }
    if(c){
    f.width=e;
    f.height=b
    }
    d.layout(e,b,c,g)
}
return f
},
forceComponentLayout:function(){
    this.doComponentLayout()
    },
setComponentLayout:function(b){
    var a=this.componentLayout;
    if(a&&a.isLayout&&a!=b){
        a.setOwner(null)
        }
        this.componentLayout=b;
    b.setOwner(this)
    },
getComponentLayout:function(){
    var a=this;
    if(!a.componentLayout||!a.componentLayout.isLayout){
        a.setComponentLayout(Ext.layout.Layout.create(a.componentLayout,"autocomponent"))
        }
        return a.componentLayout
    },
afterComponentLayout:function(c,a,b,d){
    ++this.componentLayoutCounter;
    this.fireEvent("resize",this,c,a)
    },
beforeComponentLayout:function(c,a,b,d){
    return true
    },
setPosition:function(a,c){
    var b=this;
    if(Ext.isObject(a)){
        c=a.y;
        a=a.x
        }
        if(!b.rendered){
        return b
        }
        if(a!==undefined||c!==undefined){
        b.el.setBox(a,c);
        b.onPosition(a,c);
        b.fireEvent("move",b,a,c)
        }
        return b
    },
onPosition:Ext.emptyFn,
setWidth:function(a){
    return this.setSize(a)
    },
setHeight:function(a){
    return this.setSize(undefined,a)
    },
getSize:function(){
    return this.el.getSize()
    },
getWidth:function(){
    return this.el.getWidth()
    },
getHeight:function(){
    return this.el.getHeight()
    },
getLoader:function(){
    var c=this,b=c.autoLoad?(Ext.isObject(c.autoLoad)?c.autoLoad:{
        url:c.autoLoad
        }):null,a=c.loader||b;
    if(a){
        if(!a.isLoader){
            c.loader=Ext.create("Ext.ComponentLoader",Ext.apply({
                target:c,
                autoLoad:b
            },a))
            }else{
            a.setTarget(c)
            }
            return c.loader
        }
        return null
    },
setLoading:function(c,d){
    var b=this,a;
    if(b.rendered){
        if(c!==false&&!b.collapsed){
            if(Ext.isObject(c)){
                a=c
                }else{
                if(Ext.isString(c)){
                    a={
                        msg:c
                    }
                }else{
                a={}
            }
        }
    b.loadMask=b.loadMask||Ext.create("Ext.LoadMask",d?b.getTargetEl():b.el,a);
b.loadMask.show()
}else{
    if(b.loadMask){
        Ext.destroy(b.loadMask);
        b.loadMask=null
        }
    }
}
return b.loadMask
},
setDocked:function(b,c){
    var a=this;
    a.dock=b;
    if(c&&a.ownerCt&&a.rendered){
        a.ownerCt.doComponentLayout()
        }
        return a
    },
onDestroy:function(){
    var a=this;
    if(a.monitorResize&&Ext.EventManager.resizeEvent){
        Ext.EventManager.resizeEvent.removeListener(a.setSize,a)
        }
        Ext.destroy(a.componentLayout,a.loadMask)
    },
destroy:function(){
    var a=this;
    if(!a.isDestroyed){
        if(a.fireEvent("beforedestroy",a)!==false){
            a.destroying=true;
            a.beforeDestroy();
            if(a.floating){
                delete a.floatParent;
                if(a.zIndexManager){
                    a.zIndexManager.unregister(a)
                    }
                }else{
            if(a.ownerCt&&a.ownerCt.remove){
                a.ownerCt.remove(a,false)
                }
            }
        a.onDestroy();
    Ext.destroy(a.plugins);
    if(a.rendered){
        a.el.remove()
        }
        Ext.ComponentManager.unregister(a);
    a.fireEvent("destroy",a);
    a.mixins.state.destroy.call(a);
    a.clearListeners();
    a.destroying=false;
    a.isDestroyed=true
    }
}
},
getPlugin:function(b){
    var c=0,a=this.plugins,d=a.length;
    for(;c<d;c++){
        if(a[c].pluginId===b){
            return a[c]
            }
        }
    },
isDescendantOf:function(a){
    return !!this.findParentBy(function(b){
        return b===a
        })
    }
},function(){
    this.createAlias({
        on:"addListener",
        prev:"previousSibling",
        next:"nextSibling"
    })
    });
Ext.define("Ext.Component",{
    alias:["widget.component","widget.box"],
    extend:"Ext.AbstractComponent",
    requires:["Ext.util.DelayedTask"],
    uses:["Ext.Layer","Ext.resizer.Resizer","Ext.util.ComponentDragger"],
    mixins:{
        floating:"Ext.util.Floating"
    },
    statics:{
        DIRECTION_TOP:"top",
        DIRECTION_RIGHT:"right",
        DIRECTION_BOTTOM:"bottom",
        DIRECTION_LEFT:"left",
        VERTICAL_DIRECTION:/^(?:top|bottom)$/
    },
    resizeHandles:"all",
    floating:false,
    toFrontOnShow:true,
    hideMode:"display",
    hideParent:false,
    ariaRole:"presentation",
    bubbleEvents:[],
    actionMode:"el",
    monPropRe:/^(?:scope|delay|buffer|single|stopEvent|preventDefault|stopPropagation|normalized|args|delegate)$/,
    constructor:function(a){
        a=a||{};
        
        if(a.initialConfig){
            if(a.isAction){
                this.baseAction=a
                }
                a=a.initialConfig
            }else{
            if(a.tagName||a.dom||Ext.isString(a)){
                a={
                    applyTo:a,
                    id:a.id||a
                    }
                }
        }
    this.callParent([a]);
    if(this.baseAction){
    this.baseAction.addComponent(this)
    }
},
initComponent:function(){
    var a=this;
    if(a.listeners){
        a.on(a.listeners);
        delete a.listeners
        }
        a.enableBubble(a.bubbleEvents);
    a.mons=[]
    },
afterRender:function(){
    var b=this,a=b.resizable;
    if(b.floating){
        b.makeFloating(b.floating)
        }else{
        b.el.setVisibilityMode(Ext.core.Element[b.hideMode.toUpperCase()])
        }
        if(Ext.isDefined(b.autoScroll)){
        b.setAutoScroll(b.autoScroll)
        }
        b.callParent();
    if(!(b.x&&b.y)&&(b.pageX||b.pageY)){
        b.setPagePosition(b.pageX,b.pageY)
        }
        if(a){
        b.initResizable(a)
        }
        if(b.draggable){
        b.initDraggable()
        }
        b.initAria()
    },
initAria:function(){
    var a=this.getActionEl(),b=this.ariaRole;
    if(b){
        a.dom.setAttribute("role",b)
        }
    },
setAutoScroll:function(a){
    var b=this,c;
    a=!!a;
    if(b.rendered){
        c=b.getTargetEl();
        c.setStyle("overflow",a?"auto":"");
        if(a&&(Ext.isIE6||Ext.isIE7)){
            c.position()
            }
        }
    b.autoScroll=a;
return b
},
makeFloating:function(a){
    this.mixins.floating.constructor.call(this,a)
    },
initResizable:function(a){
    a=Ext.apply({
        target:this,
        dynamic:false,
        constrainTo:this.constrainTo,
        handles:this.resizeHandles
        },a);
    a.target=this;
    this.resizer=Ext.create("Ext.resizer.Resizer",a)
    },
getDragEl:function(){
    return this.el
    },
initDraggable:function(){
    var b=this,a=Ext.applyIf({
        el:this.getDragEl(),
        constrainTo:b.constrainTo||(b.floatParent?b.floatParent.getTargetEl():b.el.dom.parentNode)
        },this.draggable);
    if(b.constrain||b.constrainDelegate){
        a.constrain=b.constrain;
        a.constrainDelegate=b.constrainDelegate
        }
        this.dd=Ext.create("Ext.util.ComponentDragger",this,a)
    },
setPosition:function(j,h,a){
    var f=this,b=f.el,k={},g,i,e,d,c;
    if(Ext.isArray(j)){
        a=h;
        h=j[1];
        j=j[0]
        }
        f.x=j;
    f.y=h;
    if(!f.rendered){
        return f
        }
        g=f.adjustPosition(j,h);
    i=g.x;
    e=g.y;
    d=Ext.isNumber(i);
    c=Ext.isNumber(e);
    if(d||c){
        if(a){
            if(d){
                k.left=i
                }
                if(c){
                k.top=e
                }
                f.stopAnimation();
            f.animate(Ext.apply({
                duration:1000,
                listeners:{
                    afteranimate:Ext.Function.bind(f.afterSetPosition,f,[i,e])
                    },
                to:k
            },a))
            }else{
            if(!d){
                b.setTop(e)
                }else{
                if(!c){
                    b.setLeft(i)
                    }else{
                    b.setLeftTop(i,e)
                    }
                }
            f.afterSetPosition(i,e)
        }
    }
return f
},
afterSetPosition:function(b,a){
    this.onPosition(b,a);
    this.fireEvent("move",this,b,a)
    },
showAt:function(a,c,b){
    if(this.floating){
        this.setPosition(a,c,b)
        }else{
        this.setPagePosition(a,c,b)
        }
        this.show()
    },
setPagePosition:function(a,e,b){
    var c=this,d;
    if(Ext.isArray(a)){
        e=a[1];
        a=a[0]
        }
        c.pageX=a;
    c.pageY=e;
    if(c.floating&&c.floatParent){
        d=c.floatParent.getTargetEl().getViewRegion();
        if(Ext.isNumber(a)&&Ext.isNumber(d.left)){
            a-=d.left
            }
            if(Ext.isNumber(e)&&Ext.isNumber(d.top)){
            e-=d.top
            }
            c.setPosition(a,e,b)
        }else{
        d=c.el.translatePoints(a,e);
        c.setPosition(d.left,d.top,b)
        }
        return c
    },
getBox:function(a){
    var c=this.getPosition(a);
    var b=this.getSize();
    b.x=c[0];
    b.y=c[1];
    return b
    },
updateBox:function(a){
    this.setSize(a.width,a.height);
    this.setPagePosition(a.x,a.y);
    return this
    },
getOuterSize:function(){
    var a=this.el;
    return{
        width:a.getWidth()+a.getMargin("lr"),
        height:a.getHeight()+a.getMargin("tb")
        }
    },
adjustSize:function(a,b){
    if(this.autoWidth){
        a="auto"
        }
        if(this.autoHeight){
        b="auto"
        }
        return{
        width:a,
        height:b
    }
},
adjustPosition:function(a,c){
    if(this.floating&&this.floatParent){
        var b=this.floatParent.getTargetEl().getViewRegion();
        a+=b.left;
        c+=b.top
        }
        return{
        x:a,
        y:c
    }
},
getPosition:function(a){
    var b=this.el,c;
    if(a===true){
        return[b.getLeft(true),b.getTop(true)]
        }
        c=this.xy||b.getXY();
    if(this.floating&&this.floatParent){
        var d=this.floatParent.getTargetEl().getViewRegion();
        c[0]-=d.left;
        c[1]-=d.top
        }
        return c
    },
getId:function(){
    return this.id||(this.id=(this.getXType()||"ext-comp")+"-"+this.getAutoId())
    },
onEnable:function(){
    var a=this.getActionEl();
    a.dom.removeAttribute("aria-disabled");
    a.dom.disabled=false;
    this.callParent()
    },
onDisable:function(){
    var a=this.getActionEl();
    a.dom.setAttribute("aria-disabled",true);
    a.dom.disabled=true;
    this.callParent()
    },
show:function(c,a,b){
    if(this.rendered&&this.isVisible()){
        if(this.toFrontOnShow&&this.floating){
            this.toFront()
            }
        }else{
    if(this.fireEvent("beforeshow",this)!==false){
        this.hidden=false;
        if(!this.rendered&&(this.autoRender||this.floating)){
            this.doAutoRender()
            }
            if(this.rendered){
            this.beforeShow();
            this.onShow.apply(this,arguments);
            if(this.ownerCt&&!this.floating&&!(this.ownerCt.suspendLayout||this.ownerCt.layout.layoutBusy)){
                this.ownerCt.doLayout()
                }
                this.afterShow.apply(this,arguments)
            }
        }
}
return this
},
beforeShow:Ext.emptyFn,
onShow:function(){
    var a=this;
    a.el.show();
    if(this.floating&&this.constrain){
        this.doConstrain()
        }
        a.callParent(arguments)
    },
afterShow:function(g,b,e){
    var f=this,a,c,d;
    g=g||f.animateTarget;
    if(!f.ghost){
        g=null
        }
        if(g){
        g=g.el?g.el:Ext.get(g);
        c=f.el.getBox();
        a=g.getBox();
        a.width+="px";
        a.height+="px";
        c.width+="px";
        c.height+="px";
        f.el.addCls(Ext.baseCSSPrefix+"hide-offsets");
        d=f.ghost();
        d.el.stopAnimation();
        d.el.animate({
            from:a,
            to:c,
            listeners:{
                afteranimate:function(){
                    delete d.componentLayout.lastComponentSize;
                    f.unghost();
                    f.el.removeCls(Ext.baseCSSPrefix+"hide-offsets");
                    if(f.floating){
                        f.toFront()
                        }
                        Ext.callback(b,e||f)
                    }
                }
        })
}else{
    if(f.floating){
        f.toFront()
        }
        Ext.callback(b,e||f)
    }
    f.fireEvent("show",f)
},
hide:function(){
    this.showOnParentShow=false;
    if(!(this.rendered&&!this.isVisible())&&this.fireEvent("beforehide",this)!==false){
        this.hidden=true;
        if(this.rendered){
            this.onHide.apply(this,arguments);
            if(this.ownerCt&&!this.floating&&!(this.ownerCt.suspendLayout||this.ownerCt.layout.layoutBusy)){
                this.ownerCt.doLayout()
                }
            }
    }
return this
},
onHide:function(f,a,d){
    var e=this,c,b;
    f=f||e.animateTarget;
    if(!e.ghost){
        f=null
        }
        if(f){
        f=f.el?f.el:Ext.get(f);
        c=e.ghost();
        c.el.stopAnimation();
        b=f.getBox();
        b.width+="px";
        b.height+="px";
        c.el.animate({
            to:b,
            listeners:{
                afteranimate:function(){
                    delete c.componentLayout.lastComponentSize;
                    c.el.hide();
                    e.afterHide(a,d)
                    }
                }
        })
}
e.el.hide();
if(!f){
    e.afterHide(a,d)
    }
},
afterHide:function(a,b){
    Ext.callback(a,b||this);
    this.fireEvent("hide",this)
    },
onDestroy:function(){
    var a=this;
    if(a.rendered){
        Ext.destroy(a.proxy,a.resizer);
        if(a.actionMode=="container"||a.removeMode=="container"){
            a.container.remove()
            }
        }
    delete a.focusTask;
a.callParent()
},
deleteMembers:function(){
    var b=arguments,a=b.length,c=0;
    for(;c<a;++c){
        delete this[b[c]]
    }
    },
focus:function(d,b){
    var c=this,a;
    if(b){
        if(!c.focusTask){
            c.focusTask=Ext.create("Ext.util.DelayedTask",c.focus)
            }
            c.focusTask.delay(Ext.isNumber(b)?b:10,null,c,[d,false]);
        return c
        }
        if(c.rendered&&!c.isDestroyed){
        a=c.getFocusEl();
        a.focus();
        if(a.dom&&d===true){
            a.dom.select()
            }
            if(c.floating){
            c.toFront(true)
            }
        }
    return c
},
getFocusEl:function(){
    return this.el
    },
blur:function(){
    if(this.rendered){
        this.getFocusEl().blur()
        }
        return this
    },
getEl:function(){
    return this.el
    },
getResizeEl:function(){
    return this.el
    },
getPositionEl:function(){
    return this.el
    },
getActionEl:function(){
    return this.el
    },
getVisibilityEl:function(){
    return this.el
    },
onResize:Ext.emptyFn,
getBubbleTarget:function(){
    return this.ownerCt
    },
getContentTarget:function(){
    return this.el
    },
cloneConfig:function(c){
    c=c||{};
    
    var d=c.id||Ext.id();
    var a=Ext.applyIf(c,this.initialConfig);
    a.id=d;
    var b=Ext.getClass(this);
    return new b(a)
    },
getXType:function(){
    return this.self.xtype
    },
findParentBy:function(a){
    var b;
    for(b=this.ownerCt;b&&!a(b,this);b=b.ownerCt){}
    return b||null
    },
findParentByType:function(a){
    return Ext.isFunction(a)?this.findParentBy(function(b){
        return b.constructor===a
        }):this.up(a)
    },
bubble:function(c,b,a){
    var d=this;
    while(d){
        if(c.apply(b||d,a||[d])===false){
            break
        }
        d=d.ownerCt
        }
        return this
    },
getProxy:function(){
    if(!this.proxy){
        this.proxy=this.el.createProxy(Ext.baseCSSPrefix+"proxy-el",Ext.getBody(),true)
        }
        return this.proxy
    }
});
Ext.define("Ext.resizer.Splitter",{
    extend:"Ext.Component",
    requires:["Ext.XTemplate"],
    uses:["Ext.resizer.SplitterTracker"],
    alias:"widget.splitter",
    renderTpl:['<tpl if="collapsible===true"><div class="'+Ext.baseCSSPrefix+"collapse-el "+Ext.baseCSSPrefix+'layout-split-{collapseDir}">&nbsp;</div></tpl>'],
    baseCls:Ext.baseCSSPrefix+"splitter",
    collapsedClsInternal:Ext.baseCSSPrefix+"splitter-collapsed",
    collapsible:false,
    collapseOnDblClick:true,
    defaultSplitMin:40,
    defaultSplitMax:1000,
    width:5,
    height:5,
    collapseTarget:"next",
    onRender:function(){
        var a=this,b=a.getCollapseTarget(),c=a.getCollapseDirection();
        Ext.applyIf(a.renderData,{
            collapseDir:c,
            collapsible:a.collapsible||b.collapsible
            });
        Ext.applyIf(a.renderSelectors,{
            collapseEl:"."+Ext.baseCSSPrefix+"collapse-el"
            });
        this.callParent(arguments);
        if(a.performCollapse!==false){
            if(a.renderData.collapsible){
                a.mon(a.collapseEl,"click",a.toggleTargetCmp,a)
                }
                if(a.collapseOnDblClick){
                a.mon(a.el,"dblclick",a.toggleTargetCmp,a)
                }
            }
        a.mon(b,"collapse",a.onTargetCollapse,a);
    a.mon(b,"expand",a.onTargetExpand,a);
    a.el.addCls(a.baseCls+"-"+a.orientation);
    a.el.unselectable();
    a.tracker=Ext.create("Ext.resizer.SplitterTracker",{
        el:a.el
        });
    a.relayEvents(a.tracker,["beforedragstart","dragstart","dragend"])
    },
getCollapseDirection:function(){
    var c=this,a,b=c.ownerCt.layout.type;
    if(c.collapseTarget.isComponent){
        a=Number(c.ownerCt.items.indexOf(c.collapseTarget)==c.ownerCt.items.indexOf(c)-1)<<1|Number(b=="hbox")
        }else{
        a=Number(c.collapseTarget=="prev")<<1|Number(b=="hbox")
        }
        c.orientation=["horizontal","vertical"][a&1];
    return["bottom","right","top","left"][a]
    },
getCollapseTarget:function(){
    var a=this;
    return a.collapseTarget.isComponent?a.collapseTarget:a.collapseTarget=="prev"?a.previousSibling():a.nextSibling()
    },
onTargetCollapse:function(a){
    this.el.addCls([this.collapsedClsInternal,this.collapsedCls])
    },
onTargetExpand:function(a){
    this.el.removeCls([this.collapsedClsInternal,this.collapsedCls])
    },
toggleTargetCmp:function(c,a){
    var b=this.getCollapseTarget();
    if(b.isVisible()){
        if(b.collapsed){
            b.expand(b.animCollapse)
            }else{
            b.collapse(this.renderData.collapseDir,b.animCollapse)
            }
        }
},
setSize:function(){
    var a=this;
    a.callParent(arguments);
    if(Ext.isIE){
        a.el.repaint()
        }
    }
});
Ext.define("Ext.button.Button",{
    alias:"widget.button",
    extend:"Ext.Component",
    requires:["Ext.menu.Manager","Ext.util.ClickRepeater","Ext.layout.component.Button","Ext.util.TextMetrics","Ext.util.KeyMap"],
    alternateClassName:"Ext.Button",
    isButton:true,
    componentLayout:"button",
    hidden:false,
    disabled:false,
    pressed:false,
    enableToggle:false,
    menuAlign:"tl-bl?",
    type:"button",
    clickEvent:"click",
    preventDefault:true,
    handleMouseEvents:true,
    tooltipType:"qtip",
    baseCls:Ext.baseCSSPrefix+"btn",
    pressedCls:"pressed",
    overCls:"over",
    focusCls:"focus",
    menuActiveCls:"menu-active",
    ariaRole:"button",
    renderTpl:'<em class="{splitCls}"><tpl if="href"><a href="{href}" target="{target}"<tpl if="tabIndex"> tabIndex="{tabIndex}"</tpl> role="link"><span class="{baseCls}-inner">{text}</span><span class="{baseCls}-icon"></span></a></tpl><tpl if="!href"><button type="{type}" hidefocus="true"<tpl if="tabIndex"> tabIndex="{tabIndex}"</tpl> role="button" autocomplete="off"><span class="{baseCls}-inner" style="{innerSpanStyle}">{text}</span><span class="{baseCls}-icon"></span></button></tpl></em>',
    scale:"small",
    allowedScales:["small","medium","large"],
    iconAlign:"left",
    arrowAlign:"right",
    arrowCls:"arrow",
    maskOnDisable:false,
    initComponent:function(){
        var a=this;
        a.callParent(arguments);
        a.addEvents("click","toggle","mouseover","mouseout","menushow","menuhide","menutriggerover","menutriggerout");
        if(a.menu){
            a.split=true;
            a.menu=Ext.menu.Manager.get(a.menu);
            a.menu.ownerCt=a
            }
            if(a.url){
            a.href=a.url
            }
            if(a.href&&!a.hasOwnProperty("preventDefault")){
            a.preventDefault=false
            }
            if(Ext.isString(a.toggleGroup)){
            a.enableToggle=true
            }
        },
initAria:function(){
    this.callParent();
    var a=this.getActionEl();
    if(this.menu){
        a.dom.setAttribute("aria-haspopup",true)
        }
    },
getActionEl:function(){
    return this.btnEl
    },
getFocusEl:function(){
    return this.btnEl
    },
setButtonCls:function(){
    var c=this,b=c.el,a=[];
    if(c.useSetClass){
        if(!Ext.isEmpty(c.oldCls)){
            c.removeClsWithUI(c.oldCls);
            c.removeClsWithUI(c.pressedCls)
            }
            if(c.iconCls||c.icon){
            if(c.text){
                a.push("icon-text-"+c.iconAlign)
                }else{
                a.push("icon")
                }
            }else{
        if(c.text){
            a.push("noicon")
            }
        }
    c.oldCls=a;
c.addClsWithUI(a);
    c.addClsWithUI(c.pressed?c.pressedCls:null)
    }
},
onRender:function(d,a){
    var e=this,c,b;
    Ext.applyIf(e.renderData,e.getTemplateArgs());
    Ext.applyIf(e.renderSelectors,{
        btnEl:e.href?"a":"button",
        btnWrap:"em",
        btnInnerEl:"."+e.baseCls+"-inner",
        btnIconEl:"."+e.baseCls+"-icon"
        });
    if(e.scale){
        e.ui=e.ui+"-"+e.scale
        }
        e.callParent(arguments);
    if(e.split&&e.arrowTooltip){
        e.arrowEl.dom[e.tooltipType]=e.arrowTooltip
        }
        e.mon(e.btnEl,{
        scope:e,
        focus:e.onFocus,
        blur:e.onBlur
        });
    b=e.el;
    if(e.icon){
        e.setIcon(e.icon)
        }
        if(e.iconCls){
        e.setIconCls(e.iconCls)
        }
        if(e.tooltip){
        e.setTooltip(e.tooltip,true)
        }
        if(e.handleMouseEvents){
        e.mon(b,{
            scope:e,
            mouseover:e.onMouseOver,
            mouseout:e.onMouseOut,
            mousedown:e.onMouseDown
            });
        if(e.split){
            e.mon(b,{
                mousemove:e.onMouseMove,
                scope:e
            })
            }
        }
    if(e.menu){
    e.mon(e.menu,{
        scope:e,
        show:e.onMenuShow,
        hide:e.onMenuHide
        });
    e.keyMap=Ext.create("Ext.util.KeyMap",e.el,{
        key:Ext.EventObject.DOWN,
        handler:e.onDownKey,
        scope:e
    })
    }
    if(e.repeat){
    c=Ext.create("Ext.util.ClickRepeater",b,Ext.isObject(e.repeat)?e.repeat:{});
    e.mon(c,"click",e.onRepeatClick,e)
    }else{
    e.mon(b,e.clickEvent,e.onClick,e)
    }
    Ext.ButtonToggleManager.register(e)
},
getTemplateArgs:function(){
    var c=this,b=c.getPersistentBtnPadding(),a="";
    if(Math.max.apply(Math,b)>0){
        a="margin:"+Ext.Array.map(b,function(d){
            return -d+"px"
            }).join(" ")
        }
        return{
        href:c.getHref(),
        target:c.target||"_blank",
        type:c.type,
        splitCls:c.getSplitCls(),
        cls:c.cls,
        text:c.text||"&#160;",
        tabIndex:c.tabIndex,
        innerSpanStyle:a
    }
},
getHref:function(){
    var a=this,b=Ext.apply({},a.baseParams);
    b=Ext.apply(b,a.params);
    return a.href?Ext.urlAppend(a.href,Ext.Object.toQueryString(b)):false
    },
setParams:function(a){
    this.params=a;
    this.btnEl.dom.href=this.getHref()
    },
getSplitCls:function(){
    var a=this;
    return a.split?(a.baseCls+"-"+a.arrowCls)+" "+(a.baseCls+"-"+a.arrowCls+"-"+a.arrowAlign):""
    },
afterRender:function(){
    var a=this;
    a.useSetClass=true;
    a.setButtonCls();
    a.doc=Ext.getDoc();
    this.callParent(arguments)
    },
setIconCls:function(b){
    var c=this,a=c.btnIconEl;
    if(a){
        a.removeCls(c.iconCls);
        a.addCls(b||"");
        c.setButtonCls()
        }
        c.iconCls=b;
    return c
    },
setTooltip:function(c,a){
    var b=this;
    if(b.rendered){
        if(!a){
            b.clearTip()
            }
            if(Ext.isObject(c)){
            Ext.tip.QuickTipManager.register(Ext.apply({
                target:b.btnEl.id
                },c));
            b.tooltip=c
            }else{
            b.btnEl.dom.setAttribute("data-"+this.tooltipType,c)
            }
        }else{
    b.tooltip=c
    }
    return b
},
getRefItems:function(a){
    var c=this.menu,b;
    if(c){
        b=c.getRefItems(a);
        b.unshift(c)
        }
        return b||[]
    },
clearTip:function(){
    if(Ext.isObject(this.tooltip)){
        Ext.tip.QuickTipManager.unregister(this.btnEl)
        }
    },
beforeDestroy:function(){
    var a=this;
    if(a.rendered){
        a.clearTip()
        }
        if(a.menu&&a.destroyMenu!==false){
        Ext.destroy(a.btnEl,a.btnInnerEl,a.menu)
        }
        Ext.destroy(a.repeater)
    },
onDestroy:function(){
    var a=this;
    if(a.rendered){
        a.doc.un("mouseover",a.monitorMouseOver,a);
        a.doc.un("mouseup",a.onMouseUp,a);
        delete a.doc;
        delete a.btnEl;
        delete a.btnInnerEl;
        Ext.ButtonToggleManager.unregister(a);
        Ext.destroy(a.keyMap);
        delete a.keyMap
        }
        a.callParent()
    },
setHandler:function(b,a){
    this.handler=b;
    this.scope=a;
    return this
    },
setText:function(b){
    var a=this;
    a.text=b;
    if(a.el){
        a.btnInnerEl.update(b||"&#160;");
        a.setButtonCls()
        }
        a.doComponentLayout();
    return a
    },
setIcon:function(a){
    var b=this,c=b.btnInnerEl;
    b.icon=a;
    if(c){
        c.setStyle("background-image",a?"url("+a+")":"");
        b.setButtonCls()
        }
        return b
    },
getText:function(){
    return this.text
    },
toggle:function(c,a){
    var b=this;
    c=c===undefined?!b.pressed:!!c;
    if(c!==b.pressed){
        if(b.rendered){
            b[c?"addClsWithUI":"removeClsWithUI"](b.pressedCls)
            }
            b.btnEl.dom.setAttribute("aria-pressed",c);
        b.pressed=c;
        if(!a){
            b.fireEvent("toggle",b,c);
            Ext.callback(b.toggleHandler,b.scope||b,[b,c])
            }
        }
    return b
},
showMenu:function(){
    var a=this;
    if(a.rendered&&a.menu){
        if(a.tooltip){
            Ext.tip.QuickTipManager.getQuickTip().cancelShow(a.btnEl)
            }
            if(a.menu.isVisible()){
            a.menu.hide()
            }
            a.menu.showBy(a.el,a.menuAlign)
        }
        return a
    },
hideMenu:function(){
    if(this.hasVisibleMenu()){
        this.menu.hide()
        }
        return this
    },
hasVisibleMenu:function(){
    var a=this.menu;
    return a&&a.rendered&&a.isVisible()
    },
onRepeatClick:function(a,b){
    this.onClick(b)
    },
onClick:function(b){
    var a=this;
    if(a.preventDefault||(a.disabled&&a.getHref())&&b){
        b.preventDefault()
        }
        if(b.button!==0){
        return
    }
    if(!a.disabled){
        if(a.enableToggle&&(a.allowDepress!==false||!a.pressed)){
            a.toggle()
            }
            if(a.menu&&!a.hasVisibleMenu()&&!a.ignoreNextClick){
            a.showMenu()
            }
            a.fireEvent("click",a,b);
        if(a.handler){
            a.handler.call(a.scope||a,a,b)
            }
            a.onBlur()
        }
    },
onMouseOver:function(b){
    var a=this;
    if(!a.disabled&&!b.within(a.el,true,true)){
        a.onMouseEnter(b)
        }
    },
onMouseOut:function(b){
    var a=this;
    if(!b.within(a.el,true,true)){
        if(a.overMenuTrigger){
            a.onMenuTriggerOut(b)
            }
            a.onMouseLeave(b)
        }
    },
onMouseMove:function(g){
    var d=this,c=d.el,f=d.overMenuTrigger,b,a;
    if(d.split){
        if(d.arrowAlign==="right"){
            b=g.getX()-c.getX();
            a=c.getWidth()
            }else{
            b=g.getY()-c.getY();
            a=c.getHeight()
            }
            if(b>(a-d.getTriggerSize())){
            if(!f){
                d.onMenuTriggerOver(g)
                }
            }else{
        if(f){
            d.onMenuTriggerOut(g)
            }
        }
}
},
getTriggerSize:function(){
    var e=this,c=e.triggerSize,b,a,d;
    if(c===d){
        b=e.arrowAlign;
        a=b.charAt(0);
        c=e.triggerSize=e.el.getFrameWidth(a)+e.btnWrap.getFrameWidth(a)+(e.frameSize&&e.frameSize[b]||0)
        }
        return c
    },
onMouseEnter:function(b){
    var a=this;
    a.addClsWithUI(a.overCls);
    a.fireEvent("mouseover",a,b)
    },
onMouseLeave:function(b){
    var a=this;
    a.removeClsWithUI(a.overCls);
    a.fireEvent("mouseout",a,b)
    },
onMenuTriggerOver:function(b){
    var a=this;
    a.overMenuTrigger=true;
    a.fireEvent("menutriggerover",a,a.menu,b)
    },
onMenuTriggerOut:function(b){
    var a=this;
    delete a.overMenuTrigger;
    a.fireEvent("menutriggerout",a,a.menu,b)
    },
enable:function(a){
    var b=this;
    b.callParent(arguments);
    b.removeClsWithUI("disabled");
    return b
    },
disable:function(a){
    var b=this;
    b.callParent(arguments);
    b.addClsWithUI("disabled");
    return b
    },
setScale:function(c){
    var a=this,b=a.ui.replace("-"+a.scale,"");
    if(!Ext.Array.contains(a.allowedScales,c)){
        throw ("#setScale: scale must be an allowed scale ("+a.allowedScales.join(", ")+")")
        }
        a.scale=c;
    a.setUI(b)
    },
setUI:function(b){
    var a=this;
    if(a.scale&&!b.match(a.scale)){
        b=b+"-"+a.scale
        }
        a.callParent([b])
    },
onFocus:function(b){
    var a=this;
    if(!a.disabled){
        a.addClsWithUI(a.focusCls)
        }
    },
onBlur:function(b){
    var a=this;
    a.removeClsWithUI(a.focusCls)
    },
onMouseDown:function(b){
    var a=this;
    if(!a.disabled&&b.button===0){
        a.addClsWithUI(a.pressedCls);
        a.doc.on("mouseup",a.onMouseUp,a)
        }
    },
onMouseUp:function(b){
    var a=this;
    if(b.button===0){
        if(!a.pressed){
            a.removeClsWithUI(a.pressedCls)
            }
            a.doc.un("mouseup",a.onMouseUp,a)
        }
    },
onMenuShow:function(b){
    var a=this;
    a.ignoreNextClick=0;
    a.addClsWithUI(a.menuActiveCls);
    a.fireEvent("menushow",a,a.menu)
    },
onMenuHide:function(b){
    var a=this;
    a.removeClsWithUI(a.menuActiveCls);
    a.ignoreNextClick=Ext.defer(a.restoreClick,250,a);
    a.fireEvent("menuhide",a,a.menu)
    },
restoreClick:function(){
    this.ignoreNextClick=0
    },
onDownKey:function(){
    var a=this;
    if(!a.disabled){
        if(a.menu){
            a.showMenu()
            }
        }
},
getPersistentBtnPadding:function(){
    var a=Ext.button.Button,e=a.persistentPadding,c,b,d,f;
    if(!e){
        e=a.persistentPadding=[0,0,0,0];
        if(!Ext.isIE){
            c=Ext.create("Ext.button.Button",{
                renderTo:Ext.getBody(),
                text:"test",
                style:"position:absolute;top:-999px;"
            });
            d=c.btnEl;
            f=c.btnInnerEl;
            d.setSize(null,null);
            b=f.getOffsetsTo(d);
            e[0]=b[1];
            e[1]=d.getWidth()-f.getWidth()-b[0];
            e[2]=d.getHeight()-f.getHeight()-b[1];
            e[3]=b[0];
            c.destroy()
            }
        }
    return e
}
},function(){
    var a={},e,d,b;
    function c(f,g){
        if(g){
            e=a[f.toggleGroup];
            for(d=0,b=e.length;d<b;d++){
                if(e[d]!==f){
                    e[d].toggle(false)
                    }
                }
            }
    }
Ext.ButtonToggleManager={
    register:function(f){
        if(!f.toggleGroup){
            return
        }
        var g=a[f.toggleGroup];
        if(!g){
            g=a[f.toggleGroup]=[]
            }
            g.push(f);
        f.on("toggle",c)
        },
    unregister:function(f){
        if(!f.toggleGroup){
            return
        }
        var g=a[f.toggleGroup];
        if(g){
            Ext.Array.remove(g,f);
            f.un("toggle",c)
            }
        },
getPressed:function(k){
    var j=a[k],h=0,f;
    if(j){
        for(f=j.length;h<f;h++){
            if(j[h].pressed===true){
                return j[h]
                }
            }
        }
    return null
}
}
});
Ext.define("Ext.container.AbstractContainer",{
    extend:"Ext.Component",
    requires:["Ext.util.MixedCollection","Ext.layout.container.Auto","Ext.ZIndexManager"],
    suspendLayout:false,
    autoDestroy:true,
    defaultType:"panel",
    isContainer:true,
    layoutCounter:0,
    baseCls:Ext.baseCSSPrefix+"container",
    bubbleEvents:["add","remove"],
    initComponent:function(){
        var a=this;
        a.addEvents("afterlayout","beforeadd","beforeremove","add","remove","beforecardswitch","cardswitch");
        a.layoutOnShow=Ext.create("Ext.util.MixedCollection");
        a.callParent();
        a.initItems()
        },
    initItems:function(){
        var b=this,a=b.items;
        b.items=Ext.create("Ext.util.MixedCollection",false,b.getComponentId);
        if(a){
            if(!Ext.isArray(a)){
                a=[a]
                }
                b.add(a)
            }
        },
afterRender:function(){
    this.getLayout();
    this.callParent()
    },
renderChildren:function(){
    var b=this,a=b.getLayout();
    b.callParent();
    if(a){
        b.suspendLayout=true;
        a.renderChildren();
        delete b.suspendLayout
        }
    },
setLayout:function(b){
    var a=this.layout;
    if(a&&a.isLayout&&a!=b){
        a.setOwner(null)
        }
        this.layout=b;
    b.setOwner(this)
    },
getLayout:function(){
    var a=this;
    if(!a.layout||!a.layout.isLayout){
        a.setLayout(Ext.layout.Layout.create(a.layout,"autocontainer"))
        }
        return a.layout
    },
doLayout:function(){
    var b=this,a=b.getLayout();
    if(b.rendered&&a&&!b.suspendLayout){
        if(!b.isFixedWidth()||!b.isFixedHeight()){
            if(b.componentLayout.layoutBusy!==true){
                b.doComponentLayout();
                if(b.componentLayout.layoutCancelled===true){
                    a.layout()
                    }
                }
        }else{
    if(a.layoutBusy!==true){
        a.layout()
        }
    }
}
return b
},
afterLayout:function(a){
    ++this.layoutCounter;
    this.fireEvent("afterlayout",this,a)
    },
prepareItems:function(b,d){
    if(!Ext.isArray(b)){
        b=[b]
        }
        var c=0,a=b.length,e;
    for(;c<a;c++){
        e=b[c];
        if(d){
            e=this.applyDefaults(e)
            }
            b[c]=this.lookupComponent(e)
        }
        return b
    },
applyDefaults:function(a){
    var b=this.defaults;
    if(b){
        if(Ext.isFunction(b)){
            b=b.call(this,a)
            }
            if(Ext.isString(a)){
            a=Ext.ComponentManager.get(a);
            Ext.applyIf(a,b)
            }else{
            if(!a.isComponent){
                Ext.applyIf(a,b)
                }else{
                Ext.applyIf(a,b)
                }
            }
    }
return a
},
lookupComponent:function(a){
    return Ext.isString(a)?Ext.ComponentManager.get(a):this.createComponent(a)
    },
createComponent:function(a,b){
    return Ext.ComponentManager.create(a,b||this.defaultType)
    },
getComponentId:function(a){
    return a.getItemId()
    },
add:function(){
    var h=this,f=Array.prototype.slice.call(arguments),a,g,b=[],c,e,k,d=-1,j;
    if(typeof f[0]=="number"){
        d=f.shift()
        }
        a=f.length>1;
    if(a||Ext.isArray(f[0])){
        g=a?f:f[0];
        h.suspendLayout=true;
        for(c=0,e=g.length;c<e;c++){
            k=g[c];
            if(d!=-1){
                k=h.add(d+c,k)
                }else{
                k=h.add(k)
                }
                b.push(k)
            }
            h.suspendLayout=false;
        h.doLayout();
        return b
        }
        j=h.prepareItems(f[0],true)[0];
    if(j.floating){
        j.onAdded(h,d)
        }else{
        d=(d!==-1)?d:h.items.length;
        if(h.fireEvent("beforeadd",h,j,d)!==false&&h.onBeforeAdd(j)!==false){
            h.items.insert(d,j);
            j.onAdded(h,d);
            h.onAdd(j,d);
            h.fireEvent("add",h,j,d)
            }
            h.doLayout()
        }
        return j
    },
registerFloatingItem:function(b){
    var a=this;
    if(!a.floatingItems){
        a.floatingItems=Ext.create("Ext.ZIndexManager",a)
        }
        a.floatingItems.register(b)
    },
onAdd:Ext.emptyFn,
onRemove:Ext.emptyFn,
insert:function(b,a){
    return this.add(b,a)
    },
move:function(b,d){
    var a=this.items,c;
    c=a.removeAt(b);
    if(c===false){
        return false
        }
        a.insert(d,c);
    this.doLayout();
    return c
    },
onBeforeAdd:function(b){
    var a=this;
    if(b.ownerCt){
        b.ownerCt.remove(b,false)
        }
        if(a.border===false||a.border===0){
        b.border=(b.border===true)
        }
    },
remove:function(a,b){
    var d=this,e=d.getComponent(a);
    if(e&&d.fireEvent("beforeremove",d,e)!==false){
        d.doRemove(e,b);
        d.fireEvent("remove",d,e)
        }
        return e
    },
doRemove:function(c,b){
    var e=this,d=e.layout,a=d&&e.rendered;
    e.items.remove(c);
    c.onRemoved();
    if(a){
        d.onRemove(c)
        }
        e.onRemove(c,b);
    if(b===true||(b!==false&&e.autoDestroy)){
        c.destroy()
        }
        if(a&&!b){
        d.afterRemove(c)
        }
        if(!e.destroying){
        e.doLayout()
        }
    },
removeAll:function(c){
    var g=this,e=g.items.items.slice(),b=[],d=0,a=e.length,f;
    g.suspendLayout=true;
    for(;d<a;d++){
        f=e[d];
        g.remove(f,c);
        if(f.ownerCt!==g){
            b.push(f)
            }
        }
    g.suspendLayout=false;
if(a){
    g.doLayout()
    }
    return b
},
getRefItems:function(c){
    var g=this,d=g.items.items,b=d.length,e=0,f,a=[];
    for(;e<b;e++){
        f=d[e];
        a.push(f);
        if(c&&f.getRefItems){
            a.push.apply(a,f.getRefItems(true))
            }
        }
    if(g.floatingItems&&g.floatingItems.accessList){
    a.push.apply(a,g.floatingItems.accessList)
    }
    return a
},
cascade:function(k,l,a){
    var j=this,e=j.items?j.items.items:[],f=e.length,d=0,h,g=a?a.concat(j):[j],b=g.length-1;
    if(k.apply(l||j,g)!==false){
        for(;d<f;d++){
            h=e[d];
            if(h.cascade){
                h.cascade(k,l,a)
                }else{
                g[b]=h;
                k.apply(l||e,g)
                }
            }
        }
    return this
},
getComponent:function(a){
    if(Ext.isObject(a)){
        a=a.getItemId()
        }
        return this.items.get(a)
    },
query:function(a){
    return Ext.ComponentQuery.query(a,this)
    },
child:function(a){
    return this.query("> "+a)[0]||null
    },
down:function(a){
    return this.query(a)[0]||null
    },
show:function(){
    this.callParent(arguments);
    this.performDeferredLayouts();
    return this
    },
performDeferredLayouts:function(){
    var e=this.layoutOnShow,d=e.getCount(),b=0,a,c;
    for(;b<d;b++){
        c=e.get(b);
        a=c.needsLayout;
        if(Ext.isObject(a)){
            c.doComponentLayout(a.width,a.height,a.isSetSize,a.ownerCt)
            }
        }
    e.clear()
},
onEnable:function(){
    Ext.Array.each(this.query("[isFormField]"),function(a){
        if(a.resetDisable){
            a.enable();
            delete a.resetDisable
            }
        });
this.callParent()
},
onDisable:function(){
    Ext.Array.each(this.query("[isFormField]"),function(a){
        if(a.resetDisable!==false&&!a.disabled){
            a.disable();
            a.resetDisable=true
            }
        });
this.callParent()
},
beforeLayout:function(){
    return true
    },
beforeDestroy:function(){
    var b=this,a=b.items,d;
    if(a){
        while((d=a.first())){
            b.doRemove(d,true)
            }
        }
    Ext.destroy(b.layout,b.floatingItems);
b.callParent()
}
});
Ext.define("Ext.container.Container",{
    extend:"Ext.container.AbstractContainer",
    alias:"widget.container",
    alternateClassName:"Ext.Container",
    getChildByElement:function(d){
        var f,b,a=0,c=this.items.items,e=c.length;
        d=Ext.getDom(d);
        for(;a<e;a++){
            f=c[a];
            b=f.getEl();
            if((b.dom===d)||b.contains(d)){
                return f
                }
            }
        return null
    }
});
Ext.define("Ext.container.Viewport",{
    extend:"Ext.container.Container",
    alias:"widget.viewport",
    requires:["Ext.EventManager"],
    alternateClassName:"Ext.Viewport",
    isViewport:true,
    ariaRole:"application",
    initComponent:function(){
        var c=this,a=Ext.fly(document.body.parentNode),b;
        c.callParent(arguments);
        a.addCls(Ext.baseCSSPrefix+"viewport");
        if(c.autoScroll){
            a.setStyle("overflow","auto")
            }
            c.el=b=Ext.getBody();
        b.setHeight=Ext.emptyFn;
        b.setWidth=Ext.emptyFn;
        b.setSize=Ext.emptyFn;
        b.dom.scroll="no";
        c.allowDomMove=false;
        Ext.EventManager.onWindowResize(c.fireResize,c);
        c.renderTo=c.el;
        c.width=Ext.core.Element.getViewportWidth();
        c.height=Ext.core.Element.getViewportHeight()
        },
    fireResize:function(a,b){
        this.setSize(a,b)
        }
    });
Ext.define("Ext.layout.container.Border",{
    alias:["layout.border"],
    extend:"Ext.layout.container.Container",
    requires:["Ext.resizer.Splitter","Ext.container.Container","Ext.fx.Anim"],
    alternateClassName:"Ext.layout.BorderLayout",
    targetCls:Ext.baseCSSPrefix+"border-layout-ct",
    itemCls:Ext.baseCSSPrefix+"border-item",
    bindToOwnerCtContainer:true,
    percentageRe:/(\d+)%/,
    slideDirection:{
        north:"t",
        south:"b",
        west:"l",
        east:"r"
    },
    constructor:function(a){
        this.initialConfig=a;
        this.callParent(arguments)
        },
    onLayout:function(){
        var a=this;
        if(!a.borderLayoutInitialized){
            a.initializeBorderLayout()
            }
            a.fixHeightConstraints();
        a.shadowLayout.onLayout();
        if(a.embeddedContainer){
            a.embeddedContainer.layout.onLayout()
            }
            if(!a.initialCollapsedComplete){
            Ext.iterate(a.regions,function(b,c){
                if(c.borderCollapse){
                    a.onBeforeRegionCollapse(c,c.collapseDirection,false,0)
                    }
                });
        a.initialCollapsedComplete=true
        }
    },
isValidParent:function(b,c,a){
    if(!this.borderLayoutInitialized){
        this.initializeBorderLayout()
        }
        return this.shadowLayout.isValidParent(b,c,a)
    },
beforeLayout:function(){
    if(!this.borderLayoutInitialized){
        this.initializeBorderLayout()
        }
        this.shadowLayout.beforeLayout()
    },
renderItems:function(a,b){},
    renderItem:function(a){},
    renderChildren:function(){
    if(!this.borderLayoutInitialized){
        this.initializeBorderLayout()
        }
        this.shadowLayout.renderChildren()
    },
getVisibleItems:function(){
    return Ext.ComponentQuery.query(":not([slideOutAnim])",this.callParent(arguments))
    },
initializeBorderLayout:function(){
    var j=this,c=0,h=j.getLayoutItems(),g=h.length,b=(j.regions={}),e=[],f=[],a=0,l=0,d,k;
    j.splitters={};
    
    for(;c<g;c++){
        d=h[c];
        b[d.region]=d;
        if(d.region!="center"&&d.collapsible&&d.collapseMode!="header"){
            d.borderCollapse=d.collapsed;
            delete d.collapsed;
            d.on({
                beforecollapse:j.onBeforeRegionCollapse,
                beforeexpand:j.onBeforeRegionExpand,
                destroy:j.onRegionDestroy,
                scope:j
            });
            j.setupState(d)
            }
        }
    d=b.center;
if(!d.flex){
    d.flex=1
    }
    delete d.width;
d.maintainFlex=true;
d=b.west;
if(d){
    d.collapseDirection=Ext.Component.DIRECTION_LEFT;
    f.push(d);
    if(d.split){
        f.push(j.splitters.west=j.createSplitter(d))
        }
        k=Ext.isString(d.width)&&d.width.match(j.percentageRe);
    if(k){
        a+=(d.flex=parseInt(k[1],10)/100);
        delete d.width
        }
    }
d=b.north;
if(d){
    d.collapseDirection=Ext.Component.DIRECTION_TOP;
    e.push(d);
    if(d.split){
        e.push(j.splitters.north=j.createSplitter(d))
        }
        k=Ext.isString(d.height)&&d.height.match(j.percentageRe);
    if(k){
        l+=(d.flex=parseInt(k[1],10)/100);
        delete d.height
        }
    }
if(b.north||b.south){
    if(b.east||b.west){
        e.push(j.embeddedContainer=Ext.create("Ext.container.Container",{
            xtype:"container",
            region:"center",
            id:j.owner.id+"-embedded-center",
            cls:Ext.baseCSSPrefix+"border-item",
            flex:b.center.flex,
            maintainFlex:true,
            layout:{
                type:"hbox",
                align:"stretch",
                getVisibleItems:j.getVisibleItems
                }
            }));
    f.push(b.center)
    }else{
    e.push(b.center)
    }
}else{
    f.push(b.center)
    }
    d=b.south;
if(d){
    d.collapseDirection=Ext.Component.DIRECTION_BOTTOM;
    if(d.split){
        e.push(j.splitters.south=j.createSplitter(d))
        }
        k=Ext.isString(d.height)&&d.height.match(j.percentageRe);
    if(k){
        l+=(d.flex=parseInt(k[1],10)/100);
        delete d.height
        }
        e.push(d)
    }
    d=b.east;
if(d){
    d.collapseDirection=Ext.Component.DIRECTION_RIGHT;
    if(d.split){
        f.push(j.splitters.east=j.createSplitter(d))
        }
        k=Ext.isString(d.width)&&d.width.match(j.percentageRe);
    if(k){
        a+=(d.flex=parseInt(k[1],10)/100);
        delete d.width
        }
        f.push(d)
    }
    if(b.north||b.south){
    j.shadowContainer=Ext.create("Ext.container.Container",{
        ownerCt:j.owner,
        el:j.getTarget(),
        layout:Ext.applyIf({
            type:"vbox",
            align:"stretch",
            getVisibleItems:j.getVisibleItems
            },j.initialConfig)
        });
    j.createItems(j.shadowContainer,e);
    if(j.splitters.north){
        j.splitters.north.ownerCt=j.shadowContainer
        }
        if(j.splitters.south){
        j.splitters.south.ownerCt=j.shadowContainer
        }
        if(j.embeddedContainer){
        j.embeddedContainer.ownerCt=j.shadowContainer;
        j.createItems(j.embeddedContainer,f);
        if(j.splitters.east){
            j.splitters.east.ownerCt=j.embeddedContainer
            }
            if(j.splitters.west){
            j.splitters.west.ownerCt=j.embeddedContainer
            }
            Ext.each([j.splitters.north,j.splitters.south],function(i){
            if(i){
                i.on("beforedragstart",j.fixHeightConstraints,j)
                }
            });
    if(a){
        b.center.flex-=a
        }
        if(l){
        j.embeddedContainer.flex-=l
        }
    }else{
    if(l){
        b.center.flex-=l
        }
    }
}else{
    j.shadowContainer=Ext.create("Ext.container.Container",{
        ownerCt:j.owner,
        el:j.getTarget(),
        layout:Ext.applyIf({
            type:(f.length==1)?"fit":"hbox",
            align:"stretch"
        },j.initialConfig)
        });
    j.createItems(j.shadowContainer,f);
    if(j.splitters.east){
        j.splitters.east.ownerCt=j.shadowContainer
        }
        if(j.splitters.west){
        j.splitters.west.ownerCt=j.shadowContainer
        }
        if(a){
        b.center.flex-=l
        }
    }
for(c=0,h=j.shadowContainer.items.items,g=h.length;c<g;c++){
    h[c].shadowOwnerCt=j.shadowContainer
    }
    if(j.embeddedContainer){
    for(c=0,h=j.embeddedContainer.items.items,g=h.length;c<g;c++){
        h[c].shadowOwnerCt=j.embeddedContainer
        }
    }
    j.shadowLayout=j.shadowContainer.getLayout();
j.borderLayoutInitialized=true
},
setupState:function(b){
    var a=b.getState;
    b.getState=function(){
        var c=a.call(b)||{},d=b.region;
        c.collapsed=!!b.collapsed;
        if(d=="west"||d=="east"){
            c.width=b.getWidth()
            }else{
            c.height=b.getHeight()
            }
            return c
        };
        
    b.addStateEvents(["collapse","expand","resize"])
    },
createItems:function(a,b){
    delete a.items;
    a.initItems();
    a.items.addAll(b)
    },
createSplitter:function(a){
    var b=this,c=(a.collapseMode!="header"),d;
    d=Ext.create("Ext.resizer.Splitter",{
        hidden:!!a.hidden,
        collapseTarget:a,
        performCollapse:!c,
        listeners:c?{
            click:{
                fn:Ext.Function.bind(b.onSplitterCollapseClick,b,[a]),
                element:"collapseEl"
            }
        }:null
    });
if(a.collapseMode=="mini"){
    a.placeholder=d;
    d.collapsedCls=a.collapsedCls
    }
    a.on({
    hide:b.onRegionVisibilityChange,
    show:b.onRegionVisibilityChange,
    scope:b
});
return d
},
fixHeightConstraints:function(){
    var c=this,a=c.embeddedContainer,b=1e+99,d=-1;
    if(!a){
        return
    }
    a.items.each(function(e){
        if(Ext.isNumber(e.maxHeight)){
            b=Math.max(b,e.maxHeight)
            }
            if(Ext.isNumber(e.minHeight)){
            d=Math.max(d,e.minHeight)
            }
        });
a.maxHeight=b;
a.minHeight=d
},
onRegionVisibilityChange:function(a){
    this.splitters[a.region][a.hidden?"hide":"show"]();
    this.layout()
    },
onSplitterCollapseClick:function(a){
    if(a.collapsed){
        this.onPlaceHolderToolClick(null,null,null,{
            client:a
        })
        }else{
        a.collapse()
        }
    },
getPlaceholder:function(c){
    var d=this,f=c.placeholder,b=c.shadowOwnerCt,e=b.layout,a=Ext.panel.Panel.prototype.getOppositeDirection(c.collapseDirection),g=(c.region=="north"||c.region=="south");
    if(c.collapseMode=="header"){
        return
    }
    if(!f){
        if(c.collapseMode=="mini"){
            f=Ext.create("Ext.resizer.Splitter",{
                id:"collapse-placeholder-"+c.id,
                collapseTarget:c,
                performCollapse:false,
                listeners:{
                    click:{
                        fn:Ext.Function.bind(d.onSplitterCollapseClick,d,[c]),
                        element:"collapseEl"
                    }
                }
            });
    f.addCls(f.collapsedCls)
    }else{
    f={
        id:"collapse-placeholder-"+c.id,
        margins:c.initialConfig.margins||Ext.getClass(c).prototype.margins,
        xtype:"header",
        orientation:g?"horizontal":"vertical",
        title:c.title,
        textCls:c.headerTextCls,
        iconCls:c.iconCls,
        baseCls:c.baseCls+"-header",
        ui:c.ui,
        indicateDrag:c.draggable,
        cls:Ext.baseCSSPrefix+"region-collapsed-placeholder "+Ext.baseCSSPrefix+"region-collapsed-"+c.collapseDirection+"-placeholder "+c.collapsedCls,
        listeners:c.floatable?{
            click:{
                fn:function(h){
                    d.floatCollapsedPanel(h,c)
                    },
                element:"el"
            }
        }:null
    };
    
if((Ext.isIE6||Ext.isIE7||(Ext.isIEQuirks))&&!g){
    f.width=25
    }
    if(!c.hideCollapseTool){
    f[g?"tools":"items"]=[{
        xtype:"tool",
        client:c,
        type:"expand-"+a,
        handler:d.onPlaceHolderToolClick,
        scope:d
    }]
    }
}
f=d.owner.createComponent(f);
if(c.isXType("panel")){
    c.on({
        titlechange:d.onRegionTitleChange,
        iconchange:d.onRegionIconChange,
        scope:d
    })
    }
}
c.placeholder=f;
f.comp=c;
return f
},
onRegionTitleChange:function(a,b){
    a.placeholder.setTitle(b)
    },
onRegionIconChange:function(b,a){
    b.placeholder.setIconCls(a)
    },
calculateChildBox:function(a){
    var b=this;
    if(b.shadowContainer.items.contains(a)){
        return b.shadowContainer.layout.calculateChildBox(a)
        }else{
        if(b.embeddedContainer&&b.embeddedContainer.items.contains(a)){
            return b.embeddedContainer.layout.calculateChildBox(a)
            }
        }
},
onBeforeRegionCollapse:function(g,j,d){
    var i=this,n=g.el,c,b=g.collapseMode=="mini",h=g.shadowOwnerCt,a=h.layout,l=g.placeholder,f=i.owner.suspendLayout,k=h.suspendLayout,m=(g.region=="north"||g.region=="west");
    i.owner.suspendLayout=true;
    h.suspendLayout=true;
    a.layoutBusy=true;
    if(h.componentLayout){
        h.componentLayout.layoutBusy=true
        }
        i.shadowContainer.layout.layoutBusy=true;
    i.layoutBusy=true;
    i.owner.componentLayout.layoutBusy=true;
    if(!l){
        l=i.getPlaceholder(g)
        }
        if(l.shadowOwnerCt===h){
        l.show()
        }else{
        h.insert(h.items.indexOf(g)+(m?0:1),l);
        l.shadowOwnerCt=h;
        l.ownerCt=i.owner
        }
        g.hidden=true;
    if(!l.rendered){
        a.renderItem(l,a.innerCt);
        if(g.region=="north"||g.region=="south"){
            l.setCalculatedSize(g.getWidth())
            }else{
            l.setCalculatedSize(undefined,g.getHeight())
            }
        }
    function e(){
    i.owner.suspendLayout=f;
    h.suspendLayout=k;
    delete a.layoutBusy;
    if(h.componentLayout){
        delete h.componentLayout.layoutBusy
        }
        delete i.shadowContainer.layout.layoutBusy;
    delete i.layoutBusy;
    delete i.owner.componentLayout.layoutBusy;
    g.collapsed=true;
    g.fireEvent("collapse",g)
    }
    if(g.animCollapse&&i.initialCollapsedComplete){
    a.layout();
    n.dom.style.zIndex=100;
    if(!b){
        l.el.hide()
        }
        n.slideOut(i.slideDirection[g.region],{
        duration:Ext.Number.from(g.animCollapse,Ext.fx.Anim.prototype.duration),
        listeners:{
            afteranimate:function(){
                n.show().setLeftTop(-10000,-10000);
                n.dom.style.zIndex="";
                if(!b){
                    l.el.slideIn(i.slideDirection[g.region],{
                        easing:"linear",
                        duration:100
                    })
                    }
                    e()
                }
            }
    })
}else{
    n.setLeftTop(-10000,-10000);
    a.layout();
    e()
    }
    return false
},
onBeforeRegionExpand:function(b,a){
    this.onPlaceHolderToolClick(null,null,null,{
        client:b
    });
    return false
    },
onPlaceHolderToolClick:function(m,s,a,c){
    var r=this,o=c.client,h=(o.collapseMode!="mini")||!o.split,q=o.el,j,d=o.placeholder,k=d.el,p=o.shadowOwnerCt,b=p.layout,i,n=r.owner.suspendLayout,l=p.suspendLayout,g;
    if(o.getActiveAnimation()){
        o.stopAnimation()
        }
        if(o.slideOutAnim){
        q.un(o.panelMouseMon);
        k.un(o.placeholderMouseMon);
        delete o.slideOutAnim;
        delete o.panelMouseMon;
        delete o.placeholderMouseMon;
        g=true
        }
        r.owner.suspendLayout=true;
    p.suspendLayout=true;
    b.layoutBusy=true;
    if(p.componentLayout){
        p.componentLayout.layoutBusy=true
        }
        r.shadowContainer.layout.layoutBusy=true;
    r.layoutBusy=true;
    r.owner.componentLayout.layoutBusy=true;
    o.hidden=false;
    o.collapsed=false;
    if(h){
        d.hidden=true
        }
        j=b.calculateChildBox(o);
    if(o.collapseTool){
        o.collapseTool.show()
        }
        if(o.animCollapse&&!g){
        q.setStyle("visibility","hidden")
        }
        q.setLeftTop(j.left,j.top);
    i=o.getSize();
    if(i.height!=j.height||i.width!=j.width){
        r.setItemSize(o,j.width,j.height)
        }
        function f(){
        r.owner.suspendLayout=n;
        p.suspendLayout=l;
        delete b.layoutBusy;
        if(p.componentLayout){
            delete p.componentLayout.layoutBusy
            }
            delete r.shadowContainer.layout.layoutBusy;
        delete r.layoutBusy;
        delete r.owner.componentLayout.layoutBusy;
        o.removeCls(Ext.baseCSSPrefix+"border-region-slide-in");
        o.fireEvent("expand",o)
        }
        if(h){
        d.el.hide()
        }
        if(o.animCollapse&&!g){
        q.dom.style.zIndex=100;
        q.slideIn(r.slideDirection[o.region],{
            duration:Ext.Number.from(o.animCollapse,Ext.fx.Anim.prototype.duration),
            listeners:{
                afteranimate:function(){
                    q.dom.style.zIndex="";
                    o.hidden=false;
                    b.onLayout();
                    f()
                    }
                }
        })
}else{
    b.onLayout();
    f()
    }
},
floatCollapsedPanel:function(i,g){
    if(g.floatable===false){
        return
    }
    var j=this,o=g.el,m=g.placeholder,n=m.el,h=g.shadowOwnerCt,b=h.layout,f=b.getChildBox(m),l=h.suspendLayout,a,d,k;
    if(i.getTarget("."+Ext.baseCSSPrefix+"tool")){
        return
    }
    if(o.getActiveAnimation()){
        return
    }
    if(g.slideOutAnim){
        j.slideOutFloatedComponent(g);
        return
    }
    function c(q){
        var p=o.getRegion().union(n.getRegion()).adjust(1,-1,-1,1);
        if(!p.contains(q.getPoint())){
            j.slideOutFloatedComponent(g)
            }
        }
    g.placeholderMouseMon=n.monitorMouseLeave(500,c);
h.suspendLayout=true;
j.layoutBusy=true;
j.owner.componentLayout.layoutBusy=true;
if(g.collapseTool){
    g.collapseTool.hide()
    }
    g.hidden=false;
g.collapsed=false;
m.hidden=true;
d=b.calculateChildBox(g);
m.hidden=false;
if(g.region=="north"||g.region=="west"){
    d[b.parallelBefore]+=f[b.parallelPrefix]-1
    }else{
    d[b.parallelBefore]-=(f[b.parallelPrefix]-1)
    }
    o.setStyle("visibility","hidden");
o.setLeftTop(d.left,d.top);
a=g.getSize();
if(a.height!=d.height||a.width!=d.width){
    j.setItemSize(g,d.width,d.height)
    }
    k={
    listeners:{
        afteranimate:function(){
            h.suspendLayout=l;
            delete j.layoutBusy;
            delete j.owner.componentLayout.layoutBusy;
            k.listeners={
                afterAnimate:function(){
                    o.show().removeCls(Ext.baseCSSPrefix+"border-region-slide-in").setLeftTop(-10000,-10000);
                    g.hidden=true;
                    g.collapsed=true;
                    delete g.slideOutAnim;
                    delete g.panelMouseMon;
                    delete g.placeholderMouseMon
                    }
                };
            
        g.slideOutAnim=k
        }
    },
duration:500
};

o.addCls(Ext.baseCSSPrefix+"border-region-slide-in");
o.slideIn(j.slideDirection[g.region],k);
g.panelMouseMon=o.monitorMouseLeave(500,c)
},
slideOutFloatedComponent:function(a){
    var c=a.el,b;
    c.un(a.panelMouseMon);
    a.placeholder.el.un(a.placeholderMouseMon);
    c.slideOut(this.slideDirection[a.region],a.slideOutAnim);
    delete a.slideOutAnim;
    delete a.panelMouseMon;
    delete a.placeholderMouseMon
    },
onRegionDestroy:function(a){
    var b=a.placeholder;
    if(b){
        delete b.ownerCt;
        b.destroy()
        }
    },
onDestroy:function(){
    var b=this,a=b.shadowContainer,c=b.embeddedContainer;
    if(a){
        delete a.ownerCt;
        Ext.destroy(a)
        }
        if(c){
        delete c.ownerCt;
        Ext.destroy(c)
        }
        delete b.regions;
    delete b.splitters;
    delete b.shadowContainer;
    delete b.embeddedContainer;
    b.callParent(arguments)
    }
});
Ext.define("Ext.app.PortalColumn",{
    extend:"Ext.container.Container",
    alias:"widget.portalcolumn",
    layout:{
        type:"anchor"
    },
    defaultType:"portlet",
    cls:"x-portal-column",
    autoHeight:true
});
Ext.define("Ext.toolbar.Item",{
    extend:"Ext.Component",
    alias:"widget.tbitem",
    alternateClassName:"Ext.Toolbar.Item",
    enable:Ext.emptyFn,
    disable:Ext.emptyFn,
    focus:Ext.emptyFn
    });
Ext.define("Ext.toolbar.Separator",{
    extend:"Ext.toolbar.Item",
    alias:"widget.tbseparator",
    alternateClassName:"Ext.Toolbar.Separator",
    baseCls:Ext.baseCSSPrefix+"toolbar-separator",
    focusable:false
});
Ext.define("Ext.layout.container.boxOverflow.Menu",{
    extend:"Ext.layout.container.boxOverflow.None",
    requires:["Ext.toolbar.Separator","Ext.button.Button"],
    alternateClassName:"Ext.layout.boxOverflow.Menu",
    noItemsMenuText:'<div class="'+Ext.baseCSSPrefix+'toolbar-no-items">(None)</div>',
    constructor:function(b){
        var a=this;
        a.callParent(arguments);
        b.beforeLayout=Ext.Function.createInterceptor(b.beforeLayout,this.clearOverflow,this);
        a.afterCtCls=a.afterCtCls||Ext.baseCSSPrefix+"box-menu-"+b.parallelAfter;
        a.menuItems=[]
        },
    onRemove:function(a){
        Ext.Array.remove(this.menuItems,a)
        },
    handleOverflow:function(a,g){
        var f=this,e=f.layout,c="get"+e.parallelPrefixCap,b={},d=[null,null];
        f.callParent(arguments);
        this.createMenu(a,g);
        b[e.perpendicularPrefix]=g[e.perpendicularPrefix];
        b[e.parallelPrefix]=g[e.parallelPrefix]-f.afterCt[c]();
        d[e.perpendicularSizeIndex]=(a.meta.maxSize-f.menuTrigger["get"+e.perpendicularPrefixCap]())/2;
        f.menuTrigger.setPosition.apply(f.menuTrigger,d);
        return{
            targetSize:b
        }
    },
clearOverflow:function(a,h){
    var g=this,f=h?h.width+(g.afterCt?g.afterCt.getWidth():0):0,b=g.menuItems,c=0,e=b.length,d;
    g.hideTrigger();
    for(;c<e;c++){
        b[c].show()
        }
        b.length=0;
    return h?{
        targetSize:{
            height:h.height,
            width:f
        }
    }:null
},
showTrigger:function(){
    this.menuTrigger.show()
    },
hideTrigger:function(){
    if(this.menuTrigger!==undefined){
        this.menuTrigger.hide()
        }
    },
beforeMenuShow:function(h){
    var g=this,b=g.menuItems,d=0,a=b.length,f,e;
    var c=function(j,i){
        return j.isXType("buttongroup")&&!(i instanceof Ext.toolbar.Separator)
        };
        
    g.clearMenu();
    h.removeAll();
    for(;d<a;d++){
        f=b[d];
        if(!d&&(f instanceof Ext.toolbar.Separator)){
            continue
        }
        if(e&&(c(f,e)||c(e,f))){
            h.add("-")
            }
            g.addComponentToMenu(h,f);
        e=f
        }
        if(h.items.length<1){
        h.add(g.noItemsMenuText)
        }
    },
createMenuConfig:function(c,a){
    var b=Ext.apply({},c.initialConfig),d=c.toggleGroup;
    Ext.copyTo(b,c,["iconCls","icon","itemId","disabled","handler","scope","menu"]);
    Ext.apply(b,{
        text:c.overflowText||c.text,
        hideOnClick:a,
        destroyMenu:false
    });
    if(d||c.enableToggle){
        Ext.apply(b,{
            group:d,
            checked:c.pressed,
            listeners:{
                checkchange:function(f,e){
                    c.toggle(e)
                    }
                }
        })
}
delete b.ownerCt;
delete b.xtype;
delete b.id;
return b
},
addComponentToMenu:function(c,a){
    var b=this;
    if(a instanceof Ext.toolbar.Separator){
        c.add("-")
        }else{
        if(a.isComponent){
            if(a.isXType("splitbutton")){
                c.add(b.createMenuConfig(a,true))
                }else{
                if(a.isXType("button")){
                    c.add(b.createMenuConfig(a,!a.menu))
                    }else{
                    if(a.isXType("buttongroup")){
                        a.items.each(function(d){
                            b.addComponentToMenu(c,d)
                            })
                        }else{
                        c.add(Ext.create(Ext.getClassName(a),b.createMenuConfig(a)))
                        }
                    }
            }
    }
}
},
clearMenu:function(){
    var a=this.moreMenu;
    if(a&&a.items){
        a.items.each(function(b){
            if(b.menu){
                delete b.menu
                }
            })
    }
},
createMenu:function(a,c){
    var k=this,h=k.layout,l=h.parallelBefore,e=h.parallelPrefix,b=c[e],g=a.boxes,d=0,j=g.length,f;
    if(!k.menuTrigger){
        k.createInnerElements();
        k.menu=Ext.create("Ext.menu.Menu",{
            listeners:{
                scope:k,
                beforeshow:k.beforeMenuShow
                }
            });
    k.menuTrigger=Ext.create("Ext.button.Button",{
        ownerCt:k.layout.owner,
        iconCls:Ext.baseCSSPrefix+h.owner.getXType()+"-more-icon",
        ui:h.owner instanceof Ext.toolbar.Toolbar?"default-toolbar":"default",
        menu:k.menu,
        getSplitCls:function(){
            return""
            },
        renderTo:k.afterCt
        })
    }
    k.showTrigger();
b-=k.afterCt.getWidth();
k.menuItems.length=0;
for(;d<j;d++){
    f=g[d];
    if(f[l]+f[e]>b){
        k.menuItems.push(f.component);
        f.component.hide()
        }
    }
},
createInnerElements:function(){
    var a=this,b=a.layout.getRenderTarget();
    if(!this.afterCt){
        b.addCls(Ext.baseCSSPrefix+a.layout.direction+"-box-overflow-body");
        this.afterCt=b.insertSibling({
            cls:Ext.layout.container.Box.prototype.innerCls+" "+this.afterCtCls
            },"before")
        }
    },
destroy:function(){
    Ext.destroy(this.menu,this.menuTrigger)
    }
});
Ext.define("Ext.layout.container.Box",{
    alias:["layout.box"],
    extend:"Ext.layout.container.Container",
    alternateClassName:"Ext.layout.BoxLayout",
    requires:["Ext.layout.container.boxOverflow.None","Ext.layout.container.boxOverflow.Menu","Ext.layout.container.boxOverflow.Scroller","Ext.util.Format","Ext.dd.DragDropManager"],
    defaultMargins:{
        top:0,
        right:0,
        bottom:0,
        left:0
    },
    padding:"0",
    pack:"start",
    type:"box",
    scrollOffset:0,
    itemCls:Ext.baseCSSPrefix+"box-item",
    targetCls:Ext.baseCSSPrefix+"box-layout-ct",
    innerCls:Ext.baseCSSPrefix+"box-inner",
    bindToOwnerCtContainer:true,
    availableSpaceOffset:0,
    reserveOffset:true,
    clearInnerCtOnLayout:false,
    flexSortFn:function(d,c){
        var e="max"+this.parallelPrefixCap,f=Infinity;
        d=d.component[e]||f;
        c=c.component[e]||f;
        if(!isFinite(d)&&!isFinite(c)){
            return false
            }
            return d-c
        },
    minSizeSortFn:function(d,c){
        return c.available-d.available
        },
    constructor:function(a){
        var b=this;
        b.callParent(arguments);
        b.flexSortFn=Ext.Function.bind(b.flexSortFn,b);
        b.initOverflowHandler()
        },
    getChildBox:function(b){
        b=b.el||this.owner.getComponent(b).el;
        var a=b.getBox(false,true);
        return{
            left:a.left,
            top:a.top,
            width:a.width,
            height:a.height
            }
        },
calculateChildBox:function(e){
    var d=this,b=d.calculateChildBoxes(d.getVisibleItems(),d.getLayoutTargetSize()).boxes,c=b.length,a=0;
    e=d.owner.getComponent(e);
    for(;a<c;a++){
        if(b[a].component===e){
            return b[a]
            }
        }
    },
calculateChildBoxes:function(p,b){
    var x=this,J=Math,l=J.max,o=Infinity,v,s=x.parallelPrefix,n=x.parallelPrefixCap,M=x.perpendicularPrefix,t=x.perpendicularPrefixCap,C="min"+n,F="min"+t,ak="max"+t,c=b[s]-x.scrollOffset,Z=b[M],ae=x.padding,r=ae[x.parallelBefore],u=r+ae[x.parallelAfter],P=ae[x.perpendicularLeftTop],K=P+ae[x.perpendicularRightBottom],ah=l(0,Z-K),aj=x.innerCt.getBorderWidth(x.perpendicularLT+x.perpendicularRB),ab=x.pack=="start",an=x.pack=="center",E=x.pack=="end",ad=Ext.Number.constrain,N=p.length,d=0,ai=0,al=0,w=0,G=0,Q=[],L=[],ag,af,h,aa,A,B,am,Y,W,X,m,e,z,I,y,O,j,R,ac,f,D,T,a,q,k,H,S,V,g,U;
    for(af=0;af<N;af++){
        h=p[af];
        A=h[M];
        if(!h.flex||!(x.align=="stretch"||x.align=="stretchmax")){
            if(h.componentLayout.initialized!==true){
                x.layoutItem(h)
                }
            }
        B=h.margins;
    H=B[x.parallelBefore]+B[x.parallelAfter];
    W={
        component:h,
        margins:B
    };
    
    if(h.flex){
        ai+=h.flex;
        aa=v
        }else{
        if(!(h[s]&&A)){
            am=h.getSize()
            }
            aa=h[s]||am[s];
        A=A||am[M]
        }
        d+=H+(aa||0);
        al+=H+(h.flex?h[C]||0:aa);
        w+=H+(h[C]||aa||0);
        if(typeof A!="number"){
        A=h["get"+t]()
        }
        G=l(G,A+B[x.perpendicularLeftTop]+B[x.perpendicularRightBottom]);
        W[s]=aa||v;
        W[M]=A||v;
        Q.push(W)
        }
        X=al-c;
m=w>c;
e=l(0,c-d-u-(x.reserveOffset?x.availableSpaceOffset:0));
if(m){
    for(af=0;af<N;af++){
        j=Q[af];
        z=p[af][C]||p[af][s]||j[s];
        j.dirtySize=j.dirtySize||j[s]!=z;
        j[s]=z
        }
    }else{
    if(X>0){
        for(af=0;af<N;af++){
            I=p[af];
            z=I[C]||0;
            if(I.flex){
                j=Q[af];
                j.dirtySize=j.dirtySize||j[s]!=z;
                j[s]=z
                }else{
                L.push({
                    minSize:z,
                    available:Q[af][s]-z,
                    index:af
                })
                }
            }
        Ext.Array.sort(L,x.minSizeSortFn);
    for(af=0,y=L.length;af<y;af++){
        O=L[af].index;
        if(O==v){
            continue
        }
        I=p[O];
        z=L[af].minSize;
        j=Q[O];
        R=j[s];
        ac=l(z,R-J.ceil(X/(y-af)));
        f=R-ac;
        j.dirtySize=j.dirtySize||j[s]!=ac;
        j[s]=ac;
        X-=f
        }
    }else{
    a=e;
    q=ai;
    T=[];
    for(af=0;af<N;af++){
        h=p[af];
        if(ab&&h.flex){
            T.push(Q[Ext.Array.indexOf(p,h)])
            }
        }
    Ext.Array.sort(T,x.flexSortFn);
for(af=0;af<T.length;af++){
    S=T[af];
    h=S.component;
    B=S.margins;
    k=J.ceil((h.flex/q)*a);
    k=Math.max(h["min"+n]||0,J.min(h["max"+n]||o,k));
    a-=k;
    q-=h.flex;
    S.dirtySize=S.dirtySize||S[s]!=k;
    S[s]=k
    }
}
}
if(an){
    r+=e/2
    }else{
    if(E){
        r+=e
        }
    }
if(x.owner.dock&&(Ext.isIE6||Ext.isIE7||Ext.isIEQuirks)&&!x.owner.width&&x.direction=="vertical"){
    ag=G+x.owner.el.getPadding("lr")+x.owner.el.getBorderWidth("lr");
    if(x.owner.frameSize){
        ag+=x.owner.frameSize.left+x.owner.frameSize.right
        }
        ah=Math.min(ah,b.width=G+ae.left+ae.right)
    }
    for(af=0;af<N;af++){
    h=p[af];
    S=Q[af];
    B=S.margins;
    g=B[x.perpendicularLeftTop]+B[x.perpendicularRightBottom];
    r+=B[x.parallelBefore];
    S[x.parallelBefore]=r;
    S[x.perpendicularLeftTop]=P+B[x.perpendicularLeftTop];
    if(x.align=="stretch"){
        U=ad(ah-g,h[F]||0,h[ak]||o);
        S.dirtySize=S.dirtySize||S[M]!=U;
        S[M]=U
        }else{
        if(x.align=="stretchmax"){
            U=ad(G-g,h[F]||0,h[ak]||o);
            S.dirtySize=S.dirtySize||S[M]!=U;
            S[M]=U
            }else{
            if(x.align==x.alignCenteringString){
                D=l(ah,G)-aj-S[M];
                if(D>0){
                    S[x.perpendicularLeftTop]=P+Math.round(D/2)
                    }
                }
        }
}
r+=(S[s]||0)+B[x.parallelAfter]
}
return{
    boxes:Q,
    meta:{
        calculatedWidth:ag,
        maxSize:G,
        nonFlexSize:d,
        desiredSize:al,
        minimumSize:w,
        shortfall:X,
        tooNarrow:m
    }
}
},
onRemove:function(a){
    this.callParent(arguments);
    if(this.overflowHandler){
        this.overflowHandler.onRemove(a)
        }
    },
initOverflowHandler:function(){
    var c=this.overflowHandler;
    if(typeof c=="string"){
        c={
            type:c
        }
    }
    var b="None";
if(c&&c.type!==undefined){
    b=c.type
    }
    var a=Ext.layout.container.boxOverflow[b];
if(a[this.type]){
    a=a[this.type]
    }
    this.overflowHandler=Ext.create("Ext.layout.container.boxOverflow."+b,this,c)
},
onLayout:function(){
    this.callParent();
    if(this.clearInnerCtOnLayout===true&&this.adjustmentPass!==true){
        this.innerCt.setSize(null,null)
        }
        var g=this,c=g.getLayoutTargetSize(),f=g.getVisibleItems(),b=g.calculateChildBoxes(f,c),e=b.boxes,h=b.meta,i,a,d;
    if(g.autoSize&&b.meta.desiredSize){
        c[g.parallelPrefix]=b.meta.desiredSize
        }
        if(h.shortfall>0){
        i=g.overflowHandler;
        a=h.tooNarrow?"handleOverflow":"clearOverflow";
        d=i[a](b,c);
        if(d){
            if(d.targetSize){
                c=d.targetSize
                }
                if(d.recalculate){
                f=g.getVisibleItems();
                b=g.calculateChildBoxes(f,c);
                e=b.boxes
                }
            }
    }else{
    g.overflowHandler.clearOverflow()
    }
    g.layoutTargetLastSize=c;
g.childBoxCache=b;
g.updateInnerCtSize(c,b);
g.updateChildBoxes(e);
g.handleTargetOverflow(c)
},
updateChildBoxes:function(g){
    var l=this,e=0,c=g.length,o=[],n=Ext.dd.DDM.getDDById(l.innerCt.id),a,f,d,h,j,b;
    for(;e<c;e++){
        f=g[e];
        h=f.component;
        if(n&&(n.getDragEl()===h.el.dom)){
            continue
        }
        d=false;
        a=l.getChildBox(h);
        if(l.animate){
            b=l.animate.callback||l.animate;
            j={
                layoutAnimation:true,
                target:h,
                from:{},
                to:{},
                listeners:{}
        };
        
        if(!isNaN(f.width)&&(f.width!=a.width)){
            d=true;
            j.to.width=f.width
            }
            if(!isNaN(f.height)&&(f.height!=a.height)){
            d=true;
            j.to.height=f.height
            }
            if(!isNaN(f.left)&&(f.left!=a.left)){
            d=true;
            j.to.left=f.left
            }
            if(!isNaN(f.top)&&(f.top!=a.top)){
            d=true;
            j.to.top=f.top
            }
            if(d){
            o.push(j)
            }
        }else{
        if(f.dirtySize){
            if(f.width!==a.width||f.height!==a.height){
                l.setItemSize(h,f.width,f.height)
                }
            }
        if(isNaN(f.left)||isNaN(f.top)){
        continue
    }
    h.setPosition(f.left,f.top)
    }
}
c=o.length;
if(c){
    var m=function(i){
        c-=1;
        if(!c){
            l.layoutBusy=false;
            if(Ext.isFunction(b)){
                b()
                }
            }
    };

var k=function(){
    l.layoutBusy=true
    };
    
for(e=0,c=o.length;e<c;e++){
    j=o[e];
    j.listeners.afteranimate=m;
    if(!e){
        j.listeners.beforeanimate=k
        }
        if(l.animate.duration){
        j.duration=l.animate.duration
        }
        h=j.target;
    delete j.target;
    h.stopAnimation();
    h.animate(j)
    }
}
},
updateInnerCtSize:function(c,a){
    var g=this,e=Math.max,f=g.align,h=g.padding,b=c.width,j=c.height,k=a.meta,d,i;
    if(g.direction=="horizontal"){
        d=b;
        i=k.maxSize+h.top+h.bottom+g.innerCt.getBorderWidth("tb");
        if(f=="stretch"){
            i=j
            }else{
            if(f=="middle"){
                i=e(j,i)
                }
            }
    }else{
    i=j;
    d=k.maxSize+h.left+h.right+g.innerCt.getBorderWidth("lr");
    if(f=="stretch"){
        d=b
        }else{
        if(f=="center"){
            d=e(b,d)
            }
        }
}
g.getRenderTarget().setSize(d||undefined,i||undefined);
if(k.calculatedWidth&&g.owner.el.getWidth()>k.calculatedWidth){
    g.owner.el.setWidth(k.calculatedWidth)
    }
    if(g.innerCt.dom.scrollTop){
    g.innerCt.dom.scrollTop=0
    }
},
handleTargetOverflow:function(c){
    var b=this.getTarget(),d=b.getStyle("overflow"),a;
    if(d&&d!="hidden"&&!this.adjustmentPass){
        a=this.getLayoutTargetSize();
        if(a.width!=c.width||a.height!=c.height){
            this.adjustmentPass=true;
            this.onLayout();
            return true
            }
        }
    delete this.adjustmentPass
},
isValidParent:function(c,d,a){
    var b=c.el?c.el.dom:Ext.getDom(c);
    return(b&&this.innerCt&&b.parentNode===this.innerCt.dom)||false
    },
getRenderTarget:function(){
    if(!this.innerCt){
        this.innerCt=this.getTarget().createChild({
            cls:this.innerCls,
            role:"presentation"
        });
        this.padding=Ext.util.Format.parseBox(this.padding)
        }
        return this.innerCt
    },
renderItem:function(d,f){
    this.callParent(arguments);
    var c=this,a=d.getEl(),b=a.dom.style,e=d.margins||d.margin;
    if(e){
        if(Ext.isString(e)||Ext.isNumber(e)){
            e=Ext.util.Format.parseBox(e)
            }else{
            Ext.applyIf(e,{
                top:0,
                right:0,
                bottom:0,
                left:0
            })
            }
        }else{
    e=Ext.apply({},c.defaultMargins)
    }
    e.top+=a.getMargin("t");
e.right+=a.getMargin("r");
e.bottom+=a.getMargin("b");
e.left+=a.getMargin("l");
b.marginTop=b.marginRight=b.marginBottom=b.marginLeft="0";
d.margins=e
},
destroy:function(){
    Ext.destroy(this.overflowHandler);
    this.callParent(arguments)
    }
});
Ext.define("Ext.layout.container.HBox",{
    alias:["layout.hbox"],
    extend:"Ext.layout.container.Box",
    alternateClassName:"Ext.layout.HBoxLayout",
    align:"top",
    alignCenteringString:"middle",
    type:"hbox",
    direction:"horizontal",
    parallelSizeIndex:0,
    perpendicularSizeIndex:1,
    parallelPrefix:"width",
    parallelPrefixCap:"Width",
    parallelLT:"l",
    parallelRB:"r",
    parallelBefore:"left",
    parallelBeforeCap:"Left",
    parallelAfter:"right",
    parallelPosition:"x",
    perpendicularPrefix:"height",
    perpendicularPrefixCap:"Height",
    perpendicularLT:"t",
    perpendicularRB:"b",
    perpendicularLeftTop:"top",
    perpendicularRightBottom:"bottom",
    perpendicularPosition:"y",
    configureItem:function(a){
        if(a.flex){
            a.layoutManagedWidth=1
            }else{
            a.layoutManagedWidth=2
            }
            if(this.align==="stretch"||this.align==="stretchmax"){
            a.layoutManagedHeight=1
            }else{
            a.layoutManagedHeight=2
            }
            this.callParent(arguments)
        }
    });
Ext.define("Ext.layout.container.VBox",{
    alias:["layout.vbox"],
    extend:"Ext.layout.container.Box",
    alternateClassName:"Ext.layout.VBoxLayout",
    align:"left",
    alignCenteringString:"center",
    type:"vbox",
    direction:"vertical",
    parallelSizeIndex:1,
    perpendicularSizeIndex:0,
    parallelPrefix:"height",
    parallelPrefixCap:"Height",
    parallelLT:"t",
    parallelRB:"b",
    parallelBefore:"top",
    parallelBeforeCap:"Top",
    parallelAfter:"bottom",
    parallelPosition:"y",
    perpendicularPrefix:"width",
    perpendicularPrefixCap:"Width",
    perpendicularLT:"l",
    perpendicularRB:"r",
    perpendicularLeftTop:"left",
    perpendicularRightBottom:"right",
    perpendicularPosition:"x",
    configureItem:function(a){
        if(a.flex){
            a.layoutManagedHeight=1
            }else{
            a.layoutManagedHeight=2
            }
            if(this.align==="stretch"||this.align==="stretchmax"){
            a.layoutManagedWidth=1
            }else{
            a.layoutManagedWidth=2
            }
            this.callParent(arguments)
        }
    });
Ext.define("Ext.layout.container.Accordion",{
    extend:"Ext.layout.container.VBox",
    alias:["layout.accordion"],
    alternateClassName:"Ext.layout.AccordionLayout",
    align:"stretch",
    fill:true,
    autoWidth:true,
    titleCollapse:true,
    hideCollapseTool:false,
    collapseFirst:false,
    animate:true,
    activeOnTop:false,
    multi:false,
    constructor:function(){
        var a=this;
        a.callParent(arguments);
        a.initialAnimate=a.animate;
        a.animate=false;
        if(a.fill===false){
            a.itemCls=Ext.baseCSSPrefix+"accordion-item"
            }
        },
beforeLayout:function(){
    var a=this;
    a.callParent(arguments);
    if(a.fill){
        if(!a.owner.el.dom.style.height||!a.getLayoutTargetSize().height){
            return false
            }
        }else{
    a.owner.componentLayout.monitorChildren=false;
    a.autoSize=true;
    a.owner.setAutoScroll(true)
    }
},
renderItems:function(g,e){
    var h=this,f=g.length,c=0,d,b=h.getLayoutTargetSize(),j=[],a;
    for(;c<f;c++){
        d=g[c];
        if(!d.rendered){
            j.push(d);
            if(h.collapseFirst){
                d.collapseFirst=h.collapseFirst
                }
                if(h.hideCollapseTool){
                d.hideCollapseTool=h.hideCollapseTool;
                d.titleCollapse=true
                }else{
                if(h.titleCollapse){
                    d.titleCollapse=h.titleCollapse
                    }
                }
            delete d.hideHeader;
        d.collapsible=true;
        d.title=d.title||"&#160;";
        d.width=b.width;
        if(h.fill){
            delete d.height;
            delete d.flex;
            if(h.expandedItem!==undefined){
                d.collapsed=true
                }else{
                if(d.hasOwnProperty("collapsed")&&d.collapsed===false){
                    d.flex=1;
                    h.expandedItem=c
                    }else{
                    d.collapsed=true
                    }
                }
            h.owner.mon(d,{
            show:h.onComponentShow,
            beforeexpand:h.onComponentExpand,
            beforecollapse:h.onComponentCollapse,
            scope:h
        })
        }else{
        delete d.flex;
        d.animCollapse=h.initialAnimate;
        d.autoHeight=true;
        d.autoScroll=false
        }
    }
}
if(f&&h.expandedItem===undefined){
    h.expandedItem=0;
    d=g[0];
    d.collapsed=false;
    if(h.fill){
        d.flex=1
        }
    }
h.callParent(arguments);
f=j.length;
for(c=0;c<f;c++){
    d=j[c];
    delete d.width;
    d.header.addCls(Ext.baseCSSPrefix+"accordion-hd");
    d.body.addCls(Ext.baseCSSPrefix+"accordion-body")
    }
},
onLayout:function(){
    var f=this;
    if(f.fill){
        f.callParent(arguments)
        }else{
        var e=f.getLayoutTargetSize(),c=f.getVisibleItems(),a=c.length,d=0,b;
        for(;d<a;d++){
            b=c[d];
            if(b.collapsed){
                c[d].setWidth(e.width)
                }else{
                c[d].setSize(null,null)
                }
            }
        }
    f.updatePanelClasses();
return f
},
updatePanelClasses:function(){
    var c=this.getLayoutItems(),d=c.length,a=true,b,e;
    for(b=0;b<d;b++){
        e=c[b];
        if(a){
            e.header.removeCls(Ext.baseCSSPrefix+"accordion-hd-sibling-expanded")
            }else{
            e.header.addCls(Ext.baseCSSPrefix+"accordion-hd-sibling-expanded")
            }
            if(b+1==d&&e.collapsed){
            e.header.addCls(Ext.baseCSSPrefix+"accordion-hd-last-collapsed")
            }else{
            e.header.removeCls(Ext.baseCSSPrefix+"accordion-hd-last-collapsed")
            }
            a=e.collapsed
        }
    },
onComponentExpand:function(f){
    var e=this,d=e.owner.items.items,a=d.length,c=0,b;
    for(;c<a;c++){
        b=d[c];
        if(b===f&&b.collapsed){
            e.setExpanded(b)
            }else{
            if(!e.multi&&(b.rendered&&b.header.rendered&&b!==f&&!b.collapsed)){
                e.setCollapsed(b)
                }
            }
    }
    e.animate=e.initialAnimate;
e.layout();
e.animate=false;
return false
},
onComponentCollapse:function(b){
    var c=this,d=b.next()||b.prev(),a=c.multi?c.owner.query(">panel:not([collapsed])"):[];
    if(c.multi){
        c.setCollapsed(b);
        if(a.length===1&&a[0]===b){
            c.setExpanded(d)
            }
            c.animate=c.initialAnimate;
        c.layout();
        c.animate=false
        }else{
        if(d){
            c.onComponentExpand(d)
            }
        }
    return false
},
onComponentShow:function(a){
    this.onComponentExpand(a)
    },
setCollapsed:function(b){
    var e=b.getDockedItems(),d,a=e.length,c=0;
    b.hiddenDocked=[];
    for(;c<a;c++){
        d=e[c];
        if((d!==b.header)&&!d.hidden){
            d.hidden=true;
            b.hiddenDocked.push(d)
            }
        }
    b.addCls(b.collapsedCls);
b.header.addCls(b.collapsedHeaderCls);
b.height=b.header.getHeight();
b.el.setHeight(b.height);
b.collapsed=true;
delete b.flex;
b.fireEvent("collapse",b);
if(b.collapseTool){
    b.collapseTool.setType("expand-"+b.getOppositeDirection(b.collapseDirection))
    }
},
setExpanded:function(b){
    var d=b.hiddenDocked,a=d?d.length:0,c=0;
    for(;c<a;c++){
        d[c].show()
        }
        if(!b.body.isVisible()){
        b.body.show()
        }
        delete b.collapsed;
    delete b.height;
    delete b.componentLayout.lastComponentSize;
    b.suspendLayout=false;
    b.flex=1;
    b.removeCls(b.collapsedCls);
    b.header.removeCls(b.collapsedHeaderCls);
    b.fireEvent("expand",b);
    if(b.collapseTool){
        b.collapseTool.setType("collapse-"+b.collapseDirection)
        }
        b.setAutoScroll(b.initialConfig.autoScroll)
    }
});
Ext.define("Ext.panel.Header",{
    extend:"Ext.container.Container",
    uses:["Ext.panel.Tool","Ext.draw.Component","Ext.util.CSS"],
    alias:"widget.header",
    isHeader:true,
    defaultType:"tool",
    indicateDrag:false,
    weight:-1,
    renderTpl:['<div class="{baseCls}-body<tpl if="bodyCls"> {bodyCls}</tpl><tpl if="uiCls"><tpl for="uiCls"> {parent.baseCls}-body-{parent.ui}-{.}</tpl></tpl>"<tpl if="bodyStyle"> style="{bodyStyle}"</tpl>></div>'],
    initComponent:function(){
        var c=this,e,b,a,d;
        c.indicateDragCls=c.baseCls+"-draggable";
        c.title=c.title||"&#160;";
        c.tools=c.tools||[];
        c.items=c.items||[];
        c.orientation=c.orientation||"horizontal";
        c.dock=(c.dock)?c.dock:(c.orientation=="horizontal")?"top":"left";
        c.addClsWithUI(c.orientation);
        c.addClsWithUI(c.dock);
        Ext.applyIf(c.renderSelectors,{
            body:"."+c.baseCls+"-body"
            });
        if(!Ext.isEmpty(c.iconCls)){
            c.initIconCmp();
            c.items.push(c.iconCmp)
            }
            if(c.orientation=="vertical"){
            if(Ext.isIE6||Ext.isIE7){
                c.width=this.width||24
                }else{
                if(Ext.isIEQuirks){
                    c.width=this.width||25
                    }
                }
            c.layout={
            type:"vbox",
            align:"center",
            clearInnerCtOnLayout:true,
            bindToOwnerCtContainer:false
        };
        
        c.textConfig={
            cls:c.baseCls+"-text",
            type:"text",
            text:c.title,
            rotate:{
                degrees:90
            }
        };
        
    d=c.ui;
    if(Ext.isArray(d)){
        d=d[0]
        }
        e=Ext.util.CSS.getRule("."+c.baseCls+"-text-"+d);
    if(e){
        b=e.style
        }
        if(b){
        Ext.apply(c.textConfig,{
            "font-family":b.fontFamily,
            "font-weight":b.fontWeight,
            "font-size":b.fontSize,
            fill:b.color
            })
        }
        c.titleCmp=Ext.create("Ext.draw.Component",{
        ariaRole:"heading",
        focusable:false,
        viewBox:false,
        flex:1,
        autoSize:true,
        margins:"5 0 0 0",
        items:[c.textConfig],
        renderSelectors:{
            textEl:"."+c.baseCls+"-text"
            }
        })
}else{
    c.layout={
        type:"hbox",
        align:"middle",
        clearInnerCtOnLayout:true,
        bindToOwnerCtContainer:false
    };
    
    c.titleCmp=Ext.create("Ext.Component",{
        xtype:"component",
        ariaRole:"heading",
        focusable:false,
        flex:1,
        renderTpl:['<span class="{cls}-text {cls}-text-{ui}">{title}</span>'],
        renderData:{
            title:c.title,
            cls:c.baseCls,
            ui:c.ui
            },
        renderSelectors:{
            textEl:"."+c.baseCls+"-text"
            }
        })
}
c.items.push(c.titleCmp);
c.items=c.items.concat(c.tools);
this.callParent()
},
initIconCmp:function(){
    this.iconCmp=Ext.create("Ext.Component",{
        focusable:false,
        renderTpl:['<img alt="" src="{blank}" class="{cls}-icon {iconCls}"/>'],
        renderData:{
            blank:Ext.BLANK_IMAGE_URL,
            cls:this.baseCls,
            iconCls:this.iconCls,
            orientation:this.orientation
            },
        renderSelectors:{
            iconEl:"."+this.baseCls+"-icon"
            },
        iconCls:this.iconCls
        })
    },
afterRender:function(){
    var a=this;
    a.el.unselectable();
    if(a.indicateDrag){
        a.el.addCls(a.indicateDragCls)
        }
        a.mon(a.el,{
        click:a.onClick,
        scope:a
    });
    a.callParent()
    },
afterLayout:function(){
    var a=this;
    a.callParent(arguments);
    if(Ext.isIE7){
        a.el.repaint()
        }
    },
addUIClsToElement:function(b,f){
    var e=this,a=e.callParent(arguments),d=[e.baseCls+"-body-"+b,e.baseCls+"-body-"+e.ui+"-"+b],g,c;
    if(!f&&e.rendered){
        if(e.bodyCls){
            e.body.addCls(e.bodyCls)
            }else{
            e.body.addCls(d)
            }
        }else{
    if(e.bodyCls){
        g=e.bodyCls.split(" ");
        for(c=0;c<d.length;c++){
            if(!Ext.Array.contains(g,d[c])){
                g.push(d[c])
                }
            }
        e.bodyCls=g.join(" ")
    }else{
    e.bodyCls=d.join(" ")
    }
}
return a
},
removeUIClsFromElement:function(b,f){
    var e=this,a=e.callParent(arguments),d=[e.baseCls+"-body-"+b,e.baseCls+"-body-"+e.ui+"-"+b],g,c;
    if(!f&&e.rendered){
        if(e.bodyCls){
            e.body.removeCls(e.bodyCls)
            }else{
            e.body.removeCls(d)
            }
        }else{
    if(e.bodyCls){
        g=e.bodyCls.split(" ");
        for(c=0;c<d.length;c++){
            Ext.Array.remove(g,d[c])
            }
            e.bodyCls=g.join(" ")
        }
    }
return a
},
addUIToElement:function(c){
    var b=this,d,a;
    b.callParent(arguments);
    a=b.baseCls+"-body-"+b.ui;
    if(!c&&b.rendered){
        if(b.bodyCls){
            b.body.addCls(b.bodyCls)
            }else{
            b.body.addCls(a)
            }
        }else{
    if(b.bodyCls){
        d=b.bodyCls.split(" ");
        if(!Ext.Array.contains(d,a)){
            d.push(a)
            }
            b.bodyCls=d.join(" ")
        }else{
        b.bodyCls=a
        }
    }
if(!c&&b.titleCmp&&b.titleCmp.rendered&&b.titleCmp.textEl){
    b.titleCmp.textEl.addCls(b.baseCls+"-text-"+b.ui)
    }
},
removeUIFromElement:function(){
    var b=this,c,a;
    b.callParent(arguments);
    a=b.baseCls+"-body-"+b.ui;
    if(b.rendered){
        if(b.bodyCls){
            b.body.removeCls(b.bodyCls)
            }else{
            b.body.removeCls(a)
            }
        }else{
    if(b.bodyCls){
        c=b.bodyCls.split(" ");
        Ext.Array.remove(c,a);
        b.bodyCls=c.join(" ")
        }else{
        b.bodyCls=a
        }
    }
if(b.titleCmp&&b.titleCmp.rendered&&b.titleCmp.textEl){
    b.titleCmp.textEl.removeCls(b.baseCls+"-text-"+b.ui)
    }
},
onClick:function(a){
    if(!a.getTarget(Ext.baseCSSPrefix+"tool")){
        this.fireEvent("click",a)
        }
    },
getTargetEl:function(){
    return this.body||this.frameBody||this.el
    },
setTitle:function(d){
    var c=this;
    if(c.rendered){
        if(c.titleCmp.rendered){
            if(c.titleCmp.surface){
                c.title=d||"";
                var b=c.titleCmp.surface.items.items[0],a=c.titleCmp.surface;
                a.remove(b);
                c.textConfig.type="text";
                c.textConfig.text=d;
                b=a.add(c.textConfig);
                b.setAttributes({
                    rotate:{
                        degrees:90
                    }
                },true);
            c.titleCmp.autoSizeSurface()
            }else{
            c.title=d||"&#160;";
            c.titleCmp.textEl.update(c.title)
            }
        }else{
    c.titleCmp.on({
        render:function(){
            c.setTitle(d)
            },
        single:true
    })
    }
}else{
    c.on({
        render:function(){
            c.layout.layout();
            c.setTitle(d)
            },
        single:true
    })
    }
},
setIconCls:function(a){
    this.iconCls=a;
    if(!this.iconCmp){
        this.initIconCmp();
        this.insert(0,this.iconCmp)
        }else{
        if(!a||!a.length){
            this.iconCmp.destroy()
            }else{
            var c=this.iconCmp,b=c.iconEl;
            b.removeCls(c.iconCls);
            b.addCls(a);
            c.iconCls=a
            }
        }
},
addTool:function(a){
    this.tools.push(this.add(a))
    },
onAdd:function(b,a){
    this.callParent([arguments]);
    if(b instanceof Ext.panel.Tool){
        b.bindTo(this.ownerCt);
        this.tools[b.type]=b
        }
    }
});
Ext.define("Ext.toolbar.Fill",{
    extend:"Ext.Component",
    alias:"widget.tbfill",
    alternateClassName:"Ext.Toolbar.Fill",
    isFill:true,
    flex:1
});
Ext.define("Ext.FocusManager",{
    singleton:true,
    alternateClassName:"Ext.FocusMgr",
    mixins:{
        observable:"Ext.util.Observable"
    },
    requires:["Ext.ComponentManager","Ext.ComponentQuery","Ext.util.HashMap","Ext.util.KeyNav"],
    enabled:false,
    focusElementCls:Ext.baseCSSPrefix+"focus-element",
    focusFrameCls:Ext.baseCSSPrefix+"focus-frame",
    whitelist:["textfield"],
    tabIndexWhitelist:["a","button","embed","frame","iframe","img","input","object","select","textarea"],
    constructor:function(){
        var a=this,b=Ext.ComponentQuery;
        a.addEvents("beforecomponentfocus","componentfocus","disable","enable");
        a.keyNav=Ext.create("Ext.util.KeyNav",Ext.getDoc(),{
            disabled:true,
            scope:a,
            backspace:a.focusLast,
            enter:a.navigateIn,
            esc:a.navigateOut,
            tab:a.navigateSiblings
            });
        a.focusData={};
        
        a.subscribers=Ext.create("Ext.util.HashMap");
        a.focusChain={};
        
        Ext.apply(b.pseudos,{
            focusable:function(f){
                var d=f.length,h=[],g=0,j,e=function(c){
                    return c&&c.focusable!==false&&b.is(c,"[rendered]:not([destroying]):not([isDestroyed]):not([disabled]){isVisible(true)}{el && c.el.dom && c.el.isVisible()}")
                    };
                    
                for(;g<d;g++){
                    j=f[g];
                    if(e(j)){
                        h.push(j)
                        }
                    }
                return h
            },
        nextFocus:function(f,e,h){
            h=h||1;
            e=parseInt(e,10);
            var d=f.length,g=e+h,j;
            for(;g!=e;g+=h){
                if(g>=d){
                    g=0
                    }else{
                    if(g<0){
                        g=d-1
                        }
                    }
                j=f[g];
            if(b.is(j,":focusable")){
                return[j]
                }else{
                if(j.placeholder&&b.is(j.placeholder,":focusable")){
                    return[j.placeholder]
                    }
                }
            }
        return[]
    },
prevFocus:function(d,c){
    return this.nextFocus(d,c,-1)
    },
root:function(e){
    var d=e.length,g=[],f=0,h;
    for(;f<d;f++){
        h=e[f];
        if(!h.ownerCt){
            g.push(h)
            }
        }
    return g
}
})
},
addXTypeToWhitelist:function(b){
    var a=this;
    if(Ext.isArray(b)){
        Ext.Array.forEach(b,a.addXTypeToWhitelist,a);
        return
    }
    if(!Ext.Array.contains(a.whitelist,b)){
        a.whitelist.push(b)
        }
    },
clearComponent:function(a){
    clearTimeout(this.cmpFocusDelay);
    if(!a.isDestroyed){
        a.blur()
        }
    },
disable:function(){
    var a=this;
    if(!a.enabled){
        return
    }
    delete a.options;
    a.enabled=false;
    Ext.ComponentManager.all.un("add",a.onComponentCreated,a);
    a.removeDOM();
    a.keyNav.disable();
    a.setFocusAll(false);
    a.fireEvent("disable",a)
    },
enable:function(a){
    var b=this;
    if(a===true){
        a={
            focusFrame:true
        }
    }
    b.options=a=a||{};

if(b.enabled){
    return
}
Ext.ComponentManager.all.on("add",b.onComponentCreated,b);
b.initDOM(a);
b.keyNav.enable();
b.setFocusAll(true,a);
b.focusEl.focus();
delete b.focusedCmp;
b.enabled=true;
b.fireEvent("enable",b)
},
focusLast:function(b){
    var a=this;
    if(a.isWhitelisted(a.focusedCmp)){
        return true
        }
        if(a.previousFocusedCmp){
        a.previousFocusedCmp.focus()
        }
    },
getRootComponents:function(){
    var a=this,c=Ext.ComponentQuery,b=c.query(":focusable:root:not([floating])"),d=c.query(":focusable:root[floating]");
    d.sort(function(f,e){
        return f.el.getZIndex()>e.el.getZIndex()
        });
    return d.concat(b)
    },
initDOM:function(b){
    var c=this,d="&#160",a=c.focusFrameCls;
    if(!Ext.isReady){
        Ext.onReady(c.initDOM,c);
        return
    }
    if(!c.focusEl){
        c.focusEl=Ext.getBody().createChild({
            tabIndex:"-1",
            cls:c.focusElementCls,
            html:d
        })
        }
        if(!c.focusFrame&&b.focusFrame){
        c.focusFrame=Ext.getBody().createChild({
            cls:a,
            children:[{
                cls:a+"-top"
                },{
                cls:a+"-bottom"
                },{
                cls:a+"-left"
                },{
                cls:a+"-right"
                }],
            style:"top: -100px; left: -100px;"
        });
        c.focusFrame.setVisibilityMode(Ext.core.Element.DISPLAY);
        c.focusFrameWidth=c.focusFrame.child("."+a+"-top").getHeight();
        c.focusFrame.hide().setLeftTop(0,0)
        }
    },
isWhitelisted:function(a){
    return a&&Ext.Array.some(this.whitelist,function(b){
        return a.isXType(b)
        })
    },
navigateIn:function(d){
    var b=this,a=b.focusedCmp,f,c;
    if(!a){
        f=b.getRootComponents();
        if(f.length){
            f[0].focus()
            }
        }else{
    c=Ext.ComponentQuery.query(">:focusable",a)[0];
    if(c){
        c.focus()
        }else{
        if(Ext.isFunction(a.onClick)){
            d.button=0;
            a.onClick(d);
            a.focus()
            }
        }
}
},
navigateOut:function(c){
    var b=this,a;
    if(!b.focusedCmp||!(a=b.focusedCmp.up(":focusable"))){
        b.focusEl.focus()
        }else{
        a.focus()
        }
        return true
    },
navigateSiblings:function(h,b,m){
    var i=this,a=b||i,n=h.getKey(),f=Ext.EventObject,j=h.shiftKey||n==f.LEFT||n==f.UP,c=n==f.LEFT||n==f.RIGHT||n==f.UP||n==f.DOWN,g=j?"prev":"next",l,d,k;
    k=(a.focusedCmp&&a.focusedCmp.comp)||a.focusedCmp;
    if(!k&&!m){
        return
    }
    if(c&&i.isWhitelisted(k)){
        return true
        }
        m=m||k.up();
    if(m){
        l=k?Ext.Array.indexOf(m.getRefItems(),k):-1;
        d=Ext.ComponentQuery.query(">:"+g+"Focus("+l+")",m)[0];
        if(d&&k!==d){
            d.focus();
            return d
            }
        }
},
onComponentBlur:function(b,c){
    var a=this;
    if(a.focusedCmp===b){
        a.previousFocusedCmp=b;
        delete a.focusedCmp
        }
        if(a.focusFrame){
        a.focusFrame.hide()
        }
    },
onComponentCreated:function(b,c,a){
    this.setFocus(a,true,this.options)
    },
onComponentDestroy:function(a){
    this.setFocus(a,false)
    },
onComponentFocus:function(n,k){
    var l=this,a=l.focusChain;
    if(!Ext.ComponentQuery.is(n,":focusable")){
        l.clearComponent(n);
        if(a[n.id]){
            return
        }
        var p=n.up();
        if(p){
            a[n.id]=true;
            p.focus()
            }
            return
    }
    l.focusChain={};
    
    clearTimeout(l.cmpFocusDelay);
    if(arguments.length!==2){
        l.cmpFocusDelay=Ext.defer(l.onComponentFocus,90,l,[n,k]);
        return
    }
    if(l.fireEvent("beforecomponentfocus",l,n,l.previousFocusedCmp)===false){
        l.clearComponent(n);
        return
    }
    l.focusedCmp=n;
    if(l.shouldShowFocusFrame(n)){
        var r="."+l.focusFrameCls+"-",b=l.focusFrame,f=l.focusFrameWidth,i=n.el.getPageBox(),q=i.top,c=i.left,m=i.width,g=i.height,h=b.child(r+"top"),d=b.child(r+"bottom"),o=b.child(r+"left"),j=b.child(r+"right");
        h.setWidth(m-2).setLeftTop(c+1,q);
        d.setWidth(m-2).setLeftTop(c+1,q+g-f);
        o.setHeight(g-2).setLeftTop(c,q+1);
        j.setHeight(g-2).setLeftTop(c+m-f,q+1);
        b.show()
        }
        l.fireEvent("componentfocus",l,n,l.previousFocusedCmp)
    },
onComponentHide:function(e){
    var d=this,f=Ext.ComponentQuery,b=false,a,c;
    if(d.focusedCmp){
        a=f.query("[id="+d.focusedCmp.id+"]",e)[0];
        b=d.focusedCmp.id===e.id||a;
        if(a){
            d.clearComponent(a)
            }
        }
    d.clearComponent(e);
if(b){
    c=f.query("^:focusable",e)[0];
    if(c){
        c.focus()
        }
    }
},
removeDOM:function(){
    var a=this;
    if(a.enabled||a.subscribers.length){
        return
    }
    Ext.destroy(a.focusEl,a.focusFrame);
    delete a.focusEl;
    delete a.focusFrame;
    delete a.focusFrameWidth
    },
removeXTypeFromWhitelist:function(b){
    var a=this;
    if(Ext.isArray(b)){
        Ext.Array.forEach(b,a.removeXTypeFromWhitelist,a);
        return
    }
    Ext.Array.remove(a.whitelist,b)
    },
setFocus:function(e,h,b){
    var d=this,c,g,f,a=function(i){
        return !Ext.Array.contains(d.tabIndexWhitelist,i.tagName.toLowerCase())&&i.tabIndex<=0
        };
        
    b=b||{};
    
    if(!e.rendered){
        e.on("afterrender",Ext.pass(d.setFocus,arguments,d),d,{
            single:true
        });
        return
    }
    c=e.getFocusEl();
    g=c.dom;
    if((h&&!d.focusData[e.id])||(!h&&d.focusData[e.id])){
        if(h){
            f={
                focusFrame:b.focusFrame
                };
                
            if(a(g)){
                f.tabIndex=g.tabIndex;
                g.tabIndex=-1
                }
                c.on({
                focus:f.focusFn=Ext.bind(d.onComponentFocus,d,[e],0),
                blur:f.blurFn=Ext.bind(d.onComponentBlur,d,[e],0),
                scope:d
            });
            e.on({
                hide:d.onComponentHide,
                close:d.onComponentHide,
                beforedestroy:d.onComponentDestroy,
                scope:d
            });
            d.focusData[e.id]=f
            }else{
            f=d.focusData[e.id];
            if("tabIndex" in f){
                g.tabIndex=f.tabIndex
                }
                c.un("focus",f.focusFn,d);
            c.un("blur",f.blurFn,d);
            e.un("hide",d.onComponentHide,d);
            e.un("close",d.onComponentHide,d);
            e.un("beforedestroy",d.onComponentDestroy,d);
            delete d.focusData[e.id]
        }
    }
},
setFocusAll:function(g,c){
    var f=this,b=Ext.ComponentManager.all.getArray(),a=b.length,e,d=0;
    for(;d<a;d++){
        f.setFocus(b[d],g,c)
        }
    },
setupSubscriberKeys:function(a,f){
    var e=this,d=a.getFocusEl(),c=f.scope,b={
        backspace:e.focusLast,
        enter:e.navigateIn,
        esc:e.navigateOut,
        scope:e
    },g=function(h){
        if(e.focusedCmp===a){
            return e.navigateSiblings(h,e,a)
            }else{
            return e.navigateSiblings(h)
            }
        };
    
Ext.iterate(f,function(i,h){
    b[i]=function(k){
        var j=g(k);
        if(Ext.isFunction(h)&&h.call(c||a,k,j)===true){
            return true
            }
            return j
        }
    },e);
return Ext.create("Ext.util.KeyNav",d,b)
},
shouldShowFocusFrame:function(c){
    var b=this,a=b.options||{};
    
    if(!b.focusFrame||!c){
        return false
        }
        if(a.focusFrame){
        return true
        }
        if(b.focusData[c.id].focusFrame){
        return true
        }
        return false
    },
subscribe:function(a,c){
    var f=this,e=Ext.Array,g={},d=f.subscribers,b=function(h){
        if(h.isContainer&&!d.containsKey(h.id)){
            e.forEach(h.query(">"),b);
            f.setFocus(h,true,c);
            h.on("add",g.onAdd,f)
            }else{
            if(!h.isContainer){
                f.setFocus(h,true,c)
                }
            }
    };

if(!a||!a.isContainer){
    return
}
if(!a.rendered){
    a.on("afterrender",Ext.pass(f.subscribe,arguments,f),f,{
        single:true
    });
    return
}
f.initDOM(c);
g.keyNav=f.setupSubscriberKeys(a,c.keys);
g.onAdd=function(i,j,h){
    b(j)
    };
    
a.on("beforedestroy",f.unsubscribe,f);
b(a);
d.add(a.id,g)
},
unsubscribe:function(a){
    var e=this,d=Ext.Array,c=e.subscribers,f,b=function(g){
        if(g.isContainer&&!c.containsKey(g.id)){
            d.forEach(g.query(">"),b);
            e.setFocus(g,false);
            g.un("add",f.onAdd,e)
            }else{
            if(!g.isContainer){
                e.setFocus(g,false)
                }
            }
    };

if(!a||!c.containsKey(a.id)){
    return
}
f=c.get(a.id);
f.keyNav.destroy();
a.un("beforedestroy",e.unsubscribe,e);
c.removeAtKey(a.id);
b(a);
e.removeDOM()
}
});
Ext.define("Ext.toolbar.Toolbar",{
    extend:"Ext.container.Container",
    requires:["Ext.toolbar.Fill","Ext.layout.container.HBox","Ext.layout.container.VBox","Ext.FocusManager"],
    uses:["Ext.toolbar.Separator"],
    alias:"widget.toolbar",
    alternateClassName:"Ext.Toolbar",
    isToolbar:true,
    baseCls:Ext.baseCSSPrefix+"toolbar",
    ariaRole:"toolbar",
    defaultType:"button",
    vertical:false,
    enableOverflow:false,
    trackMenus:true,
    itemCls:Ext.baseCSSPrefix+"toolbar-item",
    initComponent:function(){
        var b=this,a;
        if(!b.layout&&b.enableOverflow){
            b.layout={
                overflowHandler:"Menu"
            }
        }
        if(b.dock==="right"||b.dock==="left"){
        b.vertical=true
        }
        b.layout=Ext.applyIf(Ext.isString(b.layout)?{
        type:b.layout
        }:b.layout||{},{
        type:b.vertical?"vbox":"hbox",
        align:b.vertical?"stretchmax":"middle",
        clearInnerCtOnLayout:true
    });
    if(b.vertical){
        b.addClsWithUI("vertical")
        }
        if(b.ui==="footer"){
        b.ignoreBorderManagement=true
        }
        b.callParent();
    b.addEvents("overflowchange");
    a=b.vertical?["up","down"]:["left","right"];
    Ext.FocusManager.subscribe(b,{
        keys:a
    })
    },
lookupComponent:function(b){
    if(Ext.isString(b)){
        var a=Ext.toolbar.Toolbar.shortcuts[b];
        if(a){
            b={
                xtype:a
            }
        }else{
        b={
            xtype:"tbtext",
            text:b
        }
    }
    this.applyDefaults(b)
    }
    return this.callParent(arguments)
    },
applyDefaults:function(b){
    if(!Ext.isString(b)){
        b=this.callParent(arguments);
        var a=this.internalDefaults;
        if(b.events){
            Ext.applyIf(b.initialConfig,a);
            Ext.apply(b,a)
            }else{
            Ext.applyIf(b,a)
            }
        }
    return b
},
trackMenu:function(c,a){
    if(this.trackMenus&&c.menu){
        var d=a?"mun":"mon",b=this;
        b[d](c,"menutriggerover",b.onButtonTriggerOver,b);
        b[d](c,"menushow",b.onButtonMenuShow,b);
        b[d](c,"menuhide",b.onButtonMenuHide,b)
        }
    },
constructButton:function(a){
    return a.events?a:this.createComponent(a,a.split?"splitbutton":this.defaultType)
    },
onBeforeAdd:function(a){
    if(a.is("field")||(a.is("button")&&this.ui!="footer")){
        a.ui=a.ui+"-toolbar"
        }
        if(a instanceof Ext.toolbar.Separator){
        a.setUI((this.vertical)?"vertical":"horizontal")
        }
        this.callParent(arguments)
    },
onAdd:function(a){
    this.callParent(arguments);
    this.trackMenu(a);
    if(this.disabled){
        a.disable()
        }
    },
onRemove:function(a){
    this.callParent(arguments);
    this.trackMenu(a,true)
    },
onButtonTriggerOver:function(a){
    if(this.activeMenuBtn&&this.activeMenuBtn!=a){
        this.activeMenuBtn.hideMenu();
        a.showMenu();
        this.activeMenuBtn=a
        }
    },
onButtonMenuShow:function(a){
    this.activeMenuBtn=a
    },
onButtonMenuHide:function(a){
    delete this.activeMenuBtn
    }
},function(){
    this.shortcuts={
        "-":"tbseparator",
        " ":"tbspacer",
        "->":"tbfill"
    }
});
Ext.define("Ext.panel.AbstractPanel",{
    extend:"Ext.container.Container",
    requires:["Ext.util.MixedCollection","Ext.core.Element","Ext.toolbar.Toolbar"],
    baseCls:Ext.baseCSSPrefix+"panel",
    isPanel:true,
    componentLayout:"dock",
    defaultDockWeights:{
        top:1,
        left:3,
        right:5,
        bottom:7
    },
    renderTpl:['<div class="{baseCls}-body<tpl if="bodyCls"> {bodyCls}</tpl> {baseCls}-body-{ui}<tpl if="uiCls"><tpl for="uiCls"> {parent.baseCls}-body-{parent.ui}-{.}</tpl></tpl>"<tpl if="bodyStyle"> style="{bodyStyle}"</tpl>></div>'],
    border:true,
    initComponent:function(){
        var a=this;
        a.addEvents("bodyresize");
        Ext.applyIf(a.renderSelectors,{
            body:"."+a.baseCls+"-body"
            });
        if(a.frame&&a.border&&a.bodyBorder===undefined){
            a.bodyBorder=false
            }
            if(a.frame&&a.border&&(a.bodyBorder===false||a.bodyBorder===0)){
            a.manageBodyBorders=true
            }
            a.callParent()
        },
    initItems:function(){
        var b=this,a=b.dockedItems;
        b.callParent();
        b.dockedItems=Ext.create("Ext.util.MixedCollection",false,b.getComponentId);
        if(a){
            b.addDocked(a)
            }
        },
getDockedComponent:function(a){
    if(Ext.isObject(a)){
        a=a.getItemId()
        }
        return this.dockedItems.get(a)
    },
getComponent:function(a){
    var b=this.callParent(arguments);
    if(b===undefined&&!Ext.isNumber(a)){
        b=this.getDockedComponent(a)
        }
        return b
    },
initBodyStyles:function(){
    var d=this,a=d.bodyStyle,c=[],b=Ext.core.Element,e;
    if(Ext.isFunction(a)){
        a=a()
        }
        if(Ext.isString(a)){
        c=a.split(";")
        }else{
        for(e in a){
            if(a.hasOwnProperty(e)){
                c.push(e+":"+a[e])
                }
            }
        }
        if(d.bodyPadding!==undefined){
    c.push("padding: "+b.unitizeBox((d.bodyPadding===true)?5:d.bodyPadding))
    }
    if(d.frame&&d.bodyBorder){
    if(!Ext.isNumber(d.bodyBorder)){
        d.bodyBorder=1
        }
        c.push("border-width: "+b.unitizeBox(d.bodyBorder))
    }
    delete d.bodyStyle;
return c.length?c.join(";"):undefined
    },
initBodyCls:function(){
    var b=this,a="",c=b.bodyCls;
    if(c){
        Ext.each(c,function(d){
            a+=" "+d
            });
        delete b.bodyCls
        }
        return a.length>0?a:undefined
    },
initRenderData:function(){
    return Ext.applyIf(this.callParent(),{
        bodyStyle:this.initBodyStyles(),
        bodyCls:this.initBodyCls()
        })
    },
addDocked:function(a,f){
    var e=this,b=0,d,c;
    a=e.prepareItems(a);
    c=a.length;
    for(;b<c;b++){
        d=a[b];
        d.dock=d.dock||"top";
        if(e.border===false){}
        if(f!==undefined){
            e.dockedItems.insert(f+b,d)
            }else{
            e.dockedItems.add(d)
            }
            d.onAdded(e,b);
        e.onDockedAdd(d)
        }
        e.componentLayout.childrenChanged=true;
    if(e.rendered&&!e.suspendLayout){
        e.doComponentLayout()
        }
        return a
    },
onDockedAdd:Ext.emptyFn,
onDockedRemove:Ext.emptyFn,
insertDocked:function(b,a){
    this.addDocked(a,b)
    },
removeDocked:function(e,b){
    var d=this,c,a;
    if(!d.dockedItems.contains(e)){
        return e
        }
        c=d.componentLayout;
    a=c&&d.rendered;
    if(a){
        c.onRemove(e)
        }
        d.dockedItems.remove(e);
    e.onRemoved();
    d.onDockedRemove(e);
    if(b===true||(b!==false&&d.autoDestroy)){
        e.destroy()
        }
        if(a&&!b){
        c.afterRemove(e)
        }
        d.componentLayout.childrenChanged=true;
    if(!d.destroying&&!d.suspendLayout){
        d.doComponentLayout()
        }
        return e
    },
getDockedItems:function(c){
    var d=this,b=d.defaultDockWeights,a;
    if(d.dockedItems&&d.dockedItems.items.length){
        if(c){
            a=Ext.ComponentQuery.query(c,d.dockedItems.items)
            }else{
            a=d.dockedItems.items.slice()
            }
            Ext.Array.sort(a,function(f,e){
            var g=f.weight||b[f.dock],h=e.weight||b[e.dock];
            if(Ext.isNumber(g)&&Ext.isNumber(h)){
                return g-h
                }
                return 0
            });
        return a
        }
        return[]
    },
addUIClsToElement:function(b,f){
    var e=this,a=e.callParent(arguments),d=[Ext.baseCSSPrefix+b,e.baseCls+"-body-"+b,e.baseCls+"-body-"+e.ui+"-"+b],g,c;
    if(!f&&e.rendered){
        if(e.bodyCls){
            e.body.addCls(e.bodyCls)
            }else{
            e.body.addCls(d)
            }
        }else{
    if(e.bodyCls){
        g=e.bodyCls.split(" ");
        for(c=0;c<d.length;c++){
            if(!Ext.Array.contains(g,d[c])){
                g.push(d[c])
                }
            }
        e.bodyCls=g.join(" ")
    }else{
    e.bodyCls=d.join(" ")
    }
}
return a
},
removeUIClsFromElement:function(b,f){
    var e=this,a=e.callParent(arguments),d=[Ext.baseCSSPrefix+b,e.baseCls+"-body-"+b,e.baseCls+"-body-"+e.ui+"-"+b],g,c;
    if(!f&&e.rendered){
        if(e.bodyCls){
            e.body.removeCls(e.bodyCls)
            }else{
            e.body.removeCls(d)
            }
        }else{
    if(e.bodyCls){
        g=e.bodyCls.split(" ");
        for(c=0;c<d.length;c++){
            Ext.Array.remove(g,d[c])
            }
            e.bodyCls=g.join(" ")
        }
    }
return a
},
addUIToElement:function(c){
    var b=this,a=b.baseCls+"-body-"+b.ui,d;
    b.callParent(arguments);
    if(!c&&b.rendered){
        if(b.bodyCls){
            b.body.addCls(b.bodyCls)
            }else{
            b.body.addCls(a)
            }
        }else{
    if(b.bodyCls){
        d=b.bodyCls.split(" ");
        if(!Ext.Array.contains(d,a)){
            d.push(a)
            }
            b.bodyCls=d.join(" ")
        }else{
        b.bodyCls=a
        }
    }
},
removeUIFromElement:function(){
    var b=this,a=b.baseCls+"-body-"+b.ui,c;
    b.callParent(arguments);
    if(b.rendered){
        if(b.bodyCls){
            b.body.removeCls(b.bodyCls)
            }else{
            b.body.removeCls(a)
            }
        }else{
    if(b.bodyCls){
        c=b.bodyCls.split(" ");
        Ext.Array.remove(c,a);
        b.bodyCls=c.join(" ")
        }else{
        b.bodyCls=a
        }
    }
},
getTargetEl:function(){
    return this.body
    },
getRefItems:function(a){
    var b=this.callParent(arguments),d=this.getDockedItems(a?"*,* *":undefined),f=d.length,c=0,e;
    for(;c<f;c++){
        e=d[c];
        if(e.dock==="right"||e.dock==="bottom"){
            break
        }
    }
    return Ext.Array.splice(d,0,c).concat(b).concat(d)
},
beforeDestroy:function(){
    var b=this.dockedItems,a;
    if(b){
        while((a=b.first())){
            this.removeDocked(a,true)
            }
        }
    this.callParent()
},
setBorder:function(a){
    var b=this;
    b.border=(a!==undefined)?a:true;
    if(b.rendered){
        b.doComponentLayout()
        }
    }
});
Ext.define("Ext.data.Errors",{
    extend:"Ext.util.MixedCollection",
    isValid:function(){
        return this.length===0
        },
    getByField:function(e){
        var d=[],a,c,b;
        for(b=0;b<this.length;b++){
            a=this.items[b];
            if(a.field==e){
                d.push(a)
                }
            }
        return d
    }
});
Ext.define("Ext.dd.DD",{
    extend:"Ext.dd.DragDrop",
    requires:["Ext.dd.DragDropManager"],
    constructor:function(c,a,b){
        if(c){
            this.init(c,a,b)
            }
        },
scroll:true,
autoOffset:function(c,b){
    var a=c-this.startPageX;
    var d=b-this.startPageY;
    this.setDelta(a,d)
    },
setDelta:function(b,a){
    this.deltaX=b;
    this.deltaY=a
    },
setDragElPos:function(c,b){
    var a=this.getDragEl();
    this.alignElWithMouse(a,c,b)
    },
alignElWithMouse:function(b,e,c){
    var f=this.getTargetCoord(e,c),d=b.dom?b:Ext.fly(b,"_dd"),k=d.getSize(),h=Ext.core.Element,i;
    if(!this.deltaSetXY){
        i=this.cachedViewportSize={
            width:h.getDocumentWidth(),
            height:h.getDocumentHeight()
            };
            
        var a=[Math.max(0,Math.min(f.x,i.width-k.width)),Math.max(0,Math.min(f.y,i.height-k.height))];
        d.setXY(a);
        var j=d.getLeft(true);
        var g=d.getTop(true);
        this.deltaSetXY=[j-f.x,g-f.y]
        }else{
        i=this.cachedViewportSize;
        d.setLeftTop(Math.max(0,Math.min(f.x+this.deltaSetXY[0],i.width-k.width)),Math.max(0,Math.min(f.y+this.deltaSetXY[1],i.height-k.height)))
        }
        this.cachePosition(f.x,f.y);
    this.autoScroll(f.x,f.y,b.offsetHeight,b.offsetWidth);
    return f
    },
cachePosition:function(b,a){
    if(b){
        this.lastPageX=b;
        this.lastPageY=a
        }else{
        var c=Ext.core.Element.getXY(this.getEl());
        this.lastPageX=c[0];
        this.lastPageY=c[1]
        }
    },
autoScroll:function(k,j,e,l){
    if(this.scroll){
        var m=Ext.core.Element.getViewHeight();
        var b=Ext.core.Element.getViewWidth();
        var o=this.DDMInstance.getScrollTop();
        var d=this.DDMInstance.getScrollLeft();
        var i=e+j;
        var n=l+k;
        var g=(m+o-j-this.deltaY);
        var f=(b+d-k-this.deltaX);
        var c=40;
        var a=(document.all)?80:30;
        if(i>m&&g<c){
            window.scrollTo(d,o+a)
            }
            if(j<o&&o>0&&j-o<c){
            window.scrollTo(d,o-a)
            }
            if(n>b&&f<c){
            window.scrollTo(d+a,o)
            }
            if(k<d&&d>0&&k-d<c){
            window.scrollTo(d-a,o)
            }
        }
},
getTargetCoord:function(c,b){
    var a=c-this.deltaX;
    var d=b-this.deltaY;
    if(this.constrainX){
        if(a<this.minX){
            a=this.minX
            }
            if(a>this.maxX){
            a=this.maxX
            }
        }
    if(this.constrainY){
    if(d<this.minY){
        d=this.minY
        }
        if(d>this.maxY){
        d=this.maxY
        }
    }
a=this.getTick(a,this.xTicks);
d=this.getTick(d,this.yTicks);
return{
    x:a,
    y:d
}
},
applyConfig:function(){
    this.callParent();
    this.scroll=(this.config.scroll!==false)
    },
b4MouseDown:function(a){
    this.autoOffset(a.getPageX(),a.getPageY())
    },
b4Drag:function(a){
    this.setDragElPos(a.getPageX(),a.getPageY())
    },
toString:function(){
    return("DD "+this.id)
    }
});
Ext.define("Ext.dd.DDProxy",{
    extend:"Ext.dd.DD",
    statics:{
        dragElId:"ygddfdiv"
    },
    constructor:function(c,a,b){
        if(c){
            this.init(c,a,b);
            this.initFrame()
            }
        },
resizeFrame:true,
centerFrame:false,
createFrame:function(){
    var b=this;
    var a=document.body;
    if(!a||!a.firstChild){
        setTimeout(function(){
            b.createFrame()
            },50);
        return
    }
    var d=this.getDragEl();
    if(!d){
        d=document.createElement("div");
        d.id=this.dragElId;
        var c=d.style;
        c.position="absolute";
        c.visibility="hidden";
        c.cursor="move";
        c.border="2px solid #aaa";
        c.zIndex=999;
        a.insertBefore(d,a.firstChild)
        }
    },
initFrame:function(){
    this.createFrame()
    },
applyConfig:function(){
    this.callParent();
    this.resizeFrame=(this.config.resizeFrame!==false);
    this.centerFrame=(this.config.centerFrame);
    this.setDragElId(this.config.dragElId||Ext.dd.DDProxy.dragElId)
    },
showFrame:function(e,d){
    var c=this.getEl();
    var a=this.getDragEl();
    var b=a.style;
    this._resizeProxy();
    if(this.centerFrame){
        this.setDelta(Math.round(parseInt(b.width,10)/2),Math.round(parseInt(b.height,10)/2))
        }
        this.setDragElPos(e,d);
    Ext.fly(a).show()
    },
_resizeProxy:function(){
    if(this.resizeFrame){
        var a=this.getEl();
        Ext.fly(this.getDragEl()).setSize(a.offsetWidth,a.offsetHeight)
        }
    },
b4MouseDown:function(b){
    var a=b.getPageX();
    var c=b.getPageY();
    this.autoOffset(a,c);
    this.setDragElPos(a,c)
    },
b4StartDrag:function(a,b){
    this.showFrame(a,b)
    },
b4EndDrag:function(a){
    Ext.fly(this.getDragEl()).hide()
    },
endDrag:function(c){
    var b=this.getEl();
    var a=this.getDragEl();
    a.style.visibility="";
    this.beforeMove();
    b.style.visibility="hidden";
    Ext.dd.DDM.moveToEl(b,a);
    a.style.visibility="hidden";
    b.style.visibility="";
    this.afterDrag()
    },
beforeMove:function(){},
afterDrag:function(){},
toString:function(){
    return("DDProxy "+this.id)
    }
});
Ext.define("Ext.dd.DragSource",{
    extend:"Ext.dd.DDProxy",
    requires:["Ext.dd.StatusProxy","Ext.dd.DragDropManager"],
    dropAllowed:Ext.baseCSSPrefix+"dd-drop-ok",
    dropNotAllowed:Ext.baseCSSPrefix+"dd-drop-nodrop",
    animRepair:true,
    repairHighlightColor:"c3daf9",
    constructor:function(b,a){
        this.el=Ext.get(b);
        if(!this.dragData){
            this.dragData={}
        }
        Ext.apply(this,a);
    if(!this.proxy){
        this.proxy=Ext.create("Ext.dd.StatusProxy",{
            animRepair:this.animRepair
            })
        }
        this.callParent([this.el.dom,this.ddGroup||this.group,{
        dragElId:this.proxy.id,
        resizeFrame:false,
        isTarget:false,
        scroll:this.scroll===true
        }]);
    this.dragging=false
    },
getDragData:function(a){
    return this.dragData
    },
onDragEnter:function(c,d){
    var b=Ext.dd.DragDropManager.getDDById(d);
    this.cachedTarget=b;
    if(this.beforeDragEnter(b,c,d)!==false){
        if(b.isNotifyTarget){
            var a=b.notifyEnter(this,c,this.dragData);
            this.proxy.setStatus(a)
            }else{
            this.proxy.setStatus(this.dropAllowed)
            }
            if(this.afterDragEnter){
            this.afterDragEnter(b,c,d)
            }
        }
},
beforeDragEnter:function(b,a,c){
    return true
    },
alignElWithMouse:function(){
    this.callParent(arguments);
    this.proxy.sync()
    },
onDragOver:function(c,d){
    var b=this.cachedTarget||Ext.dd.DragDropManager.getDDById(d);
    if(this.beforeDragOver(b,c,d)!==false){
        if(b.isNotifyTarget){
            var a=b.notifyOver(this,c,this.dragData);
            this.proxy.setStatus(a)
            }
            if(this.afterDragOver){
            this.afterDragOver(b,c,d)
            }
        }
},
beforeDragOver:function(b,a,c){
    return true
    },
onDragOut:function(b,c){
    var a=this.cachedTarget||Ext.dd.DragDropManager.getDDById(c);
    if(this.beforeDragOut(a,b,c)!==false){
        if(a.isNotifyTarget){
            a.notifyOut(this,b,this.dragData)
            }
            this.proxy.reset();
        if(this.afterDragOut){
            this.afterDragOut(a,b,c)
            }
        }
    this.cachedTarget=null
},
beforeDragOut:function(b,a,c){
    return true
    },
onDragDrop:function(b,c){
    var a=this.cachedTarget||Ext.dd.DragDropManager.getDDById(c);
    if(this.beforeDragDrop(a,b,c)!==false){
        if(a.isNotifyTarget){
            if(a.notifyDrop(this,b,this.dragData)!==false){
                this.onValidDrop(a,b,c)
                }else{
                this.onInvalidDrop(a,b,c)
                }
            }else{
        this.onValidDrop(a,b,c)
        }
        if(this.afterDragDrop){
        this.afterDragDrop(a,b,c)
        }
    }
delete this.cachedTarget
},
beforeDragDrop:function(b,a,c){
    return true
    },
onValidDrop:function(b,a,c){
    this.hideProxy();
    if(this.afterValidDrop){
        this.afterValidDrop(b,a,c)
        }
    },
getRepairXY:function(b,a){
    return this.el.getXY()
    },
onInvalidDrop:function(b,a,c){
    this.beforeInvalidDrop(b,a,c);
    if(this.cachedTarget){
        if(this.cachedTarget.isNotifyTarget){
            this.cachedTarget.notifyOut(this,a,this.dragData)
            }
            this.cacheTarget=null
        }
        this.proxy.repair(this.getRepairXY(a,this.dragData),this.afterRepair,this);
    if(this.afterInvalidDrop){
        this.afterInvalidDrop(a,c)
        }
    },
afterRepair:function(){
    var a=this;
    if(Ext.enableFx){
        a.el.highlight(a.repairHighlightColor)
        }
        a.dragging=false
    },
beforeInvalidDrop:function(b,a,c){
    return true
    },
handleMouseDown:function(b){
    if(this.dragging){
        return
    }
    var a=this.getDragData(b);
    if(a&&this.onBeforeDrag(a,b)!==false){
        this.dragData=a;
        this.proxy.stop();
        this.callParent(arguments)
        }
    },
onBeforeDrag:function(a,b){
    return true
    },
onStartDrag:Ext.emptyFn,
startDrag:function(a,b){
    this.proxy.reset();
    this.dragging=true;
    this.proxy.update("");
    this.onInitDrag(a,b);
    this.proxy.show()
    },
onInitDrag:function(a,c){
    var b=this.el.dom.cloneNode(true);
    b.id=Ext.id();
    this.proxy.update(b);
    this.onStartDrag(a,c);
    return true
    },
getProxy:function(){
    return this.proxy
    },
hideProxy:function(){
    this.proxy.hide();
    this.proxy.reset(true);
    this.dragging=false
    },
triggerCacheRefresh:function(){
    Ext.dd.DDM.refreshCache(this.groups)
    },
b4EndDrag:function(a){},
endDrag:function(a){
    this.onEndDrag(this.dragData,a)
    },
onEndDrag:function(a,b){},
autoOffset:function(a,b){
    this.setDelta(-12,-20)
    },
destroy:function(){
    this.callParent();
    Ext.destroy(this.proxy)
    }
});
Ext.define("Ext.panel.DD",{
    extend:"Ext.dd.DragSource",
    requires:["Ext.panel.Proxy"],
    constructor:function(b,a){
        this.panel=b;
        this.dragData={
            panel:b
        };
        
        this.proxy=Ext.create("Ext.panel.Proxy",b,a);
        this.callParent([b.el,a]);
        Ext.defer(function(){
            var d=b.header,c=b.body;
            if(d){
                this.setHandleElId(d.id);
                c=d.el
                }
                c.setStyle("cursor","move");
            this.scroll=false
            },200,this)
        },
    showFrame:Ext.emptyFn,
    startDrag:Ext.emptyFn,
    b4StartDrag:function(a,b){
        this.proxy.show()
        },
    b4MouseDown:function(b){
        var a=b.getPageX(),c=b.getPageY();
        this.autoOffset(a,c)
        },
    onInitDrag:function(a,b){
        this.onStartDrag(a,b);
        return true
        },
    createFrame:Ext.emptyFn,
    getDragEl:function(a){
        return this.proxy.ghost.el.dom
        },
    endDrag:function(a){
        this.proxy.hide();
        this.panel.saveState()
        },
    autoOffset:function(a,b){
        a-=this.startPageX;
        b-=this.startPageY;
        this.setDelta(a,b)
        }
    });
Ext.define("Ext.panel.Panel",{
    extend:"Ext.panel.AbstractPanel",
    requires:["Ext.panel.Header","Ext.fx.Anim","Ext.util.KeyMap","Ext.panel.DD","Ext.XTemplate","Ext.layout.component.Dock","Ext.util.Memento"],
    alias:"widget.panel",
    alternateClassName:"Ext.Panel",
    collapsedCls:"collapsed",
    animCollapse:Ext.enableFx,
    minButtonWidth:75,
    collapsed:false,
    collapseFirst:true,
    hideCollapseTool:false,
    titleCollapse:false,
    floatable:true,
    collapsible:false,
    closable:false,
    closeAction:"destroy",
    preventHeader:false,
    headerPosition:"top",
    frame:false,
    frameHeader:true,
    initComponent:function(){
        var b=this,a;
        b.addEvents("beforeexpand","beforecollapse","expand","collapse","titlechange","iconchange");
        this.addStateEvents("expand","collapse");
        if(b.unstyled){
            b.setUI("plain")
            }
            if(b.frame){
            b.setUI(b.ui+"-framed")
            }
            b.callParent();
        b.collapseDirection=b.collapseDirection||b.headerPosition||Ext.Component.DIRECTION_TOP;
        b.bridgeToolbars()
        },
    setBorder:function(a){
        this.callParent(arguments)
        },
    beforeDestroy:function(){
        Ext.destroy(this.ghostPanel,this.dd);
        this.callParent()
        },
    initAria:function(){
        this.callParent();
        this.initHeaderAria()
        },
    initHeaderAria:function(){
        var b=this,a=b.el,c=b.header;
        if(a&&c){
            a.dom.setAttribute("aria-labelledby",c.titleCmp.id)
            }
        },
getHeader:function(){
    return this.header
    },
setTitle:function(c){
    var b=this,a=this.title;
    b.title=c;
    if(b.header){
        b.header.setTitle(c)
        }else{
        b.updateHeader()
        }
        if(b.reExpander){
        b.reExpander.setTitle(c)
        }
        b.fireEvent("titlechange",b,c,a)
    },
setIconCls:function(a){
    var c=this,b=c.iconCls;
    c.iconCls=a;
    var d=c.header;
    if(d){
        d.setIconCls(a)
        }
        c.fireEvent("iconchange",c,a,b)
    },
bridgeToolbars:function(){
    var a=this,c,b,e=a.minButtonWidth;
    function d(f,h,g){
        if(Ext.isArray(f)){
            f={
                xtype:"toolbar",
                items:f
            }
        }else{
        if(!f.xtype){
            f.xtype="toolbar"
            }
        }
    f.dock=h;
if(h=="left"||h=="right"){
    f.vertical=true
    }
    if(g){
    f.layout=Ext.applyIf(f.layout||{},{
        pack:{
            left:"start",
            center:"center"
        }
        [a.buttonAlign]||"end"
        })
    }
    return f
}
if(a.tbar){
    a.addDocked(d(a.tbar,"top"));
    a.tbar=null
    }
    if(a.bbar){
    a.addDocked(d(a.bbar,"bottom"));
    a.bbar=null
    }
    if(a.buttons){
    a.fbar=a.buttons;
    a.buttons=null
    }
    if(a.fbar){
    c=d(a.fbar,"bottom",true);
    c.ui="footer";
    if(e){
        b=c.defaults;
        c.defaults=function(f){
            var g=b||{};
            
            if((!f.xtype||f.xtype==="button"||(f.isComponent&&f.isXType("button")))&&!("minWidth" in g)){
                g=Ext.apply({
                    minWidth:e
                },g)
                }
                return g
            }
        }
    a.addDocked(c);
    a.fbar=null
    }
    if(a.lbar){
    a.addDocked(d(a.lbar,"left"));
    a.lbar=null
    }
    if(a.rbar){
    a.addDocked(d(a.rbar,"right"));
    a.rbar=null
    }
},
initTools:function(){
    var a=this;
    a.tools=a.tools||[];
    if(a.collapsible&&!(a.hideCollapseTool||a.header===false)){
        a.collapseDirection=a.collapseDirection||a.headerPosition||"top";
        a.collapseTool=a.expandTool=a.createComponent({
            xtype:"tool",
            type:"collapse-"+a.collapseDirection,
            expandType:a.getOppositeDirection(a.collapseDirection),
            handler:a.toggleCollapse,
            scope:a
        });
        if(a.collapseFirst){
            a.tools.unshift(a.collapseTool)
            }
        }
    a.addTools();
if(a.closable){
    a.addClsWithUI("closable");
    a.addTool({
        type:"close",
        handler:Ext.Function.bind(a.close,this,[])
        })
    }
    if(a.collapseTool&&!a.collapseFirst){
    a.tools.push(a.collapseTool)
    }
},
addTools:Ext.emptyFn,
close:function(){
    if(this.fireEvent("beforeclose",this)!==false){
        this.doClose()
        }
    },
doClose:function(){
    this.fireEvent("close",this);
    this[this.closeAction]()
    },
onRender:function(b,a){
    var d=this,c;
    d.initTools();
    d.updateHeader();
    d.callParent(arguments)
    },
afterComponentLayout:function(){
    var a=this;
    a.callParent(arguments);
    if(a.collapsed&&a.componentLayoutCounter==1){
        a.collapsed=false;
        a.collapse(null,false,true)
        }
    },
updateHeader:function(b){
    var a=this,e=a.header,d=a.title,c=a.tools;
    if(!a.preventHeader&&(b||d||(c&&c.length))){
        if(!e){
            e=a.header=Ext.create("Ext.panel.Header",{
                title:d,
                orientation:(a.headerPosition=="left"||a.headerPosition=="right")?"vertical":"horizontal",
                dock:a.headerPosition||"top",
                textCls:a.headerTextCls,
                iconCls:a.iconCls,
                baseCls:a.baseCls+"-header",
                tools:c,
                ui:a.ui,
                indicateDrag:a.draggable,
                border:a.border,
                frame:a.frame&&a.frameHeader,
                ignoreParentFrame:a.frame||a.overlapHeader,
                ignoreBorderManagement:a.frame||a.ignoreHeaderBorderManagement,
                listeners:a.collapsible&&a.titleCollapse?{
                    click:a.toggleCollapse,
                    scope:a
                }:null
                });
            a.addDocked(e,0);
            a.tools=e.tools
            }
            e.show();
        a.initHeaderAria()
        }else{
        if(e){
            e.hide()
            }
        }
},
setUI:function(b){
    var a=this;
    a.callParent(arguments);
    if(a.header){
        a.header.setUI(b)
        }
    },
getContentTarget:function(){
    return this.body
    },
getTargetEl:function(){
    return this.body||this.frameBody||this.el
    },
addTool:function(a){
    this.tools.push(a);
    var b=this.header;
    if(b){
        b.addTool(a)
        }
        this.updateHeader()
    },
getOppositeDirection:function(a){
    var b=Ext.Component;
    switch(a){
        case b.DIRECTION_TOP:
            return b.DIRECTION_BOTTOM;
        case b.DIRECTION_RIGHT:
            return b.DIRECTION_LEFT;
        case b.DIRECTION_BOTTOM:
            return b.DIRECTION_TOP;
        case b.DIRECTION_LEFT:
            return b.DIRECTION_RIGHT
            }
        },
collapse:function(u,f,h){
    var v=this,t=Ext.Component,k=v.getHeight(),l=v.getWidth(),w,a=0,q=v.dockedItems.items,r=q.length,p=0,s,g,o={
        from:{
            height:k,
            width:l
        },
        to:{
            height:k,
            width:l
        },
        listeners:{
            afteranimate:v.afterCollapse,
            scope:v
        },
        duration:Ext.Number.from(f,Ext.fx.Anim.prototype.duration)
        },e,d,m,b,j,n;
    if(!u){
        u=v.collapseDirection
        }
        if(h){
        f=false
        }else{
        if(v.collapsed||v.fireEvent("beforecollapse",v,u,f)===false){
            return false
            }
        }
    m=u;
v.expandDirection=v.getOppositeDirection(u);
v.hiddenDocked=[];
switch(u){
    case t.DIRECTION_TOP:case t.DIRECTION_BOTTOM:
        v.expandedSize=v.getHeight();
        d="horizontal";
        n="height";
        b="getHeight";
        j="setHeight";
        for(;p<r;p++){
        s=q[p];
        if(s.isVisible()){
            if(s.isHeader&&(!s.dock||s.dock=="top"||s.dock=="bottom")){
                e=s
                }else{
                v.hiddenDocked.push(s)
                }
            }
    }
    if(u==Ext.Component.DIRECTION_BOTTOM){
    g=v.getPosition()[1]-Ext.fly(v.el.dom.offsetParent).getRegion().top;
    o.from.top=g
    }
    break;
case t.DIRECTION_LEFT:case t.DIRECTION_RIGHT:
    v.expandedSize=v.getWidth();
    d="vertical";
    n="width";
    b="getWidth";
    j="setWidth";
    for(;p<r;p++){
    s=q[p];
    if(s.isVisible()){
        if(s.isHeader&&(s.dock=="left"||s.dock=="right")){
            e=s
            }else{
            v.hiddenDocked.push(s)
            }
        }
}
if(u==Ext.Component.DIRECTION_RIGHT){
    g=v.getPosition()[0]-Ext.fly(v.el.dom.offsetParent).getRegion().left;
    o.from.left=g
    }
    break;
default:
    throw ("Panel collapse must be passed a valid Component collapse direction")
    }
    v.setAutoScroll(false);
v.suspendLayout=true;
v.body.setVisibilityMode(Ext.core.Element.DISPLAY);
if(f&&v.collapseTool){
    v.collapseTool.disable()
    }
    v.addClsWithUI(v.collapsedCls);
if(e){
    e.addClsWithUI(v.collapsedCls);
    e.addClsWithUI(v.collapsedCls+"-"+e.dock);
    if(v.border&&(!v.frame||(v.frame&&Ext.supports.CSS3BorderRadius))){
        e.addClsWithUI(v.collapsedCls+"-border-"+e.dock)
        }
        w=e.getFrameInfo();
    a=e[b]()+(w?w[u]:0);
    e.removeClsWithUI(v.collapsedCls);
    e.removeClsWithUI(v.collapsedCls+"-"+e.dock);
    if(v.border&&(!v.frame||(v.frame&&Ext.supports.CSS3BorderRadius))){
        e.removeClsWithUI(v.collapsedCls+"-border-"+e.dock)
        }
    }else{
    e={
        hideMode:"offsets",
        temporary:true,
        title:v.title,
        orientation:d,
        dock:m,
        textCls:v.headerTextCls,
        iconCls:v.iconCls,
        baseCls:v.baseCls+"-header",
        ui:v.ui,
        frame:v.frame&&v.frameHeader,
        ignoreParentFrame:v.frame||v.overlapHeader,
        indicateDrag:v.draggable,
        cls:v.baseCls+"-collapsed-placeholder  "+Ext.baseCSSPrefix+"docked "+v.baseCls+"-"+v.ui+"-collapsed",
        renderTo:v.el
        };
        
    if(!v.hideCollapseTool){
        e[(e.orientation=="horizontal")?"tools":"items"]=[{
            xtype:"tool",
            type:"expand-"+v.expandDirection,
            handler:v.toggleCollapse,
            scope:v
        }]
        }
        e=v.reExpander=Ext.create("Ext.panel.Header",e);
    a=e[b]()+((e.frame)?e.frameSize[u]:0);
    e.hide();
    v.insertDocked(0,e)
    }
    v.reExpander=e;
v.reExpander.addClsWithUI(v.collapsedCls);
v.reExpander.addClsWithUI(v.collapsedCls+"-"+e.dock);
if(v.border&&(!v.frame||(v.frame&&Ext.supports.CSS3BorderRadius))){
    v.reExpander.addClsWithUI(v.collapsedCls+"-border-"+v.reExpander.dock)
    }
    if(u==Ext.Component.DIRECTION_RIGHT){
    o.to.left=g+(l-a)
    }else{
    if(u==Ext.Component.DIRECTION_BOTTOM){
        o.to.top=g+(k-a)
        }
    }
o.to[n]=a;
if(!v.collapseMemento){
    v.collapseMemento=new Ext.util.Memento(v)
    }
    v.collapseMemento.capture(["width","height","minWidth","minHeight"]);
v.savedFlex=v.flex;
v.minWidth=0;
v.minHeight=0;
delete v.flex;
if(f){
    v.animate(o)
    }else{
    v.setSize(o.to.width,o.to.height);
    if(Ext.isDefined(o.to.left)||Ext.isDefined(o.to.top)){
        v.setPosition(o.to.left,o.to.top)
        }
        v.afterCollapse(false,h)
    }
    return v
},
afterCollapse:function(e,b){
    var d=this,c=0,a=d.hiddenDocked.length;
    d.collapseMemento.restore(["minWidth","minHeight"]);
    d.body.hide();
    for(;c<a;c++){
        d.hiddenDocked[c].hide()
        }
        if(d.reExpander){
        d.reExpander.updateFrame();
        d.reExpander.show()
        }
        d.collapsed=true;
    if(!b){
        d.doComponentLayout()
        }
        if(d.resizer){
        d.resizer.disable()
        }
        if(Ext.Component.VERTICAL_DIRECTION.test(d.expandDirection)){
        d.collapseMemento.restore("width")
        }else{
        d.collapseMemento.restore("height")
        }
        if(d.collapseTool){
        d.collapseTool.setType("expand-"+d.expandDirection)
        }
        if(!b){
        d.fireEvent("collapse",d)
        }
        if(e&&d.collapseTool){
        d.collapseTool.enable()
        }
    },
expand:function(b){
    var f=this;
    if(!f.collapsed||f.fireEvent("beforeexpand",f,b)===false){
        return false
        }
        var e=0,c=f.hiddenDocked.length,h=f.expandDirection,j=f.getHeight(),a=f.getWidth(),g,d;
    if(b&&f.collapseTool){
        f.collapseTool.disable()
        }
        for(;e<c;e++){
        f.hiddenDocked[e].hidden=false;
        f.hiddenDocked[e].el.show()
        }
        if(f.reExpander){
        if(f.reExpander.temporary){
            f.reExpander.hide()
            }else{
            f.reExpander.removeClsWithUI(f.collapsedCls);
            f.reExpander.removeClsWithUI(f.collapsedCls+"-"+f.reExpander.dock);
            if(f.border&&(!f.frame||(f.frame&&Ext.supports.CSS3BorderRadius))){
                f.reExpander.removeClsWithUI(f.collapsedCls+"-border-"+f.reExpander.dock)
                }
                f.reExpander.updateFrame()
            }
        }
    if(f.collapseTool){
    f.collapseTool.setType("collapse-"+f.collapseDirection)
    }
    f.collapsed=false;
f.body.show();
f.removeClsWithUI(f.collapsedCls);
d={
    to:{},
    from:{
        height:j,
        width:a
    },
    listeners:{
        afteranimate:f.afterExpand,
        scope:f
    }
};

if((h==Ext.Component.DIRECTION_TOP)||(h==Ext.Component.DIRECTION_BOTTOM)){
    if(f.autoHeight){
        f.setCalculatedSize(f.width,null);
        d.to.height=f.getHeight();
        f.setCalculatedSize(f.width,d.from.height)
        }else{
        if(f.savedFlex){
            f.flex=f.savedFlex;
            d.to.height=f.ownerCt.layout.calculateChildBox(f).height;
            delete f.flex
            }else{
            d.to.height=f.expandedSize
            }
        }
    if(h==Ext.Component.DIRECTION_TOP){
    g=f.getPosition()[1]-Ext.fly(f.el.dom.offsetParent).getRegion().top;
    d.from.top=g;
    d.to.top=g-(d.to.height-j)
    }
}else{
    if((h==Ext.Component.DIRECTION_LEFT)||(h==Ext.Component.DIRECTION_RIGHT)){
        if(f.autoWidth){
            f.setCalculatedSize(null,f.height);
            d.to.width=f.getWidth();
            f.setCalculatedSize(d.from.width,f.height)
            }else{
            if(f.savedFlex){
                f.flex=f.savedFlex;
                d.to.width=f.ownerCt.layout.calculateChildBox(f).width;
                delete f.flex
                }else{
                d.to.width=f.expandedSize
                }
            }
        if(h==Ext.Component.DIRECTION_LEFT){
        g=f.getPosition()[0]-Ext.fly(f.el.dom.offsetParent).getRegion().left;
        d.from.left=g;
        d.to.left=g-(d.to.width-a)
        }
    }
}
if(b){
    f.animate(d)
    }else{
    f.setCalculatedSize(d.to.width,d.to.height);
    if(d.to.x){
        f.setLeft(d.to.x)
        }
        if(d.to.y){
        f.setTop(d.to.y)
        }
        f.afterExpand(false)
    }
    return f
},
afterExpand:function(b){
    var a=this;
    if(a.collapseMemento){
        a.collapseMemento.restoreAll()
        }
        a.setAutoScroll(a.initialConfig.autoScroll);
    if(a.savedFlex){
        a.flex=a.savedFlex;
        delete a.savedFlex;
        delete a.width;
        delete a.height
        }
        delete a.suspendLayout;
    if(b&&a.ownerCt){
        Ext.defer(a.ownerCt.doLayout,Ext.isIE6?1:0,a)
        }
        if(a.resizer){
        a.resizer.enable()
        }
        a.fireEvent("expand",a);
    if(b&&a.collapseTool){
        a.collapseTool.enable()
        }
    },
toggleCollapse:function(){
    if(this.collapsed){
        this.expand(this.animCollapse)
        }else{
        this.collapse(this.collapseDirection,this.animCollapse)
        }
        return this
    },
getKeyMap:function(){
    if(!this.keyMap){
        this.keyMap=Ext.create("Ext.util.KeyMap",this.el,this.keys)
        }
        return this.keyMap
    },
initDraggable:function(){
    this.dd=Ext.create("Ext.panel.DD",this,Ext.isBoolean(this.draggable)?null:this.draggable)
    },
ghostTools:function(){
    var b=[],a=this.initialConfig.tools;
    if(a){
        Ext.each(a,function(c){
            b.push({
                type:c.type
                })
            })
        }else{
        b=[{
            type:"placeholder"
        }]
        }
        return b
    },
ghost:function(a){
    var d=this,b=d.ghostPanel,c=d.getBox();
    if(!b){
        b=Ext.create("Ext.panel.Panel",{
            renderTo:document.body,
            floating:{
                shadow:false
            },
            frame:Ext.supports.CSS3BorderRadius?d.frame:false,
            title:d.title,
            overlapHeader:d.overlapHeader,
            headerPosition:d.headerPosition,
            width:d.getWidth(),
            height:d.getHeight(),
            iconCls:d.iconCls,
            baseCls:d.baseCls,
            tools:d.ghostTools(),
            cls:d.baseCls+"-ghost "+(a||"")
            });
        d.ghostPanel=b
        }
        b.floatParent=d.floatParent;
    if(d.floating){
        b.setZIndex(Ext.Number.from(d.el.getStyle("zIndex"),0))
        }else{
        b.toFront()
        }
        b.el.show();
    b.setPosition(c.x,c.y);
    b.setSize(c.width,c.height);
    d.el.hide();
    if(d.floatingItems){
        d.floatingItems.hide()
        }
        return b
    },
unghost:function(b,a){
    var c=this;
    if(!c.ghostPanel){
        return
    }
    if(b!==false){
        c.el.show();
        if(a!==false){
            c.setPosition(c.ghostPanel.getPosition())
            }
            if(c.floatingItems){
            c.floatingItems.show()
            }
            Ext.defer(c.focus,10,c)
        }
        c.ghostPanel.el.hide()
    },
initResizable:function(a){
    if(this.collapsed){
        a.disabled=true
        }
        this.callParent([a])
    }
});
Ext.define("Ext.window.Window",{
    extend:"Ext.panel.Panel",
    alternateClassName:"Ext.Window",
    requires:["Ext.util.ComponentDragger","Ext.util.Region","Ext.EventManager"],
    alias:"widget.window",
    baseCls:Ext.baseCSSPrefix+"window",
    resizable:true,
    draggable:true,
    constrain:false,
    constrainHeader:false,
    plain:false,
    minimizable:false,
    maximizable:false,
    minHeight:100,
    minWidth:200,
    expandOnShow:true,
    collapsible:false,
    closable:true,
    hidden:true,
    autoRender:true,
    hideMode:"visibility",
    floating:true,
    ariaRole:"alertdialog",
    itemCls:"x-window-item",
    overlapHeader:true,
    ignoreHeaderBorderManagement:true,
    initComponent:function(){
        var a=this;
        a.callParent();
        a.addEvents("resize","maximize","minimize","restore");
        if(a.plain){
            a.addClsWithUI("plain")
            }
            if(a.modal){
            a.ariaRole="dialog"
            }
        },
initStateEvents:function(){
    var a=this.stateEvents;
    Ext.each(["maximize","restore","resize","dragend"],function(b){
        if(Ext.Array.indexOf(a,b)){
            a.push(b)
            }
        });
this.callParent()
    },
getState:function(){
    var b=this,c=b.callParent()||{},a=!!b.maximized;
    c.maximized=a;
    Ext.apply(c,{
        size:a?b.restoreSize:b.getSize(),
        pos:a?b.restorePos:b.getPosition()
        });
    return c
    },
applyState:function(b){
    var a=this;
    if(b){
        a.maximized=b.maximized;
        if(a.maximized){
            a.hasSavedRestore=true;
            a.restoreSize=b.size;
            a.restorePos=b.pos
            }else{
            Ext.apply(a,{
                width:b.size.width,
                height:b.size.height,
                x:b.pos[0],
                y:b.pos[1]
                })
            }
        }
},
onMouseDown:function(){
    if(this.floating){
        this.toFront()
        }
    },
onRender:function(b,a){
    var c=this;
    c.callParent(arguments);
    c.focusEl=c.el;
    if(c.maximizable){
        c.header.on({
            dblclick:{
                fn:c.toggleMaximize,
                element:"el",
                scope:c
            }
        })
    }
},
afterRender:function(){
    var a=this,b=a.hidden,c;
    a.hidden=false;
    a.callParent();
    a.hidden=b;
    a.proxy=a.getProxy();
    a.mon(a.el,"mousedown",a.onMouseDown,a);
    if(a.maximized){
        a.maximized=false;
        a.maximize()
        }
        if(a.closable){
        c=a.getKeyMap();
        c.on(27,a.onEsc,a);
        c.disable()
        }
        if(!b){
        a.syncMonitorWindowResize();
        a.doConstrain()
        }
    },
initDraggable:function(){
    var b=this,a;
    if(!b.header){
        b.updateHeader(true)
        }
        if(b.header){
        a=Ext.applyIf({
            el:b.el,
            delegate:"#"+b.header.id
            },b.draggable);
        if(b.constrain||b.constrainHeader){
            a.constrain=b.constrain;
            a.constrainDelegate=b.constrainHeader;
            a.constrainTo=b.constrainTo||b.container
            }
            b.dd=Ext.create("Ext.util.ComponentDragger",this,a);
        b.relayEvents(b.dd,["dragstart","drag","dragend"])
        }
    },
onEsc:function(a,b){
    b.stopEvent();
    this[this.closeAction]()
    },
beforeDestroy:function(){
    var a=this;
    if(a.rendered){
        delete this.animateTarget;
        a.hide();
        Ext.destroy(a.keyMap)
        }
        a.callParent()
    },
addTools:function(){
    var a=this;
    a.callParent();
    if(a.minimizable){
        a.addTool({
            type:"minimize",
            handler:Ext.Function.bind(a.minimize,a,[])
            })
        }
        if(a.maximizable){
        a.addTool({
            type:"maximize",
            handler:Ext.Function.bind(a.maximize,a,[])
            });
        a.addTool({
            type:"restore",
            handler:Ext.Function.bind(a.restore,a,[]),
            hidden:true
        })
        }
    },
getFocusEl:function(){
    var d=this,g=d.focusEl,e=d.defaultButton||d.defaultFocus,b=typeof db,c,a;
    if(Ext.isDefined(e)){
        if(Ext.isNumber(e)){
            g=d.query("button")[e]
            }else{
            if(Ext.isString(e)){
                g=d.down("#"+e)
                }else{
                g=e
                }
            }
    }
return g||d.focusEl
},
beforeShow:function(){
    this.callParent();
    if(this.expandOnShow){
        this.expand(false)
        }
    },
afterShow:function(c){
    var b=this,a=c||b.animateTarget;
    if(a){
        b.doConstrain()
        }
        b.callParent(arguments);
    if(b.maximized){
        b.fitContainer()
        }
        b.syncMonitorWindowResize();
    if(!a){
        b.doConstrain()
        }
        if(b.keyMap){
        b.keyMap.enable()
        }
    },
doClose:function(){
    var a=this;
    if(a.hidden){
        a.fireEvent("close",a);
        a[a.closeAction]()
        }else{
        a.hide(a.animTarget,a.doClose,a)
        }
    },
afterHide:function(){
    var a=this;
    a.syncMonitorWindowResize();
    if(a.keyMap){
        a.keyMap.disable()
        }
        a.callParent(arguments)
    },
onWindowResize:function(){
    if(this.maximized){
        this.fitContainer()
        }
        this.doConstrain()
    },
minimize:function(){
    this.fireEvent("minimize",this);
    return this
    },
afterCollapse:function(){
    var a=this;
    if(a.maximizable){
        a.tools.maximize.hide();
        a.tools.restore.hide()
        }
        if(a.resizer){
        a.resizer.disable()
        }
        a.callParent(arguments)
    },
afterExpand:function(){
    var a=this;
    if(a.maximized){
        a.tools.restore.show()
        }else{
        if(a.maximizable){
            a.tools.maximize.show()
            }
        }
    if(a.resizer){
    a.resizer.enable()
    }
    a.callParent(arguments)
},
maximize:function(){
    var a=this;
    if(!a.maximized){
        a.expand(false);
        if(!a.hasSavedRestore){
            a.restoreSize=a.getSize();
            a.restorePos=a.getPosition(true)
            }
            if(a.maximizable){
            a.tools.maximize.hide();
            a.tools.restore.show()
            }
            a.maximized=true;
        a.el.disableShadow();
        if(a.dd){
            a.dd.disable()
            }
            if(a.collapseTool){
            a.collapseTool.hide()
            }
            a.el.addCls(Ext.baseCSSPrefix+"window-maximized");
        a.container.addCls(Ext.baseCSSPrefix+"window-maximized-ct");
        a.syncMonitorWindowResize();
        a.setPosition(0,0);
        a.fitContainer();
        a.fireEvent("maximize",a)
        }
        return a
    },
restore:function(){
    var a=this,b=a.tools;
    if(a.maximized){
        delete a.hasSavedRestore;
        a.removeCls(Ext.baseCSSPrefix+"window-maximized");
        if(b.restore){
            b.restore.hide()
            }
            if(b.maximize){
            b.maximize.show()
            }
            if(a.collapseTool){
            a.collapseTool.show()
            }
            a.setPosition(a.restorePos);
        a.setSize(a.restoreSize);
        delete a.restorePos;
        delete a.restoreSize;
        a.maximized=false;
        a.el.enableShadow(true);
        if(a.dd){
            a.dd.enable()
            }
            a.container.removeCls(Ext.baseCSSPrefix+"window-maximized-ct");
        a.syncMonitorWindowResize();
        a.doConstrain();
        a.fireEvent("restore",a)
        }
        return a
    },
syncMonitorWindowResize:function(){
    var b=this,c=b._monitoringResize,d=b.monitorResize||b.constrain||b.constrainHeader||b.maximized,a=b.hidden||b.destroying||b.isDestroyed;
    if(d&&!a){
        if(!c){
            Ext.EventManager.onWindowResize(b.onWindowResize,b);
            b._monitoringResize=true
            }
        }else{
    if(c){
        Ext.EventManager.removeResizeListener(b.onWindowResize,b);
        b._monitoringResize=false
        }
    }
},
toggleMaximize:function(){
    return this[this.maximized?"restore":"maximize"]()
    }
});
Ext.define("Ext.app.Portlet",{
    extend:"Ext.panel.Panel",
    alias:"widget.portlet",
    layout:"fit",
    anchor:"100%",
    frame:true,
    closable:true,
    collapsible:true,
    animCollapse:true,
    draggable:true,
    cls:"x-portlet",
    doClose:function(){
        this.el.animate({
            opacity:0,
            callback:function(){
                this.fireEvent("close",this);
                this[this.closeAction]()
                },
            scope:this
        })
        }
    });
Ext.define("Ext.app.PortalPanel",{
    extend:"Ext.panel.Panel",
    alias:"widget.portalpanel",
    requires:["Ext.layout.component.Body"],
    cls:"x-portal",
    bodyCls:"x-portal-body",
    defaultType:"portalcolumn",
    componentLayout:"body",
    autoScroll:true,
    initComponent:function(){
        var a=this;
        this.layout={
            type:"column"
        };
        
        this.callParent();
        this.addEvents({
            validatedrop:true,
            beforedragover:true,
            dragover:true,
            beforedrop:true,
            drop:true
        });
        this.on("drop",this.doLayout,this)
        },
    beforeLayout:function(){
        var b=this.layout.getLayoutItems(),a=b.length,c=0,d;
        for(;c<a;c++){
            d=b[c];
            d.columnWidth=1/a;
            d.removeCls(["x-portal-column-first","x-portal-column-last"])
            }
            b[0].addCls("x-portal-column-first");
        b[a-1].addCls("x-portal-column-last");
        return this.callParent(arguments)
        },
    initEvents:function(){
        this.callParent();
        this.dd=Ext.create("Ext.app.PortalDropZone",this,this.dropConfig)
        },
    beforeDestroy:function(){
        if(this.dd){
            this.dd.unreg()
            }
            Ext.app.PortalPanel.superclass.beforeDestroy.call(this)
        }
    });
Ext.define("Ext.panel.Table",{
    extend:"Ext.panel.Panel",
    alias:"widget.tablepanel",
    uses:["Ext.selection.RowModel","Ext.grid.Scroller","Ext.grid.header.Container","Ext.grid.Lockable"],
    cls:Ext.baseCSSPrefix+"grid",
    extraBodyCls:Ext.baseCSSPrefix+"grid-body",
    layout:"fit",
    hasView:false,
    viewType:null,
    selType:"rowmodel",
    scrollDelta:40,
    scroll:true,
    sortableColumns:true,
    verticalScrollDock:"right",
    verticalScrollerType:"gridscroller",
    horizontalScrollerPresentCls:Ext.baseCSSPrefix+"horizontal-scroller-present",
    verticalScrollerPresentCls:Ext.baseCSSPrefix+"vertical-scroller-present",
    scrollerOwner:true,
    invalidateScrollerOnRefresh:true,
    enableColumnMove:true,
    enableColumnResize:true,
    enableColumnHide:true,
    initComponent:function(){
        var g=this,a=g.scroll,d=false,c=false,h=g.columns||g.colModel,f=0,b,e=g.border;
        if(g.hideHeaders){
            e=false
            }
            if(h instanceof Ext.grid.header.Container){
            g.headerCt=h;
            g.headerCt.border=e;
            g.columns=g.headerCt.items.items
            }else{
            if(Ext.isArray(h)){
                h={
                    items:h,
                    border:e
                }
            }
            Ext.apply(h,{
            forceFit:g.forceFit,
            sortable:g.sortableColumns,
            enableColumnMove:g.enableColumnMove,
            enableColumnResize:g.enableColumnResize,
            enableColumnHide:g.enableColumnHide,
            border:e
        });
        g.columns=h.items;
        if(Ext.ComponentQuery.query("{locked !== undefined}{processed != true}",g.columns).length){
            g.self.mixin("lockable",Ext.grid.Lockable);
            g.injectLockable()
            }
        }
    g.store=Ext.data.StoreManager.lookup(g.store);
    g.addEvents("reconfigure","scrollerhide","scrollershow");
    g.bodyCls=g.bodyCls||"";
    g.bodyCls+=(" "+g.extraBodyCls);
    delete g.autoScroll;
    if(!g.hasView){
    if(!g.headerCt){
        g.headerCt=Ext.create("Ext.grid.header.Container",h)
        }
        g.columns=g.headerCt.items.items;
    if(g.hideHeaders){
        g.headerCt.height=0;
        g.headerCt.border=false;
        g.headerCt.addCls(Ext.baseCSSPrefix+"grid-header-ct-hidden");
        g.addCls(Ext.baseCSSPrefix+"grid-header-hidden");
        if(Ext.isIEQuirks){
            g.headerCt.style={
                display:"none"
            }
        }
    }
if(a===true||a==="both"){
    d=c=true
    }else{
    if(a==="horizontal"){
        c=true
        }else{
        if(a==="vertical"){
            d=true
            }else{
            g.headerCt.availableSpaceOffset=0
            }
        }
}
if(d){
    g.verticalScroller=Ext.ComponentManager.create(g.initVerticalScroller());
    g.mon(g.verticalScroller,{
        bodyscroll:g.onVerticalScroll,
        scope:g
    })
    }
    if(c){
    g.horizontalScroller=Ext.ComponentManager.create(g.initHorizontalScroller());
    g.mon(g.horizontalScroller,{
        bodyscroll:g.onHorizontalScroll,
        scope:g
    })
    }
    g.headerCt.on("columnresize",g.onHeaderResize,g);
g.relayEvents(g.headerCt,["columnresize","columnmove","columnhide","columnshow","sortchange"]);
g.features=g.features||[];
g.dockedItems=g.dockedItems||[];
g.dockedItems.unshift(g.headerCt);
g.viewConfig=g.viewConfig||{};

g.viewConfig.invalidateScrollerOnRefresh=g.invalidateScrollerOnRefresh;
b=g.getView();
b.on({
    afterrender:function(){
        b.el.scroll=Ext.Function.bind(g.elScroll,g);
        g.mon(b.el,{
            mousewheel:g.onMouseWheel,
            scope:g
        })
        },
    single:true
});
this.items=[b];
g.mon(b.store,{
    load:g.onStoreLoad,
    scope:g
});
g.mon(b,{
    refresh:g.onViewRefresh,
    scope:g
});
this.relayEvents(b,["beforeitemmousedown","beforeitemmouseup","beforeitemmouseenter","beforeitemmouseleave","beforeitemclick","beforeitemdblclick","beforeitemcontextmenu","itemmousedown","itemmouseup","itemmouseenter","itemmouseleave","itemclick","itemdblclick","itemcontextmenu","beforecontainermousedown","beforecontainermouseup","beforecontainermouseover","beforecontainermouseout","beforecontainerclick","beforecontainerdblclick","beforecontainercontextmenu","containermouseup","containermouseover","containermouseout","containerclick","containerdblclick","containercontextmenu","selectionchange","beforeselect"])
}
g.callParent(arguments)
},
initStateEvents:function(){
    var a=this.stateEvents;
    Ext.each(["columnresize","columnmove","columnhide","columnshow","sortchange"],function(b){
        if(Ext.Array.indexOf(a,b)){
            a.push(b)
            }
        });
this.callParent()
},
initHorizontalScroller:function(){
    var b=this,a={
        xtype:"gridscroller",
        dock:"bottom",
        section:b,
        store:b.store
        };
        
    return a
    },
initVerticalScroller:function(){
    var b=this,a=b.verticalScroller||{};
    
    Ext.applyIf(a,{
        xtype:b.verticalScrollerType,
        dock:b.verticalScrollDock,
        store:b.store
        });
    return a
    },
getState:function(){
    var c=this.callParent(),f=this.store.sorters.first(),d=this.headerCt.items.items,e,a=d.length,b=0;
    c.columns=[];
    for(;b<a;b++){
        e=d[b];
        c.columns[b]={
            id:e.headerId
            };
            
        if(e.hidden!==(e.initialConfig.hidden||e.self.prototype.hidden)){
            c.columns[b].hidden=e.hidden
            }
            if(e.sortable!==e.initialConfig.sortable){
            c.columns[b].sortable=e.sortable
            }
            if(e.flex){
            if(e.flex!==e.initialConfig.flex){
                c.columns[b].flex=e.flex
                }
            }else{
        if(e.width!==e.initialConfig.width){
            c.columns[b].width=e.width
            }
        }
    }
if(f){
    c.sort={
        property:f.property,
        direction:f.direction
        }
    }
return c
},
applyState:function(a){
    var c=a.columns,b=c?c.length:0,d=this.headerCt,h=d.items,l=a.sort,j=this.store,e=0,g,k,f;
    d.suspendLayout=true;
    this.callParent(arguments);
    for(;e<b;++e){
        k=c[e];
        f=d.down("gridcolumn[headerId="+k.id+"]");
        g=h.indexOf(f);
        if(e!==g){
            d.moveHeader(g,e)
            }
            if(Ext.isDefined(k.hidden)){
            f.hidden=k.hidden
            }
            if(Ext.isDefined(k.sortable)){
            f.sortable=k.sortable
            }
            if(Ext.isDefined(k.flex)){
            delete f.width;
            f.flex=k.flex
            }else{
            if(Ext.isDefined(k.width)){
                delete f.flex;
                f.minWidth=k.width;
                if(f.rendered){
                    f.setWidth(k.width)
                    }else{
                    f.width=k.width
                    }
                }
        }
    }
d.suspendLayout=false;
d.doLayout();
if(l){
    if(j.remoteSort){
        j.sorters.add(Ext.create("Ext.util.Sorter",{
            property:l.property,
            direction:l.direction
            }))
        }else{
        j.sort(l.property,l.direction)
        }
    }
},
getStore:function(){
    return this.store
    },
getView:function(){
    var a=this,b;
    if(!a.view){
        b=a.getSelectionModel();
        a.view=a.createComponent(Ext.apply({},a.viewConfig,{
            deferRowRender:a.deferRowRender,
            xtype:a.viewType,
            store:a.store,
            headerCt:a.headerCt,
            selModel:b,
            features:a.features,
            panel:a
        }));
        a.mon(a.view,{
            uievent:a.processEvent,
            scope:a
        });
        b.view=a.view;
        a.headerCt.view=a.view;
        a.relayEvents(a.view,["cellclick","celldblclick"])
        }
        return a.view
    },
setAutoScroll:Ext.emptyFn,
elScroll:function(d,e,b){
    var c=this,a;
    if(d==="up"||d==="left"){
        e=-e
        }
        if(d==="down"||d==="up"){
        a=c.getVerticalScroller();
        a.scrollByDeltaY(e)
        }else{
        a=c.getHorizontalScroller();
        a.scrollByDeltaX(e)
        }
    },
processEvent:function(f,b,a,c,d,h){
    var g=this,i;
    if(d!==-1){
        i=g.headerCt.getGridColumns()[d];
        return i.processEvent.apply(i,arguments)
        }
    },
determineScrollbars:function(){
    var h=this,d,a,e,i,k,f,g=h.verticalScroller,c=h.horizontalScroller,j=(g&&g.ownerCt===h?1:0)|(c&&c.ownerCt===h?2:0),b=0;
    if(!h.collapsed&&h.view&&h.view.el&&h.view.el.dom.firstChild){
        d=h.layout.getLayoutTargetSize();
        i=d.width+((j&1)?g.width:0);
        f=d.height+((j&2)?c.height:0);
        e=(h.headerCt.query("[flex]").length&&!h.headerCt.layout.tooNarrow)?0:h.headerCt.getFullWidth();
        if(g&&g.el){
            k=g.getSizeCalculation().height
            }else{
            a=h.view.el.child("table",true);
            k=a?a.offsetHeight:0
            }
            if(k>f){
            b=1;
            if(c&&((i-e)<g.width)){
                b=3
                }
            }else{
        if(e>i){
            b=2;
            if(g&&((f-k)<c.height)){
                b=3
                }
            }
    }
if(b!==j){
    h.suspendLayout=true;
    if(b&1){
        h.showVerticalScroller()
        }else{
        h.hideVerticalScroller()
        }
        if(b&2){
        h.showHorizontalScroller()
        }else{
        h.hideHorizontalScroller()
        }
        h.suspendLayout=false;
    h.changingScrollBars=true;
    h.doComponentLayout(h.getWidth(),h.getHeight(),false,h.ownerCt);
    h.changingScrollBars=false
    }
}
},
afterComponentLayout:function(){
    var a=this;
    a.callParent(arguments);
    if(!a.changingScrollBars){
        a.determineScrollbars()
        }
        a.invalidateScroller()
    },
onHeaderResize:function(){
    if(this.view&&this.view.rendered){
        this.determineScrollbars();
        this.invalidateScroller()
        }
    },
afterCollapse:function(){
    var a=this;
    if(a.verticalScroller){
        a.verticalScroller.saveScrollPos()
        }
        if(a.horizontalScroller){
        a.horizontalScroller.saveScrollPos()
        }
        a.callParent(arguments)
    },
afterExpand:function(){
    var a=this;
    a.callParent(arguments);
    if(a.verticalScroller){
        a.verticalScroller.restoreScrollPos()
        }
        if(a.horizontalScroller){
        a.horizontalScroller.restoreScrollPos()
        }
    },
hideHorizontalScroller:function(){
    var a=this;
    if(a.horizontalScroller&&a.horizontalScroller.ownerCt===a){
        a.verticalScroller.setReservedSpace(0);
        a.removeDocked(a.horizontalScroller,false);
        a.removeCls(a.horizontalScrollerPresentCls);
        a.fireEvent("scrollerhide",a.horizontalScroller,"horizontal")
        }
    },
showHorizontalScroller:function(){
    var a=this;
    if(a.verticalScroller){
        a.verticalScroller.setReservedSpace(Ext.getScrollbarSize().height-1)
        }
        if(a.horizontalScroller&&a.horizontalScroller.ownerCt!==a){
        a.addDocked(a.horizontalScroller);
        a.addCls(a.horizontalScrollerPresentCls);
        a.fireEvent("scrollershow",a.horizontalScroller,"horizontal")
        }
    },
hideVerticalScroller:function(){
    var a=this;
    a.setHeaderReserveOffset(false);
    if(a.verticalScroller&&a.verticalScroller.ownerCt===a){
        a.removeDocked(a.verticalScroller,false);
        a.removeCls(a.verticalScrollerPresentCls);
        a.fireEvent("scrollerhide",a.verticalScroller,"vertical")
        }
    },
showVerticalScroller:function(){
    var a=this;
    a.setHeaderReserveOffset(true);
    if(a.verticalScroller&&a.verticalScroller.ownerCt!==a){
        a.addDocked(a.verticalScroller);
        a.addCls(a.verticalScrollerPresentCls);
        a.fireEvent("scrollershow",a.verticalScroller,"vertical")
        }
    },
setHeaderReserveOffset:function(a){
    var c=this.headerCt,b=c.layout;
    if(b&&b.reserveOffset!==a){
        b.reserveOffset=a;
        c.doLayout()
        }
    },
invalidateScroller:function(){
    var b=this,a=b.verticalScroller,c=b.horizontalScroller;
    if(a){
        a.invalidate()
        }
        if(c){
        c.invalidate()
        }
    },
onHeaderMove:function(c,d,a,b){
    this.view.refresh()
    },
onHeaderHide:function(a,b){
    this.invalidateScroller()
    },
onHeaderShow:function(a,b){
    this.invalidateScroller()
    },
getVerticalScroller:function(){
    return this.getScrollerOwner().down("gridscroller[dock="+this.verticalScrollDock+"]")
    },
getHorizontalScroller:function(){
    return this.getScrollerOwner().down("gridscroller[dock=bottom]")
    },
onMouseWheel:function(m){
    var n=this,j=n.getVerticalScroller(),o=n.getHorizontalScroller(),b=-n.scrollDelta,c=m.getWheelDeltas(),h=b*c.x,g=b*c.y,l,p,a,q,d,i,k,f;
    if(o){
        p=o.scrollEl;
        if(p){
            q=p.dom;
            i=q.scrollLeft!==q.scrollWidth-q.clientWidth;
            d=q.scrollLeft!==0
            }
        }
    if(j){
    l=j.scrollEl;
    if(l){
        a=l.dom;
        k=a.scrollTop!==a.scrollHeight-a.clientHeight;
        f=a.scrollTop!==0
        }
    }
if(o){
    if((h<0&&d)||(h>0&&i)){
        m.stopEvent();
        o.scrollByDeltaX(h)
        }
    }
if(j){
    if((g<0&&f)||(g>0&&k)){
        m.stopEvent();
        j.scrollByDeltaY(g)
        }
    }
},
onViewRefresh:function(){
    this.determineScrollbars();
    if(this.invalidateScrollerOnRefresh){
        this.invalidateScroller()
        }
    },
setScrollTop:function(d){
    var c=this,b=c.getScrollerOwner(),a=c.getVerticalScroller();
    b.virtualScrollTop=d;
    if(a){
        a.setScrollTop(d)
        }
    },
getScrollerOwner:function(){
    var a=this;
    if(!this.scrollerOwner){
        a=this.up("[scrollerOwner]")
        }
        return a
    },
scrollByDeltaY:function(a){
    var b=this.getVerticalScroller();
    if(b){
        b.scrollByDeltaY(a)
        }
    },
scrollByDeltaX:function(a){
    var b=this.getVerticalScroller();
    if(b){
        b.scrollByDeltaX(a)
        }
    },
getLhsMarker:function(){
    var a=this;
    if(!a.lhsMarker){
        a.lhsMarker=Ext.core.DomHelper.append(a.el,{
            cls:Ext.baseCSSPrefix+"grid-resize-marker"
            },true)
        }
        return a.lhsMarker
    },
getRhsMarker:function(){
    var a=this;
    if(!a.rhsMarker){
        a.rhsMarker=Ext.core.DomHelper.append(a.el,{
            cls:Ext.baseCSSPrefix+"grid-resize-marker"
            },true)
        }
        return a.rhsMarker
    },
getSelectionModel:function(){
    if(!this.selModel){
        this.selModel={}
    }
    var b="SINGLE",a;
if(this.simpleSelect){
    b="SIMPLE"
    }else{
    if(this.multiSelect){
        b="MULTI"
        }
    }
Ext.applyIf(this.selModel,{
    allowDeselect:this.allowDeselect,
    mode:b
});
if(!this.selModel.events){
    a=this.selModel.selType||this.selType;
    this.selModel=Ext.create("selection."+a,this.selModel)
    }
    if(!this.selModel.hasRelaySetup){
    this.relayEvents(this.selModel,["selectionchange","beforeselect","beforedeselect","select","deselect"]);
    this.selModel.hasRelaySetup=true
    }
    if(this.disableSelection){
    this.selModel.locked=true
    }
    return this.selModel
},
onVerticalScroll:function(e,f){
    var b=this.getScrollerOwner(),c=b.query("tableview"),d=0,a=c.length;
    for(;d<a;d++){
        c[d].el.dom.scrollTop=f.scrollTop
        }
    },
onHorizontalScroll:function(d,e){
    var b=this.getScrollerOwner(),c=b.query("tableview"),a=c[1]||c[0];
    a.el.dom.scrollLeft=e.scrollLeft;
    this.headerCt.el.dom.scrollLeft=e.scrollLeft
    },
onStoreLoad:Ext.emptyFn,
getEditorParent:function(){
    return this.body
    },
bindStore:function(a){
    var b=this;
    b.store=a;
    b.getView().bindStore(a)
    },
reconfigure:function(a,b){
    var c=this,d=c.headerCt;
    if(c.lockable){
        c.reconfigureLockable(a,b)
        }else{
        d.suspendLayout=true;
        d.removeAll();
        if(b){
            d.add(b)
            }else{
            d.doLayout()
            }
            if(a){
            a=Ext.StoreManager.lookup(a);
            c.bindStore(a)
            }else{
            c.getView().refresh()
            }
            if(b){
            c.forceComponentLayout()
            }
        }
    c.fireEvent("reconfigure",c)
}
});
Ext.define("Ext.data.Types",{
    singleton:true,
    requires:["Ext.data.SortTypes"]
    },function(){
    var a=Ext.data.SortTypes;
    Ext.apply(Ext.data.Types,{
        stripRe:/[\$,%]/g,
        AUTO:{
            convert:function(b){
                return b
                },
            sortType:a.none,
            type:"auto"
        },
        STRING:{
            convert:function(c){
                var b=this.useNull?null:"";
                return(c===undefined||c===null)?b:String(c)
                },
            sortType:a.asUCString,
            type:"string"
        },
        INT:{
            convert:function(b){
                return b!==undefined&&b!==null&&b!==""?parseInt(String(b).replace(Ext.data.Types.stripRe,""),10):(this.useNull?null:0)
                },
            sortType:a.none,
            type:"int"
        },
        FLOAT:{
            convert:function(b){
                return b!==undefined&&b!==null&&b!==""?parseFloat(String(b).replace(Ext.data.Types.stripRe,""),10):(this.useNull?null:0)
                },
            sortType:a.none,
            type:"float"
        },
        BOOL:{
            convert:function(b){
                if(this.useNull&&b===undefined||b===null||b===""){
                    return null
                    }
                    return b===true||b==="true"||b==1
                },
            sortType:a.none,
            type:"bool"
        },
        DATE:{
            convert:function(c){
                var d=this.dateFormat;
                if(!c){
                    return null
                    }
                    if(Ext.isDate(c)){
                    return c
                    }
                    if(d){
                    if(d=="timestamp"){
                        return new Date(c*1000)
                        }
                        if(d=="time"){
                        return new Date(parseInt(c,10))
                        }
                        return Ext.Date.parse(c,d)
                    }
                    var b=Date.parse(c);
                return b?new Date(b):null
                },
            sortType:a.asDate,
            type:"date"
        }
    });
Ext.apply(Ext.data.Types,{
    BOOLEAN:this.BOOL,
    INTEGER:this.INT,
    NUMBER:this.FLOAT
    })
});
Ext.define("Ext.data.Field",{
    requires:["Ext.data.Types","Ext.data.SortTypes"],
    alias:"data.field",
    constructor:function(b){
        if(Ext.isString(b)){
            b={
                name:b
            }
        }
        Ext.apply(this,b);
    var d=Ext.data.Types,a=this.sortType,c;
    if(this.type){
        if(Ext.isString(this.type)){
            this.type=d[this.type.toUpperCase()]||d.AUTO
            }
        }else{
    this.type=d.AUTO
    }
    if(Ext.isString(a)){
    this.sortType=Ext.data.SortTypes[a]
    }else{
    if(Ext.isEmpty(a)){
        this.sortType=this.type.sortType
        }
    }
if(!this.convert){
    this.convert=this.type.convert
    }
},
dateFormat:null,
useNull:false,
defaultValue:"",
mapping:null,
sortType:null,
sortDir:"ASC",
allowBlank:true,
persist:true
});
Ext.define("Ext.Ajax",{
    extend:"Ext.data.Connection",
    singleton:true,
    autoAbort:false
});
Ext.define("Ext.layout.component.Tip",{
    alias:["layout.tip"],
    extend:"Ext.layout.component.Dock",
    type:"tip",
    onLayout:function(b,i){
        var g=this,c=g.owner,d=c.el,a,h,f,e,j=d.getXY();
        d.setXY([-9999,-9999]);
        this.callParent(arguments);
        if(!Ext.isNumber(b)){
            a=c.minWidth;
            h=c.maxWidth;
            if(Ext.isStrict&&(Ext.isIE6||Ext.isIE7)){
                e=g.doAutoWidth()
                }else{
                f=d.getWidth()
                }
                if(f<a){
                e=a
                }else{
                if(f>h){
                    e=h
                    }
                }
            if(e){
            this.callParent([e,i])
            }
        }
    d.setXY(j)
    },
doAutoWidth:function(){
    var d=this,b=d.owner,a=b.body,c=a.getTextWidth();
    if(b.header){
        c=Math.max(c,b.header.getWidth())
        }
        if(!Ext.isDefined(d.frameWidth)){
        d.frameWidth=b.el.getWidth()-a.getWidth()
        }
        c+=d.frameWidth+a.getPadding("lr");
    return c
    }
});
Ext.define("Ext.tip.Tip",{
    extend:"Ext.panel.Panel",
    requires:["Ext.layout.component.Tip"],
    alternateClassName:"Ext.Tip",
    minWidth:40,
    maxWidth:300,
    shadow:"sides",
    defaultAlign:"tl-bl?",
    constrainPosition:true,
    frame:false,
    autoRender:true,
    hidden:true,
    baseCls:Ext.baseCSSPrefix+"tip",
    floating:{
        shadow:true,
        shim:true,
        constrain:true
    },
    focusOnToFront:false,
    componentLayout:"tip",
    closeAction:"hide",
    ariaRole:"tooltip",
    initComponent:function(){
        this.callParent(arguments);
        this.constrain=this.constrain||this.constrainPosition
        },
    showAt:function(b){
        var a=this;
        this.callParent();
        if(a.isVisible()){
            a.setPagePosition(b[0],b[1]);
            if(a.constrainPosition||a.constrain){
                a.doConstrain()
                }
                a.toFront(true)
            }
        },
showBy:function(a,b){
    this.showAt(this.el.getAlignToXY(a,b||this.defaultAlign))
    },
initDraggable:function(){
    var a=this;
    a.draggable={
        el:a.getDragEl(),
        delegate:a.header.el,
        constrain:a,
        constrainTo:a.el.dom.parentNode
        };
        
    Ext.Component.prototype.initDraggable.call(a)
    },
ghost:undefined,
unghost:undefined
});
Ext.define("Ext.tip.ToolTip",{
    extend:"Ext.tip.Tip",
    alias:"widget.tooltip",
    alternateClassName:"Ext.ToolTip",
    showDelay:500,
    hideDelay:200,
    dismissDelay:5000,
    trackMouse:false,
    anchorToTarget:true,
    anchorOffset:0,
    targetCounter:0,
    quickShowInterval:250,
    initComponent:function(){
        var a=this;
        a.callParent(arguments);
        a.lastActive=new Date();
        a.setTarget(a.target);
        a.origAnchor=a.anchor
        },
    onRender:function(b,a){
        var c=this;
        c.callParent(arguments);
        c.anchorCls=Ext.baseCSSPrefix+"tip-anchor-"+c.getAnchorPosition();
        c.anchorEl=c.el.createChild({
            cls:Ext.baseCSSPrefix+"tip-anchor "+c.anchorCls
            })
        },
    afterRender:function(){
        var a=this,b;
        a.callParent(arguments);
        b=parseInt(a.el.getZIndex(),10)||0;
        a.anchorEl.setStyle("z-index",b+1).setVisibilityMode(Ext.core.Element.DISPLAY)
        },
    setTarget:function(d){
        var b=this,a=Ext.get(d),c;
        if(b.target){
            c=Ext.get(b.target);
            b.mun(c,"mouseover",b.onTargetOver,b);
            b.mun(c,"mouseout",b.onTargetOut,b);
            b.mun(c,"mousemove",b.onMouseMove,b)
            }
            b.target=a;
        if(a){
            b.mon(a,{
                freezeEvent:true,
                mouseover:b.onTargetOver,
                mouseout:b.onTargetOut,
                mousemove:b.onMouseMove,
                scope:b
            })
            }
            if(b.anchor){
            b.anchorTarget=b.target
            }
        },
onMouseMove:function(d){
    var b=this,a=b.delegate?d.getTarget(b.delegate):b.triggerElement=true,c;
    if(a){
        b.targetXY=d.getXY();
        if(a===b.triggerElement){
            if(!b.hidden&&b.trackMouse){
                c=b.getTargetXY();
                if(b.constrainPosition){
                    c=b.el.adjustForConstraints(c,b.el.dom.parentNode)
                    }
                    b.setPagePosition(c)
                }
            }else{
        b.hide();
        b.lastActive=new Date(0);
        b.onTargetOver(d)
        }
    }else{
    if((!b.closable&&b.isVisible())&&b.autoHide!==false){
        b.hide()
        }
    }
},
getTargetXY:function(){
    var i=this,d;
    if(i.delegate){
        i.anchorTarget=i.triggerElement
        }
        if(i.anchor){
        i.targetCounter++;
        var c=i.getOffsets(),m=(i.anchorToTarget&&!i.trackMouse)?i.el.getAlignToXY(i.anchorTarget,i.getAnchorAlign()):i.targetXY,a=Ext.core.Element.getViewWidth()-5,h=Ext.core.Element.getViewHeight()-5,k=document.documentElement,e=document.body,l=(k.scrollLeft||e.scrollLeft||0)+5,j=(k.scrollTop||e.scrollTop||0)+5,b=[m[0]+c[0],m[1]+c[1]],g=i.getSize(),f=i.constrainPosition;
        i.anchorEl.removeCls(i.anchorCls);
        if(i.targetCounter<2&&f){
            if(b[0]<l){
                if(i.anchorToTarget){
                    i.defaultAlign="l-r";
                    if(i.mouseOffset){
                        i.mouseOffset[0]*=-1
                        }
                    }
                i.anchor="left";
            return i.getTargetXY()
            }
            if(b[0]+g.width>a){
            if(i.anchorToTarget){
                i.defaultAlign="r-l";
                if(i.mouseOffset){
                    i.mouseOffset[0]*=-1
                    }
                }
            i.anchor="right";
        return i.getTargetXY()
        }
        if(b[1]<j){
        if(i.anchorToTarget){
            i.defaultAlign="t-b";
            if(i.mouseOffset){
                i.mouseOffset[1]*=-1
                }
            }
        i.anchor="top";
    return i.getTargetXY()
    }
    if(b[1]+g.height>h){
    if(i.anchorToTarget){
        i.defaultAlign="b-t";
        if(i.mouseOffset){
            i.mouseOffset[1]*=-1
            }
        }
    i.anchor="bottom";
return i.getTargetXY()
}
}
i.anchorCls=Ext.baseCSSPrefix+"tip-anchor-"+i.getAnchorPosition();
i.anchorEl.addCls(i.anchorCls);
i.targetCounter=0;
return b
}else{
    d=i.getMouseOffset();
    return(i.targetXY)?[i.targetXY[0]+d[0],i.targetXY[1]+d[1]]:d
    }
},
getMouseOffset:function(){
    var a=this,b=a.anchor?[0,0]:[15,18];
    if(a.mouseOffset){
        b[0]+=a.mouseOffset[0];
        b[1]+=a.mouseOffset[1]
        }
        return b
    },
getAnchorPosition:function(){
    var b=this,a;
    if(b.anchor){
        b.tipAnchor=b.anchor.charAt(0)
        }else{
        a=b.defaultAlign.match(/^([a-z]+)-([a-z]+)(\?)?$/);
        b.tipAnchor=a[1].charAt(0)
        }
        switch(b.tipAnchor){
        case"t":
            return"top";
        case"b":
            return"bottom";
        case"r":
            return"right"
            }
            return"left"
    },
getAnchorAlign:function(){
    switch(this.anchor){
        case"top":
            return"tl-bl";
        case"left":
            return"tl-tr";
        case"right":
            return"tr-tl";
        default:
            return"bl-tl"
            }
        },
getOffsets:function(){
    var c=this,d,b,a=c.getAnchorPosition().charAt(0);
    if(c.anchorToTarget&&!c.trackMouse){
        switch(a){
            case"t":
                b=[0,9];
                break;
            case"b":
                b=[0,-13];
                break;
            case"r":
                b=[-13,0];
                break;
            default:
                b=[9,0];
                break
                }
            }else{
    switch(a){
        case"t":
            b=[-15-c.anchorOffset,30];
            break;
        case"b":
            b=[-19-c.anchorOffset,-13-c.el.dom.offsetHeight];
            break;
        case"r":
            b=[-15-c.el.dom.offsetWidth,-13-c.anchorOffset];
            break;
        default:
            b=[25,-13-c.anchorOffset];
            break
            }
        }
d=c.getMouseOffset();
b[0]+=d[0];
b[1]+=d[1];
return b
},
onTargetOver:function(c){
    var b=this,a;
    if(b.disabled||c.within(b.target.dom,true)){
        return
    }
    a=c.getTarget(b.delegate);
    if(a){
        b.triggerElement=a;
        b.clearTimer("hide");
        b.targetXY=c.getXY();
        b.delayShow()
        }
    },
delayShow:function(){
    var a=this;
    if(a.hidden&&!a.showTimer){
        if(Ext.Date.getElapsed(a.lastActive)<a.quickShowInterval){
            a.show()
            }else{
            a.showTimer=Ext.defer(a.show,a.showDelay,a)
            }
        }else{
    if(!a.hidden&&a.autoHide!==false){
        a.show()
        }
    }
},
onTargetOut:function(b){
    var a=this;
    if(a.disabled||b.within(a.target.dom,true)){
        return
    }
    a.clearTimer("show");
    if(a.autoHide!==false){
        a.delayHide()
        }
    },
delayHide:function(){
    var a=this;
    if(!a.hidden&&!a.hideTimer){
        a.hideTimer=Ext.defer(a.hide,a.hideDelay,a)
        }
    },
hide:function(){
    var a=this;
    a.clearTimer("dismiss");
    a.lastActive=new Date();
    if(a.anchorEl){
        a.anchorEl.hide()
        }
        a.callParent(arguments);
    delete a.triggerElement
    },
show:function(){
    var a=this;
    this.callParent();
    if(this.hidden===false){
        a.setPagePosition(-10000,-10000);
        if(a.anchor){
            a.anchor=a.origAnchor
            }
            a.showAt(a.getTargetXY());
        if(a.anchor){
            a.syncAnchor();
            a.anchorEl.show()
            }else{
            a.anchorEl.hide()
            }
        }
},
showAt:function(b){
    var a=this;
    a.lastActive=new Date();
    a.clearTimers();
    if(!a.isVisible()){
        this.callParent(arguments)
        }
        if(a.isVisible()){
        a.setPagePosition(b[0],b[1]);
        if(a.constrainPosition||a.constrain){
            a.doConstrain()
            }
            a.toFront(true)
        }
        if(a.dismissDelay&&a.autoHide!==false){
        a.dismissTimer=Ext.defer(a.hide,a.dismissDelay,a)
        }
        if(a.anchor){
        a.syncAnchor();
        if(!a.anchorEl.isVisible()){
            a.anchorEl.show()
            }
        }else{
    a.anchorEl.hide()
    }
},
syncAnchor:function(){
    var c=this,a,b,d;
    switch(c.tipAnchor.charAt(0)){
        case"t":
            a="b";
            b="tl";
            d=[20+c.anchorOffset,1];
            break;
        case"r":
            a="l";
            b="tr";
            d=[-1,12+c.anchorOffset];
            break;
        case"b":
            a="t";
            b="bl";
            d=[20+c.anchorOffset,-1];
            break;
        default:
            a="r";
            b="tl";
            d=[1,12+c.anchorOffset];
            break
            }
            c.anchorEl.alignTo(c.el,a+"-"+b,d)
    },
setPagePosition:function(a,c){
    var b=this;
    b.callParent(arguments);
    if(b.anchor){
        b.syncAnchor()
        }
    },
clearTimer:function(a){
    a=a+"Timer";
    clearTimeout(this[a]);
    delete this[a]
},
clearTimers:function(){
    var a=this;
    a.clearTimer("show");
    a.clearTimer("dismiss");
    a.clearTimer("hide")
    },
onShow:function(){
    var a=this;
    a.callParent();
    a.mon(Ext.getDoc(),"mousedown",a.onDocMouseDown,a)
    },
onHide:function(){
    var a=this;
    a.callParent();
    a.mun(Ext.getDoc(),"mousedown",a.onDocMouseDown,a)
    },
onDocMouseDown:function(b){
    var a=this;
    if(a.autoHide!==true&&!a.closable&&!b.within(a.el.dom)){
        a.disable();
        Ext.defer(a.doEnable,100,a)
        }
    },
doEnable:function(){
    if(!this.isDestroyed){
        this.enable()
        }
    },
onDisable:function(){
    this.callParent();
    this.clearTimers();
    this.hide()
    },
beforeDestroy:function(){
    var a=this;
    a.clearTimers();
    Ext.destroy(a.anchorEl);
    delete a.anchorEl;
    delete a.target;
    delete a.anchorTarget;
    delete a.triggerElement;
    a.callParent()
    },
onDestroy:function(){
    Ext.getDoc().un("mousedown",this.onDocMouseDown,this);
    this.callParent()
    }
});
Ext.define("Ext.draw.CompositeSprite",{
    extend:"Ext.util.MixedCollection",
    mixins:{
        animate:"Ext.util.Animate"
    },
    isCompositeSprite:true,
    constructor:function(a){
        var b=this;
        a=a||{};
        
        Ext.apply(b,a);
        b.addEvents("mousedown","mouseup","mouseover","mouseout","click");
        b.id=Ext.id(null,"ext-sprite-group-");
        b.callParent()
        },
    onClick:function(a){
        this.fireEvent("click",a)
        },
    onMouseUp:function(a){
        this.fireEvent("mouseup",a)
        },
    onMouseDown:function(a){
        this.fireEvent("mousedown",a)
        },
    onMouseOver:function(a){
        this.fireEvent("mouseover",a)
        },
    onMouseOut:function(a){
        this.fireEvent("mouseout",a)
        },
    attachEvents:function(b){
        var a=this;
        b.on({
            scope:a,
            mousedown:a.onMouseDown,
            mouseup:a.onMouseUp,
            mouseover:a.onMouseOver,
            mouseout:a.onMouseOut,
            click:a.onClick
            })
        },
    add:function(b,c){
        var a=this.callParent(arguments);
        this.attachEvents(a);
        return a
        },
    insert:function(a,b,c){
        return this.callParent(arguments)
        },
    remove:function(b){
        var a=this;
        b.un({
            scope:a,
            mousedown:a.onMouseDown,
            mouseup:a.onMouseUp,
            mouseover:a.onMouseOver,
            mouseout:a.onMouseOut,
            click:a.onClick
            });
        a.callParent(arguments)
        },
    getBBox:function(){
        var e=0,m,h,j=this.items,f=this.length,g=Infinity,c=g,l=-g,b=g,k=-g,d,a;
        for(;e<f;e++){
            m=j[e];
            if(m.el){
                h=m.getBBox();
                c=Math.min(c,h.x);
                b=Math.min(b,h.y);
                l=Math.max(l,h.height+h.y);
                k=Math.max(k,h.width+h.x)
                }
            }
        return{
        x:c,
        y:b,
        height:l-b,
        width:k-c
        }
    },
setAttributes:function(c,e){
    var d=0,b=this.items,a=this.length;
    for(;d<a;d++){
        b[d].setAttributes(c,e)
        }
        return this
    },
hide:function(d){
    var c=0,b=this.items,a=this.length;
    for(;c<a;c++){
        b[c].hide(d)
        }
        return this
    },
show:function(d){
    var c=0,b=this.items,a=this.length;
    for(;c<a;c++){
        b[c].show(d)
        }
        return this
    },
redraw:function(){
    var e=this,d=0,c=e.items,b=e.getSurface(),a=e.length;
    if(b){
        for(;d<a;d++){
            b.renderItem(c[d])
            }
        }
        return e
},
setStyle:function(f){
    var c=0,b=this.items,a=this.length,e,d;
    for(;c<a;c++){
        e=b[c];
        d=e.el;
        if(d){
            d.setStyle(f)
            }
        }
    },
addCls:function(e){
    var d=0,c=this.items,b=this.getSurface(),a=this.length;
    if(b){
        for(;d<a;d++){
            b.addCls(c[d],e)
            }
        }
    },
removeCls:function(e){
    var d=0,c=this.items,b=this.getSurface(),a=this.length;
    if(b){
        for(;d<a;d++){
            b.removeCls(c[d],e)
            }
        }
    },
getSurface:function(){
    var a=this.first();
    if(a){
        return a.surface
        }
        return null
    },
destroy:function(){
    var c=this,a=c.getSurface(),b;
    if(a){
        while(c.getCount()>0){
            b=c.first();
            c.remove(b);
            a.remove(b)
            }
        }
    c.clearListeners()
}
});
Ext.define("Ext.draw.Surface",{
    mixins:{
        observable:"Ext.util.Observable"
    },
    requires:["Ext.draw.CompositeSprite"],
    uses:["Ext.draw.engine.Svg","Ext.draw.engine.Vml"],
    separatorRe:/[, ]+/,
    statics:{
        create:function(b,d){
            d=d||["Svg","Vml"];
            var c=0,a=d.length,e;
            for(;c<a;c++){
                if(Ext.supports[d[c]]){
                    return Ext.create("Ext.draw.engine."+d[c],b)
                    }
                }
            return false
        }
    },
availableAttrs:{
    blur:0,
    "clip-rect":"0 0 1e9 1e9",
    cursor:"default",
    cx:0,
    cy:0,
    "dominant-baseline":"auto",
    fill:"none",
    "fill-opacity":1,
    font:'10px "Arial"',
    "font-family":'"Arial"',
    "font-size":"10",
    "font-style":"normal",
    "font-weight":400,
    gradient:"",
    height:0,
    hidden:false,
    href:"http://sencha.com/",
    opacity:1,
    path:"M0,0",
    radius:0,
    rx:0,
    ry:0,
    scale:"1 1",
    src:"",
    stroke:"#000",
    "stroke-dasharray":"",
    "stroke-linecap":"butt",
    "stroke-linejoin":"butt",
    "stroke-miterlimit":0,
    "stroke-opacity":1,
    "stroke-width":1,
    target:"_blank",
    text:"",
    "text-anchor":"middle",
    title:"Ext Draw",
    width:0,
    x:0,
    y:0,
    zIndex:0
},
container:undefined,
height:352,
width:512,
x:0,
y:0,
constructor:function(a){
    var b=this;
    a=a||{};
    
    Ext.apply(b,a);
    b.domRef=Ext.getDoc().dom;
    b.customAttributes={};
    
    b.addEvents("mousedown","mouseup","mouseover","mouseout","mousemove","mouseenter","mouseleave","click");
    b.mixins.observable.constructor.call(b);
    b.getId();
    b.initGradients();
    b.initItems();
    if(b.renderTo){
        b.render(b.renderTo);
        delete b.renderTo
        }
        b.initBackground(a.background)
    },
initSurface:Ext.emptyFn,
renderItem:Ext.emptyFn,
renderItems:Ext.emptyFn,
setViewBox:Ext.emptyFn,
addCls:Ext.emptyFn,
removeCls:Ext.emptyFn,
setStyle:Ext.emptyFn,
initGradients:function(){
    var a=this.gradients;
    if(a){
        Ext.each(a,this.addGradient,this)
        }
    },
initItems:function(){
    var a=this.items;
    this.items=Ext.create("Ext.draw.CompositeSprite");
    this.groups=Ext.create("Ext.draw.CompositeSprite");
    if(a){
        this.add(a)
        }
    },
initBackground:function(b){
    var e=this,d=e.width,a=e.height,f,g,c;
    if(b){
        if(b.gradient){
            g=b.gradient;
            f=g.id;
            e.addGradient(g);
            e.background=e.add({
                type:"rect",
                x:0,
                y:0,
                width:d,
                height:a,
                fill:"url(#"+f+")"
                })
            }else{
            if(b.fill){
                e.background=e.add({
                    type:"rect",
                    x:0,
                    y:0,
                    width:d,
                    height:a,
                    fill:b.fill
                    })
                }else{
                if(b.image){
                    e.background=e.add({
                        type:"image",
                        x:0,
                        y:0,
                        width:d,
                        height:a,
                        src:b.image
                        })
                    }
                }
        }
}
},
setSize:function(a,b){
    if(this.background){
        this.background.setAttributes({
            width:a,
            height:b,
            hidden:false
        },true)
        }
    },
scrubAttrs:function(d){
    var c,b={},a={},e=d.attr;
    for(c in e){
        if(this.translateAttrs.hasOwnProperty(c)){
            b[this.translateAttrs[c]]=e[c];
            a[this.translateAttrs[c]]=true
            }
            else{
            if(this.availableAttrs.hasOwnProperty(c)&&!a[c]){
                b[c]=e[c]
                }
            }
    }
    return b
},
onClick:function(a){
    this.processEvent("click",a)
    },
onMouseUp:function(a){
    this.processEvent("mouseup",a)
    },
onMouseDown:function(a){
    this.processEvent("mousedown",a)
    },
onMouseOver:function(a){
    this.processEvent("mouseover",a)
    },
onMouseOut:function(a){
    this.processEvent("mouseout",a)
    },
onMouseMove:function(a){
    this.fireEvent("mousemove",a)
    },
onMouseEnter:Ext.emptyFn,
onMouseLeave:Ext.emptyFn,
addGradient:Ext.emptyFn,
add:function(){
    var f=Array.prototype.slice.call(arguments),h,d;
    var a=f.length>1;
    if(a||Ext.isArray(f[0])){
        var g=a?f:f[0],b=[],c,e,j;
        for(c=0,e=g.length;c<e;c++){
            j=g[c];
            j=this.add(j);
            b.push(j)
            }
            return b
        }
        h=this.prepareItems(f[0],true)[0];
    this.normalizeSpriteCollection(h);
    this.onAdd(h);
    return h
    },
normalizeSpriteCollection:function(c){
    var b=this.items,d=c.attr.zIndex,a=b.indexOf(c);
    if(a<0||(a>0&&b.getAt(a-1).attr.zIndex>d)||(a<b.length-1&&b.getAt(a+1).attr.zIndex<d)){
        b.removeAt(a);
        a=b.findIndexBy(function(e){
            return e.attr.zIndex>d
            });
        if(a<0){
            a=b.length
            }
            b.insert(a,c)
        }
        return a
    },
onAdd:function(d){
    var f=d.group,b=d.draggable,a,e,c;
    if(f){
        a=[].concat(f);
        e=a.length;
        for(c=0;c<e;c++){
            f=a[c];
            this.getGroup(f).add(d)
            }
            delete d.group
        }
        if(b){
        d.initDraggable()
        }
    },
remove:function(a,b){
    if(a){
        this.items.remove(a);
        this.groups.each(function(c){
            c.remove(a)
            });
        a.onRemove();
        if(b===true){
            a.destroy()
            }
        }
},
removeAll:function(d){
    var a=this.items.items,c=a.length,b;
    for(b=c-1;b>-1;b--){
        this.remove(a[b],d)
        }
    },
onRemove:Ext.emptyFn,
onDestroy:Ext.emptyFn,
applyTransformations:function(b){
    b.bbox.transform=0;
    this.transform(b);
    var d=this,c=false,a=b.attr;
    if(a.translation.x!=null||a.translation.y!=null){
        d.translate(b);
        c=true
        }
        if(a.scaling.x!=null||a.scaling.y!=null){
        d.scale(b);
        c=true
        }
        if(a.rotation.degrees!=null){
        d.rotate(b);
        c=true
        }
        if(c){
        b.bbox.transform=0;
        this.transform(b);
        b.transformations=[]
        }
    },
rotate:function(a){
    var e,b=a.attr.rotation.degrees,d=a.attr.rotation.x,c=a.attr.rotation.y;
    if(!Ext.isNumber(d)||!Ext.isNumber(c)){
        e=this.getBBox(a);
        d=!Ext.isNumber(d)?e.x+e.width/2:d;
        c=!Ext.isNumber(c)?e.y+e.height/2:c
        }
        a.transformations.push({
        type:"rotate",
        degrees:b,
        x:d,
        y:c
    })
    },
translate:function(b){
    var a=b.attr.translation.x||0,c=b.attr.translation.y||0;
    b.transformations.push({
        type:"translate",
        x:a,
        y:c
    })
    },
scale:function(b){
    var e,a=b.attr.scaling.x||1,f=b.attr.scaling.y||1,d=b.attr.scaling.centerX,c=b.attr.scaling.centerY;
    if(!Ext.isNumber(d)||!Ext.isNumber(c)){
        e=this.getBBox(b);
        d=!Ext.isNumber(d)?e.x+e.width/2:d;
        c=!Ext.isNumber(c)?e.y+e.height/2:c
        }
        b.transformations.push({
        type:"scale",
        x:a,
        y:f,
        centerX:d,
        centerY:c
    })
    },
rectPath:function(a,e,b,c,d){
    if(d){
        return[["M",a+d,e],["l",b-d*2,0],["a",d,d,0,0,1,d,d],["l",0,c-d*2],["a",d,d,0,0,1,-d,d],["l",d*2-b,0],["a",d,d,0,0,1,-d,-d],["l",0,d*2-c],["a",d,d,0,0,1,d,-d],["z"]]
        }
        return[["M",a,e],["l",b,0],["l",0,c],["l",-b,0],["z"]]
    },
ellipsePath:function(a,d,c,b){
    if(b==null){
        b=c
        }
        return[["M",a,d],["m",0,-b],["a",c,b,0,1,1,0,2*b],["a",c,b,0,1,1,0,-2*b],["z"]]
    },
getPathpath:function(a){
    return a.attr.path
    },
getPathcircle:function(c){
    var b=c.attr;
    return this.ellipsePath(b.x,b.y,b.radius,b.radius)
    },
getPathellipse:function(c){
    var b=c.attr;
    return this.ellipsePath(b.x,b.y,b.radiusX||(b.width/2)||0,b.radiusY||(b.height/2)||0)
    },
getPathrect:function(c){
    var b=c.attr;
    return this.rectPath(b.x,b.y,b.width,b.height,b.r)
    },
getPathimage:function(c){
    var b=c.attr;
    return this.rectPath(b.x||0,b.y||0,b.width,b.height)
    },
getPathtext:function(a){
    var b=this.getBBoxText(a);
    return this.rectPath(b.x,b.y,b.width,b.height)
    },
createGroup:function(b){
    var a=this.groups.get(b);
    if(!a){
        a=Ext.create("Ext.draw.CompositeSprite",{
            surface:this
        });
        a.id=b||Ext.id(null,"ext-surface-group-");
        this.groups.add(a)
        }
        return a
    },
getGroup:function(b){
    if(typeof b=="string"){
        var a=this.groups.get(b);
        if(!a){
            a=this.createGroup(b)
            }
        }else{
    a=b
    }
    return a
},
prepareItems:function(a,c){
    a=[].concat(a);
    var e,b,d;
    for(b=0,d=a.length;b<d;b++){
        e=a[b];
        if(!(e instanceof Ext.draw.Sprite)){
            e.surface=this;
            a[b]=this.createItem(e)
            }else{
            e.surface=this
            }
        }
    return a
},
setText:Ext.emptyFn,
createItem:Ext.emptyFn,
getId:function(){
    return this.id||(this.id=Ext.id(null,"ext-surface-"))
    },
destroy:function(){
    delete this.domRef;
    this.removeAll()
    }
});
Ext.define("Ext.draw.Component",{
    alias:"widget.draw",
    extend:"Ext.Component",
    requires:["Ext.draw.Surface","Ext.layout.component.Draw"],
    enginePriority:["Svg","Vml"],
    baseCls:Ext.baseCSSPrefix+"surface",
    componentLayout:"draw",
    viewBox:true,
    autoSize:false,
    initComponent:function(){
        this.callParent(arguments);
        this.addEvents("mousedown","mouseup","mousemove","mouseenter","mouseleave","click")
        },
    onRender:function(){
        var d=this,i=d.viewBox,b=d.autoSize,g,c,a,h,f,e;
        d.callParent(arguments);
        d.createSurface();
        c=d.surface.items;
        if(i||b){
            g=c.getBBox();
            a=g.width;
            h=g.height;
            f=g.x;
            e=g.y;
            if(d.viewBox){
                d.surface.setViewBox(f,e,a,h)
                }else{
                d.autoSizeSurface()
                }
            }
    },
autoSizeSurface:function(){
    var d=this,b=d.surface.items,e=b.getBBox(),c=e.width,a=e.height;
    b.setAttributes({
        translate:{
            x:-e.x,
            y:-e.y+(+Ext.isOpera)
            }
        },true);
if(d.rendered){
    d.setSize(c,a);
    d.surface.setSize(c,a)
    }else{
    d.surface.setSize(c,a)
    }
    d.el.setSize(c,a)
    },
createSurface:function(){
    var a=Ext.draw.Surface.create(Ext.apply({},{
        width:this.width,
        height:this.height,
        renderTo:this.el
        },this.initialConfig));
    this.surface=a;
    function b(c){
        return function(d){
            this.fireEvent(c,d)
            }
        }
    a.on({
    scope:this,
    mouseup:b("mouseup"),
    mousedown:b("mousedown"),
    mousemove:b("mousemove"),
    mouseenter:b("mouseenter"),
    mouseleave:b("mouseleave"),
    click:b("click")
    })
},
onDestroy:function(){
    var a=this.surface;
    if(a){
        a.destroy()
        }
        this.callParent(arguments)
    }
});
Ext.define("Ext.chart.TipSurface",{
    extend:"Ext.draw.Component",
    spriteArray:false,
    renderFirst:true,
    constructor:function(a){
        this.callParent([a]);
        if(a.sprites){
            this.spriteArray=[].concat(a.sprites);
            delete a.sprites
            }
        },
onRender:function(){
    var c=this,b=0,a=0,d,e;
    this.callParent(arguments);
    e=c.spriteArray;
    if(c.renderFirst&&e){
        c.renderFirst=false;
        for(a=e.length;b<a;b++){
            d=c.surface.add(e[b]);
            d.setAttributes({
                hidden:false
            },true)
            }
        }
    }
});
Ext.define("Ext.chart.Tip",{
    requires:["Ext.tip.ToolTip","Ext.chart.TipSurface"],
    constructor:function(b){
        var c=this,a,d,e;
        if(b.tips){
            c.tipTimeout=null;
            c.tipConfig=Ext.apply({},b.tips,{
                renderer:Ext.emptyFn,
                constrainPosition:false
            });
            c.tooltip=Ext.create("Ext.tip.ToolTip",c.tipConfig);
            Ext.getBody().on("mousemove",c.tooltip.onMouseMove,c.tooltip);
            if(c.tipConfig.surface){
                a=c.tipConfig.surface;
                d=a.sprites;
                e=Ext.create("Ext.chart.TipSurface",{
                    id:"tipSurfaceComponent",
                    sprites:d
                });
                if(a.width&&a.height){
                    e.setSize(a.width,a.height)
                    }
                    c.tooltip.add(e);
                c.spriteTip=e
                }
            }
    },
showTip:function(k){
    var e=this;
    if(!e.tooltip){
        return
    }
    clearTimeout(e.tipTimeout);
    var l=e.tooltip,a=e.spriteTip,c=e.tipConfig,d=l.trackMouse,j,b,i,g,h,f;
    if(!d){
        l.trackMouse=true;
        j=k.sprite;
        b=j.surface;
        i=Ext.get(b.getId());
        if(i){
            g=i.getXY();
            h=g[0]+(j.attr.x||0)+(j.attr.translation&&j.attr.translation.x||0);
            f=g[1]+(j.attr.y||0)+(j.attr.translation&&j.attr.translation.y||0);
            l.targetXY=[h,f]
            }
        }
    if(a){
    c.renderer.call(l,k.storeItem,k,a.surface)
    }else{
    c.renderer.call(l,k.storeItem,k)
    }
    l.show();
    l.trackMouse=d
    },
hideTip:function(a){
    var b=this.tooltip;
    if(!b){
        return
    }
    clearTimeout(this.tipTimeout);
    this.tipTimeout=setTimeout(function(){
        b.hide()
        },0)
    }
});
Ext.define("Ext.chart.series.Series",{
    mixins:{
        observable:"Ext.util.Observable",
        labels:"Ext.chart.Label",
        highlights:"Ext.chart.Highlight",
        tips:"Ext.chart.Tip",
        callouts:"Ext.chart.Callout"
    },
    type:null,
    title:null,
    showInLegend:true,
    renderer:function(e,a,c,d,b){
        return c
        },
    shadowAttributes:null,
    triggerAfterDraw:false,
    constructor:function(a){
        var b=this;
        if(a){
            Ext.apply(b,a)
            }
            b.shadowGroups=[];
        b.mixins.labels.constructor.call(b,a);
        b.mixins.highlights.constructor.call(b,a);
        b.mixins.tips.constructor.call(b,a);
        b.mixins.callouts.constructor.call(b,a);
        b.addEvents({
            scope:b,
            itemmouseover:true,
            itemmouseout:true,
            itemmousedown:true,
            itemmouseup:true,
            mouseleave:true,
            afterdraw:true,
            titlechange:true
        });
        b.mixins.observable.constructor.call(b,a);
        b.on({
            scope:b,
            itemmouseover:b.onItemMouseOver,
            itemmouseout:b.onItemMouseOut,
            mouseleave:b.onMouseLeave
            })
        },
    setBBox:function(a){
        var e=this,c=e.chart,b=c.chartBBox,f=a?0:c.maxGutter[0],d=a?0:c.maxGutter[1],g,h;
        g={
            x:b.x,
            y:b.y,
            width:b.width,
            height:b.height
            };
            
        e.clipBox=g;
        h={
            x:(g.x+f)-(c.zoom.x*c.zoom.width),
            y:(g.y+d)-(c.zoom.y*c.zoom.height),
            width:(g.width-(f*2))*c.zoom.width,
            height:(g.height-(d*2))*c.zoom.height
            };
            
        e.bbox=h
        },
    onAnimate:function(b,a){
        var c=this;
        b.stopAnimation();
        if(c.triggerAfterDraw){
            return b.animate(Ext.applyIf(a,c.chart.animate))
            }else{
            c.triggerAfterDraw=true;
            return b.animate(Ext.apply(Ext.applyIf(a,c.chart.animate),{
                listeners:{
                    afteranimate:function(){
                        c.triggerAfterDraw=false;
                        c.fireEvent("afterrender")
                        }
                    }
            }))
    }
},
getGutters:function(){
    return[0,0]
    },
onItemMouseOver:function(b){
    var a=this;
    if(b.series===a){
        if(a.highlight){
            a.highlightItem(b)
            }
            if(a.tooltip){
            a.showTip(b)
            }
        }
},
onItemMouseOut:function(b){
    var a=this;
    if(b.series===a){
        a.unHighlightItem();
        if(a.tooltip){
            a.hideTip(b)
            }
        }
},
onMouseLeave:function(){
    var a=this;
    a.unHighlightItem();
    if(a.tooltip){
        a.hideTip()
        }
    },
getItemForPoint:function(a,h){
    if(!this.items||!this.items.length||this.seriesIsHidden){
        return null
        }
        var f=this,b=f.items,g=f.bbox,e,c,d;
    if(!Ext.draw.Draw.withinBox(a,h,g)){
        return null
        }
        for(c=0,d=b.length;c<d;c++){
        if(b[c]&&this.isItemInPoint(a,h,b[c],c)){
            return b[c]
            }
        }
    return null
},
isItemInPoint:function(a,d,c,b){
    return false
    },
hideAll:function(){
    var f=this,b=f.items,e,a,d,c;
    f.seriesIsHidden=true;
    f._prevShowMarkers=f.showMarkers;
    f.showMarkers=false;
    f.hideLabels(0);
    for(d=0,a=b.length;d<a;d++){
        e=b[d];
        c=e.sprite;
        if(c){
            c.setAttributes({
                hidden:true
            },true)
            }
        }
    },
showAll:function(){
    var a=this,b=a.chart.animate;
    a.chart.animate=false;
    a.seriesIsHidden=false;
    a.showMarkers=a._prevShowMarkers;
    a.drawSeries();
    a.chart.animate=b
    },
getLegendColor:function(a){
    var b=this,d,c;
    if(b.seriesStyle){
        d=b.seriesStyle.fill;
        c=b.seriesStyle.stroke;
        if(d&&d!="none"){
            return d
            }
            return c
        }
        return"#000"
    },
visibleInLegend:function(a){
    var b=this.__excludes;
    if(b){
        return !b[a]
        }
        return !this.seriesIsHidden
    },
setTitle:function(a,d){
    var c=this,b=c.title;
    if(Ext.isString(a)){
        d=a;
        a=0
        }
        if(Ext.isArray(b)){
        b[a]=d
        }else{
        c.title=d
        }
        c.fireEvent("titlechange",d,a)
    }
});
Ext.define("Ext.chart.series.Cartesian",{
    extend:"Ext.chart.series.Series",
    alternateClassName:["Ext.chart.CartesianSeries","Ext.chart.CartesianChart"],
    xField:null,
    yField:null,
    axis:"left"
});
Ext.define("Ext.chart.LegendItem",{
    extend:"Ext.draw.CompositeSprite",
    requires:["Ext.chart.Shape"],
    x:0,
    y:0,
    zIndex:500,
    constructor:function(a){
        this.callParent(arguments);
        this.createLegend(a)
        },
    createLegend:function(r){
        var s=this,h=r.yFieldIndex,k=s.series,a=k.type,l=s.yFieldIndex,d=s.legend,o=s.surface,p=d.x+s.x,m=d.y+s.y,c,j=s.zIndex,b,i,q,e,n=false,g=Ext.apply(k.seriesStyle,k.style);
        function f(t){
            var u=k[t];
            return(Ext.isArray(u)?u[l]:u)
            }
            i=s.add("label",o.add({
            type:"text",
            x:20,
            y:0,
            zIndex:j||0,
            font:d.labelFont,
            text:f("title")||f("yField")
            }));
        if(a==="line"||a==="scatter"){
            if(a==="line"){
                s.add("line",o.add({
                    type:"path",
                    path:"M0.5,0.5L16.5,0.5",
                    zIndex:j,
                    "stroke-width":k.lineWidth,
                    "stroke-linejoin":"round",
                    "stroke-dasharray":k.dash,
                    stroke:g.stroke||"#000",
                    style:{
                        cursor:"pointer"
                    }
                }))
            }
            if(k.showMarkers||a==="scatter"){
            b=Ext.apply(k.markerStyle,k.markerConfig||{});
            s.add("marker",Ext.chart.Shape[b.type](o,{
                fill:b.fill,
                x:8.5,
                y:0.5,
                zIndex:j,
                radius:b.radius||b.size,
                style:{
                    cursor:"pointer"
                }
            }))
        }
    }else{
    s.add("box",o.add({
        type:"rect",
        zIndex:j,
        x:0,
        y:0,
        width:12,
        height:12,
        fill:k.getLegendColor(h),
        style:{
            cursor:"pointer"
        }
    }))
}
s.setAttributes({
    hidden:false
},true);
c=s.getBBox();
q=s.add("mask",o.add({
    type:"rect",
    x:c.x,
    y:c.y,
    width:c.width||20,
    height:c.height||20,
    zIndex:(j||0)+1000,
    fill:"#f00",
    opacity:0,
    style:{
        cursor:"pointer"
    }
}));
s.on("mouseover",function(){
    i.setStyle({
        "font-weight":"bold"
    });
    q.setStyle({
        cursor:"pointer"
    });
    k._index=h;
    k.highlightItem()
    },s);
s.on("mouseout",function(){
    i.setStyle({
        "font-weight":"normal"
    });
    k._index=h;
    k.unHighlightItem()
    },s);
if(!k.visibleInLegend(h)){
    n=true;
    i.setAttributes({
        opacity:0.5
    },true)
    }
    s.on("mousedown",function(){
    if(!n){
        k.hideAll();
        i.setAttributes({
            opacity:0.5
        },true)
        }else{
        k.showAll();
        i.setAttributes({
            opacity:1
        },true)
        }
        n=!n
    },s);
s.updatePosition({
    x:0,
    y:0
})
},
updatePosition:function(c){
    var f=this,a=f.items,e=a.length,b=0,d;
    if(!c){
        c=f.legend
        }
        for(;b<e;b++){
        d=a[b];
        switch(d.type){
            case"text":
                d.setAttributes({
                x:20+c.x+f.x,
                y:c.y+f.y
                },true);
            break;
            case"rect":
                d.setAttributes({
                translate:{
                    x:c.x+f.x,
                    y:c.y+f.y-6
                    }
                },true);
            break;
        default:
            d.setAttributes({
            translate:{
                x:c.x+f.x,
                y:c.y+f.y
                }
            },true)
        }
    }
}
});
Ext.define("Ext.chart.Legend",{
    requires:["Ext.chart.LegendItem"],
    visible:true,
    position:"bottom",
    x:0,
    y:0,
    labelFont:"12px Helvetica, sans-serif",
    boxStroke:"#000",
    boxStrokeWidth:1,
    boxFill:"#FFF",
    itemSpacing:10,
    padding:5,
    width:0,
    height:0,
    boxZIndex:100,
    constructor:function(a){
        var b=this;
        if(a){
            Ext.apply(b,a)
            }
            b.items=[];
        b.isVertical=("left|right|float".indexOf(b.position)!==-1);
        b.origX=b.x;
        b.origY=b.y
        },
    create:function(){
        var a=this;
        a.createItems();
        if(!a.created&&a.isDisplayed()){
            a.createBox();
            a.created=true;
            a.chart.series.each(function(b){
                b.on("titlechange",function(){
                    a.create();
                    a.updatePosition()
                    })
                })
            }
        },
isDisplayed:function(){
    return this.visible&&this.chart.series.findIndex("showInLegend",true)!==-1
    },
createItems:function(){
    var z=this,m=z.chart,r=m.surface,o=z.items,l=z.padding,A=z.itemSpacing,h=2,u=0,q=0,e=0,w=0,b=z.isVertical,d=Math,c=d.floor,B=d.max,g=0,s=0,t=o?o.length:0,k,j,f,v,a,n,p;
    if(t){
        for(;s<t;s++){
            o[s].destroy()
            }
        }
        o.length=[];
m.series.each(function(y,x){
    if(y.showInLegend){
        Ext.each([].concat(y.yField),function(C,i){
            v=Ext.create("Ext.chart.LegendItem",{
                legend:this,
                series:y,
                surface:m.surface,
                yFieldIndex:i
            });
            a=v.getBBox();
            p=a.width;
            n=a.height;
            if(x+i===0){
                f=b?l+n/2:l
                }else{
                f=A/(b?2:1)
                }
                v.x=c(b?l:e+f);
            v.y=c(b?w+f:l+n/2);
            e+=p+f;
            w+=n+f;
            u=B(u,p);
            q=B(q,n);
            o.push(v)
            },this)
        }
    },z);
z.width=c((b?u:e)+l*2);
    if(b&&o.length===1){
    h=1
    }
    z.height=c((b?w-h*f:q)+(l*2));
    z.itemHeight=q
    },
getBBox:function(){
    var a=this;
    return{
        x:Math.round(a.x)-a.boxStrokeWidth/2,
        y:Math.round(a.y)-a.boxStrokeWidth/2,
        width:a.width,
        height:a.height
        }
    },
createBox:function(){
    var b=this,a=b.boxSprite=b.chart.surface.add(Ext.apply({
        type:"rect",
        stroke:b.boxStroke,
        "stroke-width":b.boxStrokeWidth,
        fill:b.boxFill,
        zIndex:b.boxZIndex
        },b.getBBox()));
    a.redraw()
    },
updatePosition:function(){
    var h=this,k,i,m=h.width,l=h.height,j=h.padding,g=h.chart,n=g.chartBBox,b=g.insetPadding,d=n.width-(b*2),c=n.height-(b*2),f=n.x+b,e=n.y+b,a=g.surface,o=Math.floor;
    if(h.isDisplayed()){
        switch(h.position){
            case"left":
                k=b;
                i=o(e+c/2-l/2);
                break;
            case"right":
                k=o(a.width-m)-b;
                i=o(e+c/2-l/2);
                break;
            case"top":
                k=o(f+d/2-m/2);
                i=b;
                break;
            case"bottom":
                k=o(f+d/2-m/2);
                i=o(a.height-l)-b;
                break;
            default:
                k=o(h.origX)+b;
                i=o(h.origY)+b
                }
                h.x=k;
        h.y=i;
        Ext.each(h.items,function(p){
            p.updatePosition()
            });
        h.boxSprite.setAttributes(h.getBBox(),true)
        }
    }
});
Ext.define("Ext.chart.Chart",{
    alias:"widget.chart",
    extend:"Ext.draw.Component",
    mixins:{
        themeManager:"Ext.chart.theme.Theme",
        mask:"Ext.chart.Mask",
        navigation:"Ext.chart.Navigation"
    },
    requires:["Ext.util.MixedCollection","Ext.data.StoreManager","Ext.chart.Legend","Ext.util.DelayedTask"],
    viewBox:false,
    animate:false,
    legend:false,
    insetPadding:10,
    enginePriority:["Svg","Vml"],
    background:false,
    constructor:function(b){
        var c=this,a;
        c.initTheme(b.theme||c.theme);
        if(c.gradients){
            Ext.apply(b,{
                gradients:c.gradients
                })
            }
            if(c.background){
            Ext.apply(b,{
                background:c.background
                })
            }
            if(b.animate){
            a={
                easing:"ease",
                duration:500
            };
            
            if(Ext.isObject(b.animate)){
                b.animate=Ext.applyIf(b.animate,a)
                }else{
                b.animate=a
                }
            }
        c.mixins.mask.constructor.call(c,b);
    c.mixins.navigation.constructor.call(c,b);
    c.callParent([b])
    },
initComponent:function(){
    var b=this,c,a;
    b.callParent();
    b.addEvents("itemmousedown","itemmouseup","itemmouseover","itemmouseout","itemclick","itemdoubleclick","itemdragstart","itemdrag","itemdragend","beforerefresh","refresh");
    Ext.applyIf(b,{
        zoom:{
            width:1,
            height:1,
            x:0,
            y:0
        }
    });
b.maxGutter=[0,0];
b.store=Ext.data.StoreManager.lookup(b.store);
    c=b.axes;
    b.axes=Ext.create("Ext.util.MixedCollection",false,function(d){
    return d.position
    });
if(c){
    b.axes.addAll(c)
    }
    a=b.series;
b.series=Ext.create("Ext.util.MixedCollection",false,function(d){
    return d.seriesId||(d.seriesId=Ext.id(null,"ext-chart-series-"))
    });
if(a){
    b.series.addAll(a)
    }
    if(b.legend!==false){
    b.legend=Ext.create("Ext.chart.Legend",Ext.applyIf({
        chart:b
    },b.legend))
    }
    b.on({
    mousemove:b.onMouseMove,
    mouseleave:b.onMouseLeave,
    mousedown:b.onMouseDown,
    mouseup:b.onMouseUp,
    scope:b
})
},
afterComponentLayout:function(b,a){
    var c=this;
    if(Ext.isNumber(b)&&Ext.isNumber(a)){
        c.curWidth=b;
        c.curHeight=a;
        c.redraw(true)
        }
        this.callParent(arguments)
    },
redraw:function(a){
    var d=this,c=d.chartBBox={
        x:0,
        y:0,
        height:d.curHeight,
        width:d.curWidth
        },b=d.legend;
    d.surface.setSize(c.width,c.height);
    d.series.each(d.initializeSeries,d);
    d.axes.each(d.initializeAxis,d);
    d.axes.each(function(e){
        e.processView()
        });
    d.axes.each(function(e){
        e.drawAxis(true)
        });
    if(b!==false){
        b.create()
        }
        d.alignAxes();
    if(d.legend!==false){
        b.updatePosition()
        }
        d.getMaxGutter();
    d.resizing=!!a;
    d.axes.each(d.drawAxis,d);
    d.series.each(d.drawCharts,d);
    d.resizing=false
    },
afterRender:function(){
    var b,a=this;
    this.callParent();
    if(a.categoryNames){
        a.setCategoryNames(a.categoryNames)
        }
        if(a.tipRenderer){
        b=a.getFunctionRef(a.tipRenderer);
        a.setTipRenderer(b.fn,b.scope)
        }
        a.bindStore(a.store,true);
    a.refresh()
    },
getEventXY:function(d){
    var c=this,b=this.surface.getRegion(),g=d.getXY(),a=g[0]-b.left,f=g[1]-b.top;
    return[a,f]
    },
onClick:function(d){
    var c=this,a=c.getEventXY(d),b;
    c.series.each(function(e){
        if(Ext.draw.Draw.withinBox(a[0],a[1],e.bbox)){
            if(e.getItemForPoint){
                b=e.getItemForPoint(a[0],a[1]);
                if(b){
                    e.fireEvent("itemclick",b)
                    }
                }
        }
    },c)
},
onMouseDown:function(d){
    var c=this,a=c.getEventXY(d),b;
    if(c.mask){
        c.mixins.mask.onMouseDown.call(c,d)
        }
        c.series.each(function(e){
        if(Ext.draw.Draw.withinBox(a[0],a[1],e.bbox)){
            if(e.getItemForPoint){
                b=e.getItemForPoint(a[0],a[1]);
                if(b){
                    e.fireEvent("itemmousedown",b)
                    }
                }
        }
    },c)
},
onMouseUp:function(d){
    var c=this,a=c.getEventXY(d),b;
    if(c.mask){
        c.mixins.mask.onMouseUp.call(c,d)
        }
        c.series.each(function(e){
        if(Ext.draw.Draw.withinBox(a[0],a[1],e.bbox)){
            if(e.getItemForPoint){
                b=e.getItemForPoint(a[0],a[1]);
                if(b){
                    e.fireEvent("itemmouseup",b)
                    }
                }
        }
    },c)
},
onMouseMove:function(h){
    var g=this,a=g.getEventXY(h),f,d,c,b;
    if(g.mask){
        g.mixins.mask.onMouseMove.call(g,h)
        }
        g.series.each(function(e){
        if(Ext.draw.Draw.withinBox(a[0],a[1],e.bbox)){
            if(e.getItemForPoint){
                f=e.getItemForPoint(a[0],a[1]);
                d=e._lastItemForPoint;
                c=e._lastStoreItem;
                b=e._lastStoreField;
                if(f!==d||f&&(f.storeItem!=c||f.storeField!=b)){
                    if(d){
                        e.fireEvent("itemmouseout",d);
                        delete e._lastItemForPoint;
                        delete e._lastStoreField;
                        delete e._lastStoreItem
                        }
                        if(f){
                        e.fireEvent("itemmouseover",f);
                        e._lastItemForPoint=f;
                        e._lastStoreItem=f.storeItem;
                        e._lastStoreField=f.storeField
                        }
                    }
            }
    }else{
    d=e._lastItemForPoint;
    if(d){
        e.fireEvent("itemmouseout",d);
        delete e._lastItemForPoint;
        delete e._lastStoreField;
        delete e._lastStoreItem
        }
    }
},g)
},
onMouseLeave:function(b){
    var a=this;
    if(a.mask){
        a.mixins.mask.onMouseLeave.call(a,b)
        }
        a.series.each(function(c){
        delete c._lastItemForPoint
        })
    },
delayRefresh:function(){
    var a=this;
    if(!a.refreshTask){
        a.refreshTask=Ext.create("Ext.util.DelayedTask",a.refresh,a)
        }
        a.refreshTask.delay(a.refreshBuffer)
    },
refresh:function(){
    var a=this;
    if(a.rendered&&a.curWidth!=undefined&&a.curHeight!=undefined){
        if(a.fireEvent("beforerefresh",a)!==false){
            a.redraw();
            a.fireEvent("refresh",a)
            }
        }
},
bindStore:function(a,b){
    var c=this;
    if(!b&&c.store){
        if(a!==c.store&&c.store.autoDestroy){
            c.store.destroy()
            }else{
            c.store.un("datachanged",c.refresh,c);
            c.store.un("add",c.delayRefresh,c);
            c.store.un("remove",c.delayRefresh,c);
            c.store.un("update",c.delayRefresh,c);
            c.store.un("clear",c.refresh,c)
            }
        }
    if(a){
    a=Ext.data.StoreManager.lookup(a);
    a.on({
        scope:c,
        datachanged:c.refresh,
        add:c.delayRefresh,
        remove:c.delayRefresh,
        update:c.delayRefresh,
        clear:c.refresh
        })
    }
    c.store=a;
if(a&&!b){
    c.refresh()
    }
},
initializeAxis:function(b){
    var e=this,j=e.chartBBox,i=j.width,d=j.height,g=j.x,f=j.y,c=e.themeAttrs,a={
        chart:e
    };
    
    if(c){
        a.axisStyle=Ext.apply({},c.axis);
        a.axisLabelLeftStyle=Ext.apply({},c.axisLabelLeft);
        a.axisLabelRightStyle=Ext.apply({},c.axisLabelRight);
        a.axisLabelTopStyle=Ext.apply({},c.axisLabelTop);
        a.axisLabelBottomStyle=Ext.apply({},c.axisLabelBottom);
        a.axisTitleLeftStyle=Ext.apply({},c.axisTitleLeft);
        a.axisTitleRightStyle=Ext.apply({},c.axisTitleRight);
        a.axisTitleTopStyle=Ext.apply({},c.axisTitleTop);
        a.axisTitleBottomStyle=Ext.apply({},c.axisTitleBottom)
        }
        switch(b.position){
        case"top":
            Ext.apply(a,{
            length:i,
            width:d,
            x:g,
            y:f
        });
        break;
        case"bottom":
            Ext.apply(a,{
            length:i,
            width:d,
            x:g,
            y:d
        });
        break;
        case"left":
            Ext.apply(a,{
            length:d,
            width:i,
            x:g,
            y:d
        });
        break;
        case"right":
            Ext.apply(a,{
            length:d,
            width:i,
            x:i,
            y:d
        });
        break
        }
        if(!b.chart){
        Ext.apply(a,b);
        b=e.axes.replace(Ext.createByAlias("axis."+b.type.toLowerCase(),a))
        }else{
        Ext.apply(b,a)
        }
    },
alignAxes:function(){
    var f=this,g=f.axes,e=f.legend,b=["top","right","bottom","left"],d,c=f.insetPadding,a={
        top:c,
        right:c,
        bottom:c,
        left:c
    };
    
    function h(k){
        var j=g.findIndex("position",k);
        return(j<0)?null:g.getAt(j)
        }
        Ext.each(b,function(j){
        var l=(j==="left"||j==="right"),i=h(j),k;
        if(e!==false){
            if(e.position===j){
                k=e.getBBox();
                a[j]+=(l?k.width:k.height)+a[j]
                }
            }
        if(i&&i.bbox){
        k=i.bbox;
        a[j]+=(l?k.width:k.height)
        }
    });
d={
    x:a.left,
    y:a.top,
    width:f.curWidth-a.left-a.right,
    height:f.curHeight-a.top-a.bottom
    };
    
f.chartBBox=d;
g.each(function(i){
    var k=i.position,j=(k==="left"||k==="right");
    i.x=(k==="right"?d.x+d.width:d.x);
    i.y=(k==="top"?d.y:d.y+d.height);
    i.width=(j?d.width:d.height);
    i.length=(j?d.height:d.width)
    })
},
initializeSeries:function(g,j){
    var h=this,d=h.themeAttrs,c,e,m,o,n,k=[],f=0,b,a={
        chart:h,
        seriesId:g.seriesId
        };
        
    if(d){
        m=d.seriesThemes;
        n=d.markerThemes;
        c=Ext.apply({},d.series);
        e=Ext.apply({},d.marker);
        a.seriesStyle=Ext.apply(c,m[j%m.length]);
        a.seriesLabelStyle=Ext.apply({},d.seriesLabel);
        a.markerStyle=Ext.apply(e,n[j%n.length]);
        if(d.colors){
            a.colorArrayStyle=d.colors
            }else{
            k=[];
            for(b=m.length;f<b;f++){
                o=m[f];
                if(o.fill||o.stroke){
                    k.push(o.fill||o.stroke)
                    }
                }
            if(k.length){
            a.colorArrayStyle=k
            }
        }
    a.seriesIdx=j
}
if(g instanceof Ext.chart.series.Series){
    Ext.apply(g,a)
    }else{
    Ext.applyIf(a,g);
    g=h.series.replace(Ext.createByAlias("series."+g.type.toLowerCase(),a))
    }
    if(g.initialize){
    g.initialize()
    }
},
getMaxGutter:function(){
    var b=this,a=[0,0];
    b.series.each(function(c){
        var d=c.getGutters&&c.getGutters()||[0,0];
        a[0]=Math.max(a[0],d[0]);
        a[1]=Math.max(a[1],d[1])
        });
    b.maxGutter=a
    },
drawAxis:function(a){
    a.drawAxis()
    },
drawCharts:function(a){
    a.triggerafterrender=false;
    a.drawSeries();
    if(!this.animate){
        a.fireEvent("afterrender")
        }
    },
destroy:function(){
    this.surface.destroy();
    this.bindStore(null);
    this.callParent(arguments)
    }
});
Ext.define("Ext.chart.axis.Abstract",{
    requires:["Ext.chart.Chart"],
    constructor:function(a){
        a=a||{};
        
        var b=this,c=a.position||"left";
        c=c.charAt(0).toUpperCase()+c.substring(1);
        a.label=Ext.apply(a["axisLabel"+c+"Style"]||{},a.label||{});
        a.axisTitleStyle=Ext.apply(a["axisTitle"+c+"Style"]||{},a.labelTitle||{});
        Ext.apply(b,a);
        b.fields=[].concat(b.fields);
        this.callParent();
        b.labels=[];
        b.getId();
        b.labelGroup=b.chart.surface.getGroup(b.axisId+"-labels")
        },
    alignment:null,
    grid:false,
    steps:10,
    x:0,
    y:0,
    minValue:0,
    maxValue:0,
    getId:function(){
        return this.axisId||(this.axisId=Ext.id(null,"ext-axis-"))
        },
    processView:Ext.emptyFn,
    drawAxis:Ext.emptyFn,
    addDisplayAndLabels:Ext.emptyFn
    });
Ext.define("Ext.chart.axis.Axis",{
    extend:"Ext.chart.axis.Abstract",
    alternateClassName:"Ext.chart.Axis",
    requires:["Ext.draw.Draw"],
    forceMinMax:false,
    dashSize:3,
    position:"bottom",
    skipFirst:false,
    length:0,
    width:0,
    majorTickSteps:false,
    applyData:Ext.emptyFn,
    calcEnds:function(){
        var v=this,c=Math,x=c.max,u=c.min,e=v.chart.substore||v.chart.store,h=v.chart.series.items,m=v.fields,g=m.length,p=isNaN(v.minimum)?Infinity:v.minimum,t=isNaN(v.maximum)?-Infinity:v.maximum,n=v.prevMin,q=v.prevMax,k=false,w=0,b=[],j,d,s,o,a,f,r;
        for(s=0,o=h.length;!k&&s<o;s++){
            k=k||h[s].stacked;
            b=h[s].__excludes||b
            }
            e.each(function(i){
            if(k){
                if(!isFinite(p)){
                    p=0
                    }
                    for(a=[0,0],s=0;s<g;s++){
                    if(b[s]){
                        continue
                    }
                    f=i.get(m[s]);
                    a[+(f>0)]+=c.abs(f)
                    }
                    t=x(t,-a[0],a[1]);
                p=u(p,-a[0],a[1])
                }else{
                for(s=0;s<g;s++){
                    if(b[s]){
                        continue
                    }
                    value=i.get(m[s]);
                    t=x(t,value);
                    p=u(p,value)
                    }
                }
            });
if(!isFinite(t)){
    t=v.prevMax||0
    }
    if(!isFinite(p)){
    p=v.prevMin||0
    }
    if(p!=t&&(t!=(t>>0))){
    t=(t>>0)+1
    }
    r=Ext.draw.Draw.snapEnds(p,t,v.majorTickSteps!==false?(v.majorTickSteps+1):v.steps);
    j=r.from;
    d=r.to;
    if(v.forceMinMax){
    if(!isNaN(t)){
        r.to=t
        }
        if(!isNaN(p)){
        r.from=p
        }
    }
if(!isNaN(v.maximum)){
    r.to=v.maximum
    }
    if(!isNaN(v.minimum)){
    r.from=v.minimum
    }
    r.step=(r.to-r.from)/(d-j)*r.step;
    if(v.adjustMaximumByMajorUnit){
    r.to+=r.step
    }
    if(v.adjustMinimumByMajorUnit){
    r.from-=r.step
    }
    v.prevMin=p==t?0:p;
v.prevMax=t;
return r
},
drawAxis:function(t){
    var E=this,u,s,h=E.x,g=E.y,C=E.chart.maxGutter[0],B=E.chart.maxGutter[1],e=E.dashSize,A=E.minorTickSteps||0,z=E.minorTickSteps||0,b=E.length,F=E.position,f=[],m=false,c=E.applyData(),d=c.step,v=c.steps,r=c.from,a=c.to,w,q,p,o,n,l,k,D;
    if(E.hidden||isNaN(d)||(r==a)){
        return
    }
    E.from=c.from;
    E.to=c.to;
    if(F=="left"||F=="right"){
        q=Math.floor(h)+0.5;
        o=["M",q,g,"l",0,-b];
        w=b-(B*2)
        }else{
        p=Math.floor(g)+0.5;
        o=["M",h,p,"l",b,0];
        w=b-(C*2)
        }
        D=w/(v||1);
    l=Math.max(A+1,0);
    k=Math.max(z+1,0);
    if(E.type=="Numeric"){
        m=true;
        E.labels=[c.from]
        }
        if(F=="right"||F=="left"){
        p=g-B;
        q=h-((F=="left")*e*2);
        while(p>=g-B-w){
            o.push("M",q,Math.floor(p)+0.5,"l",e*2+1,0);
            if(p!=g-B){
                for(u=1;u<k;u++){
                    o.push("M",q+e,Math.floor(p+D*u/k)+0.5,"l",e+1,0)
                    }
                }
                f.push([Math.floor(h),Math.floor(p)]);
        p-=D;
        if(m){
            E.labels.push(E.labels[E.labels.length-1]+d)
            }
            if(D===0){
            break
        }
    }
    if(Math.round(p+D-(g-B-w))){
    o.push("M",q,Math.floor(g-b+B)+0.5,"l",e*2+1,0);
    for(u=1;u<k;u++){
        o.push("M",q+e,Math.floor(g-b+B+D*u/k)+0.5,"l",e+1,0)
        }
        f.push([Math.floor(h),Math.floor(p)]);
    if(m){
        E.labels.push(E.labels[E.labels.length-1]+d)
        }
    }
}else{
    q=h+C;
    p=g-((F=="top")*e*2);
    while(q<=h+C+w){
        o.push("M",Math.floor(q)+0.5,p,"l",0,e*2+1);
        if(q!=h+C){
            for(u=1;u<l;u++){
                o.push("M",Math.floor(q-D*u/l)+0.5,p,"l",0,e+1)
                }
            }
            f.push([Math.floor(q),Math.floor(g)]);
    q+=D;
    if(m){
        E.labels.push(E.labels[E.labels.length-1]+d)
        }
        if(D===0){
        break
    }
}
if(Math.round(q-D-(h+C+w))){
    o.push("M",Math.floor(h+b-C)+0.5,p,"l",0,e*2+1);
    for(u=1;u<l;u++){
        o.push("M",Math.floor(h+b-C-D*u/l)+0.5,p,"l",0,e+1)
        }
        f.push([Math.floor(q),Math.floor(g)]);
    if(m){
        E.labels.push(E.labels[E.labels.length-1]+d)
        }
    }
}
if(!E.axis){
    E.axis=E.chart.surface.add(Ext.apply({
        type:"path",
        path:o
    },E.axisStyle))
    }
    E.axis.setAttributes({
    path:o
},true);
E.inflections=f;
if(!t&&E.grid){
    E.drawGrid()
    }
    E.axisBBox=E.axis.getBBox();
E.drawLabel()
},
drawGrid:function(){
    var t=this,n=t.chart.surface,b=t.grid,d=b.odd,e=b.even,g=t.inflections,j=g.length-((d||e)?0:1),u=t.position,c=t.chart.maxGutter,m=t.width-2,r=false,o,p,q=1,l=[],f,a,h,k=[],s=[];
    if((c[1]!==0&&(u=="left"||u=="right"))||(c[0]!==0&&(u=="top"||u=="bottom"))){
        q=0;
        j++
    }
    for(;q<j;q++){
        o=g[q];
        p=g[q-1];
        if(d||e){
            l=(q%2)?k:s;
            f=((q%2)?d:e)||{};
            
            a=(f.lineWidth||f["stroke-width"]||0)/2;
            h=2*a;
            if(u=="left"){
                l.push("M",p[0]+1+a,p[1]+0.5-a,"L",p[0]+1+m-a,p[1]+0.5-a,"L",o[0]+1+m-a,o[1]+0.5+a,"L",o[0]+1+a,o[1]+0.5+a,"Z")
                }else{
                if(u=="right"){
                    l.push("M",p[0]-a,p[1]+0.5-a,"L",p[0]-m+a,p[1]+0.5-a,"L",o[0]-m+a,o[1]+0.5+a,"L",o[0]-a,o[1]+0.5+a,"Z")
                    }else{
                    if(u=="top"){
                        l.push("M",p[0]+0.5+a,p[1]+1+a,"L",p[0]+0.5+a,p[1]+1+m-a,"L",o[0]+0.5-a,o[1]+1+m-a,"L",o[0]+0.5-a,o[1]+1+a,"Z")
                        }else{
                        l.push("M",p[0]+0.5+a,p[1]-a,"L",p[0]+0.5+a,p[1]-m+a,"L",o[0]+0.5-a,o[1]-m+a,"L",o[0]+0.5-a,o[1]-a,"Z")
                        }
                    }
            }
    }else{
    if(u=="left"){
        l=l.concat(["M",o[0]+0.5,o[1]+0.5,"l",m,0])
        }else{
        if(u=="right"){
            l=l.concat(["M",o[0]-0.5,o[1]+0.5,"l",-m,0])
            }else{
            if(u=="top"){
                l=l.concat(["M",o[0]+0.5,o[1]+0.5,"l",0,m])
                }else{
                l=l.concat(["M",o[0]+0.5,o[1]-0.5,"l",0,-m])
                }
            }
    }
}
}
if(d||e){
    if(k.length){
        if(!t.gridOdd&&k.length){
            t.gridOdd=n.add({
                type:"path",
                path:k
            })
            }
            t.gridOdd.setAttributes(Ext.apply({
            path:k,
            hidden:false
        },d||{}),true)
        }
        if(s.length){
        if(!t.gridEven){
            t.gridEven=n.add({
                type:"path",
                path:s
            })
            }
            t.gridEven.setAttributes(Ext.apply({
            path:s,
            hidden:false
        },e||{}),true)
        }
    }else{
    if(l.length){
        if(!t.gridLines){
            t.gridLines=t.chart.surface.add({
                type:"path",
                path:l,
                "stroke-width":t.lineWidth||1,
                stroke:t.gridColor||"#ccc"
                })
            }
            t.gridLines.setAttributes({
            hidden:false,
            path:l
        },true)
        }else{
        if(t.gridLines){
            t.gridLines.hide(true)
            }
        }
}
},
getOrCreateLabel:function(c,f){
    var d=this,b=d.labelGroup,e=b.getAt(c),a=d.chart.surface;
    if(e){
        if(f!=e.attr.text){
            e.setAttributes(Ext.apply({
                text:f
            },d.label),true);
            e._bbox=e.getBBox()
            }
        }else{
    e=a.add(Ext.apply({
        group:b,
        type:"text",
        x:0,
        y:0,
        text:f
    },d.label));
    a.renderItem(e);
    e._bbox=e.getBBox()
    }
    if(d.label.rotation){
    e.setAttributes({
        rotation:{
            degrees:0
        }
    },true);
e._ubbox=e.getBBox();
e.setAttributes(d.label,true)
}else{
    e._ubbox=e._bbox
    }
    return e
},
rect2pointArray:function(k){
    var b=this.chart.surface,f=b.getBBox(k,true),l=[f.x,f.y],d=l.slice(),j=[f.x+f.width,f.y],a=j.slice(),i=[f.x+f.width,f.y+f.height],e=i.slice(),h=[f.x,f.y+f.height],c=h.slice(),g=k.matrix;
    l[0]=g.x.apply(g,d);
    l[1]=g.y.apply(g,d);
    j[0]=g.x.apply(g,a);
    j[1]=g.y.apply(g,a);
    i[0]=g.x.apply(g,e);
    i[1]=g.y.apply(g,e);
    h[0]=g.x.apply(g,c);
    h[1]=g.y.apply(g,c);
    return[l,j,i,h]
    },
intersect:function(c,a){
    var d=this.rect2pointArray(c),b=this.rect2pointArray(a);
    return !!Ext.draw.Draw.intersect(d,b).length
    },
drawHorizontalLabels:function(){
    var E=this,c=E.label,z=Math.floor,v=Math.max,w=E.chart.axes,F=E.position,j=E.inflections,n=j.length,D=E.labels,q=E.labelGroup,r=0,f,B=E.chart.maxGutter[1],d,a,t,e,m,A=0,C,s,h,p,g,l,o,k,u,b;
    l=n-1;
    t=j[0];
    b=E.getOrCreateLabel(0,E.label.renderer(D[0]));
    f=Math.abs(Math.sin(c.rotate&&(c.rotate.degrees*Math.PI/180)||0))>>0;
    for(u=0;u<n;u++){
        t=j[u];
        p=E.label.renderer(D[u]);
        C=E.getOrCreateLabel(u,p);
        a=C._bbox;
        r=v(r,a.height+E.dashSize+E.label.padding);
        o=z(t[0]-(f?a.height:a.width)/2);
        if(E.chart.maxGutter[0]==0){
            if(u==0&&w.findIndex("position","left")==-1){
                o=t[0]
                }else{
                if(u==l&&w.findIndex("position","right")==-1){
                    o=t[0]-a.width
                    }
                }
        }
    if(F=="top"){
        k=t[1]-(E.dashSize*2)-E.label.padding-(a.height/2)
        }else{
        k=t[1]+(E.dashSize*2)+E.label.padding+(a.height/2)
        }
        C.setAttributes({
        hidden:false,
        x:o,
        y:k
    },true);
    if(u!=0&&(E.intersect(C,m)||E.intersect(C,b))){
        C.hide(true);
        continue
    }
    m=C
    }
    return r
},
drawVerticalLabels:function(){
    var A=this,f=A.inflections,B=A.position,j=f.length,w=A.labels,t=0,q=Math.max,s=Math.floor,b=Math.ceil,r=A.chart.axes,u=A.chart.maxGutter[1],c,a,o,k,v=0,z,n,e,m,d,h,l,g,p;
    h=j;
    for(p=0;p<h;p++){
        o=f[p];
        m=A.label.renderer(w[p]);
        z=A.getOrCreateLabel(p,m);
        a=z._bbox;
        t=q(t,a.width+A.dashSize+A.label.padding);
        g=o[1];
        if(u<a.height/2){
            if(p==h-1&&r.findIndex("position","top")==-1){
                g=A.y-A.length+b(a.height/2)
                }else{
                if(p==0&&r.findIndex("position","bottom")==-1){
                    g=A.y-s(a.height/2)
                    }
                }
        }
    if(B=="left"){
        l=o[0]-a.width-A.dashSize-A.label.padding-2
        }else{
        l=o[0]+A.dashSize+A.label.padding+2
        }
        z.setAttributes(Ext.apply({
        hidden:false,
        x:l,
        y:g
    },A.label),true);
    if(p!=0&&A.intersect(z,k)){
        z.hide(true);
        continue
    }
    k=z
    }
    return t
},
drawLabel:function(){
    var g=this,a=g.position,b=g.labelGroup,h=g.inflections,f=0,e=0,d,c;
    if(a=="left"||a=="right"){
        f=g.drawVerticalLabels()
        }else{
        e=g.drawHorizontalLabels()
        }
        d=b.getCount();
    c=h.length;
    for(;c<d;c++){
        b.getAt(c).hide(true)
        }
        g.bbox={};
    
    Ext.apply(g.bbox,g.axisBBox);
    g.bbox.height=e;
    g.bbox.width=f;
    if(Ext.isString(g.title)){
        g.drawTitle(f,e)
        }
    },
elipsis:function(d,g,c,e,b){
    var f,a;
    if(c<e){
        d.hide(true);
        return false
        }while(g.length>4){
        g=g.substr(0,g.length-4)+"...";
        d.setAttributes({
            text:g
        },true);
        f=d.getBBox();
        if(f.width<c){
            if(typeof b=="number"){
                d.setAttributes({
                    x:Math.floor(b-(f.width/2))
                    },true)
                }
                break
        }
    }
    return true
},
setTitle:function(a){
    this.title=a;
    this.drawLabel()
    },
drawTitle:function(k,l){
    var g=this,f=g.position,b=g.chart.surface,c=g.displaySprite,j=g.title,e=(f=="left"||f=="right"),i=g.x,h=g.y,a,m,d;
    if(c){
        c.setAttributes({
            text:j
        },true)
        }else{
        a={
            type:"text",
            x:0,
            y:0,
            text:j
        };
        
        c=g.displaySprite=b.add(Ext.apply(a,g.axisTitleStyle,g.labelTitle));
        b.renderItem(c)
        }
        m=c.getBBox();
    d=g.dashSize+g.label.padding;
    if(e){
        h-=((g.length/2)-(m.height/2));
        if(f=="left"){
            i-=(k+d+(m.width/2))
            }else{
            i+=(k+d+m.width-(m.width/2))
            }
            g.bbox.width+=m.width+10
        }else{
        i+=(g.length/2)-(m.width*0.5);
        if(f=="top"){
            h-=(l+d+(m.height*0.3))
            }else{
            h+=(l+d+(m.height*0.8))
            }
            g.bbox.height+=m.height+10
        }
        c.setAttributes({
        translate:{
            x:i,
            y:h
        }
    },true)
}
});
Ext.define("Ext.layout.container.Absolute",{
    alias:"layout.absolute",
    extend:"Ext.layout.container.Anchor",
    requires:["Ext.chart.axis.Axis","Ext.fx.Anim"],
    alternateClassName:"Ext.layout.AbsoluteLayout",
    itemCls:Ext.baseCSSPrefix+"abs-layout-item",
    type:"absolute",
    onLayout:function(){
        var b=this,c=b.getTarget(),a=c.dom===document.body;
        if(!a){
            c.position()
            }
            b.paddingLeft=c.getPadding("l");
        b.paddingTop=c.getPadding("t");
        b.callParent(arguments)
        },
    adjustWidthAnchor:function(b,a){
        return b?b-a.getPosition(true)[0]:b
        },
    adjustHeightAnchor:function(b,a){
        return b?b-a.getPosition(true)[1]:b
        }
    });
Ext.define("Ext.chart.series.Line",{
    extend:"Ext.chart.series.Cartesian",
    alternateClassName:["Ext.chart.LineSeries","Ext.chart.LineChart"],
    requires:["Ext.chart.axis.Axis","Ext.chart.Shape","Ext.draw.Draw","Ext.fx.Anim"],
    type:"line",
    alias:"series.line",
    selectionTolerance:20,
    showMarkers:true,
    markerConfig:{},
    style:{},
    smooth:false,
    defaultSmoothness:3,
    fill:false,
    constructor:function(c){
        this.callParent(arguments);
        var e=this,a=e.chart.surface,f=e.chart.shadow,d,b;
        Ext.apply(e,c,{
            highlightCfg:{
                "stroke-width":3
            },
            shadowAttributes:[{
                "stroke-width":6,
                "stroke-opacity":0.05,
                stroke:"rgb(0, 0, 0)",
                translate:{
                    x:1,
                    y:1
                }
            },{
            "stroke-width":4,
            "stroke-opacity":0.1,
            stroke:"rgb(0, 0, 0)",
            translate:{
                x:1,
                y:1
            }
        },{
            "stroke-width":2,
            "stroke-opacity":0.15,
            stroke:"rgb(0, 0, 0)",
            translate:{
                x:1,
                y:1
            }
        }]
    });
e.group=a.getGroup(e.seriesId);
    if(e.showMarkers){
    e.markerGroup=a.getGroup(e.seriesId+"-markers")
    }
    if(f){
    for(d=0,b=this.shadowAttributes.length;d<b;d++){
        e.shadowGroups.push(a.getGroup(e.seriesId+"-shadows"+d))
        }
    }
},
shrink:function(b,j,k){
    var g=b.length,h=Math.floor(g/k),f=1,d=0,a=0,e=[b[0]],c=[j[0]];
    for(;f<g;++f){
        d+=b[f]||0;
        a+=j[f]||0;
        if(f%h==0){
            e.push(d/h);
            c.push(a/h);
            d=0;
            a=0
            }
        }
    return{
    x:e,
    y:c
}
},
drawSeries:function(){
    var ah=this,av=ah.chart,an=av.substore||av.store,s=av.surface,W=av.chartBBox,al={},P=ah.group,ap=av.maxGutter[0],ao=av.maxGutter[1],I=ah.showMarkers,aA=ah.markerGroup,A=av.shadow,z=ah.shadowGroups,T=ah.shadowAttributes,M=ah.smooth,p=z.length,at=["M"],Q=["M"],H=av.markerIndex,ag=[].concat(ah.axis),ai,af,aw=[],Z=[],O=[],m=true,ak=0,G=false,az=ah.markerStyle,Y=ah.seriesStyle,aC=ah.seriesLabelStyle,r=ah.colorArrayStyle,N=r&&r.length||0,B={
        left:"right",
        right:"left",
        top:"bottom",
        bottom:"top"
    },J=Ext.isNumber,ax=ah.seriesIdx,ab,g,aa,ac,v,c,ad,F,E,f,e,q,S,L,K,au,k,D,C,aB,l,o,w,a,V,ae,u,ar,t,aq,n,ay,am,aj,R,X,U,h,b,d;
    if(!an||!an.getCount()){
        return
    }
    am=Ext.apply(az,ah.markerConfig);
    R=am.type;
    delete am.type;
    aj=Ext.apply(Y,ah.style);
    if(!aj["stroke-width"]){
        aj["stroke-width"]=0.5
        }
        if(H&&aA&&aA.getCount()){
        for(L=0;L<H;L++){
            C=aA.getAt(L);
            aA.remove(C);
            aA.add(C);
            aB=aA.getAt(aA.getCount()-2);
            C.setAttributes({
                x:0,
                y:0,
                translate:{
                    x:aB.attr.translation.x,
                    y:aB.attr.translation.y
                    }
                },true)
        }
        }
    ah.unHighlightItem();
ah.cleanHighlights();
ah.setBBox();
al=ah.bbox;
ah.clipRect=[al.x,al.y,al.width,al.height];
av.axes.each(function(i){
    if(i.position==ah.axis||i.position!=B[ah.axis]){
        ak++;
        if(i.type!="Numeric"){
            m=false;
            return
        }
        m=(m&&i.type=="Numeric");
        if(i){
            D=i.calcEnds();
            if(i.position=="top"||i.position=="bottom"){
                u=D.from;
                ar=D.to
                }else{
                t=D.from;
                aq=D.to
                }
            }
    }
});
if(m&&ak==1){
    m=false
    }
    if(ah.xField&&!J(u)){
    if(ah.axis=="bottom"||ah.axis=="top"){
        k=Ext.create("Ext.chart.axis.Axis",{
            chart:av,
            fields:[].concat(ah.xField)
            }).calcEnds();
        u=k.from;
        ar=k.to
        }else{
        if(m){
            k=Ext.create("Ext.chart.axis.Axis",{
                chart:av,
                fields:[].concat(ah.xField),
                forceMinMax:true
            }).calcEnds();
            u=k.from;
            ar=k.to
            }
        }
}
if(ah.yField&&!J(t)){
    if(ah.axis=="right"||ah.axis=="left"){
        k=Ext.create("Ext.chart.axis.Axis",{
            chart:av,
            fields:[].concat(ah.yField)
            }).calcEnds();
        t=k.from;
        aq=k.to
        }else{
        if(m){
            k=Ext.create("Ext.chart.axis.Axis",{
                chart:av,
                fields:[].concat(ah.yField),
                forceMinMax:true
            }).calcEnds();
            t=k.from;
            aq=k.to
            }
        }
}
if(isNaN(u)){
    u=0;
    V=al.width/(an.getCount()-1)
    }else{
    V=al.width/((ar-u)||(an.getCount()-1))
    }
    if(isNaN(t)){
    t=0;
    ae=al.height/(an.getCount()-1)
    }else{
    ae=al.height/((aq-t)||(an.getCount()-1))
    }
    an.each(function(j,x){
    o=j.get(ah.xField);
    w=j.get(ah.yField);
    if(typeof w=="undefined"||(typeof w=="string"&&!w)){
        return
    }
    if(typeof o=="string"||typeof o=="object"||(ah.axis!="top"&&ah.axis!="bottom"&&!m)){
        o=x
        }
        if(typeof w=="string"||typeof w=="object"||(ah.axis!="left"&&ah.axis!="right"&&!m)){
        w=x
        }
        O.push(x);
    aw.push(o);
    Z.push(w)
    },ah);
au=aw.length;
if(au>al.width){
    a=ah.shrink(aw,Z,al.width);
    aw=a.x;
    Z=a.y
    }
    ah.items=[];
h=0;
au=aw.length;
for(L=0;L<au;L++){
    o=aw[L];
    w=Z[L];
    if(w===false){
        if(Q.length==1){
            Q=[]
            }
            G=true;
        ah.items.push(false);
        continue
    }else{
        F=(al.x+(o-u)*V).toFixed(2);
        E=((al.y+al.height)-(w-t)*ae).toFixed(2);
        if(G){
            G=false;
            Q.push("M")
            }
            Q=Q.concat([F,E])
        }
        if((typeof q=="undefined")&&(typeof E!="undefined")){
        q=E
        }
        if(!ah.line||av.resizing){
        at=at.concat([F,al.y+al.height/2])
        }
        if(av.animate&&av.resizing&&ah.line){
        ah.line.setAttributes({
            path:at
        },true);
        if(ah.fillPath){
            ah.fillPath.setAttributes({
                path:at,
                opacity:0.2
            },true)
            }
            if(ah.line.shadows){
            ab=ah.line.shadows;
            for(K=0,p=ab.length;K<p;K++){
                g=ab[K];
                g.setAttributes({
                    path:at
                },true)
                }
            }
        }
if(I){
    C=aA.getAt(h++);
    if(!C){
        C=Ext.chart.Shape[R](s,Ext.apply({
            group:[P,aA],
            x:0,
            y:0,
            translate:{
                x:f||F,
                y:e||(al.y+al.height/2)
                },
            value:'"'+o+", "+w+'"'
            },am));
        C._to={
            translate:{
                x:F,
                y:E
            }
        }
    }else{
    C.setAttributes({
        value:'"'+o+", "+w+'"',
        x:0,
        y:0,
        hidden:false
    },true);
    C._to={
        translate:{
            x:F,
            y:E
        }
    }
}
}
ah.items.push({
    series:ah,
    value:[o,w],
    point:[F,E],
    sprite:C,
    storeItem:an.getAt(O[L])
    });
f=F;
e=E
}
if(Q.length<=1){
    return
}
if(M){
    b=Ext.draw.Draw.smooth(Q,J(M)?M:ah.defaultSmoothness)
    }
    d=M?b:Q;
if(av.markerIndex&&ah.previousPath){
    ac=ah.previousPath;
    if(!M){
        Ext.Array.erase(ac,1,2)
        }
    }else{
    ac=Q
    }
    if(!ah.line){
    ah.line=s.add(Ext.apply({
        type:"path",
        group:P,
        path:at,
        stroke:aj.stroke||aj.fill
        },aj||{}));
    ah.line.setAttributes({
        fill:"none"
    });
    if(!aj.stroke&&N){
        ah.line.setAttributes({
            stroke:r[ax%N]
            },true)
        }
        if(A){
        ab=ah.line.shadows=[];
        for(aa=0;aa<p;aa++){
            af=T[aa];
            af=Ext.apply({},af,{
                path:at
            });
            g=av.surface.add(Ext.apply({},{
                type:"path",
                group:z[aa]
                },af));
            ab.push(g)
            }
        }
    }
if(ah.fill){
    c=d.concat([["L",F,al.y+al.height],["L",al.x,al.y+al.height],["L",al.x,q]]);
    if(!ah.fillPath){
        ah.fillPath=s.add({
            group:P,
            type:"path",
            opacity:aj.opacity||0.3,
            fill:aj.fill||r[ax%N],
            path:at
        })
        }
    }
S=I&&aA.getCount();
if(av.animate){
    v=ah.fill;
    n=ah.line;
    ad=ah.renderer(n,false,{
        path:d
    },L,an);
    Ext.apply(ad,aj||{},{
        stroke:aj.stroke||aj.fill
        });
    delete ad.fill;
    if(av.markerIndex&&ah.previousPath){
        ah.animation=ay=ah.onAnimate(n,{
            to:ad,
            from:{
                path:ac
            }
        })
    }else{
    ah.animation=ay=ah.onAnimate(n,{
        to:ad
    })
    }
    if(A){
    ab=n.shadows;
    for(K=0;K<p;K++){
        if(av.markerIndex&&ah.previousPath){
            ah.onAnimate(ab[K],{
                to:{
                    path:d
                },
                from:{
                    path:ac
                }
            })
        }else{
        ah.onAnimate(ab[K],{
            to:{
                path:d
            }
        })
    }
    }
}
if(v){
    ah.onAnimate(ah.fillPath,{
        to:Ext.apply({},{
            path:c,
            fill:aj.fill||r[ax%N]
            },aj||{})
        })
    }
    if(I){
    h=0;
    for(L=0;L<au;L++){
        if(ah.items[L]){
            l=aA.getAt(h++);
            if(l){
                ad=ah.renderer(l,an.getAt(L),l._to,L,an);
                ah.onAnimate(l,{
                    to:Ext.apply(ad,am||{})
                    })
                }
            }
    }
    for(;h<S;h++){
    l=aA.getAt(h);
    l.hide(true)
    }
}
}else{
    ad=ah.renderer(ah.line,false,{
        path:d,
        hidden:false
    },L,an);
    Ext.apply(ad,aj||{},{
        stroke:aj.stroke||aj.fill
        });
    delete ad.fill;
    ah.line.setAttributes(ad,true);
    if(A){
        ab=ah.line.shadows;
        for(K=0;K<p;K++){
            ab[K].setAttributes({
                path:d
            },true)
            }
        }
        if(ah.fill){
    ah.fillPath.setAttributes({
        path:c
    },true)
    }
    if(I){
    h=0;
    for(L=0;L<au;L++){
        if(ah.items[L]){
            l=aA.getAt(h++);
            if(l){
                ad=ah.renderer(l,an.getAt(L),l._to,L,an);
                l.setAttributes(Ext.apply(am||{},ad||{}),true)
                }
            }
    }
for(;h<S;h++){
    l=aA.getAt(h);
    l.hide(true)
    }
}
}
if(av.markerIndex){
    if(ah.smooth){
        Ext.Array.erase(Q,1,2)
        }else{
        Ext.Array.splice(Q,1,0,Q[1],Q[2])
        }
        ah.previousPath=Q
    }
    ah.renderLabels();
ah.renderCallouts()
},
onCreateLabel:function(d,j,c,e){
    var f=this,g=f.labelsGroup,a=f.label,h=f.bbox,b=Ext.apply(a,f.seriesLabelStyle);
    return f.chart.surface.add(Ext.apply({
        type:"text",
        "text-anchor":"middle",
        group:g,
        x:j.point[0],
        y:h.y+h.height/2
        },b||{}))
    },
onPlaceLabel:function(f,j,r,o,n,d){
    var t=this,k=t.chart,q=k.resizing,s=t.label,p=s.renderer,b=s.field,a=t.bbox,h=r.point[0],g=r.point[1],c=r.sprite.attr.radius,e,m,l;
    f.setAttributes({
        text:p(j.get(b)),
        hidden:true
    },true);
    if(n=="rotate"){
        f.setAttributes({
            "text-anchor":"start",
            rotation:{
                x:h,
                y:g,
                degrees:-45
            }
        },true);
    e=f.getBBox();
    m=e.width;
    l=e.height;
    h=h<a.x?a.x:h;
    h=(h+m>a.x+a.width)?(h-(h+m-a.x-a.width)):h;
    g=(g-l<a.y)?a.y+l:g
    }else{
    if(n=="under"||n=="over"){
        e=r.sprite.getBBox();
        e.width=e.width||(c*2);
        e.height=e.height||(c*2);
        g=g+(n=="over"?-e.height:e.height);
        e=f.getBBox();
        m=e.width/2;
        l=e.height/2;
        h=h-m<a.x?a.x+m:h;
        h=(h+m>a.x+a.width)?(h-(h+m-a.x-a.width)):h;
        g=g-l<a.y?a.y+l:g;
        g=(g+l>a.y+a.height)?(g-(g+l-a.y-a.height)):g
        }
    }
if(t.chart.animate&&!t.chart.resizing){
    f.show(true);
    t.onAnimate(f,{
        to:{
            x:h,
            y:g
        }
    })
}else{
    f.setAttributes({
        x:h,
        y:g
    },true);
    if(q){
        t.animation.on("afteranimate",function(){
            f.show(true)
            })
        }else{
        f.show(true)
        }
    }
},
highlightItem:function(){
    var a=this;
    a.callParent(arguments);
    if(this.line&&!this.highlighted){
        if(!("__strokeWidth" in this.line)){
            this.line.__strokeWidth=this.line.attr["stroke-width"]||0
            }
            if(this.line.__anim){
            this.line.__anim.paused=true
            }
            this.line.__anim=Ext.create("Ext.fx.Anim",{
            target:this.line,
            to:{
                "stroke-width":this.line.__strokeWidth+3
                }
            });
    this.highlighted=true
    }
},
unHighlightItem:function(){
    var a=this;
    a.callParent(arguments);
    if(this.line&&this.highlighted){
        this.line.__anim=Ext.create("Ext.fx.Anim",{
            target:this.line,
            to:{
                "stroke-width":this.line.__strokeWidth
                }
            });
    this.highlighted=false
    }
},
onPlaceCallout:function(l,q,I,F,E,d,j){
    if(!E){
        return
    }
    var L=this,r=L.chart,C=r.surface,G=r.resizing,K=L.callouts,s=L.items,u=F==0?false:s[F-1].point,w=(F==s.length-1)?false:s[F+1].point,c=[+I.point[0],+I.point[1]],z,f,M,J,n,o,H=K.offsetFromViz||30,B=K.offsetToSide||10,A=K.offsetBox||3,g,e,h,v,t,D=L.clipRect,b={
        width:K.styles.width||10,
        height:K.styles.height||10
        },m,k;
    if(!u){
        u=c
        }
        if(!w){
        w=c
        }
        J=(w[1]-u[1])/(w[0]-u[0]);
    n=(c[1]-u[1])/(c[0]-u[0]);
    o=(w[1]-c[1])/(w[0]-c[0]);
    f=Math.sqrt(1+J*J);
    z=[1/f,J/f];
    M=[-z[1],z[0]];
    if(n>0&&o<0&&M[1]<0||n<0&&o>0&&M[1]>0){
        M[0]*=-1;
        M[1]*=-1
        }else{
        if(Math.abs(n)<Math.abs(o)&&M[0]<0||Math.abs(n)>Math.abs(o)&&M[0]>0){
            M[0]*=-1;
            M[1]*=-1
            }
        }
    m=c[0]+M[0]*H;
k=c[1]+M[1]*H;
g=m+(M[0]>0?0:-(b.width+2*A));
e=k-b.height/2-A;
h=b.width+2*A;
v=b.height+2*A;
if(g<D[0]||(g+h)>(D[0]+D[2])){
    M[0]*=-1
    }
    if(e<D[1]||(e+v)>(D[1]+D[3])){
    M[1]*=-1
    }
    m=c[0]+M[0]*H;
k=c[1]+M[1]*H;
g=m+(M[0]>0?0:-(b.width+2*A));
e=k-b.height/2-A;
h=b.width+2*A;
v=b.height+2*A;
if(r.animate){
    L.onAnimate(l.lines,{
        to:{
            path:["M",c[0],c[1],"L",m,k,"Z"]
            }
        });
if(l.panel){
    l.panel.setPosition(g,e,true)
    }
}else{
    l.lines.setAttributes({
        path:["M",c[0],c[1],"L",m,k,"Z"]
        },true);
    if(l.panel){
        l.panel.setPosition(g,e)
        }
    }
for(t in l){
    l[t].show(true)
    }
},
isItemInPoint:function(h,f,z,p){
    var B=this,m=B.items,r=B.selectionTolerance,j=null,w,c,o,u,g,v,b,s,a,k,A,e,d,n,t,q,C=Math.sqrt,l=Math.abs;
    c=m[p];
    w=p&&m[p-1];
    if(p>=g){
        w=m[g-1]
        }
        o=w&&w.point;
    u=c&&c.point;
    v=w?o[0]:u[0]-r;
    b=w?o[1]:u[1];
    s=c?u[0]:o[0]+r;
    a=c?u[1]:o[1];
    e=C((h-v)*(h-v)+(f-b)*(f-b));
    d=C((h-s)*(h-s)+(f-a)*(f-a));
    n=Math.min(e,d);
    if(n<=r){
        return n==e?w:c
        }
        return false
    },
toggleAll:function(a){
    var e=this,b,d,f,c;
    if(!a){
        Ext.chart.series.Line.superclass.hideAll.call(e)
        }else{
        Ext.chart.series.Line.superclass.showAll.call(e)
        }
        if(e.line){
        e.line.setAttributes({
            hidden:!a
            },true);
        if(e.line.shadows){
            for(b=0,c=e.line.shadows,d=c.length;b<d;b++){
                f=c[b];
                f.setAttributes({
                    hidden:!a
                    },true)
                }
            }
        }
if(e.fillPath){
    e.fillPath.setAttributes({
        hidden:!a
        },true)
    }
},
hideAll:function(){
    this.toggleAll(false)
    },
showAll:function(){
    this.toggleAll(true)
    }
});
Ext.define("Ext.chart.axis.Numeric",{
    extend:"Ext.chart.axis.Axis",
    alternateClassName:"Ext.chart.NumericAxis",
    type:"numeric",
    alias:"axis.numeric",
    constructor:function(c){
        var d=this,a=!!(c.label&&c.label.renderer),b;
        d.callParent([c]);
        b=d.label;
        if(d.roundToDecimal===false){
            return
        }
        if(!a){
            b.renderer=function(e){
                return d.roundToDecimal(e,d.decimals)
                }
            }
    },
roundToDecimal:function(a,c){
    var b=Math.pow(10,c||0);
    return((a*b)>>0)/b
    },
minimum:NaN,
maximum:NaN,
decimals:2,
scale:"linear",
position:"left",
adjustMaximumByMajorUnit:false,
adjustMinimumByMajorUnit:false,
applyData:function(){
    this.callParent();
    return this.calcEnds()
    }
});
Ext.define("Ext.selection.Model",{
    extend:"Ext.util.Observable",
    alternateClassName:"Ext.AbstractSelectionModel",
    requires:["Ext.data.StoreManager"],
    allowDeselect:false,
    selected:null,
    pruneRemoved:true,
    constructor:function(a){
        var b=this;
        a=a||{};
        
        Ext.apply(b,a);
        b.addEvents("selectionchange");
        b.modes={
            SINGLE:true,
            SIMPLE:true,
            MULTI:true
        };
        
        b.setSelectionMode(a.mode||b.mode);
        b.selected=Ext.create("Ext.util.MixedCollection");
        b.callParent(arguments)
        },
    bind:function(a,b){
        var c=this;
        if(!b&&c.store){
            if(a!==c.store&&c.store.autoDestroy){
                c.store.destroy()
                }else{
                c.store.un("add",c.onStoreAdd,c);
                c.store.un("clear",c.onStoreClear,c);
                c.store.un("remove",c.onStoreRemove,c);
                c.store.un("update",c.onStoreUpdate,c)
                }
            }
        if(a){
        a=Ext.data.StoreManager.lookup(a);
        a.on({
            add:c.onStoreAdd,
            clear:c.onStoreClear,
            remove:c.onStoreRemove,
            update:c.onStoreUpdate,
            scope:c
        })
        }
        c.store=a;
    if(a&&!b){
        c.refresh()
        }
    },
selectAll:function(b){
    var e=this,d=e.store.getRange(),c=0,a=d.length,f=e.getSelection().length;
    e.bulkChange=true;
    for(;c<a;c++){
        e.doSelect(d[c],true,b)
        }
        delete e.bulkChange;
    e.maybeFireSelectionChange(e.getSelection().length!==f)
    },
deselectAll:function(b){
    var e=this,d=e.getSelection(),c=0,a=d.length,f=e.getSelection().length;
    e.bulkChange=true;
    for(;c<a;c++){
        e.doDeselect(d[c],b)
        }
        delete e.bulkChange;
    e.maybeFireSelectionChange(e.getSelection().length!==f)
    },
selectWithEvent:function(a,d,c){
    var b=this;
    switch(b.selectionMode){
        case"MULTI":
            if(d.ctrlKey&&b.isSelected(a)){
            b.doDeselect(a,false)
            }else{
            if(d.shiftKey&&b.lastFocused){
                b.selectRange(b.lastFocused,a,d.ctrlKey)
                }else{
                if(d.ctrlKey){
                    b.doSelect(a,true,false)
                    }else{
                    if(b.isSelected(a)&&!d.shiftKey&&!d.ctrlKey&&b.selected.getCount()>1){
                        b.doSelect(a,c,false)
                        }else{
                        b.doSelect(a,false)
                        }
                    }
            }
        }
    break;
case"SIMPLE":
    if(b.isSelected(a)){
    b.doDeselect(a)
    }else{
    b.doSelect(a,true)
    }
    break;
case"SINGLE":
    if(b.allowDeselect&&b.isSelected(a)){
    b.doDeselect(a)
    }else{
    b.doSelect(a,false)
    }
    break
}
},
selectRange:function(k,e,l,c){
    var h=this,j=h.store,d=0,g,f,a,b=[];
    if(h.isLocked()){
        return
    }
    if(!l){
        h.deselectAll(true)
        }
        if(!Ext.isNumber(k)){
        k=j.indexOf(k)
        }
        if(!Ext.isNumber(e)){
        e=j.indexOf(e)
        }
        if(k>e){
        f=e;
        e=k;
        k=f
        }
        for(g=k;g<=e;g++){
        if(h.isSelected(j.getAt(g))){
            d++
        }
    }
    if(!c){
    a=-1
    }else{
    a=(c=="up")?k:e
    }
    for(g=k;g<=e;g++){
    if(d==(e-k+1)){
        if(g!=a){
            h.doDeselect(g,true)
            }
        }else{
    b.push(j.getAt(g))
    }
}
h.doMultiSelect(b,true)
},
select:function(b,c,a){
    this.doSelect(b,c,a)
    },
deselect:function(b,a){
    this.doDeselect(b,a)
    },
doSelect:function(c,e,b){
    var d=this,a;
    if(d.locked){
        return
    }
    if(typeof c==="number"){
        c=[d.store.getAt(c)]
        }
        if(d.selectionMode=="SINGLE"&&c){
        a=c.length?c[0]:c;
        d.doSingleSelect(a,b)
        }else{
        d.doMultiSelect(c,e,b)
        }
    },
doMultiSelect:function(a,k,j){
    var g=this,b=g.selected,h=false,d=0,f,e;
    if(g.locked){
        return
    }
    a=!Ext.isArray(a)?[a]:a;
    f=a.length;
    if(!k&&b.getCount()>0){
        if(g.doDeselect(g.getSelection(),j)===false){
            return
        }
    }
    function c(){
    b.add(e);
    h=true
    }
    for(;d<f;d++){
    e=a[d];
    if(k&&g.isSelected(e)){
        continue
    }
    g.lastSelected=e;
    g.onSelectChange(e,true,j,c)
    }
    g.setLastFocused(e,j);
g.maybeFireSelectionChange(h&&!j)
},
doDeselect:function(a,j){
    var h=this,b=h.selected,d=0,g,e,k=0,f=0;
    if(h.locked){
        return false
        }
        if(typeof a==="number"){
        a=[h.store.getAt(a)]
        }else{
        if(!Ext.isArray(a)){
            a=[a]
            }
        }
    function c(){
    ++f;
    b.remove(e)
    }
    g=a.length;
for(;d<g;d++){
    e=a[d];
    if(h.isSelected(e)){
        if(h.lastSelected==e){
            h.lastSelected=b.last()
            }
            ++k;
        h.onSelectChange(e,false,j,c)
        }
    }
h.maybeFireSelectionChange(f>0&&!j);
return f===k
},
doSingleSelect:function(a,b){
    var d=this,f=false,c=d.selected;
    if(d.locked){
        return
    }
    if(d.isSelected(a)){
        return
    }
    function e(){
        d.bulkChange=true;
        if(c.getCount()>0&&d.doDeselect(d.lastSelected,b)===false){
            delete d.bulkChange;
            return false
            }
            delete d.bulkChange;
        c.add(a);
        d.lastSelected=a;
        f=true
        }
        d.onSelectChange(a,true,b,e);
    if(f){
        if(!b){
            d.setLastFocused(a)
            }
            d.maybeFireSelectionChange(!b)
        }
    },
setLastFocused:function(c,b){
    var d=this,a=d.lastFocused;
    d.lastFocused=c;
    d.onLastFocusChanged(a,c,b)
    },
isFocused:function(a){
    return a===this.getLastFocused()
    },
maybeFireSelectionChange:function(a){
    var b=this;
    if(a&&!b.bulkChange){
        b.fireEvent("selectionchange",b,b.getSelection())
        }
    },
getLastSelected:function(){
    return this.lastSelected
    },
getLastFocused:function(){
    return this.lastFocused
    },
getSelection:function(){
    return this.selected.getRange()
    },
getSelectionMode:function(){
    return this.selectionMode
    },
setSelectionMode:function(a){
    a=a?a.toUpperCase():"SINGLE";
    this.selectionMode=this.modes[a]?a:"SINGLE"
    },
isLocked:function(){
    return this.locked
    },
setLocked:function(a){
    this.locked=!!a
    },
isSelected:function(a){
    a=Ext.isNumber(a)?this.store.getAt(a):a;
    return this.selected.indexOf(a)!==-1
    },
hasSelection:function(){
    return this.selected.getCount()>0
    },
refresh:function(){
    var g=this,b=[],f=g.getSelection(),a=f.length,e,h,d=0,c=this.getLastFocused();
    for(;d<a;d++){
        e=f[d];
        if(!this.pruneRemoved||g.store.indexOf(e)!==-1){
            b.push(e)
            }
        }
    if(g.selected.getCount()!=b.length){
    h=true
    }
    g.clearSelections();
if(g.store.indexOf(c)!==-1){
    this.setLastFocused(c,true)
    }
    if(b.length){
    g.doSelect(b,false,true)
    }
    g.maybeFireSelectionChange(h)
},
clearSelections:function(){
    this.selected.clear();
    this.lastSelected=null;
    this.setLastFocused(null)
    },
onStoreAdd:function(){},
onStoreClear:function(){
    if(this.selected.getCount>0){
        this.clearSelections();
        this.maybeFireSelectionChange(true)
        }
    },
onStoreRemove:function(b,a){
    var d=this,c=d.selected;
    if(d.locked||!d.pruneRemoved){
        return
    }
    if(c.remove(a)){
        if(d.lastSelected==a){
            d.lastSelected=null
            }
            if(d.getLastFocused()==a){
            d.setLastFocused(null)
            }
            d.maybeFireSelectionChange(true)
        }
    },
getCount:function(){
    return this.selected.getCount()
    },
destroy:function(){},
onStoreUpdate:function(){},
onSelectChange:function(a,c,b){},
onLastFocusChanged:function(b,a){},
onEditorKey:function(b,a){},
bindComponent:function(a){}
});
Ext.define("Ext.selection.DataViewModel",{
    extend:"Ext.selection.Model",
    requires:["Ext.util.KeyNav"],
    deselectOnContainerClick:true,
    enableKeyNav:true,
    constructor:function(a){
        this.addEvents("beforedeselect","beforeselect","deselect","select");
        this.callParent(arguments)
        },
    bindComponent:function(a){
        var b=this,c={
            refresh:b.refresh,
            scope:b
        };
        
        b.view=a;
        b.bind(a.getStore());
        a.on(a.triggerEvent,b.onItemClick,b);
        a.on(a.triggerCtEvent,b.onContainerClick,b);
        a.on(c);
        if(b.enableKeyNav){
            b.initKeyNav(a)
            }
        },
onItemClick:function(b,a,d,c,f){
    this.selectWithEvent(a,f)
    },
onContainerClick:function(){
    if(this.deselectOnContainerClick){
        this.deselectAll()
        }
    },
initKeyNav:function(a){
    var b=this;
    if(!a.rendered){
        a.on("render",Ext.Function.bind(b.initKeyNav,b,[a],0),b,{
            single:true
        });
        return
    }
    a.el.set({
        tabIndex:-1
    });
    b.keyNav=Ext.create("Ext.util.KeyNav",a.el,{
        down:Ext.pass(b.onNavKey,[1],b),
        right:Ext.pass(b.onNavKey,[1],b),
        left:Ext.pass(b.onNavKey,[-1],b),
        up:Ext.pass(b.onNavKey,[-1],b),
        scope:b
    })
    },
onNavKey:function(f){
    f=f||1;
    var e=this,b=e.view,d=e.getSelection()[0],c=e.view.store.getCount(),a;
    if(d){
        a=b.indexOf(b.getNode(d))+f
        }else{
        a=0
        }
        if(a<0){
        a=c-1
        }else{
        if(a>=c){
            a=0
            }
        }
    e.select(a)
    },
onSelectChange:function(b,e,d,g){
    var f=this,a=f.view,c=e?"select":"deselect";
    if((d||f.fireEvent("before"+c,f,b))!==false&&g()!==false){
        if(e){
            a.onItemSelect(b)
            }else{
            a.onItemDeselect(b)
            }
            if(!d){
            f.fireEvent(c,f,b)
            }
        }
}
});
Ext.define("Ext.view.AbstractView",{
    extend:"Ext.Component",
    alternateClassName:"Ext.view.AbstractView",
    requires:["Ext.LoadMask","Ext.data.StoreManager","Ext.CompositeElementLite","Ext.DomQuery","Ext.selection.DataViewModel"],
    inheritableStatics:{
        getRecord:function(a){
            return this.getBoundView(a).getRecord(a)
            },
        getBoundView:function(a){
            return Ext.getCmp(a.boundView)
            }
        },
itemCls:Ext.baseCSSPrefix+"dataview-item",
loadingText:"Loading...",
loadMask:true,
loadingUseMsg:true,
selectedItemCls:Ext.baseCSSPrefix+"item-selected",
emptyText:"",
deferEmptyText:true,
trackOver:false,
blockRefresh:false,
last:false,
triggerEvent:"itemclick",
triggerCtEvent:"containerclick",
addCmpEvents:function(){},
    initComponent:function(){
    var c=this,a=Ext.isDefined,d=c.itemTpl,b={};
    
    if(d){
        if(Ext.isArray(d)){
            d=d.join("")
            }else{
            if(Ext.isObject(d)){
                b=Ext.apply(b,d.initialConfig);
                d=d.html
                }
            }
        if(!c.itemSelector){
        c.itemSelector="."+c.itemCls
        }
        d=Ext.String.format('<tpl for="."><div class="{0}">{1}</div></tpl>',c.itemCls,d);
    c.tpl=Ext.create("Ext.XTemplate",d,b)
    }
    c.callParent();
    if(Ext.isString(c.tpl)||Ext.isArray(c.tpl)){
    c.tpl=Ext.create("Ext.XTemplate",c.tpl)
    }
    c.addEvents("beforerefresh","refresh","itemupdate","itemadd","itemremove");
    c.addCmpEvents();
    if(c.store){
    c.store=Ext.data.StoreManager.lookup(c.store)
    }
    c.all=new Ext.CompositeElementLite()
    },
onRender:function(){
    var c=this,b=c.loadMask,a={
        msg:c.loadingText,
        msgCls:c.loadingCls,
        useMsg:c.loadingUseMsg
        };
        
    c.callParent(arguments);
    if(b){
        if(Ext.isObject(b)){
            a=Ext.apply(a,b)
            }
            c.loadMask=Ext.create("Ext.LoadMask",c.floating?c:c.ownerCt||c,a);
        c.loadMask.on({
            scope:c,
            beforeshow:c.onMaskBeforeShow,
            hide:c.onMaskHide
            })
        }
    },
onMaskBeforeShow:function(){
    var a=this;
    a.getSelectionModel().deselectAll();
    a.all.clear();
    if(a.loadingHeight){
        a.setCalculatedSize(undefined,a.loadingHeight)
        }
    },
onMaskHide:function(){
    if(!this.destroying&&this.loadingHeight){
        this.setHeight(this.height)
        }
    },
afterRender:function(){
    this.callParent(arguments);
    this.getSelectionModel().bindComponent(this)
    },
getSelectionModel:function(){
    var a=this,b="SINGLE";
    if(!a.selModel){
        a.selModel={}
    }
    if(a.simpleSelect){
    b="SIMPLE"
    }else{
    if(a.multiSelect){
        b="MULTI"
        }
    }
Ext.applyIf(a.selModel,{
    allowDeselect:a.allowDeselect,
    mode:b
});
if(!a.selModel.events){
    a.selModel=Ext.create("Ext.selection.DataViewModel",a.selModel)
    }
    if(!a.selModel.hasRelaySetup){
    a.relayEvents(a.selModel,["selectionchange","beforeselect","beforedeselect","select","deselect"]);
    a.selModel.hasRelaySetup=true
    }
    if(a.disableSelection){
    a.selModel.locked=true
    }
    return a.selModel
},
refresh:function(){
    var c=this,b,a;
    if(!c.rendered){
        return
    }
    c.fireEvent("beforerefresh",c);
    b=c.getTargetEl();
    a=c.store.getRange();
    b.update("");
    if(a.length<1){
        if(!c.deferEmptyText||c.hasSkippedEmptyText){
            b.update(c.emptyText)
            }
            c.all.clear()
        }else{
        c.tpl.overwrite(b,c.collectData(a,0));
        c.all.fill(Ext.query(c.getItemSelector(),b.dom));
        c.updateIndexes(0)
        }
        c.selModel.refresh();
    c.hasSkippedEmptyText=true;
    c.fireEvent("refresh",c)
    },
prepareData:function(c,b,a){
    if(a){
        Ext.apply(c,a.getAssociatedData())
        }
        return c
    },
collectData:function(c,f){
    var e=[],d=0,a=c.length,b;
    for(;d<a;d++){
        b=c[d];
        e[e.length]=this.prepareData(b[b.persistenceProperty],f+d,b)
        }
        return e
    },
bufferRender:function(a,b){
    var c=document.createElement("div");
    this.tpl.overwrite(c,this.collectData(a,b));
    return Ext.query(this.getItemSelector(),c)
    },
onUpdate:function(e,a){
    var d=this,b=d.store.indexOf(a),c;
    if(b>-1){
        c=d.bufferRender([a],b)[0];
        d.all.replaceElement(b,c,true);
        d.updateIndexes(b,b);
        d.selModel.refresh();
        d.fireEvent("itemupdate",a,b,c)
        }
    },
onAdd:function(e,b,c){
    var d=this,a;
    if(d.all.getCount()===0){
        d.refresh();
        return
    }
    a=d.bufferRender(b,c);
    d.doAdd(a,b,c);
    d.selModel.refresh();
    d.updateIndexes(c);
    d.fireEvent("itemadd",b,c,a)
    },
doAdd:function(b,a,c){
    var d=this.all;
    if(c<d.getCount()){
        d.item(c).insertSibling(b,"before",true)
        }else{
        d.last().insertSibling(b,"after",true)
        }
        Ext.Array.insert(d.elements,c,b)
    },
onRemove:function(d,a,b){
    var c=this;
    c.doRemove(a,b);
    c.updateIndexes(b);
    if(c.store.getCount()===0){
        c.refresh()
        }
        c.fireEvent("itemremove",a,b)
    },
doRemove:function(a,b){
    this.all.removeElement(b,true)
    },
refreshNode:function(a){
    this.onUpdate(this.store,this.store.getAt(a))
    },
updateIndexes:function(e,d){
    var c=this.all.elements,a=this.store.getRange();
    e=e||0;
    d=d||((d===0)?0:(c.length-1));
    for(var b=e;b<=d;b++){
        c[b].viewIndex=b;
        c[b].viewRecordId=a[b].internalId;
        if(!c[b].boundView){
            c[b].boundView=this.id
            }
        }
    },
getStore:function(){
    return this.store
    },
bindStore:function(a,b){
    var c=this;
    if(!b&&c.store){
        if(a!==c.store&&c.store.autoDestroy){
            c.store.destroy()
            }else{
            c.mun(c.store,{
                scope:c,
                datachanged:c.onDataChanged,
                add:c.onAdd,
                remove:c.onRemove,
                update:c.onUpdate,
                clear:c.refresh
                })
            }
            if(!a){
            if(c.loadMask){
                c.loadMask.bindStore(null)
                }
                c.store=null
            }
        }
    if(a){
    a=Ext.data.StoreManager.lookup(a);
    c.mon(a,{
        scope:c,
        datachanged:c.onDataChanged,
        add:c.onAdd,
        remove:c.onRemove,
        update:c.onUpdate,
        clear:c.refresh
        });
    if(c.loadMask){
        c.loadMask.bindStore(a)
        }
    }
c.store=a;
c.getSelectionModel().bind(a);
if(a&&(!b||a.getCount())){
    c.refresh(true)
    }
},
onDataChanged:function(){
    if(this.blockRefresh!==true){
        this.refresh.apply(this,arguments)
        }
    },
findItemByChild:function(a){
    return Ext.fly(a).findParent(this.getItemSelector(),this.getTargetEl())
    },
findTargetByEvent:function(a){
    return a.getTarget(this.getItemSelector(),this.getTargetEl())
    },
getSelectedNodes:function(){
    var b=[],a=this.selModel.getSelection(),d=a.length,c=0;
    for(;c<d;c++){
        b.push(this.getNode(a[c]))
        }
        return b
    },
getRecords:function(c){
    var b=[],d=0,a=c.length,e=this.store.data;
    for(;d<a;d++){
        b[b.length]=e.getByKey(c[d].viewRecordId)
        }
        return b
    },
getRecord:function(a){
    return this.store.data.getByKey(Ext.getDom(a).viewRecordId)
    },
isSelected:function(b){
    var a=this.getRecord(b);
    return this.selModel.isSelected(a)
    },
select:function(b,c,a){
    this.selModel.select(b,c,a)
    },
deselect:function(b,a){
    this.selModel.deselect(b,a)
    },
getNode:function(a){
    if(Ext.isString(a)){
        return document.getElementById(a)
        }else{
        if(Ext.isNumber(a)){
            return this.all.elements[a]
            }else{
            if(a instanceof Ext.data.Model){
                return this.getNodeByRecord(a)
                }
            }
    }
return a
},
getNodeByRecord:function(a){
    var c=this.all.elements,d=c.length,b=0;
    for(;b<d;b++){
        if(c[b].viewRecordId===a.internalId){
            return c[b]
            }
        }
    return null
},
getNodes:function(e,a){
    var d=this.all.elements,b=[],c;
    e=e||0;
    a=!Ext.isDefined(a)?Math.max(d.length-1,0):a;
    if(e<=a){
        for(c=e;c<=a&&d[c];c++){
            b.push(d[c])
            }
        }else{
    for(c=e;c>=a&&d[c];c--){
        b.push(d[c])
        }
    }
    return b
},
indexOf:function(a){
    a=this.getNode(a);
    if(Ext.isNumber(a.viewIndex)){
        return a.viewIndex
        }
        return this.all.indexOf(a)
    },
onDestroy:function(){
    var a=this;
    a.all.clear();
    a.callParent();
    a.bindStore(null);
    a.selModel.destroy()
    },
onItemSelect:function(a){
    var b=this.getNode(a);
    Ext.fly(b).addCls(this.selectedItemCls)
    },
onItemDeselect:function(a){
    var b=this.getNode(a);
    Ext.fly(b).removeCls(this.selectedItemCls)
    },
getItemSelector:function(){
    return this.itemSelector
    }
},function(){
    Ext.deprecate("extjs","4.0",function(){
        Ext.view.AbstractView.override({
            getSelectionCount:function(){
                if(Ext.global.console){
                    Ext.global.console.warn("DataView: getSelectionCount will be removed, please interact with the Ext.selection.DataViewModel")
                    }
                    return this.selModel.getSelection().length
                },
            getSelectedRecords:function(){
                if(Ext.global.console){
                    Ext.global.console.warn("DataView: getSelectedRecords will be removed, please interact with the Ext.selection.DataViewModel")
                    }
                    return this.selModel.getSelection()
                },
            select:function(a,b,d){
                if(Ext.global.console){
                    Ext.global.console.warn("DataView: select will be removed, please access select through a DataView's SelectionModel, ie: view.getSelectionModel().select()")
                    }
                    var c=this.getSelectionModel();
                return c.select.apply(c,arguments)
                },
            clearSelections:function(){
                if(Ext.global.console){
                    Ext.global.console.warn("DataView: clearSelections will be removed, please access deselectAll through DataView's SelectionModel, ie: view.getSelectionModel().deselectAll()")
                    }
                    var a=this.getSelectionModel();
                return a.deselectAll()
                }
            })
    })
});
Ext.define("Ext.view.View",{
    extend:"Ext.view.AbstractView",
    alternateClassName:"Ext.DataView",
    alias:"widget.dataview",
    inheritableStatics:{
        EventMap:{
            mousedown:"MouseDown",
            mouseup:"MouseUp",
            click:"Click",
            dblclick:"DblClick",
            contextmenu:"ContextMenu",
            mouseover:"MouseOver",
            mouseout:"MouseOut",
            mouseenter:"MouseEnter",
            mouseleave:"MouseLeave",
            keydown:"KeyDown",
            focus:"Focus"
        }
    },
addCmpEvents:function(){
    this.addEvents("beforeitemmousedown","beforeitemmouseup","beforeitemmouseenter","beforeitemmouseleave","beforeitemclick","beforeitemdblclick","beforeitemcontextmenu","beforeitemkeydown","itemmousedown","itemmouseup","itemmouseenter","itemmouseleave","itemclick","itemdblclick","itemcontextmenu","itemkeydown","beforecontainermousedown","beforecontainermouseup","beforecontainermouseover","beforecontainermouseout","beforecontainerclick","beforecontainerdblclick","beforecontainercontextmenu","beforecontainerkeydown","containermouseup","containermouseover","containermouseout","containerclick","containerdblclick","containercontextmenu","containerkeydown","selectionchange","beforeselect")
    },
afterRender:function(){
    var b=this,a;
    b.callParent();
    a={
        scope:b,
        freezeEvent:true,
        click:b.handleEvent,
        mousedown:b.handleEvent,
        mouseup:b.handleEvent,
        dblclick:b.handleEvent,
        contextmenu:b.handleEvent,
        mouseover:b.handleEvent,
        mouseout:b.handleEvent,
        keydown:b.handleEvent
        };
        
    b.mon(b.getTargetEl(),a);
    if(b.store){
        b.bindStore(b.store,true)
        }
    },
handleEvent:function(a){
    if(this.processUIEvent(a)!==false){
        this.processSpecialEvent(a)
        }
    },
processItemEvent:Ext.emptyFn,
processContainerEvent:Ext.emptyFn,
processSpecialEvent:Ext.emptyFn,
stillOverItem:function(b,a){
    var c;
    if(a&&typeof(a.offsetParent)==="object"){
        c=(b.type=="mouseout")?b.getRelatedTarget():b.getTarget();
        return Ext.fly(a).contains(c)
        }
        return false
    },
processUIEvent:function(g){
    var h=this,j=g.getTarget(h.getItemSelector(),h.getTargetEl()),a=this.statics().EventMap,f,c,i=g.type,d=h.mouseOverItem,b;
    if(!j){
        if(i=="mouseover"&&h.stillOverItem(g,d)){
            j=d
            }
            if(i=="keydown"){
            c=h.getSelectionModel().getLastSelected();
            if(c){
                j=h.getNode(c)
                }
            }
    }
if(j){
    f=h.indexOf(j);
    if(!c){
        c=h.getRecord(j)
        }
        if(h.processItemEvent(c,j,f,g)===false){
        return false
        }
        b=h.isNewItemEvent(j,g);
    if(b===false){
        return false
        }
        if((h["onBeforeItem"+a[b]](c,j,f,g)===false)||(h.fireEvent("beforeitem"+b,h,c,j,f,g)===false)||(h["onItem"+a[b]](c,j,f,g)===false)){
        return false
        }
        h.fireEvent("item"+b,h,c,j,f,g)
    }else{
    if((h.processContainerEvent(g)===false)||(h["onBeforeContainer"+a[i]](g)===false)||(h.fireEvent("beforecontainer"+i,h,g)===false)||(h["onContainer"+a[i]](g)===false)){
        return false
        }
        h.fireEvent("container"+i,h,g)
    }
    return true
},
isNewItemEvent:function(d,f){
    var c=this,a=c.mouseOverItem,b=f.type;
    switch(b){
        case"mouseover":
            if(d===a){
            return false
            }
            c.mouseOverItem=d;
        return"mouseenter";
        case"mouseout":
            if(c.stillOverItem(f,a)){
            return false
            }
            c.mouseOverItem=null;
        return"mouseleave"
        }
        return b
    },
onItemMouseEnter:function(a,c,b,d){
    if(this.trackOver){
        this.highlightItem(c)
        }
    },
onItemMouseLeave:function(a,c,b,d){
    if(this.trackOver){
        this.clearHighlight()
        }
    },
onItemMouseDown:Ext.emptyFn,
onItemMouseUp:Ext.emptyFn,
onItemFocus:Ext.emptyFn,
onItemClick:Ext.emptyFn,
onItemDblClick:Ext.emptyFn,
onItemContextMenu:Ext.emptyFn,
onItemKeyDown:Ext.emptyFn,
onBeforeItemMouseDown:Ext.emptyFn,
onBeforeItemMouseUp:Ext.emptyFn,
onBeforeItemFocus:Ext.emptyFn,
onBeforeItemMouseEnter:Ext.emptyFn,
onBeforeItemMouseLeave:Ext.emptyFn,
onBeforeItemClick:Ext.emptyFn,
onBeforeItemDblClick:Ext.emptyFn,
onBeforeItemContextMenu:Ext.emptyFn,
onBeforeItemKeyDown:Ext.emptyFn,
onContainerMouseDown:Ext.emptyFn,
onContainerMouseUp:Ext.emptyFn,
onContainerMouseOver:Ext.emptyFn,
onContainerMouseOut:Ext.emptyFn,
onContainerClick:Ext.emptyFn,
onContainerDblClick:Ext.emptyFn,
onContainerContextMenu:Ext.emptyFn,
onContainerKeyDown:Ext.emptyFn,
onBeforeContainerMouseDown:Ext.emptyFn,
onBeforeContainerMouseUp:Ext.emptyFn,
onBeforeContainerMouseOver:Ext.emptyFn,
onBeforeContainerMouseOut:Ext.emptyFn,
onBeforeContainerClick:Ext.emptyFn,
onBeforeContainerDblClick:Ext.emptyFn,
onBeforeContainerContextMenu:Ext.emptyFn,
onBeforeContainerKeyDown:Ext.emptyFn,
highlightItem:function(b){
    var a=this;
    a.clearHighlight();
    a.highlightedItem=b;
    Ext.fly(b).addCls(a.overItemCls)
    },
clearHighlight:function(){
    var b=this,a=b.highlightedItem;
    if(a){
        Ext.fly(a).removeCls(b.overItemCls);
        delete b.highlightedItem
        }
    },
refresh:function(){
    var a=this;
    a.clearHighlight();
    a.callParent(arguments);
    if(!a.isFixedHeight()){
        a.doComponentLayout()
        }
    }
});
Ext.define("Ext.view.Table",{
    extend:"Ext.view.View",
    alias:"widget.tableview",
    uses:["Ext.view.TableChunker","Ext.util.DelayedTask","Ext.util.MixedCollection"],
    cls:Ext.baseCSSPrefix+"grid-view",
    itemSelector:"."+Ext.baseCSSPrefix+"grid-row",
    cellSelector:"."+Ext.baseCSSPrefix+"grid-cell",
    selectedItemCls:Ext.baseCSSPrefix+"grid-row-selected",
    selectedCellCls:Ext.baseCSSPrefix+"grid-cell-selected",
    focusedItemCls:Ext.baseCSSPrefix+"grid-row-focused",
    overItemCls:Ext.baseCSSPrefix+"grid-row-over",
    altRowCls:Ext.baseCSSPrefix+"grid-row-alt",
    rowClsRe:/(?:^|\s*)grid-row-(first|last|alt)(?:\s+|$)/g,
    cellRe:new RegExp("x-grid-cell-([^\\s]+) ",""),
    trackOver:true,
    getRowClass:null,
    initComponent:function(){
        var a=this;
        if(a.deferRowRender!==false){
            a.refresh=function(){
                delete a.refresh;
                setTimeout(function(){
                    a.refresh()
                    },0)
                }
            }
        a.scrollState={};
    
    a.selModel.view=a;
    a.headerCt.view=a;
    a.initFeatures();
    a.tpl="<div></div>";
    a.callParent();
    a.mon(a.store,{
        load:a.onStoreLoad,
        scope:a
    })
    },
onStoreLoad:function(){
    var a=this;
    if(a.invalidateScrollerOnRefresh){
        if(Ext.isGecko){
            if(!a.scrollToTopTask){
                a.scrollToTopTask=Ext.create("Ext.util.DelayedTask",a.scrollToTop,a)
                }
                a.scrollToTopTask.delay(1)
            }else{
            a.scrollToTop()
            }
        }
},
scrollToTop:Ext.emptyFn,
addElListener:function(a,c,b){
    this.mon(this,a,c,b,{
        element:"el"
    })
    },
getGridColumns:function(){
    return this.headerCt.getGridColumns()
    },
getHeaderAtIndex:function(a){
    return this.headerCt.getHeaderAtIndex(a)
    },
getCell:function(a,b){
    var c=this.getNode(a);
    return Ext.fly(c).down(b.getCellSelector())
    },
getFeature:function(b){
    var a=this.featuresMC;
    if(a){
        return a.get(b)
        }
    },
initFeatures:function(){
    var d=this,b=0,c,a;
    d.features=d.features||[];
    c=d.features;
    a=c.length;
    d.featuresMC=Ext.create("Ext.util.MixedCollection");
    for(;b<a;b++){
        if(!c[b].isFeature){
            c[b]=Ext.create("feature."+c[b].ftype,c[b])
            }
            c[b].view=d;
        d.featuresMC.add(c[b])
        }
    },
attachEventsForFeatures:function(){
    var b=this.features,c=b.length,a=0;
    for(;a<c;a++){
        if(b[a].isFeature){
            b[a].attachEvents()
            }
        }
    },
afterRender:function(){
    var a=this;
    a.callParent();
    a.mon(a.el,{
        scroll:a.fireBodyScroll,
        scope:a
    });
    a.el.unselectable();
    a.attachEventsForFeatures()
    },
fireBodyScroll:function(b,a){
    this.fireEvent("bodyscroll",b,a)
    },
prepareData:function(c,j,e){
    var g=this,h=g.headerCt.prepareData(c,j,e,g,g.ownerCt),a=g.features,f=a.length,d=0,b,k;
    for(;d<f;d++){
        k=a[d];
        if(k.isFeature){
            Ext.apply(h,k.getAdditionalData(c,j,e,h,g))
            }
        }
    return h
},
collectData:function(d,m){
    var n=this.callParent(arguments),c=this.headerCt,l=c.getFullWidth(),b=this.features,h=b.length,a={
        rows:n,
        fullWidth:l
    },f=0,p,e=0,k,g;
    k=n.length;
    if(this.getRowClass){
        for(;e<k;e++){
            g={};
            
            n[e]["rowCls"]=this.getRowClass(d[e],e,g,this.store)
            }
        }
        for(;f<h;f++){
    p=b[f];
    if(p.isFeature&&p.collectData&&!p.disabled){
        a=p.collectData(d,n,m,l,a);
        break
    }
}
return a
},
onHeaderResize:function(e,a,d){
    var c=this,b=c.el;
    if(b){
        c.saveScrollState();
        if(Ext.isIE6||Ext.isIE7){
            if(e.el.hasCls(Ext.baseCSSPrefix+"column-header-first")){
                a+=1
                }
            }
        b.select("."+Ext.baseCSSPrefix+"grid-col-resizer-"+e.id).setWidth(a);
    b.select("."+Ext.baseCSSPrefix+"grid-table-resizer").setWidth(c.headerCt.getFullWidth());
    c.restoreScrollState();
    c.setNewTemplate();
    if(!d){
        c.el.focus()
        }
    }
},
onHeaderShow:function(b,c,a){
    if(c.oldWidth){
        this.onHeaderResize(c,c.oldWidth,a);
        delete c.oldWidth
        }else{
        if(c.width&&!c.flex){
            this.onHeaderResize(c,c.width,a)
            }
        }
    this.setNewTemplate()
},
onHeaderHide:function(b,c,a){
    this.onHeaderResize(c,0,a)
    },
setNewTemplate:function(){
    var b=this,a=b.headerCt.getColumnsForTpl(true);
    b.tpl=b.getTableChunker().getTableTpl({
        columns:a,
        features:b.features
        })
    },
getTableChunker:function(){
    return this.chunker||Ext.view.TableChunker
    },
addRowCls:function(b,a){
    var c=this.getNode(b);
    if(c){
        Ext.fly(c).addCls(a)
        }
    },
removeRowCls:function(b,a){
    var c=this.getNode(b);
    if(c){
        Ext.fly(c).removeCls(a)
        }
    },
onRowSelect:function(a){
    this.addRowCls(a,this.selectedItemCls)
    },
onRowDeselect:function(b){
    var a=this;
    a.removeRowCls(b,a.selectedItemCls);
    a.removeRowCls(b,a.focusedItemCls)
    },
onCellSelect:function(b){
    var a=this.getCellByPosition(b);
    if(a){
        a.addCls(this.selectedCellCls)
        }
    },
onCellDeselect:function(b){
    var a=this.getCellByPosition(b);
    if(a){
        a.removeCls(this.selectedCellCls)
        }
    },
onCellFocus:function(a){
    this.focusCell(a)
    },
getCellByPosition:function(b){
    var g=b.row,e=b.column,d=this.store,f=this.getNode(g),h=this.headerCt.getHeaderAtIndex(e),c,a=false;
    if(h&&f){
        c=h.getCellSelector();
        a=Ext.fly(f).down(c)
        }
        return a
    },
onRowFocus:function(d,b,a){
    var c=this,e=c.getNode(d);
    if(b){
        c.addRowCls(d,c.focusedItemCls);
        if(!a){
            c.focusRow(d)
            }
        }else{
    c.removeRowCls(d,c.focusedItemCls)
    }
},
focusRow:function(b){
    var f=this,i=f.getNode(b),c=f.el,g=0,a=f.ownerCt,h,d,e;
    if(i&&c){
        d=c.getRegion();
        h=Ext.fly(i).getRegion();
        if(h.top<d.top){
            g=h.top-d.top
            }else{
            if(h.bottom>d.bottom){
                g=h.bottom-d.bottom
                }
            }
        e=f.getRecord(i);
    b=f.store.indexOf(e);
    if(g){
        a.scrollByDeltaY(g)
        }
        f.fireEvent("rowfocus",e,i,b)
    }
},
focusCell:function(g){
    var i=this,j=i.getCellByPosition(g),b=i.el,d=0,e=0,c=b.getRegion(),a=i.ownerCt,h,f;
    if(j){
        h=j.getRegion();
        if(h.top<c.top){
            d=h.top-c.top
            }else{
            if(h.bottom>c.bottom){
                d=h.bottom-c.bottom
                }
            }
        if(h.left<c.left){
        e=h.left-c.left
        }else{
        if(h.right>c.right){
            e=h.right-c.right
            }
        }
    if(d){
    a.scrollByDeltaY(d)
    }
    if(e){
    a.scrollByDeltaX(e)
    }
    b.focus();
i.fireEvent("cellfocus",f,j,g)
}
},
scrollByDelta:function(c,b){
    b=b||"scrollTop";
    var a=this.el.dom;
    a[b]=(a[b]+=c)
    },
onUpdate:function(b,a){
    this.callParent(arguments)
    },
saveScrollState:function(){
    var b=this.el.dom,a=this.scrollState;
    a.left=b.scrollLeft;
    a.top=b.scrollTop
    },
restoreScrollState:function(){
    var b=this.el.dom,a=this.scrollState,c=this.headerCt.el.dom;
    c.scrollLeft=b.scrollLeft=a.left;
    b.scrollTop=a.top
    },
refresh:function(){
    this.setNewTemplate();
    this.callParent(arguments)
    },
processItemEvent:function(b,l,i,d){
    var g=this,j=d.getTarget(g.cellSelector,l),h=j?j.cellIndex:-1,a=g.statics().EventMap,c=g.getSelectionModel(),f=d.type,k;
    if(f=="keydown"&&!j&&c.getCurrentPosition){
        j=g.getCellByPosition(c.getCurrentPosition());
        if(j){
            j=j.dom;
            h=j.cellIndex
            }
        }
    k=g.fireEvent("uievent",f,g,j,i,h,d);
if(k===false||g.callParent(arguments)===false){
    return false
    }
    if(f=="mouseover"||f=="mouseout"){
    return true
    }
    return !((g["onBeforeCell"+a[f]](j,h,b,l,i,d)===false)||(g.fireEvent("beforecell"+f,g,j,h,b,l,i,d)===false)||(g["onCell"+a[f]](j,h,b,l,i,d)===false)||(g.fireEvent("cell"+f,g,j,h,b,l,i,d)===false))
},
processSpecialEvent:function(j){
    var m=this,b=m.statics().EventMap,d=m.features,l=d.length,n=j.type,f,o,g,h,c,k,a=m.ownerCt;
    m.callParent(arguments);
    if(n=="mouseover"||n=="mouseout"){
        return
    }
    for(f=0;f<l;f++){
        o=d[f];
        if(o.hasFeatureEvent){
            h=j.getTarget(o.eventSelector,m.getTargetEl());
            if(h){
                g=o.eventPrefix;
                c=o.getFireEventArgs("before"+g+n,m,h,j);
                k=o.getFireEventArgs(g+n,m,h,j);
                if((m.fireEvent.apply(m,c)===false)||(a.fireEvent.apply(a,c)===false)||(m.fireEvent.apply(m,k)===false)||(a.fireEvent.apply(a,k)===false)){
                    return false
                    }
                }
        }
    }
return true
},
onCellMouseDown:Ext.emptyFn,
onCellMouseUp:Ext.emptyFn,
onCellClick:Ext.emptyFn,
onCellDblClick:Ext.emptyFn,
onCellContextMenu:Ext.emptyFn,
onCellKeyDown:Ext.emptyFn,
onBeforeCellMouseDown:Ext.emptyFn,
onBeforeCellMouseUp:Ext.emptyFn,
onBeforeCellClick:Ext.emptyFn,
onBeforeCellDblClick:Ext.emptyFn,
onBeforeCellContextMenu:Ext.emptyFn,
onBeforeCellKeyDown:Ext.emptyFn,
expandToFit:function(b){
    if(b){
        var a=this.getMaxContentWidth(b);
        delete b.flex;
        b.setWidth(a)
        }
    },
getMaxContentWidth:function(g){
    var a=g.getCellInnerSelector(),c=this.el.query(a),d=0,f=c.length,e=g.el.dom.scrollWidth,b;
    for(;d<f;d++){
        b=c[d].scrollWidth;
        if(b>e){
            e=b
            }
        }
    return e
},
getPositionByEvent:function(f){
    var d=this,b=f.getTarget(d.cellSelector),c=f.getTarget(d.itemSelector),a=d.getRecord(c),g=d.getHeaderByCell(b);
    return d.getPosition(a,g)
    },
getHeaderByCell:function(b){
    if(b){
        var a=b.className.match(this.cellRe);
        if(a&&a[1]){
            return Ext.getCmp(a[1])
            }
        }
    return false
},
walkCells:function(k,l,g,m,a,n){
    var i=this,o=k.row,d=k.column,j=i.store.getCount(),f=i.getFirstVisibleColumnIndex(),b=i.getLastVisibleColumnIndex(),h={
        row:o,
        column:d
    },c=i.headerCt.getHeaderAtIndex(d);
    if(!c||c.hidden){
        return false
        }
        g=g||{};
    
    l=l.toLowerCase();
    switch(l){
        case"right":
            if(d===b){
            if(m||o===j-1){
                return false
                }
                if(!g.ctrlKey){
                h.row=o+1;
                h.column=f
                }
            }else{
            if(!g.ctrlKey){
                h.column=d+i.getRightGap(c)
                }else{
                h.column=b
                }
            }
        break;
case"left":
    if(d===f){
    if(m||o===0){
        return false
        }
        if(!g.ctrlKey){
        h.row=o-1;
        h.column=b
        }
    }else{
    if(!g.ctrlKey){
        h.column=d+i.getLeftGap(c)
        }else{
        h.column=f
        }
    }
break;
case"up":
    if(o===0){
    return false
    }else{
    if(!g.ctrlKey){
        h.row=o-1
        }else{
        h.row=0
        }
    }
break;
case"down":
    if(o===j-1){
    return false
    }else{
    if(!g.ctrlKey){
        h.row=o+1
        }else{
        h.row=j-1
        }
    }
break
}
if(a&&a.call(n||window,h)!==true){
    return false
    }else{
    return h
    }
},
getFirstVisibleColumnIndex:function(){
    var c=this.getHeaderCt(),a=c.getGridColumns(),d=Ext.ComponentQuery.query(":not([hidden])",a),b=d[0];
    return c.getHeaderIndex(b)
    },
getLastVisibleColumnIndex:function(){
    var c=this.getHeaderCt(),a=c.getGridColumns(),d=Ext.ComponentQuery.query(":not([hidden])",a),b=d[d.length-1];
    return c.getHeaderIndex(b)
    },
getHeaderCt:function(){
    return this.headerCt
    },
getPosition:function(a,e){
    var d=this,b=d.store,c=d.headerCt.getGridColumns();
    return{
        row:b.indexOf(a),
        column:Ext.Array.indexOf(c,e)
        }
    },
getRightGap:function(a){
    var f=this.getHeaderCt(),e=f.getGridColumns(),b=Ext.Array.indexOf(e,a),c=b+1,d;
    for(;c<=e.length;c++){
        if(!e[c].hidden){
            d=c;
            break
        }
    }
    return d-b
},
beforeDestroy:function(){
    if(this.rendered){
        this.el.removeAllListeners()
        }
        this.callParent(arguments)
    },
getLeftGap:function(a){
    var f=this.getHeaderCt(),e=f.getGridColumns(),c=Ext.Array.indexOf(e,a),d=c-1,b;
    for(;d>=0;d--){
        if(!e[d].hidden){
            b=d;
            break
        }
    }
    return b-c
}
});
Ext.define("Ext.grid.View",{
    extend:"Ext.view.Table",
    alias:"widget.gridview",
    stripeRows:true,
    invalidateScrollerOnRefresh:true,
    scrollToTop:function(){
        if(this.rendered){
            var b=this.ownerCt,a=b.verticalScroller;
            if(a){
                a.scrollToTop()
                }
            }
    },
onAdd:function(c,a,b){
    this.callParent(arguments);
    this.doStripeRows(b)
    },
onRemove:function(c,a,b){
    this.callParent(arguments);
    this.doStripeRows(b)
    },
onUpdate:function(d,a,b){
    var c=d.indexOf(a);
    this.callParent(arguments);
    this.doStripeRows(c,c)
    },
doStripeRows:function(b,a){
    if(this.stripeRows){
        var d=this.getNodes(b,a),f=d.length,c=0,e;
        for(;c<f;c++){
            e=d[c];
            e.className=e.className.replace(this.rowClsRe," ");
            b++;
            if(b%2===0){
                e.className+=(" "+this.altRowCls)
                }
            }
        }
},
refresh:function(b){
    this.callParent(arguments);
    this.doStripeRows(0);
    var a=this.up("gridpanel");
    if(a&&this.invalidateScrollerOnRefresh){
        a.invalidateScroller()
        }
    }
});
Ext.define("Ext.grid.Panel",{
    extend:"Ext.panel.Table",
    requires:["Ext.grid.View"],
    alias:["widget.gridpanel","widget.grid"],
    alternateClassName:["Ext.list.ListView","Ext.ListView","Ext.grid.GridPanel"],
    viewType:"gridview",
    lockable:false,
    normalCfgCopy:["invalidateScrollerOnRefresh","verticalScroller","verticalScrollDock","verticalScrollerType","scroll"],
    lockedCfgCopy:["invalidateScrollerOnRefresh"],
    initComponent:function(){
        var a=this;
        if(a.columnLines){
            a.setColumnLines(a.columnLines)
            }
            a.callParent()
        },
    setColumnLines:function(a){
        var b=this,c=(a)?"addClsWithUI":"removeClsWithUI";
        b[c]("with-col-lines")
        }
    });
Ext.define("Ext.app.GridPortlet",{
    extend:"Ext.grid.Panel",
    alias:"widget.gridportlet",
    height:300,
    myData:[["3m Co",71.72,0.02,0.03,"9/1 12:00am"],["Alcoa Inc",29.01,0.42,1.47,"9/1 12:00am"],["Altria Group Inc",83.81,0.28,0.34,"9/1 12:00am"],["American Express Company",52.55,0.01,0.02,"9/1 12:00am"],["American International Group, Inc.",64.13,0.31,0.49,"9/1 12:00am"],["AT&T Inc.",31.61,-0.48,-1.54,"9/1 12:00am"],["Boeing Co.",75.43,0.53,0.71,"9/1 12:00am"],["Caterpillar Inc.",67.27,0.92,1.39,"9/1 12:00am"],["Citigroup, Inc.",49.37,0.02,0.04,"9/1 12:00am"],["E.I. du Pont de Nemours and Company",40.48,0.51,1.28,"9/1 12:00am"],["Exxon Mobil Corp",68.1,-0.43,-0.64,"9/1 12:00am"],["General Electric Company",34.14,-0.08,-0.23,"9/1 12:00am"],["General Motors Corporation",30.27,1.09,3.74,"9/1 12:00am"],["Hewlett-Packard Co.",36.53,-0.03,-0.08,"9/1 12:00am"],["Honeywell Intl Inc",38.77,0.05,0.13,"9/1 12:00am"],["Intel Corporation",19.88,0.31,1.58,"9/1 12:00am"],["International Business Machines",81.41,0.44,0.54,"9/1 12:00am"],["Johnson & Johnson",64.72,0.06,0.09,"9/1 12:00am"],["JP Morgan & Chase & Co",45.73,0.07,0.15,"9/1 12:00am"],["McDonald's Corporation",36.76,0.86,2.4,"9/1 12:00am"],["Merck & Co., Inc.",40.96,0.41,1.01,"9/1 12:00am"],["Microsoft Corporation",25.84,0.14,0.54,"9/1 12:00am"],["Pfizer Inc",27.96,0.4,1.45,"9/1 12:00am"],["The Coca-Cola Company",45.07,0.26,0.58,"9/1 12:00am"],["The Home Depot, Inc.",34.64,0.35,1.02,"9/1 12:00am"],["The Procter & Gamble Company",61.91,0.01,0.02,"9/1 12:00am"],["United Technologies Corporation",63.26,0.55,0.88,"9/1 12:00am"],["Verizon Communications",35.57,0.39,1.11,"9/1 12:00am"],["Wal-Mart Stores, Inc.",45.45,0.73,1.63,"9/1 12:00am"]],
    change:function(a){
        if(a>0){
            return'<span style="color:green;">'+a+"</span>"
            }else{
            if(a<0){
                return'<span style="color:red;">'+a+"</span>"
                }
            }
        return a
    },
pctChange:function(a){
    if(a>0){
        return'<span style="color:green;">'+a+"%</span>"
        }else{
        if(a<0){
            return'<span style="color:red;">'+a+"%</span>"
            }
        }
    return a
},
initComponent:function(){
    var a=Ext.create("Ext.data.ArrayStore",{
        fields:[{
            name:"company"
        },{
            name:"change",
            type:"float"
        },{
            name:"pctChange",
            type:"float"
        }],
        data:this.myData
        });
    Ext.apply(this,{
        height:this.height,
        store:a,
        stripeRows:true,
        columnLines:true,
        columns:[{
            id:"company",
            text:"Company",
            flex:1,
            sortable:true,
            dataIndex:"company"
        },{
            text:"Change",
            width:75,
            sortable:true,
            renderer:this.change,
            dataIndex:"change"
        },{
            text:"% Change",
            width:75,
            sortable:true,
            renderer:this.pctChange,
            dataIndex:"pctChange"
        }]
        });
    this.callParent(arguments)
    }
});
Ext.define("Ext.data.writer.Json",{
    extend:"Ext.data.writer.Writer",
    alternateClassName:"Ext.data.JsonWriter",
    alias:"writer.json",
    root:undefined,
    encode:false,
    allowSingle:true,
    writeRecords:function(b,c){
        var a=this.root;
        if(this.allowSingle&&c.length==1){
            c=c[0]
            }
            if(this.encode){
            if(a){
                b.params[a]=Ext.encode(c)
                }else{}
    }else{
        b.jsonData=b.jsonData||{};
        
        if(a){
            b.jsonData[a]=c
            }else{
            b.jsonData=c
            }
        }
    return b
}
});
Ext.define("Ext.data.reader.Reader",{
    requires:["Ext.data.ResultSet"],
    alternateClassName:["Ext.data.Reader","Ext.data.DataReader"],
    totalProperty:"total",
    successProperty:"success",
    root:"",
    implicitIncludes:true,
    isReader:true,
    constructor:function(a){
        var b=this;
        Ext.apply(b,a||{});
        b.fieldCount=0;
        b.model=Ext.ModelManager.getModel(a.model);
        if(b.model){
            b.buildExtractors()
            }
        },
setModel:function(a,c){
    var b=this;
    b.model=Ext.ModelManager.getModel(a);
    b.buildExtractors(true);
    if(c&&b.proxy){
        b.proxy.setModel(b.model,true)
        }
    },
read:function(a){
    var b=a;
    if(a&&a.responseText){
        b=this.getResponseData(a)
        }
        if(b){
        return this.readRecords(b)
        }else{
        return this.nullResultSet
        }
    },
readRecords:function(c){
    var d=this;
    if(d.fieldCount!==d.getFields().length){
        d.buildExtractors(true)
        }
        d.rawData=c;
    c=d.getData(c);
    var f=Ext.isArray(c)?c:d.getRoot(c),h=true,b=0,e,g,a,i;
    if(f){
        e=f.length
        }
        if(d.totalProperty){
        g=parseInt(d.getTotal(c),10);
        if(!isNaN(g)){
            e=g
            }
        }
    if(d.successProperty){
    g=d.getSuccess(c);
    if(g===false||g==="false"){
        h=false
        }
    }
if(d.messageProperty){
    i=d.getMessage(c)
    }
    if(f){
    a=d.extractData(f);
    b=a.length
    }else{
    b=0;
    a=[]
    }
    return Ext.create("Ext.data.ResultSet",{
    total:e||b,
    count:b,
    records:a,
    success:h,
    message:i
})
},
extractData:function(j){
    var h=this,k=[],e=[],d=h.model,f=0,b=j.length,l=h.getIdProperty(),c,a,g;
    if(!j.length&&Ext.isObject(j)){
        j=[j];
        b=1
        }
        for(;f<b;f++){
        c=j[f];
        k=h.extractValues(c);
        a=h.getId(c);
        g=new d(k,a,c);
        e.push(g);
        if(h.implicitIncludes){
            h.readAssociated(g,c)
            }
        }
    return e
},
readAssociated:function(g,e){
    var d=g.associations.items,f=0,a=d.length,c,b,j,h;
    for(;f<a;f++){
        c=d[f];
        b=this.getAssociatedDataRoot(e,c.associationKey||c.name);
        if(b){
            h=c.getReader();
            if(!h){
                j=c.associatedModel.proxy;
                if(j){
                    h=j.getReader()
                    }else{
                    h=new this.constructor({
                        model:c.associatedName
                        })
                    }
                }
            c.read(g,h,b)
        }
    }
},
getAssociatedDataRoot:function(b,a){
    return b[a]
    },
getFields:function(){
    return this.model.prototype.fields.items
    },
extractValues:function(f){
    var a=this.getFields(),c=0,d=a.length,b={},g,e;
    for(;c<d;c++){
        g=a[c];
        e=this.extractorFunctions[c](f);
        b[g.name]=e
        }
        return b
    },
getData:function(a){
    return a
    },
getRoot:function(a){
    return a
    },
getResponseData:function(a){},
onMetaChange:function(c){
    var a=c.fields,b;
    Ext.apply(this,c);
    if(a){
        b=Ext.define("Ext.data.reader.Json-Model"+Ext.id(),{
            extend:"Ext.data.Model",
            fields:a
        });
        this.setModel(b,true)
        }else{
        this.buildExtractors(true)
        }
    },
getIdProperty:function(){
    var a=this.idProperty;
    if(Ext.isEmpty(a)){
        a=this.model.prototype.idProperty
        }
        return a
    },
buildExtractors:function(e){
    var c=this,g=c.getIdProperty(),d=c.totalProperty,b=c.successProperty,f=c.messageProperty,a;
    if(e===true){
        delete c.extractorFunctions
        }
        if(c.extractorFunctions){
        return
    }
    if(d){
        c.getTotal=c.createAccessor(d)
        }
        if(b){
        c.getSuccess=c.createAccessor(b)
        }
        if(f){
        c.getMessage=c.createAccessor(f)
        }
        if(g){
        a=c.createAccessor(g);
        c.getId=function(h){
            var i=a.call(c,h);
            return(i===undefined||i==="")?null:i
            }
        }else{
    c.getId=function(){
        return null
        }
    }
c.buildFieldExtractors()
},
buildFieldExtractors:function(){
    var d=this,a=d.getFields(),c=a.length,b=0,g=[],f,e;
    for(;b<c;b++){
        f=a[b];
        e=(f.mapping!==undefined&&f.mapping!==null)?f.mapping:f.name;
        g.push(d.createAccessor(e))
        }
        d.fieldCount=c;
    d.extractorFunctions=g
    }
},function(){
    Ext.apply(this,{
        nullResultSet:Ext.create("Ext.data.ResultSet",{
            total:0,
            count:0,
            records:[],
            success:true
        })
        })
    });
Ext.define("Ext.data.reader.Json",{
    extend:"Ext.data.reader.Reader",
    alternateClassName:"Ext.data.JsonReader",
    alias:"reader.json",
    root:"",
    useSimpleAccessors:false,
    readRecords:function(a){
        if(a.metaData){
            this.onMetaChange(a.metaData)
            }
            this.jsonData=a;
        return this.callParent([a])
        },
    getResponseData:function(a){
        try{
            var c=Ext.decode(a.responseText)
            }catch(b){
            Ext.Error.raise({
                response:a,
                json:a.responseText,
                parseError:b,
                msg:"Unable to parse the JSON returned by the server: "+b.toString()
                })
            }
            return c
        },
    buildExtractors:function(){
        var a=this;
        a.callParent(arguments);
        if(a.root){
            a.getRoot=a.createAccessor(a.root)
            }else{
            a.getRoot=function(b){
                return b
                }
            }
    },
extractData:function(a){
    var e=this.record,d=[],c,b;
    if(e){
        c=a.length;
        for(b=0;b<c;b++){
            d[b]=a[b][e]
            }
        }else{
    d=a
    }
    return this.callParent([d])
    },
createAccessor:function(){
    var a=/[\[\.]/;
    return function(c){
        if(Ext.isEmpty(c)){
            return Ext.emptyFn
            }
            if(Ext.isFunction(c)){
            return c
            }
            if(this.useSimpleAccessors!==true){
            var b=String(c).search(a);
            if(b>=0){
                return Ext.functionFactory("obj","return obj"+(b>0?".":"")+c)
                }
            }
        return function(d){
        return d[c]
        }
    }
}()
});
Ext.define("Ext.data.proxy.Proxy",{
    alias:"proxy.proxy",
    alternateClassName:["Ext.data.DataProxy","Ext.data.Proxy"],
    requires:["Ext.data.reader.Json","Ext.data.writer.Json"],
    uses:["Ext.data.Batch","Ext.data.Operation","Ext.data.Model"],
    mixins:{
        observable:"Ext.util.Observable"
    },
    batchOrder:"create,update,destroy",
    batchActions:true,
    defaultReaderType:"json",
    defaultWriterType:"json",
    isProxy:true,
    constructor:function(a){
        a=a||{};
        
        if(a.model===undefined){
            delete a.model
            }
            this.mixins.observable.constructor.call(this,a);
        if(this.model!==undefined&&!(this.model instanceof Ext.data.Model)){
            this.setModel(this.model)
            }
        },
setModel:function(b,c){
    this.model=Ext.ModelManager.getModel(b);
    var a=this.reader,d=this.writer;
    this.setReader(a);
    this.setWriter(d);
    if(c&&this.store){
        this.store.setModel(this.model)
        }
    },
getModel:function(){
    return this.model
    },
setReader:function(a){
    var b=this;
    if(a===undefined||typeof a=="string"){
        a={
            type:a
        }
    }
    if(a.isReader){
    a.setModel(b.model)
    }else{
    Ext.applyIf(a,{
        proxy:b,
        model:b.model,
        type:b.defaultReaderType
        });
    a=Ext.createByAlias("reader."+a.type,a)
    }
    b.reader=a;
return b.reader
},
getReader:function(){
    return this.reader
    },
setWriter:function(a){
    if(a===undefined||typeof a=="string"){
        a={
            type:a
        }
    }
    if(!(a instanceof Ext.data.writer.Writer)){
    Ext.applyIf(a,{
        model:this.model,
        type:this.defaultWriterType
        });
    a=Ext.createByAlias("writer."+a.type,a)
    }
    this.writer=a;
return this.writer
},
getWriter:function(){
    return this.writer
    },
create:Ext.emptyFn,
read:Ext.emptyFn,
update:Ext.emptyFn,
destroy:Ext.emptyFn,
batch:function(d,e){
    var f=this,c=Ext.create("Ext.data.Batch",{
        proxy:f,
        listeners:e||{}
    }),b=f.batchActions,a;
Ext.each(f.batchOrder.split(","),function(g){
    a=d[g];
    if(a){
        if(b){
            c.add(Ext.create("Ext.data.Operation",{
                action:g,
                records:a
            }))
            }else{
            Ext.each(a,function(h){
                c.add(Ext.create("Ext.data.Operation",{
                    action:g,
                    records:[h]
                    }))
                })
            }
        }
},f);
c.start();
return c
}
},function(){
    Ext.data.DataProxy=this
    });
Ext.define("Ext.data.proxy.Server",{
    extend:"Ext.data.proxy.Proxy",
    alias:"proxy.server",
    alternateClassName:"Ext.data.ServerProxy",
    uses:["Ext.data.Request"],
    pageParam:"page",
    startParam:"start",
    limitParam:"limit",
    groupParam:"group",
    sortParam:"sort",
    filterParam:"filter",
    directionParam:"dir",
    simpleSortMode:false,
    noCache:true,
    cacheString:"_dc",
    timeout:30000,
    constructor:function(a){
        var b=this;
        a=a||{};
        
        this.addEvents("exception");
        b.callParent([a]);
        b.extraParams=a.extraParams||{};
        
        b.api=a.api||{};
        
        b.nocache=b.noCache
        },
    create:function(){
        return this.doRequest.apply(this,arguments)
        },
    read:function(){
        return this.doRequest.apply(this,arguments)
        },
    update:function(){
        return this.doRequest.apply(this,arguments)
        },
    destroy:function(){
        return this.doRequest.apply(this,arguments)
        },
    buildRequest:function(a){
        var c=Ext.applyIf(a.params||{},this.extraParams||{}),b;
        c=Ext.applyIf(c,this.getParams(c,a));
        if(a.id&&!c.id){
            c.id=a.id
            }
            b=Ext.create("Ext.data.Request",{
            params:c,
            action:a.action,
            records:a.records,
            operation:a,
            url:a.url
            });
        b.url=this.buildUrl(b);
        a.request=b;
        return b
        },
    processResponse:function(m,c,e,d,l,n){
        var j=this,h,o,b,a,k,g,f;
        if(m===true){
            h=j.getReader();
            o=h.read(j.extractResponseData(d));
            b=o.records;
            a=b.length;
            if(o.success!==false){
                k=Ext.create("Ext.util.MixedCollection",true,function(i){
                    return i.getId()
                    });
                k.addAll(c.records);
                for(f=0;f<a;f++){
                    g=k.get(b[f].getId());
                    if(g){
                        g.beginEdit();
                        g.set(g.data);
                        g.endEdit(true)
                        }
                    }
                Ext.apply(c,{
                response:d,
                resultSet:o
            });
            c.setCompleted();
            c.setSuccessful()
            }else{
            c.setException(o.message);
            j.fireEvent("exception",this,d,c)
            }
        }else{
    j.setException(c,d);
    j.fireEvent("exception",this,d,c)
    }
    if(typeof l=="function"){
    l.call(n||j,c)
    }
    j.afterRequest(e,m)
    },
setException:function(b,a){
    b.setException({
        status:a.status,
        statusText:a.statusText
        })
    },
extractResponseData:function(a){
    return a
    },
applyEncoding:function(a){
    return Ext.encode(a)
    },
encodeSorters:function(d){
    var b=[],c=d.length,a=0;
    for(;a<c;a++){
        b[a]={
            property:d[a].property,
            direction:d[a].direction
            }
        }
    return this.applyEncoding(b)
    },
encodeFilters:function(d){
    var b=[],c=d.length,a=0;
    for(;a<c;a++){
        b[a]={
            property:d[a].property,
            value:d[a].value
            }
        }
    return this.applyEncoding(b)
},
getParams:function(q,k){
    q=q||{};
    
    var r=this,n=Ext.isDefined,o=k.groupers,a=k.sorters,i=k.filters,g=k.page,f=k.start,p=k.limit,h=r.simpleSortMode,m=r.pageParam,d=r.startParam,b=r.limitParam,c=r.groupParam,e=r.sortParam,l=r.filterParam,j=r.directionParam;
    if(m&&n(g)){
        q[m]=g
        }
        if(d&&n(f)){
        q[d]=f
        }
        if(b&&n(p)){
        q[b]=p
        }
        if(c&&o&&o.length>0){
        q[c]=r.encodeSorters(o)
        }
        if(e&&a&&a.length>0){
        if(h){
            q[e]=a[0].property;
            q[j]=a[0].direction
            }else{
            q[e]=r.encodeSorters(a)
            }
        }
    if(l&&i&&i.length>0){
    q[l]=r.encodeFilters(i)
    }
    return q
},
buildUrl:function(c){
    var b=this,a=b.getUrl(c);
    if(b.noCache){
        a=Ext.urlAppend(a,Ext.String.format("{0}={1}",b.cacheString,Ext.Date.now()))
        }
        return a
    },
getUrl:function(a){
    return a.url||this.api[a.action]||this.url
    },
doRequest:function(a,c,b){},
afterRequest:Ext.emptyFn,
onDestroy:function(){
    Ext.destroy(this.reader,this.writer)
    }
});
Ext.define("Ext.data.proxy.Ajax",{
    requires:["Ext.util.MixedCollection","Ext.Ajax"],
    extend:"Ext.data.proxy.Server",
    alias:"proxy.ajax",
    alternateClassName:["Ext.data.HttpProxy","Ext.data.AjaxProxy"],
    actionMethods:{
        create:"POST",
        read:"GET",
        update:"POST",
        destroy:"POST"
    },
    doRequest:function(a,e,b){
        var d=this.getWriter(),c=this.buildRequest(a,e,b);
        if(a.allowWrite()){
            c=d.write(c)
            }
            Ext.apply(c,{
            headers:this.headers,
            timeout:this.timeout,
            scope:this,
            callback:this.createRequestCallback(c,a,e,b),
            method:this.getMethod(c),
            disableCaching:false
        });
        Ext.Ajax.request(c);
        return c
        },
    getMethod:function(a){
        return this.actionMethods[a.action]
        },
    createRequestCallback:function(d,a,e,b){
        var c=this;
        return function(g,h,f){
            c.processResponse(h,a,d,f,e,b)
            }
        }
},function(){
    Ext.data.HttpProxy=this
    });
Ext.define("Ext.data.Model",{
    alternateClassName:"Ext.data.Record",
    mixins:{
        observable:"Ext.util.Observable"
    },
    requires:["Ext.ModelManager","Ext.data.Field","Ext.data.Errors","Ext.data.Operation","Ext.data.validations","Ext.data.proxy.Ajax","Ext.util.MixedCollection"],
    onClassExtended:function(a,b){
        var c=b.onBeforeClassCreated;
        b.onBeforeClassCreated=function(d,w){
            var v=this,x=Ext.getClassName(d),l=d.prototype,p=d.prototype.superclass,e=w.validations||[],n=w.fields||[],r=w.associations||[],q=w.belongsTo,m=w.hasMany,t=new Ext.util.MixedCollection(false,function(i){
                return i.name
                }),s=new Ext.util.MixedCollection(false,function(i){
                return i.name
                }),k=p.validations,u=p.fields,g=p.associations,f,o,h,j=[];
            d.modelName=x;
            l.modelName=x;
            if(k){
                e=k.concat(e)
                }
                w.validations=e;
            if(u){
                n=u.items.concat(n)
                }
                for(o=0,h=n.length;o<h;++o){
                t.add(new Ext.data.Field(n[o]))
                }
                w.fields=t;
            if(q){
                q=Ext.Array.from(q);
                for(o=0,h=q.length;o<h;++o){
                    f=q[o];
                    if(!Ext.isObject(f)){
                        f={
                            model:f
                        }
                    }
                    f.type="belongsTo";
                r.push(f)
                    }
                    delete w.belongsTo
            }
            if(m){
            m=Ext.Array.from(m);
            for(o=0,h=m.length;o<h;++o){
                f=m[o];
                if(!Ext.isObject(f)){
                    f={
                        model:f
                    }
                }
                f.type="hasMany";
            r.push(f)
                }
                delete w.hasMany
        }
        if(g){
        r=g.items.concat(r)
        }
        for(o=0,h=r.length;o<h;++o){
        j.push("association."+r[o].type.toLowerCase())
        }
        if(w.proxy){
        if(typeof w.proxy==="string"){
            j.push("proxy."+w.proxy)
            }else{
            if(typeof w.proxy.type==="string"){
                j.push("proxy."+w.proxy.type)
                }
            }
    }
Ext.require(j,function(){
    Ext.ModelManager.registerType(x,d);
    for(o=0,h=r.length;o<h;++o){
        f=r[o];
        Ext.apply(f,{
            ownerModel:x,
            associatedModel:f.model
            });
        if(Ext.ModelManager.getModel(f.model)===undefined){
            Ext.ModelManager.registerDeferredAssociation(f)
            }else{
            s.add(Ext.data.Association.create(f))
            }
        }
    w.associations=s;
c.call(v,d,w);
    d.setProxy(d.prototype.proxy||d.prototype.defaultProxyType);
    Ext.ModelManager.onModelDefined(d)
    })
}
},
inheritableStatics:{
    setProxy:function(a){
        if(!a.isProxy){
            if(typeof a=="string"){
                a={
                    type:a
                }
            }
            a=Ext.createByAlias("proxy."+a.type,a)
        }
        a.setModel(this);
    this.proxy=this.prototype.proxy=a;
    return a
    },
getProxy:function(){
    return this.proxy
    },
load:function(f,c){
    c=Ext.apply({},c);
    c=Ext.applyIf(c,{
        action:"read",
        id:f
    });
    var b=Ext.create("Ext.data.Operation",c),d=c.scope||this,a=null,e;
    e=function(g){
        if(g.wasSuccessful()){
            a=g.getRecords()[0];
            Ext.callback(c.success,d,[a,g])
            }else{
            Ext.callback(c.failure,d,[a,g])
            }
            Ext.callback(c.callback,d,[a,g])
        };
        
    this.proxy.read(b,e,this)
    }
},
statics:{
    PREFIX:"ext-record",
    AUTO_ID:1,
    EDIT:"edit",
    REJECT:"reject",
    COMMIT:"commit",
    id:function(a){
        var b=[this.PREFIX,"-",this.AUTO_ID++].join("");
        a.phantom=true;
        a.internalId=b;
        return b
        }
    },
editing:false,
dirty:false,
persistenceProperty:"data",
evented:false,
isModel:true,
phantom:false,
idProperty:"id",
defaultProxyType:"ajax",
constructor:function(e,b,k){
    e=e||{};
    
    var h=this,g,c,j,a,d,f=Ext.isArray(e),l=f?{}:null;
    h.internalId=(b||b===0)?b:Ext.data.Model.id(h);
    h.raw=k;
    Ext.applyIf(h,{
        data:{}
    });
h.modified={};

if(h.persistanceProperty){
    h.persistenceProperty=h.persistanceProperty
    }
    h[h.persistenceProperty]={};

h.mixins.observable.constructor.call(h);
g=h.fields.items;
c=g.length;
for(d=0;d<c;d++){
    j=g[d];
    a=j.name;
    if(f){
        l[a]=e[d]
        }else{
        if(e[a]===undefined){
            e[a]=j.defaultValue
            }
        }
}
h.set(l||e);
h.dirty=false;
h.modified={};

if(h.getId()){
    h.phantom=false
    }
    if(typeof h.init=="function"){
    h.init()
    }
    h.id=h.modelName+"-"+h.internalId
},
get:function(a){
    return this[this.persistenceProperty][a]
    },
set:function(k,f){
    var d=this,c=d.fields,j=d.modified,b=[],e,h,a,g;
    if(arguments.length==1&&Ext.isObject(k)){
        for(h in k){
            if(k.hasOwnProperty(h)){
                e=c.get(h);
                if(e&&e.convert!==e.type.convert){
                    b.push(h);
                    continue
                }
                d.set(h,k[h])
                }
            }
        for(a=0;a<b.length;a++){
        e=b[a];
        d.set(e,k[e])
        }
    }else{
    if(c){
        e=c.get(k);
        if(e&&e.convert){
            f=e.convert(f,d)
            }
        }
    g=d.get(k);
d[d.persistenceProperty][k]=f;
if(e&&e.persist&&!d.isEqual(g,f)){
    if(d.isModified(k)){
        if(d.isEqual(j[k],f)){
            delete j[k];
            d.dirty=false;
            for(h in j){
                if(j.hasOwnProperty(h)){
                    d.dirty=true;
                    break
                }
            }
            }
        }else{
    d.dirty=true;
    j[k]=g
    }
}
if(!d.editing){
    d.afterEdit()
    }
}
},
isEqual:function(d,c){
    if(Ext.isDate(d)&&Ext.isDate(c)){
        return d.getTime()===c.getTime()
        }
        return d===c
    },
beginEdit:function(){
    var a=this;
    if(!a.editing){
        a.editing=true;
        a.dirtySave=a.dirty;
        a.dataSave=Ext.apply({},a[a.persistenceProperty]);
        a.modifiedSave=Ext.apply({},a.modified)
        }
    },
cancelEdit:function(){
    var a=this;
    if(a.editing){
        a.editing=false;
        a.modified=a.modifiedSave;
        a[a.persistenceProperty]=a.dataSave;
        a.dirty=a.dirtySave;
        delete a.modifiedSave;
        delete a.dataSave;
        delete a.dirtySave
        }
    },
endEdit:function(a){
    var b=this;
    if(b.editing){
        b.editing=false;
        delete b.modifiedSave;
        delete b.dataSave;
        delete b.dirtySave;
        if(a!==true&&b.dirty){
            b.afterEdit()
            }
        }
},
getChanges:function(){
    var a=this.modified,b={},c;
    for(c in a){
        if(a.hasOwnProperty(c)){
            b[c]=this.get(c)
            }
        }
    return b
},
isModified:function(a){
    return this.modified.hasOwnProperty(a)
    },
setDirty:function(){
    var b=this,a;
    b.dirty=true;
    b.fields.each(function(c){
        if(c.persist){
            a=c.name;
            b.modified[a]=b.get(a)
            }
        },b)
},
reject:function(a){
    var c=this,b=c.modified,d;
    for(d in b){
        if(b.hasOwnProperty(d)){
            if(typeof b[d]!="function"){
                c[c.persistenceProperty][d]=b[d]
                }
            }
    }
    c.dirty=false;
c.editing=false;
c.modified={};

if(a!==true){
    c.afterReject()
    }
},
commit:function(a){
    var b=this;
    b.dirty=false;
    b.editing=false;
    b.modified={};
    
    if(a!==true){
        b.afterCommit()
        }
    },
copy:function(a){
    var b=this;
    return new b.self(Ext.apply({},b[b.persistenceProperty]),a||b.internalId)
    },
setProxy:function(a){
    if(!a.isProxy){
        if(typeof a==="string"){
            a={
                type:a
            }
        }
        a=Ext.createByAlias("proxy."+a.type,a)
    }
    a.setModel(this.self);
this.proxy=a;
return a
},
getProxy:function(){
    return this.proxy
    },
validate:function(){
    var j=Ext.create("Ext.data.Errors"),c=this.validations,e=Ext.data.validations,b,d,h,a,g,f;
    if(c){
        b=c.length;
        for(f=0;f<b;f++){
            d=c[f];
            h=d.field||d.name;
            g=d.type;
            a=e[g](d,this.get(h));
            if(!a){
                j.add({
                    field:h,
                    message:d.message||e[g+"Message"]
                    })
                }
            }
        }
    return j
},
isValid:function(){
    return this.validate().isValid()
    },
save:function(c){
    c=Ext.apply({},c);
    var e=this,f=e.phantom?"create":"update",a=null,d=c.scope||e,b,g;
    Ext.apply(c,{
        records:[e],
        action:f
    });
    b=Ext.create("Ext.data.Operation",c);
    g=function(h){
        if(h.wasSuccessful()){
            a=h.getRecords()[0];
            e.set(a.data);
            a.dirty=false;
            Ext.callback(c.success,d,[a,h])
            }else{
            Ext.callback(c.failure,d,[a,h])
            }
            Ext.callback(c.callback,d,[a,h])
        };
        
    e.getProxy()[f](b,g,e);
    return e
    },
destroy:function(c){
    c=Ext.apply({},c);
    var e=this,a=null,d=c.scope||e,b,f;
    Ext.apply(c,{
        records:[e],
        action:"destroy"
    });
    b=Ext.create("Ext.data.Operation",c);
    f=function(g){
        if(g.wasSuccessful()){
            Ext.callback(c.success,d,[a,g])
            }else{
            Ext.callback(c.failure,d,[a,g])
            }
            Ext.callback(c.callback,d,[a,g])
        };
        
    e.getProxy().destroy(b,f,e);
    return e
    },
getId:function(){
    return this.get(this.idProperty)
    },
setId:function(a){
    this.set(this.idProperty,a)
    },
join:function(a){
    this.store=a
    },
unjoin:function(){
    delete this.store
    },
afterEdit:function(){
    this.callStore("afterEdit")
    },
afterReject:function(){
    this.callStore("afterReject")
    },
afterCommit:function(){
    this.callStore("afterCommit")
    },
callStore:function(b){
    var a=this.store;
    if(a!==undefined&&typeof a[b]=="function"){
        a[b](this)
        }
    },
getAssociatedData:function(){
    return this.prepareAssociatedData(this,[],null)
    },
prepareAssociatedData:function(p,b,o){
    var k=p.associations.items,m=k.length,f={},g,a,h,r,s,e,d,n,l,q,c;
    for(n=0;n<m;n++){
        e=k[n];
        q=e.type;
        c=true;
        if(o){
            c=q==o
            }
            if(c&&q=="hasMany"){
            g=p[e.storeName];
            f[e.name]=[];
            if(g&&g.data.length>0){
                h=g.data.items;
                s=h.length;
                for(l=0;l<s;l++){
                    r=h[l];
                    d=r.id;
                    if(Ext.Array.indexOf(b,d)==-1){
                        b.push(d);
                        f[e.name][l]=r.data;
                        Ext.apply(f[e.name][l],this.prepareAssociatedData(r,b,q))
                        }
                    }
                }
        }else{
    if(c&&q=="belongsTo"){
        r=p[e.instanceName];
        if(r!==undefined){
            d=r.id;
            if(Ext.Array.indexOf(b,d)==-1){
                b.push(d);
                f[e.name]=r.data;
                Ext.apply(f[e.name],this.prepareAssociatedData(r,b,q))
                }
            }
    }
}
}
return f
}
});
Ext.define("Ext.data.Store",{
    extend:"Ext.data.AbstractStore",
    alias:"store.store",
    requires:["Ext.ModelManager","Ext.data.Model","Ext.util.Grouper"],
    uses:["Ext.data.proxy.Memory"],
    remoteSort:false,
    remoteFilter:false,
    remoteGroup:false,
    groupField:undefined,
    groupDir:"ASC",
    pageSize:25,
    currentPage:1,
    clearOnPageLoad:true,
    loading:false,
    sortOnFilter:true,
    buffered:false,
    purgePageCount:5,
    isStore:true,
    constructor:function(b){
        b=b||{};
        
        var d=this,f=b.groupers||d.groupers,a=b.groupField||d.groupField,c,e;
        if(b.buffered||d.buffered){
            d.prefetchData=Ext.create("Ext.util.MixedCollection",false,function(g){
                return g.index
                });
            d.pendingRequests=[];
            d.pagesRequested=[];
            d.sortOnLoad=false;
            d.filterOnLoad=false
            }
            d.addEvents("beforeprefetch","groupchange","prefetch");
        e=b.data||d.data;
        d.data=Ext.create("Ext.util.MixedCollection",false,function(g){
            return g.internalId
            });
        if(e){
            d.inlineData=e;
            delete b.data
            }
            if(!f&&a){
            f=[{
                property:a,
                direction:b.groupDir||d.groupDir
                }]
            }
            delete b.groupers;
        d.groupers=Ext.create("Ext.util.MixedCollection");
        d.groupers.addAll(d.decodeGroupers(f));
        this.callParent([b]);
        if(d.groupers.items.length){
            d.sort(d.groupers.items,"prepend",false)
            }
            c=d.proxy;
        e=d.inlineData;
        if(e){
            if(c instanceof Ext.data.proxy.Memory){
                c.data=e;
                d.read()
                }else{
                d.add.apply(d,e)
                }
                d.sort();
            delete d.inlineData
            }else{
            if(d.autoLoad){
                Ext.defer(d.load,10,d,[typeof d.autoLoad==="object"?d.autoLoad:undefined])
                }
            }
    },
onBeforeSort:function(){
    this.sort(this.groupers.items,"prepend",false)
    },
decodeGroupers:function(d){
    if(!Ext.isArray(d)){
        if(d===undefined){
            d=[]
            }else{
            d=[d]
            }
        }
    var c=d.length,e=Ext.util.Grouper,a,b;
for(b=0;b<c;b++){
    a=d[b];
    if(!(a instanceof e)){
        if(Ext.isString(a)){
            a={
                property:a
            }
        }
        Ext.applyIf(a,{
        root:"data",
        direction:"ASC"
    });
    if(a.fn){
        a.sorterFn=a.fn
        }
        if(typeof a=="function"){
        a={
            sorterFn:a
        }
    }
    d[b]=new e(a)
    }
}
return d
},
group:function(d,e){
    var c=this,b,a;
    if(Ext.isArray(d)){
        a=d
        }else{
        if(Ext.isObject(d)){
            a=[d]
            }else{
            if(Ext.isString(d)){
                b=c.groupers.get(d);
                if(!b){
                    b={
                        property:d,
                        direction:e
                    };
                    
                    a=[b]
                    }else{
                    if(e===undefined){
                        b.toggle()
                        }else{
                        b.setDirection(e)
                        }
                    }
            }
    }
}
if(a&&a.length){
    a=c.decodeGroupers(a);
    c.groupers.clear();
    c.groupers.addAll(a)
    }
    if(c.remoteGroup){
    c.load({
        scope:c,
        callback:c.fireGroupChange
        })
    }else{
    c.sort();
    c.fireEvent("groupchange",c,c.groupers)
    }
},
clearGrouping:function(){
    var a=this;
    a.groupers.each(function(b){
        a.sorters.remove(b)
        });
    a.groupers.clear();
    if(a.remoteGroup){
        a.load({
            scope:a,
            callback:a.fireGroupChange
            })
        }else{
        a.sort();
        a.fireEvent("groupchange",a,a.groupers)
        }
    },
isGrouped:function(){
    return this.groupers.getCount()>0
    },
fireGroupChange:function(){
    this.fireEvent("groupchange",this,this.groupers)
    },
getGroups:function(b){
    var d=this.data.items,a=d.length,c=[],j={},f,g,h,e;
    for(e=0;e<a;e++){
        f=d[e];
        g=this.getGroupString(f);
        h=j[g];
        if(h===undefined){
            h={
                name:g,
                children:[]
            };
            
            c.push(h);
            j[g]=h
            }
            h.children.push(f)
        }
        return b?j[b]:c
    },
getGroupsForGrouper:function(f,b){
    var d=f.length,e=[],a,c,h,j,g;
    for(g=0;g<d;g++){
        h=f[g];
        c=b.getGroupString(h);
        if(c!==a){
            j={
                name:c,
                grouper:b,
                records:[]
            };
            
            e.push(j)
            }
            j.records.push(h);
        a=c
        }
        return e
    },
getGroupsForGrouperIndex:function(c,h){
    var f=this,g=f.groupers,b=g.getAt(h),a=f.getGroupsForGrouper(c,b),e=a.length,d;
    if(h+1<g.length){
        for(d=0;d<e;d++){
            a[d].children=f.getGroupsForGrouperIndex(a[d].records,h+1)
            }
        }
        for(d=0;d<e;d++){
    a[d].depth=h
    }
    return a
},
getGroupData:function(a){
    var b=this;
    if(a!==false){
        b.sort()
        }
        return b.getGroupsForGrouperIndex(b.data.items,0)
    },
getGroupString:function(a){
    var b=this.groupers.first();
    if(b){
        return a.get(b.property)
        }
        return""
    },
insert:function(d,c){
    var g=this,f=false,e,b,a;
    c=[].concat(c);
    for(e=0,a=c.length;e<a;e++){
        b=g.createModel(c[e]);
        b.set(g.modelDefaults);
        c[e]=b;
        g.data.insert(d+e,b);
        b.join(g);
        f=f||b.phantom===true
        }
        if(g.snapshot){
        g.snapshot.addAll(c)
        }
        g.fireEvent("add",g,c,d);
    g.fireEvent("datachanged",g);
    if(g.autoSync&&f){
        g.sync()
        }
    },
add:function(b){
    if(!Ext.isArray(b)){
        b=Array.prototype.slice.apply(arguments)
        }
        var e=this,c=0,d=b.length,a;
    for(;c<d;c++){
        a=e.createModel(b[c]);
        b[c]=a
        }
        e.insert(e.data.length,b);
    return b
    },
createModel:function(a){
    if(!a.isModel){
        a=Ext.ModelManager.create(a,this.model)
        }
        return a
    },
each:function(b,a){
    this.data.each(b,a)
    },
remove:function(b,j){
    if(!Ext.isArray(b)){
        b=[b]
        }
        j=j===true;
    var f=this,g=false,c=0,a=b.length,h,e,d;
    for(;c<a;c++){
        d=b[c];
        e=f.data.indexOf(d);
        if(f.snapshot){
            f.snapshot.remove(d)
            }
            if(e>-1){
            h=d.phantom===true;
            if(!j&&!h){
                f.removed.push(d)
                }
                d.unjoin(f);
            f.data.remove(d);
            g=g||!h;
            f.fireEvent("remove",f,d,e)
            }
        }
    f.fireEvent("datachanged",f);
if(!j&&f.autoSync&&g){
    f.sync()
    }
},
removeAt:function(b){
    var a=this.getAt(b);
    if(a){
        this.remove(a)
        }
    },
load:function(a){
    var b=this;
    a=a||{};
    
    if(Ext.isFunction(a)){
        a={
            callback:a
        }
    }
    Ext.applyIf(a,{
    groupers:b.groupers.items,
    page:b.currentPage,
    start:(b.currentPage-1)*b.pageSize,
    limit:b.pageSize,
    addRecords:false
});
return b.callParent([a])
},
onProxyLoad:function(b){
    var d=this,c=b.getResultSet(),a=b.getRecords(),e=b.wasSuccessful();
    if(c){
        d.totalCount=c.total
        }
        if(e){
        d.loadRecords(a,b)
        }
        d.loading=false;
    d.fireEvent("load",d,a,e);
    d.fireEvent("read",d,a,b.wasSuccessful());
    Ext.callback(b.callback,b.scope||d,[a,b,e])
    },
onCreateRecords:function(d,e,l){
    if(l){
        var g=0,f=this.data,a=this.snapshot,b=d.length,k=e.records,h,c,j;
        for(;g<b;++g){
            h=d[g];
            c=k[g];
            if(c){
                j=f.indexOf(c);
                if(j>-1){
                    f.removeAt(j);
                    f.insert(j,h)
                    }
                    if(a){
                    j=a.indexOf(c);
                    if(j>-1){
                        a.removeAt(j);
                        a.insert(j,h)
                        }
                    }
                h.phantom=false;
            h.join(this)
            }
        }
    }
},
onUpdateRecords:function(d,c,h){
    if(h){
        var e=0,f=d.length,g=this.data,b=this.snapshot,a;
        for(;e<f;++e){
            a=d[e];
            g.replace(a);
            if(b){
                b.replace(a)
                }
                a.join(this)
            }
        }
    },
onDestroyRecords:function(c,d,j){
    if(j){
        var h=this,f=0,b=c.length,e=h.data,a=h.snapshot,g;
        for(;f<b;++f){
            g=c[f];
            g.unjoin(h);
            e.remove(g);
            if(a){
                a.remove(g)
                }
            }
        h.removed=[]
    }
},
getNewRecords:function(){
    return this.data.filterBy(this.filterNew).items
    },
getUpdatedRecords:function(){
    return this.data.filterBy(this.filterUpdated).items
    },
filter:function(e,f){
    if(Ext.isString(e)){
        e={
            property:e,
            value:f
        }
    }
    var d=this,a=d.decodeFilters(e),b=0,g=d.sortOnFilter&&!d.remoteSort,c=a.length;
for(;b<c;b++){
    d.filters.replace(a[b])
    }
    if(d.remoteFilter){
    d.load()
    }
    else{
    if(d.filters.getCount()){
        d.snapshot=d.snapshot||d.data.clone();
        d.data=d.data.filter(d.filters.items);
        if(g){
            d.sort()
            }
            if(!g||d.sorters.length<1){
            d.fireEvent("datachanged",d)
            }
        }
}
},
clearFilter:function(a){
    var b=this;
    b.filters.clear();
    if(b.remoteFilter){
        b.load()
        }else{
        if(b.isFiltered()){
            b.data=b.snapshot.clone();
            delete b.snapshot;
            if(a!==true){
                b.fireEvent("datachanged",b)
                }
            }
    }
},
isFiltered:function(){
    var a=this.snapshot;
    return !!a&&a!==this.data
    },
filterBy:function(b,a){
    var c=this;
    c.snapshot=c.snapshot||c.data.clone();
    c.data=c.queryBy(b,a||c);
    c.fireEvent("datachanged",c)
    },
queryBy:function(b,a){
    var c=this,d=c.snapshot||c.data;
    return d.filterBy(b,a||c)
    },
loadData:function(f,a){
    var c=this.model,e=f.length,d,b;
    for(d=0;d<e;d++){
        b=f[d];
        if(!(b instanceof Ext.data.Model)){
            f[d]=Ext.ModelManager.create(b,c)
            }
        }
    this.loadRecords(f,{
    addRecords:a
})
},
loadRecords:function(a,b){
    var e=this,c=0,d=a.length;
    b=b||{};
    
    if(!b.addRecords){
        delete e.snapshot;
        e.clearData()
        }
        e.data.addAll(a);
    for(;c<d;c++){
        if(b.start!==undefined){
            a[c].index=b.start+c
            }
            a[c].join(e)
        }
        e.suspendEvents();
    if(e.filterOnLoad&&!e.remoteFilter){
        e.filter()
        }
        if(e.sortOnLoad&&!e.remoteSort){
        e.sort()
        }
        e.resumeEvents();
    e.fireEvent("datachanged",e,a)
    },
loadPage:function(b){
    var a=this;
    a.currentPage=b;
    a.read({
        page:b,
        start:(b-1)*a.pageSize,
        limit:a.pageSize,
        addRecords:!a.clearOnPageLoad
        })
    },
nextPage:function(){
    this.loadPage(this.currentPage+1)
    },
previousPage:function(){
    this.loadPage(this.currentPage-1)
    },
clearData:function(){
    this.data.each(function(a){
        a.unjoin()
        });
    this.data.clear()
    },
prefetch:function(b){
    var c=this,a,d=c.getRequestId();
    b=b||{};
    
    Ext.applyIf(b,{
        action:"read",
        filters:c.filters.items,
        sorters:c.sorters.items,
        requestId:d
    });
    c.pendingRequests.push(d);
    a=Ext.create("Ext.data.Operation",b);
    if(c.fireEvent("beforeprefetch",c,a)!==false){
        c.loading=true;
        c.proxy.read(a,c.onProxyPrefetch,c)
        }
        return c
    },
prefetchPage:function(e,c){
    var d=this,b=d.pageSize,f=(e-1)*d.pageSize,a=f+b;
    if(Ext.Array.indexOf(d.pagesRequested,e)===-1&&!d.rangeSatisfied(f,a)){
        c=c||{};
        
        d.pagesRequested.push(e);
        Ext.applyIf(c,{
            page:e,
            start:f,
            limit:b,
            callback:d.onWaitForGuarantee,
            scope:d
        });
        d.prefetch(c)
        }
    },
getRequestId:function(){
    this.requestSeed=this.requestSeed||1;
    return this.requestSeed++
    },
onProxyPrefetch:function(b){
    var d=this,c=b.getResultSet(),a=b.getRecords(),e=b.wasSuccessful();
    if(c){
        d.totalCount=c.total;
        d.fireEvent("totalcountchange",d.totalCount)
        }
        if(e){
        d.cacheRecords(a,b)
        }
        Ext.Array.remove(d.pendingRequests,b.requestId);
    if(b.page){
        Ext.Array.remove(d.pagesRequested,b.page)
        }
        d.loading=false;
    d.fireEvent("prefetch",d,a,e,b);
    if(b.blocking){
        d.fireEvent("load",d,a,e)
        }
        Ext.callback(b.callback,b.scope||d,[a,b,e])
    },
cacheRecords:function(b,a){
    var e=this,c=0,d=b.length,f=a?a.start:0;
    if(!Ext.isDefined(e.totalCount)){
        e.totalCount=b.length;
        e.fireEvent("totalcountchange",e.totalCount)
        }
        for(;c<d;c++){
        b[c].index=f+c
        }
        e.prefetchData.addAll(b);
    if(e.purgePageCount){
        e.purgeRecords()
        }
    },
purgeRecords:function(){
    var c=this,b=c.prefetchData.getCount(),d=c.purgePageCount*c.pageSize,e=b-d-1,a=0;
    for(;a<=e;a++){
        c.prefetchData.removeAt(0)
        }
    },
rangeSatisfied:function(e,a){
    var c=this,b=e,d=true;
    for(;b<a;b++){
        if(!c.prefetchData.getByKey(b)){
            d=false;
            break
        }
    }
    return d
},
getPageFromRecordIndex:function(a){
    return Math.floor(a/this.pageSize)+1
    },
onGuaranteedRange:function(){
    var f=this,c=f.getTotalCount(),g=f.requestStart,b=((c-1)<f.requestEnd)?c-1:f.requestEnd,d=[],a,e=g;
    if(g!==f.guaranteedStart&&b!==f.guaranteedEnd){
        f.guaranteedStart=g;
        f.guaranteedEnd=b;
        for(;e<=b;e++){
            a=f.prefetchData.getByKey(e);
            d.push(a)
            }
            f.fireEvent("guaranteedrange",d,g,b);
        if(f.cb){
            f.cb.call(f.scope||f,d)
            }
        }
    f.unmask()
},
mask:function(){
    this.masked=true;
    this.fireEvent("beforeload")
    },
unmask:function(){
    if(this.masked){
        this.fireEvent("load")
        }
    },
hasPendingRequests:function(){
    return this.pendingRequests.length
    },
onWaitForGuarantee:function(){
    if(!this.hasPendingRequests()){
        this.onGuaranteedRange()
        }
    },
guaranteeRange:function(a,c,b,m){
    c=(c>this.totalCount)?this.totalCount-1:c;
    var h=this,d=a,k=h.prefetchData,e=[],g=!!k.getByKey(a),j=!!k.getByKey(c),f=h.getPageFromRecordIndex(a),l=h.getPageFromRecordIndex(c);
    h.cb=b;
    h.scope=m;
    h.requestStart=a;
    h.requestEnd=c;
    if(!g||!j){
        if(f===l){
            h.mask();
            h.prefetchPage(f,{
                callback:h.onWaitForGuarantee,
                scope:h
            })
            }else{
            h.mask();
            h.prefetchPage(f,{
                callback:h.onWaitForGuarantee,
                scope:h
            });
            h.prefetchPage(l,{
                callback:h.onWaitForGuarantee,
                scope:h
            })
            }
        }else{
    h.onGuaranteedRange()
    }
},
sort:function(){
    var d=this,c=d.prefetchData,e,f,a,b;
    if(d.buffered){
        if(d.remoteSort){
            c.clear();
            d.callParent(arguments)
            }else{
            e=d.getSorters();
            f=d.guaranteedStart;
            a=d.guaranteedEnd;
            if(e.length){
                c.sort(e);
                b=c.getRange();
                c.clear();
                d.cacheRecords(b);
                delete d.guaranteedStart;
                delete d.guaranteedEnd;
                d.guaranteeRange(f,a)
                }
                d.callParent(arguments)
            }
        }else{
    d.callParent(arguments)
    }
},
doSort:function(b){
    var e=this;
    if(e.remoteSort){
        e.load()
        }else{
        e.data.sortBy(b);
        if(!e.buffered){
            var a=e.getRange(),d=a.length,c=0;
            for(;c<d;c++){
                a[c].index=c
                }
            }
            e.fireEvent("datachanged",e)
    }
},
find:function(e,d,g,f,a,c){
    var b=this.createFilterFn(e,d,f,a,c);
    return b?this.data.findIndexBy(b,null,g):-1
    },
findRecord:function(){
    var b=this,a=b.find.apply(b,arguments);
    return a!==-1?b.getAt(a):null
    },
createFilterFn:function(d,c,e,a,b){
    if(Ext.isEmpty(c)){
        return false
        }
        c=this.data.createValueMatcher(c,e,a,b);
    return function(f){
        return c.test(f.data[d])
        }
    },
findExact:function(b,a,c){
    return this.data.findIndexBy(function(d){
        return d.get(b)===a
        },this,c)
    },
findBy:function(b,a,c){
    return this.data.findIndexBy(b,a,c)
    },
collect:function(b,a,c){
    var d=this,e=(c===true&&d.snapshot)?d.snapshot:d.data;
    return e.collect(b,"data",a)
    },
getCount:function(){
    return this.data.length||0
    },
getTotalCount:function(){
    return this.totalCount
    },
getAt:function(a){
    return this.data.getAt(a)
    },
getRange:function(b,a){
    return this.data.getRange(b,a)
    },
getById:function(a){
    return(this.snapshot||this.data).findBy(function(b){
        return b.getId()===a
        })
    },
indexOf:function(a){
    return this.data.indexOf(a)
    },
indexOfTotal:function(a){
    return a.index||this.indexOf(a)
    },
indexOfId:function(a){
    return this.data.indexOfKey(a)
    },
removeAll:function(a){
    var b=this;
    b.clearData();
    if(b.snapshot){
        b.snapshot.clear()
        }
        if(a!==true){
        b.fireEvent("clear",b)
        }
    },
first:function(a){
    var b=this;
    if(a&&b.isGrouped()){
        return b.aggregate(function(c){
            return c.length?c[0]:undefined
            },b,true)
        }else{
        return b.data.first()
        }
    },
last:function(a){
    var b=this;
    if(a&&b.isGrouped()){
        return b.aggregate(function(d){
            var c=d.length;
            return c?d[c-1]:undefined
            },b,true)
        }else{
        return b.data.last()
        }
    },
sum:function(c,a){
    var b=this;
    if(a&&b.isGrouped()){
        return b.aggregate(b.getSum,b,true,[c])
        }else{
        return b.getSum(b.data.items,c)
        }
    },
getSum:function(b,e){
    var d=0,c=0,a=b.length;
    for(;c<a;++c){
        d+=b[c].get(e)
        }
        return d
    },
count:function(a){
    var b=this;
    if(a&&b.isGrouped()){
        return b.aggregate(function(c){
            return c.length
            },b,true)
        }else{
        return b.getCount()
        }
    },
min:function(c,a){
    var b=this;
    if(a&&b.isGrouped()){
        return b.aggregate(b.getMin,b,true,[c])
        }else{
        return b.getMin(b.data.items,c)
        }
    },
getMin:function(b,f){
    var d=1,a=b.length,e,c;
    if(a>0){
        c=b[0].get(f)
        }
        for(;d<a;++d){
        e=b[d].get(f);
        if(e<c){
            c=e
            }
        }
    return c
},
max:function(c,a){
    var b=this;
    if(a&&b.isGrouped()){
        return b.aggregate(b.getMax,b,true,[c])
        }else{
        return b.getMax(b.data.items,c)
        }
    },
getMax:function(c,f){
    var d=1,b=c.length,e,a;
    if(b>0){
        a=c[0].get(f)
        }
        for(;d<b;++d){
        e=c[d].get(f);
        if(e>a){
            a=e
            }
        }
    return a
},
average:function(c,a){
    var b=this;
    if(a&&b.isGrouped()){
        return b.aggregate(b.getAverage,b,true,[c])
        }else{
        return b.getAverage(b.data.items,c)
        }
    },
getAverage:function(b,e){
    var c=0,a=b.length,d=0;
    if(b.length>0){
        for(;c<a;++c){
            d+=b[c].get(e)
            }
            return d/a
        }
        return 0
    },
aggregate:function(g,j,e,f){
    f=f||[];
    if(e&&this.isGrouped()){
        var a=this.getGroups(),c=0,d=a.length,b={},h;
        for(;c<d;++c){
            h=a[c];
            b[h.name]=g.apply(j||this,[h.children].concat(f))
            }
            return b
        }else{
        return g.apply(j||this,[this.data.items].concat(f))
        }
    }
});
Ext.define("Ext.data.JsonStore",{
    extend:"Ext.data.Store",
    alias:"store.json",
    constructor:function(a){
        a=a||{};
        
        Ext.applyIf(a,{
            proxy:{
                type:"ajax",
                reader:"json",
                writer:"json"
            }
        });
    this.callParent([a])
    }
});
Ext.define("Ext.app.ChartPortlet",{
    extend:"Ext.panel.Panel",
    alias:"widget.chartportlet",
    requires:["Ext.data.JsonStore","Ext.chart.theme.Base","Ext.chart.series.Series","Ext.chart.series.Line","Ext.chart.axis.Numeric"],
    generateData:function(){
        var b=[{
            name:"x",
            djia:10000,
            sp500:1100
        }],a;
        for(a=1;a<50;a++){
            b.push({
                name:"x",
                sp500:b[a-1].sp500+((Math.floor(Math.random()*2)%2)?-1:1)*Math.floor(Math.random()*7),
                djia:b[a-1].djia+((Math.floor(Math.random()*2)%2)?-1:1)*Math.floor(Math.random()*7)
                })
            }
            return b
        },
    initComponent:function(){
        Ext.apply(this,{
            layout:"fit",
            width:600,
            height:300,
            items:{
                xtype:"chart",
                animate:false,
                shadow:false,
                store:Ext.create("Ext.data.JsonStore",{
                    fields:["name","sp500","djia"],
                    data:this.generateData()
                    }),
                legend:{
                    position:"bottom"
                },
                axes:[{
                    type:"Numeric",
                    position:"left",
                    fields:["djia"],
                    title:"Dow Jones Average",
                    label:{
                        font:"11px Arial"
                    }
                },{
                type:"Numeric",
                position:"right",
                grid:false,
                fields:["sp500"],
                title:"S&P 500",
                label:{
                    font:"11px Arial"
                }
            }],
        series:[{
            type:"line",
            lineWidth:1,
            showMarkers:false,
            fill:true,
            axis:"left",
            xField:"name",
            yField:"djia",
            style:{
                "stroke-width":1
            }
        },{
            type:"line",
            lineWidth:1,
            showMarkers:false,
            axis:"right",
            xField:"name",
            yField:"sp500",
            style:{
                "stroke-width":1
            }
        }]
    }
});
this.callParent(arguments)
}
});
Ext.define("Ext.util.Point",{
    extend:"Ext.util.Region",
    statics:{
        fromEvent:function(a){
            a=(a.changedTouches&&a.changedTouches.length>0)?a.changedTouches[0]:a;
            return new this(a.pageX,a.pageY)
            }
        },
constructor:function(a,b){
    this.callParent([b,a,b,a])
    },
toString:function(){
    return"Point["+this.x+","+this.y+"]"
    },
equals:function(a){
    return(this.x==a.x&&this.y==a.y)
    },
isWithin:function(b,a){
    if(!Ext.isObject(a)){
        a={
            x:a,
            y:a
        }
    }
    return(this.x<=b.x+a.x&&this.x>=b.x-a.x&&this.y<=b.y+a.y&&this.y>=b.y-a.y)
    },
roundedEquals:function(a){
    return(Math.round(this.x)==Math.round(a.x)&&Math.round(this.y)==Math.round(a.y))
    }
},function(){
    this.prototype.translate=Ext.util.Region.prototype.translateBy
    });
Ext.define("Ext.Layer",{
    uses:["Ext.Shadow"],
    statics:{
        shims:[]
    },
    extend:"Ext.core.Element",
    constructor:function(b,a){
        b=b||{};
        
        var c=this,d=Ext.core.DomHelper,f=b.parentEl,e=f?Ext.getDom(f):document.body,g=b.hideMode;
        if(a){
            c.dom=Ext.getDom(a)
            }
            if(!c.dom){
            c.dom=d.append(e,b.dh||{
                tag:"div",
                cls:Ext.baseCSSPrefix+"layer"
                })
            }else{
            c.addCls(Ext.baseCSSPrefix+"layer");
            if(!c.dom.parentNode){
                e.appendChild(c.dom)
                }
            }
        if(b.cls){
        c.addCls(b.cls)
        }
        c.constrain=b.constrain!==false;
    if(g){
        c.setVisibilityMode(Ext.core.Element[g.toUpperCase()]);
        if(c.visibilityMode==Ext.core.Element.ASCLASS){
            c.visibilityCls=b.visibilityCls
            }
        }else{
    if(b.useDisplay){
        c.setVisibilityMode(Ext.core.Element.DISPLAY)
        }else{
        c.setVisibilityMode(Ext.core.Element.VISIBILITY)
        }
    }
if(b.id){
    c.id=c.dom.id=b.id
    }else{
    c.id=Ext.id(c.dom)
    }
    c.position("absolute");
    if(b.shadow){
    c.shadowOffset=b.shadowOffset||4;
    c.shadow=Ext.create("Ext.Shadow",{
        offset:c.shadowOffset,
        mode:b.shadow
        });
    c.disableShadow()
    }else{
    c.shadowOffset=0
    }
    c.useShim=b.shim!==false&&Ext.useShims;
if(b.hidden===true){
    c.hide()
    }else{
    this.show()
    }
},
getZIndex:function(){
    return parseInt((this.getShim()||this).getStyle("z-index"),10)
    },
getShim:function(){
    var b=this,c,a;
    if(!b.useShim){
        return null
        }
        if(!b.shim){
        c=b.self.shims.shift();
        if(!c){
            c=b.createShim();
            c.enableDisplayMode("block");
            c.hide()
            }
            a=b.dom.parentNode;
        if(c.dom.parentNode!=a){
            a.insertBefore(c.dom,b.dom)
            }
            b.shim=c
        }
        return b.shim
    },
hideShim:function(){
    if(this.shim){
        this.shim.setDisplayed(false);
        this.self.shims.push(this.shim);
        delete this.shim
        }
    },
disableShadow:function(){
    if(this.shadow){
        this.shadowDisabled=true;
        this.shadow.hide();
        this.lastShadowOffset=this.shadowOffset;
        this.shadowOffset=0
        }
    },
enableShadow:function(a){
    if(this.shadow){
        this.shadowDisabled=false;
        this.shadowOffset=this.lastShadowOffset;
        delete this.lastShadowOffset;
        if(a){
            this.sync(true)
            }
        }
},
sync:function(b){
    var i=this,m=i.shadow,g,e,a;
    if(!this.updating&&this.isVisible()&&(m||this.useShim)){
        var d=this.getShim(),c=this.getLeft(true),n=this.getTop(true),k=this.getWidth(),f=this.getHeight(),j;
        if(m&&!this.shadowDisabled){
            if(b&&!m.isVisible()){
                m.show(this)
                }else{
                m.realign(c,n,k,f)
                }
                if(d){
                j=d.getStyle("z-index");
                if(j>i.zindex){
                    i.shim.setStyle("z-index",i.zindex-2)
                    }
                    d.show();
                if(m.isVisible()){
                    g=m.el.getXY();
                    e=d.dom.style;
                    a=m.el.getSize();
                    e.left=(g[0])+"px";
                    e.top=(g[1])+"px";
                    e.width=(a.width)+"px";
                    e.height=(a.height)+"px"
                    }else{
                    d.setSize(k,f);
                    d.setLeftTop(c,n)
                    }
                }
        }else{
    if(d){
        j=d.getStyle("z-index");
        if(j>i.zindex){
            i.shim.setStyle("z-index",i.zindex-2)
            }
            d.show();
        d.setSize(k,f);
        d.setLeftTop(c,n)
        }
    }
}
return this
},
remove:function(){
    this.hideUnders();
    this.callParent()
    },
beginUpdate:function(){
    this.updating=true
    },
endUpdate:function(){
    this.updating=false;
    this.sync(true)
    },
hideUnders:function(){
    if(this.shadow){
        this.shadow.hide()
        }
        this.hideShim()
    },
constrainXY:function(){
    if(this.constrain){
        var f=Ext.core.Element.getViewWidth(),b=Ext.core.Element.getViewHeight(),k=Ext.getDoc().getScroll(),j=this.getXY(),g=j[0],e=j[1],a=this.shadowOffset,i=this.dom.offsetWidth+a,c=this.dom.offsetHeight+a,d=false;
        if((g+i)>f+k.left){
            g=f-i-a;
            d=true
            }
            if((e+c)>b+k.top){
            e=b-c-a;
            d=true
            }
            if(g<k.left){
            g=k.left;
            d=true
            }
            if(e<k.top){
            e=k.top;
            d=true
            }
            if(d){
            Ext.Layer.superclass.setXY.call(this,[g,e]);
            this.sync()
            }
        }
    return this
},
getConstrainOffset:function(){
    return this.shadowOffset
    },
setVisible:function(e,b,d,g,f){
    var c=this,a;
    a=function(){
        if(e){
            c.sync(true)
            }
            if(g){
            g()
            }
        };
    
if(!e){
    this.hideUnders(true)
    }
    this.callParent([e,b,d,g,f]);
if(!b){
    a()
    }
    return this
},
beforeFx:function(){
    this.beforeAction();
    return this.callParent(arguments)
    },
afterFx:function(){
    this.callParent(arguments);
    this.sync(this.isVisible())
    },
beforeAction:function(){
    if(!this.updating&&this.shadow){
        this.shadow.hide()
        }
    },
setLeft:function(a){
    this.callParent(arguments);
    return this.sync()
    },
setTop:function(a){
    this.callParent(arguments);
    return this.sync()
    },
setLeftTop:function(b,a){
    this.callParent(arguments);
    return this.sync()
    },
setXY:function(c,a,b,e,d){
    e=this.createCB(e);
    this.fixDisplay();
    this.beforeAction();
    this.callParent([c,a,b,e,d]);
    if(!a){
        e()
        }
        return this
    },
createCB:function(c){
    var a=this,b=a.shadow&&a.shadow.isVisible();
    return function(){
        a.constrainXY();
        a.sync(b);
        if(c){
            c()
            }
        }
},
setX:function(a,b,c,e,d){
    this.setXY([a,this.getY()],b,c,e,d);
    return this
    },
setY:function(e,a,b,d,c){
    this.setXY([this.getX(),e],a,b,d,c);
    return this
    },
setSize:function(a,c,b,d,f,e){
    f=this.createCB(f);
    this.beforeAction();
    this.callParent([a,c,b,d,f,e]);
    if(!b){
        f()
        }
        return this
    },
setWidth:function(a,b,c,e,d){
    e=this.createCB(e);
    this.beforeAction();
    this.callParent([a,b,c,e,d]);
    if(!b){
        e()
        }
        return this
    },
setHeight:function(b,a,c,e,d){
    e=this.createCB(e);
    this.beforeAction();
    this.callParent([b,a,c,e,d]);
    if(!a){
        e()
        }
        return this
    },
setBounds:function(b,h,d,a,c,e,g,f){
    g=this.createCB(g);
    this.beforeAction();
    if(!c){
        Ext.Layer.superclass.setXY.call(this,[b,h]);
        Ext.Layer.superclass.setSize.call(this,d,a);
        g()
        }else{
        this.callParent([b,h,d,a,c,e,g,f])
        }
        return this
    },
setZIndex:function(a){
    this.zindex=a;
    if(this.getShim()){
        this.shim.setStyle("z-index",a++)
        }
        if(this.shadow){
        this.shadow.setZIndex(a++)
        }
        this.setStyle("z-index",a);
    return this
    }
});
Ext.define("Ext.data.ArrayStore",{
    extend:"Ext.data.Store",
    alias:"store.array",
    uses:["Ext.data.reader.Array"],
    constructor:function(a){
        a=a||{};
        
        Ext.applyIf(a,{
            proxy:{
                type:"memory",
                reader:"array"
            }
        });
    this.callParent([a])
    },
loadData:function(e,a){
    if(this.expandData===true){
        var d=[],b=0,c=e.length;
        for(;b<c;b++){
            d[d.length]=[e[b]]
            }
            e=d
        }
        this.callParent([e,a])
    }
},function(){
    Ext.data.SimpleStore=Ext.data.ArrayStore
    });
Ext.define("Ext.resizer.Resizer",{
    mixins:{
        observable:"Ext.util.Observable"
    },
    uses:["Ext.resizer.ResizeTracker","Ext.Component"],
    alternateClassName:"Ext.Resizable",
    handleCls:Ext.baseCSSPrefix+"resizable-handle",
    pinnedCls:Ext.baseCSSPrefix+"resizable-pinned",
    overCls:Ext.baseCSSPrefix+"resizable-over",
    proxyCls:Ext.baseCSSPrefix+"resizable-proxy",
    wrapCls:Ext.baseCSSPrefix+"resizable-wrap",
    dynamic:true,
    handles:"s e se",
    height:null,
    width:null,
    heightIncrement:0,
    widthIncrement:0,
    minHeight:20,
    minWidth:20,
    maxHeight:10000,
    maxWidth:10000,
    pinned:false,
    preserveRatio:false,
    transparent:false,
    possiblePositions:{
        n:"north",
        s:"south",
        e:"east",
        w:"west",
        se:"southeast",
        sw:"southwest",
        nw:"northwest",
        ne:"northeast"
    },
    constructor:function(a){
        var g=this,f,l,k=g.handles,b,j,e,c=0,h;
        this.addEvents("beforeresize","resizedrag","resize");
        if(Ext.isString(a)||Ext.isElement(a)||a.dom){
            f=a;
            a=arguments[1]||{};
            
            a.target=f
            }
            g.mixins.observable.constructor.call(g,a);
        f=g.target;
        if(f){
            if(f.isComponent){
                g.el=f.getEl();
                if(f.minWidth){
                    g.minWidth=f.minWidth
                    }
                    if(f.minHeight){
                    g.minHeight=f.minHeight
                    }
                    if(f.maxWidth){
                    g.maxWidth=f.maxWidth
                    }
                    if(f.maxHeight){
                    g.maxHeight=f.maxHeight
                    }
                    if(f.floating){
                    if(!this.hasOwnProperty("handles")){
                        this.handles="n ne e se s sw w nw"
                        }
                    }
            }else{
        g.el=g.target=Ext.get(f)
        }
    }else{
    g.target=g.el=Ext.get(g.el)
    }
    l=g.el.dom.tagName;
if(l=="TEXTAREA"||l=="IMG"){
    g.originalTarget=g.target;
    g.target=g.el=g.el.wrap({
        cls:g.wrapCls,
        id:g.el.id+"-rzwrap"
        });
    g.el.setPositioning(g.originalTarget.getPositioning());
    g.originalTarget.clearPositioning();
    var d=g.originalTarget.getBox();
    g.el.setBox(d)
    }
    g.el.position();
    if(g.pinned){
    g.el.addCls(g.pinnedCls)
    }
    g.resizeTracker=Ext.create("Ext.resizer.ResizeTracker",{
    disabled:g.disabled,
    target:g.target,
    constrainTo:g.constrainTo,
    overCls:g.overCls,
    throttle:g.throttle,
    originalTarget:g.originalTarget,
    delegate:"."+g.handleCls,
    dynamic:g.dynamic,
    preserveRatio:g.preserveRatio,
    heightIncrement:g.heightIncrement,
    widthIncrement:g.widthIncrement,
    minHeight:g.minHeight,
    maxHeight:g.maxHeight,
    minWidth:g.minWidth,
    maxWidth:g.maxWidth
    });
g.resizeTracker.on("mousedown",g.onBeforeResize,g);
    g.resizeTracker.on("drag",g.onResize,g);
    g.resizeTracker.on("dragend",g.onResizeEnd,g);
    if(g.handles=="all"){
    g.handles="n s e w ne nw se sw"
    }
    k=g.handles=g.handles.split(/ |\s*?[,;]\s*?/);
    j=g.possiblePositions;
    e=k.length;
    b=g.handleCls+" "+(this.target.isComponent?(g.target.baseCls+"-handle "):"")+g.handleCls+"-";
    for(;c<e;c++){
    if(k[c]&&j[k[c]]){
        h=j[k[c]];
        g[h]=Ext.create("Ext.Component",{
            owner:this,
            region:h,
            cls:b+h,
            renderTo:g.el
            });
        g[h].el.unselectable();
        if(g.transparent){
            g[h].el.setOpacity(0)
            }
        }
}
if(Ext.isNumber(g.width)){
    g.width=Ext.Number.constrain(g.width,g.minWidth,g.maxWidth)
    }
    if(Ext.isNumber(g.height)){
    g.height=Ext.Number.constrain(g.height,g.minHeight,g.maxHeight)
    }
    if(g.width!=null||g.height!=null){
    if(g.originalTarget){
        g.originalTarget.setWidth(g.width);
        g.originalTarget.setHeight(g.height)
        }
        g.resizeTo(g.width,g.height)
    }
    g.forceHandlesHeight()
},
disable:function(){
    this.resizeTracker.disable()
    },
enable:function(){
    this.resizeTracker.enable()
    },
onBeforeResize:function(c,d){
    var a=this.target.getBox();
    return this.fireEvent("beforeresize",this,a.width,a.height,d)
    },
onResize:function(d,f){
    var c=this,a=c.target.getBox();
    c.forceHandlesHeight();
    return c.fireEvent("resizedrag",c,a.width,a.height,f)
    },
onResizeEnd:function(d,f){
    var c=this,a=c.target.getBox();
    c.forceHandlesHeight();
    return c.fireEvent("resize",c,a.width,a.height,f)
    },
resizeTo:function(b,a){
    this.target.setSize(b,a);
    this.fireEvent("resize",this,b,a,null)
    },
getEl:function(){
    return this.el
    },
getTarget:function(){
    return this.target
    },
destroy:function(){
    var c;
    for(var b=0,a=this.handles.length;b<a;b++){
        c=this[this.possiblePositions[this.handles[b]]];
        delete c.owner;
        Ext.destroy(c)
        }
    },
forceHandlesHeight:function(){
    var a=this,b;
    if(Ext.isIE6){
        b=a.east;
        if(b){
            b.setHeight(a.el.getHeight())
            }
            b=a.west;
        if(b){
            b.setHeight(a.el.getHeight())
            }
            a.el.repaint()
        }
    }
});
Ext.define("Ext.resizer.SplitterTracker",{
    extend:"Ext.dd.DragTracker",
    requires:["Ext.util.Region"],
    enabled:true,
    overlayCls:Ext.baseCSSPrefix+"resizable-overlay",
    getPrevCmp:function(){
        var a=this.getSplitter();
        return a.previousSibling()
        },
    getNextCmp:function(){
        var a=this.getSplitter();
        return a.nextSibling()
        },
    onBeforeStart:function(g){
        var d=this,f=d.getPrevCmp(),a=d.getNextCmp(),c=d.getSplitter().collapseEl,b;
        if(c&&(g.getTarget()===d.getSplitter().collapseEl.dom)){
            return false
            }
            if(a.collapsed||f.collapsed){
            return false
            }
            b=d.overlay=Ext.getBody().createChild({
            cls:d.overlayCls,
            html:"&#160;"
        });
        b.unselectable();
        b.setSize(Ext.core.Element.getViewWidth(true),Ext.core.Element.getViewHeight(true));
        b.show();
        d.prevBox=f.getEl().getBox();
        d.nextBox=a.getEl().getBox();
        d.constrainTo=d.calculateConstrainRegion()
        },
    onStart:function(b){
        var a=this.getSplitter();
        a.addCls(a.baseCls+"-active")
        },
    calculateConstrainRegion:function(){
        var f=this,a=f.getSplitter(),g=a.getWidth(),h=a.defaultSplitMin,b=a.orientation,d=f.prevBox,i=f.getPrevCmp(),c=f.nextBox,e=f.getNextCmp(),k,j;
        if(b==="vertical"){
            k=Ext.create("Ext.util.Region",d.y,(i.maxWidth?d.x+i.maxWidth:c.right-(e.minWidth||h))+g,d.bottom,d.x+(i.minWidth||h));
            j=Ext.create("Ext.util.Region",c.y,c.right-(e.minWidth||h),c.bottom,(e.maxWidth?c.right-e.maxWidth:d.x+(d.minWidth||h))-g)
            }else{
            k=Ext.create("Ext.util.Region",d.y+(i.minHeight||h),d.right,(i.maxHeight?d.y+i.maxHeight:c.bottom-(e.minHeight||h))+g,d.x);
            j=Ext.create("Ext.util.Region",(e.maxHeight?c.bottom-e.maxHeight:d.y+(i.minHeight||h))-g,c.right,c.bottom-(e.minHeight||h),c.x)
            }
            return k.intersect(j)
        },
    performResize:function(g){
        var i=this,c=i.getOffset("dragTarget"),a=i.getSplitter(),d=a.orientation,j=i.getPrevCmp(),h=i.getNextCmp(),b=a.ownerCt,f=b.getLayout();
        b.suspendLayout=true;
        if(d==="vertical"){
            if(j){
                if(!j.maintainFlex){
                    delete j.flex;
                    j.setSize(i.prevBox.width+c[0],j.getHeight())
                    }
                }
            if(h){
            if(!h.maintainFlex){
                delete h.flex;
                h.setSize(i.nextBox.width-c[0],h.getHeight())
                }
            }
    }else{
    if(j){
        if(!j.maintainFlex){
            delete j.flex;
            j.setSize(j.getWidth(),i.prevBox.height+c[1])
            }
        }
    if(h){
    if(!h.maintainFlex){
        delete h.flex;
        h.setSize(j.getWidth(),i.nextBox.height-c[1])
        }
    }
}
delete b.suspendLayout;
f.onLayout()
},
endDrag:function(){
    var a=this;
    if(a.overlay){
        a.overlay.remove();
        delete a.overlay
        }
        a.callParent(arguments)
    },
onEnd:function(c){
    var a=this,b=a.getSplitter();
    b.removeCls(b.baseCls+"-active");
    a.performResize()
    },
onDrag:function(f){
    var c=this,g=c.getOffset("dragTarget"),d=c.getSplitter(),b=d.getEl(),a=d.orientation;
    if(a==="vertical"){
        b.setX(c.startRegion.left+g[0])
        }else{
        b.setY(c.startRegion.top+g[1])
        }
    },
getSplitter:function(){
    return Ext.getCmp(this.getDragCt().id)
    }
});
Ext.define("Ext.util.CSS",function(){
    var d=null;
    var c=document;
    var b=/(-[a-z])/gi;
    var a=function(e,f){
        return f.charAt(1).toUpperCase()
        };
        
    return{
        singleton:true,
        constructor:function(){
            this.rules={};
            
            this.initialized=false
            },
        createStyleSheet:function(h,k){
            var g,f=c.getElementsByTagName("head")[0],j=c.createElement("style");
            j.setAttribute("type","text/css");
            if(k){
                j.setAttribute("id",k)
                }
                if(Ext.isIE){
                f.appendChild(j);
                g=j.styleSheet;
                g.cssText=h
                }else{
                try{
                    j.appendChild(c.createTextNode(h))
                    }catch(i){
                    j.cssText=h
                    }
                    f.appendChild(j);
                g=j.styleSheet?j.styleSheet:(j.sheet||c.styleSheets[c.styleSheets.length-1])
                }
                this.cacheStyleSheet(g);
            return g
            },
        removeStyleSheet:function(f){
            var e=document.getElementById(f);
            if(e){
                e.parentNode.removeChild(e)
                }
            },
    swapStyleSheet:function(h,e){
        var g=document;
        this.removeStyleSheet(h);
        var f=g.createElement("link");
        f.setAttribute("rel","stylesheet");
        f.setAttribute("type","text/css");
        f.setAttribute("id",h);
        f.setAttribute("href",e);
        g.getElementsByTagName("head")[0].appendChild(f)
        },
    refreshCache:function(){
        return this.getRules(true)
        },
    cacheStyleSheet:function(k){
        if(!d){
            d={}
        }
        try{
        var n=k.cssRules||k.rules,l,h=n.length-1,f,g;
        for(;h>=0;--h){
            l=n[h].selectorText;
            if(l){
                l=l.split(",");
                g=l.length;
                for(f=0;f<g;f++){
                    d[Ext.String.trim(l[f]).toLowerCase()]=n[h]
                    }
                }
            }
    }catch(m){}
},
getRules:function(g){
    if(d===null||g){
        d={};
        
        var j=c.styleSheets,h=0,f=j.length;
        for(;h<f;h++){
            try{
                if(!j[h].disabled){
                    this.cacheStyleSheet(j[h])
                    }
                }catch(k){}
        }
    }
return d
},
getRule:function(e,g){
    var f=this.getRules(g);
    if(!Ext.isArray(e)){
        return f[e.toLowerCase()]
        }
        for(var h=0;h<e.length;h++){
        if(f[e[h]]){
            return f[e[h].toLowerCase()]
            }
        }
    return null
},
updateRule:function(e,h,g){
    if(!Ext.isArray(e)){
        var j=this.getRule(e);
        if(j){
            j.style[h.replace(b,a)]=g;
            return true
            }
        }else{
    for(var f=0;f<e.length;f++){
        if(this.updateRule(e[f],h,g)){
            return true
            }
        }
    }
return false
}
}
}());
Ext.define("Ext.selection.RowModel",{
    extend:"Ext.selection.Model",
    alias:"selection.rowmodel",
    requires:["Ext.util.KeyNav"],
    deltaScroll:5,
    enableKeyNav:true,
    constructor:function(){
        this.addEvents("beforedeselect","beforeselect","deselect","select");
        this.callParent(arguments)
        },
    bindComponent:function(a){
        var b=this;
        b.views=b.views||[];
        b.views.push(a);
        b.bind(a.getStore(),true);
        a.on({
            itemmousedown:b.onRowMouseDown,
            scope:b
        });
        if(b.enableKeyNav){
            b.initKeyNav(a)
            }
        },
initKeyNav:function(a){
    var b=this;
    if(!a.rendered){
        a.on("render",Ext.Function.bind(b.initKeyNav,b,[a],0),b,{
            single:true
        });
        return
    }
    a.el.set({
        tabIndex:-1
    });
    b.keyNav=new Ext.util.KeyNav(a.el,{
        up:b.onKeyUp,
        down:b.onKeyDown,
        right:b.onKeyRight,
        left:b.onKeyLeft,
        pageDown:b.onKeyPageDown,
        pageUp:b.onKeyPageUp,
        home:b.onKeyHome,
        end:b.onKeyEnd,
        scope:b
    });
    a.el.on(Ext.EventManager.getKeyEvent(),b.onKeyPress,b)
    },
getRowsVisible:function(){
    var e=false,a=this.views[0],d=a.getNode(0),b,c;
    if(d){
        b=Ext.fly(d).getHeight();
        c=a.el.getHeight();
        e=Math.floor(c/b)
        }
        return e
    },
onKeyEnd:function(d,a){
    var c=this,b=c.store.getAt(c.store.getCount()-1);
    if(b){
        if(d.shiftKey){
            c.selectRange(b,c.lastFocused||0);
            c.setLastFocused(b)
            }else{
            if(d.ctrlKey){
                c.setLastFocused(b)
                }else{
                c.doSelect(b)
                }
            }
    }
},
onKeyHome:function(c,a){
    var b=this,d=b.store.getAt(0);
    if(d){
        if(c.shiftKey){
            b.selectRange(d,b.lastFocused||0);
            b.setLastFocused(d)
            }else{
            if(c.ctrlKey){
                b.setLastFocused(d)
                }else{
                b.doSelect(d,false)
                }
            }
    }
},
onKeyPageUp:function(h,d){
    var g=this,i=g.getRowsVisible(),b,c,a,f;
    if(i){
        b=g.lastFocused?g.store.indexOf(g.lastFocused):0;
        c=b-i;
        if(c<0){
            c=0
            }
            a=g.store.getAt(c);
        if(h.shiftKey){
            f=g.store.getAt(b);
            g.selectRange(a,f,h.ctrlKey,"up");
            g.setLastFocused(a)
            }else{
            if(h.ctrlKey){
                h.preventDefault();
                g.setLastFocused(a)
                }else{
                g.doSelect(a)
                }
            }
    }
},
onKeyPageDown:function(h,b){
    var f=this,i=f.getRowsVisible(),a,g,d,c;
    if(i){
        a=f.lastFocused?f.store.indexOf(f.lastFocused):0;
        g=a+i;
        if(g>=f.store.getCount()){
            g=f.store.getCount()-1
            }
            d=f.store.getAt(g);
        if(h.shiftKey){
            c=f.store.getAt(a);
            f.selectRange(d,c,h.ctrlKey,"down");
            f.setLastFocused(d)
            }else{
            if(h.ctrlKey){
                h.preventDefault();
                f.setLastFocused(d)
                }else{
                f.doSelect(d)
                }
            }
    }
},
onKeyPress:function(d,b){
    if(d.getKey()===d.SPACE){
        d.stopEvent();
        var c=this,a=c.lastFocused;
        if(a){
            if(c.isSelected(a)){
                c.doDeselect(a,false)
                }else{
                c.doSelect(a,true)
                }
            }
    }
},
onKeyUp:function(g,d){
    var f=this,c=f.views[0],a=f.store.indexOf(f.lastFocused),b;
    if(a>0){
        b=f.store.getAt(a-1);
        if(g.shiftKey&&f.lastFocused){
            if(f.isSelected(f.lastFocused)&&f.isSelected(b)){
                f.doDeselect(f.lastFocused,true);
                f.setLastFocused(b)
                }else{
                if(!f.isSelected(f.lastFocused)){
                    f.doSelect(f.lastFocused,true);
                    f.doSelect(b,true)
                    }else{
                    f.doSelect(b,true)
                    }
                }
        }else{
    if(g.ctrlKey){
        f.setLastFocused(b)
        }else{
        f.doSelect(b)
        }
    }
}
},
onKeyDown:function(g,d){
    var f=this,c=f.views[0],a=f.store.indexOf(f.lastFocused),b;
    if(a+1<f.store.getCount()){
        b=f.store.getAt(a+1);
        if(f.selected.getCount()===0){
            f.doSelect(b)
            }else{
            if(g.shiftKey&&f.lastFocused){
                if(f.isSelected(f.lastFocused)&&f.isSelected(b)){
                    f.doDeselect(f.lastFocused,true);
                    f.setLastFocused(b)
                    }else{
                    if(!f.isSelected(f.lastFocused)){
                        f.doSelect(f.lastFocused,true);
                        f.doSelect(b,true)
                        }else{
                        f.doSelect(b,true)
                        }
                    }
            }else{
        if(g.ctrlKey){
            f.setLastFocused(b)
            }else{
            f.doSelect(b)
            }
        }
}
}
},
scrollByDeltaX:function(d){
    var a=this.views[0],c=a.up(),b=c.horizontalScroller;
    if(b){
        b.scrollByDeltaX(d)
        }
    },
onKeyLeft:function(b,a){
    this.scrollByDeltaX(-this.deltaScroll)
    },
onKeyRight:function(b,a){
    this.scrollByDeltaX(this.deltaScroll)
    },
onRowMouseDown:function(b,a,d,c,f){
    b.el.focus();
    this.selectWithEvent(a,f)
    },
onSelectChange:function(f,c,k,a){
    var h=this,l=h.views,d=l.length,j=h.store,b=j.indexOf(f),g=c?"select":"deselect",e=0;
    if((k||h.fireEvent("before"+g,h,f,b))!==false&&a()!==false){
        for(;e<d;e++){
            if(c){
                l[e].onRowSelect(b,k)
                }else{
                l[e].onRowDeselect(b,k)
                }
            }
        if(!k){
        h.fireEvent(g,h,f,b)
        }
    }
},
onLastFocusChanged:function(h,d,b){
    var a=this.views,g=a.length,c=this.store,f,e=0;
    if(h){
        f=c.indexOf(h);
        if(f!=-1){
            for(;e<g;e++){
                a[e].onRowFocus(f,false)
                }
            }
        }
if(d){
    f=c.indexOf(d);
    if(f!=-1){
        for(e=0;e<g;e++){
            a[e].onRowFocus(f,true,b)
            }
        }
    }
},
onEditorTab:function(h,f){
    var g=this,i=g.views[0],c=h.getActiveRecord(),b=h.getActiveColumn(),d=i.getPosition(c,b),j=f.shiftKey?"left":"right",a=i.walkCells(d,j,f,this.preventWrap);
    if(a){
        h.startEditByPosition(a)
        }
    },
selectByPosition:function(a){
    var b=this.store.getAt(a.row);
    this.select(b)
    }
});
Ext.define("Ext.grid.Scroller",{
    extend:"Ext.Component",
    alias:"widget.gridscroller",
    weight:110,
    cls:Ext.baseCSSPrefix+"scroller",
    focusable:false,
    reservedSpace:0,
    renderTpl:['<div class="'+Ext.baseCSSPrefix+'scroller-ct" id="{baseId}_ct">','<div class="'+Ext.baseCSSPrefix+'stretcher" id="{baseId}_stretch"></div>',"</div>"],
    initComponent:function(){
        var c=this,b=c.dock,a=Ext.baseCSSPrefix+"scroller-vertical",d="width";
        c.offsets={
            bottom:0
        };
        
        c.scrollProp="scrollTop";
        c.vertical=true;
        if(b==="top"||b==="bottom"){
            a=Ext.baseCSSPrefix+"scroller-horizontal";
            d="height";
            c.scrollProp="scrollLeft";
            c.vertical=false;
            c.weight+=5
            }
            c[d]=c.scrollerSize=Ext.getScrollbarSize()[d];
        c.cls+=(" "+a);
        Ext.applyIf(c.renderSelectors,{
            stretchEl:"."+Ext.baseCSSPrefix+"stretcher",
            scrollEl:"."+Ext.baseCSSPrefix+"scroller-ct"
            });
        c.callParent()
        },
    initRenderData:function(){
        var b=this,a=b.callParent(arguments)||{};
        
        a.baseId=b.id;
        return a
        },
    afterRender:function(){
        var a=this;
        a.callParent();
        a.mon(a.scrollEl,"scroll",a.onElScroll,a);
        Ext.cache[a.el.id].skipGarbageCollection=true
        },
    onAdded:function(a){
        this.ownerGrid=a;
        this.callParent(arguments)
        },
    getSizeCalculation:function(){
        var g=this,c=g.getPanel(),f=1,b=1,d,h;
        if(!g.vertical){
            var e=c.query("tableview"),a=e[1]||e[0];
            if(!a){
                return false
                }
                f=a.headerCt.getFullWidth();
            if(Ext.isIEQuirks){
                f--
            }
        }else{
        d=c.down("tableview:not([lockableInjected])");
        if(!d||!d.el){
            return false
            }
            h=d.el.child("table",true);
        if(!h){
            return false
            }
            b=h.offsetHeight
        }
        if(isNaN(f)){
        f=1
        }
        if(isNaN(b)){
        b=1
        }
        return{
        width:f,
        height:b
    }
},
invalidate:function(d){
    var f=this,e=f.stretchEl;
    if(!e||!f.ownerCt){
        return
    }
    var i=f.getSizeCalculation(),h=f.scrollEl,b=h.dom,c=f.reservedSpace,g,a=5;
    if(i){
        e.setSize(i);
        i=f.el.getSize(true);
        if(f.vertical){
            i.width+=a;
            i.height-=c;
            g="left"
            }else{
            i.width-=c;
            i.height+=a;
            g="top"
            }
            h.setSize(i);
        b.style[g]=(-a)+"px";
        b.scrollTop=b.scrollTop
        }
    },
afterComponentLayout:function(){
    this.callParent(arguments);
    this.invalidate()
    },
restoreScrollPos:function(){
    var c=this,b=this.scrollEl,a=b&&b.dom;
    if(c._scrollPos!==null&&a){
        a[c.scrollProp]=c._scrollPos;
        c._scrollPos=null
        }
    },
setReservedSpace:function(b){
    var a=this;
    if(a.reservedSpace!==b){
        a.reservedSpace=b;
        a.invalidate()
        }
    },
saveScrollPos:function(){
    var c=this,b=this.scrollEl,a=b&&b.dom;
    c._scrollPos=a?a[c.scrollProp]:null
    },
setScrollTop:function(c){
    var b=this.scrollEl,a=b&&b.dom;
    if(a){
        return a.scrollTop=Ext.Number.constrain(c,0,a.scrollHeight-a.clientHeight)
        }
    },
setScrollLeft:function(c){
    var b=this.scrollEl,a=b&&b.dom;
    if(a){
        return a.scrollLeft=Ext.Number.constrain(c,0,a.scrollWidth-a.clientWidth)
        }
    },
scrollByDeltaY:function(c){
    var b=this.scrollEl,a=b&&b.dom;
    if(a){
        return this.setScrollTop(a.scrollTop+c)
        }
    },
scrollByDeltaX:function(c){
    var b=this.scrollEl,a=b&&b.dom;
    if(a){
        return this.setScrollLeft(a.scrollLeft+c)
        }
    },
scrollToTop:function(){
    this.setScrollTop(0)
    },
onElScroll:function(a,b){
    this.fireEvent("bodyscroll",a,b)
    },
getPanel:function(){
    var a=this;
    if(!a.panel){
        a.panel=this.up("[scrollerOwner]")
        }
        return a.panel
    }
});
Ext.define("Ext.grid.header.Container",{
    extend:"Ext.container.Container",
    uses:["Ext.grid.ColumnLayout","Ext.grid.column.Column","Ext.menu.Menu","Ext.menu.CheckItem","Ext.menu.Separator","Ext.grid.plugin.HeaderResizer","Ext.grid.plugin.HeaderReorderer"],
    border:true,
    alias:"widget.headercontainer",
    baseCls:Ext.baseCSSPrefix+"grid-header-ct",
    dock:"top",
    weight:100,
    defaultType:"gridcolumn",
    defaultWidth:100,
    sortAscText:"Sort Ascending",
    sortDescText:"Sort Descending",
    sortClearText:"Clear Sort",
    columnsText:"Columns",
    lastHeaderCls:Ext.baseCSSPrefix+"column-header-last",
    firstHeaderCls:Ext.baseCSSPrefix+"column-header-first",
    headerOpenCls:Ext.baseCSSPrefix+"column-header-open",
    triStateSort:false,
    ddLock:false,
    dragging:false,
    sortable:true,
    initComponent:function(){
        var a=this;
        a.headerCounter=0;
        a.plugins=a.plugins||[];
        if(!a.isHeader){
            a.resizer=Ext.create("Ext.grid.plugin.HeaderResizer");
            a.reorderer=Ext.create("Ext.grid.plugin.HeaderReorderer");
            if(!a.enableColumnResize){
                a.resizer.disable()
                }
                if(!a.enableColumnMove){
                a.reorderer.disable()
                }
                a.plugins.push(a.reorderer,a.resizer)
            }
            if(a.isHeader&&!a.items){
            a.layout="auto"
            }else{
            a.layout={
                type:"gridcolumn",
                availableSpaceOffset:a.availableSpaceOffset,
                align:"stretchmax",
                resetStretch:true
            }
        }
        a.defaults=a.defaults||{};
    
    Ext.applyIf(a.defaults,{
        width:a.defaultWidth,
        triStateSort:a.triStateSort,
        sortable:a.sortable
        });
    a.callParent();
    a.addEvents("columnresize","headerclick","headertriggerclick","columnmove","columnhide","columnshow","sortchange","menucreate")
    },
onDestroy:function(){
    Ext.destroy(this.resizer,this.reorderer);
    this.callParent()
    },
onAdd:function(b){
    var a=this;
    if(!b.headerId){
        b.headerId="h"+(++a.headerCounter)
        }
        a.callParent(arguments);
    a.purgeCache()
    },
onRemove:function(b){
    var a=this;
    a.callParent(arguments);
    a.purgeCache()
    },
afterRender:function(){
    this.callParent();
    var a=this.up("[store]").store,c=a.sorters,d=c.first(),b;
    if(d){
        b=this.down("gridcolumn[dataIndex="+d.property+"]");
        if(b){
            b.setSortState(d.direction,false,true)
            }
        }
},
afterLayout:function(){
    if(!this.isHeader){
        var e=this,d=e.query(">gridcolumn:not([hidden])"),c,b,a;
        e.callParent(arguments);
        if(d.length){
            b=d[0].el;
            if(b!==e.pastFirstHeaderEl){
                if(e.pastFirstHeaderEl){
                    e.pastFirstHeaderEl.removeCls(e.firstHeaderCls)
                    }
                    b.addCls(e.firstHeaderCls);
                e.pastFirstHeaderEl=b
                }
                a=d[d.length-1].el;
            if(a!==e.pastLastHeaderEl){
                if(e.pastLastHeaderEl){
                    e.pastLastHeaderEl.removeCls(e.lastHeaderCls)
                    }
                    a.addCls(e.lastHeaderCls);
                e.pastLastHeaderEl=a
                }
            }
    }
},
onHeaderShow:function(f){
    var j=this,k=j.ownerCt,c=j.getMenu(),d,b,g,a,h,e;
    if(c){
        g=c.down("menucheckitem[headerId="+f.id+"]");
        if(g){
            g.setChecked(true,true)
            }
            d=c.query("#columnItem>menucheckitem[checked]");
        b=d.length;
        if((j.getVisibleGridColumns().length>1)&&j.disabledMenuItems&&j.disabledMenuItems.length){
            if(b==1){
                Ext.Array.remove(j.disabledMenuItems,d[0])
                }
                for(e=0,h=j.disabledMenuItems.length;e<h;e++){
                a=j.disabledMenuItems[e];
                if(!a.isDestroyed){
                    a[a.menu?"enableCheckChange":"enable"]()
                    }
                }
            if(b==1){
            j.disabledMenuItems=d
            }else{
            j.disabledMenuItems=[]
            }
        }
}
if(!f.isGroupHeader){
    if(j.view){
        j.view.onHeaderShow(j,f,true)
        }
        if(k){
        k.onHeaderShow(j,f)
        }
    }
j.fireEvent("columnshow",j,f);
j.doLayout()
},
onHeaderHide:function(f,c){
    var b=this,a=b.ownerCt,e=b.getMenu(),d;
    if(e){
        d=e.down("menucheckitem[headerId="+f.id+"]");
        if(d){
            d.setChecked(false,true)
            }
            b.setDisabledItems()
        }
        if(!f.isGroupHeader){
        if(b.view){
            b.view.onHeaderHide(b,f,true)
            }
            if(a){
            a.onHeaderHide(b,f)
            }
            if(!c){
            b.doLayout()
            }
        }
    b.fireEvent("columnhide",b,f)
},
setDisabledItems:function(){
    var d=this,f=d.getMenu(),b=0,a,e,c;
    e=f.query("#columnItem>menucheckitem[checked]");
    if((e.length===1)){
        if(!d.disabledMenuItems){
            d.disabledMenuItems=[]
            }
            if((d.getVisibleGridColumns().length===1)&&e[0].menu){
            e=e.concat(e[0].menu.query("menucheckitem[checked]"))
            }
            a=e.length;
        for(b=0;b<a;b++){
            c=e[b];
            if(!Ext.Array.contains(d.disabledMenuItems,c)){
                c.disabled=false;
                c[c.menu?"disableCheckChange":"disable"]();
                d.disabledMenuItems.push(c)
                }
            }
        }
},
tempLock:function(){
    this.ddLock=true;
    Ext.Function.defer(function(){
        this.ddLock=false
        },200,this)
    },
onHeaderResize:function(c,a,b){
    this.tempLock();
    if(this.view&&this.view.rendered){
        this.view.onHeaderResize(c,a,b)
        }
        this.fireEvent("columnresize",this,c,a)
    },
onHeaderClick:function(c,b,a){
    this.fireEvent("headerclick",this,c,b,a)
    },
onHeaderTriggerClick:function(c,b,a){
    if(this.fireEvent("headertriggerclick",this,c,b,a)!==false){
        this.showMenuBy(a,c)
        }
    },
showMenuBy:function(b,f){
    var d=this.getMenu(),e=d.down("#ascItem"),c=d.down("#descItem"),a;
    d.activeHeader=d.ownerCt=f;
    d.setFloatParent(f);
    f.titleContainer.addCls(this.headerOpenCls);
    a=f.sortable?"enable":"disable";
    if(e){
        e[a]()
        }
        if(c){
        c[a]()
        }
        d.showBy(b)
    },
onMenuDeactivate:function(){
    var a=this.getMenu();
    a.activeHeader.titleContainer.removeCls(this.headerOpenCls)
    },
moveHeader:function(a,b){
    this.tempLock();
    this.onHeaderMoved(this.move(a,b),a,b)
    },
purgeCache:function(){
    var a=this;
    delete a.gridDataColumns;
    if(a.menu){
        a.menu.destroy();
        delete a.menu
        }
    },
onHeaderMoved:function(e,b,d){
    var c=this,a=c.ownerCt;
    if(a){
        a.onHeaderMove(c,e,b,d)
        }
        c.fireEvent("columnmove",c,e,b,d)
    },
getMenu:function(){
    var a=this;
    if(!a.menu){
        a.menu=Ext.create("Ext.menu.Menu",{
            hideOnParentHide:false,
            items:a.getMenuItems(),
            listeners:{
                deactivate:a.onMenuDeactivate,
                scope:a
            }
        });
    a.setDisabledItems();
    a.fireEvent("menucreate",a,a.menu)
    }
    return a.menu
},
getMenuItems:function(){
    var c=this,b=[],a=c.enableColumnHide?c.getColumnMenu(c):null;
    if(c.sortable){
        b=[{
            itemId:"ascItem",
            text:c.sortAscText,
            cls:"xg-hmenu-sort-asc",
            handler:c.onSortAscClick,
            scope:c
        },{
            itemId:"descItem",
            text:c.sortDescText,
            cls:"xg-hmenu-sort-desc",
            handler:c.onSortDescClick,
            scope:c
        }]
        }
        if(a&&a.length){
        b.push("-",{
            itemId:"columnItem",
            text:c.columnsText,
            cls:Ext.baseCSSPrefix+"cols-icon",
            menu:a
        })
        }
        return b
    },
onSortAscClick:function(){
    var b=this.getMenu(),a=b.activeHeader;
    a.setSortState("ASC")
    },
onSortDescClick:function(){
    var b=this.getMenu(),a=b.activeHeader;
    a.setSortState("DESC")
    },
getColumnMenu:function(f){
    var c=[],b=0,e,a=f.query(">gridcolumn[hideable]"),g=a.length,d;
    for(;b<g;b++){
        e=a[b];
        d=Ext.create("Ext.menu.CheckItem",{
            text:e.text,
            checked:!e.hidden,
            hideOnClick:false,
            headerId:e.id,
            menu:e.isGroupHeader?this.getColumnMenu(e):undefined,
            checkHandler:this.onColumnCheckChange,
            scope:this
        });
        if(g===1){
            d.disabled=true
            }
            c.push(d);
        e.on({
            destroy:Ext.Function.bind(d.destroy,d)
            })
        }
        return c
    },
onColumnCheckChange:function(a,b){
    var c=Ext.getCmp(a.headerId);
    c[b?"show":"hide"]()
    },
getColumnsForTpl:function(e){
    var c=[],d=this.getGridColumns(e),g=d.length,a=0,f,b;
    for(;a<g;a++){
        f=d[a];
        if(f.hidden){
            b=0
            }else{
            b=f.getDesiredWidth();
            if((a==0)&&(Ext.isIE6||Ext.isIE7)){
                b+=1
                }
            }
        c.push({
        dataIndex:f.dataIndex,
        align:f.align,
        width:b,
        id:f.id,
        cls:f.tdCls,
        columnId:f.getItemId()
        })
    }
    return c
},
getColumnCount:function(){
    return this.getGridColumns().length
    },
getFullWidth:function(d){
    var c=0,b=this.getVisibleGridColumns(d),e=b.length,a=0;
    for(;a<e;a++){
        if(!isNaN(b[a].width)){
            if(b[a].getDesiredWidth){
                c+=b[a].getDesiredWidth()
                }else{
                c+=b[a].getWidth()
                }
            }
    }
    return c
},
clearOtherSortStates:function(a){
    var c=this.getGridColumns(),e=c.length,b=0,d;
    for(;b<e;b++){
        if(c[b]!==a){
            d=c[b].sortState;
            c[b].setSortState(null,true)
            }
        }
    },
getVisibleGridColumns:function(a){
    return Ext.ComponentQuery.query(":not([hidden])",this.getGridColumns(a))
    },
getGridColumns:function(b){
    var c=this,a=b?null:c.gridDataColumns;
    if(!a){
        c.gridDataColumns=a=[];
        c.cascade(function(d){
            if((d!==c)&&!d.isGroupHeader){
                a.push(d)
                }
            })
    }
    return a
},
getHeaderIndex:function(b){
    var a=this.getGridColumns();
    return Ext.Array.indexOf(a,b)
    },
getHeaderAtIndex:function(a){
    var b=this.getGridColumns();
    return b[a]
    },
prepareData:function(h,c,j,l,a){
    var g={},d=this.gridDataColumns||this.getGridColumns(),e=d.length,f=0,i,o,k,n,b,m=a.store;
    for(;f<e;f++){
        b={
            tdCls:"",
            style:""
        };
        
        i=d[f];
        o=i.id;
        k=i.renderer;
        n=h[i.dataIndex];
        if(typeof k==="string"){
            i.renderer=k=Ext.util.Format[k]
            }
            if(typeof k==="function"){
            n=k.call(i.scope||this.ownerCt,n,b,j,c,f,m,l)
            }
            g[o+"-modified"]=j.isModified(i.dataIndex)?Ext.baseCSSPrefix+"grid-dirty-cell":"";
        g[o+"-tdCls"]=b.tdCls;
        g[o+"-tdAttr"]=b.tdAttr;
        g[o+"-style"]=b.style;
        if(n===undefined||n===null||n===""){
            n="&#160;"
            }
            g[o]=n
        }
        return g
    },
expandToFit:function(a){
    if(this.view){
        this.view.expandToFit(a)
        }
    }
});
Ext.define("Ext.view.TableChunker",{
    singleton:true,
    requires:["Ext.XTemplate"],
    metaTableTpl:["{[this.openTableWrap()]}",'<table class="'+Ext.baseCSSPrefix+"grid-table "+Ext.baseCSSPrefix+'grid-table-resizer" border="0" cellspacing="0" cellpadding="0" {[this.embedFullWidth()]}>',"<tbody>",'<tr class="'+Ext.baseCSSPrefix+'grid-header-row">','<tpl for="columns">','<th class="'+Ext.baseCSSPrefix+'grid-col-resizer-{id}" style="width: {width}px; height: 0px;"></th>',"</tpl>","</tr>","{[this.openRows()]}","{row}",'<tpl for="features">',"{[this.embedFeature(values, parent, xindex, xcount)]}","</tpl>","{[this.closeRows()]}","</tbody>","</table>","{[this.closeTableWrap()]}"],
    constructor:function(){
        Ext.XTemplate.prototype.recurse=function(b,a){
            return this.apply(a?b[a]:b)
            }
        },
embedFeature:function(b,d,a,e){
    var c="";
    if(!b.disabled){
        c=b.getFeatureTpl(b,d,a,e)
        }
        return c
    },
embedFullWidth:function(){
    return'style="width: {fullWidth}px;"'
    },
openRows:function(){
    return'<tpl for="rows">'
    },
closeRows:function(){
    return"</tpl>"
    },
metaRowTpl:['<tr class="'+Ext.baseCSSPrefix+'grid-row {addlSelector} {[this.embedRowCls()]}" {[this.embedRowAttr()]}>','<tpl for="columns">','<td class="{cls} '+Ext.baseCSSPrefix+"grid-cell "+Ext.baseCSSPrefix+'grid-cell-{columnId} {{id}-modified} {{id}-tdCls} {[this.firstOrLastCls(xindex, xcount)]}" {{id}-tdAttr}><div unselectable="on" class="'+Ext.baseCSSPrefix+"grid-cell-inner "+Ext.baseCSSPrefix+'unselectable" style="{{id}-style}; text-align: {align};">{{id}}</div></td>',"</tpl>","</tr>"],
firstOrLastCls:function(b,c){
    var a="";
    if(b===1){
        a=Ext.baseCSSPrefix+"grid-cell-first"
        }else{
        if(b===c){
            a=Ext.baseCSSPrefix+"grid-cell-last"
            }
        }
    return a
},
embedRowCls:function(){
    return"{rowCls}"
    },
embedRowAttr:function(){
    return"{rowAttr}"
    },
openTableWrap:function(){
    return""
    },
closeTableWrap:function(){
    return""
    },
getTableTpl:function(j,b){
    var h,g={
        openRows:this.openRows,
        closeRows:this.closeRows,
        embedFeature:this.embedFeature,
        embedFullWidth:this.embedFullWidth,
        openTableWrap:this.openTableWrap,
        closeTableWrap:this.closeTableWrap
        },f={},c=j.features||[],l=c.length,e=0,k={
        embedRowCls:this.embedRowCls,
        embedRowAttr:this.embedRowAttr,
        firstOrLastCls:this.firstOrLastCls
        },d=Array.prototype.slice.call(this.metaRowTpl,0),a;
    for(;e<l;e++){
        if(!c[e].disabled){
            c[e].mutateMetaRowTpl(d);
            Ext.apply(k,c[e].getMetaRowTplFragments());
            Ext.apply(f,c[e].getFragmentTpl());
            Ext.apply(g,c[e].getTableFragments())
            }
        }
    d=Ext.create("Ext.XTemplate",d.join(""),k);
    j.row=d.applyTemplate(j);
    a=Ext.create("Ext.XTemplate",this.metaTableTpl.join(""),g);
    h=a.applyTemplate(j);
    if(!b){
    h=Ext.create("Ext.XTemplate",h,f)
    }
    return h
}
});
Ext.define("Ext.data.Batch",{
    mixins:{
        observable:"Ext.util.Observable"
    },
    autoStart:false,
    current:-1,
    total:0,
    isRunning:false,
    isComplete:false,
    hasException:false,
    pauseOnException:true,
    constructor:function(a){
        var b=this;
        b.addEvents("complete","exception","operationcomplete");
        b.mixins.observable.constructor.call(b,a);
        b.operations=[]
        },
    add:function(a){
        this.total++;
        a.setBatch(this);
        this.operations.push(a)
        },
    start:function(){
        this.hasException=false;
        this.isRunning=true;
        this.runNextOperation()
        },
    runNextOperation:function(){
        this.runOperation(this.current+1)
        },
    pause:function(){
        this.isRunning=false
        },
    runOperation:function(d){
        var e=this,c=e.operations,b=c[d],a;
        if(b===undefined){
            e.isRunning=false;
            e.isComplete=true;
            e.fireEvent("complete",e,c[c.length-1])
            }else{
            e.current=d;
            a=function(f){
                var g=f.hasException();
                if(g){
                    e.hasException=true;
                    e.fireEvent("exception",e,f)
                    }else{
                    e.fireEvent("operationcomplete",e,f)
                    }
                    if(g&&e.pauseOnException){
                    e.pause()
                    }else{
                    f.setCompleted();
                    e.runNextOperation()
                    }
                };
            
        b.setStarted();
        e.proxy[b.action](b,a,e)
        }
    }
});
Ext.define("Ext.data.Request",{
    action:undefined,
    params:undefined,
    method:"GET",
    url:undefined,
    constructor:function(a){
        Ext.apply(this,a)
        }
    });
Ext.define("Ext.tip.QuickTip",{
    extend:"Ext.tip.ToolTip",
    alternateClassName:"Ext.QuickTip",
    interceptTitles:false,
    title:"&#160;",
    tagConfig:{
        namespace:"data-",
        attribute:"qtip",
        width:"qwidth",
        target:"target",
        title:"qtitle",
        hide:"hide",
        cls:"qclass",
        align:"qalign",
        anchor:"anchor"
    },
    initComponent:function(){
        var a=this;
        a.target=a.target||Ext.getDoc();
        a.targets=a.targets||{};
        
        a.callParent()
        },
    register:function(c){
        var g=Ext.isArray(c)?c:arguments,d=0,a=g.length,f,b,e;
        for(;d<a;d++){
            c=g[d];
            f=c.target;
            if(f){
                if(Ext.isArray(f)){
                    for(b=0,e=f.length;b<e;b++){
                        this.targets[Ext.id(f[b])]=c
                        }
                    }else{
                this.targets[Ext.id(f)]=c
                }
            }
        }
},
unregister:function(a){
    delete this.targets[Ext.id(a)]
},
cancelShow:function(a){
    var b=this,c=b.activeTarget;
    a=Ext.get(a).dom;
    if(b.isVisible()){
        if(c&&c.el==a){
            b.hide()
            }
        }else{
    if(c&&c.el==a){
        b.clearTimer("show")
        }
    }
},
getTipCfg:function(d){
    var b=d.getTarget(),c,a;
    if(this.interceptTitles&&b.title&&Ext.isString(b.title)){
        c=b.title;
        b.qtip=c;
        b.removeAttribute("title");
        d.preventDefault()
        }else{
        a=this.tagConfig;
        b=d.getTarget("["+a.namespace+a.attribute+"]");
        if(b){
            c=b.getAttribute(a.namespace+a.attribute)
            }
        }
    return c
},
onTargetOver:function(h){
    var d=this,g=h.getTarget(),i,b,c,f,a;
    if(d.disabled){
        return
    }
    d.targetXY=h.getXY();
    if(!g||g.nodeType!==1||g==document||g==document.body){
        return
    }
    if(d.activeTarget&&((g==d.activeTarget.el)||Ext.fly(d.activeTarget.el).contains(g))){
        d.clearTimer("hide");
        d.show();
        return
    }
    if(g){
        Ext.Object.each(d.targets,function(e,j){
            var k=Ext.fly(j.target);
            if(k&&(k.dom===g||k.contains(g))){
                i=k.dom;
                return false
                }
            });
    if(i){
        d.activeTarget=d.targets[i.id];
        d.activeTarget.el=g;
        d.anchor=d.activeTarget.anchor;
        if(d.anchor){
            d.anchorTarget=g
            }
            d.delayShow();
        return
    }
}
i=Ext.get(g);
b=d.tagConfig;
c=b.namespace;
f=d.getTipCfg(h);
if(f){
    a=i.getAttribute(c+b.hide);
    d.activeTarget={
        el:g,
        text:f,
        width:+i.getAttribute(c+b.width)||null,
        autoHide:a!="user"&&a!=="false",
        title:i.getAttribute(c+b.title),
        cls:i.getAttribute(c+b.cls),
        align:i.getAttribute(c+b.align)
        };
        
    d.anchor=i.getAttribute(c+b.anchor);
    if(d.anchor){
        d.anchorTarget=g
        }
        d.delayShow()
    }
},
onTargetOut:function(b){
    var a=this;
    if(a.activeTarget&&b.within(a.activeTarget.el)&&!a.getTipCfg(b)){
        return
    }
    a.clearTimer("show");
    if(a.autoHide!==false){
        a.delayHide()
        }
    },
showAt:function(c){
    var a=this,b=a.activeTarget;
    if(b){
        if(!a.rendered){
            a.render(Ext.getBody());
            a.activeTarget=b
            }
            if(b.title){
            a.setTitle(b.title||"");
            a.header.show()
            }else{
            a.header.hide()
            }
            a.body.update(b.text);
        a.autoHide=b.autoHide;
        a.dismissDelay=b.dismissDelay||a.dismissDelay;
        if(a.lastCls){
            a.el.removeCls(a.lastCls);
            delete a.lastCls
            }
            if(b.cls){
            a.el.addCls(b.cls);
            a.lastCls=b.cls
            }
            a.setWidth(b.width);
        if(a.anchor){
            a.constrainPosition=false
            }else{
            if(b.align){
                c=a.el.getAlignToXY(b.el,b.align);
                a.constrainPosition=false
                }else{
                a.constrainPosition=true
                }
            }
    }
a.callParent([c])
},
hide:function(){
    delete this.activeTarget;
    this.callParent()
    }
});
Ext.define("Ext.menu.Item",{
    extend:"Ext.Component",
    alias:"widget.menuitem",
    alternateClassName:"Ext.menu.TextItem",
    activeCls:Ext.baseCSSPrefix+"menu-item-active",
    ariaRole:"menuitem",
    canActivate:true,
    clickHideDelay:1,
    destroyMenu:true,
    disabledCls:Ext.baseCSSPrefix+"menu-item-disabled",
    hideOnClick:true,
    isMenuItem:true,
    menuAlign:"tl-tr?",
    menuExpandDelay:200,
    menuHideDelay:200,
    renderTpl:['<tpl if="plain">',"{text}","</tpl>",'<tpl if="!plain">','<a class="'+Ext.baseCSSPrefix+'menu-item-link" href="{href}" <tpl if="hrefTarget">target="{hrefTarget}"</tpl> hidefocus="true" unselectable="on">','<img src="{icon}" class="'+Ext.baseCSSPrefix+'menu-item-icon {iconCls}" />','<span class="'+Ext.baseCSSPrefix+'menu-item-text" <tpl if="menu">style="margin-right: 17px;"</tpl> >{text}</span>','<tpl if="menu">','<img src="'+Ext.BLANK_IMAGE_URL+'" class="'+Ext.baseCSSPrefix+'menu-item-arrow" />',"</tpl>","</a>","</tpl>"],
    maskOnDisable:false,
    activate:function(){
        var a=this;
        if(!a.activated&&a.canActivate&&a.rendered&&!a.isDisabled()&&a.isVisible()){
            a.el.addCls(a.activeCls);
            a.focus();
            a.activated=true;
            a.fireEvent("activate",a)
            }
        },
blur:function(){
    this.$focused=false;
    this.callParent(arguments)
    },
deactivate:function(){
    var a=this;
    if(a.activated){
        a.el.removeCls(a.activeCls);
        a.blur();
        a.hideMenu();
        a.activated=false;
        a.fireEvent("deactivate",a)
        }
    },
deferExpandMenu:function(){
    var a=this;
    if(!a.menu.rendered||!a.menu.isVisible()){
        a.parentMenu.activeChild=a.menu;
        a.menu.parentItem=a;
        a.menu.parentMenu=a.menu.ownerCt=a.parentMenu;
        a.menu.showBy(a,a.menuAlign)
        }
    },
deferHideMenu:function(){
    if(this.menu.isVisible()){
        this.menu.hide()
        }
    },
deferHideParentMenus:function(){
    Ext.menu.Manager.hideAll()
    },
expandMenu:function(a){
    var b=this;
    if(b.menu){
        clearTimeout(b.hideMenuTimer);
        if(a===0){
            b.deferExpandMenu()
            }else{
            b.expandMenuTimer=Ext.defer(b.deferExpandMenu,Ext.isNumber(a)?a:b.menuExpandDelay,b)
            }
        }
},
focus:function(){
    this.$focused=true;
    this.callParent(arguments)
    },
getRefItems:function(a){
    var c=this.menu,b;
    if(c){
        b=c.getRefItems(a);
        b.unshift(c)
        }
        return b||[]
    },
hideMenu:function(a){
    var b=this;
    if(b.menu){
        clearTimeout(b.expandMenuTimer);
        b.hideMenuTimer=Ext.defer(b.deferHideMenu,Ext.isNumber(a)?a:b.menuHideDelay,b)
        }
    },
initComponent:function(){
    var b=this,c=Ext.baseCSSPrefix,a=[c+"menu-item"];
    b.addEvents("activate","click","deactivate");
    if(b.plain){
        a.push(c+"menu-item-plain")
        }
        if(b.cls){
        a.push(b.cls)
        }
        b.cls=a.join(" ");
    if(b.menu){
        b.menu=Ext.menu.Manager.get(b.menu)
        }
        b.callParent(arguments)
    },
onClick:function(b){
    var a=this;
    if(!a.href){
        b.stopEvent()
        }
        if(a.disabled){
        return
    }
    if(a.hideOnClick){
        a.deferHideParentMenusTimer=Ext.defer(a.deferHideParentMenus,a.clickHideDelay,a)
        }
        Ext.callback(a.handler,a.scope||a,[a,b]);
    a.fireEvent("click",a,b);
    if(!a.hideOnClick){
        a.focus()
        }
    },
onDestroy:function(){
    var a=this;
    clearTimeout(a.expandMenuTimer);
    clearTimeout(a.hideMenuTimer);
    clearTimeout(a.deferHideParentMenusTimer);
    if(a.menu){
        delete a.menu.parentItem;
        delete a.menu.parentMenu;
        delete a.menu.ownerCt;
        if(a.destroyMenu!==false){
            a.menu.destroy()
            }
        }
    a.callParent(arguments)
},
onRender:function(a,d){
    var b=this,c="."+Ext.baseCSSPrefix;
    Ext.applyIf(b.renderData,{
        href:b.href||"#",
        hrefTarget:b.hrefTarget,
        icon:b.icon||Ext.BLANK_IMAGE_URL,
        iconCls:b.iconCls+(b.checkChangeDisabled?" "+b.disabledCls:""),
        menu:Ext.isDefined(b.menu),
        plain:b.plain,
        text:b.text
        });
    Ext.applyIf(b.renderSelectors,{
        itemEl:c+"menu-item-link",
        iconEl:c+"menu-item-icon",
        textEl:c+"menu-item-text",
        arrowEl:c+"menu-item-arrow"
        });
    b.callParent(arguments)
    },
setHandler:function(b,a){
    this.handler=b||null;
    this.scope=a
    },
setIconCls:function(a){
    var b=this;
    if(b.iconEl){
        if(b.iconCls){
            b.iconEl.removeCls(b.iconCls)
            }
            if(a){
            b.iconEl.addCls(a)
            }
        }
    b.iconCls=a
},
setText:function(c){
    var b=this,a=b.textEl||b.el;
    b.text=c;
    if(b.rendered){
        a.update(c||"");
        b.ownerCt.redoComponentLayout()
        }
    }
});
Ext.define("Ext.menu.KeyNav",{
    extend:"Ext.util.KeyNav",
    requires:["Ext.FocusManager"],
    constructor:function(b){
        var a=this;
        a.menu=b;
        a.callParent([b.el,{
            down:a.down,
            enter:a.enter,
            esc:a.escape,
            left:a.left,
            right:a.right,
            space:a.enter,
            tab:a.tab,
            up:a.up
            }])
        },
    down:function(b){
        var a=this,c=a.menu.focusedItem;
        if(c&&b.getKey()==Ext.EventObject.DOWN&&a.isWhitelisted(c)){
            return true
            }
            a.focusNextItem(1)
        },
    enter:function(a){
        var b=this.menu;
        if(b.activeItem){
            b.onClick(a)
            }
        },
escape:function(a){
    Ext.menu.Manager.hideAll()
    },
focusNextItem:function(f){
    var g=this.menu,b=g.items,d=g.focusedItem,c=d?b.indexOf(d):-1,a=c+f;
    while(a!=c){
        if(a<0){
            a=b.length-1
            }else{
            if(a>=b.length){
                a=0
                }
            }
        var e=b.getAt(a);
    if(g.canActivateItem(e)){
        g.setActiveItem(e);
        break
    }
    a+=f
    }
},
isWhitelisted:function(a){
    return Ext.FocusManager.isWhitelisted(a)
    },
left:function(b){
    var c=this.menu,d=c.focusedItem,a=c.activeItem;
    if(d&&this.isWhitelisted(d)){
        return true
        }
        c.hide();
    if(c.parentMenu){
        c.parentMenu.focus()
        }
    },
right:function(c){
    var d=this.menu,f=d.focusedItem,a=d.activeItem,b;
    if(f&&this.isWhitelisted(f)){
        return true
        }
        if(a){
        b=d.activeItem.menu;
        if(b){
            a.expandMenu(0);
            Ext.defer(function(){
                b.setActiveItem(b.items.getAt(0))
                },25)
            }
        }
},
tab:function(b){
    var a=this;
    if(b.shiftKey){
        a.up(b)
        }else{
        a.down(b)
        }
    },
up:function(b){
    var a=this,c=a.menu.focusedItem;
    if(c&&b.getKey()==Ext.EventObject.UP&&a.isWhitelisted(c)){
        return true
        }
        a.focusNextItem(-1)
    }
});
Ext.define("Ext.menu.Separator",{
    extend:"Ext.menu.Item",
    alias:"widget.menuseparator",
    canActivate:false,
    focusable:false,
    hideOnClick:false,
    plain:true,
    separatorCls:Ext.baseCSSPrefix+"menu-item-separator",
    text:"&#160;",
    onRender:function(b,d){
        var c=this,a=c.separatorCls;
        c.cls+=" "+a;
        Ext.applyIf(c.renderSelectors,{
            itemSepEl:"."+a
            });
        c.callParent(arguments)
        }
    });
Ext.define("Ext.grid.LockingView",{
    mixins:{
        observable:"Ext.util.Observable"
    },
    eventRelayRe:/^(beforeitem|beforecontainer|item|container|cell)/,
    constructor:function(c){
        var f=this,h=[],a=f.eventRelayRe,b=c.locked.getView(),g=c.normal.getView(),d,e;
        Ext.apply(f,{
            lockedView:b,
            normalView:g,
            lockedGrid:c.locked,
            normalGrid:c.normal,
            panel:c.panel
            });
        f.mixins.observable.constructor.call(f,c);
        d=b.events;
        for(e in d){
            if(d.hasOwnProperty(e)&&a.test(e)){
                h.push(e)
                }
            }
        f.relayEvents(b,h);
    f.relayEvents(g,h);
    g.on({
        scope:f,
        itemmouseleave:f.onItemMouseLeave,
        itemmouseenter:f.onItemMouseEnter
        });
    b.on({
        scope:f,
        itemmouseleave:f.onItemMouseLeave,
        itemmouseenter:f.onItemMouseEnter
        })
    },
getGridColumns:function(){
    var a=this.lockedGrid.headerCt.getGridColumns();
    return a.concat(this.normalGrid.headerCt.getGridColumns())
    },
getEl:function(a){
    return this.getViewForColumn(a).getEl()
    },
getViewForColumn:function(b){
    var a=this.lockedView,c;
    a.headerCt.cascade(function(d){
        if(d===b){
            c=true;
            return false
            }
        });
return c?a:this.normalView
},
onItemMouseEnter:function(c,b){
    var f=this,d=f.lockedView,a=f.normalView,e;
    if(c.trackOver){
        if(c!==d){
            a=d
            }
            e=a.getNode(b);
        a.highlightItem(e)
        }
    },
onItemMouseLeave:function(c,b){
    var e=this,d=e.lockedView,a=e.normalView;
    if(c.trackOver){
        if(c!==d){
            a=d
            }
            a.clearHighlight()
        }
    },
relayFn:function(c,b){
    b=b||[];
    var a=this.lockedView;
    a[c].apply(a,b||[]);
    a=this.normalView;
    a[c].apply(a,b||[])
    },
getSelectionModel:function(){
    return this.panel.getSelectionModel()
    },
getStore:function(){
    return this.panel.store
    },
getNode:function(a){
    return this.normalView.getNode(a)
    },
getCell:function(b,c){
    var a=this.getViewForColumn(c),d;
    d=a.getNode(b);
    return Ext.fly(d).down(c.getCellSelector())
    },
getRecord:function(b){
    var a=this.lockedView.getRecord(b);
    if(!b){
        a=this.normalView.getRecord(b)
        }
        return a
    },
addElListener:function(a,c,b){
    this.relayFn("addElListener",arguments)
    },
refreshNode:function(){
    this.relayFn("refreshNode",arguments)
    },
refresh:function(){
    this.relayFn("refresh",arguments)
    },
bindStore:function(){
    this.relayFn("bindStore",arguments)
    },
addRowCls:function(){
    this.relayFn("addRowCls",arguments)
    },
removeRowCls:function(){
    this.relayFn("removeRowCls",arguments)
    }
});
Ext.define("Ext.draw.Matrix",{
    requires:["Ext.draw.Draw"],
    constructor:function(h,g,l,k,j,i){
        if(h!=null){
            this.matrix=[[h,l,j],[g,k,i],[0,0,1]]
            }else{
            this.matrix=[[1,0,0],[0,1,0],[0,0,1]]
            }
        },
add:function(s,p,m,k,i,h){
    var n=this,g=[[],[],[]],r=[[s,m,i],[p,k,h],[0,0,1]],q,o,l,j;
    for(q=0;q<3;q++){
        for(o=0;o<3;o++){
            j=0;
            for(l=0;l<3;l++){
                j+=n.matrix[q][l]*r[l][o]
                }
                g[q][o]=j
            }
        }
        n.matrix=g
},
prepend:function(s,p,m,k,i,h){
    var n=this,g=[[],[],[]],r=[[s,m,i],[p,k,h],[0,0,1]],q,o,l,j;
    for(q=0;q<3;q++){
        for(o=0;o<3;o++){
            j=0;
            for(l=0;l<3;l++){
                j+=r[q][l]*n.matrix[l][o]
                }
                g[q][o]=j
            }
        }
        n.matrix=g
},
invert:function(){
    var j=this.matrix,i=j[0][0],h=j[1][0],n=j[0][1],m=j[1][1],l=j[0][2],k=j[1][2],g=i*m-h*n;
    return new Ext.draw.Matrix(m/g,-h/g,-n/g,i/g,(n*k-m*l)/g,(h*l-i*k)/g)
    },
clone:function(){
    var i=this.matrix,h=i[0][0],g=i[1][0],m=i[0][1],l=i[1][1],k=i[0][2],j=i[1][2];
    return new Ext.draw.Matrix(h,g,m,l,k,j)
    },
translate:function(a,b){
    this.prepend(1,0,0,1,a,b)
    },
scale:function(b,e,a,d){
    var c=this;
    if(e==null){
        e=b
        }
        c.add(1,0,0,1,a,d);
    c.add(b,0,0,e,0,0);
    c.add(1,0,0,1,-a,-d)
    },
rotate:function(c,b,g){
    c=Ext.draw.Draw.rad(c);
    var e=this,f=+Math.cos(c).toFixed(9),d=+Math.sin(c).toFixed(9);
    e.add(f,d,-d,f,b,g);
    e.add(1,0,0,1,-b,-g)
    },
x:function(a,c){
    var b=this.matrix;
    return a*b[0][0]+c*b[0][1]+b[0][2]
    },
y:function(a,c){
    var b=this.matrix;
    return a*b[1][0]+c*b[1][1]+b[1][2]
    },
get:function(b,a){
    return +this.matrix[b][a].toFixed(4)
    },
toString:function(){
    var a=this;
    return[a.get(0,0),a.get(0,1),a.get(1,0),a.get(1,1),0,0].join()
    },
toSvg:function(){
    var a=this;
    return"matrix("+[a.get(0,0),a.get(1,0),a.get(0,1),a.get(1,1),a.get(0,2),a.get(1,2)].join()+")"
    },
toFilter:function(){
    var a=this;
    return"progid:DXImageTransform.Microsoft.Matrix(M11="+a.get(0,0)+", M12="+a.get(0,1)+", M21="+a.get(1,0)+", M22="+a.get(1,1)+", Dx="+a.get(0,2)+", Dy="+a.get(1,2)+")"
    },
offset:function(){
    var a=this.matrix;
    return[a[0][2].toFixed(4),a[1][2].toFixed(4)]
    },
split:function(){
    function d(f){
        return f[0]*f[0]+f[1]*f[1]
        }
        function b(f){
        var g=Math.sqrt(d(f));
        f[0]/=g;
        f[1]/=g
        }
        var a=this.matrix,c={
        translateX:a[0][2],
        translateY:a[1][2]
        },e;
    e=[[a[0][0],a[0][1]],[a[1][1],a[1][1]]];
    c.scaleX=Math.sqrt(d(e[0]));
    b(e[0]);
    c.shear=e[0][0]*e[1][0]+e[0][1]*e[1][1];
    e[1]=[e[1][0]-e[0][0]*c.shear,e[1][1]-e[0][1]*c.shear];
    c.scaleY=Math.sqrt(d(e[1]));
    b(e[1]);
    c.shear/=c.scaleY;
    c.rotate=Math.asin(-e[0][1]);
    c.isSimple=!+c.shear.toFixed(9)&&(c.scaleX.toFixed(9)==c.scaleY.toFixed(9)||!c.rotate);
    return c
    }
});
Ext.define("Ext.data.proxy.Client",{
    extend:"Ext.data.proxy.Proxy",
    alternateClassName:"Ext.data.ClientProxy",
    clear:function(){}
});
Ext.define("Ext.draw.SpriteDD",{
    extend:"Ext.dd.DragSource",
    constructor:function(b,a){
        var d=this,c=b.el;
        d.sprite=b;
        d.el=c;
        d.dragData={
            el:c,
            sprite:b
        };
        
        d.callParent([c,a]);
        d.sprite.setStyle("cursor","move")
        },
    showFrame:Ext.emptyFn,
    createFrame:Ext.emptyFn,
    getDragEl:function(a){
        return this.el
        },
    getRegion:function(){
        var i=this,f=i.el,k,d,c,n,m,q,a,j,g,p,o;
        o=i.sprite;
        p=o.getBBox();
        try{
            k=Ext.core.Element.getXY(f)
            }catch(h){}
        if(!k){
            return null
            }
            d=k[0];
        c=d+p.width;
        n=k[1];
        m=n+p.height;
        return Ext.create("Ext.util.Region",n,c,m,d)
        },
    startDrag:function(b,e){
        var d=this,a=d.sprite.attr,c=a.translation;
        if(d.sprite.vml){
            d.prevX=b+a.x;
            d.prevY=e+a.y
            }else{
            d.prevX=b-c.x;
            d.prevY=e-c.y
            }
        },
onDrag:function(f){
    var d=f.getXY(),c=this,b=c.sprite,a=b.attr;
    c.translateX=d[0]-c.prevX;
    c.translateY=d[1]-c.prevY;
    b.setAttributes({
        translate:{
            x:c.translateX,
            y:c.translateY
            }
        },true);
if(b.vml){
    c.prevX=d[0]+a.x||0;
    c.prevY=d[1]+a.y||0
    }
}
});
Ext.define("Ext.tip.QuickTipManager",function(){
    var b,a=false;
    return{
        requires:["Ext.tip.QuickTip"],
        singleton:true,
        alternateClassName:"Ext.QuickTips",
        init:function(f,d){
            if(!b){
                if(!Ext.isReady){
                    Ext.onReady(function(){
                        Ext.tip.QuickTipManager.init(f)
                        });
                    return
                }
                var c=Ext.apply({
                    disabled:a
                },d),e=c.className,g=c.xtype;
                if(e){
                    delete c.className
                    }else{
                    if(g){
                        e="widget."+g;
                        delete c.xtype
                        }
                    }
                if(f!==false){
                c.renderTo=document.body
                }
                b=Ext.create(e||"Ext.tip.QuickTip",c)
            }
        },
destroy:function(){
    if(b){
        var c;
        b.destroy();
        b=c
        }
    },
ddDisable:function(){
    if(b&&!a){
        b.disable()
        }
    },
ddEnable:function(){
    if(b&&!a){
        b.enable()
        }
    },
enable:function(){
    if(b){
        b.enable()
        }
        a=false
    },
disable:function(){
    if(b){
        b.disable()
        }
        a=true
    },
isEnabled:function(){
    return b!==undefined&&!b.disabled
    },
getQuickTip:function(){
    return b
    },
register:function(){
    b.register.apply(b,arguments)
    },
unregister:function(){
    b.unregister.apply(b,arguments)
    },
tips:function(){
    b.register.apply(b,arguments)
    }
}
}());
Ext.define("Ext.panel.Tool",{
    extend:"Ext.Component",
    requires:["Ext.tip.QuickTipManager"],
    alias:"widget.tool",
    baseCls:Ext.baseCSSPrefix+"tool",
    disabledCls:Ext.baseCSSPrefix+"tool-disabled",
    toolPressedCls:Ext.baseCSSPrefix+"tool-pressed",
    toolOverCls:Ext.baseCSSPrefix+"tool-over",
    ariaRole:"button",
    renderTpl:['<img src="{blank}" class="{baseCls}-{type}" role="presentation"/>'],
    stopEvent:true,
    initComponent:function(){
        var a=this;
        a.addEvents("click");
        a.type=a.type||a.id;
        Ext.applyIf(a.renderData,{
            baseCls:a.baseCls,
            blank:Ext.BLANK_IMAGE_URL,
            type:a.type
            });
        a.renderSelectors.toolEl="."+a.baseCls+"-"+a.type;
        a.callParent()
        },
    afterRender:function(){
        var a=this;
        a.callParent(arguments);
        if(a.qtip){
            if(Ext.isObject(a.qtip)){
                Ext.tip.QuickTipManager.register(Ext.apply({
                    target:a.id
                    },a.qtip))
                }else{
                a.toolEl.dom.qtip=a.qtip
                }
            }
        a.mon(a.toolEl,{
        click:a.onClick,
        mousedown:a.onMouseDown,
        mouseover:a.onMouseOver,
        mouseout:a.onMouseOut,
        scope:a
    })
    },
setType:function(a){
    var b=this;
    b.type=a;
    if(b.rendered){
        b.toolEl.dom.className=b.baseCls+"-"+a
        }
        return b
    },
bindTo:function(a){
    this.owner=a
    },
onClick:function(d,c){
    var b=this,a;
    if(b.disabled){
        return false
        }
        a=b.owner||b.ownerCt;
    b.el.removeCls(b.toolPressedCls);
    b.el.removeCls(b.toolOverCls);
    if(b.stopEvent!==false){
        d.stopEvent()
        }
        Ext.callback(b.handler,b.scope||b,[d,c,a,b]);
    b.fireEvent("click",b,d);
    return true
    },
onDestroy:function(){
    if(Ext.isObject(this.tooltip)){
        Ext.tip.QuickTipManager.unregister(this.id)
        }
        this.callParent()
    },
onMouseDown:function(){
    if(this.disabled){
        return false
        }
        this.el.addCls(this.toolPressedCls)
    },
onMouseOver:function(){
    if(this.disabled){
        return false
        }
        this.el.addCls(this.toolOverCls)
    },
onMouseOut:function(){
    this.el.removeCls(this.toolOverCls)
    }
});
Ext.define("Ext.grid.Lockable",{
    requires:["Ext.grid.LockingView"],
    syncRowHeight:true,
    spacerHidden:true,
    unlockText:"Unlock",
    lockText:"Lock",
    determineXTypeToCreate:function(){
        var c=this,f;
        if(c.subGridXType){
            f=c.subGridXType
            }else{
            var d=this.getXTypes().split("/"),b=d.length,e=d[b-1],a=d[b-2];
            if(a!=="tablepanel"){
                f=a
                }else{
                f=e
                }
            }
        return f
    },
injectLockable:function(){
    this.lockable=true;
    this.hasView=true;
    var j=this,a=j.determineXTypeToCreate(),g=j.getSelectionModel(),b={
        xtype:a,
        enableAnimations:false,
        scroll:false,
        scrollerOwner:false,
        selModel:g,
        border:false,
        cls:Ext.baseCSSPrefix+"grid-inner-locked"
        },h={
        xtype:a,
        enableAnimations:false,
        scrollerOwner:false,
        selModel:g,
        border:false
    },e=0,d,c,f;
    j.addCls(Ext.baseCSSPrefix+"grid-locked");
    Ext.copyTo(h,j,j.normalCfgCopy);
    Ext.copyTo(b,j,j.lockedCfgCopy);
    for(;e<j.normalCfgCopy.length;e++){
        delete j[j.normalCfgCopy[e]]
    }
    for(e=0;e<j.lockedCfgCopy.length;e++){
        delete j[j.lockedCfgCopy[e]]
    }
    j.lockedHeights=[];
    j.normalHeights=[];
    d=j.processColumns(j.columns);
    b.width=d.lockedWidth;
    b.columns=d.locked;
    h.columns=d.normal;
    j.store=Ext.StoreManager.lookup(j.store);
    b.store=j.store;
    h.store=j.store;
    h.flex=1;
    b.viewConfig=j.lockedViewConfig||{};
    
    b.viewConfig.loadingUseMsg=false;
    h.viewConfig=j.normalViewConfig||{};
    
    Ext.applyIf(b.viewConfig,j.viewConfig);
    Ext.applyIf(h.viewConfig,j.viewConfig);
    j.normalGrid=Ext.ComponentManager.create(h);
    j.lockedGrid=Ext.ComponentManager.create(b);
    j.view=Ext.create("Ext.grid.LockingView",{
        locked:j.lockedGrid,
        normal:j.normalGrid,
        panel:j
    });
    if(j.syncRowHeight){
        j.lockedGrid.getView().on({
            refresh:j.onLockedGridAfterRefresh,
            itemupdate:j.onLockedGridAfterUpdate,
            scope:j
        });
        j.normalGrid.getView().on({
            refresh:j.onNormalGridAfterRefresh,
            itemupdate:j.onNormalGridAfterUpdate,
            scope:j
        })
        }
        c=j.lockedGrid.headerCt;
    f=j.normalGrid.headerCt;
    c.lockedCt=true;
    c.lockableInjected=true;
    f.lockableInjected=true;
    c.on({
        columnshow:j.onLockedHeaderShow,
        columnhide:j.onLockedHeaderHide,
        columnmove:j.onLockedHeaderMove,
        sortchange:j.onLockedHeaderSortChange,
        columnresize:j.onLockedHeaderResize,
        scope:j
    });
    f.on({
        columnmove:j.onNormalHeaderMove,
        sortchange:j.onNormalHeaderSortChange,
        scope:j
    });
    j.normalGrid.on({
        scrollershow:j.onScrollerShow,
        scrollerhide:j.onScrollerHide,
        scope:j
    });
    j.lockedGrid.on("afterlayout",j.onLockedGridAfterLayout,j,{
        single:true
    });
    j.modifyHeaderCt();
    j.items=[j.lockedGrid,j.normalGrid];
    j.layout={
        type:"hbox",
        align:"stretch"
    }
},
processColumns:function(f){
    var e=0,a=f.length,b=0,d=[],c=[],g;
    for(;e<a;++e){
        g=f[e];
        g.processed=true;
        if(g.locked){
            b+=g.width;
            d.push(g)
            }else{
            c.push(g)
            }
        }
    return{
    lockedWidth:b,
    locked:d,
    normal:c
}
},
onLockedGridAfterLayout:function(){
    var b=this,a=b.lockedGrid.getView();
    a.on({
        refresh:b.createSpacer,
        beforerefresh:b.destroySpacer,
        scope:b
    })
    },
onLockedHeaderMove:function(){
    if(this.syncRowHeight){
        this.onNormalGridAfterRefresh()
        }
    },
onNormalHeaderMove:function(){
    if(this.syncRowHeight){
        this.onLockedGridAfterRefresh()
        }
    },
createSpacer:function(){
    var d=this,b=Ext.getScrollBarWidth()+(Ext.isIE?2:0),a=d.lockedGrid.getView(),c=a.el;
    d.spacerEl=Ext.core.DomHelper.append(c,{
        cls:d.spacerHidden?(Ext.baseCSSPrefix+"hidden"):"",
        style:"height: "+b+"px;"
        },true)
    },
destroySpacer:function(){
    var a=this;
    if(a.spacerEl){
        a.spacerEl.destroy();
        delete a.spacerEl
        }
    },
onLockedGridAfterRefresh:function(){
    var e=this,a=e.lockedGrid.getView(),c=a.el,f=c.query(a.getItemSelector()),d=f.length,b=0;
    e.lockedHeights=[];
    for(;b<d;b++){
        e.lockedHeights[b]=f[b].clientHeight
        }
        e.syncRowHeights()
    },
onNormalGridAfterRefresh:function(){
    var e=this,a=e.normalGrid.getView(),c=a.el,f=c.query(a.getItemSelector()),d=f.length,b=0;
    e.normalHeights=[];
    for(;b<d;b++){
        e.normalHeights[b]=f[b].clientHeight
        }
        e.syncRowHeights()
    },
onLockedGridAfterUpdate:function(a,b,c){
    this.lockedHeights[b]=c.clientHeight;
    this.syncRowHeights()
    },
onNormalGridAfterUpdate:function(a,b,c){
    this.normalHeights[b]=c.clientHeight;
    this.syncRowHeights()
    },
syncRowHeights:function(){
    var k=this,b=k.lockedHeights,l=k.normalHeights,a=[],j=b.length,g=0,m,d,e,h,f=k.getVerticalScroller(),c;
    if(b.length&&l.length){
        m=k.lockedGrid.getView();
        d=k.normalGrid.getView();
        e=m.el.query(m.getItemSelector());
        h=d.el.query(d.getItemSelector());
        for(;g<j;g++){
            if(!isNaN(b[g])&&!isNaN(l[g])){
                if(b[g]>l[g]){
                    Ext.fly(h[g]).setHeight(b[g])
                    }else{
                    if(b[g]<l[g]){
                        Ext.fly(e[g]).setHeight(l[g])
                        }
                    }
            }
        }
    k.normalGrid.invalidateScroller();
if(f&&f.setViewScrollTop){
    f.setViewScrollTop(k.virtualScrollTop)
    }else{
    c=d.el.dom.scrollTop;
    d.el.dom.scrollTop=c;
    m.el.dom.scrollTop=c
    }
    k.lockedHeights=[];
k.normalHeights=[]
}
},
onScrollerShow:function(a,b){
    if(b==="horizontal"){
        this.spacerHidden=false;
        this.spacerEl.removeCls(Ext.baseCSSPrefix+"hidden")
        }
    },
onScrollerHide:function(a,b){
    if(b==="horizontal"){
        this.spacerHidden=true;
        this.spacerEl.addCls(Ext.baseCSSPrefix+"hidden")
        }
    },
modifyHeaderCt:function(){
    var a=this;
    a.lockedGrid.headerCt.getMenuItems=a.getMenuItems(true);
    a.normalGrid.headerCt.getMenuItems=a.getMenuItems(false)
    },
onUnlockMenuClick:function(){
    this.unlock()
    },
onLockMenuClick:function(){
    this.lock()
    },
getMenuItems:function(b){
    var f=this,g=f.unlockText,h=f.lockText,c="xg-hmenu-unlock",e="xg-hmenu-lock",a=Ext.Function.bind(f.onUnlockMenuClick,f),d=Ext.Function.bind(f.onLockMenuClick,f);
    return function(){
        var i=Ext.grid.header.Container.prototype.getMenuItems.call(this);
        i.push("-",{
            cls:c,
            text:g,
            handler:a,
            disabled:!b
            });
        i.push({
            cls:e,
            text:h,
            handler:d,
            disabled:b
        });
        return i
        }
    },
lock:function(a,d){
    var c=this,e=c.normalGrid,g=c.lockedGrid,f=e.headerCt,b=g.headerCt;
    a=a||f.getMenu().activeHeader;
    if(a.flex){
        a.width=a.getWidth();
        delete a.flex
        }
        f.remove(a,false);
    b.suspendLayout=true;
    if(Ext.isDefined(d)){
        b.insert(d,a)
        }else{
        b.add(a)
        }
        b.suspendLayout=false;
    c.syncLockedSection()
    },
syncLockedSection:function(){
    var a=this;
    a.syncLockedWidth();
    a.lockedGrid.getView().refresh();
    a.normalGrid.getView().refresh()
    },
syncLockedWidth:function(){
    var b=this,a=b.lockedGrid.headerCt.getFullWidth(true);
    b.lockedGrid.setWidth(a);
    b.doComponentLayout()
    },
onLockedHeaderResize:function(){
    this.syncLockedWidth()
    },
onLockedHeaderHide:function(){
    this.syncLockedWidth()
    },
onLockedHeaderShow:function(){
    this.syncLockedWidth()
    },
onLockedHeaderSortChange:function(b,c,a){
    if(a){
        this.normalGrid.headerCt.clearOtherSortStates(null,true)
        }
    },
onNormalHeaderSortChange:function(b,c,a){
    if(a){
        this.lockedGrid.headerCt.clearOtherSortStates(null,true)
        }
    },
unlock:function(a,d){
    var c=this,e=c.normalGrid,g=c.lockedGrid,f=e.headerCt,b=g.headerCt;
    if(!Ext.isDefined(d)){
        d=0
        }
        a=a||b.getMenu().activeHeader;
    b.remove(a,false);
    c.syncLockedWidth();
    c.lockedGrid.getView().refresh();
    f.insert(d,a);
    c.normalGrid.getView().refresh()
    },
reconfigureLockable:function(a,b){
    var c=this,e=c.lockedGrid,d=c.normalGrid;
    if(b){
        e.headerCt.removeAll();
        d.headerCt.removeAll();
        b=c.processColumns(b);
        e.setWidth(b.lockedWidth);
        e.headerCt.add(b.locked);
        d.headerCt.add(b.normal)
        }
        if(a){
        a=Ext.data.StoreManager.lookup(a);
        c.store=a;
        e.bindStore(a);
        d.bindStore(a)
        }else{
        e.getView().refresh();
        d.getView().refresh()
        }
    }
});
Ext.define("Ext.data.proxy.Memory",{
    extend:"Ext.data.proxy.Client",
    alias:"proxy.memory",
    alternateClassName:"Ext.data.MemoryProxy",
    constructor:function(a){
        this.callParent([a]);
        this.setReader(this.reader)
        },
    read:function(c,f,d){
        var e=this,b=e.getReader(),a=b.read(e.data);
        Ext.apply(c,{
            resultSet:a
        });
        c.setCompleted();
        c.setSuccessful();
        Ext.callback(f,d||e,[c])
        },
    clear:Ext.emptyFn
    });
Ext.define("Ext.menu.CheckItem",{
    extend:"Ext.menu.Item",
    alias:"widget.menucheckitem",
    checkedCls:Ext.baseCSSPrefix+"menu-item-checked",
    uncheckedCls:Ext.baseCSSPrefix+"menu-item-unchecked",
    groupCls:Ext.baseCSSPrefix+"menu-group-icon",
    hideOnClick:false,
    afterRender:function(){
        var a=this;
        this.callParent();
        a.checked=!a.checked;
        a.setChecked(!a.checked,true)
        },
    initComponent:function(){
        var a=this;
        a.addEvents("beforecheckchange","checkchange");
        a.callParent(arguments);
        Ext.menu.Manager.registerCheckable(a);
        if(a.group){
            if(!a.iconCls){
                a.iconCls=a.groupCls
                }
                if(a.initialConfig.hideOnClick!==false){
                a.hideOnClick=true
                }
            }
    },
disableCheckChange:function(){
    var a=this;
    if(a.iconEl){
        a.iconEl.addCls(a.disabledCls)
        }
        a.checkChangeDisabled=true
    },
enableCheckChange:function(){
    var a=this;
    a.iconEl.removeCls(a.disabledCls);
    a.checkChangeDisabled=false
    },
onClick:function(b){
    var a=this;
    if(!a.disabled&&!a.checkChangeDisabled&&!(a.checked&&a.group)){
        a.setChecked(!a.checked)
        }
        this.callParent([b])
    },
onDestroy:function(){
    Ext.menu.Manager.unregisterCheckable(this);
    this.callParent(arguments)
    },
setChecked:function(c,a){
    var b=this;
    if(b.checked!==c&&(a||b.fireEvent("beforecheckchange",b,c)!==false)){
        if(b.el){
            b.el[c?"addCls":"removeCls"](b.checkedCls)[!c?"addCls":"removeCls"](b.uncheckedCls)
            }
            b.checked=c;
        Ext.menu.Manager.onCheckChange(b,c);
        if(!a){
            Ext.callback(b.checkHandler,b.scope,[b,c]);
            b.fireEvent("checkchange",b,c)
            }
        }
}
});
Ext.define("Ext.menu.Menu",{
    extend:"Ext.panel.Panel",
    alias:"widget.menu",
    requires:["Ext.layout.container.Fit","Ext.layout.container.VBox","Ext.menu.CheckItem","Ext.menu.Item","Ext.menu.KeyNav","Ext.menu.Manager","Ext.menu.Separator"],
    allowOtherMenus:false,
    ariaRole:"menu",
    defaultAlign:"tl-bl?",
    floating:true,
    constrain:true,
    hidden:true,
    hideMode:"visibility",
    ignoreParentClicks:false,
    isMenu:true,
    showSeparator:true,
    minWidth:120,
    initComponent:function(){
        var b=this,d=Ext.baseCSSPrefix,a=[d+"menu"],c=b.bodyCls?[b.bodyCls]:[];
        b.addEvents("click","mouseenter","mouseleave","mouseover");
        Ext.menu.Manager.register(b);
        if(b.plain){
            a.push(d+"menu-plain")
            }
            b.cls=a.join(" ");
        c.unshift(d+"menu-body");
        b.bodyCls=c.join(" ");
        b.layout={
            type:"vbox",
            align:"stretchmax",
            autoSize:true,
            clearInnerCtOnLayout:true,
            overflowHandler:"Scroller"
        };
        
        if(b.floating===false&&b.initialConfig.hidden!==true){
            b.hidden=false
            }
            b.callParent(arguments);
        b.on("beforeshow",function(){
            var e=!!b.items.length;
            if(e&&b.rendered){
                b.el.setStyle("visibility",null)
                }
                return e
            })
        },
    afterRender:function(a){
        var b=this,d=Ext.baseCSSPrefix,c="&#160;";
        b.callParent(arguments);
        if(b.showSeparator){
            b.iconSepEl=b.layout.getRenderTarget().insertFirst({
                cls:d+"menu-icon-separator",
                html:c
            })
            }
            b.focusEl=b.el.createChild({
            cls:d+"menu-focus",
            tabIndex:"-1",
            html:c
        });
        b.mon(b.el,{
            click:b.onClick,
            mouseover:b.onMouseOver,
            scope:b
        });
        b.mouseMonitor=b.el.monitorMouseLeave(100,b.onMouseLeave,b);
        if(b.showSeparator&&((!Ext.isStrict&&Ext.isIE)||Ext.isIE6)){
            b.iconSepEl.setHeight(b.el.getHeight())
            }
            b.keyNav=Ext.create("Ext.menu.KeyNav",b)
        },
    afterLayout:function(){
        var j=this;
        j.callParent(arguments);
        if((!Ext.iStrict&&Ext.isIE)||Ext.isIE6){
            var a=j.layout.getRenderTarget(),c=0,b=j.dockedItems,d=b.length,f=0,g,h,e;
            c=a.getWidth();
            e=c+j.body.getBorderWidth("lr")+j.body.getPadding("lr");
            j.body.setWidth(e);
            for(;f<d,g=b.getAt(f);f++){
                if(g.dock=="left"||g.dock=="right"){
                    e+=g.getWidth()
                    }
                }
            j.el.setWidth(e)
        }
    },
canActivateItem:function(a){
    return a&&!a.isDisabled()&&a.isVisible()&&(a.canActivate||a.getXTypes().indexOf("menuitem")<0)
    },
deactivateActiveItem:function(){
    var a=this;
    if(a.activeItem){
        a.activeItem.deactivate();
        if(!a.activeItem.activated){
            delete a.activeItem
            }
        }
    if(a.focusedItem){
    a.focusedItem.blur();
    if(!a.focusedItem.$focused){
        delete a.focusedItem
        }
    }
},
clearStretch:function(){
    if(this.rendered){
        this.items.each(function(a){
            if(a.componentLayout){
                delete a.componentLayout.lastComponentSize
                }
                if(a.el){
                a.el.setWidth(null)
                }
            })
    }
},
onAdd:function(){
    var a=this;
    a.clearStretch();
    a.callParent(arguments);
    if(Ext.isIE6||Ext.isIE7){
        Ext.Function.defer(a.doComponentLayout,10,a)
        }
    },
onRemove:function(){
    this.clearStretch();
    this.callParent(arguments)
    },
redoComponentLayout:function(){
    if(this.rendered){
        this.clearStretch();
        this.doComponentLayout()
        }
    },
getFocusEl:function(){
    return this.focusEl
    },
hide:function(){
    this.deactivateActiveItem();
    this.callParent(arguments)
    },
getItemFromEvent:function(a){
    return this.getChildByElement(a.getTarget())
    },
lookupComponent:function(b){
    var a=this;
    if(Ext.isString(b)){
        b=a.lookupItemFromString(b)
        }else{
        if(Ext.isObject(b)){
            b=a.lookupItemFromObject(b)
            }
        }
    b.minWidth=b.minWidth||a.minWidth;
return b
},
lookupItemFromObject:function(c){
    var b=this,d=Ext.baseCSSPrefix,a,e;
    if(!c.isComponent){
        if(!c.xtype){
            c=Ext.create("Ext.menu."+(Ext.isBoolean(c.checked)?"Check":"")+"Item",c)
            }else{
            c=Ext.ComponentManager.create(c,c.xtype)
            }
        }
    if(c.isMenuItem){
    c.parentMenu=b
    }
    if(!c.isMenuItem&&!c.dock){
    a=[d+"menu-item",d+"menu-item-cmp"];
    e=Ext.Function.createInterceptor;
    c.focus=e(c.focus,function(){
        this.$focused=true
        },c);
    c.blur=e(c.blur,function(){
        this.$focused=false
        },c);
    if(!b.plain&&(c.indent===true||c.iconCls==="no-icon")){
        a.push(d+"menu-item-indent")
        }
        if(c.rendered){
        c.el.addCls(a)
        }else{
        c.cls=(c.cls?c.cls:"")+" "+a.join(" ")
        }
        c.isMenuItem=true
    }
    return c
},
lookupItemFromString:function(a){
    return(a=="separator"||a=="-")?Ext.createWidget("menuseparator"):Ext.createWidget("menuitem",{
        canActivate:false,
        hideOnClick:false,
        plain:true,
        text:a
    })
    },
onClick:function(c){
    var b=this,a;
    if(b.disabled){
        c.stopEvent();
        return
    }
    if((c.getTarget()==b.focusEl.dom)||c.within(b.layout.getRenderTarget())){
        a=b.getItemFromEvent(c)||b.activeItem;
        if(a){
            if(a.getXTypes().indexOf("menuitem")>=0){
                if(!a.menu||!b.ignoreParentClicks){
                    a.onClick(c)
                    }else{
                    c.stopEvent()
                    }
                }
        }
    b.fireEvent("click",b,a,c)
}
},
onDestroy:function(){
    var a=this;
    Ext.menu.Manager.unregister(a);
    if(a.rendered){
        a.el.un(a.mouseMonitor);
        a.keyNav.destroy();
        delete a.keyNav
        }
        a.callParent(arguments)
    },
onMouseLeave:function(b){
    var a=this;
    a.deactivateActiveItem();
    if(a.disabled){
        return
    }
    a.fireEvent("mouseleave",a,b)
    },
onMouseOver:function(d){
    var c=this,f=d.getRelatedTarget(),a=!c.el.contains(f),b=c.getItemFromEvent(d);
    if(a&&c.parentMenu){
        c.parentMenu.setActiveItem(c.parentItem);
        c.parentMenu.mouseMonitor.mouseenter()
        }
        if(c.disabled){
        return
    }
    if(b){
        c.setActiveItem(b);
        if(b.activated&&b.expandMenu){
            b.expandMenu()
            }
        }
    if(a){
    c.fireEvent("mouseenter",c,d)
    }
    c.fireEvent("mouseover",c,b,d)
},
setActiveItem:function(b){
    var a=this;
    if(b&&(b!=a.activeItem&&b!=a.focusedItem)){
        a.deactivateActiveItem();
        if(a.canActivateItem(b)){
            if(b.activate){
                b.activate();
                if(b.activated){
                    a.activeItem=b;
                    a.focusedItem=b;
                    a.focus()
                    }
                }else{
            b.focus();
            a.focusedItem=b
            }
        }
    b.el.scrollIntoView(a.layout.getRenderTarget())
}
},
showBy:function(b,f,e){
    var a=this,d,c;
    if(a.floating&&b){
        a.layout.autoSize=true;
        a.doAutoRender();
        b=b.el||b;
        d=a.el.getAlignToXY(b,f||a.defaultAlign,e);
        if(a.floatParent){
            c=a.floatParent.getTargetEl().getViewRegion();
            d[0]-=c.x;
            d[1]-=c.y
            }
            a.showAt(d)
        }
        return a
    },
showAt:function(){
    this.callParent(arguments);
    if(this.floating){
        this.doConstrain()
        }
    },
doConstrain:function(){
    var f=this,g=f.el.getY(),h,e,b,i=g,j,d,a,c;
    delete f.height;
    f.setSize();
    e=f.getHeight();
    if(f.floating){
        d=Ext.fly(f.el.dom.parentNode);
        a=d.getScroll().top;
        c=d.getViewSize().height;
        j=g-a;
        h=f.maxHeight?f.maxHeight:c-j;
        if(e>c){
            h=c;
            i=g-j
            }else{
            if(h<e){
                i=g-(e-h);
                h=e
                }
            }
    }else{
    h=f.getHeight()
    }
    if(f.maxHeight){
    h=Math.min(f.maxHeight,h)
    }
    if(e>h&&h>0){
    f.layout.autoSize=false;
    f.setHeight(h);
    if(f.showSeparator){
        f.iconSepEl.setHeight(f.layout.getRenderTarget().dom.scrollHeight)
        }
    }
b=f.getConstrainVector(f.el.dom.parentNode);
if(b){
    f.setPosition(f.getPosition()[0]+b[0])
    }
    f.el.setY(i)
}
});
Ext.define("Ext.draw.Sprite",{
    mixins:{
        observable:"Ext.util.Observable",
        animate:"Ext.util.Animate"
    },
    requires:["Ext.draw.SpriteDD"],
    dirty:false,
    dirtyHidden:false,
    dirtyTransform:false,
    dirtyPath:true,
    dirtyFont:true,
    zIndexDirty:true,
    isSprite:true,
    zIndex:0,
    fontProperties:["font","font-size","font-weight","font-style","font-family","text-anchor","text"],
    pathProperties:["x","y","d","path","height","width","radius","r","rx","ry","cx","cy"],
    constructor:function(a){
        var b=this;
        a=a||{};
        
        b.id=Ext.id(null,"ext-sprite-");
        b.transformations=[];
        Ext.copyTo(this,a,"surface,group,type,draggable");
        b.bbox={};
        
        b.attr={
            zIndex:0,
            translation:{
                x:null,
                y:null
            },
            rotation:{
                degrees:null,
                x:null,
                y:null
            },
            scaling:{
                x:null,
                y:null,
                cx:null,
                cy:null
            }
        };
        
    delete a.surface;
    delete a.group;
    delete a.type;
    delete a.draggable;
    b.setAttributes(a);
    b.addEvents("beforedestroy","destroy","render","mousedown","mouseup","mouseover","mouseout","mousemove","click");
    b.mixins.observable.constructor.apply(this,arguments)
    },
initDraggable:function(){
    var a=this;
    a.draggable=true;
    if(!a.el){
        a.surface.createSpriteElement(a)
        }
        a.dd=Ext.create("Ext.draw.SpriteDD",a,Ext.isBoolean(a.draggable)?null:a.draggable);
    a.on("beforedestroy",a.dd.destroy,a.dd)
    },
setAttributes:function(j,m){
    var r=this,h=r.fontProperties,p=h.length,f=r.pathProperties,e=f.length,q=!!r.surface,a=q&&r.surface.customAttributes||{},b=r.attr,k,n,g,c,o,l,s,d;
    j=Ext.apply({},j);
    for(k in a){
        if(j.hasOwnProperty(k)&&typeof a[k]=="function"){
            Ext.apply(j,a[k].apply(r,[].concat(j[k])))
            }
        }
    if(!!j.hidden!==!!b.hidden){
    r.dirtyHidden=true
    }
    for(n=0;n<e;n++){
    k=f[n];
    if(k in j&&j[k]!==b[k]){
        r.dirtyPath=true;
        break
    }
}
if("zIndex" in j){
    r.zIndexDirty=true
    }
    for(n=0;n<p;n++){
    k=h[n];
    if(k in j&&j[k]!==b[k]){
        r.dirtyFont=true;
        break
    }
}
g=j.translate;
c=b.translation;
if(g){
    if((g.x&&g.x!==c.x)||(g.y&&g.y!==c.y)){
        Ext.apply(c,g);
        r.dirtyTransform=true
        }
        delete j.translate
    }
    o=j.rotate;
l=b.rotation;
if(o){
    if((o.x&&o.x!==l.x)||(o.y&&o.y!==l.y)||(o.degrees&&o.degrees!==l.degrees)){
        Ext.apply(l,o);
        r.dirtyTransform=true
        }
        delete j.rotate
    }
    s=j.scale;
d=b.scaling;
if(s){
    if((s.x&&s.x!==d.x)||(s.y&&s.y!==d.y)||(s.cx&&s.cx!==d.cx)||(s.cy&&s.cy!==d.cy)){
        Ext.apply(d,s);
        r.dirtyTransform=true
        }
        delete j.scale
    }
    Ext.apply(b,j);
r.dirty=true;
if(m===true&&q){
    r.redraw()
    }
    return this
},
getBBox:function(){
    return this.surface.getBBox(this)
    },
setText:function(a){
    return this.surface.setText(this,a)
    },
hide:function(a){
    this.setAttributes({
        hidden:true
    },a);
    return this
    },
show:function(a){
    this.setAttributes({
        hidden:false
    },a);
    return this
    },
remove:function(){
    if(this.surface){
        this.surface.remove(this);
        return true
        }
        return false
    },
onRemove:function(){
    this.surface.onRemove(this)
    },
destroy:function(){
    var a=this;
    if(a.fireEvent("beforedestroy",a)!==false){
        a.remove();
        a.surface.onDestroy(a);
        a.clearListeners();
        a.fireEvent("destroy")
        }
    },
redraw:function(){
    this.surface.renderItem(this);
    return this
    },
setStyle:function(){
    this.el.setStyle.apply(this.el,arguments);
    return this
    },
addCls:function(a){
    this.surface.addCls(this,a);
    return this
    },
removeCls:function(a){
    this.surface.removeCls(this,a);
    return this
    }
});
Ext.define("Ext.draw.engine.Svg",{
    extend:"Ext.draw.Surface",
    requires:["Ext.draw.Draw","Ext.draw.Sprite","Ext.draw.Matrix","Ext.core.Element"],
    engine:"Svg",
    trimRe:/^\s+|\s+$/g,
    spacesRe:/\s+/,
    xlink:"http://www.w3.org/1999/xlink",
    translateAttrs:{
        radius:"r",
        radiusX:"rx",
        radiusY:"ry",
        path:"d",
        lineWidth:"stroke-width",
        fillOpacity:"fill-opacity",
        strokeOpacity:"stroke-opacity",
        strokeLinejoin:"stroke-linejoin"
    },
    parsers:{},
    minDefaults:{
        circle:{
            cx:0,
            cy:0,
            r:0,
            fill:"none",
            stroke:null,
            "stroke-width":null,
            opacity:null,
            "fill-opacity":null,
            "stroke-opacity":null
        },
        ellipse:{
            cx:0,
            cy:0,
            rx:0,
            ry:0,
            fill:"none",
            stroke:null,
            "stroke-width":null,
            opacity:null,
            "fill-opacity":null,
            "stroke-opacity":null
        },
        rect:{
            x:0,
            y:0,
            width:0,
            height:0,
            rx:0,
            ry:0,
            fill:"none",
            stroke:null,
            "stroke-width":null,
            opacity:null,
            "fill-opacity":null,
            "stroke-opacity":null
        },
        text:{
            x:0,
            y:0,
            "text-anchor":"start",
            "font-family":null,
            "font-size":null,
            "font-weight":null,
            "font-style":null,
            fill:"#000",
            stroke:null,
            "stroke-width":null,
            opacity:null,
            "fill-opacity":null,
            "stroke-opacity":null
        },
        path:{
            d:"M0,0",
            fill:"none",
            stroke:null,
            "stroke-width":null,
            opacity:null,
            "fill-opacity":null,
            "stroke-opacity":null
        },
        image:{
            x:0,
            y:0,
            width:0,
            height:0,
            preserveAspectRatio:"none",
            opacity:null
        }
    },
createSvgElement:function(d,a){
    var c=this.domRef.createElementNS("http://www.w3.org/2000/svg",d),b;
    if(a){
        for(b in a){
            c.setAttribute(b,String(a[b]))
            }
        }
        return c
},
createSpriteElement:function(a){
    var b=this.createSvgElement(a.type);
    b.id=a.id;
    if(b.style){
        b.style.webkitTapHighlightColor="rgba(0,0,0,0)"
        }
        a.el=Ext.get(b);
    this.applyZIndex(a);
    a.matrix=Ext.create("Ext.draw.Matrix");
    a.bbox={
        plain:0,
        transform:0
    };
    
    a.fireEvent("render",a);
    return b
    },
getBBox:function(a,b){
    var c=this["getPath"+a.type](a);
    if(b){
        a.bbox.plain=a.bbox.plain||Ext.draw.Draw.pathDimensions(c);
        return a.bbox.plain
        }
        a.bbox.transform=a.bbox.transform||Ext.draw.Draw.pathDimensions(Ext.draw.Draw.mapPath(c,a.matrix));
    return a.bbox.transform
    },
getBBoxText:function(h){
    var j={},f,k,a,c,g,b;
    if(h&&h.el){
        b=h.el.dom;
        try{
            j=b.getBBox();
            return j
            }catch(d){}
        j={
            x:j.x,
            y:Infinity,
            width:0,
            height:0
        };
        
        g=b.getNumberOfChars();
        for(c=0;c<g;c++){
            f=b.getExtentOfChar(c);
            j.y=Math.min(f.y,j.y);
            k=f.y+f.height-j.y;
            j.height=Math.max(j.height,k);
            a=f.x+f.width-j.x;
            j.width=Math.max(j.width,a)
            }
            return j
        }
    },
hide:function(){
    Ext.get(this.el).hide()
    },
show:function(){
    Ext.get(this.el).show()
    },
hidePrim:function(a){
    this.addCls(a,Ext.baseCSSPrefix+"hide-visibility")
    },
showPrim:function(a){
    this.removeCls(a,Ext.baseCSSPrefix+"hide-visibility")
    },
getDefs:function(){
    return this._defs||(this._defs=this.createSvgElement("defs"))
    },
transform:function(d){
    var g=this,a=Ext.create("Ext.draw.Matrix"),f=d.transformations,h=f.length,c=0,b,e;
    for(;c<h;c++){
        b=f[c];
        e=b.type;
        if(e=="translate"){
            a.translate(b.x,b.y)
            }else{
            if(e=="rotate"){
                a.rotate(b.degrees,b.x,b.y)
                }else{
                if(e=="scale"){
                    a.scale(b.x,b.y,b.centerX,b.centerY)
                    }
                }
        }
    }
d.matrix=a;
d.el.set({
    transform:a.toSvg()
    })
},
setSize:function(a,c){
    var d=this,b=d.el;
    a=+a||d.width;
    c=+c||d.height;
    d.width=a;
    d.height=c;
    b.setSize(a,c);
    b.set({
        width:a,
        height:c
    });
    d.callParent([a,c])
    },
getRegion:function(){
    var e=this.el.getXY(),c=this.bgRect.getXY(),b=Math.max,a=b(e[0],c[0]),d=b(e[1],c[1]);
    return{
        left:a,
        top:d,
        right:a+this.width,
        bottom:d+this.height
        }
    },
onRemove:function(a){
    if(a.el){
        a.el.remove();
        delete a.el
        }
        this.callParent(arguments)
    },
setViewBox:function(b,d,c,a){
    if(isFinite(b)&&isFinite(d)&&isFinite(c)&&isFinite(a)){
        this.callParent(arguments);
        this.el.dom.setAttribute("viewBox",[b,d,c,a].join(" "))
        }
    },
render:function(c){
    var f=this;
    if(!f.el){
        var e=f.width||10,b=f.height||10,d=f.createSvgElement("svg",{
            xmlns:"http://www.w3.org/2000/svg",
            version:1.1,
            width:e,
            height:b
        }),a=f.getDefs(),g=f.createSvgElement("rect",{
            width:"100%",
            height:"100%",
            fill:"#000",
            stroke:"none",
            opacity:0
        }),h;
        if(Ext.isSafari3){
            h=f.createSvgElement("rect",{
                x:-10,
                y:-10,
                width:"110%",
                height:"110%",
                fill:"none",
                stroke:"#000"
            })
            }
            d.appendChild(a);
        if(Ext.isSafari3){
            d.appendChild(h)
            }
            d.appendChild(g);
        c.appendChild(d);
        f.el=Ext.get(d);
        f.bgRect=Ext.get(g);
        if(Ext.isSafari3){
            f.webkitRect=Ext.get(h);
            f.webkitRect.hide()
            }
            f.el.on({
            scope:f,
            mouseup:f.onMouseUp,
            mousedown:f.onMouseDown,
            mouseover:f.onMouseOver,
            mouseout:f.onMouseOut,
            mousemove:f.onMouseMove,
            mouseenter:f.onMouseEnter,
            mouseleave:f.onMouseLeave,
            click:f.onClick
            })
        }
        f.renderAll()
    },
onMouseEnter:function(a){
    if(this.el.parent().getRegion().contains(a.getPoint())){
        this.fireEvent("mouseenter",a)
        }
    },
onMouseLeave:function(a){
    if(!this.el.parent().getRegion().contains(a.getPoint())){
        this.fireEvent("mouseleave",a)
        }
    },
processEvent:function(b,f){
    var d=f.getTarget(),a=this.surface,c;
    this.fireEvent(b,f);
    if(d.nodeName=="tspan"&&d.parentNode){
        d=d.parentNode
        }
        c=this.items.get(d.id);
    if(c){
        c.fireEvent(b,c,f)
        }
    },
tuneText:function(h,j){
    var a=h.el.dom,b=[],l,g,k,d,e,c,f;
    if(j.hasOwnProperty("text")){
        b=this.setText(h,j.text)
        }
        if(b.length){
        l=this.getBBoxText(h).height;
        for(d=0,e=b.length;d<e;d++){
            f=(Ext.isFF3_0||Ext.isFF3_5)?2:4;
            b[d].setAttribute("dy",d?l*1.2:l/f)
            }
            h.dirty=true
        }
    },
setText:function(k,d){
    var g=this,a=k.el.dom,j=a.getAttribute("x"),b=[],m,h,l,e,f,c;
    while(a.firstChild){
        a.removeChild(a.firstChild)
        }
        c=String(d).split("\n");
    for(e=0,f=c.length;e<f;e++){
        l=c[e];
        if(l){
            h=g.createSvgElement("tspan");
            h.appendChild(document.createTextNode(Ext.htmlDecode(l)));
            h.setAttribute("x",j);
            a.appendChild(h);
            b[e]=h
            }
        }
    return b
},
renderAll:function(){
    this.items.each(this.renderItem,this)
    },
renderItem:function(a){
    if(!this.el){
        return
    }
    if(!a.el){
        this.createSpriteElement(a)
        }
        if(a.zIndexDirty){
        this.applyZIndex(a)
        }
        if(a.dirty){
        this.applyAttrs(a);
        this.applyTransformations(a)
        }
    },
redraw:function(a){
    a.dirty=a.zIndexDirty=true;
    this.renderItem(a)
    },
applyAttrs:function(q){
    var l=this,c=q.el,p=q.group,h=q.attr,r=l.parsers,f=l.gradientsMap||{},j=Ext.isSafari&&!Ext.isStrict,e,g,k,o,d,n,b,a,m;
    if(p){
        e=[].concat(p);
        k=e.length;
        for(g=0;g<k;g++){
            p=e[g];
            l.getGroup(p).add(q)
            }
            delete q.group
        }
        o=l.scrubAttrs(q)||{};
    
    q.bbox.plain=0;
    q.bbox.transform=0;
    if(q.type=="circle"||q.type=="ellipse"){
        o.cx=o.cx||o.x;
        o.cy=o.cy||o.y
        }else{
        if(q.type=="rect"){
            o.rx=o.ry=o.r
            }else{
            if(q.type=="path"&&o.d){
                o.d=Ext.draw.Draw.pathToString(Ext.draw.Draw.pathToAbsolute(o.d))
                }
            }
    }
q.dirtyPath=false;
if(o["clip-rect"]){
    l.setClip(q,o);
    delete o["clip-rect"]
}
if(q.type=="text"&&o.font&&q.dirtyFont){
    c.set({
        style:"font: "+o.font
        });
    q.dirtyFont=false
    }
    if(q.type=="image"){
    c.dom.setAttributeNS(l.xlink,"href",o.src)
    }
    Ext.applyIf(o,l.minDefaults[q.type]);
if(q.dirtyHidden){
    (h.hidden)?l.hidePrim(q):l.showPrim(q);
    q.dirtyHidden=false
    }
    for(n in o){
    if(o.hasOwnProperty(n)&&o[n]!=null){
        if(j&&("color|stroke|fill".indexOf(n)>-1)&&(o[n] in f)){
            o[n]=f[o[n]]
            }
            if(n in r){
            c.dom.setAttribute(n,r[n](o[n],q,l))
            }else{
            c.dom.setAttribute(n,o[n])
            }
        }
}
if(q.type=="text"){
    l.tuneText(q,o)
    }
    b=h.style;
if(b){
    c.setStyle(b)
    }
    q.dirty=false;
if(Ext.isSafari3){
    l.webkitRect.show();
    setTimeout(function(){
        l.webkitRect.hide()
        })
    }
},
setClip:function(b,f){
    var e=this,d=f["clip-rect"],a,c;
    if(d){
        if(b.clip){
            b.clip.parentNode.parentNode.removeChild(b.clip.parentNode)
            }
            a=e.createSvgElement("clipPath");
        c=e.createSvgElement("rect");
        a.id=Ext.id(null,"ext-clip-");
        c.setAttribute("x",d.x);
        c.setAttribute("y",d.y);
        c.setAttribute("width",d.width);
        c.setAttribute("height",d.height);
        a.appendChild(c);
        e.getDefs().appendChild(a);
        b.el.dom.setAttribute("clip-path","url(#"+a.id+")");
        b.clip=c
        }
    },
applyZIndex:function(c){
    var a=this.normalizeSpriteCollection(c),d=c.el,b;
    if(this.el.dom.childNodes[a+2]!==d.dom){
        if(a>0){
            do{
                b=this.items.getAt(--a).el
                }while(!b&&a>0)
        }
        d.insertAfter(b||this.bgRect)
        }
        c.zIndexDirty=false
    },
createItem:function(a){
    var b=Ext.create("Ext.draw.Sprite",a);
    b.surface=this;
    return b
    },
addGradient:function(g){
    g=Ext.draw.Draw.parseGradient(g);
    var e=this,d=g.stops.length,a=g.vector,k=Ext.isSafari&&!Ext.isStrict,h,f,j,c,b;
    b=e.gradientsMap||{};
    
    if(!k){
        if(g.type=="linear"){
            h=e.createSvgElement("linearGradient");
            h.setAttribute("x1",a[0]);
            h.setAttribute("y1",a[1]);
            h.setAttribute("x2",a[2]);
            h.setAttribute("y2",a[3])
            }else{
            h=e.createSvgElement("radialGradient");
            h.setAttribute("cx",g.centerX);
            h.setAttribute("cy",g.centerY);
            h.setAttribute("r",g.radius);
            if(Ext.isNumber(g.focalX)&&Ext.isNumber(g.focalY)){
                h.setAttribute("fx",g.focalX);
                h.setAttribute("fy",g.focalY)
                }
            }
        h.id=g.id;
    e.getDefs().appendChild(h);
    for(c=0;c<d;c++){
        f=g.stops[c];
        j=e.createSvgElement("stop");
        j.setAttribute("offset",f.offset+"%");
        j.setAttribute("stop-color",f.color);
        j.setAttribute("stop-opacity",f.opacity);
        h.appendChild(j)
        }
    }else{
    b["url(#"+g.id+")"]=g.stops[0].color
    }
    e.gradientsMap=b
},
hasCls:function(a,b){
    return b&&(" "+(a.el.dom.getAttribute("class")||"")+" ").indexOf(" "+b+" ")!=-1
    },
addCls:function(e,g){
    var f=e.el,d,a,c,b=[],h=f.getAttribute("class")||"";
    if(!Ext.isArray(g)){
        if(typeof g=="string"&&!this.hasCls(e,g)){
            f.set({
                "class":h+" "+g
                })
            }
        }else{
    for(d=0,a=g.length;d<a;d++){
        c=g[d];
        if(typeof c=="string"&&(" "+h+" ").indexOf(" "+c+" ")==-1){
            b.push(c)
            }
        }
    if(b.length){
    f.set({
        "class":" "+b.join(" ")
        })
    }
}
},
removeCls:function(j,f){
    var g=this,b=j.el,d=b.getAttribute("class")||"",c,h,e,k,a;
    if(!Ext.isArray(f)){
        f=[f]
        }
        if(d){
        a=d.replace(g.trimRe," ").split(g.spacesRe);
        for(c=0,e=f.length;c<e;c++){
            k=f[c];
            if(typeof k=="string"){
                k=k.replace(g.trimRe,"");
                h=Ext.Array.indexOf(a,k);
                if(h!=-1){
                    Ext.Array.erase(a,h,1)
                    }
                }
        }
        b.set({
    "class":a.join(" ")
    })
}
},
destroy:function(){
    var a=this;
    a.callParent();
    if(a.el){
        a.el.remove()
        }
        delete a.el
    }
});
Ext.define("Ext.draw.engine.Vml",{
    extend:"Ext.draw.Surface",
    requires:["Ext.draw.Draw","Ext.draw.Color","Ext.draw.Sprite","Ext.draw.Matrix","Ext.core.Element"],
    engine:"Vml",
    map:{
        M:"m",
        L:"l",
        C:"c",
        Z:"x",
        m:"t",
        l:"r",
        c:"v",
        z:"x"
    },
    bitesRe:/([clmz]),?([^clmz]*)/gi,
    valRe:/-?[^,\s-]+/g,
    fillUrlRe:/^url\(\s*['"]?([^\)]+?)['"]?\s*\)$/i,
    pathlike:/^(path|rect)$/,
    NonVmlPathRe:/[ahqstv]/ig,
    partialPathRe:/[clmz]/g,
    fontFamilyRe:/^['"]+|['"]+$/g,
    baseVmlCls:Ext.baseCSSPrefix+"vml-base",
    vmlGroupCls:Ext.baseCSSPrefix+"vml-group",
    spriteCls:Ext.baseCSSPrefix+"vml-sprite",
    measureSpanCls:Ext.baseCSSPrefix+"vml-measure-span",
    zoom:21600,
    coordsize:1000,
    coordorigin:"0 0",
    path2vml:function(s){
        var m=this,t=m.NonVmlPathRe,b=m.map,e=m.valRe,q=m.zoom,d=m.bitesRe,f=Ext.Function.bind(Ext.draw.Draw.pathToAbsolute,Ext.draw.Draw),l,n,c,a,h,o,g,k;
        if(String(s).match(t)){
            f=Ext.Function.bind(Ext.draw.Draw.path2curve,Ext.draw.Draw)
            }else{
            if(!String(s).match(m.partialPathRe)){
                l=String(s).replace(d,function(r,v,j){
                    var u=[],i=v.toLowerCase()=="m",p=b[v];
                    j.replace(e,function(w){
                        if(i&&u[length]==2){
                            p+=u+b[v=="m"?"l":"L"];
                            u=[]
                            }
                            u.push(Math.round(w*q))
                        });
                    return p+u
                    });
                return l
                }
            }
        n=f(s);
    l=[];
    for(h=0,o=n.length;h<o;h++){
        c=n[h];
        a=n[h][0].toLowerCase();
        if(a=="z"){
            a="x"
            }
            for(g=1,k=c.length;g<k;g++){
            a+=Math.round(c[g]*m.zoom)+(g!=k-1?",":"")
            }
            l.push(a)
        }
        return l.join(" ")
    },
translateAttrs:{
    radius:"r",
    radiusX:"rx",
    radiusY:"ry",
    lineWidth:"stroke-width",
    fillOpacity:"fill-opacity",
    strokeOpacity:"stroke-opacity",
    strokeLinejoin:"stroke-linejoin"
},
minDefaults:{
    circle:{
        fill:"none",
        stroke:null,
        "stroke-width":null,
        opacity:null,
        "fill-opacity":null,
        "stroke-opacity":null
    },
    ellipse:{
        cx:0,
        cy:0,
        rx:0,
        ry:0,
        fill:"none",
        stroke:null,
        "stroke-width":null,
        opacity:null,
        "fill-opacity":null,
        "stroke-opacity":null
    },
    rect:{
        x:0,
        y:0,
        width:0,
        height:0,
        rx:0,
        ry:0,
        fill:"none",
        stroke:null,
        "stroke-width":null,
        opacity:null,
        "fill-opacity":null,
        "stroke-opacity":null
    },
    text:{
        x:0,
        y:0,
        "text-anchor":"start",
        font:'10px "Arial"',
        fill:"#000",
        stroke:null,
        "stroke-width":null,
        opacity:null,
        "fill-opacity":null,
        "stroke-opacity":null
    },
    path:{
        d:"M0,0",
        fill:"none",
        stroke:null,
        "stroke-width":null,
        opacity:null,
        "fill-opacity":null,
        "stroke-opacity":null
    },
    image:{
        x:0,
        y:0,
        width:0,
        height:0,
        preserveAspectRatio:"none",
        opacity:null
    }
},
onMouseEnter:function(a){
    this.fireEvent("mouseenter",a)
    },
onMouseLeave:function(a){
    this.fireEvent("mouseleave",a)
    },
processEvent:function(b,f){
    var d=f.getTarget(),a=this.surface,c;
    this.fireEvent(b,f);
    c=this.items.get(d.id);
    if(c){
        c.fireEvent(b,c,f)
        }
    },
createSpriteElement:function(g){
    var e=this,d=g.attr,f=g.type,i=e.zoom,b=g.vml||(g.vml={}),j=Math.round,c=(f==="image")?e.createNode("image"):e.createNode("shape"),k,h,a;
    c.coordsize=i+" "+i;
    c.coordorigin=d.coordorigin||"0 0";
    Ext.get(c).addCls(e.spriteCls);
    if(f=="text"){
        b.path=k=e.createNode("path");
        k.textpathok=true;
        b.textpath=a=e.createNode("textpath");
        a.on=true;
        c.appendChild(a);
        c.appendChild(k)
        }
        c.id=g.id;
    g.el=Ext.get(c);
    e.el.appendChild(c);
    if(f!=="image"){
        h=e.createNode("skew");
        h.on=true;
        c.appendChild(h);
        g.skew=h
        }
        g.matrix=Ext.create("Ext.draw.Matrix");
    g.bbox={
        plain:null,
        transform:null
    };
    
    g.fireEvent("render",g);
    return g.el
    },
getBBox:function(a,b){
    var c=this["getPath"+a.type](a);
    if(b){
        a.bbox.plain=a.bbox.plain||Ext.draw.Draw.pathDimensions(c);
        return a.bbox.plain
        }
        a.bbox.transform=a.bbox.transform||Ext.draw.Draw.pathDimensions(Ext.draw.Draw.mapPath(c,a.matrix));
    return a.bbox.transform
    },
getBBoxText:function(b){
    var a=b.vml;
    return{
        x:a.X+(a.bbx||0)-a.W/2,
        y:a.Y-a.H/2,
        width:a.W,
        height:a.H
        }
    },
applyAttrs:function(m){
    var s=this,d=m.vml,j=m.group,b=m.attr,c=m.el,o=c.dom,p,u,r,n,k,q,l,t,a;
    if(j){
        r=[].concat(j);
        k=r.length;
        for(n=0;n<k;n++){
            j=r[n];
            s.getGroup(j).add(m)
            }
            delete m.group
        }
        q=s.scrubAttrs(m)||{};
    
    if(m.zIndexDirty){
        s.setZIndex(m)
        }
        Ext.applyIf(q,s.minDefaults[m.type]);
    if(m.type=="image"){
        Ext.apply(m.attr,{
            x:q.x,
            y:q.y,
            width:q.width,
            height:q.height
            });
        a=m.getBBox();
        c.setStyle({
            width:a.width+"px",
            height:a.height+"px"
            });
        o.src=q.src
        }
        if(o.href){
        o.href=q.href
        }
        if(o.title){
        o.title=q.title
        }
        if(o.target){
        o.target=q.target
        }
        if(o.cursor){
        o.cursor=q.cursor
        }
        if(m.dirtyHidden){
        (q.hidden)?s.hidePrim(m):s.showPrim(m);
        m.dirtyHidden=false
        }
        if(m.dirtyPath){
        if(m.type=="circle"||m.type=="ellipse"){
            var f=q.x,e=q.y,h=q.rx||q.r||0,g=q.ry||q.r||0;
            o.path=Ext.String.format("ar{0},{1},{2},{3},{4},{1},{4},{1}",Math.round((f-h)*s.zoom),Math.round((e-g)*s.zoom),Math.round((f+h)*s.zoom),Math.round((e+g)*s.zoom),Math.round(f*s.zoom));
            m.dirtyPath=false
            }else{
            if(m.type!=="text"&&m.type!=="image"){
                m.attr.path=q.path=s.setPaths(m,q)||q.path;
                o.path=s.path2vml(q.path);
                m.dirtyPath=false
                }
            }
    }
if("clip-rect" in q){
    s.setClip(m,q)
    }
    if(m.type=="text"){
    s.setTextAttributes(m,q)
    }
    if(q.opacity||q["stroke-opacity"]||q.fill){
    s.setFill(m,q)
    }
    if(q.stroke||q["stroke-opacity"]||q.fill){
    s.setStroke(m,q)
    }
    p=b.style;
if(p){
    c.setStyle(p)
    }
    m.dirty=false
},
setZIndex:function(a){
    if(a.el){
        if(a.attr.zIndex!=undefined){
            a.el.setStyle("zIndex",a.attr.zIndex)
            }
            a.zIndexDirty=false
        }
    },
setPaths:function(b,c){
    var a=b.attr;
    b.bbox.plain=null;
    b.bbox.transform=null;
    if(b.type=="circle"){
        a.rx=a.ry=c.r;
        return Ext.draw.Draw.ellipsePath(b)
        }else{
        if(b.type=="ellipse"){
            a.rx=c.rx;
            a.ry=c.ry;
            return Ext.draw.Draw.ellipsePath(b)
            }else{
            if(b.type=="rect"){
                a.rx=a.ry=c.r;
                return Ext.draw.Draw.rectPath(b)
                }else{
                if(b.type=="path"&&a.path){
                    return Ext.draw.Draw.pathToAbsolute(a.path)
                    }
                }
        }
}
return false
},
setFill:function(j,e){
    var g=this,c=j.el.dom,i=c.fill,b=false,f,h,a,k,d;
    if(!i){
        i=c.fill=g.createNode("fill");
        b=true
        }
        if(Ext.isArray(e.fill)){
        e.fill=e.fill[0]
        }
        if(e.fill=="none"){
        i.on=false
        }else{
        if(typeof e.opacity=="number"){
            i.opacity=e.opacity
            }
            if(typeof e["fill-opacity"]=="number"){
            i.opacity=e["fill-opacity"]
            }
            i.on=true;
        if(typeof e.fill=="string"){
            a=e.fill.match(g.fillUrlRe);
            if(a){
                a=a[1];
                if(a.charAt(0)=="#"){
                    h=g.gradientsColl.getByKey(a.substring(1))
                    }
                    if(h){
                    k=e.rotation;
                    d=-(h.angle+270+(k?k.degrees:0))%360;
                    if(d===0){
                        d=180
                        }
                        i.angle=d;
                    i.type="gradient";
                    i.method="sigma";
                    i.colors.value=h.colors
                    }else{
                    i.src=a;
                    i.type="tile"
                    }
                }else{
            i.color=Ext.draw.Color.toHex(e.fill);
            i.src="";
            i.type="solid"
            }
        }
}
if(b){
    c.appendChild(i)
    }
},
setStroke:function(b,g){
    var e=this,d=b.el.dom,h=b.strokeEl,f=false,c,a;
    if(!h){
        h=b.strokeEl=e.createNode("stroke");
        f=true
        }
        if(Ext.isArray(g.stroke)){
        g.stroke=g.stroke[0]
        }
        if(!g.stroke||g.stroke=="none"||g.stroke==0||g["stroke-width"]==0){
        h.on=false
        }else{
        h.on=true;
        if(g.stroke&&!g.stroke.match(e.fillUrlRe)){
            h.color=Ext.draw.Color.toHex(g.stroke)
            }
            h.joinstyle=g["stroke-linejoin"];
        h.endcap=g["stroke-linecap"]||"round";
        h.miterlimit=g["stroke-miterlimit"]||8;
        c=parseFloat(g["stroke-width"]||1)*0.75;
        a=g["stroke-opacity"]||1;
        if(Ext.isNumber(c)&&c<1){
            h.weight=1;
            h.opacity=a*c
            }else{
            h.weight=c;
            h.opacity=a
            }
        }
    if(f){
    d.appendChild(h)
    }
},
setClip:function(b,f){
    var e=this,c=b.el,a=b.clipEl,d=String(f["clip-rect"]).split(e.separatorRe);
    if(!a){
        a=b.clipEl=e.el.insertFirst(Ext.getDoc().dom.createElement("div"));
        a.addCls(Ext.baseCSSPrefix+"vml-sprite")
        }
        if(d.length==4){
        d[2]=+d[2]+(+d[0]);
        d[3]=+d[3]+(+d[1]);
        a.setStyle("clip",Ext.String.format("rect({1}px {2}px {3}px {0}px)",d[0],d[1],d[2],d[3]));
        a.setSize(e.el.width,e.el.height)
        }else{
        a.setStyle("clip","")
        }
    },
setTextAttributes:function(h,c){
    var g=this,a=h.vml,e=a.textpath.style,f=g.span.style,i=g.zoom,j=Math.round,k={
        fontSize:"font-size",
        fontWeight:"font-weight",
        fontStyle:"font-style"
    },b,d;
    if(h.dirtyFont){
        if(c.font){
            e.font=f.font=c.font
            }
            if(c["font-family"]){
            e.fontFamily='"'+c["font-family"].split(",")[0].replace(g.fontFamilyRe,"")+'"';
            f.fontFamily=c["font-family"]
            }
            for(b in k){
            d=c[k[b]];
            if(d){
                e[b]=f[b]=d
                }
            }
        g.setText(h,c.text);
    if(a.textpath.string){
        g.span.innerHTML=String(a.textpath.string).replace(/</g,"&#60;").replace(/&/g,"&#38;").replace(/\n/g,"<br>")
        }
        a.W=g.span.offsetWidth;
    a.H=g.span.offsetHeight+2;
    if(c["text-anchor"]=="middle"){
        e["v-text-align"]="center"
        }else{
        if(c["text-anchor"]=="end"){
            e["v-text-align"]="right";
            a.bbx=-Math.round(a.W/2)
            }else{
            e["v-text-align"]="left";
            a.bbx=Math.round(a.W/2)
            }
        }
}
a.X=c.x;
a.Y=c.y;
a.path.v=Ext.String.format("m{0},{1}l{2},{1}",Math.round(a.X*i),Math.round(a.Y*i),Math.round(a.X*i)+1);
h.bbox.plain=null;
h.bbox.transform=null;
h.dirtyFont=false
},
setText:function(a,b){
    a.vml.textpath.string=Ext.htmlDecode(b)
    },
hide:function(){
    this.el.hide()
    },
show:function(){
    this.el.show()
    },
hidePrim:function(a){
    a.el.addCls(Ext.baseCSSPrefix+"hide-visibility")
    },
showPrim:function(a){
    a.el.removeCls(Ext.baseCSSPrefix+"hide-visibility")
    },
setSize:function(b,a){
    var c=this;
    b=b||c.width;
    a=a||c.height;
    c.width=b;
    c.height=a;
    if(c.el){
        if(b!=undefined){
            c.el.setWidth(b)
            }
            if(a!=undefined){
            c.el.setHeight(a)
            }
            c.applyViewBox();
        c.callParent(arguments)
        }
    },
setViewBox:function(b,d,c,a){
    this.callParent(arguments);
    this.viewBox={
        x:b,
        y:d,
        width:c,
        height:a
    };
    
    this.applyViewBox()
    },
applyViewBox:function(){
    var d=this,k=d.viewBox,a=d.width,g=d.height,f,e,i,b,h,c,j;
    if(k&&(a||g)){
        f=k.x;
        e=k.y;
        i=k.width;
        b=k.height;
        h=g/b;
        c=a/i;
        if(i*h<a){
            f-=(a-i*h)/2/h
            }
            if(b*c<g){
            e-=(g-b*c)/2/c
            }
            j=1/Math.max(i/a,b/g);
        d.viewBoxShift={
            dx:-f,
            dy:-e,
            scale:j
        };
        
        d.items.each(function(l){
            d.transform(l)
            })
        }
    },
onAdd:function(a){
    this.callParent(arguments);
    if(this.el){
        this.renderItem(a)
        }
    },
onRemove:function(a){
    if(a.el){
        a.el.remove();
        delete a.el
        }
        this.callParent(arguments)
    },
render:function(a){
    var c=this,f=Ext.getDoc().dom;
    if(!c.createNode){
        try{
            if(!f.namespaces.rvml){
                f.namespaces.add("rvml","urn:schemas-microsoft-com:vml")
                }
                c.createNode=function(e){
                return f.createElement("<rvml:"+e+' class="rvml">')
                }
            }catch(d){
        c.createNode=function(e){
            return f.createElement("<"+e+' xmlns="urn:schemas-microsoft.com:vml" class="rvml">')
            }
        }
}
if(!c.el){
    var b=f.createElement("div");
    c.el=Ext.get(b);
    c.el.addCls(c.baseVmlCls);
    c.span=f.createElement("span");
    Ext.get(c.span).addCls(c.measureSpanCls);
    b.appendChild(c.span);
    c.el.setSize(c.width||10,c.height||10);
    a.appendChild(b);
    c.el.on({
        scope:c,
        mouseup:c.onMouseUp,
        mousedown:c.onMouseDown,
        mouseover:c.onMouseOver,
        mouseout:c.onMouseOut,
        mousemove:c.onMouseMove,
        mouseenter:c.onMouseEnter,
        mouseleave:c.onMouseLeave,
        click:c.onClick
        })
    }
    c.renderAll()
},
renderAll:function(){
    this.items.each(this.renderItem,this)
    },
redraw:function(a){
    a.dirty=true;
    this.renderItem(a)
    },
renderItem:function(a){
    if(!this.el){
        return
    }
    if(!a.el){
        this.createSpriteElement(a)
        }
        if(a.dirty){
        this.applyAttrs(a);
        if(a.dirtyTransform){
            this.applyTransformations(a)
            }
        }
},
rotationCompensation:function(d,c,a){
    var b=Ext.create("Ext.draw.Matrix");
    b.rotate(-d,0.5,0.5);
    return{
        x:b.x(c,a),
        y:b.y(c,a)
        }
    },
transform:function(s){
    var B=this,v=Ext.create("Ext.draw.Matrix"),l=s.transformations,q=l.length,w=0,j=0,c=1,b=1,h="",e=s.el,x=e.dom,t=x.style,a=B.zoom,f=s.skew,A,z,n,g,m,k,u,r,p,o,d;
    for(;w<q;w++){
        n=l[w];
        g=n.type;
        if(g=="translate"){
            v.translate(n.x,n.y)
            }else{
            if(g=="rotate"){
                v.rotate(n.degrees,n.x,n.y);
                j+=n.degrees
                }else{
                if(g=="scale"){
                    v.scale(n.x,n.y,n.centerX,n.centerY);
                    c*=n.x;
                    b*=n.y
                    }
                }
        }
    }
if(B.viewBoxShift){
    v.scale(B.viewBoxShift.scale,B.viewBoxShift.scale,-1,-1);
    v.add(1,0,0,1,B.viewBoxShift.dx,B.viewBoxShift.dy)
    }
    s.matrix=v;
if(s.type!="image"&&f){
    f.matrix=v.toString();
    f.offset=v.offset()
    }else{
    A=v.matrix[0][2];
    z=v.matrix[1][2];
    p=a/c;
    o=a/b;
    x.coordsize=Math.abs(p)+" "+Math.abs(o);
    r=j*(c*((b<0)?-1:1));
    if(r!=t.rotation&&!(r===0&&!t.rotation)){
        t.rotation=r
        }
        if(j){
        m=B.rotationCompensation(j,A,z);
        A=m.x;
        z=m.y
        }
        if(c<0){
        h+="x"
        }
        if(b<0){
        h+=" y";
        k=-1
        }
        if(h!=""&&!x.style.flip){
        t.flip=h
        }
        d=(A*-p)+" "+(z*-o);
    if(d!=x.coordorigin){
        x.coordorigin=(A*-p)+" "+(z*-o)
        }
    }
},
createItem:function(a){
    return Ext.create("Ext.draw.Sprite",a)
    },
getRegion:function(){
    return this.el.getRegion()
    },
addCls:function(a,b){
    if(a&&a.el){
        a.el.addCls(b)
        }
    },
removeCls:function(a,b){
    if(a&&a.el){
        a.el.removeCls(b)
        }
    },
addGradient:function(d){
    var a=this.gradientsColl||(this.gradientsColl=Ext.create("Ext.util.MixedCollection")),b=[],c=Ext.create("Ext.util.MixedCollection");
    c.addAll(d.stops);
    c.sortByKey("ASC",function(f,e){
        f=parseInt(f,10);
        e=parseInt(e,10);
        return f>e?1:(f<e?-1:0)
        });
    c.eachKey(function(f,e){
        b.push(f+"% "+e.color)
        });
    a.add(d.id,{
        colors:b.join(","),
        angle:d.angle
        })
    },
destroy:function(){
    var a=this;
    a.callParent(arguments);
    if(a.el){
        a.el.remove()
        }
        delete a.el
    }
});
Ext.define("Ext.data.reader.Array",{
    extend:"Ext.data.reader.Json",
    alternateClassName:"Ext.data.ArrayReader",
    alias:"reader.array",
    buildExtractors:function(){
        this.callParent(arguments);
        var a=this.model.prototype.fields.items,c=a.length,d=[],b;
        for(b=0;b<c;b++){
            d.push(function(e){
                return function(f){
                    return f[e]
                    }
                }(a[b].mapping||b))
        }
        this.extractorFunctions=d
    }
});
Ext.define("Ext.resizer.ResizeTracker",{
    extend:"Ext.dd.DragTracker",
    dynamic:true,
    preserveRatio:false,
    constrainTo:null,
    constructor:function(b){
        var d=this;
        if(!b.el){
            if(b.target.isComponent){
                d.el=b.target.getEl()
                }else{
                d.el=b.target
                }
            }
        this.callParent(arguments);
    if(d.preserveRatio&&d.minWidth&&d.minHeight){
        var c=d.minWidth/d.el.getWidth(),a=d.minHeight/d.el.getHeight();
        if(a>c){
            d.minWidth=d.el.getWidth()*a
            }else{
            d.minHeight=d.el.getHeight()*c
            }
        }
    if(d.throttle){
    var e=Ext.Function.createThrottled(function(){
        Ext.resizer.ResizeTracker.prototype.resize.apply(d,arguments)
        },d.throttle);
    d.resize=function(g,h,f){
        if(f){
            Ext.resizer.ResizeTracker.prototype.resize.apply(d,arguments)
            }else{
            e.apply(null,arguments)
            }
        }
}
},
onBeforeStart:function(a){
    this.startBox=this.el.getBox()
    },
getDynamicTarget:function(){
    var a=this.target;
    if(this.dynamic){
        return a
        }else{
        if(!this.proxy){
            this.proxy=a.isComponent?a.getProxy().addCls(Ext.baseCSSPrefix+"resizable-proxy"):a.createProxy({
                tag:"div",
                cls:Ext.baseCSSPrefix+"resizable-proxy",
                id:a.id+"-rzproxy"
                },Ext.getBody());
            this.proxy.removeCls(Ext.baseCSSPrefix+"proxy-el")
            }
        }
    this.proxy.show();
return this.proxy
},
onStart:function(a){
    this.activeResizeHandle=Ext.getCmp(this.getDragTarget().id);
    if(!this.dynamic){
        this.resize(this.startBox,{
            horizontal:"none",
            vertical:"none"
        })
        }
    },
onDrag:function(a){
    if(this.dynamic||this.proxy){
        this.updateDimensions(a)
        }
    },
updateDimensions:function(r,l){
    var s=this,c=s.activeResizeHandle.region,f=s.getOffset(s.constrainTo?"dragTarget":null),j=s.startBox,g,o=0,t=0,i,p,a=0,v=0,u,m=f[0]<0?"right":"left",q=f[1]<0?"down":"up",h,b;
    switch(c){
        case"south":
            t=f[1];
            b=2;
            break;
        case"north":
            t=-f[1];
            v=-t;
            b=2;
            break;
        case"east":
            o=f[0];
            b=1;
            break;
        case"west":
            o=-f[0];
            a=-o;
            b=1;
            break;
        case"northeast":
            t=-f[1];
            v=-t;
            o=f[0];
            h=[j.x,j.y+j.height];
            b=3;
            break;
        case"southeast":
            t=f[1];
            o=f[0];
            h=[j.x,j.y];
            b=3;
            break;
        case"southwest":
            o=-f[0];
            a=-o;
            t=f[1];
            h=[j.x+j.width,j.y];
            b=3;
            break;
        case"northwest":
            t=-f[1];
            v=-t;
            o=-f[0];
            a=-o;
            h=[j.x+j.width,j.y+j.height];
            b=3;
            break
            }
            var d={
        width:j.width+o,
        height:j.height+t,
        x:j.x+a,
        y:j.y+v
        };
        
    i=Ext.Number.snap(d.width,s.widthIncrement);
    p=Ext.Number.snap(d.height,s.heightIncrement);
    if(i!=d.width||p!=d.height){
        switch(c){
            case"northeast":
                d.y-=p-d.height;
                break;
            case"north":
                d.y-=p-d.height;
                break;
            case"southwest":
                d.x-=i-d.width;
                break;
            case"west":
                d.x-=i-d.width;
                break;
            case"northwest":
                d.x-=i-d.width;
                d.y-=p-d.height
                }
                d.width=i;
        d.height=p
        }
        if(d.width<s.minWidth||d.width>s.maxWidth){
        d.width=Ext.Number.constrain(d.width,s.minWidth,s.maxWidth);
        if(a){
            d.x=j.x+(j.width-d.width)
            }
        }else{
    s.lastX=d.x
    }
    if(d.height<s.minHeight||d.height>s.maxHeight){
    d.height=Ext.Number.constrain(d.height,s.minHeight,s.maxHeight);
    if(v){
        d.y=j.y+(j.height-d.height)
        }
    }else{
    s.lastY=d.y
    }
    if(s.preserveRatio||r.shiftKey){
    var n,k;
    g=s.startBox.width/s.startBox.height;
    n=Math.min(Math.max(s.minHeight,d.width/g),s.maxHeight);
    k=Math.min(Math.max(s.minWidth,d.height*g),s.maxWidth);
    if(b==1){
        d.height=n
        }else{
        if(b==2){
            d.width=k
            }else{
            u=Math.abs(h[0]-this.lastXY[0])/Math.abs(h[1]-this.lastXY[1]);
            if(u>g){
                d.height=n
                }else{
                d.width=k
                }
                if(c=="northeast"){
                d.y=j.y-(d.height-j.height)
                }else{
                if(c=="northwest"){
                    d.y=j.y-(d.height-j.height);
                    d.x=j.x-(d.width-j.width)
                    }else{
                    if(c=="southwest"){
                        d.x=j.x-(d.width-j.width)
                        }
                    }
            }
    }
}
}
if(t===0){
    q="none"
    }
    if(o===0){
    m="none"
    }
    s.resize(d,{
    horizontal:m,
    vertical:q
},l)
},
getResizeTarget:function(a){
    return a?this.target:this.getDynamicTarget()
    },
resize:function(b,d,a){
    var c=this.getResizeTarget(a);
    if(c.isComponent){
        if(c.floating){
            c.setPagePosition(b.x,b.y)
            }
            c.setSize(b.width,b.height)
        }else{
        c.setBox(b);
        if(this.originalTarget){
            this.originalTarget.setBox(b)
            }
        }
},
onEnd:function(a){
    this.updateDimensions(a,true);
    if(this.proxy){
        this.proxy.hide()
        }
    }
});
Ext.define("Ext.grid.ColumnLayout",{
    extend:"Ext.layout.container.HBox",
    alias:"layout.gridcolumn",
    type:"column",
    reserveOffset:false,
    clearInnerCtOnLayout:false,
    beforeLayout:function(){
        var g=this,c=0,b=g.getLayoutItems(),a=b.length,f,e,d;
        if(!Ext.isDefined(g.availableSpaceOffset)){
            d=g.owner.up("tablepanel").verticalScroller;
            g.availableSpaceOffset=d?d.width-1:0
            }
            e=g.callParent(arguments);
        g.innerCt.setHeight(23);
        if(g.align=="stretchmax"){
            for(;c<a;c++){
                f=b[c];
                f.el.setStyle({
                    height:"auto"
                });
                f.titleContainer.setStyle({
                    height:"auto",
                    paddingTop:"0"
                });
                if(f.componentLayout&&f.componentLayout.lastComponentSize){
                    f.componentLayout.lastComponentSize.height=f.el.dom.offsetHeight
                    }
                }
            }
        return e
},
calculateChildBoxes:function(k,c){
    var h=this,b=h.callParent(arguments),f=b.boxes,a=b.meta,g=f.length,d=0,e,j;
    if(c.width&&!h.isColumn){
        if(h.owner.forceFit){
            for(;d<g;d++){
                e=f[d];
                j=e.component;
                j.minWidth=Ext.grid.plugin.HeaderResizer.prototype.minColWidth;
                j.flex=e.width
                }
                b=h.callParent(arguments)
            }else{
            if(a.tooNarrow){
                c.width=a.desiredSize
                }
            }
    }
return b
},
afterLayout:function(){
    var d=this,c=0,b=d.getLayoutItems(),a=b.length;
    d.callParent(arguments);
    if(!d.owner.hideHeaders&&d.align=="stretchmax"){
        for(;c<a;c++){
            b[c].setPadding()
            }
        }
    },
updateInnerCtSize:function(b,d){
    var c=this,a;
    if(!c.isColumn){
        c.tooNarrow=d.meta.tooNarrow;
        a=(c.reserveOffset?c.availableSpaceOffset:0);
        if(d.meta.tooNarrow){
            b.width=d.meta.desiredSize+a
            }else{
            b.width+=a
            }
        }
    return c.callParent(arguments)
},
doOwnerCtLayouts:function(){
    var a=this.owner.ownerCt;
    if(!a.componentLayout.layoutBusy){
        a.doComponentLayout()
        }
    }
});
Ext.define("Ext.grid.column.Column",{
    extend:"Ext.grid.header.Container",
    alias:"widget.gridcolumn",
    requires:["Ext.util.KeyNav"],
    alternateClassName:"Ext.grid.Column",
    baseCls:Ext.baseCSSPrefix+"column-header "+Ext.baseCSSPrefix+"unselectable",
    hoverCls:Ext.baseCSSPrefix+"column-header-over",
    handleWidth:5,
    sortState:null,
    possibleSortStates:["ASC","DESC"],
    renderTpl:'<div class="'+Ext.baseCSSPrefix+'column-header-inner"><span class="'+Ext.baseCSSPrefix+'column-header-text">{text}</span><tpl if="!values.menuDisabled"><div class="'+Ext.baseCSSPrefix+'column-header-trigger"></div></tpl></div>',
    dataIndex:null,
    text:"&#160",
    sortable:true,
    hideable:true,
    menuDisabled:false,
    renderer:false,
    align:"left",
    draggable:true,
    initDraggable:Ext.emptyFn,
    isHeader:true,
    initComponent:function(){
        var c=this,b,a;
        if(Ext.isDefined(c.header)){
            c.text=c.header;
            delete c.header
            }
            if(c.flex){
            c.minWidth=c.minWidth||Ext.grid.plugin.HeaderResizer.prototype.minColWidth
            }else{
            c.minWidth=c.width
            }
            if(!c.triStateSort){
            c.possibleSortStates.length=2
            }
            if(Ext.isDefined(c.columns)){
            c.isGroupHeader=true;
            c.items=c.columns;
            delete c.columns;
            delete c.flex;
            c.width=0;
            for(b=0,a=c.items.length;b<a;b++){
                c.width+=c.items[b].width||Ext.grid.header.Container.prototype.defaultWidth
                }
                c.minWidth=c.width;
            c.cls=(c.cls||"")+" "+Ext.baseCSSPrefix+"group-header";
            c.sortable=false;
            c.fixed=true;
            c.align="center"
            }
            Ext.applyIf(c.renderSelectors,{
            titleContainer:"."+Ext.baseCSSPrefix+"column-header-inner",
            triggerEl:"."+Ext.baseCSSPrefix+"column-header-trigger",
            textEl:"."+Ext.baseCSSPrefix+"column-header-text"
            });
        c.callParent(arguments)
        },
    onAdd:function(a){
        a.isSubHeader=true;
        a.addCls(Ext.baseCSSPrefix+"group-sub-header")
        },
    onRemove:function(a){
        a.isSubHeader=false;
        a.removeCls(Ext.baseCSSPrefix+"group-sub-header")
        },
    initRenderData:function(){
        var a=this;
        Ext.applyIf(a.renderData,{
            text:a.text,
            menuDisabled:a.menuDisabled
            });
        return a.callParent(arguments)
        },
    setText:function(a){
        this.text=a;
        if(this.rendered){
            this.textEl.update(a)
            }
        },
getOwnerHeaderCt:function(){
    return this.up(":not([isHeader])")
    },
getIndex:function(){
    return this.isGroupColumn?false:this.getOwnerHeaderCt().getHeaderIndex(this)
    },
afterRender:function(){
    var b=this,a=b.el;
    b.callParent(arguments);
    a.addCls(Ext.baseCSSPrefix+"column-header-align-"+b.align).addClsOnOver(b.overCls);
    b.mon(a,{
        click:b.onElClick,
        dblclick:b.onElDblClick,
        scope:b
    });
    if(!Ext.isIE8||!Ext.isStrict){
        b.mon(b.getFocusEl(),{
            focus:b.onTitleMouseOver,
            blur:b.onTitleMouseOut,
            scope:b
        })
        }
        b.mon(b.titleContainer,{
        mouseenter:b.onTitleMouseOver,
        mouseleave:b.onTitleMouseOut,
        scope:b
    });
    b.keyNav=Ext.create("Ext.util.KeyNav",a,{
        enter:b.onEnterKey,
        down:b.onDownKey,
        scope:b
    })
    },
setSize:function(a,m){
    var j=this,b=j.ownerCt,g=j.getOwnerHeaderCt(),k,h,e,c=j.getWidth(),d=0,n=true,f,l;
    if(a!==c){
        if(b.isGroupHeader){
            k=b.items.items;
            h=k.length;
            for(e=0;e<h;e++){
                l=k[e];
                f=l.hidden;
                if(!l.rendered&&!f){
                    n=false;
                    break
                }
                if(!f){
                    d+=(l===j)?a:l.getWidth()
                    }
                }
            if(n){
            b.minWidth=d;
            b.setWidth(d)
            }
        }
    j.callParent(arguments)
    }
},
afterComponentLayout:function(c,a){
    var d=this,b=this.getOwnerHeaderCt();
    d.callParent(arguments);
    if(c&&!d.isGroupHeader&&b){
        b.onHeaderResize(d,c,true)
        }
    },
setPadding:function(){
    var c=this,a,b=parseInt(c.textEl.getStyle("line-height"),10);
    if(!c.isGroupHeader){
        a=c.el.getViewSize().height;
        if(c.titleContainer.getHeight()<a){
            c.titleContainer.dom.style.height=a+"px"
            }
        }
    a=c.titleContainer.getViewSize().height;
if(b){
    c.titleContainer.setStyle({
        paddingTop:Math.max(((a-b)/2),0)+"px"
        })
    }
    if(Ext.isIE&&c.triggerEl){
    c.triggerEl.setHeight(a)
    }
},
onDestroy:function(){
    var a=this;
    Ext.destroy(a.keyNav);
    delete a.keyNav;
    a.callParent(arguments)
    },
onTitleMouseOver:function(){
    this.titleContainer.addCls(this.hoverCls)
    },
onTitleMouseOut:function(){
    this.titleContainer.removeCls(this.hoverCls)
    },
onDownKey:function(a){
    if(this.triggerEl){
        this.onElClick(a,this.triggerEl.dom||this.el.dom)
        }
    },
onEnterKey:function(a){
    this.onElClick(a,this.el.dom)
    },
onElDblClick:function(d,a){
    var c=this,b=c.ownerCt;
    if(b&&Ext.Array.indexOf(b.items,c)!==0&&c.isOnLeftEdge(d)){
        b.expandToFit(c.previousSibling("gridcolumn"))
        }
    },
onElClick:function(d,b){
    var c=this,a=c.getOwnerHeaderCt();
    if(a&&!a.ddLock){
        if(c.triggerEl&&(d.target===c.triggerEl.dom||b===c.triggerEl.dom||d.within(c.triggerEl))){
            a.onHeaderTriggerClick(c,d,b)
            }else{
            if(d.getKey()||(!c.isOnLeftEdge(d)&&!c.isOnRightEdge(d))){
                c.toggleSortState();
                a.onHeaderClick(c,d,b)
                }
            }
    }
},
processEvent:function(f,b,a,c,d,g){
    return this.fireEvent.apply(this,arguments)
    },
toggleSortState:function(){
    var b=this,a,c;
    if(b.sortable){
        a=Ext.Array.indexOf(b.possibleSortStates,b.sortState);
        c=(a+1)%b.possibleSortStates.length;
        b.setSortState(b.possibleSortStates[c])
        }
    },
doSort:function(b){
    var a=this.up("tablepanel").store;
    a.sort({
        property:this.getSortParam(),
        direction:b
    })
    },
getSortParam:function(){
    return this.dataIndex
    },
setSortState:function(a,j,f){
    var g=this,h=Ext.baseCSSPrefix+"column-header-sort-",i=h+"ASC",c=h+"DESC",b=h+"null",e=g.getOwnerHeaderCt(),d=g.sortState;
    if(d!==a&&g.getSortParam()){
        g.addCls(h+a);
        if(a&&!f){
            g.doSort(a)
            }
            switch(a){
            case"DESC":
                g.removeCls([i,b]);
                break;
            case"ASC":
                g.removeCls([c,b]);
                break;
            case null:
                g.removeCls([i,c]);
                break
                }
                if(e&&!g.triStateSort&&!j){
            e.clearOtherSortStates(g)
            }
            g.sortState=a;
        e.fireEvent("sortchange",e,g,a)
        }
    },
hide:function(){
    var f=this,c,a,d,g,e=0,b=f.getOwnerHeaderCt();
    f.oldWidth=f.getWidth();
    if(f.isGroupHeader){
        c=f.items.items;
        f.callParent(arguments);
        b.onHeaderHide(f);
        for(d=0,a=c.length;d<a;d++){
            c[d].hidden=true;
            b.onHeaderHide(c[d],true)
            }
            return
    }
    g=f.ownerCt.componentLayout.layoutBusy;
    f.ownerCt.componentLayout.layoutBusy=true;
    f.callParent(arguments);
    f.ownerCt.componentLayout.layoutBusy=g;
    b.onHeaderHide(f);
    if(f.ownerCt.isGroupHeader){
        c=f.ownerCt.query(">:not([hidden])");
        if(!c.length){
            f.ownerCt.hide()
            }else{
            for(d=0,a=c.length;d<a;d++){
                e+=c[d].getWidth()
                }
                f.ownerCt.minWidth=e;
            f.ownerCt.setWidth(e)
            }
        }
},
show:function(){
    var f=this,d=f.getOwnerHeaderCt(),g,b,a,c,e=0;
    g=f.ownerCt.componentLayout.layoutBusy;
    f.ownerCt.componentLayout.layoutBusy=true;
    f.callParent(arguments);
    f.ownerCt.componentLayout.layoutBusy=g;
    if(f.isSubHeader){
        if(!f.ownerCt.isVisible()){
            f.ownerCt.show()
            }
        }
    if(f.isGroupHeader&&!f.query(":not([hidden])").length){
    b=f.query(">*");
    for(c=0,a=b.length;c<a;c++){
        b[c].show()
        }
    }
    if(f.ownerCt.isGroupHeader){
    b=f.ownerCt.query(">:not([hidden])");
    for(c=0,a=b.length;c<a;c++){
        e+=b[c].getWidth()
        }
        f.ownerCt.minWidth=e;
    f.ownerCt.setWidth(e)
    }
    if(d){
    d.onHeaderShow(f)
    }
},
getDesiredWidth:function(){
    var a=this;
    if(a.rendered&&a.componentLayout&&a.componentLayout.lastComponentSize){
        return a.componentLayout.lastComponentSize.width
        }else{
        if(a.flex){
            return a.width
            }else{
            return a.width
            }
        }
},
getCellSelector:function(){
    return"."+Ext.baseCSSPrefix+"grid-cell-"+this.getItemId()
    },
getCellInnerSelector:function(){
    return this.getCellSelector()+" ."+Ext.baseCSSPrefix+"grid-cell-inner"
    },
isOnLeftEdge:function(a){
    return(a.getXY()[0]-this.el.getLeft()<=this.handleWidth)
    },
isOnRightEdge:function(a){
    return(this.el.getRight()-a.getXY()[0]<=this.handleWidth)
    }
});
Ext.define("Ext.grid.plugin.HeaderResizer",{
    extend:"Ext.util.Observable",
    requires:["Ext.dd.DragTracker","Ext.util.Region"],
    alias:"plugin.gridheaderresizer",
    disabled:false,
    configs:{
        dynamic:true
    },
    colHeaderCls:Ext.baseCSSPrefix+"column-header",
    minColWidth:40,
    maxColWidth:1000,
    wResizeCursor:"col-resize",
    eResizeCursor:"col-resize",
    init:function(a){
        this.headerCt=a;
        a.on("render",this.afterHeaderRender,this,{
            single:true
        })
        },
    destroy:function(){
        if(this.tracker){
            this.tracker.destroy()
            }
        },
afterHeaderRender:function(){
    var b=this.headerCt,a=b.el;
    b.mon(a,"mousemove",this.onHeaderCtMouseMove,this);
    this.tracker=Ext.create("Ext.dd.DragTracker",{
        disabled:this.disabled,
        onBeforeStart:Ext.Function.bind(this.onBeforeStart,this),
        onStart:Ext.Function.bind(this.onStart,this),
        onDrag:Ext.Function.bind(this.onDrag,this),
        onEnd:Ext.Function.bind(this.onEnd,this),
        tolerance:3,
        autoStart:300,
        el:a
    })
    },
onHeaderCtMouseMove:function(c,a){
    if(this.headerCt.dragging){
        if(this.activeHd){
            this.activeHd.el.dom.style.cursor="";
            delete this.activeHd
            }
        }else{
    var f=c.getTarget("."+this.colHeaderCls,3,true),b,d;
    if(f){
        b=Ext.getCmp(f.id);
        if(b.isOnLeftEdge(c)){
            d=b.previousNode("gridcolumn:not([hidden]):not([isGroupHeader])")
            }else{
            if(b.isOnRightEdge(c)){
                d=b
                }else{
                d=null
                }
            }
        if(d){
        if(d.isGroupHeader){
            d=d.getVisibleGridColumns();
            d=d[d.length-1]
            }
            if(d&&!(d.fixed||this.disabled)){
            this.activeHd=d;
            b.el.dom.style.cursor=this.eResizeCursor
            }
        }else{
    b.el.dom.style.cursor="";
    delete this.activeHd
    }
}
}
},
onBeforeStart:function(b){
    var a=b.getTarget();
    this.dragHd=this.activeHd;
    if(!!this.dragHd&&!Ext.fly(a).hasCls("x-column-header-trigger")&&!this.headerCt.dragging){
        this.tracker.constrainTo=this.getConstrainRegion();
        return true
        }else{
        this.headerCt.dragging=false;
        return false
        }
    },
getConstrainRegion:function(){
    var a=this.dragHd.el,b=Ext.util.Region.getRegion(a);
    return b.adjust(0,this.maxColWidth-a.getWidth(),0,this.minColWidth)
    },
onStart:function(s){
    var u=this,g=u.dragHd,b=g.el,n=b.getWidth(),i=u.headerCt,k=s.getTarget();
    if(u.dragHd&&!Ext.fly(k).hasCls("x-column-header-trigger")){
        i.dragging=true
        }
        u.origWidth=n;
    if(!u.dynamic){
        var d=b.getXY(),q=i.up("[scrollerOwner]"),f=u.dragHd.up(":not([isGroupHeader])"),j=f.up(),c=q.getLhsMarker(),m=q.getRhsMarker(),a=m.parent(),h=a.getLeft(true),r=a.getTop(true),p=a.translatePoints(d),o=j.body.getHeight()+i.getHeight(),l=p.top-r;
        c.setTop(l);
        m.setTop(l);
        c.setHeight(o);
        m.setHeight(o);
        c.setLeft(p.left-h);
        m.setLeft(p.left+n-h)
        }
    },
onDrag:function(g){
    if(!this.dynamic){
        var f=this.tracker.getXY("point"),a=this.headerCt.up("[scrollerOwner]"),h=a.getRhsMarker(),c=h.parent(),b=c.translatePoints(f),d=c.getLeft(true);
        h.setLeft(b.left-d)
        }else{
        this.doResize()
        }
    },
onEnd:function(g){
    this.headerCt.dragging=false;
    if(this.dragHd){
        if(!this.dynamic){
            var f=this.dragHd,c=this.headerCt.up("[scrollerOwner]"),d=c.getLhsMarker(),i=c.getRhsMarker(),a=f.getWidth(),h=this.tracker.getOffset("point"),b=-9999;
            d.setLeft(b);
            i.setLeft(b)
            }
            this.doResize()
        }
    },
doResize:function(){
    if(this.dragHd){
        var b=this.dragHd,a,c=this.tracker.getOffset("point");
        if(b.flex){
            delete b.flex
            }
            if(this.headerCt.forceFit){
            a=b.nextNode("gridcolumn:not([hidden]):not([isGroupHeader])");
            if(a){
                this.headerCt.componentLayout.layoutBusy=true
                }
            }
        b.minWidth=this.origWidth+c[0];
    b.setWidth(b.minWidth);
    if(a){
        delete a.flex;
        a.setWidth(a.getWidth()-c[0]);
        this.headerCt.componentLayout.layoutBusy=false;
        this.headerCt.doComponentLayout()
        }
    }
},
disable:function(){
    this.disabled=true;
    if(this.tracker){
        this.tracker.disable()
        }
    },
enable:function(){
    this.disabled=false;
    if(this.tracker){
        this.tracker.enable()
        }
    }
});
Ext.define("Ext.ShadowPool",{
    singleton:true,
    requires:["Ext.core.DomHelper"],
    markup:function(){
        if(Ext.supports.CSS3BoxShadow){
            return'<div class="'+Ext.baseCSSPrefix+'css-shadow" role="presentation"></div>'
            }else{
            if(Ext.isIE){
                return'<div class="'+Ext.baseCSSPrefix+'ie-shadow" role="presentation"></div>'
                }else{
                return'<div class="'+Ext.baseCSSPrefix+'frame-shadow" role="presentation"><div class="xst" role="presentation"><div class="xstl" role="presentation"></div><div class="xstc" role="presentation"></div><div class="xstr" role="presentation"></div></div><div class="xsc" role="presentation"><div class="xsml" role="presentation"></div><div class="xsmc" role="presentation"></div><div class="xsmr" role="presentation"></div></div><div class="xsb" role="presentation"><div class="xsbl" role="presentation"></div><div class="xsbc" role="presentation"></div><div class="xsbr" role="presentation"></div></div></div>'
                }
            }
    }(),
    shadows:[],
    pull:function(){
    var a=this.shadows.shift();
    if(!a){
        a=Ext.get(Ext.core.DomHelper.insertHtml("beforeBegin",document.body.firstChild,this.markup));
        a.autoBoxAdjust=false
        }
        return a
    },
push:function(a){
    this.shadows.push(a)
    },
reset:function(){
    Ext.Array.each(this.shadows,function(a){
        a.remove()
        });
    this.shadows=[]
    }
});
Ext.define("Ext.dd.DragZone",{
    extend:"Ext.dd.DragSource",
    constructor:function(b,a){
        this.callParent([b,a]);
        if(this.containerScroll){
            Ext.dd.ScrollManager.register(this.el)
            }
        },
getDragData:function(a){
    return Ext.dd.Registry.getHandleFromEvent(a)
    },
onInitDrag:function(a,b){
    this.proxy.update(this.dragData.ddel.cloneNode(true));
    this.onStartDrag(a,b);
    return true
    },
afterRepair:function(){
    var a=this;
    if(Ext.enableFx){
        Ext.fly(a.dragData.ddel).highlight(a.repairHighlightColor)
        }
        a.dragging=false
    },
getRepairXY:function(a){
    return Ext.core.Element.fly(this.dragData.ddel).getXY()
    },
destroy:function(){
    this.callParent();
    if(this.containerScroll){
        Ext.dd.ScrollManager.unregister(this.el)
        }
    }
});
Ext.define("Ext.dd.Registry",{
    singleton:true,
    constructor:function(){
        this.elements={};
        
        this.handles={};
        
        this.autoIdSeed=0
        },
    getId:function(b,a){
        if(typeof b=="string"){
            return b
            }
            var c=b.id;
        if(!c&&a!==false){
            c="extdd-"+(++this.autoIdSeed);
            b.id=c
            }
            return c
        },
    register:function(d,e){
        e=e||{};
        
        if(typeof d=="string"){
            d=document.getElementById(d)
            }
            e.ddel=d;
        this.elements[this.getId(d)]=e;
        if(e.isHandle!==false){
            this.handles[e.ddel.id]=e
            }
            if(e.handles){
            var c=e.handles;
            for(var b=0,a=c.length;b<a;b++){
                this.handles[this.getId(c[b])]=e
                }
            }
        },
unregister:function(d){
    var f=this.getId(d,false);
    var e=this.elements[f];
    if(e){
        delete this.elements[f];
        if(e.handles){
            var c=e.handles;
            for(var b=0,a=c.length;b<a;b++){
                delete this.handles[this.getId(c[b],false)]
            }
            }
        }
},
getHandle:function(a){
    if(typeof a!="string"){
        a=a.id
        }
        return this.handles[a]
    },
getHandleFromEvent:function(b){
    var a=b.getTarget();
    return a?this.handles[a.id]:null
    },
getTarget:function(a){
    if(typeof a!="string"){
        a=a.id
        }
        return this.elements[a]
    },
getTargetFromEvent:function(b){
    var a=b.getTarget();
    return a?this.elements[a.id]||this.handles[a.id]:null
    }
});
Ext.define("Ext.Shadow",{
    requires:["Ext.ShadowPool"],
    constructor:function(b){
        Ext.apply(this,b);
        if(typeof this.mode!="string"){
            this.mode=this.defaultMode
            }
            var d=this.offset,c={
            h:0
        },a=Math.floor(this.offset/2);
        switch(this.mode.toLowerCase()){
            case"drop":
                if(Ext.supports.CSS3BoxShadow){
                c.w=c.h=-d;
                c.l=c.t=d
                }else{
                c.w=0;
                c.l=c.t=d;
                c.t-=1;
                if(Ext.isIE){
                    c.l-=d+a;
                    c.t-=d+a;
                    c.w-=a;
                    c.h-=a;
                    c.t+=1
                    }
                }
            break;
        case"sides":
            if(Ext.supports.CSS3BoxShadow){
            c.h-=d;
            c.t=d;
            c.l=c.w=0
            }else{
            c.w=(d*2);
            c.l=-d;
            c.t=d-1;
            if(Ext.isIE){
                c.l-=(d-a);
                c.t-=d+a;
                c.l+=1;
                c.w-=(d-a)*2;
                c.w-=a+1;
                c.h-=1
                }
            }
        break;
    case"frame":
        if(Ext.supports.CSS3BoxShadow){
        c.l=c.w=c.t=0
        }else{
        c.w=c.h=(d*2);
        c.l=c.t=-d;
        c.t+=1;
        c.h-=2;
        if(Ext.isIE){
            c.l-=(d-a);
            c.t-=(d-a);
            c.l+=1;
            c.w-=(d+a+1);
            c.h-=(d+a);
            c.h+=1
            }
            break
    }
    }
    this.adjusts=c
},
offset:4,
defaultMode:"drop",
show:function(a){
    a=Ext.get(a);
    if(!this.el){
        this.el=Ext.ShadowPool.pull();
        if(this.el.dom.nextSibling!=a.dom){
            this.el.insertBefore(a)
            }
        }
    this.el.setStyle("z-index",this.zIndex||parseInt(a.getStyle("z-index"),10)-1);
if(Ext.isIE&&!Ext.supports.CSS3BoxShadow){
    this.el.dom.style.filter="progid:DXImageTransform.Microsoft.alpha(opacity=50) progid:DXImageTransform.Microsoft.Blur(pixelradius="+(this.offset)+")"
    }
    this.realign(a.getLeft(true),a.getTop(true),a.getWidth(),a.getHeight());
this.el.dom.style.display="block"
},
isVisible:function(){
    return this.el?true:false
    },
realign:function(b,o,k,g){
    if(!this.el){
        return
    }
    var a=this.adjusts,i=this.el.dom,j=i.style,c,f,m,e,h,n;
    j.left=(b+a.l)+"px";
    j.top=(o+a.t)+"px";
    c=Math.max(k+a.w,0);
    f=Math.max(g+a.h,0);
    h=c+"px";
    n=f+"px";
    if(j.width!=h||j.height!=n){
        j.width=h;
        j.height=n;
        if(Ext.supports.CSS3BoxShadow){
            j.boxShadow="0 0 "+this.offset+"px 0 #888"
            }else{
            if(!Ext.isIE){
                m=i.childNodes;
                e=Math.max(0,(c-12))+"px";
                m[0].childNodes[1].style.width=e;
                m[1].childNodes[1].style.width=e;
                m[2].childNodes[1].style.width=e;
                m[1].style.height=Math.max(0,(f-12))+"px"
                }
            }
    }
},
hide:function(){
    if(this.el){
        this.el.dom.style.display="none";
        Ext.ShadowPool.push(this.el);
        delete this.el
        }
    },
setZIndex:function(a){
    this.zIndex=a;
    if(this.el){
        this.el.setStyle("z-index",a)
        }
    }
});
Ext.define("Ext.grid.header.DragZone",{
    extend:"Ext.dd.DragZone",
    colHeaderCls:Ext.baseCSSPrefix+"column-header",
    maxProxyWidth:120,
    constructor:function(a){
        this.headerCt=a;
        this.ddGroup=this.getDDGroup();
        this.callParent([a.el]);
        this.proxy.el.addCls(Ext.baseCSSPrefix+"grid-col-dd")
        },
    getDDGroup:function(){
        return"header-dd-zone-"+this.headerCt.up("[scrollerOwner]").id
        },
    getDragData:function(b){
        var d=b.getTarget("."+this.colHeaderCls),a;
        if(d){
            a=Ext.getCmp(d.id);
            if(!this.headerCt.dragging&&a.draggable&&!(a.isOnLeftEdge(b)||a.isOnRightEdge(b))){
                var c=document.createElement("div");
                c.innerHTML=Ext.getCmp(d.id).text;
                return{
                    ddel:c,
                    header:a
                }
            }
        }
    return false
},
onBeforeDrag:function(){
    return !(this.headerCt.dragging||this.disabled)
    },
onInitDrag:function(){
    this.headerCt.dragging=true;
    this.callParent(arguments)
    },
onDragDrop:function(){
    this.headerCt.dragging=false;
    this.callParent(arguments)
    },
afterRepair:function(){
    this.callParent();
    this.headerCt.dragging=false
    },
getRepairXY:function(){
    return this.dragData.header.el.getXY()
    },
disable:function(){
    this.disabled=true
    },
enable:function(){
    this.disabled=false
    }
});
Ext.define("Ext.dd.DropZone",{
    extend:"Ext.dd.DropTarget",
    requires:["Ext.dd.Registry"],
    getTargetFromEvent:function(a){
        return Ext.dd.Registry.getTargetFromEvent(a)
        },
    onNodeEnter:function(d,a,c,b){},
    onNodeOver:function(d,a,c,b){
        return this.dropAllowed
        },
    onNodeOut:function(d,a,c,b){},
    onNodeDrop:function(d,a,c,b){
        return false
        },
    onContainerOver:function(a,c,b){
        return this.dropNotAllowed
        },
    onContainerDrop:function(a,c,b){
        return false
        },
    notifyEnter:function(a,c,b){
        return this.dropNotAllowed
        },
    notifyOver:function(a,c,b){
        var d=this.getTargetFromEvent(c);
        if(!d){
            if(this.lastOverNode){
                this.onNodeOut(this.lastOverNode,a,c,b);
                this.lastOverNode=null
                }
                return this.onContainerOver(a,c,b)
            }
            if(this.lastOverNode!=d){
            if(this.lastOverNode){
                this.onNodeOut(this.lastOverNode,a,c,b)
                }
                this.onNodeEnter(d,a,c,b);
            this.lastOverNode=d
            }
            return this.onNodeOver(d,a,c,b)
        },
    notifyOut:function(a,c,b){
        if(this.lastOverNode){
            this.onNodeOut(this.lastOverNode,a,c,b);
            this.lastOverNode=null
            }
        },
notifyDrop:function(a,c,b){
    if(this.lastOverNode){
        this.onNodeOut(this.lastOverNode,a,c,b);
        this.lastOverNode=null
        }
        var d=this.getTargetFromEvent(c);
    return d?this.onNodeDrop(d,a,c,b):this.onContainerDrop(a,c,b)
    },
triggerCacheRefresh:function(){
    Ext.dd.DDM.refreshCache(this.groups)
    }
});
Ext.define("Ext.grid.header.DropZone",{
    extend:"Ext.dd.DropZone",
    colHeaderCls:Ext.baseCSSPrefix+"column-header",
    proxyOffsets:[-4,-9],
    constructor:function(a){
        this.headerCt=a;
        this.ddGroup=this.getDDGroup();
        this.callParent([a.el])
        },
    getDDGroup:function(){
        return"header-dd-zone-"+this.headerCt.up("[scrollerOwner]").id
        },
    getTargetFromEvent:function(a){
        return a.getTarget("."+this.colHeaderCls)
        },
    getTopIndicator:function(){
        if(!this.topIndicator){
            this.topIndicator=Ext.core.DomHelper.append(Ext.getBody(),{
                cls:"col-move-top",
                html:"&#160;"
            },true)
            }
            return this.topIndicator
        },
    getBottomIndicator:function(){
        if(!this.bottomIndicator){
            this.bottomIndicator=Ext.core.DomHelper.append(Ext.getBody(),{
                cls:"col-move-bottom",
                html:"&#160;"
            },true)
            }
            return this.bottomIndicator
        },
    getLocation:function(d,b){
        var a=d.getXY()[0],c=Ext.fly(b).getRegion(),g,f;
        if((c.right-a)<=(c.right-c.left)/2){
            g="after"
            }else{
            g="before"
            }
            return{
            pos:g,
            header:Ext.getCmp(b.id),
            node:b
        }
    },
positionIndicator:function(w,o,u){
    var a=this.getLocation(u,o),q=a.header,g=a.pos,f=w.nextSibling("gridcolumn:not([hidden])"),t=w.previousSibling("gridcolumn:not([hidden])"),d,l,r,s,b,c,k,m,x,v;
    if(!q.draggable&&q.getIndex()==0){
        return false
        }
        this.lastLocation=a;
    if((w!==q)&&((g==="before"&&f!==q)||(g==="after"&&t!==q))&&!q.isDescendantOf(w)){
        var n=Ext.dd.DragDropManager.getRelated(this),j=n.length,p=0,h;
        for(;p<j;p++){
            h=n[p];
            if(h!==this&&h.invalidateDrop){
                h.invalidateDrop()
                }
            }
        this.valid=true;
    l=this.getTopIndicator();
    r=this.getBottomIndicator();
    if(g==="before"){
        s="tl";
        b="bl"
        }else{
        s="tr";
        b="br"
        }
        c=q.el.getAnchorXY(s);
    k=q.el.getAnchorXY(b);
    m=this.headerCt.el;
    x=m.getLeft();
    v=m.getRight();
    c[0]=Ext.Number.constrain(c[0],x,v);
    k[0]=Ext.Number.constrain(k[0],x,v);
    c[0]-=4;
    c[1]-=9;
    k[0]-=4;
    l.setXY(c);
    r.setXY(k);
    l.show();
    r.show()
    }else{
    this.invalidateDrop()
    }
},
invalidateDrop:function(){
    this.valid=false;
    this.hideIndicators()
    },
onNodeOver:function(b,a,d,c){
    if(c.header.el.dom!==b){
        this.positionIndicator(c.header,b,d)
        }
        return this.valid?this.dropAllowed:this.dropNotAllowed
    },
hideIndicators:function(){
    this.getTopIndicator().hide();
    this.getBottomIndicator().hide()
    },
onNodeOut:function(){
    this.hideIndicators()
    },
onNodeDrop:function(d,k,j,f){
    if(this.valid){
        this.invalidateDrop();
        var h=f.header,g=this.lastLocation,m=h.ownerCt,a=m.items.indexOf(h),i=g.header.ownerCt,n=i.items.indexOf(g.header),c=this.headerCt,l,b;
        if(g.pos==="after"){
            n++
        }
        if(m!==i&&m.lockableInjected&&i.lockableInjected&&i.lockedCt){
            b=m.up("[scrollerOwner]");
            b.lock(h,n)
            }else{
            if(m!==i&&m.lockableInjected&&i.lockableInjected&&m.lockedCt){
                b=m.up("[scrollerOwner]");
                b.unlock(h,n)
                }else{
                if((m===i)&&(n>m.items.indexOf(h))){
                    n--
                }
                if(m!==i){
                    m.suspendLayout=true;
                    m.remove(h,false);
                    m.suspendLayout=false
                    }
                    if(m.isGroupHeader){
                    if(!m.items.getCount()){
                        l=m.ownerCt;
                        l.suspendLayout=true;
                        l.remove(m,false);
                        m.el.dom.parentNode.removeChild(m.el.dom);
                        l.suspendLayout=false
                        }else{
                        m.minWidth=m.getWidth()-h.getWidth();
                        m.setWidth(m.minWidth)
                        }
                    }
                i.suspendLayout=true;
            if(m===i){
                i.move(a,n)
                }else{
                i.insert(n,h)
                }
                i.suspendLayout=false;
            if(i.isGroupHeader){
                h.savedFlex=h.flex;
                delete h.flex;
                h.width=h.getWidth();
                i.minWidth=i.getWidth()+h.getWidth()-(h.savedFlex?1:0);
                i.setWidth(i.minWidth)
                }else{
                if(h.savedFlex){
                    h.flex=h.savedFlex;
                    delete h.width
                    }
                }
            c.purgeCache();
        c.doLayout();
        c.onHeaderMoved(h,a,n);
        if(!m.items.getCount()){
            m.destroy()
            }
        }
}
}
}
});
Ext.define("Ext.grid.plugin.HeaderReorderer",{
    extend:"Ext.util.Observable",
    requires:["Ext.grid.header.DragZone","Ext.grid.header.DropZone"],
    alias:"plugin.gridheaderreorderer",
    init:function(a){
        this.headerCt=a;
        a.on("render",this.onHeaderCtRender,this)
        },
    destroy:function(){
        Ext.destroy(this.dragZone,this.dropZone)
        },
    onHeaderCtRender:function(){
        this.dragZone=Ext.create("Ext.grid.header.DragZone",this.headerCt);
        this.dropZone=Ext.create("Ext.grid.header.DropZone",this.headerCt);
        if(this.disabled){
            this.dragZone.disable()
            }
        },
enable:function(){
    this.disabled=false;
    if(this.dragZone){
        this.dragZone.enable()
        }
    },
disable:function(){
    this.disabled=true;
    if(this.dragZone){
        this.dragZone.disable()
        }
    }
});
