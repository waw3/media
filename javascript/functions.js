function changebr()
{
	var v = document.getElementsByClassName("list1")[0];
	var selectedValue = v.options[v.selectedIndex].value;
	var video = videojs('MY_VIDEO_1');
	var source = video.currentSrc();
	var split = source.split("&");
	if(split[0].indexOf("transcode.php") > -1)
	{
		var newsource = split[0]+"&time="+Math.trunc(video.currentTime())+"&br="+selectedValue;
	}
	else
	{
		var source2 = decodeURIComponent(source);
		if(source2.indexOf("show") > -1)
		{
			var source3 = source2.split("/shows/");
			var newsource = "transcode.php?media="+encodeURIComponent(source3[1])+"&time="+Math.trunc(video.currentTime())+"&br="+selectedValue;
		}
		else
		{
			var source3 = source2.split("/movies/");
			var newsource = "transcode.php?media="+encodeURIComponent(source3[1])+"&time="+Math.trunc(video.currentTime())+"&br="+selectedValue;
		}
	}
	video.src(newsource);
	video.load();
	video.play();
}