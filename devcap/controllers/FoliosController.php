<?php

namespace app\controllers;

use Yii;
use yii\db\ActiveQuery;
use app\models\TblFolios;
use app\models\TblClientes;
use app\models\TblProyectos;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\db\Query;
use yii\db\Command;
use yii\db\Expression;
use yii\web\UploadedFile;
use yii\grid\GridView;



class FoliosController extends Controller
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
     * Lists all tblcandidatos models.
     * @return mixed
     */
    public function actionIndex()
    {
        $request = Yii::$app->request;
        $tamanio_pagina= 20;
        

        if (Yii::$app->request->isAjax) {
            //INICIO AJAX

            $data = Yii::$app->request->post();
            $post=null;
            parse_str($data['data'],$post);

            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $folio =(!empty($post['folio']))? trim($post['folio']):'';
            $nombreFolio =(!empty($post['nombre']))? trim($post['nombre']):'';
            $cliente =(!empty($post['cliente']))? trim($post['cliente']):'';
            $pagina         =(!empty($post['page']))? trim($post['page']):'';
            $datosProyectos = [];
            if(empty($pagina)){
                $pagina=0;
            }else{
                $pagina= $pagina-1;
            }

            $total_registros = (new \yii\db\Query())
                        ->select("
                                 count(tbl_folios.PK_FOLIO) as count,
                                ")
                        ->from('tbl_folios')
                        ->andFilterWhere(
                            ['and',
                                ['LIKE', 'tbl_folios.FOLIO', $folio],
                                ['LIKE', 'tbl_folios.DESC_FOLIO', $nombreFolio],
                                ['LIKE', 'tbl_folios.FK_CLIENTE', $cliente]
                            ])
                        ->one();

            $total_paginas= ceil($total_registros['count']/$tamanio_pagina);
            if($total_registros['count']<=$tamanio_pagina){
                $pagina=0;
            }

            $datosFolios = (new \yii\db\Query())
                        ->select("
                 tbl_folios.PK_FOLIO, 
                 tbl_folios.FOLIO, 
				 tbl_folios.DESC_FOLIO,
				 tbl_folios.NOMBRE_CORTO_FOLIO,
				 tbl_clientes.NOMBRE_CLIENTE
				 ")
                        ->from('tbl_folios')
                        ->join('LEFT JOIN','tbl_clientes',
                                'tbl_folios.FK_CLIENTE = tbl_clientes.PK_CLIENTE')
                        ->andFilterWhere(
                            ['and',
                                ['LIKE', 'tbl_folios.FOLIO', $folio],
                                ['LIKE', 'tbl_folios.DESC_FOLIO', $nombreFolio],
                                ['LIKE', 'tbl_folios.FK_CLIENTE', $cliente]
                            ])
                        ->offset($pagina*$tamanio_pagina)
                        ->limit($tamanio_pagina)
                        ->all();

            foreach ($datosFolios as $array) {
                $datosProyectos[$array['PK_FOLIO']] = (new \yii\db\Query())
                            ->select(["COUNT(tbl_proyectos.PK_PROYECTO) as CANTIDAD"
                                        ])
                            ->from('tbl_proyectos')
                            ->join('LEFT JOIN','tbl_folios',
                                    'tbl_proyectos.FK_FOLIO = tbl_folios.PK_FOLIO')
                            ->andWhere(['tbl_folios.PK_FOLIO'=>$array['PK_FOLIO']
                                ])
                            ->all();
            }
            $bgColor1 = '#FFFFFF';
            $bgColor2 = '#F5F5F5';

            $html='';
            $contadorFolios = 0;
            foreach ($datosFolios as $array) {
                $i = 0;
                 
            if($contadorFolios%2==0){
                    $bgColor = $bgColor1;
                } else {
                    $bgColor = $bgColor2;
                }
	           foreach ($datosProyectos[$array['PK_FOLIO']] as $arrayProy) {
                    if(!empty($arrayProy['CANTIDAD'])){
                        $cantidadProy = ($arrayProy['CANTIDAD']!='')?'<p data-toggle="modal" href="#candidatos-comentarios-cancelacion" onclick="traerComentarios('.$array['PK_FOLIO'].','.$arrayProy['CANTIDAD'].')" class="name">Ver Detalle</p>':'N/A';
            		$pointer = $arrayProy['CANTIDAD']!=''?" cursor: pointer;":'';
                    } else {
                        $cantidadProy = '';
			            $pointer='';
                    }
               
                    $disabled = ($arrayProy['CANTIDAD'] > 0) ? 'disabled="disabled"': '';
                    
                    $html.= '<tr>';

                        $html.= "<td style='background-color: $bgColor;' class='border-top'>&nbsp;</td>";
                        $html.= "<td style='background-color: $bgColor;' class='border-top'><a href='".Url::to(["folios/view",'id'=> $array['PK_FOLIO']])."'>".$array['FOLIO']."</a></td>";
                        $html.= "<td style='background-color: $bgColor;' class='border-top'>".$array['DESC_FOLIO']."</td>";
                        $html.= "<td style='background-color: $bgColor;' class='border-top'>".$array['NOMBRE_CORTO_FOLIO']."</td>";
			            $html.= "<td style='background-color: $bgColor;' class='border-top'>".$array['NOMBRE_CLIENTE']."</td>";
                        $html.= "<td style='background-color: $bgColor;' class='border-top'>".$arrayProy['CANTIDAD']."</td>";
                        if ($arrayProy['CANTIDAD'] < 1)
                            $html.= "<td title='Eliminar' style='background-color: #FFFFFF;' class='item-delete icon-24x24' data-pk_folio='".$array['PK_FOLIO']."'></td>";
                        else
                            $html.= "<td style='background-color: $bgColor;' class='border-top'>&nbsp;</td>";

                    $contadorFolios++;
                    $i++;
                }
                $html.= '</tr>';
            }

            $cantFolios = count($datosFolios);
            if($cantFolios == 0){
                $html.="<tr>";
                    $html.='<td colspan="7"  style="text-align: center;font-size: large; font-weight: bold; text-decoration: underline; width: 100%; height: 100px;"></br> NO SE ENCONTRARON DATOS CON LOS PARAMETROS ESPECIFICADOS </br></br></td>';
                $html.="</tr>";
            }

            return [
                'data'=>$html,
                'total_paginas'=>$total_paginas,
                'pagina'=>$pagina,
                'post'=>$post,
                'total_registros'=>$total_registros['count'],
                'datosFolios'=>$datosFolios,
                'datosProyectos'=>$datosProyectos,
            ];
            //FIN AJAX
            //return ['hola'=>'hola'];
        }
        else{

            $dFolio = new tblFolios;
            $dProyecto = new tblProyectos;
            return $this->render('index');
        }

    }


    /**
     * Displays a single TblFolios model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        $modelProyectos = TblProyectos::find()->where(['FK_FOLIO' => $id])->asArray()->all();
        $modelCliente = TblClientes::find()->where(['PK_CLIENTE' => $model->FK_CLIENTE])->one();
        $model->FK_CLIENTE = $modelCliente->NOMBRE_CLIENTE;
        return $this->render('view', [
            'model' => $model,
            'modelProyectos' => $modelProyectos,
        ]);
    }

    /**
     * Creates a new TblFolios model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new TblFolios();

        if ($model->load(Yii::$app->request->post())) {
            $model->FECHA_REGISTRO=date('Y-m-d H:i:s');
            $model->save();
            return $this->redirect(['view', 'id' => $model->PK_FOLIO]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing TblFolios model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $modelProyectos = TblProyectos::find()->where(['FK_FOLIO' => $id])->asArray()->all();
        $cantProyectos = count($modelProyectos);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->PK_FOLIO]);
        } else {
            return $this->render('update', [
                'model' => $model,
                'cantProyectos' => $cantProyectos
            ]);
        }
    }

    /**
     * Deletes an existing TblFolios model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        if (Yii::$app->request->isAjax) {

            $this->findModel($id)->delete();

            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return true;
        }
    }

    /**
     * Finds the TblFolios model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TblFolios the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TblFolios::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
