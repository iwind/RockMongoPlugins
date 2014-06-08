<?php

//import base controller
import("classes.BaseController");

class Menu2Controller extends BaseController {
	public function doIndex() {
		echo "menu2";
	}

	public function doAbout() {
		$dbName = x("dbname");//Get the request parameter
		echo "menu3 " . $dbName;
	}

	public function doNut() {
		//display views/menu2/nut.php
		$this->display();
	}
}

?>