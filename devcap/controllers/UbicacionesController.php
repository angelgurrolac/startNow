<?php

namespace app\controllers;

use Yii;
use app\models\TblCatUbicaciones;
use app\models\TblDomicilios;
use app\models\tblcatmunicipios;
use app\models\tblcatestados;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

use yii\db\Query;
use yii\db\Expression;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * UbicacionesController implements the CRUD actions for TblCatUbicaciones model.
 */
class UbicacionesController extends Controller
{
    // public function behaviors()
    // {
    //     return [
    //       'access' => [
    //       'class' => \yii\filters\AccessControl::className(),
    //       'only' => ['index', 'view','create', 'update', 'delete'],
    //       'rules' => [
    //         [
    //           'actions' => ['index', 'view',],
    //           'allow' => true,
    //           'roles' => ['@'],
    //           //'matchCallback' => function ($rule, $action) {
    //           // return PermissionHelpers::requireMinimumRole('Admin') && PermissionHelpers::requireStatus('Active');
    //           //}
    //         ],
    //         [
    //           'actions' => [ 'create', 'update', 'delete'],
    //           'allow' => true,
    //           'roles' => ['@'],
    //           //'matchCallback' => function ($rule, $action) {
    //           //return PermissionHelpers::requireMinimumRole('SuperUser') && PermissionHelpers::requireStatus('Active');
    //          // }
    //         ],
    //       ],
    //     ],
    //         'verbs' => [
    //             'class' => VerbFilter::className(),
    //             'actions' => [
    //                 'delete' => ['post'],
    //             ],
    //         ],
    //     ];
    // }
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
     * Lists all TblCatUbicaciones models.
     * @return mixed
     */
    public function actionIndex()
    {
        $tamanio_pagina=9;
        if (Yii::$app->request->isAjax) {
            $data = Yii::$app->request->post();
            $post=null;
            parse_str($data['data'],$post);
            $nombre  =(!empty($post['nombre']))? trim($post['nombre']):'';
            $cliente =(!empty($post['FK_CLIENTE']))? trim($post['FK_CLIENTE']):'';
            $pagina  =(!empty($data['pagina']))? trim($data['pagina']):'';

            $total_elementos= (new \yii\db\Query())
                            ->select(["ub.DESC_UBICACION as campo1",])
                            ->from('tbl_cat_ubicaciones as ub')
                            ->join('LEFT JOIN','tbl_domicilios d',
                                'ub.FK_DOMICILIO = d.PK_DOMICILIO')
                            ->join('LEFT JOIN','tbl_cat_estados e',
                                'e.PK_ESTADO = d.FK_ESTADO')
                            ->join('LEFT JOIN','tbl_cat_municipios as m',
                                'm.PK_MUNICIPIO = d.FK_MUNICIPIO AND m.FK_ESTADO=d.FK_ESTADO')
                            ->andFilterWhere(['=', 'ub.FK_CLIENTE', $cliente])
                            ->andFilterWhere(
                                ['or',
                                    ['LIKE', 'ub.DESC_UBICACION', $nombre],
                                    ['LIKE', 'e.DESC_ESTADO', $nombre],
                                    ['LIKE', 'm.DESC_MUNICIPIO', $nombre],
                                ])
                        ->count();

            if($total_elementos<$tamanio_pagina){
                $pagina=1;
            }
         
            $dataProvider = new ActiveDataProvider([
                'query' => (new \yii\db\Query())
                            ->select(['ub.PK_UBICACION as id',
                                "ub.DESC_UBICACION as campo5",'e.DESC_ESTADO as campo2',
                                "m.DESC_MUNICIPIO as campo1"])
                            ->from('tbl_cat_ubicaciones ub')
                            ->join('LEFT JOIN','tbl_domicilios d',
                                'ub.FK_DOMICILIO = d.PK_DOMICILIO')
                            ->join('LEFT JOIN','tbl_cat_estados e',
                                'e.PK_ESTADO = d.FK_ESTADO')
                            ->join('LEFT JOIN','tbl_cat_municipios as m',
                                'm.PK_MUNICIPIO = d.FK_MUNICIPIO AND m.FK_ESTADO=d.FK_ESTADO')
                            ->andFilterWhere(['=', 'ub.FK_CLIENTE', $cliente])
                            ->andFilterWhere(
                                ['or',
                                    ['LIKE', 'ub.DESC_UBICACION', $nombre],
                                    ['LIKE', 'e.DESC_ESTADO', $nombre],
                                    ['LIKE', 'm.DESC_MUNICIPIO', $nombre],
                                ])
                            ->orderBy('campo1 asc')
                        ,
                'pagination' => [
                    'pageSize' => $tamanio_pagina,
                    'page' => $pagina-1,
                ],
            ]);

            /*$resultado=$dataProvider->getModels();
            foreach ($resultado as $key => $value) {
                $domicilio = tbldomicilios::findOne($resultado[$key]->FK_DOMICILIO);
                $municipio = tblcatmunicipios::findOne($domicilio->FK_MUNICIPIO);
                $estado = tblcatestados::findOne($domicilio->FK_ESTADO);
                $pais = tblcatpaises::findOne($domicilio->FK_PAIS);
                $domicilioStr = 'C. '.$domicilio->CALLE.' '.$domicilio->NUM_INTERIOR.' '.
                    'COL. '.$domicilio->COLONIA.' '.$domicilio->CP.' '.$municipio->DESC_MUNICIPIO.', '.
                    $estado->DESC_ESTADO.'.';

                $giros = tblcatgiro::find()->where(['PK_GIRO' => $resultado[$key]->FK_GIRO])->limit(1)->one();
                if($giros){
                    $idgiro = $giros->NOMBRE_GIRO;
                }else{
                    $idgiro = 'Sin Definir';
                }
                $resultado[$key]->FK_DOMICILIO=$domicilioStr;
                $resultado[$key]->FK_GIRO=$idgiro;
            }*/

            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $res = array(
                'cliente'        => $cliente,
                'pagina'        => $pagina,
                'data'          => $dataProvider->getModels(),
                'total_paginas' => ceil($dataProvider->getPagination()->totalCount / $tamanio_pagina),
            );
     
            return $res;    
        }else{
            $dataProvider = new ActiveDataProvider([
                'query' => (new \yii\db\Query())
                            ->select(["tbl_cat_ubicaciones.DESC_UBICACION as campo1", ])
                            ->from('tbl_cat_ubicaciones')
                            ->join('LEFT JOIN','tbl_domicilios',
                                'tbl_cat_ubicaciones.FK_DOMICILIO = tbl_domicilios.PK_DOMICILIO')
                            ->andFilterWhere(['=', 'tbl_cat_ubicaciones.FK_CLIENTE', ''])
                            ->andFilterWhere(
                                ['or',
                                    ['LIKE', 'tbl_cat_ubicaciones.DESC_UBICACION', ''],
                                ]),
                'pagination' => [
                    'pageSize' => $tamanio_pagina,
                    'page' => 0,
                ],
            ]);
            
            return $this->render('index', [
                'data' => $dataProvider->getModels(),
                'total_paginas' => ceil($dataProvider->getPagination()->totalCount / $tamanio_pagina),
                'dataProvider' => $dataProvider,
            ]);
        }
    }

