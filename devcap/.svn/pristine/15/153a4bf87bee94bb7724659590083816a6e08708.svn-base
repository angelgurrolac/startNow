<?php
/*************
Descripcion: Gestiona el correcto funcionamineto del modulo Facturas, esta engloba los siguientes modulos
    1.- Consulta de Facturas Registradas y Cobradas.
    2.- Proyectos por Facturar.
    3.- Consulta Detallada de Proyecto
    4.- Cancelación de Facturas
    5.- Reporte de Facturas Canceladas
************
Autor: Emmanuel Sántiz.
************
Version: 1.0
************
Fecha de Creación: 02/06/2016
************
Log de Modificaciones.
    1.- 02/06/2016: Se agrego la accion index y la accion Cobrar.
    2.- 03/06/2016: Se agrego la accion index4.
    3.- 06/06/2016: Se agrego la accion index2 y la acion Cancelar.
    4.- 08/06/2016: Se agrego la accion index3.
    4.- 14/06/2016: Se agrego la accion view.
    5.- 25/06/2016: Se agregaron comentarios de Descripción, Autor, versión, fecha de creación, log de modificaciones.
*************/
namespace app\controllers;
use Yii;
use app\models\TblBitComentariosFacturas;
use app\models\TblFacturas;
use app\models\TblPeriodos;
use app\models\TblDocumentos;
use app\models\TblDocumentosProyectos;
use app\models\TblProyectosFases;
use app\models\TblEmpleados;
use app\models\SubirArchivo;
use app\models\TblAsignaciones;
use app\models\TblAsignacionesReemplazos;
use app\models\TblAsignacionesSeguimiento;
use app\models\TblBitComentariosAsignaciones;
use app\models\TblBitComentariosSeguimientoProy;
use app\models\TblCatBolsas;
use app\models\TblBitBlsDocs;
use app\models\TblProyectosPeriodos;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\db\Query;
use yii\db\Expression;
use yii\db\Command;
use yii\web\UploadedFile;
use app\components\FechasComponent AS Fechas;
use app\components\FacturasComponent AS FacturasComponent;

class FacturasController extends \yii\web\Controller
{

    public function behaviors()
    {
        return \yii\helpers\ArrayHelper::merge(parent::behaviors(), [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ]);
    }

    public function actionIndex() {

        $cliente = '';
        $ODC = '';
        $PK_RAZON_SOCIAL = '';
        $Factura = '';
        $Identificador = '';
        $Entregado = '';
        $Pagado = '';
        $entregado = '';
        $pagado = '';
        $Limit = 'Limit 0,20';
        $actual = 1;
        $Bolsa = '';
        $tipoBusqueda = '';
        $vencidas = '';

        $nuevo = array();

        $totalaPagar = 0;

        $cantServicios = 0;
        $cantBolsas = 0;

        $titulosFacturas = array('<th width="1.69%" ></th>',
            '<th width="7.50%" class="text-center">Factura</th>',
            '<th width="7.63%" class="text-center">Identificador</th>',
            '<th width="10.69%" class="text-center">Nombre del Cliente</th>',
            '<th width="7.50%" class="text-center">Nombre</th>',
            '<th width="11.69%" class="text-center">ODC</th>',
            '<th width="7.69%" class="text-center">Servicio</th>',
            '<th width="4.69%" class="text-center">Horas</th>',
            '<th width="5.69%" class="text-center">Empresa</th>',
            '<th width="7.69%" class="text-center">Monto</th>',
            '<th width="7.69%" class="text-center">Entregado</th>',
            '<th width="6.69%" class="text-center">Pagado</th>',
            '<th width="5.69%" class="text-center">Días</th>',
            '<th width="4.69%" class="text-center">Estatus</th>');

        $titulosBolsa = array('<th width="1.69%" ></th>',
            '<th width="9.60%" class="text-center">Factura</th>',
            '<th width="12.69%" class="text-center">Numero de Bolsa</th>',
            '<th width="12.69%" class="text-center">Nombre del Cliente</th>',
            '<th width="9.69%" class="text-center">Servicio</th>',
            '<th width="8.69%" class="text-center">Empresa</th>',
            '<th width="8.69%" class="text-center">Monto</th>',
            '<th width="9.69%" class="text-center">Entregado</th>',
            '<th width="10.2%" class="text-center">Pagado</th>',
            '<th width="6.69%" class="text-center">Días</th>',
            '<th width="8.69%" class="text-center">Estatus</th>');

        $modelCancel = new TblBitComentariosFacturas();

        if (Yii::$app->request->isAjax) {
            $data = Yii::$app->request->post();
            $post=null;
            parse_str($data['data'],$post);

            $cliente = $post['cliente'];
            $ODC = $post['ODC'];
            $PK_RAZON_SOCIAL = $post['PK_RAZON_SOCIAL'];
            $Factura = $post['Factura'];
            $Identificador = $post['Identificador'];
            $Bolsa = $post['Bolsa'];
            $actual = $post['page'];
            $tipoBusqueda = $post['tipoBusqueda'];
            $Limit = 'Limit '.(($actual * 20)-20).', 20';

            if (!empty($post['Entregado'])) {
                $Entregado = trim($post['Entregado']);
                $Entregado = explode('/', $Entregado);
                $Entregado = $Entregado[2].'-'.$Entregado[1].'-'.$Entregado[0];
            }

            if (!empty($post['Pagado'])) {
                $Pagado = trim($post['Pagado']);
                $Pagado = explode('/', $Pagado);
                $Pagado = $Pagado[2].'-'.$Pagado[1].'-'.$Pagado[0];
            }

            if ($tipoBusqueda == '') {

                $titulos = $titulosFacturas;

                if (isset($post['vencidas'])) {
                    $vencidas = $post['vencidas'];
                    $titulos[11] = '<th width="7.69%" class="text-center">Fecha Estimada</th>';
                    $titulos[12] = '<th width="4.69%" class="text-center">Días Retraso</th>';
                }

                // PARA MOTIVOS DEL PAGINADO RECUPERAMOS EL TOTAL DE LOS REGISTROS A PAGINAR
                $iCount = 1;
                $connection = \Yii::$app->db;
                $totalF = $connection->createCommand("CALL SP_FACTURAS_INDEX_SEL_PAGINADO(:Identificador, :cliente, :PK_RAZON_SOCIAL, :Factura, :ODC, :Entregado, :Pagado, '', :Count, :vencidas)")
                    ->bindValue(':Identificador', $Identificador)
                    ->bindValue(':cliente', $cliente)
                    ->bindValue(':PK_RAZON_SOCIAL', $PK_RAZON_SOCIAL)
                    ->bindValue(':Factura', $Factura)
                    ->bindValue(':ODC', $ODC)
                    ->bindValue(':Entregado', $Entregado)
                    ->bindValue(':Pagado', $Pagado)
                    ->bindValue(':Count', $iCount)
                    ->bindValue(':vencidas', $vencidas)
                    ->queryAll();
                //Cerrar Conexion
                $connection->close();
                $todos = $totalF[0]["num_rows"];
                $cantServicios = $todos;

                //Contador de coincidencias de facturas en Bolsas
                $connection = \Yii::$app->db;
                $totalF = $connection->createCommand("CALL SP_FACTURAS_INDEX_SEL_BLS_PAGINADO('', :P_FACTURA, '', '', '', :P_COUNT, :vencidas)")
                    ->bindValue(':P_FACTURA', $Factura)
                    ->bindValue(':P_COUNT', $iCount)
                    ->bindValue(':vencidas', $vencidas)
                    ->queryAll();
                //Cerrar Conexion
                $connection->close();
                $cantBolsas = (($Identificador != '' || $ODC != ''|| $cliente != '' || $PK_RAZON_SOCIAL != '' || $Entregado != '' || $Pagado != '') && $Factura == '') ? $cantBolsas : $totalF[0]["num_rows"];

                //calculo de total pendiente de pago
                if (isset($post['vencidas'])) {
                    $connection = \Yii::$app->db;
                    $totalpendiente = $connection->createCommand("CALL SP_FACTURAS_INDEX_SEL_PAGINADO('', '', '', '', '', '', '', '', 0, :vencidas)")
                    ->bindValue(':vencidas', $vencidas)
                    ->queryAll();
                    //Cerrar Conexion
                    $connection->close();

                    $facturasArrayPendientes = array();
                    foreach($totalpendiente AS $numeroFactura) {
                        if(isset($numeroFactura["NUM_FACTURA"]) && !empty($numeroFactura["NUM_FACTURA"])) {
                            $facturasArrayPendientes[] = "'" .$numeroFactura["NUM_FACTURA"] . "'";
                        }
                    }

                    $cFacturasBuscarPendientes = "' '";
                    if(count($facturasArrayPendientes) > 0) {
                        $cFacturasBuscarPendientes = implode(",", $facturasArrayPendientes);
                    }

                    $connection = \Yii::$app->db;
                    /*$facturasvencidas = $connection->createCommand("CALL SP_FACTURAS_INDEX_SEL_PERIODOS_POR_FAC(:P_NUM_FACTURA, NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL)")
                        ->bindValue(':P_NUM_FACTURA', $cFacturasBuscarPendientes)
                        ->queryAll();*/
                    $facturasvencidas = $connection->createCommand("CALL SP_FACTURAS_INDEX_SEL_PERIODOS_POR_FAC(:P_NUM_FACTURA, :Identificador, :cliente, :PK_RAZON_SOCIAL, :Factura, :ODC, :Entregado, :Pagado, '', :Count, :vencidas)")
                        ->bindValue(':P_NUM_FACTURA', $cFacturasBuscarPendientes)
                        ->bindValue(':Identificador', $Identificador)
                        ->bindValue(':cliente', $cliente)
                        ->bindValue(':PK_RAZON_SOCIAL', $PK_RAZON_SOCIAL)
                        ->bindValue(':Factura', $Factura)
                        ->bindValue(':ODC', $ODC)
                        ->bindValue(':Entregado', $Entregado)
                        ->bindValue(':Pagado', $Pagado)
                        ->bindValue(':Count', $iCount)
                        ->bindValue(':vencidas', $vencidas)
                        ->queryAll();
                    //Cerrar Conexion
                    $connection->close();

                    foreach ($facturasvencidas as $key) {
                        $totalaPagar = $totalaPagar + $key['MONTO_FACTURA'];
                    }

                }

                // RECUPERAMOS LOS NUMEROS DE LAS FACTURAS A MOSTRAR POR PAGINA, SERAN UTILIZADOS PARA REALIZAR UN FILTRO
                $iCount = 0;
                $connection = \Yii::$app->db;
                $aFacturas = $connection->createCommand("CALL SP_FACTURAS_INDEX_SEL_PAGINADO(:Identificador, :cliente, :PK_RAZON_SOCIAL, :Factura, :ODC, :Entregado, :Pagado, :Limit, :Count, :vencidas)")
                    ->bindValue(':Identificador', $Identificador)
                    ->bindValue(':cliente', $cliente)
                    ->bindValue(':PK_RAZON_SOCIAL', $PK_RAZON_SOCIAL)
                    ->bindValue(':Factura', $Factura)
                    ->bindValue(':ODC', $ODC)
                    ->bindValue(':Entregado', $Entregado)
                    ->bindValue(':Pagado', $Pagado)
                    ->bindValue(':Limit', $Limit)
                    ->bindValue(':Count', $iCount)
                    ->bindValue(':vencidas', $vencidas)
                    ->queryAll();
                //Cerrar Conexion
                $connection->close();

                // CONSTRUIMOS UN ARRAY CON LOS NUMEROS DE FACTURAS A MOSTRAR EN EL LISTADO POR PAGINA
                $facturasArray = array();
                foreach($aFacturas AS $numeroFactura)
                {
                    if(isset($numeroFactura["NUM_FACTURA"]) && !empty($numeroFactura["NUM_FACTURA"]))
                    {
                         $facturasArray[] = "'" .$numeroFactura["NUM_FACTURA"] . "'";
                    }
                }

                // COMBERTIMOS EL ARRAY CON UN STRING SEPARADO POR COMAS PARA REALIZAR EL FILTRO
                // es nececsario que el string se inicialize de esta manera para que el SP no truene
                $cFacturasBuscar = "' '";
                if(count($facturasArray) > 0)
                {
                    $cFacturasBuscar = implode(",", $facturasArray);
                }

                // RECUPERAMOS LA  INFORMACION DE LAS FACTURAS CON SUS HORAS POR PERIODOS PARA MOSTRAR POR PAGINA
                $connection = \Yii::$app->db;
                /*$facturas = $connection->createCommand("CALL SP_FACTURAS_INDEX_SEL_PERIODOS_POR_FAC(:P_NUM_FACTURA, NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL)")
                    ->bindValue(':P_NUM_FACTURA', $cFacturasBuscar)
                    ->queryAll();*/
                $facturas = $connection->createCommand("CALL SP_FACTURAS_INDEX_SEL_PERIODOS_POR_FAC(:P_NUM_FACTURA, :Identificador, :cliente, :PK_RAZON_SOCIAL, :Factura, :ODC, :Entregado, :Pagado, '', :Count, :vencidas)")
                        ->bindValue(':P_NUM_FACTURA', $cFacturasBuscar)
                        ->bindValue(':Identificador', $Identificador)
                        ->bindValue(':cliente', $cliente)
                        ->bindValue(':PK_RAZON_SOCIAL', $PK_RAZON_SOCIAL)
                        ->bindValue(':Factura', $Factura)
                        ->bindValue(':ODC', $ODC)
                        ->bindValue(':Entregado', $Entregado)
                        ->bindValue(':Pagado', $Pagado)
                        ->bindValue(':Count', $iCount)
                        ->bindValue(':vencidas', $vencidas)
                        ->queryAll();
                //Cerrar Conexion
                $connection->close();

                // REALIZAMOS LA AGRUPACION DE LAS FACTURAS
                $nuevo = FacturasComponent::getRegistradasCobradasServicios($facturas);

            } else {

                $titulos = $titulosBolsa;

                if (isset($post['vencidas'])) {
                    $vencidas = $post['vencidas'];
                    $titulos[8] = '<th width="10.2%" class="text-center">Fecha Estimada</th>';
                    $titulos[9] = '<th width="6.69%" class="text-center">Días Retraso</th>';
                }

                // PARA MOTIVOS DEL PAGINADO RECUPERAMOS EL TOTAL DE LOS REGISTROS A PAGINAR
                $iCount = 1;
                $connection = \Yii::$app->db;
                $totalF = $connection->createCommand("CALL SP_FACTURAS_INDEX_SEL_BLS_PAGINADO(:P_PK_RAZON_SOCIAL, :P_FACTURA, :P_CLIENTE, :P_BOLSA, '', :P_COUNT, :vencidas)")
                    ->bindValue(':P_PK_RAZON_SOCIAL', $PK_RAZON_SOCIAL)
                    ->bindValue(':P_FACTURA', $Factura)
                    ->bindValue(':P_CLIENTE', $cliente)
                    ->bindValue(':P_BOLSA', $Bolsa)
                    ->bindValue(':P_COUNT', $iCount)
                    ->bindValue(':vencidas', $vencidas)
                    ->queryAll();
                //Cerrar Conexion
                $connection->close();
                $todos = $totalF[0]["num_rows"];
                $cantBolsas = $todos;

                //Contador de coincidencias de facturas en Servicios
                $connection = \Yii::$app->db;
                $totalF = $connection->createCommand("CALL SP_FACTURAS_INDEX_SEL_PAGINADO('', '', '', :Factura, '', '', '', '', :Count, '')")
                    ->bindValue(':Factura', $Factura)
                    ->bindValue(':Count', $iCount)
                    ->queryAll();
                //Cerrar Conexion
                $connection->close();
                $cantServicios = (($cliente != '' || $PK_RAZON_SOCIAL != '' || $Bolsa != '') && $Factura == '') ? $cantServicios : $totalF[0]["num_rows"];

                //calculo de total pendiente de pago
                if (isset($post['vencidas'])) {
                    $connection = \Yii::$app->db;
                    $totalpendiente = $connection->createCommand("CALL SP_FACTURAS_INDEX_SEL_BLS_PAGINADO('', '', '', '', '', 0, :vencidas)")
                    ->bindValue(':vencidas', $vencidas)
                    ->queryAll();
                    //Cerrar Conexion
                    $connection->close();

                    $facturasArrayPendientes = array();
                    foreach($totalpendiente AS $numeroFactura) {
                        if(isset($numeroFactura["NUM_FACTURA"]) && !empty($numeroFactura["NUM_FACTURA"])) {
                            $facturasArrayPendientes[] = "'" .$numeroFactura["NUM_FACTURA"] . "'";
                        }
                    }

                    $cFacturasBuscarPendientes = "' '";
                    if(count($facturasArrayPendientes) > 0) {
                        $cFacturasBuscarPendientes = implode(",", $facturasArrayPendientes);
                    }

                    $connection = \Yii::$app->db;
                    $facturasvencidas = $connection->createCommand("CALL SP_FACTURAS_INDEX_SEL_BLS_PERIODOS_POR_FAC(:P_NUM_FACTURA, NULL,NULL,NULL,NULL,NULL,NULL,NULL)")
                        ->bindValue(':P_NUM_FACTURA', $cFacturasBuscarPendientes)
                        ->queryAll();
                    //Cerrar Conexion
                    $connection->close();

                    foreach ($facturasvencidas as $key) {
                        $totalaPagar = $totalaPagar + $key['MONTO_FACTURA'];
                    }

                }

                // RECUPERAMOS LOS NUMEROS DE LAS FACTURAS A MOSTRAR POR PAGINA, SERAN UTILIZADOS PARA REALIZAR UN FILTRO
                $iCount = 0;
                $connection = \Yii::$app->db;
                $aFacturas = $connection->createCommand("CALL SP_FACTURAS_INDEX_SEL_BLS_PAGINADO(:P_PK_RAZON_SOCIAL, :P_FACTURA, :P_CLIENTE, :P_BOLSA, :P_LIMIT, :P_COUNT, :vencidas)")
                    ->bindValue(':P_PK_RAZON_SOCIAL', $PK_RAZON_SOCIAL)
                    ->bindValue(':P_FACTURA', $Factura)
                    ->bindValue(':P_CLIENTE', $cliente)
                    ->bindValue(':P_BOLSA', $Bolsa)
                    ->bindValue(':P_LIMIT', $Limit)
                    ->bindValue(':P_COUNT', $iCount)
                    ->bindValue(':vencidas', $vencidas)
                    ->queryAll();
                //Cerrar Conexion
                $connection->close();

                // CONSTRUIMOS UN ARRAY CON LOS NUMEROS DE FACTURAS A MOSTRAR EN EL LISTADO POR PAGINA
                $facturasArray = array();
                foreach($aFacturas AS $numeroFactura)
                {
                    if(isset($numeroFactura["NUM_FACTURA"]) && !empty($numeroFactura["NUM_FACTURA"]))
                    {
                         $facturasArray[] = "'" .$numeroFactura["NUM_FACTURA"] . "'";
                    }
                }

                // COMBERTIMOS EL ARRAY CON UN STRING SEPARADO POR COMAS PARA REALIZAR EL FILTRO
                // ES NECESARIO QUE EL STRING SE INICIALICE CON COMILLAS PARA QUE EL SP NO TRUENE CUANDO NO TENGA VALOR
                $cFacturasBuscar = "' '";
                if(count($facturasArray) > 0)
                {
                    $cFacturasBuscar = implode(",", $facturasArray);
                }

                // RECUPERAMOS LA INFORMACION DE LAS FACTURAS CON SUS HORAS POR PERIODOS PARA MOSTRAR POR PAGINA
                $connection = \Yii::$app->db;
                $facturas = $connection->createCommand("CALL SP_FACTURAS_INDEX_SEL_BLS_PERIODOS_POR_FAC(:P_NUM_FACTURA, NULL,NULL,NULL,NULL,NULL,NULL,NULL)")
                    ->bindValue(':P_NUM_FACTURA', $cFacturasBuscar)
                    ->queryAll();
                //Cerrar Conexion
                $connection->close();

                // REALIZAMOS LA AGRUPACION DE LAS FACTURAS
                $nuevo = FacturasComponent::getRegistradasCobradasBolsas($facturas);

            }


            $total_paginas = ($todos < 20) ? 1 : ceil($todos / 20);

            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return ['facturas' => $nuevo,
                    'titulos' => $titulos,
                    'totalaPagar' => $totalaPagar,
                    'total_paginas' => $total_paginas,
                    'pagina' => $actual,
                    'post' => $post,
                    'total_registros' => $todos,
                    'cantServicios' => $cantServicios,
                    'cantBolsas' => $cantBolsas];
        } else {
            return $this->render('index', ['modelCancel' => $modelCancel]);
        }
    }

