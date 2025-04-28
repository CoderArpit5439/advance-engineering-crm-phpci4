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

    $routes->group('dashboard', function ($routes) {
        $routes->get('fetch-dashboard', 'Dashboard\DashboardController::fetchDashboardDetails');
    });


    $routes->group('customer', function ($routes) {
        $routes->get('fetch-customer', 'Customers\CustomerController::fetchCustomer');
        $routes->get('fetch-list-customer-name', 'Customers\CustomerController::fetchCustomerNameList');
        $routes->post('add-customer', 'Customers\CustomerController::creatCustomer');
        $routes->post('update-customer', 'Customers\CustomerController::updateCustomer');
        $routes->get('remove-customer', 'Customers\CustomerController::removeCustomer');
    });

    $routes->group('quotation', function ($routes) {
        $routes->get('fetch-quotation', 'Quotation\QuotationController::fetchQuotation');
        $routes->post('add-quotation', 'Quotation\QuotationController::creatQuotation');
        $routes->get('delete-quotation', 'Quotation\QuotationController::deleteQuotation');
        $routes->post('get-single-product', 'QuotationProduction\QuotationProductionController::getSingleQuotationProduction');
    });

    $routes->group('invoice', function ($routes) {
        $routes->get('fetch-invoice', 'Invoice\InvoiceController::fetchAllInvoices');
        $routes->post('add-invoice', 'Invoice\InvoiceController::creatInvoice');
        $routes->get('delete-invoice', 'Invoice\InvoiceController::deleteInvoice');
        $routes->post('update-invoice', 'Invoice\InvoiceController::updateInvoiceDescription');
    });

    $routes->group('product', function ($routes) {
        $routes->get('fetch-product', 'Products\ProductController::fetchProduct');
        $routes->post('add-product', 'Products\ProductController::creatProduct');
        $routes->post('update-product', 'Products\ProductController::updateProduct');
    });

    $routes->group('lead', function ($routes) {
        $routes->post('add-lead', 'Lead\LeadController::createLead');
        $routes->get('fetch-lead', 'Lead\LeadController::fetchLeads');
        $routes->post('update-lead', 'Lead\LeadController::updateLead');
        $routes->get('remove-lead', 'Lead\LeadController::removeLead');
        $routes->get('search-lead-by-mobile', 'Lead\LeadController::searchLeadByMobile');
    });

    $routes->group('inquiry', function ($routes) {
        $routes->post('add-inquiry', 'Inquiry\InquiryController::addInquiry');
        $routes->get('fetch-inquiry', 'Inquiry\InquiryController::inquiryFetch');
        $routes->post('update-inquiry', 'Inquiry\InquiryController::updateInquiry');
        $routes->get('remove-inquiry', 'Inquiry\InquiryController::removeInquiry');
    });

    $routes->group('task', function ($routes) {
        $routes->post('add-task', 'Task\TaskController::addTask');
        $routes->get('fetch-task', 'Task\TaskController::fetchTask');
        $routes->post('update-task', 'Task\TaskController::updateTask');
        $routes->get('remove-task', 'Task\TaskController::removeTask');
    });

    $routes->group('support', function ($routes) {
        $routes->post('add-support', 'Support\SupportController::addSupport');
        $routes->get('fetch-support', 'Support\SupportController::fetchSupport');
        $routes->post('update-support', 'Support\SupportController::updateSupport');
        $routes->get('remove-support', 'Support\SupportController::removeSupport');
    });

    $routes->group('inventory', function ($routes) {
        $routes->post('add-inventory', 'Inventory\InventoryController::addInventory');
        $routes->get('fetch-inventory', 'Inventory\InventoryController::fetchInventory');
        $routes->post('update-inventory', 'Inventory\InventoryController::updateInventory');
        $routes->get('remove-inventory', 'Inventory\InventoryController::removeInventory');
    });

    $routes->group('media', function ($routes) {
        // $routes->get('media-list', 'Media\MediaController::getMedia');
        $routes->post('add-media', 'Media\MediaController::addMedia');
    });

    $routes->group('graphic', function ($routes) {
        $routes->post('add-graphic', 'Graphic\GraphicController::addGraphic');
        $routes->get('fetch-graphic', 'Graphic\GraphicController::fetchGraphic');
        $routes->post('approve-graphic', 'Graphic\GraphicController::updateGraphic');
    });

    $routes->group('manufacturing', function ($routes) {
        $routes->post('add-manufacturing', 'Manufacturing\ManufacturingController::addManufacturing');
        $routes->post('update-manufacturing', 'Manufacturing\ManufacturingController::updateManufacturing');
        $routes->get('remove-manufacturing', 'Manufacturing\ManufacturingController::removemanufacturing');
        $routes->get('get-manufacturing', 'Manufacturing\ManufacturingController::manufacturingFetch');
    });

    $routes->group('employee', function ($routes) {
        $routes->post('add-employe', 'Employee\EmployeeController::AddEmployee');
        $routes->get('get-employe', 'Employee\EmployeeController::GetEmployee');
        $routes->get('remove-employe', 'Employee\EmployeeController::RemoveEmployee');
    });

    $routes->group('company', function ($routes) {
        $routes->post('add-company', 'Company\CompanyController::AddCompany');
        $routes->get('get-company', 'Company\CompanyController::GetCompany');
        $routes->post('update-company', 'Company\CompanyController::UpdateCompany');
    });

    $routes->group('plant', function ($routes) {
        $routes->post('add-plant', 'Plant\PlantController::AddPlant');
        $routes->get('get-plant', 'Plant\PlantController::GetPlant');
        $routes->post('update-plant', 'Plant\PlantController::UpdatePlants');
    });
    $routes->group('order', function ($routes) {
        $routes->post('add-order', 'Order\OrderController::createOrder');
        $routes->get('get-order', 'Order\OrderController::fetchOrders');
        $routes->post('update-order', 'Order\OrderController::updateOrder');
    });

    $routes->group('product', function ($routes) {
        $routes->post('single-product', 'Products\ProductController::getSingleProduct');
    });

    $routes->group('quotationproduct', function ($routes) {
        $routes->post('add-quotationproduct', 'QuotationProduction\QuotationProductionController::addQuotationProductionController');
    });

    $routes->group('media', function ($routes) {
        // $routes->get('media-list', 'Media\MediaController::getMedia');
        $routes->post('add-media', 'Media\MediaController::addMedia');
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
        $routes->get('remove-cat', 'Category\CategoryController::removeCategory');
        $routes->get('single-category', 'Category\CategoryController::singleCategory');
    });

    $routes->group('inquiry', function ($routes) {
        $routes->post('insert-inquiry', 'Customers\CustomerController::creatInquiry');
    });

    $routes->group('home', function ($routes) {
        $routes->get('fetch-all', 'Products\ProductController::fetchHome');
    });
});
