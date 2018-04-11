<?php

namespace app\controllers;

use Yii;
use yii\base\Model;
use app\models\TblClientes;

use yii\db\ActiveQuery;
use app\models\tbldomicilios;
use app\models\tblcatubicaciones;
use app\models\tblcatmunicipios;
use app\models\tblcatestados;
use app\models\tblcatpaises;
use app\models\tblcatgiro;

use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\grid\GridView;
use yii\db\Query;
use yii\db\Expression;

/**
 * ClientesController implements the CRUD actions for TblClientes model.
 */
class ClientesController extends Controller
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
     * Lists all TblClientes models.
     * @return mixed
     */
    public function actionIndex()
    {
        $tamanio_pagina=9;
        if (Yii::$app->request->isAjax) {
            $data = Yii::$app->request->post();
            $post=null;
            parse_str($data['data'],$post);
            $nombre =(!empty($post['nombre']))? trim($post['nombre']):'';
            $rfc    =(!empty($post['rfc']))? trim($post['rfc']):'';
            $giro   =(!empty($post['giro']))? trim($post['giro']):'';
            $pagina =(!empty($data['pagina']))? trim($data['pagina']):'';

          
                $query= (new \yii\db\Query())
                        ->select(['count(c.PK_CLIENTE) as cont'])
                        ->from('tbl_clientes as c')
                        ->andFilterWhere(['or',
                            ['LIKE', 'c.ALIAS_CLIENTE', $nombre],
                            ['LIKE', 'c.NOMBRE_CLIENTE', $nombre],
                            ])
                        ->andFilterWhere(['and',
                            ['LIKE', 'c.RFC', $rfc],
                            ['=', 'c.FK_GIRO', $giro],
                        ])
                        ->orderBy('c.NOMBRE_CLIENTE ASC' )->one()['cont'];
                if($query<$tamanio_pagina){
                    $pagina=1;
                }
             
                $dataProvider = new ActiveDataProvider([
                    'query'=>(new \yii\db\Query())
                            ->select(['c.PK_CLIENTE as id',
                                'c.NOMBRE_CLIENTE AS campo1',
                                'c.FK_GIRO AS campo2',
                                'c.RFC AS campo3',
                                ])
                            ->from('tbl_clientes as c')
                            ->andFilterWhere(['or',
                                ['LIKE', 'c.ALIAS_CLIENTE', $nombre],
                                ['LIKE', 'c.NOMBRE_CLIENTE', $nombre],
                                ])
                            ->andFilterWhere(['and',
                                ['LIKE', 'c.RFC', $rfc],
                                ['=', 'c.FK_GIRO', $giro],
                            ])
                            ->orderBy('c.NOMBRE_CLIENTE ASC' )
                            ,
                    'pagination' => [
                        'pageSize' => $tamanio_pagina,
                        'page' => $pagina-1,
                    ],
                ]);
                
            


            $resultado=$dataProvider->getModels();
            foreach ($resultado as $key => $value) {
                // $domicilio = tbldomicilios::findOne($resultado[$key]->FK_DOMICILIO);
                // $municipio = tblcatmunicipios::findOne($domicilio->FK_MUNICIPIO);
                // $estado = tblcatestados::findOne($domicilio->FK_ESTADO);
                // $pais = tblcatpaises::findOne($domicilio->FK_PAIS);
                // $domicilioStr = 'C. '.$domicilio->CALLE.' '.$domicilio->NUM_INTERIOR.' '.
                //     'COL. '.$domicilio->COLONIA.' '.$domicilio->CP.' '.$municipio->DESC_MUNICIPIO.', '.
                //     $estado->DESC_ESTADO.'.';

                $giros = tblcatgiro::find()->where(['PK_GIRO' => $resultado[$key]['campo2']])->limit(1)->one();
                if($giros){
                    $idgiro = $giros->NOMBRE_GIRO;
                }else{
                    $idgiro = 'Sin Definir';
                }
                // $resultado[$key]->FK_DOMICILIO=$domicilioStr;
                $resultado[$key]['campo2']=$idgiro;
            }

            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $res = array(
                'pagina'        => $pagina,
                'data'          => $resultado,
                'total_paginas' => ceil($dataProvider->getPagination()->totalCount / $tamanio_pagina),
                'total_registros' => $query,
            );
     
            return $res;    
        }else{
            return $this->render('index', [
                'total_paginas' => 0,
            ]);
            
        }
    }

    /**
     * Displays a single TblClientes model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $model= $this->findModel($id);
       // var_dump($model);
        $model->FK_GIRO= tblcatgiro::find()->where(['PK_GIRO' => $model->FK_GIRO])->limit(1)->one();

        $domicilio = tbldomicilios::findOne($model->FK_DOMICILIO);
        //var_dump($domicilio);
        $pais      = tblcatpaises::findOne($domicilio->FK_PAIS);
        $estado    = tblcatestados::findOne($domicilio->FK_ESTADO);
        $municipio = tblcatmunicipios::find()->where(['PK_MUNICIPIO'=>$domicilio->FK_MUNICIPIO,'FK_ESTADO'=>$domicilio->FK_ESTADO,'FK_PAIS'=>$domicilio->FK_PAIS])->limit(1)->one();
        $extra['NUM_INTERIOR']   = $domicilio->NUM_INTERIOR;
        $extra['NUM_EXTERIOR']   = $domicilio->NUM_EXTERIOR;
        $extra['PISO']   = $domicilio->PISO;
        $extra['COLONIA']        = $domicilio->COLONIA;
        $extra['FK_MUNICIPIO'] = $municipio->DESC_MUNICIPIO;
        $extra['FK_ESTADO']    = $estado->DESC_ESTADO;
        $extra['CP']             = $domicilio->CP;
        $extra['PAIS']           = $pais->DESC_PAIS;
        return $this->render('view', [
            'model' => $model,
            'extra' => $extra,
            'domicilio' => $domicilio,
            
        ]);
    }

    /**
     * Creates a new TblClientes model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new tblclientes();
        $modelDomicilios = new tbldomicilios();

        if(!isset($model, $modelDomicilios)){
            throw new Exception("Error al obtener Datos de form");   
        }

        if ($model->load(Yii::$app->request->post()) && $modelDomicilios->load(Yii::$app->request->post())) {
                $modelDomicilios->save(false);

                $model->FECHA_REGISTRO=date('Y-m-d h:i:s');
                $model->FK_DOMICILIO=$modelDomicilios->PK_DOMICILIO;
                $model->HORAS_ASIGNACION = ($model->HORAS_ASIGNACION == null ? 0 : $model->HORAS_ASIGNACION);
                $model->FK_USUARIO_CREADOR= user_info()['PK_USUARIO'];
                $insert= $model->save(false);

                if($insert){
                    $descripcionBitacora = 'PK_CLIENTE='.$model->PK_CLIENTE.',NOMBRE_CLIENTE='.$model->NOMBRE_CLIENTE.',FK_GIRO='.$model->FK_GIRO.',FK_DOMICILIO='.$model->FK_DOMICILIO;
                    user_log_bitacora($descripcionBitacora,'Alta de Datos Cliente',$model->PK_CLIENTE );            
                }
                //Yii::$app->session->setFlash('success', 'Los datos se han guardado correctamente.');
                // return $this->redirect(['view', 'id' => $model->PK_CLIENTE]);
                return $this->redirect(['create', 'id' => $model->PK_CLIENTE,'action'=>'insert']);
        } else {
            //Yii::$app->session->setFlash('success', 'Error al guardar, inténtelo de nuevo.');
            return $this->render('create', [
                'model' => $model,
                'modelDomicilios' => $modelDomicilios,
            ]);
        }
    }

    /**
     * Updates an existing TblClientes model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        $sql = 'select * from tbl_domicilios AS td WHERE td.pk_domicilio = 
                (select tc.fk_domicilio from tbl_clientes tc where tc.pk_cliente ='.$id.')';

        $modelDomicilios = tbldomicilios::findBySql($sql)->limit(1)->one();

        $extra['DESC_MUNICIPIO']     = tblcatestados::findOne($modelDomicilios->FK_ESTADO);
        //$extra['DESC_ESTADO']     = tblcatmunicipios::findOne($modelDomicilios->FK_MUNICIPIO);
        $extra['DESC_ESTADO']  = tblcatmunicipios::find()->where(['PK_MUNICIPIO'=>$modelDomicilios->FK_MUNICIPIO,'FK_ESTADO'=>$modelDomicilios->FK_ESTADO,'FK_PAIS'=>$modelDomicilios->FK_PAIS])->limit(1)->one();

        if (!isset($model, $modelDomicilios)) {
            throw new NotFoundHttpException("El cliente no fue encontrado");
        }

        if ($model->load(Yii::$app->request->post()) && $modelDomicilios->load(Yii::$app->request->post()) ) {
            // $isValid = $model->validate();
            // $isValid = $modelDomicilios->validate() && $isValid;
            // if($isValid){
                $model->save(false);
                $modelDomicilios->save(false);

                $descripcionBitacora = 'PK_CLIENTE='.$model->PK_CLIENTE.',NOMBRE_CLIENTE='.$model->NOMBRE_CLIENTE;
                user_log_bitacora($descripcionBitacora,'Modificación de datos de Cliente',$model->PK_CLIENTE );            

                // Yii::$app->session->setFlash('success', 'El Registro se ha modificado correctamente');
                return $this->redirect(['view', 'id' => $id]);
            // }
        }

        return $this->render('update', [
            'model' => $model,
            'modelDomicilios' => $modelDomicilios,
            'extra' => $extra,
        ]);
    }

    /**
     * Deletes an existing TblClientes model.
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
     * Finds the TblClientes model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TblClientes the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TblClientes::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
