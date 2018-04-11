<?php

namespace app\controllers;

use Yii;
use app\models\TblBonoPuntualidad;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use yii\db\Command;
use yii\db\Expression;
use yii\db\Query;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * BonoPuntualidadController implements the CRUD actions for TblBonoPuntualidad model.
 */

/*********************************************************************************************************
    Control de cambios
**********************************************************************************************************
    Autor: Saul Castillo Montes
    Fecha: 07/06/2016
    Descripción: Se crea el componente
**********************************************************************************************************
    Autor: Saul Castillo Montes
    Fecha: 13/06/2016
    Descripción: Se agrega el apartado de Control de cambios
*********************************************************************************************************/
class BonoPuntualidadController extends Controller
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
     * Lists all TblBonoPuntualidad models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => TblBonoPuntualidad::find(),
        ]);

        $fechas = (new Query)
             ->select([
                'DISTINCT(MONTH(FECHA_REGISTRO) - 1) as mes',
                'YEAR(FECHA_REGISTRO) as anio'])
             ->from('tbl_bono_puntualidad')
             ->all();

        $meses_fecha=[];
        foreach ($fechas as $key => $value) {
            if($value['mes']<10){
                $value['mes']= '0'.$value['mes'];
            }
            $meses_fecha[]= ['mes_fecha'=>obtener_nombre_mes($value['mes']).' '.$value['anio'], 'value'=>'01/'.$value['mes'].'/'.$value['anio']];
        }

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'meses_fecha' => $meses_fecha,
        ]);
    }

    /**
     * Displays a single TblBonoPuntualidad model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new TblBonoPuntualidad model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */

    public function actionCreate()
    {
        $validador = 0;
        $meses = [
            0 => 'Diciembre',
            1 => 'Enero',
            2 => 'Febrero',
            3 => 'Marzo',
            4 => 'Abril',
            5 => 'Mayo',
            6 => 'Junio',
            7 => 'Julio',
            8 => 'Agosto',
            9 => 'Septiembre',
            10 => 'Octubre',
            11 => 'Noviembre',
        ];
        $mesActual = date('n') - 1;
        $anioActual = date('Y');
        if($mesActual==0){
            $anioBono = $anioActual -1;
            $mesBono = 12;
        } else {
            $anioBono = $anioActual;
            $mesBono = $mesActual;
        }
        $mes = $meses[$mesActual];
        //Verificar unidad de negocio del empleado
        if((isset(user_info()['IS_ADMIN_UNIDAD_NEGOCIO'])&&!empty(user_info()['IS_ADMIN_UNIDAD_NEGOCIO']))||is_super_admin()){
            $banderaUnidadNegocio = true;
        } else {
            $banderaUnidadNegocio = false;
        }
        if (Yii::$app->request->post()) {
            $data = Yii::$app->request->post();
            $validador = 1;
            $fechaActual = date('Y-m-d H:i:s');
            $PK_USUARIO = user_info()['PK_USUARIO'];
            foreach($data['hid_chk'] as $idEmpleado){
                $comentarios = isset($data['hid_com_'.$idEmpleado])?$data['hid_com_'.$idEmpleado]:null;
                $model = new TblBonoPuntualidad();
                $model->FK_EMPLEADO = $idEmpleado;
                $model->FK_ADMINISTRADORA = $data['hid_adm_'.$idEmpleado];
                $model->FK_USUARIO = $PK_USUARIO;
                $model->COMENTARIOS = $comentarios;
                $model->FECHA_REGISTRO = $fechaActual;
                $model->save(false);

                //Inserta bitacora
                $descripcionBitacora = 'PK_BONO_PUNTUALIDAD='.$model->PK_BONO_PUNTUALIDAD
                                        .',FK_EMPLEADO='.$model->FK_EMPLEADO
                                        .',MES_BONO='.$mesBono
                                        .',ANIO_BONO='.$anioBono;
                user_log_bitacora($descripcionBitacora,'Registro de Bono de puntualidad',$model->PK_BONO_PUNTUALIDAD);
            }
            return $this->redirect(['create', 'validador' => $validador]);
            /*return $this->render('_form', [
                'model' => $model,
                'mes' => $mes,
                'validador' => $validador,
            ]);*/
        } else {
            $model = new TblBonoPuntualidad();
            return $this->render('_form', [
                'model' => $model,
                'mes' => $mes,
                'validador' => $validador,
                'banderaUnidadNegocio' => $banderaUnidadNegocio
            ]);
        }
    }

    /**
     * Updates an existing TblBonoPuntualidad model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->PK_BONO_PUNTUALIDAD]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing TblBonoPuntualidad model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete()
    {
        $data = Yii::$app->request->post();
        foreach($data['chkBono'] as $idBono){
            $model = $this->findModel($idBono);
            $fechaRegistroUNIX = strtotime($model->FECHA_REGISTRO);
            $mesBono = (date('n',$fechaRegistroUNIX)-1)==0?'12':(date('n',$fechaRegistroUNIX)-1);
            $anioBono = (date('n',$fechaRegistroUNIX)-1)==0?(date('Y',$fechaRegistroUNIX)-1):date('Y',$fechaRegistroUNIX);
            //Inserta bitacora
            $descripcionBitacora = 'PK_BONO_PUNTUALIDAD='.$model->PK_BONO_PUNTUALIDAD
                                    .',FK_EMPLEADO='.$model->FK_EMPLEADO
                                    .',MES_BONO='.$mesBono
                                    .',ANIO_BONO='.$anioBono;
            user_log_bitacora($descripcionBitacora,'Eliminacion de Bono de puntualidad',$model->PK_BONO_PUNTUALIDAD);
            $model->delete();
        }
        return $this->redirect(['index']);
    }

    /*obtiene mediante una llamada en ajax, todos los empleados a los cuales no se les ha dado de alta el bono de puntualidad*/
    public function actionIndex2(){
        $request = Yii::$app->request;
        $tamanio_pagina= 10;    

        if (Yii::$app->request->isAjax) {
            $data = Yii::$app->request->post();
            $post=null;
            parse_str($data['data'],$post);

            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            //Se recogen parametros de la vista
            $nombre      =(!empty($post['nombre']))? ("'".trim($post['nombre'])."'"):'null';
            $idAdministradora  =(!empty($post['idAdministradora']))? trim($post['idAdministradora']):'null';
            $idUnidadNegocio =(!empty($post['idUnidadNegocio']))? trim($post['idUnidadNegocio']):'null';
            $pagina         =(!empty($post['page']))? trim($post['page']):'';

            //Se determina el numero de pagina
            if(empty($pagina)){
                $pagina=0;
            }else{
                $pagina= $pagina-1;
            }

            //Verificar unidad de negocio del empleado
            $unidadNegocioUsuario = user_info()['INFO_EMP']['FK_UNIDAD_NEGOCIO'];
            if((isset(user_info()['IS_ADMIN_UNIDAD_NEGOCIO'])&&!empty(user_info()['IS_ADMIN_UNIDAD_NEGOCIO']))||is_super_admin()){
                $unidadNegocioFiltrar = (!empty($post['idUnidadNegocio']))? $idUnidadNegocio:0;
            } else {
                $unidadNegocioFiltrar = $unidadNegocioUsuario;
            }

            $connection = \Yii::$app->db;
            $total_registros = $connection->createCommand("CALL SP_BONO_PUNTUALIDAD_CREATE_SELECT($nombre,$idAdministradora,$unidadNegocioFiltrar)")->queryAll();
            $total_registros = count($total_registros);
            $total_paginas= ceil($total_registros/$tamanio_pagina);
            if($total_registros<=$tamanio_pagina){
                $pagina=0;
            }
            //$limit = $tamanio_pagina;
            //$offset = $pagina*$tamanio_pagina;
            $html='';
            $datosEmpleadosSinBono = [];
            if($total_registros > 0){
                $datosEmpleadosSinBono = $connection->createCommand("CALL SP_BONO_PUNTUALIDAD_CREATE_SELECT($nombre,$idAdministradora,$unidadNegocioFiltrar)")->queryAll();
                $bgColor1 = '#FFFFFF';
                $bgColor2 = '#F5F5F5';
                $contadorEmpleadosSinBono = 0;
                foreach($datosEmpleadosSinBono as $array){
                    $contadorEmpleadosSinBono ++;
                    if($contadorEmpleadosSinBono%2==0){
                        $bgColor = $bgColor2;
                    } else {
                        $bgColor = $bgColor1;
                    }
                    $nombreEmp = $array['APELLIDO_PAT_EMP'].' '.$array['APELLIDO_MAT_EMP'].' '.$array['NOMBRE_EMP'];
                    $html.= '<tr>';
                        $html.= '<td width="35%" style="background-color:'.$bgColor.'">'.$nombreEmp.'</td>';
                        $html.= '<td width="30%" style="background-color:'.$bgColor.'"><input type="hidden" id="admin_'.$array['PK_EMPLEADO'].'" value="'.$array['PK_ADMINISTRADORA'].'">'.$array['NOMBRE_ADMINISTRADORA'].'</td>';
                        $html.= '<td width="10%" style="background-color:'.$bgColor.';text-align: center;"><input type="checkbox" class="checkboxBono" style="checkboxBono border-top chk" id="chk_'.$array['PK_EMPLEADO'].'" name="chk_'.$array['PK_EMPLEADO'].'" value="'.$array['PK_EMPLEADO'].'"></td>';
                        $html.= '<td width="25%" style="background-color:'.$bgColor.';text-align: center;"><input type="text" class="textBono" id="textBono_'.$array['PK_EMPLEADO'].'" name="textBono_'.$array['PK_EMPLEADO'].'" maxlength="20" disabled="disabled"></td>';
                    $html.= '</tr>';
                }
            } else {
                $html.="<tr>";
                    $html.='<td colspan="7" rowspan="3" style="text-align: center;font-size: large; font-weight: bold; text-decoration: underline; width: 100%; height: 100px;"></br> NO SE ENCONTRARON DATOS CON LOS PARAMETROS ESPECIFICADOS </br></br></td>';
                $html.="</tr>";
            }

            $connection->close();
            return [
                'data'=>$html,
                'total_registros'=>$total_registros,
                'total_paginas'=>$total_paginas,
                'pagina'=>$pagina,
                'datosEmpleadosSinBono'=>$datosEmpleadosSinBono,
                'query'=>"CALL SP_BONO_PUNTUALIDAD_CREATE_SELECT($nombre,$idAdministradora,$unidadNegocioFiltrar)",
            ];
        }
    }

    /*obtiene mediante una llamada en ajax, todos los empleados con bono de puntualidad*/
    public function actionIndex3(){
        $request = Yii::$app->request;
        $tamanio_pagina= 10;    
        $meses = [
            0 => 'Diciembre',
            1 => 'Enero',
            2 => 'Febrero',
            3 => 'Marzo',
            4 => 'Abril',
            5 => 'Mayo',
            6 => 'Junio',
            7 => 'Julio',
            8 => 'Agosto',
            9 => 'Septiembre',
            10 => 'Octubre',
            11 => 'Noviembre',
        ];

        if (Yii::$app->request->isAjax) {
            $data = Yii::$app->request->post();
            $post=null;
            parse_str($data['data'],$post);

            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            //Se reciben parametros
            $nombre      =(!empty($post['nombre']))? ("'".trim($post['nombre'])."'"):'null';
            $idAdministradora  =(!empty($post['idAdministradora']))? trim($post['idAdministradora']):'null';
            if( !empty($post['mesFecha']) ){
                $mesFecha  = "'".transform_date(trim($post['mesFecha']))."'";
            } else {
                $mesFecha = 'null';
            }
            $pagina =(!empty($post['page']))? trim($post['page']):'';
            
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
            $total_registros = $connection->createCommand("CALL SP_BONO_PUNTUALIDAD_INDEX_SELECT($nombre,$idAdministradora,$mesFecha,$unidadNegocioFiltrar,null,null)")->queryAll();
            $total_registros = count($total_registros);
            $total_paginas= ceil($total_registros/$tamanio_pagina);
            if($total_registros<=$tamanio_pagina){
                $pagina=0;
            }
            $limit = $tamanio_pagina;
            $offset = $pagina*$tamanio_pagina;
            $html='';
            $datosEmpleadosConBono = [];
            if($total_registros > 0){
                $datosEmpleadosConBono = $connection->createCommand("CALL SP_BONO_PUNTUALIDAD_INDEX_SELECT($nombre,$idAdministradora,$mesFecha,$unidadNegocioFiltrar,$limit,$offset)")->queryAll();
                $bgColor1 = '#FFFFFF';
                $bgColor2 = '#F5F5F5';
                $contadorEmpleadosConBono = 0;
                foreach($datosEmpleadosConBono as $array){
                    $contadorEmpleadosConBono ++;
                    if($contadorEmpleadosConBono%2==0){
                        $bgColor = $bgColor2;
                    } else {
                        $bgColor = $bgColor1;
                    }
                    $nombreEmp = $array['APELLIDO_PAT_EMP'].' '.$array['APELLIDO_MAT_EMP'].' '.$array['NOMBRE_EMP'];
                    $html.= '<tr>';
                        $html.= '<td width="5%" style="background-color:'.$bgColor.'"><input type="checkbox" class="chk_quitar_bono" name="chkBono[]" value="'.$array['PK_BONO_PUNTUALIDAD'].'"></td>';
                        $html.= '<td width="30%" style="background-color:'.$bgColor.'">'.$nombreEmp.'</td>';
                        $html.= '<td width="15%" style="background-color:'.$bgColor.'">'.$array['NOMBRE_ADMINISTRADORA'].'</td>';
                        $html.= '<td width="25%" style="background-color:'.$bgColor.'"><input type="text" class="textBono" id="textBono_'.$array['PK_BONO_PUNTUALIDAD'].'" name="textBono_'.$array['PK_BONO_PUNTUALIDAD'].'" maxlength="20" disabled="disabled" value="'.$array['COMENTARIOS'].'"></td>';
                        $html.= '<td width="15%" style="background-color:'.$bgColor.'">'.$meses[$array['MES']].' '.$array['ANIO'].'</td>';
                        $html.= '<td width="10%" style="background-color:'.$bgColor.'"><label class="toggle"><input type="checkbox" data-pk="'.$array['PK_BONO_PUNTUALIDAD'].'" data-accion="habilitar" class="checkbox habilitar_comentario" /><span class="toggle-text"></span><span class="toggle-handle"></span></label></td>';
                    $html.= '</tr>';
                }
            } else {
                $html.="<tr>";
                    $html.='<td colspan="7" rowspan="3" style="text-align: center;font-size: large; font-weight: bold; text-decoration: underline; width: 100%; height: 100px;"></br> NO SE ENCONTRARON DATOS CON LOS PARAMETROS ESPECIFICADOS </br></br></td>';
                $html.="</tr>";
            }
            $connection->close();
            return [
                'data'=>$html,
                'total_registros'=>$total_registros,
                'total_paginas'=>$total_paginas,
                'pagina'=>$pagina,
                'query' => "CALL SP_BONO_PUNTUALIDAD_INDEX_SELECT($nombre,$idAdministradora,$mesFecha,null,null)",
            ];
        }
    }

    /**
     * Finds the TblBonoPuntualidad model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return TblBonoPuntualidad the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TblBonoPuntualidad::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionGuardar_comentario(){
        if (Yii::$app->request->isAjax) {
            $data = Yii::$app->request->post();
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $comentario = (isset($data['comentario'])&&!empty($data['comentario']))?$data['comentario']:null;
            $model = TblBonoPuntualidad::find()->where(['PK_BONO_PUNTUALIDAD'=>$data['pk']])->one();
            $comentarioAnterior = $model->COMENTARIOS;
            $model->COMENTARIOS = $comentario;
            $model->save(false);
            //Inserta bitacora
            $descripcionBitacora = 'PK_BONO_PUNTUALIDAD='.$model->PK_BONO_PUNTUALIDAD
                                    .',COMENTARIO_ANTERIOR='.$comentarioAnterior
                                    .',COMENTARIO_NUEVO='.$comentario;
            user_log_bitacora($descripcionBitacora,'Modificación de Comentario',$model->PK_BONO_PUNTUALIDAD);
            $res = [
                'data'=>$data,
            ];

            return $res;
        }
    }
}
