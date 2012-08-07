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



class LoginController extends AbstractController{
	
	protected function cargarDatos(){
		global $r;
		$r->redirect = $this->get('redirect');
		
		parent::cargarDatos();
	}
	
	public function accionLogin(){
		
		try{
			
			$usuario = $this->post('usuario');
			$password = $this->post('password');
			
			if ($usuario == null || ! is_string ( $usuario ))
				$this->agregarError('Nombre de usuario no proporcionado');
			
			if ($password == null || ! is_string ( $password ))
				$this->agregarError( 'Password no proporcionado');
			
			if($this->hayErrores())
				return;
				
			//Realizar la autenticacion
						
			global $config;
			
			$authFactory = new $config['authFactory']();			
			$autentificador = $authFactory->getAutentificador($usuario, $password);
			
			$resultado = $autentificador->autentificar ();
			
			if ($resultado->esValido ()) {
				global $u;
				$identidad = $resultado->getIdentidad ();
				
				if($authFactory->areIdentitiesPersistent()){
					$u->setIdentityPersistor($authFactory->getIdentityPersistor());
				}
				
				$uid = $u->buscarUId($identidad);
				
				if(is_null($uid) && $authFactory->areIdentitiesPersistent()){
					$uid = $u->agregarUsuario($identidad);
				}

				$redirect = $this->getParam('redirect');
				if(!$redirect){
					global $config;					
					$redirect = $config ['inicial'];
				}
				
				$q = $this->getParam('query');
				if($q){
					$redirect = $redirect.'?'.$q;
				}
				
				$u->cambiarUsuario($uid);
				
				if(!$this->request->getParam('notRedirect')){
					$this->redirect($redirect);					
				}
				
				global $r;
				$r->validLogin = true;				
				
			} else {
				global $r;
				$r->loginInvalido = true;
				$this->agregarErrores($resultado->getMensajes());		
			}
		
		}catch(LoginException $e){
			$this->agregarError(new Error($e->getMessage()));
		}
	}
			
}