<?php

namespace app\controllers;

use Yii;
use app\models\TblHardwareLicencias;
use app\models\TblHardware;
use app\models\TblHardwareEmpleados;
use app\models\TblEmpleados;
use app\models\TblCatTipoLicencia;
use app\models\TblCatEstatusLicencia;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use app\models\TblLicencias;

/**
 * LicenciasController implements the CRUD actions for TblLicencias model.
 */
class LicenciasController extends Controller
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
    public function actionIndex()
    {
      $request = Yii::$app->request;

   // die();
      if (!Yii::$app->request->isAjax) {
    $this->view->params['DatosEstatusLicencia'] = ArrayHelper::map(
            TblCatEstatusLicencia::find()->orderBy(['DESC_ESTATUS_LICENCIA'=>SORT_ASC])->asArray()->all(),
            'PK_ESTATUS_LICENCIA',
            'DESC_ESTATUS_LICENCIA'
        );
        return $this->render('index');
      } else
    echo(1);
      {
    echo '0';
   // die();


        //Obtener las licencias asignadas al hardware
        $Licencias =  Yii::$app->Licencias->getLicencias();

       // \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $res = array(

            'data'          => $Licencias,
        );


        return $res;
      }
    }

    public function actionBuscar()
    {
      $request = Yii::$app->request;

      //Obtener las licencias asignadas al hardware
      $LicenciasFiltros =  Yii::$app->Licencias->
          getLicenciasFiltros(

            $request->post('nombre', null),
            $request->post('estatusLicencia', null)

          );

      \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

      $res = array(

          'nombre' => $request->post('nombre'),
          'estatusLicencia' => $request->post('estatusLicencia'),
          'data'          => $LicenciasFiltros,
      );

      return $res;
    }

    /**
     * Displays a single TblLicencias model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
       $model = $this->findModel($id);
       $modelCatTipoLicencia       = new TblCatTipoLicencia();
       $modelCatEstatusLicencia    = new TblCatEstatusLicencia();
       $modelCatEstatusLicencia    = TblHardwareLicencias::find()->where(['FK_Licencia' => $model->PK_LICENCIA])->asArray()->all();

       $hardwareAsociados = (new \yii\db\Query())
                        ->select([" tbl_hardware_licencias.PK_Hardware_Licencias,
                                    CONCAT(tbl_empleados.NOMBRE_EMP, ' ', tbl_empleados.APELLIDO_PAT_EMP, ' ', tbl_empleados.APELLIDO_MAT_EMP) as EMPLEADO,
                                    tbl_hardware.CODIGO_EISEI,
                                    tbl_hardware_licencias.FECHA_REGISTRO ,
                                    DATE_ADD(tbl_hardware_licencias.FECHA_REGISTRO, INTERVAL tbl_licencias.Vigencia MONTH) AS FECHA_VIGENCIA,
                                    DATEDIFF(DATE_ADD(tbl_hardware_licencias.FECHA_REGISTRO, INTERVAL tbl_licencias.Vigencia MONTH) , NOW()) AS DIAS_RESTANTES,
                                    ((DATEDIFF(NOW(), tbl_hardware_licencias.FECHA_REGISTRO))/DATEDIFF(DATE_ADD(tbl_hardware_licencias.FECHA_REGISTRO, INTERVAL tbl_licencias.Vigencia MONTH), tbl_hardware_licencias.FECHA_REGISTRO)) * 100 as PORCENTAJE

                                      "])
                        ->from('tbl_licencias')

                        ->join('INNER JOIN','tbl_hardware_licencias',
                                'tbl_hardware_licencias.FK_Licencia = tbl_licencias.PK_LICENCIA')

                        ->join('INNER JOIN','tbl_hardware',
                                'tbl_hardware.PK_hardware = tbl_hardware_licencias.FK_hardware')

                        ->join('LEFT JOIN','tbl_hardware_empleados',
                                'tbl_hardware_empleados.FK_Hardware = tbl_hardware.PK_hardware')

                        ->join('LEFT JOIN','tbl_empleados',
                                'tbl_empleados.PK_Empleado = tbl_hardware_empleados.FK_Empleado')

                        /*->join('LEFT JOIN','tbl_cat_ubicaciones',
                                'tbl_perfil_empleados.FK_UBICACION_FISICA = tbl_cat_ubicaciones.PK_UBICACION')*/
                        ->andFilterWhere(
                            ['and',
                                ['=', 'tbl_licencias.PK_LICENCIA', $model->PK_LICENCIA]
                            ])
                        ->all();
      //  DD($hardwareAsociados);

            $modelEstatusLicencia= (new \yii\db\Query())
                    ->select(['es.DESC_ESTATUS_LICENCIA'])
                    ->from('tbl_cat_estatus_licencia as es')
                    ->join('INNER JOIN','tbl_licencias as hw',
                        'es.PK_ESTATUS_LICENCIA = hw.FK_Estatus_Licencia')
                    ->where(['hw.PK_LICENCIA'=>$id])
                   // ->orderBy('es.DESC_Estatus_Hardware ASC')
                    ->all();


             $modelTipoLicencia= (new \yii\db\Query())
                        ->select(['es.DESC_TIPO_LICENCIA'])
                        ->from('tbl_cat_tipo_licencia as es')
                        ->join('INNER JOIN','tbl_licencias as hw',
                            'es.PK_Tipo_Licencia = hw.FK_Tipo_Licencia')
                        ->where(['hw.PK_LICENCIA'=>$id])
                       // ->orderBy('es.DESC_Estatus_Hardware ASC')
                        ->all();

       if ($model->load(Yii::$app->request->post()) && $model->save()) {
           return $this->redirect(['index', 'id' => $model->PK_Licencias]);
       } else {
           return $this->render('view', [
                'model' => $model,
                'modelCatTipoLicencia' => $modelCatTipoLicencia,
                'modelCatEstatusLicencia' => $modelCatEstatusLicencia,
                'hardwareAsociados' => $hardwareAsociados,
                'id' => $model->PK_LICENCIA,
                'modelEstatusLicencia' => $modelEstatusLicencia,
                'modelTipoLicencia' => $modelTipoLicencia,
           ]);
       }
   }

    /**
   * Crea un modelo nuevo de tipo tblLicencias o retona la vista CREATe.
   * Si la creacion es correcta retorna a la vista VIEW con el valor de PK_LICENCIA del modelo $model recién generado.
   * @return vista VIEW | vista CREATE
   */
  public function actionCreate()
  {
      //Se define $model de tipo TblLicencias
      $model = new TblLicencias();

      //Se define  $model->FK_Estatus_Licencia = 2 (Estatus de Nueva).
      $model->FK_ESTATUS_LICENCIA = 2;

      //Si el request es por POST.
      if ($model->load(Yii::$app->request->post())) {

          //Se Guarda el modelo $model con la informacion obtenida por POST.
          if ($model->save(false))
          {
            //Se guarda la informacion en la bitacora
            $descripcionBitacora = 'PK_LICENCIA='.$model->PK_LICENCIA.',NOMBRE='.$model->NOMBRE;
            user_log_bitacora_soporte($descripcionBitacora,'Alta de Licencia de Hardware',$model->PK_LICENCIA );
          }

          //Se redirecciona a la vista VIEW con el valor de PK_LICENCIA del modelo $model.
        //  return $this->redirect(['view', 'id' => $model->PK_LICENCIA]);
          return $this->redirect(['index']);
      } else {
          //De lo contrario; se retorna la vista CREATE con el modelo $model
          return $this->render('create', [
              'model' => $model
          ]);
      }
  }


  /**
    * Actualiza un existente modelo tblLicencias y tblHardwareLicencias.
    * Si la actualización es correcta retorna a la vista VIEW.
    * @param integer $id
    * @return vista VIEW | vista CREATE
    */
    public function actionUpdate($id)
    {
        //Se obtiene el modelo que se desea actualizar.
        $model = $this->findModel($id);

        //Si el request es por POST.
        if ($model->load(Yii::$app->request->post())) {

            //Se valida que se hayan eliminado Hardwares Asociados, para eliminarnos de la base de datos.
            $resultEliminarHardwares = true;
            if (Yii::$app->request->post('PK_Hardware_Licencias_Eliminados') != '')
                $resultEliminarHardwares = TblHardwareLicencias::deleteAll("PK_Hardware_Licencias in (" . Yii::$app->request->post('PK_Hardware_Licencias_Eliminados') . ")");

            //Si se realiza la elimianción correcta de los Hardwares Asociados en la base de datos, procede al guardado de la Licencia.
            if ($resultEliminarHardwares != false) {

                //Se contabilizan los Hardwares Asociados
                $totalDeHardwaresAsociados =  TblHardwareLicencias::find()->where(['FK_LICENCIA' => $model->PK_LICENCIA])->count();

                //Se realiza la asignacion del estatus
                if ($totalDeHardwaresAsociados == 0)
                    $model->FK_ESTATUS_LICENCIA = 2; //NUEVA
                else if ($totalDeHardwaresAsociados >= $model->PCS)
                    $model->FK_ESTATUS_LICENCIA = 3; //FINALIZADA
                else
                    $model->FK_ESTATUS_LICENCIA = 1; //EN USO

                //Se Guarda el modelo $model con la informacion obtenida por POST.y el Estatus recalculado.
                if ($model->save())
                {
                  //Se guarda la bitacora
                  $descripcionBitacora = 'PK_LICENCIA='.$model->PK_LICENCIA.',Nombre='.$model->NOMBRE;
                    user_log_bitacora_soporte($descripcionBitacora,'Actualización de Licencia de Hardware',$model->PK_LICENCIA );
                }

                //Se redirecciona a la vista VIEW con el valor de PK_LICENCIA del modelo $model.
                //return $this->redirect(['update', 'id' => $model->PK_LICENCIA]);
                  return $this->redirect(['index']);
           }
       } else {

            //Se realiza la consulta de los HardwareAsociados ligados a la licencia que se desea actualizar.
            $hardwareAsociados = (new \yii\db\Query())
            ->select([" tbl_hardware_licencias.PK_HARDWARE_LICENCIAS,
                        CONCAT(tbl_empleados.NOMBRE_EMP, ' ', tbl_empleados.APELLIDO_PAT_EMP, ' ', tbl_empleados.APELLIDO_MAT_EMP) as EMPLEADO,
                        tbl_hardware.CODIGO_EISEI,
                        DATE_FORMAT(tbl_hardware_licencias.FECHA_REGISTRO, '%d/%m/%Y') as FECHA_REGISTRO,
                        DATE_FORMAT(DATE_ADD(tbl_hardware_licencias.FECHA_REGISTRO, INTERVAL tbl_licencias.Vigencia MONTH), '%d/%m/%Y') AS FECHA_VIGENCIA,
                        DATEDIFF(DATE_ADD(tbl_hardware_licencias.FECHA_REGISTRO, INTERVAL tbl_licencias.Vigencia MONTH) , NOW()) AS DIAS_RESTANTES,
                        ((DATEDIFF(NOW(), tbl_hardware_licencias.FECHA_REGISTRO))/DATEDIFF(DATE_ADD(tbl_hardware_licencias.FECHA_REGISTRO, INTERVAL tbl_licencias.Vigencia MONTH), tbl_hardware_licencias.FECHA_REGISTRO)) * 100 as PORCENTAJE
                          "])
            ->from('tbl_licencias')

            ->join('INNER JOIN','tbl_hardware_licencias',
                    'tbl_hardware_licencias.FK_Licencia = tbl_licencias.PK_LICENCIA')

            ->join('INNER JOIN','tbl_hardware',
                    'tbl_hardware.PK_hardware = tbl_hardware_licencias.FK_hardware')

            ->join('LEFT JOIN','tbl_hardware_empleados',
                    'tbl_hardware_empleados.FK_Hardware = tbl_hardware.PK_hardware')

            ->join('LEFT JOIN','tbl_empleados',
                    'tbl_empleados.PK_Empleado = tbl_hardware_empleados.FK_Empleado')
            ->andFilterWhere(
                ['and',
                    ['=', 'tbl_licencias.PK_LICENCIA', $model->PK_LICENCIA]
                ])
            ->all();

            //Se retorna la vista update con el modelo $model a actualizar.
           return $this->render('update', [
                'model' => $model,
                'hardwareAsociados' => $hardwareAsociados,
           ]);
       }
   }


    /**
     * Deletes an existing TblLicencias model.
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
     * Finds the TblLicencias model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TblLicencias the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TblLicencias::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
