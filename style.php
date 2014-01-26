/*
Created by: Adam Ludvik
Last Updated: June 16, 2011
*/

/*defines subsite specific path to image directory*/
<?php
include ("image_dir.php");
?>


/* Structuring
--------------*/

body {
	text-align: center;
	}

div#wrapper{
	margin-left: auto;
	margin-right: auto;
	text-align: left;
	width: 1000px;
	}

div#header{
	z-index: 5;
	margin-top: 0px;
	width: 1000px;
	height: 108px;
	}

div#nav{
	height: 3.1em;
	width: 1000px;
	/*position: relative;
	left: 170px;
	top: 86px;*/
	}

div#nav#ul{
	text-align: center;
	}
	
ul#menu{
	text-align: center;
	}

div#content{
	/*margin-top: -28px;
	padding-top: 35px;
	position: relative;
	z-index: 2;*/
	width: 1000px;
	margin-top: .5em;
}

/*div#leftbar{
	/*z-index: 2;
	padding-left: 10px;
	padding-right: 10px;
	padding-top: 0px;
	text-align: center;
	float: left;
	width: 250px;
	height: 200px;
}*/

div#leftbar{
	float: left;
	width: 345px;
	min-height: 820px;
	padding-left: 10px;
	padding-right: 10px;
}
div#middlebar{
	/*z-index: 2;
	margin-left: 20px;
	margin-top: 10px;*/
	float: left;
	width: 720px;
	min-height: 820px;
	padding-left: 20px;
	padding-right: 10px;
}

div#rightbar{
	/*z-index: 2;
	position: relative;
	left: -100px;
	width: 200px;
	padding-left: 5px;
	padding-right: 10px;
	padding-top: 0px;*/
	float: left;
	width: 250px;
}
div#rightbar img{
	max-width: 200px;
}

div#footer{
	clear: both;
	text-align: center;
	width: 994px;
	position: relative;
	top: 10px;
	height: 4em;
	padding-top: .5em;
	padding-bottom: .5em;
	}

div#shoutbox{
	float: right;
	width: 200px;
	margin: .5em;
	padding:.5em;
	}
/* Looks
---------*/

body{
	background: #a25251 url('<?php echo $image_dir; ?>/background.png') no-repeat top center scroll;
	font-family: verdana;
	font-size: 15px;
	}
hr{
	width: 80%;
	border: 1px dotted #000;
	}
h1{
	font-size: 1.5em;
	}
h2{
	font-size: 1.3em;
	}
h3{
	color: #a25251;
	font-size: 1.5em;
	margin-top: .5em;
	margin-bottom: .5em;
	font-family: courier;
	}
h4{
	color: #a25251;
	text-align: center;
	}
a, a:visited{
	color: #026aff;
	}
a:hover{
	color: #5bc06e;
	}
ul{
list-style-type: square;
}
#fancy_text{
	font-family: courier;
	font-style: italic;
	color: #a25251;
	}
	
div#wrapper{
	background: transparent;
	}

/*writes subsite specific header style*/
<?php include("header.css"); ?>

div#nav a{
	color: #fff;
	font-family: arial;
	font-size: 1.2em;
	}

div#content{
	background: transparent;
	}
	
div#leftbar{
	background: url('<?php echo $image_dir; ?>/transparency.png');
	}
	
div#middlebar{
	background: url('<?php echo $image_dir; ?>/transparency.png');
	}

div#rightbar{
	background: transparent;
	text-align: right;
	}	

div#footer{
	color: #5b7964;
	background: #a1d6b2;
	border: solid black;
	}
div#footer a{
	color: #5b7964;
	}
div#footer a:hover{
	color: #851d1c;
	}
	
div#shoutbox{
	border: thin solid black;
	font-family: courier;
	font-size: 1.1em;
	}
	
/* Fixes
---------*/

img{
	border: none;
}

/* JS Menu by David Boggus: http://www.boggusweb.com/
-----------*/

.dbMenu .subMenu{
	
}
.dbMenu li{
	float: right;
	padding-right: .8em;
	padding-top: .6em;
}
.dbMenu li ul{
	position: absolute;
	/*top: 3em;*/
	left: 0em;

}

.dbMenu li ul li{
	float:none;
	margin-left: 0.5em;
}
#nav_nob{
	margin-right: .5em;
	}

/* JS Menu (Global) by David Boggus: http://www.boggusweb.com/
--------------------*/

#menu{
	position:relative;
	z-index:1;
	top:0;
	left:0;
	padding:0px;
	margin:0px;
	margin-right: .8em;
	list-style:none;
}
#menu li a:hover{
	color: #851d1c;
	text-decoration: underline;
	}
.nav_li li ul{
	position:relative;
	display:none;
	padding: 0;
	list-style:none;
	color: #fff;
}
.nav_li li{
	position:relative;
	padding: 0;
	padding-top: 1em;
	color:#fff;
	height: 2em;
}
.subMenu{
	background-repeat:no-repeat;
}
.click{
	/*background-color:#87ca90;*/
}
.click ul{
	display:block;
}
.hover, ul li a:hover{
	cursor:pointer;
}
li ul li.hover, li ul li.hover a:hover{
}
li a{
}
li a, li ul li a, li.hover ul li a{
	text-decoration:none;
}
li.hover a, li ul li.hover a{
	text-decoration: underline;
	color: #851d1c;
}