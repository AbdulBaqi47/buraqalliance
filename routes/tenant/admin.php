<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Tenant\{AddonsController, VisaController, LedgerController, ExpenseController ,EmployeeController,AjaxController, ChequeController, ClientController, ImportController, InstallmentController, InvoiceController, ReportController, SimController, TaskController, TransactionLedgerController, VehicleBillController};
use App\Http\Controllers\Tenant\Auth\LoginController;
use App\Http\Controllers\Tenant\Auth\RegisterController;
use App\Http\Controllers\Tenant\DriverController;
use App\Http\Controllers\Tenant\BookingController;
use App\Http\Controllers\Tenant\HomeController;
use App\Http\Controllers\Tenant\InvestorController;
use App\Http\Controllers\Tenant\TestController;
use App\Http\Controllers\Tenant\VehicleController;
use App\Http\Controllers\Tenant\StatementLedgerController;
use App\Http\Controllers\API\ApiController;
use App\Http\Middleware\EnsureApiTokenIsValid;
use App\Models\Tenant\Driver;
use App\Accounts\Controllers\AccountController;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
| Feel free to customize them however you want. Good luck!
|
*/

Route::get('/', function () {

    return redirect(route('tenant.admin.dashboard'));
});

#===============================================================
#           CLEAN ROUTES
#===============================================================

