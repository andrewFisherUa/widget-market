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
