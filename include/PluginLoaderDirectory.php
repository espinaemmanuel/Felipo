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




class PluginLoaderDirectory extends PluginLoader {
	
	private $_classMap;
	
	protected function loadClassMap() {
		$this->_classMap = array ();
		
		$className = get_called_class ();
		$dir = preg_replace ( '/PluginLoader/', '', $className );
		$dir[0] = strtolower($dir[0]);
		
		if (! $dir) {
			return;
		}
		
		$fullDir = Loader::getInstance ()->getFullPath ( "include/$dir" );
		
		$handle = opendir ( $fullDir );
		
		if ($handle) {
			
			while ( false !== ($file = readdir ( $handle )) ) {
				if($file == '.' || $file == '..'){
					continue;
				}
				$class = preg_replace ( '/.php/', '', $file );
				$this->_classMap [$class] = "include/$dir/$file";
			}
			
			closedir ( $handle );
		}
	
	}
	
	public function getPath($nombreClase) {
		
		if (is_null ( $this->_classMap )) {
			$this->loadClassMap ();
		}
		
		if (! array_key_exists ( $nombreClase, $this->_classMap )) {
			return null;
		}
		
		return $this->_classMap [$nombreClase];
	
	}
}

?>