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

    $routes->group('quotation', function ($routes) {
        $routes->get('fetch-quotation', 'Quotation\QuotationController::fetchQuotation');
        $routes->post('add-quotation', 'Quotation\QuotationController::creatQuotation');
    });
    $routes->group('invoice', function ($routes) {
        $routes->get('fetch-invoice', 'Invoice\InvoiceController::fetchInvoice');
        $routes->post('add-invoice', 'Invoice\InvoiceController::creatInvoice');
    });
    
    $routes->group('product', function ($routes) {
        $routes->get('fetch-product', 'Products\ProductController::fetchProduct');
        $routes->post('add-product', 'Products\ProductController::creatProduct');
        $routes->post('update-product', 'Products\ProductController::updateProduct');
    });
    
    $routes->group('category', function ($routes) {
        $routes->get('fetch-category', 'Category\CategoryController::fetchCategory');
        $routes->get('fetch-list-category', 'Category\CategoryController::fetchCategoryList');
        $routes->post('add-category', 'Category\CategoryController::creatCategory');
        $routes->post('update-category', 'Category\CategoryController::updateCategory');
    });


    $routes->group('graphic', function ($routes) {
        $routes->post('add-graphic', 'Graphic\GraphicController::addGraphic');
        $routes->get('fetch-graphic', 'Graphic\GraphicController::fetchGraphic');
    });
    
    
});
$routes->get('product', 'Products\ProductController::fetchProduct');

$routes->group('web', function ($routes) {

    $routes->group('customer', function ($routes) {
        $routes->post('login', 'Authentication\LoginController::login');
        $routes->get('detail', 'Customers\CustomerController::CustomerDetail');
        $routes->get('add-to-cart', 'Customers\CustomerController::addToCart');
        $routes->get('cart-list', 'Customers\CustomerController::CartList');
        $routes->get('cart-remove', 'Customers\CustomerController::removeCart');
    });
    
    $routes->group('product', function ($routes) {
        $routes->get('fetch-product', 'Products\ProductController::fetchProduct');
        $routes->get('single-product', 'Products\ProductController::singleProduct');
    });
    
    $routes->group('category', function ($routes) {
        $routes->get('fetch-all', 'Category\CategoryController::fetchCategory');
        $routes->get('single-category', 'Category\CategoryController::singleCategory');
    });
    
    $routes->group('inquiry', function ($routes) {
        $routes->post('insert-inquiry', 'Customers\CustomerController::creatInquiry');
    });
    
    $routes->group('home', function ($routes) {
        $routes->get('fetch-all', 'Products\ProductController::fetchHome');
    });

});