(function e(t,n,r){function s(o,u){if(!n[o]){if(!t[o]){var a=typeof require=="function"&&require;if(!u&&a)return a(o,!0);if(i)return i(o,!0);throw new Error("Cannot find module '"+o+"'")}var f=n[o]={exports:{}};t[o][0].call(f.exports,function(e){var n=t[o][1][e];return s(n?n:e)},f,f.exports,e,t,n,r)}return n[o].exports}var i=typeof require=="function"&&require;for(var o=0;o<r.length;o++)s(r[o]);return s})({1:[function(require,module,exports){
(function (global){
'use strict';
var Mutation = global.MutationObserver || global.WebKitMutationObserver;

var scheduleDrain;

{
  if (Mutation) {
    var called = 0;
    var observer = new Mutation(nextTick);
    var element = global.document.createTextNode('');
    observer.observe(element, {
      characterData: true
    });
    scheduleDrain = function () {
      element.data = (called = ++called % 2);
    };
  } else if (!global.setImmediate && typeof global.MessageChannel !== 'undefined') {
    var channel = new global.MessageChannel();
    channel.port1.onmessage = nextTick;
    scheduleDrain = function () {
      channel.port2.postMessage(0);
    };
  } else if ('document' in global && 'onreadystatechange' in global.document.createElement('script')) {
    scheduleDrain = function () {

      // Create a <script> element; its readystatechange event will be fired asynchronously once it is inserted
      // into the document. Do so, thus queuing up the task. Remember to clean up once it's been called.
      var scriptEl = global.document.createElement('script');
      scriptEl.onreadystatechange = function () {
        nextTick();

        scriptEl.onreadystatechange = null;
        scriptEl.parentNode.removeChild(scriptEl);
        scriptEl = null;
      };
      global.document.documentElement.appendChild(scriptEl);
    };
  } else {
    scheduleDrain = function () {
      setTimeout(nextTick, 0);
    };
  }
}

var draining;
var queue = [];
//named nextTick for less confusing stack traces
function nextTick() {
  draining = true;
  var i, oldQueue;
  var len = queue.length;
  while (len) {
    oldQueue = queue;
    queue = [];
    i = -1;
    while (++i < len) {
      oldQueue[i]();
    }
    len = queue.length;
  }
  draining = false;
}

module.exports = immediate;
function immediate(task) {
  if (queue.push(task) === 1 && !draining) {
    scheduleDrain();
  }
}

}).call(this,typeof self !== "undefined" ? self : typeof window !== "undefined" ? window : {})
},{}],2:[function(require,module,exports){
'use strict';
var immediate = require('immediate');

/* istanbul ignore next */
function INTERNAL() {}

var handlers = {};

var REJECTED = ['REJECTED'];
var FULFILLED = ['FULFILLED'];
var PENDING = ['PENDING'];

module.exports = Promise;

function Promise(resolver) {
  if (typeof resolver !== 'function') {
    throw new TypeError('resolver must be a function');
  }
  this.state = PENDING;
  this.queue = [];
  this.outcome = void 0;
  if (resolver !== INTERNAL) {
    safelyResolveThenable(this, resolver);
  }
}

Promise.prototype["catch"] = function (onRejected) {
  return this.then(null, onRejected);
};
Promise.prototype.then = function (onFulfilled, onRejected) {
  if (typeof onFulfilled !== 'function' && this.state === FULFILLED ||
    typeof onRejected !== 'function' && this.state === REJECTED) {
    return this;
  }
  var promise = new this.constructor(INTERNAL);
  if (this.state !== PENDING) {
    var resolver = this.state === FULFILLED ? onFulfilled : onRejected;
    unwrap(promise, resolver, this.outcome);
  } else {
    this.queue.push(new QueueItem(promise, onFulfilled, onRejected));
  }

  return promise;
};
function QueueItem(promise, onFulfilled, onRejected) {
  this.promise = promise;
  if (typeof onFulfilled === 'function') {
    this.onFulfilled = onFulfilled;
    this.callFulfilled = this.otherCallFulfilled;
  }
  if (typeof onRejected === 'function') {
    this.onRejected = onRejected;
    this.callRejected = this.otherCallRejected;
  }
}
QueueItem.prototype.callFulfilled = function (value) {
  handlers.resolve(this.promise, value);
};
QueueItem.prototype.otherCallFulfilled = function (value) {
  unwrap(this.promise, this.onFulfilled, value);
};
QueueItem.prototype.callRejected = function (value) {
  handlers.reject(this.promise, value);
};
QueueItem.prototype.otherCallRejected = function (value) {
  unwrap(this.promise, this.onRejected, value);
};

function unwrap(promise, func, value) {
  immediate(function () {
    var returnValue;
    try {
      returnValue = func(value);
    } catch (e) {
      return handlers.reject(promise, e);
    }
    if (returnValue === promise) {
      handlers.reject(promise, new TypeError('Cannot resolve promise with itself'));
    } else {
      handlers.resolve(promise, returnValue);
    }
  });
}

handlers.resolve = function (self, value) {
  var result = tryCatch(getThen, value);
  if (result.status === 'error') {
    return handlers.reject(self, result.value);
  }
  var thenable = result.value;

  if (thenable) {
    safelyResolveThenable(self, thenable);
  } else {
    self.state = FULFILLED;
    self.outcome = value;
    var i = -1;
    var len = self.queue.length;
    while (++i < len) {
      self.queue[i].callFulfilled(value);
    }
  }
  return self;
};
handlers.reject = function (self, error) {
  self.state = REJECTED;
  self.outcome = error;
  var i = -1;
  var len = self.queue.length;
  while (++i < len) {
    self.queue[i].callRejected(error);
  }
  return self;
};

function getThen(obj) {
  // Make sure we only access the accessor once as required by the spec
  var then = obj && obj.then;
  if (obj && (typeof obj === 'object' || typeof obj === 'function') && typeof then === 'function') {
    return function appyThen() {
      then.apply(obj, arguments);
    };
  }
}

function safelyResolveThenable(self, thenable) {
  // Either fulfill, reject or reject with error
  var called = false;
  function onError(value) {
    if (called) {
      return;
    }
    called = true;
    handlers.reject(self, value);
  }

  function onSuccess(value) {
    if (called) {
      return;
    }
    called = true;
    handlers.resolve(self, value);
  }

  function tryToUnwrap() {
    thenable(onSuccess, onError);
  }

  var result = tryCatch(tryToUnwrap);
  if (result.status === 'error') {
    onError(result.value);
  }
}

function tryCatch(func, value) {
  var out = {};
  try {
    out.value = func(value);
    out.status = 'success';
  } catch (e) {
    out.status = 'error';
    out.value = e;
  }
  return out;
}

Promise.resolve = resolve;
function resolve(value) {
  if (value instanceof this) {
    return value;
  }
  return handlers.resolve(new this(INTERNAL), value);
}

Promise.reject = reject;
function reject(reason) {
  var promise = new this(INTERNAL);
  return handlers.reject(promise, reason);
}

Promise.all = all;
function all(iterable) {
  var self = this;
  if (Object.prototype.toString.call(iterable) !== '[object Array]') {
    return this.reject(new TypeError('must be an array'));
  }

  var len = iterable.length;
  var called = false;
  if (!len) {
    return this.resolve([]);
  }

  var values = new Array(len);
  var resolved = 0;
  var i = -1;
  var promise = new this(INTERNAL);

  while (++i < len) {
    allResolver(iterable[i], i);
  }
  return promise;
  function allResolver(value, i) {
    self.resolve(value).then(resolveFromAll, function (error) {
      if (!called) {
        called = true;
        handlers.reject(promise, error);
      }
    });
    function resolveFromAll(outValue) {
      values[i] = outValue;
      if (++resolved === len && !called) {
        called = true;
        handlers.resolve(promise, values);
      }
    }
  }
}

Promise.race = race;
function race(iterable) {
  var self = this;
  if (Object.prototype.toString.call(iterable) !== '[object Array]') {
    return this.reject(new TypeError('must be an array'));
  }

  var len = iterable.length;
  var called = false;
  if (!len) {
    return this.resolve([]);
  }

  var i = -1;
  var promise = new this(INTERNAL);

  while (++i < len) {
    resolver(iterable[i]);
  }
  return promise;
  function resolver(value) {
    self.resolve(value).then(function (response) {
      if (!called) {
        called = true;
        handlers.resolve(promise, response);
      }
    }, function (error) {
      if (!called) {
        called = true;
        handlers.reject(promise, error);
      }
    });
  }
}

},{"immediate":1}],3:[function(require,module,exports){
'use strict';
var XMLClient=require("./Client");
var BridgeClient=require("./BridgeClient");
var PseudoClient=require("./PseudoClient");
var VideorollClient=require("./VideorollClient");
var sendStatistic=require("./Statistic");
var isMobile=require("./util/UTILS").isMobile;
var repareUrl=require("./util/UTILS").repareUrl;
function BlockDisptcher(slot) {
this.ads_last=[];
this.calcControlCnt=110;
this.noTrailer=0;
this.Pid=0;
this.repeat=0;
this.root_src=0;
this.blockId=0;
this.sControl=0;
this.exitFlag=0;
this.secondRound=0;
this.Bloks=[];
this.exitFrame=0;
this.BlocksPlayed={};
this.currentIdBloks=-1;
this.StartInd=-1;
this.PlayStatus=0;
this.cacheCascade={};
this.cachePlay={};
this.referer=window.location.href;
this.referer_host=window.location.hostname;
this.trailerTime=0;
if(slot)
this.slot=slot;
else
this.slot=this.createSlot();

this.GlobalMyGUITemp = 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function (c) {
            var r = Math.random() * 16 | 0, v = c == 'x' ? r : (r & 0x3 | 0x8);
            return v.toString(16);
});

this.IntStab=null;

};
BlockDisptcher.prototype.drowSlot=function(settings){
	if(settings.width==0){
		this.slot.style.width="100%";
		this.slot.style.minWidth="550px";}
		else
       this.slot.style.width=settings.width+"px";
   if(settings.height==0){
	   this.slot.style.height="100%";
	   this.slot.style.minHeight="340px";}
   else
this.slot.style.height=settings.height+"px";
this.sControl=settings.control;
}
BlockDisptcher.prototype.drowControl=function(){
        var LastControllerPan = document.createElement("DIV");
        LastControllerPan.style.position = "absolute";
        LastControllerPan.style.top = "calc(50% - 25px)";
        LastControllerPan.style.right = "10px";
        LastControllerPan.style.opacity = "0.5";
        LastControllerPan.style.filter = "alpha(Opacity=50)";
        LastControllerPan.style.color = "#FFFFFF";
        LastControllerPan.style.zIndex = "4500";
        LastControllerPan.className = "lastController";
		LastControllerPan.style.width = "50px";
        LastControllerPan.style.height = "50px";

		
        var LastcloseRemain = document.createElement("DIV");
        LastcloseRemain.style.display = "block";
        LastcloseRemain.style.marginLeft = "5px";
        LastcloseRemain.fontSize = "12px";
        var  LastcloseDiv = document.createElement("DIV");

        LastcloseDiv.style.marginLeft = "5px";
        LastcloseDiv.style.backgroundImage = "url(//apptoday.ru/ug/img/exit.png) ";
        LastcloseDiv.style.backgroundRepeat = "no-repeat";
        LastcloseDiv.style.backgroundSize = "contain";
        LastcloseDiv.style.content = '';
        LastcloseDiv.className = "hover_button";
       
        LastcloseDiv.title = "закрыть рекламу";
        LastcloseDiv.style.cursor = "pointer";
        LastcloseDiv.style.display = "block";
		LastcloseDiv.style.width = "100%";
        LastcloseDiv.style.height = "100%";
		this.slot.style.position="relative";
		LastControllerPan.appendChild(LastcloseDiv);
		
		this.slot.appendChild(LastControllerPan);
		LastcloseDiv.onclick = function () {
			this.Bloks=[];
			this.exitFrame=1;
			/*
	var pack={
   page_key:this.GlobalMyGUITemp,
   event:"myButtonClose",
   id_src:0,
   block:this.blockId,
   control:this.sControl,
   pid:this.Pid,
   is_mobile:0
   };
    sendStatistic(pack);
	*/
		this.slot.innerHTML="";
	    this.slot.style.display="none";
		this.stopBlock();
		if(this.slot && this.slot.parentNode){
		this.slot.parentNode.removeChild(this.slot);
		this.slot=null;
		}
        }.bind(this);


};


BlockDisptcher.prototype.calcControl=function(SecondsToClose){
	this.calcControlCnt=SecondsToClose; 
	
};
BlockDisptcher.prototype.calcStart=function(SecondsToClose){

   this.IntStab=  setInterval(function(){

   if(SecondsToClose<0){
   
   clearInterval(this.IntStab);
   this.IntStab=null;
   this.drowControl();
   return;
   }
   SecondsToClose--;
   }.bind(this), 1000);
};
BlockDisptcher.prototype.stopBlock=function(){
   
	var playCount=0;
   
	for(var nz in this.BlocksPlayed){
	 playCount++;
	 //console.log(["отыграл",nz]); 
	}
	

	 	this.currentIdBloks++;
	if(  typeof this.SubscribeVAST !="function" && typeof this.SubscribeOut !="function")
	{
		if(playCount){
    if(this.checkPlayedStatus()){
		/*
			var pack={
   page_key:this.GlobalMyGUITemp,
   event:"myLate",
   id_src:0,
   block:this.blockId,
   control:this.sControl,
   pid:this.Pid,
   is_mobile:0
   };
    sendStatistic(pack);
		*/
	}
	else{
		/*
	var pack={
   page_key:this.GlobalMyGUITemp,
   event:"myBk",
   id_src:0,
   block:this.blockId,
   control:this.sControl,
   pid:this.Pid,
   is_mobile:0
   };
    sendStatistic(pack);
	*/
	}
		}else{
	/*	var pack={
   page_key:this.GlobalMyGUITemp,
   event:"myEmpty",
   id_src:0,
   block:this.blockId,
   control:this.sControl,
   pid:this.Pid,
   is_mobile:0
   };
    sendStatistic(pack);	
	*/		
		}
   }
	if(this.checkPlayedStatus()) { 
	//console.log([250111,"still plaing",this.BlocksPlayed,this.currentIdBloks]);
    return;
	}
	console.log(["change M",playCount,this.repeat]);
	// console.log([250118,"close block :: -1 ",this.currentIdBloks,this.Bloks.length, playCount]);
	if(playCount || this.repeat){

	  //console.log([250118,"close block :: -2 ",this.currentIdBloks,this.Bloks, playCount,typeof this.Bloks[this.currentIdBloks]]);
	 if(typeof this.Bloks[this.currentIdBloks] !="undefined"){
	this.StartBlocks(this.currentIdBloks);
	return;
	}
	
	}else{
		
		
		if(typeof this.SubscribeVAST !="function" && this.ads_last.length>0 && this.StartInd>-2){
			this.fillBlock(this.ads_last);
			this.StartInd=-2;
			this.currentIdBloks=this.Bloks.length-1;
			this.StartBlocks(this.currentIdBloks);
			return;
		}
		if(this.StartInd==-2){
		return;
		}
		
		
	}
	 this.exitFlag=1;
   if(typeof this.SubscribeVAST =="function")
	{
	this.SubscribeVAST({event:"END_BLOCK"});
	return;
	}
	if(typeof this.SubscribeOut =="function")
	{
	this.SubscribeOut({event:"END_BLOCK"});
	return;
	}
	//console.log([25011,"close block ::",this.BlocksPlayed, playCount]);
	
	//todoru
	this.stopCondition();
	
	//this.deleteSlot();
	
	
	
};
BlockDisptcher.prototype.fillBlock=function(links, second, second_cheap){
	var second=second || 0;
	var second_cheap=second_cheap || 0;
   var ind=this.Bloks.length;
   this.Bloks[ind]={stack:[],hash:{}};
   for(var i=0,j=links.length;i<j;i++){
	if (second && links[i].id=='16'){
		return;
	}

   
     var cheap = (links[i].hasOwnProperty("cheap"))?links[i].cheap:0; 
	 if(!cheap) cheap=0;
	 var player=(links[i].hasOwnProperty("player"))?links[i].player:0;
	 
	 if(!player) player=0;
	 var rame=(links[i].hasOwnProperty("frame"))?links[i].frame:"";

     var partner_script= (links[i].hasOwnProperty("partner_script"))?links[i].partner_script:"";
     var lasti =(links[i].hasOwnProperty("lasti"))?links[i].lasti:0;
	  console.log(["new entry",links[i].id]);
	  //console.log(["new entry",links[i].id,links[i].title,player,cheap]);
	 this.Bloks[ind].hash[links[i].id]={id:links[i].id,lasti:lasti,title:links[i].title,src:links[i].src,status:0,player:player,cheap:cheap,frame:rame,psc:partner_script,second:second,second_cheap:second_cheap};
	 this.Bloks[ind].stack.push(links[i].id);
   }
};
BlockDisptcher.prototype.StartBlocks=function(idBlock){
  this.currentIdBloks=idBlock;
  this.DispatchQueue(this.StartInd);
  //this.DispatchQueue(-1);
};
BlockDisptcher.prototype.DispatchQueue=function(ind){

   if(!this.cacheCascade.hasOwnProperty(this.currentIdBloks))
   this.cacheCascade[this.currentIdBloks]={};
   
   if(this.cacheCascade[this.currentIdBloks].hasOwnProperty(ind)){
   console.log(["oll-ready",ind,this.currentIdBloks]); 
   return;
   }
   
   this.cacheCascade[this.currentIdBloks][ind]=1;
   this.nextLink(this.currentIdBloks);
	};
BlockDisptcher.prototype.checkPlayedStatus=function(){
  if(!this.cachePlay.hasOwnProperty(this.currentIdBloks)) return 0;
  for(var x in this.cachePlay[this.currentIdBloks]){
  
  if(this.cachePlay[this.currentIdBloks][x]) {return 1;}
  }
  return 0;
}	
BlockDisptcher.prototype.setPlayedStatus=function(id,ind){
 if(!this.cachePlay.hasOwnProperty(this.currentIdBloks))
 this.cachePlay[this.currentIdBloks]={};
 this.cachePlay[this.currentIdBloks][id]=ind;
 
}
BlockDisptcher.prototype.changeLink=function(id){

  //if(this.exitFlag) return;
 console.log([id,"change all",id]);

 this.setPlayedStatus(id,0);
 this.DispatchQueue(id);
}
BlockDisptcher.prototype.nextLink=function(idBlock){
  if(this.currentIdBloks!=idBlock){ 
     /*
   var pack={
	
   page_key:this.GlobalMyGUITemp,
   event:"myChange",
   id_src:0,
   block:this.blockId,
   control:this.sControl,
   pid:this.Pid,
   is_mobile:0
   };
   //sendStatistic(pack);
   */
   console.log(["change stop"]);
  return;
  }

  if(this.checkPlayedStatus()){
   setTimeout(function(){
   this.nextLink(idBlock);
   }.bind(this),300)
   return;
   }
   // console.log(["change cat"]);
   if(this.Bloks.hasOwnProperty(idBlock)){
	    //console.log(["change a",this.Bloks[idBlock]]);
    if(this.Bloks[idBlock].stack.length){
		// console.log(["change b"]);
	  var linkId=this.Bloks[idBlock].stack.shift();
	  //console.log(["change k",linkId]);
	  if(linkId && linkId>0){
	  
	  this.PlayLink(linkId);
	  return;
	   }
	 }
   }
   

	this.stopBlock();
	//this.slot.innerHTML='';
	//this.slot.style.display="none";
};

BlockDisptcher.prototype.checkPosition=function(){
  // Получаем позиции элемента
  this.slot.position = {
    top: window.pageYOffset + this.slot.getBoundingClientRect().top + this.slot.offsetHeight*0.5, 
    left: window.pageXOffset + this.slot.getBoundingClientRect().left + this.slot.offsetWidth*0.5, 
    right: window.pageXOffset + this.slot.getBoundingClientRect().right - this.slot.offsetWidth*0.5, 
    bottom: window.pageYOffset + this.slot.getBoundingClientRect().bottom  - this.slot.offsetHeight*0.5,
  };
  
  // Получаем позиции окна
  window.position = {
    top: window.pageYOffset, 
    left: window.pageXOffset, 
    right: window.pageXOffset + document.documentElement.clientWidth, 
    bottom: window.pageYOffset + document.documentElement.clientHeight
  };
 //console.log([this.slot.innerHeight, window.innerHeight, window.position, this.slot.position]);
  /*console.log([12345, this.slot.position.bottom, '>', window.position.top]);
  console.log([12345, this.slot.position.top, '<', window.position.bottom]);
  console.log([12345, this.slot.position.right, '>', window.position.left]);
  console.log([12345, this.slot.position.left, '<', window.position.right]);*/
  if(this.slot.position.bottom > window.position.top && // Если позиция нижней части элемента больше позиции верхней чайти окна
     this.slot.position.top < window.position.bottom && // Если позиция верхней части элемента меньше позиции нижней чайти окна
     this.slot.position.right > window.position.left && // Если позиция правой стороны элемента больше позиции левой части окна
     this.slot.position.left < window.position.right){ // Если позиция левой стороны элемента меньше позиции правой чайти окна
    return 1;
  }else{
	return 2;
  };
};

BlockDisptcher.prototype.PlayLink=function(linkId){
  if(this.checkPlayedStatus()) return;
  if(!this.Bloks[this.currentIdBloks].hash.hasOwnProperty(linkId)) this.changeLink(linkId);

  this.setPlayedStatus(linkId,1);

  
   var rf=this.referer;
   switch(this.Bloks[this.currentIdBloks].hash[linkId].player){
    case 8:
   // var client = new PseudoClient(this.slot);
	var client = new VideorollClient(this.slot,"https://apptoday.ru/frames/tvigle.html");
	client.playerType="HTML";
	//var client = new VideorollClient(this.slot,"https://apptoday.ru/frames/tvigle.html");
	//client.dFrame=this.Bloks[this.currentIdBloks].hash[linkId].frame;
	break;
    case 5:
    var client = new PseudoClient(this.slot);
	//client.dFrame=this.Bloks[this.currentIdBloks].hash[linkId].frame;
	break;
	case 7:
    var client = new PseudoClient(this.slot);
	client.StartClick="_272686235544";

	//client.dFrame=this.Bloks[this.currentIdBloks].hash[linkId].frame;
	break;
    case 6:
    var client = new PseudoClient(this.slot);
	client.StartClick="_043411077744";

	//client.dFrame=this.Bloks[this.currentIdBloks].hash[linkId].frame;
	break;
    case 3:
	var client = new BridgeClient(this.slot,"//apptoday.ru/frames/inline.html");
	break;
	case 2:
	//var client = new XMLClient(this.slot);
	var client = new BridgeClient(this.slot,"//i-trailer.ru/player/html5/osipov/autonew.html");
	break;
    case 1:
	//var client = new XMLClient(this.slot);
	
	 var client = new BridgeClient(this.slot,"https://kinodrevo.ru/frames/inline.html");
	break;
	case 14:
	var client = new VideorollClient(this.slot,"https://apptoday.ru/frames/videoroll.html");
	break;
	case 15:
	var client = new VideorollClient(this.slot,"https://mp.vidlent.ru/frames/megogo.html");
	break;
	case 16:
	var client = new VideorollClient(this.slot,"//video.kinodrevo.ru/frames/adv.html");
	break;
	case 50:
	var client = new PseudoClient(this.slot);
	client.playerType="FRAME";
	break;
    default:
	//var client = new BridgeClient(this.slot,"//kinodrevo.ru/frames/inline.html");
	var client = new XMLClient(this.slot);
	break;
   }
   var pscC=this.Bloks[this.currentIdBloks].hash[linkId].psc;
  
    client.pscC=pscC;
    var orig=this.Bloks[this.currentIdBloks].hash[linkId].src;
    var url=repareUrl(this.Bloks[this.currentIdBloks].hash[linkId].src,{referer:rf,pid:this.Pid});
    console.log([25011,"coll link",this.Bloks[this.currentIdBloks].hash[linkId].player,linkId,this.Bloks[this.currentIdBloks].hash[linkId].title]);

  if(typeof this.SubscribeOut=="function")
	{

	 client.SubscribeOut=function(data){
	
	  this.SubscribeOut(data);
	 }.bind(this);
    } else{
		
	}
	if(typeof this.SubscribeVAST=="function")
	{
	 client.SubscribeVAST=function(data){
	
	  this.SubscribeVAST(data);
	 }.bind(this);
    }
   client.id=this.Bloks[this.currentIdBloks].hash[linkId].id;
   client.second=this.Bloks[this.currentIdBloks].hash[linkId].second;
   client.second_cheap=this.Bloks[this.currentIdBloks].hash[linkId].second_cheap;
   client.orig=orig;
   
   if(isMobile())
   client.is_Mobile=1;
   else
   client.is_Mobile=0;
   client.lasti=this.Bloks[this.currentIdBloks].hash[linkId].lasti;
   client.blockId=this.blockId;
   client.title=this.Bloks[this.currentIdBloks].hash[linkId].title;
   client.GlobalMyGUITemp = this.GlobalMyGUITemp;
   client.Pid=this.Pid;
   client.root_src=this.root_src;
   client.sControl=this.sControl;
   	client.subscribe(function(result){

	
	return this.changeLink(linkId);
	}, "AdError", this);
	if(typeof this.SubscribeOut =="function")
	{
	}else{
   var pack={
   page_key:this.GlobalMyGUITemp,
   event:"request",
   id_src:client.id,
   block:this.blockId,
   control:this.sControl,
   pid:this.Pid,
   is_mobile:client.is_Mobile
   };
  
   sendStatistic(pack);
   }
	
   client.FetchData(url).then(function(result){
	if (this.Pid==1227){
		client.muted();
		window.addEventListener('mouseover', function(e) {
			this.slot.onmouseover = function() {
				client.unmutedMax();
			}
		}.bind(this), false);
		window.addEventListener('mouseout', function(e) {
			this.slot.onmouseover = function() {
				client.muted();
			}
		}.bind(this), false);
	}
    client.subscribe(function(result){
		if(this.exitFrame) {client.stop(); return;}
		console.log(["Change MK"]);
    this.BlocksPlayed[client.id]=1;	
	if(this.calcControlCnt>0){
		
	this.calcStart(this.calcControlCnt);
	this.calcControlCnt=0;
	}

	
	}, "AdVideoStart", this) ;
	client.play().then(function(){
	client.subscribe(function(result){
	
	return this.changeLink(linkId);
	}, "AdStopped", this) ;
	client.subscribe(function(result){
	
	return this.changeLink(linkId);
	}, "stop", this) ;
	client.subscribe(function(result){
	return this.changeLink(linkId);
	}, "AdSkipped", this) ;
   
	
	
	}.bind(this)).catch(function(err){

	}.bind(this));
		
		
		
		
		
	}.bind(this)).catch(function(error){
	
	
	

	  return this.changeLink(linkId);
    }.bind(this));
   
   
}
BlockDisptcher.prototype.stopCondition=function(){
	var  playCount=0;
	for(var nz in this.BlocksPlayed){
	 playCount++;

	}
	// 
	if(playCount && this.noTrailer ==0){
	   
	this.playTrailer();
	return;
	}
	this.deleteSlot();
};
BlockDisptcher.prototype.playTrailer=function(){
if(this.trailerTime || this.exitFrame){
	 
this.deleteSlot();

return;
}
console.log(["igr",this.trailerTime]); 
 var pack={
   page_key:this.GlobalMyGUITemp,
   event:"myTrailer",
   id_src:0,
   block:this.blockId,
   control:this.sControl,
   pid:this.Pid,
   is_mobile:0
   };
  
   sendStatistic(pack);

 var root = this.slot.appendChild(document.createElement("div"));
        root.id = "mp-video-player";
        root.className = "mp-video-player";
       
	     var mediaPlayer = root.appendChild(document.createElement("video"));
		 if(!mediaPlayer.canPlayType("video/mp4")){
			this.deleteSlot(); 
			 return; 
		 }
		 //console.log(["отыграл и ... "]); 
         mediaPlayer.style.display = "block";
		 mediaPlayer.controls=true;
		 mediaPlayer.autoplay=true;
		  mediaPlayer.style.width=this.slot.style.width;
		  mediaPlayer.style.height=this.slot.style.height;
                var source = document.createElement("source");
                source.type = "video/mp4";
                source.src = "https://video.market-place.su/mp4/ivancarevichiseryjvolk3m.mp4";
                mediaPlayer.appendChild(source);
				mediaPlayer.volume=0.0;
                mediaPlayer.load();
				
				
				mediaPlayer.onended = function() {
					
					this.deleteSlot();
				}.bind(this);
				mediaPlayer.addEventListener('loadeddata', function() {
   var pack={
   page_key:this.GlobalMyGUITemp,
   event:"myTrailerLoaded",
   id_src:0,
   block:this.blockId,
   control:this.sControl,
   pid:this.Pid,
   is_mobile:0
   };
    sendStatistic(pack);
                }.bind(this), false);
	mediaPlayer.addEventListener('error', function() {
   var pack={
   page_key:this.GlobalMyGUITemp,
   event:"myTrailerError",
   id_src:0,
   block:this.blockId,
   control:this.sControl,
   pid:this.Pid,
   is_mobile:0
   };
    sendStatistic(pack);
                }.bind(this), false);			
				

				//console.log(["откуда тут это  ",this.trailerTime]); 
this.trailerTime=1;


}
BlockDisptcher.prototype.createSlot=function(){
var slot=document.createElement('DIV');

//slot.style.border="1px solid #000000";
document.body.appendChild(slot);
return slot;
}
BlockDisptcher.prototype.deleteSlot=function(slot){
//console.log([250111,(this.slot && this.slot.parentNode),"stat tttttt -> 0"]);
if(this.slot && this.slot.parentNode)
	
this.slot.parentNode.removeChild(this.slot);
}
module.exports=BlockDisptcher;
},{"./BridgeClient":4,"./Client":5,"./PseudoClient":6,"./Statistic":7,"./VideorollClient":8,"./util/UTILS":13}],4:[function(require,module,exports){
'use strict';
var XMLLoader=require("./util/XML");
var VideoEvent=require("./util//VideoEvent");
var VPAIDEvent=require("./util//VPAIDEvent");
var loadEvents=require("./util//loadEvents");
var sendStatistic=require("./Statistic");
var BridgePlayer=require("./util/players/BridgePlayer");
var getNextBlockUrl=require("./util/UTILS").getNextBlockUrl; 
var Promise = require('lie');
function BridgeClient(slot,frame){
    this.slot=slot;
	this.blockId=0;
    this.parameters={slot:slot,version: "2.0"};
    this.id=0;
	this.instreamStart=0;
    this.timeOutHandler=null;
    this.title="Никак";
    this.mediaPlayer=null;
    this.subscribers = {};
    this.flags={};
    this.flags.paused = false;
    this.flags.started = false;
    this.flags.stopped = false;     
    this.medyaFile={type:'',file:''};
    this.eventCounter=0;
	this.FrameSrc=frame;
};
BridgeClient.prototype.subscribe = function (handler, events, context) {
        if (typeof events === "string") {
            events = [events];
        }
        for (var i = 0, max = events.length; i < max; i++) {
            var event = events[i];
            if (!this.subscribers[event]) {
			
                this.subscribers[event] = [];
            }
            this.subscribers[event].push({fn: handler, ctx: context || null});
        }
    };
BridgeClient.prototype.unsubscribe = function (handler, events) {
        if (typeof events === "string") {
            events = [events];
        }
        for (var i = events.length; i >= 0; i--) {
            var subscribers = this.subscribers[events[i]];
            if (subscribers && Array.isArray(subscribers) && subscribers.length) {
                for (var j = 0, max = subscribers.length; j < max; j++) {
                    if (subscribers[j].fn === handler) {
                        subscribers.splice(j, 1);
                    }
                }
            }
        }
    };
BridgeClient.prototype.muted = function () {
 if(this.mediaPlayer){
 this.mediaPlayer.setVolume(0);
 //delete this.mediaPlayer;
  }
};	

BridgeClient.prototype.unmuted = function () {
 if(this.mediaPlayer){
 this.mediaPlayer.setVolumes(1);
 //delete this.mediaPlayer;
  }
};

BridgeClient.prototype.unmutedMax = function () {
 if(this.mediaPlayer){
 this.mediaPlayer.setVolume(1);
 //delete this.mediaPlayer;
  }
};
BridgeClient.prototype.stop = function () {
 if(this.mediaPlayer){
 this.mediaPlayer.stop();
 delete this.mediaPlayer;
  }
};		
BridgeClient.prototype.play=function(){
return new Promise(function(resolve, reject) { 
//"https://video.market-place.su/v1/img/loader.gif";
   if(this.mediaPlayer){
   this.mediaPlayer.play();
   this.slot.style.backgroundColor="#000000";
   resolve();
   }
   else{
   reject(["error mediaPlayer",{}]);
   }
   
    //setTimeout(function() { reject('timeout 8 sek'); }, 8000);
  }.bind(this));
};
BridgeClient.prototype.FetchData=function(url){

       return new Promise(function(resolve, reject) { 
	     this.timeOutHandler=setTimeout(function() { 
		
		 this.stop();
		 reject({status:'timeout 9 sek'});
		 }.bind(this), 9000);
		    function getPromised(){
			if(this.timeOutHandler){
		    clearTimeout(this.timeOutHandler);
		    }
			
			resolve({status:"this ok"});
			};
			this.medyaFile.type="Bridge";
		    this.mediaPlayer = new BridgePlayer(this.parameters.slot);
			this.mediaPlayer.getPromised=getPromised.bind(this);
			this.mediaPlayer.id=this.id;
			
		    this.mediaPlayer.title=this.title; 	
			if(this.hasOwnProperty("orig"))
			url=this.orig;
			this.mediaPlayer.init({
                mediapath: '',
                xmlLoader: null,
				url:url,
				frame:this.FrameSrc,
				id_src:this.id
				
            }, $mediaEventHandler, this);
			
			

	     }.bind(this));
function $mediaEventHandler(event) {

     
	  event.data = event.data || {};
	  //var myEvent = VPAIDEvent.convertFromVAST(event.type);
	  var myEvent = event.type;
	 

	  var params = {};	
	    var pack={
        page_key:this.GlobalMyGUITemp, 
        event:event.type+' ))',
        id_src:this.id,
		block:this.blockId,
        pid:this.Pid,
		e1:myEvent
        };
	    //sendStatistic(pack);
	    
	 // var myEvent = event.type;
	  if(!myEvent){ 
	 
	  return;
	  }
	  if(this.id==1 || this.id==2){
	   switch(myEvent){

	   case "AdVideoFirstQuartile":
	   return;
	   break;
	   case "AdVideoMidpoint":
	   case "AdVideoThirdQuartile":
	   case "AdVideoComplete":
	   if(!this.instreamStart) return;
	   break;
	   case "AdViewable":
	   this.instreamStart=1;
	   myEvent="AdVideoFirstQuartile";
	   break;	
	   }
	  }
	   //
	   
	   
	   switch(myEvent){
	   case "AdLoaded":
	   case "AdStarted":
	   case "AdVideoStart":
	   case "AdImpression":
	   case "AdVideoFirstQuartile":
	   case "AdVideoMidpoint":
	   case "AdVideoThirdQuartile":
	   case "AdVideoComplete":
	   case "AdClickThru":
	   case "AdViewable":
	   case "click":
	  
	    var pack={
        page_key:this.GlobalMyGUITemp,
        event:myEvent,
        id_src:this.id,
		block:this.blockId,
		control:this.sControl,
        pid:this.Pid,
		is_mobile:this.is_Mobile,
		second:this.second,
		second_cheap:this.second_cheap
        };
	    sendStatistic(pack);
       break;	
       case "AdError":
	  
       break;	   
	  }
     
	  
	  $notifyObservers.call(this, new VPAIDEvent(VPAIDEvent.convertFromVAST(event.type), event.data));
	 }
    function $notifyObservers(event) {
       (this.subscribers[event.type] || []).forEach(function (item) {
		 
            item.fn.call(item.ctx, event);
        });
    }	 		 
		 
		 
		 
};	
module.exports=BridgeClient;
},{"./Statistic":7,"./util//VPAIDEvent":14,"./util//VideoEvent":15,"./util//loadEvents":18,"./util/UTILS":13,"./util/XML":16,"./util/players/BridgePlayer":19,"lie":2}],5:[function(require,module,exports){
'use strict';
var XMLLoader=require("./util/XML");
var VideoEvent=require("./util//VideoEvent");
var VPAIDEvent=require("./util//VPAIDEvent");
var loadEvents=require("./util//loadEvents");
var sendStatistic=require("./Statistic");
var VPAIDPlayer=require("./util/players/VPAIDPlayer");
var VideoPlayer=require("./util/players/VideoPlayer");
var getNextBlockUrl=require("./util/UTILS").getNextBlockUrl; 
var getHostName=require("./util/UTILS").getHostName;
var Promise = require('lie');
function Client(slot){  
 this.slot=slot;
 this.blockId=0;
 this.parameters={slot:slot,version: "2.0"};
 this.id=0;
 this.instreamStart=0;
 this.VastFlag=0;
 this.timeOutHandler=null;
 this.title="Никак";
 this.mediaPlayer=null;
 this.subscribers = {};
  this.flags={};
  this.flags.paused = false;
  this.flags.started = false;
  this.flags.stopped = false;     
  this.medyaFile={type:'',file:''};
  this.eventCounter=0;
  this.secondRound=0;
};
Client.prototype.subscribe = function (handler, events, context) {
        if (typeof events === "string") {
            events = [events];
        }
        for (var i = 0, max = events.length; i < max; i++) {
            var event = events[i];
            if (!this.subscribers[event]) {
			//console.log(["евент начало дня",event,this.title]);
                this.subscribers[event] = [];
            }
            this.subscribers[event].push({fn: handler, ctx: context || null});
        }
    };
Client.prototype.unsubscribe = function (handler, events) {
        if (typeof events === "string") {
            events = [events];
        }
        for (var i = events.length; i >= 0; i--) {
            var subscribers = this.subscribers[events[i]];
            if (subscribers && Array.isArray(subscribers) && subscribers.length) {
                for (var j = 0, max = subscribers.length; j < max; j++) {
                    if (subscribers[j].fn === handler) {
                        subscribers.splice(j, 1);
                    }
                }
            }
        }
    };

Client.prototype.muted = function () {
 if(this.mediaPlayer){
 this.mediaPlayer.setVolume(0);
 //delete this.mediaPlayer;
  }
};	

Client.prototype.unmuted = function () {
 if(this.mediaPlayer){
 this.mediaPlayer.setVolumes(1);
 //delete this.mediaPlayer;
  }
};

Client.prototype.unmutedMax = function () {
 if(this.mediaPlayer){
 this.mediaPlayer.setVolume(1);
 //delete this.mediaPlayer;
  }
};
	
Client.prototype.resume = function () {
 if(this.mediaPlayer){
 this.mediaPlayer.resume();
 //delete this.mediaPlayer;
  }
};	
	
Client.prototype.pause = function () {
 if(this.mediaPlayer){
 this.mediaPlayer.pause();
 //delete this.mediaPlayer;
  }
};	

Client.prototype.stop = function () {
 if(this.mediaPlayer){
 this.mediaPlayer.stop();
 //delete this.mediaPlayer;
  }
};	
Client.prototype.play=function(){
return new Promise(function(resolve, reject) { 
//"https://video.market-place.su/v1/img/loader.gif";
   if(this.mediaPlayer){
   this.mediaPlayer.play();
   this.slot.style.backgroundColor="#000000";
   this.slot.style.background="#000000 url('https://video.market-place.su/v1/img/loader.gif') no-repeat center center"; 
   this.slot.style.backgroundSize='50px 50px';
   resolve();
   }
   else{
   reject(["error mediaPlayer",{}]);
   }
   
    //setTimeout(function() { reject('timeout 8 sek'); }, 8000);
  }.bind(this));
};
Client.prototype.FetchData=function(url){

    return new Promise(function(resolve, reject) { 

	
	    $prepareAd.call(this,url,function(error,result){
	     if(error){
     
		 
	      reject({status:'src url failed'}); 
	     }
		 if(this.timeOutHandler){
		 clearTimeout(this.timeOutHandler);
		 }
		 resolve(result);
		 }.bind(this));
		 this.timeOutHandler=setTimeout(function() { 


		 this.stop();
		 reject({status:'timeout 9 sek'});
		 }.bind(this), 9000);

		 
	     }.bind(this));
		
	 function $prepareAd(url,done) {

	 	
       this.mediaPlayer = false;	
	   this.xmlLoader = new XMLLoader();
	   if (this.id==61 || this.id==64){
		this.xmlLoader.withCredentials=false;
	   }
	   /*if (this.id==4){
		console.log([1234567]);
		setTimeout(function() { 

		 this.stop();
		 }.bind(this), 20000);
		 
	   }*/
		
	   this.xmlLoader.id_player=this.id;
	   this.xmlLoader.title_player=this.title;
	   
	   this.xmlLoader.loadVast(url, function (err, result) {
		   console.log(["к 44444 ->",result,err]);
	    if(err){
	       return done(err,null);

	    }

		var MediaPlayer;
		
			this.medyaFile.type=result.type;
            if (result.type == "VideoPlayer") {
               MediaPlayer = VideoPlayer;
            }
            else {
			   if(result.type == "VPAIDPlayer"){
			   MediaPlayer = VPAIDPlayer;
			   }else{

			   }
            }
		    if (typeof MediaPlayer != "function") {
              				
				
			   return done({status: "player "+result.type +" not ready"});
            }
			 //console.log([222222,"url 1",result.type]);
			function getPromised(){

			return done(null,result); 
			};
			 //console.log([222222,"url 2",result.type,MediaPlayer]);
			this.mediaPlayer = new MediaPlayer(this.parameters.slot);
			this.mediaPlayer.getPromised=getPromised.bind(this);
			this.mediaPlayer.id=this.id;
		    this.mediaPlayer.title=this.title; 
			this.mediaPlayer.init({
                mediapath: '',
				pscC:this.pscC,
                xmlLoader: this.xmlLoader
            }, $mediaEventHandler, this);
	
	    }.bind(this), this);
	  } 
	 function $mediaEventHandler(event) {
	 
	  
	 
	  if(typeof this.SubscribeOut =="function"){
	 
	  
	  }
	 
	  event.data = event.data || {};
	  var params = {};	

	   var myEvent = VPAIDEvent.convertFromVAST(event.type);
      
        event.data.loadedEvent = loadEvents(this.xmlLoader, event.type, params);
	    //var erf='';
	    //var location=window.location.href;
	    //if(window.parent)
	    //erf = window.parent.document.referrer
	     //var erf = (window.location != window.parent.location) ? document.referrer : document.location.href;

	  //getHostName
	  if(!myEvent){
	 
	  return;
	  }
	   
	   if(this.id==1 || this.id==2){
	   switch(myEvent){

	   case "AdVideoFirstQuartile":
	   return;
	   break;
	   case "AdVideoMidpoint":
	   case "AdVideoThirdQuartile":
	   case "AdVideoComplete":
	   if(!this.instreamStart) return;
	   break;
	   case "AdViewable":
	   this.instreamStart=1;
	   myEvent="AdVideoFirstQuartile";
	   break;	
	   }
	  }
	   
	   switch(myEvent){
	   case "AdLoaded":
	   case "AdStarted":
	   case "AdVideoStart":
	   case "AdImpression":
	   case "AdVideoFirstQuartile":
	   case "AdVideoMidpoint":
	   case "AdVideoThirdQuartile":
	   case "AdVideoComplete":
	   case "AdClickThru":
	   case "AdViewable":
	   case "click":
	   if(typeof this.SubscribeVAST =="function"){
	   this.SubscribeVAST({event:myEvent});
		}
	   if(typeof this.SubscribeOut =="function"){
	   this.SubscribeOut({event:myEvent});
	   if(this.VastFlag){
	    var pack={
        page_key:this.GlobalMyGUITemp,
        event:myEvent,
        id_src:this.id,
		block:this.blockId,
		control:this.sControl,
        pid:this.Pid,
		is_mobile:this.is_Mobile,
		second:this.second,
		second_cheap:this.second_cheap
        };
	    sendStatistic(pack);
	   }
	   // console.log(["тест васт флаг",this.VastFlag])
	   }else{
	    var pack={
        page_key:this.GlobalMyGUITemp,
        event:myEvent,
        id_src:this.id,
		block:this.blockId,
		control:this.sControl,
        pid:this.Pid,
		is_mobile:this.is_Mobile,
		second:this.second,
		second_cheap:this.second_cheap
        };
	    sendStatistic(pack);
		}
		
       break;	
       case "AdError":
	    //console.log([888888,"тайное событие 2",event.type,event,this.title]);
       break;	   
	  }
    
	$notifyObservers.call(this, new VPAIDEvent(myEvent, event.data));
	  //console.log([888888,"любое событие событие 2",event.type,VPAIDEvent(VPAIDEvent.convertFromVAST(event.type),this.title]);
	  //$notifyObservers.call(this, new VPAIDEvent(VPAIDEvent.convertFromVAST(event.type), event.data));
	 }
    function $notifyObservers(event) {
		
		//console.log(["васт евент тип",event.type,this.subscribers[event.type],this.title]);
       (this.subscribers[event.type] || []).forEach(function (item) {
		// console.log([item.ctx,event.type,item.fn]);
            item.fn.call(item.ctx, event);
        });
    }	 
    };
module.exports=Client;
},{"./Statistic":7,"./util//VPAIDEvent":14,"./util//VideoEvent":15,"./util//loadEvents":18,"./util/UTILS":13,"./util/XML":16,"./util/players/VPAIDPlayer":23,"./util/players/VideoPlayer":24,"lie":2}],6:[function(require,module,exports){
'use strict';
var XMLLoader=require("./util/XML");
var VideoEvent=require("./util//VideoEvent");
var VPAIDEvent=require("./util//VPAIDEvent");
var loadEvents=require("./util//loadEvents");
var sendStatistic=require("./Statistic");
var PseudoPlayer=require("./util/players/PSEUDOPlayer");
var FRAMEPlayer=require("./util/players/FRAMEPlayer");

//var HTMLPlayer=require("./util/players/HTMLPlayer");

var getNextBlockUrl=require("./util/UTILS").getNextBlockUrl; 
var Promise = require('lie');
function PseudoClient(slot,frame){
    this.slot=slot;
	this.dframe="";
	this.blockId=0;
	this.StartClick=0;
    this.parameters={slot:slot,version: "2.0"};
    this.id=0;
    this.timeOutHandler=null;
    this.title="Ќикак";
    this.mediaPlayer=null;
    this.subscribers = {};
    this.flags={};
    this.flags.paused = false;
    this.flags.started = false;
    this.flags.stopped = false;     
    this.medyaFile={type:'',file:''};
    this.eventCounter=0;
	this.secondRound=0;
	this.FrameSrc=frame;
};
PseudoClient.prototype.subscribe = function (handler, events, context) {
        if (typeof events === "string") {
            events = [events];
        }
        for (var i = 0, max = events.length; i < max; i++) {
            var event = events[i];
            if (!this.subscribers[event]) {
			
                this.subscribers[event] = [];
            }
            this.subscribers[event].push({fn: handler, ctx: context || null});
        }
    };
PseudoClient.prototype.unsubscribe = function (handler, events) {
        if (typeof events === "string") {
            events = [events];
        }
        for (var i = events.length; i >= 0; i--) {
            var subscribers = this.subscribers[events[i]];
            if (subscribers && Array.isArray(subscribers) && subscribers.length) {
                for (var j = 0, max = subscribers.length; j < max; j++) {
                    if (subscribers[j].fn === handler) {
                        subscribers.splice(j, 1);
                    }
                }
            }
        }
    };
PseudoClient.prototype.stop = function () {
 if(this.mediaPlayer){
 this.mediaPlayer.stop();
 delete this.mediaPlayer;
  }
};		
PseudoClient.prototype.muted = function () {
 if(this.mediaPlayer){
 this.mediaPlayer.setVolume(0);
 //delete this.mediaPlayer;
  }
};	

PseudoClient.prototype.unmuted = function () {
 if(this.mediaPlayer){
 this.mediaPlayer.setVolumes(1);
 //delete this.mediaPlayer;
  }
};

PseudoClient.prototype.unmutedMax = function () {
 if(this.mediaPlayer){
 this.mediaPlayer.setVolume(1);
 //delete this.mediaPlayer;
  }
};
PseudoClient.prototype.play=function(){
return new Promise(function(resolve, reject) { 
//"https://video.market-place.su/v1/img/loader.gif";
   if(this.mediaPlayer){
   this.mediaPlayer.play();
   this.slot.style.backgroundColor="#000000";
   resolve();
   }
   else{
   reject(["error mediaPlayer",{}]);
   }
   
    //setTimeout(function() { reject('timeout 8 sek'); }, 8000);
  }.bind(this));
};
PseudoClient.prototype.FetchData=function(url){

        return new Promise(function(resolve, reject) { 
	  
	     this.timeOutHandler=setTimeout(function() { 
		 //console.log([2501177,"остановлен",this.title])
		 this.stop();
		 reject({status:'timeout 9 sek'});
		 }.bind(this), 9000);
		    function getPromised(){
			if(this.timeOutHandler){
		    clearTimeout(this.timeOutHandler);
		    }
			console.log(["клю"]);
			resolve({status:"this ok"});
			};
			
			if(this.hasOwnProperty("playerType")){
			switch(this.playerType){
			case "FRAME":
			this.medyaFile.type=this.playerType;
			this.mediaPlayer = new FRAMEPlayer(this.parameters.slot);
			
			//return reject({status:'not found player '+this.playerType+' in Pseudo'});
            break;			
			//case "HTML":
			//this.medyaFile.type=this.playerType;
		    //this.mediaPlayer = new HTMLPlayer(this.parameters.slot);
			
			//break;
			default:
			return reject({status:'not found player '+this.playerType+' in Pseudo'});
			break;
			}
			
			 
			}else{
			this.medyaFile.type="Pseudo";
		    this.mediaPlayer = new PseudoPlayer(this.parameters.slot);
			}
			this.mediaPlayer.getPromised=getPromised.bind(this);
			
			
			this.mediaPlayer.id=this.id;
			
			
			
		    this.mediaPlayer.title=this.title; 	
			
			this.mediaPlayer.init({
                mediapath: '',
                xmlLoader: null,
				url:url,
				frame:this.dFrame,
				id_src:this.id,
				StartClick:this.StartClick
            }, $mediaEventHandler, this);
			
			

	     }.bind(this));
function $mediaEventHandler(event) {

      
	  event.data = event.data || {};
	  //var myEvent = VPAIDEvent.convertFromVAST(event.type);
	  var myEvent = event.type;
	 
	  

	    
	 // var myEvent = event.type;
	  if(!myEvent){ 
	 
	  return;
	  }
	   
	   switch(myEvent){
		  
	   case "AdLoaded":
	   case "AdStarted":
	   case "AdVideoStart":
	   case "AdImpression":
	   case "AdVideoFirstQuartile":
	   case "AdVideoMidpoint":
	   case "AdVideoThirdQuartile":
	   case "AdVideoComplete":
	   case "AdClickThru":
	   case "AdViewable":
	 
	    var pack={
        page_key:this.GlobalMyGUITemp,
        event:myEvent,
        id_src:this.id,
		block:this.blockId,
		control:this.sControl,
        pid:this.Pid,
		is_mobile:this.is_Mobile,
		second:this.second,
		second_cheap:this.second_cheap
        };
	    sendStatistic(pack);
       break;	
       case "AdError":
	    //console.log([888888," тайное событие",event.type,event,this.title]);
       break;	   
	  }
      //event.data.loadedEvent = loadEvents(this.xmlLoader, event.type, params);
	  var mE=new VPAIDEvent(VPAIDEvent.convertFromVAST(event.type));
	  if(!mE.type){
		  mE={type:myEvent,data:null};
	  }
	  //console.log([888822," ¤вное событие",event.type,myEvent,this.title]);  
	  $notifyObservers.call(this, mE);
	 //$notifyObservers.call(this, );
	 }
    function $notifyObservers(event) {
       (this.subscribers[event.type] || []).forEach(function (item) {
		 console.log([item.ctx,event.type,item.fn]);
            item.fn.call(item.ctx, event);
        });
    }	 		 
		 
		 
		 
};	
module.exports=PseudoClient;
},{"./Statistic":7,"./util//VPAIDEvent":14,"./util//VideoEvent":15,"./util//loadEvents":18,"./util/UTILS":13,"./util/XML":16,"./util/players/FRAMEPlayer":20,"./util/players/PSEUDOPlayer":22,"lie":2}],7:[function(require,module,exports){
'use strict';
function sendStatistic(data) {
 var img = new Image();
 //img.src = "https://video.market-place.su/statistic/?p=" + Math.random() + '&data=' + encodeURIComponent(JSON.stringify(data)); 
 //console.log([32411,"отсылаю статистику",img.src]);

}
module.exports=sendStatistic;
},{}],8:[function(require,module,exports){
'use strict';
var XMLLoader=require("./util/XML");
var VideoEvent=require("./util//VideoEvent");
var VPAIDEvent=require("./util//VPAIDEvent");
var loadEvents=require("./util//loadEvents");
var sendStatistic=require("./Statistic");
var VideorollPlayer=require("./util/players/VideorollPlayer");
var HTMLPlayer=require("./util/players/HTMLPlayer");
var getNextBlockUrl=require("./util/UTILS").getNextBlockUrl; 
var Promise = require('lie');
function VideorollClient(slot,frame){
    this.slot=slot;
	this.blockId=0;
	this.lasti=0;
    this.parameters={slot:slot,version: "2.0"};
    this.id=0;
    this.timeOutHandler=null;
    this.title="Никак";
    this.mediaPlayer=null;
    this.subscribers = {};
    this.flags={};
    this.flags.paused = false;
    this.flags.started = false;
    this.flags.stopped = false;     
    this.medyaFile={type:'',file:''};
    this.eventCounter=0;
	this.secondRound=0;
	this.FrameSrc=frame;
};
VideorollClient.prototype.subscribe = function (handler, events, context) {
        if (typeof events === "string") {
            events = [events];
        }
        for (var i = 0, max = events.length; i < max; i++) {
            var event = events[i];
			
            if (!this.subscribers[event]) {
			
                this.subscribers[event] = [];
            }
            this.subscribers[event].push({fn: handler, ctx: context || null});
        }
    };
VideorollClient.prototype.unsubscribe = function (handler, events) {
        if (typeof events === "string") {
            events = [events];
        }
        for (var i = events.length; i >= 0; i--) {
            var subscribers = this.subscribers[events[i]];
            if (subscribers && Array.isArray(subscribers) && subscribers.length) {
                for (var j = 0, max = subscribers.length; j < max; j++) {
				
                    if (subscribers[j].fn === handler) {
                        subscribers.splice(j, 1);
                    }
                }
            }
        }
    };
VideorollClient.prototype.stop = function () {
 if(this.mediaPlayer){
 this.mediaPlayer.stop();
 //this.mediaPlayer;
  }
};		
VideorollClient.prototype.muted = function () {
 if(this.mediaPlayer){
 this.mediaPlayer.setVolume(0);
 //delete this.mediaPlayer;
  }
};	

VideorollClient.prototype.unmuted = function () {
 if(this.mediaPlayer){
 this.mediaPlayer.setVolumes(1);
 //delete this.mediaPlayer;
  }
};

VideorollClient.prototype.unmutedMax = function () {
 if(this.mediaPlayer){
 this.mediaPlayer.setVolume(1);
 //delete this.mediaPlayer;
  }
};
VideorollClient.prototype.play=function(){
return new Promise(function(resolve, reject) { 
//"https://video.market-place.su/v1/img/loader.gif";
   if(this.mediaPlayer){
   this.mediaPlayer.play();
   this.slot.style.backgroundColor="#000000";
   resolve();
   }
   else{
   reject(["error mediaPlayer",{}]);
   }
   
    //setTimeout(function() { reject('timeout 8 sek'); }, 8000);
  }.bind(this));
};
VideorollClient.prototype.FetchData=function(url){

       return new Promise(function(resolve, reject) { 
	     this.timeOutHandler=setTimeout(function() { 
		 
		 this.stop();
		
		 reject({status:'timeout 9 sek'});
		 }.bind(this), 9000);
		    function getPromised(){
			if(this.timeOutHandler){
		    clearTimeout(this.timeOutHandler);
		    }
			
			resolve({status:"this ok"});
			};
			if(this.hasOwnProperty("playerType")){
			switch(this.playerType){
			case "HTML":
			this.medyaFile.type=this.playerType;
		    this.mediaPlayer = new HTMLPlayer(this.parameters.slot);
			
			break;
			default:
			return reject({status:'not found player '+this.playerType+' in Pseudo'});
			break;
			}
			
			 
			}else{
			this.medyaFile.type="Videoroll";
		    this.mediaPlayer = new VideorollPlayer(this.parameters.slot);
			}
			this.mediaPlayer.getPromised=getPromised.bind(this);
			this.mediaPlayer.id=this.id;
			
		    this.mediaPlayer.title=this.title; 	

			this.mediaPlayer.init({
                mediapath: '',
                xmlLoader: null,
				url:url,
				lasti:this.lasti,
				frame:this.FrameSrc,
				id_src:this.id
				
            }, $mediaEventHandler, this);
			
			

	     }.bind(this));
function $mediaEventHandler(event) {

    
	  event.data = event.data || {};
	  //var myEvent = VPAIDEvent.convertFromVAST(event.type);
	   console.log([888822," ¤вное событие",event.type,myEvent,this.title]);  
	  var myEvent = event.type;
	 
     
	  var params = {};	
	    var pack={
        page_key:this.GlobalMyGUITemp, 
        event:event.type+' ))',
        id_src:this.id,
		block:this.blockId,
        pid:this.Pid,
		e1:myEvent
        };
	    //sendStatistic(pack);
	    
	 // var myEvent = event.type;
	  if(!myEvent){ 
	 
	  return;
	  }
	   
	   switch(myEvent){
	   case "AdLoaded":
	   case "AdStarted":
	   case "AdVideoStart":
	   case "AdImpression":
	   case "AdVideoFirstQuartile":
	   case "AdVideoMidpoint":
	   case "AdVideoThirdQuartile":
	   case "AdVideoComplete":
	   case "AdClickThru":
	   case "AdViewable":
	
	    var pack={
        page_key:this.GlobalMyGUITemp,
        event:myEvent,
        id_src:this.id,
		block:this.blockId,
		control:this.sControl,
        pid:this.Pid,
		second:this.second,
		second_cheap:this.second_cheap
        };
	    sendStatistic(pack);
       break;	
       case "AdError":
	    
       break;	   
	  }
      var mE=new VPAIDEvent(VPAIDEvent.convertFromVAST(event.type));
	  if(!mE.type){
		  mE={type:myEvent,data:event.data};
	  }
	  
	  $notifyObservers.call(this,  mE);
	 }
    function $notifyObservers(event) {
	
       (this.subscribers[event.type] || []).forEach(function (item) {
		
            item.fn.call(item.ctx, event);
        });
    }	 		 
		 
		 
		 
};	
module.exports=VideorollClient;
},{"./Statistic":7,"./util//VPAIDEvent":14,"./util//VideoEvent":15,"./util//loadEvents":18,"./util/UTILS":13,"./util/XML":16,"./util/players/HTMLPlayer":21,"./util/players/VideorollPlayer":25,"lie":2}],9:[function(require,module,exports){
'use strict';
var Dispatcher=require("./BlockDispatcher");
var getHostName=require("./util/UTILS").getHostName;
var parseConfig=require("./util/UTILS").parseConfig;
var isMobile=require("./util/UTILS").isMobile;
var sendStatistic=require("./Statistic");
var Configurator=require("./util/Configurator");
var BridgeLib = require('./util/iFrameBridge');
var Bridge=BridgeLib.Bridge;
var CallAction = BridgeLib.callAction;
function myVastClient_1275(){
};

myVastClient_1275.prototype.getDefault=function(data){ 
   var config=[]; 
   
    // config.push({id:555,title:'Украина не спит',src:"https://video.market-place.su/vast/uvelichenie.xml"});

	return config;
}

myVastClient_1275.prototype.playFrame=function(data){ 


var conf=parseConfig(window.location.href);

if(!conf.hasOwnProperty("data"))
return;

var d1=decodeURIComponent(conf.data);
try{
var d2=JSON.parse(d1);



if(d2.hasOwnProperty("url")){
var ind="broadcast";
if(d2.hasOwnProperty("index")){
   ind=d2.index;
}
 var root_src=0;
if(d2.hasOwnProperty("id_src")){
   root_src=d2.id_src;
}
 //d2.url="https://instreamvideo.ru/core/vpaid/linear_test?pid=7&wtag=kinodrevo&vr=1&rid={rnd}&puid7=1&puid8=7&puid10=1&puid11=1&puid12=16&dl=&duration=360&vn=";
 var config=[]; 

 config.push({id:99999,title:'Тест FRAME',src:d2.url,player:0});
 			var bridge=new Bridge(); 
			


            //var res=this.getDefault();
			//console.log(["индекс готов  111"]);
			var dsp=new Dispatcher();
			dsp.referer='https://kinodrevo.ru';
			dsp.slot.style.width="100%";
			dsp.slot.style.height="100%";
			dsp.root_src=root_src;
			this.parentMessage({index:ind,event:"AdLoaded"});
			dsp.SubscribeOut=function(data){
			switch(data.event){
			case "END_BLOCK":
			this.parentMessage({index:ind,event:"BlockStoppped"});
			break;
			default:
			
			this.parentMessage({index:ind,event:data.event});
			break;
			}
			
			}.bind(this);
			dsp.Pid=0;
			dsp.fillBlock(config);
			bridge.addAction("adEvent",function(data){
		
			if(!data.hasOwnProperty("event")) return;

			
			switch(data.event){
		    case "Start":
			
			
			dsp.StartBlocks(0);
			break;
			}
  	        });

           
			return;
 }
}catch(e){
//console.log(e);
}
this.parentMessage({index:ind,event:"AdError"});
}
myVastClient_1275.prototype.parentMessage=function(data){ 
  if(window.parent){
  CallAction('adEvent', {index: data.index, event:data.event}, window.parent);
  }
}
myVastClient_1275.prototype.playAuto=function(data){ 
console.log(["rrrr",data]);
if(!data.hasOwnProperty("pid")) return;
if(!data.hasOwnProperty("container")) return;
var  container=document.getElementById(data.container);
if(!container){
var  container=document.getElementById("mp-video-widget-container-59bb833673217-701");
console.log("но дата контайнер");
if(!container)
 return;
 }
			/*if(! /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) && data.pid!='11') {
var ifrme = document.createElement("iframe");
ifrme.setAttribute("src","https://rekovers.ru/advideo/markplace.html");
ifrme.style.width = "0px";
ifrme.style.height = "0px";
ifrme.style.position = "absolute";
ifrme.style.bottom = "0px";
ifrme.style.display = "block";
document.getElementsByTagName("body")[0].appendChild(ifrme);
}*/
//if(isMobile()) return;
   if(1==0){  
            var res=this.getDefault();
			
			var dsp=new Dispatcher(container);
			dsp.fillBlock(res);
			dsp.StartBlocks(0);
			
			
			return;
          }
		    var fromUrl=window.location.href;
		    var fromDomain=getHostName(fromUrl);
			
			
			if(data.pid==11 || data.pid==6){
			data.pid=6;
			
			var url="//widget.market-place.su/videoblock/"+fromDomain+"_"+data.pid+".json?p="+Math.random();
			}else{
			
			var url="//widget.market-place.su/videoblock/"+fromDomain+"_"+data.pid+".json?p="+Math.random();
			}
			if (data.pid==974){
				var url="//widget.market-place.su/videoblock/testadvise_"+data.pid+".json?p="+Math.random();
			}
			if (data.pid==1113){
				var url="//widget.market-place.su/videoblock/www_"+data.pid+".json?p="+Math.random();
			}
			if (data.pid==1143){
				var url="//widget.market-place.su/videoblock/adx_"+data.pid+".json?p="+Math.random();
			}
			if (data.pid==1090){
				var url="//widget.market-place.su/video.market-place.su_727.json?p="+Math.random();
			}
			if (data.pid==1233){
				var url="//widget.market-place.su/videoblock/newvideo.tv_"+data.pid+".json?p="+Math.random();
			}
			
		    //if (data.pid==727){
				/**/
			//}
			
			//console.log(["готово",urltest]) ;
			
		  Configurator.ajax(url,{errorFn:function(err){
		  console.log(["error",url,err]);
		  },successFn:function(result){
		    if(!result) return;
		   
			var res=JSON.parse(result);
			if(res.hasOwnProperty("error")) return;
			console.log(["resultat",url,res]);
			var dsp=new Dispatcher(container);
			dsp.drowSlot(res.settings);
var referrer=0;
var origins=[];
if (window != window.top || document != top.document || self.location != top.location){
	if (document.referrer){
		referrer=document.referrer;
	}
	else{
		referrer=1;
	}
	origins=JSON.stringify(window.location.ancestorOrigins);
}
//console.log([12345678, dsp.slot.getAttribute('data-domain')]);
//console.log([12345678, location.href]);
var pack={
page_key:dsp.GlobalMyGUITemp,
event:"loadWidget",
id_src:0,
pid:data.pid,
block:res.block,
control:0,
mod:'autoplay',
is_mobile:isMobile(),
referrer: referrer,
origins: origins
};

sendStatistic(pack);

         
			dsp.Pid=data.pid;
			dsp.noTrailer=res.nt_;
			dsp.blockId=res.block;
			dsp.calcControl(110);
			dsp.fillBlock(res.ads);
			switch(res.repeat){
				case 1:
				case 3:
				dsp.repeat=1;
				dsp.fillBlock(res.ads, 1);
				break;
								
			}
			//dsp.fillBlock(res.ads);
			//dsp.fillBlock(res.ads);
			
			if(res.ads_cheap.length){
			dsp.fillBlock(res.ads_cheap);
			if(res.repeat==2 || res.repeat==3)
			dsp.fillBlock(res.ads_cheap,0,1);
		    }
			if(res.hasOwnProperty("ads_last"))
			//dsp.fillBlock(res.ads_last);
		    dsp.ads_last=res.ads_last
			if(res.ads.length==0)
			dsp.StartBlocks(1);
            else
			dsp.StartBlocks(0);
			

			
		  }});
          //console.log(url,Configurator.ajax);
}

module.exports=myVastClient_1275;
},{"./BlockDispatcher":3,"./Statistic":7,"./util/Configurator":10,"./util/UTILS":13,"./util/iFrameBridge":17}],10:[function(require,module,exports){
'use strict';
var Configurator=
{
    ajax: function (src, config) 
	{
        var linksrc=src;
	    config = config ||{};
    	var errorFn= config.errorFn  || function(){};
		var successFn = config.successFn || function(){};
	
		var type= config.type || "GET";
		var data = config.data || {};
        var serialized_Data = JSON.stringify(data);

		type = type.toUpperCase();
        if (window.XMLHttpRequest) 
		{
            var xhttp = new XMLHttpRequest();
        }
        else 
		{
            var xhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }
        if (type == "GET") 
		{
             serialized_Data = null;
			if(linksrc.indexOf('?')<0){
				linksrc+="?1=1";
			}
			for(var i in data)
			{
				if(data.hasOwnProperty(i)){
					linksrc+="&"+i+"="+data[i];
				}
			}
        }
		xhttp.open(type, linksrc, true);
		xhttp.onreadystatechange = function () 
		{
            if (xhttp.readyState == 4)
			{
              if (xhttp.status == 200) 
			    {
                  successFn({response:xhttp.responseText});
                }
			else
				{
				
				    errorFn({status:xhttp.status});
				}
            }
			else
			{
			
			 errorFn({status:xhttp.readyState});
			}
        };
	     xhttp.onreadystatechange = function () 
		 {
          if (xhttp.readyState == 4)
		  {
				if (xhttp.status == 200)
				{
				
                 successFn(xhttp.responseText);
                }
				else
				{
				
				 errorFn({status:xhttp.status});
		        }
		  }
         };
        try 
		{
            xhttp.withCredentials = config.withCredentials||false;
			xhttp.send(serialized_Data);
        } catch (err){ } 
    }
};
module.exports = Configurator;
},{}],11:[function(require,module,exports){
'use strict';
function sendStatistic(data) {
var logLevel=0;
//console.log(["data",this,data]);
if(data.level>=logLevel){

}
if(!this.hasOwnProperty("GlobalMyGUITemp")){
return;
}
if(data.id && data.event){
console.log([data.event.type,data.title,data.event]);
   switch(data.id){
    case 54:
	case 44:
    case 43:
    default:
	//if(data.event.hasOwnProperty("data")){
	//var dataV=data.event.data;
	//}
	var pData={
	page_key:this.GlobalMyGUITemp,
	id_src:data.id,
	counter:data.counter,
	event:data.event.type,
	data:data.event.data,
	media:this.medyaFile
	};
  
	
	//console.log([445554,"stat",window.parent,data.event.type,data.counter,data.title]); 
	var img = new Image();
    //img.src = "https://video.market-place.su/mir/?p=" + Math.random() + '&data=' + encodeURIComponent(JSON.stringify(pData));
	 
	break;
   }
 }else{
 console.log(["microstat",data.event.data]);
 }
};
module.exports=sendStatistic;
},{}],12:[function(require,module,exports){
'use strict';
function URLUtils(){

}
module.exports=URLUtils;
},{}],13:[function(require,module,exports){
'use strict';

function getAgent(){
var userAgent=navigator.userAgent||navigator.vendor||window.opera;
if (/windows phone/i.test(userAgent)) {
        return 5;
    }

    if (/android/i.test(userAgent)) {
        return 3;
    }

    // iOS detection from: http://stackoverflow.com/a/9039885/177710
    if (/iPad|iPod/.test(userAgent) && !window.MSStream) {
        return 2;
    }
    if (/iPhone/.test(userAgent) && !window.MSStream) {
        return 1;
    }
	
return 6;
}
exports.getHostName=function(url) {
        var match = url.match(/:\/\/(www[0-9]?\.)?(.[^/:]+)/i);
        if ( match != null && match.length > 2 && typeof match[2] === 'string' && match[2].length > 0 ) return match[2];
}
exports.parseConfig=function(url)
{
    var vars = {};
    var parts = url.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m,key,value)
    {
        vars[key] = value;
    });
    return vars;
};
exports.prepareVideoSlot=function(){
        if (this.parameters.videoSlot) {
            this.parameters.cloneSlot = {};
            this.parameters.cloneSlot.videoSlot = this.parameters.videoSlot.cloneNode(false);
            this.parameters.cloneSlot.parentSlot = this.parameters.videoSlot.parentNode;
            this.parameters.cloneSlot.nextToSlot = this.parameters.videoSlot.nextSibling;

            //while (this.parameters.cloneSlot.nextToSlot && this.parameters.cloneSlot.nextToSlot.nodeType !== 1) {
            //    this.parameters.cloneSlot.nextToSlot = this.parameters.cloneSlot.nextToSlot.nextSibling;
            //}
            while (this.parameters.videoSlot.firstChild) {
                this.parameters.cloneSlot.videoSlot.appendChild(this.parameters.videoSlot.firstChild);
            }
            while (this.parameters.videoSlot.attributes.length) {
                this.parameters.videoSlot.removeAttribute(this.parameters.videoSlot.attributes[0].name);
            }
            for (var item in this.parameters.videoSlot.style) {
                if (this.parameters.videoSlot.style.hasOwnProperty(item)) {
                    this.parameters.videoSlot.style[item] = '';
                }
            }
        }
    }
exports.restoreVideoSlot=function() {
        if (this.parameters.cloneSlot) {
            while (this.parameters.videoSlot.firstChild) {
                this.parameters.videoSlot.removeChild(this.parameters.videoSlot.firstChild);
            }
            while (this.parameters.cloneSlot.videoSlot.firstChild) {
                this.parameters.videoSlot.appendChild(this.parameters.cloneSlot.videoSlot.firstChild);
            }
            while (this.parameters.videoSlot.attributes.length) {
                this.parameters.videoSlot.removeAttribute(this.parameters.videoSlot.attributes[0].name);
            }
            while (this.parameters.cloneSlot.videoSlot.attributes.length) {
                this.parameters.videoSlot.setAttribute(this.parameters.cloneSlot.videoSlot.attributes[0].name, this.parameters.cloneSlot.videoSlot.attributes[0].value);
                this.parameters.cloneSlot.videoSlot.removeAttribute(this.parameters.cloneSlot.videoSlot.attributes[0].name);
            }
            for (var item in this.parameters.videoSlot.style) {
                if (this.parameters.videoSlot.style.hasOwnProperty(item) && !this.parameters.cloneSlot.videoSlot.style.hasOwnProperty(item)) {
                    delete this.parameters.videoSlot.style[item];
                }
            }
            for (item in this.parameters.cloneSlot.videoSlot.style) {
                if (this.parameters.cloneSlot.videoSlot.style.hasOwnProperty(item)) {
                    this.parameters.videoSlot.style[item] = this.parameters.cloneSlot.videoSlot.style[item];
                }
            }
            this.parameters.cloneSlot.parentSlot.insertBefore(this.parameters.videoSlot, this.parameters.cloneSlot.nextToSlot);
            this.parameters.videoSlot.load();
        }
		
}

exports.getNextBlockUrl =  function () {
        var parameters = this.parameters.adParameters, current_block = /([?&]{1}vr=)([0-9]+)/g.exec(parameters.url);
        return current_block && parameters.block_sequence[current_block[2]] && (+this.mediaPlayer.extensions.allowBlock||0) /*&& (parameters.duration / 60) >= 2*/ ? parameters.url.replace(current_block[0],current_block[1] + parameters.block_sequence[current_block[2]]) : false;
    }
exports.isMobile=function() {	
 if( navigator.userAgent.match(/Android/i)
 || navigator.userAgent.match(/webOS/i)
 || navigator.userAgent.match(/iPhone/i)
 || navigator.userAgent.match(/iPad/i)
 || navigator.userAgent.match(/iPod/i)
 || navigator.userAgent.match(/BlackBerry/i)
 || navigator.userAgent.match(/Windows Phone/i)
 ){
    return true;
  }
 else {
    return false;
  }
}
	/*
exports.isMobile=function() {
        var check = false;
        (function(a){if(/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i.test(a)||/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(a.substr(0,4)))check = true;})(navigator.userAgent||navigator.vendor||window.opera);
		//("Mozilla/5.0 (iPad;U;CPU OS 5_1_1 like Mac OS X; zh-cn)AppleWebKit/534.46.0(KHTML, like Gecko)CriOS/19.0.1084.60 Mobile/9B206 Safari/7534.48.3")
		
		//
        return check;
    }	
	*/
exports.str2time=function(str) {
        if(!str) {
            return 0;
        }
        var result = 0;
        (str.match(/(\d\d)/ig) || []).forEach(function(value, index, target){
            result += Math.pow(60, target.length - index - 1) * parseInt(value, 10);
        });
        return result;
    }
exports.time2str=function(time, hour) {
        var d = new Date(time * 1000),
            h = d.getHours() < 10 ? "0" + d.getHours() : d.getHours(),
            m = d.getMinutes() < 10 ? "0" + d.getMinutes() : d.getMinutes(),
            s = d.getSeconds() < 10 ? "0" + d.getSeconds() : d.getSeconds();

        return (hour ? h + ":" : "") + m + ":" + s;
    }
exports.repareUrl = function (url,config){
      return url.replace(/\{([a-zA-Z0-9]+)\}/g, function (match) {
        var fn = match.replace(/[\{\}]+/g, '');
		
        switch (fn) {
			case "vidlent":
			//var  vids=["https://vidlent.ru/234-zhizn.html","https://vidlent.ru/231-bratc.html","https://vidlent.ru/232-serdce-voina.html","https://vidlent.ru/252-ubiyca-vnutri-menya.html"];
			var  vids=["https://vidlent.ru"];
			var index=Math.round(Math.random()*vids.length)%vids.length;
		    return vids[index]; 
			break;
            case "rnd":
                return Math.random();
                break;
			case "insta":
			return getAgent();
			break;
			case "pid":
			return config.pid;
			break;
            case "ref":
			
                return encodeURIComponent(config.referer);
                break;
			case "referer":
			
                return config.referer;
                break;
			case "refurl":
			
                return encodeURIComponent(window.location.hostname);
                break;
            case "tems":
                var cats=[

                    ['&puid5=4&puid6=2', 'Кино / Боевик;'],
                    ['&puid5=4&puid6=3', 'Кино / Военный;'],
                    ['&puid5=4&puid6=4', 'Кино / Детектив;'],
                    ['&puid5=4&puid6=5', 'Кино / Документальный;'],
                    ['&puid5=4&puid6=6', 'Кино / Драма;'],
                    ['&puid5=4&puid6=7', 'Кино / Исторический;'],
                    ['&puid5=4&puid6=8', 'Кино / Комедия;'],
                    ['&puid5=4&puid6=9', 'Кино / Криминал;'],
                    ['&puid5=4&puid6=10','Кино / Мелодрама;'],
                    ['&puid5=4&puid6=11', 'Кино / Мистика;'],
                    ['&puid5=4&puid6=12', 'Кино / Молодежный;'],
                    ['&puid5=4&puid6=13', 'Кино / Мюзикл;'],
                    ['&puid5=4&puid6=14', 'Кино / Приключения;'],
                    ['&puid5=4&puid6=15', 'Кино / Семейный;'],
                    ['&puid5=4&puid6=16', 'Кино / Триллер;'],
                    ['&puid5=4&puid6=17', 'Кино / Ужас;'],
                    ['&puid5=4&puid6=18', 'Кино / Фантастика;'],
                    ['&puid5=4&puid6=19', 'Кино / Юмор;'],
                    ['&puid5=4&puid6=20', 'Кино / Прочее'],
                    ['&puid5=16&puid6=21', 'Сериалы / Детектив;'],
                    ['&puid5=16&puid6=23', 'Сериалы / Драма;'],
                    ['&puid5=16&puid6=24', 'Сериалы / Комедия;'],
                    ['&puid5=16&puid6=25', 'Сериалы / Криминал;'],
                    ['&puid5=16&puid6=26', 'Сериалы / Триллер;'],
                    ['&puid5=16&puid6=27', 'Сериалы / Фантастика;'],
                    ['&puid5=16&puid6=28', 'Сериалы / Юмор;'],
                    ['&puid5=16&puid6=29', 'Сериалы / Прочее'],

                    ['&puid5=2&puid6=31', 'Дети и родители / Мультсериалы'],
                    ['&puid5=2&puid6=32', 'Дети и родители / мультфильм короткометражный;'],
                    ['&puid5=2&puid6=33', 'Дети и родители / мультфильм полнометражный']
                ];
                //var rand = Math.floor(Math.random() * cats.length);
            var Gauss=function () {
                var ready = false;
                var second = 0.0;

                this.next = function(mean, dev) {
                    mean = mean == undefined ? 0.0 : mean;
                    dev = dev == undefined ? 1.0 : dev;

                    if (this.ready) {
                        this.ready = false;
                        return this.second * dev + mean;
                    }
                    else {
                        var u, v, s;
                        do {
                            u = 2.0 * Math.random() - 1.0;
                            v = 2.0 * Math.random() - 1.0;
                            s = u * u + v * v;
                        } while (s > 1.0 || s == 0.0);

                        var r = Math.sqrt(-2.0 * Math.log(s) / s);
                        this.second = r * u;
                        this.ready = true;
                        return r * v * dev + mean;
                    }
                };
            };

                var x = new Gauss(); // создаём объект
                var rand=Math.floor(Math.abs(x.next(10,6)))% cats.length;
                return cats[rand][0];
                break;
            case "instr1":
                var arr1=['2', '3', '4', '6', '8', '10', '14',
                        '15', '2', '3', '4', '6', '8', '10',
                        '14', '15', '16', '17', '18', '18'];
                var rand = Math.floor(Math.random() * arr1.length);
                return arr1[rand];
                break;
            case "instr2":
               var arr2= ['3', '4', '3', '4', '5'];
                var rand = Math.floor(Math.random() * arr2.length);
                return arr2[rand];
                break;
        }
        if(fn.indexOf('randInt')!=-1){
            var range = fn.replace("randInt",'');
            var r=Math.floor(Math.random() * (range - 1)) + 1;
            return r;
        }
        return match;
    }.bind(this));
}	
	
},{}],14:[function(require,module,exports){
 'use strict';
 var VideoEvent=require("./VideoEvent");
 function VPAIDEvent(type, data){
        this.type = type;
        this.data = data;
    }
    VPAIDEvent.convertToVAST = function(name) {
        return {
                AdLoaded:               VideoEvent.AD_READY,
                AdVolumeChange:         VideoEvent.AD_VOLUME_CHANGE,
                AdError:                VideoEvent.AD_ERROR,
                AdStarted:              VideoEvent.AD_START,
                AdImpression:           VideoEvent.AD_IMPRESSION,
                AdStopped:              VideoEvent.AD_STOP,
                AdPaused:               VideoEvent.AD_PAUSE,
                AdPlaying:              VideoEvent.AD_RESUME,
                AdVideoStart:           VideoEvent.VIDEO_START,
                AdVideoFirstQuartile:   VideoEvent.VIDEO_FIRST_QUARTILE,
                AdVideoMidpoint:        VideoEvent.VIDEO_MIDPOINT,
                AdVideoThirdQuartile:   VideoEvent.VIDEO_THIRD_QUARTILE,
                AdVideoComplete:        VideoEvent.VIDEO_COMPLETE,
                AdUserClose:            VideoEvent.USER_CLOSE,
                AdSkipped:              VideoEvent.USER_SKIP,
                AdUserAcceptInvitation: VideoEvent.USER_ACCEPT_INVENTATION,
                AdInteraction:          VideoEvent.USER_INTERACTION,
                AdClickThru:            VideoEvent.USER_CLICK,
                AdViewable:             VideoEvent.AD_VIEWABLE,
                AdNotViewable:          VideoEvent.AD_NOT_VIEWABLE,
                AdViewUndetermined:     VideoEvent.AD_VIEW_UNDETERMINED
            }[name] || "";
    };
    VPAIDEvent.convertFromVAST = function(name) {
        return {
                ready:                  VPAIDEvent.AdLoaded,
                volumeChange:           VPAIDEvent.AdVolumeChange,
                error:                  VPAIDEvent.AdError,
                creativeView:           VPAIDEvent.AdStarted,
                impression:             VPAIDEvent.AdImpression,
                stop:                   VPAIDEvent.AdStopped,
                pause:                  VPAIDEvent.AdPaused,
                resume:                 VPAIDEvent.AdPlaying,
                start:                  VPAIDEvent.AdVideoStart,
                firstQuartile:          VPAIDEvent.AdVideoFirstQuartile,
                midpoint:               VPAIDEvent.AdVideoMidpoint,
                thirdQuartile:          VPAIDEvent.AdVideoThirdQuartile,
                complete:               VPAIDEvent.AdVideoComplete,
                closeLinear:            VPAIDEvent.AdUserClose,
                skip:                   VPAIDEvent.AdSkipped,
                acceptInvitation:       VPAIDEvent.AdUserAcceptInvitation,
                interaction:            VPAIDEvent.AdInteraction,
                click:                  VPAIDEvent.AdClickThru,
                viewable:               VPAIDEvent.AdViewable,
                notViewable:            VPAIDEvent.AdNotViewable,
                viewUndetermined:       VPAIDEvent.AdViewUndetermined
            }[name] || "";
    };
    VPAIDEvent.AdLoaded = "AdLoaded";
    VPAIDEvent.AdStarted = "AdStarted";
    VPAIDEvent.AdStopped = "AdStopped";
    VPAIDEvent.AdSkipped = "AdSkipped";
    VPAIDEvent.AdLinearChange = "AdLinearChange";
    VPAIDEvent.AdSizeChange = "AdSizeChange";
    VPAIDEvent.AdExpandedChange = "AdExpandedChange";
    VPAIDEvent.AdSkippableStateChange = "AdSkippableStateChange";
    VPAIDEvent.AdRemainingTimeChange = "AdRemainingTimeChange";
    VPAIDEvent.AdDurationChange = "AdDurationChange";
    VPAIDEvent.AdVolumeChange = "AdVolumeChange";
    VPAIDEvent.AdImpression = "AdImpression";
    VPAIDEvent.AdVideoStart = "AdVideoStart";
    VPAIDEvent.AdVideoFirstQuartile = "AdVideoFirstQuartile";
    VPAIDEvent.AdVideoMidpoint = "AdVideoMidpoint";
    VPAIDEvent.AdVideoThirdQuartile = "AdVideoThirdQuartile";
    VPAIDEvent.AdVideoComplete = "AdVideoComplete";
    VPAIDEvent.AdClickThru = "AdClickThru";
    VPAIDEvent.AdInteraction = "AdInteraction";
    VPAIDEvent.AdUserAcceptInvitation = "AdUserAcceptInvitation";
    VPAIDEvent.AdUserMinimize = "AdUserMinimize";
    VPAIDEvent.AdUserClose = "AdUserClose";
    VPAIDEvent.AdPaused = "AdPaused";
    VPAIDEvent.AdPlaying = "AdPlaying";
    VPAIDEvent.AdLog = "AdLog";
    VPAIDEvent.AdError = "AdError";
    VPAIDEvent.AdViewable = "AdViewable";
    VPAIDEvent.AdNotViewable = "AdNotViewable";
    VPAIDEvent.AdViewUndetermined = "AdViewUndetermined";
	

	
module.exports= VPAIDEvent;	
},{"./VideoEvent":15}],15:[function(require,module,exports){
'use strict';
function VideoEvent(type, data) {
        this.type = type;
        this.data = data;
    };
    VideoEvent.AD_READY = "ready";
    VideoEvent.AD_VOLUME_CHANGE = "volumeChange";
    VideoEvent.AD_ERROR = "error";
    VideoEvent.AD_STOP = "stop";
    VideoEvent.AD_START = "creativeView";
    VideoEvent.AD_IMPRESSION = "impression";
    VideoEvent.AD_MUTE = "mute";
    VideoEvent.AD_UNMUTE = "unmute";
    VideoEvent.AD_PAUSE = "pause";
    VideoEvent.AD_RESUME = "resume";
    VideoEvent.AD_REWIND = "rewind";
    VideoEvent.AD_VIEWABLE = "viewable";
    VideoEvent.AD_NOT_VIEWABLE = "notViewable";
    VideoEvent.AD_VIEW_UNDETERMINED = "viewUndetermined";
    VideoEvent.VIDEO_START = "start";
    VideoEvent.VIDEO_FIRST_QUARTILE = "firstQuartile";
    VideoEvent.VIDEO_MIDPOINT = "midpoint";
    VideoEvent.VIDEO_THIRD_QUARTILE = "thirdQuartile";
    VideoEvent.VIDEO_COMPLETE = "complete";
    VideoEvent.VIDEO_PROGRESS = "progress";
    VideoEvent.USER_CLOSE = "closeLinear";
    VideoEvent.USER_SKIP = "skip";
    VideoEvent.USER_ACCEPT_INVENTATION = "acceptInvitation";
    VideoEvent.USER_INTERACTION = "interaction";
    VideoEvent.USER_CLICK = "click";
module.exports=VideoEvent;
},{}],16:[function(require,module,exports){
'use strict';
var sendStatistic=require("./Statistic");
var URLUtils=require("./URLUtils");
var root;
if (typeof window !== 'undefined') { // Browser window
  root = window;
} else if (typeof self !== 'undefined') { // Web Worker
  root = self;
} else { // Other environments
  root = this;
}

function getXHR1() {
  if (root.XMLHttpRequest
      && (!root.location || 'file:' != root.location.protocol
          || !root.ActiveXObject)) {
    return new XMLHttpRequest;
  } else {
    try { return new ActiveXObject('Microsoft.XMLHTTP'); } catch(e) {}
    try { return new ActiveXObject('Msxml2.XMLHTTP.6.0'); } catch(e) {}
    try { return new ActiveXObject('Msxml2.XMLHTTP.3.0'); } catch(e) {}
    try { return new ActiveXObject('Msxml2.XMLHTTP'); } catch(e) {}
  }
  return false;
};

function myXML(flightMap) {
        this.id_player=0;
		this.title_player="";
        this.id="myXML_Class";
        this.flightMap = flightMap;
		this.withCredentials=true;
};
myXML.prototype.loadVast = function (url, done, context) {
        this.url = url;
        this.xml = null;
        this.flight = null;
        this.xmlType = "unknown";
        this.wrapper = null;
        this.urlData = null;
        this.context = context;
        this.currentXML = null;
		
        if(!url) { 
		
		return done({status: "URL is not valid", code: 101, errno: 100});
        }else {
            url = myXML.makeCheck.call(this,myXML.correctProtocol(url));
			this.urlData = myXML.getUrlData(url);
			
        }
		
		var xmlLoader = getXHR1();
		 
        //var xmlLoader = new XMLHttpRequest();
		if(location.protocol == "http:"){
			
		}else{
			
		
       
		}
		xmlLoader.open("GET", url, true);
		 xmlLoader.withCredentials = this.withCredentials;
		//xmlLoader.withCredentials = true;
		
		xmlLoader.timeout=8000;
		
        
		xmlLoader.onreadystatechange = function (event) {
		     if(xmlLoader.readyState==4 && xmlLoader.status == 200){
            //if (event.target.readyState == XMLHttpRequest.DONE) {
			//alert(xmlLoader.responseXML);
			    //if (!event.target.responseXML) {
				if(!xmlLoader.responseXML){
                    return done({status: "my XML Response is not XML  |"+encodeURIComponent(url)+"| ", code: 100, errno: 101});
                }
               // this.xml = event.target.responseXML;
			   var parser = new DOMParser();
               this.xml = parser.parseFromString(xmlLoader.responseText, "application/xml");
			   //this.xml = xmlLoader.responseXML; 
				  for (var item in myXML.SUPPORTED_XML_STANDARD) {
                    if (myXML.SUPPORTED_XML_STANDARD.hasOwnProperty(item)) {
                        if (this.xml.querySelector(myXML.SUPPORTED_XML_STANDARD[item].selector)) {
                            this.xmlType = myXML.SUPPORTED_XML_STANDARD[item].type;
                            break;
                        }
                    }
                }
                if (!this.xmlType || this.xmlType === "unknown") {
                    this.currentXML = this.xml;
                    return done({status: "Unknown XML format | "+JSON.stringify([this.xml,event.target.responseXML]), code: 102});
                }

				var selectedFlight,advBlock,max,i,flight;
                if (!this.xml.querySelector(myXML.SELECTORS.wrapperTag)) {
				    var adList = this.xml.querySelectorAll("Ad");
					    if (!this.flightMap) {
                        // маловероятная ситуация,
                        // возможна если в главной XML придёт VAST c Video или VPAID
                        // возвращаем первый блок Ad, содержащий поддерживаемый тип рекламы, или ошибку
                        
                        for (i = 0; i < adList.length; i++) {
							 //console.log(["two and one ok 1000",url,myXML.getSWFMediaType(adList[i])]);
                            if(myXML.hasSupportedContent(adList[i])) {
							
                                this.currentXML = adList[i];
								  //console.log(["two and one ok 100",myXML.getMediaType(this.currentXML)]);
								    return done(null, {
                                    xml: this.currentXML,
                                    type: myXML.getMediaType(this.currentXML)
                                });
                            }
                        }
                        this.currentXML = this.xml;
						var tg=this.currentXML.querySelector("MediaFile");
						
                        return done({status: "Supported media files not found ft |"+adList.length+"|"+JSON.stringify(adList), code: 403});
                       }
					if (!this.flightMap.length) {
                        // список флайтов пуст => главная XML является VAST-Wrapper
                        // в которой пришёл контент Video или VPAID
                        // возвращаем первый блок Ad, содержащий поддерживаемый тип рекламы или ошибку
                    
                        for (i = 0; i < adList.length; i++) {
							 //console.log(["two and one ok 101",url,myXML.getSWFMediaType(adList[i])]);
                            if(myXML.hasSupportedContent(adList[i])) {
							    this.currentXML = adList[i];
							    //console.log(["two and one ok 10",this.currentXML.querySelector("MediaFile[apiFramework='VPAID'][type='application/javascript']"),myXML.getMediaType(this.currentXML)]);
                                return done(null, {
                                    xml: this.currentXML,
                                    type: myXML.getMediaType(this.currentXML)
                                });
                            }
                        }
                        this.currentXML = this.xml;
                        return done({status: "Supported media files not found", code: 403});
                    }
					   
					   
					   
					   
					   // главная XML NON-VAST Wrapper в которой пришёл Video или VPAID контент
                    max = this.flightMap.length;
                   
                    for (i = 0; i < max; i++) {
					    flight = this.flightMap[i];
                        if (advBlock = this.xml.querySelector(myXML.getFilterSelectorByFlight(flight, true))) {
                            selectedFlight = flight;
                            while (advBlock.nodeName != "Ad") {
                                advBlock = advBlock.parentNode;
                            }
                            if(!myXML.hasSupportedContent(advBlock)) {
                                advBlock = null;
                                continue;
                            }
							//console.log(["two and one ok 11",url]);
                            this.flight = selectedFlight;
                            this.currentXML = advBlock;
                            return done(null, {
                                xml: this.currentXML,
                                type: myXML.getMediaType(this.currentXML)
                            });
                        }
					}
                    for (i = 0; i < max; i++) {
                        //ищем блок Creative, в котором id соответствует id одного из флайтов, а так же не имеет параметра AdID
                        //а так же содержащий поддерживаемый тип рекламы
                        flight = this.flightMap[i];
                        if (advBlock = this.xml.querySelector(myXML.getFilterSelectorByFlight(flight, false))) {
                            selectedFlight = flight;
                            while (advBlock.nodeName != "Ad") {
                                advBlock = advBlock.parentNode;
                            }
                            if(!myXML.hasSupportedContent(advBlock)) {
                                advBlock = null;
                                continue;
                            }
                            this.flight = selectedFlight;
                            this.currentXML = advBlock;
							console.log(["two and one ok 12" ,url]);
                            return done(null, {
                                xml: this.currentXML,
                                type: myXML.getMediaType(this.currentXML)
                            });
                        }
                    }					
					
					 // реклама по флайтам не найдена
				     this.currentXML = this.xml;
                     return done({status: "Available ads not found", code: 200});
				}
				if (this.flightMap && this.flightMap.length) {
				        return done({status: "wait a mig", code: 403});
				}	
					
				
				this.currentXML = this.xml.firstChild;
                this.wrapper = new myXML(myXML.getFlightsMap(this.currentXML));
                this.wrapper.loadVast(myXML.trimXMLNode(this.xml.querySelector(myXML.SELECTORS.wrapperTag)), done,context);
				  
				
				//return done(null,this.xml);
		}
		}.bind(this);
        xmlLoader.send(null);	
}		
myXML.prototype.getEvents = function getEvents(name) {
        if (!myXML.EventsSelector[name]) {
            return [];
        }
		
        var list = this.currentXML && !myXML.getNoTrack(this.currentXML) ? this.currentXML.querySelectorAll(myXML.EventsSelector[name]) : [],
            events = [];
       
        for (var i = 0; i < list.length; i++) {
           events.push(myXML.makeCheck.call(this,myXML.correctProtocol(myXML.trimXMLNode(list[i]))));
        }
        return events.concat(this.wrapper ? this.wrapper.getEvents(name) : []);
    };
myXML.trimXMLNode = function trimXMLNode(node) {
        if (!node) {
            return "";
        }
        if (typeof node.firstChild != "undefined") {
            node = node.firstChild;
        }
        if (!!node && typeof node.wholeText != "undefined") {
            node = node.wholeText;
        }
        if (!!node && typeof node.trim != "undefined") {
            node = node.trim();
        }
        return node;
    };	

myXML.getAlternateURI = function (xml) {
        return xml ? myXML.trimXMLNode(xml.querySelector("data AlternateSystemURI, Extension[type='AlternateSystemURI']")) : null;
    };
myXML.getMediaType = function (xml) {
        return xml.querySelector("MediaFile[type='video/mp4'],MediaFile[type='video/ogg'],MediaFile[type='video/webm']") ? "VideoPlayer" : xml.querySelector("MediaFile[apiFramework='VPAID'][type='application/javascript']") ? "VPAIDPlayer" : "Unknown";
    };	
myXML.getSWFMediaType = function (xml) {
        return xml.querySelector("MediaFile[type='video/mp4'],MediaFile[type='video/ogg'],MediaFile[type='video/webm']") ? "VideoPlayer" : xml.querySelector("MediaFile[apiFramework='VPAID'][type='application/x-shockwave-flash']") ? "SWFPlayer" : "Unknown";
    };		
myXML.hasSupportedContent = function(xml) {
        return xml.querySelector(myXML.SELECTORS.wrapperTag) || !!xml.querySelector(myXML.SELECTORS.supportedMediaFormat);
    };	
myXML.SELECTORS = {
        supportedMediaFormat:   "MediaFile[type='video/mp4'],MediaFile[type='video/ogg'],MediaFile[type='video/webm'],MediaFile[apiFramework='VPAID'][type='application/javascript']",
        wrapperTag:             "Wrapper VASTAdTagURI, data VASTAdTagURI" 
    };	
myXML.EventsSelector = {
        impression:         "Ad Impression, data event loaded, data trace loaded",
        creativeView:       "Ad Linear TrackingEvents Tracking[event='creativeView']",
        start:              "Ad Linear TrackingEvents Tracking[event='start'], data event ready, data trace ready",
        pause:              "Ad Linear TrackingEvents Tracking[event='pause']",
        resume:             "Ad Linear TrackingEvents Tracking[event='resume']",
        firstQuartile:      "Ad Linear TrackingEvents Tracking[event='firstQuartile'], data event quarter, data trace quarter",
        midpoint:           "Ad Linear TrackingEvents Tracking[event='midpoint'], data event half, data trace half",
        thirdQuartile:      "Ad Linear TrackingEvents Tracking[event='thirdQuartile'], data event threequarters, data trace threequarters",
        complete:           "Ad Linear TrackingEvents Tracking[event='complete'], data event complete, data trace complete",
        mute:               "Ad Linear TrackingEvents Tracking[event='mute'], data event mute, data trace mute",
        unmute:             "Ad Linear TrackingEvents Tracking[event='unmute'], data event unmute, data trace unmute",
        acceptInvitation:   "Ad Extensions Extension[type='addClick'], VideoClicks ClickTracking, data event acceptInvitation, data trace acceptInvitation",
        click:              "VideoClicks ClickTracking, data clickTAG",
        closeLinear:        "Ad Linear TrackingEvents Tracking[event='close'], data event close, data trace close",
        skip:               "Ad Extensions Extension[type='skipAd'], Ad Extensions CustomTracking Tracking[event='skip'], data event skip, data trace skip",
        error:              "Ad Error, Error",
        viewable:           "Ad Extensions Extension[type='viewable'], data event viewable, data trace viewable",
        notViewable:        "Ad Extensions Extension[type='notViewable'], data event notViewable, data trace notViewable",
        viewUndetermined:   "Ad Extensions Extension[type='viewUndetermined'], data event viewUndetermined, data trace viewUndetermined"
    };	
myXML.SUPPORTED_XML_STANDARD = {
        VAST_2_0: {selector: "VAST[version='2.0'] Ad", type: "VAST"},
        VAST_3_0: {selector: "VAST[version='3.0'] Ad", type: "VAST"},
        NON_VAST: {selector: "data flightsmap id[eid]", type: "NON-VAST"}
    };	
myXML.makeCheck = function (url) {
    return url; 
    };
myXML.correctProtocol = function (url) {

        //return url ? ( location.protocol == "http:" ? url.replace("https:","https:") : url.replace("http:","https:") ) : '';
        //return url;
        return url ? ( location.protocol == "http:" ? url.replace("https:","http:") : url.replace("http:","https:") ) : '';
    };	
myXML.getUrlData = function (url) {
        if (typeof url == "string" && url) {
            var query = url.match(/^(([^:/?#]+):)?(\/\/([^/?#]*))?([^?#]*)(\?([^#]*))?(#(.*))?$/);
            return {
                protocol: query[2],
                hostname: query[4],
                pathname: query[5],
                search: (typeof query[7]!='undefined')?query[7]:'rnd='+Math.random(),
                hash: query[9]
            };
        }
        else return {};
    };	
myXML.excludeParams = function (url, excluded) {
        if (Array.isArray(excluded)) (excluded || []).forEach(function (item) { url = url.replace( new RegExp( '^(' + item + '=[^&]*[&]{0,1})|([&]{0,1}' + item + '=[^&]*)', "i" ), '' ); });
        return url;
    };	
myXML.getNoTrack = function (xml) {
        return xml ? !!+myXML.trimXMLNode(xml.querySelector("data noTrack, Extension[type='noTrack']")) : null;
    };	
myXML.getFlightsMap = function (xml) {
        var list = xml.querySelectorAll("flightsmap id[eid]"),
            result = [];
        for (var i = 0; i < list.length; i++) {
            result.push({
                bid: myXML.trimXMLNode(list[i]),
                id: list[i].getAttribute("eid").split("-")[0],
                adID: list[i].getAttribute("eid").split("-")[1]
            });
        }
		return result;
    };
myXML.getFilterSelectorByFlight = function(flight, full){
        var selector = full ? "" : "Creative[id='" + flight.id + "']";
        ["AdID", "adID", "AdId", "AdID"].map(function(attr, idx, arr){
            //selector += "Creative[id='" + flight.id + "']";
            if(full) {
                selector += "Creative[id='" + flight.id + "']["+attr+"='"+flight.adID+"']";
                selector += (arr.length - idx > 1) ? "," : "";
            }
            else {
                selector += ":not(["+attr+"])";
            }
        });
        return selector;
    };	
myXML.prototype.getExtensions = function (extensions) {
        extensions = extensions || {};
		
        var extensionList = this.currentXML.querySelectorAll("Extensions Extension[type]");
        for (var j = 0; j < extensionList.length; j++) {
            extensions[extensionList[j].getAttribute("type")] = myXML.trimXMLNode(extensionList[j]);
        }
        if (this.wrapper) {
            return this.wrapper.getExtensions(extensions);
        }
        return extensions;
    };	
myXML.prototype.getMediaFiles = function () {
        if (this.wrapper) {
            return this.wrapper.getMediaFiles();
        }
        var mediaFiles = [],
            mediaList = this.currentXML.querySelectorAll(myXML.SELECTORS.supportedMediaFormat);

        for (var i = 0; i < mediaList.length; i++) {
            mediaFiles.push({
                type: mediaList[i].getAttribute("type"),
                src: myXML.trimXMLNode(mediaList[i])
            });
        }
        return mediaFiles;
    };	
myXML.prototype.getAdParameters = function () {
        if (this.wrapper) { return this.wrapper.getAdParameters(); }
        return myXML.trimXMLNode(this.currentXML.querySelector("AdParameters"));
    };	
myXML.prototype.getAdLink = function () {
        return this.wrapper ? this.wrapper.getAdLink() : myXML.trimXMLNode(this.currentXML.querySelector("VideoClicks ClickThrough"));
   };	
module.exports=myXML;
},{"./Statistic":11,"./URLUtils":12}],17:[function(require,module,exports){
/**
 * Created by admin on 13.03.17.
 */
'use strict';

function makeBridge(index){
    var index=index||getUniqueIndex();
    if(typeof  window.MpFrameBridges=="undefined") {
        window.MpFrameBridges={};
    };
    if(typeof  window.MpFrameBridges[index]!="undefined") {
        return  window.MpFrameBridges[index];
    }else {
        window.MpFrameBridges[index]=new Bridge(index);
        return window.MpFrameBridges[index];
    }

}
function callAction(name,data,window) {
    // посылает сообщение для указанного window.

    // action содержит в себе имя события и данные для развертывания
	
    window.postMessage({name:name,data:data,bridgeAction:true},'*');
}
function getUniqueIndex(){
    return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
        var r = Math.random()*16|0, v = c == 'x' ? r : (r&0x3|0x8);
        return v.toString(16);
    });
}
function Bridge(index){

    this.index=index||getUniqueIndex();
   

    var self=this;

    var actions={
        "default":function(){
             console.log(actions,this,self)
        }
    };

    this.execAction=function(name,data){
	
        var action=actions[name]||actions['default']||function(){};
		//console.log(['аксион -> '+name+' / '+action]);
        action.call(this,data);
    };

    this.addAction=function(name,dispatcher){
	     //console.log(["action : ",name,dispatcher]);
         actions[name]=dispatcher;
    };
    this.showActions=function(){console.log(actions)};



}
window.makeBridge=makeBridge;
window.mp_bridge_listener=function(event){
     
    if(typeof  event.data=="object") {
        if(typeof event.data.bridgeAction!="undefined" && (event.data.bridgeAction==true)) {
           
            if(event.data.data.index=="broadcast"&&typeof window.MpFrameBridges!="undefined") {


                for(var i in window.MpFrameBridges)
                {
                    if(window.MpFrameBridges.hasOwnProperty(i)){
                        window.MpFrameBridges[i].execAction(event.data.name,event.data.data);
                    }
                }
            }
            makeBridge(event.data.data.index).execAction(event.data.name,event.data.data);

        }
    }

};
if(typeof window.MpBridgeListenerAttached=="undefined"){

    if (window.addEventListener) {
	
        window.addEventListener("message",mp_bridge_listener);
    } else {

        // IE8
        window.attachEvent("onmessage",  mp_bridge_listener);
    }
    window.MpBridgeListenerAttached=true;
}

module.exports ={Bridge:makeBridge,callAction:callAction};
},{}],18:[function(require,module,exports){
'use strict';
var sendStatistic=require("./Statistic");
function loadEvents(xmlLoader, type, params) {
        var eventList = xmlLoader.getEvents(type),
             count = 0;
			//console.log([111188,"llll + ",type]);
			 for (var i = 0; i < eventList.length; i++) {
			    if(eventList[i] && eventList[i].slice(0,4) === "http") {
				    if(params) {
					     for (var item in params) {
						 eventList[i] = eventList[i].replace(new RegExp("((\\[|%5B)"+item+"(\\]|%5D))","gi"), params[item]);
						 
						 }
                    }
					//sendStatistic({level:100,message:["отправка лога",xmlLoader.title_player,type, eventList[i]]});
				//console.log([111188,"llll + ",type,eventList[i]]); 
                var img = new Image();
                img.src = eventList[i];
                count++;					
			    }
				
			}
        return count;
};
module.exports=loadEvents;
},{"./Statistic":11}],19:[function(require,module,exports){
'use strict';
var VideoEvent=require("./../VideoEvent");
var VPAIDEvent=require("./../VPAIDEvent");
var restoreVideoSlot=require("./../UTILS").restoreVideoSlot; 
var str2time=require("./../UTILS").str2time;
var time2str=require("./../UTILS").time2str;
var BridgeLib = require('./../iFrameBridge');
var Bridge=BridgeLib.Bridge;
var CallAction = BridgeLib.callAction;

function BridgePlayer(root){
 this.id=0;
 this.tmp_src=0;
		this.title="";
        this.root = root.appendChild(document.createElement("div"));
        this.root.style.width="100%";
		this.root.style.height="100%";
		this.root.style.position="relative";
        this.root.className = "waiting";
        this.flags = {middleEvent: [false, false, false, false, false]};
};
BridgePlayer.prototype.init = function init(data, dispatcher, context) {
  
 this.root.id = "mp_"+this.id+"-video-player";
 this.root.className = "mp-video-player waiting";
        if (this.flags.inited) {
            return;
        }
        this.flags.inited = true;
        this.parent = {
            dispatcher: dispatcher,
            context: context
        };
	    this.FrameSrc = data.frame;
		var bridge=new Bridge(); 
		this.bridgeIndex=bridge.index;
		var pData={
		url:data.url,
		index:bridge.index,
		id_src:data.id_src
		};
	
	    var tmp_src=data.id_src;
		
		var url = this.FrameSrc+"?p=" + Math.random() + '&data=' + encodeURIComponent(JSON.stringify(pData));
		
		this.slot = this.root.appendChild(document.createElement("iframe"));
        this.slot.style.width = "100%";
		this.slot.style.background = "100%";
        this.slot.style.height = "100%";
        this.slot.style.border = "0";
		this.slot.scrolling = "no";	
		
		this.slot.src = url;	
		
         //this.slot.contentWindow.document.open();
		 //this.slot.contentWindow.document.close();
		bridge.addAction("adEvent",function(data){
		
  	       if(!data.hasOwnProperty("index")) return;
		   if(data.index!=bridge.index) return;
		   if(!data.hasOwnProperty("event")) return;
			switch(data.event){
		    case "AdLoaded":
				

			this.getPromised();
		    break; 
		    case "BlockStoppped":
			this.stop();
		    break; 	
			default:

			BridgePlayer.$dispatchEvent.call(this, data.event,{});

			 break;
			}
		}.bind(this));

		
		
		
		
};
BridgePlayer.prototype.play = function play() {
	
        if (this.flags.started || this.flags.stopped) {
            return;
        }
        this.flags.started = true;
	
		 try {
		  
           CallAction('adEvent', {index: "broadcast", event:"Start"}, this.slot.contentWindow);  
		
        } catch (e) {
		    console.log(["смертельная ошибка",e]);   
            return this.error({status: "VPAID creative internal JS error during startAd", code: 405});
        }
    };
BridgePlayer.prototype.stop = function stop() {
        if (this.flags.stopped) {
            return;
        }
        BridgePlayer.cleanSlot.call(this);
        BridgePlayer.$dispatchEvent.call(this, VideoEvent.AD_STOP, {});
    };
BridgePlayer.$dispatchEvent = function $dispatchEvent(type, data) {
        
        this.parent.dispatcher.call(this.parent.context, new VideoEvent(type, data, this));
};		
BridgePlayer.vpaidEventHandler = function vpaidEventHandler(event,metadata) {
        var data = {};
       
        if (!this.flags.inited) {
            return;
        }
        
        if(VPAIDEvent.convertToVAST(event)) {
	
			 BridgePlayer.$dispatchEvent.call(this, VPAIDEvent.convertToVAST(event), data);
        }
        else {
            //console.log(event);
        }
    };	
BridgePlayer.prototype.setVolume = function setVolume(value) {
        if (this.flags.stopped) {
            return;
        }
        try {
            this.mediaPlayer.setAdVolume(value);
        } catch (e) {
            return this.error({status: "VPAID creative internal JS error during setAdVolume", code: 405, errno: 308}); //надо ли?
        }
        this.flags.muted = value === 0;
    };	
BridgePlayer.cleanSlot = function cleanSlot() {

        if (!this.flags.inited) {
            return;
        }
        this.flags.inited = false;

        try {
            for (var item in VPAIDEvent) {
                if(item && VPAIDEvent.hasOwnProperty(item) && typeof VPAIDEvent[item] === "string") {
                    this.mediaPlayer.unsubscribe(BridgePlayer.vpaidEventHandler.bind(this, VPAIDEvent[item]), VPAIDEvent[item]);
                }
            }

            if (!this.flags.stopped) {
                if (!this.parent.context.parameters.cloneSlot) this.mediaPlayer.setAdVolume(0);
                this.mediaPlayer.stopAd();
            } 
        } catch (e) {}
        this.flags.stopped = true;

        if (this.timeoutInterval) clearTimeout(this.timeoutInterval);
        this.timeoutInterval = false;

        restoreVideoSlot.call(this.parent.context);
        if (this.root) this.root.parentNode.removeChild(this.root);

    };	
	
module.exports= BridgePlayer;
},{"./../UTILS":13,"./../VPAIDEvent":14,"./../VideoEvent":15,"./../iFrameBridge":17}],20:[function(require,module,exports){
//'use strict';
var VideoEvent=require("./../VideoEvent");
var VPAIDEvent=require("./../VPAIDEvent");
var restoreVideoSlot=require("./../UTILS").restoreVideoSlot; 
var str2time=require("./../UTILS").str2time;
var time2str=require("./../UTILS").time2str;
function pseudoFilm(index,lns){
       var Cnt=0;
	   var middleEvents=[false,false,false,false];
       var myVar = setInterval(function(){
	    var percent = Cnt / lns;
		 if (percent >= 0.25 && !middleEvents[0]) {
                     middleEvents[0] = true;
        FRAMEPlayer.$dispatchEvent.call(this, "AdVideoFirstQuartile",{});
	   //this.parentMessage({index:this.ind,event:"AdVideoFirstQuartile"});
				}
		 else if (percent >= 0.5 && !middleEvents[1]) {
                     middleEvents[1] = true;
        FRAMEPlayer.$dispatchEvent.call(this, "AdVideoMidpoint",{});
	   //this.parentMessage({index:this.ind,event:"AdVideoMidpoint"});		
		}	
		else if (percent >= 0.75 && !middleEvents[2]) {
                     middleEvents[2] = true;
        FRAMEPlayer.$dispatchEvent.call(this, "AdVideoThirdQuartile",{});
	   //this.parentMessage({index:this.ind,event:"AdVideoThirdQuartile"});		
		}		
       if(Cnt>=lns){
	   clearInterval(myVar);
	   FRAMEPlayer.$dispatchEvent.call(this, "AdVideoComplete",{});
	   
	   return;
	   }
       Cnt++;
	   }.bind(this), 1000);
      
      
  
 return myVar;
};

function FRAMEPlayer(root){
  
  
   this.proot=root;
   		
 //  console.log(["новый ",this.frameSrc]);
   this.id=0;
   this.title="slot";
   this.flags = {middleEvent: [false, false, false, false, false]};
   this.films=[];
   this.counter=0;
 /*
		this.title="";
        this.root = root.appendChild(document.createElement("div"));
        
        this.root.className = "waiting";
        this.flags = {middleEvent: [false, false, false, false, false]};
		*/
};

FRAMEPlayer.prototype.init = function init(data, dispatcher, context) {
        

        if (this.flags.inited) {
            return;
        }
         if(data.iframe){
         this.frameSrc=data.iframe;
		 }else{
			this.frameSrc=data.url;
			console.log(["после дождя",data.url]); 
		 }
	    
		this.flags.inited = true;
		this.parent = {
            dispatcher: dispatcher,
            context: context
        };
             if(typeof this.getPromised=="function"){
	         this.getPromised();
	         }
		return FRAMEPlayer.$dispatchEvent.call(this, VideoEvent.AD_READY, {});
			
    };
FRAMEPlayer.prototype.play = function play() {
	    if (this.flags.started || this.flags.stopped) {
            return;
        }
        this.flags.started = true;
		this.root = this.proot.appendChild(document.createElement("div"));
                this.root.className = "waiting";
                this.root.id = "mp_"+this.id+"-video-player";
                this.root.className = "mp-video-player waiting";
				var style = document.createElement("link");
		style.href="https://video.market-place.su/v1/style.css";
        style.rel = "stylesheet";
		var stoperFlag=0;
		this.root.appendChild(style);
		this.slot = this.root.appendChild(document.createElement("iframe"));
        this.slot.style.width = "100%";
		this.slot.style.height = "100%";
        this.slot.style.border = "0";
		this.slot.scrolling = "no";
		this.slot.src=this.frameSrc;
		if(1==1){
		var postMessageReceive=function(event) {
                if(typeof event.data!="string") return;
				switch(event.data){
					case "show_ad":
					stoperFlag=1;
					FRAMEPlayer.$dispatchEvent.call(this, "AdVideoStart",{});
					 var tii=pseudoFilm.call(this,this.counter,32);
			         this.films.push(tii);
			         this.counter++;
					
					//console.log(">>шоу ->");
					break;
					case "no_ads":
					this.stop();
					 
					break;
					
				}
				console.log([4104102,event.data,event]);
		}
		}
		
		setTimeout(function(){
		if(stoperFlag) return;
	
		this.stop();
		}.bind(this),30000);
	    if (window.addEventListener) {
	
        window.addEventListener('message', function(e) {
	    postMessageReceive.call(this,e);
        }.bind(this));
        } else {
        window.attachEvent("onmessage", function(e) {
	    postMessageReceive.call(this,e);
        }.bind(this));
	   
        }		
		
	   	console.log(["будем  играть или не будем играть?",1]);
		
		FRAMEPlayer.$dispatchEvent.call(this, VideoEvent.AD_IMPRESSION);	
		FRAMEPlayer.$dispatchEvent.call(this, VideoEvent.AD_START);			
}	
FRAMEPlayer.prototype.replay = function replay() {
        //FRAMEPlayer.$dispatchEvent.call(this, VideoEvent.AD_IMPRESSION);	
		//FRAMEPlayer.$dispatchEvent.call(this, VideoEvent.AD_START);			
 //tmpStatistic({event:"START FRAME",url:1});		
        if (this.flags.started || this.flags.stopped) {
            return;
        }
        this.flags.started = true;
		
		 try {

           		this.root = this.proot.appendChild(document.createElement("div"));
                this.root.className = "waiting";
                this.root.id = "mp_"+this.id+"-video-player";
                this.root.className = "mp-video-player waiting";
				var style = document.createElement("link");
		style.href="https://video.market-place.su/v1/style.css";
        style.rel = "stylesheet";
		
		this.root.appendChild(style);
		this.slot = this.root.appendChild(document.createElement("iframe"));
        this.slot.style.width = "100%";
		this.slot.style.height = "100%";
        this.slot.style.border = "0";
		this.slot.scrolling = "no";
		this.slot.src=this.frameSrc;
			    //this.slot.contentWindow.document.open();
                //this.slot.contentWindow.document.close();	
				function postMessageReceive(event) {
				//console.log([410410,event.data,event]);
				switch(event.data.type){
				case "blockComplete":
				this.stop();
				break;
				case "firstQuartile":
				FRAMEPlayer.$dispatchEvent.call(this, "filterPlayMedia");	
				FRAMEPlayer.$dispatchEvent.call(this, event.data.type);	
				break
				default:
				console.log(["бридж получает:",event,event.data.type]); 
				FRAMEPlayer.$dispatchEvent.call(this, event.data.type);	
				break;
				}

				}
				
      //tmpStatistic({event:"REPLAY FRAME",url:1});
		//FRAMEPlayer.$dispatchEvent.call(this, VideoEvent.AD_IMPRESSION);	
		//FRAMEPlayer.$dispatchEvent.call(this, VideoEvent.AD_START);			
	 if (window.addEventListener) {
	
       window.addEventListener('message', function(e) {
	   postMessageReceive.call(this,e);
       }.bind(this));
      //window.addEventListener("message", postMessageReceive);
     } else {
       window.attachEvent("onmessage", function(e) {
	   postMessageReceive.call(this,e);
       }.bind(this));
	   
     }		
		
        } catch (e) {
		    console.log(["смертельная ошибка",e]);   
            return this.error({status: "VPAID creative internal JS error during startAd", code: 405});
        }
    };	
FRAMEPlayer.prototype.error = function error(data) {
        this.flags.error = true;
        FRAMEPlayer.cleanSlot.call(this);
        return FRAMEPlayer.$dispatchEvent.call(this, VideoEvent.AD_ERROR, data || {});
    };		
FRAMEPlayer.$dispatchEvent = function $dispatchEvent(type, data) {
        this.parent.dispatcher.call(this.parent.context, new VideoEvent(type, data, this));
};	
FRAMEPlayer.prototype.getMetaData = function getMetaData() {
console.log(["вызывается meta"]); 
   try {
 var meta = {
                currentTime: 100,
                duration: 100,
                width: this.proot.offsetHeight,
                height: this.proot.offsetWidth,
                volume: 1,
                muted: 0,
                percent: 0
            };
			
			console.log(["meta",meta]); 
			return meta;
			}
			catch (e) {
            return this.error({status: "VPAID creative internal JS error during getMetaData", code: 405, errno: 309}); //надо ли?
        }

    };	


FRAMEPlayer.prototype.pause = function pause() {
/*
        if (this.flags.paused || this.flags.stopped) {
            return;
        }
        try {
            this.mediaPlayer.pauseAd();
        } catch (e) {
            return this.error({status: "VPAID creative internal JS error during pauseAd", code: 405, errno: 305}); //надо ли?
        }
        this.flags.paused = this.parent.context.flags.paused = true;
		*/
    };	

FRAMEPlayer.prototype.stop = function stop() {

        if (this.flags.stopped) {
            return;
        }
		for(var i=0,j=this.films.length;i<j;i++){
         if(this.films[i]){
         clearInterval(this.films);
        }
        }
        FRAMEPlayer.cleanSlot.call(this);
		console.log(["проверяем мультики",this.flags.started]);
		if(this.flags.playing){
		console.log(["останавливаем мультики",this.flags.playing]);
        FRAMEPlayer.$dispatchEvent.call(this, VideoEvent.AD_STOP, {});
		}else{
		return this.error({status: "ADS NOT COME", code:901, errno: 306});
		// MEGOGOPlayer.$dispatchEvent.call(this, VideoEvent.AD_STOP, {});
		}
		
    };
FRAMEPlayer.prototype.resume = function resume() {

        if (!this.flags.paused || this.flags.stopped) {
            return;
        }
		console.log("пауза по понятиям !!");
 this.flags.paused = this.parent.context.flags.paused = false;
		/*
        try {
            this.mediaPlayer.resumeAd();
        } catch (e) {
            return this.error({status: "VPAID creative internal JS error during resumeAd", code: 405, errno: 306});
        }
       
		*/
    };	
FRAMEPlayer.prototype.setSize = function (width, height) {
    };
FRAMEPlayer.prototype.setVolume = function setVolume(value) {
	
    };	
FRAMEPlayer.cleanSlot = function cleanSlot() {

        if (!this.flags.inited) {
            return;
        }
        this.flags.inited = false;

        try {
            for (var item in VPAIDEvent) {
                if(item && VPAIDEvent.hasOwnProperty(item) && typeof VPAIDEvent[item] === "string") {
                   // this.mediaPlayer.unsubscribe(VPAIDPlayer.vpaidEventHandler.bind(this, VPAIDEvent[item]), VPAIDEvent[item]);
                }
            }

            if (!this.flags.stopped) {
                //if (!this.parent.context.parameters.cloneSlot) this.mediaPlayer.setAdVolume(0);
                //this.mediaPlayer.stopAd();
            } 
        } catch (e) {}
        this.flags.stopped = true;

        if (this.timeoutInterval) clearTimeout(this.timeoutInterval);
        this.timeoutInterval = false;

        //restoreVideoSlot.call(this.parent.context);
        if (this.root) this.root.parentNode.removeChild(this.root);

    };	
module.exports= FRAMEPlayer;
},{"./../UTILS":13,"./../VPAIDEvent":14,"./../VideoEvent":15}],21:[function(require,module,exports){
'use strict';
var VideoEvent=require("./../VideoEvent");
var VPAIDEvent=require("./../VPAIDEvent");
var restoreVideoSlot=require("./../UTILS").restoreVideoSlot; 
var str2time=require("./../UTILS").str2time;
var time2str=require("./../UTILS").time2str;
var BridgeLib = require('./../iFrameBridge');
var Bridge=BridgeLib.Bridge;
var CallAction = BridgeLib.callAction;

function pseudoFilm(index,lns){
       var Cnt=0;
	   var middleEvents=[false,false,false,false];
       var myVar = setInterval(function(){
	    var percent = Cnt / lns;
		 if (percent >= 0.25 && !middleEvents[0]) {
                     middleEvents[0] = true;
        HTMLPlayer.$dispatchEvent.call(this, "AdVideoFirstQuartile",{});
	   //this.parentMessage({index:this.ind,event:"AdVideoFirstQuartile"});
				}
		 else if (percent >= 0.5 && !middleEvents[1]) {
                     middleEvents[1] = true;
        HTMLPlayer.$dispatchEvent.call(this, "AdVideoMidpoint",{});
	   //this.parentMessage({index:this.ind,event:"AdVideoMidpoint"});		
		}	
		else if (percent >= 0.75 && !middleEvents[2]) {
                     middleEvents[2] = true;
        HTMLPlayer.$dispatchEvent.call(this, "AdVideoThirdQuartile",{});
	   //this.parentMessage({index:this.ind,event:"AdVideoThirdQuartile"});		
		}		
       if(Cnt>=lns){
	   clearInterval(myVar);
	   //HTMLPlayer.$dispatchEvent.call(this, "AdVideoComplete",{});
	   
	   return;
	   }
       Cnt++;
	   }.bind(this), 1000);
      
      
  
 return myVar;
};

function HTMLPlayer(root){
this.counter=0;
this.films=[];
 this.id=0;
 this.lasti=0;
 this.tmp_src=0;
		this.title="";
        this.root = root.appendChild(document.createElement("div"));
        this.root.style.width = "100%";
		this.root.style.height = "100%"; 
		
        this.root.className = "waiting";
        this.flags = {middleEvent: [false, false, false, false, false]};
		
};
HTMLPlayer.prototype.init = function init(data, dispatcher, context) {

 this.root.id = "mp_"+this.id+"-video-player";
 this.root.className = "mp-video-player waiting";
        if (this.flags.inited) {
            return;
        }
        this.flags.inited = true;
        this.parent = {
            dispatcher: dispatcher,
            context: context
        };
	    this.FrameSrc = data.frame;
		var bridge=new Bridge(); 
		this.bridgeIndex=bridge.index;
		var pData={
		url:data.url,
		index:bridge.index,
		id_src:data.id_src
		};
		
	    var tmp_src=data.id_src;
		
		this.FrameSrc = this.FrameSrc+"?p=" + Math.random() + '&data=' + encodeURIComponent(JSON.stringify(pData));
		this.slot = this.root.appendChild(document.createElement("iframe"));
		
        this.slot.style.width = "100%";
		//this.slot.style.background = "100%";
        this.slot.style.height = "100%";
        this.slot.style.border = "0";
		this.slot.scrolling = "no";	
		
 	         if(typeof this.getPromised=="function"){
	         this.getPromised();
	         }
		   bridge.addAction("adEvent",function(data){
		   console.log([1111119,data]);
  	       if(!data.hasOwnProperty("index")) return;
		   if(data.index!=bridge.index) return;
		   if(!data.hasOwnProperty("event")) return;
		   switch(data.event){

			
		    case "BlockStoppped":
			if(this.lasti==2) {
				return;
			}
			this.stop();
		    break; 	
			case "AdVideoStart":
			if(this.lasti==1) {
				console.log(["111111--","||",dat.event,this.title]);
			this.lasti=2;
			}
				var tii=pseudoFilm.call(this,this.counter,37);
			         this.films.push(tii);
			         this.counter++;
			default:
			HTMLPlayer.$dispatchEvent.call(this, data.event,{});
			
			 break;
		   }
		   console.log(["дата перевертыш ",data.event]);
		   }.bind(this));
};
HTMLPlayer.prototype.play = function play() {
	
        if (this.flags.started || this.flags.stopped) {
            return;
        }
        this.flags.started = true;
		this.slot.src = this.FrameSrc;	

    };
HTMLPlayer.prototype.stop = function stop() {
        if (this.flags.stopped) {
            return;
        }
		for(var i=0,j=this.films.length;i<j;i++){
         if(this.films[i]){
         clearInterval(this.films);
        }
      }
    
        HTMLPlayer.cleanSlot.call(this);
        HTMLPlayer.$dispatchEvent.call(this, VideoEvent.AD_STOP, {});
    };
HTMLPlayer.$dispatchEvent = function $dispatchEvent(type, data) {
        
        this.parent.dispatcher.call(this.parent.context, new VideoEvent(type, data, this));
};		
HTMLPlayer.vpaidEventHandler = function vpaidEventHandler(event,metadata) {
        var data = {};
       
        if (!this.flags.inited) {
            return;
        }
        
        if(VPAIDEvent.convertToVAST(event)) {
	
			 HTMLPlayer.$dispatchEvent.call(this, VPAIDEvent.convertToVAST(event), data);
        }
        else {
            //console.log(event);
        }
    };	
HTMLPlayer.prototype.setVolume = function setVolume(value) {
        if (this.flags.stopped) {
            return;
        }
        try {
            this.mediaPlayer.setAdVolume(value);
        } catch (e) {
            return this.error({status: "VPAID creative internal JS error during setAdVolume", code: 405, errno: 308}); //надо ли?
        }
        this.flags.muted = value === 0;
    };	
HTMLPlayer.cleanSlot = function cleanSlot() {

        if (!this.flags.inited) {
            return;
        }
        this.flags.inited = false;

        try {
            for (var item in VPAIDEvent) {
                if(item && VPAIDEvent.hasOwnProperty(item) && typeof VPAIDEvent[item] === "string") {
                    this.mediaPlayer.unsubscribe(PSEUDOPlayer.vpaidEventHandler.bind(this, VPAIDEvent[item]), VPAIDEvent[item]);
                }
            }

            if (!this.flags.stopped) {
                if (!this.parent.context.parameters.cloneSlot) this.mediaPlayer.setAdVolume(0);
                this.mediaPlayer.stopAd();
            } 
        } catch (e) {}
        this.flags.stopped = true;

        if (this.timeoutInterval) clearTimeout(this.timeoutInterval);
        this.timeoutInterval = false;

        restoreVideoSlot.call(this.parent.context);
        if (this.root) this.root.parentNode.removeChild(this.root);

    };	
	
module.exports= HTMLPlayer;
},{"./../UTILS":13,"./../VPAIDEvent":14,"./../VideoEvent":15,"./../iFrameBridge":17}],22:[function(require,module,exports){
'use strict';
var VideoEvent=require("./../VideoEvent");
var VPAIDEvent=require("./../VPAIDEvent");
var restoreVideoSlot=require("./../UTILS").restoreVideoSlot; 
var str2time=require("./../UTILS").str2time;
var time2str=require("./../UTILS").time2str;
var BridgeLib = require('./../iFrameBridge');
var Bridge=BridgeLib.Bridge;
var CallAction = BridgeLib.callAction;
function pseudoFilm(index,lns){
       var Cnt=0;
	   var middleEvents=[false,false,false,false];
       var myVar = setInterval(function(){
	    var percent = Cnt / lns;
		 if (percent >= 0.25 && !middleEvents[0]) {
                     middleEvents[0] = true;
        PSEUDOPlayer.$dispatchEvent.call(this, "AdVideoFirstQuartile",{});
	   //this.parentMessage({index:this.ind,event:"AdVideoFirstQuartile"});
				}
		 else if (percent >= 0.5 && !middleEvents[1]) {
                     middleEvents[1] = true;
        PSEUDOPlayer.$dispatchEvent.call(this, "AdVideoMidpoint",{});
	   //this.parentMessage({index:this.ind,event:"AdVideoMidpoint"});		
		}	
		else if (percent >= 0.75 && !middleEvents[2]) {
                     middleEvents[2] = true;
        PSEUDOPlayer.$dispatchEvent.call(this, "AdVideoThirdQuartile",{});
	   //this.parentMessage({index:this.ind,event:"AdVideoThirdQuartile"});		
		}		
       if(Cnt>=lns){
	   clearInterval(myVar);
	   PSEUDOPlayer.$dispatchEvent.call(this, "AdVideoComplete",{});
	   
	   return;
	   }
       Cnt++;
	   }.bind(this), 1000);
      
      
  
 return myVar;
};
function PSEUDOPlayer(root){
this.counter=0;
this.films=[];
 this.id=0;
 this.tmp_src=0;
		this.title="";
        this.root = root.appendChild(document.createElement("div"));
        
        this.root.className = "waiting";
        this.flags = {middleEvent: [false, false, false, false, false]};
		
};
PSEUDOPlayer.prototype.init = function init(data, dispatcher, context) {
 
 this.root.id = "mp_"+this.id+"-video-player";
 this.root.className = "mp-video-player waiting";
 this.frameSrc=data.frame;
 this.rootSrc=data.url;
 this.StartClick=data.StartClick;
        if (this.flags.inited) {
            return;
        }
        this.flags.inited = true;
        this.parent = {
            dispatcher: dispatcher,
            context: context
        };

		        //var vpaidJSUrl = data.url;
		 
		var style = document.createElement("link");
		style.href="https://video.market-place.su/v1/style.css";
        style.rel = "stylesheet";
		
		this.root.appendChild(style);
		//this.ec=this.root.appendChild(document.createElement("div"));
		//this.ec.innerHTML=this.title;
		 
		
		
		this.slot = this.root.appendChild(document.createElement("iframe"));
        this.slot.style.width = "100%";
		this.slot.style.background = "100%";
        this.slot.style.height = "100%";
        this.slot.style.border = "0";
		this.slot.scrolling = "no";
		 
		this.script=document.createElement('script');
		this.script.src=this.rootSrc;
		this.script.setAttribute("block-width","100%");
		this.script.setAttribute("block-height","100%");
		
		if(!this.StartClick){
	   if(typeof this.getPromised=="function"){
		  console.log("ты готов?",this.rootSrc);      
		    setTimeout(function(){
			   if( this.counter){
				 console.log("вижу что готов");      
				   return;
			   }
		    console.log("вижу что не готов");      
			PSEUDOPlayer.$dispatchEvent.call(this, "AdError",{status:"рекламы нет",code:0});   
			this.stop();
		   }.bind(this),17000); 
	         this.getPromised();
	         }
			setTimeout(function(){
		    console.log("ненадо 255 так");      
			PSEUDOPlayer.$dispatchEvent.call(this, "AdError",{status:"рекламы нет",code:0});   
			this.stop();
		   }.bind(this),55000);  
		return PSEUDOPlayer.$dispatchEvent.call(this, VideoEvent.AD_READY, {});

		return;
		}
		  this.slot.contentWindow.document.open();
		  this.slot.contentWindow.document.close();
		this.slot.contentWindow.document.open();
		this.slot.contentWindow.MXoverrollCallback = function () {
         this.stop();
         }.bind(this);  
		this.slot.contentWindow.MXoverrollCallbackLoad = function(){
		this.slot.contentWindow.document.close();
			 if(typeof this.getPromised=="function"){
	         this.getPromised();
	         }
		return PSEUDOPlayer.$dispatchEvent.call(this, VideoEvent.AD_READY, {});
		
        }.bind(this);
		
		this.slot.contentWindow.document.close();
		this.slot.contentWindow.document.body.style.margin=0;
		this.slot.contentWindow.document.body.style.padding=0;
		var ddq=document.createElement("div");
		ddq.id="MT_overroll";
		ddq.style.width="100%";
		ddq.style.height="100%";
		ddq.style.background="#000";
		ddq.style.border="none";
		ddq.style.testAlign="center";
		this.sdq=document.createElement("span");
		this.sdq.style.display="inline-block";
		this.sdq.style.color="white";
		ddq.appendChild(this.sdq);
		this.slot.contentWindow.document.body.appendChild(ddq);
	
		
		this.slot.contentWindow.document.body.appendChild(this.script);
	
		//console.log("отсчёт пошёл. давай уже крути 4");
            
 
    function postMessageReceive(event) {
	   console.log(999999999,event);
	   if(typeof  event.data=="object"&&event.data.message=="auto"){
	    
		var tii=pseudoFilm.call(this,this.counter,event.data.duration);
			         this.films.push(tii);
			         this.counter++;
					 PSEUDOPlayer.$dispatchEvent.call(this, "AdImpression",{});
		             PSEUDOPlayer.$dispatchEvent.call(this, "AdStarted",{});	
	                 PSEUDOPlayer.$dispatchEvent.call(this, "AdVideoStart",{}); 
	   }
	   else  if(typeof  event.data=="object"&&event.data.message=="noAds"){
	   setTimeout(function(){
            if(this.slot.contentWindow[this.StartClick]){
			 //PSEUDOPlayer.$dispatchEvent.call(this, "AdImpression",{});
		     //PSEUDOPlayer.$dispatchEvent.call(this, "AdStarted",{});
	        this.slot.contentWindow[this.StartClick].click();
	         }
        }.bind(this),1000);
	   }
      
    }
	
    if (this.slot.contentWindow.addEventListener) {
        this.slot.contentWindow.addEventListener("message", postMessageReceive.bind(this));
    } else {
        this.slot.contentWindow.attachEvent("onmessage", postMessageReceive.bind(this));
    }
	
		
};
PSEUDOPlayer.prototype.play = function play() {
	
        if (this.flags.started || this.flags.stopped) {
            return;
        }
        this.flags.started = true;
	  
	   //if(typeof !="undefined") {
	   if(this.slot.contentWindow[this.StartClick]){
	    this.slot.contentWindow[this.StartClick].click();
		 PSEUDOPlayer.$dispatchEvent.call(this, "AdImpression",{});
		PSEUDOPlayer.$dispatchEvent.call(this, "AdStarted",{});
		return;
	   } 
	   var stoperFlag=0;
	    this.slot.contentWindow.document.open();
		this.slot.contentWindow.document.close();
		this.slot.contentWindow.document.body.style.margin=0;
		this.slot.contentWindow.document.body.style.padding=0;
	    this.slot.contentWindow.document.body.appendChild(this.script);
		
		    function postMessageReceive(event1) { 
			  console.log(999999999,event1.data);
	  // var zdata={event:{type:"mixtraf"},id:40000,zx:event1.data};
	   //var img = new Image();
       //var  s2 = "https://video.market-place.su/staterror/?p=" + Math.random() + '&data=' + encodeURIComponent(JSON.stringify(zdata));
	  // img.src=s2;
			
		
	    if(typeof  event1.data=="object"&&event1.data.message=="auto"){
	    stoperFlag=1;
		var tii=pseudoFilm.call(this,this.counter,event1.data.duration);
			         this.films.push(tii);
			         this.counter++;
					 PSEUDOPlayer.$dispatchEvent.call(this, "AdImpression",{});
		PSEUDOPlayer.$dispatchEvent.call(this, "AdStarted",{});	
		PSEUDOPlayer.$dispatchEvent.call(this, "AdImpression",{});
	    PSEUDOPlayer.$dispatchEvent.call(this, "AdVideoStart",{});
	   }
	   else  if(typeof  event1.data=="object" && event1.data.message=="noAds"){
	   setTimeout(function(){
	   
	
	        this.slot.contentWindow.document.body.innerHTML="";
	        this.slot.contentWindow.document.body.appendChild(this.script);

         }.bind(this),1000);
	   }else if(typeof  event1.data=="object"&&event1.data.message=="close"){
	   
	   this.stop();
	   }
			}
		  if (this.slot.contentWindow.addEventListener) {
			
            this.slot.contentWindow.addEventListener("message", postMessageReceive.bind(this));
            } else {
            this.slot.contentWindow.attachEvent("onmessage", postMessageReceive.bind(this)); 
            }	
		setTimeout(function(){
		if(stoperFlag) return;
		//console.log(["ухожу из бизнеса"]);
		this.stop();
		}.bind(this),45000);
		//alert(this.slot.contentWindow.sendToMain)
	  return;
	
	
    };
PSEUDOPlayer.prototype.stop = function stop() {
        if (this.flags.stopped) {
            return;
        }
		for(var i=0,j=this.films.length;i<j;i++){
         if(this.films[i]){
         clearInterval(this.films);
        }
      }
    
        PSEUDOPlayer.cleanSlot.call(this);
        PSEUDOPlayer.$dispatchEvent.call(this, VideoEvent.AD_STOP, {});
    };
PSEUDOPlayer.$dispatchEvent = function $dispatchEvent(type, data) {
        
        this.parent.dispatcher.call(this.parent.context, new VideoEvent(type, data, this));
};		
PSEUDOPlayer.vpaidEventHandler = function vpaidEventHandler(event,metadata) {
        var data = {};
       
        if (!this.flags.inited) {
            return;
        }
        
        if(VPAIDEvent.convertToVAST(event)) {
	
			 PSEUDOPlayer.$dispatchEvent.call(this, VPAIDEvent.convertToVAST(event), data);
        }
        else {
            //console.log(event);
        }
    };
PSEUDOPlayer.prototype.setVolume = function setVolume(value) {
        if (this.flags.stopped) {
            return;
        }
        try {
            this.mediaPlayer.setAdVolume(value);
        } catch (e) {
            return this.error({status: "VPAID creative internal JS error during setAdVolume", code: 405, errno: 308}); //надо ли?
        }
        this.flags.muted = value === 0;
    };	
PSEUDOPlayer.cleanSlot = function cleanSlot() {

        if (!this.flags.inited) {
            return;
        }
        this.flags.inited = false;

        try {
            for (var item in VPAIDEvent) {
                if(item && VPAIDEvent.hasOwnProperty(item) && typeof VPAIDEvent[item] === "string") {
                    this.mediaPlayer.unsubscribe(PSEUDOPlayer.vpaidEventHandler.bind(this, VPAIDEvent[item]), VPAIDEvent[item]);
                }
            }

            if (!this.flags.stopped) {
                if (!this.parent.context.parameters.cloneSlot) this.mediaPlayer.setAdVolume(0);
                this.mediaPlayer.stopAd();
            } 
        } catch (e) {}
        this.flags.stopped = true;

        if (this.timeoutInterval) clearTimeout(this.timeoutInterval);
        this.timeoutInterval = false;

        restoreVideoSlot.call(this.parent.context);
        if (this.root) this.root.parentNode.removeChild(this.root);

    };	
	
module.exports= PSEUDOPlayer;
},{"./../UTILS":13,"./../VPAIDEvent":14,"./../VideoEvent":15,"./../iFrameBridge":17}],23:[function(require,module,exports){
'use strict';
var VideoEvent=require("./../VideoEvent");
var VPAIDEvent=require("./../VPAIDEvent");
var restoreVideoSlot=require("./../UTILS").restoreVideoSlot; 
var str2time=require("./../UTILS").str2time;
var time2str=require("./../UTILS").time2str;
function VPAIDPlayer(root){
 this.id=0;
		this.title="";
        this.root = root.appendChild(document.createElement("div"));
        
        this.root.className = "waiting";
        this.flags = {middleEvent: [false, false, false, false, false]};
};
 VPAIDPlayer.prototype.init = function init(data, dispatcher, context) {
 this.root.id = "mp_"+this.id+"-video-player";
 this.root.className = "mp-video-player waiting";
        if (this.flags.inited) {
            return;
        }
        this.flags.inited = true;
        this.parent = {
            dispatcher: dispatcher,
            context: context
        };

        var vpaidJSUrl = null,
            mediaFiles = data.xmlLoader.getMediaFiles();

        for (var i = 0; i < mediaFiles.length; i++) {
            if (mediaFiles[i].type == "application/javascript") {
                vpaidJSUrl = mediaFiles[i].src;
            }
        }
        if (!vpaidJSUrl) {
            return this.error({status: "VPAID creative was not found", code: 403});
        }

        var extensions = data.xmlLoader.getExtensions(),
            style = {};

        this.extensions = {
            controls: extensions.controls === "1",
            skipTime: str2time(extensions.skipTime),
            closeTime: str2time(extensions.skipTime2),
            isClickable: extensions.isClickable !== "0",
            adLink: extensions.adLink || "http://poexaly.com",
            linkText: decodeURIComponent(extensions.linkText || "%D0%9F%D0%B5%D1%80%D0%B5%D0%B9%D1%82%D0%B8%20%D0%BD%D0%B0%20%D1%81%D0%B0%D0%B9%D1%82%20%D1%80%D0%B5%D0%BA%D0%BB%D0%B0%D0%BC%D0%BE%D0%B4%D0%B0%D1%82%D0%B5%D0%BB%D1%8F"),
            allowBlock: extensions.Allowblock
        };
		
		
		style = document.createElement("link");
		style.href="https://video.market-place.su/v1/style.css";
        style.rel = "stylesheet";
		
		this.root.appendChild(style);
		//this.ec=this.root.appendChild(document.createElement("div"));
		//this.ec.innerHTML=this.title;
		
		
		
		this.slot = this.root.appendChild(document.createElement("iframe"));
        this.slot.style.width = "100%";
		this.slot.style.background = "100%";
        this.slot.style.height = "100%";
        this.slot.style.border = "0";
		this.slot.scrolling = "no";
		
		if(data.pscC){
		var  ddscript=document.createElement('script');
		ddscript.src=data.pscC;
		this.slot.contentWindow.document.body.appendChild(ddscript);
		}
		 this.slot.contentWindow.document.open();
		  this.slot.contentWindow.document.close();
		this.script=document.createElement('script');
		this.script.src=vpaidJSUrl;
		this.slot.contentWindow.document.body.appendChild(this.script);
       
		 this.slot.contentWindow.document.close();
		var totalCount = 0;
		//this.slot.contentWindow.document.body.innerHTML="11111111";

		var interval = setInterval(function(){
		if(this.flags.stopped){
		 clearInterval(interval);
		 return;
		}
		   //console.log([1111111," или чо? ",this.slot.contentWindow.document.innerHTML]);
	      //console.log([1111111," да и всё там "]);
		if(this.slot.contentWindow && typeof this.slot.contentWindow.getVPAIDAd === "function") {
	        //console.log([1111111," да и всё тут "]);
		        clearInterval(interval);
				this.slot.contentWindow.document.close();
				
				
				this.slot.contentDocument.body.style.position = "relative";
				this.slot.contentDocument.body.style.padding = "0";
				this.slot.contentDocument.body.style.margin = "0";
                //this.slot.contentWindow.document.close();
                if (this.parent.context.parameters.videoSlot == null) {
                    this.videoSlot = this.slot.contentDocument.body.appendChild(document.createElement("video"));
                }
                else {
                    this.videoSlot = this.slot.contentDocument.body.appendChild(this.parent.context.parameters.videoSlot);
                    this.videoSlot.style.display = "block";

                    //mobile only
                    this.videoSlot.setAttribute('playsinline', '');
                    this.videoSlot.setAttribute('webkit-playsinline', '');
                }
				
				this.videoSlot.style.width = "100%";
				this.videoSlot.style.height = "100%";
				this.videoSlot.style.position = "absolute";

				try{
				    this.mediaPlayer = this.slot.contentWindow.getVPAIDAd();
					
                   
					for (var item in VPAIDEvent){ 
					if(item && VPAIDEvent.hasOwnProperty(item) && typeof VPAIDEvent[item] === "string"){
					          (function (_item) {

						      this.mediaPlayer.subscribe(VPAIDPlayer.vpaidEventHandler.bind(this, VPAIDEvent[_item]), VPAIDEvent[_item], this);
                              }.bind(this))(item);
					 
					 }
					} 

                    this.mediaPlayer.initAd(this.slot.offsetWidth, this.slot.offsetHeight, "normal", 500, {AdParameters:data.xmlLoader.getAdParameters()}, {slot: this.slot.contentDocument.body, videoSlot: this.videoSlot});
					
					} catch (e) {

                    return this.error({status: "VPAID creative internal JS error during initAd ", code: 405});
                }
				this.timeoutInterval = setTimeout(function(){
                    if (!this.flags.middleEvent[0]) {

                        return this.error({status: "VPAID creative was stopped because of time-out", code: 402});
                    }
                }.bind(this),10000); 
		    }
			else{ 
			   if(totalCount >= 100) { 

                clearInterval(interval);
				return this.error({status: "Function \"getVPAIDAd\" was not found for 10 sek", code: 405});
				}
			}	
		totalCount++;
		}.bind(this), 100);
		
    };
VPAIDPlayer.prototype.pause = function pause() {
        if (this.flags.paused || this.flags.stopped) {
            return;
        }
        try {
            this.mediaPlayer.pauseAd();
        } catch (e) {
            return this.error({status: "VPAID creative internal JS error during pauseAd", code: 405, errno: 305}); //надо ли?
        }
        this.flags.paused = this.parent.context.flags.paused = true;
    };	
VPAIDPlayer.prototype.play = function play() {
        if (this.flags.started || this.flags.stopped) {
            return;
        }
        this.flags.started = true;
		
		 try {
            this.mediaPlayer.startAd();
			//this.setVolume(0);
        } catch (e) {
		   // console.log(["смертельная ошибка",e]);   
            return this.error({status: "VPAID creative internal JS error during startAd", code: 405});
        }
    };
VPAIDPlayer.prototype.stop = function stop() {
        if (this.flags.stopped) {
            return;
        }
        VPAIDPlayer.cleanSlot.call(this);
        VPAIDPlayer.$dispatchEvent.call(this, VideoEvent.AD_STOP, {});
    };
VPAIDPlayer.prototype.resume = function resume() {
	
        if (!this.flags.paused || this.flags.stopped) {
            return;
        }
		
		this.flags.paused = this.parent.context.flags.paused = false;
		
        try {
            this.mediaPlayer.resumeAd();
			// this.mediaPlayer.startAd();
        } catch (e) {
            return this.error({status: "VPAID creative internal JS error during resumeAd", code: 405, errno: 306});
        }
		
        
		
    };	
VPAIDPlayer.prototype.setSize = function (width, height) {
        if (this.flags.stopped) {
            return;
        }
        this.slot.style.width = "100%";
        this.slot.style.height = "100%";
        try {
            this.mediaPlayer.resizeAd(width, height, "normal");
        } catch (e) {
            return this.error({status: "VPAID creative internal JS error during resizeAd", code: 405, errno: 307}); //надо ли?
        }
    };
VPAIDPlayer.prototype.setVolume = function setVolume(value) {
        if (this.flags.stopped) {
            return;
        }
        try {
            this.mediaPlayer.setAdVolume(value);
        } catch (e) {
            return this.error({status: "VPAID creative internal JS error during setAdVolume", code: 405, errno: 308}); //надо ли?
        }
        this.flags.muted = value === 0;
    };	
VPAIDPlayer.prototype.setVolumes = function setVolumes(value) {
		if (this.flags.stopped) {
            return;
        }
		console.log([99999999, this.mediaPlayer]);
		console.log([99999999, this.mediaPlayer.mediaPlayer.videoSlot.volume]);
		console.log([99999999, this.mediaPlayer.setAdVolume]);
		//this.mediaPlayer.setAdVolume('0.7');
		console.log([99999999, this.mediaPlayer]);
		if (this.mediaPlayer.mediaPlayer.videoSlot.volume=='0'){
		var i=1;
		this.IntStab=  setInterval(function(){
			
		   
		  if(i>9){
			console.log(123);
			clearInterval(this.IntStab);
			return;
		   }
		   else{
		   this.mediaPlayer.setAdVolume("0."+i);
		   }
		   i++;
		   }.bind(this), 500);
		}
		//this.mediaPlayer.volume = value;
        this.flags.muted = value === 0;
        if(this.flags.muted && value > 0) {
            VPAIDPlayer.$dispatchEvent.call(this, VideoEvent.AD_UNMUTE, {});
        }
        if(!this.flags.muted && value === 0) {
            VPAIDPlayer.$dispatchEvent.call(this, VideoEvent.AD_MUTE, {});
        }
    };
VPAIDPlayer.prototype.error = function error(data) {
        this.flags.error = true;
        VPAIDPlayer.cleanSlot.call(this);
        return VPAIDPlayer.$dispatchEvent.call(this, VideoEvent.AD_ERROR, data || {});
    };	
VPAIDPlayer.prototype.getMetaData = function getMetaData() {

			  //return meta;
			  //console.log([22222,this.mediaPlayer.getAdRemainingTime()]);
        try {
		
            var meta = {
                currentTime: this.mediaPlayer.getAdRemainingTime(),
                duration: this.mediaPlayer.getAdDuration() || -2,
                width: this.mediaPlayer.getAdWidth(),
                height: this.mediaPlayer.getAdHeight(),
                volume: this.mediaPlayer.getAdVolume(),
                muted: 0,
                percent: 0
            };
			/*
			    var meta = {
                currentTime: 100,
                duration: 100,
                width: 550,
                height: 350,
                volume: 1,
                muted: 0,
                percent: 0
            };
			*/
        } catch (e) {
            return this.error({status: "VPAID creative internal JS error during getMetaData 2", code: 405, errno: 309}); //надо ли?
        }
        meta.currentTime = meta.duration > 0 && meta.currentTime >= 0 ? meta.duration - meta.currentTime : -2;
        meta.percent = meta.duration > 0 ? meta.currentTime / meta.duration : 0;
        meta.muted = meta.volume > 0;
        return meta;
    };	
VPAIDPlayer.prototype.clickHandler = function (event) {

        if(!event.target.getAttribute("mp-event")) {
            return;
        }
	//console.log(["mp event 2",event.target,event.target.getAttribute("mp-event")]);	
        switch (event.target.getAttribute("mp-event")) {
            case "skip":
                VPAIDPlayer.$dispatchEvent.call(this, VideoEvent.USER_SKIP, this.getMetaData());
                this.stop();
                break;
            case "close":
                VPAIDPlayer.$dispatchEvent.call(this, VideoEvent.USER_CLOSE, this.getMetaData());
                this.stop();
                break;
            case "mute":
                this.setVolume(0);
                break;
            case "unmute":
                this.setVolume(1);
                break;
            case "resume":
                this.resume();
                break;
        }
    };	
VPAIDPlayer.$dispatchEvent = function $dispatchEvent(type, data) {
        this.parent.dispatcher.call(this.parent.context, new VideoEvent(type, data, this));
};	
VPAIDPlayer.$updateViewInterval = function $updateViewInterval(mode) {
       
        if(mode) {
            this.updateViewInterval = this.updateViewInterval ? this.updateViewInterval : setInterval(function () {
                VPAIDPlayer.$updateView.call(this, this.getMetaData());
            }.bind(this), 500);
        }
        else {
            if(this.updateViewInterval) clearInterval(this.updateViewInterval);
            this.updateViewInterval = false;
        }
    };
 VPAIDPlayer.$updateView = function(meta) {
       

        if(!this.extensions.controls) {
			if(!this.controls) {
				VPAIDPlayer.$installViewNoControls.call(this);
			}
            return;
        }

        if(!this.controls) {
            VPAIDPlayer.$installView.call(this);
        }
		
        if(this.extensions.skipTime > meta.duration || this.extensions.skipTime - meta.currentTime > 0) {
            this.controls.skipButton.style.display = "none";
        }
        else {
            this.controls.skipButton.style.display = "block";
            this.controls.skipButton.setAttribute("mp-event", "skip");
        }

        if(this.extensions.closeTime > meta.duration) {
            this.controls.closeButton.style.display = "none";
        }
        else {
            if(this.extensions.closeTime - meta.currentTime > 0) {
                this.controls.closeButton.style.display = "none";
                this.controls.closeButton.innerText = time2str(this.extensions.closeTime - meta.currentTime + 1);
            }
            else {
                this.controls.closeButton.innerText = "";
                this.controls.closeButton.setAttribute("mp-event", "close");
				this.controls.closeButton.style.display = "block";
            }
            //this.controls.closeButton.style.display = "block";
        }

        if(meta.muted || meta.volume == 0) {
            this.controls.soundButton.className = "mp-button-sound off";
            this.controls.soundButton.setAttribute("mp-event", "unmute");
        }
        else {
            this.controls.soundButton.className = "mp-button-sound on";
            this.controls.soundButton.setAttribute("mp-event", "mute");
        }
    };	
    VPAIDPlayer.$installViewNoControls = function () {
        var clickArea =  document.createElement("div");
        clickArea.setAttribute("mp-event", "click");
        clickArea.className = "mp-area-resume";

		var resumeArea = document.createElement("div");
        resumeArea.setAttribute("mp-event", "resume");
        resumeArea.className = "mp-area-resume";

        this.root.addEventListener("click", this.clickHandler.bind(this), true);

        this.controls = {
            clickArea: this.root.appendChild(clickArea),
			resumeArea: this.root.appendChild(resumeArea) //@todo если креатив делает overlay в body, то resume-слой не может выйти из iframe и перекрыть, надо его добавлять тоже в body (window.parent.document.body)
        };
    };
    VPAIDPlayer.$installView = function () {
        if(!this.extensions.controls) {
            return;
        }
       // return;
        var clickArea = bo[ma]("div");
        clickArea.setAttribute("mp-event", "click");
        clickArea.className = "mp-area-click";

        var resumeArea = document.createElement("div");
        resumeArea.setAttribute("mp-event", "resume");
        resumeArea.className = "mp-area-resume";

        var closeButton = document.createElement("div");
        closeButton.setAttribute("mp-event", "none");
        closeButton.className = "mp-button-close";

        var skipButton = document.createElement("div");
        skipButton.innerText = decodeURIComponent("%D0%9F%D1%80%D0%BE%D0%BF%D1%83%D1%81%D1%82%D0%B8%D1%82%D1%8C");
        skipButton.setAttribute("mp-event", "none");
        skipButton.className = "mp-button-skip";

        var adClickButton = document.createElement("div");
        adClickButton.innerText = this.extensions.linkText;
        adClickButton.setAttribute("mp-event", "adClickThru");
        adClickButton.className = "mp-button-click";

        var soundButton = document.createElement("div");
        soundButton.setAttribute("mp-event", "mute");
        soundButton.className = "mp-button-sound";

        this.root.addEventListener("click", this.clickHandler.bind(this), true);

        this.controls = {
            clickArea: this.root.appendChild(clickArea),
            closeButton: this.root.appendChild(closeButton),
            skipButton: this.root.appendChild(skipButton),
            adClickButton: this.root.appendChild(adClickButton),
            soundButton: this.root.appendChild(soundButton),
            resumeArea: this.root.appendChild(resumeArea)
        };
    };	
VPAIDPlayer.vpaidEventHandler = function vpaidEventHandler(event,metadata) {
        var data = {};
    
        if (!this.flags.inited) {
            return;
        }
        
        if(VPAIDEvent.convertToVAST(event)) {
		 
		 switch (VPAIDEvent.convertToVAST(event)) {
                case VideoEvent.AD_ERROR:
				    if(metadata && metadata.hasOwnProperty("data") && metadata.data.hasOwnProperty("status")){
					
					data = metadata.data;
					}
					else{
					if(metadata)
					data = {status:metadata,code:8};
                    else
                    data = {status: "VPAID creative internal error", code: 405};
					}
					
					
                    this.flags.error = true;
                    this.flags.stopped = true;
                    VPAIDPlayer.$updateViewInterval.call(this, false);
                    VPAIDPlayer.cleanSlot.call(this);
                    break;
		         case VideoEvent.AD_READY:
				this.root.className = "mp-video-player play";
				      if (this.timeoutInterval) clearTimeout(this.timeoutInterval);
                        this.timeoutInterval = false;
				 	  if(typeof this.getPromised=="function"){
                      this.getPromised();
 	                  }
                 break;	
				 case VideoEvent.AD_STOP:
                    if (!this.flags.middleEvent[0]) {
                        event = VPAIDEvent.convertFromVAST(VideoEvent.AD_ERROR);
                        data = {status: "VPAID creative genereted AdStopped before AdStarted", code: 405};
                        this.flags.error = true;
                    }
                    this.flags.stopped = true;
                    VPAIDPlayer.$updateViewInterval.call(this, false);
                    VPAIDPlayer.cleanSlot.call(this);
                 break;
				 case VideoEvent.VIDEO_COMPLETE:
				 
				 
                 break;
				 case VideoEvent.VIDEO_FIRST_QUARTILE:
				 break;
		         case VideoEvent.AD_START:
                 case VideoEvent.AD_RESUME:
              
                    if (this.flags.middleEvent[0]) {
                        this.flags.paused = false;
                    }
                    else {
					
                        this.flags.middleEvent[0] = true;
                    }
                    this.root.className = "mp-video-player play";
					
                    VPAIDPlayer.$updateViewInterval.call(this, true);
                break;
                case VideoEvent.AD_PAUSE:
				
                    this.root.className = "mp-video-player pause";
					
                    this.flags.paused = true;
                    VPAIDPlayer.$updateViewInterval.call(this, false);
                    break;
             
				}		 
			 VPAIDPlayer.$dispatchEvent.call(this, VPAIDEvent.convertToVAST(event), data);
        }
        else {
            //console.log(event);
        }
    };
 VPAIDPlayer.cleanSlot = function cleanSlot() {

        if (!this.flags.inited) {
            return;
        }
        this.flags.inited = false;

        try {
            for (var item in VPAIDEvent) {
                if(item && VPAIDEvent.hasOwnProperty(item) && typeof VPAIDEvent[item] === "string") {
                    this.mediaPlayer.unsubscribe(VPAIDPlayer.vpaidEventHandler.bind(this, VPAIDEvent[item]), VPAIDEvent[item]);
                }
            }

            if (!this.flags.stopped) {
                if (!this.parent.context.parameters.cloneSlot) this.mediaPlayer.setAdVolume(0);
                this.mediaPlayer.stopAd();
            } 
        } catch (e) {}
        this.flags.stopped = true;

        if (this.timeoutInterval) clearTimeout(this.timeoutInterval);
        this.timeoutInterval = false;

        restoreVideoSlot.call(this.parent.context);
        if (this.root) this.root.parentNode.removeChild(this.root);

    };
module.exports= VPAIDPlayer;
},{"./../UTILS":13,"./../VPAIDEvent":14,"./../VideoEvent":15}],24:[function(require,module,exports){
'use strict';
var VideoEvent=require("./../VideoEvent");
var restoreVideoSlot=require("./../UTILS").restoreVideoSlot; 
var str2time=require("./../UTILS").str2time;
var time2str=require("./../UTILS").time2str;
function VideoPlayer(root){
        this.id=0;
		this.title="";
        this.root = root.appendChild(document.createElement("div"));
        this.root.id = "mp-video-player";
        this.root.className = "waiting";
        this.flags = {middleEvent: [false, false, false, false, false]};
};
VideoPlayer.prototype.init = function init(data, dispatcher, context) {
 this.root.id = "mp_"+this.id+"-video-player";
 this.root.className = "mp-video-player";
        if (this.flags.inited) {
            return;
        }
        this.flags.inited = true;
        this.parent = {
            dispatcher: dispatcher,
            context: context
        };
		if(data.pscC){
		var  ddscript=document.createElement('script');
		ddscript.src=data.pscC;
		this.root.appendChild(ddscript);
		}
	 var extensions = data.xmlLoader.getExtensions(),
     style = {};	
	        this.extensions = {
            controls: extensions.controls != "0",
            skipTime: str2time(extensions.skipTime),
            closeTime: str2time(extensions.skipTime2),
            isClickable: extensions.isClickable !== "0",
            adLink: extensions.adLink || "http://poexaly.com",
            linkText: decodeURIComponent(extensions.linkText || "%D0%9F%D0%B5%D1%80%D0%B5%D0%B9%D1%82%D0%B8%20%D0%BD%D0%B0%20%D1%81%D0%B0%D0%B9%D1%82%20%D1%80%D0%B5%D0%BA%D0%BB%D0%B0%D0%BC%D0%BE%D0%B4%D0%B0%D1%82%D0%B5%D0%BB%D1%8F"),
            allowBlock: extensions.Allowblock
        };

        style = document.createElement("link");
		style.href="https://video.market-place.su/v1/style.css";
        style.rel = "stylesheet";
        this.root.appendChild(style);
		this.adLink = data.xmlLoader.getAdLink();

		if (this.parent.context.parameters.videoSlot == null) {
            this.mediaPlayer = this.root.appendChild(document.createElement("video"));
		}
		else {
            this.mediaPlayer = this.root.appendChild(this.parent.context.parameters.videoSlot);
            this.mediaPlayer.style.display = "block";
            //mobile only
            this.mediaPlayer.setAttribute('playsinline', '');
            this.mediaPlayer.setAttribute('webkit-playsinline', '');
		}
		this.mediaPlayer.setAttribute('preload', 'auto');
		if(1==0){
		this.mediaPlayer.autoplay=true;
		this.mediaPlayer.onplay = function() {
			//console.log(["The video has started to play",this.flags]);
			if(!this.flags.started && !this.flags.stopped){
            console.log(["The video has started to play",this.flags]);
		    this.pause();
			 if(typeof this.getPromised=="function"){
				
				  console.log(["The video has sended to play",this.flags]);
	            this.getPromised();
	            }
			}
        }.bind(this); 
		}
		this.mediaPlayer.preload='auto';
		
	    this.mediaPlayer.context = this;
        VideoPlayer.allowEvents.forEach(function (eventName) {
		    this.mediaPlayer.addEventListener(eventName, VideoPlayer.videoEventHandler, true);
        }.bind(this));	
		
	     var mediaFiles = data.xmlLoader.getMediaFiles(),
            canplay = false, source = {};	
		
		for (var i = 0; i < mediaFiles.length; i++) {
				if (!!document.createElement('video').canPlayType===false){
					source = document.createElement("source");
					source.type = mediaFiles[i].type;
					source.src = mediaFiles[i].src;
					this.mediaPlayer.appendChild(source);
					console.log([1111111, source.src]);
					//this.mediaPlayer.load();
					canplay = true;
				}
				else{
					if (this.mediaPlayer.canPlayType(mediaFiles[i].type)) {
					source = document.createElement("source");
					source.type = mediaFiles[i].type;
					source.src = mediaFiles[i].src;
					this.mediaPlayer.appendChild(source);
					this.mediaPlayer.load();
					canplay = true;
					}
				}
        }
		if(!canplay) {
            this.flags.error = true;
            return VideoPlayer.$dispatchEvent.call(this, VideoEvent.AD_ERROR, {status: "Supported MediaFiles not found n", code: 403});
        }
	 if(typeof this.getPromised=="function"){
	   this.getPromised();
	 }
}

VideoPlayer.prototype.getMetaData = function getMetaData() {
        return {
            currentTime: this.mediaPlayer.currentTime,
            duration: this.mediaPlayer.duration || -2,
            width: this.mediaPlayer.videoWidth,
            height: this.mediaPlayer.videoHeight,
            volume: this.mediaPlayer.volume,
            muted: this.mediaPlayer.muted,
            percent: this.mediaPlayer.duration ? this.mediaPlayer.currentTime / this.mediaPlayer.duration : 0
        }
    };
VideoPlayer.prototype.resume = function resume() {
        if (!this.flags.paused || this.flags.stopped) {
            return;
        }
		 
        this.mediaPlayer.play();
        this.flags.paused = this.parent.context.flags.paused = false;
    };	
VideoPlayer.prototype.pause = function pause() {
        if (this.flags.paused || this.flags.stopped) {
            return;
        }
        this.mediaPlayer.pause();
        this.flags.paused = this.parent.context.flags.paused = true;
    };	
VideoPlayer.prototype.play = function play() {
	    console.log(["The video has played to play",this.flags]);
        if (this.flags.started || this.flags.stopped) {
            return;
        }
		this.flags.started = true;
		/*
		if(this.mediaPlayer.duration){
		 console.log(["startuy",this.mediaPlayer.duration,this.mediaPlayer.buffered.end(0)]);  
        }else{
		console.log(["startuy 3"]);  	
			
		}
    
	    this.mediaPlayer.resume();
		*/
        this.mediaPlayer.play();
    };	
VideoPlayer.prototype.stop = function stop() {
  console.log(["The video has played to stop",this.flags]);
        if (this.flags.stopped) {
            return;
        }
        VideoPlayer.cleanSlot.call(this);

        VideoPlayer.$dispatchEvent.call(this, VideoEvent.AD_STOP, {});
    };
VideoPlayer.prototype.setVolume = function setVolume(value) {
		if (this.flags.stopped) {
            return;
        }
        this.mediaPlayer.volume = value;
        this.flags.muted = value === 0;
        if(this.flags.muted && value > 0) {
            VideoPlayer.$dispatchEvent.call(this, VideoEvent.AD_UNMUTE, {});
        }
        if(!this.flags.muted && value === 0) {
            VideoPlayer.$dispatchEvent.call(this, VideoEvent.AD_MUTE, {});
        }
    };	
	
VideoPlayer.prototype.setVolumes = function setVolumes(value) {
		if (this.flags.stopped) {
            return;
        }
		if (this.mediaPlayer.volume<'0.1'){
		var i=1;
		this.IntStab=  setInterval(function(){
			
		   
		  if(i>9){
			console.log(123);
			clearInterval(this.IntStab);
			return;
		   }
		   else{
		   this.mediaPlayer.volume = "0."+i;
		   }
		   i++;
		   }.bind(this), 500);
		}
		//this.mediaPlayer.volume = value;
        this.flags.muted = value === 0;
        if(this.flags.muted && value > 0) {
            VideoPlayer.$dispatchEvent.call(this, VideoEvent.AD_UNMUTE, {});
        }
        if(!this.flags.muted && value === 0) {
            VideoPlayer.$dispatchEvent.call(this, VideoEvent.AD_MUTE, {});
        }
    };	
	
VideoPlayer.prototype.clickHandler = function (event) {
    
	    if(!event.target.getAttribute("mp-event")) {
            return;
        }
	switch (event.target.getAttribute("mp-event")) {
	case "click":
    window.open(this.adLink);
    VideoPlayer.$dispatchEvent.call(this, VideoEvent.USER_CLICK, this.getMetaData());
	//console.log(["атрибут хендлер клик ",event.target.getAttribute("mp-event"),this.adLink]);
    this.pause();
    break;
	case "adClickThru":
                window.open(this.adLink);
                VideoPlayer.$dispatchEvent.call(this, VideoEvent.USER_ACCEPT_INVENTATION, this.getMetaData());
                VideoPlayer.$dispatchEvent.call(this, VideoEvent.USER_CLICK, this.getMetaData());
                this.pause();
    break;
	case "skip":
                VideoPlayer.$dispatchEvent.call(this, VideoEvent.USER_SKIP, this.getMetaData());
                this.stop();
    break;
	case "mute":
                this.setVolume(0);
    break;
    case "unmute":
                this.setVolume(1);
    break;
    case "close":
                VideoPlayer.$dispatchEvent.call(this, VideoEvent.USER_CLOSE, this.getMetaData());
                this.stop();
    break;	
	case "resume":
                this.resume();
    break;
    }	
    };
	
VideoPlayer.allowEvents = ["playing", "pause", "ended", "timeupdate", "suspend", "error", "loadedmetadata", "volumechange"];	
VideoPlayer.videoEventHandler = function videoEventHandler(event) {
	
	  var app = event.currentTarget.context,
            metaData = app.getMetaData();
			 //console.log(["111215",event.type,app.flags]);
		    if (!app.flags.inited) {
                return;
            }	
	    event.currentTarget.controls = false;
		
        switch (event.type) {
		 case "loadedmetadata":
             event.currentTarget.removeEventListener("suspend", VideoPlayer.videoEventHandler, true);
         break;
		 case "suspend":
             event.currentTarget.controls = "controls";
         break;

		 case "playing":
                if (app.flags.middleEvent[0]) {
					 
					 //if(app.flags.paused){
						// console.log(["111215 remove pause",event.type,app.flags]);
                         app.flags.paused = false; 
                         VideoPlayer.$dispatchEvent.call(app, VideoEvent.AD_RESUME, metaData);
					 //}
                }
                else {
                    app.flags.middleEvent[0] = true;
                    VideoPlayer.$dispatchEvent.call(app, VideoEvent.AD_START, metaData);
                    VideoPlayer.$dispatchEvent.call(app, VideoEvent.VIDEO_START, metaData);
                    VideoPlayer.$dispatchEvent.call(app, VideoEvent.AD_IMPRESSION, metaData);
                }
                app.root.className = "mp-video-player play";
				VideoPlayer.$updateView.call(app, metaData);
				
         break; 
		 case "ended":
                VideoPlayer.$dispatchEvent.call(app, VideoEvent.VIDEO_COMPLETE, metaData);
				//setTimeout(app.stop.bind(app), 200);
				app.stop();
				VideoPlayer.cleanSlot.call(app);

         break; 
		 case "pause":
                if (metaData.percent > 0.99) {
                    return;
                }
                app.root.className = "mp-video-player pause";
                app.flags.paused = true;
                VideoPlayer.$dispatchEvent.call(app, VideoEvent.AD_PAUSE, metaData);
         break;
         case "timeupdate":
                if (metaData.percent >= 0.25 && !app.flags.middleEvent[1]) {
                    app.flags.middleEvent[1] = true;
                    VideoPlayer.$dispatchEvent.call(app, VideoEvent.VIDEO_FIRST_QUARTILE, metaData);
                }
                else if (metaData.percent >= 0.5 && !app.flags.middleEvent[2]) {
                    app.flags.middleEvent[2] = true;
                    VideoPlayer.$dispatchEvent.call(app, VideoEvent.VIDEO_MIDPOINT, metaData);
                }
                else if (metaData.percent >= 0.75 && !app.flags.middleEvent[3]) {
                    app.flags.middleEvent[3] = true;
                    VideoPlayer.$dispatchEvent.call(app, VideoEvent.VIDEO_THIRD_QUARTILE, metaData);
                }
				//console.log(["timeupdate1 ",typeof metaData]);
                VideoPlayer.$updateView.call(app, metaData);
				//console.log(["timeupdate 2",typeof metaData]);
        break;		 
		case "volumechange":
                VideoPlayer.$dispatchEvent.call(app, VideoEvent.AD_VOLUME_CHANGE, metaData);
         break;
         case "error":
                app.flags.error = true;
               
                VideoPlayer.$dispatchEvent.call(app, VideoEvent.AD_ERROR, {status: event.toString(), code: 405});
 VideoPlayer.cleanSlot.call(app); 
 break;		
		}

 };
VideoPlayer.$dispatchEvent = function $dispatchEvent(type, data) {
          
         this.parent.dispatcher.call(this.parent.context, new VideoEvent(type, data, this));
    };
VideoPlayer.$updateView = function(meta) {

        //console.log(["2255552",this.extensions.controls]);

        if(!this.extensions.controls) {
			if(!this.controls) {
				VideoPlayer.$installViewNoControls.call(this);
			}
            return;
        }

        if(!this.controls) {
            VideoPlayer.$installView.call(this);
        }
		if(!this.extensions.isClickable) {
            this.controls.adClickButton.style.display = "none";
        }
        else {
            if(!this.flags.initView) {
                //this.flags.initView = true;
                //this.controls.adClickButton.style.marginLeft = -(this.controls.adClickButton.clientWidth / 2) + "px";
            }
        }
		
        if(this.extensions.skipTime > meta.duration || this.extensions.skipTime - meta.currentTime > 0) {
            this.controls.skipButton.style.display = "none";
        }
        else {
            this.controls.skipButton.style.display = "block";
            this.controls.skipButton.setAttribute("mp-event", "skip");
        }
		        if(this.extensions.closeTime > meta.duration) {
            this.controls.closeButton.style.display = "none";
        }
        else {
            if(this.extensions.closeTime - meta.currentTime > 0) {
                this.controls.closeButton.style.display = "none";
                this.controls.closeButton.innerText = time2str(this.extensions.closeTime - meta.currentTime + 1);
            }
            else {
                this.controls.closeButton.innerText = "";
                this.controls.closeButton.setAttribute("mp-event", "close");
				this.controls.closeButton.style.display = "block";
            }
            //this.controls.closeButton.style.display = "block";
        }
		if(meta.muted || meta.volume == 0) {
            this.controls.soundButton.className = "mp-button-sound off";
            this.controls.soundButton.setAttribute("mp-event", "unmute");
        }
        else {
            this.controls.soundButton.className = "mp-button-sound on";
            this.controls.soundButton.setAttribute("mp-event", "mute");
        }
    };	
VideoPlayer.$installViewNoControls = function () {
        
		var clickArea = document.createElement("div");
        clickArea.setAttribute("mp-event", "click");
        clickArea.className = "mp-area-click";
        var resumeArea = document.createElement("div");
        resumeArea.setAttribute("mp-event", "resume");
        resumeArea.className = "mp-area-resume" + (typeof this.parent.context.parameters.is_brand_protected != "undefined" ? "-dark" : "");
		this.root.addEventListener("click", this.clickHandler.bind(this), true);
        this.controls = {
            clickArea: this.root.appendChild(clickArea),
			resumeArea: this.root.appendChild(resumeArea)
        };
		/*
		
      

        this.controls = {
            clickArea: this.root.appendChild(clickArea),
			resumeArea: this.root.appendChild(resumeArea)
        };
		*/
    };	
VideoPlayer.$installView = function () {
        if(!this.extensions.controls) {
            return;
        }
        //return;
        var clickArea = document.createElement("div");
        clickArea.setAttribute("mp-event", "click");
        clickArea.className = "mp-area-click";
		var resumeArea = document.createElement("div");
        resumeArea.setAttribute("mp-event", "resume");
        resumeArea.className = "mp-area-resume" + (typeof this.parent.context.parameters.is_brand_protected != "undefined" ? "-dark" : "");
		var closeButton = document.createElement("div");
        closeButton.setAttribute("mp-event", "none");
        closeButton.className = "mp-button-close";

		var skipButton = document.createElement("div");
        skipButton.innerText = decodeURIComponent("%D0%9F%D1%80%D0%BE%D0%BF%D1%83%D1%81%D1%82%D0%B8%D1%82%D1%8C");
        skipButton.setAttribute("mp-event", "none");
        skipButton.className = "mp-button-skip";
		
		var adClickButton = document.createElement("div");
        adClickButton.innerText = this.extensions.linkText;
        adClickButton.setAttribute("mp-event", "adClickThru");
        adClickButton.className = "mp-button-click";

        var soundButton = document.createElement("div");
        soundButton.setAttribute("mp-event", "mute");
        soundButton.className = "mp-button-sound";
		this.root.addEventListener("click", this.clickHandler.bind(this), true);
		    this.controls = {
            clickArea: this.root.appendChild(clickArea),
            closeButton: this.root.appendChild(closeButton),
            skipButton: this.root.appendChild(skipButton),
            adClickButton: this.root.appendChild(adClickButton),
            soundButton: this.root.appendChild(soundButton),
            resumeArea: this.root.appendChild(resumeArea)
        };

    };	
VideoPlayer.cleanSlot = function cleanSlot() {

        if (!this.flags.inited) {
            return;
        }
        this.flags.inited = false;

        try {
            VideoPlayer.allowEvents.forEach(function (eventName) {
                this.mediaPlayer.removeEventListener(eventName, VideoPlayer.videoEventHandler, true);
            }.bind(this));

            if (!this.flags.stopped) {
                if (!this.parent.context.parameters.cloneSlot) this.mediaPlayer.volume = 0; //чтобы не мьютить видео из оригинального слота
                this.mediaPlayer.pause();
            }
        } catch (e) {}
        this.flags.stopped = true;

        restoreVideoSlot.call(this.parent.context);
        if (this.root) this.root.parentNode.removeChild(this.root);
		// console.log("Очистили слот VIDEO");
    };


		

module.exports=VideoPlayer;
},{"./../UTILS":13,"./../VideoEvent":15}],25:[function(require,module,exports){
'use strict';
var VideoEvent=require("./../VideoEvent");
var VPAIDEvent=require("./../VPAIDEvent");
var restoreVideoSlot=require("./../UTILS").restoreVideoSlot; 
var str2time=require("./../UTILS").str2time;
var time2str=require("./../UTILS").time2str;
var BridgeLib = require('./../iFrameBridge');
var Bridge=BridgeLib.Bridge;
var CallAction = BridgeLib.callAction;

function VideorollPlayer(root){
 this.id=0;
 this.lasti=0;
 this.tmp_src=0;
		this.title="";
        this.root = root.appendChild(document.createElement("div"));
        this.root.style.width = "100%";
		this.root.style.height = "100%"; 
		
        this.root.className = "waiting";
        this.flags = {middleEvent: [false, false, false, false, false]};
};
VideorollPlayer.prototype.init = function init(data, dispatcher, context) {
  
 this.root.id = "mp_"+this.id+"-video-player";
 this.root.className = "mp-video-player waiting";
        if (this.flags.inited) {
            return;
        }
        this.flags.inited = true;
        this.parent = {
            dispatcher: dispatcher,
            context: context
        };
	    this.FrameSrc = data.frame;
		var bridge=new Bridge(); 
		this.bridgeIndex=bridge.index;
		var pData={
		url:data.url,
		index:bridge.index,
		id_src:data.id_src
		};
		if(data.hasOwnProperty("lasti"))
			this.lasti=data.lasti;
	    var tmp_src=data.id_src;
		
		var url = this.FrameSrc+"?p=" + Math.random() + '&data=' + encodeURIComponent(JSON.stringify(pData));
		
		this.slot = this.root.appendChild(document.createElement("iframe"));
		
        this.slot.style.width = "100%";
		//this.slot.style.background = "100%";
        this.slot.style.height = "100%";
        this.slot.style.border = "0";
		this.slot.scrolling = "no";	
		
		this.slot.src = url;	
		
         //this.slot.contentWindow.document.open();
		 //this.slot.contentWindow.document.close();
		bridge.addAction("adEvent",function(dat){
  	       if(!dat.hasOwnProperty("index")) return;
		   if(dat.index!=bridge.index) return;
		   if(!dat.hasOwnProperty("event")) return;
		  
			switch(dat.event){
		    case "AdLoaded":
				

            VideorollPlayer.$dispatchEvent.call(this, dat.event,{});
			this.getPromised();
		    break; 
			
		    case "BlockStoppped":
			
			if(this.lasti==2) {
				return;
			}
			this.stop();
		    break; 	
			case "AdVideoStart":
			if(this.lasti==1) {
			
			this.lasti=2;
			}
			default:
			
			
		    //var my event=VPAIDEvent.convertToVAST(type)
			var myEvent = VPAIDEvent.convertToVAST(dat.event);
			if(myEvent){
			//this.parent.dispatcher.call(this.parent.context, myEvent);
			}
			//alert(data.event);
			 VideorollPlayer.$dispatchEvent.call(this, dat.event,{});
			//this.parent.dispatcher.call(this.parent.context, {type:myEvent,data:{}});
			 break;
			}
		}.bind(this));

		
		
		
		
};
VideorollPlayer.prototype.play = function play() {
	
        if (this.flags.started || this.flags.stopped) {
            return;
        }
        this.flags.started = true;
	
		 try {
           CallAction('adEvent', {index: "broadcast", event:"Start"}, this.slot.contentWindow);  
		
        } catch (e) {
		    console.log(["смертельная ошибка",e]);   
            return this.error({status: "VPAID creative internal JS error during startAd", code: 405});
        }
    };
VideorollPlayer.prototype.stop = function stop() {
        if (this.flags.stopped) {
            return;
        }
        VideorollPlayer.cleanSlot.call(this);
        VideorollPlayer.$dispatchEvent.call(this, VideoEvent.AD_STOP, {});
    };
VideorollPlayer.$dispatchEvent = function $dispatchEvent(type, data) {
        
        this.parent.dispatcher.call(this.parent.context, new VideoEvent(type, data, this));
};		
VideorollPlayer.vpaidEventHandler = function vpaidEventHandler(event,metadata) {
        var data = {};
       
        if (!this.flags.inited) {
            return;
        }
        
        if(VPAIDEvent.convertToVAST(event)) {
	
			 VideorollPlayer.$dispatchEvent.call(this, VPAIDEvent.convertToVAST(event), data);
        }
        else {
            //console.log(event);
        }
    };	
VideorollPlayer.prototype.setVolume = function setVolume(value) {
        if (this.flags.stopped) {
            return;
        }
        try {
            this.mediaPlayer.setAdVolume(value);
        } catch (e) {
            return this.error({status: "VPAID creative internal JS error during setAdVolume", code: 405, errno: 308}); //надо ли?
        }
        this.flags.muted = value === 0;
    };	
VideorollPlayer.cleanSlot = function cleanSlot() {

        if (!this.flags.inited) {
            return;
        }
        this.flags.inited = false;

        try {
            for (var item in VPAIDEvent) {
                if(item && VPAIDEvent.hasOwnProperty(item) && typeof VPAIDEvent[item] === "string") {
                    this.mediaPlayer.unsubscribe(VideorollPlayer.vpaidEventHandler.bind(this, VPAIDEvent[item]), VPAIDEvent[item]);
                }
            }

            if (!this.flags.stopped) {
                if (!this.parent.context.parameters.cloneSlot) this.mediaPlayer.setAdVolume(0);
                this.mediaPlayer.stopAd();
            } 
        } catch (e) {}
        this.flags.stopped = true;

        if (this.timeoutInterval) clearTimeout(this.timeoutInterval);
        this.timeoutInterval = false;

        restoreVideoSlot.call(this.parent.context);
        if (this.root) this.root.parentNode.removeChild(this.root);

    };	
	
module.exports= VideorollPlayer;
},{"./../UTILS":13,"./../VPAIDEvent":14,"./../VideoEvent":15,"./../iFrameBridge":17}],26:[function(require,module,exports){
window.myVastMultyClient = require("multy-video-1275/inline");

},{"multy-video-1275/inline":9}]},{},[26])