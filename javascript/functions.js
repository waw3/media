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
function hideCurrent()
{
	if(document.getElementById("recentlyAddedWrapper").style.display != "none")
	{
		var elements = document.getElementsByClassName('currentlyWatching');
		for (var i = 1; i < elements.length; i++){
		elements[i].style.display = "none";
		}
		$("#recentlyAddedWrapper").animate({width: "0%"}, 500);
		setTimeout(function(){
		elements[0].style.display = "none";
		document.getElementById("recentlyAddedWrapper").style.display = "none";
		var elements2 = document.getElementsByClassName('recentMovies');
		for (var i = 1; i < elements2.length; i++){
		elements2[i].style.top = "88px";
		}
		},600);
		
		
	}
	else
	{
		var elements = document.getElementsByClassName('currentlyWatching');
		elements[0].style.display = "block";
		document.getElementById("recentlyAddedWrapper").style.display = "block";
		$("#recentlyAddedWrapper").animate({width: "100%"}, 500);
		setTimeout(function(){
		for (var i = 1; i < elements.length; i++){
		elements[i].style.display = "block";	
		}
		},600);
		var elements2 = document.getElementsByClassName('recentMovies');
		for (var i = 1; i < elements2.length; i++){
		elements2[i].style.top = "472px";
		}

	}
	
}
function hideRecentM()
{
	if(document.getElementById("recentlyAddedMovies").style.display != "none")
	{
		var elements = document.getElementsByClassName('recentMovies');
		for (var i = 1; i < elements.length; i++){
		elements[i].style.display = "none";
		}
		$("#recentlyAddedMovies").animate({width: "0%"}, 500);
		setTimeout(function(){
		elements[0].style.display = "none";
		document.getElementById("recentlyAddedMovies").style.display = "none";
		},600);
	}
	else
	{
		var elements = document.getElementsByClassName('recentMovies');
		elements[0].style.display = "block";
		document.getElementById("recentlyAddedMovies").style.display = "block";
		$("#recentlyAddedMovies").animate({width: "100%"}, 500);
		setTimeout(function(){
		for (var i = 1; i < elements.length; i++){
		elements[i].style.display = "block";
		}
		},600);

	}
}
function scrollX(element,p)
{
	$(element).animate( { scrollLeft: '+='+p }, 200);
}