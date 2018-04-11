<?php

namespace app\controllers;

use Yii;
use app\models\TblCandidatoEmpleado;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\TblBeneficiario;
use app\models\TblContactos;
use app\models\TblCandidatos;
use app\models\TblBitAdministradoraEmpleado;
use app\models\TblCandidatosHabilidades;
use app\models\TblCandidatosHerramientas;
use app\models\TblCandidatosTecnologias;
use app\models\TblVacantesCandidatos;
use app\models\TblCatEstados;
use app\models\TblCatPaises;
use app\models\TblCatMunicipios;

class AdministradoraController extends Controller
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
     * Lists all TblAdministradora models.
     * @return mixed
     */
    public function actionIndex()
    {
            $model = new TblCandidatoEmpleado();

            $modelBeneficiario = new TblBeneficiario(['scenario'=>'SCENARIO_SEGUNDO_BENEF']);
            $modelBeneficiario2 = new TblBeneficiario();
            $modelAdminContacto = new TblContactos();
            $modelEstado = new TblCatEstados();
            $modelCandidatos = new TblCandidatos();
            $modelBitAdmonEmpleado = new TblBitAdministradoraEmpleado();
            $data = Yii::$app->request->post();

                if ($model->load(Yii::$app->request->post()) && $modelAdminContacto->load(Yii::$app->request->post()))
                {       $modelBeneficiario2 = new TblBeneficiario(['scenario'=>'SCENARIO_SEGUNDO_BENEF']);


                        $modelAdminContacto->FECHA_REGISTRO = date('Y-m-d H:i:s');
                        $modelAdminContacto->save(false);


                        $modelBeneficiario->attributes= $data['TblBeneficiario'][1];
                        $modelBeneficiario->FECHA_REGISTRO =    date('Y-m-d H:i:s');
                        $modelBeneficiario->save(false);

                        $modelBeneficiario2->attributes=$data['TblBeneficiario'][2];
                        $modelBeneficiario2->FECHA_REGISTRO =    date('Y-m-d H:i:s');
                        $modelBeneficiario2->save(true);

                        $model->CURP = ($data['TblCandidatoEmpleado']['CURP'] == '' ? NULL : $data['TblCandidatoEmpleado']['CURP']);
                        $model->FECHA_NACIMIENTO = transform_date($data['TblCandidatoEmpleado']['FECHA_NACIMIENTO'], 'Y-m-d');
                        $model->FK_CONTACTO = $modelAdminContacto->PK_ADM_CONTC;
                        $model->FK_BENEFICIARIO_1 = $modelBeneficiario->PK_ADM_BENEF;
                        $model->FK_BENEFICIARIO_2 = $modelBeneficiario2->PK_ADM_BENEF;
                        $model->FECHA_REGISTRO =   date('Y-m-d H:i:s');
                        $pais = TblCatPaises::find()->select(['DESC_PAIS'])->where(['PK_PAIS' => $data['fk_pais']])->limit(1)->one();
                        $estado = TblCatEstados::find()->select(['DESC_ESTADO'])->where(['PK_ESTADO' => $data['fk_estado']])->limit(1)->one();
                        $municipio = TblCatMunicipios::find()->select(['DESC_MUNICIPIO'])->where(['PK_MUNICIPIO' => $data['fk_municipio'],'FK_ESTADO' => $data['fk_estado'] ])->limit(1)->one();
                        //dd($municipio['DESC_MUNICIPIO']);
                        //dd($data['fk_municipio']);
                        $model->PAIS = $data['fk_pais'];
                        $model->ESTADO = $data['fk_estado'];
                        $model->MUNICIPIO = $data['fk_municipio'];
                        //se agregaron estos campos para poder hacer referencia a la base de datos tbl_candidato_empleado
                        //$model->LUGAR_NACIMIENTO = $municipio['DESC_MUNICIPIO'].", ".$estado['DESC_ESTADO'].", ".$pais['DESC_PAIS'];
                        $model->save(false);

                       if ($modelCandidatos->load(Yii::$app->request->post()) == true)
                       {
                            if ($data['TblCandidatos']['PK_CANDIDATO']!= NULL || $data['TblCandidatos']['PK_CANDIDATO']!= '')
                               {

                            $modelBitAdmonEmpleado->FK_CANDIDATO =       $data['TblCandidatos']['PK_CANDIDATO'];
                            $modelBitAdmonEmpleado->FK_REGISTRO_ADMON =  $model->PK_REGISTRO;
                            $modelBitAdmonEmpleado->FECHA_REGISTRO =     date('Y-m-d');
                            $modelBitAdmonEmpleado->save(false);

                            $modelVacantesCandidatos = TblVacantesCandidatos::find()->where(['FK_CANDIDATO' => $data['TblCandidatos']['PK_CANDIDATO']])->limit(1)->one();
                            $modelVacantesCandidatos->FK_ESTATUS_ACTUAL_CANDIDATO = 6;
                            $modelVacantesCandidatos->FECHA_ACTUALIZACION= date('Y-m-d H:i:s');
                            $modelVacantesCandidatos->save(false);

                            $modelCandidatos->ESTATUS_CAND_APLIC = 0;
                            $modelCandidatos->save(false);

                               }
                        }

                $descripcionBitacora = 'ADMINISTRADORA ='.$model->PK_REGISTRO.','.$model->NOMBRE.' '.$model->APELLIDO_PATERNO.' '.$model->APELLIDO_MATERNO;
                user_log_bitacora($descripcionBitacora,'Alta en Administradora',$model->PK_REGISTRO);

                return $this->redirect(['index', 'action'=>'insert']);
        }

        else
         {
             return $this->render('index',[
                'model' => $model,
                'modelCandidatos' => $modelCandidatos,
                'modelAdminContacto' => $modelAdminContacto,
                'modelBeneficiario2' => $modelBeneficiario2,
                'modelEstado' => $modelEstado,
                'modelBeneficiario' => $modelBeneficiario,
            ]);
         }

    }
 /* Funcion que se ejecuta por medio de ajax, la cual se encarga de obtener los datos de un candidato para su administradora*/
    public function actionObtener_datos_candidato()
{
        if (Yii::$app->request->isAjax) {
            $data = Yii::$app->request->post();
            $idCandidato= $data['idCandidato'];
            if($idCandidato != '' && $idCandidato >0){
                $modelCandidatos = TblCandidatos::find()->where(['PK_CANDIDATO' => $idCandidato])->limit(1)->one();
                \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                return[
                    'idCandidato' => $idCandidato,
                    'APELLIDO_PATERNO' => $modelCandidatos->APELLIDO_PATERNO,
                    'APELLIDO_MATERNO' => $modelCandidatos->APELLIDO_MATERNO,
                    'NOMBRE' => $modelCandidatos->NOMBRE,
                    'EDAD' => $modelCandidatos->EDAD,
                    'TELEFONO' => $modelCandidatos->TELEFONO,
                ];
            }

        }
}

        public function actionValidar_campos()
{
        if (Yii::$app->request->isAjax){
            $data = Yii::$app->request->post();

            if(isset($data['PK_REGISTRO'])){
                $pk_registro= explode(":", $data['PK_REGISTRO']);
                $pk_registro = $pk_registro[0];
            } else {
                $pk_registro = 0;
            }

            $curp= explode(":", $data['curp']);
            $curp = $curp[0];
            $nss= explode(":", $data['nss']);
            $nss = $nss[0];
            $rfc= explode(":", $data['rfc']);
            $rfc = $rfc[0];
            $rfc = str_replace('-', '', $rfc);
            $curpRepetido = false;
            $nssRepetido = false;
            $rfcRepetido  = false;

            if(strlen($curp) == 18){
                $modelPerfilEmpleadosCURP = TblCandidatoEmpleado::find()->where(['CURP' => $curp])->andWhere(['NOT IN','PK_REGISTRO',$pk_registro])->all();
                if(count($modelPerfilEmpleadosCURP)>0){
                $curpRepetido = true;
                }
            }

            if(strlen($nss) > 0){
                $modelPerfilEmpleadosNSS = TblCandidatoEmpleado::find()->where(['NSS' => $nss])->andWhere(['NOT IN','PK_REGISTRO',$pk_registro])->all();
                if(count($modelPerfilEmpleadosNSS)>0){
                    $nssRepetido = true;
                }
            }

            if(strlen($rfc) == 13){
                $modelPerfilEmpleadosRFC = TblCandidatoEmpleado::find()->where(['RFC' => $rfc])->andWhere(['NOT IN','PK_REGISTRO',$pk_registro])->all();
                if(count($modelPerfilEmpleadosRFC)>0){
                    $rfcRepetido = true;
                }
            }

            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return [
                'curpRepetido' => $curpRepetido,
                'nssRepetido' => $nssRepetido,
                'rfcRepetido' => $rfcRepetido,
                'code' => 100,
            ];
        }
    }

    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }


    public function actionCreate()
    {
        $model = new TblAdministradora();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->PK_REGISTRO]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }


    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->PK_REGISTRO]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }


    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }


    protected function findModel($id)
    {
        if (($model = TblAdministradora::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
