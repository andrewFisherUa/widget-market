'use strict';
exports.isDevise=function(){
	var t = true;
	function detect(ua) {
		function getFirstMatch(regex) {
			var match = ua.match(regex);
			return (match && match.length > 1 && match[1]) || '';
		}
		function getSecondMatch(regex) {
			var match = ua.match(regex);
			return (match && match.length > 1 && match[2]) || '';
		}
		
		var iosdevice = getFirstMatch(/(ipod|iphone|ipad)/i).toLowerCase(),
		likeAndroid = /like android/i.test(ua),
		android = !likeAndroid && /android/i.test(ua),
		nexusMobile = /nexus\s*[0-6]\s*/i.test(ua),
		nexusTablet = !nexusMobile && /nexus\s*[0-9]+/i.test(ua),
		chromeos = /CrOS/.test(ua),
		silk = /silk/i.test(ua),
		sailfish = /sailfish/i.test(ua),
		tizen = /tizen/i.test(ua),
		webos = /(web|hpw)os/i.test(ua),
		windowsphone = /windows phone/i.test(ua),
		samsungBrowser = /SamsungBrowser/i.test(ua),
		windows = !windowsphone && /windows/i.test(ua),
		mac = !iosdevice && !silk && /macintosh/i.test(ua),
		linux = !android && !sailfish && !tizen && !webos && /linux/i.test(ua),
		edgeVersion = getSecondMatch(/edg([ea]|ios)\/(\d+(\.\d+)?)/i),
		versionIdentifier = getFirstMatch(/version\/(\d+(\.\d+)?)/i),
		tablet = /tablet/i.test(ua) && !/tablet pc/i.test(ua),
		mobile = !tablet && /[^-]mobi/i.test(ua),
		xbox = /xbox/i.test(ua),
		result
		
		if (/opera/i.test(ua)) {
			result = {
				opera: t,
				name: 'Opera',
				version: versionIdentifier || getFirstMatch(/(?:opera|opr|opios|opt)[\s\/](\d+(\.\d+)?)/i)
			}
		}
		else if (/opr\/|opios/i.test(ua)) {
			result = {
				opera: t,
				name: 'Opera',
				version: getFirstMatch(/(?:opr|opios)[\s\/](\d+(\.\d+)?)/i) || versionIdentifier
			}
		}
		else if (/opt/i.test(ua)) {
			result = {
				operaTouch: t,
				name: 'Opera Touch',
				version: getFirstMatch(/(?:opt)[\s\/](\d+(\.\d+)?)/i) || versionIdentifier
			}
		}
		else if (/SamsungBrowser/i.test(ua)) {
			result = {
				samsungBrowser: t,
				name: 'Samsung Internet for Android',
				version: versionIdentifier || getFirstMatch(/(?:SamsungBrowser)[\s\/](\d+(\.\d+)?)/i)
			}
		}
		else if (/coast/i.test(ua)) {
			result = {
				coast: t,
				name: 'Opera Coast',
				version: versionIdentifier || getFirstMatch(/(?:coast)[\s\/](\d+(\.\d+)?)/i)
			}
		}
		else if (/yabrowser/i.test(ua)) {
			result = {
				yandexbrowser: t,
				name: 'Yandex Browser',
				version: versionIdentifier || getFirstMatch(/(?:yabrowser)[\s\/](\d+(\.\d+)?)/i)
			}
		}
		else if (/ucbrowser/i.test(ua)) {
			result = {
				ucbrowser: t,
				name: 'UC Browser',
				version: getFirstMatch(/(?:ucbrowser)[\s\/](\d+(?:\.\d+)+)/i)
			}
		}
		else if (/mxios/i.test(ua)) {
			result = {
				maxthon: t,
				name: 'Maxthon',
				version: getFirstMatch(/(?:mxios)[\s\/](\d+(?:\.\d+)+)/i)
			}
		}
		else if (/epiphany/i.test(ua)) {
			result = {
				epiphany: t,
				name: 'Epiphany',
				version: getFirstMatch(/(?:epiphany)[\s\/](\d+(?:\.\d+)+)/i)
			}
		}
		else if (/puffin/i.test(ua)) {
			result = {
				puffin: t,
				name: 'Puffin',
				version: getFirstMatch(/(?:puffin)[\s\/](\d+(?:\.\d+)?)/i)
			}
		}
		else if (/sleipnir/i.test(ua)) {
			result = {
				sleipnir: t,
				name: 'Sleipnir',
				version: getFirstMatch(/(?:sleipnir)[\s\/](\d+(?:\.\d+)+)/i)
			}
		}
		else if (/k-meleon/i.test(ua)) {
			result = {
				kMeleon: t,
				name: 'K-Meleon',
				version: getFirstMatch(/(?:k-meleon)[\s\/](\d+(?:\.\d+)+)/i)
			}
		}
		else if (windowsphone) {
			result = {
				windowsphone: t,
				name: 'Windows Phone',
				osname: 'Windows Phone'
			}
			if (edgeVersion) {
				result.msedge = t,
				result.version = edgeVersion
			}
			else {
				result.msie = t,
				result.version = getFirstMatch(/iemobile\/(\d+(\.\d+)?)/i)
			}
		}
		else if (/msie|trident/i.test(ua)) {
			result = {
				msie: t,
				name: 'Internet Explorer',
				version: getFirstMatch(/(?:msie |rv:)(\d+(\.\d+)?)/i)
			}
		}
		else if (chromeos) {
			result = {
				chromeos: t,
				chromeBook: t,
				chrome: t,
				name: 'Chrome',
				osname: 'Chrome OS',
				version: getFirstMatch(/(?:chrome|crios|crmo)\/(\d+(\.\d+)?)/i)
			}
		} 
		else if (/edg([ea]|ios)/i.test(ua)) {
			result = {
				msedge: t,
				name: 'Microsoft Edge',
				version: edgeVersion
			}
		}
		else if (/vivaldi/i.test(ua)) {
			result = {
				vivaldi: t,
				name: 'Vivaldi',
				version: getFirstMatch(/vivaldi\/(\d+(\.\d+)?)/i) || versionIdentifier
			}
		}
		else if (sailfish) {
			result = {
				sailfish: t,
				name: 'Sailfish',
				osname: 'Sailfish OS',
				version: getFirstMatch(/sailfish\s?browser\/(\d+(\.\d+)?)/i)
			}
		}
		else if (/seamonkey\//i.test(ua)) {
			result = {
				seamonkey: t,
				name: 'SeaMonkey',
				version: getFirstMatch(/seamonkey\/(\d+(\.\d+)?)/i)
			}
		}
		else if (/firefox|iceweasel|fxios/i.test(ua)) {
			result = {
				firefox: t,
				name: 'Firefox',
				version: getFirstMatch(/(?:firefox|iceweasel|fxios)[ \/](\d+(\.\d+)?)/i)
			}
			if (/\((mobile|tablet);[^\)]*rv:[\d\.]+\)/i.test(ua)) {
				result.firefoxos = t,
				result.osname = 'Firefox OS'
			}
		}
		else if (silk) {
			result =  {
				silk: t,
				name: 'Amazon Silk',
				version : getFirstMatch(/silk\/(\d+(\.\d+)?)/i)
			}
		}
		else if (/phantom/i.test(ua)) {
			result = {
				phantom: t,
				name: 'PhantomJS',
				version: getFirstMatch(/phantomjs\/(\d+(\.\d+)?)/i)
			}
		}
		else if (/slimerjs/i.test(ua)) {
			result = {
				slimer: t,
				name: 'SlimerJS',
				version: getFirstMatch(/slimerjs\/(\d+(\.\d+)?)/i)
			}
		}
		else if (/blackberry|\bbb\d+/i.test(ua) || /rim\stablet/i.test(ua)) {
			result = {
				blackberry: t,
				name: 'BlackBerry',
				osname: 'BlackBerry OS',
				version: versionIdentifier || getFirstMatch(/blackberry[\d]+\/(\d+(\.\d+)?)/i)
			}
		}
		else if (webos) {
			result = {
				webos: t,
				name: 'WebOS',
				osname: 'WebOS',
				version: versionIdentifier || getFirstMatch(/w(?:eb)?osbrowser\/(\d+(\.\d+)?)/i)
			}
		}
		else if (/bada/i.test(ua)) {
			result = {
				bada: t,
				name: 'Bada',
				osname: 'Bada',
				version: getFirstMatch(/dolfin\/(\d+(\.\d+)?)/i)
			};
		}
		else if (tizen) {
			result = {
				tizen: t,
				name: 'Tizen',
				osname: 'Tizen',
				version: getFirstMatch(/(?:tizen\s?)?browser\/(\d+(\.\d+)?)/i) || versionIdentifier
			};
		}
		else if (/qupzilla/i.test(ua)) {
			result = {
				qupzilla: t,
				name: 'QupZilla',
				version: getFirstMatch(/(?:qupzilla)[\s\/](\d+(?:\.\d+)+)/i) || versionIdentifier
			}
		}
		else if (/chromium/i.test(ua)) {
			result = {
				chromium: t,
				name: 'Chromium',
				version: getFirstMatch(/(?:chromium)[\s\/](\d+(?:\.\d+)?)/i) || versionIdentifier
			}
		}
		else if (/chrome|crios|crmo/i.test(ua)) {
			result = {
				chrome: t,
				name: 'Chrome',
				version: getFirstMatch(/(?:chrome|crios|crmo)\/(\d+(\.\d+)?)/i)
			}
		}
		else if (android) {
			result = {
				name: 'Android',
				version: versionIdentifier
			}
		}
		else if (/safari|applewebkit/i.test(ua)) {
			result = {
				safari: t,
				name: 'Safari'
			}
			if (versionIdentifier) {
				result.version = versionIdentifier
			}
		}
		else if (iosdevice) {
			result = {
				name : iosdevice == 'iphone' ? 'iPhone' : iosdevice == 'ipad' ? 'iPad' : 'iPod'
			}
			if (versionIdentifier) {
				result.version = versionIdentifier
			}
		}
		else if(/googlebot/i.test(ua)) {
			result = {
				googlebot: t,
				name: 'Googlebot',
				version: getFirstMatch(/googlebot\/(\d+(\.\d+))/i) || versionIdentifier
			}
		}
		else {
			result = {
				name: getFirstMatch(/^(.*)\/(.*) /),
				version: getSecondMatch(/^(.*)\/(.*) /)
			};
		}

		// set webkit or gecko flag for browsers based on these engines
		if (!result.msedge && /(apple)?webkit/i.test(ua)) {
			if (/(apple)?webkit\/537\.36/i.test(ua)) {
				result.name = result.name || "Blink";
				 result.blink = t;
			}
			else{
				result.name = result.name || "Webkit";
				result.webkit = t;
			}
			if (!result.version && versionIdentifier) {
				result.version = versionIdentifier
			}
		}
		else if (!result.opera && /gecko\//i.test(ua)) {
			result.gecko = t;
			result.name = result.name || "Gecko";
			result.version = result.version || getFirstMatch(/gecko\/(\d+(\.\d+)?)/i);
		}

		if (!result.windowsphone && (android || result.silk)) {
			result.android = t;
			result.osname = 'Android';
		}
		else if (!result.windowsphone && iosdevice) {
			result[iosdevice] = t;
			result.ios = t;
			result.osname = 'iOS'
		}
		else if (mac) {
			result.mac = t;
			result.osname = 'macOS';
		}
		else if (xbox) {
			result.xbox = t;
			result.osname = 'Xbox';
		}
		else if (windows) {
			result.windows = t;
			result.osname = 'Windows'
		}
		else if (linux) {
			result.linux = t;
			result.osname = 'Linux'
		}

		function getWindowsVersion (s) {
		  switch (s) {
			case 'NT': return 'NT'
			case 'XP': return 'XP'
			case 'NT 5.0': return '2000'
			case 'NT 5.1': return 'XP'
			case 'NT 5.2': return '2003'
			case 'NT 6.0': return 'Vista'
			case 'NT 6.1': return '7'
			case 'NT 6.2': return '8'
			case 'NT 6.3': return '8.1'
			case 'NT 10.0': return '10'
			default: return undefined
		  }
		}

		// OS version extraction
		var osVersion = '';
			if (result.windows) {
				osVersion = getWindowsVersion(getFirstMatch(/Windows ((NT|XP)( \d\d?.\d)?)/i))
			}
			else if (result.windowsphone) {
				osVersion = getFirstMatch(/windows phone (?:os)?\s?(\d+(\.\d+)*)/i);
			}
			else if (result.mac) {
				osVersion = getFirstMatch(/Mac OS X (\d+([_\.\s]\d+)*)/i);
				osVersion = osVersion.replace(/[_\s]/g, '.');
			}
			else if (iosdevice) {
				osVersion = getFirstMatch(/os (\d+([_\s]\d+)*) like mac os x/i);
				osVersion = osVersion.replace(/[_\s]/g, '.');
			}
			else if (android) {
				osVersion = getFirstMatch(/android[ \/-](\d+(\.\d+)*)/i);
			}
			else if (result.webos) {
				osVersion = getFirstMatch(/(?:web|hpw)os\/(\d+(\.\d+)*)/i);
			}
			else if (result.blackberry) {
				osVersion = getFirstMatch(/rim\stablet\sos\s(\d+(\.\d+)*)/i);
			}
			else if (result.bada) {
				osVersion = getFirstMatch(/bada\/(\d+(\.\d+)*)/i);
			}
			else if (result.tizen) {
				osVersion = getFirstMatch(/tizen[\/\s](\d+(\.\d+)*)/i);
			}
			if (osVersion) {
				result.osversion = osVersion;
			}

		// device type extraction
		var osMajorVersion = !result.windows && osVersion.split('.')[0];
		if (tablet || nexusTablet || iosdevice == 'ipad' || (android && (osMajorVersion == 3 || (osMajorVersion >= 4 && !mobile))) || result.silk) {
			result.device='tablet';
		}
		else if (mobile || iosdevice == 'iphone' || iosdevice == 'ipod' || android || nexusMobile || result.blackberry || result.webos || result.bada) {
			result.device='mobile';
		}
		else{
			result.device='desktop';
		}
		return result;
	}
	var device = detect(typeof navigator !== 'undefined' ? navigator.userAgent || '' : '');
	//alert ("Девайc - "+device.device+"\n"+"Система - "+device.osname+"\n"+"Версия - "+device.osversion+"\n"+"Браузер - "+device.name+"\n"+"Версия браузера - "+device.version);
	return device;
}