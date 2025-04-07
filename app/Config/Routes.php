<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

$routes->post('/generate', 'Home::generate');

$routes->get('/app/debug', 'Debug::index');


// This route is used to shares without a password
$routes->get('/s/(:any)', 'Home::share/$1');

// This route is used to shares with a password
$routes->get('/p/(:any)', 'Home::protected/$1');
$routes->post('/p/(:any)', 'Home::protected/$1');

foreach (explode(',', getenv('EXCLUDED_SHORTCODES')) as $shortcode) {
    $routes->get($shortcode, function () {
        return 'Este shortcode está reservado.';
    });
}

$routes->get('(:any)', 'Home::redirectOrShow/$1');
