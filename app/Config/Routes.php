<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

$routes->group('crm', function ($routes) {

    $routes->group('customer', function ($routes) {
        $routes->get('fetch-customer', 'Customers\CustomerController::fetchCustomer');
        $routes->post('add-customer', 'Customers\CustomerController::creatCustomer');
    });
}); 