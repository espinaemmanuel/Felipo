<?php
/**
 * Licensed to the Apache Software Foundation (ASF) under one
 * or more contributor license agreements.  See the NOTICE file
 * distributed with this work for additional information
 * regarding copyright ownership.  The ASF licenses this file
 * to you under the Apache License, Version 2.0 (the
 * "License"); you may not use this file except in compliance
 * with the License.  You may obtain a copy of the License at
 * 
 *   http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing,
 * software distributed under the License is distributed on an
 * "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY
 * KIND, either express or implied.  See the License for the
 * specific language governing permissions and limitations
 * under the License.
 */



class HtmlHelper {
	
	protected $css = array();
	protected $js = array();
	protected $module;
	
	public function __construct($module){
		$this->module = $module;
	}
	
	public function addExternalJs($name){
		global $config;
		
		if(!array_key_exists($name, $config['external_js'])){
			throw new UndefinedExternalScriptException($name);
		}
		
		array_push($this->js, $config['external_js'][$name]);	
	}
	
	protected function addInclusion($name, $module = null, $type){
		if($module == null){
			$module = $this->module;
		}
		
		$path = "$module/$type/$name.$type";
		
		if(!Loader::getInstance()->fileExists("app/$path")){
			if($module === 'common'){
				throw new FileNotFoundException("app/$path");
			} else {
				$this->addInclusion($name, 'common', $type);
			}
		} else {
			array_push($this->$type, $path);
		}
	}
	
	public function addJs($name, $module = null){
		$this->addInclusion($name, $module, 'js');
	}
	
	public function addCss($name, $module = null){
		$this->addInclusion($name, $module, 'css');
	}
	
	public function addIncludes(){
		foreach($this->css as $css){
			echo "<link rel='stylesheet' type='text/css' href='$css'   />\n";
		}
		
		echo "\n";
		
		foreach($this->js as $js){
			echo "<script type='text/javascript' src='$js'></script>\n";
		}
	}
	
	public function link($text, $target, $params){
		
		$extraParams = '';
		if(isset($params['class'])){
			$extraParams .= 'class="'.$params['class'].'" ';
		}
		
		if(isset($params['id'])){
			$extraParams .= 'id="'.$params['id'].'" ';
		}
		return "<a href='$target' $extraParams>$text</a>";		
	}

}

?>