<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// =============================================================
// BOOTSTRAP
// =============================================================

define('BASE_URL', '/textpro');
define('APP_ROOT', dirname(__DIR__));

require_once APP_ROOT . '/vendor/autoload.php';

session_start();

$dotenv = Dotenv\Dotenv::createImmutable(APP_ROOT);
$dotenv->load();

use App\Core\Router;

$router = new Router();

// =============================================================
// ROTAS PÚBLICAS
// =============================================================

$router->get('/', 'HomeController@index');
$router->get('/plans', 'PlanController@listPlans');

// =============================================================
// AUTENTICAÇÃO
// =============================================================

$router->get('/register', 'AuthController@registerForm');
$router->post('/register', 'AuthController@register');

$router->get('/login', 'AuthController@loginForm');
$router->post('/login', 'AuthController@login');

$router->get('/logout', 'AuthController@logout');
$router->get('/forgot-password', 'AuthController@forgotPasswordForm');
$router->post('/forgot-password', 'AuthController@forgotPassword');
$router->get('/reset-password', 'AuthController@resetPasswordForm');
$router->post('/reset-password', 'AuthController@resetPassword');
// =============================================================
// STRIPE
// =============================================================

$router->get('/plans/checkout/{id}', 'PlanController@checkout');    
$router->get('/plans/success', 'PlanController@success');
$router->post('/webhook/stripe', 'WebhookController@handleStripe');
$router->post('/webhook/asaas', 'WebhookController@handleAsaas');

// =============================================================
// ÁREA DO CLIENTE
// =============================================================

$router->get('/dashboard', 'ClientController@dashboard');
$router->get('/documents', 'ClientController@listDocuments');

$router->get('/editor', 'DocumentController@openEditor');
$router->get('/editor/{id}', 'DocumentController@openEditor');

$router->post('/document/save', 'DocumentController@save');

$router->get('/document/export/pdf/{id}', 'DocumentController@exportPdf');
$router->get('/document/export/mp3/{id}', 'DocumentController@exportMp3');

$router->get('/profile/password', 'ClientController@passwordForm');
$router->post('/profile/password/update', 'ClientController@updatePassword');
$router->get('/profile', 'ProfileController@index');
$router->post('/profile/update', 'ProfileController@update');

$router->get('/payments', 'PaymentController@index');
$router->get('/template/{id}', 'TemplateController@show');
$router->post('/upload/image', 'UploadController@image');




// =============================================================
// ÁREA ADMIN
// =============================================================

$router->get('/admin', 'AdminController@dashboard');

// ---------------- USUÁRIOS ----------------
$router->get('/admin/users', 'AdminUserController@index');
$router->post('/admin/users/deactivate/{id}', 'AdminUserController@deactivate');
$router->post('/admin/users/activate/{id}', 'AdminUserController@activate');
$router->post('/admin/users/reset-password/{id}', 'AdminUserController@resetPassword');
$router->get('/admin/users/create', 'AdminUserController@create');
$router->post('/admin/users/store', 'AdminUserController@store');
$router->get('/admin/users/edit/{id}', 'AdminUserController@editForm');
$router->post('/admin/users/update/{id}', 'AdminUserController@update');
$router->post('/admin/users/trial/(\d+)', 'AdminUserController@giveTrial');

// ---------------- ROLES ----------------
$router->get('/admin/roles', 'AdminRoleController@index');
$router->get('/admin/roles/edit/{id}', 'AdminRoleController@editForm');
$router->post('/admin/roles/edit/{id}', 'AdminRoleController@update');

// ---------------- PERMISSÕES ----------------
$router->get('/admin/permissions', 'AdminPermissionController@index');

// ---------------- USUÁRIOS → ROLES ----------------
$router->get('/admin/users/roles/{id}', 'AdminUserRoleController@editForm');
$router->post('/admin/users/roles/{id}', 'AdminUserRoleController@update');

// ---------------- PLANOS ----------------
$router->get('/admin/plans', 'AdminPlanController@index');
$router->get('/admin/plans/create', 'AdminPlanController@createForm');
$router->post('/admin/plans/create', 'AdminPlanController@create');
$router->get('/admin/plans/edit/{id}', 'AdminPlanController@editForm');
$router->post('/admin/plans/edit/{id}', 'AdminPlanController@update');
$router->post('/admin/plans/deactivate/{id}', 'AdminPlanController@deactivate');

// ---------------- TEMPLATES ----------------
$router->get('/admin/templates', 'AdminTemplateController@index');
$router->get('/admin/templates/create', 'AdminTemplateController@createForm');
$router->post('/admin/templates/create', 'AdminTemplateController@create');
$router->get('/admin/templates/edit/{id}', 'AdminTemplateController@editForm');
$router->post('/admin/templates/edit/{id}', 'AdminTemplateController@update');
$router->post('/admin/templates/delete/{id}', 'AdminTemplateController@delete');

// ---------------- PAGAMENTOS ----------------
$router->get('/admin/payments', 'PaymentController@adminIndex');
$router->post('/admin/payments/confirm/{id}', 'PaymentController@confirmManual');

// =============================================================
// EXECUÇÃO
// =============================================================

$router->run();
