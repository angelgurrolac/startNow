<?php

namespace app\controllers;

use Yii;
use app\models\TblVacacionesEmpleado;
use app\models\TblBitVacacionesEmpleado;
use app\models\TblFechasVacaciones;
use app\models\TblPerfilEmpleados;
use app\models\TblEmpleados;
use app\models\TblAsignacionesSeguimiento;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\VacacionesFechasSearch;
use yii\data\SqlDataProvider;
use yii\db\Query;
use yii\db\Expression;
use yii\db\ActiveQuery;

/**
 * VacacionesController implements the CRUD actions for TblVacacionesEmpleado model.
 */
/*********************************************************************************************************
    Control de cambios
**********************************************************************************************************
    Autor: Saul Castillo Montes
    Fecha: 09/07/2016
    Descripción: Se crea el componente
**********************************************************************************************************
    Autor: Saul Castillo Montes
    Fecha: 26/07/2016
    Descripción: Se agrega el apartado de Control de cambios
*********************************************************************************************************/
class VacacionesController extends Controller
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
     * Lists all TblVacacionesEmpleado models.
     * @return mixed
     */
    public function actionIndex()
    {
      $connection = \Yii::$app->db;
      $datosIncidencias = $connection->createCommand("CALL SP_VACACIONES_INDEX_SELECT(null,0,null,null)")->queryAll();

      $model = new TblVacacionesEmpleado();
      $modelBitVacaciones = new TblBitVacacionesEmpleado();
      return $this->render('index',
          [
          'datosIncidencias' => $datosIncidencias,
          'model' => $model,
          'modelBitVacaciones' => $modelBitVacaciones,
          ]);
    }

    /**
     * Displays a single TblVacacionesEmpleado model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new TblVacacionesEmpleado model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        //$model = new TblVacacionesEmpleado();
        if (Yii::$app->request->post()){
            //1.- Se extraen todas las variables de entrada
            $data = Yii::$app->request->post();
            $connection = \Yii::$app->db;

            //2.- Se extraen las variables
            $idVacacionesEmpleado = $data['PK_VACACIONES_EMPLEADO'];
            $pkEmpleado = $data['PK_EMPLEADO'];
            $aplica2x1 = $data['aplica2x1'];
            $fechasSolicitadas = $data['fechasSolicitadas'];
            $comentarios = $data['comentarios'];
            if($aplica2x1==1){
                $diasSolicitados = 1;
            } else {
                $diasSolicitados = $data['diasSolicitados'];
            }

            //3.- Se inicializan modelos y se hacen calculos
            $model = $this->findModel($idVacacionesEmpleado);
            $dias_disponibles_vacaciones = $model->DIAS_DISPONIBLES;
            $dias_disponibles_hrs_extra = $model->DIAS_HORAS_EXTRAS;
            $dias_periodo_anterior = $model->DIAS_PERIODO_ANTERIOR;
            $total_dias_disponibles = $dias_disponibles_vacaciones + $dias_disponibles_hrs_extra;
            $diasDisponiblesPorAño = $connection->createCommand("CALL SP_VACACIONES_OBTENER_DIAS_DISPONIBLES(".$model->PK_VACACIONES_EMPLEADO.")")->queryAll();
            $diasProximosAnios = $connection->createCommand("CALL SP_VACACIONES_OBTENER_DIAS_PROXIMOS_ANIOS(".$model->PK_VACACIONES_EMPLEADO.")")->queryAll();

            //4.- Se aplican las reglas de negocio las cuales validan los distintos escenarios a tomar dependiendo de la cantidad de dias disponibles y dias solicitados
            if($diasSolicitados <= $dias_periodo_anterior){//Los dias del periodo anterior son suficientes para cubrir los dias solicitados
              $modelBitVacaciones = new TblBitVacacionesEmpleado();
              $modelBitVacaciones->FK_VACACIONES_EMPLEADO = $idVacacionesEmpleado;
              $modelBitVacaciones->FK_TIPO_VACACIONES = 2;
              $modelBitVacaciones->ANIO = date('Y');
              $modelBitVacaciones->APLICA_2X1 = $aplica2x1;
              $modelBitVacaciones->DIAS_DISPONIBLES = 0;
              $modelBitVacaciones->DIAS_PERIODO_ANTERIOR = $dias_periodo_anterior;
              $modelBitVacaciones->DIAS_EJECUTADOS = $diasSolicitados;
              $modelBitVacaciones->DIAS_HORAS_EXTRAS = $diasSolicitados;
              $modelBitVacaciones->COMENTARIOS = $comentarios;
              $modelBitVacaciones->FECHA_REGISTRO = date('Y-m-d H:i:s');
              $modelBitVacaciones->save(false);

              $model->DIAS_PERIODO_ANTERIOR = $model->DIAS_PERIODO_ANTERIOR - $diasSolicitados;
              $model->DIAS_DISPONIBLES = $model->DIAS_DISPONIBLES + $model->DIAS_PERIODO_ANTERIOR - $diasSolicitados;
              $model->FECHA_ACTUALIZACION = date('Y-m-d H:i:s');
              $model->save(false);

              $diasTotalesBitacora = $modelBitVacaciones->DIAS_HORAS_EXTRAS + $modelBitVacaciones->DIAS_DISPONIBLES;

              for ($i=0; $i < $diasTotalesBitacora; $i++) {
                  $relacionFechasBitacora[] = $modelBitVacaciones->PK_BIT_VACACIONES_EMPLEADO;
              }
          }elseif($diasSolicitados <= $dias_disponibles_hrs_extra && $dias_disponibles_hrs_extra != 0){//Los dias por horas extras son suficientes para cubrir los dias solicitados
                $diasPordescontar = $diasSolicitados - $dias_periodo_anterior;
                $modelBitVacaciones = new TblBitVacacionesEmpleado();
                $modelBitVacaciones->FK_VACACIONES_EMPLEADO = $idVacacionesEmpleado;
                $modelBitVacaciones->FK_TIPO_VACACIONES = 2;
                $modelBitVacaciones->ANIO = date('Y');
                $modelBitVacaciones->APLICA_2X1 = $aplica2x1;
                $modelBitVacaciones->DIAS_DISPONIBLES = 0;
                $modelBitVacaciones->DIAS_PERIODO_ANTERIOR = $dias_periodo_anterior;
                $modelBitVacaciones->DIAS_EJECUTADOS = $diasSolicitados;
                $modelBitVacaciones->DIAS_HORAS_EXTRAS = $dias_disponibles_hrs_extra;
                $modelBitVacaciones->COMENTARIOS = $comentarios;
                $modelBitVacaciones->FECHA_REGISTRO = date('Y-m-d H:i:s');
                $modelBitVacaciones->save(false);

                $model->DIAS_HORAS_EXTRAS = $model->DIAS_HORAS_EXTRAS - $diasPordescontar;
                $model->FECHA_ACTUALIZACION = date('Y-m-d H:i:s');
                $model->save(false);

                $diasTotalesBitacora = $modelBitVacaciones->DIAS_HORAS_EXTRAS + $modelBitVacaciones->DIAS_DISPONIBLES;
                for ($i=0; $i < $diasTotalesBitacora; $i++) { 
                    $relacionFechasBitacora[] = $modelBitVacaciones->PK_BIT_VACACIONES_EMPLEADO;
                }
            } elseif($diasSolicitados <= $total_dias_disponibles) {//Los dias totales disponibles del empleado son suficientes para cubrir los dias solicitados
                $cont = 1;
                $diasPordescontar = $diasSolicitados - $dias_disponibles_hrs_extra - $dias_periodo_anterior;
                foreach($diasDisponiblesPorAño as $array){
                    $modelBitVacaciones = new TblBitVacacionesEmpleado();  
                    $modelBitVacaciones->DIAS_HORAS_EXTRAS = $cont==1?$dias_disponibles_hrs_extra:0;                      
                    if($array['DIAS_DISPONIBLES'] >= $diasPordescontar){
                        $modelBitVacaciones->DIAS_DISPONIBLES = $diasPordescontar;
                        $diasPordescontar = 0;
                    } else {
                        $modelBitVacaciones->DIAS_DISPONIBLES = $array['DIAS_DISPONIBLES'];
                        $diasPordescontar = $diasPordescontar - $array['DIAS_DISPONIBLES'];
                    }

                    $modelBitVacaciones->FK_VACACIONES_EMPLEADO = $idVacacionesEmpleado;
                    $modelBitVacaciones->FK_TIPO_VACACIONES = 2;
                    $modelBitVacaciones->ANIO = $array['ANIO'];
                    $modelBitVacaciones->APLICA_2X1 = $aplica2x1;
                    $modelBitVacaciones->DIAS_EJECUTADOS = $diasSolicitados;
                    $modelBitVacaciones->COMENTARIOS = $comentarios;
                    $modelBitVacaciones->FECHA_REGISTRO = date('Y-m-d H:i:s');
                    $modelBitVacaciones->save(false);

                    $diasTotalesBitacora = $modelBitVacaciones->DIAS_HORAS_EXTRAS + $modelBitVacaciones->DIAS_DISPONIBLES;
                    for ($i=0; $i < $diasTotalesBitacora; $i++) { 
                        $relacionFechasBitacora[] = $modelBitVacaciones->PK_BIT_VACACIONES_EMPLEADO;
                    }

                    $cont++;
                    if($diasPordescontar==0){
                        break;
                    }
                }

                $model->DIAS_HORAS_EXTRAS = 0;
                $model->DIAS_DISPONIBLES = $model->DIAS_DISPONIBLES + $dias_disponibles_hrs_extra - $diasSolicitados;
                $model->FECHA_ACTUALIZACION = date('Y-m-d H:i:s');
                $model->save(false);
            } else {//Los dias solicitados por el empleado son mayores a los dias disponibles
                $cont = 1;
                $diasPordescontar = $diasSolicitados - $dias_disponibles_hrs_extra;
                foreach($diasDisponiblesPorAño as $array){
                    if($array['DIAS_DISPONIBLES']<0){//Cuando haya un año que aun no llega y ya se hayan disfrutado dias de el
                        break;
                    }
                    $modelBitVacaciones = new TblBitVacacionesEmpleado();  
                    $modelBitVacaciones->DIAS_HORAS_EXTRAS = $cont==1?$dias_disponibles_hrs_extra:0;                      
                    if($array['DIAS_DISPONIBLES'] >= $diasPordescontar){
                        $modelBitVacaciones->DIAS_DISPONIBLES = $diasPordescontar;
                        $diasPordescontar = 0;
                    } else {
                        $modelBitVacaciones->DIAS_DISPONIBLES = $array['DIAS_DISPONIBLES'];
                        $diasPordescontar = $diasPordescontar - $array['DIAS_DISPONIBLES'];
                    }

                    $modelBitVacaciones->FK_VACACIONES_EMPLEADO = $idVacacionesEmpleado;
                    $modelBitVacaciones->FK_TIPO_VACACIONES = 2;
                    $modelBitVacaciones->ANIO = $array['ANIO'];
                    $modelBitVacaciones->APLICA_2X1 = $aplica2x1;
                    $modelBitVacaciones->DIAS_EJECUTADOS = $diasSolicitados;
                    $modelBitVacaciones->COMENTARIOS = $comentarios;
                    $modelBitVacaciones->FECHA_REGISTRO = date('Y-m-d H:i:s');
                    $modelBitVacaciones->save(false);

                    $diasTotalesBitacora = $modelBitVacaciones->DIAS_HORAS_EXTRAS + $modelBitVacaciones->DIAS_DISPONIBLES;
                    for ($i=0; $i < $diasTotalesBitacora; $i++) { 
                        $relacionFechasBitacora[] = $modelBitVacaciones->PK_BIT_VACACIONES_EMPLEADO;
                    }

                    $cont++;
                    if($diasPordescontar==0){
                        break;
                    }
                }

                foreach($diasProximosAnios as $array) {
                    $nuevosDiasdisponibles = $array['DIAS_VACACIONES'];
                    foreach($diasDisponiblesPorAño as $array2){
                        if($array['ANIO'] == $array2['ANIO'] && $array2['DIAS_DISPONIBLES']<0){
                            $nuevosDiasdisponibles = $nuevosDiasdisponibles + $array2['DIAS_DISPONIBLES'];
                        }
                    }
                    if($nuevosDiasdisponibles != 0){
                        $nuevosDiasPorDisfrutar[] = [
                            'ANIO' => $array['ANIO'],
                            'DIAS_DISPONIBLES' => $nuevosDiasdisponibles
                        ];
                    }   
                }

                foreach($nuevosDiasPorDisfrutar as $array){
                    $modelBitVacaciones = new TblBitVacacionesEmpleado();  
                    $modelBitVacaciones->DIAS_HORAS_EXTRAS = $cont==1?$dias_disponibles_hrs_extra:0;                      
                    if($array['DIAS_DISPONIBLES'] >= $diasPordescontar){
                        $modelBitVacaciones->DIAS_DISPONIBLES = $diasPordescontar;
                        $diasPordescontar = 0;
                    } else {
                        $modelBitVacaciones->DIAS_DISPONIBLES = $array['DIAS_DISPONIBLES'];
                        $diasPordescontar = $diasPordescontar - $array['DIAS_DISPONIBLES'];
                    }

                    $modelBitVacaciones->FK_VACACIONES_EMPLEADO = $idVacacionesEmpleado;
                    $modelBitVacaciones->FK_TIPO_VACACIONES = 2;
                    $modelBitVacaciones->ANIO = $array['ANIO'];
                    $modelBitVacaciones->APLICA_2X1 = $aplica2x1;
                    $diasEjecutados = $data['diasSolicitados'];
                    $modelBitVacaciones->COMENTARIOS = $comentarios;
                    $modelBitVacaciones->FECHA_REGISTRO = date('Y-m-d H:i:s');
                    $modelBitVacaciones->save(false);

                    $diasTotalesBitacora = $modelBitVacaciones->DIAS_HORAS_EXTRAS + $modelBitVacaciones->DIAS_DISPONIBLES;
                    for ($i=0; $i < $diasTotalesBitacora; $i++) { 
                        $relacionFechasBitacora[] = $modelBitVacaciones->PK_BIT_VACACIONES_EMPLEADO;
                    }
                    
                    $cont++;
                    if($diasPordescontar==0){
                        break;
                    }
                }
                $model->DIAS_HORAS_EXTRAS = 0;
                $model->DIAS_DISPONIBLES = $model->DIAS_DISPONIBLES + $dias_disponibles_hrs_extra - $diasSolicitados;
                $model->FECHA_ACTUALIZACION = date('Y-m-d H:i:s');
                $model->save(false);
            }

            //5.- se insertan los dias solicitados por el empleado en la tabla tbl_fechas_vacaciones
            $i=0;

            $strFechasSolicitadas = '';
            $pkAsignacionRel = '';

            foreach($fechasSolicitadas as $key => $value){
                $modelFechasVacaciones = new TblFechasVacaciones();
                $modelFechasVacaciones->FK_BIT_VACACIONES_EMPLEADO = $aplica2x1==0?$relacionFechasBitacora[$i]:$modelBitVacaciones->PK_BIT_VACACIONES_EMPLEADO;
                $modelFechasVacaciones->FECHA_SOLICITADA =  transform_date($value,'Y-m-d');
                $modelFechasVacaciones->FECHA_REGISTRO = date('Y-m-d H:i:s');
                $modelFechasVacaciones->SAVE(false);
                $i++;


                /* INICIO HRIBI 23092016 - Se consulta si el empleado esta registrado en alguna asignación con estatus 1 de Pendiente o 2 de En ejecución o 3 Finalizada 
                                      entonces se realiza un registro en el seguimiento de asignaciones notificando que se esta registrando vacaciones a este empleado,
                                      siempre y cuando la fecha del dia solicitado de vacaciones no caiga en un lapso de tiempo en el que la asignación tenga estatus de DETENIDA.
                    
                    Actualización - El registro de las vacaciones se insertara para las asignaciones con los estatus: En ejecución, Pendiente, Finalizado si las vacaciones quedan en el rango de la asignación.(FECHA_INI_ASIGNACION - FECHA_FIN_ASIGNACION) y si esta en ejecución que no caiga en una fecha donde estuvo DETENIDA la asignación.
                    Para los estatus: Cancelada y Detenida, se validara insertar el registro de vacaciones cuando las vacaciones queden en el rango desde el inicio de asignación hasta la fecha de cancelación o detención que se registre en dicha asignación. (FECHA_INI_ASIGNACION - FECHA_CANCELACION)(FECHA_INI_ASIGNACION - FECHA_DETENCION)
                    Para los estatus: Cerrada, Rechazada y Pendiente Aprobación: No se insertara registro de vacaciones.
                    Para todos los estatus (Excepto Cerrada, Rechazada y Pendiente Aprobación): Validar que las fechas de vacaciones no caiga en una fecha donde estuvo. DETENIDA la asignación. */
                                      
                $sqlFiltroAsig = '';
                $sqlConcatenado = "
                    SELECT * FROM 
                    (SELECT * FROM tbl_asignaciones a 
                    WHERE a.FK_EMPLEADO =".$pkEmpleado."
                    ORDER BY a.PK_ASIGNACION DESC) as tbl_asig_filtro
                    WHERE '".$modelFechasVacaciones->FECHA_SOLICITADA."' BETWEEN tbl_asig_filtro.FECHA_INI AND tbl_asig_filtro.FECHA_FIN";

                $sqlGetEstatusAsignacion = $connection->createCommand($sqlConcatenado)->queryOne();
                //var_dump($sqlGetEstatusAsignacion);
                if(!empty($sqlGetEstatusAsignacion)){
                    if($sqlGetEstatusAsignacion['FK_ESTATUS_ASIGNACION'] > 0 && $sqlGetEstatusAsignacion['FK_ESTATUS_ASIGNACION'] < 4){
                        //var_dump("1,2,3");
                        $sqlFiltroAsig = $connection->createCommand("
                        SELECT * FROM 
                        (SELECT * FROM tbl_asignaciones a 
                        WHERE a.FK_EMPLEADO = ".$pkEmpleado." AND (a.FK_ESTATUS_ASIGNACION = 1 OR a.FK_ESTATUS_ASIGNACION = 2 OR a.FK_ESTATUS_ASIGNACION = 3)
                        ORDER BY a.PK_ASIGNACION DESC) as tbl_asig_filtro
                        WHERE '".$modelFechasVacaciones->FECHA_SOLICITADA."' BETWEEN tbl_asig_filtro.FECHA_INI AND tbl_asig_filtro.FECHA_FIN
                        AND tbl_asig_filtro.PK_ASIGNACION 
                            NOT IN (select ca.FK_ASIGNACION from tbl_bit_comentarios_asignaciones ca 
                            WHERE ca.FK_ASIGNACION = tbl_asig_filtro.PK_ASIGNACION and FK_ESTATUS_ASIGNACION = 6
                            and '".$modelFechasVacaciones->FECHA_SOLICITADA."' BETWEEN ca.FECHA_FIN and ca.FECHA_RETOMADA)")->queryOne();
                    }else if($sqlGetEstatusAsignacion['FK_ESTATUS_ASIGNACION'] == 5 || $sqlGetEstatusAsignacion['FK_ESTATUS_ASIGNACION'] == 6){
                        //var_dump("5,6");
                        //var_dump($modelFechasVacaciones->FECHA_SOLICITADA);
                        $sqlFiltroAsig = $connection->createCommand("
                        SELECT * FROM 
                        (SELECT * FROM tbl_asignaciones a 
                        WHERE a.FK_EMPLEADO = ".$pkEmpleado." AND (a.FK_ESTATUS_ASIGNACION = 5 OR a.FK_ESTATUS_ASIGNACION = 6)
                        ORDER BY a.PK_ASIGNACION DESC) as tbl_asig_filtro
                        WHERE '".$modelFechasVacaciones->FECHA_SOLICITADA."' BETWEEN tbl_asig_filtro.FECHA_INI AND tbl_asig_filtro.FECHA_FIN
                        AND tbl_asig_filtro.PK_ASIGNACION 
                            IN (select ca.FK_ASIGNACION from tbl_bit_comentarios_asignaciones ca 
                            WHERE ca.FK_ASIGNACION = tbl_asig_filtro.PK_ASIGNACION and (FK_ESTATUS_ASIGNACION = 6 or FK_ESTATUS_ASIGNACION = 5)
                            and '".$modelFechasVacaciones->FECHA_SOLICITADA."' BETWEEN tbl_asig_filtro.FECHA_INI and ca.FECHA_FIN)")->queryOne();
                    }
                }

                if(!empty($sqlFiltroAsig) || $sqlFiltroAsig){
                    if($strFechasSolicitadas != ''){
                        $strFechasSolicitadas = $strFechasSolicitadas.', '.transform_date($value,'d/m/Y');
                    }else{
                        $strFechasSolicitadas = transform_date($value,'d/m/Y');
                    }
                    $pkAsignacionRel = $sqlFiltroAsig['PK_ASIGNACION'];
                }
            }

            //6.- Se registra en bitacora la accion realizada, detallando los dias con los que inicio el empleado la transaccion, y los dias finales despues de ejecutarla.
            $descripcionBitacora = 'El recurso tiene los siguientes días de vacaciones: '.$strFechasSolicitadas;
            user_log_bitacora($descripcionBitacora,'Registro de Vacaciones de Empleado',$model->PK_VACACIONES_EMPLEADO);

            $modelAsignacionesSeguimiento = new TblAsignacionesSeguimiento();
            if(!empty($strFechasSolicitadas) || $strFechasSolicitadas && !empty($pkAsignacionRel)){

            }

            //6.- Se registra en bitacora la accion realizada, detallando los dias con los que inicio el empleado la transaccion, y los dias finales despues de ejecutarla.
            /*$descripcionBitacora = 'DIAS DISPONIBLES ANTES DE ACTUALIZACION ='.$dias_disponibles_vacaciones
                                    .', DIAS DISPONIBLES DESPUES DE ACTUALIZACION ='.$model->DIAS_DISPONIBLES
                                    .', DIAS POR HORAS EXTRAS ANTES DE ACTUALIZACION ='.$dias_disponibles_hrs_extra
                                    .', DIAS POR HORAS EXTRAS DESPUES DE ACTUALIZACION ='.$model->DIAS_HORAS_EXTRAS;
            user_log_bitacora($descripcionBitacora,'Registro de Vacaciones de Empleado',$model->PK_VACACIONES_EMPLEADO);*/

            /* INICIO HRIBI 23092016 - Se consulta si el empleado esta registrado en alguna asignación con estatus 1 de Pendiente o 2 de En ejecución
                                  entonces se realiza un registro en el seguimiento de asignaciones notificando que se esta registrando vacaciones a este empleado*/
            
            $sqlFiltroAsig = $connection->createCommand("
                SELECT tbl_asig_filtro.PK_ASIGNACION FROM 
                (SELECT * FROM tbl_asignaciones a 
                WHERE a.FK_EMPLEADO =".$pkEmpleado." AND (a.FK_ESTATUS_ASIGNACION = 1 OR a.FK_ESTATUS_ASIGNACION = 2)
                ORDER BY a.PK_ASIGNACION DESC) as tbl_asig_filtro
                WHERE '".$modelFechasVacaciones->FECHA_SOLICITADA."' BETWEEN tbl_asig_filtro.FECHA_INI AND tbl_asig_filtro.FECHA_FIN")->queryOne();
            
            $modelAsignacionesSeguimiento = new TblAsignacionesSeguimiento();
            if(!empty($sqlFiltroAsig)){

                $modelAsignacionesSeguimiento->COMENTARIOS = $descripcionBitacora;
                $modelAsignacionesSeguimiento->FECHA_REGISTRO = date('Y-m-d H:i:s');
                $modelAsignacionesSeguimiento->FK_ASIGNACION = $pkAsignacionRel;
                $modelAsignacionesSeguimiento->FK_USUARIO = user_info()['PK_USUARIO'];
                $modelAsignacionesSeguimiento->save(false);
            }

            /* FIN HRIBI */
            $connection->close();
            if($data['postAction']=='index'){
                return $this->redirect(['index']);
            } else {
                return $this->redirect(['update', 'id' => $data['PK_VACACIONES_EMPLEADO'],'action'=>'insert']);
            }
        } 
    }

    /**
     * Updates an existing TblVacacionesEmpleado model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $acumularDiasInicial = $model->ACUMULAR_DIAS;
        $diasAnioAnterior = 0;
        $action = '';
        if($model->ACUMULAR_DIAS==0){
            $modelUltimoAnioVacaciones = TblBitVacacionesEmpleado::find()
                ->select([
                    'ANIO',
                    ])
                ->andWhere(['FK_VACACIONES_EMPLEADO'=> $id])
                ->andWhere(['FK_TIPO_VACACIONES'=>1])
                ->orderBy('ANIO DESC')
                ->limit(1)
                ->asArray()
                ->all();
            if(count($modelUltimoAnioVacaciones) > 0){
                $anio = $modelUltimoAnioVacaciones[0]['ANIO'] - 1;
                $modelBitVacacionesAnioAnterior = TblBitVacacionesEmpleado::find()
                    ->select([
                        'DIAS_DISPONIBLES',
                        ])
                    ->andWhere(['FK_VACACIONES_EMPLEADO'=> $id])
                    ->andWhere(['FK_TIPO_VACACIONES'=>3])
                    ->andWhere(['ANIO' => $anio])
                    ->orderBy('ANIO DESC')
                    ->limit(1)
                    ->asArray()
                    ->all();
                
                if(count($modelBitVacacionesAnioAnterior) > 0){
                    $diasAnioAnterior = $modelBitVacacionesAnioAnterior[0]['DIAS_DISPONIBLES'];
                } 
            } else {
                $diasAnioAnterior = 0;
            }                
        }

        if (Yii::$app->request->post()) {
            //1.- Se recogen variables de post
            $data = Yii::$app->request->post();
            $diasDisponibles = $data['diasDisponiblesFinales'];
            $action = 'update';

            //2.- Modificacion a la tabla tbl_vacaciones_empleado y registro de bitacora
            if($model->DIAS_DISPONIBLES != $diasDisponibles || $model->DIAS_HORAS_EXTRAS != $data['TblVacacionesEmpleado']['DIAS_HORAS_EXTRAS'] || $model->ACUMULAR_DIAS != $data['TblVacacionesEmpleado']['ACUMULAR_DIAS'] ){
                $descripcionBitacora = 'PK_BIT_VACACIONES_EMPLEADO='.$id
                                    .',DIAS_DISP_INI='.$model->DIAS_DISPONIBLES
                                    .',DIAS_DISP_FIN='.$diasDisponibles
                                    .',DIAS_HRS_EXS_INI='.$model->DIAS_HORAS_EXTRAS
                                    .',DIAS_HRS_EXS_FIN='.$data['TblVacacionesEmpleado']['DIAS_HORAS_EXTRAS']
                                    .',ACUMULAR_DIAS_INI='.$model->ACUMULAR_DIAS
                                    .',ACUMULAR_DIAS_FIN='.$data['TblVacacionesEmpleado']['ACUMULAR_DIAS'];
                user_log_bitacora($descripcionBitacora,'Update dias Disponibles/Horas_Extra y/o bandera acumular días',$id); 
            }
            $model->DIAS_DISPONIBLES = $diasDisponibles;
            $model->DIAS_HORAS_EXTRAS = $data['TblVacacionesEmpleado']['DIAS_HORAS_EXTRAS'];
            $model->ACUMULAR_DIAS = $data['TblVacacionesEmpleado']['ACUMULAR_DIAS'];
            $model->save(false);
            $borro = 1;
            
            //3.- Eliminacion de dias de vacaciones
            if(isset($data['arrayFechasEliminadas'])){
                $arrayFechasEliminadas = $data['arrayFechasEliminadas'];
                foreach($arrayFechasEliminadas as $index => $array){
                    if($array['APLICA_2X1']==0){
                        $modelFechasVacaciones = TblFechasVacaciones::find()->where(['PK_FECHAS_VACACIONES' => $index])->one();
                        $fechaEliminar= $modelFechasVacaciones->FECHA_SOLICITADA;
                        $fechaPK = $index;
                        $modelFechasVacaciones->delete();
                        $modelBitVacaciones = TblBitVacacionesEmpleado::find()->where(['PK_BIT_VACACIONES_EMPLEADO' => $array['PK_BIT_VACACIONES_EMPLEADO'] ])->one();
                        $modelBitVacaciones->DIAS_DISPONIBLES = $modelBitVacaciones->DIAS_DISPONIBLES - $array['DIAS_DISPONIBLES'];
                        $modelBitVacaciones->DIAS_HORAS_EXTRAS = $modelBitVacaciones->DIAS_HORAS_EXTRAS - $array['DIAS_HORAS_EXTRAS'];
                        $modelBitVacaciones->save(false);
                        $arrayBitEscenario[] = 1;
                    } else {
                        $modelFechasVacaciones = TblFechasVacaciones::find()
                                                ->select(['PK_FECHAS_VACACIONES','FECHA_SOLICITADA'])
                                                ->where(['FK_BIT_VACACIONES_EMPLEADO' => $array['PK_BIT_VACACIONES_EMPLEADO']])
                                                ->asArray()
                                                ->all();
                        $fechaEliminar='';
                        $fechaPK='';
                        foreach($modelFechasVacaciones as $detalleFechaseliminar){
                            $fechaEliminar.=$detalleFechaseliminar['FECHA_SOLICITADA'].',';
                            $fechaPK.=$detalleFechaseliminar['PK_FECHAS_VACACIONES'].',';
                        }
                        $fechaEliminar = trim($fechaEliminar,',');
                        $fechaPK = trim($fechaPK,',');
                        $FK_BIT_VACACIONES_EMPLEADO = $array['PK_BIT_VACACIONES_EMPLEADO'];
                        TblFechasVacaciones::deleteAll("FK_BIT_VACACIONES_EMPLEADO = $FK_BIT_VACACIONES_EMPLEADO");
                        $modelBitVacaciones = TblBitVacacionesEmpleado::find()->where(['PK_BIT_VACACIONES_EMPLEADO' => $array['PK_BIT_VACACIONES_EMPLEADO'] ])->one();
                        $modelBitVacaciones->DIAS_DISPONIBLES = 0;
                        $modelBitVacaciones->DIAS_HORAS_EXTRAS = 0;
                        $modelBitVacaciones->save(false);
                        $arrayBitEscenario[] = 2;
                    }

                    if($modelBitVacaciones->DIAS_DISPONIBLES == 0 && $modelBitVacaciones->DIAS_HORAS_EXTRAS ==0){
                        $modelBitVacaciones->delete();
                    }

                    //3.1.- Se registra en bitacora el dia eliminado.
                    $descripcionBitacora = 'PK_BIT_VACACIONES_EMPLEADO='.$array['PK_BIT_VACACIONES_EMPLEADO']
                                            .',PK_FECHAS_VACACIONES='.$fechaPK
                                            .',DIAS_DISP='.$array['DIAS_DISPONIBLES']
                                            .',DIAS_HRS_EXS='.$array['DIAS_HORAS_EXTRAS']
                                            .',AP_2X1='.$array['APLICA_2X1']
                                            .',FECHAS_ELIMINADAS='.$fechaEliminar;
                    user_log_bitacora($descripcionBitacora,'Eliminacion de dias de vacaciones',$index);
                }
            }  

            //4.- Cancela dias del año anterior en dado caso de que el empleado tenga habilitada la opcion de Acumular dias inicial
            if($data['TblVacacionesEmpleado']['ACUMULAR_DIAS'] == 1 && $acumularDiasInicial==0){
                if(isset($anio)){
                    $anio = $modelUltimoAnioVacaciones[0]['ANIO'] - 1;
                    $modelBitAnioAnteriorCancelado = TblBitVacacionesEmpleado::find()->where(['FK_TIPO_VACACIONES' => 3, 'ANIO' => $anio, 'FK_VACACIONES_EMPLEADO'=> $id])->one();
                    if(count($modelBitAnioAnteriorCancelado) > 0){
                        $modelBitAnioAnteriorCancelado->delete();
                        $borro = 2;
                    }
                }
            }
        }

        $modelBitVacaciones = TblBitVacacionesEmpleado::find()
        ->select([
            'PK_BIT_VACACIONES_EMPLEADO',
            'ANIO',
            'APLICA_2X1',
            'DIAS_DISPONIBLES',
            'DIAS_HORAS_EXTRAS',
            'YEAR(FECHA_REGISTRO) ANIO_FECHA_REGISTRO',
            ])
        ->andWhere(['FK_VACACIONES_EMPLEADO'=> $id])
        ->andWhere(['FK_TIPO_VACACIONES'=>2])
        ->asArray()
        ->all();
        $connection = \Yii::$app->db;
        $informacionGeneralVacaciones = $connection->createCommand("CALL SP_VACACIONES_UPDATE_SELECT($id)")->queryAll();
        $vacacionesSolicitadas = $connection->createCommand("CALL SP_VACACIONES_OBTENER_FECHAS($id)")->queryAll();

        $searchModel = new VacacionesFechasSearch();
        $queryFiltrado = $searchModel->search(\Yii::$app->request->get());
        $dataProvider = $queryFiltrado;

        $nuevoArrayDias=[];
        foreach($modelBitVacaciones as $array){
            $pkBitVacaciones = $array['PK_BIT_VACACIONES_EMPLEADO'];
            $diasVacaciones = $array['DIAS_DISPONIBLES'];
            $diasHrsExtra = $array['DIAS_HORAS_EXTRAS'];
            $APLICA_2X1 = $array['APLICA_2X1'];
            $validadorAnio2x1 = 0;
            foreach($vacacionesSolicitadas as $array2){
                if($array2['PK_BIT_VACACIONES_EMPLEADO']==$pkBitVacaciones){
                    $diasAumentarHrsExtra = 0;
                    $diasAumentarDisponibles = 0;
                    if($diasHrsExtra > 0){
                        $diasHrsExtra--;
                        $diasAumentarHrsExtra = 1;
                    } elseif($diasVacaciones > 0) {
                        $diasVacaciones--;
                        $diasAumentarDisponibles = 1;
                    }
                    //Definición del año en que aplican las vacaciones
                    if($APLICA_2X1==1){
                        if($validadorAnio2x1==0){
                            $anio = $diasAumentarHrsExtra==1?$array['ANIO_FECHA_REGISTRO']:$array['ANIO'];
                            $aplicaDiasDisponibles = $diasAumentarDisponibles;
                            $aplicaDiasHrsExtra = $diasAumentarHrsExtra;
                            $validadorAnio2x1 = 1;
                        }elseif($validadorAnio2x1==1){
                            $validadorAnio2x1 = 0;
                            $diasAumentarDisponibles = $aplicaDiasDisponibles;
                            $diasAumentarHrsExtra = $aplicaDiasHrsExtra;
                        }
                    } else {
                        $anio = $diasAumentarHrsExtra==1?$array['ANIO_FECHA_REGISTRO']:$array['ANIO'];
                    }

                    //Definicion de leyenda
                    if($APLICA_2X1==1){
                        $leyenda = $diasAumentarHrsExtra==1?'2x1 - HRS. EXTRA':'2x1 - VACACIONES';
                    } else {
                        $leyenda = $diasAumentarHrsExtra==1?'HRS. EXTRA':'VACACIONES';
                    }

                    $comentarios = $array2['COMENTARIOS']==''?$leyenda:($leyenda.' - '.$array2['COMENTARIOS']);
                    $nuevoArrayDias[]=[
                        'PK_FECHAS_VACACIONES' => $array2['PK_FECHAS_VACACIONES'],
                        'PK_BIT_VACACIONES_EMPLEADO' => $array2['PK_BIT_VACACIONES_EMPLEADO'],
                        'DIAS_DISPONIBLES' => $diasAumentarDisponibles,
                        'DIAS_HORAS_EXTRAS' => $diasAumentarHrsExtra,
                        'APLICA_2X1' => $APLICA_2X1,
                        'FECHA_SOLICITADA' => $array2['FECHA_SOLICITADA'],
                        'COMENTARIOS' => $comentarios,
                        'ANIO' => $anio,
                    ];
                }
            }
        }
        
        $connection->close();
        return $this->render('update', [
            'model' => $model,
            'informacionGeneralVacaciones' => $informacionGeneralVacaciones,
            'dataProvider'=> $dataProvider,
            'nuevoArrayDias' => $nuevoArrayDias,
            'diasAnioAnterior' => $diasAnioAnterior,
            'modelBitVacaciones' => new TblBitVacacionesEmpleado,
            'action' => $action,
        ]);
    }

    /**
     * Deletes an existing TblVacacionesEmpleado model.
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
     * Finds the TblVacacionesEmpleado model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TblVacacionesEmpleado the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TblVacacionesEmpleado::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionIndex2(){
        $tamanio_pagina=10;
        if (Yii::$app->request->isAjax) {
            $data = Yii::$app->request->post();
            $post=null;
            parse_str($data['data'],$post);
            $nombre =(!empty($post['nombre']))? ("'".trim($post['nombre'])."'"):'null';
            $pagina =(!empty($post['page']))? trim($post['page']):'';

            //Verificar unidad de negocio del empleado
            $unidadNegocioUsuario = user_info()['INFO_EMP']['FK_UNIDAD_NEGOCIO'];
            if((isset(user_info()['IS_ADMIN_UNIDAD_NEGOCIO'])&&!empty(user_info()['IS_ADMIN_UNIDAD_NEGOCIO']))||is_super_admin()){
                $unidadNegocioFiltrar = 0;
            } else {
                $unidadNegocioFiltrar = $unidadNegocioUsuario;
            }

            if(empty($pagina)){
                $pagina=0;
            }else{
                $pagina= $pagina-1;
            }

            $connection = \Yii::$app->db;
            $total_registros = $connection->createCommand("CALL SP_VACACIONES_INDEX_SELECT($nombre,$unidadNegocioFiltrar,null,null)")->queryAll();
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
                $connection = \Yii::$app->db;
                $datosIncidencias = $connection->createCommand("CALL SP_VACACIONES_INDEX_SELECT($nombre,$unidadNegocioFiltrar,$limit,$offset)")->queryAll();
            }

            $connection->close();
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $res = array(
                'pagina'        => $pagina+1,
                'data'          => $datosIncidencias,
                'total_paginas' => $total_paginas,
                'total_registros' => $total_registros,
                'unidadNegocioFiltrar' => $unidadNegocioFiltrar,
                'query' => "CALL SP_VACACIONES_INDEX_SELECT($nombre,$unidadNegocioFiltrar,$limit,$offset)",
            );
            //print_r($res);
            return $res;
        }
        else{
             return $this->render('index', [
                'total_paginas' => 0,
            ]);
            
        }
    }

    public function actionEjecutar_diario()
    {
        $connection = \Yii::$app->db;
        $connection->createCommand("CALL SP_VACACIONES_APLICACION_DIAS()")->execute();
        echo "Se ejecutó SP_VACACIONES_APLICACION_DIAS() ";
        $connection->createCommand("CALL SP_VACACIONES_VENCIMIENTO_DIAS()")->execute();
        echo "Se ejecutó SP_VACACIONES_VENCIMIENTO_DIAS() ";

        $connection->close();
    }
}
