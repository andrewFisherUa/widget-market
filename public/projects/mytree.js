(function e(t,n,r){function s(o,u){if(!n[o]){if(!t[o]){var a=typeof require=="function"&&require;if(!u&&a)return a(o,!0);if(i)return i(o,!0);throw new Error("Cannot find module '"+o+"'")}var f=n[o]={exports:{}};t[o][0].call(f.exports,function(e){var n=t[o][1][e];return s(n?n:e)},f,f.exports,e,t,n,r)}return n[o].exports}var i=typeof require=="function"&&require;for(var o=0;o<r.length;o++)s(r[o]);return s})({1:[function(require,module,exports){
'use strict';
var  Httpclient=
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
console.log([17,xhttp.status]);

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
        } catch (err){} 
    }
};
module.exports = Httpclient;
},{}],2:[function(require,module,exports){
'use strict';
/**
 * Created by mambrin on 28.03.17.
 */
function mynode(parent)
{
this.parent=parent;
this.li=null;
this.ul=null;
this.name=null;
this.checkbox=null;
this.id=null;
this.checkedTops={};
this.onclickFn =function(e){
};
this.oncheckFn =function(e){

};
};
mynode.prototype.clearBranch=function(parent) {
while (parent.firstChild) {
    	var ul=parent.firstChild.querySelector('ul');
		if(ul){
		this.clearBranch(ul);
		}
		parent.removeChild(parent.firstChild);
	}
	parent.parentNode.removeChild(parent);
};
mynode.prototype.checkResult=function(parent,res) {

 for ( var child = parent && parent.firstChild; child; child = child.nextSibling ) {
	    if ( child.nodeType === 1 && (child.nodeName.toLowerCase() === 'li' )) 
		{
		
		if(child.firstChild.querySelector('input').checked){
		res[child.dataset.id]=child.firstChild.lastChild.innerHTML;
		}else{
		var ul=child.querySelector('ul');
		if(ul){
		this.checkResult(ul,res);
		}
		}
    	}
	  }
};
mynode.prototype.setInputByChildren=function() {
var cnt=0;
var checked=true;
if(this.ul){
var ls=this.ul.querySelectorAll("li input"); 
for(var i=0,j=ls.length;i<j;i++){
cnt++;
if(!ls[i].checked){
  checked=false;
 }
}
if(cnt){
 this.checkbox.checked=checked;
}
}
if(this.parent){
this.parent.setInputByChildren();
}else{

this.checkedTops={};
this.checkResult(this.ul,this.checkedTops);
this.afterChecked.call(this);

}
};
mynode.prototype.checkInput=function(ul) {
if(this.checkbox && this.checkbox.checked){
return true;
}
return false;
};
mynode.prototype.getContailer=function(ul) {
this.ul=ul;
if(this.li){
this.li.appendChild(ul);
}
};
mynode.prototype.show=function(ul,params) {
if(this.parent==null){
ul.appendChild(this.createTopNode(params));
}
else{
ul.appendChild(this.createTopNode(params));
}
};
mynode.prototype.createTopNode=function(params) {
var self=this;
//console.log([22,params.childrenCount]);
this.name=params.name;
this.id=params.id;
this.li=document.createElement("LI");
this.li.dataset.id=params.id;
var dd1=document.createElement("DIV");
this.checkbox=document.createElement("input");
this.checkbox.type="checkbox";

if(params.hasOwnProperty("ch2") && params.ch2)
this.checkbox.checked=true;
this.checkbox.onclick=function(){
if(self.ul){
var ls=self.ul.querySelectorAll("li input"); 
for(var i=0,j=ls.length;i<j;i++){
ls[i].checked=this.checked;
}
}
if(self.parent){
self.parent.setInputByChildren();
}
};
if(this.parent && this.parent.checkInput())
this.checkbox.checked=true;
var b1=document.createElement("span");
b1.className="pushopen";
var n1=document.createElement("span");
if(params.hasOwnProperty("found") && params.found)
n1.style.color="#0000FF";
n1.innerHTML=this.name;
if(params.childrenCount){
b1.innerHTML="+";
b1.style.cursor="pointer";
b1.onclick=function (){
self.onclickFn.call(self);
}
n1.onclick=function (){
self.onclickFn.call(self);
}
}
else
b1.innerHTML=" ";

this.li.appendChild(dd1);
dd1.appendChild(b1);
dd1.appendChild(this.checkbox);
dd1.appendChild(n1);
return this.li;
};
module.exports = mynode; 

},{}],3:[function(require,module,exports){
'use strict';
/**
 * Created by mambrin on 28.03.17.
 */
var mynode= require('./mynode');
var httpclient = require('./httpclient');
function mytree(bodyobj)
{
this.rootId=90401;
this.rootName="Категории";
this.body=bodyobj;
this.rootNode=new mynode(null);
function afterChecked(){
console.log([111,"настаивай как хочешь"]);
}
this.rootNode.afterChecked=afterChecked;
var ul=document.createElement("UL");
this.body.appendChild(ul); 
var params ={name:this.rootName,id:this.rootId};
this.rootNode.show(ul,params); 
};
mytree.prototype.setFunctionCh2=function(fn) {
this.rootNode.afterChecked=fn;
};
mytree.prototype.openInd=function(args) {
if(!args.psg.length) return;

var url='https://widget.market-place.su/rekrut-product/admin/categories/0/open?data='+encodeURIComponent(JSON.stringify(args));
var self=this;
var parent=this.rootNode;

httpclient.ajax(url,{errorFn:function(res){
 console.log([1,'error',res]);
},successFn:function(res){
		try{
		
			var config=JSON.parse(res);
			console.log([558,"tree conf --",config]); 
			
			if(config.hasOwnProperty(self.rootId)){
			// alert(self.rootNode.innerHTML);
			// self.rootNode.clearBranch(self.rootNode.ul);
			//  self.openBranch(config.data,{parent:parent});
			self.openTree(config,{parent:parent});
			parent.setInputByChildren();
			}
		}catch(e){
			console.log('битая конфигурация',e);
		}
},withCredentials:true});

}
mytree.prototype.openSearch=function(args) {

if(!args.name) return;
args.psg=[];
var ind;
for(ind in this.rootNode.checkedTops)
{
args.psg.push(ind);
}
var url='https://widget.market-place.su/rekrut-product/admin/categories/0/research?data='+encodeURIComponent(JSON.stringify(args));
var self=this;
var parent=this.rootNode;
httpclient.ajax(url,{errorFn:function(res){
 console.log([1,'error',res]);
},successFn:function(res){
		try{
			var config=JSON.parse(res);
			console.log([558,"tree conf --",config]); 
			if(config.hasOwnProperty(self.rootId)){
			self.rootNode.clearBranch(self.rootNode.ul);
			self.openTree(config,{parent:parent});
			}
		}catch(e){
			console.log('битая конфигурация',e);
		}
},withCredentials:true});
};
mytree.prototype.openStart=function(args) {
args=args||{};
var url='https://widget.market-place.su/rekrut-product/admin/categories';
if(args.hasOwnProperty("id")){
url+="/"+args.id;
}
var parent=this.rootNode;
if(args.hasOwnProperty("parent")){
var parent=args.parent;
}
var self=this;
httpclient.ajax(url,{errorFn:function(res){
 console.log([1,'error',res]);
},successFn:function(res){
		try{
			var config=JSON.parse(res);
            self.openBranch(config.data,{parent:parent});
		}catch(e){
			console.log('битая конфигурация',e);
		}
	},withCredentials:true});
};
mytree.prototype.openTree=function(nodes,args) {
var self=this;
if(!nodes.hasOwnProperty([args.parent.id])) return;
var onClickOpen = function(e){
   var spi=this.li.querySelector("span.pushopen");
   if(this.ul){
  
   if(this.li.className==''){
   this.li.className='cl';
   spi.innerHTML='+';
   }else{
   this.li.className='';
   spi.innerHTML='-';
   }
   return;
   }
   spi.innerHTML='-';
   var params={id:this.id,parent:this};
   self.openStart(params);
 }
 var ul=document.createElement("UL");
 args.parent.getContailer(ul); 
 var x;
 for(x in nodes[args.parent.id]){
     var myNode=new mynode(args.parent);
	 myNode.onclickFn=onClickOpen;
	 myNode.show(ul, nodes[args.parent.id][x]);
	 self.openTree(nodes,{parent:myNode});
    
 }
};
mytree.prototype.openBranch=function(nodes,args) {
var self=this;
var onClickOpen = function(e){
   var spi=this.li.querySelector("span.pushopen");
   if(this.ul){
  
   if(this.li.className==''){
   this.li.className='cl';
   spi.innerHTML='+';
   }else{
   this.li.className='';
   spi.innerHTML='-';
   }
   return;
   }
   spi.innerHTML='-';
   var params={id:this.id,parent:this};
   self.openStart(params);
 }
nodes=nodes||[{name:"лист 1",id:1}
,{name:"лист 2",id:2}
,{name:"лист 3",id:3}
,{name:"лист 4",id:4}
,{name:"лист 5",id:5}];
var ul=document.createElement("UL");
args.parent.getContailer(ul); 
 for(var i=0,j=nodes.length;i<j;i++){
    var myNode=new mynode(args.parent);
	myNode.onclickFn=onClickOpen;
	myNode.show(ul,nodes[i]);
	}
};
module.exports = mytree; 

},{"./httpclient":1,"./mynode":2}],4:[function(require,module,exports){
'use strict';
/**
 * Created by mambrin on 28.03.17.
 */
var mytree= require('./../models/mytree');
function categorytree(panel,cls,modal,resContainer,prevs,userfunc,postfunc,jf)
{
this.src_id=panel.dataset["id"];
this.searchInput=modal.querySelector("input.microsearch_input");
this.searchInput.value=""; 
this.searchButton=modal.querySelector("button.microsearch_button");
this.TreeBody=modal.querySelector("#teletree");
this.TreeBody.innerHTML=''; 
this.mytree=new mytree(this.TreeBody);
var self=this;
this.selfCloseNode=function(obj){
  obj.parentNode.removeChild(obj); 
 if(typeof postfunc=="function"){ postfunc();}
}
this.mytree.setFunctionCh2(function (){
	 if(typeof userfunc=="function"){ userfunc(this.checkedTops);}
});
this.rootId=90401;
var self=this;
this.searchButton.onclick=function(){

 var params={name:self.searchInput.value};
 self.mytree.openSearch(params);

}
this.searchInput.onkeyup = function(event)
{
event = event || window.event;
if((event.keyCode == 13)) 
 {
 var params={name:self.searchInput.value};
 self.mytree.openSearch(params);
 return false;
 }  
};
  if(prevs.length){
     var params={psg:prevs};
     this.mytree.openInd(params);
  }else{
     var params={id:[this.rootId]};
     this.mytree.openStart(params);
  }


};
module.exports = categorytree; 
window.ContextMyCategory =categorytree;
},{"./../models/mytree":3}]},{},[4])