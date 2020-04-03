<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Servicio_controllers extends CI_Controller {

	public function index()
	{
		//$this->load->view('welcome_message');
	}

	public function estadistica_ruta()
    {

        $Empresa            = ($_POST['Empresa']=="UNIMARK") ? 1 : 2;
        $Ruta               = $_POST['Ruta'];
        $Mes                = $_POST['Mes'];
        $anno               = $_POST['Annio'];

        /*$Empresa            = 1;
        $Ruta               = "F06";
        $Mes                = "4";
        $anno               = "2020";*/




        $this->servicios_model->estadistica_ruta($Empresa,$Ruta,$Mes,$anno);
    }
    public function estadistica_articulos_ruta()
    {
        $Empresa            = ($_POST['Empresa']=="UNIMARK") ? 1 : 2;
        $Ruta               = $_POST['Ruta'];
        $Mes                = $_POST['Mes'];
        $anno               = $_POST['Annio'];
        $Filtro             = $_POST['Filtro'];
        $Grafica            = $_POST['Grafica'];

        /*$Empresa            = 1;
        $Ruta               = "F06";
        $Mes                = "3";
        $anno               = "2020";
        $Filtro             = "";
        $Grafica            = "";*/


        $this->servicios_model->estadistica_articulos_ruta($Empresa,$Ruta,$Mes,$anno,$Filtro,$Grafica);
    }

    public function Login($Usurio,$Contra,$empresa,$device){
        $this->servicios_model->Login($Usurio,$Contra,$empresa,$device);
    }

    public function mora_por_ruta(){

        $Empresa            = ($_POST['Empresa']=="UNIMARK") ? 1 : 2;
        $Ruta               = $_POST['Ruta'];

        /*$Empresa            = 1;
        $Ruta               = "F06";*/


        $this->servicios_model->mora_por_ruta($Empresa,$Ruta);

    }
    public function mora_por_cliente(){

        $Empresa            = ($_POST['Empresa']=="UNIMARK") ? 1 : 2;
        $Ruta               = $_POST['Ruta'];

        /*$Empresa            = 1;
        $Ruta               = "F06";*/


        $this->servicios_model->mora_por_cliente($Empresa,$Ruta);

    }
    public function facturas_vencidas(){

        $DiasMora               = intval($_POST['DiasMora']);
        $Ruta                   = $_POST['Ruta'];


        /*$DiasMora           = intval("130");
        $Ruta                   = "F06";*/
        $this->servicios_model->facturas_vencidas($DiasMora,$Ruta);
    }



}
