<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

$routes->group('crm', function ($routes) {

    $routes->group('auth', function ($routes) {
        $routes->post('login', 'Authentication\LoginController::crmLogin');
    });

    $routes->group('customer', function ($routes) {
        $routes->get('fetch-customer', 'Customers\CustomerController::fetchCustomer');
        $routes->get('fetch-list-customer-name', 'Customers\CustomerController::fetchCustomerNameList');
        $routes->post('add-customer', 'Customers\CustomerController::creatCustomer');
        $routes->post('update-customer', 'Customers\CustomerController::updateCustomer');
    });

    $routes->group('product', function ($routes) {
        $routes->get('fetch-product', 'Products\ProductController::fetchProduct');
        // $routes->get('fetch-list-customer-name', 'Customers\CustomerController::fetchCustomerNameList');
        $routes->post('add-product', 'Products\ProductController::creatProduct');
        // $routes->post('update-customer', 'Customers\CustomerController::updateCustomer');
    });

});

$routes->group('web', function ($routes) {
    $routes->group('customer', function ($routes) {
        $routes->post('login', 'Authentication\LoginController::login');
    });

    $routes->group('product', function ($routes) {
        $routes->get('fetch-product', 'Products\ProductController::fetchProduct');
    });
   
    $routes->group('home', function ($routes) {
        $routes->get('fetch-all', 'Products\ProductController::fetchHome');
    });

});