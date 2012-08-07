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




class RestControllerGenerico extends AbstractController {
	
	protected function accionIndex() {
		$nombreRecurso = ucfirst ( $this->request->getIdActivo () );
		
		try {
			//Verificar que el recurso existe
			$pathRecurso = 'app/' . $this->getModuleName () . '/resources/' . $nombreRecurso . '.php';
			if (! Loader::getInstance ()->fileExists ( $pathRecurso ))
				throw new UnknownRestResourceException ( $nombreRecurso );
				
			Loader::getInstance()->requireOnce($pathRecurso);
			
			$recurso = new $nombreRecurso ( $this );
			
			$metodo = strtolower ( $_SERVER ['REQUEST_METHOD'] );
			
			if(!method_exists($recurso, $metodo)){
				throw new UnsupportedMethodException($metodo);
			}
			
			$recurso->$metodo ( $this->request );
			
			global $r;
			$r->resource = $recurso->serialize();
				
		} catch ( Exception $e ) {
			
			global $r;
			$recurso->setException($e);
			
			Logger::logException($e);
		}
		
		$this->template = 'include/rest/rest.html.php';
	}
}