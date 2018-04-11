<?php

namespace app\controllers;

use Yii;
use app\models\tblcatproyectos;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * DomiciliosController implements the CRUD actions for tbldomicilios model.
 */
class CatalogopController extends Controller
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
     * Lists all tbldomicilios models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => tblcatproyectos::find(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single tbldomicilios model.
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
     * Creates a new tbldomicilios model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new tblcatproyectos();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->PK_PROYECTO]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing tbldomicilios model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->PK_PROYECTO]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing tbldomicilios model.
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
     * Finds the tbldomicilios model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return tbldomicilios the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = tblcatproyectos::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
