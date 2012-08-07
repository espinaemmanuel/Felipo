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




class Logger {
	
	private static $_log;
	
	private static function getZendLog(){		
		if(!self::$_log){
			require_once 'Zend/Log/Writer/Stream.php';
			require_once 'Zend/Log.php';
			
			global $config;
			$writer = new Zend_Log_Writer_Stream(Loader::getInstance()->getFullPath($config['logFile']));
			self::$_log = new Zend_Log($writer);			
		}
		
		return self::$_log;	
	}
	
	public static function logException(Exception $e){
		$file = $e->getFile();
		$uri = $_SERVER['REQUEST_URI'];
		$linea = $e->getLine();
		$mensaje= $e->getMessage();
		
		self::getZendLog()->log("[$uri] $file ($linea): $mensaje", Zend_Log::ERR);
	}
	
	public static function logError($error){
		$uri = $_SERVER['REQUEST_URI'];
		
		self::getZendLog()->log("[$uri] ERROR RUNTIME: $error", Zend_Log::ERR);
	}
	
	public static function logWarning($warning){
		$uri = $_SERVER['REQUEST_URI'];
		
		self::getZendLog()->log("[$uri] ERROR RUNTIME: $warning", Zend_Log::WARN);
	}

}