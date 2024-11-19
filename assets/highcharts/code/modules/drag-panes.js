/*
 Highcharts JS v7.1.2 (2019-06-03)

 Drag-panes module

 (c) 2010-2019 Highsoft AS
 Author: Kacper Madej

 License: www.highcharts.com/license
*/
(function(a){"object"===typeof module&&module.exports?(a["default"]=a,module.exports=a):"function"===typeof define&&define.amd?define("highcharts/modules/drag-panes",["highcharts","highcharts/modules/stock"],function(c){a(c);a.Highcharts=c;return a}):a("undefined"!==typeof Highcharts?Highcharts:void 0)})(function(a){function c(m,a,c,t){m.hasOwnProperty(a)||(m[a]=t.apply(null,c))}a=a?a._modules:{};c(a,"modules/drag-panes.src.js",[a["parts/Globals.js"]],function(a){var c=a.hasTouch,m=a.merge,t=a.wrap,
y=a.isNumber,f=a.addEvent,w=a.relativeLength,z=a.objectEach,u=a.Axis,x=a.Pointer;m(!0,u.prototype.defaultYAxisOptions,{minLength:"10%",maxLength:"100%",resize:{controlledAxis:{next:[],prev:[]},enabled:!1,cursor:"ns-resize",lineColor:"#cccccc",lineDashStyle:"Solid",lineWidth:4,x:0,y:0}});a.AxisResizer=function(b){this.init(b)};a.AxisResizer.prototype={init:function(b,a){this.axis=b;this.options=b.options.resize;this.render();a||this.addMouseEvents()},render:function(){var b=this.axis,a=b.chart,d=this.options,
h=d.x,c=d.y,k=Math.min(Math.max(b.top+b.height+c,a.plotTop),a.plotTop+a.plotHeight),l={};a.styledMode||(l={cursor:d.cursor,stroke:d.lineColor,"stroke-width":d.lineWidth,dashstyle:d.lineDashStyle});this.lastPos=k-c;this.controlLine||(this.controlLine=a.renderer.path().addClass("highcharts-axis-resizer"));this.controlLine.add(b.axisGroup);d=a.styledMode?this.controlLine.strokeWidth():d.lineWidth;l.d=a.renderer.crispLine(["M",b.left+h,k,"L",b.left+b.width+h,k],d);this.controlLine.attr(l)},addMouseEvents:function(){var b=
this,a=b.controlLine.element,d=b.axis.chart.container,h=[],v,k,l;b.mouseMoveHandler=v=function(a){b.onMouseMove(a)};b.mouseUpHandler=k=function(a){b.onMouseUp(a)};b.mouseDownHandler=l=function(a){b.onMouseDown(a)};h.push(f(d,"mousemove",v),f(d.ownerDocument,"mouseup",k),f(a,"mousedown",l));c&&h.push(f(d,"touchmove",v),f(d.ownerDocument,"touchend",k),f(a,"touchstart",l));b.eventsToUnbind=h},onMouseMove:function(b){b.touches&&0===b.touches[0].pageX||!this.grabbed||(this.hasDragged=!0,this.updateAxes(this.axis.chart.pointer.normalize(b).chartY-
this.options.y))},onMouseUp:function(b){this.hasDragged&&this.updateAxes(this.axis.chart.pointer.normalize(b).chartY-this.options.y);this.grabbed=this.hasDragged=this.axis.chart.activeResizer=null},onMouseDown:function(){this.axis.chart.pointer.reset(!1,0);this.grabbed=this.axis.chart.activeResizer=!0},updateAxes:function(b){var a=this,d=a.axis.chart,c=a.options.controlledAxis,f=0===c.next.length?[d.yAxis.indexOf(a.axis)+1]:c.next,c=[a.axis].concat(c.prev),k=[],l=!1,r=d.plotTop,p=d.plotHeight,m=r+
p,q;b=Math.max(Math.min(b,m),r);q=b-a.lastPos;1>q*q||([c,f].forEach(function(c,f){c.forEach(function(c,g){var e=(c=y(c)?d.yAxis[c]:f||g?d.get(c):c)&&c.options,n,h;e&&"navigator-y-axis"!==e.id&&(g=c.top,h=Math.round(w(e.minLength,p)),n=Math.round(w(e.maxLength,p)),f?(q=b-a.lastPos,e=Math.round(Math.min(Math.max(c.len-q,h),n)),g=c.top+q,g+e>m&&(n=m-e-g,b+=n,g+=n),g<r&&(g=r,g+e>m&&(e=p)),e===h&&(l=!0),k.push({axis:c,options:{top:100*(g-r)/p+"%",height:100*e/p+"%"}})):(e=Math.round(Math.min(Math.max(b-
g,h),n)),e===n&&(l=!0),b=g+e,k.push({axis:c,options:{height:100*e/p+"%"}})))})}),l||(k.forEach(function(a){a.axis.update(a.options,!1)}),d.redraw(!1)))},destroy:function(){var a=this;delete a.axis.resizer;this.eventsToUnbind&&this.eventsToUnbind.forEach(function(a){a()});a.controlLine.destroy();z(a,function(b,c){a[c]=null})}};u.prototype.keepProps.push("resizer");f(u,"afterRender",function(){var b=this.resizer,c=this.options.resize;c&&(c=!1!==c.enabled,b?c?b.init(this,!0):b.destroy():c&&(this.resizer=
new a.AxisResizer(this)))});f(u,"destroy",function(a){!a.keepEvents&&this.resizer&&this.resizer.destroy()});t(x.prototype,"runPointActions",function(a){this.chart.activeResizer||a.apply(this,Array.prototype.slice.call(arguments,1))});t(x.prototype,"drag",function(a){this.chart.activeResizer||a.apply(this,Array.prototype.slice.call(arguments,1))})});c(a,"masters/modules/drag-panes.src.js",[],function(){})});
//# sourceMappingURL=drag-panes.js.map
