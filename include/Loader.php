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




class Loader {
	
	private static $__instance;
	private $__basePath;	
	private $__dirModulos = array();
	
	private $__pluginLoaders = array();
	private $__pluginsCargados = array();
	
	private function __construct(){
	
	}
	
	public function setBasePath($basePath){
		if(!is_dir($basePath) || !is_readable($basePath))
			die('Error al definir el basePath');
		
		$this->__basePath = $basePath;
	}
	
	/**
	 * @return Loader instancia singleton
	 */
	public static function getInstance(){
		if(!self::$__instance)
			self::$__instance = new Loader();
			
		return self::$__instance;
	}
	
	public function loadCommonTemplate($template){
		$path = "app/common/templates/$template.html.php";
		
		if(!$this->fileExists($path)){
			throw new FileNotFoundException();
		}
		
		return new Template($path);
	}
	
	public function getAcl($controller, $module){
		$aclName = str_replace('Controller', 'Acl', $controller);
		
		if ($this->fileExists("$module/acl/$aclName.php")){
			$this->requireOnce("$module/acl/$aclName.php");
			return new $aclName();
		}

	}
	
	public function getDefaultTemplate($controller, $module){
		$tpl = str_replace('Controller', '', $controller);
		$tpl[0] = strtolower($tpl[0]);
		
		return $this->getPathTemplate($tpl, $module);
		
	}
	
	public function getPathTemplate($templateName, $module){
		$path = "app/$module/templates/$templateName.html.php";
		
		if(!$this->fileExists($path)){
			throw new FileNotFoundException($path);
		}
		
		return $path;
	}
	
	public function getLibPath(){
		if(!$this->__basePath)
			die("basePath no definido");
			
		return $this->__basePath.'lib';
	}
	
	public function fileExists($path){
		if(!$this->__basePath)
			die("basePath no definido");
			
		$absolute_path = $this->__basePath.$path;
		return file_exists($absolute_path);
	}
	
	public function getRelativePath($fullPath){
		if(!$this->__basePath)
			die("basePath no definido");
		
		return str_replace($this->__basePath, '', $fullPath);
	}
	
	public function getFullPath($path){
		if(!$this->__basePath)
			die("basePath no definido");
			
		return $this->__basePath.$path;				
	}
	
	public function requireOnce($path){	
		if(!$this->fileExists($path)){
			require_once $this->__basePath.'/include/FileNotFoundException.php';
			throw new FileNotFoundException($path);			
		}
		require_once $this->__basePath.$path;
	}
	
	public function includeFile($path){
		if(!$this->fileExists($path)){
			Logger::logWarning("File not found $path in $this->__basePath");
			return;			
		}
		include $this->__basePath.$path;
	}
	
	public function includeFileAndGetVariables($path, $varNames){
		if(!$this->fileExists($path)){
			Logger::logWarning("File not found $path in $this->__basePath");
			return false;
		}
		
		include $this->__basePath.$path;
		
		$definedVars = get_defined_vars();
		
		if(is_null($varNames)){
			return $definedVars;
		}
		
		if(is_string($varNames)){
			$varNames = array($varNames);
		}
		
		if(is_array($varNames)){
			$returnVars = array();
			
			foreach($varNames as $name){
				if(array_key_exists($name, $definedVars)){
					$returnVars[$name] = $definedVars[$name];
				}
			}
			
			if(count($returnVars) == 0){
				return null;
			}
			
			$keys = array_keys($returnVars);
			
			return count($keys)==1 ? $returnVars[$keys[0]] : $returnVars;
		}
		
		return null;
	}
	
	
	public function moduloExiste($modulo){
		if(array_key_exists($modulo, $this->__dirModulos))
			return true;

		if($this->fileExists("/app/$modulo")){
			$this->__dirModulos[$modulo] = $this->__basePath.'/app/'.$modulo;
			return true;
		} else {
			return false;
		}

	}
	
	public function controladorExiste($modulo, $controlador){
		if(!$this->moduloExiste($modulo))
			throw new ModuloInexistenteException($modulo);
			
		return $this->fileExists("app/$modulo/pageControllers/$controlador.php");
	}
	
	public function getModuleConfDir($module){
		if(!$this->moduloExiste($module)){
			throw new ModuloInexistenteException();
		}
		
		return "app/$module/config";
	}
	
	public function loadModuleConfig($module, $environment){
		$this->loadConfig($this->getModuleConfDir($module), $environment);
	}
	
	public function loadConfig($path, $environment){
		
		if($path[strlen($path)-1] != DIRECTORY_SEPARATOR){
			$path .= DIRECTORY_SEPARATOR;
		}
		
		$path .= strtolower($environment).'.php';
		
		$loadedConfig = $this->includeFileAndGetVariables($path, 'config');
		
		if($loadedConfig === false){
			Logger::logError("Configuration not found in ".$path);
			return;
		}
		
		global $config;
		if (is_null($config)){
			$config = array();
			$config['plugins'] = array();
		}
		
		$loadedConfig['plugins'] = array_merge($config['plugins'], $loadedConfig['plugins']);
		$config = array_merge($config, $loadedConfig);
	}
	
	public function loadPlugins($config){
		//Load modules
		if(array_key_exists('plugins', $config))
			
			$config['plugins'] = array_unique($config['plugins']);
		
			foreach($config['plugins'] as $plugin){
			if(!in_array($plugin, $this->__pluginsCargados)){
				$pathPlugin = "include/$plugin/PluginLoader".ucfirst($plugin).".php";
				if($this->fileExists($pathPlugin)){
					$this->requireOnce($pathPlugin);
					$nombreClase = "PluginLoader".ucfirst($plugin);
					$pluginLoader = new $nombreClase;
					$pluginLoader->loadHook();
					$this->addPluginLoader($pluginLoader);
					array_push($this->__pluginsCargados, $plugin);
				}
			}
		}
		$config['plugins'] = $this->__pluginsCargados;
	}
	
	public function addPluginLoader(PluginLoader $ml){
		array_push($this->__pluginLoaders, $ml);
	}
	
	public function isModel($class_name){
		return preg_match('/_AR$/', $class_name) > 0;
	}
	
	public function loadModel($class_name, $moduleName){
		$path = "app/$moduleName/model/$class_name.php";

		if(!$this->fileExists($path)){
			throw new ModuloInexistenteException();
		}
		
		$this->requireOnce($path);
	}
	
	public function loadClass($class_name, $moduleName = null){
		
		if(!is_null($moduleName) && $this->isModel($class_name)){
			$this->loadModel($class_name, $moduleName);
			return;
		}
		
		//Search in module loaders
		$path = null;
		
		foreach($this->__pluginLoaders as $pl){
			$path = $pl->getPath($class_name);
			if($path && $this->fileExists($path))
				break;
		}
		
		//Search include directory
		if(is_null($path)){
			if($this->fileExists("include/$class_name.php")){
				$path = "include/$class_name.php";
			}
		}
		
		if(is_null($path)){
			throw new ClassNotFoundException($class_name);
		} else {
			$this->requireOnce($path);
		}
					
	}
}

?>