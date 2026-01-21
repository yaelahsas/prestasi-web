<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/userguide3/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
$route['default_controller'] = 'auth';
$route['dashboard'] = 'dashboard';
$route['dashboard/get_stats'] = 'dashboard/get_stats';
$route['guru'] = 'guru';
$route['guru/get_data'] = 'guru/get_guru_data';
$route['guru/get_by_id/(:num)'] = 'guru/get_guru_by_id/$1';
$route['guru/get_kelas'] = 'guru/get_kelas';
$route['guru/get_mapel'] = 'guru/get_mapel';
$route['guru/save'] = 'guru/save_guru';
$route['guru/delete/(:num)'] = 'guru/delete_guru/$1';
$route['guru/toggle_status/(:num)'] = 'guru/toggle_status/$1';
$route['guru/search'] = 'guru/search';
$route['kelas'] = 'kelas';
$route['kelas/get_data'] = 'kelas/get_kelas_data';
$route['kelas/get_by_id/(:num)'] = 'kelas/get_kelas_by_id/$1';
$route['kelas/save'] = 'kelas/save_kelas';
$route['kelas/delete/(:num)'] = 'kelas/delete_kelas/$1';
$route['kelas/toggle_status/(:num)'] = 'kelas/toggle_status/$1';
$route['kelas/search'] = 'kelas/search';
$route['mapel'] = 'mapel';
$route['mapel/get_data'] = 'mapel/get_mapel_data';
$route['mapel/get_by_id/(:num)'] = 'mapel/get_mapel_by_id/$1';
$route['mapel/save'] = 'mapel/save_mapel';
$route['mapel/delete/(:num)'] = 'mapel/delete_mapel/$1';
$route['mapel/toggle_status/(:num)'] = 'mapel/toggle_status/$1';
$route['mapel/search'] = 'mapel/search';
$route['users'] = 'users';
$route['users/get_data'] = 'users/get_users_data';
$route['users/get_by_id/(:num)'] = 'users/get_user_by_id/$1';
$route['users/save'] = 'users/save_user';
$route['users/delete/(:num)'] = 'users/delete_user/$1';
$route['users/toggle_status/(:num)'] = 'users/toggle_status/$1';
$route['users/search'] = 'users/search';
$route['users/get_total_users_aktif'] = 'users/get_total_users_aktif';
$route['users/get_total_users_nonaktif'] = 'users/get_total_users_nonaktif';
$route['sekolah'] = 'sekolah';
$route['sekolah/get_data'] = 'sekolah/get_sekolah_data';
$route['sekolah/get_by_id/(:num)'] = 'sekolah/get_sekolah_by_id/$1';
$route['sekolah/save'] = 'sekolah/save_sekolah';
$route['sekolah/delete/(:num)'] = 'sekolah/delete_sekolah/$1';
$route['sekolah/search'] = 'sekolah/search';
$route['sekolah/get_total_sekolah'] = 'sekolah/get_total_sekolah';
$route['jurnal'] = 'jurnal';
$route['jurnal/get_data'] = 'jurnal/get_jurnal_data';
$route['jurnal/get_by_id/(:num)'] = 'jurnal/get_jurnal_by_id/$1';
$route['jurnal/get_guru'] = 'jurnal/get_guru';
$route['jurnal/get_kelas'] = 'jurnal/get_kelas';
$route['jurnal/get_mapel'] = 'jurnal/get_mapel';
$route['jurnal/save'] = 'jurnal/save_jurnal';
$route['jurnal/delete/(:num)'] = 'jurnal/delete_jurnal/$1';
$route['jurnal/search'] = 'jurnal/search';
$route['jurnal/filter_by_tanggal'] = 'jurnal/filter_by_tanggal';

// API routes for WhatsApp bot integration
$route['api/auth'] = 'api/auth';
$route['api/jurnal/create'] = 'api/create_jurnal';
$route['api/jurnal/list'] = 'api/get_all_jurnal';
$route['api/jurnal/view/(:num)'] = 'api/get_jurnal/$1';
$route['api/jurnal/search'] = 'api/search_jurnal';
$route['api/guru/list'] = 'api/get_guru';
$route['api/kelas/list'] = 'api/get_kelas';
$route['api/mapel/list'] = 'api/get_mapel';

// Legacy sekolah routes
$route['sekolah'] = 'sekolah';
$route['sekolah/tambah'] = 'sekolah/tambah_sekolah';
$route['sekolah/edit/(:num)'] = 'sekolah/edit_sekolah/$1';
$route['sekolah/hapus/(:num)'] = 'sekolah/hapus_sekolah/$1';
$route['sekolah/api/get_sekolah'] = 'sekolah/api_get_sekolah';
$route['laporan'] = 'laporan';
$route['laporan/cetak_jurnal_bulanan'] = 'laporan/cetak_jurnal_bulanan';
$route['laporan/cetak_laporan_guru'] = 'laporan/cetak_laporan_guru';
$route['laporan/cetak_laporan_kelas'] = 'laporan/cetak_laporan_kelas';
$route['laporan/cetak_laporan_mapel'] = 'laporan/cetak_laporan_mapel';
$route['laporan/cetak_rekap_kehadiran'] = 'laporan/cetak_rekap_kehadiran';
// API routes for statistics
$route['api/statistik_jurnal'] = 'laporan/get_statistik_jurnal';
$route['api/total_guru_aktif'] = 'guru/get_total_guru_aktif';
$route['api/total_kelas_aktif'] = 'kelas/get_total_kelas_aktif';
$route['api/total_mapel_aktif'] = 'mapel/get_total_mapel_aktif';
$route['analytics'] = 'analytics';
$route['analytics/api/dashboard'] = 'analytics/get_dashboard_data';
$route['analytics/api/revenue'] = 'analytics/get_revenue_data';
$route['analytics/api/transactions'] = 'analytics/get_transactions';
$route['analytics/api/kpi'] = 'analytics/get_kpi_data';
$route['analytics/export'] = 'analytics/export_data';
$route['analytics/report'] = 'analytics/generate_report';
$route['analytics/realtime'] = 'analytics/get_realtime_updates';
$route['analytics/insights'] = 'analytics/get_insights';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;