    public function actionCancelar(){

        $modelCancel = new TblBitComentariosFacturas();
        if ($modelCancel->load(Yii::$app->request->post()) ){
            $data = Yii::$app->request->post();

            $tipo = ($data['tipo'] == '') ? 'Servicio' : 'Bolsa';

            $asignacion = (new \yii\db\Query)
                ->select([
                    'e.EMAIL_EMP',
                    "concat_ws(' ',e.NOMBRE_EMP,e.APELLIDO_PAT_EMP,e.APELLIDO_MAT_EMP) nombre_emp"
                ])
                ->from('tbl_asignaciones a')
                ->join('inner join', 'tbl_empleados e','e.PK_EMPLEADO =  a.FK_RESPONSABLE_OP')
                ->where(['a.PK_ASIGNACION'=>$data['idAsignacion']])->one();

            $modelCancel->MOTIVO = $data['TblBitComentariosFacturas']['MOTIVO'];
            $modelCancel->FECHA_REGISTRO = date('Y-m-d H:i:s');
            $modelCancel->FECHA_FIN = transform_date($data['TblBitComentariosFacturas']['FECHA_FIN'],'Y-m-d');
            $modelCancel->FK_FACTURA = $data['idFactura'];
            $modelCancel->save(false);

            $modelFactura = TblFacturas::find()->where(['PK_FACTURA' => $data['idFactura']])->limit(1)->one();
            $modelFactura->FK_ESTATUS = 3;
            $modelFactura->save(false);

            $periodos = TblPeriodos::find()->where(['FK_DOCUMENTO_FACTURA'=>$modelFactura->FK_DOC_FACTURA])->all();
            foreach ($periodos as $key) {
                $key->FK_DOCUMENTO_FACTURA = null;
                $key->FK_DOCUMENTO_FACTURA_XML = null;
                $key->save(false);
            }

            //Envio de Correo a Responsable de OP
            $de = 'rh.ert@eisei.net.mx';
            //$para = 'emmanuel.santiz@eisei.net.mx';
            $para = array($asignacion['EMAIL_EMP'], 'miguel.rodriguez@eisei.net.mx', 'irving.rivera@eisei.net.mx');
            $asunto = 'Eliminación de factura por '.$tipo;
            $nombre_usuario = $asignacion['nombre_emp'];

            $mensaje="<style>p, ul, li {font-family: Arial, Helvetica, sans-serif; font-size: 12px;}</style>
                <p>Buen d&iacute;a <b>$nombre_usuario.</b>
                <p>El usuario <b>".user_info()['NOMBRE_COMPLETO']."</b> Canceló una factura <b>".$data['numFactura']."</b>, El motivo por el cual fue cancelada la factura <b>".$data['TblBitComentariosFacturas']['MOTIVO']."</b> y la fecha de cancelaci&oacute;n <b>".$data['TblBitComentariosFacturas']['FECHA_FIN']."</b>.</p>
                <p>Saludos.</p>";

            send_mail($de,$para, $asunto, $mensaje);

            $descripcionBitacora = 'MOTIVO='.$modelCancel->MOTIVO.', FECHA_FIN='.$modelCancel->FECHA_FIN;
            user_log_bitacora_contabilidad($descripcionBitacora,'Eliminación / Cancelación de Factura',$modelCancel->FK_FACTURA);

            return $this->redirect(['canceladas']);
        }

    }

    public function actionCanceladas() {

        $Identificador = '';
        $ODC = '';
        $Factura = '';
        $PK_RAZON_SOCIAL = '';
        $Entregado = '';
        $Cancelado = '';
        $Bolsa = '';

        $entregado = '';
        $cancelado = '';

        $cantServicios = 0;
        $cantBolsas = 0;

        $titulosFacturas = array(
        '<th width="12.09%" class="text-center">Factura</th>',
        '<th width="9.09%" class="text-center">Identificador</th>',
        '<th width="16.09%" class="text-center">Nombre del Identificador</th>',
        '<th width="9.09%" class="text-center">ODC</th>',
        '<th width="9.09%" class="text-center">Servicio</th>',
        '<th width="4.09%" class="text-center">Horas</th>',
        '<th width="13.09%" class="text-center">Empresa</th>',

        '<th width="5.09%" class="text-center">Monto</th>',
        '<th width="9.09%" class="text-center">Entregado</th>',
        '<th width="9.09%" class="text-center">Cancelado</th>',
        '<th width="4.09%" class="text-center">Motivo</th>');

        $titulosBolsa = array(
        '<th width="10.09%" class="text-center">Factura</th>',
        '<th width="12.69%" class="text-center">Numero de Bolsa</th>',
        '<th width="16.69%" class="text-center">Nombre del Cliente</th>',
        '<th width="8.09%" class="text-center">Servicio</th>',
        '<th width="9.09%" class="text-center">Empresa</th>',
        '<th width="10.09%" class="text-center">Monto</th>',
        '<th width="13.09%" class="text-center">Entregado</th>',
        '<th width="13.09%" class="text-center">Cancelado</th>',
        '<th width="5.09%" class="text-center">Motivo</th>');

        $Limit = 'Limit 0,20';
        $actual = 1;
        $tipoBusqueda = '';
        $total = 0;

        if (Yii::$app->request->isAjax) {
            $data = Yii::$app->request->post();
            $post=null;
            parse_str($data['data'],$post);

            $Identificador = $post['Identificador'];
            $ODC = $post['ODC'];
            $Factura = $post['Factura'];
            $PK_RAZON_SOCIAL = $post['PK_RAZON_SOCIAL'];
            $actual = $post['page'];
            $tipoBusqueda = $post['tipoBusqueda'];
            $Bolsa = $post['Bolsa'];
            $Limit = 'Limit '.(($actual * 20)-20).', 20';

            if (!empty($post['Entregado'])) {
                $Entregado = trim($post['Entregado']);
                $Entregado = explode('/', $Entregado);
                $Entregado = $Entregado[2].'-'.$Entregado[1].'-'.$Entregado[0];
            }

            if (!empty($post['Cancelado'])) {
                $Cancelado = trim($post['Cancelado']);
                $Cancelado = explode('/', $Cancelado);
                $Cancelado = $Cancelado[2].'-'.$Cancelado[1].'-'.$Cancelado[0];
            }

            $titulos = ($tipoBusqueda == '') ? $titulosFacturas : $titulosBolsa;


            if($tipoBusqueda == '') {

                // PARA MOTIVOS DEL PAGINADO RECUPERAMOS EL TOTAL DE LOS REGISTROS A PAGINAR
                $count = 1;
                //Abrir Conexion
                $connection = \Yii::$app->db;
                $totalF = $connection->createCommand("CALL SP_FACTURAS_VIEWCANCELADAS_SEL_PAGINADO(:Identificador, :PK_RAZON_SOCIAL, :Factura, :ODC, :Entregado, :Cancelado, '', :Bolsa, :Count)")
                    ->bindValue(':Identificador', $Identificador)
                    ->bindValue(':PK_RAZON_SOCIAL', $PK_RAZON_SOCIAL)
                    ->bindValue(':Factura', $Factura)
                    ->bindValue(':ODC', $ODC)
                    ->bindValue(':Entregado', $Entregado)
                    ->bindValue(':Cancelado', $Cancelado)
                    ->bindValue(':Bolsa', $Bolsa)
                    ->bindValue(':Count', $count)
                    ->queryAll();
                //Cerrar Conexion
                $connection->close();
                $todos = $totalF[0]["num_rows"];
                $cantServicios = $todos;

                //Contador de coincidencias de facturas en Bolsas
                $connection = \Yii::$app->db;
                $totalF = $connection->createCommand("CALL SP_FACTURAS_VIEWCANCELADAS_SEL_BLS_PAGINADO('', '', :Factura, '', '', '', '', '', :Count)")
                    ->bindValue(':Factura', $Factura)
                    ->bindValue(':Count', $count)
                    ->queryAll();
                //Cerrar Conexion
                $connection->close();
                $cantBolsas = (($Identificador != '' || $ODC != '' || $PK_RAZON_SOCIAL != '' || $Entregado != '' || $Cancelado != '') && $Factura == '') ? $cantBolsas : $totalF[0]["num_rows"];

                // RECUPERAMOS LOS NUMEROS DE LAS FACTURAS A MOSTRAR POR PAGINA, SERAN UTILIZADOS PARA REALIZAR UN FILTRO
                $connection = \Yii::$app->db;
                $count = 0;
                $aFacturas = $connection->createCommand("CALL SP_FACTURAS_VIEWCANCELADAS_SEL_PAGINADO(:Identificador, :PK_RAZON_SOCIAL, :Factura, :ODC, :Entregado, :Cancelado, :Limit, :Bolsa, :Count)")
                    ->bindValue(':Identificador', $Identificador)
                    ->bindValue(':PK_RAZON_SOCIAL', $PK_RAZON_SOCIAL)
                    ->bindValue(':Factura', $Factura)
                    ->bindValue(':ODC', $ODC)
                    ->bindValue(':Entregado', $Entregado)
                    ->bindValue(':Cancelado', $Cancelado)
                    ->bindValue(':Limit', $Limit)
                    ->bindValue(':Bolsa', $Bolsa)
                    ->bindValue(':Count', $count)
                    ->queryAll();
                //Cerrar Conexion
                $connection->close();

                // CONSTRUIMOS UN ARRAY CON LOS NUMEROS DE FACTURAS A MOSTRAR EN EL LISTADO POR PAGINA
                $facturasArray = array();
                foreach($aFacturas AS $numeroFactura)
                {
                    if(isset($numeroFactura["NUM_FACTURA"]) && !empty($numeroFactura["NUM_FACTURA"]))
                    {
                         $facturasArray[] = "'" .$numeroFactura["NUM_FACTURA"] . "'";
                    }
                }

                // COMBERTIMOS EL ARRAY CON UN STRING SEPARADO POR COMAS PARA REALIZAR EL FILTRO
                // ES NECESARIO QUE EL STRING SE INICIALICE CON COMILLAS PARA QUE EL SP NO TRUENE CUANDO NO TENGA VALOR
                $cFacturasBuscar = "' '";
                if(count($facturasArray) > 0)
                {
                    $cFacturasBuscar = implode(",", $facturasArray);
                }

                // RECUPERAMOS LA INFORMACION DE LAS FACTURAS CON SUS HORAS POR PERIODOS PARA MOSTRAR POR PAGINA
                $connection = \Yii::$app->db;
                $facturas = $connection->createCommand("CALL SP_FACTURAS_VIEWCANCELADAS_SEL_PERIODOS_FAC(:P_NUM_FACTURA, NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL)")
                    ->bindValue(':P_NUM_FACTURA', $cFacturasBuscar)
                    ->queryAll();
                //Cerrar Conexion
                $connection->close();


                // REALIZAMOS LA AGRUPACION DE LAS FACTURAS
                $nuevo = FacturasComponent::getCanceladasServicios($facturas);

            } else {

                // PARA MOTIVOS DEL PAGINADO RECUPERAMOS EL TOTAL DE LOS REGISTROS A PAGINAR
                $count = 1;
                //Abrir Conexion
                $connection = \Yii::$app->db;
                $totalF = $connection->createCommand("CALL SP_FACTURAS_VIEWCANCELADAS_SEL_BLS_PAGINADO(:Identificador, :PK_RAZON_SOCIAL, :Factura, :ODC, :Entregado, :Cancelado, '', :Bolsa, :Count)")
                    ->bindValue(':Identificador', $Identificador)
                    ->bindValue(':PK_RAZON_SOCIAL', $PK_RAZON_SOCIAL)
                    ->bindValue(':Factura', $Factura)
                    ->bindValue(':ODC', $ODC)
                    ->bindValue(':Entregado', $Entregado)
                    ->bindValue(':Cancelado', $Cancelado)
                    ->bindValue(':Bolsa', $Bolsa)
                    ->bindValue(':Count', $count)
                    ->queryAll();
                //Cerrar Conexion
                $connection->close();
                $todos = $totalF[0]["num_rows"];
                $cantBolsas = $total;

                //Contador de coincidencias de facturas en Servicios
                $connection = \Yii::$app->db;
                $totalF = $connection->createCommand("CALL SP_FACTURAS_VIEWCANCELADAS_SEL_PAGINADO('', '', :Factura, '', '', '', '', '', :Count)")
                    ->bindValue(':Factura', $Factura)
                    ->bindValue(':Count', $count)
                    ->queryAll();
                //Cerrar Conexion
                $connection->close();
                $cantServicios = (($Bolsa != '' || $PK_RAZON_SOCIAL != '' || $Entregado != '' || $Cancelado != '') && $Factura == '') ? $cantServicios : $totalF[0]["num_rows"];

                // RECUPERAMOS LOS NUMEROS DE LAS FACTURAS A MOSTRAR POR PAGINA, SERAN UTILIZADOS PARA REALIZAR UN FILTRO
                $connection = \Yii::$app->db;
                $count = 0;
                $aFacturas = $connection->createCommand("CALL SP_FACTURAS_VIEWCANCELADAS_SEL_BLS_PAGINADO(:Identificador, :PK_RAZON_SOCIAL, :Factura, :ODC, :Entregado, :Cancelado, :Limit, :Bolsa, :Count)")
                    ->bindValue(':Identificador', $Identificador)
                    ->bindValue(':PK_RAZON_SOCIAL', $PK_RAZON_SOCIAL)
                    ->bindValue(':Factura', $Factura)
                    ->bindValue(':ODC', $ODC)
                    ->bindValue(':Entregado', $Entregado)
                    ->bindValue(':Cancelado', $Cancelado)
                    ->bindValue(':Limit', $Limit)
                    ->bindValue(':Bolsa', $Bolsa)
                    ->bindValue(':Count', $count)
                    ->queryAll();
                //Cerrar Conexion
                $connection->close();

                // CONSTRUIMOS UN ARRAY CON LOS NUMEROS DE FACTURAS A MOSTRAR EN EL LISTADO POR PAGINA
                $facturasArray = array();
                foreach($aFacturas AS $numeroFactura)
                {
                    if(isset($numeroFactura["NUM_FACTURA"]) && !empty($numeroFactura["NUM_FACTURA"]))
                    {
                         $facturasArray[] = "'" . $numeroFactura["NUM_FACTURA"] . "'";
                    }
                }

                // COMBERTIMOS EL ARRAY CON UN STRING SEPARADO POR COMAS PARA REALIZAR EL FILTRO
                // ES NECESARIO QUE EL STRING SE INICIALICE CON COMILLAS PARA QUE EL SP NO TRUENE CUANDO NO TENGA VALOR
                $cFacturasBuscar = "' '";
                if(count($facturasArray) > 0)
                {
                    $cFacturasBuscar = implode(",", $facturasArray);
                }

                // RECUPERAMOS LA INFORMACION DE LAS FACTURAS CON SUS HORAS POR PERIODOS PARA MOSTRAR POR PAGINA
                $connection = \Yii::$app->db;
                $facturas = $connection->createCommand("CALL SP_FACTURAS_VIEWCANCELADAS_SEL_BLS_PERIODOS_FAC(:P_NUM_FACTURA, NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL)")
                    ->bindValue(':P_NUM_FACTURA', $cFacturasBuscar)
                    ->queryAll();
                //Cerrar Conexion
                $connection->close();


                // REALIZAMOS LA AGRUPACION DE LAS FACTURAS
                $nuevo = FacturasComponent::getCanceladasServicios($facturas);
            }

            $total_paginas = ($todos < 20) ? 1 : ceil($todos / 20);

            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return ['facturas' => $nuevo,
                    'total_paginas' => $total_paginas,
                    'pagina' => $actual,
                    'post' => $post,
                    'total_registros' => $todos,
                    'total' => $total,
                    'titulos' => $titulos,
                    'cantServicios' => $cantServicios,
                    'cantBolsas' => $cantBolsas];
        } else {
            return $this->render('canceladas');
        }
    }

