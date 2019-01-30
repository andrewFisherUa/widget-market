'use strict';
var yaWidgetLib = require('./yaWidget');
var BridgeLib = require('./iFrameBridge');
var Bridge = BridgeLib.Bridge;
var CallAction = BridgeLib.callAction;
var ajax = require('./httpclient').ajax;
function symmetricDifference(a1, a2) {
  var result = [];
  for (var i = 0; i < a1.length; i++) {
    if (a2.indexOf(a1[i]) === -1) {
      result.push(a1[i]);
    }
  }
  for (i = 0; i < a2.length; i++) {
    if (a1.indexOf(a2[i]) === -1) {
      result.push(a2[i]);
    }
  }
  return result;
}

var WidgetEditor = function (container,src) {
	
this.container=container.get(0);
this.typeResourse=1;
if(this.container.dataset.hasOwnProperty("idtype"))
this.typeResourse=this.container.dataset.idtype;	
this.id = this.container.dataset.id;
if(this.typeResourse==3){
	if (this.container.id=='mobile_area'){
		this.src=src||"//widget.market-place.su/widget_tizer/mobile_render"
		}
		else{
		this.src=src||"//widget.market-place.su/widget_tizer/render"
		}
}else{
if (this.container.id=='mobile_area'){
this.src=src||"//widget.market-place.su/widget/mobile_render"
}
else{
this.src=src||"//widget.market-place.su/widget/render"
}
}
this.iFrame=null;
this.currentTemplateName='';
this.defCategories=[];
this.defH1='';
this.defDriver=0;
this.summa_from=0;
this.summa_to=0;
this.currentTemplateRows={row:0,cols:0};
this.params={width:"200px",height:"200px",categories:''}; 
this.plugins={};

 this.Bridge = new Bridge();
 this.index=this.Bridge.index;
 console.log(["индекс",this.index]);
    var self= this;
    this.Bridge.addAction('resize', function (data) {
	    //alert("resize приехал "+data.size);
        self.resize(data.size);
		//self.loadPlugins(["WidgetSliderPlugin"]);
        // self.fire('ready');
        // self.appendInFrameElement({id:"geo",className:"geo-frame"},'<input value="inframe"  type="button">');
    });
    this.Bridge.addAction('adExec', function (data) {
		 //var str ='  console.log(["exec приехал ура"]); ';
		 //eval(str);
	     console.log(["exec приехал ",data]);
		 if(data.func)
		 eval(data.func);
    });	
	
	
	 this.on('ready', function (e) {
	 //alert("я тоже тоже");
	 });
	 this.readyState=true;
};

