function changebr(length)
{
	var v = document.getElementsByClassName("list1")[0];
	var selectedValue = v.options[v.selectedIndex].value;
	var source = video.currentSrc();
	var split = source.split("&");
	newsource = split[0]+"&br="+selectedValue+"&time="+Math.trunc(video.currentTime());
	playvideo(newsource);
	cbitrate = true;
}
function playvideo(source)
{
	if(cbitrate == true)
	{
		if(source.indexOf("&br=") > -1)
		{
			video.src(source);
		}
		else
		{
			var oldsource = video.currentSrc();
			var source2 = oldsource.split("&time=");
			var source3 = source.split("&time=");
			video.src(source2[0]+"&time="+source3[1]);
		}
	}
	else
	{
		video.src(source);
	}
	video.load();
	setTimeout(function(){
	video.play();
	}, 3000);
}