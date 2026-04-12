<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$route['default_controller'] = "users1/login";

$route['404_override'] = '';

$route['translate_uri_dashes'] = FALSE;
 

$route['users1/save_attendance_summary/(:num)']['post'] = 'users1/save_attendance_summary/$1';
$route['users1/bulk_exempt_attendance_summary/(:any)']['post'] = 'users1/bulk_exempt_attendance_summary/$1';
$route['Users1/update_status'] = 'Users1/update_status';
$route['Users1/update_due_date'] = 'Users1/update_due_date';
$route['Users1/update_assignee'] = 'Users1/update_assignee';

$route['users2/submit_correction_request'] = 'users2/submit_correction_request';

$route['users2/get_user_correction_requests'] = 'users2/get_user_correction_requests';
$route['users2/cancel_correction_request'] = 'users2/cancel_correction_request';
$route['users2/print_salary_certificate/(:num)'] = 'users2/print_salary_certificate/$1';

 $route['users1/update_order_status'] = 'users1/update_order_status';
// أو مختصر:
$route['orders/update-status'] = 'users1/update_order_status';

$route['ProjectCostReport'] = 'ProjectCostReport/index';
$route['ProjectCostReport/project/(:num)'] = 'ProjectCostReport/project/$1';
$route['ProjectCostReport/export_summary_csv'] = 'ProjectCostReport/export_summary_csv';
$route['ProjectCostReport/export_project_csv/(:num)'] = 'ProjectCostReport/export_project_csv/$1';


$route['AnnualIncentives'] = 'AnnualIncentives/index';
$route['AnnualIncentives/create'] = 'AnnualIncentives/create';
$route['AnnualIncentives/store_setup'] = 'AnnualIncentives/store_setup';
$route['AnnualIncentives/employees/(:num)'] = 'AnnualIncentives/employees/$1';
$route['AnnualIncentives/search_emp1'] = 'AnnualIncentives/search_emp1';
$route['AnnualIncentives/add_employee'] = 'AnnualIncentives/add_employee';
$route['AnnualIncentives/remove_employee'] = 'AnnualIncentives/remove_employee';
$route['AnnualIncentives/calc/(:num)'] = 'AnnualIncentives/calc/$1';
$route['AnnualIncentives/update_multiplier'] = 'AnnualIncentives/update_multiplier';
$route['AnnualIncentives/save_batch'] = 'AnnualIncentives/save_batch';
$route['AnnualIncentives/view/(:num)'] = 'AnnualIncentives/view/$1';
$route['AnnualIncentives/export_excel/(:num)'] = 'AnnualIncentives/export_excel/$1';
$route['AnnualIncentives/print/(:num)']        = 'AnnualIncentives/print_view/$1';
$route['AnnualIncentives/edit_settings/(:num)'] = 'AnnualIncentives/edit_settings/$1';
$route['AnnualIncentives/update_settings']      = 'AnnualIncentives/update_settings';
$route['AnnualIncentives/reset_calculations'] = 'AnnualIncentives/reset_calculations';
$route['OrgStructureEditor'] = 'OrgStructureEditor/index';


$route['OrgStructureEditor/print'] = 'OrgStructureEditor/print_view';
$route['OrgStructureEditor/excel'] = 'OrgStructureEditor/export_excel';

$route['AnnualEvaluation'] = 'AnnualEvaluation/index';
$route['AnnualEvaluation/save'] = 'AnnualEvaluation/save';

$route['AnnualEvaluationSupervisor'] = 'AnnualEvaluationSupervisor/index';
$route['AnnualEvaluationSupervisor/form/(:any)'] = 'AnnualEvaluationSupervisor/form/$1';
$route['AnnualEvaluationSupervisor/save'] = 'AnnualEvaluationSupervisor/save';

$route['AnnualEvaluationAdmin'] = 'AnnualEvaluationAdmin/index';
$route['AnnualEvaluationAdmin/detail/(:any)'] = 'AnnualEvaluationAdmin/detail/$1';


$route['AnnualEvaluationImports'] = 'AnnualEvaluationImports/index';
$route['AnnualEvaluationImports/upload_master'] = 'AnnualEvaluationImports/upload_master';
$route['AnnualEvaluationImports/upload_discipline'] = 'AnnualEvaluationImports/upload_discipline';
$route['AnnualEvaluationImports/upload_courses'] = 'AnnualEvaluationImports/upload_courses';

$route['AnnualEvaluation/print/(:any)'] = 'AnnualEvaluation/print_a4/$1';





















































 

