<?php

namespace app\controllers;

use Yii;
use app\models\TblIncidenciasNomina;
use app\models\TblEmpleados;
use app\models\TblPerfilEmpleados;
use app\models\TblCatTipoIncidencia;
use app\models\TblCatDiasVacaciones;
use app\models\TblVacacionesEmpleado;
use app\models\TblAsignacionesSeguimiento;
use app\models\TblAsignaciones;
use app\models\TblPrimasVacacionalesRechazadas;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use yii\db\Expression;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * IncidenciasNominaController implements the CRUD actions for TblIncidenciasNomina model.
 */
/*********************************************************************************************************
    Control de cambios
**********************************************************************************************************
    Autor: Saul Castillo Montes
    Fecha: 17/06/2016
    Descripción: Se crea el componente
**********************************************************************************************************
    Autor: Saul Castillo Montes
    Fecha: 24/06/2016
    Descripción: Se agrega el apartado de Control de cambios
*********************************************************************************************************/
class IncidenciasNominaController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all TblIncidenciasNomina models.
     * @return mixed
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

        //se crea funcion para comprobantes view
    public function actionComprobantes($id)
    {

        $modelPerfilEmpleados = tblperfilempleados::find()->where(['FK_EMPLEADO' => $id])->limit(1)->one();

        return $this->render('comprobantes', [
            'model' => $modelPerfilEmpleados]);
       
        
    }

    /**
     * Displays a single TblIncidenciasNomina model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    public function actionCreate_comprobante()
    {

        return $this->render('_form_comprobante');
    }


    /**
     * Creates a new TblIncidenciasNomina model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new TblIncidenciasNomina();
        if ($model->load(Yii::$app->request->post())){
            //Recibe todas las variables del formulario en la variable $data
            $data = Yii::$app->request->post();
            $connection = \Yii::$app->db;
            $comentarioSinAmperson = '';
            $sueldoNetoActual = $data['sueldoNeto'];
            
            //Cambia valores y da nuevos valores a variables del modelo
            $model->FK_ADMINISTRADORA = $data['idAdministradora'];
            $model->FK_ESTATUS_INCIDENCIA = 1;
            $model->FK_USUARIO = user_info()['PK_USUARIO'];
            $model->QUINCENA_APLICAR = transform_date($model->QUINCENA_APLICAR,'Y-m-d');
            $model->VIGENCIA = transform_date($model->VIGENCIA,'Y-m-d');
            //Como mejora se sustituyen los caracteres 'Amperson' ya que estos caracteres provocan error en la libreria que genera los exceles.
            if($data['TblIncidenciasNomina']['COMENTARIOS'] != ''){
                $comentarioSinAmperson = str_replace('&', 'y', $data['TblIncidenciasNomina']['COMENTARIOS']);
            }
            $model->COMENTARIOS = $comentarioSinAmperson != '' ? $comentarioSinAmperson : $data['TblIncidenciasNomina']['COMENTARIOS'];
            $model->FECHA_REGISTRO = date('Y-m-d H:i:s');
            if($model->FK_TIPO_INCIDENCIA==8){
                $modelEmpleados = TblEmpleados::find()->where(['PK_EMPLEADO' => $model->FK_EMPLEADO])->limit(1)->one();
                $modelPerfilEmpleados = TblPerfilEmpleados::find()->where(['FK_EMPLEADO' => $model->FK_EMPLEADO])->limit(1)->one();
                $nombreEmpleado = $modelEmpleados->NOMBRE_EMP.' '.$modelEmpleados->APELLIDO_PAT_EMP.' '.$modelEmpleados->APELLIDO_MAT_EMP;
                $correoInterno = ($modelEmpleados->EMAIL_INTERNO!=''?$modelEmpleados->EMAIL_INTERNO:'');
                if($modelPerfilEmpleados->FK_ESTATUS_RECURSO==2){
                    $correoAsignado = ($modelEmpleados->EMAIL_ASIGNADO!=''?$modelEmpleados->EMAIL_ASIGNADO:'');
                } else {
                    $correoAsignado = '';
                }
                $quincena = transform_date($model->QUINCENA_APLICAR, 'd/m/Y');
                $this->enviarCorreoIncidenciaPrimaVacacional($quincena, $model->VALOR, $nombreEmpleado, $correoInterno, $correoAsignado);
            }

            //Valida los casos en los tipos de incidencia en los que la fecha de vigencia siempre tiene que ser igual a la fecha de quincena a aplicar
            //Faltas, Aumento. Prima Vacacional, Baja del Empleado, Incapacidad de Enfermedad, Incapacidad de Maternidad
            if(in_array($model->FK_TIPO_INCIDENCIA,[1,4,8,9,10,11])){
                $model->VIGENCIA = $model->QUINCENA_APLICAR;
            }
            
            //Valida si esta chequeado el Checkbox que indica si el campo Descuento/Bonificacion se pasa como positivo o negativo
            if(isset($data['chk_cambiarSigno'])){
                $model->DESCUENTO_BONIFICACION = ($model->DESCUENTO_BONIFICACION)*-1;
            }

            //Valida si la fechad e baja fue capturada, y de ser asi, la transforma al formado Y-m-d
            if(isset($model->FECHA_BAJA)) {
                $fechaBajaSinFormato = $model->FECHA_BAJA;
                $model->FECHA_BAJA = transform_date($model->FECHA_BAJA,'Y-m-d');
            }

            //Guarda el rgistro de la incidencia
            $model->save(false);   
            
            //Valida si la quincena a aplicar es mayor a la fecha de hoy. De se la quincena a aplicar igual a la de hoy, afecta en la tabla de empleado el salariod ele mpleado con el nuevo
            $quincenaAplicar = strtotime(transform_date($model->QUINCENA_APLICAR,'d-m-Y'));
            //if($quincenaAplicar <= strtotime('now') && $model->FK_TIPO_INCIDENCIA==4){
            if($model->FK_TIPO_INCIDENCIA==4){
                $modelEmpleados = TblEmpleados::find()->where(['PK_EMPLEADO' => $model->FK_EMPLEADO])->limit(1)->one();
                $modelPerfilEmpleados = tblperfilempleados::find()->where(['FK_EMPLEADO' => $model->FK_EMPLEADO])->limit(1)->one();
                $modelPerfilEmpleados->SUELDO_NETO = $model->VALOR;
                $modelPerfilEmpleados->save(false);

                $nombreEmpleado = $modelEmpleados->NOMBRE_EMP.' '.$modelEmpleados->APELLIDO_PAT_EMP.' '.$modelEmpleados->APELLIDO_MAT_EMP;
                $correoInterno = ($modelEmpleados->EMAIL_INTERNO!=''?$modelEmpleados->EMAIL_INTERNO:'');
                if($modelPerfilEmpleados->FK_ESTATUS_RECURSO==2){
                    $correoAsignado = ($modelEmpleados->EMAIL_ASIGNADO!=''?$modelEmpleados->EMAIL_ASIGNADO:'');
                } else {
                    $correoAsignado = '';
                }
                $quincena = transform_date($model->QUINCENA_APLICAR, 'd/m/Y');
                $this->enviarCorreoIncidenciaActualizacionSueldo($quincena, $model->VALOR, $nombreEmpleado, $correoInterno, $correoAsignado);
            } 

            if($sueldoNetoActual > $model->VALOR && $model->FK_TIPO_INCIDENCIA==4){
                $descripcionBitacora = 'PK_INCIDENCIA_NOMINA='.$model->PK_INCIDENCIA_NOMINA.", sueldo_actual=$sueldoNetoActual, nuevo_sueldo=".$model->VALOR;
            } else {
                $descripcionBitacora = 'PK_INCIDENCIA_NOMINA='.$model->PK_INCIDENCIA_NOMINA;
            }

            user_log_bitacora($descripcionBitacora,'Alta de Incidencia',$model->PK_INCIDENCIA_NOMINA);

            if($data['TblIncidenciasNomina']['FK_TIPO_INCIDENCIA'] == 1
                || $data['TblIncidenciasNomina']['FK_TIPO_INCIDENCIA'] == 9
                || $data['TblIncidenciasNomina']['FK_TIPO_INCIDENCIA'] == 10
                || $data['TblIncidenciasNomina']['FK_TIPO_INCIDENCIA'] == 11){
                $tipoIncidencia = $connection->createCommand("
                    SELECT cti.DESC_TIPO_INCIDENCIA FROM tbl_cat_tipo_incidencia cti
                    WHERE ".$data['TblIncidenciasNomina']['FK_TIPO_INCIDENCIA']." = CTI.PK_TIPO_INCIDENCIA")->queryOne();
                
                $sqlFiltroAsig = $connection->createCommand("
                    SELECT tbl_asig_filtro.PK_ASIGNACION FROM 
                    (SELECT * FROM tbl_asignaciones a 
                    WHERE a.FK_EMPLEADO =".$model->FK_EMPLEADO." AND (a.FK_ESTATUS_ASIGNACION = 1 OR a.FK_ESTATUS_ASIGNACION = 2)
                    ORDER BY a.PK_ASIGNACION DESC) as tbl_asig_filtro
                    WHERE '".$model->QUINCENA_APLICAR."' BETWEEN tbl_asig_filtro.FECHA_INI AND tbl_asig_filtro.FECHA_FIN")->queryOne();
                
                if(!empty($sqlFiltroAsig) && !empty($tipoIncidencia)){
                    $modelAsignacionesSeguimiento = new TblAsignacionesSeguimiento();
                    if(isset($model->FECHA_BAJA)) {
                        $model->FECHA_BAJA = transform_date($model->FECHA_BAJA,'d/m/Y');
                        $modelAsignacionesSeguimiento->COMENTARIOS = "SE CREÓ TIPO DE INCIDENCIA: ".$tipoIncidencia['DESC_TIPO_INCIDENCIA'].", COMENTARIOS INSERTADOS: ".$data['TblIncidenciasNomina']['COMENTARIOS'].", FECHA DE BAJA: ".transform_date($model->FECHA_BAJA, 'd/m/Y').", FECHA DE REGISTRO: ".date('d/m/Y');
                    }
                    $modelAsignacionesSeguimiento->COMENTARIOS = "SE CREÓ TIPO DE INCIDENCIA: ".$tipoIncidencia['DESC_TIPO_INCIDENCIA'].", COMENTARIOS INSERTADOS: ".$data['TblIncidenciasNomina']['COMENTARIOS'].", FECHA DE QUINCENA A APLICAR: ".transform_date($model->QUINCENA_APLICAR, 'd/m/Y').", FECHA DE REGISTRO: ".date('d/m/Y');
                    $modelAsignacionesSeguimiento->FECHA_REGISTRO = date('Y-m-d H:i:s');
                    $modelAsignacionesSeguimiento->FK_ASIGNACION = $sqlFiltroAsig['PK_ASIGNACION'];
                    $modelAsignacionesSeguimiento->FK_USUARIO = user_info()['PK_USUARIO'];
                    $modelAsignacionesSeguimiento->save(false);
                }
            }
            
            if($model->FK_TIPO_INCIDENCIA==9){//Si se registra una incidencia de tipo baja de empleado
                $modelEmpleados = TblEmpleados::find()->where(['PK_EMPLEADO' => $model->FK_EMPLEADO])->limit(1)->one();
                $modelPerfilEmpleados = TblPerfilEmpleados::find()->where(['FK_EMPLEADO' => $model->FK_EMPLEADO])->limit(1)->one();
                $nombreEmpleado = $modelEmpleados->NOMBRE_EMP.' '.$modelEmpleados->APELLIDO_PAT_EMP.' '.$modelEmpleados->APELLIDO_MAT_EMP;
                $unidadNegocioEmpleado = $modelPerfilEmpleados->FK_UNIDAD_NEGOCIO;
                $this->enviarCorreoIncidenciaBaja($fechaBajaSinFormato, $nombreEmpleado, $unidadNegocioEmpleado);
            }

            $connection->close();

            //Redirecciona al usuario a la vista [incidencias-nomina/index]
            return $this->redirect(['update','id'=>$model->PK_INCIDENCIA_NOMINA, 'action'=>'insert']);
        } else {
            return $this->render('_form', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing TblIncidenciasNomina model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->QUINCENA_APLICAR = transform_date($model->QUINCENA_APLICAR,'d/m/Y');
        $model->VIGENCIA = transform_date($model->VIGENCIA,'d/m/Y');
        $model->FECHA_BAJA = isset($model->FECHA_BAJA)?transform_date($model->FECHA_BAJA,'d/m/Y'):null;
        $modelEmpleados = TblEmpleados::find()->where(['PK_EMPLEADO' => $model->FK_EMPLEADO])->limit(1)->one();
        $modelPerfilEmpleados = TblPerfilEmpleados::find()->where(['FK_EMPLEADO' => $model->FK_EMPLEADO])->limit(1)->one();
        $modelTipoIncidencias = TblCatTipoIncidencia::find()->where(['PK_TIPO_INCIDENCIA' => $model->FK_TIPO_INCIDENCIA])->limit(1)->one();
        $extra['nombreEmpleado'] = $modelEmpleados->NOMBRE_EMP.' '.$modelEmpleados->APELLIDO_PAT_EMP.' '.$modelEmpleados->APELLIDO_MAT_EMP;
        $comentarioSinAmperson = '';
        $extra['descTpoIncidencias'] = $modelTipoIncidencias->DESC_TIPO_INCIDENCIA;
        $connection = \Yii::$app->db;
        
        if ($model->load(Yii::$app->request->post())) {
            //Recibe todas las variables del formulario en la variable $data
            $data = Yii::$app->request->post();
            $sueldoNetoActual = $data['sueldoNeto'];
             //Cambia valores y da nuevos valores a variables del modelo
            $model->QUINCENA_APLICAR = transform_date($model->QUINCENA_APLICAR,'Y-m-d');
            $model->VIGENCIA = transform_date($model->VIGENCIA,'Y-m-d');
            //Como mejora se sustituyen los caracteres 'Amperson' ya que estos caracteres provocan error en la libreria que genera los exceles.
            if($data['TblIncidenciasNomina']['COMENTARIOS'] != ''){
                $comentarioSinAmperson = str_replace('&', 'y', $data['TblIncidenciasNomina']['COMENTARIOS']);
            }
            $model->COMENTARIOS = $comentarioSinAmperson != '' ? $comentarioSinAmperson : $data['TblIncidenciasNomina']['COMENTARIOS'];
            //Valida los casos en los tipos de incidencia en los que la fecha de vigencia siempre tiene que ser igual a la fecha de quincena a aplicar
            //Faltas, Aumento. Prima Vacacional, Baja del Empleado, Incapacidad de Enfermedad, Incapacidad de Maternidad
            if(in_array($model->FK_TIPO_INCIDENCIA,[1,4,8,9,10,11])){
                $model->VIGENCIA = $model->QUINCENA_APLICAR;
            }
            
            //Valida si esta chequeado el Checkbox que indica si el campo Descuento/Bonificacion se pasa como positivo o negativo
            if(isset($data['chk_cambiarSigno'])){
                $model->DESCUENTO_BONIFICACION = ($model->DESCUENTO_BONIFICACION)*-1;
            }

            //Valida si la fecha de baja fue capturada, y de ser asi, la transforma al formado Y-m-d
            if(isset($model->FECHA_BAJA)) {
                $model->FECHA_BAJA = transform_date($model->FECHA_BAJA,'Y-m-d');
            }

            //dd($model);
            //Guarda el rgistro de la incidencia
            $model->save(false);   

            if($model->FK_TIPO_INCIDENCIA==4){
                
                $modelPerfilEmpleados = tblperfilempleados::find()->where(['FK_EMPLEADO' => $model->FK_EMPLEADO])->limit(1)->one();
                $modelPerfilEmpleados->SUELDO_NETO = $model->VALOR;
                $modelPerfilEmpleados->save(false);
                
                $descripcionBitacora = 'PK_INCIDENCIA_NOMINA='.$model->PK_INCIDENCIA_NOMINA.", sueldo_actual=$sueldoNetoActual, nuevo_sueldo=".$model->VALOR;
            } else {
                $descripcionBitacora = 'PK_INCIDENCIA_NOMINA='.$model->PK_INCIDENCIA_NOMINA;
            }

            user_log_bitacora($descripcionBitacora,'Modificación de Incidencia',$model->PK_INCIDENCIA_NOMINA);

            $tipoIncidencia = $connection->createCommand("
                SELECT cti.DESC_TIPO_INCIDENCIA FROM tbl_cat_tipo_incidencia cti
                WHERE ".$data['tipoIncidencia']." = CTI.PK_TIPO_INCIDENCIA")->queryOne();

            $sqlFiltroAsig = $connection->createCommand("
                SELECT tbl_asig_filtro.PK_ASIGNACION FROM 
                (SELECT * FROM tbl_asignaciones a 
                WHERE a.FK_EMPLEADO =".$data['idEmpleado']." AND (a.FK_ESTATUS_ASIGNACION = 1 OR a.FK_ESTATUS_ASIGNACION = 2)
                ORDER BY a.PK_ASIGNACION DESC) as tbl_asig_filtro
                WHERE '".$model->QUINCENA_APLICAR."' BETWEEN tbl_asig_filtro.FECHA_INI AND tbl_asig_filtro.FECHA_FIN")->queryOne();
            
            if(!empty($sqlFiltroAsig) && !empty($tipoIncidencia)){
                $modelAsignacionesSeguimiento = new TblAsignacionesSeguimiento();
                if(isset($model->FECHA_BAJA)) {
                    $model->FECHA_BAJA = transform_date($model->FECHA_BAJA,'d/m/Y');
                    $modelAsignacionesSeguimiento->COMENTARIOS = "SE CREÓ TIPO DE INCIDENCIA: ".$tipoIncidencia['DESC_TIPO_INCIDENCIA'].", COMENTARIOS INSERTADOS: ".$data['TblIncidenciasNomina']['COMENTARIOS'].", FECHA DE BAJA: ".transform_date($model->FECHA_BAJA, 'd/m/Y').", FECHA DE REGISTRO: ".date('d/m/Y');
                }
                $modelAsignacionesSeguimiento->COMENTARIOS = "SE CREÓ TIPO DE INCIDENCIA: ".$tipoIncidencia['DESC_TIPO_INCIDENCIA'].", COMENTARIOS INSERTADOS: ".$data['TblIncidenciasNomina']['COMENTARIOS'].", FECHA DE QUINCENA A APLICAR: ".transform_date($model->QUINCENA_APLICAR, 'd/m/Y').", FECHA DE REGISTRO: ".date('d/m/Y');
                $modelAsignacionesSeguimiento->FECHA_REGISTRO = date('Y-m-d H:i:s');
                $modelAsignacionesSeguimiento->FK_ASIGNACION = $sqlFiltroAsig['PK_ASIGNACION'];
                $modelAsignacionesSeguimiento->FK_USUARIO = user_info()['PK_USUARIO'];
                $modelAsignacionesSeguimiento->save(false);
            }

            $connection->close();
            return $this->redirect(['update', 'id' => $model->PK_INCIDENCIA_NOMINA, 'action'=>'insert']);
        } else {
            return $this->render('_form_update', [
                'model' => $model,
                'extra' => $extra,
            ]);
        }
    }

    /**
     * Deletes an existing TblIncidenciasNomina model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the TblIncidenciasNomina model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return TblIncidenciasNomina the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TblIncidenciasNomina::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /* Funcion que se ejecuta por medio de ajax, la cual se encarga de obtener el salario de un empleado y su administradora*/
    public function actionObtener_datos_empleados(){
        if (Yii::$app->request->isAjax) {
            $data = Yii::$app->request->post();
            $idEmpleado= explode(":", $data['idEmpleado']);
            $idEmpleado = $idEmpleado[0];
            if($idEmpleado != '' && $idEmpleado >0){
                $modelEmpleados = TblEmpleados::find()->where(['PK_EMPLEADO' => $idEmpleado])->limit(1)->one();
                $modelPerfilEmpleados = tblperfilempleados::find()->where(['FK_EMPLEADO' => $idEmpleado])->limit(1)->one();
                $modelVacacionesEmpleados = TblVacacionesEmpleado::find()->where(['FK_EMPLEADO' => $idEmpleado])->limit(1)->one();
                if(isset($modelVacacionesEmpleados->DIAS_DISPONIBLES)){
                    $vacacionesDisponibles = $modelVacacionesEmpleados->DIAS_DISPONIBLES;
                } else {
                    $vacacionesDisponibles = 0;
                }
                $anioIngreso = substr($modelPerfilEmpleados->FECHA_INGRESO, 0,4);
                $fechaIngresoPrimaVacacional = $anioIngreso<2012?'2012-01-01':$modelPerfilEmpleados->FECHA_INGRESO;//Se hace esta validacion debido a que la prima vacional se empezo a pagar a partir del 2012
                $anioIngresoPrimaVacacional = substr($fechaIngresoPrimaVacacional, 0,4);
                $mesIngresoPrimaVacacional = substr($fechaIngresoPrimaVacacional, 5,2);
                $diaIngresoPrimaVacacional = intval(substr($fechaIngresoPrimaVacacional, 8,2));
                $aniosEmpresa = CalculaEdad($fechaIngresoPrimaVacacional);
                if($anioIngresoPrimaVacacional < date('Y') && date('m') == $mesIngresoPrimaVacacional && date('j') < $diaIngresoPrimaVacacional){
                    $aniosEmpresa = $aniosEmpresa + 1;
                }
                $modelDiasVacaciones = TblCatDiasVacaciones::find()->where(['ANIOS_ANTIGUEDAD' => $aniosEmpresa])->limit(1)->one();
                $modelDiasVacacionesSiguienteAnio = TblCatDiasVacaciones::find()->where(['ANIOS_ANTIGUEDAD' => ($aniosEmpresa+1)])->limit(1)->one();
                
                //Valida si el empleado tiene una asignacion pendiente o en ejecucion
                $modelAsignaciones = TblAsignaciones::find()
                                        ->where(['FK_EMPLEADO' => $idEmpleado])
                                        ->andWhere(['IN','FK_ESTATUS_ASIGNACION',array(1,2)])//Asignaciones con estatus 'Pendiente' o 'Ej Ejecución'
                                        ->all();
                $cantAsignacionesPendientes = count($modelAsignaciones);
                \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                return[
                    'idEmpleado' => $idEmpleado,
                    'idAdministradora' => $modelPerfilEmpleados->FK_ADMINISTRADORA,
                    'sueldoNeto' => $modelPerfilEmpleados->SUELDO_NETO,
                    'sexo' => $modelEmpleados->FK_GENERO_EMP,
                    'aniosEmpresa' => $aniosEmpresa,
                    'vacacionesPorAnio' => $modelDiasVacaciones->DIAS_VACACIONES,
                    'vacacionesSiguienteAnio' => $modelDiasVacacionesSiguienteAnio->DIAS_VACACIONES,
                    'vacacionesDisponibles' => $vacacionesDisponibles,
                    'fechaIngreso' => $modelPerfilEmpleados->FECHA_INGRESO,
                    'cantAsignacionesPendientes' => $cantAsignacionesPendientes,
                    'unidadNegocio' => $modelPerfilEmpleados->FK_UNIDAD_NEGOCIO,
                    'nombreEmpleado' => $modelEmpleados->NOMBRE_EMP.' '.$modelEmpleados->APELLIDO_PAT_EMP.' '.$modelEmpleados->APELLIDO_MAT_EMP,
                ];
            }
            
        }
    }

    //index2: ontiene las incidencias de nomina solicitadas por el usuario en la vista index.php
    public function actionIndex2(){
        $tamanio_pagina=10;
        if (Yii::$app->request->isAjax) {
            //Obtencion de parametros recibidos mediante AJAX
            $data = Yii::$app->request->post();
            $post=null;
            parse_str($data['data'],$post);
            $nombre =(!empty($post['nombre']))? ("'".trim($post['nombre'])."'"):'null';
            $idAdministradora =(!empty($post['idAdministradora']))? ("'".trim($post['idAdministradora'])."'"):'null';
            $idtipoIncidencia =(!empty($post['idtipoIncidencia']))? ("'".trim($post['idtipoIncidencia'])."'"):'null';
            $ingresoFechaIni =(!empty($post['fechaIni']))? ("'".transform_date(trim($post['fechaIni']),'Y-m-d')."'"):'null';
            $ingresoFechaFin =(!empty($post['fechaFin']))? ("'".transform_date(trim($post['fechaFin']),'Y-m-d')."'"):'null';
            $pagina         =(!empty($post['page']))? trim($post['page']):'';
            if(empty($pagina)){
                $pagina=0;
            }else{
                $pagina= $pagina-1;
            }

            //Verificar unidad de negocio del empleado
            $unidadNegocioUsuario = user_info()['INFO_EMP']['FK_UNIDAD_NEGOCIO'];
            if((isset(user_info()['IS_ADMIN_UNIDAD_NEGOCIO'])&&!empty(user_info()['IS_ADMIN_UNIDAD_NEGOCIO']))||is_super_admin()){
                $unidadNegocioFiltrar = 0;
            } else {
                $unidadNegocioFiltrar = $unidadNegocioUsuario;
            }
            
            $connection = \Yii::$app->db;
            //Valida si el post viene desde alguna bandeja
            if(!empty($post['ids_pendiente_prima_vacacional'])){
                $total_registros = $connection->createCommand("CALL SP_INCIDENCIAS_INDEX_PRIMAS_VACACIONALES($nombre,$idAdministradora,$unidadNegocioFiltrar,null,null)")->queryAll();
                $total_registros = count($total_registros);
                $total_paginas= ceil($total_registros/$tamanio_pagina);
                if($total_registros<=$tamanio_pagina){
                    $pagina=0;
                }
                $limit = $tamanio_pagina;
                $offset = $pagina*$tamanio_pagina;
                $html='';

                $datosIncidencias = [];
                if($total_registros > 0){
                    $datosIncidencias = $connection->createCommand("CALL SP_INCIDENCIAS_INDEX_PRIMAS_VACACIONALES($nombre,$idAdministradora,$unidadNegocioFiltrar,$limit,$offset)")->queryAll();
                }
            } else {
                $total_registros = $connection->createCommand("CALL SP_INCIDENCIAS_INDEX_SELECT($nombre,$idAdministradora,$idtipoIncidencia,$ingresoFechaIni,$ingresoFechaFin,$unidadNegocioFiltrar,null,null)")->queryAll();
                $total_registros = count($total_registros);
                $total_paginas= ceil($total_registros/$tamanio_pagina);
                if($total_registros<=$tamanio_pagina){
                    $pagina=0;
                }
                $limit = $tamanio_pagina;
                $offset = $pagina*$tamanio_pagina;
                $html='';

                $datosIncidencias = [];
                if($total_registros > 0){
                    $datosIncidencias = $connection->createCommand("CALL SP_INCIDENCIAS_INDEX_SELECT($nombre,$idAdministradora,$idtipoIncidencia,$ingresoFechaIni,$ingresoFechaFin,$unidadNegocioFiltrar,$limit,$offset)")->queryAll();
                }
            }
                

            $connection->close();

            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $res = array(
                'pagina'        => $pagina+1,
                'data'          => $datosIncidencias,
                'total_paginas' => $total_paginas,
                'total_registros' => $total_registros,
                'post' => $post,
                //'query' => "CALL SP_INCIDENCIAS_INDEX_SELECT($nombre,$idAdministradora,$idtipoIncidencia,$ingresoFechaIni,$ingresoFechaFin,$unidadNegocioFiltrar,$limit,$offset)",
            );
            return $res;    
        }else{
             return $this->render('index', [
                'total_paginas' => 0,
            ]);
            
        }
    }

    public function actionAprobacion_primas_vacacionales(){
        $data = Yii::$app->request->post();        
        foreach ($data['primaVacacional'] as $idEmpleado => $bandera_aprobar) {
            if($bandera_aprobar==1){
                $modelPrimaVacacional = new TblIncidenciasNomina();
                $modelPerfilEmpleados = TblPerfilEmpleados::find()->where(['FK_EMPLEADO'=> $idEmpleado])->one();
                $quincena = date('Y-m').'-15';
                $modelPrimaVacacional->FK_EMPLEADO = $idEmpleado;
                $modelPrimaVacacional->FK_ADMINISTRADORA = $modelPerfilEmpleados->FK_ADMINISTRADORA;
                $modelPrimaVacacional->FK_TIPO_INCIDENCIA = 8;
                $modelPrimaVacacional->FK_ESTATUS_INCIDENCIA = 1;
                $modelPrimaVacacional->DIAS = NULL;
                $modelPrimaVacacional->VALOR = $data['valor'][$idEmpleado];
                $modelPrimaVacacional->QUINCENA_APLICAR = $quincena;
                $modelPrimaVacacional->VIGENCIA = $quincena;
                $modelPrimaVacacional->FECHA_BAJA = NULL;
                $modelPrimaVacacional->PORCENTAJE_INCAPACIDAD = NULL;
                $modelPrimaVacacional->IMSS = NULL;
                $modelPrimaVacacional->COMENTARIOS = 'APLICACION PRIMA VACACIONAL DESDE BANDEJA';
                $modelPrimaVacacional->DESCUENTO_BONIFICACION = NULL;
                $modelPrimaVacacional->FK_USUARIO = user_info()['PK_USUARIO'];
                $modelPrimaVacacional->FECHA_REGISTRO = date('Y-m-d H:i:s');  
                $modelPrimaVacacional->save(false);

                //Envio de correo de prima vacacional
                $modelEmpleados = TblEmpleados::find()->where(['PK_EMPLEADO' => $modelPrimaVacacional->FK_EMPLEADO])->limit(1)->one();
                $modelPerfilEmpleados = TblPerfilEmpleados::find()->where(['FK_EMPLEADO' => $modelPrimaVacacional->FK_EMPLEADO])->limit(1)->one();
                $nombreEmpleado = $modelEmpleados->NOMBRE_EMP.' '.$modelEmpleados->APELLIDO_PAT_EMP.' '.$modelEmpleados->APELLIDO_MAT_EMP;
                $correoInterno = ($modelEmpleados->EMAIL_INTERNO!=''?$modelEmpleados->EMAIL_INTERNO:'');
                if($modelPerfilEmpleados->FK_ESTATUS_RECURSO==2){
                    $correoAsignado = ($modelEmpleados->EMAIL_ASIGNADO!=''?$modelEmpleados->EMAIL_ASIGNADO:'');
                } else {
                    $correoAsignado = '';
                }
                $quincenaAplicar = transform_date($modelPrimaVacacional->QUINCENA_APLICAR, 'd/m/Y');
                $this->enviarCorreoIncidenciaPrimaVacacional($quincenaAplicar, $modelPrimaVacacional->VALOR, $nombreEmpleado, $correoInterno, $correoAsignado);
            }elseif($bandera_aprobar==2){
                $modelPrimaRechazada = new TblPrimasVacacionalesRechazadas();
                $modelPrimaRechazada->FK_EMPLEADO = $idEmpleado;
                $modelPrimaRechazada->FK_USUARIO = user_info()['PK_USUARIO'];
                $modelPrimaRechazada->FECHA_REGISTRO= date('Y-m-d H:i:s'); 
                $modelPrimaRechazada->save(false); 
            }
        }

        return $this->redirect(['index']);
    }

    public function actionIndex_nomina(){
        if (Yii::$app->request->isAjax) {
            //Se obtienen parametros recibidos a traves de peticion de AJAX
            $data = Yii::$app->request->post();
            $idAdministradora =(!empty($data['idAdministradora']))? (trim($data['idAdministradora'])):'null';
            $quincenaAplicar = (!empty($data['quincenaAplicar']))?"'".(transform_date(trim($data['quincenaAplicar'])))."'":'null';
            $nombre =(!empty($data['nombre']))? ("'".trim($data['nombre'])."'"):'null';
            $idUbicacionFisica =(!empty($data['idUbicacionFisica']))? trim($data['idUbicacionFisica']):'null';
            
            //Verificar unidad de negocio del empleado
            $unidadNegocioUsuario = user_info()['INFO_EMP']['FK_UNIDAD_NEGOCIO'];
            if((isset(user_info()['IS_ADMIN_UNIDAD_NEGOCIO'])&&!empty(user_info()['IS_ADMIN_UNIDAD_NEGOCIO']))||is_super_admin()){
                $unidadNegocioFiltrar = 0;
            } else {
                $unidadNegocioFiltrar = $unidadNegocioUsuario;
            }

            //Se realiza conexion y se ejecuta el SP_NOMINA_INDEX_SELECT
            $connection = \Yii::$app->db;
            $datosNomina = $connection->createCommand("CALL SP_NOMINA_INDEX_SELECT($idAdministradora,$quincenaAplicar,$nombre,$idUbicacionFisica,$unidadNegocioFiltrar)")->queryAll();
            
            
            if(count($datosNomina) > 0){
                $html='
                <table class="table table-index2 table-fixed">
                    <thead>
                        <tr>
                            <th class="text-center" width="8.33%">Nombre</th>
                            <th class="text-center" width="7.5%">Administradora</th>
                            <th class="text-center" width="9.16%">Puesto</th>
                            <th class="text-center" width="8.33%">Ubicación Fisica</th>
                            <th class="text-center" width="8.33%">Sueldo Quincenal</th>
                            <th class="text-center" width="8.33%">Fecha Ultimo Aumento</th>
                            <th class="text-center" width="8.33%">Monto</th>
                            <th class="text-center" width="8.33%">Fecha de Antigüedad</th>
                            <th class="text-center" width="8.33%">Vacaciones Disponibles</th>
                            <th class="text-center" width="8.33%">Total Incidencia</th>
                            <th class="text-center" width="8.33%">Neto a Pagar</th>
                            <th class="text-center" width="8.33%">Quincena</th>
                        </tr>
                    </thead>
                    <tbody>';
                        foreach($datosNomina as $index => $array){
                            $sueldoQuincenal = $array['SUELDO_NETO']/2;
                            if($array['SUELDO_ANTERIOR']){
                                $diferenciaSueldoNuevoAnterior = $array['SUELDO_NETO']/2 - $array['SUELDO_ANTERIOR']/2;
                            }
                            if($array['VALOR_BAJA'] > 0){
                                $quincena = $array['VALOR_BAJA'];
                            }elseif($array['VALOR_INCAPACIDAD_MATERNIDAD'] > 0){
                                $quincena = $array['VALOR_INCAPACIDAD_MATERNIDAD'];
                            }elseif($array['VALOR_INCAPACIDAD_ENFERMEDAD'] > 0){
                                $quincena = $array['VALOR_INCAPACIDAD_ENFERMEDAD'];
                            }else {
                                $quincena = $sueldoQuincenal + $array['TOTAL_INCIDENCIAS'];
                            }
                            $html.=
                            '<tr>'.
                                '<td class="text-center" width="8.33%">'.$array['NOMBRE_EMP'].' '.$array['APELLIDO_PAT_EMP'].' '.$array['APELLIDO_MAT_EMP'].'</td>'.
                                '<td class="text-center" width="7.5%">'.$array['NOMBRE_ADMINISTRADORA'].'</td>'.
                                '<td class="text-center" width="9.16%">'.$array['DESC_PUESTO'].'</td>'.
                                '<td class="text-center" width="8.33%">'.$array['DESC_UBICACION'].'</td>'.
                                '<td class="text-center" width="8.33%">$ '.number_format($sueldoQuincenal,2,'.',',').'</td>'.
                                '<td class="text-center" width="8.33%">'.$array['AUMENTO_QUINCENA'].'</td>'.
                                '<td class="text-center" width="8.33%">$ '.($array['SUELDO_ANTERIOR']?number_format($diferenciaSueldoNuevoAnterior,2,'.',','):'').'</td>'.
                                '<td class="text-center" width="8.33%">'.$array['FECHA_INGRESO'].'</td>'.
                                '<td class="text-center" width="8.33%">'.$array['VACACIONES_DIAS_DISPONIBLES'].'</td>'.
                                '<td class="text-center" width="8.33%"><a href="javascript: void(0);" onclick="llamarDetalleIncidencias('.$array['PK_EMPLEADO'].',\''.transform_date($array['QUINCENA'],'Y-m-d').'\')">$ '.number_format($array['TOTAL_INCIDENCIAS'],2,'.',',').'</a></td>'.
                                '<td class="text-center" width="8.33%">$ '.number_format($quincena,2,'.',',').'</td>'.
                                '<td class="text-center" width="8.33%">'.$array['QUINCENA'].'</td>'.
                            '</tr>';
                        }
                    $html.='
                    </tbody>
                </table>';
            } else {
                $html = '<div class="contenedor contenedor-vacante"><h4 style="text-align:center">No existen coincidencias que cuenten con los criterios especificados en los parámetros de búsqueda</h4><div class="clear"></div></div>';
            }
            
            $connection->close();
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $res = array(
                'data' => $html,
                'datos' => $quincenaAplicar,
            );
            return $res;  
        } else {
            return $this->render('index_nomina');
        }
    }

    public function actionObtener_detalle_incidencias(){
        if (Yii::$app->request->isAjax) {
            $data = Yii::$app->request->post();
            $idEmpleado =(!empty($data['idEmpleado']))? (trim($data['idEmpleado'])):'null';
            $quincena = (!empty($data['quincena']))?"'".(transform_date(trim($data['quincena'])))."'":'null';
            $connection = \Yii::$app->db;
            $detalleIncidencias = $connection->createCommand("CALL SP_NOMINA_DETALLE_SELECT($idEmpleado,$quincena)")->queryAll();
            $html='';
            $total = 0;
            foreach($detalleIncidencias as $array){
                $total += $array['VALOR'];
                $signo= $array['VALOR']>0?'+':'';
                $html.='<tr width="100%" style="border-bottom:1px solid grey">'.
                            '<td width="33.33%">'.$array['DESC_TIPO_INCIDENCIA'].'</td>'.
                            '<td width="33.33%" class="text-center">'.$signo.number_format($array['VALOR'],2,'.',',').'</td>'.
                            '<td width="33.33%" class="text-center">'.$array['QUINCENA'].'</td>'.
                        '</tr>';
            }
            $signo= $total>0?'+':'';
            $html.='<tr style="background-color:#ccc">'.
                        '<td width="33.33%" class="font-bold" >Total:</td>'.
                        '<td width="33.33%" class="font-bold text-center">'.$signo.number_format($total,2,'.',',').'</td>'.
                        '<td width="33.33%" class="text-center"></td>'.
                    '</tr>';

            $connection->close();
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $res = array(
                'data' => $html,
            );
            return $res;
        }
    }

    /*
        Ajax Baja
     */
    public function actionConsulta_baja(){
        $data = Yii::$app->request->post();
        $fecha_baja = transform_date($data['fecha_baja'],'Y-m-d');
        $fecha_ingreso = transform_date($data['fecha_ingreso'],'Y-m-d');

        list($ano,$mes,$dia) = explode("-",$fecha_ingreso);
        $ano_diferencia  = date("Y",strtotime($fecha_baja)) - $ano;
        $mes_diferencia = date("m",strtotime($fecha_baja)) - $mes;
        $dia_diferencia   = date("d",strtotime($fecha_baja)) - $dia;
        if ($dia_diferencia < 0 || $mes_diferencia < 0)
            $ano_diferencia--;
        $modelDiasVacaciones = TblCatDiasVacaciones::find()->where(['ANIOS_ANTIGUEDAD' => $ano_diferencia+1])->limit(1)->one();
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $res = [
            // 'data'=>$data,
            'fecha_baja'=>$fecha_baja,
            'ano_diferencia'=>$ano_diferencia,
            'dias_vacaciones'=>$modelDiasVacaciones->DIAS_VACACIONES,
        ];
        return $res;
    }

    public function actionEnvio_correo_intento_baja(){
        if (Yii::$app->request->isAjax){
            //Recepción de variables de entrada
            $data = Yii::$app->request->post();
            $idEmpleado= explode(":", $data['idEmpleado']);
            $idEmpleado = $idEmpleado[0];
            $FK_UNIDAD_NEGOCIO_ANTERIOR= explode(":", $data['FK_UNIDAD_NEGOCIO_ANTERIOR']);
            $FK_UNIDAD_NEGOCIO_ANTERIOR = $FK_UNIDAD_NEGOCIO_ANTERIOR[0];
            $NOMBRE_EMPLEADO= explode(":", $data['NOMBRE_EMPLEADO']);
            $NOMBRE_EMPLEADO = $NOMBRE_EMPLEADO[0];
            $fechaHoraHoy = date('d/m/Y H:i:s');
            $envioCorreo =false;

            //Obtener Puestos para Correo
            $puestosEnviarCorreo= explode(',',get_config('EMPLEADOS','PUESTOS_ENVIO_CORREOS_BAJA'));

            //Obtener datos de usuario
            $nombreUsuario = $_SESSION['usuario']['NOMBRE_COMPLETO'];
            
            //Obtener Unidad de Negocio
            $unidadNegocioExcepcion1 = explode(',',get_config('CONFIG','EXCEPCION_BTN_REGLA_UNIDAD_NEGOCIO'));
            if(in_array($FK_UNIDAD_NEGOCIO_ANTERIOR, $unidadNegocioExcepcion1)){
                $unidadNegocio = $unidadNegocioExcepcion1;
            } else {
                $unidadNegocio[] = $FK_UNIDAD_NEGOCIO_ANTERIOR;
            }

            //Obtener correos de Destino
            $arrayCorreos = [];
            $query = new Query;
            $query->select('emp.EMAIL_INTERNO, emp.NOMBRE_EMP, emp.APELLIDO_PAT_EMP, emp.APELLIDO_MAT_EMP')
                ->from('tbl_perfil_empleados as perfil')
                ->join('LEFT JOIN','tbl_empleados as emp',
                    'perfil.FK_EMPLEADO = emp.PK_EMPLEADO')
                ->where(['IN','perfil.FK_PUESTO', $puestosEnviarCorreo])
                ->andWhere(['IN','perfil.FK_UNIDAD_NEGOCIO',$unidadNegocio])
                ->andWhere(['NOT IN','perfil.FK_ESTATUS_RECURSO',array(4,6)])
                ->distinct();
            $command = $query->createCommand();
            $rows = $command->queryAll();
            foreach($rows as $array){
                if($array['EMAIL_INTERNO']){
                    $arrayCorreos[] = $array['EMAIL_INTERNO'];
                }
            }

            if(count($arrayCorreos) > 0){
                // Variables de envio de correo
                $de= get_config('EMPLEADOS','CORREO_FELICITACIONES');
                $para=$arrayCorreos;
                $asunto= 'Intento de Registro de Incidencia "Baja de Empleado" ';
                $mensaje="Buen día <br><br>
                El usuario <b>$nombreUsuario</b> intento registrar una incidencia de tipo baja al empleado <u>$NOMBRE_EMPLEADO</u>, el cual tiene asignaciones con estatus <b>Pendientes y/o En Ejecución</b>, favor de revisarlo.<br><br>
                $fechaHoraHoy";

                //Envio de correo
                $enviado = send_mail($de,$para, $asunto, $mensaje,[]);
                $envioCorreo=true;
            }
            
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return [
                'envioCorreo' => $envioCorreo,
            ];
        }
    }

    public function enviarCorreoIncidenciaPrimaVacacional($quincena, $valor, $nombreEmpleado, $correoInterno, $correoAsignado){
        
        $correosFijosPrimaVacacional= explode(',',get_config('INCIDENCIAS-NOMINA','CORREO_INCIDENCIA_PRIMA_VACACIONAL'));
        if($correoInterno!='' || $correoAsignado!=''){
            $arrayCorreos = $correosFijosPrimaVacacional;
            if($correoInterno!=''){
                $arrayCorreos[] =  $correoInterno;
            }
            if($correoAsignado!=''){
                $arrayCorreos[] =  $correoAsignado;
            }
            
            $valorPrimaVacacional = number_format($valor,2,'.',',');
            $añoActual = date('Y');
            $de=get_config('EMPLEADOS','CORREO_FELICITACIONES');
            $para=$arrayCorreos;
            $asunto= 'Pago de Prima Vacacional';
            $mensaje="Buen día $nombreEmpleado.<br><br>
            Mediante este conducto se te notifica que tu prima vacacional del año $añoActual se te depositará en la quincena $quincena, por un valor de: $ $valorPrimaVacacional <br><br>
            Saludos.
            <br><br><br><br><br><br><br>";
            $enviado = send_mail($de,$para, $asunto, $mensaje,[]);
        } else{
            $de= get_config('EMPLEADOS','CORREO_FELICITACIONES');
            $para=$correosFijosPrimaVacacional;
            $asunto= 'Fallo al enviar pago de Prima Vacacional';
            $mensaje="Buen día. <br><br>
            El empleado <b>$nombreEmpleado</b> no tiene correos capturados para notificarle el pago de su prima vacacional. <br><br>
            Saludos.";
            $enviado = send_mail($de,$para, $asunto, $mensaje,[]);
        }
    }

    public function enviarCorreoIncidenciaBaja($fechaBaja, $nombreEmpleado, $unidadNegocioEmpleado)
    {
        //Obtener Puestos para Correo
        $puestosEnviarCorreo= explode(',',get_config('EMPLEADOS','PUESTOS_ENVIO_CORREOS_BAJA'));
        
        //Obtener Unidad de Negocio
        $unidadNegocioExcepcion1 = explode(',',get_config('CONFIG','EXCEPCION_BTN_REGLA_UNIDAD_NEGOCIO'));
        if(in_array($unidadNegocioEmpleado, $unidadNegocioExcepcion1)){
            $unidadNegocio = $unidadNegocioExcepcion1;
        } else {
            $unidadNegocio[] = $unidadNegocioEmpleado;
        }

        //Obtener correos de Destino
        $arrayCorreos = [];
        $query = new Query;
        $query->select('emp.EMAIL_INTERNO')
            ->from('tbl_perfil_empleados as perfil')
            ->join('LEFT JOIN','tbl_empleados as emp',
                'perfil.FK_EMPLEADO = emp.PK_EMPLEADO')
            ->where(['IN','perfil.FK_PUESTO', $puestosEnviarCorreo])
            ->andWhere(['IN','perfil.FK_UNIDAD_NEGOCIO',$unidadNegocio])
            ->andWhere(['NOT IN','perfil.FK_ESTATUS_RECURSO',array(4,6)])
            ->distinct();
        $command = $query->createCommand();
        $rows = $command->queryAll();
        foreach($rows as $array){
            if($array['EMAIL_INTERNO']){
                $arrayCorreos[] = $array['EMAIL_INTERNO'];
            }
        }

        $correosFijos = explode(',',get_config('EMPLEADOS','ENVIO_CORREOS_FIJOS_BAJA_EMPLEADO'));

        $arrayCorreos = array_merge($arrayCorreos,$correosFijos);

        if(count($arrayCorreos) > 0){
            // Variables de envio de correo
            $de= get_config('EMPLEADOS','CORREO_FELICITACIONES');
            $para=$arrayCorreos;
            $asunto= 'Registro de Incidencia "Baja de Empleado"';
            $mensaje="Buen día <br><br>
            Al empleado <u>$nombreEmpleado</u> se le registro una incidencia de tipo <b>Baja</b>, por lo cual se prevee que no se presente a laborar a partir del dia: $fechaBaja.<br><br>
            Saludos";

            //Envio de correo
            $enviado = send_mail($de,$para, $asunto, $mensaje,[]);
        }
    }

    public function enviarCorreoIncidenciaActualizacionSueldo($quincena, $valor, $nombreEmpleado, $correoInterno, $correoAsignado){
        
        $correosFijosPrimaVacacional= explode(',',get_config('INCIDENCIAS-NOMINA','CORREO_INCIDENCIA_PRIMA_VACACIONAL'));
        if($correoInterno!='' || $correoAsignado!=''){
            $arrayCorreos = $correosFijosPrimaVacacional;
            if($correoInterno!=''){
                $arrayCorreos[] =  $correoInterno;
            }
            if($correoAsignado!=''){
                $arrayCorreos[] =  $correoAsignado;
            }
            
            $valorPrimaVacacional = number_format($valor,2,'.',',');
            $añoActual = date('Y');
            $de=get_config('EMPLEADOS','CORREO_FELICITACIONES');
            $para=$arrayCorreos;
            $asunto= 'Actualización de sueldo';
            $mensaje="<b>Buen día $nombreEmpleado.<br><br>
            Mediante este conducto se te notifica que tendrás un AUMENTO DE SUELDO, el cuál se te depositará en la quincena $quincena, siendo tu nuevo sueldo mensual: $ $valorPrimaVacacional <br><br>
            Saludos.</b>
            <br><br><br><br><br><br><br>";
            $enviado = send_mail($de,$para, $asunto, $mensaje,[]);
        } else{
            $de= get_config('EMPLEADOS','CORREO_FELICITACIONES');
            $para=$correosFijosPrimaVacacional;
            $asunto= 'Fallo al enviar pago de Prima Vacacional';
            $mensaje="Buen día. <br><br>
            El empleado <b>$nombreEmpleado</b> no tiene correos capturados para notificarle el pago de su prima vacacional. <br><br>
            Saludos.";
            $enviado = send_mail($de,$para, $asunto, $mensaje,[]);
        }
    }

    /*
        Ajax consultar salario para incidencias
    */

    public function actionConsultar_salario(){
        $data = Yii::$app->request->post();
        $quincena_aplicar = transform_date($data['quincena_aplicar'],'Y-m-d');
        $idEmpleado = $data['idEmpleado'];
        $modelIncidenciasAumento = TblIncidenciasNomina::find()
            ->andFilterWhere(
                ['and',
                    ['=', 'FK_EMPLEADO', $idEmpleado],
                    ['<=', 'QUINCENA_APLICAR', $quincena_aplicar],
                    ['=','FK_TIPO_INCIDENCIA', 4],
                ])
            ->orderBy('QUINCENA_APLICAR DESC')
            ->limit(1)
            ->one();
        $sueldo = empty($modelIncidenciasAumento)?null:$modelIncidenciasAumento->VALOR;
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $res = [
            'sueldoNeto' => $sueldo,
        ];
        return $res;
    }

    public function actionCancelar_incidencia()
    {
        $data = Yii::$app->request->post();
        $pk_incidencia_nomina = $data['pk_incidencia_nomina'];
        $model = $this->findModel($pk_incidencia_nomina);

        $model->FK_ESTATUS_INCIDENCIA=2;
        $model->save(false);
        //Inserta bitacora
        $descripcionBitacora = 'PK_INCIDENCIA_NOMINA='.$model->PK_INCIDENCIA_NOMINA;
        user_log_bitacora($descripcionBitacora,'Cancelación de Incidencia',$model->PK_INCIDENCIA_NOMINA);
        return $this->redirect(['index']);
    }
}