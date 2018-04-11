<?php

namespace app\controllers;

use Yii;
use app\models\TblHardwareLicencias;
use app\models\TblHardware;
use app\models\TblLicencias;
use app\models\TblHardwareEmpleados;
use app\models\TblEmpleados;
use app\models\TblCatTipoLicencia;
use app\models\TblCatEstatusLicencia;

use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;


/**
 * AsociarLicenciaController implements the CRUD actions for TblHardwareLicencias model.
 */
class AsociarLicenciaController extends Controller
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
     * Lists all TblHardwareLicencias models.
     * @return mixed
     */
    public function actionIndex($id)
    {
      $request = Yii::$app->request;

      if (!Yii::$app->request->isAjax) {
        $dataProvider = new ActiveDataProvider([
            'query' => tblHardwareLicencias::find()->joinWith(['fKLicencia'])->joinWith(['fKHardware'])
        ]);

        $Hardware = TblHardware::find()->where(['PK_HARDWARE' => $id])->limit(1)->one();

        $recurso = 'vacio';
        if (tblHardwareEmpleados::find()->where(['FK_HARDWARE' => $id])->exists())
        {
            $HardwareEmpleados = TblHardwareEmpleados::find()->where(['FK_HARDWARE' => $id])->limit(1)->one();
            $Empleados = TblEmpleados::find()->where(['PK_EMPLEADO' => $HardwareEmpleados->FK_EMPLEADO])->limit(1)->one();
            $recurso = $Empleados->NOMBRE_EMP . ' ' . $Empleados->APELLIDO_PAT_EMP . ' ' . $Empleados->APELLIDO_MAT_EMP;
        }


        $this->view->params['Recurso'] = $recurso;
        $this->view->params['Equipo'] = $Hardware->EQUIPO;
        $this->view->params['Modelo'] = $Hardware->MODELO;
        $this->view->params['No_Serie'] = $Hardware->NO_SERIE;
        $this->view->params['Codigo_Eisei'] = $Hardware->CODIGO_EISEI;
        $this->view->params['DatosTipoLicencia'] = ArrayHelper::map(
            TblCatTipoLicencia::find()->orderBy(['DESC_TIPO_LICENCIA'=>SORT_ASC])->asArray()->all(),
            'PK_TIPO_LICENCIA',
            'DESC_TIPO_LICENCIA'
        );
        $this->view->params['DatosEstatusLicencia'] = ArrayHelper::map(
            TblCatEstatusLicencia::find()->orderBy(['DESC_ESTATUS_LICENCIA'=>SORT_ASC])->asArray()->all(),
            'PK_ESTATUS_LICENCIA',
            'DESC_ESTATUS_LICENCIA'
        );

        $this->view->params['LicenciasAsignadas'] =  Yii::$app->HardwareLicencias->getLicenciasAsignadas($id);
        $this->view->params['LicenciasSinAsignar'] =  Yii::$app->HardwareLicencias->getLicenciasSinAsignar($id);

        $this->view->params['idHardware'] = $request->get('id');

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
      } else
      {
        //Obtener las licencias asignadas al hardware
        $LicenciasAsignadas =  Yii::$app->HardwareLicencias->getLicenciasAsignadas($id);

        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $res = array(
            'idHardware' => $request->get('id'),
            'data'          => $LicenciasAsignadas,
        );
        return $res;
      }
    }

    /**
     * Displays a single TblHardwareLicencias model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
            'hardware' => $parametros,
        ]);
    }

    /**
     * Creates a new TblHardwareLicencias model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new TblHardwareLicencias();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->PK_HARDWARE_LICENCIAS]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Creates a new TblHardwareLicencias model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionGuardar()
    {
      $request = Yii::$app->request;

      //\Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

      $PK_HARDWARE = $request->post('PK_HARDWARE', null);
      $licenciasPorAsociar = $request->post('licenciasPorAsociar', null);
      $licenciasAEliminar = $request->post('licenciasAEliminar', null);

      if (strlen($licenciasPorAsociar) > 0)
      {
        $licencias = explode(',', $licenciasPorAsociar);
        $licencias = array_unique($licencias);

        foreach ($licencias as $key => $value) {


          $exists = tblHardwareLicencias::find()->where(
            ['FK_LICENCIA' => $value]
          )->andFilterWhere(
            ['FK_HARDWARE' => $PK_HARDWARE]
          )->exists();

          if (!$exists) {
            $model = new TblHardwareLicencias();

            $model->FK_HARDWARE = $PK_HARDWARE;
            $model->FK_LICENCIA = $value;

            $model->FECHA_REGISTRO = date('Y-m-d h:i:s');

            if( $model->save() ){
              $descripcionBitacora = 'Asociar: PK_HARDWARE='.$model->FK_HARDWARE.',FK_LICENCIA='.$model->FK_LICENCIA;
              user_log_bitacora_soporte($descripcionBitacora, 'Asociacion de Licencias', $model->FK_HARDWARE );
            }

            $modelLicencias = TblLicencias::find()->where(['PK_LICENCIA' => $model->FK_LICENCIA])->one();
            $modelLicencias->FK_ESTATUS_LICENCIA = 1;
            $modelLicencias->save(false);
          }
        }
      }

      if (strlen($licenciasAEliminar) > 0)
      {
        $licencias = explode(',', $licenciasAEliminar);
        $licencias = array_unique($licencias);

        foreach ($licencias as $key => $value) {

          $registroHardwareLicencia = tblHardwareLicencias::find()->where(
            ['FK_LICENCIA' => $value]
          )->andFilterWhere(
            ['FK_HARDWARE' => $PK_HARDWARE]
          )->one();


          $registroHardwareLicencia->delete();


          $sql = 'SELECT COUNT(*) FROM tbl_hardware_licencias where FK_LICENCIA = '.$value;
          $cnt =  Yii::$app->db->createCommand($sql)->queryScalar();

          if ($cnt == 0)
          {
            $modelLicencias = TblLicencias::find()->where(['PK_LICENCIA' => $value])->one();
            $modelLicencias->FK_ESTATUS_LICENCIA = 2;
            $modelLicencias->save(false);
          }

          $descripcionBitacora = 'PK_HARDWARE='.$PK_HARDWARE.',FK_LICENCIA='.$value;
          user_log_bitacora_soporte($descripcionBitacora, 'Asociacion de Licencias', $PK_HARDWARE );
        }
      }
      $res = array(
        'idHardware' => $PK_HARDWARE,
        'resultado'  => 'success',
        'licenciasPorAsociar' => $licenciasPorAsociar,
        'licenciasAEliminar' => $licenciasAEliminar,

      );

      //var_export($res);

      //die();
      return $res;

    }


    /**
     * Updates an existing TblHardwareLicencias model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->PK_HARDWARE_LICENCIAS]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing TblHardwareLicencias model.
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
     * Deletes an existing TblHardwareLicencias model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionBuscar()
    {
      $request = Yii::$app->request;

      //Obtener las licencias asignadas al hardware
      $LicenciasAsignadas =  Yii::$app->HardwareLicencias->
          getLicenciasSinAsignarB(
            $request->post('PK_HARDWARE'),
            $request->post('nombre', null),
            $request->post('estatusLicencia'),
            $request->post('tipoLicencia')
          );

      \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

      $res = array(
          'idHardware' => $request->post('PK_HARDWARE'),
          'nombre' => $request->post('nombre'),
          'estatusLicencia' => $request->post('estatusLicencia'),
          'tipoLicencia' => $request->post('tipoLicencia'),
          'data'          => $LicenciasAsignadas,
      );

      return $res;
    }






    /**
     * Finds the TblHardwareLicencias model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TblHardwareLicencias the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TblHardwareLicencias::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