Route::group([
    'prefix' => 'admin',
    'namespace' => 'Tenant'
], function () {

    Route::get('test-route', [TestController::class, 'index']);


    // Authentication Routes...
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('tenant.admin.login');
    Route::post('login', [LoginController::class, 'login']);
    Route::post('logout', [LoginController::class, 'logout'])->name('tenant.admin.logout');

    // Registration Routes...
    Route::get('register', [RegisterController::class, 'showRegistrationForm'])->name('tenant.admin.register');
    Route::post('register', [RegisterController::class, 'register']);

    Route::middleware(['auth:tenant_auth'])->group(function () {

        #-------------------------------------------------------------
        #           AJAX ROUTES (used the datatables)
        #-------------------------------------------------------------
        Route::get('/getDrivers/{type}', [AjaxController::class, 'getDrivers'])->name('tenant.admin.drivers.data');
        Route::get('/getAddons', [AjaxController::class, 'getAddons'])->name('tenant.admin.addons.data');
        Route::get('/get/ajax/activity/log',[AjaxController::class, 'getActivityLog'])->name('tenant.admin.getActivityLog');

        Route::get('/getempployeeLedger/addon', [AjaxController::class, 'getEmployeeLedgerAddon'])->name('tenant.admin.employee.ledger.addon');
        Route::get('/getclients/{type}', [AjaxController::class, 'getClients'])->name('tenant.admin.clients.data');
        Route::get('/getsims', [AjaxController::class, 'getSims'])->name('tenant.admin.sims.data');
        Route::get('/getEntitysims', [AjaxController::class, 'getEntityBasedSims'])->name('tenant.admin.getEntitySims.data')->middleware(['check_role']);
        Route::get('/getAddonSettings', [AjaxController::class, 'getAddonSetting'])->name('tenant.admin.addons.setting.data');
        Route::get('/getVehicleBillsSettings', [AjaxController::class, 'getVehicleBillsSetting'])->name('tenant.admin.vehicle.bills.setting.data');
        Route::get('/getbooking', [AjaxController::class, 'getBookings'])->name('tenant.admin.bookings.data');
        Route::get('/getvehicles/{type}', [AjaxController::class, 'getVehicles'])->name('tenant.admin.vehicles.data');
        Route::get('/getinstallments', [AjaxController::class, 'getInstallments'])->name('tenant.admin.installments.data');
        Route::get('/getinvestors', [AjaxController::class, 'getInvestors'])->name('tenant.admin.investors.data');
        Route::get('/getledger/{filter?}', [AjaxController::class, 'getLedger'])->name('tenant.admin.ledger.data');
        Route::get('/getreports', [AjaxController::class, 'getReports'])->name('tenant.admin.reports.data');

        Route::get('/getStatementLedgerGroups', [AjaxController::class, 'getStatementLedgerGroups'])->name('tenant.admin.statementledger.groups.data');
        Route::get('/getemployees', [AjaxController::class, 'getEmployees'])->name('tenant.admin.employee.data');
        Route::get('/getempployeeLedger', [AjaxController::class, 'getEmployeeLedger'])->name('tenant.admin.employee.ledger.data');

        Route::get('/getdriverledger', [AjaxController::class, 'getDriverLedger'])->name('tenant.admin.drivers.statement.data');
        Route::get('/getvehicleledgeraddon/driver/{filter?}', [AjaxController::class, 'getVehicleLedgerDriverAddon'])->name('tenant.admin.vehicleledger.addon.driver')->middleware(['check_role']);


        Route::get('/getempployeeLedger/addon', [AjaxController::class, 'getEmployeeLedgerAddon'])->name('tenant.admin.employee.ledger.addon');
        Route::get('/getinternaltasks', [AjaxController::class, 'getInternalTasks'])->name('tenant.admin.tasks.internal.data');



        #-------------------------------------------------------------
        #  GLOBAL Routes (access restrictions not applied on these)
        #-------------------------------------------------------------
        Route::get('/', [HomeController::class, 'index'])->name('tenant.admin.dashboard');

        # WIll fetch settings like low inventory
        Route::POST('/app-config', [AjaxController::class, 'getAppConfig'])->name('tenant.admin.appconfig');

        // Profile
        Route::get('/profile', [HomeController::class, 'showProfileForm'])->name('tenant.admin.profile');
        Route::post('/profile', [HomeController::class, 'save_profile']);

        # Generate blob against file path
        Route::post('files/generate', [HomeController::class, 'getFile']);


        # Tasks Routes
        Route::prefix('tasks')->group(function () {


            # Tasks:Config routes
            Route::prefix('config')
            ->middleware(['check_role'])
            ->group(function () {
                Route::get('/', [TaskController::class, 'view_internal_tasks'])->name('tenant.admin.tasks.internal.view');

                Route::get('/add', [TaskController::class, 'show_internal_task_from'])->name('tenant.admin.tasks.internal.add');
                Route::post('/add', [TaskController::class, 'create_internal_task']);

                Route::post('/edit', [TaskController::class, 'edit_internal_task'])->name('tenant.admin.tasks.internal.edit');

            });

            # Tasks:frontend routes
            Route::prefix('frontend')->group(function () {
                Route::get('/settings', [TaskController::class, 'view_frontend_settings'])->name('tenant.admin.tasks.frontend.settings');
                Route::get('/', [TaskController::class, 'view_frontend'])->name('tenant.admin.tasks.frontend.view');

                # Internal Task - Actions
                Route::post('/{id}/update-status', [TaskController::class, 'update_internal_task_status'])->name('tenant.admin.tasks.internal.updatestatus');
            });
        });


        #-------------------------------------------------------------
        #       RESTRICTED Routes ( Accessed by granted users only)
        #-------------------------------------------------------------
        Route::middleware(['check_role'])->group(function () {

            # Vehicle routes
            Route::prefix('vehicles')->group(function () {
                Route::get('/vehicle', [VehicleController::class, 'ViewVehiclesVehicle'])->name('tenant.admin.vehicles.vehicle.view');
                Route::get('/bike', [VehicleController::class, 'ViewVehiclesBike'])->name('tenant.admin.vehicles.bike.view');
                Route::get('/add', [VehicleController::class, 'showVehicleForm'])->name('tenant.admin.vehicles.add');
                Route::POST('/add', [VehicleController::class, 'create']);
                Route::POST('/add/bulk', [VehicleController::class, 'addBulk'])->name('tenant.admin.vehicles.bulk.add');

                Route::GET('/{id}/edit', [VehicleController::class, 'showEditForm'])->name('tenant.admin.vehicles.single.edit');
                Route::POST('/edit', [VehicleController::class, 'edit'])->name('tenant.admin.vehicles.edit');

                Route::get('/{vehicle}/history', [BookingController::class, 'show_history'])->name('tenant.admin.bookings.closed.show_history');
                Route::POST('/{vehicle}/edit_history', [BookingController::class, 'edit_history'])->name('tenant.admin.bookings.closed.edit_history');
                Route::POST('/{vehicle}/delete_history', [BookingController::class, 'delete_history'])->name('tenant.admin.bookings.closed.delete_history');

                Route::get('/{vehicle}/changeBooking', [BookingController::class, 'changeTmpVehicleView'])->name('tenant.admin.bookings.closed.change_tmp_vehicle');
                Route::post('/{vehicle}/changeBooking', [BookingController::class, 'changeTmpVehicleAction']);

                # Vehicle Entities routes
                Route::prefix('/{id}/entities')->group(function () {
                    Route::get('/', [VehicleController::class, 'ViewVehicleEntities'])->name('tenant.admin.vehicles.entities.view');
                    Route::get('/add', [VehicleController::class, 'showVehicleEntitiesForm'])->name('tenant.admin.vehicles.entities.add');
                    Route::POST('/add', [VehicleController::class, 'create_entity']);

                    Route::GET('/edit-dates', [VehicleController::class, 'showEntitiesEditDatesForm'])->name('tenant.admin.vehicles.entities.edit.dates');
                    Route::GET('/edit-img', [VehicleController::class, 'showEntitiesEditImgForm'])->name('tenant.admin.vehicles.entities.edit.img');
                    Route::POST('/edit', [VehicleController::class, 'edit_entities'])->name('tenant.admin.vehicles.entities.edit');
                    Route::GET('/change_rental_company', [VehicleController::class, 'show_rental_company_entities'])->name('tenant.admin.vehicles.entities.show_rental_company');
                    Route::POST('/change_rental_company', [VehicleController::class, 'change_rental_company_entities']);

                    Route::DELETE('/delete', [VehicleController::class, 'delete_entities'])->name('tenant.admin.vehicles.entities.delete');

                });
            });

            # Addons routes
            Route::prefix('addons')->group(function () {
                Route::get('/{dept}/view', [AddonsController::class, 'view'])->name('tenant.admin.addons.view')->withoutMiddleware('check_role');
                Route::get('/add', [AddonsController::class, 'showAddonsForm'])->name('tenant.admin.addons.add')->middleware('onair_route:50%');
                Route::POST('/add', [AddonsController::class, 'create']);

                Route::GET('/{id}/edit', [AddonsController::class, 'showEditForm'])->name('tenant.admin.addons.single.edit')->middleware('onair_route');
                Route::POST('/{id}/edit', [AddonsController::class, 'edit']);

                Route::POST('/{id}/changeStatus', [AddonsController::class, 'changeStatusAction'])->name('tenant.admin.addons.changeStatus');
                Route::POST('/{id}/pay', [AddonsController::class, 'markAsPaidAction'])->name('tenant.admin.addons.mark_as_paid');

                Route::GET('{id}/charge', [AddonsController::class, 'showChargeForm'])->name('tenant.admin.addons.charge');
                Route::POST('{id}/charge', [AddonsController::class, 'create_charge']);

                Route::get('/expense/{type}', [AddonsController::class, 'showAddonExpenseForm'])->name('tenant.admin.addons.expense.add')->middleware('onair_route');
                Route::POST('/expense/{type}', [AddonsController::class, 'saveAddonExpense']);
                Route::POST('/expense/edit', [AddonsController::class, 'editAddonExpense'])->name('tenant.admin.addons.expense.edit')->middleware('onair_route');
                Route::get('/breakdown/{id}',[AddonsController::class, 'showBreakDownView'])->name('tenant.admin.addons.breakdown');
                // Breakdown Action Requests
                Route::POST('/breakdown/{id}/edit',[AddonsController::class, 'breakdown_edit_action'])->name('tenant.admin.addons.breakdown.edit');
                Route::POST('/breakdown/{id}/delete',[AddonsController::class, 'breakdown_delete_action'])->name('tenant.admin.addons.breakdown.delete');
                Route::POST('/breakdown/{id}/charge',[AddonsController::class, 'breakdown_charge_action'])->name('tenant.admin.addons.breakdown.charge');
                Route::POST('/breakdown/{id}/remove-charge',[AddonsController::class, 'breakdown_remove_charge_action'])->name('tenant.admin.addons.breakdown.remove-charge');


                Route::GET('/settings', [AddonsController::class, 'viewSettings'])->name('tenant.admin.addons.setting.view');
                Route::GET('/settings/create', [AddonsController::class, 'showSettingForm'])->name('tenant.admin.addons.setting.create');
                Route::POST('/settings/create', [AddonsController::class, 'create_setting']);
                Route::GET('/settings/{id}/edit', [AddonsController::class, 'showSettingEditForm'])->name('tenant.admin.addons.setting.single.edit');
                Route::POST('/settings/update', [AddonsController::class, 'edit_setting'])->name('tenant.admin.addons.setting.edit');

            });

            # Cleints routes
            Route::prefix('clients')->group(function () {
                Route::get('/aggregators', [ClientController::class, 'ViewClientsAggregator'])->name('tenant.admin.clients.aggregator.view');
                Route::get('/suppliers', [ClientController::class, 'ViewClientsSupplier'])->name('tenant.admin.clients.supplier.view');
                Route::get('/add', [ClientController::class, 'showClientForm'])->name('tenant.admin.clients.add');
                Route::POST('/add', [ClientController::class, 'create_client']);

                # Cleint Entities routes
                Route::prefix('/{id}/entities')->group(function () {
                    Route::get('/', [ClientController::class, 'ViewClientEntities'])->name('tenant.admin.clients.entities.view');
                    Route::get('/add', [ClientController::class, 'showClientEntitiesForm'])->name('tenant.admin.clients.entities.add');
                    Route::POST('/add', [ClientController::class, 'create_entity']);

                    # Different routes for edit, so access can be given to each of them
                    Route::GET('/edit-monthly_rent', [ClientController::class, 'showEntitiesEditMonthlyRentForm'])->name('tenant.admin.clients.entities.edit.monthly_rent');
                    Route::GET('/edit-refid', [ClientController::class, 'showEntitiesEditRefIDForm'])->name('tenant.admin.clients.entities.edit.refid');
                    Route::GET('/edit-dates', [ClientController::class, 'showEntitiesEditDatesForm'])->name('tenant.admin.clients.entities.edit.dates');
                    Route::POST('/edit', [ClientController::class, 'edit_entities'])->name('tenant.admin.clients.entities.edit');

                    Route::DELETE('/delete', [ClientController::class, 'delete_entities'])->name('tenant.admin.clients.entities.delete');

                });

                Route::GET('/edit', [ClientController::class, 'showClientEditForm'])->name('tenant.admin.clients.edit');
                Route::POST('/edit', [ClientController::class, 'edit_client']);
            });

            Route::prefix('vehicle-bills')->group(function () {

                Route::prefix('settings')->group(function () {
                    Route::GET('/', [VehicleBillController::class, 'viewSettings'])->name('tenant.admin.vehicle.bills.setting.view');
                    Route::GET('/create', [VehicleBillController::class, 'showSettingForm'])->name('tenant.admin.vehicle.bills.setting.create');
                    Route::POST('/create', [VehicleBillController::class, 'create_setting']);
                    Route::GET('/{id}/edit', [VehicleBillController::class, 'showSettingEditForm'])->name('tenant.admin.vehicle.bills.setting.single.edit');
                    Route::POST('/update', [VehicleBillController::class, 'edit_setting'])->name('tenant.admin.vehicle.bills.setting.edit');
                });

                Route::prefix('breakdown/{id}')->group(function () {
                    Route::GET('/', [VehicleBillController::class, 'showBreakDownView'])->name('tenant.admin.vehicle.bills.breakdown');
                });



            });

            Route::prefix('invoices')->group(function () {

                Route::GET('/', [InvoiceController::class, 'viewInvoices'])->name('tenant.admin.invoices.view');
                Route::GET('/data', [AjaxController::class, 'getInvoices'])->name('tenant.admin.invoices.data')->withoutMiddleware('check_role');

                Route::GET('/{client_id}/related-invoices', [InvoiceController::class, 'getRelatedInvoices'])->name('tenant.admin.invoices.related');

                Route::GET('/create', [InvoiceController::class, 'showInvoiceForm'])->name('tenant.admin.invoices.create')->middleware('onair_route:full');
                Route::POST('/create', [InvoiceController::class, 'create_invoice']);

                Route::GET('/{id}/edit', [InvoiceController::class, 'showEditInvoiceForm'])->name('tenant.admin.invoices.single.edit')->middleware('onair_route:full');
                Route::POST('/edit', [InvoiceController::class, 'edit_invoice'])->name('tenant.admin.invoices.edit');

                Route::DELETE('{id}/delete', [InvoiceController::class, 'delete_invoice'])->name('tenant.admin.invoices.delete');
            });

            # Transaction Ledger Routes
            Route::prefix('transaction-ledger')->group(function () {
                Route::get('/breakdown/{id}',[TransactionLedgerController::class, 'showBreakDownView'])->name('tenant.admin.tl.breakdown');

                Route::POST('/breakdown/{id}/edit-payable',[TransactionLedgerController::class, 'editBreakdownPayable'])->name('tenant.admin.tl.breakdown.edit_payable');
                Route::DELETE('/breakdown/{id}/delete-payable',[TransactionLedgerController::class, 'deleteBreakdownPayable'])->name('tenant.admin.tl.breakdown.delete_payable');
            });

            # statement ledger
            Route::prefix('statementledger')->group(function () {
                Route::get('{id}/driver', [StatementLedgerController::class, 'ViewDriverLedger'])->name('tenant.admin.statementledger.driver.view');
                Route::get('{id}/company', [StatementLedgerController::class, 'ViewCompanyLedger'])->name('tenant.admin.statementledger.company.view');

                Route::get('/{id}/data', [AjaxController::class, 'getStatementLedger'])->name('tenant.admin.statementledger.data')->withoutMiddleware('check_role');
                Route::get('/{id}/addons/data', [AjaxController::class, 'getStatementLedgerAddon'])->name('tenant.admin.statementledger.addon.data')->withoutMiddleware('check_role');

                Route::get('{id}/transfer_balance', [StatementLedgerController::class, 'transferBalance'])->name('tenant.admin.statementledger.transfer_balance');
                Route::post('{id}/transfer_balance', [StatementLedgerController::class, 'transferBalanceAction']);

                Route::get('{id}/transaction', [StatementLedgerController::class, 'showTransactionForm'])->name('tenant.admin.statementledger.transaction');
                Route::post('{id}/transaction', [StatementLedgerController::class, 'create_transaction']);

                Route::get('{id}/transaction/cash-pay', [StatementLedgerController::class, 'showTransactionForm'])
                ->name('tenant.admin.statementledger.transaction.cash_pay')
                ->defaults('type', 'cash_pay');
                Route::post('{id}/transaction/cash-pay', [StatementLedgerController::class, 'create_transaction_cashpay']);

                Route::get('{id}/transaction/cash-receive', [StatementLedgerController::class, 'showTransactionForm'])
                ->name('tenant.admin.statementledger.transaction.cash_receive')
                ->defaults('type', 'cash_receive');
                Route::post('{id}/transaction/cash-receive', [StatementLedgerController::class, 'create_transaction_cashreceive']);

                Route::get('{id}/linkedView', [StatementLedgerController::class, 'show_linked_view'])->name('tenant.admin.statementledger.linked.view');

                Route::get('{id}/edit', [StatementLedgerController::class, 'showEditForm'])->name('tenant.admin.statementledger.transaction.edit');
                Route::POST('{id}/edit', [StatementLedgerController::class, 'updateStatementLedger']);

                Route::get('{id}/details', [StatementLedgerController::class, 'show_details'])->name('tenant.admin.statementledger.transaction.viewDetails');

                Route::delete('{id}/delete', [StatementLedgerController::class, 'deleteVehicleLedger'])->name('tenant.admin.statementledger.transaction.delete');


                # Group Settings
                Route::GET('/groups', [StatementLedgerController::class, 'viewGroups'])->name('tenant.admin.statementledger.groups.view');
                Route::GET('/groups/create', [StatementLedgerController::class, 'showGroupForm'])->name('tenant.admin.statementledger.groups.create');
                Route::POST('/groups/create', [StatementLedgerController::class, 'create_group']);
                Route::GET('/groups/{id}/edit', [StatementLedgerController::class, 'showGroupEditForm'])->name('tenant.admin.statementledger.groups.single.edit');
                Route::POST('/groups/update', [StatementLedgerController::class, 'edit_group'])->name('tenant.admin.statementledger.groups.edit');

            });

            # Import Pages
            Route::prefix('imports')->group(function () {

                Route::get('statement-ledger', [ImportController::class, 'showImportStatementLedgerForm'])->name('tenant.admin.imports.statement_ledger');
                Route::post('statement-ledger', [ImportController::class, 'import_statement_ledger']);

                Route::get('vehicle-bills', [ImportController::class, 'showImportVehicleBillsForm'])->name('tenant.admin.imports.vehicle_bills');
                Route::post('vehicle-bills', [ImportController::class, 'import_vehicle_bills']);

                Route::get('transactions', [ImportController::class, 'showImportTransactionForm'])->name('tenant.admin.imports.transaction_ledgers');
                Route::post('transactions', [ImportController::class, 'import_transactions']);

                Route::get('installments', [ImportController::class, 'showImportInstallments'])->name('tenant.admin.imports.installments');
                Route::post('installments', [ImportController::class, 'import_installments']);

                Route::get('income', [ImportController::class, 'showImportIncomes'])->name('tenant.admin.imports.incomes');
                Route::post('income', [ImportController::class, 'import_incomes']);

                Route::get('sims', [ImportController::class, 'showImportSims'])->name('tenant.admin.imports.sims');
                Route::post('sims', [ImportController::class, 'import_sims']);

                Route::get('simbills', [ImportController::class, 'showImportSimbills'])->name('tenant.admin.imports.simbills');
                Route::post('simbills', [ImportController::class, 'import_simbills']);

                Route::get('histories', [AjaxController::class, 'getImportHistories'])->name('tenant.admin.imports.histories.data');
                Route::DELETE('histories/{id}/delete', [ImportController::class, 'delete_import_history'])->name('tenant.admin.imports.histories.delete');

            });

            # Cheque Routes
            Route::prefix('cheques')->group(function () {
                Route::get('/add', [ChequeController::class, 'showChequeForm'])->name('tenant.admin.cheques.add');
                Route::POST('/add', [ChequeController::class, 'create_cheque']);
            });

            Route::prefix('installment')->group(function () {
                Route::get('/{type}', [InstallmentController::class, 'view'])->name('tenant.admin.installment.view');
                Route::get('/{id}/edit', [InstallmentController::class, 'edit'])->name('tenant.admin.installment.edit');
                Route::post('/{id}/edit', [InstallmentController::class, 'editAction']);
                Route::get('/{id}/delete', [InstallmentController::class, 'delete'])->name('tenant.admin.installment.delete');
            });

            # Reporting
            Route::prefix('reports')->group(function () {

                Route::get('/', [ReportController::class, 'ViewReporting'])->name('tenant.admin.reports.view');

                Route::post('/generate', [ReportController::class, 'generate'])->name('tenant.admin.reports.generate');

            });

            # daily ledger
            Route::prefix('ledger')->group(function () {
                Route::get('/', [LedgerController::class, 'ViewLedger'])->name('tenant.admin.ledger.view');

                Route::get('{id}/edit', [LedgerController::class, 'showSingleEditForm'])->name('tenant.admin.ledger.single.edit')->middleware('onair_route');
                Route::get('/edit', [LedgerController::class, 'showEditForm'])->name('tenant.admin.ledger.edit');
                Route::POST('/edit', [LedgerController::class, 'edit']);

                Route::DELETE('/{id}/delete', [LedgerController::class, 'delete'])->name('tenant.admin.ledger.delete');
            });

            # Company expense
            Route::prefix('company-expense')->group(function () {
                Route::get('/add', [ExpenseController::class, 'showExpenseForm'])->name('tenant.admin.expense.add')->middleware('onair_route');
                Route::POST('/add', [ExpenseController::class, 'create']);
                Route::POST('/edit', [ExpenseController::class, 'edit'])->name('tenant.admin.expense.edit');
            });

            # Sim routes
            Route::prefix('sims')->group(function () {
                Route::get('/', [SimController::class, 'ViewSims'])->name('tenant.admin.sims.view');
                Route::get('/add', [SimController::class, 'showSimForm'])->name('tenant.admin.sims.add');
                Route::POST('/add', [SimController::class, 'create_sim']);

                # Sim Entities routes
                Route::prefix('/{id}/entities')->group(function () {
                    Route::get('/', [SimController::class, 'ViewSimEntities'])->name('tenant.admin.sims.entities.view');
                    Route::get('/add', [SimController::class, 'showSimEntitiesForm'])->name('tenant.admin.sims.entities.add');
                    Route::POST('/add', [SimController::class, 'create_entity']);

                    # Different routes for edit, so access can be given to each of them
                    Route::GET('/edit-allowed-balance', [SimController::class, 'showEntitiesEditAllowedBalanceForm'])->name('tenant.admin.sims.entities.edit.allowedbalance');
                    Route::GET('/edit-contract-date', [SimController::class, 'showEntitiesEditContractDateForm'])->name('tenant.admin.sims.entities.edit.contractdate');
                    Route::GET('/edit-dates', [SimController::class, 'showEntitiesEditDatesForm'])->name('tenant.admin.sims.entities.edit.dates');
                    Route::POST('/edit', [SimController::class, 'edit_entities'])->name('tenant.admin.sims.entities.edit');

                    Route::DELETE('/delete', [SimController::class, 'delete_entities'])->name('tenant.admin.sims.entities.delete');

                });

                Route::GET('{id}/edit', [SimController::class, 'showSimEditForm'])->name('tenant.admin.sims.edit');
                Route::POST('{id}/edit', [SimController::class, 'edit_sim']);
            });

            # Employee routes
            Route::prefix('employees')->group(function () {
                Route::get('/', [EmployeeController::class, 'ViewEmployees'])->name('tenant.admin.employee.view');
                Route::get('/add', [EmployeeController::class, 'showEmployeeForm'])->name('tenant.admin.employee.add')->middleware('onair_route');
                Route::POST('/add', [EmployeeController::class, 'create']);

                # Single part routes
                Route::GET('/{id}/edit', [EmployeeController::class, 'showEditForm'])->name('tenant.admin.employee.single.edit')->middleware('onair_route');
                Route::POST('/edit', [EmployeeController::class, 'edit'])->name('tenant.admin.employee.edit');


                # Route access
                Route::get('/routes/add', [EmployeeController::class, 'showRoutesForm'])->name('tenant.admin.employee.routes.add')->middleware('onair_route:80%');
                Route::POST('/routes/add', [EmployeeController::class, 'create_routes']);


                # Custom Route access
                Route::get('/custom_routes/{id}/add', [EmployeeController::class, 'showCustomRoutesForm'])->name('tenant.admin.employee.custom_routes.add');
                Route::POST('/custom_routes/{id}/add', [EmployeeController::class, 'create_custom_routes']);

                # Employee ledger
                Route::get('/ledger', [EmployeeController::class, 'ViewEmployeesLedger'])->name('tenant.admin.employee.ledger.view');

                #Employee Ledger Bills and Expenses
                #advance
                Route::get('/ledger/advance/add', [EmployeeController::class, 'showAdvanceForm'])->name('tenant.admin.employee.ledger.advance.add')->middleware('onair_route');
                Route::POST('/ledger/advance/add', [EmployeeController::class, 'createAdvance']);

                #bonus
                Route::get('/ledger/bonus/add', [EmployeeController::class, 'showBonusForm'])->name('tenant.admin.employee.ledger.bonus.add')->middleware('onair_route');
                Route::POST('/ledger/bonus/add', [EmployeeController::class, 'createBonus']);

                #fine
                Route::get('/ledger/fine/add', [EmployeeController::class, 'showFineForm'])->name('tenant.admin.employee.ledger.fine.add')->middleware('onair_route');
                Route::POST('/ledger/fine/add', [EmployeeController::class, 'createFine']);

                #salaries
                Route::get('/ledger/salary/generate', [EmployeeController::class, 'generateSalaryForm'])->name('tenant.admin.employee.ledger.salary.generate')->middleware('onair_route');
                Route::POST('/ledger/salary/generate', [EmployeeController::class, 'createSalary']);

                Route::POST('/ledger/salary/calculate', [EmployeeController::class, 'calculateSalary'])->name('tenant.admin.employee.ledger.salary.calculate');

                Route::get('/ledger/salary/pay', [EmployeeController::class, 'paySalaryForm'])->name('tenant.admin.employee.ledger.salary.pay')->middleware('onair_route');
                Route::POST('/ledger/salary/pay', [EmployeeController::class, 'paySalary']);
            });

            # Driver routes
            Route::prefix('drivers')->group(function () {
                Route::get('{id}/getDriverAddons', [AjaxController::class, 'getDriverAddons'])->name('tenant.admin.drivers.addons.data');

                Route::get('/driver', [DriverController::class, 'ViewDrivers'])->name('tenant.admin.drivers.driver.view');
                Route::get('/rider', [DriverController::class, 'ViewRiders'])->name('tenant.admin.drivers.riders.view');
                Route::get('/add', [DriverController::class, 'addDrivers'])->name('tenant.admin.drivers.add');
                Route::post('/add', [DriverController::class, 'storeDriver']);
                Route::get('/edit/{driver}', [DriverController::class, 'showEditDriver'])->name('tenant.admin.drivers.edit');
                Route::get('/delete/{driver}', [DriverController::class, 'delete'])->name('tenant.admin.drivers.delete');
                Route::post('/edit/{driver}', [DriverController::class, 'updateDriver']);
                Route::get('/detail/{driver}', [DriverController::class, 'viewDriverDetails'])->name('tenant.admin.drivers.viewDetails');
                Route::get('/assign/{driver}/investor', [DriverController::class, 'assignInvestorView'])->name('tenant.admin.drivers.assignInvestor');
                Route::post('/assign/{driver}/investor', [DriverController::class, 'assignInvestorAction']);
                Route::get('/assign/{driver}/vehicle', [DriverController::class, 'assignVehicleView'])->name('tenant.admin.drivers.assignVehicle');
                Route::post('/assign/{driver}/vehicle', [DriverController::class, 'assignVehicleAction']);
                Route::get('/{driver}/changeStatus', [DriverController::class, 'changeStatusView'])->name('tenant.admin.drivers.changeStatus')->middleware('onair_route');
                Route::post('/{driver}/changeStatus', [DriverController::class, 'changeStatusAction']);
                Route::get('/{driver}/changeBooking', [DriverController::class, 'changeBookingView'])->name('tenant.admin.drivers.changeBooking')->middleware('onair_route');
                Route::post('/{driver}/changeBooking', [DriverController::class, 'changeBookingAction']);
                Route::get('/{driver}/view_booking_history', [DriverController::class, 'viewBookingHistory'])->name('tenant.admin.drivers.viewBookingHistory');
                Route::get('/{driver}/view_account_statement', [DriverController::class, 'viewAccountStatement'])->name('tenant.admin.drivers.statement.view');
                Route::POST('/{id}/delete_booking_history_item', [DriverController::class, 'deleteBookingHistoryItem'])->name('tenant.admin.drivers.deleteBookingHistoryItem');
                Route::POST('/{id}/edit_booking_history_item', [DriverController::class, 'actionEditBookingHistory'])->name('tenant.admin.drivers.editBookingHistoryItem');
            });
            # Passport routes
            Route::prefix('passports')->group(function(){
                Route::get('/view', [DriverController::class, 'showDriverPassports'])->name('tenant.admin.drivers.passports.view');
                Route::get('/{id}/change', [DriverController::class, 'changeDriverPassportStatus'])->name('tenant.admin.drivers.passports.change_status')->middleware('onair_route');
                Route::post('/{id}/change', [DriverController::class, 'changeDriverPassportStatusAction']);
                Route::get('/{id}/dates', [DriverController::class, 'editPassportHistoryDates'])->name('tenant.admin.drivers.passports.history.edit_dates')->middleware('onair_route');
                Route::post('/{id}/dates', [DriverController::class, 'editPassportHistoryDatesAction']);
                Route::get('/{driver}/view_passport_history', [DriverController::class, 'viewPassportHistory'])->name('tenant.admin.drivers.passports.history.view');
            });


            Route::prefix('activity_log')->group(function(){
                Route::get('/', [HomeController::class, 'activity_view'])->name('tenant.admin.log_activity.view');
            });
        });

    });
});



