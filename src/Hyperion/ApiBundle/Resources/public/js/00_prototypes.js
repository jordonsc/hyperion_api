if (!window.console) console = {};
console.log   = console.log   || function(){};
console.warn  = console.warn  || function(){};
console.error = console.error || function(){};
console.info  = console.info  || function(){};

String.prototype.trim = function()      { return this.replace(/^\s+|\s+$/g,""); };
String.prototype.contains = function(x)	{ return this.indexOf(x) != -1; };
Math.roundTo = function(x, precision)    { return Math.round(x * Math.pow(10, precision)) / Math.pow(10, precision); };