    public function actionPendientes_facturar() {

        $Identificador = '';
        $ODC = '';
        $cliente = '';
        $servicio = '';
        $HDE = '';
        $actual = 1;
        $tipoBusqueda = '';
        $Bolsa = '';

        $serv = array();
        $bls = array();
        $nuevo = array();
        $bolsas = array();
        $bls_asign = array();
        $totalCliente = 0;
        $cont = 0;
        $todos = 0;
        $servicios = array();
        $total = 0;
        $pendietes_bolsas = NULL;

        $cantServicios = 0;
        $cantBolsas = 0;

        $Limit = 'Limit 0,20';

        $titulosFacturas = array('<th width="9%" class="text-center">Identificador</th>',
            '<th width="8%" class="text-center">Nombre del Cliente</th>',
            '<th width="10%" class="text-center">Nombre</th>',
            '<th width="8%" class="text-center">Servicio</th>',
            '<th width="5%" class="text-center">Horas</th>',
            '<th width="7%" class="text-center">SP</th>',
            '<th width="9%" class="text-center">ODC</th>',
            '<th width="8%" class="text-center">Monto ODC</th>',
            '<th width="8%" class="text-center">HDE</th>',
            '<th width="14%" class="text-center">Monto HDE</th>',
            '<th width="7%" class="text-center">Fecha Inicio</th>',
            '<th width="6%" class="text-center">Fecha Fin</th>');

        $titulosBolsa = array('<th width="10%" class="text-center">Id Petición</th>',
            '<th width="10%" class="text-center">Numero de Bolsa</th>',
            '<th width="10%" class="text-center">Fecha de Bolsa</th>',
            '<th width="10%" class="text-center">Cliente</th>',
            '<th width="10%" class="text-center">Tarifa</th>',
            '<th width="13.5%" class="text-center">Monto a Facturar</th>',
            '<th width="10%" class="text-center">Empresa</th>');

        if (Yii::$app->request->isAjax) {
            $data = Yii::$app->request->post();
            $post=null;
            parse_str($data['data'],$post);


            $Peticion = $post['Peticion'];
            $Identificador = $post['Identificador'];
            $ODC = $post['ODC'];
            $cliente = $post['cliente'];
            $servicio = $post['servicios'];
            $HDE = $post['HDE'];
            $actual = $post['page'];
            $Bolsa = $post['Bolsa'];
            $tipoBusqueda = $post['tipoBusqueda'];

            $Limit = 'Limit '.(($actual * 20)-20).', 20';

            if($tipoBusqueda == '') {

                $titulos = $titulosFacturas;

                // RECUPERAAMOS EL  TOTAL DE LAS FACTURAS PARA EL PAGINADO
                $connection = \Yii::$app->db;
                $totalF = $connection->createCommand("CALL SP_FACTURAS_VIEWPORFACTURAR_SEL(:Identificador, :ODC, :cliente, :servicio, :HDE, :LIMIT, :COUNT)")
                    ->bindValue(':Identificador', $Identificador)
                    ->bindValue(':ODC', $ODC)
                    ->bindValue(':cliente', $cliente)
                    ->bindValue(':servicio', $servicio)
                    ->bindValue(':HDE', $HDE)
                    ->bindValue(':LIMIT', '')
                    ->bindValue(':COUNT', 1)
                    ->queryAll();
                $connection->close();
                $todos = $totalF[0]['num_rows'];
                $cantServicios = $todos;

                //Contador de coincidencias de facturas en Bolsas
                $connection = \Yii::$app->db;
                $totalF = $connection->createCommand("CALL SP_FACTURAS_VIEWPORFACTURAR_SEL_BLS('', '', '', 1, '')")->queryAll();
                $connection->close();
                $cantBolsas = ($Identificador == '' && $ODC == '' && $cliente == '' && $servicio == '' && $HDE == '') ? $totalF[0]['num_rows'] : $cantBolsas;

                // RECUPERAMOS LOS DATOS A MOSTRAR POR PAGINA
                $connection = \Yii::$app->db;
                $facturas = $connection->createCommand("CALL SP_FACTURAS_VIEWPORFACTURAR_SEL(:Identificador, :ODC, :cliente, :servicio, :HDE, :LIMIT, :COUNT)")
                    ->bindValue(':Identificador', $Identificador)
                    ->bindValue(':ODC', $ODC)
                    ->bindValue(':cliente', $cliente)
                    ->bindValue(':servicio', $servicio)
                    ->bindValue(':HDE', $HDE)
                    ->bindValue(':LIMIT', $Limit)
                    ->bindValue(':COUNT', 0)
                    ->queryAll();
                $connection->close();

                // RECUPERAMOS TODAS LAS FACTURAS PARA SUMAR EL TOTAL
                $connection = \Yii::$app->db;
                $facturasServicios = $connection->createCommand("CALL SP_FACTURAS_VIEWPORFACTURAR_SEL(:Identificador, :ODC, :cliente, :servicio, :HDE, :LIMIT, :COUNT)")
                    ->bindValue(':Identificador', $Identificador)
                    ->bindValue(':ODC', $ODC)
                    ->bindValue(':cliente', $cliente)
                    ->bindValue(':servicio', $servicio)
                    ->bindValue(':HDE', $HDE)
                    ->bindValue(':LIMIT', '')
                    ->bindValue(':COUNT', 0)
                    ->queryAll();
                $connection->close();

                foreach($facturasServicios AS $value)
                {
                    // VALIDAMOS SI EXISTE EL MONTO DE LA HDE
                    if(!is_null($value['MONTO_HDE']) && $value['MONTO_HDE'] !="")
                    {
                        $total = $total + $value['MONTO_HDE'];
                    }
                    else
                    {
                        $total = $total + $value['MONTO'];
                    }
                }

            } else {

                $titulos = $titulosBolsa;

                // RECUPERAMOS EL TOTAL DE REGISTROS A MOSTRAR
                $connection = \Yii::$app->db;
                $totalF = $connection->createCommand("CALL SP_FACTURAS_VIEWPORFACTURAR_SEL_BLS(:Bolsa, :cliente, :Limit, :Count, :Peticion)")
                        ->bindValue(':Bolsa', $Bolsa)
                        ->bindValue(':cliente', $cliente)
                        ->bindValue(':Peticion', $Peticion)
                        ->bindValue(':Limit', '')
                        ->bindValue(':Count', 1)
                        ->queryAll();
                $connection->close();
                $todos = $totalF[0]['num_rows'];
                $cantBolsas = $todos;

                // RECUPERAMOS LAS BOLSAS A MOSTRAR POR PAGINA
                $connection = \Yii::$app->db;
                $facturas = $connection->createCommand("CALL SP_FACTURAS_VIEWPORFACTURAR_SEL_BLS(:Bolsa, :cliente, :Limit, :Count, :Peticion)")
                        ->bindValue(':Bolsa', $Bolsa)
                        ->bindValue(':cliente', $cliente)
                        ->bindValue(':Peticion', $Peticion)
                        ->bindValue(':Limit', $Limit)
                        ->bindValue(':Count', 0)
                        ->queryAll();
                $connection->close();

                //Contador de coincidencias de facturas en Servicios
                $connection = \Yii::$app->db;
                $totalF = $connection->createCommand("CALL SP_FACTURAS_VIEWPORFACTURAR_SEL('', '', '', '', '', '', 1)")->queryAll();
                $cantServicios = ($Bolsa == '' && $cliente == '') ? $totalF[0]['num_rows'] : $cantServicios;

                // SE RECORREN TODAS LOS PERIODOS QUE NO CUENTAN CON UN DOCUMENTO  DE FACTURA EN EL PERIODO FK_FACTURA IS NULL
                foreach ($facturas as $key => $value) {

                    $periodo = NULL;
                    $montoFactura = NULL;

                    // SE COMPRUEBA QUE LA BOLSA SEA VALIDA
                    if ($value['PK_BOLSA'] != null ) {

                        // SI EXISTEN PENDIENTES  SE RECORREN PARA VALIDAR SI ES NECESARIO SUMAR EL MONTO
                        if($value['FK_PERIODOS']) {

                            // VALIDAMOS QUE EL ULTIMO CARACTER DEL STRING NO SEA UNA COMA, ESTO ES PARA QUE AL MOMENTO DE HACER EL EXPLODE EL ARRAY SE GENERE CORRECTAMENTE
                            $value['FK_PERIODOS'] = (substr($value['FK_PERIODOS'], -1) == ",") ? substr($value['FK_PERIODOS'], 0, -1): $value['FK_PERIODOS'];
                            $fks_bolsas = explode(",", $value["FK_PERIODOS"]);

                            // VALIDAMOS DE DONDE SE SUMA EL MONTO DE LA FACTURA, SI ES UNA ASIGNACION SE RECUPERA DE LA TABLA DE PERIODOS
                            if($value["TIPO_SERVICIO"] == 1)
                            {
                                // RECUPERAMOS EL ID DE LA ASIGNACION ES NECESARIO PARA CONSTRUIR LA URL QUE MANDA  A LA ACCION VIEW, Y SI AGREGAMOS EL KEY PK_PROYECTO COMO NULL
                                $periodo = TblPeriodos::find()->where(['PK_PERIODO'  =>  $fks_bolsas[0]])->one();
                                $facturas[$cont]['PK_ASIGNACION'] = $periodo['FK_ASIGNACION'];
                                $facturas[$cont]['PK_PROYECTO'] = NULL;

                                // REALIZAMOS LA SUMATORIA DE LOS PERIODOS DEL PAQUETE
                                $montoFactura = (new \yii\db\Query())->select(['SUM(MONTO_FACTURA) AS MONTO_FACTURA, SUM(MONTO_HDE) AS MONTO_HDE, SUM(MONTO) AS MONTO_ODC '])->from('tbl_periodos')->where(['PK_PERIODO' => $fks_bolsas])->one();
                            }

                            // SI ES UN PROYECTO SE RECUPERA DE LA TABLA PROYECTO PERIODOS
                            if($value["TIPO_SERVICIO"] == 2)
                            {
                                // RECUPERAMOS EL ID DE LA ASIGNACION ES NECESARIO PARA CONSTRUIR LA URL QUE MANDA  A LA ACCION VIEW, Y SI AGREGAMOS EL KEY PK_PROYECTO COMO NULL
                                $periodo = TblProyectosPeriodos::find()->where(['PK_PROYECTO_PERIODO'  =>  $fks_bolsas[0]])->one();
                                $proyectofase = TblProyectosFases::find()->where(['PK_PROYECTO_FASE' => $periodo['FK_PROYECTO_FASE']])->one();

                                $facturas[$cont]['PK_PROYECTO'] = $proyectofase['FK_PROYECTO'];
                                $facturas[$cont]['PK_ASIGNACION'] = $periodo['FK_PROYECTO_FASE'];

                                // REALIZAMOS LA SUMATORIA DE LOS PERIODOS DEL PROYECTO PARA LA COLUMNA MONTO FACTURA
                                $montoFactura = (new \yii\db\Query())->select(['SUM(MONTO_FACTURA) AS MONTO_FACTURA, SUM(MONTO_HDE) AS MONTO_HDE, SUM(MONTO_ODC) AS MONTO_ODC'])->from('tbl_proyectos_periodos')->where(['PK_PROYECTO_PERIODO' => $fks_bolsas])->one();
                            }

                            // AGREGAMOS EL VALOR AL NUEVO ARRAY Y COLOCAMOS EL KEY PARA LA COLUMNA MONTO FACTURA DEL LISTADO, ES LA SUMATORIA DE LOS PERIDOS POR PAQUETE
                            if(!is_null($montoFactura['MONTO_HDE']) || $montoFactura['MONTO_HDE'] != "")
                            {
                                $facturas[$cont]["MONTO_FACTURA"] = $montoFactura["MONTO_HDE"];
                            }
                            else
                            {
                                $facturas[$cont]["MONTO_FACTURA"] = $montoFactura["MONTO_ODC"];
                            }
                        }
                    }

                    $cont++;
                }

                // RECUPERAMOS TODAS LAS BOLSAS PARA SUMAR EL MONTO AL TOTAL DE ACUERDO A LOS CRITERIOS DE BUSQUEDA
                $connection = \Yii::$app->db;
                $facturasBls = $connection->createCommand("CALL SP_FACTURAS_VIEWPORFACTURAR_SEL_BLS(:Bolsa, :cliente, :Limit, :Count, :Peticion)")
                        ->bindValue(':Bolsa', $Bolsa)
                        ->bindValue(':cliente', $cliente)
                        ->bindValue(':Peticion', $Peticion)
                        ->bindValue(':Limit', '')
                        ->bindValue(':Count', 0)
                        ->queryAll();
                $connection->close();

                foreach ($facturasBls as $key => $value) {

                    $periodo = NULL;
                    $montoFactura = NULL;
                    $montoTotal = NULL;

                    // SE COMPRUEBA QUE LA BOLSA SEA VALIDA
                    if ($value['PK_BOLSA'] != null ) {

                        // SI EXISTEN PENDIENTES  SE RECORREN PARA VALIDAR SI ES NECESARIO SUMAR EL MONTO
                        if($value['FK_PERIODOS']) {

                            // VALIDAMOS QUE EL ULTIMO CARACTER DEL STRING NO SEA UNA COMA, ESTO ES PARA QUE AL MOMENTO DE HACER EL EXPLODE EL ARRAY SE GENERE CORRECTAMENTE
                            $value['FK_PERIODOS'] = (substr($value['FK_PERIODOS'], -1) == ",") ? substr($value['FK_PERIODOS'], 0, -1): $value['FK_PERIODOS'];
                            $fks_bolsas = explode(",", $value["FK_PERIODOS"]);

                            // VALIDAMOS DE DONDE SE SUMA EL MONTO DE LA FACTURA, SI ES UNA ASIGNACION SE RECUPERA DE LA TABLA DE PERIODOS
                            if($value["TIPO_SERVICIO"] == 1)
                            {
                                // REALIZAMOS LA SUMATORIA DE LOS PERIODOS DEL PAQUETE
                                $montoFactura = (new \yii\db\Query())->select(['SUM(MONTO_FACTURA) AS MONTO_FACTURA, SUM(MONTO_HDE) AS MONTO_HDE, SUM(MONTO) AS MONTO_ODC '])->from('tbl_periodos')->where(['PK_PERIODO' => $fks_bolsas])->one();
                            }

                            // SI ES UN PROYECTO SE RECUPERA DE LA TABLA PROYECTO PERIODOS
                            if($value["TIPO_SERVICIO"] == 2)
                            {
                                // REALIZAMOS LA SUMATORIA DE LOS PERIODOS DEL PROYECTO PARA LA COLUMNA MONTO FACTURA
                                $montoFactura = (new \yii\db\Query())->select(['SUM(MONTO_FACTURA) AS MONTO_FACTURA, SUM(MONTO_HDE) AS MONTO_HDE, SUM(MONTO_ODC) AS MONTO_ODC'])->from('tbl_proyectos_periodos')->where(['PK_PROYECTO_PERIODO' => $fks_bolsas])->one();
                            }

                            // AGREGAMOS EL VALOR AL NUEVO ARRAY Y COLOCAMOS EL KEY PARA LA COLUMNA MONTO FACTURA DEL LISTADO, ES LA SUMATORIA DE LOS PERIDOS POR PAQUETE
                            if(!is_null($montoFactura['MONTO_HDE']) || $montoFactura['MONTO_HDE'] != "")
                            {
                                $montoTotal = $montoFactura["MONTO_HDE"];
                            }
                            else
                            {
                                $montoTotal = $montoFactura["MONTO_ODC"];
                            }

                            $total = $total + $montoTotal;
                        }
                    }
                }
            }

            $total_paginas = (($todos + 1) < 20) ? 1 : ceil(($todos + 1) / 20);

            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return ['facturas' => $facturas,
                    'total_paginas' => $total_paginas,
                    'pagina' => $actual,
                    'totalCliente' => $totalCliente,
                    'total' => $total,
                    'total_registros' => $todos,
                    'post' => $post,
                    'titulos' => $titulos,
                    'cantBolsas' => $cantBolsas,
                    'cantServicios' => $cantServicios];
        } else {
            return $this->render('pendientes_facturar');
        }
    }

