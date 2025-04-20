<?php

$routes->get('/', 'Home::index');
$routes->post('/generate', 'Home::generate');
$routes->get('/s/(:any)', 'Home::share/$1');
$routes->get('/p/(:any)', 'Home::protected/$1');
$routes->post('/p/(:any)', 'Home::protected/$1');
$routes->get('(:any)', 'Home::redirectOrShow/$1');
$routes->post('(:any)', 'Home::redirectOrShow/$1');
