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




class AutentificadorBaseDatos implements Autentificador {
	
	private $usuario;
	private $password;
	private $db;
	
	public $userField = 'usuario';
	public $passwordField = 'password';
	public $tableName = 'Usuario';
	public $uidField = 'uid';
	public $enabledField = 'valido';
	
	
	public function __construct($usuario, $password, DbConnection $conn){
		$this->usuario = $usuario;
		$this->password = $password;
		$this->db = $conn;
	}
	
	public function validate(){
		if(!ereg('^[A-Za-zÑñ0-9]*$', $this->usuario))
			throw new AutentificadorException('El nombre de usuario contiene caracteres distintos de letras y numeros');

		if(!ereg('^[A-Za-zÑñ0-9]*$', $this->password))
			throw new AutentificadorException('El password contiene caracteres distintos de letras y numeros');
	}
	
	/**
	 * Realiza la autentificacion
	 * 
	 * @return ResultadoAutentificar
	 */
	public function autentificar(){
		
		$this->validate();
		
		$usuario = $this->db->getTodos($this->tableName, array( 'where' => sprintf("%s = '%s'", $this->userField, $this->usuario)));
					
		if(count($usuario)>1)
			throw new AutentificadorException('Error inesperado: se encontraron varios usuarios con ese nombre de usuario');
		
		if(count($usuario)==0)
				return new ResultadoAutentificar(ResultadoAutentificar::FALLO_IDENTIDAD_NO_ENCONTRADA, null, array("El nombre de usuario no existe"));
		
		$usuario = $usuario[0];

		$this->password = EncriptadorPasswords::encriptar($this->password);
		
		if (array_key_exists($this->enabledField, $usuario) && !$usuario[$this->enabledField])
			return new ResultadoAutentificar(ResultadoAutentificar::FALLO_CREDENCIAL_INVALIDA , null, array("El usuario no esta habilitado"));
		
		if ($this->password !== $usuario['password'])
			return new ResultadoAutentificar(ResultadoAutentificar::FALLO_CREDENCIAL_INVALIDA , null, array("El password es incorrecto"));
			
		return new ResultadoAutentificar(ResultadoAutentificar::EXITO , $usuario[$this->uidField], array());	

	}
}