    /**
     * Lists all TblFacturas models.
     * @return mixed
     */
    public function actionIndex5()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => TblFacturas::find(),
        ]);

        return $this->render('index5', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single TblFacturas model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $tamanio_pagina=9;

        $modelSubirDocFactura = new SubirArchivo();
        $modelSubirDocFactura->extensions = 'pdf, zip';
        $modelSubirDocFactura->noRequired = true;

        $modelSubirDocFacturaXML = new SubirArchivo();
        $modelSubirDocFacturaXML->extensions = 'xml, zip';
        $modelSubirDocFacturaXML->noRequired = true;

        $datos = Yii::$app->request->get();
        $connection = \Yii::$app->db;

        $PK_ASIGNACION = (isset($_GET['id'])) ? $_GET['id'] : null;
        $PK_PROYECTO = (isset($_GET['proyecto'])) ? $_GET['proyecto'] : null;

        $sql =  $connection->createCommand("CALL SP_FACTURAS_VIEW_GENERAL(:PK_ASIGNACION, :PK_PROYECTO)")
                    ->bindValue(':PK_ASIGNACION', $PK_ASIGNACION)
                    ->bindValue(':PK_PROYECTO', $PK_PROYECTO)
                    ->queryOne();
        $connection->close();

        $connection = \Yii::$app->db;
        $modelComentariosAsignaciones3 = array();
        if(!isset($_GET['proyecto']))
        {
            $modelComentariosAsignaciones3= TblBitComentariosAsignaciones::find()->where(['FK_ASIGNACION'=>$sql['PK_ASIGNACION']])->andWhere(['=','FK_ESTATUS_ASIGNACION',5])->orderBy(['FECHA_FIN' => SORT_ASC])->asArray()->all();
        }

        $modeloDocumentos = new TblDocumentos();
        $modeloDocumentosXML = new TblDocumentos();
        $modeloFactura = new TblFacturas();
        $camposOldValues = '';
        $camposNewValues = '';
        $facturaOldValues = '';
        $periodoOldValues = '';
        $documentoOldValues = '';
        $documentoXMLOldValues = '';

        if ($modeloDocumentos->load(Yii::$app->request->post())) {

            $datos = Yii::$app->request->get();
            $data = Yii::$app->request->post();

                if($data['TblDocumentos']['FK_TIPO_DOCUMENTO'] == 4) {

                $transaction = $connection->beginTransaction();//Inicio del funcionamiento de la 'Transacción' con su respectivo try/catch
                try{

                    $modelSubirDocFactura->file = UploadedFile::getInstance($modelSubirDocFactura, '[7]file');
                    if (!empty($modelSubirDocFactura->file)) {
                        $fechaHoraHoy = date('YmdHis');
                        $rutaGuardado = ($data['tipoFactura'] == '') ? '../uploads/DocumentosPeriodos/' : '../uploads/ProyectosDocumentos/';

                        $nombreFisico = $fechaHoraHoy.'_'.quitar_acentos($modelSubirDocFactura->file->basename);
                        $nombreBD = quitar_acentos($modelSubirDocFactura->file->basename);
                        $extension = $modelSubirDocFactura->upload($rutaGuardado,$nombreFisico);
                        $rutaDoc = ($data['tipoFactura'] == '') ? '/uploads/DocumentosPeriodos/' : '../uploads/ProyectosDocumentos/';
                        $pk_documento_factura='';
                    }else{
                        $pk_documento_factura= (isset($data['pk_documento_factura'])&&!empty($data['pk_documento_factura']))?$data['pk_documento_factura']:'';
                    }


                    $modelSubirDocFacturaXML->file = UploadedFile::getInstance($modelSubirDocFacturaXML, '[8]file');
                    if (!empty($modelSubirDocFacturaXML->file)) {
                        $fechaHoraHoyXML = date('YmdHis');
                        $rutaGuardadoXML = ($data['tipoFactura'] == '') ? '../uploads/DocumentosPeriodos/' : '../uploads/ProyectosDocumentos/';
                        $nombreFisicoXML = $fechaHoraHoyXML.'_'.quitar_acentos($modelSubirDocFacturaXML->file->basename);
                        $nombreBDXML = quitar_acentos($modelSubirDocFacturaXML->file->basename);
                        $extensionXML = $modelSubirDocFacturaXML->upload($rutaGuardadoXML,$nombreFisicoXML);
                        $rutaDocXML = ($data['tipoFactura'] == '') ? '/uploads/DocumentosPeriodos/' : '../uploads/ProyectosDocumentos/';
                        $pk_documento_factura_xml='';
                    }
                    else{
                        $pk_documento_factura_xml= (isset($data['pk_documento_factura_xml'])&&!empty($data['pk_documento_factura_xml']))?$data['pk_documento_factura_xml']:'';
                    }

                    if(isset($data['periodo'])){
                    foreach ($data['periodo'] as $key => $value) {
                        $pk_periodo_factura =  $value;
                        $facturaNuevaModifica = ($data['tipoFactura'] == '') ? TblPeriodos::find()->where(['PK_PERIODO' => $pk_periodo_factura])->limit(1)->one() : TblProyectosPeriodos::find()->where(['PK_PROYECTO_PERIODO' => $pk_periodo_factura])->one();
                        // $pk_documento_factura= $facturaNuevaModifica->FK_DOCUMENTO_FACTURA;
                        /**
                         * Se crea una nueva factura
                         */

                        if($facturaNuevaModifica->FK_DOCUMENTO_FACTURA==null&&empty($pk_documento_factura)){
                            if ($data['tipoFactura'] != '') {
                                $modeloDocumentos = new TblDocumentosProyectos();
                                $modeloDocumentosXML = new TblDocumentosProyectos();

                                $modeloDocumentos->FK_PROYECTO_FASE = $data['fk_proyecto_fase'];
                                $modeloDocumentos->FK_PROYECTO_PERIODO = $pk_periodo_factura;
                                $modeloDocumentos->FK_TIPO_DOCUMENTO = $data['TblDocumentos']['FK_TIPO_DOCUMENTO'];
                                $modeloDocumentos->FECHA_DOCUMENTO = isset($data['TblDocumentos']['FECHA_DOCUMENTO']) ? transform_date($data['TblDocumentos']['FECHA_DOCUMENTO'],'Y-m-d') : null;
                                $modeloDocumentos->NUM_DOCUMENTO = isset($data['TblDocumentos']['NUM_DOCUMENTO']) ? $data['TblDocumentos']['NUM_DOCUMENTO'] : null;
                                $modeloDocumentos->NUM_SP = isset($data['numSPFactura']) ? $data['numSPFactura'] : null;
                                $modeloDocumentos->COMENTARIOS = $data['comentariosFactura'];
                                $modeloDocumentos->FK_CLIENTE = ($data['FK_CLIENTE'] != '') ? $data['FK_CLIENTE'] : null;
                                $modeloDocumentos->TARIFA = isset($data['TblDocumentos']['TARIFA']) ? $data['TblDocumentos']['TARIFA'] : null;
                                $modeloDocumentos->HORAS = $data['horas_factura'][$pk_periodo_factura];
                                $modeloDocumentos->MONTO = $data['monto_factura'][$pk_periodo_factura];
                                $modeloDocumentos->FECHA_REGISTRO = date('Y-m-d H:i:s');

                                if (!empty($modelSubirDocFactura->file)) {
                                    $modeloDocumentos->NOMBRE_DOCUMENTO    = $nombreBD.'.'.$extension;
                                    $modeloDocumentos->RUTA_DOCUMENTO = $rutaDoc.$nombreFisico.'.'.$extension;
                                }
                                $modeloDocumentos->save(false);

                                $modeloDocumentosXML->FK_PROYECTO_FASE = $data['fk_proyecto_fase'];
                                $modeloDocumentosXML->FK_PROYECTO_PERIODO = $pk_periodo_factura;
                                $modeloDocumentosXML->FK_TIPO_DOCUMENTO = 5;
                                $modeloDocumentosXML->FECHA_DOCUMENTO = isset($data['TblDocumentos']['FECHA_DOCUMENTO']) ? transform_date($data['TblDocumentos']['FECHA_DOCUMENTO'],'Y-m-d') : null;
                                $modeloDocumentosXML->NUM_DOCUMENTO = isset($data['TblDocumentos']['NUM_DOCUMENTO']) ? $data['TblDocumentos']['NUM_DOCUMENTO'] : null;
                                $modeloDocumentosXML->NUM_SP = isset($data['numSPFactura']) ? $data['numSPFactura'] : null;
                                $modeloDocumentosXML->COMENTARIOS = $data['comentariosFactura'];
                                $modeloDocumentosXML->FK_CLIENTE = ($data['FK_CLIENTE'] != '') ? $data['FK_CLIENTE'] : null;
                                $modeloDocumentosXML->TARIFA = isset($data['TblDocumentos']['TARIFA']) ? $data['TblDocumentos']['TARIFA'] : null;
                                $modeloDocumentosXML->HORAS = $data['horas_factura'][$pk_periodo_factura];
                                $modeloDocumentosXML->MONTO = $data['monto_factura'][$pk_periodo_factura];
                                $modeloDocumentosXML->FECHA_REGISTRO = date('Y-m-d H:i:s');

                                if (!empty($modelSubirDocFacturaXML->file)) {
                                    $modeloDocumentosXML->NOMBRE_DOCUMENTO    = $nombreBDXML.'.'.$extensionXML;
                                    $modeloDocumentosXML->RUTA_DOCUMENTO = $rutaDocXML.$nombreFisicoXML.'.'.$extensionXML;
                                }
                                $modeloDocumentosXML->save(false);

                                $modelSeguimiento = new TblBitComentariosSeguimientoProy();
                                $modelSeguimiento->COMENTARIOS = 'Se Creo un documento Factura_'.$modeloDocumentos->NUM_DOCUMENTO.'_'.obtener_nombre_mes(date('m',strtotime($facturaNuevaModifica->FECHA_INI))).date('Y',strtotime($facturaNuevaModifica->FECHA_INI));
                                $modelSeguimiento->FECHA_REGISTRO = date('Y-m-d H:i:s');
                                $modelSeguimiento->FK_PROYECTO_FASE = $data['fk_proyecto_fase'];
                                $modelSeguimiento->FK_USUARIO = user_info()['PK_USUARIO'];
                                $modelSeguimiento->save(false);

                                $pk_documento_factura = $modeloDocumentos->PK_DOCUMENTO;
                                $pk_documento_factura_xml = $modeloDocumentosXML->PK_DOCUMENTO;
                                $modeloFactura->FK_PROYECTO_PERIODO = $pk_periodo_factura;
                                $modeloFactura->FK_PROYECTO_DOC_FACTURA = $pk_documento_factura;
                                $modeloFactura->FECHA_EMISION         = !empty($data['TblFacturas']['FECHA_EMISION']) ? transform_date($data['TblFacturas']['FECHA_EMISION'],'Y-m-d') : null;
                                $modeloFactura->FECHA_ENTREGA_CLIENTE         = !empty($data['TblFacturas']['FECHA_ENTREGA_CLIENTE']) ? transform_date($data['TblFacturas']['FECHA_ENTREGA_CLIENTE'],'Y-m-d') : null;
                                $modeloFactura->NUMERO_IR             = isset($data['TblFacturas']['NUMERO_IR']) ? $data['TblFacturas']['NUMERO_IR'] : null;
                                $modeloFactura->FK_ESTATUS            = 1;
                                $modeloFactura->FK_SERVICIO           = isset($PK_PROYECTO) ? 2 : 1;
                                $modeloFactura->FK_PORCENTAJE         = 1;
                                $modeloFactura->TOTAL_FACTURABLE      = $data['total_facturable'][$pk_periodo_factura];
                                $modeloFactura->COMENTARIOS           = isset($data['comentariosFactura']) ? $data['comentariosFactura'] : null;
                                $modeloFactura->save(false);

                                //BITACORA
                                $descripcionBitacora = 'TIPO_DOCUMENTO=FACTURA, NUM_DOCUMENTO='.$modeloDocumentos->NUM_DOCUMENTO;
                                user_log_bitacora_contabilidad($descripcionBitacora,'Creacion Documento Factura en Periodos',$pk_periodo_factura);

                                $descripcionBitacora = 'TIPO_DOCUMENTO=XML, NUM_DOCUMENTO='.$modeloDocumentosXML->NUM_DOCUMENTO;
                                user_log_bitacora_contabilidad($descripcionBitacora,'Creacion Documento XML en Periodos',$pk_documento_factura_xml);

                                $descripcionBitacora = 'FK_DOC_FACTURA='.$modeloFactura->FK_PROYECTO_DOC_FACTURA.', FECHA_EMISION='.$modeloFactura->FECHA_EMISION.', FECHA_ENTREGA_CLIENTE='.$modeloFactura->FECHA_ENTREGA_CLIENTE.', FECHA_INGRESO_BANCO='.$modeloFactura->FECHA_INGRESO_BANCO.', COMENTARIOS='.$modeloFactura->COMENTARIOS;
                                user_log_bitacora_contabilidad($descripcionBitacora,'Cargar Información de Factura',$modeloFactura->FK_PERIODO);

                                // AGREGADO PARA MODIFICAR LA TABLA tbl_bit_bls_docs PARA AGREGAR LA BANDERA DE QUE EL PAQUETE DE LA ASIGNACION YA FUE FACTURADA
                                if(isset($datos['bls']))
                                {
                                    $bitBlsDocs = TblBitBlsDocs::find()->where(['FK_BOLSA' => $datos['bls']])->asArray()->all();
                                    foreach($bitBlsDocs as $paquete)
                                    {
                                        $periodosString = '';
                                        $fks_bolsas = array();

                                        $periodosString = (substr($paquete['FK_PROYECTO_PERIODOS'], -1) == ",") ? substr($paquete['FK_PROYECTO_PERIODOS'], 0, -1): $paquete['FK_PROYECTO_PERIODOS'];
                                        $fks_bolsas = explode(",", $periodosString);
                                        if(in_array($datos['PK_PERIODO'], $fks_bolsas))
                                        {
                                            $model = TblBitBlsDocs::find()->where(['PK_BIT_BLS' => $paquete['PK_BIT_BLS']])->limit(1)->one();
                                            $model->FACTURADO = 1;
                                            $model->save(false);
                                            $res = $model;
                                        }
                                    }
                                }
                            } else {

                                $modeloDocumentos->FECHA_DOCUMENTO      = isset($data['TblDocumentos']['FECHA_DOCUMENTO']) ? transform_date($data['TblDocumentos']['FECHA_DOCUMENTO'],'Y-m-d') : null;
                                $modeloDocumentos->NUM_DOCUMENTO        = isset($data['TblDocumentos']['NUM_DOCUMENTO']) ? $data['TblDocumentos']['NUM_DOCUMENTO'] : null;
                                $modeloDocumentos->NUM_SP               = isset($data['numSPFactura']) ? $data['numSPFactura'] : null;
                                $modeloDocumentos->TARIFA               = isset($data['TblDocumentos']['TARIFA']) ? $data['TblDocumentos']['TARIFA'] : null;
                                $modeloDocumentos->FK_RAZON_SOCIAL      = isset($data['TblDocumentos']['FK_RAZON_SOCIAL']) ? $data['TblDocumentos']['FK_RAZON_SOCIAL'] : null;
                                $modeloDocumentos->FK_ASIGNACION        = $datos['id'];
                                $modeloDocumentos->FK_TIPO_DOCUMENTO    = isset($data['TblDocumentos']['FK_TIPO_DOCUMENTO']) ? $data['TblDocumentos']['FK_TIPO_DOCUMENTO'] : null;
                                $modeloDocumentos->FK_UNIDAD_NEGOCIO    = isset($data['TblDocumentos']['FK_UNIDAD_NEGOCIO']) ? $data['TblDocumentos']['FK_UNIDAD_NEGOCIO'] : null;
                                $modeloDocumentos->FECHA_REGISTRO       = date('Y-m-d H:i:s');
                                $modeloDocumentos->FK_CLIENTE           = $data['FK_CLIENTE'];
                                $modeloDocumentos->CONSECUTIVO_TIPO_DOC = $data['consecutivo'];

                                if (!empty($modelSubirDocFactura->file)) {
                                    $modeloDocumentos->NOMBRE_DOCUMENTO    = $nombreBD.'.'.$extension;
                                    $modeloDocumentos->DOCUMENTO_UBICACION = $rutaDoc.$nombreFisico.'.'.$extension;
                                }
                                $modeloDocumentos->save(false);

                                $modeloDocumentosXML->FECHA_DOCUMENTO      = isset($data['TblDocumentos']['FECHA_DOCUMENTO']) ? transform_date($data['TblDocumentos']['FECHA_DOCUMENTO'],'Y-m-d') : null;
                                $modeloDocumentosXML->NUM_DOCUMENTO        = isset($data['TblDocumentos']['NUM_DOCUMENTO']) ? $data['TblDocumentos']['NUM_DOCUMENTO'] : null;
                                $modeloDocumentosXML->NUM_SP               = isset($data['numSPFactura']) ? $data['numSPFactura'] : null;
                                $modeloDocumentosXML->TARIFA               = isset($data['TblDocumentos']['TARIFA']) ? $data['TblDocumentos']['TARIFA'] : null;
                                $modeloDocumentosXML->FK_RAZON_SOCIAL      = isset($data['TblDocumentos']['FK_RAZON_SOCIAL']) ? $data['TblDocumentos']['FK_RAZON_SOCIAL'] : null;
                                $modeloDocumentosXML->FK_ASIGNACION        = $datos['id'];
                                $modeloDocumentosXML->FK_TIPO_DOCUMENTO    = 5;
                                $modeloDocumentosXML->FK_UNIDAD_NEGOCIO    = isset($data['TblDocumentos']['FK_UNIDAD_NEGOCIO']) ? $data['TblDocumentos']['FK_UNIDAD_NEGOCIO'] : null;
                                $modeloDocumentosXML->FECHA_REGISTRO       = date('Y-m-d H:i:s');
                                $modeloDocumentosXML->FK_CLIENTE           = $data['FK_CLIENTE'];
                                $modeloDocumentosXML->CONSECUTIVO_TIPO_DOC = $data['consecutivo'];

                                if (!empty($modelSubirDocFacturaXML->file)) {
                                    $modeloDocumentosXML->NOMBRE_DOCUMENTO    = $nombreBDXML.'.'.$extensionXML;
                                    $modeloDocumentosXML->DOCUMENTO_UBICACION = $rutaDocXML.$nombreFisicoXML.'.'.$extensionXML;
                                }
                                $modeloDocumentosXML->save(false);

                                $modelSeguimiento = new TblAsignacionesSeguimiento();
                                $modelSeguimiento->COMENTARIOS = 'Modificaci&oacute;n de documento Factura_'.$modeloDocumentos->NUM_DOCUMENTO.'_'.obtener_nombre_mes(date('m',strtotime($facturaNuevaModifica->FECHA_INI))).date('Y',strtotime($facturaNuevaModifica->FECHA_INI));
                                $modelSeguimiento->FECHA_REGISTRO = date('Y-m-d H:i:s');
                                $modelSeguimiento->FK_ASIGNACION = $modeloDocumentos->FK_ASIGNACION;
                                $modelSeguimiento->FK_USUARIO = user_info()['PK_USUARIO'];
                                $modelSeguimiento->save(false);

                                $pk_documento_factura=  $modeloDocumentos->PK_DOCUMENTO;
                                $pk_documento_factura_xml=  $modeloDocumentosXML->PK_DOCUMENTO;

                                $modeloFactura->FK_PERIODO            = $pk_periodo_factura;
                                $modeloFactura->FK_DOC_FACTURA        = $pk_documento_factura;
                                $modeloFactura->FECHA_EMISION         = !empty($data['TblFacturas']['FECHA_EMISION']) ? transform_date($data['TblFacturas']['FECHA_EMISION'],'Y-m-d') : null;
                                $modeloFactura->FECHA_ENTREGA_CLIENTE = !empty($data['TblFacturas']['FECHA_ENTREGA_CLIENTE']) ? transform_date($data['TblFacturas']['FECHA_ENTREGA_CLIENTE'],'Y-m-d') : null;
                                $modeloFactura->FECHA_INGRESO_BANCO   = null;
                                $modeloFactura->FECHA_RECEPCION_IR    = null;
                                $modeloFactura->CONTACTO_ENTREGA      = isset($data['TblFacturas']['CONTACTO_ENTREGA']) ? $data['TblFacturas']['CONTACTO_ENTREGA'] : null;
                                $modeloFactura->FK_SERVICIO           = isset($PK_PROYECTO) ? 2 : 1;
                                $modeloFactura->NUMERO_IR             = isset($data['TblFacturas']['NUMERO_IR']) ? $data['TblFacturas']['NUMERO_IR'] : null;
                                $modeloFactura->FK_ESTATUS            = 1;
                                $modeloFactura->FK_PORCENTAJE         = 1;
                                $modeloFactura->TOTAL_FACTURABLE      = $data['total_facturable'][$pk_periodo_factura];
                                $modeloFactura->COMENTARIOS           = isset($data['comentariosFactura']) ? $data['comentariosFactura'] : null;
                                $modeloFactura->save(false);
                                //dd($modeloFactura);
                                //Bitacora para crear nuevo registro de un Documento Factura en Periodos.
                                $descripcionBitacora = 'FK_ASIGNACION='.$modeloDocumentos->FK_ASIGNACION.', TIPO_DOCUMENTO=FACTURA, NUM_DOCUMENTO='.$modeloDocumentos->NUM_DOCUMENTO.', FK_CLIENTE='.$data['FK_CLIENTE'];
                                user_log_bitacora_contabilidad($descripcionBitacora,'Modificar Documento Factura en Periodos',$pk_periodo_factura);

                                $descripcionBitacora = 'FK_ASIGNACION='.$modeloDocumentosXML->FK_ASIGNACION.', TIPO_DOCUMENTO=XML, NUM_DOCUMENTO='.$modeloDocumentosXML->NUM_DOCUMENTO.', FK_CLIENTE='.$data['FK_CLIENTE'];
                                user_log_bitacora_contabilidad($descripcionBitacora,'Modificar Documento XML en Periodos',$pk_documento_factura_xml);

                                $descripcionBitacora = 'FK_DOC_FACTURA='.$modeloFactura->FK_DOC_FACTURA.', FECHA_EMISION='.$modeloFactura->FECHA_EMISION.', FECHA_ENTREGA_CLIENTE='.$modeloFactura->FECHA_ENTREGA_CLIENTE.', FECHA_INGRESO_BANCO='.$modeloFactura->FECHA_INGRESO_BANCO.', COMENTARIOS='.$modeloFactura->COMENTARIOS;
                                user_log_bitacora_contabilidad($descripcionBitacora,'Cargar Información de Factura',$modeloFactura->FK_PERIODO);

                                // AGREGADO PARA MODIFICAR LA TABLA tbl_bit_bls_docs PARA AGREGAR LA BANDERA DE QUE EL PAQUETE DEL PROYECTO YA FUE FACTURADA
                                if(isset($datos['bls']))
                                {
                                    $bitBlsDocs = TblBitBlsDocs::find()->where(['FK_BOLSA' => $datos['bls']])->asArray()->all();
                                    foreach($bitBlsDocs as $paquete)
                                    {
                                        $periodosString = '';
                                        $fks_bolsas = array();

                                        $periodosString = (substr($paquete['FK_PERIODOS'], -1) == ",") ? substr($paquete['FK_PERIODOS'], 0, -1): $paquete['FK_PERIODOS'];
                                        $fks_bolsas = explode(",", $periodosString);

                                        if(in_array($datos['PK_PERIODO'], $fks_bolsas))
                                        {
                                            $model = TblBitBlsDocs::find()->where(['PK_BIT_BLS' => $paquete['PK_BIT_BLS']])->limit(1)->one();
                                            $model->FACTURADO = 1;
                                            $model->save(false);
                                            $res = $model;
                                        }
                                    }
                                }

                                /* INICIO HRIBI 13/07/2016 - Se envía correo a Iselda y a Gaby notificando que se han dado de alta el PDF y el XML de una Factura */
                                if(isset($nombreBD)){
                                    $modeloDocumentoFACTURA= TblDocumentos::findOne($pk_documento_factura);
                                    $modeloDocumentoXML= TblDocumentos::findOne($pk_documento_factura_xml);
                                    $ubicacion_doc_factura= $modeloDocumentoFACTURA->DOCUMENTO_UBICACION;
                                    $ubicacion_doc_xml= $modeloDocumentoXML->DOCUMENTO_UBICACION;

                                    // INICIO HRIBI - array para concatenar en el asunto del correo, el nombre del mes perteneciente al periodo donde se guarda el doc factura.
                                    $arrayMeses = array("01" => "Enero",
                                                        "02" => "Febrero",
                                                        "03" => "Marzo",
                                                        "04" => "Abril",
                                                        "05" => "Mayo",
                                                        "06" => "Junio",
                                                        "07" => "Julio",
                                                        "08" => "Agosto",
                                                        "09" => "Septiembre",
                                                        "10" => "Octubre",
                                                        "11" => "Noviembre",
                                                        "12" => "Diciembre", );
                                    $mesPeriodo = date("m",strtotime($facturaNuevaModifica->FECHA_INI));
                                    // FIN HRIBI

                                    $is_pr= get_config('CONFIG','PRODUCCION');
                                    $de= get_config('PERIODOS','CORREO_REMITENTE_FACTURA');
                                    $prefix_correo = '';
                                    if($is_pr){

                                        $arr_validar_factura_1= explode(',',get_config('PERIODOS','PARA_FACTURA_BTN'));

                                        //Banorte
                                        // if($sql['RFC']=='BMN930209927'||$sql['RFC']=='ITA081127UZ1'){
                                        if (in_array($sql['RFC'],$arr_validar_factura_1)) {
                                            //$para = array('jorge.mendiola@eisei.net.mx','nestor.tristan@eisei.net.mx','maria.delgado@eisei.net.mx');
                                            $prefix_correo= get_config('PERIODOS','PREFIX_BANORTE');
                                            $para= explode(',',get_config('PERIODOS','CORREO_DESTINO_FACTURA_OTROS'));
                                        }else{
                                            if($sql['RFC']==get_config('PERIODOS','IS_IBM')){
                                                $prefix_correo= get_config('PERIODOS','PREFIX_IBM');
                                            }
                                            //$para = array('jorge.mendiola@eisei.net.mx','maria.delgado@eisei.net.mx');

                                            $para= explode(',',get_config('PERIODOS','CORREO_DESTINO_FACTURA_OTROS'));

                                        }

                                    }else{
                                        $para= explode(',',get_config('PERIODOS','CORREO_DESTINO_FACTURA_OTROS'));
                                    }
                                    // INICIO MOD HRIBI - Concatenación en el asunto del correo del número del documento, mes y año pertenecientes al periodo donde se guarda el doc HdE.
                                    $asunto= $prefix_correo.' Documentos de Facturación '.'Factura_'.$modeloDocumentoFACTURA->NUM_DOCUMENTO.'_'.$arrayMeses[$mesPeriodo].date("Y",strtotime($facturaNuevaModifica->FECHA_INI));;
                                    // FIN MOD HRIBI
                                    $nombre_usuario= user_info()['NOMBRE_COMPLETO'];

                                    $mensaje='<style>p, ul, li {font-family: Arial, Helvetica, sans-serif; font-size: 12px;}</style>
                                        <p>Buen d&iacute;a</p>
                                        <p>Adjunto PDF y XML registrados en la facturaci&oacute;n</p>
                                        <p>Saludos y Gracias.</p>';

                                    $enviado = send_mail($de,$para, $asunto, $mensaje,['..'.$ubicacion_doc_factura,'..'.$ubicacion_doc_xml]);
                                }
                                /* FIN HRIBI 13/07/2016 */
                            }

                        }elseif($data['postTypeFACTURA'] != 'create'){
                            /**
                             * Se modifica una factura ya existente
                             */

                            $facturaOldValues = ($data['tipoFactura'] == '') ? $connection->createCommand("SELECT * FROM tbl_facturas f WHERE f.FK_DOC_FACTURA =".$data['pk_documento_factura'])->queryOne() : $connection->createCommand("SELECT * FROM tbl_facturas f WHERE f.FK_PROYECTO_DOC_FACTURA =".$data['pk_documento_factura'])->queryOne();
                            $periodoOldValues = ($data['tipoFactura'] == '') ? $connection->createCommand("SELECT * FROM tbl_periodos p WHERE p.PK_PERIODO =".$facturaOldValues['FK_PERIODO'])->queryOne() : $connection->createCommand("SELECT * FROM tbl_proyectos_periodos p WHERE p.PK_PROYECTO_PERIODO =".$facturaOldValues['FK_PROYECTO_PERIODO'])->queryOne();
                            $documentoOldValues = ($data['tipoFactura'] == '') ? $connection->createCommand("SELECT * FROM tbl_documentos d WHERE d.PK_DOCUMENTO =".$periodoOldValues['FK_DOCUMENTO_FACTURA'])->queryOne() : $connection->createCommand("SELECT * FROM tbl_documentos_proyectos d WHERE d.PK_DOCUMENTO =".$periodoOldValues['FK_DOCUMENTO_FACTURA'])->queryOne();
                            if(!empty($periodoOldValues['FK_DOCUMENTO_FACTURA_XML'])){
                                $documentoXMLOldValues = ($data['tipoFactura'] == '') ? $connection->createCommand("SELECT * FROM tbl_documentos d WHERE d.PK_DOCUMENTO =".$periodoOldValues['FK_DOCUMENTO_FACTURA_XML'])->queryOne() : $connection->createCommand("SELECT * FROM tbl_documentos_proyectos d WHERE d.PK_DOCUMENTO =".$periodoOldValues['FK_DOCUMENTO_FACTURA_XML'])->queryOne();
                            }else{
                                $documentoXMLOldValues = '';
                            }

                            /*Condición para verificar si al actualizar los datos se esta ingresando un documento nuevo*/
                            if(empty($pk_documento_factura)){
                                if (!empty($modelSubirDocFactura->file)) {
                                    if($data['tipoFactura'] == '') {
                                        $modeloDocumentos = new TblDocumentos();
                                        $modeloDocumentos->CONSECUTIVO_TIPO_DOC = $data['consecutivo'];
                                        $modeloDocumentos->FK_ASIGNACION        = $datos['id'];
                                        $modeloDocumentos->NOMBRE_DOCUMENTO     = $nombreBD.'.'.$extension;
                                        $modeloDocumentos->DOCUMENTO_UBICACION  = $rutaDoc.$nombreFisico.'.'.$extension;
                                        $modeloDocumentos->FK_TIPO_DOCUMENTO    = isset($data['TblDocumentos']['FK_TIPO_DOCUMENTO']) ? $data['TblDocumentos']['FK_TIPO_DOCUMENTO'] : null;
                                    } else {
                                        $modeloDocumentos = new TblDocumentosProyectos();
                                        $modeloDocumentos->NOMBRE_DOCUMENTO = $nombreBD.'.'.$extension;
                                        $modeloDocumentos->RUTA_DOCUMENTO = $rutaDoc.$nombreFisico.'.'.$extension;
                                        $modeloDocumentos->FK_TIPO_DOCUMENTO = isset($data['TblDocumentos']['FK_TIPO_DOCUMENTO']) ? $data['TblDocumentos']['FK_TIPO_DOCUMENTO'] : null;
                                    }
                                }else{
                                    $modeloDocumentos = ($data['tipoFactura'] == '') ? TblDocumentos::findOne($facturaNuevaModifica->FK_DOCUMENTO_FACTURA) : TblDocumentosProyectos::findOne($facturaNuevaModifica->FK_DOCUMENTO_FACTURA);
                                }

                                if($data['tipoFactura'] == '') {
                                    $modeloDocumentos->FECHA_DOCUMENTO      = isset($data['TblDocumentos']['FECHA_DOCUMENTO']) ? transform_date($data['TblDocumentos']['FECHA_DOCUMENTO'],'Y-m-d') : null;
                                    $modeloDocumentos->NUM_DOCUMENTO        = isset($data['TblDocumentos']['NUM_DOCUMENTO']) ? $data['TblDocumentos']['NUM_DOCUMENTO'] : null;
                                    $modeloDocumentos->NUM_SP               = isset($data['numSPFactura']) ? $data['numSPFactura'] : null;
                                    $modeloDocumentos->TARIFA               = isset($data['TblDocumentos']['TARIFA']) ? $data['TblDocumentos']['TARIFA'] : null;
                                    $modeloDocumentos->FK_RAZON_SOCIAL      = isset($data['TblDocumentos']['FK_RAZON_SOCIAL']) ? $data['TblDocumentos']['FK_RAZON_SOCIAL'] : null;
                                    $modeloDocumentos->FK_UNIDAD_NEGOCIO    = isset($data['TblDocumentos']['FK_UNIDAD_NEGOCIO']) ? $data['TblDocumentos']['FK_UNIDAD_NEGOCIO'] : null;
                                    $modeloDocumentos->FECHA_REGISTRO       = date('Y-m-d H:i:s');
                                    $modeloDocumentos->FK_CLIENTE           = $data['FK_CLIENTE'];
                                    $modeloDocumentos->CONSECUTIVO_TIPO_DOC = $data['consecutivo'];
                                    $modeloDocumentos->save(false);

                                    //Bitacora para modificación de Documento Factura en Periodos.
                                    $descripcionBitacora = 'FK_ASIGNACION='.$modeloDocumentos->FK_ASIGNACION.', TIPO_DOCUMENTO=FACTURA, NUM_DOCUMENTO='.$modeloDocumentos->NUM_DOCUMENTO.', FK_CLIENTE='.$modeloDocumentos->FK_CLIENTE;
                                    user_log_bitacora_contabilidad($descripcionBitacora,'Cargar Documento Factura en Periodos',$facturaNuevaModifica->FK_DOCUMENTO_FACTURA);

                                    $modelSeguimiento = new TblAsignacionesSeguimiento();
                                    $modelSeguimiento->COMENTARIOS    = 'Carga de documento Factura_'.$modeloDocumentos->NUM_DOCUMENTO.'_'.obtener_nombre_mes(date('m',strtotime($facturaNuevaModifica->FECHA_INI))).date('Y',strtotime($facturaNuevaModifica->FECHA_INI));
                                    $modelSeguimiento->FECHA_REGISTRO = date('Y-m-d H:i:s');
                                    $modelSeguimiento->FK_ASIGNACION  = $modeloDocumentos->FK_ASIGNACION;
                                    $modelSeguimiento->FK_USUARIO     = user_info()['PK_USUARIO'];
                                    $modelSeguimiento->save(false);

                                    if (!empty($modelSubirDocFactura->file)) {
                                        $modeloDocumentos->NOMBRE_DOCUMENTO    = $nombreBD.'.'.$extension;
                                        $modeloDocumentos->DOCUMENTO_UBICACION = $rutaDoc.$nombreFisico.'.'.$extension;
                                    }
                                    $modeloDocumentos->save(false);
                                    $pk_documento_factura = $modeloDocumentos->PK_DOCUMENTO;

                                } else {
                                    $modeloDocumentos->FK_PROYECTO_FASE = $data['fk_proyecto_fase'];
                                    $modeloDocumentos->FK_PROYECTO_PERIODO = $pk_periodo_factura;
                                    $modeloDocumentos->FK_TIPO_DOCUMENTO = $data['TblDocumentos']['FK_TIPO_DOCUMENTO'];
                                    $modeloDocumentos->FECHA_DOCUMENTO = isset($data['TblDocumentos']['FECHA_DOCUMENTO']) ? transform_date($data['TblDocumentos']['FECHA_DOCUMENTO'],'Y-m-d') : null;
                                    $modeloDocumentos->NUM_DOCUMENTO = isset($data['TblDocumentos']['NUM_DOCUMENTO']) ? $data['TblDocumentos']['NUM_DOCUMENTO'] : null;
                                    $modeloDocumentos->NUM_SP = isset($data['numSPFactura']) ? $data['numSPFactura'] : null;
                                    $modeloDocumentos->COMENTARIOS = $data['comentariosFactura'];
                                    $modeloDocumentos->FK_CLIENTE = ($data['FK_CLIENTE'] != '') ? $data['FK_CLIENTE'] : null;
                                    $modeloDocumentos->TARIFA = isset($data['TblDocumentos']['TARIFA']) ? $data['TblDocumentos']['TARIFA'] : null;
                                    $modeloDocumentos->HORAS = $data['horas_factura'][$pk_periodo_factura];
                                    $modeloDocumentos->MONTO = $data['monto_factura'][$pk_periodo_factura];
                                    $modeloDocumentos->FECHA_REGISTRO = date('Y-m-d H:i:s');

                                    $descripcionBitacora = 'TIPO_DOCUMENTO=FACTURA, NUM_DOCUMENTO='.$modeloDocumentos->NUM_DOCUMENTO;
                                    user_log_bitacora_contabilidad($descripcionBitacora,'Actualiza Documento Factura en Periodos',$facturaNuevaModifica->FK_DOCUMENTO_FACTURA);

                                    $modelSeguimiento = new TblBitComentariosSeguimientoProy();
                                    $modelSeguimiento->COMENTARIOS = 'Modificaci&oacute;n de documento Factura_'.$modeloDocumentos->NUM_DOCUMENTO.'_'.obtener_nombre_mes(date('m',strtotime($facturaNuevaModifica->FECHA_INI))).date('Y',strtotime($facturaNuevaModifica->FECHA_INI));
                                    $modelSeguimiento->FECHA_REGISTRO = date('Y-m-d H:i:s');
                                    $modelSeguimiento->FK_PROYECTO_FASE = $data['fk_proyecto_fase'];
                                    $modelSeguimiento->FK_USUARIO = user_info()['PK_USUARIO'];
                                    $modelSeguimiento->save(false);

                                    $modeloDocumentos->save(false);
                                    $pk_documento_factura = $modeloDocumentos->PK_DOCUMENTO;

                                }

                            }else{/*Si al actualizar no se esta dando de alta un documento nuevo, se modifican los datos del documento ya existente*/
                                $modeloDocumentos = ($data['tipoFactura'] == '') ? TblDocumentos::findOne($pk_documento_factura) : TblDocumentosProyectos::findOne($pk_documento_factura);
                                //var_dump($modeloDocumentos);

                                $modeloDocumentos->NUM_DOCUMENTO     = isset($data['TblDocumentos']['NUM_DOCUMENTO']) ? $data['TblDocumentos']['NUM_DOCUMENTO'] : null;
                                $modeloDocumentos->FK_RAZON_SOCIAL   = isset($data['TblDocumentos']['FK_RAZON_SOCIAL']) ? $data['TblDocumentos']['FK_RAZON_SOCIAL'] : null;
                                $modeloDocumentos->save(false);
                                //dd($modeloDocumentos);
                                //Bitacora para modificación de Documento Factura en Periodos.
                                if($data['tipoFactura'] == '') {
                                    $descripcionBitacora = 'FK_ASIGNACION='.$modeloDocumentos->FK_ASIGNACION.', TIPO_DOCUMENTO=FACTURA, NUM_DOCUMENTO='.$modeloDocumentos->NUM_DOCUMENTO.', FK_CLIENTE='.$modeloDocumentos->FK_CLIENTE;
                                    user_log_bitacora_contabilidad($descripcionBitacora,'Actualiza Documento Factura en Periodos',$facturaNuevaModifica->FK_DOCUMENTO_FACTURA);

                                    $modelSeguimiento = new TblAsignacionesSeguimiento();
                                    $modelSeguimiento->COMENTARIOS    = 'Modificación de documento Factura_'.$modeloDocumentos->NUM_DOCUMENTO.'_'.obtener_nombre_mes(date('m',strtotime($facturaNuevaModifica->FECHA_INI))).date('Y',strtotime($facturaNuevaModifica->FECHA_INI));
                                    $modelSeguimiento->FECHA_REGISTRO = date('Y-m-d H:i:s');
                                    $modelSeguimiento->FK_ASIGNACION  = $modeloDocumentos->FK_ASIGNACION;
                                    $modelSeguimiento->FK_USUARIO     = user_info()['PK_USUARIO'];
                                    $modelSeguimiento->save(false);
                                } else {
                                    $descripcionBitacora = 'TIPO_DOCUMENTO=FACTURA, NUM_DOCUMENTO='.$modeloDocumentos->NUM_DOCUMENTO;
                                    user_log_bitacora_contabilidad($descripcionBitacora,'Actualiza Documento Factura en Periodos',$facturaNuevaModifica->FK_DOCUMENTO_FACTURA);

                                    $modelSeguimiento = new TblBitComentariosSeguimientoProy();
                                    $modelSeguimiento->COMENTARIOS = 'Modificaci&oacute;n de documento Factura_'.$modeloDocumentos->NUM_DOCUMENTO.'_'.obtener_nombre_mes(date('m',strtotime($facturaNuevaModifica->FECHA_INI))).date('Y',strtotime($facturaNuevaModifica->FECHA_INI));
                                    $modelSeguimiento->FECHA_REGISTRO = date('Y-m-d H:i:s');
                                    $modelSeguimiento->FK_PROYECTO_FASE = $data['fk_proyecto_fase'];
                                    $modelSeguimiento->FK_USUARIO = user_info()['PK_USUARIO'];
                                    $modelSeguimiento->save(false);
                                }

                            }

                            if(empty($pk_documento_factura_xml)){

                                if (!empty($modelSubirDocFacturaXML->file)) {
                                    if($data['tipoFactura'] == '') {
                                        $modeloDocumentosXML = new TblDocumentos();
                                        $modeloDocumentosXML->CONSECUTIVO_TIPO_DOC = $data['consecutivo'];
                                        $modeloDocumentosXML->FK_ASIGNACION        = $datos['id'];
                                        $modeloDocumentosXML->NOMBRE_DOCUMENTO     = $nombreBDXML.'.'.$extensionXML;
                                        $modeloDocumentosXML->DOCUMENTO_UBICACION  = $rutaDocXML.$nombreFisicoXML.'.'.$extensionXML;
                                        $modeloDocumentosXML->FK_TIPO_DOCUMENTO    = 5;
                                    } else {
                                        $modeloDocumentosXML = new TblDocumentosProyectos();
                                        $modeloDocumentosXML->NOMBRE_DOCUMENTO     = $nombreBDXML.'.'.$extensionXML;
                                        $modeloDocumentosXML->RUTA_DOCUMENTO  = $rutaDocXML.$nombreFisicoXML.'.'.$extensionXML;
                                        $modeloDocumentosXML->FK_TIPO_DOCUMENTO    = 5;
                                    }

                                }else{
                                    $modeloDocumentosXML = ($data['tipoFactura'] == '') ? TblDocumentos::findOne($facturaNuevaModifica->FK_DOCUMENTO_FACTURA_XML) : TblDocumentosProyectos::findOne($facturaNuevaModifica->FK_DOCUMENTO_FACTURA_XML);
                                }
                                if($modeloDocumentosXML){
                                    $modeloDocumentosXML->FECHA_DOCUMENTO   = isset($data['TblDocumentos']['FECHA_DOCUMENTO']) ? transform_date($data['TblDocumentos']['FECHA_DOCUMENTO'],'Y-m-d') : null;
                                    $modeloDocumentosXML->NUM_DOCUMENTO     = isset($data['TblDocumentos']['NUM_DOCUMENTO']) ? $data['TblDocumentos']['NUM_DOCUMENTO'] : null;
                                    $modeloDocumentosXML->TARIFA            = isset($data['TblDocumentos']['TARIFA']) ? $data['TblDocumentos']['TARIFA'] : null;
                                    $modeloDocumentosXML->FK_RAZON_SOCIAL   = isset($data['TblDocumentos']['FK_RAZON_SOCIAL']) ? $data['TblDocumentos']['FK_RAZON_SOCIAL'] : null;
                                    if($data['tipoFactura'] == '') {
                                        $modeloDocumentosXML->FK_UNIDAD_NEGOCIO = isset($data['TblDocumentos']['FK_UNIDAD_NEGOCIO']) ? $data['TblDocumentos']['FK_UNIDAD_NEGOCIO'] : null;
                                        $modeloDocumentosXML->FK_CLIENTE = $data['FK_CLIENTE'];
                                    } else {
                                        $modeloDocumentosXML->FECHA_REGISTRO = date('Y-m-d H:i:s');
                                    }

                                    $modeloDocumentosXML->save(false);
                                    $pk_documento_factura_xml = $modeloDocumentosXML->PK_DOCUMENTO;

                                    //Bitacora para modificación de Documento XML en Periodos.
                                    if($data['tipoFactura'] == '') {
                                        $descripcionBitacora = 'FK_ASIGNACION='.$modeloDocumentosXML->FK_ASIGNACION.', TIPO_DOCUMENTO=FACTURA, NUM_DOCUMENTO='.$modeloDocumentosXML->NUM_DOCUMENTO.', FK_CLIENTE='.$modeloDocumentos->FK_CLIENTE;
                                        user_log_bitacora_contabilidad($descripcionBitacora,'Cargar Documento XML en Periodos',$facturaNuevaModifica->FK_DOCUMENTO_FACTURA);
                                    } else {
                                        $descripcionBitacora = 'TIPO_DOCUMENTO=FACTURA, NUM_DOCUMENTO='.$modeloDocumentosXML->NUM_DOCUMENTO;
                                        user_log_bitacora_contabilidad($descripcionBitacora,'Cargar Documento XML en Periodos',$facturaNuevaModifica->FK_DOCUMENTO_FACTURA);
                                    }
                                }
                            }
                            //var_dump("pk_periodo_factura: ");
                            //var_dump($pk_periodo_factura);
                            $modeloFacturaUpd = ($data['tipoFactura'] == '') ? TblFacturas::find()->where(['=','FK_PERIODO', $pk_periodo_factura])->limit(1)->one() : TblFacturas::find()->where(['=','FK_PROYECTO_PERIODO', $pk_periodo_factura])->limit(1)->one();
                            //var_dump("modeloFacturaUpd -> obtiene la factura si se va a actualizar");
                            //var_dump("modeloFacturaUpd: ");
                            //var_dump($modeloFacturaUpd);

                            if($modeloFacturaUpd){
                                //var_dump("Se actualiza factura en el periodo");
                                if ($data['tipoFactura'] == '') {
                                    $modeloFacturaUpd->FK_DOC_FACTURA = $pk_documento_factura;
                                } else {
                                    $modeloFacturaUpd->FK_PROYECTO_DOC_FACTURA = $pk_documento_factura;
                                }
                                $modeloFacturaUpd->FECHA_EMISION         = !empty($data['TblFacturas']['FECHA_EMISION']) ? transform_date($data['TblFacturas']['FECHA_EMISION'],'Y-m-d') : null;
                                $modeloFacturaUpd->FECHA_ENTREGA_CLIENTE = !empty($data['TblFacturas']['FECHA_ENTREGA_CLIENTE']) ? transform_date($data['TblFacturas']['FECHA_ENTREGA_CLIENTE'],'Y-m-d') : null;
                                $modeloFacturaUpd->FECHA_INGRESO_BANCO   = !empty($data['TblFacturas']['FECHA_INGRESO_BANCO']) ? transform_date($data['TblFacturas']['FECHA_INGRESO_BANCO'],'Y-m-d') : null;
                                $modeloFacturaUpd->FECHA_RECEPCION_IR    = !empty($data['TblFacturas']['FECHA_RECEPCION_IR']) ? transform_date($data['TblFacturas']['FECHA_RECEPCION_IR'],'Y-m-d') : null;
                                $modeloFacturaUpd->CONTACTO_ENTREGA      = isset($data['TblFacturas']['CONTACTO_ENTREGA']) ? $data['TblFacturas']['CONTACTO_ENTREGA'] : null;
                                $modeloFacturaUpd->FK_SERVICIO           = isset($PK_PROYECTO) ? 2 : 1;
                                $modeloFacturaUpd->NUMERO_IR             = isset($data['TblFacturas']['NUMERO_IR']) ? $data['TblFacturas']['NUMERO_IR'] : null;
                                if($modeloFacturaUpd->FECHA_INGRESO_BANCO != null){
                                    $modeloFacturaUpd->FK_ESTATUS = 2;
                                }else{
                                    $modeloFacturaUpd->FK_ESTATUS = 1;
                                }
                                $modeloFacturaUpd->TOTAL_FACTURABLE      = $data['total_facturable'][$pk_periodo_factura];
                                $modeloFacturaUpd->COMENTARIOS           = isset($data['comentariosFactura']) ? $data['comentariosFactura'] : null;
                                $modeloFacturaUpd->save(false);
                                $modeloFactura = $modeloFacturaUpd;
                                //dd($modeloFacturaUpd);

                                //Bitacora para modificación de registro en tblFacturas.
                                $fk_doc = ($data['tipoFactura'] == '') ? 'FK_DOC_FACTURA='.$modeloFacturaUpd->FK_DOC_FACTURA : 'FK_PROYECTO_DOC_FACTURA='.$modeloFacturaUpd->FK_PROYECTO_DOC_FACTURA;
                                $descripcionBitacora = $fk_doc.', FECHA_EMISION='.$modeloFacturaUpd->FECHA_EMISION.
                                ', FECHA_ENTREGA_CLIENTE='.$modeloFacturaUpd->FECHA_ENTREGA_CLIENTE.
                                ', FECHA_INGRESO_BANCO='.$modeloFacturaUpd->FECHA_INGRESO_BANCO.', COMENTARIOS='.$modeloFacturaUpd->COMENTARIOS;
                                user_log_bitacora_contabilidad($descripcionBitacora,'Modificar Información de Factura',$modeloFactura->FK_PERIODO);
                            }else{
                                //var_dump("Se crea nueva factura en el periodo");
                                $modeloFactura = new TblFacturas();
                                //Consulta para verificar si ya existe un documento factura del conjunto de periodos que se estan procesando (encaso de que sean un conjunto de periodos y NO, un sólo periodo)
                                $modeloFacturaSelf = ($data['tipoFactura'] == '') ? TblFacturas::find()->where(['=','FK_DOC_FACTURA', $pk_documento_factura])->limit(1)->one() : TblFacturas::find()->where(['=','FK_PROYECTO_DOC_FACTURA', $pk_documento_factura])->limit(1)->one();
                                if($data['tipoFactura'] == '') {
                                    $modeloFactura->FK_PERIODO = $pk_periodo_factura;
                                    $modeloFactura->FK_DOC_FACTURA = $pk_documento_factura;
                                } else {
                                    $modeloFactura->FK_PROYECTO_PERIODO = $pk_periodo_factura;
                                    $modeloFactura->FK_PROYECTO_DOC_FACTURA = $pk_documento_factura;
                                }

                                //Si ya existe una factura (del primer periodo del conjunto de periodos tratados), se copiaran los datos de la factura existente en la nueva factura con el mismo PK_DOCUMENTO que se creara para el periodo en curso
                                if($modeloFacturaSelf){
                                    //var_dump("Si ya existe una factura (del primer periodo del conjunto de periodos tratados)");
                                    $modeloFactura->FECHA_EMISION         = !empty($modeloFacturaSelf->FECHA_EMISION) ? transform_date($modeloFacturaSelf->FECHA_EMISION,'Y-m-d') : null;
                                    $modeloFactura->FECHA_ENTREGA_CLIENTE = !empty($modeloFacturaSelf->FECHA_ENTREGA_CLIENTE) ? transform_date($modeloFacturaSelf->FECHA_ENTREGA_CLIENTE,'Y-m-d') : null;
                                    $modeloFactura->FECHA_INGRESO_BANCO   = !empty($modeloFacturaSelf->FECHA_INGRESO_BANCO) ? transform_date($modeloFacturaSelf->FECHA_INGRESO_BANCO,'Y-m-d') : null;
                                    $modeloFactura->FECHA_RECEPCION_IR    = !empty($modeloFacturaSelf->FECHA_RECEPCION_IR) ? transform_date($modeloFacturaSelf->FECHA_RECEPCION_IR,'Y-m-d') : null;
                                    $modeloFactura->CONTACTO_ENTREGA      = isset($modeloFacturaSelf->CONTACTO_ENTREGA) ? $modeloFacturaSelf->CONTACTO_ENTREGA : null;
                                    $modeloFactura->FK_SERVICIO           = isset($modeloFacturaSelf->FK_SERVICIO) ? $modeloFacturaSelf->FK_SERVICIO : null;
                                    $modeloFactura->NUMERO_IR             = isset($modeloFacturaSelf->NUMERO_IR) ? $modeloFacturaSelf->NUMERO_IR : null;
                                    $modeloFactura->FK_ESTATUS            = isset($modeloFacturaSelf->FK_ESTATUS) ? $modeloFacturaSelf->FK_ESTATUS : null;
                                    $modeloFactura->FK_PORCENTAJE         = isset($modeloFacturaSelf->FK_PORCENTAJE) ? $modeloFacturaSelf->FK_PORCENTAJE : null;
                                    $modeloFactura->TOTAL_FACTURABLE      = isset($modeloFacturaSelf->TOTAL_FACTURABLE) ? $modeloFacturaSelf->TOTAL_FACTURABLE : null;
                                    $modeloFactura->COMENTARIOS           = isset($modeloFacturaSelf->COMENTARIOS) ? $modeloFacturaSelf->COMENTARIOS : null;
                                    $modeloFactura->save(false);

                                }else{
                                    //var_dump("Si NO existe una factura NUEVA se crea)");
                                    $modeloFactura->FECHA_EMISION         = !empty($data['TblFacturas']['FECHA_EMISION']) ? transform_date($data['TblFacturas']['FECHA_EMISION'],'Y-m-d') : null;
                                    $modeloFactura->FECHA_ENTREGA_CLIENTE = !empty($data['TblFacturas']['FECHA_ENTREGA_CLIENTE']) ? transform_date($data['TblFacturas']['FECHA_ENTREGA_CLIENTE'],'Y-m-d') : null;
                                    $modeloFactura->FECHA_INGRESO_BANCO   = null;
                                    $modeloFactura->FECHA_RECEPCION_IR    = null;
                                    $modeloFactura->CONTACTO_ENTREGA      = isset($data['TblFacturas']['CONTACTO_ENTREGA']) ? $data['TblFacturas']['CONTACTO_ENTREGA'] : null;
                                    $modeloFactura->FK_SERVICIO           = isset($PK_PROYECTO) ? 2 : 1;
                                    $modeloFactura->NUMERO_IR             = isset($data['TblFacturas']['NUMERO_IR']) ? $data['TblFacturas']['NUMERO_IR'] : null;
                                    $modeloFactura->FK_ESTATUS            = 1;
                                    $modeloFactura->FK_PORCENTAJE         = 1;
                                    $modeloFactura->TOTAL_FACTURABLE      = $data['total_facturable'][$pk_periodo_factura];
                                    $modeloFactura->COMENTARIOS           = isset($data['comentariosFactura']) ? $data['comentariosFactura'] : null;
                                    $modeloFactura->save(false);
                                }
                                //dd($modeloFactura);
                                //Bitacora para crear nuevo registro de un Documento Factura en Periodos.
                                if($data['tipoFactura'] == '') {
                                    $descripcionBitacora = 'FK_ASIGNACION='.$modeloDocumentos->FK_ASIGNACION.', TIPO_DOCUMENTO=FACTURA, NUM_DOCUMENTO='.$modeloDocumentos->NUM_DOCUMENTO.', FK_CLIENTE='.$data['FK_CLIENTE'];
                                    user_log_bitacora_contabilidad($descripcionBitacora,'Modificar Documento Factura en Periodos',$pk_periodo_factura);

                                    $descripcionBitacora = 'FK_DOC_FACTURA='.$modeloFactura->FK_DOC_FACTURA.', FECHA_EMISION='.$modeloFactura->FECHA_EMISION.', FECHA_ENTREGA_CLIENTE='.$modeloFactura->FECHA_ENTREGA_CLIENTE.', FECHA_INGRESO_BANCO='.$modeloFactura->FECHA_INGRESO_BANCO.', COMENTARIOS='.$modeloFactura->COMENTARIOS;
                                    user_log_bitacora_contabilidad($descripcionBitacora,'Cargar Información de Factura',$modeloFactura->FK_PERIODO);
                                } else {
                                    $descripcionBitacora = 'TIPO_DOCUMENTO=FACTURA, NUM_DOCUMENTO='.$modeloDocumentos->NUM_DOCUMENTO;
                                    user_log_bitacora_contabilidad($descripcionBitacora,'Modificar Documento Factura en Periodos',$pk_periodo_factura);

                                    $descripcionBitacora = 'FECHA_EMISION='.$modeloFactura->FECHA_EMISION.', FECHA_ENTREGA_CLIENTE='.$modeloFactura->FECHA_ENTREGA_CLIENTE.', FECHA_INGRESO_BANCO='.$modeloFactura->FECHA_INGRESO_BANCO.', COMENTARIOS='.$modeloFactura->COMENTARIOS;
                                    user_log_bitacora_contabilidad($descripcionBitacora,'Cargar Información de Factura',$modeloFactura->FK_PERIODO);
                                }
                            }

                        }

                        if($data['tipoFactura'] == '') {
                            $modelPeriodosUpd = TblPeriodos::find()->where(['PK_PERIODO' => $pk_periodo_factura])->limit(1)->one();
                            $modelPeriodosUpd->FK_DOCUMENTO_FACTURA = $pk_documento_factura;
                            $modelPeriodosUpd->FK_DOCUMENTO_FACTURA_XML = $pk_documento_factura_xml;
                            $modelPeriodosUpd->TARIFA_FACTURA = $data['TblDocumentos']['TARIFA'];
                            $modelPeriodosUpd->HORAS_FACTURA = $data['horas_factura'][$pk_periodo_factura];
                            $modelPeriodosUpd->MONTO_FACTURA = $data['monto_factura'][$pk_periodo_factura];
                            $modelPeriodosUpd->save(false);

                            $descripcionBitacora = 'PK_PERIODO='.$modelPeriodosUpd->PK_PERIODO.', TIPO_DOCUMENTO=FACTURA, FK_DOCUMENTO_FACTURA='.$modelPeriodosUpd->FK_DOCUMENTO_FACTURA.', FK_DOCUMENTO_FACTURA_XML='.$modelPeriodosUpd->FK_DOCUMENTO_FACTURA_XML;
                            user_log_bitacora_contabilidad($descripcionBitacora,'Actualiza Periodo con información de la Factura relacionada',$facturaNuevaModifica->FK_DOCUMENTO_FACTURA);

                        } else {
                            //var_dump("Se agrega factura en periodo de proyectos");
                            $modelPeriodosUpd = TblProyectosPeriodos::find()->where(['PK_PROYECTO_PERIODO' => $pk_periodo_factura])->limit(1)->one();
                            $modelPeriodosUpd->FK_DOCUMENTO_FACTURA = $pk_documento_factura;
                            $modelPeriodosUpd->FK_DOCUMENTO_FACTURA_XML = $pk_documento_factura_xml;
                            $modelPeriodosUpd->TARIFA_FACTURA = $data['TblDocumentos']['TARIFA'];
                            $modelPeriodosUpd->HORAS_FACTURA = $data['horas_factura'][$pk_periodo_factura];
                            $modelPeriodosUpd->MONTO_FACTURA = $data['monto_factura'][$pk_periodo_factura];
                            $modelPeriodosUpd->save(false);

                            $descripcionBitacora = 'TIPO_DOCUMENTO=FACTURA, FK_DOCUMENTO_FACTURA='.$modelPeriodosUpd->FK_DOCUMENTO_FACTURA.', FK_DOCUMENTO_FACTURA_XML='.$modelPeriodosUpd->FK_DOCUMENTO_FACTURA_XML;
                            user_log_bitacora_contabilidad($descripcionBitacora,'Actualiza Periodo con información de la Factura relacionada',$facturaNuevaModifica->FK_DOCUMENTO_FACTURA);
                        }
                    }

                }

                //var_dump($modeloFactura);
                //dd("termino de facturar todos los periodos de la bolsa");

                if($data['notificar_modificacion'] == 1){
                    $is_pr= get_config('CONFIG','PRODUCCION');
                    $de= get_config('PERIODOS','CORREO_REMITENTE_FACTURA');
                    $prefix_correo = '';
                    if($is_pr){

                        $arr_validar_factura_1= explode(',',get_config('PERIODOS','PARA_FACTURA_BTN'));

                        //Banorte
                        // if($sql['RFC']=='BMN930209927'||$sql['RFC']=='ITA081127UZ1'){
                        if (in_array($sql['RFC'],$arr_validar_factura_1)) {
                            //$para = array('jorge.mendiola@eisei.net.mx','nestor.tristan@eisei.net.mx','maria.delgado@eisei.net.mx');
                            $prefix_correo= get_config('PERIODOS','PREFIX_BANORTE');
                            $para= explode(',',get_config('PERIODOS','CORREO_DESTINO_FACTURA_OTROS'));
                        }else{
                            if($sql['RFC']==get_config('PERIODOS','IS_IBM')){
                                $prefix_correo= get_config('PERIODOS','PREFIX_IBM');
                            }
                            //$para = array('jorge.mendiola@eisei.net.mx','maria.delgado@eisei.net.mx');

                            $para= explode(',',get_config('PERIODOS','CORREO_DESTINO_FACTURA_OTROS'));
                        }

                    }else{
                        $para= explode(',',get_config('PERIODOS','CORREO_DESTINO_POR_FACTURAR_OTROS'));
                    }

                    // INICIO MOD HRIBI - Concatenación en el asunto del correo del número del documento, mes y año pertenecientes al periodo donde se guarda el doc HdE.
                    $asunto= 'Modificación de factura'.'Factura_'.$modeloDocumentos->NUM_DOCUMENTO;
                    // FIN MOD HRIBI
                    $nombre_usuario= user_info()['NOMBRE_COMPLETO'];
                    if($data['tipoFactura'] == '') {
                        $responsableOP= $connection->createCommand("
                            SELECT concat_ws(' ',e.NOMBRE_EMP,e.APELLIDO_PAT_EMP,e.APELLIDO_MAT_EMP) nombre_responsable_op
                            FROM tbl_asignaciones a
                            LEFT JOIN tbl_empleados e on a.fk_responsable_op = e.pk_empleado
                            where a.PK_ASIGNACION = ".$modeloDocumentos->FK_ASIGNACION)->queryOne();
                    } else {
                        $responsableOP= $connection->createCommand("
                            SELECT concat_ws(' ',e.NOMBRE_EMP,e.APELLIDO_PAT_EMP,e.APELLIDO_MAT_EMP) nombre_responsable_op
                            FROM tbl_proyectos p
                            LEFT JOIN tbl_empleados e on p.fk_responsable_op = e.pk_empleado
                            where p.PK_PROYECTO = ".$_GET['proyecto'])->queryOne();
                    }
                    $dateNow = ("now");
                    $dateNow = str_replace('/', '-', $dateNow);

                    if(!empty($documentoOldValues)){
                        if($documentoOldValues['NUM_DOCUMENTO'] != $modeloDocumentos->NUM_DOCUMENTO){
                            $camposOldValues = $camposOldValues."Número de Factura: ".$documentoOldValues['NUM_DOCUMENTO']."<br/>";
                            $camposNewValues = $camposNewValues."Número de Factura: ".$modeloDocumentos->NUM_DOCUMENTO."<br/>";
                        }
                        if($documentoOldValues['NOMBRE_DOCUMENTO'] != $modeloDocumentos->NOMBRE_DOCUMENTO){
                            $camposOldValues = $camposOldValues."Nombre del documento: ".$documentoOldValues['NOMBRE_DOCUMENTO']."<br/>";
                            $camposNewValues = $camposNewValues." Nombre del documento: ".$modeloDocumentos->NOMBRE_DOCUMENTO."<br/>";
                        }
                        if(!empty($documentoXMLOldValues)){
                            if($documentoXMLOldValues['NOMBRE_DOCUMENTO'] != $modeloDocumentosXML->NOMBRE_DOCUMENTO){
                                $camposOldValues = $camposOldValues."Nombre del documento: ".$documentoXMLOldValues['NOMBRE_DOCUMENTO']."<br/>";
                                $camposNewValues = $camposNewValues."Nombre del documento: ".$modeloDocumentosXML->NOMBRE_DOCUMENTO."<br/>";
                            }
                        }
                        if(!empty($facturaOldValues)){
                            if($facturaOldValues['FECHA_EMISION'] != $modeloFactura->FECHA_EMISION){
                                $camposOldValues = $camposOldValues."Fecha de Emisión: ".$facturaOldValues['FECHA_EMISION']."<br/>";
                                $camposNewValues = $camposNewValues."Fecha de Emisión: ".str_replace('/', '-', $modeloFactura->FECHA_EMISION)."<br/>";
                            }
                            if($facturaOldValues['FECHA_ENTREGA_CLIENTE'] != $modeloFactura->FECHA_ENTREGA_CLIENTE){
                                $camposOldValues = $camposOldValues."Fecha de Entrega al Cliente: ".$facturaOldValues['FECHA_ENTREGA_CLIENTE']."<br/>";
                                $camposNewValues = $camposNewValues."Fecha de Entrega al Cliente: ".str_replace('/', '-', $modeloFactura->FECHA_ENTREGA_CLIENTE)."<br/>";
                            }
                            if($facturaOldValues['CONTACTO_ENTREGA'] != $modeloFactura->CONTACTO_ENTREGA){
                                $camposOldValues = $camposOldValues."Contacto de Entrega: ".$facturaOldValues['CONTACTO_ENTREGA']."<br/>";
                                $camposNewValues = $camposNewValues."Contacto de Entrega: ".$modeloFactura->CONTACTO_ENTREGA."<br/>";
                            }
                            if($facturaOldValues['FECHA_INGRESO_BANCO'] != $modeloFactura->FECHA_INGRESO_BANCO){
                                $camposOldValues = $camposOldValues."Fecha de Ingreso al Banco: ".$facturaOldValues['FECHA_INGRESO_BANCO']."<br/>";
                                $camposNewValues = $camposNewValues."Fecha de Ingreso al Banco: ".str_replace('/', '-', $modeloFactura->FECHA_INGRESO_BANCO)."<br/>";
                            }
                            if($facturaOldValues['COMENTARIOS'] != $modeloFactura->COMENTARIOS){
                                $camposOldValues = $camposOldValues."Observaciones: ".$facturaOldValues['COMENTARIOS']."<br/>";
                                $camposNewValues = $camposNewValues."Observaciones: ".$modeloFactura->COMENTARIOS."<br/>";
                            }
                        }

                        $mensaje = '';
                        $mensaje .= '<style>p, ul, li {font-family: Arial, Helvetica, sans-serif; font-size: 12px;}</style>';
                        $mensaje .= '<p>Buen d&iacute;a <b>'.$responsableOP['nombre_responsable_op'].'</b></p>';
                        if ($camposOldValues == '' && $camposNewValues == '') {
                            $mensaje .= '<p>El usuario <b>'.user_info()['NOMBRE_COMPLETO'].'</b> entro al detalle de la factura <b>'.$modeloDocumentos->NUM_DOCUMENTO.'</b>, en la fecha <b>'.date('d/m/Y', strtotime($dateNow)).'</b>, no realiz&oacute; ning&uacute;n cambio pero decidi&oacute; notificar.</p>';
                        } else {
                            $mensaje .= '<p>El usuario <b>'.user_info()['NOMBRE_COMPLETO'].' </b>modifico una factura, la fecha en que se realizo &eacute;sta modificaci&oacute;n es: <b>'.date('d/m/Y', strtotime($dateNow)).'</b></p>';
                            $mensaje .= '<p><b>Numero de Factura: </b>'.$modeloDocumentos->NUM_DOCUMENTO.'</p>';
                            $mensaje .= '<p><b>Los campos modificados son: </b></p>';
                            $mensaje .= '<p><b>Campos antes de la Modificaci&oacute;n: </b><br/>'.$camposOldValues.'</p>';
                            $mensaje .= '<p><b>Campos después de la Modificaci&oacute;n: </b><br/>'.$camposNewValues.'</p>';
                        }
                        $mensaje .= '<p>Saludos.</p>';

                        $enviado = send_mail($de,$para,$asunto,$mensaje);
                    }

                }
                $transaction->commit();//Si todo el proceso es correcto se ejectua la sentencia commit para guardar todas las operaciones en la base de datos.

                }catch(\Exception $e){
                    $transaction->rollBack();//Si sucede algún error durante la operación, se ejecuta la sentencia rollback para evitar que hagan modificaciones en base de datos.
                    throw $e;
                }//FIN del funcionamiento de la 'Transacción' con su respectivo try/catch

                }//FIN del if($data['TblDocumentos']['FK_TIPO_DOCUMENTO'] == 4)


                $this->refresh();
        }//FIN del if ($modeloDocumentos->load(Yii::$app->request->post()))

        if (Yii::$app->request->isAjax) {
            $data = Yii::$app->request->post();
            $post=null;
            parse_str($data['data'],$post);

            $pagina =(!empty($data['pagina']))? trim($data['pagina']):'';

            $query = (new \yii\db\Query())
                            ->select('tbl_periodos.PK_PERIODO
                                    , tbl_periodos.FECHA_INI
                                    , tbl_periodos.FECHA_FIN
                                    , tbl_periodos.TARIFA
                                    , tbl_periodos.MONTO
                                    , tbl_empleados.NOMBRE_EMP
                                    , tbl_empleados.APELLIDO_PAT_EMP
                                    ')
                            ->from('tbl_periodos')
                            ->join('JOIN','tbl_asignaciones',
                                    'tbl_asignaciones.PK_ASIGNACION = tbl_periodos.FK_ASIGNACION')
                            ->join('JOIN','tbl_empleados',
                                    'tbl_empleados.PK_EMPLEADO = tbl_periodos.FK_EMPLEADO')
                            /*->join('LEFT JOIN','tbl_documentos',
                                    'tbl_documentos.FK_PERIODO = tbl_periodos.PK_PERIODO')*/
                            ->orderBy('tbl_periodos.PK_PERIODO DESC')
                            -> all();

            if(count($query)<$tamanio_pagina){
                $pagina=1;
            }

            $dataProvider = new ActiveDataProvider([
                'query'=>(new \yii\db\Query())
                             ->select('tbl_periodos.PK_PERIODO
                                    , tbl_periodos.FECHA_INI
                                    , tbl_periodos.FECHA_FIN
                                    , tbl_periodos.TARIFA
                                    , tbl_periodos.MONTO
                                    , tbl_empleados.NOMBRE_EMP
                                    , tbl_empleados.APELLIDO_PAT_EMP
                                    ')
                            ->from('tbl_periodos')
                            ->join('JOIN','tbl_asignaciones',
                                    'tbl_asignaciones.PK_ASIGNACION = tbl_periodos.FK_ASIGNACION')
                            ->join('JOIN','tbl_empleados',
                                    'tbl_empleados.PK_EMPLEADO = tbl_periodos.FK_EMPLEADO')
                            ->orderBy('tbl_periodos.PK_PERIODO DESC')
                        ,
                'pagination' => [
                    'pageSize' => $tamanio_pagina,
                    'page' => $pagina-1,
                ],
            ]);

            $resultado=$dataProvider->getModels();
            //var_dump($resultado);
            foreach ($resultado as $key => $value) {

                $periodos = TblPeriodos::find()->where(['PK_PERIODO' => $resultado[$key]['PK_PERIODO']])->limit(1)->one();
                if($periodos){
                    $idperiodo = $periodos->PK_PERIODO;
                }else{
                    $idperiodo = 'Sin Definir';
                }

                $resultado[$key]['PK_PERIODO']=$idperiodo;
            }

            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $res = array(
                'pagina'        => $pagina,
                'data'          => $dataProvider->getModels(),
                'total_paginas' => ceil($dataProvider->getPagination()->totalCount / $tamanio_pagina),
            );

            return $res;
        }else{
            $dataProvider = new ActiveDataProvider([
                'query' => TblPeriodos::find()->orderBy(['PK_PERIODO' => SORT_DESC]),
                'pagination' => [
                    'pageSize' => $tamanio_pagina,
                    'page' => 0,
                ],
            ]);

            $datos = Yii::$app->request->get();
            if($modelComentariosAsignaciones3){
                $periodosAsignacion = (new \yii\db\Query())
                        ->select('PK_PERIODO, FECHA_INI, FECHA_FIN, TARIFA')
                        ->from('tbl_periodos')
                        ->where(['FK_ASIGNACION' => $datos['id'] ])
                        ->andWhere(['<','FECHA_INI',$modelComentariosAsignaciones3[0]['FECHA_FIN'] ])
                        ->orderBy(['FECHA_INI' => SORT_ASC])
                        ->all();

                        $tarifas = '';
                        if ($periodosAsignacion) {
                            foreach ($periodosAsignacion as $key) {
                                $tarifas[$key['PK_PERIODO']] = $key['TARIFA'];
                            }
                        }

            }else{
                $periodosAsignacion = (new \yii\db\Query())
                        ->select('PK_PERIODO, FECHA_INI, FECHA_FIN, TARIFA')
                        ->from('tbl_periodos')
                        ->where(['FK_ASIGNACION' => $datos['id'] ])
                        ->orderBy(['FECHA_INI' => SORT_ASC])
                        ->all();

                        $tarifas = '';
                        if ($periodosAsignacion) {
                            foreach ($periodosAsignacion as $key) {
                                $tarifas[$key['PK_PERIODO']] = $key['TARIFA'];
                            }
                        }

            }

            $reemplazos = TblAsignacionesReemplazos::find()->where(['FK_ASIGNACION' => $datos['id'] ])->orderBy(['PK_ASIGNACION_REEMPLAZO' => SORT_DESC])->asArray()->limit(1)->one();
            return $this->render('view', [
                //'model' => $this->findModel($id),
                'data' => $dataProvider->getModels(),
                'total_paginas' => ceil($dataProvider->getPagination()->totalCount / $tamanio_pagina),
                'dataProvider' => $dataProvider,
                'periodosAsignacion' => $periodosAsignacion,
                'modeloDocumentos' => $modeloDocumentos,
                'modelSubirDocFactura' => $modelSubirDocFactura,
                'modelSubirDocFacturaXML' => $modelSubirDocFacturaXML,
                'modeloFactura' => $modeloFactura,
                'sql' => $sql,
                'reemplazos' => $reemplazos,
                'tarifas' => $tarifas,
                'modelComentariosAsignaciones3' => $modelComentariosAsignaciones3,
            ]);
        }
        $connection->close();
    }

    public function actionGet_bolsa() {
        if (Yii::$app->request->isAjax) {
            $data = Yii::$app->request->post();

            $bloque_bolsas = TblBitBlsDocs::find()->where(['PK_BIT_BLS' => $data['id_bit_bls']])->andWhere(['IS NOT','FK_PERIODOS',null])->all();

            if($bloque_bolsas) {
                foreach ($bloque_bolsas as $key) {
                    $bloque_periodos = explode(',', $key['FK_PERIODOS']);
                    unset($bloque_periodos[count($bloque_periodos)-1]);
                    if (in_array($data['fk_periodo'], $bloque_periodos)) {
                        for($i = 0; $i < count($bloque_periodos); $i++) {
                            $resultado[] = (new \yii\db\Query())
                                ->select([
                                    'p.FK_ASIGNACION AS pk_asignacion', 'P.PK_PERIODO AS pk_periodo', 'p.HORAS AS horas', 'p.MONTO AS monto',
                                    'CONCAT(DATE_FORMAT(p.FECHA_INI, \'%d/%m/%Y\')," al ",DATE_FORMAT(p.FECHA_FIN, \'%d/%m/%Y\')) AS periodo',
                                    'CONCAT(e.NOMBRE_EMP," ",e.APELLIDO_PAT_EMP," ",e.APELLIDO_MAT_EMP) AS res', 'd.NUM_DOCUMENTO AS factura',
                                    'f.FECHA_INGRESO_BANCO as pagado'
                                ])
                                ->from('tbl_periodos p')
                                ->join('inner join','tbl_asignaciones a', 'p.FK_ASIGNACION = a.PK_ASIGNACION')
                                ->join('inner JOIN','tbl_empleados as e', 'a.fk_empleado = e.PK_empleado')
                                ->join('left join','tbl_documentos d', 'p.FK_DOCUMENTO_FACTURA = d.PK_DOCUMENTO')
                                ->join('left join','tbl_facturas f', 'f.FK_PERIODO = p.PK_PERIODO and f.FK_ESTATUS <> 3')
                                ->Where(['p.PK_PERIODO'=>$bloque_periodos[$i]])->one();
                        }
                        break;
                    }
                }
            }   else {

                $model = TblCatBolsas::find()->select('NUMERO_BOLSA')->where(['PK_BOLSA'=>$data['id_bolsa']])->one();
                $documento_bolsa= TblDocumentos::find()->where(['PK_DOCUMENTO'=>$model->FK_DOCUMENTO])->asArray()->one();

                $fks_asignaciones = TblDocumentos::find()->select(['FK_ASIGNACION'])->where(['LIKE','NUM_DOCUMENTO','BLS_ODC_'.$model->NUMERO_BOLSA])->distinct()->asArray()->column();
                $pks_documentos = TblDocumentos::find()->select(['PK_DOCUMENTO','FK_ASIGNACION'])->where(['LIKE','NUM_DOCUMENTO','BLS_ODC_'.$model->NUMERO_BOLSA])->asArray()->all();

                $asignaciones= (new \yii\db\Query())
                    ->select([
                                'distinct (a.PK_ASIGNACION)',
                                'e.NOMBRE_EMP',
                                'e.APELLIDO_PAT_EMP',
                                'e.APELLIDO_MAT_EMP',
                                'DATE_FORMAT(a.FECHA_INI, \'%d/%m/%Y\') as FECHA_INI',
                                'DATE_FORMAT(a.FECHA_FIN, \'%d/%m/%Y\') as FECHA_FIN',
                                'a.TARIFA',
                                'tbl_cat_puestos.DESC_PUESTO',
                                'a.MONTO',
                                'ea.DESC_ESTATUS_ASIGNACION',
                                'a.FK_ESTATUS_ASIGNACION',
                                'a.NOMBRE',
                                'a.fk_empleado',
                                'cl.alias_cliente',
                                'cl.NOMBRE_CLIENTE',

                                '(
                                    SELECT td2.NUM_DOCUMENTO
                                        FROM tbl_documentos td2
                                        INNER join tbl_periodos p2
                                        on p2.FK_ASIGNACION = td2.FK_ASIGNACION
                                        and YEAR(
                                                p2.fecha_ini
                                            ) = '.date('Y').'
                                        AND MONTH(
                                                p2.fecha_ini
                                        ) = '.date('m').'
                                        AND td2.FK_TIPO_DOCUMENTO=2
                                        AND td2.PK_DOCUMENTO= p2.FK_DOCUMENTO_ODC
                                        where p2.fk_asignacion= a.pk_asignacion
                                        limit 1
                                ) as NUM_DOCUMENTO',
                                'cl.HORAS_ASIGNACION'
                                ])
                    ->from('tbl_asignaciones as a')
                    ->join('inner JOIN','tbl_empleados as e',
                        'a.fk_empleado = e.PK_empleado')
                    ->join('LEFT JOIN','tbl_perfil_empleados',
                        'tbl_perfil_empleados.FK_empleado = e.PK_empleado')
                    ->join('LEFT JOIN','tbl_clientes as cl',
                        'a.FK_CLIENTE = cl.PK_cliente')
                    ->join('LEFT JOIN','tbl_cat_puestos',
                        'tbl_perfil_empleados.fk_puesto = tbl_cat_puestos.PK_puesto')
                    ->join('LEFT JOIN','tbl_cat_unidades_negocio',
                        'tbl_perfil_empleados.FK_RAZON_SOCIAL = tbl_cat_unidades_negocio.PK_UNIDAD_NEGOCIO')
                    ->join('LEFT JOIN','tbl_periodos p',
                        'p.FK_Asignacion = a.PK_asignacion')
                    ->join('LEFT JOIN','tbl_documentos td',
                        'td.FK_ASIGNACION = p.FK_ASIGNACION ')
                    ->join('LEFT JOIN','tbl_cat_estatus_asignaciones ea',
                       'ea.pk_estatus_asignacion = a.FK_ESTATUS_ASIGNACION')
                    ->join('LEFT JOIN','tbl_asignaciones_reemplazos as ar',
                        'ar.FK_ASIGNACION = a.PK_ASIGNACION')
                    ->join('LEFT JOIN','tbl_empleados as e_ar',
                        'ar.FK_EMPLEADO = e_ar.PK_EMPLEADO')
                    ->where(['IN','a.PK_ASIGNACION',$fks_asignaciones])
                    ->groupBy(['a.PK_ASIGNACION'])
                    ->orderBy('a.PK_ASIGNACION DESC')
                    ->all();

                $periodos= array();
                foreach ($fks_asignaciones as $key => $value) {

                    $periodos[$value][] = (new \yii\db\Query())
                        ->select([
                        'p.*',
                        'p.MONTO_FACTURA',
                        'd.PK_DOCUMENTO',
                        'd.NUM_DOCUMENTO',
                        'f.PK_FACTURA',
                        'f.FECHA_ENTREGA_CLIENTE',
                        'f.FK_DOC_FACTURA',
                        'f.FECHA_INGRESO_BANCO',
                        'f.CONTACTO_ENTREGA',
                        'f.FK_ESTATUS',
                        'e.NOMBRE_EMP',
                        'e.APELLIDO_PAT_EMP',
                        'e.APELLIDO_MAT_EMP',
                        ])
                    ->from('tbl_periodos as p')
                    ->join('left join','tbl_documentos d',
                        'p.FK_DOCUMENTO_FACTURA = d.PK_DOCUMENTO')
                    ->join('left join','tbl_facturas f',
                        'f.FK_PERIODO = p.PK_PERIODO and f.FK_ESTATUS <> 3')
                    ->join('left join','tbl_cat_contactos c',
                        'c.PK_CONTACTO = f.CONTACTO_ENTREGA')
                    ->join('left join','tbl_empleados e',
                            'e.PK_EMPLEADO = p.FK_EMPLEADO')
                    ->andWhere(['p.FK_ASIGNACION'=>$value])
                    ->orderBy('p.FECHA_INI')
                    ->all();
                }

                $arr_periodos= '';
                $asg=1;
                $facturas = array();
                $resultado = array();

                $it = 0;
                foreach ($asignaciones as $key => $value) {
                            $cont=1;
                            foreach ($periodos[$value['PK_ASIGNACION']] as $key2 => $value2) {
                                foreach ($value2 as $key3 => $value3) {
                                    $imprimir = true;
                                    foreach ($pks_documentos as $doc => $val_doc) {
                                        if($value['PK_ASIGNACION']==$val_doc['FK_ASIGNACION']&&$val_doc['PK_DOCUMENTO']==$value3['FK_DOCUMENTO_ODC']){
                                            $imprimir = true;
                                            break;
                                        }else{
                                            $imprimir= false;
                                        }
                                    }

                                    if($imprimir){
                                        if(isset($value3['NUM_DOCUMENTO'])){
                                            $arr_periodos.=$value3['PK_PERIODO'].",";
                                        }
                                        $resultado[$it]['pk_asignacion'] = $value['PK_ASIGNACION'];
                                        $resultado[$it]['pk_periodo'] = $value3['PK_PERIODO'];
                                        $resultado[$it]['res'] = $value3['NOMBRE_EMP'].' '.$value3['APELLIDO_PAT_EMP'].' '.$value3['APELLIDO_MAT_EMP'];
                                        $resultado[$it]['periodo'] = $cont.' - '.transform_date($value3['FECHA_INI'],'d/m/Y').' al '.transform_date($value3['FECHA_FIN'],'d/m/Y');
                                        $resultado[$it]['horas'] = $value3['HORAS'];
                                        $resultado[$it]['monto'] = $value3['MONTO'];
                                        $resultado[$it]['factura'] = (isset($value3['NUM_DOCUMENTO'])?$value3['NUM_DOCUMENTO']:"");
                                        $resultado[$it]['pagado'] = (isset($value3['FECHA_INGRESO_BANCO'])?transform_date($value3['FECHA_INGRESO_BANCO'],'d/m/Y'):'');
                                        $it++;

                                    }
                                    $cont++;
                                }
                                $cont=1;
                            }

                }

            }

            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return ['data' => $data,
            'periodos' => $resultado];

        }
    }

    public function actionGet_periodo_docs_factura() {
        if (Yii::$app->request->isAjax) {

            $data = Yii::$app->request->post();
            $post = null;
            $modelo = TblPeriodos::find()->where(['PK_PERIODO'=>$data['data']])->one();
            $modeloBolsa = TblDocumentos::find()->where(['PK_DOCUMENTO' => $modelo->FK_DOCUMENTO_HDE])->limit(1)->one();
            $connection = \Yii::$app->db;
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            $es_bolsa= 0;
            if ($modeloBolsa&&strpos($modeloBolsa->NUM_DOCUMENTO, 'BLS_HDE_') !== false) {
                $es_bolsa=1;
            }

            $modelo2 = $connection->createCommand("select
                p.*, td.NUM_DOCUMENTO, td.FK_RAZON_SOCIAL, tdfac.NUM_DOCUMENTO NUM_FACTURA FROM tbl_periodos p
                LEFT JOIN tbl_documentos tdfac
                ON p.FK_DOCUMENTO_FACTURA = tdfac.PK_DOCUMENTO
                INNER JOIN tbl_documentos td
                ON P.FK_DOCUMENTO_ODC = td.PK_DOCUMENTO WHERE p.PK_PERIODO = ".($modelo->PK_PERIODO))->queryOne();

            /*$transaction = $connection->beginTransaction();
            try{*/
                $modelohde = TblDocumentos::find()->where(['PK_DOCUMENTO' => $modelo->FK_DOCUMENTO_HDE])->limit(1)->one();

                $modelodocfactura = ($modelo->FK_DOCUMENTO_FACTURA != null) ? TblDocumentos::find()
                                                                            ->where(['PK_DOCUMENTO' => $modelo->FK_DOCUMENTO_FACTURA])
                                                                            ->limit(1)->one() : null;
                $modelodocfacturaxml = ($modelo->FK_DOCUMENTO_FACTURA_XML != null) ? TblDocumentos::find()
                                                                        ->where(['PK_DOCUMENTO' => $modelo->FK_DOCUMENTO_FACTURA_XML])
                                                                        ->limit(1)->one() : null;
                $modelofactura = ($modelo->FK_DOCUMENTO_FACTURA != null) ? TblFacturas::find()
                                                                            ->where(['FK_DOC_FACTURA' => $modelo->FK_DOCUMENTO_FACTURA])
                                                                            //->andWhere(['=','FK_PERIODO',$data['data']])
                                                                            ->limit(1)->one() : null;

                $sqlAsigEmpCliCont = $connection->createCommand(" select
                                    ta.PK_ASIGNACION,
                                    te.NOMBRE_EMP,
                                    te.APELLIDO_PAT_EMP,
                                    ta.TARIFA,
                                    ta.HORAS,
                                    tc.NOMBRE_CLIENTE,
                                    tc.PK_CLIENTE,
                                    tc.RFC,
                                    tcc.NOMBRE_CONTACTO
                                    from tbl_asignaciones ta
                                        left join tbl_clientes tc
                                        on ta.FK_CLIENTE = tc.PK_CLIENTE
                                        inner join tbl_cat_contactos tcc
                                        on ta.FK_CONTACTO = tcc.PK_CONTACTO
                                        left join tbl_empleados te
                                        on ta.FK_EMPLEADO = te.PK_EMPLEADO
                                        where ta.PK_ASIGNACION = ".($modelo->FK_ASIGNACION))->queryOne();


                $modelosfactura = $connection->createCommand("select
                                    p.PK_PERIODO,
                                    p.FECHA_INI,
                                    p.FECHA_FIN,
                                    p.HORAS_FACTURA,
                                    p.MONTO_FACTURA,
                                    p.FK_DOCUMENTO_ODC,
                                    p.FK_DOCUMENTO_HDE,
                                    p.FK_DOCUMENTO_FACTURA FK_FACTURA_EN_PERIODO,
                                    f.FK_DOC_FACTURA FK_FACTURA_EN_FACTURA,
                                    f.PK_FACTURA,
                                    P.HORAS,
                                    P.MONTO,
                                    P.HORAS_HDE,
                                    P.MONTO_HDE,
                                    P.FACTURA_PROVISION,
                                    td.NUM_DOCUMENTO NUM_ODC,
                                    td2.NUM_DOCUMENTO NUM_FACTURA,
                                    td.FK_RAZON_SOCIAL,
                                    tc.NOMBRE_CLIENTE,
                                    p.TARIFA,
                                    p.HORAS_DEVENGAR,
                                    p.FK_ASIGNACION,
                                    CONCAT(te.NOMBRE_EMP,' ',te.APELLIDO_PAT_EMP,' ',te.APELLIDO_MAT_EMP) AS RECURSO
                                    FROM tbl_periodos P
                                    INNER JOIN tbl_documentos td
                                        ON p.FK_DOCUMENTO_ODC = td.PK_DOCUMENTO
                                    LEFT JOIN tbl_documentos td2
                                        ON p.FK_DOCUMENTO_FACTURA = td2.PK_DOCUMENTO
                                    left join tbl_asignaciones ta
                                    on p.FK_ASIGNACION = ta.PK_ASIGNACION
                                    LEFT JOIN tbl_clientes tc
                                    on ta.FK_CLIENTE = tc.PK_CLIENTE
                                    left join tbl_empleados te
                                    on ta.FK_EMPLEADO = te.PK_EMPLEADO
                                    LEFT JOIN tbl_facturas f
                                        ON f.FK_PERIODO= p.PK_PERIODO
                                        AND f.FK_DOC_FACTURA = p.FK_DOCUMENTO_FACTURA
                                        WHERE p.FK_DOCUMENTO_HDE IS NULL
                                        ORDER BY p.FK_ASIGNACION = ".($modelo->FK_ASIGNACION)." DESC")->queryAll();

                /*$modelosfactura= (new \yii\db\Query())
                                 ->select(['td.PK_DOCUMENTO',
                                    'p.PK_PERIODO',
                                    'p.FECHA_INI',
                                    'p.FECHA_FIN',
                                    'p.HORAS_FACTURA',
                                    'p.MONTO_FACTURA',
                                    'p.FK_DOCUMENTO_FACTURA',
                                    'f.PK_FACTURA',
                                    'P.HORAS_HDE',
                                    'P.MONTO_HDE',
                                    ])
                                 ->from('tbl_documentos td')
                                 ->join('inner join', 'tbl_periodos p',
                                    'p.FK_DOCUMENTO_HDE = td.PK_DOCUMENTO')
                                 ->join('left join', 'tbl_facturas f',
                                    'f.fk_periodo= p.pk_periodo')
                                 ->where(['=','p.FK_ASIGNACION',$sqlAsigEmpCliCont['PK_ASIGNACION']])
                                 ->orderBy('p.FECHA_INI ASC')
                                 ->all();*/

                $sqlDocODC = $connection->createCommand(" select td.NUM_SP, td.NUM_DOCUMENTO AS NUM_DOC_ODC, td.FK_RAZON_SOCIAL
                                    FROM tbl_periodos tp
                                    JOIN tbl_documentos td
                                    ON tp.FK_DOCUMENTO_ODC = td.PK_DOCUMENTO
                                    AND tp.PK_PERIODO = ".($modelo->PK_PERIODO))->queryOne();

                $sqlDocHDE = $connection->createCommand(" select td.NUM_DOCUMENTO AS NUM_DOC_HDE, tp.TARIFA_HDE, tp.HORAS_HDE, tp.TARIFA_FACTURA, tp.HORAS_FACTURA, tp.MONTO_FACTURA
                                    FROM tbl_periodos tp
                                    JOIN tbl_documentos td
                                    ON tp.FK_DOCUMENTO_HDE = td.PK_DOCUMENTO
                                    AND tp.PK_PERIODO = ".($modelo->PK_PERIODO))->queryOne();
                // dd($sql);
                $cliente_sql= strtolower($sqlAsigEmpCliCont['NOMBRE_CLIENTE']);
                $consecutivo=0;

                if(empty($modelohde)){
                    // if((strpos( $cliente_sql,'banorte')!==false&&strpos( $cliente_sql,'ixe')!==false)){
                    if($sqlAsigEmpCliCont['RFC']=='BMN930209927'||$sqlAsigEmpCliCont['RFC']=='ITA081127UZ1'){
                        $modelohde= new TblDocumentos();
                    }else{
                        $modelohde= new TblDocumentos();
                        $rfc= substr($sqlAsigEmpCliCont['RFC'],0,3);

                        $query="SELECT MAX(CONSECUTIVO_TIPO_DOC) AS cont FROM tbl_documentos WHERE FK_TIPO_DOCUMENTO=3 AND FK_CLIENTE= ".$sqlAsigEmpCliCont['PK_CLIENTE'];
                        // $query="SELECT COUNT(DISTINCT NUM_DOCUMENTO) as cont FROM tbl_documentos where FK_ASIGNACION=".$modelo->FK_ASIGNACION." AND FK_TIPO_DOCUMENTO=2 AND FECHA_REGISTRO='".date('Y-m-d')."'";
                        // dd($query);
                        $consecutivo= $connection->createCommand($query)->queryOne()['cont'];
                        $consecutivo++;
                        $modelohde->NUM_DOCUMENTO =$rfc.'_HDE'.str_pad($consecutivo,5,'0',STR_PAD_LEFT).'_'.date('dmy');
                    }
                }else{
                    $modelohde->DOCUMENTO_UBICACION= utf8_encode($modelohde->DOCUMENTO_UBICACION);
                }

                if($modelodocfactura != null){
                    $modelodocfactura->DOCUMENTO_UBICACION= utf8_encode($modelodocfactura->DOCUMENTO_UBICACION);
                }

                if($modelodocfacturaxml != null){
                    $modelodocfacturaxml->DOCUMENTO_UBICACION= utf8_encode($modelodocfacturaxml->DOCUMENTO_UBICACION);
                }
                $connection->close();
                return $res = array(
                    'es_bolsa' => $es_bolsa,
                    'periodo' => $modelo2,
                    'modelohde' => $modelohde,
                    'modelodocfactura' => $modelodocfactura,
                    'modelodocfacturaxml' => $modelodocfacturaxml,
                    'modelofactura' => $modelofactura,
                    'sqlDocODC' => $sqlDocODC,
                    'sqlDocHDE' => $sqlDocHDE,
                    'sqlAsignacion' => $sqlAsigEmpCliCont,
                    'consecutivo' => $consecutivo,
                    'modelosfactura'=>$modelosfactura

                );


            /*}catch(\Exception $e){
                echo "Exception";
                $transaction->rollBack();
                echo "catch";
                throw $e;
            }*/

        }
    }

    public function actionPendientes_pago(){
        $cliente = '';
        $ODC = '';
        $pk_razon_social = '';
        $pk_unidad_negocio = '';
        $Factura = '';
        $Identificador = '';
        $Entregado = '';
        $factura_provision = '';
        $Limit = 'Limit 0,20';
        $actual = 1;
        $Bolsa = '';
        $tipoBusqueda = '';
        $dias_pendientes_pago = 0;

        $totaPendientePago = 0;
        $totalPendintePagoPorCliente = 0;

        $cantServicios = 0;
        $cantBolsas = 0;

        if (Yii::$app->request->isAjax) {
            $data = Yii::$app->request->post();
            $post=null;
            parse_str($data['data'],$post);

            $cliente = $post['cliente'];
            $ODC = $post['ODC'];
            $pk_razon_social = $post['pk_razon_social'];
            $pk_unidad_negocio = $post['pk_unidad_negocio'];
            //$pk_unidad_negocio = $post['pk_unidad_negocio'];
            $Factura = $post['Factura'];
            $Identificador = $post['Identificador'];
            $tipoBusqueda = $post['tipoBusqueda'];

            if($tipoBusqueda != ''){
                $Bolsa = $post['Bolsa'];
            }
            $actual = $post['page'];

            $Limit = 'Limit '.(($actual * 20)-20).', 20';

            if (!empty($post['Entregado'])) {
                $Entregado = transform_date(trim($post['Entregado']), 'Y-m-d');
            }else{
                $Entregado = '';
            }

            switch ($post['dias_pendientes_pago']) {
                case 1:
                    $dias_pendientes_pago = 15;
                    break;
                case 2:
                    $dias_pendientes_pago = 30;
                    break;
                case 3:
                    $dias_pendientes_pago = 45;
                    break;
                case 4:
                    $dias_pendientes_pago = 1000;
                    break;
            }

            if($tipoBusqueda == ''){
                //Abrir Conexion
                // PARA MOTIVOS DEL PAGINADO RECUPERAMOS EL TOTAL DE LOS REGISTROS A PAGINAR
                $count = 1;
                $connection = \Yii::$app->db;
                $facturasTotal = $connection->createCommand("CALL SP_FACTURAS_INDEXPENDIENTEPAGO_SEL_PAGINADO(:Identificador, :ODC, :Factura, :pk_razon_social, :Entregado, :cliente, :factura_provision, :Limit, :pendiente_pago, :pk_unidad_negocio, :Count)")
                    ->bindValue(':Identificador', $Identificador)
                    ->bindValue(':ODC', $ODC)
                    ->bindValue(':Factura', $Factura)
                    ->bindValue(':pk_razon_social', $pk_razon_social)
                    ->bindValue(':Entregado', $Entregado)
                    ->bindValue(':cliente', $cliente)
                    ->bindValue(':factura_provision', $factura_provision)
                    ->bindValue(':Limit', '')
                    ->bindValue(':pendiente_pago', $dias_pendientes_pago)
                    ->bindValue(':pk_unidad_negocio', $pk_unidad_negocio)
                    ->bindValue(':Count', $count)
                    ->queryAll();
                //Cerrar Conexion
                $connection->close();
                $todos = $facturasTotal[0]["num_rows"];
                $cantServicios = $todos;

                //Contador de coincidencias de facturas en Bolsas
                $connection = \Yii::$app->db;
                $totalF = $connection->createCommand("CALL SP_FACTURAS_INDEXPENDIENTEPAGO_SEL_BLS_PAGINADO('', '', :Factura, '', '', '', '', '', '', :Count)")
                    ->bindValue(':Factura', $Factura)
                    ->bindValue(':Count', $count)
                    ->queryAll();
                //Cerrar Conexion
                $connection->close();
                $cantBolsas = (($Identificador != '' || $ODC != ''|| $cliente != '' || $pk_razon_social != '' || $Entregado != ''||$pk_unidad_negocio!='') && $Factura == '') ? $cantBolsas : $totalF[0]["num_rows"];

                // RECUPERAMOS LOS NUMEROS DE LAS FACTURAS A MOSTRAR POR PAGINA, SERAN UTILIZADOS PARA REALIZAR UN FILTRO
                // PARA MOTIVOS DEL PAGINADO RECUPERAMOS EL TOTAL DE LOS REGISTROS A PAGINAR
                $count = 0;
                $connection = \Yii::$app->db;
                $aFacturas = $connection->createCommand("CALL SP_FACTURAS_INDEXPENDIENTEPAGO_SEL_PAGINADO(:Identificador, :ODC, :Factura, :pk_razon_social, :Entregado, :cliente, :factura_provision, :Limit,:pendiente_pago, :pk_unidad_negocio, :Count)")
                    ->bindValue(':Identificador', $Identificador)
                    ->bindValue(':ODC', $ODC)
                    ->bindValue(':Factura', $Factura)
                    ->bindValue(':pk_razon_social', $pk_razon_social)
                    ->bindValue(':Entregado', $Entregado)
                    ->bindValue(':cliente', $cliente)
                    ->bindValue(':factura_provision', $factura_provision)
                    ->bindValue(':Limit', $Limit)
                    ->bindValue(':pendiente_pago', $dias_pendientes_pago)
                    ->bindValue(':pk_unidad_negocio', $pk_unidad_negocio)
                    ->bindValue(':Count', $count)
                    ->queryAll();
                //Cerrar Conexion
                $connection->close();

                // CONSTRUIMOS UN ARRAY CON LOS NUMEROS DE FACTURAS A MOSTRAR EN EL LISTADO POR PAGINA
                $facturasArray = array();
                foreach($aFacturas AS $numeroFactura)
                {
                    if(isset($numeroFactura["NUM_FACTURA"]) && !empty($numeroFactura["NUM_FACTURA"]))
                    {
                         $facturasArray[] = "'".$numeroFactura["NUM_FACTURA"]. "'";
                    }
                }

                // COMBERTIMOS EL ARRAY CON UN STRING SEPARADO POR COMAS PARA REALIZAR EL FILTRO
                // es nececsario que el string se inicialize de esta manera para que el SP no truene
                $cFacturasBuscar = "' '";
                if(count($facturasArray) > 0)
                {
                    $cFacturasBuscar = implode(",", $facturasArray);
                }

                // RECUPERAMOS LA  INFORMACION DE LAS FACTURAS CON SUS HORAS POR PERIODOS PARA MOSTRAR POR PAGINA
                $connection = \Yii::$app->db;
                $facturas = $connection->createCommand("CALL SP_FACTURAS_INDEXPENDIENTEPAGO_SEL_PERIODOS_FAC(:P_NUM_FACTURA, NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL)")
                    ->bindValue(':P_NUM_FACTURA', $cFacturasBuscar)
                    ->queryAll();
                //Cerrar Conexion
                $connection->close();

                // REALIZAMOS LA AGRUPACION DE LAS FACTURAS
                $nuevo = FacturasComponent::getPendientesPagoServicios($facturas);

                //REALIZAMOS LA SUMATORIA DEL TOTAL COBRADO DEL MONTO DE LA FACTURA
                $connection = \Yii::$app->db;
                $facturasServ = $connection->createCommand("CALL SP_FACTURAS_INDEXPENDIENTEPAGO_SEL_PERIODOS_FAC(:P_IN_FACTURAS, :Identificador, :ODC, :Factura, :pk_razon_social, :Entregado, :cliente, :factura_provision,:p_limit, :pendiente_pago, :pk_unidad_negocio)")
                    ->bindValue(':P_IN_FACTURAS', '')
                    ->bindValue(':Identificador', $Identificador)
                    ->bindValue(':ODC', $ODC)
                    ->bindValue(':Factura', $Factura)
                    ->bindValue(':pk_razon_social', $pk_razon_social)
                    ->bindValue(':Entregado', $Entregado)
                    ->bindValue(':cliente', $cliente)
                    ->bindValue(':factura_provision', $factura_provision)
                    ->bindValue(':p_limit', '')
                    ->bindValue(':pendiente_pago', $dias_pendientes_pago)
                    ->bindValue(':pk_unidad_negocio', $pk_unidad_negocio)
                    ->queryAll();
                //Cerrar Conexion
                $connection->close();

                foreach ($facturasServ as $key)
                {
                    $totaPendientePago = $totaPendientePago + $key['MONTO_FACTURA'];
                }

            } else {

                 // PARA MOTIVOS DEL PAGINADO RECUPERAMOS EL TOTAL DE LOS REGISTROS A PAGINAR
                $count = 1;
                $connection = \Yii::$app->db;
                $facturasTotal = $connection->createCommand("CALL SP_FACTURAS_INDEXPENDIENTEPAGO_SEL_BLS_PAGINADO(:Identificador, :ODC, :Factura, :pk_razon_social, :Entregado, :cliente, :factura_provision, :Limit, :p_BOLSA, :Count)")
                    ->bindValue(':Identificador', $Identificador)
                    ->bindValue(':ODC', $ODC)
                    ->bindValue(':Factura', $Factura)
                    ->bindValue(':pk_razon_social', $pk_razon_social)
                    ->bindValue(':Entregado', $Entregado)
                    ->bindValue(':cliente', $cliente)
                    ->bindValue(':factura_provision', $factura_provision)
                    ->bindValue(':Limit', '')
                    ->bindValue(':p_BOLSA', $Bolsa)
                    ->bindValue(':Count', $count)
                    ->queryAll();
                //Cerrar Conexion
                $connection->close();
                $todos = $facturasTotal[0]["num_rows"];
                $cantBolsas = $todos;

                //Contador de coincidencias de facturas en Servicios
                $connection = \Yii::$app->db;
                $totalF = $connection->createCommand("CALL SP_FACTURAS_INDEXPENDIENTEPAGO_SEL_PAGINADO('', '', :Factura, '', '', '', '', '','', '', :Count)")
                    ->bindValue(':Factura', $Factura)
                    ->bindValue(':Count', $count)
                    ->queryAll();
                //Cerrar Conexion
                $connection->close();
                $cantServicios = (($Bolsa != '' || $cliente != '' || $pk_razon_social != '' || $pk_unidad_negocio != '' || $Entregado != '') && $Factura == '') ? $cantServicios : $totalF[0]["num_rows"];

               // RECUPERAMOS LOS NUMEROS DE LAS FACTURAS A MOSTRAR POR PAGINA, SERAN UTILIZADOS PARA REALIZAR UN FILTRO
                // PARA MOTIVOS DEL PAGINADO RECUPERAMOS EL TOTAL DE LOS REGISTROS A PAGINAR
                $count = 0;
                $connection = \Yii::$app->db;
                $aFacturas = $connection->createCommand("CALL SP_FACTURAS_INDEXPENDIENTEPAGO_SEL_BLS_PAGINADO(:Identificador, :ODC, :Factura, :pk_razon_social, :Entregado, :cliente, :factura_provision, :Limit, :p_BOLSA, :Count)")
                    ->bindValue(':Identificador', $Identificador)
                    ->bindValue(':ODC', $ODC)
                    ->bindValue(':Factura', $Factura)
                    ->bindValue(':pk_razon_social', $pk_razon_social)
                    ->bindValue(':Entregado', $Entregado)
                    ->bindValue(':cliente', $cliente)
                    ->bindValue(':factura_provision', $factura_provision)
                    ->bindValue(':Limit', $Limit)
                    ->bindValue(':p_BOLSA', $Bolsa)
                    ->bindValue(':Count', $count)
                    ->queryAll();
                //Cerrar Conexion
                $connection->close();

                // CONSTRUIMOS UN ARRAY CON LOS NUMEROS DE FACTURAS A MOSTRAR EN EL LISTADO POR PAGINA
                $facturasArray = array();
                foreach($aFacturas AS $numeroFactura)
                {
                    if(isset($numeroFactura["NUM_FACTURA"]) && !empty($numeroFactura["NUM_FACTURA"]))
                    {
                         $facturasArray[] = "'".$numeroFactura["NUM_FACTURA"]. "'";
                    }
                }

                // COMBERTIMOS EL ARRAY CON UN STRING SEPARADO POR COMAS PARA REALIZAR EL FILTRO
                // es nececsario que el string se inicialize de esta manera para que el SP no truene
                $cFacturasBuscar = "' '";
                if(count($facturasArray) > 0)
                {
                    $cFacturasBuscar = implode(",", $facturasArray);
                }

                 // RECUPERAMOS LA  INFORMACION DE LAS FACTURAS POR PERIODOS PARA MOSTRAR POR PAGINA
                $connection = \Yii::$app->db;
                $facturas = $connection->createCommand("CALL SP_FACTURAS_INDEXPENDIENTEPAGO_SEL_BLS_PERIODOS_FAC(:P_NUM_FACTURA, NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL)")
                    ->bindValue(':P_NUM_FACTURA', $cFacturasBuscar)
                    ->queryAll();
                //Cerrar Conexion
                $connection->close();

                // REALIZAMOS LA AGRUPACION DE LAS FACTURAS
                $nuevo = FacturasComponent::getPendientesPagoBolsas($facturas);

                //REALIZAMOS LA SUMATORIA DEL TOTAL DE LO QUE FALTA POR FACTURAR
                $connection = \Yii::$app->db;
                $facturasBls = $connection->createCommand("CALL SP_FACTURAS_INDEXPENDIENTEPAGO_SEL_BLS_PERIODOS_FAC(:IN_FACTURAS,:Identificador, :ODC, :Factura, :pk_razon_social, :Entregado, :cliente, :factura_provision, :p_limit, :p_BOLSA)")
                    ->bindValue(':IN_FACTURAS', '')
                    ->bindValue(':Identificador', $Identificador)
                    ->bindValue(':ODC', $ODC)
                    ->bindValue(':Factura', $Factura)
                    ->bindValue(':pk_razon_social', $pk_razon_social)
                    ->bindValue(':Entregado', $Entregado)
                    ->bindValue(':cliente', $cliente)
                    ->bindValue(':factura_provision', $factura_provision)
                    ->bindValue(':p_limit', '')
                    ->bindValue(':p_BOLSA', $Bolsa)
                    ->queryAll();
                //Cerrar Conexion
                $connection->close();

                foreach ($facturasBls as $key)
                {
                    $totaPendientePago = $totaPendientePago + $key['MONTO_FACTURA'];
                }
            }

            //Inicio Paginacion
            $total_paginas = ($todos < 20) ? 1 : ceil($todos / 20);
            //Fin Paginaicon


            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return ['facturas' => $nuevo,
                    'total_paginas' => $total_paginas,
                    'total_registros' => $todos,
                    'pagina' => $actual,
                    'post' => $post,
                    'totaPendientePago' => $totaPendientePago,
                    'totalPendintePagoPorCliente' => $totalPendintePagoPorCliente,
                    'cantBolsas' => $cantBolsas,
                    'cantServicios' => $cantServicios];
        } else {
            return $this->render('pendientes_pago');
        }
    }

    /**
     * Creates a new TblFacturas model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new TblFacturas();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->PK_FACTURA]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing TblFacturas model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        //$model = $this->findModel($id);
        $model = TblFacturas::find()->where(['PK_FACTURA' => $id])->limit(1)->one();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->PK_FACTURA]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    public function actionEliminar_factura(){
        if (Yii::$app->request->isAjax){
            $data=Yii::$app->request->post();
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            $pks_periodos= [];
            $periodosBitBlsDocs = [];
            //$bitBlsDocs = '';
            //$periodoEncontrado = '';
            //$fkBitBls_borrar = '';

            //Se construye un arreglo con todos los periodos relacionados a la factura que esta siendo eliminada de la bolsa.
            if($data['pks_periodos']){
                $pks_periodos = explode(',',trim($data['pks_periodos'],','));
            }

            if(isset($data['id_bolsa'])){
                //Se seleccionan todos los registros relacionados a la misma bolsa.
                $periodosBitBlsDocs = ($data['pk_proyecto'] != '') ? TblBitBlsDocs::find()->select(['PK_BIT_BLS','FK_PROYECTO_PERIODOS'])->where(['FK_BOLSA'=>$data['id_bolsa']])->asArray()->all() :
                                                                TblBitBlsDocs::find()->select(['PK_BIT_BLS','FK_PERIODOS'])->where(['FK_BOLSA'=>$data['id_bolsa']])->asArray()->all();
            }

            $periodo = '';
            $factura = '';
            $horasBolsa = '';
            $pkBolsa = [];
            $numeroDocOdc = '';
            $id_bolsa = '';
            $banEliminarFact = true;

            $pk_periodo_consultar = $data['pk_periodo_exist'];

            foreach ($pks_periodos as $pk_periodo) {
                $periodo = ($data['pk_proyecto'] != '') ? TblProyectosPeriodos::find()->where(['PK_PROYECTO_PERIODO'=>$pk_periodo])->one() : TblPeriodos::find()->where(['PK_PERIODO'=>$pk_periodo])->one();

                if(($data['pk_proyecto'] != '') ? $periodo->PK_PROYECTO_PERIODO : $periodo->PK_PERIODO == ($pk_periodo_consultar!= '' ? $pk_periodo_consultar : $pk_periodo)){
                    $factura = ($data['pk_proyecto'] != '') ? TblFacturas::find()->where(['FK_PROYECTO_DOC_FACTURA'=>$periodo->FK_DOCUMENTO_FACTURA])->andWhere(['FK_PROYECTO_PERIODO'=>($pk_periodo_consultar!= '' ? $pk_periodo_consultar : $pk_periodo)])->one() : TblFacturas::find()->where(['FK_DOC_FACTURA'=>$periodo->FK_DOCUMENTO_FACTURA])->andWhere(['FK_PERIODO'=>($pk_periodo_consultar != '' ? $pk_periodo_consultar : $pk_periodo)])->one();
                    $factura->delete();
                    $banEliminarFact = false;
                }

                if($data['asignaciones_facturas'] == "asignaciones"){
                    $connection = \Yii::$app->db;
                    $numeroDocOdc = $connection->createCommand("select td.NUM_DOCUMENTO from tbl_periodos tp join tbl_documentos td on td.PK_DOCUMENTO = tp.FK_DOCUMENTO_ODC where tp.FK_DOCUMENTO_ODC = ".$periodo['FK_DOCUMENTO_ODC'])->queryOne();
                    $connection->close();

                    if(strpos($numeroDocOdc['NUM_DOCUMENTO'], 'BLS_ODC_') !== false){
                        $pkBolsa = TblCatBolsas::find()->select(['PK_BOLSA'])->where(['LIKE', 'NUMERO_BOLSA', substr($numeroDocOdc['NUM_DOCUMENTO'], 8)])->one();
                    }
                }

                if($data['id_bolsa'] != '' || count($pkBolsa) > 0){
                    //Se recorren los registros relacionados a la misma bolsa buscando la coincidencia con el pk_periodo a eliminar y se guarda el PK_BIT_BLS donde esta contenida la coincidencia.
                    //foreach ($periodosBitBlsDocs as $fk_periodosBitBls) {
                    //   $periodoEncontrado = strpos($fk_periodosBitBls['FK_PERIODOS'], $pk_periodo.',');
                    //    if($periodoEncontrado){
                    //        $fkBitBls_borrar = $fk_periodosBitBls['PK_BIT_BLS'];
                    //    }
                    //}
                    //$bitBlsDocs = TblBitBlsDocs::find()->where(['PK_BIT_BLS'=>$fkBitBls_borrar])->one();

                    $horasBolsa = ($data['pk_proyecto'] != '') ? $periodo['HORAS_ODC'] : $periodo['HORAS'];
                    $connection = \Yii::$app->db;
                    $connection->createCommand("update tbl_cat_bolsas tcb set HORAS_DISPONIBLES = tcb.HORAS_DISPONIBLES+".$horasBolsa." where tcb.PK_BOLSA = ".(count($pkBolsa) > 0 ? $pkBolsa['PK_BOLSA'] : $data['id_bolsa']))->execute();
                    $connection->close();
                }

                //if ($data['pk_proyecto'] != '') {
                //    $factura->FK_PROYECTO_PERIODO = null;
                //    $factura->FK_PROYECTO_DOC_FACTURA = null;
                //} else {
                //    $factura->FK_PERIODO = null;
                //   $factura->FK_DOC_FACTURA = null;
                //}
                //$factura->save(false);

                $periodo->FK_DOCUMENTO_FACTURA=null;
                $periodo->FK_DOCUMENTO_FACTURA_XML=null;
                //$periodo->TARIFA_FACTURA=null;
                $periodo->HORAS_FACTURA=null;
                $periodo->MONTO_FACTURA=null;
                $periodo->save(false);

                //$bitBlsDocs->delete();
            }

            $res= [
                'data'=>$data,
                'pks_periodos'=>$pks_periodos,
                'factura'=>$factura,
                'periodo'=>$periodo,
                'periodosBitBlsDocs'=>$periodosBitBlsDocs,
                'pkBolsa'=>$pkBolsa,
                'numeroDocOdc'=>$numeroDocOdc,
                'pk_periodo_consultar'=>$pk_periodo_consultar,
                'banEliminarFact'=>$banEliminarFact,
            ];
            return $res;
        }
    }

}