    /**
     * Displays a single TblCatUbicaciones model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => (new \yii\db\Query())
                            ->select(["*",])
                            ->from('tbl_cat_ubicaciones')
                            ->join('LEFT JOIN','tbl_domicilios',
                                'tbl_cat_ubicaciones.FK_DOMICILIO = tbl_domicilios.PK_DOMICILIO')
                            ->join('LEFT JOIN','tbl_clientes',
                                'tbl_cat_ubicaciones.FK_CLIENTE = tbl_clientes.PK_CLIENTE')
                            ->join('LEFT JOIN','tbl_cat_estados',
                                'tbl_cat_estados.PK_ESTADO = tbl_domicilios.FK_ESTADO')
                            ->join('LEFT JOIN','tbl_cat_municipios',
                                'tbl_cat_municipios.PK_MUNICIPIO = tbl_domicilios.FK_MUNICIPIO')
                            ->join('LEFT JOIN','tbl_cat_paises',
                                'tbl_cat_paises.PK_PAIS = tbl_domicilios.FK_PAIS')
                            ->andFilterWhere(['=', 'tbl_cat_ubicaciones.PK_UBICACION', $id])->limit(1)->one(),
        ]);
    }
    public function actionModal(){
        
            $data = Yii::$app->request->post();
            $model= (new \yii\db\Query())
                            ->select(["*",])
                            ->from('tbl_cat_ubicaciones')
                            ->join('LEFT JOIN','tbl_domicilios',
                                'tbl_cat_ubicaciones.FK_DOMICILIO = tbl_domicilios.PK_DOMICILIO')
                            ->join('LEFT JOIN','tbl_clientes',
                                'tbl_cat_ubicaciones.FK_CLIENTE = tbl_clientes.PK_CLIENTE')
                            ->join('LEFT JOIN','tbl_cat_estados',
                                'tbl_cat_estados.PK_ESTADO = tbl_domicilios.FK_ESTADO')
                            ->join('LEFT JOIN','tbl_cat_municipios',
                                'tbl_cat_municipios.PK_MUNICIPIO = tbl_domicilios.FK_MUNICIPIO and tbl_cat_municipios.FK_ESTADO=tbl_domicilios.FK_ESTADO')
                            ->join('LEFT JOIN','tbl_cat_paises',
                                'tbl_cat_paises.PK_PAIS = tbl_domicilios.FK_PAIS')
                            ->andFilterWhere(['=', 'tbl_cat_ubicaciones.PK_UBICACION', $data['id']])->limit(1)->one();

            \Yii::$app->response->format = \yii\web\Response::FORMAT_HTML;
            ?>
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h3 class="modal-title" class="campos-title font-bold"> 
                    <a href="<?php echo Url::to(["update",'PK_UBICACION'=>$model["PK_UBICACION"],'FK_CLIENTE'=>$model['FK_CLIENTE']]); ?>" title="Ver" class="item-update iconview-24x24"></a>
                    Datos de ubicaci&oacute;n  
                </h3>
            </div>
            <div class="modal-body">
               
                <div class="row">
                    
                    <div class="form-group col-lg-6 col-md-4 col-sm-4">
                        <?= Html::label('Cliente al que pertenece',null,['class'=>'campos-label']) ?>
                        <p class="font-bold"><?php echo $model['NOMBRE_CLIENTE'] ?></p>
                    </div>
                    <div class="form-group col-lg-6 col-md-4 col-sm-4">
                        <?= Html::label('Colonia',null,['class'=>'campos-label']) ?>
                        <p class="font-bold"><?php echo $model['COLONIA'] ?></p>
                    </div>
                    <div class="form-group col-lg-6 col-md-4 col-sm-4">
                        <?= Html::label('Ubicaci&oacute;n',null,['class'=>'campos-label']) ?>
                        <p class="font-bold"><?php echo $model['DESC_UBICACION'] ?></p>
                    </div>
                    <div class="form-group col-lg-6 col-md-4 col-sm-4">
                        <?= Html::label('Ciudad/municipio',null,['class'=>'campos-label']) ?>
                        <p class="font-bold"><?php echo $model['DESC_MUNICIPIO'] ?></p>
                    </div>
                    <div class="form-group col-lg-6 col-md-4 col-sm-4">
                        <?= Html::label('Calle',null,['class'=>'campos-label']) ?>
                        <p class="font-bold"><?php echo $model['CALLE'] ?></p>
                    </div>
                    <div class="form-group col-lg-6 col-md-4 col-sm-4">
                        <?= Html::label('Estado',null,['class'=>'campos-label']) ?>
                        <p class="font-bold"><?php echo $model['DESC_ESTADO'] ?></p>
                    </div>
                    <div class="form-group col-lg-6 col-md-4 col-sm-4">
                        <?= Html::label('N&uacute;mero interior',null,['class'=>'campos-label']) ?>
                        <p class="font-bold"><?php echo $model['NUM_INTERIOR'] ?></p>
                    </div>
                    <div class="form-group col-lg-6 col-md-4 col-sm-4">
                        <?= Html::label('N&uacute;mero exterior',null,['class'=>'campos-label']) ?>
                        <p class="font-bold"><?php echo $model['NUM_EXTERIOR'] ?></p>
                    </div>
                    <div class="form-group col-lg-6 col-md-4 col-sm-4">
                        <?= Html::label('C&oacute;digo postal',null,['class'=>'campos-label']) ?>
                        <p class="font-bold"><?php echo $model['CP'] ?></p>
                    </div>
                    <div class="form-group col-lg-6 col-md-4 col-sm-4">
                        <?= Html::label('Pa&iacute;s',null,['class'=>'campos-label']) ?>
                        <p class="font-bold"><?php echo $model['DESC_PAIS'] ?></p>
                    </div>
                    <div class="form-group col-lg-6 col-md-4 col-sm-4">
                        <?= Html::label('Piso',null,['class'=>'campos-label']) ?>
                        <p class="font-bold"><?php echo $model['PISO'] ?></p>
                    </div>
                    <div class="form-group col-lg-12 col-md-4 col-sm-4">
                        <?= Html::label('Requisitos o documentos para ingresar al edificio',null,['class'=>'campos-label']) ?>
                        <p class="font-bold"><?php echo $model['DOCS_INGRESO'] ?></p>
                    </div>

                    
                    <div class="clear"></div>
                </div>
            </div>
            <?php 
        
            // $model= TblCatContactos::findOne('1');
            // var_dump( $model);
    }

    /**
     * Creates a new TblCatUbicaciones model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $modelUbicacion = new TblCatUbicaciones();
        $modelDomicilio = new TblDomicilios();

        if ($modelUbicacion->load(Yii::$app->request->post()) && $modelDomicilio->load(Yii::$app->request->post())) {
            $modelDomicilio->save(false);
            $modelUbicacion->FK_DOMICILIO= $modelDomicilio->PK_DOMICILIO;
            $modelUbicacion->FECHA_REGISTRO=date('Y-m-d H:i:s');
            $modelUbicacion->save(false);

            $descripcionBitacora = 'FK_CLIENTE='.$modelUbicacion->FK_CLIENTE.',DESC_UBICACION='.$modelUbicacion->DESC_UBICACION.',FK_DOMICILIO='.$modelUbicacion->FK_DOMICILIO;
            user_log_bitacora($descripcionBitacora,'Registrar Ubicación de Cliente',$modelUbicacion->PK_UBICACION );     

            $estados  = tblcatestados::findOne($modelDomicilio->FK_ESTADO);
            $municipios = tblcatmunicipios::find()->where(['PK_MUNICIPIO'=>$modelDomicilio->FK_MUNICIPIO,'FK_ESTADO'=>$modelDomicilio->FK_ESTADO,'FK_PAIS'=>$modelDomicilio->FK_PAIS])->limit(1)->one();
            $extra['DESC_ESTADO']= $estados->DESC_ESTADO;
            $extra['DESC_MUNICIPIO']= $municipios->DESC_MUNICIPIO;
            // var_dump($modelUbicacion);
            // return $this->redirect(['view', 'id' => $modelUbicacion->PK_UBICACION]);
            return $this->render('update', [
                'modelUbicacion' => $modelUbicacion,
                'modelDomicilio' => $modelDomicilio,
                'extra' => $extra,
                'mensaje' => 'Los datos se han guardado correctamente',
                'action' => 'insert',
            ]);
        } else {
            return $this->render('create', [
                'modelUbicacion' => $modelUbicacion,
                'modelDomicilio' => $modelDomicilio,
            ]);
        }
    }

    /**
     * Updates an existing TblCatUbicaciones model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($PK_UBICACION)
    {
        $modelUbicacion = $this->findModel($PK_UBICACION);
        $modelDomicilio = tbldomicilios::findOne($modelUbicacion->FK_DOMICILIO);

        $estados  = tblcatestados::findOne($modelDomicilio->FK_ESTADO);
        $municipios = tblcatmunicipios::find()->where(['PK_MUNICIPIO'=>$modelDomicilio->FK_MUNICIPIO,'FK_ESTADO'=>$modelDomicilio->FK_ESTADO,'FK_PAIS'=>$modelDomicilio->FK_PAIS])->limit(1)->one();
        $extra['DESC_ESTADO']= $estados->DESC_ESTADO;
        $extra['DESC_MUNICIPIO']= $municipios->DESC_MUNICIPIO;

        if ($modelUbicacion->load(Yii::$app->request->post()) && $modelDomicilio->load(Yii::$app->request->post())) {
            $modelDomicilio->save(false);
            $modelUbicacion->save(false);

            $descripcionBitacora = 'FK_CLIENTE='.$modelUbicacion->FK_CLIENTE.',DESC_UBICACION='.$modelUbicacion->DESC_UBICACION.',FK_DOMICILIO='.$modelUbicacion->FK_DOMICILIO;
            user_log_bitacora($descripcionBitacora,'Modificar Ubicación de Cliente',$modelUbicacion->PK_UBICACION ); 
            
            // return $this->redirect(['view', 'PK_UBICACION' => $modelUbicacion->PK_UBICACION]);
            return $this->render('update', [
                'modelUbicacion' => $modelUbicacion,
                'modelDomicilio' => $modelDomicilio,
                'extra' => $extra,
                'mensaje' => 'Los datos se han guardado correctamente',
                'action' => 'insert',
            ]);
        } else {
            return $this->render('update', [
                'modelUbicacion' => $modelUbicacion,
                'modelDomicilio' => $modelDomicilio,
                'extra' => $extra,
                'mensaje' => 'Los datos se han guardado correctamente',
                'action' => '',
            ]);
        }
    }

    /**
     * Deletes an existing TblCatUbicaciones model.
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
     * Finds the TblCatUbicaciones model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TblCatUbicaciones the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TblCatUbicaciones::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
