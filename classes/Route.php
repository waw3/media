<?php

class Route
{

	private $Uri = array();
	private $files = array();
	private $_trim = '/\^$';
	
	public function add($uri, $file)
	{
		$uri = trim($uri, $this->_trim);
		$this->_listUri[] = $uri;
		$this->files[] = $file;
	}
	
	public function submit()
	{	
		$uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '/';
		//print_r($uriArray);
		$uriArray = explode('/',$uri);
		if($uri == '/' || empty($uriArray[2])) { include "home.php"; exit();}
		$baseUri = $uriArray[1].'/'.$uriArray[2];
		$uriArray = array_slice($uriArray, 3, count($uriArray));
		for($i = 0; $i < count($this->_listUri); $i++)
		{
			 $cUri = $this->_listUri[$i];
			 
			 if(stripos($cUri,$baseUri) !== false)
			 {
				include $this->files[$i];
				$class = substr($this->files[$i],0,strlen($this->files[$i])-4);
				$page = new $class($uriArray);
				exit();
			 }
		}
		include "home.php";	
	}	
}