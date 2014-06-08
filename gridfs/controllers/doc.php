<?php

import("classes.BaseController");

class DocController extends BaseController {
	/**
	 * Preview document
	 */
	public function doPreview() {
		$db = xn("db");
		$collection = xn("collection");
		$recordId = xn("record_id");

		$prefix = preg_replace("/\\.files$/", "", $collection);

		$mongodb = $this->_mongo->selectDB($db);
		$file = $mongodb->getGridFS($prefix)->get(rock_real_id($recordId));
		$filename = $file->getFilename();
		$info = pathinfo($filename);
		$extension = isset($info["extension"]) ? strtolower($info["extension"]) : "";

		//preview picture
		if (in_array($extension, array( "png", "gif", "jpg", "jpeg", "bmp" ))) {
			//get image size
			$image = @imagecreatefromstring($file->getBytes());
			if (!$image) {
				$this->_outputJson(array(
					"code" => 501,
					"message" => "invalid image format"
				));
			}

			if ($extension == "jpg") {
				$extension = "jpeg";
			}
			$size = $file->getSize();
			if (is_object($size) && ($size instanceof MongoInt64)) {
				$size = $size->value;
			}
			$this->_outputJson(array(
				"code" => 200,
				"data" => array(
					"name" => $filename,
					"size" => r_human_bytes($size),
					"width" => imagesx($image),
					"height" => imagesy($image),
					"image" => "data:image/{$extension};base64," . base64_encode($file->getBytes())
				)
			));
		}

		$this->_outputJson(array(
			"code" => 201
		));
	}

	/**
	 * Upload file
	 */
	public function doUpload() {
		$this->db = xn("db");
		$this->collection = xn("collection");

		if ($_SERVER["REQUEST_METHOD"] == "POST") {
			if (empty($_FILES["file"]["tmp_name"]) || $_FILES["file"]["error"] > 0) {
				$this->error = "Upload failed";
				$this->display();
				return;
			}

			$prefix = preg_replace("/\\.files$/", "", $this->collection);
			$gridfs = $this->_mongo->selectDB($this->db)->getGridFS($prefix)->storeFile($_FILES["file"]["tmp_name"], array(
				"filename" => $_FILES["file"]["name"]
			));

			$this->message = "Upload successfully";
		}

		$this->display();
	}
}

?>