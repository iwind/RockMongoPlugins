<?php

import("classes.BaseController");

class IndexController extends BaseController {
	public function doIndex() {
		$this->db_name = xn("db");
		$this->collection_name = xn("collection");

		$this->map_function = xn("map_function");
		$this->reduce_function = xn("reduce_function");
		$this->query_filter = xn("query_filter");
		$this->out_options = xn("out_options");
		$this->keeptemp = xn("keeptemp");
		$this->jsmode = xn("jsmode");
		$this->verbose = xn("verbose");
		$this->sort = xn("sort");
		$this->limit = xi("limit");
		$this->finalize_function = xn("finalize_function");
		$this->scope_vars = xn("scope_vars");

		if (!$this->map_function) {
			$this->map_function = 'function () {}';
		}
 		if (!$this->reduce_function) {
 			$this->reduce_function = 'function (key, values) {}';
 		}
		if (!$this->out_options) {
			$this->out_options = '{ inline:1 }';
		}

		if ($this->isPost()) {
			$db = $this->_mongo->selectDB($this->db_name);

			$commandArray = array();


			$command = '

					 { mapreduce : "' . $this->collection_name . '",' .
					   'map : ' . $this->map_function . ',
					   reduce : ' . $this->reduce_function;
			$commandArray = array(
				"mapreduce" => $this->collection_name,
				"map" => new MongoCode($this->map_function),
				"reduce" => new MongoCode($this->reduce_function)
			);
			if ($this->query_filter) {
				$command .= ', query : ' . $this->query_filter;
				$commandArray["query"] = $this->_decodeJson($this->query_filter, true);
			}
			if ($this->sort) {
				$command .= ',sort : ' . $this->sort;
				$commandArray["sort"] = $this->_decodeJson($this->sort, true);
			}
			if ($this->limit > 0) {
				$command .= ', limit: ' . $this->limit;
				$commandArray["limit"] = $this->limit;
			}

			if ($this->out_options) {
				$command .= ', out : ' . $this->out_options;
				$commandArray["out"] = $this->_decodeJson($this->out_options, true);
			}

			if ($this->keeptemp) {
				$command .= ', keeptemp:' . $this->keeptemp;
				$commandArray["keeptemp"] = ($this->keeptemp == "true");
			}
			if ($this->finalize_function) {
				$command .= ', finalize: ' . $this->finalize_function;
				$commandArray["finalize"] = new MongoCode($this->finalize_function);
			}
			if ($this->scope_vars) {
				$command .= ', scope:' . $this->scope_vars;
				$commandArray["scope"] = $this->_decodeJson($this->scope_vars, true);
			}
			if ($this->jsmode) {
				$command .= ', jsMode : ' . $this->jsmode;
				$commandArray["jsMode"] = ($this->jsmode == "true");
			}
			if ($this->verbose) {
				$command .= ', verbose : ' . $this->verbose;
				$commandArray["verbose"] = ($this->verbose == "true");
			}
			$command .= ' }';

			$this->command = json_format_html($command);
			$ret = $db->command($commandArray);

			if ($ret["ok"]) {
				$this->message = $this->_highlight($ret, "json");
			}
			else {
				$this->error = $this->_highlight($ret, "json");
			}
		}

		$this->display();
	}
}