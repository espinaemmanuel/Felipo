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




class View {
	
	const HTTP_OK = 'HTTP/1.0 200 Ok';
	const HTTP_NOT_FOUND = 	'HTTP/1.0 404 Not Found';
	const HTTP_SERVER_ERROR = 'HTTP/1.0 500 Internal Server Error';
	
	
	/**
	 * 
	 * @var Template
	 */
	private $template;
	
	private $r;
	
	private $http_status;
	
	public function __construct(Template $template){
		$this->template = $template;
	}
	
	public function setAssociations($r){
		$this->r = $r;
	}
	
	public function setStatus($status){
		$this->http_status = $status;
	}
	
	public function render(){
		if(!headers_sent() && $this->http_status != null){
			header($this->http_status);
		}
		
		$this->template->render($this->r);
	}

}