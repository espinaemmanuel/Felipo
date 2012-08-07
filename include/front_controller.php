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



$base_path = preg_replace('/include\/front_controller.php$/', '', __FILE__);

if(!file_exists($base_path.'include/Loader.php'))
	die('No se encuentra el loader');
require_once $base_path.'include/Loader.php';

$loader = Loader::getInstance();
$loader->setBasePath($base_path);
$loader->requireOnce('include/PluginLoader.php');
$loader->addPluginLoader(new PluginLoaderControllers());

if(!set_include_path($loader->getLibPath()))
	die('no se puede modificar include path');

$environmentResolver = new EnvironmentResolver();
$environmentResolver->loadRules();

global $ENV;
$ENV = $environmentResolver->resolve();

// Cargar la configuracion general de todos los modulos
$loader->loadConfig('conf', $ENV);

function __autoload($class_name) {
	global $MODULE;
	Loader::getInstance()->loadClass($class_name, $MODULE);
}

//Obtener nombre del modulo, controlador y accion
$request = new Request();
$MODULE = $request->getModulo();

//Verificar que la aplicacion existe
if(!$loader->moduloExiste($request->getModulo())){
	header("HTTP/1.0 404 Not Found");
	echo "error: el modulo ". $request->getModulo()." no existe";
	exit();
} 

//Cargar la configuracion especifica del modulo sobreescribiendo la anterior
$loader->loadModuleConfig($request->getModulo(), $ENV);

//Establecer la conexion por defecto, que es el nombre de la aplicacion
global $config;

//Mandatory plugins
$config['plugins'] = array_merge($config['plugins'], array('viewHelpers'));
$loader->loadPlugins($config);

if($config['ssl'] && $_SERVER['HTTPS']!="on")
  {
     $redirect= "https://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
     header("Location:$redirect");
  }
	
/**
 * Datos que se incluiran en el template
 */
$r = new Render();
$u = new Usuarios();

//Obtener el controlador

$nombreControlador = ucfirst($request->getControlador()).'Controller';

if(!$loader->controladorExiste($request->getModulo(), $nombreControlador)){
	header("HTTP/1.0 404 Not Found");
	echo "error: el recurso ". $request->getModulo()." no existe";
	exit(0);
}

$controlador = new $nombreControlador;
$controlador->setUri($request->getUri());
$controlador->initSession();
$controlador->run($request);