#===============================================================
#           OLD ROUTES - WILL DELETE THEM LATER
#===============================================================

Route::group([
    'prefix' => 'oldadmin',
    'namespace' => 'Admin'
], function () {

    Route::get('/tr-update-ledger/{id}/{amount}', function ($id, $amount) {
        $amount = (float)$amount;
        $id = (int)$id;
        $ledger = \App\Models\Tenant\Ledger::with('relations')->findOrFail($id);

        $ledger_relations = $ledger->relations;

        foreach ($ledger_relations as $relation) {
            # Update source
            $source = $relation->source;
            $source->amount = $amount;
            $source->update();
        }
        # Update Ledger
        $ledger->amount = $amount;
        $ledger->update();

        return "done";
    });


    #-------------------------------------------------------------
    #           AJAX ROUTES (used the datatables)
    #-------------------------------------------------------------

    #-------------------------------------------------------------
    #  GLOBAL Routes (access restrictions not applied on these)
    #-------------------------------------------------------------

    #-------------------------------------------------------------
    #       RESTRICTED Routes ( Accessed by granted users only)
    #-------------------------------------------------------------
    Route::middleware(['check_role'])->group(function () {


    });
});


/*
|--------------------------------------------------------------------------
| Account Routes
|--------------------------------------------------------------------------
|
|
|
|
*/

