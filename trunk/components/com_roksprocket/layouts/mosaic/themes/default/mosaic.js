/*
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2012 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */
((function(){if(typeof this.RokSprocket=="undefined"){this.RokSprocket={};}else{Object.merge(this.RokSprocket,{Mosaic:null,MosaicBuilder:null});}var a=new Class({Implements:[Options,Events],options:{settings:{}},initialize:function(d){this.setOptions(d);
this.mosaics=document.getElements("[data-mosaic]");this.mosaic={};this.settings={};try{RokMediaQueries.on("every",this.mediaQuery.bind(this));}catch(c){if(typeof console!="undefined"){console.error('Error while trying to add a RokMediaQuery "match" event',c);
}}},attach:function(c,d){c=typeOf(c)=="number"?document.getElements("[data-mosaic="+this.getID(c)+"]"):c;d=typeOf(d)=="string"?JSON.decode(d):d;var e=(c?new Elements([c]).flatten():this.mosaics);
e.each(function(f){f.store("roksprocket:mosaic:attached",true);this.setSettings(f,d,"restore");var g={loadmore:f.retrieve("roksprocket:mosaic:loadmore",function(h,i){if(h){h.preventDefault();
}this.loadMore.call(this,h,f,i);}.bind(this)),ordering:f.retrieve("roksprocket:mosaic:ordering",function(i,h){this.orderBy.call(this,i,f,h);}.bind(this)),filtering:f.retrieve("roksprocket:mosaic:filtering",function(i,h){this.filterBy.call(this,i,f,h);
}.bind(this)),document:document.retrieve("roksprocket:mosaic:document",function(i,h){this.toggleShift.call(this,i,f,h);}.bind(this))};f.addEvent("click:relay([data-mosaic-loadmore])",g.loadmore);
f.addEvent("click:relay([data-mosaic-orderby])",g.ordering);f.addEvent("click:relay([data-mosaic-filterby])",g.filtering);f.retrieve("roksprocket:mosaic:ajax",new RokSprocket.Request({model:"mosaic",action:"getPage",onRequest:this.onRequest.bind(this,f),onSuccess:function(h){this.onSuccess(h,f,f.retrieve("roksprocket:mosaic:ajax"));
}.bind(this)}));document.addEvents({"keydown:keys(shift)":g.document,"keyup:keys(shift)":g.document});this.initializeMosaic(f,function(){this.mediaQuery.delay(5,this,RokMediaQueries.getQuery());
}.bind(this));},this);},detach:function(c){c=typeOf(c)=="number"?document.getElements("[data-mosaic="+this.getID(c)+"]"):c;var d=(c?new Elements([c]).flatten():this.mosaics);
d.each(function(e){e.store("roksprocket:mosaic:attached",false);var f={loadmore:e.retrieve("roksprocket:mosaic:loadmore"),ordering:e.retrieve("roksprocket:mosaic:ordering"),filtering:e.retrieve("roksprocket:mosaic:filtering"),document:document.retrieve("roksprocket:mosaic:document")};
e.removeEvent("click:relay([data-mosaic-loadmore])",f.loadmore);e.removeEvent("click:relay([data-mosaic-orderby])",f.ordering);e.removeEvent("click:relay([data-mosaic-filterby])",f.filtering);
document.removeEvents({"keydown:keys(shift)":f.document,"keyup:keys(shift)":f.document});},this);},mediaQuery:function(e){var f,d,c;for(var g in this.mosaic){c=this.mosaic[g];
c.resize("fast");}},setSettings:function(c,f,e){var g=this.getID(c),d=Object.clone(this.getSettings(c)||this.options.settings);if(!e||!this.settings["id-"+g]){this.settings["id-"+g]=Object.merge(d,f||d);
}},getSettings:function(c){var d=this.getID(c);return this.settings["id-"+d];},getContainer:function(c){if(!c){c=document.getElements("[data-mosaic]");
}if(typeOf(c)=="number"){c=document.getElement("[data-mosaic="+c+"]");}if(typeOf(c)=="string"){c=document.getElement(c);}return c;},getID:function(c){if(typeOf(c)=="number"){c=document.getElement("[data-mosaic="+c+"]");
}if(typeOf(c)=="string"){c=document.getElement(c);}return c.get("data-mosaic");},loadMore:function(d,c,f){c=this.getContainer(c);f=(typeOf(f)=="number")?f:this.getSettings(c).page||1;
if(!c.retrieve("roksprocket:mosaic:attached")){return;}var e=c.retrieve("roksprocket:mosaic:ajax"),h=c.getElement("[data-mosaic-filterby].active"),g={moduleid:c.get("data-mosaic"),behavior:!f?"reset":"append",filter:h?h.get("data-mosaic-filterby")||"all":"all",page:++f};
if(d&&d.shift){g.all=true;}if(!e.isRunning()){e.cancel().setParams(g).send();}},filterBy:function(e,c,d){c.getElements("[data-mosaic-filterby]").removeClass("active");
d.addClass("active");c.addClass("refreshing");this.loadMore(e,c,0);},nextAll:function(d,c){d=this.getContainer(d);if(typeOf(d)=="element"){return this.next(d,c);
}d.each(function(e){this.next(e,c);},this);},toggleShift:function(g,d,e){var f=g.type||"keyup",c=d.getElements("[data-mosaic-loadmore]");if(!c.length){return true;
}if(f=="keydown"){c.addClass("load-all");}else{c.removeClass("load-all");}},onRequest:function(d){var c=d.getElements("[data-mosaic-loadmore]");if(c){c.addClass("loader");
}this.detach(d);},onSuccess:function(k,e){var f="id-"+this.getID(e),p=e.retrieve("roksprocket:mosaic:ajax"),o=e.getElement("[data-mosaic-items]"),l=k.getPath("payload.html"),m=k.getPath("payload.page"),n=k.getPath("payload.more"),d=k.getPath("payload.behavior"),i=this.getSettings(e),r=i.animations,h;
this.setSettings(e,{page:(d=="reset"?1:m)});e.removeClass("refreshing");var g=new Element("div",{html:l}),c=g.getChildren(),q={};q=this.getAnimation(e,"_set").style;
moofx(c).style(q);o.adopt(c);h=new Elements(c.getElements("img").flatten());this._loadImages(h.get("src"),function(){if(d=="reset"){this.mosaic[f].bricks.each(function(t,s){(function(){q=this.getAnimation(e,"_out");
moofx(t).style(q.style);moofx(t).animate(q.animate,{curve:"cubic-bezier(0.37,0.61,0.59,0.87)",duration:"250ms",callback:function(){t.dispose();}});}).delay(s*50,this);
},this);}this.mosaic[f][d](c,function(){loadmore=e.getElements("[data-mosaic-loadmore]");if(loadmore){loadmore.removeClass("loader");}c=this.mosaic[f].bricks.filter(function(s){return c.contains(s);
});c.each(function(t,s){(function(){q=this.getAnimation(e,"_in");moofx(t).animate(q.animate,{curve:"cubic-bezier(0.37,0.61,0.59,0.87)",duration:"300ms"});
}).delay(s*100,this);},this);this.attach(e);e.getElements("[data-mosaic-loadmore]").removeClass("load-all")[!n?"addClass":"removeClass"]("hide");}.bind(this));
}.bind(this));},getAnimation:function(c,h){var e=this.getSettings(c),d=e.animations||null,f={},g={_set:{style:{opacity:0},animate:{}},_out:{style:{opacity:1},animate:{opacity:0}},_in:{style:{},animate:{opacity:1}}};
d=d?d.erase("fade"):null;if(d&&d.contains("flip")){d=d.erase("scale").erase("rotate");}switch(d?d.join(","):null){case"scale":g._set["style"]=Object.merge(g._set["style"],{transform:"scale(0.5)"});
g._out["style"]=Object.merge(g._out["style"],{"transform-origin":"50% 50%"});g._out["animate"]=Object.merge(g._out["animate"],{transform:Browser.ie9?"scale(0.001)":"scale(0)"});
g._in["animate"]=Object.merge(g._in["animate"],{transform:Browser.ie9?"matrix(1, 0, 0, 1, 0, 0)":"scale(1)"});break;case"rotate":g._set["style"]=Object.merge(g._set["style"],{"transform-origin":"0 0",transform:"rotate(-10deg)"});
g._out["style"]=Object.merge(g._out["style"],{"transform-origin":"0 0"});g._out["animate"]=Object.merge(g._out["animate"],{transform:"rotate(10deg)"});
g._in["animate"]=Object.merge(g._in["animate"],{transform:"rotate(0)"});break;case"rotate,scale":case"scale,rotate":g._set["style"]=Object.merge(g._set["style"],{"transform-origin":"0 0",transform:"scale(0.5) rotate(-10deg)"});
g._out["style"]=Object.merge(g._out["style"],{"transform-origin":"50% 50%"});g._out["animate"]=Object.merge(g._out["animate"],{transform:Browser.ie9?"scale(0.001) rotate(10deg)":"scale(0) rotate(10deg)"});
g._in["animate"]=Object.merge(g._in["animate"],{transform:Browser.ie9?"matrix(1, 0, 0, 1, 0, 0)":"scale(1) rotate(0)"});break;case"flip":g._set["style"]=Object.merge(g._set["style"],{"transform-origin":"50% 50%",transform:"scale(0.5) rotateY(360deg)"});
g._out["style"]=Object.merge(g._out["style"],{"transform-origin":"50% 50%"});g._out["animate"]=Object.merge(g._out["animate"],{transform:Browser.ie9?"scale(0.0001) rotateY(360deg)":"scale(0.5) rotateY(360deg)"});
g._in["animate"]=Object.merge(g._in["animate"],{transform:"scale(1) rotateY(0)"});break;default:}return g[h];},orderBy:function(e,c,d){var g="id-"+this.getID(c);
if(!this.mosaic||!this.mosaic[g]){throw new Error("RokSprocket Mosaic: Mosaic class not available");}var f=d.get("data-mosaic-orderby");this.mosaic[g].order(f);
c.getElements("[data-mosaic-orderby]").removeClass("active");if(f!="random"){d.addClass("active");}},initializeMosaic:function(d,k){var i="id-"+this.getID(d),c;
if(this.mosaic&&this.mosaic[i]){if(typeof k=="function"){k.call(this.mosaic[i].bricks);}c=d.getElements("[data-mosaic-loadmore]");if(c){c.removeClass("loader");
}return this.mosaic[i];}var h=d.getElements("img"),f=d.getElement("[data-mosaic-items]"),g=d.getElement(".active[data-mosaic-orderby]"),e={container:d,animated:true,gutter:0,order:g?g.get("data-mosaic-orderby"):(d.getElements("[data-mosaic-orderby]").length?"random":"default")};
if(k&&typeof k=="function"){e.callback=k;}moofx(f).style({"transform-style":"preserve-3d","backface-visibility":"hidden",opacity:1});moofx(f.getElements("[data-mosaic-item]")).style(this.getAnimation(d,"_in").animate);
if(!h.length){c=d.getElements("[data-mosaic-loadmore]");if(c){c.removeClass("loader");}this.mosaic[i]=new RokSprocket.MosaicBuilder(f,e);}else{this._loadImages(h.get("src"),function(){c=d.getElements("[data-mosaic-loadmore]");
if(c){c.removeClass("loader");}this.mosaic[i]=new RokSprocket.MosaicBuilder(f,e);}.bind(this));}return this.mosaic[i];},_loadImages:function(c,d){return c.length?new Asset.images(c,{onComplete:d.bind(this)}):d.bind(this)();
}});var b=new Class({Implements:[Options,Events],options:{container:null,resizeable:false,animated:false,gutter:0,fitwidth:false,order:"default",containerStyle:{position:"relative"}},initialize:function(d,c){this.setOptions(c);
this.element=document.id(d)||document.getElement(d)||null;if(!this.element){throw new Error('Mosaic Builder Error: Element "'+d+'" not found in the DOM.');
}this.styleQueue=[];this.originalState=this.getBricks();this.build();this.init(c.callback||null);},build:function(){var c=this.element.style;this.originalStyle={height:c.height||""};
Object.each(this.options.containerStyle,function(d,e){this.originalStyle[e]=c[e]||"";},this);moofx(this.element).style(this.originalStyle);this.offset={x:this.element.getStyle("padding-left").toInt(),y:this.element.getStyle("padding-top").toInt()};
this.isFluid=this.options.columnWidth&&typeof this.options.columnWidth==="function";this.reloadItems(this.options.order||null);},init:function(c){this.getColumns();
this.reLayout(c);},getBricks:function(c){return(c?c:this.element.getElements("[data-mosaic-item]")).setStyle("position","absolute");},reloadItems:function(c,d){this.bricks=this.getBricks(d);
if(c=="random"||c=="default"){if(c=="random"){this.bricks=this.bricks.shuffle();}if(c=="default"){this.bricks=this.originalState.clone();}return this.bricks;
}this.bricks=c?this.orderBy(c):this.bricks;return this.bricks;},orderBy:function(c){var d=false;return this.bricks.sort(function(g,f){var e=g.getElement("[data-mosaic-order-"+c+"]"),h=f.getElement("[data-mosaic-order-"+c+"]");
if(!e||!h){if(console&&console.error&&!d){console.error('RokSprocket MosaicBuilder: Trying to sort by "'+c+'" but no sorting rule has been found.');}d=true;
return 0;}e=e.get("data-mosaic-order-"+c);h=h.get("data-mosaic-order-"+c);return e==h?0:(e<h?-1:1);}.bind(this));},reload:function(c){this.reloadItems();
this.init(c);},layout:function(d,m,k){for(var h=0,l=d.length;h<l;h++){this.placeBrick(d[h]);}var c={},n={};c.height=Math.max.apply(Math,this.colYs);if(this.options.fitwidth){var f=0;
h=this.cols;while(--h){if(this.colYs[h]!==0){break;}f++;}c.width=(this.cols-f)*this.columnWidth-this.options.gutter;}this.styleQueue.push({element:this.element,style:c});
var e=!this.isLaidOut?"style":(this.options.animated&&!k?"animate":"style"),g;this.styleQueue.each(function(p,o){n={curve:"cubic-bezier(0.37,0.61,0.59,0.87)",duration:"400ms"};
if(o==this.styleQueue.length-1){if(m){n.callback=m.bind(m,d);}}moofx(p.element)[e](p.style,n);},this);this.styleQueue.empty();if(m&&e=="style"){m.call(d);
}this.isLaidOut=true;},getColumns:function(){var c=this.options.fitwidth?this.element.getParent():this.element,d=c.offsetWidth;this.columnWidth=this.isFluid?this.options.columnWidth(d):this.options.columnWidth||(this.bricks.length&&this.bricks[0].offsetWidth)||d;
this.columnWidth+=this.options.gutter;this.cols=Math.ceil((d+this.options.gutter)/this.columnWidth);this.cols=Math.max(this.cols,1);},placeBrick:function(h){h=document.id(h);
var l,p,e,n;l=Math.ceil(h.offsetWidth/(this.columnWidth+this.options.gutter));l=Math.min(l,this.cols);if(l==1){e=this.colYs;}else{p=this.cols+1-l;e=[];
(p).times(function(q){n=this.colYs.slice(j,j+l);e[j]=Math.max.apply(Math,n);},this);}var c=Math.min.apply(Math,e),o=0;for(var f=0,k=e.length;f<k;f++){if(e[f]===c){o=f;
break;}}var g={top:c+this.offset.y};g.left=o*(100/this.cols)+"%";this.styleQueue.push({element:h,style:g});var m=c+h.offsetHeight+((this.options.gutter||0)),d=this.cols+1-e.length;
(d).times(function(q){this.colYs[o+q]=m;},this);},resize:function(c){var d=this.cols;this.getColumns();if((this.isFluid||c)&&this.cols!==d||c){this.reLayout(null,c);
}},reLayout:function(e,c){var d=this.cols;this.colYs=[];while(d--){this.colYs.push(0);}this.layout(this.bricks,e,c);},reset:function(c,d){c=c.filter(function(e){return e.get("data-mosaic-item")!==null||e.getElement("data-mosaic-item");
});this.bricks=this.originalState=new Elements();c.setStyles({top:0,left:0,position:"absolute"});this.appendedBricks.delay(1,this,[c,c,d]);},append:function(c,d){c=c.filter(function(e){return e.get("data-mosaic-item")!==null||e.getElement("data-mosaic-item");
});if(!c){return;}c.setStyles({top:this.element.getSize().y,left:0,position:"absolute"});this.appendedBricks.delay(1,this,[c,null,d]);},appendedBricks:function(e,d,g){var c=this.options.container.getElement("[data-mosaic-orderby].active")||this.options.container.getElement("[data-mosaic-orderby=random]"),f=c?c.get("data-mosaic-orderby"):(this.options.container.getElements("[data-mosaic-orderby]").length?"random":"default");
this.originalState.append(e);this.order(f,d,g);},order:function(d,c,e){this.reloadItems(d,c||null);this.init(e);}});this.RokSprocket.Mosaic=a;this.RokSprocket.MosaicBuilder=b;
Element.implement({mosaic:function(d){var c=this.retrieve("roksprocket:mosaic:builder");if(!c){c=this.store("roksprocket:mosaic:builder",new RokSprocket.MosaicBuilder(this,d));
}return c;}});if(MooTools.version<"1.4.4"&&(Browser.name=="ie"&&Browser.version<9)){((function(){var c=["rel","data-next","data-mosaic","data-mosaic-items","data-mosaic-item","data-mosaic-content","data-mosaic-page","data-mosaic-next","data-mosaic-order","data-mosaic-orderby","data-mosaic-order-title","data-mosaic-order-date","data-mosaic-filterby","data-mosaic-loadmore"];
c.each(function(d){Element.Properties[d]={get:function(){return this.getAttribute(d);}};});})());}})());