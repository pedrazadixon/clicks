<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

$routes->post('/generate', 'Home::generate');

$routes->get('/s/(:any)', 'Home::share/$1');

foreach (explode(',', getenv('EXCLUDED_SHORTCODES')) as $shortcode) {
    $routes->get($shortcode, function () {
        return 'Este shortcode está reservado.';
    });
}

$routes->get('(:any)', 'Home::redirectOrShow/$1');
