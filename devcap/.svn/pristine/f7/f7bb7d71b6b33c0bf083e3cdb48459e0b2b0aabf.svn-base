<?php

use app\models\TblContactos;
use app\models\TblDocumentosEmpleados;
use app\models\TblBitComentariosEmpleados;
use app\models\TblPerfilEmpleados;
use app\models\TblBitUnidadNegocioAsig;
use app\models\tblAdministradoraBeneficiario;
use yii\widgets\DetailView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\db\Query;
use yii\db\Command;
use yii\db\Expression;
use yii\data\ActiveDataProvider;
/* @var $this yii\web\View */
/* @var $model app\models\tblempleados */

$idA = '';

if(isset($_GET['idAsignacion'])){
  $idA = abs($_GET['idAsignacion']);
}

$this->title = 'Consulta detallada de recurso';
$this->params['breadcrumbs'][] = ['label' => 'Tblempleados', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$datosDocumentosEmpleados = TblDocumentosEmpleados::find()->select('NOMBRE_DOCUMENTO, RUTA_DOCUMENTO, FECHA_CREACION')->where(['FK_EMPLEADO' => $model->PK_EMPLEADO])->asArray()->all();
$datosComentariosEmpleado = TblBitComentariosEmpleados::find()->where(['FK_EMPLEADO' => $model->PK_EMPLEADO])->asArray()->all();
$operacionBaja = TblPerfilEmpleados::find()->where(['FK_EMPLEADO' => $modelPerfilEmpleados->FK_EMPLEADO])->asArray()->one();

/*$dataProvider = new ActiveDataProvider([
                'query'=>(new \yii\db\Query())
                            ->select([
                                        '(CASE
                                            WHEN tbuna.FK_REGISTRO = 0 THEN DATE_FORMAT(tbuna.FECHA_CREACION, "%d/%m/%Y")
                                            WHEN tbuna.FK_REGISTRO > 0 THEN DATE_FORMAT(a.FECHA_INI, "%d/%m/%Y")
                                        END) AS FECHA_CREACION
                                            ',
                                        'te.NOMBRE_EMP',
                                        'te.APELLIDO_PAT_EMP',
                                        'te.APELLIDO_MAT_EMP',
                                        'tun.DESC_UNIDAD_NEGOCIO',
                                        'tbuna.FK_REGISTRO'])
                            ->from('tbl_bit_unidad_negocio_asig as tbuna')
                            ->join('left join','tbl_asignaciones a',
                                'a.PK_ASIGNACION = tbuna.FK_REGISTRO')
                            ->join('JOIN','tbl_empleados te',
                                'te.PK_EMPLEADO = tbuna.FK_EMPLEADO')
                            ->join('JOIN','tbl_cat_unidades_negocio as tun',
                                'tun.PK_UNIDAD_NEGOCIO = tbuna.FK_REGISTRO')
                            ->andFilterWhere(
                            ['and',
                                ['=', 'tbuna.FK_EMPLEADO', $modelPerfilEmpleados->FK_EMPLEADO],
                            ])
                            ->orderBy('tbuna.PK_UNIDAD_NEGOCIO_ASIG DESC')
                            ]);
$resultado=$dataProvider->getModels();*/
$connection = \Yii::$app->db;
$resultado = $connection->createCommand(" select
                                            (CASE
                                                WHEN tbuna.FK_REGISTRO = 0 THEN DATE_FORMAT(tbuna.FECHA_CREACION, '%d/%m/%Y')
                                                WHEN tbuna.FK_REGISTRO > 0 THEN DATE_FORMAT(a.FECHA_INI, '%d/%m/%Y')
                                            END) AS FECHA_CREACION,
                                        te.NOMBRE_EMP,
                                        te.APELLIDO_PAT_EMP,
                                        te.APELLIDO_MAT_EMP,
                                        tun.DESC_UNIDAD_NEGOCIO,
                                        tbuna.FK_REGISTRO
                                    from tbl_bit_unidad_negocio_asig as tbuna
                                    left join tbl_asignaciones a on a.PK_ASIGNACION = tbuna.FK_REGISTRO
                                    join tbl_empleados te on te.PK_EMPLEADO = tbuna.FK_EMPLEADO
                                    join tbl_cat_unidades_negocio as tun on tun.PK_UNIDAD_NEGOCIO = tbuna.FK_UNIDAD_NEGOCIO
                                    where tbuna.FK_EMPLEADO = ".$modelPerfilEmpleados->FK_EMPLEADO."
                                    ORDER by tbuna.PK_UNIDAD_NEGOCIO_ASIG DESC")->queryAll();

$connection->close();

$cambiosUnidadesNegocio = TblBitUnidadNegocioAsig::find()->where(['FK_EMPLEADO' => $modelPerfilEmpleados->FK_EMPLEADO])->asArray()->count();
//var_dump($operacionBaja);
$session = Yii::$app->session;
?>
<style type="text/css">
.pInactivo{
    color: #A7A7A7;
    margin: 0px;
    text-decoration: underline;
}
</style>
<div class="col-lg-12">

    <h1 class="title row">

        <?php if($idA != 0){ ?>

            <div class="col-lg-7">
                <a href="<?php echo Url::to(['empleados/index', 'idAsignacion' => $idA]); ?>"  class="return-arrow icon-12x21"></a> <?= Html::encode($this->title) ?>
            </div>
        <?php }else{?>

            <div class="col-lg-7">
                <a href="<?php echo Url::to(['empleados/index']); ?>"  class="return-arrow icon-12x21"></a> <?= Html::encode($this->title) ?>
            </div>
        <?php } ?>

        <?php if($session->get('usuario')['IS_SUPER_ADMIN']==1){ ?>
                <?php if(valida_permisos(['boton_asociar_candidatos'])){ ?>
                    <?= Html::a('COMPROBANTES DE NOMINA', ['incidencias-nomina/comprobantes','id' =>$model->PK_EMPLEADO], ['class' => 'btn btn-success der']) ?>
                <?php } ?>
           <?php } ?>
    </h1>

    <div class="resultados clientes-view">
        <div class="campos padding-bottom">
            <div class="row">
                <div class='col-lg-8 padding-top'>
                    <h3 class="campos-title font-bold">
                        <?php if(valida_permisos(['empleados/cambiar_empleado'])){ ?>
                            <a href="<?php echo Url::to(["empleados/cambiar_empleado",'id'=>$model->PK_EMPLEADO]);/*echo Url::to(["update",'id'=>$model->PK_EMPLEADO]);*/ ?>" title="Ver" class="item-update iconview-24x24"></a>
                        <?php } ?>
                        <?php /* ?>
                        <a href="<?php echo Url::to(["delete",'id'=>$model->PK_CLIENTE]); ?>" title="Ver" class="item-delete iconview-24x24"></a>
                        */ ?>
                        Datos Personales
                    </h3>
                    <div class="col-lg-3 col-md-3 col-sm-4 col-xs-12">
                        <div class="foto-perfil" style="background-image:url(../..<?= $model->FOTO_EMP ?>)">

                        </div>

                    </div>
                    <div class="col-lg-9 col-md-9 col-sm-8 col-xs-12">

                            <div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12 title-destacado">
                                <p class="font-bold"><?= $model->NOMBRE_EMP.' '.$model->APELLIDO_PAT_EMP.' '.$model->APELLIDO_MAT_EMP ?></p>
                            </div>
                        <div class="row">
                            <div class="form-group col-lg-4 col-md-4 col-sm-4">
                                <?= Html::label($model->getAttributeLabel('FK_GENERO_EMP'),null,['class'=>'campos-label']) ?>
                                <p class="font-bold"><?php echo $model->FK_GENERO_EMP ?></p>
                            </div>
                            <div class="form-group col-lg-4 col-md-4 col-sm-4">
                                <?= Html::label($model->getAttributeLabel('LUGAR_NAC_EMP'),null,['class'=>'campos-label']) ?>
                                <p class="font-bold"><?php echo $model->LUGAR_NAC_EMP ?></p>
                            </div>
                            <div class="form-group col-lg-4 col-md-4 col-sm-4">
                                <?= Html::label($model->getAttributeLabel('EMAIL_EMP'),null,['class'=>'campos-label']) ?>
                                <p class="font-bold"><?php echo $model->EMAIL_EMP ?></p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-lg-4 col-md-4 col-sm-4">
                                <?= Html::label($model->getAttributeLabel('FECHA_NAC_EMP'),null,['class'=>'campos-label']) ?>
                                <p class="font-bold"><?php echo transform_date($model->FECHA_NAC_EMP,'d/m/Y') ?></p>
                            </div>
                            <div class="form-group col-lg-4 col-md-4 col-sm-4">
                                <?= Html::label($model->getAttributeLabel('RFC_EMP'),null,['class'=>'campos-label']) ?>
                                <p class="font-bold"><?php echo $model->RFC_EMP ?></p>
                            </div>
                            <div class="form-group col-lg-4 col-md-4 col-sm-4">
                                <?= Html::label($model->getAttributeLabel('NSS_EMP'),null,['class'=>'campos-label']) ?>
                                <p class="font-bold"><?php echo $model->NSS_EMP ?></p>
                            </div>
                        </div>
                        <div class="row">

                            <div class="form-group col-lg-4 col-md-4 col-sm-4">
                                <?= Html::label($model->getAttributeLabel('NACIONALIDAD_EMP'),null,['class'=>'campos-label']) ?>
                                <p class="font-bold"><?php echo $model->NACIONALIDAD_EMP ?></p>
                            </div>
                            <div class="form-group col-lg-4 col-md-4 col-sm-4">
                                <?= Html::label($model->getAttributeLabel('CURP_EMP'),null,['class'=>'campos-label']) ?>
                                <p class="font-bold"><?php echo $model->CURP_EMP ?></p>
                            </div>
                            <div class="form-group col-lg-4 col-md-4 col-sm-4">
                                <?= Html::label($model->getAttributeLabel('EMAIL_INTERNO'),null,['class'=>'campos-label']) ?>
                                <p class="font-bold"><?php echo ($model->EMAIL_INTERNO==null?'N/C':$model->EMAIL_INTERNO); ?></p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-lg-4 col-md-4 col-sm-4">
                                <?= Html::label($model->getAttributeLabel('EMAIL_ASIGNADO'),null,['class'=>'campos-label']) ?>
                                <p class="font-bold"><?php echo ($model->EMAIL_ASIGNADO==null?'N/C':$model->EMAIL_ASIGNADO); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class='col-lg-4 border-left padding-top'>
                    <h3 class="campos-title font-bold">
                        <?php if(valida_permisos(['empleados/cambiar_domicilio'])){ ?>
                            <a href="<?php echo Url::to(["empleados/cambiar_domicilio",'id'=>$model->PK_EMPLEADO]); ?>" title="Ver" class="item-update iconview-24x24"></a>
                        <?php } ?>
                        <?php /* ?>
                        <a href="<?php echo Url::to(["delete",'id'=>$model->PK_CLIENTE]); ?>" title="Ver" class="item-delete iconview-24x24"></a>
                        */ ?>
                        Domicilio Actual
                    </h3>
                    <div class="form-group col-lg-6 col-md-4 col-sm-4">
                        <?= Html::label($modelDomicilios->getAttributeLabel('CALLE'),null,['class'=>'campos-label']) ?>
                        <p class="font-bold"><?php echo $modelDomicilios->CALLE ?></p>
                    </div>
                    <div class="form-group col-lg-6 col-md-4 col-sm-4">
                        <?= Html::label($modelDomicilios->getAttributeLabel('COLONIA'),null,['class'=>'campos-label']) ?>
                        <p class="font-bold"><?php echo $modelDomicilios->COLONIA ?></p>
                    </div>
                    <div class="form-group col-lg-6 col-md-4 col-sm-4">
                        <?= Html::label($modelDomicilios->getAttributeLabel('CP'),null,['class'=>'campos-label']) ?>
                        <p class="font-bold"><?php echo $modelDomicilios->CP ?></p>
                    </div>
                    <div class="form-group col-lg-6 col-md-4 col-sm-4">
                        <?= Html::label($modelDomicilios->getAttributeLabel('FK_PAIS'),null,['class'=>'campos-label']) ?>
                        <p class="font-bold"><?php echo $modelDomicilios->FK_PAIS ?></p>
                    </div>
                    <div class="form-group col-lg-6 col-md-4 col-sm-4">
                        <?= Html::label($modelDomicilios->getAttributeLabel('FK_ESTADO'),null,['class'=>'campos-label']) ?>
                        <p class="font-bold"><?php echo $modelDomicilios->FK_ESTADO ?></p>
                    </div>
                    <div class="form-group col-lg-6 col-md-4 col-sm-4">
                        <?= Html::label($modelDomicilios->getAttributeLabel('FK_MUNICIPIO'),null,['class'=>'campos-label']) ?>
                        <p class="font-bold"><?php echo $modelDomicilios->FK_MUNICIPIO ?></p>
                    </div>
                    <div class="form-group col-lg-6 col-md-4 col-sm-4">
                        <?= Html::label($modelDomicilios->getAttributeLabel('TELEFONO'),null,['class'=>'campos-label']) ?>
                        <p class="font-bold"><?php echo $modelDomicilios->TELEFONO ?></p>
                    </div>
                    <div class="form-group col-lg-6 col-md-4 col-sm-4">
                        <?= Html::label($modelDomicilios->getAttributeLabel('CELULAR'),null,['class'=>'campos-label']) ?>
                        <p class="font-bold"><?php echo $modelDomicilios->CELULAR ?></p>
                    </div>
                    <div class="form-group col-lg-6 col-md-4 col-sm-4">
                        <?php if(isset($modelContactos) ==false){?>
                            <?= Html::label('Contacto por Emergencia',null,['class'=>'campos-label'])?>
                        <?php $modelContactos= '';
                        } else{?>
                        <?= Html::label($modelContactos->getAttributeLabel('CASO_ACCIDENTE'),null,['class'=>'campos-label']) ?>
                        <p class="font-bold"><?php echo $modelContactos->CASO_ACCIDENTE ?></p><?php } ?>
                    </div>
                    <div class="form-group col-lg-6 col-md-4 col-sm-4">
                        <?= Html::label($modelDomicilios->getAttributeLabel('TEL_EMERGENCIA'),null,['class'=>'campos-label']) ?>
                        <p class="font-bold"><?php echo $modelDomicilios->TEL_EMERGENCIA ?></p>
                    </div>
                    <?php
                    if ($modelAdministradoraBenef!='')
                    {
                    ?>
                            <div class="clearfix"></div>
                        <h3 class="campos-title font-bold">
                            <?php if(valida_permisos(['empleados/cambiar_beneficiario'])){ ?>
                                <a href="<?php echo Url::to(["empleados/cambiar_beneficiario",'id'=>$model->PK_EMPLEADO]); ?>" title="Ver" class="item-update iconview-24x24"></a>
                            <?php } ?>
                            <?php /* ?>
                            <a href="<?php echo Url::to(["delete",'id'=>$model->PK_CLIENTE]); ?>" title="Ver" class="item-delete iconview-24x24"></a>
                            */ ?>
                           Contactos y Beneficiario
                        </h3>
                        <div class="form-group col-lg-6 col-md-4 col-sm-4">
                            <?= Html::label($modelAdministradoraBenef->getAttributeLabel('NOMBRE_BEN'),null,['class'=>'campos-label']) ?>
                            <p class="font-bold"><?php echo $modelAdministradoraBenef->NOMBRE_BEN ?></p>
                        </div>
                        <div class="form-group col-lg-6 col-md-4 col-sm-4">
                            <?= Html::label($modelAdministradoraBenef->getAttributeLabel('RFC_BEN'),null,['class'=>'campos-label']) ?>
                            <p class="font-bold"><?php echo $modelAdministradoraBenef->RFC_BEN ?></p>
                        </div>
                        <div class="form-group col-lg-6 col-md-4 col-sm-4">
                            <?= Html::label($modelAdministradoraBenef->getAttributeLabel('PARENTESCO_BEN'),null,['class'=>'campos-label']) ?>
                            <p class="font-bold"><?php echo $modelAdministradoraBenef->PARENTESCO_BEN ?></p>
                        </div>
                        <div class="form-group col-lg-6 col-md-4 col-sm-4">
                            <?= Html::label($modelAdministradoraBenef->getAttributeLabel('PORCENTAJE'),null,['class'=>'campos-label']) ?>
                            <p class="font-bold"><?php echo $modelAdministradoraBenef->PORCENTAJE ?></p>
                        </div>
                        <div class="form-group col-lg-12 col-md-4 col-sm-4">
                            <?= Html::label($modelAdministradoraBenef->getAttributeLabel('DOMICILIO'),null,['class'=>'campos-label']) ?>
                            <p class="font-bold"><?php echo $modelAdministradoraBenef->DOMICILIO ?></p>
                                </div>
                            <?php } ?>

                             <?php
                            if ($SecondmodelBenef!='')
                            {
                            ?>
                                <div class="clearfix"></div>
                                <h3 class="campos-title font-bold">
                                    <?php if(valida_permisos(['empleados/cambiar_segundobeneficiario'])){ ?>
                                        <a href="<?php echo Url::to(["empleados/cambiar_segundobeneficiario",'id'=>$model->PK_EMPLEADO]); ?>" title="Ver" class="item-update iconview-24x24"></a>
                                    <?php } ?>
                                    <?php /* ?>
                                    <a href="<?php echo Url::to(["delete",'id'=>$model->PK_CLIENTE]); ?>" title="Ver" class="item-delete iconview-24x24"></a>
                                    */ ?>
                                   Segundo Beneficiario
                                </h3>
                                <div class="form-group col-lg-6 col-md-4 col-sm-4">
                                    <?= Html::label($SecondmodelBenef->getAttributeLabel('NOMBRE_BEN'),null,['class'=>'campos-label']) ?>
                                    <p class="font-bold"><?php echo $SecondmodelBenef->NOMBRE_BEN ?></p>
                                </div>
                                <div class="form-group col-lg-6 col-md-4 col-sm-4">
                                    <?= Html::label($SecondmodelBenef->getAttributeLabel('RFC_BEN'),null,['class'=>'campos-label']) ?>
                                    <p class="font-bold"><?php echo $SecondmodelBenef->RFC_BEN ?></p>
                                </div>
                                <div class="form-group col-lg-6 col-md-4 col-sm-4">
                                    <?= Html::label($SecondmodelBenef->getAttributeLabel('PARENTESCO_BEN'),null,['class'=>'campos-label']) ?>
                                    <p class="font-bold"><?php echo $SecondmodelBenef->PARENTESCO_BEN ?></p>
                                </div>
                                <div class="form-group col-lg-6 col-md-4 col-sm-4">
                                    <?= Html::label($SecondmodelBenef->getAttributeLabel('PORCENTAJE'),null,['class'=>'campos-label']) ?>
                                    <p class="font-bold"><?php echo $SecondmodelBenef->PORCENTAJE ?></p>
                                </div>
                                <div class="form-group col-lg-12 col-md-4 col-sm-4">
                                    <?= Html::label($SecondmodelBenef->getAttributeLabel('DOMICILIO'),null,['class'=>'campos-label']) ?>
                                    <p class="font-bold"><?php echo $SecondmodelBenef->DOMICILIO ?></p>
                                </div>
                            <?php } ?>
                </div>
            </div>
        </div>
        <div class="campos padding-bottom">
            <div class="row">
                <div class='col-lg-8 col-md-12 col-sm-12 col-xs-12 border-right padding-top'>
                    <h3 class="campos-title font-bold">
                        <?php if(valida_permisos(['empleados/cambiar_perfil'])){ ?>
                            <a href="<?php echo Url::to(["empleados/cambiar_perfil",'id'=>$model->PK_EMPLEADO]); ?>" title="Ver" class="item-update iconview-24x24"></a>
                        <?php } ?>
                        <?php /* ?>
                        <a href="<?php echo Url::to(["delete",'id'=>$model->PK_CLIENTE]); ?>" title="Ver" class="item-delete iconview-24x24"></a>
                        */ ?>
                        Perfil de Empleado
                    </h3>

                    <div class="col-lg-4 col-md-12 col-sm-12 col-xs-12 zero-padding">
                        <div class="col-lg-12 col-md-6 col-sm-6">
                            <?php
                                if(strcmp($modelPerfilEmpleados->CV_ORIGINAL,'')!=0){
                                    ?> <a href="<?= $modelPerfilEmpleados->CV_ORIGINAL ?>" download="<?= 'CVOriginal_'.$model->NOMBRE_EMP.' '.$model->APELLIDO_PAT_EMP.' '.$model->APELLIDO_MAT_EMP ?>"> Descargar CV Original </a> <?php
                                } else {
                                    ?> <p class="pInactivo">Descargar CV Original</p><?php
                                }
                            ?>
                        </div>
                        <div class="col-lg-12 col-md-6 col-sm-6">
                            <?php
                                if(strcmp($modelPerfilEmpleados->CV_EISEI,'')!=0){
                                    ?> <a href="<?= $modelPerfilEmpleados->CV_EISEI ?>" download="<?= 'CVEISEI_'.$model->NOMBRE_EMP.' '.$model->APELLIDO_PAT_EMP.' '.$model->APELLIDO_MAT_EMP ?>"> Descargar CV EISEI </a>  <?php
                                } else {
                                    ?> <p class="pInactivo">Descargar CV EISEI</p><?php
                                }
                            ?>
                        </div>
                        <div class="form-group col-lg-12 col-md-6 col-sm-6">
                            <?= Html::label($modelPerfilEmpleados->getAttributeLabel('FK_ESTATUS_RECURSO'),null,['class'=>'campos-label']) ?>
                            <p class="font-bold"><?php echo $modelPerfilEmpleados->FK_ESTATUS_RECURSO ?>
                            <?php if($operacionBaja['TIPO_OPERACION'] != null && $operacionBaja['TIPO_OPERACION'] == 'BAJA'){ ?>
                                <a href="javascript:void(0);" data-toggle="modal" data-target="#ver-mas">Ver m&aacute;s...</a>
                            <?php } ?>
                            </p>
                            <?php if($modelBitComentariosEmpleados>0){
                            ?>
                                <h6 style="background-color: #9A9796; color: white;padding: 3px;float: left;">
                                    <?= $modelBitComentariosEmpleados==1?"Se ha dado de baja una vez":"Se ha dado de baja $modelBitComentariosEmpleados veces"; ?>
                                </h6>
                                <div class="clear"> </div></br>
                            <?php
                            }
                            ?>
                        </div>
                        <?php if(valida_permisos(['ver_campos_monetarios_empleado'])){ ?>
                            <div class="form-group col-lg-12 col-md-6 col-sm-6">
                                <?= Html::label($modelPerfilEmpleados->getAttributeLabel('COSTO_RECURSO'),null,['class'=>'campos-label']) ?>
                                <p class="font-bold">$ <?php echo number_format($modelPerfilEmpleados->COSTO_RECURSO,2) ?></p>
                            </div>
                        <?php } ?>
                        <div class="form-group col-lg-12 col-md-6 col-sm-6">
                            <?= Html::label($modelPerfilEmpleados->getAttributeLabel('FK_UNIDAD_NEGOCIO'),null,['class'=>'campos-label']) ?>
                            <p class="font-bold"><?php echo $modelPerfilEmpleados->FK_UNIDAD_NEGOCIO ?>
                            <?php if($cambiosUnidadesNegocio>1){ ?>
                                <a href="javascript:void(0);" data-toggle="modal" data-target="#ver-mas-unidadnegocio">Ver m&aacute;s...</a>
                            </p>
                                <h6 style="background-color: #9A9796; color: white;padding: 3px;float: left;">
                                    <?= $cambiosUnidadesNegocio==2?"Se ha modificado unidad de negocio una vez":"Se ha modificado la unidad de negocio $cambiosUnidadesNegocio ".(($cambiosUnidadesNegocio==1)?' vez ':" veces "); ?>
                                </h6>
                                <div class="clear"> </div></br>
                            <?php
                            }
                            ?>
                        </div>
                        <div class="form-group col-lg-12 col-md-6 col-sm-6">
                            <?= Html::label($modelPerfilEmpleados->getAttributeLabel('FK_UNIDAD_TRABAJO'),null,['class'=>'campos-label']) ?>
                            <p class="font-bold"><?php echo $modelPerfilEmpleados->FK_UNIDAD_TRABAJO ?></p>
                        </div>

                        <?php
                            if($modelPuestos->PK_PUESTO!=1){
                        ?>
                            <div class="form-group col-lg-12 col-md-6 col-sm-6">
                                <?= Html::label($modelPerfilEmpleados->getAttributeLabel('FK_JEFE_DIRECTO'),null,['class'=>'campos-label']) ?>
                                <p class="font-bold"><?php echo $modelPerfilEmpleados->FK_JEFE_DIRECTO ?></p>
                            </div>
                        <?php
                            }
                        ?>

                        <div class="form-group col-lg-12 col-md-6 col-sm-6">
                            <?= Html::label($modelPerfilEmpleados->getAttributeLabel('FK_ADMINISTRADORA'),null,['class'=>'campos-label']) ?>
                            <p class="font-bold"><?php echo $modelPerfilEmpleados->FK_ADMINISTRADORA ?></p>
                        </div>
                        <div class="form-group col-lg-12 col-md-6 col-sm-6">
                            <?= Html::label($modelPerfilEmpleados->getAttributeLabel('APLICA_BONO_PUNTUALIDAD'),null,['class'=>'campos-label']) ?>
                            <p class="font-bold"><?= $modelPerfilEmpleados->APLICA_BONO_PUNTUALIDAD==1?'SI':'NO'; ?></p>
                        </div>
                        <div class="form-group col-lg-12 col-md-6 col-sm-6">
                            <?= Html::label($modelPerfilEmpleados->getAttributeLabel('ID_EMP_ADMINISTRADORA'),null,['class'=>'campos-label']) ?>
                            <?php if($modelPerfilEmpleados->ID_EMP_ADMINISTRADORA == 'Sin ID Empleado'){ ?>
                                <p class="font-bold" style="color:red"><?php echo $modelPerfilEmpleados->ID_EMP_ADMINISTRADORA ?></p>
                            <?php } else { ?>
                                <p class="font-bold"><?php echo $modelPerfilEmpleados->ID_EMP_ADMINISTRADORA ?></p>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="col-lg-8 col-md-12 col-sm-12 col-xs-12">
                        <div class="row">
                            <div class="form-group col-lg-6 col-md-6 col-sm-6">
                                <?= Html::label($modelPerfilEmpleados->getAttributeLabel('FK_RAZON_SOCIAL'),null,['class'=>'campos-label']) ?>
                                <p class="font-bold"><?php echo $modelPerfilEmpleados->FK_RAZON_SOCIAL ?></p>
                            </div>
                            <div class="form-group col-lg-6 col-md-6 col-sm-6">
                                <?= Html::label($modelPerfilEmpleados->getAttributeLabel('FK_TIPO_SERVICIO'),null,['class'=>'campos-label']) ?>
                                <p class="font-bold"><?php echo $modelPerfilEmpleados->FK_TIPO_SERVICIO ?></p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-lg-6 col-md-6 col-sm-6">
                                <?= Html::label($modelPerfilEmpleados->getAttributeLabel('FK_UBICACION'),null,['class'=>'campos-label']) ?>
                                <p class="font-bold"><?php echo $modelPerfilEmpleados->FK_UBICACION ?></p>
                            </div>
                            <div class="form-group col-lg-6 col-md-6 col-sm-6">
                                <?= Html::label($modelPerfilEmpleados->getAttributeLabel('FK_UBICACION_FISICA'),null,['class'=>'campos-label']) ?>
                                <p class="font-bold"><?php echo $modelPerfilEmpleados->FK_UBICACION_FISICA ?></p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-lg-6 col-md-6 col-sm-6">
                                <?= Html::label($modelPerfilEmpleados->getAttributeLabel('FK_AREA'),null,['class'=>'campos-label']) ?>
                                <p class="font-bold"><?php echo $modelPerfilEmpleados->FK_AREA ?></p>
                            </div>
                            <div class="form-group col-lg-6 col-md-6 col-sm-6">
                                <?= Html::label($modelPerfilEmpleados->getAttributeLabel('FK_PUESTO'),null,['class'=>'campos-label']) ?>
                                <p class="font-bold"><?php echo $modelPerfilEmpleados->FK_PUESTO ?></p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-lg-6 col-md-6 col-sm-6">
                                <?= Html::label($modelPerfilEmpleados->getAttributeLabel('FECHA_INGRESO'),null,['class'=>'campos-label']) ?>
                                <p class="font-bold"><?php echo transform_date($modelPerfilEmpleados->FECHA_INGRESO,'d/m/Y') ?></p>
                            </div>
                            <?php if(valida_permisos(['ver_campos_monetarios_empleado'])){ ?>
                                <div class="form-group col-lg-6 col-md-6 col-sm-6">
                                    <?= Html::label($modelPerfilEmpleados->getAttributeLabel('SUELDO_NETO'),null,['class'=>'campos-label']) ?>
                                    <?php if(count($incidenciasAumentos)>1){
                                            $lenght = count($incidenciasAumentos) - 1;
                                            ?>
                                            <p class="font-bold">$ <?php echo number_format($incidenciasAumentos[$lenght]['VALOR'],2) ?>

                                            <a href="javascript:void(0);" data-toggle="modal" data-target="#ver-mas-sueldos">Ver m&aacute;s...</a>

                                    <?php
                                    }
                                    else{ ?>
                                      <p class="font-bold">$ <?php echo number_format($modelPerfilEmpleados->SUELDO_NETO,2) ?>
                                    <?php
                                    }
                                    ?>
                                    </p>
                                </div>
                            <?php } ?>
                        </div>
                        <div class="row">
                            <div class="form-group col-lg-6 col-md-6 col-sm-6">
                                <?= Html::label($modelPerfilEmpleados->getAttributeLabel('FK_CONTRATO'),null,['class'=>'campos-label']) ?>
                                <p class="font-bold"><?php echo $modelPerfilEmpleados->FK_CONTRATO ?></p>
                            </div>
                            <?php if(valida_permisos(['ver_campos_monetarios_empleado'])){ ?>
                                <div class="form-group col-lg-6 col-md-6 col-sm-6">
                                    <?= Html::label($modelPerfilEmpleados->getAttributeLabel('SUELDO_DIARIO'),null,['class'=>'campos-label']) ?>
                                    <p class="font-bold">$ <?php echo number_format($modelPerfilEmpleados->SUELDO_DIARIO,2) ?></p>
                                </div>
                            <?php } ?>
                        </div>
                        <div class="row">
                            <div class="form-group col-lg-6 col-md-6 col-sm-6">
                                <?= Html::label($modelPerfilEmpleados->getAttributeLabel('FK_DURACION_CONTRATO'),null,['class'=>'campos-label']) ?>
                                <p class="font-bold"><?php echo $modelPerfilEmpleados->FK_DURACION_CONTRATO ?></p>
                            </div>
                            <?php if(valida_permisos(['ver_campos_monetarios_empleado'])){ ?>
                                <div class="form-group col-lg-6 col-md-6 col-sm-6">
                                    <?= Html::label($modelPerfilEmpleados->getAttributeLabel('APORTACION_IMSS'),null,['class'=>'campos-label']) ?>
                                    <p class="font-bold">$ <?php echo number_format($modelPerfilEmpleados->APORTACION_IMSS,2) ?></p>
                                </div>
                            <?php } ?>
                        </div>
                        <div class="row">
                            <?php if(valida_permisos(['ver_campos_monetarios_empleado'])){ ?>
                                <div class="form-group col-lg-6 col-md-6 col-sm-6">
                                    <?= Html::label($modelPerfilEmpleados->getAttributeLabel('APORTACION_INFONAVIT'),null,['class'=>'campos-label']) ?>
                                    <p class="font-bold">$ <?php echo number_format($modelPerfilEmpleados->APORTACION_INFONAVIT,2) ?></p>
                                </div>
                            <?php } ?>
                            <?php if(valida_permisos(['ver_campos_monetarios_empleado'])){ ?>
                                <div class="form-group col-lg-6 col-md-6 col-sm-6">
                                    <?= Html::label($modelPerfilEmpleados->getAttributeLabel('ISR'),null,['class'=>'campos-label']) ?>
                                    <p class="font-bold">$ <?php echo number_format($modelPerfilEmpleados->ISR,2) ?></p>
                                </div>
                            <?php } ?>
                        </div>
                        <div class="row">
                            <div class="form-group col-lg-6 col-md-6 col-sm-6">
                                <?= Html::label($modelPerfilEmpleados->getAttributeLabel('FK_RANK_TECNICO'),null,['class'=>'campos-label']) ?>
                                <p class="font-bold"><?php echo $modelPerfilEmpleados->FK_RANK_TECNICO ?></p>
                            </div>
                            <?php if(valida_permisos(['ver_campos_monetarios_empleado'])){ ?>
                                <div class="form-group col-lg-6 col-md-6 col-sm-6">
                                    <?= Html::label($modelPerfilEmpleados->getAttributeLabel('TARIFA'),null,['class'=>'campos-label']) ?>
                                    <p class="font-bold">$ <?php echo number_format($modelPerfilEmpleados->TARIFA,2) ?></p>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-12 col-sm-12 col-xs-12 padding-top">
                    <h3 class="campos-title font-bold">
                        <?php if(valida_permisos(['empleados/cambiar_documentos'])){ ?>
                            <a href="<?php echo Url::to(["empleados/cambiar_documentos",'id'=>$model->PK_EMPLEADO]); ?>" title="Ver" class="item-update iconview-24x24"></a>
                        <?php } ?>
                        Documentos Personales
                    </h3>

                    <table id="tablaDocumentosentos" class="table table-hover">
                        <thead>
                            <th>Nombre</th>
                            <th>Fecha Creación</th>
                        </thead>

                        <?php
                        foreach($datosDocumentosEmpleados as $arrayDocs)
                        {
                        ?>
                        <tr>
                            <td>
                                <a href="../..<?= $arrayDocs['RUTA_DOCUMENTO'] ?>" download="<?= $model->NOMBRE_EMP.' '.$model->APELLIDO_PAT_EMP.' '.$model->APELLIDO_MAT_EMP.'_'.$arrayDocs['NOMBRE_DOCUMENTO'] ?>"><?= $arrayDocs['NOMBRE_DOCUMENTO'] ?></a>
                            </td>
                            <td><?= transform_date($arrayDocs['FECHA_CREACION'],'d/m/Y') ?></td>
                        </tr>
                        <?php
                        }

                        ?>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<style>
    /*
    Max width before this PARTICULAR table gets nasty
    This query will take effect for any screen smaller than 760px
    and also iPads specifically.
    */
    @media
    only screen and (max-width: 760px),
    (min-device-width: 768px) and (max-device-width: 1024px)  {

        /* Force table to not be like tables anymore */
        table, thead, tbody, th, td, tr {
            display: block;
        }

        /* Hide table headers (but not display: none;, for accessibility) */
        thead tr {
            position: absolute;
            top: -9999px;
            left: -9999px;
        }

        tr { border: 1px solid #ccc; }

        td {
            /* Behave  like a "row" */
            border: none;
            border-bottom: 1px solid #eee;
            position: relative;
            padding-left: 50%!important;
            word-break: break-all;
        }

        td:before {
            /* Now like a table header */
            position: absolute;
            /* Top/left values mimic padding */
            top: 6px;
            left: 6px;
            width: 45%;
            padding-right: 10px;
            white-space: nowrap;
        }

        /*
        Label the data
        */
        td:nth-of-type(1):before { content: "Nombre";  text-align:  center; font-weight: 700;}
        td:nth-of-type(2):before { content: "Fecha Creación";  text-align:  center; font-weight: 700;}
        /*td:nth-of-type(3):before { content: "Motivo";  text-align:  center; font-weight: 700;}*/
        /*td:nth-of-type(4):before { content: "Comentarios"; text-align:  center; font-weight: 700; }*/
       /* td:nth-of-type(5):before { content: "Wars of Trek?"; }
        td:nth-of-type(6):before { content: "Porn Name"; }
        td:nth-of-type(7):before { content: "Date of Birth"; }
        td:nth-of-type(8):before { content: "Dream Vacation City"; }
        td:nth-of-type(9):before { content: "GPA"; }
        td:nth-of-type(10):before { content: "Arbitrary Data"; }*/
    }

    /* Smartphones (portrait and landscape) ----------- */
    /*@media only screen
    and (min-device-width : 320px)
    and (max-device-width : 480px) {
        body {
            padding: 0;
            margin: 0;
            width: 320px; }
        }*/

    /* iPads (portrait and landscape) ----------- */
    /*@media only screen and (min-device-width: 768px) and (max-device-width: 1024px) {
        body {
            width: 495px;
        }
    }*/
</style>

<div class="modal fade" id="ver-mas" tabindex="-1" role="dialog" aria-labelledby="Ver-mas">
    <div class="modal-dialog" role="document">
        <div class="modal-content" style='border-radius: 0px;'>
            <div class="modal-body" style="padding: 0px; position: relative;">
                    <img src="/erteisei/web/iconos/linea - modal - cancelacion.png" width="100%" height="8px" />
                    <div class="clear"></div>
                    <h3 class="modal-title">Comentarios por la baja del empleado</h3>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="well" style="height: 300px; overflow: auto;">
                        <table class="table table-hover" style="font-size:12px; border:1px solid #eaeaea">
                            <thead>
                                <th style="width: 10%" class="text-center">#</th>
                                <th style="width: 15%" class="text-center">Fecha</th>
                                <!-- <th class="text-center">Fecha registro</th> -->
                                <th style="width: 37.5%">Motivo</th>
                                <th style="width: 37.5%">Comentarios</th>
                            </thead>
                            <tbody>
                                <?php
                                    $cont=1;
                                    foreach ($datosComentariosEmpleado as $key => $value) { ?>
                                        <tr>
                                            <td class="text-center"><?php echo $cont ?></td>
                                            <td class="text-center"><?php echo date("d/m/Y", strtotime($value['FECHA_BAJA'])) ?> </td>
                                            <!-- <td class="text-center"><?php echo $value['FECHA_REGISTRO'] ?></td> -->
                                            <td><?php echo $value['MOTIVO'] ?></td>
                                            <td><?php echo $value['COMENTARIOS'] ?></td>
                                        </tr>
                                <?php } ?>
                            </tbody>
                        </table>

                    </div>
                </div>
            </div>
            <div class="row text-center" >
                <button type="button" class="btn btn-cancel" data-dismiss="modal">Cerrar</button>
                <div class="form-group">
                    <div class="help-block" id="res-modal"></div>
                </div>
            </div>

        </div>
    </div>
</div>

<div class="modal fade" id="ver-mas-unidadnegocio" tabindex="-1" role="dialog" aria-labelledby="Ver-mas">
    <div class="modal-dialog" role="document">
        <div class="modal-content" style='border-radius: 0px;'>
            <div class="modal-body" style="padding: 0px; position: relative;">
                    <img src="/erteisei/web/iconos/linea - modal - cancelacion.png" width="100%" height="8px" />
                    <div class="clear"></div>
                    <h3 class="modal-title">Comentarios por el cambio de unidad de negocio del empleado</h3>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="well" style="height: 300px; overflow: auto;">
                        <table class="table table-hover" style="font-size:12px; border:1px solid #eaeaea">
                            <thead>
                                <th style="width: 10%" class="text-center">#</th>
                                <th style="width: 15%" class="text-center">Fecha</th>
                                <!-- <th class="text-center">Fecha registro</th> -->
                                <th style="width: 45%">Empleado</th>
                                <th style="width: 20%">Unidad de negocio</th>
                                <th style="width: 10%">Asignación</th>
                            </thead>
                            <tbody>
                                <?php
                                    $cont=0;
                                    foreach ($resultado as $key => $value) {
                                        $cont++;
                                ?>
                                        <tr>
                                            <td class="text-center"><?php echo $cont ?></td>
                                            <td class="text-center"><?php echo $value['FECHA_CREACION'] ?></td>
                                            <td class="text-center"><?php echo $value['NOMBRE_EMP'].' '.$value['APELLIDO_PAT_EMP'].' '.$value['APELLIDO_MAT_EMP'] ?></td>
                                            <td class="text-center"><?php echo $value['DESC_UNIDAD_NEGOCIO'] ?></td>
                                            <td class="text-center">#<?php echo $value['FK_REGISTRO'] ?></td>
                                        </tr>
                                <?php } ?>
                            </tbody>
                        </table>

                    </div>
                </div>
            </div>
            <div class="row text-center" >
                <button type="button" class="btn btn-cancel" data-dismiss="modal">Cerrar</button>
                <div class="form-group">
                    <div class="help-block" id="res-modal"></div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="ver-mas-sueldos" tabindex="-1" role="dialog" aria-labelledby="Ver-mas">
    <div class="modal-dialog" role="document">
        <div class="modal-content" style='border-radius: 0px;'>
            <div class="modal-body" style="padding: 0px; position: relative;">
                    <img src="/erteisei/web/iconos/linea - modal - cancelacion.png" width="100%" height="8px" />
                    <div class="clear"></div>
                    <h3 class="modal-title">Información de Incidencia de Nómina</h3>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="well" style="height: 300px; overflow: auto;">
                        <table class="table table-hover" style="font-size:12px; border:1px solid #eaeaea">
                            <thead>
                                <th style="width: 10%" class="text-center">#</th>
                                <th style="width: 40%" class="text-center">Tipo Sueldo</th>
                                <th style="width: 15%" class="text-center">Fecha</th>
                                <th style="width: 35%" class="text-center">Valor</th>
                            </thead>
                            <tbody>
                                <?php
                                    $cont=0;
                                    foreach ($incidenciasAumentos as $key => $array) {
                                        $cont++;
                                ?>
                                        <tr>
                                            <td class="text-center"><?= $cont ?></td>
                                            <td class="text-center"><?= $array['FK_TIPO_INCIDENCIA']==12?'SUELDO INICIAL':'ACTUALIZACIÓN DE SUELDO' ?></td>
                                            <td class="text-center"><?= transform_date($array['QUINCENA_APLICAR'],'d/m/Y') ?></td>
                                            <td class="text-center">$ <?= number_format($array['VALOR'],2) ?></td>
                                        </tr>
                                <?php } ?>
                            </tbody>
                        </table>

                    </div>
                </div>
            </div>
            <div class="row text-center" >
                <button type="button" class="btn btn-cancel" data-dismiss="modal">Cerrar</button>
                <div class="form-group">
                    <div class="help-block" id="res-modal"></div>
                </div>
            </div>
        </div>
    </div>
</div>
