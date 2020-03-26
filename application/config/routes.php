<?php
defined('BASEPATH') OR exit('No direct script access allowed');


$route['default_controller'] = 'servicio_controllers';
$route['estadistica_ruta'] = 'Servicio_controllers/estadistica_ruta';
$route['estadistica_articulos_ruta'] = 'Servicio_controllers/estadistica_articulos_ruta';
$route['Login/(:any)/(:any)/(:any)/(:any)'] = 'Servicio_controllers/Login/$1/$2/$3/$4';

//$route de mora
$route['mora_por_ruta']     = 'Servicio_controllers/mora_por_ruta';
$route['mora_por_cliente']  = 'Servicio_controllers/mora_por_cliente';
$route['facturas_vencidas']  = 'Servicio_controllers/facturas_vencidas';




$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;