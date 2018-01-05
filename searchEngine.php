<?php
include "bootstrap.php";

function search($keyword) {

	$options = array
	('hostname' => SOLR_SERVER_HOSTNAME, 
	 'login' => SOLR_SERVER_USERNAME, 
	 'password' => SOLR_SERVER_PASSWORD, 
	 'port' => SOLR_SERVER_PORT, 
	);

	$client = new SolrClient($options);

	//$client -> setResponseWriter('json');

	$query = new SolrQuery();

	$query -> setQuery($keyword);
	

	$query -> setStart(0);

	$query -> setRows(2000);

	$query -> addField('url') -> addField('title') -> addField('content') -> addField('anchor');

	$response = null;

	try {
		$query_response = $client -> query($query);
		$response = $query_response -> getResponse();
	} 
	catch(Exception $e) {
		print $e -> getMessage();
	}
	
	return $response;
}
?>

