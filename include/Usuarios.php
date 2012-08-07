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




class Usuarios {
	
	private $_uid = null;
	private $_db = null;
	
	private $_identityPersistor;
	
	/**
	 * @return the $_identityPersistor
	 */
	public function getIdentityPersistor() {
		return $this->_identityPersistor;
	}

	/**
	 * @param field_type $_identityPersistor
	 */
	public function setIdentityPersistor($_identityPersistor) {
		$this->_identityPersistor = $_identityPersistor;
	}

	public function __construct(){
		$this->getUId();
	}
	
	
	//TODO: Pasar al persistor
	/**
	 * Enter description here...
	 *
	 * @return DbConnection
	 */
	public function getDb(){
		if($this->_db == null)
			$this->_db = new DbConnection('intranet');
		return $this->_db;
	}
	
	/**
	 * Cambia de usuario y salta a otro modulo.
	 *
	 * @param unknown_type $uid
	 * @param unknown_type $destino
	 */
	
	public function cambiarUsuario($uid){
		
		if(is_null($uid))
			throw new ParametroIncorrectoException('uid=null');
			
		$_SESSION ['usuario'] = $uid;
		$this->_uid = $_SESSION ['usuario'];
		
	}
	
	/**
	 * Devuelve el usuario actualmente en sesion. null si todavia el usuario no inicion sesion
	 *
	 * @return unknown
	 */
	
	public function getUId() {
		if($this->_uid ==null && isset($_SESSION ['usuario']))
			$this->_uid = intval($_SESSION ['usuario']);
		return $this->_uid;
	}
	
	/**
	 * Obtiene el nombre de usuario del usuario en sesion
	 *
	 * @param unknown_type $uid
	 */
	
	public function getUsuario(){
		
		//TODO: Usar el persistor de usuarios
		
		$uid = $this->getUId();
		if(is_null($uid))
			return null;
		
		$uid = intval($uid);		
		$sql = "SELECT usuario FROM Usuario WHERE uid = $uid";		
		$usuario = $this->getDb()->query($sql);		
		return $usuario[0]['usuario'];
	}
	
	/**
	 * Busca el uid del usuario cuyo nombre es pasado como parametro. Si el usuario no existe devuelve null
	 *
	 */
	
	public function buscarUId($usuario){
		
		//TODO: usar el persistor
		
		if(is_null($this->_identityPersistor)){
			return $usuario;
		}
	
		/*
		$sql = "SELECT u.uid FROM Usuario u WHERE usuario = '$usuario'";
											 
		$array = $this->getDb()->query($sql);
		if(count($array)>1)
			throw new ResultadoInesperadoException("Se ha encontrado mas de un usuario con el nombre $usuario");
		
		if(count($array)==0)
			return null;
		
		return intval($array[0]['uid']);
		*/
	}
	
	/**
	 * Agrega un nuevo usuario al sistema y devuelve su uid
	 *
	 */
	
	public function agregarUsuario($usuario){
		
		//TODO: Usar el persistor de usuarios
		
		if($this->buscarUId($usuario)!==NULL)
			throw new UsuarioExistenteException("No se puede agregar el usuario $usuario, porque el mismo ya existe");

		$this->getDb()->beginTransaction();
			$array = $this->getDb()->query("SELECT MAX(uid) id FROM Usuario");
			$id = $array[0]['id'] + 1;		
			$array = $this->getDb()->query("INSERT INTO Usuario VALUES ($id, '$usuario')");
		$this->getDb()->commit();

		return $id;
	}
	
	public function getGrupos(){
		
		//TODO: Usar el persistor de usuarios
		
		if($this->_uid === null)
			return array();
		
		$uid = intval($this->_uid);
					
		$sql = "SELECT g.nombre FROM Grupo g, UsuarioGrupo ug
				WHERE 	ug.idUsuario = $uid AND
						ug.idGrupo = g.id";
											 
		$array = $this->getDb()->query($sql);
		$r = array();
		foreach($array as $valor)
			array_push($r, $valor['nombre']);

		return $r;
	}
	
	public function canAccess($user, $module){
		return false;
	}
	
	public function canAnyoneAccess($module){
		return false;
	}
	
}