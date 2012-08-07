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




class ResultadoAutentificar {
	
	const FALLO = -1;
	const FALLO_CREDENCIAL_INVALIDA = -2;
	const FALLO_IDENTIDAD_NO_ENCONTRADA = -3;
	const EXITO = 1;
		
	private $_codigo;
	private $_identidad;
	private $_mensajes;
	
	public function __construct($codigo, $identidad, array $mensajes = array()){
		$this->_codigo = $codigo;
		$this->_identidad = $identidad;
		$this->_mensajes = $mensajes;		
	}
	
	public function getCodigo(){
		return $this->_codigo;
	}
	
	public function getIdentidad(){
		return $this->_identidad;
	}
	
	public function getMensajes(){
		return $this->_mensajes;
	}
	
	public function esValido(){
		return $this->_codigo == self::EXITO ;
	}
		
}