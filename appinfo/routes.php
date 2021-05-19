<?php
return [
	'routes' => [
		['name' => 'CustomProperties#index', 'url' => '/properties', 'verb' => 'GET'],
		['name' => 'CustomProperties#update', 'url' => '/properties', 'verb' => 'POST'],

		['name' => 'AdminSettings#index', 'url' => '/customproperties', 'verb' => 'GET'],
		['name' => 'AdminSettings#create', 'url' => '/customproperties', 'verb' => 'POST'],
		['name' => 'AdminSettings#delete', 'url' => '/customproperties/{id}', 'verb' => 'DELETE']
	]
];
