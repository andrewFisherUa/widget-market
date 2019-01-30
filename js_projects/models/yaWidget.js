'use strict';

var yaWidget = function (container,clid) {


container.innerHTML = '';
container.innerHTML = '<div id="marketWidget_'+container.id+'"></div>';
container.dataset.clid=clid;


var script = document.createElement('script');
        script.src = 'https://aflt.market.yandex.ru/widget/script/api';
        script.type = 'text/javascript';
        document.body.appendChild(script);
	    script.onload = function (){
			var id = this.id;
			 clid =this.dataset.clid;
			 var start = function(){
				window.removeEventListener("YaMarketAffiliateLoad", start);
				YaMarketAffiliate.createWidget({
                  containerId: "marketWidget_"+this.id,
                  type: "offers",
                  params: {clid: clid,
                  searchText: "Смартфон",
                  themeId: 2
                  }
                 });
			   
			}.bind(this);
			
            window.YaMarketAffiliate ? start() : window.addEventListener("YaMarketAffiliateLoad", start);

		}.bind(container)
	
return;
        
console.log('Test yaWidget');

}



module.exports = yaWidget;