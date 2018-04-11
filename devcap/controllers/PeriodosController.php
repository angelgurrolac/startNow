<?php

namespace app\controllers;

use Yii;
use app\models\TblBitAsignacionTarifas;
use app\models\TblPeriodos;
use app\models\TblEmpleados;
use app\models\SubirArchivo;
use app\models\TblDocumentos;
use app\models\tblperfilempleados;
use app\models\TblAsignaciones;
use app\models\TblAsignacionesReemplazos;
use app\models\TblFacturas;
use app\models\TblAsignacionesSeguimiento;
use app\models\TblBitComentariosAsignaciones;
use app\models\TblCatBolsas;
use app\models\TblCatTarifas;
use app\models\TblTarifasClientes;
use app\models\TblCatUnidadesNegocio;
use app\models\TblProyectosPeriodos;
use app\models\TblDocumentosProyectos;
use app\models\TblBitBlsDocs;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\db\Query;
use yii\db\Expression;
use yii\web\UploadedFile;


/**
 * PeriodosController implements the CRUD actions for TblPeriodos model.
 */
class PeriodosController extends Controller
{
    /*public function behaviors()
    {
        return [
          'access' => [
          'class' => \yii\filters\AccessControl::className(),
          'only' => ['index', 'view','create', 'update', 'delete'],
          'rules' => [
            [
              'actions' => ['index', 'view',],
              'allow' => true,
              'roles' => ['@'],
              //'matchCallback' => function ($rule, $action) {
              // return PermissionHelpers::requireMinimumRole('Admin') && PermissionHelpers::requireStatus('Active');
              //}
            ],
            [
              'actions' => [ 'create', 'update', 'delete'],
              'allow' => true,
              'roles' => ['@'],
              //'matchCallback' => function ($rule, $action) {
              //return PermissionHelpers::requireMinimumRole('SuperUser') && PermissionHelpers::requireStatus('Active');
             // }
            ],
          ],
        ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }*/
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

