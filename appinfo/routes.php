<?php
/**
 * Create your routes in here. The name is the lowercase name of the controller
 * without the controller part, the stuff after the hash is the method.
 * e.g. page#index -> OCA\CustomProperties\Controller\PageController->index()
 *
 * The controller class has to be registered in the application.php file since
 * it's instantiated in there
 */
return [
	'routes' => [
		['name' => 'CustomProperties#index', 'url' => '/properties', 'verb' => 'GET'],
		['name' => 'CustomProperties#update', 'url' => '/properties', 'verb' => 'POST'],

		['name' => 'AdminSettings#index', 'url' => '/customproperties', 'verb' => 'GET'],
		['name' => 'AdminSettings#create', 'url' => '/customproperties', 'verb' => 'POST'],
		['name' => 'AdminSettings#delete', 'url' => '/customproperties/{id}', 'verb' => 'DELETE']
	]
];