use App\Http\Controllers\API\ScriptsController;

Route::group([
    'prefix' => 'accounts',
    'namespace' => '\App\Accounts\Controllers',
    'middleware' => ['auth:tenant_auth']
], function () {

    #-------------------------------------------------------------
    #       RESTRICTED Routes ( Accessed by granted users only)
    #-------------------------------------------------------------
    Route::middleware(['check_role'])->group(function () {
        Route::GET('/', [AccountController::class, 'ViewAccounts'])->name('module.accounts.view');
        Route::GET('{id}/transactions', [AccountController::class, 'ViewAccountTransactions'])->name('module.accounts.transactions.view');
        Route::GET('/transactions/payables', [AccountController::class, 'ViewPayablesAccountTransactions'])->name('accounts.transaction.pending');
        Route::GET('/transactions/receivable', [AccountController::class, 'ViewReceivableAccountTransactions'])->name('accounts.transaction.receivables');

        Route::GET('/pending/transaction/pay/{id}', [AccountController::class, 'showPayPendingAccountTransactionForm'])->name('accounts.transaction.pending.pay');
        Route::POST('/pending/transaction/pay/{id}', [AccountController::class, 'PayPendingAccountTransaction']);


        Route::GET('/transactions/pending/data', [AccountController::class, 'ViewPendingAccountTransactionsData'])->name('module.accounts.transactions.pending.data');
        Route::GET('/transactions/pending/{id}/edit', [AccountController::class, 'showEditPendingAccountTransactions'])->name('module.accounts.transactions.pending.edit');
        Route::POST('/transactions/pending/{id}/edit', [AccountController::class, 'editPendingAccountTransactionsAction']);

        #Add Transaction
        Route::get('{id}/transactions/add', [AccountController::class, 'showTransactionForm'])->name('module.accounts.transactions.add');
        Route::POST('{id}/transactions/add', [AccountController::class, 'createTransaction']);

        Route::get('{id}/transactions/edit', [AccountController::class, 'showTransactionEditForm'])->name('module.accounts.transactions.edit')->middleware('onair_route');
        Route::POST('{id}/transactions/edit', [AccountController::class, 'editTransaction']);

        Route::DELETE('/{id}/transactions/delete', [AccountController::class, 'deleteTransaction'])->name('module.accounts.transactions.delete');

        Route::GET('/add', [AccountController::class, 'showAccountsForm'])->name('module.accounts.add')->middleware('onair_route');
        Route::POST('/add', [AccountController::class, 'create']);

        Route::GET('/transfer/add', [AccountController::class, 'showTransferForm'])->name('module.accounts.transfer.add')->middleware('onair_route');
        Route::POST('/transfer/add', [AccountController::class, 'create_transfer']);
    });

    #-------------------------------------------------------------
    #  GLOBAL Routes (access restrictions not applied on these)
    #-------------------------------------------------------------

    Route::GET('{id}/gettransactions', [AccountController::class, 'getTransactions'])->name('module.accounts.transactions.data');

    # used to fetch accounts (with balance) to update selector
    Route::GET('/fetch-accounts', [AccountController::class, 'FetchAccounts'])->name('module.accounts.fetchaccounts');
});




/*
|
|
|
|
|--------------------------------------------------------------------------
| Account Routes
|--------------------------------------------------------------------------
*/


Route::group([
    'prefix' => '_core',
    'namespace' => 'API',
    'middleware' => [
        EnsureApiTokenIsValid::class,
    ]
], function () {
    Route::POST('/add-transaction', [ApiController::class, 'addTransaction']);
    Route::DELETE('/delete-transaction', [ApiController::class, 'deleteTransaction']);

    # investor routes
    Route::prefix('scripts')->group(function () {

        Route::get('/db-tables-list', [ScriptsController::class, 'getDatabaseTableList']);
        Route::get('/db-tables-data', [ScriptsController::class, 'getDatabaseTableData']);

    });

});
