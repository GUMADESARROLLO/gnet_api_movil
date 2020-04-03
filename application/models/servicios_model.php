<?php
class servicios_model extends CI_Model
{
    public function __construct(){
        parent::__construct();
        $this->load->database();
    }

    private function view($Empresa){
      return ($Empresa == 1) ?  "VtasTotal_UMK" : "GP_VtasTotal_UMK";
    }

    public function mora_por_ruta($Empresa,$Ruta){

        $qMoraPorRuta="SELECT
                            SUM(T0.NoVencidos) AS NoVencidos,
                            SUM(T0.Dias30) AS Dias30,
                            SUM(T0.Dias60) AS Dias60,
                            SUM(T0.Dias90) AS Dias90,
                            SUM(T0.Dias120) AS Dias120,
                            SUM(T0.Mas120) AS Mas120 
                        FROM
                            GMV_ClientesPerMora T0 
                        WHERE
                            T0.VENDEDOR = '".$Ruta."'";

        $rMoraPorRuta = $this->sqlsrv->fetchArray($qMoraPorRuta);

        $array = array();
        $i=0;
        foreach($rMoraPorRuta as $key){
            $Total = $key['Dias30']+$key['Dias60']+$key['Dias90']+$key['Dias120']+$key['Mas120'];
            $array[$i]['NoVencidos']    = "C$ ".number_format($key['NoVencidos'],2);
            $array[$i]['Dias30']        = "C$ ".number_format($key['Dias30'],2);
            $array[$i]['Dias60']        = "C$ ".number_format($key['Dias60'],2);
            $array[$i]['Dias90']        = "C$ ".number_format($key['Dias90'],2);
            $array[$i]['Dias120']       = "C$ ".number_format($key['Dias120'],2);
            $array[$i]['Mas120']        = "C$ ".number_format($key['Mas120'],2);
            $array[$i]['mTotal']        = "C$ ".number_format($Total,2);
            $i++;
        }
        echo json_encode($array);
        $this->sqlsrv->close();

    }
    public function mora_por_cliente($Empresa,$Ruta){

        $qMoraPorRuta="SELECT
                            T0.NOMBRE,
                            T0.NoVencidos,
                            T0.Dias30,
                            T0.Dias60,
                            T0.Dias90,
                            T0.Dias120,
                            T0.Mas120 
                        FROM
                            GMV_ClientesPerMora T0 
                        WHERE
                            T0.VENDEDOR = '".$Ruta."'";

        $rMoraPorRuta = $this->sqlsrv->fetchArray($qMoraPorRuta);

        $array = array();
        $i=0;
        foreach($rMoraPorRuta as $key){


            $array[$i]['Nombre']        = $key['NOMBRE'];
            $array[$i]['NoVencidos']    = "C$ ".number_format($key['NoVencidos'],2);
            $array[$i]['Dias30']        = "C$ ".number_format($key['Dias30'],2);
            $array[$i]['Dias60']        = "C$ ".number_format($key['Dias60'],2);
            $array[$i]['Dias90']        = "C$ ".number_format($key['Dias90'],2);
            $array[$i]['Dias120']       = "C$ ".number_format($key['Dias120'],2);
            $array[$i]['Mas120']        = "C$ ".number_format($key['Mas120'],2);
            $i++;
        }
        echo json_encode($array);
        $this->sqlsrv->close();

    }
    public function facturas_vencidas($DiasMora,$Ruta){

        $qMoraPorCliente="SELECT
                            T0.CLIENTE,
                            T0.VENDEDOR,
                            T0.NOMBRE,
                            T0.DOCUMENTO,
                            T0.TIPO,
                            T0.FECHA,
                            T0.FECHA_VENCE,
                            T0.DVencidos,
                            T0.SALDO_LOCAL 
                        FROM
                            Softland.dbo.APK_CxC_DocVenxCL T0 WHERE T0.VENDEDOR= '".$Ruta."'";

        if($DiasMora <= 0){
            $qMoraPorCliente .= " AND (T0.TIPO <> 'N/C') AND (T0.DVencidos < 0)";
        }elseif($DiasMora == 30){
            $qMoraPorCliente .= " AND (T0.TIPO <> 'N/C') AND (T0.DVencidos BETWEEN 1 AND 30)";
        }elseif($DiasMora == 60){
            $qMoraPorCliente .= " AND (T0.TIPO <> 'N/C') AND (T0.DVencidos BETWEEN 31 AND 60)";
        }elseif($DiasMora == 90){
            $qMoraPorCliente .= " AND (T0.TIPO <> 'N/C') AND (T0.DVencidos BETWEEN 61 AND 90)";
        }elseif($DiasMora == 120){
            $qMoraPorCliente .= " AND (T0.TIPO <> 'N/C') AND (T0.DVencidos BETWEEN 91 AND 120)";
        }elseif($DiasMora > 120){
            $qMoraPorCliente .= " AND (T0.TIPO <> 'N/C') AND (T0.DVencidos >= ".$DiasMora.")";
        }


        $rMoraPorCliente = $this->sqlsrv->fetchArray($qMoraPorCliente);

        $array = array();
        $i=0;
        foreach($rMoraPorCliente as $key){
            $array[$i]['CLIENTE']          = $key['CLIENTE'];
            $array[$i]['NOMBRE']           = $key['NOMBRE'];
            $array[$i]['DOCUMENTO']        = $key['DOCUMENTO'];
            $array[$i]['FECHA']            = $key['FECHA']->format('d-m-Y');
            $array[$i]['FECHA_VENCE']      = $key['FECHA_VENCE']->format('d-m-Y');
            $array[$i]['DVencidos']        = number_format($key['DVencidos'],0,'.','');
            $array[$i]['SALDO_LOCAL']      = number_format($key['SALDO_LOCAL'],2,'.','');
            $i++;
        }
        echo json_encode($array);
        $this->sqlsrv->close();

    }


