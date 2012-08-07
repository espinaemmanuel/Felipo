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




class Request {
	
	private $_uri;
	private $_modulo;
	private $_controlador;
	private $_idActivo;
	
	private $_get;
	private $_post;

	public function getUri() {
		return $this->_uri;
	}
	
	public function getComponenteUri($indice){
		$array_uri = preg_split( '/\//', $this->getUri(), 4, PREG_SPLIT_NO_EMPTY);
		return  array_key_exists($indice, $array_uri)?$array_uri[$indice]:null;
	}

	public function getModulo() {
		return $this->_modulo;
	}

	public function getControlador() {
		return $this->_controlador;
	}

	public function getIdActivo() {
		return $this->_idActivo;
	}

	public function setUri($_uri) {
		$this->_uri = $_uri;
	}

	public function setModulo($_modulo) {
		$this->_modulo = $_modulo;
	}

	public function setControlador($_controlador) {
		$this->_controlador = $_controlador;
	}

	public function setIdActivo($_idActivo) {
		$this->_idActivo = $_idActivo;
	}

	public function __construct(){
		//Quitar la parte de la query que esta despues de ?
		$this->_uri = preg_replace( '/\?.*$/' , ''  , $_SERVER['REQUEST_URI']);
		
		//Quitar el prefijo si ha sido definido
		global $config;
		if(array_key_exists('prefix', $config)){
			$this->_uri = str_replace( $config['prefix'] , ''  , $this->_uri);
		}			

		$array_uri = preg_split( '/\//', $this->_uri, 4, PREG_SPLIT_NO_EMPTY);

	
		$this->_modulo = isset($array_uri[0])?$array_uri[0]:$config['defaultModule'];
		$this->_controlador = isset($array_uri[1])?$array_uri[1]:'index';
		$this->_idActivo =  isset($array_uri[2])?$array_uri[2]:null;
		
	}
	
	private function escapeQuotes() {
		if (get_magic_quotes_gpc ()) {
			if (! $this->_get) {
				$this->_get = array ();
				foreach ( $_GET as $key => $val ) {
					$this->_get [$key] = stripcslashes($val);
				}
			}
			
			if (! $this->_post) {
				$this->_post = array ();
				foreach ( $_POST as $key => $val ) {
					$this->_post [$key] = stripcslashes($val);
				}
			}		
		}	
	}
	
	public function get($string = null){
		$this->escapeQuotes();
		if($string == null){
			return $this->_get;
		}
		return array_key_exists($string, $this->_get)?$this->_get[$string]:null;
	}
	
	public function post($string = null){
		$this->escapeQuotes();
		if($string == null){
			return $this->_post;
		}
		return array_key_exists($string, $this->_post)?$this->_post[$string]:null;
	}
	
	public function setParam($string, $value){
		$this->escapeQuotes();
		$this->_post[$string] = $value;
	}
	
	public function getParam($string = null){
		
		if($string == null){
			return array_merge($this->get(), $this->post());
		}
		
		$p = $this->get($string);
		if (is_null($p)){
			$p = $this->post($string);
		}
		return $p;
	}
	
	public function getParamsAsArray(){
		return array_merge($this->get(), $this->post());
	}
	
}

?>