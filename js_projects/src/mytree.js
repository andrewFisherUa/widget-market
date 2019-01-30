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