    /**
     * Lists all TblPeriodos models.
     * @return mixed
     */
    public function actionIndex()
    {
        $tamanio_pagina=9;

        $modelSubirDoc = new SubirArchivo();
        $modelSubirDoc->extensions = 'pdf, docx';
        $modelSubirDoc->noRequired = true;

        $modelSubirDocHde = new SubirArchivo();
        $modelSubirDocHde->extensions = 'pdf, docx';
        $modelSubirDocHde->noRequired = true;

        $modelSubirDocFactura = new SubirArchivo();
        $modelSubirDocFactura->extensions = 'pdf, zip';
        $modelSubirDocFactura->noRequired = true;

        $modelSubirDocFacturaXML = new SubirArchivo();
        $modelSubirDocFacturaXML->extensions = 'xml, zip';
        $modelSubirDocFacturaXML->noRequired = true;

        $datos = Yii::$app->request->get();
        $connection = \Yii::$app->db;

        $sql = $connection->createCommand(" select
                            a.PK_ASIGNACION,
                            e.NOMBRE_EMP,
                            e.PK_EMPLEADO,
                            e.APELLIDO_PAT_EMP,
                            a.NOMBRE,
                            a.TARIFA,
                            a.HORAS,
                            a.FECHA_INI,
                            a.FECHA_FIN,
                            a.MONTO,
                            c.NOMBRE_CLIENTE,
                            c.PK_CLIENTE,
                            c.HORAS_ASIGNACION,
                            c.RFC,
                            a.FK_ESTATUS_ASIGNACION,
                            cont.NOMBRE_CONTACTO,
                            t.PK_CAT_TARIFA,
                            t.DESC_TARIFA
                            from tbl_asignaciones a
                                left join tbl_clientes c
                                on a.FK_CLIENTE = c.PK_CLIENTE
                                inner join tbl_cat_contactos cont
                                on a.FK_CONTACTO = cont.PK_CONTACTO
                                left join tbl_empleados e
                                on a.FK_EMPLEADO = e.PK_EMPLEADO
                                left join tbl_cat_tarifas t
                                    on t.PK_CAT_TARIFA = a.FK_CAT_TARIFA
                                where a.PK_ASIGNACION = ".($datos['id']))->queryOne();

        $modelUnidadNegocio = $connection->createCommand("select
                            a.PK_ASIGNACION,
                            u.PK_UNIDAD_NEGOCIO,
                            u.DESC_UNIDAD_NEGOCIO
                            from tbl_asignaciones a
                            inner join tbl_cat_unidades_negocio u
                            on a.PK_ASIGNACION =".($datos['id']).
                            " and a.FK_UNIDAD_NEGOCIO = u.PK_UNIDAD_NEGOCIO")->queryOne();

        $modelComentariosAsignaciones3= TblBitComentariosAsignaciones::find()->where(['FK_ASIGNACION'=>$sql['PK_ASIGNACION']])->andWhere(['=','FK_ESTATUS_ASIGNACION',5])->orderBy(['FECHA_FIN' => SORT_ASC])->asArray()->all();
        $modeloDocumentos = new TblDocumentos;
        $modeloDocumentosXML = new TblDocumentos;
        $modeloHde = new TblDocumentos;
        $modeloFactura = new TblFacturas;
        $mesPeriodo = '';
        $facturaNuevaModifica = '';

        if ($modeloDocumentos->load(Yii::$app->request->post())) {
            $datos = Yii::$app->request->get();
            $data = Yii::$app->request->post();

            $pk_documento_factura = '';
            $pk_documento_factura_xml='';
            $facturaNueva = 'false';

                //Condición sólo para documentos de tipo 'FACTURA'.

                if($data['TblDocumentos']['FK_TIPO_DOCUMENTO'] == 4) {
                    $modelSubirDocFactura->file = UploadedFile::getInstance($modelSubirDocFactura, '[7]file');
                    if (!empty($modelSubirDocFactura->file)) {
                        $fechaHoraHoy = date('YmdHis');
                        $rutaGuardado = '../uploads/DocumentosPeriodos/';
                        $nombreFisico = $fechaHoraHoy.'_'.quitar_acentos(utf8_decode($modelSubirDocFactura->file->basename));
                        $nombreBD = quitar_acentos(utf8_decode($modelSubirDocFactura->file->basename));
                        $extension = $modelSubirDocFactura->upload($rutaGuardado,$nombreFisico);
                        $rutaDoc = '/uploads/DocumentosPeriodos/';
                        $pk_documento_factura='';
                        $facturaNueva = 'true';
                    }else{
                        $pk_documento_factura= (isset($data['pk_documento_factura'])&&!empty($data['pk_documento_factura']))?$data['pk_documento_factura']:'';
                    }
                    $modelSubirDocFacturaXML->file = UploadedFile::getInstance($modelSubirDocFacturaXML, '[8]file');
                    if (!empty($modelSubirDocFacturaXML->file)) {
                        $fechaHoraHoyXML = date('YmdHis');
                        $rutaGuardadoXML = '../uploads/DocumentosPeriodos/';
                        $nombreFisicoXML = $fechaHoraHoyXML.'_'.quitar_acentos(utf8_decode($modelSubirDocFacturaXML->file->basename));
                        $nombreBDXML = quitar_acentos(utf8_decode($modelSubirDocFacturaXML->file->basename));
                        $extensionXML = $modelSubirDocFacturaXML->upload($rutaGuardadoXML,$nombreFisicoXML);
                        $rutaDocXML = '/uploads/DocumentosPeriodos/';
                        $pk_documento_factura_xml='';
                    }
                    else{
                        $pk_documento_factura_xml= (isset($data['pk_documento_factura_xml'])&&!empty($data['pk_documento_factura_xml']))?$data['pk_documento_factura_xml']:'';
                    }

                if(isset($data['periodo'])){
                    foreach ($data['periodo'] as $key => $value) {
                        $pk_periodo_factura = $value;
                        $facturaNuevaModifica = TblPeriodos::find()->where(['PK_PERIODO' => $pk_periodo_factura])->limit(1)->one();
                        // $pk_documento_factura= $facturaNuevaModifica->FK_DOCUMENTO_FACTURA;
                        // INICIO HRIBI 02/08/2016- array para concatenar en el asunto del correo, el nombre del(os) mes(es) perteneciente(s) al periodo donde se guarda el documento de Factura.
                        $mesPeriodo = $mesPeriodo.(date("m",strtotime($facturaNuevaModifica->FECHA_INI)).',');
                        // FIN HRIBI

                        /**
                         * Se crea una nueva factura
                         */
                        if($facturaNuevaModifica->FK_DOCUMENTO_FACTURA==null&&empty($pk_documento_factura)){
                            $facturaNueva = 'true';
                            $modeloDocumentos->FECHA_DOCUMENTO      = isset($data['TblDocumentos']['FECHA_DOCUMENTO']) ? transform_date($data['TblDocumentos']['FECHA_DOCUMENTO'],'Y-m-d') : null;
                            $modeloDocumentos->NUM_DOCUMENTO        = isset($data['TblDocumentos']['NUM_DOCUMENTO']) ? $data['TblDocumentos']['NUM_DOCUMENTO'] : null;
                            $modeloDocumentos->NUM_SP               = isset($data['numSPFactura']) ? $data['numSPFactura'] : null;
                            //$modeloDocumentos->TARIFA               = isset($data['TblDocumentos']['TARIFA']) ? $data['TblDocumentos']['TARIFA'] : null;
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
                            //$modeloDocumentosXML->TARIFA               = isset($data['TblDocumentos']['TARIFA']) ? $data['TblDocumentos']['TARIFA'] : null;
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

                            $modelSeguimiento = new TblAsignacionesSeguimiento;
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
                            $modeloFactura->FECHA_INGRESO_BANCO   = !empty($data['TblFacturas']['FECHA_INGRESO_BANCO']) ? transform_date($data['TblFacturas']['FECHA_INGRESO_BANCO'],'Y-m-d') : null;
                            $modeloFactura->FECHA_RECEPCION_IR    = !empty($data['TblFacturas']['FECHA_RECEPCION_IR']) ? transform_date($data['TblFacturas']['FECHA_RECEPCION_IR'],'Y-m-d') : null;
                            //$modeloFactura->FACTURA_PROVISION     = 2;//isset($data['TblFacturas']['FACTURA_PROVISION']) ? $data['TblFacturas']['FACTURA_PROVISION'] : null;
                            $modeloFactura->CONTACTO_ENTREGA      = isset($data['TblFacturas']['CONTACTO_ENTREGA']) ? $data['TblFacturas']['CONTACTO_ENTREGA'] : null;
                            $modeloFactura->FK_SERVICIO           = 1;
                            $modeloFactura->NUMERO_IR             = isset($data['TblFacturas']['NUMERO_IR']) ? $data['TblFacturas']['NUMERO_IR'] : null;
                            $modeloFactura->FK_ESTATUS            = 1;
                            $modeloFactura->FK_PORCENTAJE         = 1;
                            $modeloFactura->TOTAL_FACTURABLE      = $data['total_facturable'][$pk_periodo_factura];
                            $modeloFactura->COMENTARIOS           = isset($data['comentariosFactura']) ? $data['comentariosFactura'] : null;
                            $modeloFactura->save(false);
                            //Bitacora para crear nuevo registro de un Documento Factura en Periodos.
                            $descripcionBitacora = 'FK_ASIGNACION='.$modeloDocumentos->FK_ASIGNACION.', TIPO_DOCUMENTO=FACTURA, NUM_DOCUMENTO='.$modeloDocumentos->NUM_DOCUMENTO.', FK_CLIENTE='.$data['FK_CLIENTE'];
                            user_log_bitacora($descripcionBitacora,'Modificar Documento Factura en Periodos',$pk_periodo_factura);

                            $descripcionBitacora = 'FK_ASIGNACION='.$modeloDocumentosXML->FK_ASIGNACION.', TIPO_DOCUMENTO=XML, NUM_DOCUMENTO='.$modeloDocumentosXML->NUM_DOCUMENTO.', FK_CLIENTE='.$data['FK_CLIENTE'];
                            user_log_bitacora($descripcionBitacora,'Modificar Documento XML en Periodos',$pk_documento_factura_xml);

                            $descripcionBitacora = 'FK_DOC_FACTURA='.$modeloFactura->FK_DOC_FACTURA.', FECHA_ENTREGA_CLIENTE='.$modeloFactura->FECHA_ENTREGA_CLIENTE.', COMENTARIOS='.$modeloFactura->COMENTARIOS;
                            user_log_bitacora($descripcionBitacora,'Cargar Información de Factura',$modeloFactura->FK_PERIODO);

                        }else{
                            /**
                             * Se modifica una factura ya existente
                             */

                            if(empty($pk_documento_factura)){
                                // $modelSubirDocFactura->file = UploadedFile::getInstance($modelSubirDocFactura, '[7]file');
                                if (!empty($modelSubirDocFactura->file)) {
                                    $modeloDocumentos = new TblDocumentos;
                                    $modeloDocumentos->CONSECUTIVO_TIPO_DOC = $data['consecutivo'];
                                    $modeloDocumentos->FK_ASIGNACION        = $datos['id'];
                                    $modeloDocumentos->NOMBRE_DOCUMENTO     = $nombreBD.'.'.$extension;
                                    $modeloDocumentos->DOCUMENTO_UBICACION  = $rutaDoc.$nombreFisico.'.'.$extension;
                                    $modeloDocumentos->FK_TIPO_DOCUMENTO    = isset($data['TblDocumentos']['FK_TIPO_DOCUMENTO']) ? $data['TblDocumentos']['FK_TIPO_DOCUMENTO'] : null;
                                }else{
                                    $modeloDocumentos = TblDocumentos::findOne($facturaNuevaModifica->FK_DOCUMENTO_FACTURA);
                                }

                                $modeloDocumentos->FECHA_DOCUMENTO   = isset($data['TblDocumentos']['FECHA_DOCUMENTO']) ? transform_date($data['TblDocumentos']['FECHA_DOCUMENTO'],'Y-m-d') : null;
                                $modeloDocumentos->NUM_DOCUMENTO     = isset($data['TblDocumentos']['NUM_DOCUMENTO']) ? $data['TblDocumentos']['NUM_DOCUMENTO'] : null;
                                //$modeloDocumentos->TARIFA            = isset($data['TblDocumentos']['TARIFA']) ? $data['TblDocumentos']['TARIFA'] : null;
                                $modeloDocumentos->FK_RAZON_SOCIAL   = isset($data['TblDocumentos']['FK_RAZON_SOCIAL']) ? $data['TblDocumentos']['FK_RAZON_SOCIAL'] : null;
                                $modeloDocumentos->FK_UNIDAD_NEGOCIO = isset($data['TblDocumentos']['FK_UNIDAD_NEGOCIO']) ? $data['TblDocumentos']['FK_UNIDAD_NEGOCIO'] : null;


                                $modeloDocumentos->save(false);

                                //Bitacora para modificación de Documento Factura en Periodos.
                                $descripcionBitacora = 'FK_ASIGNACION='.$modeloDocumentos->FK_ASIGNACION.', TIPO_DOCUMENTO=FACTURA, NUM_DOCUMENTO='.$modeloDocumentos->NUM_DOCUMENTO.', FK_CLIENTE='.$modeloDocumentos->FK_CLIENTE;
                                user_log_bitacora($descripcionBitacora,'Cargar Documento Factura en Periodos',$facturaNuevaModifica->FK_DOCUMENTO_FACTURA);

                                $modelSeguimiento = new TblAsignacionesSeguimiento;
                                $modelSeguimiento->COMENTARIOS    = 'Cargar de documento Factura_'.$modeloDocumentos->NUM_DOCUMENTO.'_'.obtener_nombre_mes(date('m',strtotime($facturaNuevaModifica->FECHA_INI))).date('Y',strtotime($facturaNuevaModifica->FECHA_INI));
                                $modelSeguimiento->FECHA_REGISTRO = date('Y-m-d H:i:s');
                                $modelSeguimiento->FK_ASIGNACION  = $modeloDocumentos->FK_ASIGNACION;
                                $modelSeguimiento->FK_USUARIO     = user_info()['PK_USUARIO'];
                                $modelSeguimiento->save(false);

                                $pk_documento_factura = $modeloDocumentos->PK_DOCUMENTO;

                            }else{/*Si al actualizar no se esta dando de alta un documento nuevo, se modifican los datos del documento ya existente*/

                                $modeloDocumentos = TblDocumentos::findOne($pk_documento_factura);

                                $modeloDocumentos->NUM_DOCUMENTO     = isset($data['TblDocumentos']['NUM_DOCUMENTO']) ? $data['TblDocumentos']['NUM_DOCUMENTO'] : null;
                                $modeloDocumentos->FK_RAZON_SOCIAL   = isset($data['TblDocumentos']['FK_RAZON_SOCIAL']) ? $data['TblDocumentos']['FK_RAZON_SOCIAL'] : null;
                                $modeloDocumentos->save(false);

                                //Bitacora para modificación de Documento Factura en Periodos.
                                $descripcionBitacora = 'FK_ASIGNACION='.$modeloDocumentos->FK_ASIGNACION.', TIPO_DOCUMENTO=FACTURA, NUM_DOCUMENTO='.$modeloDocumentos->NUM_DOCUMENTO.', FK_CLIENTE='.$modeloDocumentos->FK_CLIENTE;
                                user_log_bitacora($descripcionBitacora,'Actualiza Documento Factura en Periodos',$facturaNuevaModifica->FK_DOCUMENTO_FACTURA);

                                $modelSeguimiento = new TblAsignacionesSeguimiento;
                                $modelSeguimiento->COMENTARIOS    = 'Modificación de documento Factura_'.$modeloDocumentos->NUM_DOCUMENTO.'_'.obtener_nombre_mes(date('m',strtotime($facturaNuevaModifica->FECHA_INI))).date('Y',strtotime($facturaNuevaModifica->FECHA_INI));
                                $modelSeguimiento->FECHA_REGISTRO = date('Y-m-d H:i:s');
                                $modelSeguimiento->FK_ASIGNACION  = $modeloDocumentos->FK_ASIGNACION;
                                $modelSeguimiento->FK_USUARIO     = user_info()['PK_USUARIO'];
                                $modelSeguimiento->save(false);
                            }

                            if(empty($pk_documento_factura_xml)){
                                if (!empty($modelSubirDocFacturaXML->file)) {
                                    $modeloDocumentosXML = new TblDocumentos;
                                    $modeloDocumentosXML->CONSECUTIVO_TIPO_DOC = $data['consecutivo'];
                                    $modeloDocumentosXML->FK_ASIGNACION        = $datos['id'];
                                    $modeloDocumentosXML->NOMBRE_DOCUMENTO     = $nombreBDXML.'.'.$extensionXML;
                                    $modeloDocumentosXML->DOCUMENTO_UBICACION  = $rutaDocXML.$nombreFisicoXML.'.'.$extensionXML;
                                    $modeloDocumentosXML->FK_TIPO_DOCUMENTO    = 5;
                                }else{
                                    $modeloDocumentosXML = TblDocumentos::findOne($facturaNuevaModifica->FK_DOCUMENTO_FACTURA_XML);
                                }
                                if($modeloDocumentosXML){
                                    $modeloDocumentosXML->FECHA_DOCUMENTO   = isset($data['TblDocumentos']['FECHA_DOCUMENTO']) ? transform_date($data['TblDocumentos']['FECHA_DOCUMENTO'],'Y-m-d') : null;
                                    $modeloDocumentosXML->NUM_DOCUMENTO     = isset($data['TblDocumentos']['NUM_DOCUMENTO']) ? $data['TblDocumentos']['NUM_DOCUMENTO'] : null;
                                    //$modeloDocumentosXML->TARIFA            = isset($data['TblDocumentos']['TARIFA']) ? $data['TblDocumentos']['TARIFA'] : null;
                                    $modeloDocumentosXML->FK_RAZON_SOCIAL   = isset($data['TblDocumentos']['FK_RAZON_SOCIAL']) ? $data['TblDocumentos']['FK_RAZON_SOCIAL'] : null;
                                    $modeloDocumentosXML->FK_UNIDAD_NEGOCIO = isset($data['TblDocumentos']['FK_UNIDAD_NEGOCIO']) ? $data['TblDocumentos']['FK_UNIDAD_NEGOCIO'] : null;

                                    $modeloDocumentosXML->save(false);
                                    $pk_documento_factura_xml = $modeloDocumentosXML->PK_DOCUMENTO;
                                }
                            }

                            $modeloFacturaUpd = TblFacturas::find()
                                                       ->where(['=','FK_PERIODO', $pk_periodo_factura])
                                                       //->andWhere(['=','FK_ESTATUS', '1'])
                                                       //->andWhere(['=','FK_ESTATUS', '2'])
                                                       ->limit(1)
                                                       ->one();

                            if($modeloFacturaUpd){
                                $modeloFacturaUpd->FK_DOC_FACTURA        = $pk_documento_factura;
                                $modeloFacturaUpd->FECHA_EMISION         = !empty($data['TblFacturas']['FECHA_EMISION']) ? transform_date($data['TblFacturas']['FECHA_EMISION'],'Y-m-d') : null;
                                $modeloFacturaUpd->FECHA_ENTREGA_CLIENTE = !empty($data['TblFacturas']['FECHA_ENTREGA_CLIENTE']) ? transform_date($data['TblFacturas']['FECHA_ENTREGA_CLIENTE'],'Y-m-d') : null;
                                $modeloFacturaUpd->FECHA_INGRESO_BANCO   = !empty($data['TblFacturas']['FECHA_INGRESO_BANCO']) ? transform_date($data['TblFacturas']['FECHA_INGRESO_BANCO'],'Y-m-d') : null;
                                $modeloFacturaUpd->FECHA_RECEPCION_IR    = !empty($data['TblFacturas']['FECHA_RECEPCION_IR']) ? transform_date($data['TblFacturas']['FECHA_RECEPCION_IR'],'Y-m-d') : null;
                                //$modeloFacturaUpd->FACTURA_PROVISION     = 2;//isset($data['TblFacturas']['FACTURA_PROVISION']) ? $data['TblFacturas']['FACTURA_PROVISION'] : null;
                                $modeloFacturaUpd->CONTACTO_ENTREGA      = isset($data['TblFacturas']['CONTACTO_ENTREGA']) ? $data['TblFacturas']['CONTACTO_ENTREGA'] : null;
                                $modeloFacturaUpd->FK_SERVICIO           = 1;
                                $modeloFacturaUpd->NUMERO_IR             = isset($data['TblFacturas']['NUMERO_IR']) ? $data['TblFacturas']['NUMERO_IR'] : null;
                                if($modeloFacturaUpd->FECHA_INGRESO_BANCO != null){
                                    $modeloFacturaUpd->FK_ESTATUS = 2;
                                }else{
                                    $modeloFacturaUpd->FK_ESTATUS = 1;
                                }
                                $modeloFacturaUpd->TOTAL_FACTURABLE      = $data['total_facturable'][$pk_periodo_factura];
                                $modeloFacturaUpd->COMENTARIOS           = isset($data['comentariosFactura']) ? $data['comentariosFactura'] : null;
                                $modeloFacturaUpd->save(false);

                                //Bitacora para modificación de registro en tblFacturas.
                                $descripcionBitacora = 'FK_DOC_FACTURA='.$modeloFacturaUpd->FK_DOC_FACTURA.', FECHA_ENTREGA_CLIENTE='.$modeloFacturaUpd->FECHA_ENTREGA_CLIENTE.
                                ', FECHA_INGRESO_BANCO='.$modeloFacturaUpd->FECHA_INGRESO_BANCO.', COMENTARIOS='.$modeloFacturaUpd->COMENTARIOS;
                                user_log_bitacora($descripcionBitacora,'Modificar Información de Factura',$modeloFactura->FK_PERIODO);
                            }else{
                                $modeloFactura = new TblFacturas;
                                $modeloFactura->FK_PERIODO            = $pk_periodo_factura;
                                $modeloFactura->FK_DOC_FACTURA        = $pk_documento_factura;
                                $modeloFactura->FECHA_EMISION         = !empty($data['TblFacturas']['FECHA_EMISION']) ? transform_date($data['TblFacturas']['FECHA_EMISION'],'Y-m-d') : null;
                                $modeloFactura->FECHA_ENTREGA_CLIENTE = !empty($data['TblFacturas']['FECHA_ENTREGA_CLIENTE']) ? transform_date($data['TblFacturas']['FECHA_ENTREGA_CLIENTE'],'Y-m-d') : null;
                                $modeloFactura->FECHA_INGRESO_BANCO   = !empty($data['TblFacturas']['FECHA_INGRESO_BANCO']) ? transform_date($data['TblFacturas']['FECHA_INGRESO_BANCO'],'Y-m-d') : null;
                                $modeloFactura->FECHA_RECEPCION_IR    = !empty($data['TblFacturas']['FECHA_RECEPCION_IR']) ? transform_date($data['TblFacturas']['FECHA_RECEPCION_IR'],'Y-m-d') : null;
                                //$modeloFactura->FACTURA_PROVISION     = 2;//isset($data['TblFacturas']['FACTURA_PROVISION']) ? $data['TblFacturas']['FACTURA_PROVISION'] : null;
                                $modeloFactura->CONTACTO_ENTREGA      = isset($data['TblFacturas']['CONTACTO_ENTREGA']) ? $data['TblFacturas']['CONTACTO_ENTREGA'] : null;
                                $modeloFactura->FK_SERVICIO           = 1;
                                $modeloFactura->NUMERO_IR             = isset($data['TblFacturas']['NUMERO_IR']) ? $data['TblFacturas']['NUMERO_IR'] : null;
                                $modeloFactura->FK_ESTATUS            = 1;
                                $modeloFactura->FK_PORCENTAJE         = 1;
                                $modeloFactura->TOTAL_FACTURABLE      = $data['total_facturable'][$pk_periodo_factura];
                                $modeloFactura->COMENTARIOS           = isset($data['comentariosFactura']) ? $data['comentariosFactura'] : null;
                                $modeloFactura->save(false);

                                //Bitacora para crear nuevo registro de un Documento Factura en Periodos.
                                $descripcionBitacora = 'FK_ASIGNACION='.$modeloDocumentos->FK_ASIGNACION.', TIPO_DOCUMENTO=FACTURA, NUM_DOCUMENTO='.$modeloDocumentos->NUM_DOCUMENTO.', FK_CLIENTE='.$data['FK_CLIENTE'];
                                user_log_bitacora($descripcionBitacora,'Modificar Documento Factura en Periodos',$pk_periodo_factura);

                                $descripcionBitacora = 'FK_DOC_FACTURA='.$modeloFactura->FK_DOC_FACTURA.', FECHA_ENTREGA_CLIENTE='.$modeloFactura->FECHA_ENTREGA_CLIENTE.', COMENTARIOS='.$modeloFactura->COMENTARIOS;
                                user_log_bitacora($descripcionBitacora,'Cargar Información de Factura',$modeloFactura->FK_PERIODO);

                            }
                        }
                        $modelPeriodosUpd = TblPeriodos::find()->where(['PK_PERIODO' => $pk_periodo_factura])->limit(1)->one();
                        $modelPeriodosUpd->FK_DOCUMENTO_FACTURA = $pk_documento_factura;
                        $modelPeriodosUpd->FK_DOCUMENTO_FACTURA_XML = $pk_documento_factura_xml;
                        //$modelPeriodosUpd->TARIFA_FACTURA = $data['TblDocumentos']['TARIFA'];
                        $modelPeriodosUpd->HORAS_FACTURA = $data['horas_factura'][$pk_periodo_factura];
                        $modelPeriodosUpd->MONTO_FACTURA = $data['monto_factura'][$pk_periodo_factura];
                        $modelPeriodosUpd->save(false);

                    }
                }

                    if(isset($nombreBD) && $facturaNueva = 'true'){
                        $modeloDocumentoFactura = TblDocumentos::findOne($pk_documento_factura);
                        $ubicacion_doc_factura = $modeloDocumentoFactura['DOCUMENTO_UBICACION'];
                        $ubicacion_doc_factura_xml = '';

                        if($pk_documento_factura_xml != null){
                            $modeloDocumentoFacturaXML = TblDocumentos::findOne($pk_documento_factura_xml);
                            $ubicacion_doc_factura_xml = $modeloDocumentoFacturaXML['DOCUMENTO_UBICACION'];
                        }

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

                        $is_pr = get_config('CONFIG','PRODUCCION');
                        $de = get_config('PERIODOS','CORREO_REMITENTE_FACTURA');
                        $prefix_correo = '';

                        if($is_pr){

                            $arr_validar_factura_btn= explode(',',get_config('PERIODOS','PARA_FACTURA_BTN'));

                            //Valida que sea Unidad de negocio Banorte Monterrey
                            if($modelUnidadNegocio['DESC_UNIDAD_NEGOCIO'] == 'Banorte Monterrey' || $modelUnidadNegocio['DESC_UNIDAD_NEGOCIO'] == 'Banorte DF'){
                                //Valida que sea cliente Banorte -> ($sql['RFC']=='BMN930209927) || ($sql['RFC']=='ITA081127UZ1')
                                if (in_array($sql['RFC'],$arr_validar_factura_btn)) {
                                    $prefix_correo = get_config('PERIODOS','PREFIX_BANORTE');
                                    $para= explode(',',get_config('PERIODOS','CORREO_DESTINO_FACTURA_BTN'));
                                // Valida que sea cliente IBM -> ($sql['RFC']=='IMC9701024T5')
                                }elseif($sql['RFC']==get_config('PERIODOS','IS_IBM')){
                                    $prefix_correo = get_config('PERIODOS','PREFIX_IBM');
                                    $para= explode(',',get_config('PERIODOS','CORREO_DESTINO_FACTURA_IBM'));
                                // Validad que se cliente STEFANINI -> ($sql['RFC']=='STE9607266C7')
                                }elseif($sql['RFC']==get_config('PERIODOS','IS_STEFANINI')){
                                    $prefix_correo = get_config('PERIODOS','PREFIX_STEFANINI');
                                    $para= explode(',',get_config('PERIODOS','CORREO_DESTINO_FACTURA_STE'));
                                }
                            }elseif($modelUnidadNegocio['DESC_UNIDAD_NEGOCIO'] == 'Nuevos Negocios'){
                                $prefix_correo = get_config('PERIODOS','PREFIX_NUEVOS_NEGOCIOS');
                                $para= explode(',',get_config('PERIODOS','CORREO_DESTINO_FACTURA_NN'));
                            //Validación para cualquier otra unidad de nebocio y/o cliente que no es considerada en las condiciones anteriores.
                            }else{
                                $prefix_correo = '';
                                $para= explode(',',get_config('PERIODOS','CORREO_DESTINO_FACTURA_OTROS'));
                            }
                        //Validación cuando se ejecuta un ambiente distinto a PR
                        }else{
                            $prefix_correo = 'PRUEBA QA ';
                            $para= explode(',',get_config('PERIODOS','CORREO_DESTINO_FACTURA_BTN'));
                        }

                        // INICIO MOD HRIBI - Concatenación en el asunto del correo del número del documento, mes y año pertenecientes al periodo donde se guarda el doc HdE.
                        $asunto= $prefix_correo.' Factura_'.$modeloDocumentoFactura['NUM_DOCUMENTO'].'_'.$sql['NOMBRE'];
                        // FIN MOD HRIBI
                        $nombre_usuario= user_info()['NOMBRE_COMPLETO'];

                        $mensaje='<style>p, ul, li {font-family: Arial, Helvetica, sans-serif; font-size: 12px;}</style>
                            <p>Buen d&iacute;a</p>
                            <p>Adjunto Factura y XML de la facturaci&oacute;n</p>
                            <p>Los periodos afectados por esta factura son: </p>';

                            $mesPeriodos = trim($mesPeriodo,',');
                            foreach (explode(",",$mesPeriodos) as $key => $mes) {
                                $mensaje .='<p>'.$arrayMeses[$mes].' del '.date("Y",strtotime($facturaNuevaModifica->FECHA_INI)).'</p>';
                            }
                            $mensaje .= '<p>Saludos y Gracias.</p>';

                        if($ubicacion_doc_factura_xml != ''){
                            $enviado = send_mail($de,$para, $asunto, $mensaje,['..'.$ubicacion_doc_factura,'..'.$ubicacion_doc_factura_xml]);
                        }else{
                            $enviado = send_mail($de,$para, $asunto, $mensaje,['..'.$ubicacion_doc_factura]);
                        }

                    }

                }

                if(isset($data['es_bolsaHDE'])&&$data['es_bolsaHDE']!=1){
                    //Condición sólo para documentos de tipo 'HDE'.
                    if($data['TblDocumentos']['FK_TIPO_DOCUMENTO'] == 3) {

                        $hdeNuevaModifica = TblPeriodos::find()->where(['PK_PERIODO' => $data['pk_periodo_hde']])->limit(1)->one();

                        if($hdeNuevaModifica->FK_DOCUMENTO_HDE == null) {
                            $modeloDocumentos->FECHA_DOCUMENTO      = isset($data['TblDocumentos']['FECHA_DOCUMENTO']) ? transform_date($data['TblDocumentos']['FECHA_DOCUMENTO'],'Y-m-d') : null;
                            $modeloDocumentos->NUM_DOCUMENTO        = isset($data['TblDocumentos']['NUM_DOCUMENTO']) ? $data['TblDocumentos']['NUM_DOCUMENTO'] : null;
                            $modeloDocumentos->NUM_SP               = isset($data['TblDocumentos']['NUM_SP']) ? $data['TblDocumentos']['NUM_SP'] : null;
                            //$modeloDocumentos->TARIFA               = isset($data['TblDocumentos']['TARIFA']) ? $data['TblDocumentos']['TARIFA'] : null;
                            $modeloDocumentos->FK_RAZON_SOCIAL      = isset($data['TblDocumentos']['FK_RAZON_SOCIAL']) ? $data['TblDocumentos']['FK_RAZON_SOCIAL'] : null;
                            $modeloDocumentos->FK_ASIGNACION        = $datos['id'];
                            $modeloDocumentos->FK_TIPO_DOCUMENTO    = isset($data['TblDocumentos']['FK_TIPO_DOCUMENTO']) ? $data['TblDocumentos']['FK_TIPO_DOCUMENTO'] : null;
                            $modeloDocumentos->FK_UNIDAD_NEGOCIO    = isset($data['TblDocumentos']['FK_UNIDAD_NEGOCIO']) ? $data['TblDocumentos']['FK_UNIDAD_NEGOCIO'] : null;
                            $modeloDocumentos->FECHA_REGISTRO       = date('Y-m-d H:i:s');
                            $modeloDocumentos->FK_CLIENTE           = $data['FK_CLIENTE'];
                            $modeloDocumentos->CONSECUTIVO_TIPO_DOC = $data['consecutivo'];

                            $modelSubirDocHde->file = UploadedFile::getInstance($modelSubirDocHde, '[6]file');
                            if (!empty($modelSubirDocHde->file)) {
                                $fechaHoraHoy                          = date('YmdHis');
                                $rutaGuardado                          = '../uploads/DocumentosPeriodos/';
                                $nombreFisico                          = $fechaHoraHoy.'_'.quitar_acentos(utf8_decode($modelSubirDocHde->file->basename));
                                $nombreBD                              = quitar_acentos(utf8_decode($modelSubirDocHde->file->basename));
                                $extension                             = $modelSubirDocHde->upload($rutaGuardado,$nombreFisico);
                                $rutaDoc                               = '/uploads/DocumentosPeriodos/';
                                $modeloDocumentos->NOMBRE_DOCUMENTO    = $nombreBD.'.'.$extension;
                                $modeloDocumentos->DOCUMENTO_UBICACION = $rutaDoc.$nombreFisico.'.'.$extension;
                             }
                            $modeloDocumentos->save(false);
                            //Bitacora para crear nuevo registro de un Documento HDE en Periodos.
                            $descripcionBitacora              = 'FK_ASIGNACION='.$modeloDocumentos->FK_ASIGNACION.', TIPO_DOCUMENTO=HDE, NUM_DOCUMENTO='.$modeloDocumentos->NUM_DOCUMENTO.', FK_CLIENTE='.$modeloDocumentos->FK_CLIENTE;
                            user_log_bitacora($descripcionBitacora,'Modificar Documento HDE en Periodos',$hdeNuevaModifica->FK_DOCUMENTO_HDE);

                            $modelSeguimiento                 = new TblAsignacionesSeguimiento;
                            // $modelSeguimiento->load(Yii    ::$app->request->post());
                            $modelSeguimiento->COMENTARIOS    = 'Modificaci&oacute;n de documento HDE_'.$modeloDocumentos->NUM_DOCUMENTO.'_'.obtener_nombre_mes(date("m",strtotime($hdeNuevaModifica->FECHA_INI))).date("Y",strtotime($hdeNuevaModifica->FECHA_INI));
                            $modelSeguimiento->FECHA_REGISTRO = date('Y-m-d H:i:s');
                            $modelSeguimiento->FK_ASIGNACION  = $modeloDocumentos->FK_ASIGNACION;
                            $modelSeguimiento->FK_USUARIO     = user_info()['PK_USUARIO'];
                            $modelSeguimiento->save(false);

                            $id = $modeloDocumentos->PK_DOCUMENTO;
                        }else{
                            $modeloDocumentos                    = TblDocumentos::findOne($hdeNuevaModifica->FK_DOCUMENTO_HDE);
                            $modeloDocumentos->FECHA_DOCUMENTO   = isset($data['TblDocumentos']['FECHA_DOCUMENTO']) ? transform_date($data['TblDocumentos']['FECHA_DOCUMENTO'],'Y-m-d') : null;
                            $modeloDocumentos->NUM_DOCUMENTO     = isset($data['TblDocumentos']['NUM_DOCUMENTO']) ? $data['TblDocumentos']['NUM_DOCUMENTO'] : null;
                            //$modeloDocumentos->TARIFA            = isset($data['TblDocumentos']['TARIFA']) ? $data['TblDocumentos']['TARIFA'] : null;
                            $modeloDocumentos->FK_RAZON_SOCIAL   = isset($data['TblDocumentos']['FK_RAZON_SOCIAL']) ? $data['TblDocumentos']['FK_RAZON_SOCIAL'] : null;
                            $modeloDocumentos->FK_UNIDAD_NEGOCIO = isset($data['TblDocumentos']['FK_UNIDAD_NEGOCIO']) ? $data['TblDocumentos']['FK_UNIDAD_NEGOCIO'] : null;


                                $modelSubirDocHde->file = UploadedFile::getInstance($modelSubirDocHde, '[6]file');
                                if (!empty($modelSubirDocHde->file)) {
                                    $fechaHoraHoy                          = date('YmdHis');
                                    $rutaGuardado                          = '../uploads/DocumentosPeriodos/';
                                    $nombreFisico                          = $fechaHoraHoy.'_'.quitar_acentos(utf8_decode($modelSubirDocHde->file->basename));
                                    $nombreBD                              = quitar_acentos(utf8_decode($modelSubirDocHde->file->basename));
                                    $extension                             = $modelSubirDocHde->upload($rutaGuardado,$nombreFisico);
                                    $rutaDoc                               = '/uploads/DocumentosPeriodos/';
                                    $modeloDocumentos->NOMBRE_DOCUMENTO    = $nombreBD.'.'.$extension;
                                    $modeloDocumentos->DOCUMENTO_UBICACION = $rutaDoc.$nombreFisico.'.'.$extension;
                                }
                            $modeloDocumentos->save(false);
                            //Bitacora para modificación de Documento HDE en Periodos.
                            $descripcionBitacora              = 'FK_ASIGNACION='.$modeloDocumentos->FK_ASIGNACION.', TIPO_DOCUMENTO=HDE, NUM_DOCUMENTO='.$modeloDocumentos->NUM_DOCUMENTO.', FK_CLIENTE='.$modeloDocumentos->FK_CLIENTE;
                            user_log_bitacora($descripcionBitacora,'Cargar Documento HDE en Periodos',$data['pk_periodo_hde']);

                            $modelSeguimiento                 = new TblAsignacionesSeguimiento;
                            // $modelSeguimiento->load(Yii    ::$app->request->post());
                            $modelSeguimiento->COMENTARIOS    = 'Cargar de documento HDE_'.$modeloDocumentos->NUM_DOCUMENTO.'_'.obtener_nombre_mes(date("m",strtotime($hdeNuevaModifica->FECHA_INI))).date("Y",strtotime($hdeNuevaModifica->FECHA_INI));
                            $modelSeguimiento->FECHA_REGISTRO = date('Y-m-d H:i:s');
                            $modelSeguimiento->FK_ASIGNACION  = $modeloDocumentos->FK_ASIGNACION;
                            $modelSeguimiento->FK_USUARIO     = user_info()['PK_USUARIO'];
                            $modelSeguimiento->save(false);

                            $id = $modeloDocumentos->PK_DOCUMENTO;
                        }
                        if(isset($nombreBD) && $hdeNuevaModifica->FK_DOCUMENTO_FACTURA == null){
                            $modeloDocumentoODC= TblDocumentos::findOne($hdeNuevaModifica->FK_DOCUMENTO_ODC);
                            $ubicacion_doc_odc= $modeloDocumentoODC->DOCUMENTO_UBICACION;
                            $modeloDocumentoHDE= TblDocumentos::findOne($id);
                            $ubicacion_doc_hde= $modeloDocumentoHDE->DOCUMENTO_UBICACION;

                            // INICIO HRIBI - array para concatenar en el asunto del correo, el nombre del mes perteneciente al periodo donde se guarda el doc HdE.
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
                            $mesPeriodo = date("m",strtotime($hdeNuevaModifica->FECHA_INI));
                            // FIN HRIBI

                            $is_pr= get_config('CONFIG','PRODUCCION');
                            $de= get_config('PERIODOS','CORREO_REMITENTE_FACTURA');
                            $prefix_correo = '';
                            if($is_pr){

                                $arr_validar_factura_btn= explode(',',get_config('PERIODOS','PARA_FACTURA_BTN'));

                                //Banorte
                                // if($sql['RFC']=='BMN930209927'||$sql['RFC']=='ITA081127UZ1')
                                if (in_array($sql['RFC'],$arr_validar_factura_btn)) {
                                    //$para = array('jorge.mendiola@eisei.net.mx','nestor.tristan@eisei.net.mx','maria.delgado@eisei.net.mx');
                                    $prefix_correo= get_config('PERIODOS','PREFIX_BANORTE');
                                    $para= explode(',',get_config('PERIODOS','CORREO_DESTINO_POR_FACTURAR_BTN'));
                                // if($sql['RFC']=='IMC9701024T5')
                                }elseif($sql['RFC']==get_config('PERIODOS','IS_IBM')){
                                    //$para = array('jorge.mendiola@eisei.net.mx','maria.delgado@eisei.net.mx');
                                    $prefix_correo= get_config('PERIODOS','PREFIX_IBM');
                                    $para= explode(',',get_config('PERIODOS','CORREO_DESTINO_POR_FACTURAR_IBM'));
                                // if($sql['RFC']=='STE9607266C7')
                                }elseif($sql['RFC']==get_config('PERIODOS','IS_STEFANINI')){
                                    //$para = array('eveylin@eisei.net.mx','maria.delgado@eisei.net.mx');
                                    $prefix_correo= get_config('PERIODOS','PREFIX_STEFANINI');
                                    $para= explode(',',get_config('PERIODOS','CORREO_DESTINO_POR_FACTURAR_STE'));
                                }else{
                                    $para= explode(',',get_config('PERIODOS','CORREO_DESTINO_POR_FACTURAR_OTROS'));
                                }

                            }else{
                                $para= explode(',',get_config('PERIODOS','CORREO_DESTINO_POR_FACTURAR_OTROS'));
                            }
                            // INICIO MOD HRIBI - Concatenación en el asunto del correo del número del documento, mes y año pertenecientes al periodo donde se guarda el doc HdE.
                            $asunto= $prefix_correo.' Pendiente por facturar '.'OdC_'.$modeloDocumentoODC->NUM_DOCUMENTO.'_'.$arrayMeses[$mesPeriodo].date("Y",strtotime($hdeNuevaModifica->FECHA_INI));;

                            // FIN MOD HRIBI
                            $nombre_usuario= user_info()['NOMBRE_COMPLETO'];

                            $mensaje='<style>p, ul, li {font-family: Arial, Helvetica, sans-serif; font-size: 12px;}</style>
                                <p>Buen d&iacute;a</p>
                                <p>Adjunto OdC y HdE para su facturaci&oacute;n</p>
                                <p>Saludos y Gracias.</p>';

                            $enviado = send_mail($de,$para, $asunto, $mensaje,['..'.$ubicacion_doc_hde,'..'.$ubicacion_doc_odc]);
                        }
                    }
                }

                /*if($data['TblDocumentos']['FK_TIPO_DOCUMENTO'] == 2) {

                    $odcNuevaModifica = TblPeriodos::find()->where(['PK_PERIODO' => $data['pk_periodo_odc']])->limit(1)->one();

                    if($odcNuevaModifica->FK_DOCUMENTO_ODC == null) {
                        $modeloDocumentos->FECHA_DOCUMENTO = isset($data['TblDocumentos']['FECHA_DOCUMENTO']) ? transform_date($data['TblDocumentos']['FECHA_DOCUMENTO'],'Y-m-d') : null;
                        $modeloDocumentos->NUM_DOCUMENTO = isset($data['TblDocumentos']['NUM_DOCUMENTO']) ? $data['TblDocumentos']['NUM_DOCUMENTO'] : null;
                        $modeloDocumentos->NUM_SP = isset($data['TblDocumentos']['NUM_SP']) ? $data['TblDocumentos']['NUM_SP'] : null;
                        $modeloDocumentos->TARIFA = isset($data['TblDocumentos']['TARIFA']) ? $data['TblDocumentos']['TARIFA'] : null;
                        $modeloDocumentos->FK_RAZON_SOCIAL = isset($data['TblDocumentos']['FK_RAZON_SOCIAL']) ? $data['TblDocumentos']['FK_RAZON_SOCIAL'] : null;
                        $modeloDocumentos->FK_ASIGNACION = $datos['id'];
                        $modeloDocumentos->FK_TIPO_DOCUMENTO = isset($data['TblDocumentos']['FK_TIPO_DOCUMENTO']) ? $data['TblDocumentos']['FK_TIPO_DOCUMENTO'] : null;
                        $modeloDocumentos->FK_UNIDAD_NEGOCIO = isset($data['TblDocumentos']['FK_UNIDAD_NEGOCIO']) ? $data['TblDocumentos']['FK_UNIDAD_NEGOCIO'] : null;
                        $modeloDocumentos->FECHA_REGISTRO = date('Y-m-d H:i:s');
                        $modeloDocumentos->FK_CLIENTE = $data['FK_CLIENTE'];
                        $modeloDocumentos->CONSECUTIVO_TIPO_DOC = $data['consecutivo'];

                        $modelSubirDoc->file = UploadedFile::getInstance($modelSubirDoc, '[5]file');
                        if (!empty($modelSubirDoc->file)) {
                            $fechaHoraHoy = date('YmdHis');
                            $rutaGuardado = '../uploads/DocumentosPeriodos/';
                            $nombreFisico = $fechaHoraHoy.'_'.$modelSubirDoc->file->basename;
                            $nombreBD = $modelSubirDoc->file->basename;
                            $extension = $modelSubirDoc->upload($rutaGuardado,$nombreFisico);
                            $rutaDoc = '/uploads/DocumentosPeriodos/';
                            $modeloDocumentos->NOMBRE_DOCUMENTO = $nombreBD.'.'.$extension;
                            $modeloDocumentos->DOCUMENTO_UBICACION = $rutaDoc.$nombreFisico.'.'.$extension;
                         }
                        $modeloDocumentos->save(false);
                        $id = $modeloDocumentos->PK_DOCUMENTO;
                    }else{
                        $modeloDocumentos = TblDocumentos::findOne($odcNuevaModifica->FK_DOCUMENTO_ODC);
                        $modeloDocumentos->FECHA_DOCUMENTO = isset($data['TblDocumentos']['FECHA_DOCUMENTO']) ? transform_date($data['TblDocumentos']['FECHA_DOCUMENTO'],'Y-m-d') : null;
                        $modeloDocumentos->NUM_DOCUMENTO = isset($data['TblDocumentos']['NUM_DOCUMENTO']) ? $data['TblDocumentos']['NUM_DOCUMENTO'] : null;
                        $modeloDocumentos->TARIFA = isset($data['TblDocumentos']['TARIFA']) ? $data['TblDocumentos']['TARIFA'] : null;
                        $modeloDocumentos->FK_RAZON_SOCIAL = isset($data['TblDocumentos']['FK_RAZON_SOCIAL']) ? $data['TblDocumentos']['FK_RAZON_SOCIAL'] : null;
                        $modeloDocumentos->FK_UNIDAD_NEGOCIO = isset($data['TblDocumentos']['FK_UNIDAD_NEGOCIO']) ? $data['TblDocumentos']['FK_UNIDAD_NEGOCIO'] : null;


                            $modelSubirDoc->file = UploadedFile::getInstance($modelSubirDoc, '[5]file');
                            if (!empty($modelSubirDoc->file)) {
                                $fechaHoraHoy = date('YmdHis');
                                $rutaGuardado = '../uploads/DocumentosPeriodos/';
                                $nombreFisico = $fechaHoraHoy.'_'.$modelSubirDoc->file->basename;
                                $nombreBD = $modelSubirDoc->file->basename;
                                $extension = $modelSubirDoc->upload($rutaGuardado,$nombreFisico);
                                $rutaDoc = '/uploads/DocumentosPeriodos/';
                                $modeloDocumentos->NOMBRE_DOCUMENTO = $nombreBD.'.'.$extension;
                                $modeloDocumentos->DOCUMENTO_UBICACION = $rutaDoc.$nombreFisico.'.'.$extension;
                            }

                        $modeloDocumentos->save(false);
                        $id = $modeloDocumentos->PK_DOCUMENTO;
                    }
                }*/
                /*if($data['TblDocumentos']['FK_TIPO_DOCUMENTO'] == 3) {

                    $modelSubirDocHde->file = UploadedFile::getInstance($modelSubirDocHde, 'file');
                    if (!empty($modelSubirDocHde->file)) {
                        $fechaHoraHoy = date('YmdHis');
                        $rutaGuardado = '../uploads/DocumentosPeriodos/';
                        $nombreFisico = $fechaHoraHoy.'_'.$modelSubirDocHde->file->basename;
                        $nombreBD = $modelSubirDocHde->file->basename;
                        $extension = $modelSubirDocHde->upload($rutaGuardado,$nombreFisico);
                        $rutaDoc = '/uploads/DocumentosPeriodos/';
                        $modeloDocumentos->NOMBRE_DOCUMENTO = $nombreBD.'.'.$extension;
                        $modeloDocumentos->DOCUMENTO_UBICACION = $rutaDoc.$nombreFisico.'.'.$extension;
                    }

                    if ($modelSubirDocHde->file == null) {
                        $modeloDocumentos = TblDocumentos::findOne($data['TblDocumentos']['PK_DOCUMENTO']);
                        $modeloDocumentos->NUM_DOCUMENTO = isset($data['TblDocumentos']['NUM_DOCUMENTO']) ? $data['TblDocumentos']['NUM_DOCUMENTO'] : null;
                        $modeloDocumentos->NUM_SP = isset($data['TblDocumentos']['NUM_SP']) ? $data['TblDocumentos']['NUM_SP'] : null;
                        $modeloDocumentos->TARIFA = isset($data['TblDocumentos']['TARIFA']) ? $data['TblDocumentos']['TARIFA'] : null;
                        $modeloDocumentos->FK_RAZON_SOCIAL = isset($data['TblDocumentos']['FK_RAZON_SOCIAL']) ? $data['TblDocumentos']['FK_RAZON_SOCIAL'] : null;
                        $modeloDocumentos->FK_UNIDAD_NEGOCIO = isset($data['TblDocumentos']['FK_UNIDAD_NEGOCIO']) ? $data['TblDocumentos']['FK_UNIDAD_NEGOCIO'] : null;
                    } else {
                        $modeloDocumentos->NUM_DOCUMENTO = isset($data['TblDocumentos']['NUM_DOCUMENTO']) ? $data['TblDocumentos']['NUM_DOCUMENTO'] : null;
                        $modeloDocumentos->NUM_SP = isset($data['TblDocumentos']['NUM_SP']) ? $data['TblDocumentos']['NUM_SP'] : null;
                        $modeloDocumentos->TARIFA = isset($data['TblDocumentos']['TARIFA']) ? $data['TblDocumentos']['TARIFA'] : null;
                        $modeloDocumentos->FK_RAZON_SOCIAL = isset($data['TblDocumentos']['FK_RAZON_SOCIAL']) ? $data['TblDocumentos']['FK_RAZON_SOCIAL'] : null;
                        $modeloDocumentos->FK_ASIGNACION = $datos['id'];
                        $modeloDocumentos->FK_TIPO_DOCUMENTO = isset($data['TblDocumentos']['FK_TIPO_DOCUMENTO']) ? $data['TblDocumentos']['FK_TIPO_DOCUMENTO'] : null;
                        $modeloDocumentos->FK_UNIDAD_NEGOCIO = isset($data['TblDocumentos']['FK_UNIDAD_NEGOCIO']) ? $data['TblDocumentos']['FK_UNIDAD_NEGOCIO'] : null;
                        $modeloDocumentos->FECHA_REGISTRO = date('Y-m-d H:i:s');
                        $modeloDocumentos->FK_CLIENTE = $data['FK_CLIENTE'];
                        $modeloDocumentos->CONSECUTIVO_TIPO_DOC = $data['consecutivo'];
                    }
                    $modeloDocumentos->save(false);
                }*/

                if(isset($data['es_bolsa'])&&$data['es_bolsa']!=1){
                    //Condición sólo para documentos de tipo 'ODC'.
                    if($data['TblDocumentos']['FK_TIPO_DOCUMENTO'] == 2) {

                        $odcNuevaModifica = TblPeriodos::find()->where(['PK_PERIODO' => $data['chkPeriodos']])->limit(1)->one();

                        $modelSubirDoc->file = UploadedFile::getInstance($modelSubirDoc, '[5]file');
                        if (!empty($modelSubirDoc->file)) {
                            $fechaHoraHoy = date('YmdHis');
                            $rutaGuardado = '../uploads/DocumentosPeriodos/';
                            $nombreFisico = $fechaHoraHoy.'_'.quitar_acentos(utf8_decode($modelSubirDoc->file->basename));
                            $nombreBD = quitar_acentos(utf8_decode($modelSubirDoc->file->basename));

                            $extension = $modelSubirDoc->upload($rutaGuardado,$nombreFisico);
                            $rutaDoc = '/uploads/DocumentosPeriodos/';
                            $modeloDocumentos->NOMBRE_DOCUMENTO = $nombreBD.'.'.$extension;
                            $modeloDocumentos->DOCUMENTO_UBICACION = $rutaDoc.$nombreFisico.'.'.$extension;
                            //$modeloDocumentos->FK_USUARIO = $pk_usuario_loggin = isset(user_info()['PK_USUARIO']) ? user_info()['PK_USUARIO'] : null;
                            $modeloDocumentos->FK_USUARIO = isset(user_info()['PK_USUARIO']) ? user_info()['PK_USUARIO'] : null;
                        }

                        if ($modelSubirDoc->file == null) { //Condición para identificar si es un update de un registro ODC

                            $modeloDocumentos = TblDocumentos::findOne($data['TblDocumentos']['PK_DOCUMENTO']);
                            $modeloDocumentos->FECHA_DOCUMENTO = isset($data['TblDocumentos']['FECHA_DOCUMENTO']) ? transform_date($data['TblDocumentos']['FECHA_DOCUMENTO'],'Y-m-d') : null;
                            $modeloDocumentos->NUM_DOCUMENTO = isset($data['TblDocumentos']['NUM_DOCUMENTO']) ? $data['TblDocumentos']['NUM_DOCUMENTO'] : null;
                            $modeloDocumentos->NUM_SP = isset($data['TblDocumentos']['NUM_SP']) ? $data['TblDocumentos']['NUM_SP'] : null;

                            //$modeloDocumentos->TARIFA = isset($data['TblDocumentos']['TARIFA']) ? $data['TblDocumentos']['TARIFA'] : null;
                            $modeloDocumentos->FK_RAZON_SOCIAL = isset($data['TblDocumentos']['FK_RAZON_SOCIAL']) ? $data['TblDocumentos']['FK_RAZON_SOCIAL'] : null;
                            $modeloDocumentos->FK_UNIDAD_NEGOCIO = isset($data['TblDocumentos']['FK_UNIDAD_NEGOCIO']) ? $data['TblDocumentos']['FK_UNIDAD_NEGOCIO'] : null;
                            //Bitacora para crear nuevo registro de un Documento ODC en Periodos.
                            $descripcionBitacora = 'FK_ASIGNACION='.$modeloDocumentos->FK_ASIGNACION.', TIPO_DOCUMENTO=ODC, NUM_DOCUMENTO='.$modeloDocumentos->NUM_DOCUMENTO.', FK_CLIENTE='.$modeloDocumentos->FK_CLIENTE.', FK_USUARIO '.$modeloDocumentos->FK_USUARIO;
                            user_log_bitacora($descripcionBitacora,'Modificar Documento ODC en Periodos',$data['TblDocumentos']['PK_DOCUMENTO']);

                            $modelSeguimiento = new TblAsignacionesSeguimiento;
                            // $modelSeguimiento->load(Yii::$app->request->post());
                            $modelSeguimiento->COMENTARIOS = 'Modificaci&oacute;n de documento OdC_'.$modeloDocumentos->NUM_DOCUMENTO.'_'.obtener_nombre_mes(date("m",strtotime($odcNuevaModifica->FECHA_INI))).date("Y",strtotime($odcNuevaModifica->FECHA_INI));
                            $modelSeguimiento->FECHA_REGISTRO = date('Y-m-d H:i:s');
                            $modelSeguimiento->FK_ASIGNACION = $modeloDocumentos->FK_ASIGNACION;
                            $modelSeguimiento->FK_USUARIO = user_info()['PK_USUARIO'];
                            $modelSeguimiento->save(false);

                        } else { //Condición para identificar si es un create de un registro ODC

                            $modeloDocumentos->FECHA_DOCUMENTO = isset($data['TblDocumentos']['FECHA_DOCUMENTO']) ? transform_date($data['TblDocumentos']['FECHA_DOCUMENTO'],'Y-m-d') : null;
                            $modeloDocumentos->NUM_DOCUMENTO = isset($data['TblDocumentos']['NUM_DOCUMENTO']) ? $data['TblDocumentos']['NUM_DOCUMENTO'] : null;
                            $modeloDocumentos->NUM_SP = isset($data['TblDocumentos']['NUM_SP']) ? $data['TblDocumentos']['NUM_SP'] : null;
                            //$modeloDocumentos->TARIFA = isset($data['TblDocumentos']['TARIFA']) ? $data['TblDocumentos']['TARIFA'] : null;
                            $modeloDocumentos->FK_RAZON_SOCIAL = isset($data['TblDocumentos']['FK_RAZON_SOCIAL']) ? $data['TblDocumentos']['FK_RAZON_SOCIAL'] : null;
                            $modeloDocumentos->FK_ASIGNACION = $datos['id'];
                            $modeloDocumentos->FK_TIPO_DOCUMENTO = isset($data['TblDocumentos']['FK_TIPO_DOCUMENTO']) ? $data['TblDocumentos']['FK_TIPO_DOCUMENTO'] : null;
                            $modeloDocumentos->FK_UNIDAD_NEGOCIO = isset($data['TblDocumentos']['FK_UNIDAD_NEGOCIO']) ? $data['TblDocumentos']['FK_UNIDAD_NEGOCIO'] : null;
                            $modeloDocumentos->FECHA_REGISTRO = date('Y-m-d H:i:s');
                            $modeloDocumentos->FK_CLIENTE = $data['FK_CLIENTE'];
                            $modeloDocumentos->CONSECUTIVO_TIPO_DOC = $data['consecutivo'];

                            //Bitacora para modificación de Documento ODC en Periodos.
                            $descripcionBitacora = 'FK_ASIGNACION='.$modeloDocumentos->FK_ASIGNACION.', TIPO_DOCUMENTO=ODC, NUM_DOCUMENTO='.$modeloDocumentos->NUM_DOCUMENTO.', FK_CLIENTE='.$modeloDocumentos->FK_CLIENTE.', FK_USUARIO '.$modeloDocumentos->FK_USUARIO;
                            user_log_bitacora($descripcionBitacora,'Cargar Documento ODC en Periodos',$data['TblDocumentos']['PK_DOCUMENTO']);

                            $modelSeguimiento = new TblAsignacionesSeguimiento;
                            // $modelSeguimiento->load(Yii::$app->request->post());
                            $modelSeguimiento->COMENTARIOS = 'Carga de documento OdC_'.$modeloDocumentos->NUM_DOCUMENTO.'_'.obtener_nombre_mes(date("m",strtotime($odcNuevaModifica->FECHA_INI))).date("Y",strtotime($odcNuevaModifica->FECHA_INI));
                            $modelSeguimiento->FECHA_REGISTRO = date('Y-m-d H:i:s');
                            $modelSeguimiento->FK_ASIGNACION = $modeloDocumentos->FK_ASIGNACION;
                            $modelSeguimiento->FK_USUARIO = user_info()['PK_USUARIO'];
                            $modelSeguimiento->save(false);
                        }

                        $modeloDocumentos->save(false);
                    }
                    $id = $modeloDocumentos->PK_DOCUMENTO;
                }

                if (isset($data['chkPeriodos'])) { //identifica si estan seleccionados uno o mas periodos en el alta de ODC

                   /* if($data['TblDocumentos']['FK_TIPO_DOCUMENTO'] == 4) {
                        $modelPeriodosUpd = TblPeriodos::find()->where(['PK_PERIODO' => $data['pk_periodo_factura']])->limit(1)->one();
                        $modelPeriodosUpd->FK_DOCUMENTO_FACTURA = $id;
                        $modelPeriodosUpd->TARIFA_FACTURA = $data['TblDocumentos']['TARIFA'];
                        $modelPeriodosUpd->HORAS_FACTURA = $data['horasFactura'];
                        $modelPeriodosUpd->MONTO_FACTURA = $data['montoFactura'];
                        $modelPeriodosUpd->save(false);
                    } else*/
                    //Entra con documentos
                    if($data['TblDocumentos']['FK_TIPO_DOCUMENTO'] == 3) { //Condición para documentos tipo HDE


                        $bolsa_documento_hde= '';
                        if(isset($data['es_bolsaHDE'])&&$data['es_bolsaHDE']==1){
                            $bolsa_documento_hde = TblDocumentos::find()->where(['PK_DOCUMENTO'=>$data['TblDocumentos']['PK_DOCUMENTO']])->one();
                            $datos_bolsa = TblCatBolsas::find()->where(['PK_BOLSA'=>$data['pk_bolsaHDE']])->one();
                        }

                        $modelPeriodosUpd = TblPeriodos::find()->where(['PK_PERIODO' => $data['pk_periodo_hde']])->limit(1)->one();

                        if(isset($data['es_bolsaHDE'])&&$data['es_bolsaHDE']==1){
                            $datos_bolsa->HORAS_DISPONIBLES = $datos_bolsa->HORAS_DISPONIBLES + $modelPeriodosUpd->HORAS_HDE;
                            $datos_bolsa->MONTO_DISPONIBLE = $datos_bolsa->MONTO_DISPONIBLE + $modelPeriodosUpd->MONTO_HDE;
                        }

                        if(isset($data['es_bolsaHDE'])&&$data['es_bolsaHDE']!=1){
                            $modelPeriodosUpd->FK_DOCUMENTO_HDE = $id;
                            //$modelPeriodosUpd->TARIFA_HDE = $data['TblDocumentos']['TARIFA'];
                        }else{
                            $modelPeriodosUpd->HORAS = $data['txtHoras'];
                            $modelPeriodosUpd->MONTO = $data['txtMonto'];
                        }

                        $modelPeriodosUpd->HORAS_HDE = $data['txtHoras'];
                        if ($data['txtHoras'] != $modelPeriodosUpd->HORAS_DEVENGAR) {

                            $modelSeguimiento = new TblAsignacionesSeguimiento();
                            $modelSeguimiento->COMENTARIOS = 'El Periodo <b>'.$modelPeriodosUpd->PK_PERIODO.'</b> ha tenido una actualizaci&oacute;n de <b>Horas a Devengar,</b> de <b>'.$modelPeriodosUpd->HORAS_DEVENGAR.'</b> horas a <b>'.$data['txtHoras'].'</b> horas.';
                            $modelSeguimiento->FECHA_REGISTRO = date('Y-m-d H:i:s');
                            $modelSeguimiento->FK_ASIGNACION = $modelPeriodosUpd->FK_ASIGNACION;
                            $modelSeguimiento->FK_USUARIO = user_info()['PK_USUARIO'];
                            $modelSeguimiento->save(false);

                            $modelPeriodosUpd->HORAS_DEVENGAR = $data['txtHoras'];
                        }
                        $modelPeriodosUpd->MONTO_HDE = $data['txtMonto'];

                        $docFactura = TblFacturas::find()->where(['FK_DOC_FACTURA' => $modelPeriodosUpd->FK_DOCUMENTO_FACTURA ])->orderBy(['PK_FACTURA' => SORT_DESC])->limit(1)-> one();

                        if(!isset($docFactura->FECHA_INGRESO_BANCO)){
                            $modelPeriodosUpd->HORAS_FACTURA = $data['txtHoras'];
                            $modelPeriodosUpd->MONTO_FACTURA = ($data['txtHoras'] * $data['TblDocumentos']['TARIFA']);
                        }

                        $modelPeriodosUpd->save(false);

                        if(isset($data['es_bolsaHDE'])&&$data['es_bolsaHDE']==1){
                            $datos_bolsa->HORAS_DISPONIBLES =  $datos_bolsa->HORAS_DISPONIBLES - $modelPeriodosUpd->HORAS_HDE;
                            $datos_bolsa->MONTO_DISPONIBLE = $datos_bolsa->MONTO_DISPONIBLE - $modelPeriodosUpd->MONTO_HDE;
                            $datos_bolsa->save(false);
                        }

                    } else { //Condición para documentos tipo ODC

                        $newAsignacion = TblAsignaciones::find()->where(['PK_ASIGNACION' => $datos['id']])->limit(1)->one();
                        $periodosImpactados = null;

                        $bolsa_documento_odc= '';
                        if(isset($data['es_bolsa'])&&$data['es_bolsa']==1){
                            $bolsa_documento_odc = TblDocumentos::find()->where(['PK_DOCUMENTO'=>$data['TblDocumentos']['PK_DOCUMENTO']])->one();
                            $datos_bolsa = TblCatBolsas::find()->where(['PK_BOLSA'=>$data['pk_bolsa']])->one();
                        }

                        foreach ($data['chkPeriodos'] as $key => $value) { //Recorre los periodos seleccionados para cargar ODC

                            $modelPeriodosUpd = TblPeriodos::findOne($value);
                            if(isset($data['es_bolsa'])&&$data['es_bolsa']==1&&$modelPeriodosUpd->FK_DOCUMENTO_ODC!=null){
                                $datos_bolsa->HORAS_DISPONIBLES = (float)$datos_bolsa->HORAS_DISPONIBLES+ (float)$modelPeriodosUpd->HORAS;
                                $datos_bolsa->MONTO_DISPONIBLE = (float)$datos_bolsa->MONTO_DISPONIBLE + (float)$modelPeriodosUpd->MONTO;
                            }

                            $horas = $data['txtHoras_'.$value];
                            if(isset($data['cambio'])){
                                $monto = $data['TblPeriodos']['TARIFA']*$horas;
                                $monto = isset($data['txtMonto_'.$value]) ? $data['txtMonto_'.$value] : ($horas * $data['TblPeriodos']['TARIFA']);
                            }else{
                                if(isset($data['TblPeriodos']['TARIFA'])){
                                    $monto = isset($data['txtMonto_'.$value]) ? $data['txtMonto_'.$value] : ($horas * $data['TblPeriodos']['TARIFA']);
                                }else{
                                    $monto = isset($data['txtMonto_'.$value]) ? $data['txtMonto_'.$value] : ($horas * $data['mask_tarifa_odc']);
                                }
                            }
                            //$monto = isset($data['txtMonto_'.$value]) ? $data['txtMonto_'.$value] : ($data['txtHoras_'.$value] * $data['TblPeriodos']['TARIFA']);
                            //$modelPeriodosUpd->FK_DOCUMENTO_ODC = ($modelSubirDoc->file == null) ? ($modelPeriodosUpd->FK_DOCUMENTO_ODC == 'null' || $modelPeriodosUpd->FK_DOCUMENTO_ODC == null) ? $id : $modelPeriodosUpd->FK_DOCUMENTO_ODC : $id;
                            if(isset($data['es_bolsa'])&&$data['es_bolsa']!=1){

                                $modelPeriodosUpd->FK_DOCUMENTO_ODC = ($modelSubirDoc->file == null) ? ($modelPeriodosUpd->FK_DOCUMENTO_ODC == 'null' || $modelPeriodosUpd->FK_DOCUMENTO_ODC == null) ? $id : $id : $id;
                                $modelPeriodosUpd->FACTURA_PROVISION= $data['Tblperiodos']['FACTURA_PROVISION'];

                                if($data['Tblperiodos']['FACTURA_PROVISION'] == 1){

                                    //SECCION DE CODIGO PARA ENVIAR CORREO ADJUNTO EN ODC CON PROVISION SI

                                        $modeloDocumentoODC= TblDocumentos::findOne($id);
                                        $ubicacion_doc_odc= $modeloDocumentoODC->DOCUMENTO_UBICACION;

                                        // INICIO HRIBI - array para concatenar en el asunto del correo, el nombre del mes perteneciente al periodo donde se guarda el doc HdE.
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
                                        $mesPeriodo = date("m",strtotime($modelPeriodosUpd->FECHA_INI));
                                        // FIN HRIBI

                                        $is_pr= get_config('CONFIG','PRODUCCION');
                                        $de= get_config('PERIODOS','CORREO_REMITENTE_FACTURA');
                                        $prefix_correo = '';
                                        if($is_pr){

                                            $arr_validar_factura_btn= explode(',',get_config('PERIODOS','PARA_FACTURA_BTN'));

                                            //Banorte
                                            // if($sql['RFC']=='BMN930209927'||$sql['RFC']=='ITA081127UZ1')
                                            if (in_array($sql['RFC'],$arr_validar_factura_btn)) {
                                                //$para = array('jorge.mendiola@eisei.net.mx','nestor.tristan@eisei.net.mx','maria.delgado@eisei.net.mx');
                                                $prefix_correo= get_config('PERIODOS','PREFIX_BANORTE');
                                                $para= explode(',',get_config('PERIODOS','CORREO_DESTINO_POR_FACTURAR_BTN'));
                                            // if($sql['RFC']=='IMC9701024T5')
                                            }elseif($sql['RFC']==get_config('PERIODOS','IS_IBM')){
                                                //$para = array('jorge.mendiola@eisei.net.mx','maria.delgado@eisei.net.mx');
                                                $prefix_correo= get_config('PERIODOS','PREFIX_IBM');
                                                $para= explode(',',get_config('PERIODOS','CORREO_DESTINO_POR_FACTURAR_IBM'));
                                            // if($sql['RFC']=='STE9607266C7')
                                            }elseif($sql['RFC']==get_config('PERIODOS','IS_STEFANINI')){
                                                //$para = array('eveylin@eisei.net.mx','maria.delgado@eisei.net.mx');
                                                $prefix_correo= get_config('PERIODOS','PREFIX_STEFANINI');
                                                $para= explode(',',get_config('PERIODOS','CORREO_DESTINO_POR_FACTURAR_STE'));
                                            }else{
                                                $para= explode(',',get_config('PERIODOS','CORREO_DESTINO_POR_FACTURAR_OTROS'));
                                            }

                                        }else{
                                            $para= explode(',',get_config('PERIODOS','CORREO_DESTINO_POR_FACTURAR_OTROS'));
                                        }
                                        // INICIO MOD HRIBI - Concatenación en el asunto del correo del número del documento, mes y año pertenecientes al periodo donde se guarda el doc ODC.
                                        $asunto = 'Provisión - '. $prefix_correo.' Pendiente por facturar '.'OdC_'.$modeloDocumentoODC->NUM_DOCUMENTO.'_'.$arrayMeses[$mesPeriodo].date("Y",strtotime($modelPeriodosUpd->FECHA_INI));

                                        // FIN MOD HRIBI
                                        $nombre_usuario= user_info()['NOMBRE_COMPLETO'];

                                        $mensaje='<style>p, ul, li {font-family: Arial, Helvetica, sans-serif; font-size: 12px;}</style>
                                            <p>Buen d&iacute;a</p>
                                            <p>Adjunto OdC para la facturaci&oacute;n del Periodo '.transform_date($modelPeriodosUpd->FECHA_INI, "d/m/Y").' al '.transform_date($modelPeriodosUpd->FECHA_FIN, "d/m/Y").' por la cantidad de $'.$modelPeriodosUpd->MONTO.'</p>
                                            <p>Saludos y Gracias.</p>';

                                        $enviado = send_mail($de,$para, $asunto, $mensaje,['..'.$ubicacion_doc_odc]);


                                }
                            }else{

                                if($modelPeriodosUpd->FK_DOCUMENTO_ODC==null){
                                    $nuevo_documento_odc                       = new TblDocumentos();
                                    $nuevo_documento_odc->FECHA_DOCUMENTO      = $bolsa_documento_odc->FECHA_DOCUMENTO;
                                    $nuevo_documento_odc->NUM_DOCUMENTO        = $bolsa_documento_odc->NUM_DOCUMENTO;
                                    $nuevo_documento_odc->NUM_SP               = isset($data['TblDocumentos']['NUM_SP']) ? $data['TblDocumentos']['NUM_SP'] : null;
                                    //$nuevo_documento_odc->TARIFA               = $bolsa_documento_odc->TARIFA;
                                    $nuevo_documento_odc->FK_RAZON_SOCIAL      = $bolsa_documento_odc->FK_RAZON_SOCIAL;
                                    $nuevo_documento_odc->FK_ASIGNACION        = $bolsa_documento_odc->FK_ASIGNACION;
                                    $nuevo_documento_odc->FK_TIPO_DOCUMENTO    = $bolsa_documento_odc->FK_TIPO_DOCUMENTO;
                                    $nuevo_documento_odc->DOCUMENTO_UBICACION  = $bolsa_documento_odc->DOCUMENTO_UBICACION;
                                    $nuevo_documento_odc->NOMBRE_DOCUMENTO     = $bolsa_documento_odc->NOMBRE_DOCUMENTO;
                                    $nuevo_documento_odc->FK_UNIDAD_NEGOCIO    = isset($data['TblDocumentos']['FK_UNIDAD_NEGOCIO']) ? $data['TblDocumentos']['FK_UNIDAD_NEGOCIO'] : null;
                                    $nuevo_documento_odc->FECHA_REGISTRO       = date('Y-m-d H:i:s');
                                    $nuevo_documento_odc->FK_CLIENTE           = $data['FK_CLIENTE'];
                                    $nuevo_documento_odc->CONSECUTIVO_TIPO_DOC = $bolsa_documento_odc->CONSECUTIVO_TIPO_DOC;
                                    $nuevo_documento_odc->save(false);
                                    $modelPeriodosUpd->FK_DOCUMENTO_ODC        =$nuevo_documento_odc->PK_DOCUMENTO;
                                    $modelPeriodosUpd->TARIFA                  =isset($data['TblPeriodos']['TARIFA']) ? $data['TblPeriodos']['TARIFA'] : null;

                                    $nuevo_documento_hde                       = new TblDocumentos();
                                    $nuevo_documento_hde->FECHA_DOCUMENTO      = $bolsa_documento_odc->FECHA_DOCUMENTO;
                                    $nuevo_documento_hde->NUM_DOCUMENTO        = str_replace('BLS_ODC_','BLS_HDE_',$bolsa_documento_odc->NUM_DOCUMENTO);
                                    $nuevo_documento_hde->DOCUMENTO_UBICACION  = $bolsa_documento_odc->DOCUMENTO_UBICACION;
                                    $nuevo_documento_hde->NOMBRE_DOCUMENTO     = $bolsa_documento_odc->NOMBRE_DOCUMENTO;
                                    //$nuevo_documento_hde->TARIFA               = $bolsa_documento_odc->TARIFA;
                                    $nuevo_documento_hde->FK_ASIGNACION        = $bolsa_documento_odc->FK_ASIGNACION;
                                    $nuevo_documento_hde->FK_TIPO_DOCUMENTO    = 3;
                                    $nuevo_documento_hde->CONSECUTIVO_TIPO_DOC = 0;
                                    $nuevo_documento_hde->FK_RAZON_SOCIAL      = $bolsa_documento_odc->FK_RAZON_SOCIAL;
                                    $nuevo_documento_hde->save(false);

                                    $modelPeriodosUpd->FK_DOCUMENTO_HDE = $nuevo_documento_hde->PK_DOCUMENTO;
                                    $modelPeriodosUpd->HORAS_HDE        = $horas;
                                    $modelPeriodosUpd->MONTO_HDE        = $monto;
                                    $modelPeriodosUpd->FACTURA_PROVISION= $data['TblPeriodos']['FACTURA_PROVISION'];

                                }else{
                                    $documento_periodo_bolsa                      = TblDocumentos::find()->where(['PK_DOCUMENTO'=>$modelPeriodosUpd->FK_DOCUMENTO_ODC])->one();
                                    $documento_periodo_bolsa->DOCUMENTO_UBICACION = $bolsa_documento_odc->DOCUMENTO_UBICACION;
                                    $documento_periodo_bolsa->NOMBRE_DOCUMENTO    = $bolsa_documento_odc->NOMBRE_DOCUMENTO;
                                    $documento_periodo_bolsa->NUM_SP              = isset($data['TblDocumentos']['NUM_SP']) ? $data['TblDocumentos']['NUM_SP'] : $documento_periodo_bolsa->NUM_SP;
                                    $documento_periodo_bolsa->FK_CLIENTE          = $data['FK_CLIENTE'];
                                    $documento_periodo_bolsa->save(false);
                                    $modelPeriodosUpd->TARIFA                     =isset($data['TblPeriodos']['TARIFA']) ? $data['TblPeriodos']['TARIFA'] : $modelPeriodosUpd->TARIFA;
                                    $modelPeriodosUpd->HORAS_HDE        = $horas;
                                    $modelPeriodosUpd->MONTO_HDE        = $monto;
                                    $modelPeriodosUpd->FACTURA_PROVISION = isset($data['TblPeriodos']['FACTURA_PROVISION']) ? $data['TblPeriodos']['FACTURA_PROVISION'] : $modelPeriodosUpd->FACTURA_PROVISION;
                                }
                            }
                            $modelPeriodosUpd->HORAS = $horas;
                            if(isset($data['cambio'])){
                                $modelPeriodosUpd->MONTO = $data['TblPeriodos']['TARIFA']*$horas;
                                $modelPeriodosUpd->TARIFA = $data['TblPeriodos']['TARIFA'];
                                /*if(isset($data['pk_cat_tarifa_select'])){
                                    $modelPeriodosUpd->FK_CAT_TARIFA = $data['pk_cat_tarifa_select'];
                                }*/
                            }else{
                                $modelPeriodosUpd->MONTO = $modelPeriodosUpd->TARIFA*$horas;
                            }
                            $modelPeriodosUpd->save(false);

                            if(isset($data['es_bolsa'])&&$data['es_bolsa']==1){
                                $datos_bolsa->HORAS_DISPONIBLES =  (float)$datos_bolsa->HORAS_DISPONIBLES - (float)$modelPeriodosUpd->HORAS;
                                $datos_bolsa->MONTO_DISPONIBLE = (float)$datos_bolsa->MONTO_DISPONIBLE - (float)$modelPeriodosUpd->MONTO;
                            }

                            /*Inicio Cambio de Tarifas*/
                            if ($newAsignacion->TARIFA != $modelPeriodosUpd->TARIFA) {

                                $nuevaTarifa = new TblBitAsignacionTarifas();
                                $nuevaTarifa->FK_ASIGNACION = $newAsignacion->PK_ASIGNACION;
                                $nuevaTarifa->FK_PERIODO = $modelPeriodosUpd->PK_PERIODO;
                                $nuevaTarifa->CAMBIO_TARIFA = $modelPeriodosUpd->TARIFA;
                                $nuevaTarifa->FK_CAT_TARIFA = isset($data['pk_cat_tarifa_select']) ? $data['pk_cat_tarifa_select'] : null;
                                $nuevaTarifa->FECHA_REGISTRO = date('Y-m-d H:i:s');

                                $nuevaTarifa->save(false);
                                /*if ($newAsignacion->FKS_PERIODOS_CAMBIO_TARIFA == null) {
                                    $newAsignacion->FKS_PERIODOS_CAMBIO_TARIFA = $modelPeriodosUpd->PK_PERIODO.',';
                                } else {
                                    $newAsignacion->FKS_PERIODOS_CAMBIO_TARIFA = $newAsignacion->FKS_PERIODOS_CAMBIO_TARIFA.$modelPeriodosUpd->PK_PERIODO.',';
                                }

                                $newAsignacion->TARIFA = $modelPeriodosUpd->TARIFA;*/
                                // if(isset($data['cambio'])&&isset($data['pk_cat_tarifa_select'])){
                                //     $newAsignacion->FK_CAT_TARIFA = $data['pk_cat_tarifa_select'];
                                // }
                                $newAsignacion->TARIFA = $modelPeriodosUpd->TARIFA;
                                $newAsignacion->FK_CAT_TARIFA = isset($data['pk_cat_tarifa_select']) ? $data['pk_cat_tarifa_select'] : null;
                                $newAsignacion->save(false);

                                /**
                                 * Cambiar Tarifa periodos
                                 */
                                $proximos_periodos= TblPeriodos::find()->where(['FK_ASIGNACION'=>$modelPeriodosUpd->FK_ASIGNACION])->andWhere(['>','FECHA_INI',$modelPeriodosUpd->FECHA_INI])->all();

                                foreach ($proximos_periodos as $key => $value2) {
                                    if(!$value2->FK_DOCUMENTO_ODC){
                                        $next_periodo= TblPeriodos::find()->where(['PK_PERIODO'=>$value2->PK_PERIODO])->one();
                                        $next_periodo->TARIFA = $data['TblPeriodos']['TARIFA'];
                                        $new_monto = $next_periodo->TARIFA * $next_periodo->HORAS;
                                        $next_periodo->MONTO=$new_monto;
                                        $next_periodo->save(false);
                                    }else{
                                        break;
                                    }
                                }

                            }
                            /*Fin Cambio de Tafiras*/

                            if($periodosImpactados == null){
                                $periodosImpactados = $periodosImpactados.''.$value;
                            }else{
                                $periodosImpactados = $periodosImpactados.', '.$value;
                            }
                        }
                        if(isset($data['es_bolsa'])&&$data['es_bolsa']==1){
                            $datos_bolsa->save(false);
                            if(isset($data['eliminar_periodo_bolsa'])){
                                foreach ($data['eliminar_periodo_bolsa'] as $pk_periodo_desligar) {
                                    $periodo_desligar = TblPeriodos::find()->where(['PK_PERIODO'=>$pk_periodo_desligar])->one();

                                    $documento_odc= TblDocumentos::find()->where(['PK_DOCUMENTO'=>$periodo_desligar->FK_DOCUMENTO_ODC])->one();
                                    $documento_odc->delete();

                                    $documento_hde= TblDocumentos::find()->where(['PK_DOCUMENTO'=>$periodo_desligar->FK_DOCUMENTO_HDE])->one();
                                    $documento_hde->delete();

                                    $datos_bolsa->HORAS_DISPONIBLES = $datos_bolsa->HORAS_DISPONIBLES + $periodo_desligar->HORAS;
                                    $datos_bolsa->MONTO_DISPONIBLE =  $datos_bolsa->HORAS_DISPONIBLES * $datos_bolsa->TARIFA;

                                    $datos_bolsa->save(false);

                                    $periodo_desligar->FK_DOCUMENTO_ODC=null;
                                    $periodo_desligar->FK_DOCUMENTO_HDE=null;
                                    $periodo_desligar->TARIFA_HDE=null;
                                    $periodo_desligar->HORAS_HDE=null;
                                    $periodo_desligar->MONTO_HDE=null;
                                    $periodo_desligar->save(false);
                                }
                            }
                        }

                        //Bitacora para Periodos impactados por Carga/Modificación de Documento ODC.
                        $descripcionBitacora = 'FK_PERIODOS_IMPACTADOS= '.$periodosImpactados.' EN REGISTRO_AFECTADO SE INSERTA EL VALOR DE -PK_ASIGNACION-';
                        user_log_bitacora($descripcionBitacora,'Periodos Impactados',$datos['id']);
                    }
                }
                $this->redirect(['periodos/index','id'=>$datos['id']]);
        }

        if (Yii::$app->request->isAjax) {
            $data = Yii::$app->request->post();
            $post=null;
            parse_str($data['data'],$post);

            /*
            $pagina =(!empty($data['pagina']))? trim($data['pagina']):'';

            $query= TblPeriodos::find()
                        //->andWhere(['LIKE', 'NOMBRE_CLIENTE', $nombre])
                        //->andWhere(['LIKE', 'RFC', $rfc])
                        //->andWhere(['LIKE', 'FK_GIRO', $giro])
                        ->orderBy(['PK_PERIODO' => SORT_DESC])->all();

            if(count($query)<$tamanio_pagina){
                $pagina=1;
            }*/

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
            /*$dataProvider = new ActiveDataProvider([
                'query'=>TblPeriodos::find()
                        //->andWhere(['LIKE', 'NOMBRE_CLIENTE', $nombre])
                        //->andWhere(['LIKE', 'RFC', $rfc])
                        //->andWhere(['LIKE', 'FK_GIRO', $giro])
                        ->orderBy(['PK_PERIODO' => SORT_DESC])
                        ,
                'pagination' => [
                    'pageSize' => $tamanio_pagina,
                    'page' => $pagina-1,
                ],
            ]);*/

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
            $cat_tarifas=[];
            //if($sql['DESC_TARIFA']){
                $fks_cat_tarifas = TblTarifasClientes::find()->select(['FK_CAT_TARIFA'])->where(['FK_CLIENTE'=>$sql['PK_CLIENTE']])->column();
                $cat_tarifas= TblCatTarifas::find()->andWhere(['IN','PK_CAT_TARIFA',$fks_cat_tarifas])->asArray()->all();
            //}


            return $this->render('index', [
                'data' => $dataProvider->getModels(),
                'total_paginas' => ceil($dataProvider->getPagination()->totalCount / $tamanio_pagina),
                'dataProvider' => $dataProvider,
                'modelSubirDoc' => $modelSubirDoc,
                'periodosAsignacion' => $periodosAsignacion,
                'modeloDocumentos' => $modeloDocumentos,
                'modelSubirDocHde' => $modelSubirDocHde,
                'modelSubirDocFactura' => $modelSubirDocFactura,
                'modelSubirDocFacturaXML' => $modelSubirDocFacturaXML,
                'modeloHde' => $modeloHde,
                'modeloFactura' => $modeloFactura,
                'sql' => $sql,
                'reemplazos' => $reemplazos,
                'tarifas' => $tarifas,
                'modelComentariosAsignaciones3' => $modelComentariosAsignaciones3,
                'cat_tarifas' => $cat_tarifas,
            ]);
        }
        $connection->close();
    }

    /**
     * Displays a single TblPeriodos model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    public function actionGetmodelid() {
        if (Yii::$app->request->isAjax) {

            $data = Yii::$app->request->post();
            $post = null;
            parse_str($data['data'],$post);

            $modelPeriodoDummy = TblPeriodos::find()->where(['PK_PERIODO' => $data['data'] ])->limit(1)->one();
            $modelo = TblDocumentos::find()->where(['PK_DOCUMENTO' => $modelPeriodoDummy->FK_DOCUMENTO_ODC])->limit(1)->one();

            $docs_odc=[];
            $bolsa=[];
            $es_bolsa= 0;
            if ($modelo&&strpos($modelo->NUM_DOCUMENTO, 'BLS_ODC_') !== false) {
                $es_bolsa=1;
                $bolsa = TblCatBolsas::find()->where(['NUMERO_BOLSA'=>str_replace('BLS_ODC_', '', $modelo->NUM_DOCUMENTO)])->asArray()->one();
                $num_doc= $modelo->NUM_DOCUMENTO;
                $pk_doc_asignacion= $modelPeriodoDummy->FK_ASIGNACION;
                $docs_odc= TblDocumentos::find()->select(['PK_DOCUMENTO'])->where(['NUM_DOCUMENTO'=>$num_doc,'FK_ASIGNACION'=>$pk_doc_asignacion])->andWhere(['<>','PK_DOCUMENTO',$modelo->PK_DOCUMENTO])->asArray()->column();
                $docs_odc = TblPeriodos::find()->where(['in','FK_DOCUMENTO_ODC',$docs_odc])->asArray()->all();
            }

            // $datos = Yii::$app->request->get();
            $connection = \Yii::$app->db;
            $sql = $connection->createCommand(" select
                                tbl_asignaciones.PK_ASIGNACION,
                                tbl_empleados.NOMBRE_EMP,
                                tbl_empleados.APELLIDO_PAT_EMP,
                                tbl_asignaciones.TARIFA,
                                tbl_asignaciones.HORAS,
                                tbl_asignaciones.NUMERO_PERIODOS,
                                tbl_clientes.NOMBRE_CLIENTE,
                                tbl_clientes.PK_CLIENTE,
                                tbl_clientes.RFC,
                                tbl_cat_contactos.NOMBRE_CONTACTO
                                from tbl_asignaciones
                                    left join tbl_clientes
                                    on tbl_asignaciones.FK_CLIENTE = tbl_clientes.PK_CLIENTE
                                    inner join tbl_cat_contactos
                                    on tbl_asignaciones.FK_CONTACTO = tbl_cat_contactos.PK_CONTACTO
                                    left join tbl_empleados
                                    on tbl_asignaciones.FK_EMPLEADO = tbl_empleados.PK_EMPLEADO
                                    where tbl_asignaciones.PK_ASIGNACION = ".($modelPeriodoDummy->FK_ASIGNACION))->queryOne();

            $cliente_sql= strtolower($sql['NOMBRE_CLIENTE']);
            $clienteRFC = $sql['RFC'];
            $numeroPeriodos = $sql['NUMERO_PERIODOS'];
            $consecutivo=0;
            $comparaSoloNumeros = true;
            if ($modelo) {
                $noSoloNumeros = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ-_";
                for ($i=0; $i<strlen($modelo->NUM_DOCUMENTO); $i++){
                    if (strpos($noSoloNumeros, substr($modelo->NUM_DOCUMENTO,$i,1))===false){
                        $comparaSoloNumeros = false;
                    }
                }
                //$modeloPeriodos = TblPeriodos::find()->where(['FK_DOCUMENTO_ODC' => $modelo->PK_DOCUMENTO ])->all();
                if ($comparaSoloNumeros === false) {
                    $modeloPeriodos = $connection->createCommand("
                    select
                        tb1.PK_PERIODO,
                        tb1.FECHA_INI,
                        tb1.FECHA_FIN,
                        tb1.TARIFA,
                        tb1.HORAS,
                        tb1.MONTO,
                        tb1.FK_ASIGNACION,
                        tb1.FK_DOCUMENTO_ODC,
                        tb1.FK_DOCUMENTO_HDE,
                        tb1.TARIFA_HDE,
                        tb1.HORAS_HDE,
                        tb1.MONTO_HDE,
                        tb1.FK_DOCUMENTO_FACTURA,
                        tb1.TARIFA_FACTURA,
                        tb1.HORAS_FACTURA,
                        tb1.FK_DOCUMENTO_FACTURA_XML,
                        tb1.MONTO_FACTURA,
                        tb1.FACTURA_PROVISION,
                        tb1.MONTO_FACTURA_BOLSA,
                        tb1.FK_EMPLEADO,
                        tb1.HORAS_DEVENGAR,
                        tb1.FK_BOLSA,
                        tb1.FECHA_INGRESO,
                        tb1.FK_CAT_TARIFA,
                        td.PK_DOCUMENTO,
                        td.FECHA_DOCUMENTO,
                        td.NUM_DOCUMENTO,
                        td.NUM_SP,
                        td.DOCUMENTO_UBICACION,
                        td.NOMBRE_DOCUMENTO,
                        td.FK_PROYECTO_FASE,
                        td.FK_TIPO_DOCUMENTO,
                        td.CONSECUTIVO_TIPO_DOC,
                        td.FK_UNIDAD_NEGOCIO,
                        td.FK_CLIENTE,
                        td.FK_RAZON_SOCIAL,
                        td.FK_USUARIO,
                        td.NUM_REFERENCIA,
                        td.FECHA_REGISTRO
                    from
                        (SELECT *
                        FROM tbl_periodos tp
                        where tp.FK_ASIGNACION = ".$modelPeriodoDummy->FK_ASIGNACION."
                        GROUP BY tp.PK_PERIODO
                        ORDER by tp.PK_PERIODO DESC) as tb1
                    left join tbl_documentos td
                    on tb1.FK_DOCUMENTO_ODC = td.PK_DOCUMENTO
                    where tb1.FK_DOCUMENTO_ODC IS NULL OR td.NUM_DOCUMENTO LIKE '%".($modelo->NUM_DOCUMENTO)."%' ")->queryAll();
                }else{

                    $modeloPeriodos = $connection->createCommand("
                    select
                        tb1.PK_PERIODO,
                        tb1.FECHA_INI,
                        tb1.FECHA_FIN,
                        tb1.TARIFA,
                        tb1.HORAS,
                        tb1.MONTO,
                        tb1.FK_ASIGNACION,
                        tb1.FK_DOCUMENTO_ODC,
                        tb1.FK_DOCUMENTO_HDE,
                        tb1.TARIFA_HDE,
                        tb1.HORAS_HDE,
                        tb1.MONTO_HDE,
                        tb1.FK_DOCUMENTO_FACTURA,
                        tb1.TARIFA_FACTURA,
                        tb1.HORAS_FACTURA,
                        tb1.FK_DOCUMENTO_FACTURA_XML,
                        tb1.MONTO_FACTURA,
                        tb1.FACTURA_PROVISION,
                        tb1.MONTO_FACTURA_BOLSA,
                        tb1.FK_EMPLEADO,
                        tb1.HORAS_DEVENGAR,
                        tb1.FK_BOLSA,
                        tb1.FECHA_INGRESO,
                        tb1.FK_CAT_TARIFA,
                        td.PK_DOCUMENTO,
                        td.FECHA_DOCUMENTO,
                        td.NUM_DOCUMENTO,
                        td.NUM_SP,
                        td.DOCUMENTO_UBICACION,
                        td.NOMBRE_DOCUMENTO,
                        td.FK_PROYECTO_FASE,
                        td.FK_TIPO_DOCUMENTO,
                        td.CONSECUTIVO_TIPO_DOC,
                        td.FK_UNIDAD_NEGOCIO,
                        td.FK_CLIENTE,
                        td.FK_RAZON_SOCIAL,
                        td.FK_USUARIO,
                        td.NUM_REFERENCIA,
                        td.FECHA_REGISTRO
                    from
                        (SELECT *
                        FROM tbl_periodos tp
                        where tp.FK_ASIGNACION = ".$modelPeriodoDummy->FK_ASIGNACION."
                        GROUP BY tp.PK_PERIODO
                        ORDER by tp.PK_PERIODO DESC) as tb1
                    left join tbl_documentos td
                    on tb1.FK_DOCUMENTO_ODC = td.PK_DOCUMENTO
                    where tb1.FK_DOCUMENTO_ODC IS NULL OR td.NUM_DOCUMENTO = ".($modelo->NUM_DOCUMENTO))->queryAll();
                }

                $modelo->FECHA_DOCUMENTO= transform_date($modelo->FECHA_DOCUMENTO,'d/m/Y');
                $modelo->DOCUMENTO_UBICACION= utf8_encode($modelo->DOCUMENTO_UBICACION);

            } else {
                $modelo = new TblDocumentos;
                /*
                // if((strpos( $cliente_sql,'banorte')!==false&&strpos( $cliente_sql,'ixe')!==false)||strpos( $cliente_sql,'ibm')!==false){
                if($sql['RFC']=='BMN930209927'||$sql['RFC']=='ITA081127UZ1'||$sql['RFC']=='IMC9701024T5'){
                // if($cliente_sql=='banorte'||$cliente_sql=='ibm'){
                   // $modeloDocumentos->scenario = 'required_num_documento';
                }else{
                    $rfc= substr($sql['RFC'],0,3);
                    $query="SELECT MAX(CONSECUTIVO_TIPO_DOC) AS cont FROM tbl_documentos WHERE FK_TIPO_DOCUMENTO=2 AND FK_CLIENTE= ".$sql['PK_CLIENTE'];
                    // $query="SELECT COUNT(DISTINCT NUM_DOCUMENTO) as cont FROM tbl_documentos where FK_ASIGNACION=".$modelPeriodoDummy->FK_ASIGNACION." AND FK_TIPO_DOCUMENTO=2 AND FECHA_REGISTRO='".date('Y-m-d')."'";
                    $consecutivo= $connection->createCommand($query)->queryOne()['cont'];
                    $consecutivo++;
                    $modelo->NUM_DOCUMENTO =$rfc.'_ODC'.str_pad($consecutivo,5,'0',STR_PAD_LEFT).'_'.date('dmy');
                }
                $modeloPeriodos = null;*/
                $modeloPeriodos = $connection->createCommand("
                    SELECT * FROM tbl_periodos tp
                    WHERE tp.FK_ASIGNACION = ".$modelPeriodoDummy->FK_ASIGNACION." AND tp.FK_DOCUMENTO_ODC IS NULL
                    GROUP BY tp.PK_PERIODO
                    ORDER by tp.PK_PERIODO DESC")->queryAll();
            }
            $hoy = date('Y-m-d');
            $periodos_limpios = TblPeriodos::find()->select(['PK_PERIODO','HORAS', 'MONTO', 'FECHA_INI', 'FECHA_FIN'])->where(['FK_ASIGNACION'=>$modelPeriodoDummy->FK_ASIGNACION])->andWhere(['FK_DOCUMENTO_ODC'=>null])->andWhere("FECHA_INI < '$hoy'")->asArray()->all();

            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $connection->close();
            return $res = array(
                'modelo'           => $modelo,
                'modeloPeriodos'   => $modeloPeriodos,
                'consecutivo'      => $consecutivo,
                'docs_odc'         => $docs_odc,
                'periodos_limpios' => $periodos_limpios,
                'es_bolsa'         => $es_bolsa,
                'bolsa'            => $bolsa,
                'modelPeriodoDummy'=> $modelPeriodoDummy,
                'clienteRFC'       => $clienteRFC,
                'numeroPeriodos' => $numeroPeriodos
            );
        }
    }

     public function actionGetperiodoid() {
        if (Yii::$app->request->isAjax) {

            $data = Yii::$app->request->post();
            $post = null;
            parse_str($data['data'], $post);


            $modelo = $this->findModel($data['data']);
            $modelohde = TblDocumentos::find()->where(['PK_DOCUMENTO' => $modelo->FK_DOCUMENTO_HDE])->limit(1)->one();
            $modelodocfactura = ($modelo->FK_DOCUMENTO_FACTURA != null) ? TblDocumentos::find()
                                                                        ->where(['PK_DOCUMENTO' => $modelo->FK_DOCUMENTO_FACTURA])
                                                                        ->limit(1)->one() : null;
            $modelodocfacturaxml = ($modelo->FK_DOCUMENTO_FACTURA_XML != null) ? TblDocumentos::find()
                                                                        ->where(['PK_DOCUMENTO' => $modelo->FK_DOCUMENTO_FACTURA_XML])
                                                                        ->limit(1)->one() : null;
            $modelofactura = ($modelo->FK_DOCUMENTO_FACTURA != null) ? TblFacturas::find()
                                                                        ->where(['FK_DOC_FACTURA' => $modelo->FK_DOCUMENTO_FACTURA])
                                                                        ->andWhere(['=','FK_PERIODO',$data['data']])
                                                                        ->limit(1)->one() : null;
            $bolsa=[];
            $es_bolsa= 0;
            if ($modelohde&&strpos($modelohde->NUM_DOCUMENTO, 'BLS_HDE_') !== false) {
                $es_bolsa=1;
                $bolsa = TblCatBolsas::find()->where(['NUMERO_BOLSA'=>str_replace('BLS_HDE_', '', $modelohde->NUM_DOCUMENTO)])->asArray()->one();
            }

            // $datos = Yii::$app->request->get();
            $connection = \Yii::$app->db;
            $sql = $connection->createCommand(" select
                                tbl_asignaciones.PK_ASIGNACION,
                                tbl_empleados.NOMBRE_EMP,
                                tbl_empleados.APELLIDO_PAT_EMP,
                                tbl_asignaciones.TARIFA,
                                tbl_asignaciones.HORAS,
                                tbl_clientes.NOMBRE_CLIENTE,
                                tbl_clientes.PK_CLIENTE,
                                tbl_clientes.RFC,
                                tbl_cat_contactos.NOMBRE_CONTACTO
                                from tbl_asignaciones
                                    left join tbl_clientes
                                    on tbl_asignaciones.FK_CLIENTE = tbl_clientes.PK_CLIENTE
                                    inner join tbl_cat_contactos
                                    on tbl_asignaciones.FK_CONTACTO = tbl_cat_contactos.PK_CONTACTO
                                    left join tbl_empleados
                                    on tbl_asignaciones.FK_EMPLEADO = tbl_empleados.PK_EMPLEADO
                                    where tbl_asignaciones.PK_ASIGNACION = ".($modelo->FK_ASIGNACION))->queryOne();

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
                                'f.fk_periodo= p.pk_periodo and f.FK_ESTATUS<>3')
                             ->where(['=','p.FK_ASIGNACION',$sql['PK_ASIGNACION']])
                             ->orderBy('p.FECHA_INI ASC')
                             ->all();*/

            $modelosfactura = $connection->createCommand("select
                                tp.FK_ASIGNACION,
                                td.PK_DOCUMENTO,
                                td.NUM_DOCUMENTO,
                                tp.PK_PERIODO,
                                tp.FECHA_INI,
                                tp.FECHA_FIN,
                                tp.HORAS,
                                tp.MONTO,
                                tp.FK_DOCUMENTO_ODC,
                                tp.FK_DOCUMENTO_HDE,
                                tp.FK_DOCUMENTO_FACTURA,
                                tf.PK_FACTURA,
                                tp.HORAS_HDE,
                                tp.MONTO_HDE
                                from tbl_documentos td
                                inner join tbl_periodos tp on tp.FK_DOCUMENTO_ODC = td.PK_DOCUMENTO and tp.FK_DOCUMENTO_HDE is not null
                                left join tbl_facturas tf on tf.FK_PERIODO = tp.PK_PERIODO and tp.FK_DOCUMENTO_FACTURA = tf.FK_DOC_FACTURA
                                where tp.FK_ASIGNACION =".$sql['PK_ASIGNACION']."
                                and td.NUM_DOCUMENTO = (select
                                    d.NUM_DOCUMENTO from tbl_periodos p inner join tbl_documentos d
                                    on p.FK_DOCUMENTO_ODC = d.PK_DOCUMENTO and p.PK_PERIODO = ".$modelo->PK_PERIODO.")
                                    order by tp.FECHA_INI asc")->queryAll();


            $sqlDocODC = $connection->createCommand(" select td.NUM_SP, td.NUM_DOCUMENTO AS NUM_DOC_ODC, td.FK_RAZON_SOCIAL
                                FROM tbl_periodos tp
                                JOIN tbl_documentos td
                                ON tp.FK_DOCUMENTO_ODC = td.PK_DOCUMENTO
                                AND tp.PK_PERIODO = ".($modelo->PK_PERIODO))->queryOne();

            $sqlDocHDE = $connection->createCommand(" select td.NUM_DOCUMENTO AS NUM_DOC_HDE, tp.TARIFA, tp.HORAS_HDE, tp.HORAS_FACTURA, tp.MONTO_FACTURA
                                FROM tbl_periodos tp
                                JOIN tbl_documentos td
                                ON tp.FK_DOCUMENTO_HDE = td.PK_DOCUMENTO
                                AND tp.PK_PERIODO = ".($modelo->PK_PERIODO))->queryOne();
            $cliente_sql= strtolower($sql['NOMBRE_CLIENTE']);
            $consecutivo=0;

            if(empty($modelohde)){
                // if((strpos( $cliente_sql,'banorte')!==false&&strpos( $cliente_sql,'ixe')!==false)){
                if($sql['RFC']=='BMN930209927'||$sql['RFC']=='ITA081127UZ1'){
                    $modelohde= new TblDocumentos();
                }else{
                    $modelohde= new TblDocumentos();
                    $rfc= substr($sql['RFC'],0,3);

                    $query="SELECT MAX(CONSECUTIVO_TIPO_DOC) AS cont FROM tbl_documentos WHERE FK_TIPO_DOCUMENTO=3 AND FK_CLIENTE= ".$sql['PK_CLIENTE'];
                    // $query="SELECT COUNT(DISTINCT NUM_DOCUMENTO) as cont FROM tbl_documentos where FK_ASIGNACION=".$modelo->FK_ASIGNACION." AND FK_TIPO_DOCUMENTO=2 AND FECHA_REGISTRO='".date('Y-m-d')."'";
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

            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $connection->close();
            return $res = array(
                'periodo' => $modelo,
                'modelohde' => $modelohde,
                'modelodocfactura' => $modelodocfactura,
                'modelodocfacturaxml' => $modelodocfacturaxml,
                'modelofactura' => $modelofactura,
                'consecutivo' => $consecutivo,
                'sqlDocODC' => $sqlDocODC,
                'sqlDocHDE' => $sqlDocHDE,
                'sqlAsignacion' => $sql,
                'modelosfactura'=>$modelosfactura,
                'es_bolsa'         => $es_bolsa,
                'bolsa'            => $bolsa,
            );
        }
    }

    public function actionGetautomaticos() {
        if (Yii::$app->request->isAjax) {

            $data = Yii::$app->request->post();
            $post = null;
            parse_str($data['data'],$post);

            $asignacion = TblAsignaciones::find()->where(['PK_ASIGNACION' => $data['data']])->limit(1)->one();
            $reemplazos = TblAsignacionesReemplazos::find()->where(['FK_ASIGNACION' => $data['data']])->orderBy(['PK_ASIGNACION_REEMPLAZO' => SORT_DESC])->limit(1)->one();
            $periodos = TblPeriodos::find()->where(['FK_ASIGNACION' => $data['data'] ])->all();

            /*Declaracion de variables*/
            $meses = array('01', '03', '05', '07', '08', '10', '12');
            $suma = 1;
            $fechas = '';
            $dias = 0;
            $diasSiguientes = 0;
            /*Fin Declaracion de variables*/

            /*Asignacion de fechas*/
            $fechaInicio = $asignacion->FECHA_INI;
            $fechaFinal = $asignacion->FECHA_FIN;

            if ($reemplazos) {
                $fechaFinal = ($fechaFinal > $reemplazos->FECHA_FIN) ? $fechaFinal : $reemplazos->FECHA_FIN;
            }

            //Nueva Regla de negocio, si la asignacion esta Detenida Estatus = 6 la fecha final sera la fecha en que se detubo la asignacion
            if ($asignacion->FK_ESTATUS_ASIGNACION == 6) {
                $validarDetenido = TblBitComentariosAsignaciones::find()->where(['=','FK_ESTATUS_ASIGNACION',6])
                ->andWhere(['=','FK_ASIGNACION',$asignacion->PK_ASIGNACION])->andWhere(['FECHA_RETOMADA'=> null])->limit(1)->one();
                $fechaFinal = $validarDetenido->FECHA_FIN;
            }

            //Nueva Regla de negocio, si la asignacion esta Cancelada Estatus = 5 la fecha final sera la fecha en la que se cancelo la asignacion
            if($asignacion->FK_ESTATUS_ASIGNACION == 5) {
                $validarCancelado = TblBitComentariosAsignaciones::find()->where(['=','FK_ESTATUS_ASIGNACION',5])
                ->andWhere(['=','FK_ASIGNACION',$asignacion->PK_ASIGNACION])->andWhere(['FECHA_RETOMADA'=> null])->limit(1)->one();
                $fechaFinal = $validarCancelado->FECHA_FIN;

            }

            if ($periodos) {
                $fechaInicio = $periodos[(count($periodos) - 1)]['FECHA_FIN'];
                $suma = 0;
            }
            /*Fin Asignacion de fechas*/

            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            if ($fechaInicio > $fechaFinal) {
                return ['rango' => 0];
            }

            /*Calculo de Periodos*/
            $fechaIni = explode('-', $fechaInicio);
            $fechaFin = explode('-', $fechaFinal);

            if ($fechaFin[0] > $fechaIni[0]) {
                $rango = (12 - $fechaIni[1]) + ($fechaFin[1] + $suma);
            } else {
                $rango = ($fechaFin[1] - $fechaIni[1] + $suma);
            }
            /*Fin Calculo de Periosos*/

            /*Generacion fechas de los periodos*/
            for ($i = 0; $i < $rango; $i++) {

                if ($i == 0 && $suma != 0) {
                    /*Calculo Inicio*/
                    $tempDate = explode('-', $fechaInicio);
                    /*Genearcion de periodos menores a un mes*/
                    if (in_array($tempDate[1], $meses)) {
                        if ($tempDate[2] < 31) {
                            $diasSiguientes = 31 - $tempDate[2];
                        }
                    } elseif ($tempDate[1] == '02') {
                        if ( ($tempDate[0] % 4 == 0) && ( ($tempDate[0] % 100 != 0) || ($tempDate[0] % 400 == 0) )) {
                            if ($tempDate[2]< 29) {
                                $diasSiguientes = 29 - $tempDate[2];
                            }
                        }
                        else {
                            if ($tempDate[2] < 28) {
                                $diasSiguientes = 28 - $tempDate[2];
                            }
                        }
                    } else {
                        if ($tempDate[2] < 30) {
                            $diasSiguientes = 30 - $tempDate[2];
                        }
                    }
                    /*Fin Genearcion de periodos menores a un mes*/

                    /*Suma de dias para cuadrar los periodos*/
                    $fachaini = strtotime ( '+'.$diasSiguientes.' day' , strtotime ( $fechaInicio ) ) ;
                    $fachaFin = date ( 'Y-m-d' , $fachaini );
                    $fechas[$i] = $fechaInicio.'#'.$fachaFin;

                    $fachaini = strtotime ( '+1 day' , strtotime ( $fachaFin ) ) ;
                    $fachaFin = date ( 'Y-m-d' , $fachaini );
                    /*Fin suma de dias para cuadrar los periodos*/

                    $fechaInicio = $fachaFin;
                    /*Fin Calculo Inicio*/

                } else {

                    /*Suma un dia si existen periodos*/
                    if ($i == 0) {
                        $fachaini = strtotime ( '+1 day' , strtotime ( $fechaInicio ) ) ;
                        $fechaInicio = date ( 'Y-m-d' , $fachaini );
                    }
                    /*Suma un dia si existen periodos*/

                    /*Generacion de periodos de un mes*/
                    $tempDate = explode('-', $fechaInicio);

                    if (in_array($tempDate[1], $meses)) {
                        $dias = 30;
                    } elseif ($tempDate[1] == '02') {
                        if ( ($tempDate[0] % 4 == 0) && ( ($tempDate[0] % 100 != 0) || ($tempDate[0] % 400 == 0) )) {
                            $dias = 28;
                        }
                        else {
                            $dias = 27;
                        }
                    } else {
                        $dias = 29;
                    }
                    /*Fin Generacion de periodos de un mes*/

                    /*Suma de dias para cuadrar los periodos*/
                    $fachaini = strtotime ( '+'.$dias.' day' , strtotime ( $fechaInicio ) ) ;
                    $fachaFin = date ( 'Y-m-d' , $fachaini );

                        /*Condicion para establecer la fecha fin del ultimo periodo igual a la fecha fin de la asignacion*/
                        if ($i == ($rango - 1)) {
                            $fachaFin = $fechaFinal;
                        }
                        /*Condicion para establecer la fecha fin del ultimo periodo igual a la fecha fin de la asignacion*/

                    $fechas[$i] = $fechaInicio.'#'.$fachaFin;

                    $fachaini = strtotime ( '+1 day' , strtotime ( $fachaFin ) ) ;
                    $fachaFin = date ( 'Y-m-d' , $fachaini );
                    /*Fin Suma de dias para cuadrar los periodos*/

                    $fechaInicio = $fachaFin;
                }

            }
            /*Fin Generacion fechas de los periodos*/

            return $res = array(
                'rango' => $fechas
            );
        }
    }

    public function actionModal(){
        if (Yii::$app->request->isAjax) {
            $data = Yii::$app->request->post();
            $model= TblCatContactos::findOne($data['id']);

            $puestosContacto = tblcatcontactospuestos::findOne($model->FK_PUESTO);
            $cliente = tblclientes::findOne($model->FK_CLIENTE);
            $ubicacion = tblcatubicaciones::findOne($model->FK_UBICACION);

            $extra['DESC_PUESTO'] = $puestosContacto->DESC_PUESTO;
            $extra['NOMBRE_CLIENTE'] = $cliente->NOMBRE_CLIENTE;
            $extra['FK_UBICACION'] = $ubicacion->DESC_UBICACION;

            \Yii::$app->response->format = \yii\web\Response::FORMAT_HTML;
            ?>
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h3 class="modal-title campos-title font-bold">
                    <a href="<?php echo Url::to(["update",'PK_CONTACTO'=>$model->PK_CONTACTO,'FK_CLIENTE'=>$model->FK_CLIENTE]); ?>" title="Ver" class="item-update iconview-24x24"></a>
                    Datos de Contacto
                </h3>
            </div>
            <div class="modal-body">


                <!-- <div class="row">
                    <div class="form-group col-lg-6 col-md-4 col-sm-4">
                        <?= Html::label($model->getAttributeLabel('FECHA_REGISTRO'),null,['class'=>'campos-label']) ?>
                        <p class="font-bold"><?php echo $model->FECHA_REGISTRO ?></p>
                    </div>
                    <div class="form-group col-lg-6 col-md-4 col-sm-4">
                        <?= Html::label($model->getAttributeLabel('FK_CLIENTE'),null,['class'=>'campos-label']) ?>
                        <p class="font-bold"><?php echo $extra['NOMBRE_CLIENTE'] ?></p>
                    </div>
                    <div class="form-group col-lg-6 col-md-4 col-sm-4">
                        <?= Html::label($model->getAttributeLabel('NOMBRE_CONTACTO'),null,['class'=>'campos-label']) ?>
                        <p class="font-bold"><?php echo $model->NOMBRE_CONTACTO ?></p>
                    </div>
                    <div class="form-group col-lg-6 col-md-4 col-sm-4">
                        <?= Html::label($model->getAttributeLabel('APELLIDO_PAT'),null,['class'=>'campos-label']) ?>
                        <p class="font-bold"><?php echo $model->APELLIDO_PAT ?></p>
                    </div>
                    <div class="form-group col-lg-6 col-md-4 col-sm-4">
                        <?= Html::label($model->getAttributeLabel('APELLIDO_MAT'),null,['class'=>'campos-label']) ?>
                        <p class="font-bold"><?php echo $model->APELLIDO_MAT ?></p>
                    </div>
                    <div class="form-group col-lg-6 col-md-4 col-sm-4">
                        <?= Html::label($model->getAttributeLabel('TEL_OFICINA'),null,['class'=>'campos-label']) ?>
                        <p class="font-bold"><?php echo $model->TEL_OFICINA ?></p>
                    </div>
                    <div class="form-group col-lg-6 col-md-4 col-sm-4">
                        <?= Html::label($model->getAttributeLabel('TEL_EXTENSION'),null,['class'=>'campos-label']) ?>
                        <p class="font-bold"><?php echo $model->TEL_EXTENSION ?></p>
                    </div>
                    <div class="form-group col-lg-6 col-md-4 col-sm-4">
                        <?= Html::label($model->getAttributeLabel('TEL_CELULAR'),null,['class'=>'campos-label']) ?>
                        <p class="font-bold"><?php echo $model->TEL_CELULAR ?></p>
                    </div>
                    <div class="form-group col-lg-6 col-md-4 col-sm-4">
                        <?= Html::label($model->getAttributeLabel('EMAIL'),null,['class'=>'campos-label']) ?>
                        <p class="font-bold"><?php echo $model->EMAIL ?></p>
                    </div>
                    <div class="form-group col-lg-6 col-md-4 col-sm-4">
                        <?= Html::label($model->getAttributeLabel('FK_PUESTO'),null,['class'=>'campos-label']) ?>
                        <p class="font-bold"><?php echo $extra['DESC_PUESTO'] ?></p>
                    </div>
                    <div class="form-group col-lg-6 col-md-4 col-sm-4">
                        <?= Html::label($model->getAttributeLabel('FK_UBICACION'),null,['class'=>'campos-label']) ?>
                        <p class="font-bold"><?php echo $extra['FK_UBICACION'] ?></p>
                    </div>
                    <div class="form-group col-lg-12 col-md-4 col-sm-4">
                        <?= Html::label($model->getAttributeLabel('COMENTARIOS'),null,['class'=>'campos-label']) ?>
                        <p class="font-bold"><?php echo $model->COMENTARIOS ?></p>
                    </div>

                    <div class="clear"></div>
                </div> -->


              <?php $form = ActiveForm::begin(); ?>

              <?= $form->field($model, 'FECHA_INI')->textInput() ?>

              <?= $form->field($model, 'FECHA_FIN')->textInput() ?>

              <?= $form->field($model, 'TARIFA')->textInput(['maxlength' => true]) ?>

              <?= $form->field($model, 'HORAS')->textInput() ?>

              <?= $form->field($model, 'MONTO')->textInput(['maxlength' => true]) ?>

              <?= $form->field($model, 'FK_ASIGNACION')->textInput() ?>

              <?= $form->field($model, 'FECHA_INGRESO')->textInput() ?>

              <div class="form-group">
                  <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
              </div>

              <?php ActiveForm::end(); ?>


            </div>
            <?php
        }
            // $model= TblCatContactos::findOne('1');
            // var_dump( $model);
    }

    /**
     * Creates a new TblPeriodos model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
      $model = new TblPeriodos();

      if (Yii::$app->request->isAjax) {
            $data = Yii::$app->request->post();
            $post=null;
            parse_str($data['data'],$post);
            $keyAsig     =(!empty($post['keyAsignacion']))? trim($post['keyAsignacion']):'';
            $fechaInicio =(!empty($post['fechaInicio']))? trim($post['fechaInicio']):'';
            $fechaFin    =(!empty($post['fechaFin']))? trim($post['fechaFin']):'';
            $tarifa      =(!empty($post['tarifa']))? trim($post['tarifa']):'';
            $horas       =(!empty($post['horas_cliente']))? trim($post['horas_cliente']):'';
            $monto       =(!empty($post['monto']))? trim($post['monto']):'';
            $horas_asignacion =(!empty($post['horas_asignacion']))? trim($post['horas_asignacion']):'';

            if(!isset($model)){
              throw new Exception("Error al obtener Datos de form");
            }

            $asignacion = TblAsignaciones::find()->where(['PK_ASIGNACION' => $keyAsig])->limit(1)->one();

            $datos= TblPeriodos::find()->where(['FK_ASIGNACION'=>$keyAsig])->all();
            $insert=false;
            $insert2=false;
            $insert3=false;

            //Verififca si la asignacion se encuantra en la tabla TblAsignacionesRemplazos,
            //si lo esta, calcular el rango de las fechas entre inicio y fin de asignacion
            //para determinar si el periodo a crear va a ser para un recurso anterior. Todo
            //esto en caso de que se introduzcan fechas anteriores a la fecha de inicio de
            //la asignacion mandada

            $tieneRemplazo = TblAsignacionesReemplazos::find()->where(['FK_ASIGNACION'=>$keyAsig])->orderBy(['PK_ASIGNACION_REEMPLAZO' => SORT_DESC])->all();

            $fip = transform_date($fechaInicio,'Y-m-d');

            if($fip < $asignacion->FECHA_INI && $tieneRemplazo){

                \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                if($datos){
                    $nueva_fecha_ini = str_replace('/','-',$fechaInicio);
                    $nueva_fecha_fin = str_replace('/','-',$fechaFin);

                    foreach ($datos as $key => $value) {
                        $fecha_ini_p = str_replace('/','-',$value->FECHA_INI);
                        $fecha_fin_p = str_replace('/','-',$value->FECHA_FIN);
                        
                        $start_date     = $fecha_ini_p;
                        $end_date       = $fecha_fin_p;
                        $date_from_user = $nueva_fecha_ini;
                        $insert =$this->check_in_range($start_date, $end_date, $date_from_user);

                        $date_from_user = $nueva_fecha_fin;
                        $insert2 =$this->check_in_range($start_date, $end_date, $date_from_user);

                        $start_date     = $nueva_fecha_ini;
                        $end_date       = $nueva_fecha_fin;
                        $date_from_user = $fecha_ini_p;
                        $insert3 =$this->check_in_range($start_date, $end_date, $date_from_user);

                        if($insert||$insert2||$insert3){
                            $res= array(
                                'res'=>'¡Este periodo incluye fechas que ya est&aacute;n definidas en otro periodo, Favor de Verificarlo!',
                                'insert'=>$insert,
                                'insert2'=>$insert2,
                                'insert3'=>$insert3,
                                );
                            return $res;
                            break;
                        }

                    }       
                }

                \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                if($tieneRemplazo){
                    $nueva_fecha_ini = str_replace('/','-',$fechaInicio);
                    $nueva_fecha_fin = str_replace('/','-',$fechaFin);

                    foreach ($tieneRemplazo as $key => $value) {
                        $fecha_ini_a = str_replace('/','-',$value->FECHA_INICIO);
                        $fecha_fin_a = str_replace('/','-',$value->FECHA_FIN);
                            
                        $start_date     = $fecha_ini_a;
                        $end_date       = $fecha_fin_a;
                        $date_from_user = $nueva_fecha_ini;
                        $insert =$this->check_in_range($start_date, $end_date, $date_from_user);

                        $date_from_user = $nueva_fecha_fin;
                        $insert2 =$this->check_in_range($start_date, $end_date, $date_from_user);

                        $start_date     = $nueva_fecha_ini;
                        $end_date       = $nueva_fecha_fin;
                        $date_from_user = $fecha_ini_a;
                        $insert3 =$this->check_in_range($start_date, $end_date, $date_from_user);

                        if($insert||$insert2||$insert3){

                            $perfilEmpAnterior = tblperfilempleados::find()->where(['FK_EMPLEADO'=>$value->FK_EMPLEADO])->limit(1)->one();

                            $tarifaAnterior = $perfilEmpAnterior->TARIFA;

                            $fecha_ini_nueva = transform_date($fechaInicio,'Y-m-d');
                            $fecha_fin_nueva = transform_date($fechaFin,'Y-m-d');
                
                            $model->FECHA_INI=transform_date($fechaInicio,'Y-m-d');
                            $model->FECHA_FIN=$fecha_fin_nueva;

                            $model->TARIFA=$tarifaAnterior;

                            $fecha_ini_dia = substr($fechaInicio, 0, -8);
                            $fecha_ini_mes = substr($fechaInicio, 3, -5);
                            $fecha_ini_anio = substr($fechaInicio, 6);

                            $fecha_fin_dia = substr($fechaFin, 0, -8);
                            $fecha_fin_mes = substr($fechaFin, 3, -5);
                            $fecha_fin_anio = substr($fechaFin, 6);

                            $fecha_ini_horas = date('Y-m-d H:i:s', mktime(0, 0, 0, $fecha_ini_mes, $fecha_ini_dia, $fecha_ini_anio));

                            $fecha_fin_horas = date('Y-m-d H:i:s', mktime(0, 0, 0, $fecha_fin_mes, $fecha_fin_dia, $fecha_fin_anio));

                            $dia_mls = (24*60*60*1000);
                            $hora_mls = (60*60*1000)+1;
                            $dias = 0;

                            $dia_fecha_inicio_ultimo = cal_days_in_month(CAL_GREGORIAN, (int) $fecha_ini_mes, (int) $fecha_ini_anio);//Ultimo dia del mes de la fecha de inicio

                            $dia_fecha_fin_ultimo = cal_days_in_month(CAL_GREGORIAN, (int) $fecha_fin_mes, (int) $fecha_fin_anio);//Ultimo dia del mes de la fecha fin

                            $dias_habiles = 0;
                            $dias_habiles_mes_uno = 0;
                            $dias_habiles_mes_dos = 0;

                            if(($fecha_ini_mes!=$fecha_fin_mes)||($fecha_ini_mes==$fecha_fin_mes && $fecha_ini_anio!=$fecha_fin_anio)){

                              //Si la fecha inicial no inicia el dia primero, se toman en cuenta solo los dias habiles correspondientes a ese mes y acorde a la fecha de inicio
                              if($fecha_ini_dia!='01'){

                                $fecha_inicio_calculada = date('Y-m-d H:i:s', mktime(0, 0, 0, $fecha_ini_mes, $fecha_ini_dia, $fecha_ini_anio));//Inicializa la fecha de inicio

                                $fecha_inicio_mes_fin = date('Y-m-d H:i:s', mktime(0, 0, 0, $fecha_ini_mes, $dia_fecha_inicio_ultimo, $fecha_ini_anio));//Inicializa la fecha fin de este mes

                                $tiempo_inicio_mls = convierte_milisegundos($fecha_inicio_calculada);
                                $tiempo_fin_mls = convierte_milisegundos($fecha_inicio_mes_fin);

                                for($i=$tiempo_inicio_mls;$i<=$tiempo_fin_mls;$i=$i+$dia_mls){

                                  $dato1 = $i/1000;

                                  $tiempo_inicio_date = date('Y-m-d',$dato1);

                                  $fechastr = strtotime($tiempo_inicio_date);

                                  if(date('w',$fechastr)>0&&date('w',$fechastr)<6){
                                    $dias_habiles++;
                                    $dias_habiles_mes_uno++;
                                  }
                                }

                                $dato2 = (convierte_milisegundos($fecha_inicio_mes_fin) + $dia_mls)/1000;

                                $fecha_ini_horas = date('Y-m-d',$dato2);

                              }

                             //Se saca la diferencia de dias entre la fecha de inicio y la fecha de fin, y se le agrega un dia mas, correspondiente al ultimo dia de la fecha fin
                              $dias_diferencia = (convierte_milisegundos($fecha_fin_horas)-convierte_milisegundos($fecha_ini_horas)+$dia_mls)/$dia_mls;
                              $dias = $dias + $dias_diferencia;
                              $dias_del_mes=0;

                             //Si el dia de la fecha fin no es el dia final del mes
                              if( intval($dia_fecha_fin_ultimo) != intval($fecha_fin_dia)){

                                $fecha_inicio_mes_fin = date('Y-m-d H:i:s', mktime(0, 0, 0, $fecha_fin_mes, 1, $fecha_fin_anio));

                                for($i=convierte_milisegundos($fecha_inicio_mes_fin);$i<=convierte_milisegundos($fecha_fin_horas);$i=$i+$dia_mls){

                                  $dato1 = $i/1000;

                                  $tiempo_actual = date('Y-m-d',$dato1);

                                  $fechastr = strtotime($tiempo_actual);

                                  if(date('w',$fechastr)>0&&date('w',$fechastr)<6){
                                    $dias_habiles++;
                                    $dias_habiles_mes_dos++;
                                  }
                                  $dias_del_mes++;
                                }
                                $dias = $dias - $dias_del_mes;
                              }
                              $meses = round($dias/30);
                            } else {//Si la fecha de inicio y la fecha de fin estan en el mismo mes

                              if($fecha_ini_dia=='01' && intval($dia_fecha_fin_ultimo) == intval($fecha_fin_dia)){//Si el mes no esta completo
                                $meses = 1;
                              }else{//Se sacan dias habiles
                                
                                for($i=convierte_milisegundos($fecha_ini_horas);$i<=(convierte_milisegundos($fecha_fin_horas)+$hora_mls);$i=$i+$dia_mls){//En la condicion se agrega una hora mas un milisegundo por efectos del cambios de horario

                                  $dato1 = $i/1000;

                                  $fecha = date('Y-m-d',$dato1);

                                  $fechastr = strtotime($fecha);

                                  if(date('w', $fechastr) > 0 && date('w', $fechastr) < 6){

                                    $dias_habiles++;
                                    $dias_habiles_mes_uno++;
                                  }
                                }
                                $meses = 0;
                              }
                            }

                            $horas_cliente = $horas_asignacion;
                            $horas_mes_uno = 0.00;
                            $horas_mes_dos = 0.00;
                            $horas_dias_habiles = 0.00;

                            if($dias_habiles_mes_uno * 8 > $horas_cliente){

                              $horas_mes_uno = $horas_cliente;
                            } else {

                              $horas_mes_uno = $dias_habiles_mes_uno * 8;
                            }

                            if($dias_habiles_mes_dos * 8 > $horas_cliente){

                              $horas_mes_dos = $horas_cliente;
                            } else {

                              $horas_mes_dos = $dias_habiles_mes_dos * 8;
                            }

                            $horas_dias_habiles = (float) $horas_mes_uno + (float) $horas_mes_dos;

                            if($meses > 0){

                              $horasTotal = ($dias_habiles * 8) + floor($meses * $horas_cliente);
                            }else{

                              $horasTotal = ($dias_habiles * 8);
                            }

                            $monto_total = $horasTotal * $tarifaAnterior;


                            $model->FK_ASIGNACION=$keyAsig;
                            $model->FK_EMPLEADO= $value->FK_EMPLEADO;
                            $model->HORAS=$horasTotal;

                            $model->MONTO=$monto_total;
                            
                            $model->FECHA_INGRESO=date('Y-m-d H:i:s');        

                            $model->save(false);
                        }

                    }                         
                }

            }else{

                \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                if($datos){
                    $nueva_fecha_ini = str_replace('/','-',$fechaInicio);
                    $nueva_fecha_fin = str_replace('/','-',$fechaFin);

                    foreach ($datos as $key => $value) {
                        $fecha_ini_p = str_replace('/','-',$value->FECHA_INI);
                        $fecha_fin_p = str_replace('/','-',$value->FECHA_FIN);
                        
                        $start_date     = $fecha_ini_p;
                        $end_date       = $fecha_fin_p;
                        $date_from_user = $nueva_fecha_ini;
                        $insert =$this->check_in_range($start_date, $end_date, $date_from_user);

                        $date_from_user = $nueva_fecha_fin;
                        $insert2 =$this->check_in_range($start_date, $end_date, $date_from_user);

                        $start_date     = $nueva_fecha_ini;
                        $end_date       = $nueva_fecha_fin;
                        $date_from_user = $fecha_ini_p;
                        $insert3 =$this->check_in_range($start_date, $end_date, $date_from_user);

                        if($insert||$insert2||$insert3){
                            $res= array(
                                'res'=>'¡Este periodo incluye fechas que ya est&aacute;n definidas en otro periodo, Favor de Verificarlo!',
                                'insert'=>$insert,
                                'insert2'=>$insert2,
                                'insert3'=>$insert3,
                                );
                            return $res;
                            break;
                        }

                    }       
                }

                $fecha_fin_nueva = transform_date($fechaFin,'Y-m-d');
                
                $model->FECHA_INI=transform_date($fechaInicio,'Y-m-d');
                $model->FECHA_FIN=$fecha_fin_nueva;
                $model->TARIFA=$tarifa;
                $model->HORAS=$horas;
                $model->MONTO=$monto;
                $model->FK_ASIGNACION=$keyAsig;
                $model->FK_EMPLEADO=$asignacion->FK_EMPLEADO;
                
                $model->FECHA_INGRESO=date('Y-m-d H:i:s');        

                $model->save(false);

                if ($fecha_fin_nueva > $asignacion->FECHA_FIN) {
                    $asignacion->FECHA_FIN = $fecha_fin_nueva;
                    $asignacion->save(false);
                }
                $res= array(
                    'res'=>'El periodo se a guardado correctamente.',
                    'insert'=>$insert,
                    'insert2'=>$insert2,
                    'insert3'=>$insert3,
                    'model'=>$model,
                    );
                return $res;
            }

                    
       if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['asignaciones/view','id' => $model->FK_ASIGNACION]);
        } else {
            
            return $this->redirect(['asignaciones/view','id' => $model->FK_ASIGNACION]);
        }
      }else{
        
        return $this->redirect(['asignaciones/view','id' => $model->FK_ASIGNACION]);
      }
    }

    

    //funcion para agregar los periodos automaticamente
    public function actionCreatevarios() {

        if (Yii::$app->request->isAjax) {

            $data = Yii::$app->request->post();
            $post = null;
            parse_str($data['data'], $post);

            $dato = false;

            if (isset($post['opc']) == 'proyecto') {
                foreach ($post['fecha_inicio'] as $key => $value) {
                    $model = new TblProyectosPeriodos();
                    $model->FK_PROYECTO_FASE = $post['FK_PROYECTO_FASE'];
                    $model->FECHA_INI = $post['fecha_inicio'][$key];
                    $model->FECHA_FIN = $post['fecha_final'][$key];
                    $model->TARIFA_ODC = $post['TARIFA_ODC'];
                    $model->HORAS_ODC = $post['horas'][$key];
                    $model->MONTO_ODC = $post['monto'][$key];
                    $model->FK_CAT_TARIFA = ($post['FK_CAT_TARIFA'] ? $post['FK_CAT_TARIFA'] : null);
                    $model->save(false);
                }
                $dato = true;
            } else {
                for ($i = 0; $i < $post['cant_periodos']; $i++) {
                    $modelo = new TblPeriodos();
                    $modelo->FECHA_INI = $post['fecha_inicio_'.$i];
                    $modelo->FECHA_FIN = $post['fecha_final_'.$i];
                    $modelo->TARIFA = $post['tarifa'];
                    $modelo->HORAS = $post['horas_'.$i];
                    $modelo->MONTO = $post['monto_'.$i];
                    $modelo->FK_ASIGNACION = $post['idAsig'];
                    $modelo->FK_EMPLEADO = $post['idEmpl'];
                    $modelo->FECHA_INGRESO = date('Y-m-d H:i:s');
                    $modelo->save();
                }
                $dato = true;
            }

            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                return ['exito' => $dato, 'post' => $post];
        }

    }

    /**
     * Updates an existing TblPeriodos model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate()
    {

        if (Yii::$app->request->isAjax) {

            $data = Yii::$app->request->post();
            $post=null;
            parse_str($data['data'],$post);
            $idPeriodo   =(!empty($post['pkPeriodo']))? trim($post['pkPeriodo']):'';
            $keyAsig     =(!empty($post['keyAsignacion']))? trim($post['keyAsignacion']):'';
            $fechaInicio =(!empty($post['fechaInicio']))? trim($post['fechaInicio']):'';
            $fechaFin    =(!empty($post['fechaFin']))? trim($post['fechaFin']):'';
            $tarifa      =(!empty($post['tarifa']))? trim($post['tarifa']):'';
            $horas       =(!empty($post['horas_cliente']))? trim($post['horas_cliente']):'';
            $horas_devengar =(!empty($post['HORAS_DEVENGAR']))? trim($post['HORAS_DEVENGAR']):'';
            $monto_real =(!empty($post['MONTO_REAL']))? trim($post['MONTO_REAL']):'';
            $monto       =(!empty($post['monto']))? trim($post['monto']):'';

            $model = $this->findModel($idPeriodo);
        
            $asignacion = TblAsignaciones::find()->where(['PK_ASIGNACION' => $keyAsig])->limit(1)->one();

            $datos = TblPeriodos::findBySql("SELECT * FROM tbl_periodos".

                                            " WHERE FK_ASIGNACION =".$keyAsig."".

                                            " AND PK_PERIODO != ".$idPeriodo)->all();

            $insert=false;
            $insert2=false;
            $insert3=false;

            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            if($datos){
                $nueva_fecha_ini = str_replace('/','-',$fechaInicio);
                $nueva_fecha_fin = str_replace('/','-',$fechaFin);

                foreach ($datos as $key => $value) {
                    $fecha_ini_p = str_replace('/','-',$value->FECHA_INI);
                    $fecha_fin_p = str_replace('/','-',$value->FECHA_FIN);
                    
                    $start_date     = $fecha_ini_p;
                    $end_date       = $fecha_fin_p;
                    $date_from_user = $nueva_fecha_ini;
                    $insert =$this->check_in_range($start_date, $end_date, $date_from_user);

                    $date_from_user = $nueva_fecha_fin;
                    $insert2 =$this->check_in_range($start_date, $end_date, $date_from_user);

                    $start_date     = $nueva_fecha_ini;
                    $end_date       = $nueva_fecha_fin;
                    $date_from_user = $fecha_ini_p;
                    $insert3 =$this->check_in_range($start_date, $end_date, $date_from_user);

                    if($insert||$insert2||$insert3){
                        $res= array(
                            'res'=>'¡Este periodo incluye fechas que ya est&aacute;n definidas en otro periodo, Favor de Verificarlo!',
                            'insert'=>$insert,
                            'insert2'=>$insert2,
                            'insert3'=>$insert3,
                            );
                        return $res;
                        break;
                    }

                }       
            }

            $fecha_fin_nueva = transform_date($fechaFin,'Y-m-d');
            
            $model->FECHA_INI=transform_date($fechaInicio,'Y-m-d');
            $model->FECHA_FIN=$fecha_fin_nueva;
            $model->TARIFA=$tarifa;
            $model->HORAS=$horas;
            $model->HORAS_DEVENGAR=$horas_devengar;
            $model->MONTO_REAL=$monto_real;
            $model->MONTO=$monto;

            $model->save(false);

            if ($fecha_fin_nueva > $asignacion->FECHA_FIN) {
                $asignacion->FECHA_FIN = $fecha_fin_nueva;
                $asignacion->save(false);
            }

            $res= array(
                'res'=>'El periodo se actualizo correctamente.',
                'insert'=>$insert,
                'insert2'=>$insert2,
                'insert3'=>$insert3,
                'model'=>$model,
                );
            return $res;

            if ($model->load(Yii::$app->request->post()) && $model->save()) {
                
                return $this->redirect(['asignaciones/view','id' => $keyAsig]);
            } else {

                $res= array(
                'res'=>'Error al actualizar.',
                );
                return $res;

                return $this->redirect(['asignaciones/view','id' => $keyAsig]);
            }
                   
          }else{
            
            return $this->redirect(['asignaciones/view','id' => $keyAsig]);
          }
        
    }

    public function actionCompara(){

        $data = Yii::$app->request->post();

        $asig = $data['asig'];
        $f = $data['fecha'];
        $b = '';
        $b2 = false;

        $asignacion = TblAsignaciones::find()->where(['PK_ASIGNACION' => $asig])->limit(1)->one();

        $fecha_ini_a = str_replace('/','-', $asignacion->FECHA_INI);
        $fecha_fin_a = str_replace('/','-', $asignacion->FECHA_FIN);

        $start_date     = $fecha_ini_a;
        $end_date       = $fecha_fin_a;
        $date_from_user = str_replace('/','-', $f);
        $b2 =$this->check_in_range($start_date, $end_date, $date_from_user);

        $tieneRemplazo = TblAsignacionesReemplazos::find()->where(['FK_ASIGNACION'=>$asig])->orderBy(['PK_ASIGNACION_REEMPLAZO' => SORT_DESC])->all();

        $fip = transform_date($f,'Y-m-d');

        if($tieneRemplazo && $fip < $asignacion->FECHA_INI){

            $b = 1;

        }else{

            $b = 0;
        }

        if($b2){

            $b2 = true;
            
        }

        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $res = array(
            'b' => $b,
            'b2' => $b2,
        );

        return $res;
    }

    /*public function actionUpdateajax(){
        if (Yii::$app->request->isAjax){
            $data=Yii::$app->request->post();
            parse_str($data['data'],$post);
            $monto =(!empty($post['monto']))? trim($post['monto']):'';
            $model = TblPeriodos::find()->where(['PK_PERIODO' => $post['pkPeriodo']])->limit(1)->one();
            $horasAntes = ($model->HORAS_DEVENGAR) ? $model->HORAS_DEVENGAR : 0;

            if (isset($post['fechaInicio'])) {
              $model->FECHA_INI=transform_date($post['fechaInicio'],'Y-m-d');
            }
            if (isset($post['fechaFin'])) {
              $model->FECHA_FIN=transform_date($post['fechaFin'],'Y-m-d');
            }

            $model->MONTO=$monto;

            // se agrego esta condicion para que no truene el editar desde la pantalla de view facturas
            if(isset($post['HORAS_DEVENGAR']))
            {
                $model->HORAS_DEVENGAR = $post['HORAS_DEVENGAR'];
            }

            $model->save(false);

            $modelSeguimiento = new TblAsignacionesSeguimiento();
            $modelSeguimiento->COMENTARIOS = ($model->HORAS_DEVENGAR) ? 'El Periodo <b>'.$post['id_periodo'].'</b> ha tenido una actualizaci&oacute;n de <b>Horas a Devengar,</b> de <b>'.$horasAntes.'</b> horas a <b>'.$model->HORAS_DEVENGAR.'</b> horas.' : 'Se actualizaron datos en este periodo '.$model->PK_PERIODO;
            $modelSeguimiento->FECHA_REGISTRO = date('Y-m-d H:i:s');
            $modelSeguimiento->FK_ASIGNACION = $model->FK_ASIGNACION;
            $modelSeguimiento->FK_USUARIO = user_info()['PK_USUARIO'];
            $modelSeguimiento->save(false);
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $res = array(
                'data'        => $data,
                'post'        => $post,
                'model'        => $model,
            );

            return $res;
        }else{

        }
    }*/
    public function actionDeleteajax(){
        if (Yii::$app->request->isAjax){
            $data=Yii::$app->request->post();

            $model = TblPeriodos::find()->where(['PK_PERIODO' => $data['id_periodo']])->limit(1)->one();

            //Borrar registros en bloque de facturas pendientes por facturar
            $bitBlsDocs = TblBitBlsDocs::find()->andWhere(['IS NOT','FK_PERIODOS',null])->andWhere(['<>','FK_PERIODOS',''])->andWhere(['LIKE', 'FK_PERIODOS',$data['id_periodo'].','])->all();

            foreach($bitBlsDocs as $key) {
                $periodos = explode(',', $key->FK_PERIODOS);
                unset($periodos[count($periodos)-1]);
                if (in_array($data['id_periodo'], $periodos)) {
                    $todos = count($periodos);
                    if ($todos < 2) {
                        $key->delete();
                        break;
                    } else {
                        $cadena = '';
                        for ($i = 0; $i < $todos; $i++) {
                            if ($periodos[$i] == $data['id_periodo']) {
                                unset($periodos[$i]);
                            } else {
                                $cadena .= $periodos[$i].',';
                            }
                        }
                        $key->FK_PERIODOS = $cadena;
                        $key->save(false);
                        break;
                    }
                }
            }

            //Bitacora para insertar registro de periodos eliminados.
            $descripcionBitacora = "FK_ASIGNACION= ".$model->FK_ASIGNACION." PK_PERIODO= ".$model->PK_PERIODO." FECHA_INI_PERIODO= ".$model->FECHA_INI." FECHA_FIN_PERIODO=".$model->FECHA_FIN." HORAS_PERIODO= ".$model->HORAS." MONTO_PERIODO= ".$model->MONTO;
            user_log_bitacora($descripcionBitacora,'Periodo Eliminado',$model['PK_PERIODO']);

            $model->delete();

            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $res = array(
                'data'        => $data,
            );

            return $res;
        }else{

        }
    }

    /**
     * Deletes an existing TblPeriodos model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the TblPeriodos model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TblPeriodos the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TblPeriodos::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function check_in_range($start_date, $end_date, $date_from_user)
    {
        // Convert to timestamp
        $start_ts = strtotime($start_date);
        $end_ts = strtotime($end_date);
        $user_ts = strtotime($date_from_user);

        // Check that user date is between start & end
        return (($user_ts >= $start_ts) && ($user_ts <= $end_ts));
    }

    public function actionPreborrado() {
        if (Yii::$app->request->isAjax) {
            $data = Yii::$app->request->post();

            $periodo = TblPeriodos::findOne($data['PK_PERIODO']);

            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            return ['periodo' => $periodo];
        }
    }

    public function actionEliminar_hde(){
        if (Yii::$app->request->isAjax){
            $data=Yii::$app->request->post();
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            if (isset($data['proyecto']) == 'true') {
                if ($data['pk_bolsa'] != '') {
                    $pks_periodos = [];
                    $periodos = TblProyectosPeriodos::find()->where(['FK_BOLSA' => $data['pk_bolsa']])->all();
                    $bolsa = TblCatBolsas::find()->where(['PK_BOLSA' => $data['pk_bolsa']])->one();
                    foreach ($periodos as $key) {

                        $pks_periodos[] = $key->PK_PROYECTO_PERIODO;

                        $documento_odc = TblDocumentosProyectos::find()->where(['PK_DOCUMENTO' => $key->FK_DOCUMENTO_ODC])->one();
                        if($documento_odc) {
                            $documento_odc->delete();
                        }
                        $key->FK_DOCUMENTO_ODC = null;

                        $bolsa->HORAS_DISPONIBLES = $bolsa->HORAS_DISPONIBLES + $key->HORAS_HDE;
                        $bolsa->MONTO_DISPONIBLE = $bolsa->HORAS_DISPONIBLES * $bolsa->TARIFA;
                        $bolsa->save(false);

                        /*$hde = TblDocumentosProyectos::find()->where(['PK_DOCUMENTO' => $key->FK_DOCUMENTO_HDE])->one();
                        if($hde) {
                            $hde->delete();
                        }*/
                        $key->FK_DOCUMENTO_HDE = null;
                        $key->HORAS_HDE = null;
                        $key->MONTO_HDE = null;
                        $key->FK_BOLSA = null;
                        $key->save(false);
                    }
                } else {
                    $pks_periodos = $data['pk_periodos'];
                    $periodo = TblProyectosPeriodos::findOne($pks_periodos);
                    $hde = TblDocumentosProyectos::findOne($periodo->FK_DOCUMENTO_HDE);

                    $hde->delete();

                    $periodo->FK_DOCUMENTO_HDE = null;
                    $periodo->HORAS_HDE = null;
                    $periodo->MONTO_HDE = null;
                    $periodo->save(false);
                }

            } else {
                $pks_periodos= [];
                if($data['pks_periodos']){
                    $pks_periodos= explode(',',trim($data['pks_periodos'],','));
                }

                foreach ($pks_periodos as $pk_periodo) {
                    $periodo = TblPeriodos::find()->where(['PK_PERIODO'=>$pk_periodo])->one();
                    $documento_hde= TblDocumentos::find()->where(['PK_DOCUMENTO'=>$periodo->FK_DOCUMENTO_HDE])->one();

                    if ($documento_hde&&strpos($documento_hde->NUM_DOCUMENTO, 'BLS_HDE_') !== false) {
                        $documento_odc= TblDocumentos::find()->where(['PK_DOCUMENTO'=>$periodo->FK_DOCUMENTO_ODC])->one();
                        $documento_odc->delete();

                        $numero_bolsa= str_replace('BLS_HDE_','',$documento_hde->NUM_DOCUMENTO);
                        $bolsa = TblCatBolsas::find()->where(['NUMERO_BOLSA'=>$numero_bolsa])->one();

                        $bolsa->HORAS_DISPONIBLES = $bolsa->HORAS_DISPONIBLES + $periodo->HORAS;
                        $bolsa->MONTO_DISPONIBLE =  $bolsa->HORAS_DISPONIBLES * $bolsa->TARIFA;

                        $bolsa->save(false);

                        $periodo->FK_DOCUMENTO_ODC=null;
                        // $periodo->TARIFA=null;
                        // $periodo->HORAS=null;
                        // $periodo->MONTO=null;
                    }

                    $documento_hde->delete();

                    $periodo->FK_DOCUMENTO_HDE=null;
                    //$periodo->TARIFA_HDE=null;
                    $periodo->HORAS_HDE=null;
                    $periodo->MONTO_HDE=null;
                    $periodo->save(false);
                }
            }

            $res= [
                'data'=>$data,
                'pks_periodos'=>$pks_periodos,
            ];
            return $res;
        }
    }

    public function actionEliminar_odc(){
        if (Yii::$app->request->isAjax){
            $data=Yii::$app->request->post();
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            if (isset($data['proyecto']) == 'true') {
                if ($data['pk_bolsa'] != '') {
                    $pks_periodos = [];
                    $periodos = TblProyectosPeriodos::find()->where(['FK_BOLSA' => $data['pk_bolsa']])->all();
                    $bolsa = TblCatBolsas::find()->where(['PK_BOLSA' => $data['pk_bolsa']])->one();
                    foreach ($periodos as $key) {

                        $pks_periodos[] = $key->PK_PROYECTO_PERIODO;

                        $documento_odc = TblDocumentosProyectos::find()->where(['PK_DOCUMENTO' => $key->FK_DOCUMENTO_ODC])->one();
                        if($documento_odc) {
                            $documento_odc->delete();
                        }
                        $key->FK_DOCUMENTO_ODC = null;
                        $bolsa->save(false);

                        $key->FK_BOLSA = null;
                        $key->save(false);
                    }
                } else {
                    $pks_periodos = $data['pks_periodos'];
                    $periodo = TblProyectosPeriodos::findOne($pks_periodos);
                    $odc = TblDocumentosProyectos::findOne($periodo->FK_DOCUMENTO_ODC);

                    $odc->delete();

                    $periodo->FK_DOCUMENTO_ODC = null;
                    $periodo->HORAS_ODC = null;
                    $periodo->save(false);
                }

            } else {
                $pks_periodos= [];
                if($data['pks_periodos']){
                    $pks_periodos= explode(',',trim($data['pks_periodos'],','));
                }

                foreach ($pks_periodos as $pk_periodo) {
                    $periodo = TblPeriodos::find()->where(['PK_PERIODO'=>$pk_periodo])->one();

                    $documento_odc= TblDocumentos::find()->where(['PK_DOCUMENTO'=>$periodo->FK_DOCUMENTO_ODC])->one();
                    if ($documento_odc&&strpos($documento_odc->NUM_DOCUMENTO, 'BLS_ODC_') !== false) {

                    }else{

                        $periodo->FK_DOCUMENTO_ODC=null;
                        $periodo->save(false);
                    }
                }
            }

            $res= [
                'data'=>$data,
                'pks_periodos'=>$pks_periodos,
            ];
            return $res;
        }
    }
}
