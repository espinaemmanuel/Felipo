<?php

class Query extends RecursoRest {
	
	public function get(Request $request){
		$query = new SolrQuery('http://localhost:8983/solr/');
		$query->addFilter('cat', $this->_controller->getParam('cat'));
		$query->setQuery($this->_controller->getParam('text'));
		$query->setSort($this->_controller->getParam('sort'));
		$query->setOffset($this->_controller->getParam('start'));
		$query->setNumResults($this->_controller->getParam('rows'));
				
		$results = $query->execute();
		
		$this->num = $results['response']['numFound'];
		$docs = $results['response']['docs'];
		$highlighting = array_key_exists('highlighting', $results)? $results['highlighting']:array();
		
		$this->docs = $docs;
	}
}

?>