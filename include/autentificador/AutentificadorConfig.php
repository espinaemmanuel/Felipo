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




class AutentificadorConfig implements Autentificador {
	
	private $usuario;
	private $password;
	
	public function __construct($usuario, $password){
		$this->usuario = $usuario;
		$this->password = $password;		
	}
	
	public function autentificar(){
		
		global $config;
		
		$auth = $config['auth'];
		
		if(is_null($auth))
			throw new AutentificadorException('$config[\'auth\'] no esta definida');
		
		if(!array_key_exists('users', $auth)){
			throw new AutentificadorException('$config[\'auth\'][\'users\'] no esta definida');
		}
		
		if(!array_key_exists($this->usuario, $auth['users'])){
			return new ResultadoAutentificar(ResultadoAutentificar::FALLO_IDENTIDAD_NO_ENCONTRADA, null, array("El nombre de usuario no existe"));
		}
		
		if($auth['users'][$this->usuario] != $this->password){
			return new ResultadoAutentificar(ResultadoAutentificar::FALLO_CREDENCIAL_INVALIDA , null, array("El password es incorrecto"));
		}	
					
		return new ResultadoAutentificar(ResultadoAutentificar::EXITO , $this->usuario, array());	

	}
}