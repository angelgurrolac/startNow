<?php

namespace app\controllers;
use Yii;
use app\models\tblclientes;
use app\models\TblCatContactos;
use app\models\tblcatcontactospuestos;
use app\models\tblcatubicaciones;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

use yii\db\Query;
use yii\db\Expression;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * ContactosController implements the CRUD actions for TblCatContactos model.
 */
class ContactosController extends Controller
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
     * Lists all TblCatContactos models.
     * @return mixed
     */
    public function actionIndex()
    {
        
        $tamanio_pagina=12;
        if (Yii::$app->request->isAjax) {
            $data = Yii::$app->request->post();
            $post=null;
            parse_str($data['data'],$post);
            $nombre  =(!empty($post['nombre-c']))? trim($post['nombre-c']):'';
            $cliente =(!empty($post['FK_CLIENTE']))? trim($post['FK_CLIENTE']):'';
            $pagina  =(!empty($data['pagina']))? trim($data['pagina']):'';

            $total_elementos= (new \yii\db\Query())
                            ->select(['count(con.PK_CONTACTO) as cont'])
                            ->from('tbl_cat_contactos as con')
                            ->join('LEFT JOIN','tbl_cat_puestos_contacto',
                                'con.FK_PUESTO = tbl_cat_puestos_contacto.PK_PUESTO')
                            ->join('LEFT JOIN','tbl_clientes AS c',
                                'con.FK_CLIENTE = c.PK_CLIENTE')
                            ->join('LEFT JOIN','tbl_cat_ubicaciones ub',
                                'con.FK_UBICACION = ub.PK_UBICACION')
                            ->andFilterWhere(['=', 'con.FK_CLIENTE', $cliente])
                            ->andFilterWhere(
                                ['or',
                                    ['LIKE', 'con.NOMBRE_CONTACTO', $nombre],
                                    ['LIKE', 'con.APELLIDO_PAT', $nombre],
                                    ['LIKE', 'con.APELLIDO_MAT', $nombre],
                                ])
                        ->one()['cont'];
            if($total_elementos<$tamanio_pagina){
                $pagina=1;
            }
         
            $dataProvider = new ActiveDataProvider([
                'query'=>(new \yii\db\Query())
                            ->select(['con.PK_CONTACTO as id',
                            "CONCAT_WS(' ', con.NOMBRE_CONTACTO, con.APELLIDO_PAT, con.APELLIDO_MAT) AS campo1", 
                            'tbl_cat_puestos_contacto.DESC_PUESTO as campo2', 
                            'con.ESTATUS as campo3',
                            "(CASE con.ESTATUS WHEN 1 THEN  'INACTIVO' ELSE 'ACTIVO' END )as campo4",
                            "ub.DESC_UBICACION as campo5","cte.NOMBRE_CLIENTE as campo6"])
                            ->from('tbl_cat_contactos as con')
                            ->join('LEFT JOIN','tbl_cat_puestos_contacto',
                                'con.FK_PUESTO = tbl_cat_puestos_contacto.PK_PUESTO')
                            ->join('LEFT JOIN','tbl_clientes cte',
                                'con.FK_CLIENTE = cte.PK_CLIENTE')
                            ->join('LEFT JOIN','tbl_cat_ubicaciones ub',
                                'con.FK_UBICACION = ub.PK_UBICACION')
                            ->andFilterWhere(['=', 'con.FK_CLIENTE', $cliente])
                            ->andFilterWhere(
                                ['or',
                                    ['LIKE', 'con.NOMBRE_CONTACTO', $nombre],
                                    ['LIKE', 'con.APELLIDO_PAT', $nombre],
                                    ['LIKE', 'con.APELLIDO_MAT', $nombre],
                                ])
                            ->orderBy('campo1 asc')
                        ,
                'pagination' => [
                    'pageSize' => $tamanio_pagina,
                    'page' => $pagina-1,
                ],
            ]);

            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $res = array(
                'post'        => $cliente,
                'pagina'        => $pagina,
                'data'          => $dataProvider->getModels(),
                'total_paginas' => ceil($dataProvider->getPagination()->totalCount / $tamanio_pagina),
                'total_registros' => $total_elementos,
            );
     
            return $res;    
        }else{
            $dataProvider = new ActiveDataProvider([
                'query' => TblCatContactos::find(),
            ]);

            return $this->render('index', [
                'dataProvider' => $dataProvider,
            ]);
        }


    }

    /**
     * Displays a single TblCatContactos model.
     * @param integer $PK_CONTACTO
     * @param integer $FK_CLIENTE
     * @return mixed
     */
    public function actionView($PK_CONTACTO, $FK_CLIENTE)
    {
        $model= $this->findModel($PK_CONTACTO, $FK_CLIENTE);
        //$model-> FK_PUESTO = tblcatcontactospuestos::find()->select('DESC_PUESTO')->where(['PK_PUESTO' => $model->FK_PUESTO])->limit(1)->one();

        //Se meten todos los valores de los modelos en un arreglo
        $puestosContacto = tblcatcontactospuestos::findOne($model->FK_PUESTO);
        $cliente = tblclientes::findOne($model->FK_CLIENTE);
       
        $extra['DESC_PUESTO'] = $puestosContacto->DESC_PUESTO;
        $extra['NOMBRE_CLIENTE'] = $cliente->NOMBRE_CLIENTE;

        return $this->render('view', [
            'model' => $model,
            'extra' => $extra,
        ]);
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
                <h3 class="modal-title" class="campos-title font-bold"> 
                    <a href="<?php echo Url::to(["update",'PK_CONTACTO'=>$model->PK_CONTACTO,'FK_CLIENTE'=>$model->FK_CLIENTE]); ?>" title="Ver" class="item-update iconview-24x24"></a>
                    Datos de Contacto  
                </h3>
            </div>
            <div class="modal-body">
               
                <div class="row">
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
                </div>
            </div>
            <?php 
        }
            // $model= TblCatContactos::findOne('1');
            // var_dump( $model);
    }

    /**
     * Creates a new TblCatContactos model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new TblCatContactos();
        $model->FECHA_REGISTRO=date('Y-m-d H:i:s');

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $descripcionBitacora = 'FK_CLIENTE='.$model->FK_CLIENTE.',NOMBRE_CONTACTO='.$model->NOMBRE_CONTACTO.',FK_UBICACION='.$model->FK_UBICACION.',FK_PUESTO='.$model->FK_PUESTO;
            user_log_bitacora($descripcionBitacora,'Registrar Contacto de Cliente',$model->PK_CONTACTO );    
            // return $this->redirect(['view', 'PK_CONTACTO' => $model->PK_CONTACTO, 'FK_CLIENTE' => $model->FK_CLIENTE]);
            return $this->render('update', [
                'model' => $model,
                'mensaje' => 'Los datos se han guardado correctamente',
                'action' => 'insert',
            ]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing TblCatContactos model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $PK_CONTACTO
     * @param integer $FK_CLIENTE
     * @return mixed
     */
    public function actionUpdate($PK_CONTACTO, $FK_CLIENTE)
    {
        $model = $this->findModel($PK_CONTACTO, $FK_CLIENTE);
        
        if ($model->load(Yii::$app->request->post()) && $model->save(false)) {
            $descripcionBitacora = 'FK_CLIENTE='.$model->FK_CLIENTE.',NOMBRE_CONTACTO='.$model->NOMBRE_CONTACTO.',FK_UBICACION='.$model->FK_UBICACION.',FK_PUESTO='.$model->FK_PUESTO;
            user_log_bitacora($descripcionBitacora,'Modificar Contacto de Cliente',$model->PK_CONTACTO ); 
            // return $this->redirect(['view', 'PK_CONTACTO' => $model->PK_CONTACTO, 'FK_CLIENTE' => $model->FK_CLIENTE]);
            return $this->render('update', [
                'model' => $model,
                'mensaje' => 'Los datos se han guardado correctamente',
                'action' => 'insert',
            ]);
        } else {
            return $this->render('update', [
                'model' => $model,
                'mensaje' => 'Los datos se han guardado correctamente',
                'action' => '',
            ]);
        }
    }

    /**
     * Deletes an existing TblCatContactos model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $PK_CONTACTO
     * @param integer $FK_CLIENTE
     * @return mixed
     */
    public function actionDelete($PK_CONTACTO, $FK_CLIENTE)
    {
        $this->findModel($PK_CONTACTO, $FK_CLIENTE)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the TblCatContactos model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $PK_CONTACTO
     * @param integer $FK_CLIENTE
     * @return TblCatContactos the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($PK_CONTACTO, $FK_CLIENTE)
    {
        if (($model = TblCatContactos::findOne(['PK_CONTACTO' => $PK_CONTACTO, 'FK_CLIENTE' => $FK_CLIENTE])) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
