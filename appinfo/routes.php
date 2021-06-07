<?php
return [
	'routes' => [
		['name' => 'CustomProperties#index', 'url' => '/customproperties', 'verb' => 'GET'],
		['name' => 'CustomProperties#create', 'url' => '/customproperties', 'verb' => 'PUT'],
		['name' => 'CustomProperties#update', 'url' => '/customproperties', 'verb' => 'POST'],
		['name' => 'CustomProperties#delete', 'url' => '/customproperties/{id}', 'verb' => 'DELETE']
	]
];
