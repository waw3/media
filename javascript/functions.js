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
function removeCW(dir)
{
	var dir2 = video.currentSrc();
	var newdir = "";
	
	if(dir2.indexOf("&time=") > -1)
	{
		newdir = dir2.split("&time=");
		dir2 = newdir[0];
	}
	
	newdir = dir2.split("?media=");
	dir2 = newdir[1];		
	$.get( "time.php", { media: dir2,
	remove: "true" });
	window.location = "shows.php?show="+dir;
}
function popup(id)
{
	video.pause();
	var e = document.createElement("div");
	e.id = id;
	var w = document.createElement("div");
	w.id = id+"Wrapper";
	var selectList = document.createElement('select');
	var array = ["Report problem with playback","Report problem with video","Report problem with page"];
	var form = document.createElement('form');
	form.id = "reportForm";
	form.action='javascript:report()';
	selectList.setAttribute("id", "mySelect");
	var textArea = document.createElement('textarea');
	var label = document.createElement('label');
	label.innerHTML = "Describe problem:";
	label.style.color = "#E8E8E8";
	label.style.marginTop = "20px";
	textArea.setAttribute('form','reportForm');
	textArea.setAttribute('name','reportText');
	textArea.setAttribute('cols',60);
	textArea.setAttribute('rows', 4);
	textArea.style.backgroundColor = "#303030";
	textArea.style.color = "#E8E8E8";
	textArea.style.resize = "none";
	textArea.style.border = "none";
	textArea.style.outline = "none";
	var submit = document.createElement("input");
	submit.setAttribute('type',"submit");
	submit.setAttribute('value',"Submit");
	submit.id = "button";
	submit.style.marginRight = "500px";
	submit.style.marginLeft = "60px";
	submit.style.marginRight = "0px";
	for (var i = 0; i < array.length; i++) 
	{
		var option = document.createElement("option");
		option.setAttribute("value", array[i]);
		option.text = array[i];
		selectList.appendChild(option);
	}
	form.appendChild(selectList);
	form.appendChild(submit);
	w.onclick = function () {
		document.body.removeChild(e);
		document.body.removeChild(w);
		video.play();
	};
	e.appendChild(form);
	e.appendChild(label);
	e.appendChild(textArea);
	document.body.appendChild(w);
	document.body.appendChild(e);
}