WidgetEditor.prototype.on = function (eventName, handler) {

    if (typeof this.events == "undefined") {
        this.events = {};
    }
    if (typeof this.events[eventName] == "undefined") {
        this.events[eventName] = [];
    }
    this.events[eventName].push(handler);
};
WidgetEditor.prototype.checkReloadin = function (params) {
	//summa_from
	//summa_to
console.log(params);
var sumaBlack=0;
var _diff=[];
var _diffH1=0;

if(params.hasOwnProperty("categories")){
	_diff=symmetricDifference(params.categories,this.defCategories);
	this.defCategories=params.categories;
	
}

//params.h1=params.h1.replace(/\&/,'\&');
if(params.hasOwnProperty('h1')){
	if(this.defH1!=params.h1){
	this.defH1=params.h1;
	_diffH1=1;
    }
}
if(params.hasOwnProperty('driver')){
	if(this.defDriver!=params.driver){
		
	    this.defDriver=params.driver;
	   _diffH1=1;
	//return true;
    }
}
if(params.hasOwnProperty('summa_from')){
	if(this.summa_from != params.summa_from){
		this.summa_from = params.summa_from;
		sumaBlack=1;
	}
}
if(params.hasOwnProperty('summa_to')){
	if(this.summa_to != params.summa_to){
		this.summa_to = params.summa_to;
		sumaBlack=1;
	}
}


if(this.currentTemplateRows.cols!=params.cols || this.currentTemplateRows.row!=params.row || this.currentTemplateName!=params.name){
this.currentTemplateRows.cols=params.cols;
this.currentTemplateRows.row=params.row;
this.currentTemplateName=params.name;
return true;
}
if(sumaBlack) {return true;}
   if(_diff.length || _diffH1){
	return true;
	}
this.currentTemplateRows.cols=params.cols;
this.currentTemplateRows.row=params.row;
this.currentTemplateName=params.name;

//return true;

if(params.hasOwnProperty("name")){
if(this.currentTemplateName==params.name){

    var pack = {
        type: "style",
        style: '#widget {width: 50px !important;\
        max-width: 50px !important;}'
    };

if (this.container.id=='mobile_area'){
	var pack={
exec: '\
	 var TempName="'+params.name+'";\
  	 var myStyles= document.styleSheets;\
	 var sheet=null;\
	 for(var i=0,j=myStyles.length;i<j;i++){\
	 sheet=myStyles[i];\
	 }\
	 if(sheet){\
	 var foot="'+params.mobile_background+'".split(",");\
	 foot[3]="1)";\
	 var foot_back=foot.join(",");\
	 sheet.cssRules[0].style.background="'+params.mobile_background+'";\
	 sheet.cssRules[0].style.fontFamily="'+params.mobile_font_family+'";\
	 sheet.cssRules[1].style.background="'+params.background_model+'";\
	 sheet.cssRules[2].style.background="'+params.background_model_hover+'";\
	 }\
    ', /* выполняется во фрейме через eval sheet.insertRule("#widget { max-width:10px } ", 0); \ */
        src: "", // путь к файлу
        onload: '' //
};
}
else{
var pack={
exec: '\
	 var TempName="'+params.name+'";\
  	 var myStyles= document.styleSheets;\
	 var sheet=null;\
	 for(var i=0,j=myStyles.length;i<j;i++){\
	 sheet=myStyles[i];\
	 }\
	 if(sheet){\
	 var foot="'+params.background+'".split(",");\
	 foot[3]="1)";\
	 var foot_back=foot.join(",");\
	 console.log(foot_back);\
	 console.log(["плеер",sheet.cssRules[0]]); \
	 sheet.cssRules[0].style.maxWidth="'+params.width+'";\
	 sheet.cssRules[0].style.width="'+params.width+'";\
	 sheet.cssRules[0].style.height="'+params.height+'";\
	 sheet.cssRules[0].style.borderRadius="'+params.border_radius+'px";\
	 if (TempName=="module-block-yandex_left"){\
	 sheet.cssRules[0].style.background=foot_back;\
	 }\
	 else{\
	 sheet.cssRules[0].style.background="'+params.background+'";\
	 }\
	 sheet.cssRules[0].style.fontFamily="'+params.font_family+'";\
	 sheet.cssRules[1].style.background=foot_back;\
	 sheet.cssRules[2].style.borderColor="'+params.border_color+'";\
	 sheet.cssRules[2].style.borderWidth="'+params.border_width+'px";\
	 sheet.cssRules[2].style.borderRadius="'+params.border_radius+'px";\
	 sheet.cssRules[3].style.background="'+params.background_model+'";\
	 sheet.cssRules[4].style.background="'+params.background_model_hover+'";\
	 sheet.cssRules[5].style.background="'+params.background_model_hover+'";\
	 sheet.cssRules[6].style.fontSize="calc(14px * '+params.font_size+')";\
	 sheet.cssRules[7].style.fontSize="calc(16px * '+params.font_size+')";\
	 }\
    ', /* выполняется во фрейме через eval sheet.insertRule("#widget { max-width:10px } ", 0); \ */
        src: "", // путь к файлу
        onload: '' //
};
}


	
//alert(11);	
if(this.defDriver==2){
setTimeout(function(){
	this.appendInFrameJS(pack);
}.bind(this),4000);
}else{
	this.appendInFrameJS(pack);
}

//this.appendInFrameCSS(pack);	

return false;
}
//this.currentTemplateName=params.name;
}
return true;
}

