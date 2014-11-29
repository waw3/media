<?php header ("Content-type: text/css"); ?>
.newUser {
 -webkit-animation: color_change 1s infinite alternate;
 -moz-animation: color_change 1s infinite alternate;
 -ms-animation: color_change 1s infinite alternate;
 -o-animation: color_change 1s infinite alternate;
 animation: color_change 1s infinite alternate;
}

@-webkit-keyframes color_change {
 from { color: red; }
 to { color: white; }
}
@-moz-keyframes color_change {
 from { color: red; }
 to { color: white; }
}
@-ms-keyframes color_change {
 from { color: red; }
 to { color: white; }
}
@-o-keyframes color_change {
 from { color: red; }
 to { color: white; }
}
@keyframes color_change {
 from { color: red; }
 to { color: white; }
}
body 
{
	background-color:#202020;
	margin-top: 0px;
	margin-bottom: 0px;
	overflow: hidden;

}
html {
	font-family: Verdana, Geneva, sans-serif;
	font-weight: bold;
}
#wrapper
{
	width: 100%;
	height: 100%;
	position: fixed;
	text-align: center;
}
space
{
	display:inline-block;
	width:150px;
	margin-right: 10px;
	margin-bottom:10px;
	text-align:right;
}

label
{

	font-size: 10px;
	font-weight: bold;
}
formtext
{
	display: inline-block;
    min-width: 150px;
	margin-right: 25px;
    text-align: right;
}
input
{
	float: right;
	margin-left: 10px;
	outline-color: red;
}
p
{
	text-align: right;
}
#main
{
	position: relative;
	margin-top: 10px;
	max-width: 100%;
	margin-right: 15px;
	height: 83%;
	background-color:#404040;
	text-align:center;
	color: #FFFFFF;
	-moz-box-shadow: 0 0 5px 5px #404040;
	-webkit-box-shadow: 0 0 5px 5px #404040;
	box-shadow: 0 0 5px 5px #404040;
	-moz-border-radius: 4px;
	-webkit-border-radius: 4px;
	border-radius: 4px;
	z-index: 1;
	overflow: auto;
	overflow-y: auto;
}
#center
{
	position: relative;
	width: 640px;
	height: 360;
	-moz-box-shadow: 0 0 10px 2px #200000;
	-webkit-box-shadow: 0 0   10px 2px #200000;
	box-shadow: 0 0 10px 2px #200000;
}
#login
{
	position: relative;
	margin: 30 auto;
}
#textfield
{
	background-color:#404040; 
	border: 0;color: #FFFFFF;
	-moz-border-radius: 4px; 
	-webkit-border-radius: 4px; 
	border-radius: 4px;
}
#searchbar
{
	display: inline-block;
	position: relative;
	max-width: 100%;
	min-width: 250px;
	height: 26px;
	margin: 0 auto;
	z-index: 2;
	-moz-box-shadow: 0 0 10px 2px #FFFFFF;
	-webkit-box-shadow: 0 0   10px 2px #FFFFFF;
	box-shadow: 0 0 10px 2px #FFFFFF;
	-moz-border-radius: 4px;
	-webkit-border-radius: 4px;
	border-radius: 4px;
	align:center;
}
#header
{
	color: #FFFFFF;
	position: absolute; 
	bottom: 5px; 
	left:0;
    right:0;
    margin-left:auto;
    margin-right:auto;
    background-color: #101010;
	 padding-right: 7px;
	height: 30px;
	-moz-box-shadow: 0 0 10px 2px #FFFFFF;
	-webkit-box-shadow: 0 0   10px 2px #FFFFFF;
	box-shadow: 0 0 10px 2px #FFFFFF;
	-moz-border-radius: 4px;
	-webkit-border-radius: 4px;
	border-radius: 4px;
	z-index: 3;
	text-align: center;
	

}
#submit
{
	background-color: #101010;
	font: bold 12px/18px sans-serif;
	margin-left: 5px;
	color: #FFFFFF;
	border: 0;
	-moz-border-radius: 4px;
	-webkit-border-radius: 4px;
	border-radius: 4px;
}
#submit:hover
{
	color: red;
	cursor:pointer;
	-webkit-transition: all 0.2s;
	-moz-transition: all 0.2s;
	-ms-transition: all 0.2s;
	-o-transition: all 0.2s;
	transition: all 0.2s;

}
#button
{
	background-color: #303030;
	font: bold 24px/32px sans-serif;
	color: #FFFFFF;
	border: 0;
	-moz-border-radius: 4px;
	-webkit-border-radius: 4px;
	border-radius: 4px;
	margin-top: 5px;
	float: right;
}
#button:hover
{
	color: red;
	cursor:pointer;
	-webkit-transition: all 0.2s;
	-moz-transition: all 0.2s;
	-ms-transition: all 0.2s;
	-o-transition: all 0.2s;
	transition: all 0.2s;
}
h1 
{
	font-size: 23px;
	text-align: center;
	text-shadow: 5px 3px 5px rgba(0,0,0,0.75);
}
h2 
{
	color: red;
	font-size: 12px;
	text-align: center;
}
h3 
{
	color: green;
	font-size: 12px;
	text-align: center;
}
#nav 
{
    position: relative;
	text-align: center;
    background-color: #101010;
}
ul 
{
  
  display: inline;
  margin: 0;
  padding: 15px 4px 17px 0;
  list-style: none;
}
ul li 
{
  color: #FFFFFF;
  font: bold 12px/18px sans-serif;
  display: inline-block;
  margin-right: -4px;
  position: relative;
  padding: 5px 7px;
  background-color: #101010;
  cursor: pointer;
  width: 55px;
  -webkit-transition: all 0.2s;
  -moz-transition: all 0.2s;
  -ms-transition: all 0.2s;
  -o-transition: all 0.2s;
  transition: all 0.2s;
}
ul li:hover 
{
  color: red;
}
.wrapper
{
    display: table;
    table-layout: relative;
    width: 610px;
	min-height: 550px;
	margin:0 auto;
}
#videocontainer
{
	position: relative;
}
.metadataContainer
{
	display: inline-block;
	height: 300px;
	width: 182px;
	float: right;
	text-align: center;
	
}
#contentWrapper 
{
position: relative;
margin: 0 auto;
width: 90%;
text-align: center;
}
#moviePosterContainer
{
	display: inline-block;
	height: 149px;
	width: 93px;
	text-align: center;
	white-space:nowrap;
	overflow: hidden;
	vertical-align: top;
}
#moviePosterContainer:hover
{
	cursor:pointer;
}
#posters
{
	width: 75px;
	height: 111px;
	bottom: 0;
}
#recentlyAddedWrapper
{
    max-width:95%;
    height:175px;
    margin: 0 auto;
    text-align:center;
	white-space: nowrap;
	background-color: #303030;
	-moz-border-radius: 4px;
	-webkit-border-radius: 4px;
	border-radius: 4px;
	overflow-x: auto;
}