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




class AutentificadorLdap implements Autentificador {
	
	private $_usuario;
	private $_password;
	private $_parametrosLdap;
	
	public function __construct($usuario, $password, array $parametrosLdap) {
		$this->_usuario = $usuario;
		$this->_password = $password;
		$this->_parametrosLdap = $parametrosLdap;
	}
	
	/**
	 * Realiza la autentificacion
	 * 
	 * @return ResultadoAutentificar
	 */
	public function autentificar() {
		
		$ds = @ldap_connect ( $this->_parametrosLdap ['host'] );
		$filtro = sprintf ( $this->_parametrosLdap ['accountFilterFormat'], $this->_usuario );
		$search = @ldap_search ( $ds, $this->_parametrosLdap ['baseDn'], $filtro );
		
		if(!$search)
			throw new LdapException("No se pudo conectar al servidor de dominio");
		
		if (@ldap_count_entries ( $ds, $search ) == 0)
			return new ResultadoAutentificar ( ResultadoAutentificar::FALLO_IDENTIDAD_NO_ENCONTRADA, null, array ("El nombre de usuario no existe" ) );
		
		if (@ldap_count_entries ( $ds, $search ) > 1)
			throw new AutentificadorException ( "El nombre de usuario aparece mas de una ves en el servidor de directorio" );
		
		$info = @ldap_get_entries ( $ds, $search );
		
		@ldap_free_result ( $search );
		
		$usuario = str_replace ( 'dc', 'DC', $info [0] ['dn'] );
		
		$bind = @ldap_bind ( $ds, $usuario, $this->_password );
		if (! $bind || ! isset ( $bind ))
			return new ResultadoAutentificar ( ResultadoAutentificar::FALLO_CREDENCIAL_INVALIDA, null, array ("El password es incorrecto" ) );
		
		return new ResultadoAutentificar ( ResultadoAutentificar::EXITO, $this->_usuario, array () );
	
	}
}