// функция в разработке. pars - это параметры, которые передаются в editor.blade
WidgetEditor.prototype.reloadYandexAPI = function (pars) {
	pars = pars||{};

	// if(!this.checkReloadin(params)){
	// return;
	// }
	console.log(["Шаримся в ф-ции reloadYandexAPI",pars]);

	for (var key in pars){

	console.log(["Шаримся в ф-ции reloadYandexAPI",key,pars[key]]);
	if(!pars.hasOwnProperty(key))
	pars[key]=pars[key];
	}
	// var self=this;

	for (let key in pars){alert(key)}

	// alert(pars["type"]);
	
	// pars ?  alert(pars) : alert('pars пустой') ;
	pars.hasOwnProperty("type") ?  alert(pars["type"]+" есть") : alert('pars пустой') ;

}


WidgetEditor.prototype.createIframe = function (params) {
params=params||{};

if(!this.checkReloadin(params)){
return;
}

for (var pkey in this.params){

console.log(["пключ рел",pkey,params[pkey]]);
if(!params.hasOwnProperty(pkey))
params[pkey]=this.params[pkey];
}
var self=this;

this.getProducts(params,function d1(res){

//console.log([""]);
//if(res=="yusrom2328050"){
	//var z1 = new yaWidgetLib(self.container);
	//return;

//}

var data=JSON.parse(res);
console.log(["дата !",data]);
if(data.hasOwnProperty('hash')){
	if(data.hash=="myyandex"){
		//console.log(["ffff->",data]);
	var z1 = new yaWidgetLib(self.container,data.clid); 
	return;
	}
	
self.iFrame=document.createElement("iframe");
    self.iFrame.scrolling = "yes";
    self.iFrame.style.border = "0";
    self.iFrame.style.margin = "0";
    self.iFrame.style.width = "100%";
	if (self.container.id=='mobile_area'){
		self.iFrame.style.height = "310px";
	}
	else{
	self.iFrame.style.height = "600px";
	}
var prcn='';
if(data.hasOwnProperty('cnt')){
	var prcn='&cnt='+data.cnt;
}	

var url=self.src+'?data='+encodeURIComponent(JSON.stringify(params))+'&index='+self.index+'&hash='+data.hash+prcn;
	console.log(["утро 2",url]);
self.iFrame.src=url;

if (self.container.id=='mobile_area'){
	var preview=document.getElementById('preview_mobile_cut_cut');
	preview.appendChild(self.iFrame);
	var text=document.createElement('span');
	text.id="mobile_text";
	text.textContent='"Олигархи, вперед на подвиги !!!"';
	preview.appendChild(text);
}
else{
self.container.appendChild(self.iFrame);
}
}else{
console.log(["ненайду концов"]);
}

},function d2(error){

}); 


};
WidgetEditor.prototype.reloadIframe = function (params) {
  if(!this.iFrame)
	  return this.createIframe(params);
if(!this.checkReloadin(params)){

return;
}

var self=this;
for (var pkey in this.params){
console.log(["пключ",pkey,params[pkey]]);
if(!params.hasOwnProperty(pkey))
params[pkey]=this.params[pkey];
}


this.getProducts(params,function d1(res){
var data=JSON.parse(res);

if(data.hasOwnProperty('hash')){
var prcn='';
if(data.hasOwnProperty('cnt')){
	var prcn='&cnt='+data.cnt;
}
console.log(["готов забугор",params]); 

var url=self.src+'?data='+encodeURIComponent(JSON.stringify(params))+'&index='+self.index+'&hash='+data.hash+prcn;
console.log(["утро",url]);
self.iFrame.src=url;

if (self.container.id=='mobile_area'){
	var preview=document.getElementById('preview_mobile_cut_cut');
	preview.appendChild(self.iFrame);
	var text=document.createElement('span');
	text.id="mobile_text"
	text.textContent='" Есть ещё кто то кто лучше других"';
	var oldtext=document.getElementById('mobile_text');
	if(oldtext)
	preview.removeChild(oldtext);
	preview.appendChild(text);
}
else{
self.container.appendChild(self.iFrame);
}
}else{
	self.iFrame.src="";
console.log(["ненайду концов 2"]);

}},function d2(error){
console.log(["ненайду концов 1"]);
});
};
WidgetEditor.prototype.reDrowContainer = function () {

	if (this.container.id=='mobile_area'){
		
	}else{
		return;
	}
	var oldDiv=document.getElementById("preview_mobile_cut_cut");
	if(oldDiv){
	var newDiv=document.createElement('div');
	newDiv.id=oldDiv.id;
	newDiv.innerHTML='Олимпийские игры, которые пройдут в южнокорейском Пченчхане в феврале 2018 года.';
    oldDiv.parentNode.replaceChild(newDiv, oldDiv);
	}


}
WidgetEditor.prototype.getProducts = function (params,callback,onerror) {
params=params||{};
var packets={};

	var defaults={
     geo_id:'' 
    ,url:window.location.href
    ,text:{models:{},texts:{}}
	,categories:''
	,count:3
	,nostat:1
	,driver:1
	,summa_from:0
	,summa_to:0
    };
	this.reDrowContainer();
	for(var pkey in defaults){
	if(params.hasOwnProperty(pkey) && params[pkey])
    packets[pkey]=params[pkey];
	else
	packets[pkey]=defaults[pkey]; 
    }
	
	if(params.hasOwnProperty('h1')){
		packets.userText=params.h1;

		
	}
	//if(packets.driver==2){
    //var url="//newapi.market-place.su/dev?mth=api&id="+this.id+"&rnd="+Math.random()+"&data="+encodeURIComponent(JSON.stringify(packets));
	//}else{
		//console.log(['editor say request']);
	 var url="//request.market-place.su/dev?mth=api&id="+this.id+"&rnd="+Math.random()+"&data="+encodeURIComponent(JSON.stringify(packets));
	//}
    
    callback=callback||function(){};
    onerror=onerror||function(){};
	
    ajax(url, {
        successFn: function (res) {
			console.log(['непойду и не буду',res]);	
            callback(res);
        },
        errorFn: function (error) {
            onerror(error);
        }
    });
	
};
WidgetEditor.prototype.resize = function (size) {
    

    var width = size.width;
   
    var height = (parseInt(size.height) + 55) + "px";
    this.iFrame.style.width = width;
    this.iFrame.style.height = height;

};
WidgetEditor.prototype.unloadPlugins = function () {

};
WidgetEditor.prototype.loadPlugins = function (plugins) {
for(var i=0,j=plugins.length;i<j;i++){
 
  }
};
WidgetEditor.prototype.appendScript=function (src,callback){
    callback=callback||function(){};
    var script=document.createElement('script');
    script.src=src;

    script.onload=function(){
            callback();
    };
    document.body.appendChild(script);
};
WidgetEditor.prototype.appendInFrameCSS = function (package_) {
    var pack = {
        type: "style",
        style: ".someclass{ color:red;}"
    };
    for (var i in pack) {
        if (package_.hasOwnProperty(i)) {
            pack[i] = package_[i];
        }
    }
    this.sendToFrame('appendStyle', pack);
	console.log('style');
};
WidgetEditor.prototype.appendInFrameJS = function (package_) {
 var pack = {
        exec: "", // выполняется во фрейме через eval
        src: "", // путь к файлу
        onload: '' // выполняется eval после подгрузки src
    };
  for (var i in pack) {
        if (package_.hasOwnProperty(i)) {
            pack[i] = package_[i];
        }
    }	
	this.sendToFrame('appendScript', pack);
};
     
WidgetEditor.prototype.sendToFrame = function (action, data,cnt) {
	cnt = cnt || 0;
    if (!this.readyState)return;
	if(!this.iFrame){
		console.log(["дата фрейма",data]);
		if(1==0 && cnt<3){
		setTimeout(function(){
			this.sendToFrame(action, data, (cnt+1));
		    }.bind(this),700);
		}
		return;
		
	}
    data.index = this.index;
	if(!this.iFrame.contentWindow){
		console.log(["нету фрейма или"]); 
		if(1==0 && cnt<4){
		setTimeout(function(){
			this.sendToFrame(action, data,(cnt+1));
		    }.bind(this),700);
		}
		return;
	}
	
	console.log(["по фрейму",action,data,this.iFrame.contentWindow]); 
    CallAction(action, data, this.iFrame.contentWindow);
};

module.exports = WidgetEditor;