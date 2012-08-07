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




class Render {
	
	protected $_datos = array();
	
	//TODO: crear el metodo escape que reemplaza los caracteres no validos como '<' '>' '/'
	
	public function __get($dato){
		$valor = array_key_exists($dato, $this->_datos)?$this->_datos[$dato]:'';
		
		return $valor;
	}
	
	public function __set($nombre, $valor){
		$this->_datos[$nombre] = $valor;
	}
	
	public function agregarArray(array $array){
		foreach($array as $key => $valor){
			$this->$key = $valor;
		}
	}
		
	public function incluirVariablesGet(){
		$this->GET = $_GET;
	}
	
	public function getDatos(){
		return $this->_datos;
	}
	
	public function accion($texto, $nombreControlador, $nombreAccion, $idActivo, $parametros = null, $atributos = null){
		$_param = '';
		$_clase = '';
		
		$url = $nombreControlador===null?'':'/'.$this->app.'/'.$nombreControlador;
		if($idActivo && $nombreControlador)
			$url .= "/$idActivo";
			
		$nombreAccion = $nombreAccion===null?'':'a='.$nombreAccion;
		
		if (is_array($atributos) && array_key_exists('class', $atributos))
			$_clase = 'class="'.$atributos['class'].'" ';
		
		if(is_array($parametros))
			foreach($parametros as $clave => $valor)
				$_param .= "&$clave=$valor";
		
		if($nombreAccion || $_param)
		 $url .= "?$nombreAccion"."$_param'";
			
		echo "<a href='$url' $_clase>$texto</a>";
		
	}

}