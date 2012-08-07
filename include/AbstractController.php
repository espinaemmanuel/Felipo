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




abstract class AbstractController {
	
	private $errors = array();
	protected $template;
	protected $uri;
	protected $request;
	protected $anonimousAccess = null;
	protected $_loginParams = null;
	
	
	/**
	 * possible values: false, public, private_no_expire, private, nocache
	 * @var unknown_type
	 */
	protected $_cacheType = null;

	public function getBd(){
		return ConnectionManager::getInstance()->get();				
	}
	
	public function setUri($uri){
		$this->uri = $uri;
	}
	
	public function initSession(){
		if(!is_null($this->_cacheType)){
			session_cache_limiter($this->_cacheType);
		}
		session_start();
	}
	
	/**
	 * Nothing done by default
	 *
	 */
	protected function indexAction(){
		
	}
	
	/**
	 * Returns the ACL associated with this controller.
	 * The ACL defines control over executed actions.
	 *
	 * @return unknown
	 */
	public function getAcl(){
		$className = get_class($this);
		
		return Loader::getInstance()->getAcl($className, $this->request->getModulo());
	}
	
	protected function loadData(){
		
	}
	
	/**
	 * Analiza la accion pasada como parametro y la ejecuta.
	 * 
	 * La accion la toma del parametro. Si el mismo es null, la toma de los parametros pasados en el POST o GET
	 * Toma primero la accion de POST y si no existe, del GET. La accion viene en una variable llamada 'a'
	 *
	 * Si no hay ninguna accion, ejecuta la accion por defecto index
	 *
	 */
	
	protected function processForm($action = null){	
		if(is_null($action)){
			if(array_key_exists('a', $_POST)){
				$action = $_POST['a'];
			} else if(array_key_exists('a', $_GET)) {
				$action = $_GET['a'];
			} else {
				$action = 'index';
			}
		}
		
		$methodName = ucfirst($action).'Action';
		if(method_exists($this, $methodName)){
			$this->invokeAction($methodName);
		} else {
			throw new UndefinedActionException($action);								
		}
	}
	
	protected function invokeAction($methodName){
		$acl = $this->getAcl();
		
		if($acl && !$acl->isAllowed($methodName, $this))
			throw new RecursoNoAutorizadoException($methodName);
		
		$this->$methodName();		 
	}
	
	public function getModuleName(){
		return $this->request->getModulo();
	}
	
	public function run(Request $request){
		try{
			$this->request = $request;
			
			if(!$this->canAccess()){
				
				global $u;
				if ($u->getUId()===null){	
										
					$data = array();
					$data['redirect'] = $this->request->getUri();
					$data['query'] = http_build_query($_GET);
					if($this->_loginParams){
						$data = array_merge($data, $this->_loginParams);
					}					
					$q = http_build_query($data);
					
					global $config;
					$this->redirect($config['loginPage'].'/?'.$q);
				} else 
					throw new AccesoNoAutorizadoException("No tiene permiso para ver esta pagina");
			}

			$this->r = (object) array();
			$this->r->hh = new HtmlHelper($this->getModuleName());
			$this->r->app = $this->getModuleName();
			$this->r->uri = $this->uri;
				
			$this->processForm();
			$this->loadData();
			
			$this->addCssAndJs();
				
			$this->r->errors = $this->errors;

			
			$this->render($this->getView());
			
		}catch(Exception $e){
			
			Logger::logException($e);
			
			$template = Loader::getInstance()->loadCommonTemplate('exception');				
			$view = new View($template);
			$r = (object) array();
			$r->e = $e;
			$view->setAssociations($r);
			$view->render();
		}
		
	}
	
	protected function addCssAndJs(){
		if(isset($this->css) && is_array($this->css)){
			foreach($this->css as $css){
				$this->r->hh->addCss($css);
			}
		}
		
		if(isset($this->external_js) && is_array($this->external_js)){
			foreach($this->external_js as $external_js){
				$this->r->hh->addExternalJs($external_js);
			}		
		}
		
		if(isset($this->js) && is_array($this->js)){
			foreach($this->js as $js){
				$this->r->hh->addJs($js);
			}
		}
	}
	
	protected function defaultTemplate(){
		$controller = get_class($this);
		global $request;
		$module = $request->getModulo();
		
		$tplPath = Loader::getInstance()->getDefaultTemplate($controller, $module);
		
		return new Template($tplPath);
	}
	
	public function getView(){
			
		if($this->template === null)
			$this->template = $this->defaultTemplate();
			
		return new View($this->template);
	}
	
	public function render(View $view){
		if($view === null)
			throw new ControladorException('View not defined');
		
		$view->setAssociations($this->r);
		$view->render();
	}
	
	public function get($string = null){
		$val = array_key_exists($string, $_GET)?$_GET[$string]:null;
		
		if(!is_null($val)){
			$val = urldecode($val);
		}
		
		return $val;
	}
	
	public function post($string = null){
		return array_key_exists($string, $_POST)?$_POST[$string]:null;
	}
	
	public function getParam($string = null){
		$p = $this->get($string);
		if (is_null($p)){
			$p = $this->post($string);
		}
		return $p;
	}
	
	protected function redirect($url){
		header("Location: $url");
		exit(0);		
	}
	
	protected function addError($error){
		if($error instanceof Error ){
				array_push($this->errors, $error);
		} else if(is_string($error)){
				array_push($this->errors, new Error($error));
		}
	}
	
	/**
	 * Especifica si el usuario actualmente logueado tiene permiso para acceder a este controlador.
	 *
	 * @return unknown
	 */
		
	protected function canAccess(){
		global $u;
		global $config;
		
		if ($this->anonimousAccess === true || $config['anonymousAccess'] === true && $this->anonimousAccess !== false)
			return true;
		
		if($u->getUId() === null)
			return false;
			
		if($config['notRestrictedAccess'] === true){
			return true;
		}
		
		if($u->canAnyoneAccess($this->getModuleName())){
			return true;
		};
		
		if($u->canAccess($u->getUId(), $this->getModuleName())){
			return true;
		};
		
		return false;
	}
		
	protected function addErrors(array $errors){
		foreach($errors as $error)
			$this->addError($error);
	}
	
	protected function gotErrors(){
		return count($this->errors)>0;
	}
	
	protected function validateParams($formFile){
		$vb = new ValidatorBuilder();
		$v = $vb->buildValidator(Loader::getInstance()->getFullPath('app/'.$this->getModuleName().'/form/'.$formFile));

		return $v->esValido($this->request->getParamsAsArray());
	}
}