    public function estadistica_ruta($Empresa,$Ruta,$Mes,$anno)
    {
        $qVENTA_REAL = $this->sqlsrv->fetchArray("SELECT
                                                      ISNULL(SUM(t0.VENTA), 0) as VENTA_REAL,
                                                      ISNULL(SUM(t0.CANTIDAD), 0) as VENTA_REAL_CANTIDAD
                                                    FROM
                                                        Softland.DBO.".$this->view($Empresa)." t0 
                                                    WHERE
                                                        t0.nMes= '".$Mes."' 
                                                        AND t0.[año] = '".$anno."' 
                                                        AND t0.Ruta= '".$Ruta."' AND t0.Venta> 0",SQLSRV_FETCH_ASSOC);

        $qVENTA_META = $this->sqlsrv->fetchArray("SELECT
                                                        ISNULL(SUM(t0.val), 0) as VENTA_META,
                                                        ISNULL(SUM(t0.Meta), 0) as META_items
                                                    FROM
                                                        [DESARROLLO].[dbo].[gn_cuota_x_productos] t0 
                                                    WHERE
                                                        IdPeriodo = ( SELECT IdPeriodo FROM [DESARROLLO].[dbo].[gn_periodos] t1 WHERE t1.nMes= '".$Mes."' AND t1.Anno= '".$anno."' AND t1.IdCompany = '".$Empresa."' )  AND t0.CodVendedor='".$Ruta."'",SQLSRV_FETCH_ASSOC);


        $qComportamiento = $this->sqlsrv->fetchArray("SELECT
                                                        (t0.nMes - 1) as Posicion,
                                                        SUBSTRING('ENE FEB MAR ABR MAY JUN JUL AGO SEP OCT NOV DIC ', (t0.nMes * 4) - 3, 3) AS Mes,
                                                        ISNULL( SUM ( t0.VENTA ), 0 ) AS VENTA_REAL
                                                        FROM
                                                            Softland.DBO.VtasTotal_UMK t0 
                                                        WHERE	
                                                            t0.[año] = YEAR(GETDATE())
                                                            AND t0.Ruta= '".$Ruta."'
                                                            AND t0.Venta > 0
                                                            GROUP BY t0.nMes");



        $array = array();
        $i=0;




        // META DE VENTA EN MONTO
        $Meta_monto = $qVENTA_META[0]['VENTA_META'];
        $Real_monto = $qVENTA_REAL[0]['VENTA_REAL'];



        $array['data_venta_monto'][$i]['mMeta']         = number_format($Meta_monto,2);
        $array['data_venta_monto'][$i]['mRetal']        = number_format($Real_monto,2);
        $array['data_venta_monto'][$i]['mCumpliento']   = (number_format($Meta_monto,0)==0) ? 0 : number_format((100 * $Real_monto) / $Meta_monto,2);

        // META DE VENTA EN CANTIDAD
        $Meta_cantidad = $qVENTA_META[0]['META_items'];
        $Real_cantidad = $qVENTA_REAL[0]['VENTA_REAL_CANTIDAD'];

        $array['data_venta_cantidad'][$i]['mMeta']         = number_format($Meta_cantidad,2);
        $array['data_venta_cantidad'][$i]['mRetal']        = number_format($Real_cantidad,0);
        $array['data_venta_cantidad'][$i]['mCumpliento']   = (number_format($Meta_cantidad,0)==0) ? 0 : number_format((100 * $Real_cantidad) / $Meta_cantidad,2);

        foreach($qComportamiento as $key){

            $Real_monto        = $key['VENTA_REAL'];
            $Meta_monto        = floatval($this->getPerMesRuta($Empresa,$Ruta,$key['Posicion'] +1));

            $array['data_comportamiento'][$i]['Posicion']    = number_format($key['Posicion'],0);
            $array['data_comportamiento'][$i]['Mes']         = $key['Mes'];
            $array['data_comportamiento'][$i]['mCumpliento']   = number_format((100 * $Real_monto) / $Meta_monto,2);

            $i++;
        }
        echo json_encode($array);
        $this->sqlsrv->close();
    }
    function getPerMesRuta($Empresa,$Ruta,$Mes){
        $qVENTA_META = $this->sqlsrv->fetchArray("SELECT
                                                        ISNULL(SUM(t0.val), 0) as VENTA_META
                                                    FROM
                                                        [DESARROLLO].[dbo].[gn_cuota_x_productos] t0 
                                                    WHERE
                                                        IdPeriodo = ( SELECT IdPeriodo FROM [DESARROLLO].[dbo].[gn_periodos] t1 WHERE t1.nMes= '".$Mes."' AND t1.Anno= YEAR(GETDATE())  AND t1.IdCompany = '".$Empresa."' )  AND t0.CodVendedor='".$Ruta."'",SQLSRV_FETCH_ASSOC);
        return $qVENTA_META[0]['VENTA_META'];
        $this->sqlsrv->close();

    }
    public function estadistica_articulos_ruta($Empresa,$Ruta,$Mes,$anno,$Filtro,$Grafica)
    {
        $i=0;
        $inArticulos ="";
        $rtnArticulo=array();
          $qVENTA_META = $this->sqlsrv->fetchArray("SELECT
                                                        CodProducto,NombreProducto,sum(val) as val, sum(Meta) as Meta
                                                    FROM
                                                        [DESARROLLO].[dbo].[gn_cuota_x_productos] t0 
                                                    WHERE
                                                        IdPeriodo = ( SELECT IdPeriodo FROM [DESARROLLO].[dbo].[gn_periodos] t1 WHERE t1.nMes= '".$Mes."' AND t1.Anno= '".$anno."' AND t1.IdCompany = '".$Empresa."' )  AND t0.CodVendedor='".$Ruta."' GROUP BY CodProducto,NombreProducto",SQLSRV_FETCH_ASSOC);





        foreach($qVENTA_META as $key){

            $Meta_Monto = $key['val'];
            $Real_Monto = number_format($this->getSaleProductDetall($Empresa,$Ruta,$Mes,$anno, $key['CodProducto']),2,'.','');

            $Meta_cantidad = $key['Meta'];
            $Real_cantidad = number_format($this->getSaleProductCantidad($Empresa,$Ruta,$Mes,$anno, $key['CodProducto']),0,'.','');

            $inArticulos .= "'". $key['CodProducto']."',";

            //echo $Grafica. " - > ".$Filtro. " - > " .$Real_Monto.'<br>';
            if($Grafica == "Monto"){
                if($Filtro=="REAL"){
                    if($Real_Monto!="0.00"){
                        $rtnArticulo[$i]['mCodigo']      = $key['CodProducto'];
                        $rtnArticulo[$i]['mName']        = strtoupper($key['NombreProducto']);
                        $rtnArticulo[$i]['mMeta_monto']  = number_format($Meta_Monto,0,'.','');
                        $rtnArticulo[$i]['mReal_monto']  = $Real_Monto;
                        $rtnArticulo[$i]['mcump_monto']  = ($Meta_Monto=="0") ? "0" : number_format((100 * $Real_Monto) / $Meta_Monto,2);
                        $rtnArticulo[$i]['mMeta_canti']  = number_format($Meta_cantidad,0,'.','');
                        $rtnArticulo[$i]['mReal_canti']  = $Real_cantidad;
                        $rtnArticulo[$i]['mcump_canti']  = ($Meta_cantidad=="0") ? "0" : number_format((100 * $Real_cantidad) / $Meta_cantidad,2);
                        $i++;
                    }
                }elseif($Real_Monto=="0.00"){
                    $rtnArticulo[$i]['mCodigo']      = $key['CodProducto'];
                    $rtnArticulo[$i]['mName']        = strtoupper($key['NombreProducto']);
                    $rtnArticulo[$i]['mMeta_monto']  = number_format($Meta_Monto,0,'.','');
                    $rtnArticulo[$i]['mReal_monto']  = $Real_Monto;
                    $rtnArticulo[$i]['mcump_monto']  = ($Meta_Monto=="0") ? "0" : number_format((100 * $Real_Monto) / $Meta_Monto,2);
                    $rtnArticulo[$i]['mMeta_canti']  = number_format($Meta_cantidad,0,'.','');
                    $rtnArticulo[$i]['mReal_canti']  = $Real_cantidad;
                    $rtnArticulo[$i]['mcump_canti']  = ($Meta_cantidad=="0") ? "0" : number_format((100 * $Real_cantidad) / $Meta_cantidad,2);
                    $i++;
                }
            }elseif($Grafica == "Items"){
                if($Filtro=="REAL"){
                    if($Real_cantidad!="0.00"){
                        $rtnArticulo[$i]['mCodigo']      = $key['CodProducto'];
                        $rtnArticulo[$i]['mName']        = strtoupper($key['NombreProducto']);
                        $rtnArticulo[$i]['mMeta_monto']  = number_format($Meta_Monto,0,'.','');
                        $rtnArticulo[$i]['mReal_monto']  = $Real_Monto;
                        $rtnArticulo[$i]['mcump_monto']  = ($Meta_Monto=="0") ? "0" : number_format((100 * $Real_Monto) / $Meta_Monto,2);
                        $rtnArticulo[$i]['mMeta_canti']  = number_format($Meta_cantidad,0,'.','');
                        $rtnArticulo[$i]['mReal_canti']  = $Real_cantidad;
                        $rtnArticulo[$i]['mcump_canti']  = ($Meta_cantidad=="0") ? "0" : number_format((100 * $Real_cantidad) / $Meta_cantidad,2);
                        $i++;
                    }
                }elseif($Real_cantidad=="0.00"){
                    $rtnArticulo[$i]['mCodigo']      = $key['CodProducto'];
                    $rtnArticulo[$i]['mName']        = strtoupper($key['NombreProducto']);
                    $rtnArticulo[$i]['mMeta_monto']  = number_format($Meta_Monto,0,'.','');
                    $rtnArticulo[$i]['mReal_monto']  = $Real_Monto;
                    $rtnArticulo[$i]['mcump_monto']  = ($Meta_Monto=="0") ? "0" : number_format((100 * $Real_Monto) / $Meta_Monto,2);
                    $rtnArticulo[$i]['mMeta_canti']  = number_format($Meta_cantidad,0,'.','');
                    $rtnArticulo[$i]['mReal_canti']  = $Real_cantidad;
                    $rtnArticulo[$i]['mcump_canti']  = ($Meta_cantidad=="0") ? "0" : number_format((100 * $Real_cantidad) / $Meta_cantidad,2);
                    $i++;
                }
            }else{
                $rtnArticulo[$i]['mCodigo']      = $key['CodProducto'];
                $rtnArticulo[$i]['mName']        = strtoupper($key['NombreProducto']);
                $rtnArticulo[$i]['mMeta_monto']  = number_format($Meta_Monto,0,'.','');
                $rtnArticulo[$i]['mReal_monto']  = $Real_Monto;
                $rtnArticulo[$i]['mcump_monto']  = ($Meta_Monto=="0") ? "0" : number_format((100 * $Real_Monto) / $Meta_Monto,2);
                $rtnArticulo[$i]['mMeta_canti']  = number_format($Meta_cantidad,0,'.','');
                $rtnArticulo[$i]['mReal_canti']  = $Real_cantidad;
                $rtnArticulo[$i]['mcump_canti']  = ($Meta_cantidad=="0") ? "0" : number_format((100 * $Real_cantidad) / $Meta_cantidad,2);
                $i++;
            }

        }
        //ARTICULOS VENDIDOS NO DEFINIDOS DENTRO DE LA MENTA Y FUERON FACTURADOS


        $inArticulos = substr($inArticulos,0,-1);
        $View            = $this->view($Empresa);
        $Q = "SELECT
                                                     ARTICULO,DESCRIPCION,SUM(Venta) AS Venta,SUM(CANTIDAD) AS CANTIDAD
                                                    FROM
                                                        Softland.DBO.".$View." t0 
                                                    WHERE
                                                        t0.nMes= '".$Mes."' 
                                                        AND t0.[año] = '".$anno."' 
                                                        AND t0.Ruta= '".$Ruta."' 
                                                        AND t0.ARTICULO not in (".$inArticulos.")
                                                        AND t0.Venta > 0
                                                        GROUP BY 	ARTICULO,DESCRIPCION";


        if(count($qVENTA_META) > 0){
            $qVENTA_NO_EN_META = $this->sqlsrv->fetchArray($Q,SQLSRV_FETCH_ASSOC);
            foreach($qVENTA_NO_EN_META as $key){
                $Meta_Monto = "0";
                $Real_Monto = $key['Venta'];
                $Meta_cantidad = "0";
                $Real_cantidad = $key['CANTIDAD'];
                if($Grafica == "Monto" || $Grafica==""){
                    if($Filtro=="REAL" || $Filtro==""){
                        $rtnArticulo[$i]['mCodigo']      = $key['ARTICULO'];
                        $rtnArticulo[$i]['mName']        = strtoupper($key['DESCRIPCION'])."(No def. en meta)";
                        $rtnArticulo[$i]['mMeta_monto']  = number_format($Meta_Monto,2,'.','');
                        $rtnArticulo[$i]['mReal_monto']  = number_format($Real_Monto,2,'.','');
                        $rtnArticulo[$i]['mcump_monto']  = ($Meta_Monto=="0") ? "0" : number_format((100 * $Real_Monto) / $Meta_Monto,2);
                        $rtnArticulo[$i]['mMeta_canti']  = number_format($Meta_cantidad,0,'.','');
                        $rtnArticulo[$i]['mReal_canti']  = number_format($Real_cantidad,0,'.','');
                        $rtnArticulo[$i]['mcump_canti']  = ($Meta_cantidad=="0") ? "0" : number_format((100 * $Real_cantidad) / $Meta_cantidad,2);
                        $i++;
                    }
                }
            }
        }else{
            $rtnArticulo[$i]['mCodigo']      = "-";
            $rtnArticulo[$i]['mName']        = "Sin Datos";
            $rtnArticulo[$i]['mMeta_monto']  = "0";
            $rtnArticulo[$i]['mReal_monto']  = "0";
            $rtnArticulo[$i]['mcump_monto']  = "0";
            $rtnArticulo[$i]['mMeta_canti']  = "0";
            $rtnArticulo[$i]['mReal_canti']  = "0";
            $rtnArticulo[$i]['mcump_canti']  = "0";
        }




     echo json_encode($rtnArticulo);
        $this->sqlsrv->close();

    }
    private function getSaleProductDetall($Empresa,$Ruta,$Mes,$anno,$Articulo){

        $View            = $this->view($Empresa);


        $qVENTA_REAL = $this->sqlsrv->fetchArray("SELECT
                                                      ISNULL(SUM(t0.VENTA), 0) as VENTA_REAL
                                                    FROM
                                                        Softland.DBO.".$View." t0 
                                                    WHERE
                                                        t0.nMes= '".$Mes."' 
                                                        AND t0.[año] = '".$anno."' 
                                                        AND t0.Ruta= '".$Ruta."' 
                                                        AND t0.ARTICULO= '".$Articulo."'
                                                        AND t0.Venta> 0",SQLSRV_FETCH_ASSOC);

        return $qVENTA_REAL[0]['VENTA_REAL'];

    }
    private function getSaleProductCantidad($Empresa,$Ruta,$Mes,$anno,$Articulo){

        $View            = $this->view($Empresa);

        $qVENTA_REAL = $this->sqlsrv->fetchArray("SELECT
                                                      ISNULL( SUM ( t0.CANTIDAD ), 0 ) AS CANTIDAD 
                                                    FROM
                                                        Softland.DBO.".$View." t0 
                                                    WHERE
                                                        t0.nMes= '".$Mes."' 
                                                        AND t0.[año] = '".$anno."' 
                                                        AND t0.Ruta= '".$Ruta."' 
                                                        AND t0.ARTICULO= '".$Articulo."'
                                                        AND t0.Venta> 0",SQLSRV_FETCH_ASSOC);

        return $qVENTA_REAL[0]['CANTIDAD'];

    }
    public function Login($Usurio,$Contra,$empresa,$device){

        $this->db->where('Usuario', $Usurio);
        $this->db->where('Contrasena', $Contra);
        $query_existe_usuario = $this->db->get('usuarios');

        if($query_existe_usuario->num_rows()>0){
            if($query_existe_usuario->result_array()[0]["Activo"]=="S"){

                $this->db->where('Id_Usuario', $Usurio);
                $this->db->where('Empresa', $empresa);
                $query_permiso_empresa = $this->db->get('permisos');
                if($query_permiso_empresa->num_rows()>0){

                    $set['result'][] = array('user_id' => $query_existe_usuario->result_array()[0]["Usuario"], 'name' => $query_existe_usuario->result_array()[0]["Nombre"], 'success' => '1');
                    if ($query_existe_usuario->result_array()[0]["Dispositivo"]==null){
                        $this->db->where('Usuario', $Usurio);
                        $this->db->where('Contrasena', $Contra);
                        $this->db->update('usuarios', array(
                            'Dispositivo' => $device
                        ));
                    }

                }else{
                    $set['result'][] = array('msg' => 'Login failed', 'success' => '0');
                }

            }else{
                $set['result'][] = array('msg' => 'Account disabled', 'success' => '2');
            }

        }else{
            $set['result'][] = array('msg' => 'Login failed', 'success' => '0');
        }

        header( 'Content-Type: application/json; charset=utf-8' );
        $json = json_encode($set);

        echo $json;
    }

}
?>
