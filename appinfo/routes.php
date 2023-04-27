<?php
/**
 * Create your routes in here. The name is the lowercase name of the controller
 * without the controller part, the stuff after the hash is the method.
 * e.g. page#index -> OCA\AdvancedDashboard\Controller\PageController->index()
 *
 * The controller class has to be registered in the application.php file since
 * it's instantiated in there
 */
return [
    'routes' => [
	['name' => 'page#index', 'url' => '/', 'verb' => 'GET'],
	['name' => 'page#active', 'url' => '/usersactive', 'verb' => 'GET'],
       	['name' => 'page#postactive', 'url' => '/usersactive/active', 'verb' => 'POST'],
       	['name' => 'page#filesByTime', 'url' => '/filesbytime','verb' => 'GET'],
       	['name' => 'page#filesByTimePOST', 'url' => '/filesbytime/post','verb' => 'POST'],
    ]
];
