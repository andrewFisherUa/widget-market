'use strict';
/**
 * Created by mambrin on 28.03.17.
 */
var mynode= require('./nextnode');
var httpclient = require('./httpclient');
function mytree(bodyobj)
{
	
this.rootId=90401;
this.rootName="Категории";
this.body=bodyobj;
this.rootNode=new mynode(null);
function afterChecked(){
//console.log([111,"настаивай как хочешь"]);
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

var url='https://widget.market-place.su/rekrut-product/categories/0/open?data='+encodeURIComponent(JSON.stringify(args));

var self=this;
var parent=this.rootNode;

httpclient.ajax(url,{errorFn:function(res){
 console.log([1,'error',res]);
},successFn:function(res){
		try{
		
			var config=JSON.parse(res);
			//console.log([558,"tree conf --",config]); 
			
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
var url='https://widget.market-place.su/rekrut-product/categories/0/research?data='+encodeURIComponent(JSON.stringify(args));
var self=this;
var parent=this.rootNode;
httpclient.ajax(url,{errorFn:function(res){
 console.log([1,'error',res]);
},successFn:function(res){
		try{
			var config=JSON.parse(res);
			//console.log([558,"tree conf --",config]); 
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
var url='https://widget.market-place.su/rekrut-product/categories';

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
