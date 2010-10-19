	var params = {
		quality: "high",
		//scale: "noscale",
		wmode: "transparent",
		allowscriptaccess: "always",
		bgcolor: "#FFFFFF"
	};
	var flashvars = {
		siteXML: "http://rubinstein.local/media/flash/xml/site.xml",
		lg : "fr"
	};
	var attributes = {
		id: "flashcontent",
		name: "flashcontent"
	};
	swfobject.embedSWF("http://rubinstein.local/media/flash/main.swf", "flashcontent", "1024", "374", "10.0.0", "http://rubinstein.local/media/flash/expressInstall.swf", flashvars, params, attributes);