<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\jui\DatePicker;

use app\models\tblcatpaises;
use app\models\TblContactos;
use app\models\TblEmpleados;
use app\models\tblcatestados;
use app\models\tblcatmunicipios;
use app\models\tbldomicilios;
use app\models\tblcatgenero;
use app\models\TblCatRazonSocial;
use app\models\TblCatUbicacionRazonSocial;
use app\models\TblCatAdministradoras;
use app\models\TblCatDuracionTipoServicios;
use app\models\TblCatTipoContrato;
use app\models\TblCatTipoServicios;
use app\models\TblCatAreas;
use app\models\TblCatPuestos;
use app\models\TblCatRankTecnico;
use app\models\TblCatUnidadesNegocio;
use app\models\TblCatUnidadTrabajo;
use app\models\TblCatUbicaciones;
/* @var $this yii\web\View */
/* @var $model app\models\tblempleados */
/* @var $form yii\widgets\ActiveForm */

use yii\helpers\Url;
use kartik\select2\Select2;
use yii\web\JsExpression;

$url_paises = \yii\helpers\Url::to(['site/paises']);
$url_estados = \yii\helpers\Url::to(['site/estados']);
$url_municipios = \yii\helpers\Url::to(['site/municipios']);
$url_areas = \yii\helpers\Url::to(['site/areas']);
$url_puestos = \yii\helpers\Url::to(['site/puestos']);

$modelo_cargado='empleados';

$datosDomicilios = ArrayHelper::map(tbldomicilios::find()->asArray()->all(), 'PK_DOMICILIO','COLONIA');
$datosGenero = ArrayHelper::map(tblcatgenero::find()->asArray()->all(), 'PK_GENERO','DESC_GENERO');
$datosCatPaises = ArrayHelper::map(tblcatpaises::find()->asArray()->all(), 'PK_PAIS', 'DESC_PAIS');
$datosRazonSocial = ArrayHelper::map(TblCatRazonSocial::find()->asArray()->all(), 'PK_RAZON_SOCIAL', 'DESC_RAZON_SOCIAL');
$datosUbicacion = ArrayHelper::map(TblCatUbicacionRazonSocial::find()->asArray()->all(), 'PK_UBICACION_RAZON_SOCIAL', 'DESC_UBICACION');
$datosAdministradora = ArrayHelper::map(TblCatAdministradoras::find()->asArray()->all(), 'PK_ADMINISTRADORA', 'NOMBRE_ADMINISTRADORA');
$datosDuracionTipoServicio = ArrayHelper::map(TblCatDuracionTipoServicios::find()->asArray()->all(), 'PK_DURACION', 'DESC_DURACION');
$datosTipoContrato = ArrayHelper::map(TblCatTipoContrato::find()->asArray()->all(), 'PK_TIPO_CONTRATO', 'DESC_TIPO_CONTRATO');
$datosTipoServicios = ArrayHelper::map(TblCatTipoServicios::find()->asArray()->all(), 'PK_TIPO_SERVICIO', 'DESC_TIPO_SERVICIO');
$datosAreas = ArrayHelper::map(TblCatAreas::find()->orderBy(['DESC_AREA'=>SORT_ASC])->asArray()->all(), 'PK_AREA', 'DESC_AREA');
$datosRankTecnico = ArrayHelper::map(TblCatRankTecnico::find()->asArray()->all(), 'PK_RANK_TECNICO', 'DESC_RANK_TECNICO');
$datosUnidadNegocio = ArrayHelper::map(TblCatUnidadesNegocio::find()->asArray()->all(), 'PK_UNIDAD_NEGOCIO', 'DESC_UNIDAD_NEGOCIO');
$datosUnidadTrabajo = ArrayHelper::map(TblCatUnidadTrabajo::find()->asArray()->all(), 'PK_UNIDAD_TRABAJO', 'DESC_UNIDAD_TRABAJO');
$datosUbicacionFisica = ArrayHelper::map(TblCatUbicaciones::find()->where(['=','PROPIA_CLIENTE','Propia'])->asArray()->all(), 'PK_UBICACION', 'DESC_UBICACION');
$datosJefeDirecto = ArrayHelper::map(
                    TblEmpleados::find()
                    ->select(['PK_EMPLEADO',"CONCAT(NOMBRE_EMP,' ',APELLIDO_PAT_EMP,' ',APELLIDO_MAT_EMP) AS NOMBRE"])
                    ->join('LEFT JOIN','tbl_perfil_empleados',
                                'tbl_empleados.PK_EMPLEADO = tbl_perfil_empleados.FK_EMPLEADO')
                    ->join('LEFT JOIN','tbl_cat_puestos',
                                'tbl_perfil_empleados.FK_PUESTO = tbl_cat_puestos.PK_PUESTO')
                    ->where(['tbl_cat_puestos.PERMITIR_SUBORDINADOS' => 1])
                    ->andWhere(['IN','tbl_perfil_empleados.FK_ESTATUS_RECURSO',[1,3,5] ])
                    ->asArray()
                    ->all()
                    , 'PK_EMPLEADO'
                    , 'NOMBRE');
$datosAplicaBono = [0=>'NO', 1=>'SI'];
$datosAdministradoraDummy = ArrayHelper::map(TblCatAdministradoras::find()->asArray()->all(), 'PK_ADMINISTRADORA', 'PORC_COMISION_ADMIN_EMPLEADO');
$colums='col-xs-12 col-sm-6 col-md-3 col-lg-3';
$colums2='col-xs-6 col-sm-2 col-md-2 col-lg-2';
$colums3='col-xs-6 col-sm-2 col-md-2 col-lg-6';
$colums4='col-xs-6 col-sm-6 col-md-6 col-lg-12';
$colums5='col-xs-6 col-sm-12 col-md-12 col-lg-12';

?>

<style type="text/css">

    .modal-content p{
        display:block !important;
        text-align: center; 
        font-weight: bold;
    } 
</style>

<script>
    var ajaxasignacion = '<?php echo Url::to(["$modelo_cargado/create"]); ?>';
   
</script>

<div class="tblempleados-form">
    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data', 'id' => 'form_empleados']]); ?>

        <?php
        foreach ($datosAdministradoraDummy as $key => $value) {
            echo "<input type='hidden' id='administradora_$key' value='$value'/>";
        }
        ?>
        <div class="row form-container">
            <?php 
                if (empty($modelBitAdministradoraEmp))
                {
                ?>
                    <h3 class="campos-title font-bold">
                        Motivos de Contratación
                    </h3>
                    <?= $form->field($modelMotivosContrato, 'MOTIVO_CONTRATACION',
                    [
                    'template' => ' <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">{label}{input}{error}{hint}<div class="clear"></div></div>',
                    ]
                    )->textInput(['maxlength' => true, 'class'=>'form-control', 'onblur'=>"this.value=this.value.toUpperCase()"]) ?> 

                    <?= $form->field($modelMotivosContrato, 'COMENTARIOS',
                    [
                    'template' => ' <div class="'.$colums3.'">{label}{input}{error}{hint}<div class="clear"></div></div>', 
                    ]
                    )->textArea(['maxlength' => true, 'class'=>'form-control', 'onblur'=>"this.value=this.value.toUpperCase()"]) ?> 
            <?php } ?>
            <div class="clear"></div>
        </div>
            
        <div class="row form-container"> <!-- Form captura TBLEMPLEADO -->
           
            <h3 class="campos-title font-bold">
                <div class='circle-row izq'>1</div>Datos personales
            </h3>
            <div class="clear"></div>

            <div class='col-xs-12 col-sm-12 col-md-12 col-lg-3'>                
                <?php /* $form->field($modelSubirFotoEmpleado, '[1]file',
                [
                    'template' => ' <div class="'.$colums4.'">{input}{error}{hint}<div class="clear"></div></div>',
                ]
                )->fileInput(['id'=>"foto-perfil",
                // 'accept'=>"image/png, image/jpeg"

                ]) */?>

                <?= $form->field($modelSubirFotoEmpleado, '[1]file',
                [
                    'template' => ' <div class="'.$colums5.' text-center">{input}<label class="control-label">Foto de perfil</label>{error}{hint}<div class="clear"></div></div>',
                ]
                )->fileInput() ?>

                <?= $form->field($model, 'FOTO_EMP')->hiddenInput(['value' => 'defaultValue',])->label(false); ?>

            </div>
            
            <div class='col-xs-12 col-sm-12 col-md-12 col-lg-9'>
                <div class="row">
                    <?= $form->field($model, 'NOMBRE_EMP',
                        [
                            'template' => ' <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">{label}{input}{error}{hint}<div class="clear"></div></div>',
                        ]
                        )->textInput(['maxlength' => true, 'class'=>'form-control', 'onblur'=>"this.value=this.value.toUpperCase()"]) ?>
                    
                    <?= $form->field($model, 'APELLIDO_PAT_EMP', 
                        [
                            'template' => ' <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">{label}{input}{error}{hint}<div class="clear"></div></div>',
                        ]
                        )->textInput(['maxlength' => true, 'onblur'=>"this.value=this.value.toUpperCase()"]) ?>

                    <?= $form->field($model, 'APELLIDO_MAT_EMP', 
                        [
                            'template' => ' <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">{label}{input}{error}{hint}<div class="clear"></div></div>',
                        ]
                        )->textInput(['maxlength' => true, 'onblur'=>"this.value=this.value.toUpperCase()"]) ?>
                </div>
                    
                <div class="row">
                    <?= 
                        $form->field($model, 'FK_GENERO_EMP',
                        [
                            'template' => ' <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">{label}{input}{error}{hint}<div class="clear"></div></div>',
                        ]
                        )->radioList($datosGenero);
                    ?>

                    <?= $form->field($model, 'FECHA_NAC_EMP',
                        [
                            'template' => ' <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">{label}{input}{error}{hint}<div class="clear"></div></div>',
                        ]
                        )->textInput(['maxlength' => true, 'class' => 'form-control datepicker datepicker-upa', 'onchange'=>'calcular_anios(this);', 'onblur'=>'calcular_anios(this);','placeholder'=>'DD/MM/AAAA'])
                    /*->widget(\yii\jui\DatePicker::classname(), [
                            'clientOptions' => [
                            ],
                            'options' => [
                                'class' => 'form-control datepicker',
                                'onchange'=>'calcular_anios(this);',
                                'onblur'=>'calcular_anios(this);',
                                'placeholder'=>'AAAA/MM/DD',
                                'autocomplete'=>'off'
                                // 'readonly' => 'readonly',
                            ],
                        ]) */
                    ?>

                    <?= $form->field($model, 'NACIONALIDAD_EMP', 
                        [
                            'template' => ' <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">{label}{input}{error}{hint}<div class="clear"></div></div>',
                        ]
                        )->textInput(['maxlength' => true, 'onblur'=>"this.value=this.value.toUpperCase()"]) ?>
                </div>
                <div class="row">

                <div class="form-group field-tblcontactos-pais required">
                            <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">
                                <?= Html::label('Pais de Origen','search',['class'=>'control-label']) ?> 
                                <?php echo Select2::widget([
                                    'value'=>((isset($modelDomicilios))?$modelDomicilios['FK_PAIS']:''),
                                    'data' => $datosCatPaises,
                                    'name'      => 'fk_pais',
                                    'id'      => 'fk_pais',
                                    'model'     => '',
                                    'options' => ['placeholder' => ''],
                                    'pluginOptions' => [
                                        'allowClear' => true,
                                    ],
                                ]); ?>
                                <div class="help-block"></div>
                            <div class="clear"></div></div>
                </div>
                 <div class="form-group field-tblcontactos-estado required">
                            <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">
                                <?= Html::label('Estado Origen','search',['class'=>'control-label']) ?> 
                                <?php echo Select2::widget([
                                    'value'=>$extra['DESC_ESTADO']->DESC_ESTADO,
                                    'name'      => 'fk_estado',
                                    'id'      => 'fk_estado',
                                    'model'     => '',
                                    'options' => ['placeholder' => ''],
                                    'pluginOptions' => [
                                        'allowClear' => true,
                                        'ajax' => [
                                            'url' => $url_estados,
                                            'dataType' => 'json',
                                            'delay' => 250,
                                            'data' => new JsExpression('function(params) { return {q:params.term,p:$("#fk_pais").val()}; }')
                                        ],
                                        'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                                        'templateResult' => new JsExpression('function(data) { return data.text; }'),
                                        'templateSelection' => new JsExpression('function (data) { return data.text; }'),
                                    ],
                                ]); ?>
                                <div class="help-block"></div>
                            <div class="clear"></div></div>
                        </div>
                        <div class="form-group field-tblcontactos-ciudad required">
                            <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">
                                <?= Html::label('Ciudad / Municipio Origen','search',['class'=>'control-label']) ?> 
                                <?php echo Select2::widget([
                                    'value'=>$extra['DESC_MUNICIPIO']->DESC_MUNICIPIO,
                                    'name'      => 'fk_municipio',
                                    'model'     => '',
                                    // 'data' => $datosCatMunicipios,
                                    'options' => ['placeholder' => ''],
                                    'pluginOptions' => [
                                        'allowClear' => true,
                                        // 'minimumInputLength' => 1,
                                        'ajax' => [
                                            'url' => $url_municipios,
                                            'dataType' => 'json',
                                            'delay' => 250,
                                            'data' => new JsExpression('function(params) { return {q:params.term,p:$("#fk_pais").val(),e:$("#fk_estado").val()}; }')
                                        ],
                                        'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                                        'templateResult' => new JsExpression('function(data) { return data.text; }'),
                                        'templateSelection' => new JsExpression('function (data) { return data.text; }'),
                                    ],
                                ]); ?>
                                <div class="help-block"></div>
                            <div class="clear"></div></div>
                        </div>


                    <?= $form->field($model, 'RFC_EMP', 
                        [
                            'template' => ' <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">{label}{input}{error}{hint}<div class="clear"></div></div>',
                        ]
                        )->textInput(['maxlength' => true, 'onblur'=>"this.value=this.value.toUpperCase()"]) ?>

                    <?= $form->field($model, 'CURP_EMP', 
                        [
                            'template' => ' <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">{label}{input}{error}{hint}<div class="clear"></div></div>',
                        ]
                        )->textInput(['maxlength' => true, 'onblur'=>"this.value=this.value.toUpperCase()"]) ?>
                </div>

                <div class="row">
                    <?= $form->field($model, 'NSS_EMP', 
                        [
                            'template' => ' <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">{label}{input}{error}{hint}<div class="clear"></div></div>',
                        ]
                        )->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'EMAIL_EMP', 
                        [
                            'template' => ' <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">{label}{input}{error}{hint}<div class="clear"></div></div>',
                        ]
                        )->textInput(['maxlength' => true]) ?>
                        
                    <?= $form->field($model, 'EMAIL_INTERNO', 
                        [
                            'template' => ' <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">{label}{input}{error}{hint}<div class="clear"></div></div>',
                        ]
                        )->textInput(['maxlength' => true]) ?> 

                    
                </div>
                <div class="row">
                    <?= $form->field($model, 'EMAIL_ASIGNADO', 
                        [
                            'template' => ' <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">{label}{input}{error}{hint}<div class="clear"></div></div>',
                        ]
                        )->textInput(['maxlength' => true]) ?> 
                </div>

                <?= $form->field($model, 'FK_DOMICILIO', 
                    [
                        'template' => '{input}',
                    ]
                    )->hiddenInput()->label(false); ?>

            </div>
        </div>
        <div class="row form-container">  <!-- Form captura TBLDOMICILIOS -->
            
            <h3 class="campos-title font-bold">
                <div class='circle-row izq'>2</div>Domicilio Actual
            </h3>
            <div class="clear"></div>

            <?= $form->field($modelDomicilios, 'CALLE', 
                [
                    'template' => ' <div class="'.$colums.'">{label}{input}{error}{hint}<div class="clear"></div></div>',
                ]
                )->textInput(['maxlength' => true])->label('Calle y Número')  ?>

            <?= $form->field($modelDomicilios, 'COLONIA', 
                [
                    'template' => ' <div class="'.$colums.'">{label}{input}{error}{hint}<div class="clear"></div></div>',
                ]
                )->textInput(['maxlength' => true]) ?>

            <?= $form->field($modelDomicilios, 'CP', 
                [
                    'template' => ' <div class="'.$colums.'">{label}{input}{error}{hint}<div class="clear"></div></div>',
                ]
                )->textInput(['maxlength' => true]) ?>

            <?= $form->field($modelDomicilios, 'FK_PAIS', 
                [
                    'template' => ' <div class="'.$colums.'">{label}{input}{error}{hint}<div class="clear"></div></div>',
                ])->widget(Select2::classname(), [
                'data' => $datosCatPaises,
                'options' => ['placeholder' => ''],
                'pluginOptions' => [
                    'allowClear' => true,
                ],
                ]); ?>

            <?= $form->field($modelDomicilios, 'FK_ESTADO', 
                [
                    'template' => ' <div class="'.$colums.'">{label}{input}{error}{hint}<div class="clear"></div></div>',
                ])->widget(Select2::classname(), [
                'initValueText'=>((isset($extra))?$extra['DESC_ESTADO']->DESC_ESTADO:''),
                // 'data' => $datosCatEstados,
                'options' => ['placeholder' => ''],
                'pluginOptions' => [
                    'allowClear' => true,
                    // 'minimumInputLength' => 1,
                    'ajax' => [
                        'url' => $url_estados,
                        'dataType' => 'json',
                        'delay' => 250,
                        'data' => new JsExpression('function(params) { return {q:params.term,p:$("#tbldomicilios-fk_pais").val()}; }')
                    ],
                    'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                    'templateResult' => new JsExpression('function(data) { return data.text; }'),
                    'templateSelection' => new JsExpression('function (data) { return data.text; }'),
                ],
                ]); ?>

            <?= $form->field($modelDomicilios, 'FK_MUNICIPIO', 
                [
                    'template' => ' <div class="'.$colums.'">{label}{input}{error}{hint}<div class="clear"></div></div>',
                ])->widget(Select2::classname(), [
                'initValueText'=>((isset($extra))?$extra['DESC_MUNICIPIO']->DESC_MUNICIPIO:''),
                // 'data' => $datosCatMunicipios,
                'options' => ['placeholder' => ''],
                'pluginOptions' => [
                    'allowClear' => true,
                    // 'minimumInputLength' => 1,
                    'ajax' => [
                        'url' => $url_municipios,
                        'dataType' => 'json',
                        'delay' => 250,
                        'data' => new JsExpression('function(params) { return {q:params.term,p:$("#tbldomicilios-fk_pais").val(),e:$("#tbldomicilios-fk_estado").val()}; }')
                    ],
                    'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                    'templateResult' => new JsExpression('function(data) { return data.text; }'),
                    'templateSelection' => new JsExpression('function (data) { return data.text; }'),
                ],
                ]); ?>

            <?= $form->field($modelDomicilios, 'TELEFONO', 
                [
                    'template' => ' <div class="'.$colums.' numeric-integer">{label}{input}{error}{hint}<div class="clear"></div></div>',
                ]
                )->textInput(['maxlength' => true]) ?>

            <?= $form->field($modelDomicilios, 'CELULAR', 
                [
                    'template' => ' <div class="'.$colums.' numeric-integer">{label}{input}{error}{hint}<div class="clear"></div></div>',
                ]
                )->textInput(['maxlength' => true]) ?>

            <?= $form->field($modelDomicilios, 'TEL_EMERGENCIA', 
                [
                    'template' => ' <div class="'.$colums.' numeric-integer">{label}{input}{error}{hint}<div class="clear"></div></div>',
                ]
                )->textInput(['maxlength' => true]) ?>

            <?= $form->field($modelDomicilios, 'NUM_EXTERIOR', 
                [
                    'template' => '{input}',
                ]
                )->hiddenInput(['value' => '0'])->label(false); ?>

            <?= $form->field($modelDomicilios, 'NUM_INTERIOR', 
                [
                    'template' => '{input}',
                ]
                )->hiddenInput(['value' => '0'])->label(false); ?>

            <?= $form->field($modelDomicilios, 'PISO', 
                [
                    'template' => '{input}',
                ]
                )->hiddenInput(['value' => '00'])->label(false); ?>
        </div>

         <div class="row form-container">  <!-- Form captura BENEFICIARIOS -->
            <h3 class="campos-title font-bold">
                <div class='circle-row izq'>3</div>Datos de Contacto 
            </h3>
            <div class="clear"></div>

            <?= $form->field($modelAdministradoraDatosPer, 'NOMBRE_PADRE', 
                [
                    'template' => ' <div class="'.$colums.'">{label}{input}{error}{hint}<div class="clear"></div></div>',
                ]
                )->textInput(['maxlength' => true])->label('Nombre del Padre')  ?>

            <?= $form->field($modelAdministradoraDatosPer, 'TEL_PADRE', 
                [
                    'template' => ' <div class="'.$colums.' numeric-integer">{label}{input}{error}{hint}<div class="clear"></div></div>',
                ]
                )->textInput(['maxlength' => true]) ?>

            <?= $form->field($modelAdministradoraDatosPer, 'NOMBRE_MADRE', 
                [
                    'template' => ' <div class="'.$colums.'">{label}{input}{error}{hint}<div class="clear"></div></div>',
                ]
                )->textInput(['maxlength' => true]) ?>


            <?= $form->field($modelAdministradoraDatosPer, 'TEL_MADRE', 
                [
                    'template' => ' <div class="'.$colums.' numeric-integer">{label}{input}{error}{hint}<div class="clear"></div></div>',
                ]
                )->textInput(['maxlength' => true]) ?>

            <?= $form->field($modelAdministradoraDatosPer, 'TIPO_SANGRE', 
                [
                     'template' => ' <div class="'.$colums.'">{label}{input}{error}{hint}<div class="clear"></div></div>',
                ]
                )->textInput(['maxlength' => true]) ?>

            <?= $form->field($modelAdministradoraDatosPer, 'TEL_ACCIDENTE', 
                [
                    'template' => ' <div class="'.$colums.' numeric-integer">{label}{input}{error}{hint}<div class="clear"></div></div>',
                ]
                )->textInput(['maxlength' => true]) ?>                                   
        </div>

        <div class="row form-container">
        <h3 class="campos-title font-bold">
                <div class='circle-row izq'>4</div>Datos del Beneficiario 
        </h3>
                <div class="clear"></div> 
            <?= $form->field($modelAdministradoraBenef, '[1]NOMBRE_BEN', 
                [
                     'template' => ' <div class="'.$colums.'">{label}{input}{error}{hint}<div class="clear"></div></div>',
                ]
                )->textInput(['maxlength' => true])->label('Nombre del Beneficiario') ?>      


            <?= $form->field($modelAdministradoraBenef, '[1]RFC_BEN', 
                [
                     'template' => ' <div class="'.$colums.'">{label}{input}{error}{hint}<div class="clear"></div></div>',
                ]
                )->textInput(['maxlength' => true]) ?>

                <div class="clearfix visible-xs"></div>

            <?= $form->field($modelAdministradoraBenef, '[1]PARENTESCO_BEN', 
                [
                     'template' => ' <div class="'.$colums.'">{label}{input}{error}{hint}<div class="clear"></div></div>',
                ]
                )->textInput(['maxlength' => true]) ?>

            <?= $form->field($modelAdministradoraBenef, '[1]PORCENTAJE', 
                [
                      'template' => ' <div class="'.$colums.' numeric-integer">{label}{input}{error}{hint}<div class="clear"></div></div>',
                ]
                )->textInput(['maxlength' => true]) ?>

                <div class="clearfix visible-lg visible-md visible-xs"></div>
                
            <?= $form->field($modelAdministradoraBenef, '[1]DOMICILIO', 
                [
                     'template' => ' <div class="'.$colums.'">{label}{input}{error}{hint}<div class="clear"></div></div>',
                ]
                )->textInput(['maxlength' => true]) ?>
        </div> 

        <?php 
            if ($SecondmodelBenef!='')
            {
        ?>
        <div class="row form-container">
        <h3 class="campos-title font-bold">
                <div ></div>Datos del Segundo Beneficiario 
        </h3>
                <div class="clear"></div> 
            <?= $form->field($SecondmodelBenef, '[2]NOMBRE_BEN', 
                [
                     'template' => ' <div class="'.$colums.'">{label}{input}{error}{hint}<div class="clear"></div></div>',
                ]
                )->textInput(['maxlength' => true])->label('Nombre del Beneficiario') ?>      

            <?= $form->field($SecondmodelBenef, '[2]RFC_BEN', 
                [
                     'template' => ' <div class="'.$colums.'">{label}{input}{error}{hint}<div class="clear"></div></div>',
                ]
                )->textInput(['maxlength' => true]) ?>

                <div class="clearfix visible-xs"></div>

            <?= $form->field($SecondmodelBenef, '[2]PARENTESCO_BEN', 
                [
                     'template' => ' <div class="'.$colums.'">{label}{input}{error}{hint}<div class="clear"></div></div>',
                ]
                )->textInput(['maxlength' => true]) ?>

            <?= $form->field($SecondmodelBenef, '[2]PORCENTAJE', 
                [
                      'template' => ' <div class="'.$colums.' numeric-integer">{label}{input}{error}{hint}<div class="clear"></div></div>',
                ]
                )->textInput(['maxlength' => true]) ?>

                <div class="clearfix visible-lg visible-md visible-xs"></div>
                
            <?= $form->field($SecondmodelBenef, '[2]DOMICILIO', 
                [
                     'template' => ' <div class="'.$colums.'">{label}{input}{error}{hint}<div class="clear"></div></div>',
                ]
                )->textInput(['maxlength' => true]) ?>
        </div>  
        <?php } ?>

           <div class="row form-container">
            <h3 class="campos-title font-bold">
                <div class='circle-row izq'>5</div>Perfil de Empleado
            </h3>
            <div class="clear"></div>

            <div class='col-xs-12 col-sm-12 col-md-12 col-lg-3'>

                <?= $form->field($modelSubirCVOriginal, '[2]file',
                [
                    'template' => ' <div class="'.$colums4.' text-center">{input}<label class="control-label">Anexar CV Original</label>{error}{hint}<div class="clear"></div></div>',
                ]
                )->fileInput()  ?>

                <?= $form->field($modelSubirCVEISEI, '[3]file',
                [
                    'template' => ' <div class="'.$colums4.' text-center">{input}<label class="control-label">Anexar CV EISEI</label>{error}{hint}<div class="clear"></div></div>',
                ]
                )->fileInput()  ?>

                <?= $form->field($modelPerfilEmpleados, 'CV_ORIGINAL')->hiddenInput(['value' => 'defaultValue'])->label(false); ?>
                <?= $form->field($modelPerfilEmpleados, 'CV_EISEI')->hiddenInput(['value' => 'defaultValue'])->label(false); ?>

            </div>
            
            <div class='col-xs-12 col-sm-12 col-md-12 col-lg-9'>

                <div class="row">
                    <?= $form->field($modelPerfilEmpleados, 'FK_RAZON_SOCIAL', 
                        [
                            'template' => ' <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">{label}{input}{error}{hint}<div class="clear"></div></div>',
                        ])->widget(Select2::classname(), [
                        'data' => $datosRazonSocial,
                        'options' => ['placeholder' => ''],
                        'pluginOptions' => [
                            'allowClear' => true,
                        ],
                        ]); ?>

                    <?= $form->field($modelPerfilEmpleados, 'FK_UBICACION', 
                        [
                            'template' => ' <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">{label}{input}{error}{hint}<div class="clear"></div></div>',
                        ])->widget(Select2::classname(), [
                        'data' => $datosUbicacion,
                        'options' => ['placeholder' => ''],
                        'pluginOptions' => [
                            'allowClear' => true,
                        ],
                        ]); ?>

                    <?= $form->field($modelPerfilEmpleados, 'FK_UBICACION_FISICA', 
                        [
                            'template' => ' <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">{label}{input}{error}{hint}<div class="clear"></div></div>',
                        ])->widget(Select2::classname(), [
                        'data' => $datosUbicacionFisica,
                        'options' => ['placeholder' => ''],
                        'pluginOptions' => [
                            'allowClear' => true,
                        ],
                        ]); ?>
                </div>
                <div class="row">
                    <?= $form->field($modelPerfilEmpleados, 'FK_ADMINISTRADORA', 
                        [
                            'template' => ' <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">{label}{input}{error}{hint}<div class="clear"></div></div>',
                        ])->widget(Select2::classname(), [
                        'data' => $datosAdministradora,
                        'options' => ['placeholder' => '',
                                        'onchange' => 'calcularCostoRecurso()',
                        ],
                        'pluginOptions' => [
                            'allowClear' => true,
                        ],
                        ]); ?>

                    <?= $form->field($modelPerfilEmpleados, 'FECHA_INGRESO',
                        [
                            'template' => ' <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">{label}{input}{error}{hint}<div class="clear"></div></div>',
                        ]
                        )->textInput(['maxlength' => true, 'class' => 'form-control datepicker datepicker-upa','placeholder'=>'DD/MM/AAAA'])
                    ?>

                    <?= $form->field($modelPerfilEmpleados, 'FK_CONTRATO', 
                        [
                            'template' => ' <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">{label}{input}{error}{hint}<div class="clear"></div></div>',
                        ])->widget(Select2::classname(), [
                        'data' => $datosTipoContrato,
                        'options' => ['placeholder' => ''],
                        'pluginOptions' => [
                            'allowClear' => true,
                        ],
                        ]); ?>
                </div>
                <div class="row">
                    <?= $form->field($modelPerfilEmpleados, 'FK_DURACION_CONTRATO', 
                        [
                            'template' => ' <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">{label}{input}{error}{hint}<div class="clear"></div></div>',
                        ])->widget(Select2::classname(), [
                        'data' => $datosDuracionTipoServicio,
                        'options' => ['placeholder' => ''],
                        'pluginOptions' => [
                            'allowClear' => true,
                        ],
                        ]); ?>

                    <?= $form->field($modelPerfilEmpleados, 'FK_TIPO_SERVICIO', 
                        [
                            'template' => ' <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">{label}{input}{error}{hint}<div class="clear"></div></div>',
                        ])->widget(Select2::classname(), [
                        'data' => $datosTipoServicios,
                        'options' => ['placeholder' => ''],
                        'pluginOptions' => [
                            'allowClear' => true,
                        ],
                        ]); ?>

                    <?= $form->field($modelPerfilEmpleados, 'FK_AREA', 
                        [
                            'template' => ' <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">{label}{input}{error}{hint}<div class="clear"></div></div>',
                        ])->widget(Select2::classname(), [
                        //'initValueText'=>((isset($extra))?$extra['DESC_MUNICIPIO']->DESC_ESTADO:''),
                        // 'data' => $datosCatEstados,
                        'options' => ['placeholder' => ''],
                        'pluginOptions' => [
                            'allowClear' => true,
                            // 'minimumInputLength' => 1,
                            'ajax' => [
                                'url' => $url_areas,
                                'dataType' => 'json',
                                'delay' => 250,
                                'data' => new JsExpression('function(params) { return {q:params.term,p:$("#tblperfilempleados-fk_tipo_servicio").val()}; }')
                            ],
                            'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                            'templateResult' => new JsExpression('function(data) { return data.text; }'),
                            'templateSelection' => new JsExpression('function (data) { return data.text; }'),
                        ],
                        ]); ?>
                </div>
                <div class="row">
                    <?= $form->field($modelPerfilEmpleados, 'FK_PUESTO', 
                        [
                            'template' => ' <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">{label}{input}{error}{hint}<div class="clear"></div></div>',
                        ])->widget(Select2::classname(), [
                        //'initValueText'=>((isset($extra))?$extra['DESC_MUNICIPIO']->DESC_ESTADO:''),
                        // 'data' => $datosCatEstados,
                        'options' => ['placeholder' => ''],
                        'pluginOptions' => [
                            'allowClear' => true,
                            // 'minimumInputLength' => 1,
                            'ajax' => [
                                'url' => $url_puestos,
                                'dataType' => 'json',
                                'delay' => 250,
                                'data' => new JsExpression('function(params) { return {q:params.term,p:$("#tblperfilempleados-fk_area").val()}; }')
                            ],
                            'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                            'templateResult' => new JsExpression('function(data) { return data.text; }'),
                            'templateSelection' => new JsExpression('function (data) { return data.text; }'),
                        ],
                        ]);  ?> 

                    <?php /* $form->field($modelPerfilEmpleados, 'FK_PUESTO', 
                        [
                            'template' => ' <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">{label}{input}{error}{hint}<div class="clear"></div></div>',
                        ])->widget(Select2::classname(), [
                        //'initValueText'=>((isset($extra))?$extra['DESC_PUESTO']->DESC_PUESTO:''),
                        'options' => ['placeholder' => ''],
                        'pluginOptions' => [
                            'allowClear' => true,
                        ],
                        ]);*/ ?> 

                    <?= $form->field($modelPerfilEmpleados, 'FK_UNIDAD_NEGOCIO', 
                        [
                            'template' => ' <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">{label}{input}{error}{hint}<div class="clear"></div></div>',
                        ])->widget(Select2::classname(), [
                        'data' => $datosUnidadNegocio,
                        'options' => ['placeholder' => ''],
                        'pluginOptions' => [
                            'allowClear' => true,
                        ],
                        ]); ?>

                    <?= $form->field($modelPerfilEmpleados, 'FK_UNIDAD_TRABAJO', 
                        [
                            'template' => ' <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">{label}{input}{error}{hint}<div class="clear"></div></div>',
                        ])->widget(Select2::classname(), [
                        'data' => $datosUnidadTrabajo,
                        'options' => ['placeholder' => ''],
                        'pluginOptions' => [
                            'allowClear' => true,
                        ],
                        ]); ?>                
                </div>
                <div class="row">
                    <?= $form->field($modelPerfilEmpleados, 'FK_JEFE_DIRECTO', 
                        [
                            'template' => ' <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">{label}{input}{error}{hint}<div class="clear"></div></div>',
                        ])->widget(Select2::classname(), [
                        'data' => $datosJefeDirecto,
                        'options' => ['placeholder' => ''],
                        'pluginOptions' => [
                            'allowClear' => true,
                        ],
                        ]); ?> 

                    <?= $form->field($modelPerfilEmpleados, 'SUELDO_NETO', 
                        [
                            'template' => ' <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4 numeric">{label}{input}{error}{hint}<div class="clear"></div></div>',
                        ]
                        )->textInput(['maxlength' => true, 'onchange' => 'calcularCostoRecurso()']) ?> 

                    <?= $form->field($modelPerfilEmpleados, 'SUELDO_DIARIO', 
                        [
                            'template' => ' <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4 numeric">{label}{input}{error}{hint}<div class="clear"></div></div>',
                        ]
                        )->textInput(['maxlength' => true, 'onchange' => 'calcularCostoRecurso()']) ?>                          
                </div>
                <div class="row">
                    <?= $form->field($modelPerfilEmpleados, 'APORTACION_IMSS', 
                        [
                            'template' => ' <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4 numeric">{label}{input}{error}{hint}<div class="clear"></div></div>',
                        ]
                        )->textInput(['maxlength' => true, 'onchange' => 'calcularCostoRecurso()']) ?>   

                    <?= $form->field($modelPerfilEmpleados, 'APORTACION_INFONAVIT', 
                        [
                            'template' => ' <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4 numeric">{label}{input}{error}{hint}<div class="clear"></div></div>',
                        ]
                        )->textInput(['maxlength' => true, 'onchange' => 'calcularCostoRecurso()']) ?>

                    <?= $form->field($modelPerfilEmpleados, 'ISR', 
                        [
                            'template' => ' <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4 numeric">{label}{input}{error}{hint}<div class="clear"></div></div>',
                        ]
                        )->textInput(['maxlength' => true, 'onchange' => 'calcularCostoRecurso()']) ?> 
                </div>
                <div class="row">
                    <?= $form->field($modelPerfilEmpleados, 'FK_RANK_TECNICO', 
                        [
                            'template' => ' <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">{label}{input}{error}{hint}<div class="clear"></div></div>',
                        ])->widget(Select2::classname(), [
                        'data' => $datosRankTecnico,
                        'options' => ['placeholder' => ''],
                        'pluginOptions' => [
                            'allowClear' => true,
                        ],
                        ]); ?> 
                        
                    <?= $form->field($modelPerfilEmpleados, 'TARIFA', 
                        [
                            'template' => ' <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4 numeric">{label}{input}{error}{hint}<div class="clear"></div></div>',
                        ])->textInput(['readonly' => 'readonly'])
                        //)->textInput(['maxlength' => true]) ?> 
                        
                    <?= $form->field($modelPerfilEmpleados, 'COSTO_RECURSO', 
                        [
                            'template' => ' <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4 numeric">{label}{input}{error}{hint}<div class="clear"></div></div>',
                        ]
                        )->textInput(['readonly' => 'readonly']);  ?> 
                </div>
                <div class="row">
                    <?= $form->field($modelPerfilEmpleados, 'APLICA_BONO_PUNTUALIDAD', 
                        [
                            'template' => ' <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">{label}{input}{error}{hint}<div class="clear"></div></div>',
                        ])->widget(Select2::classname(), [
                        'data' => $datosAplicaBono,
                        'options' => ['placeholder' => ''],
                        'pluginOptions' => [
                            'allowClear' => true,
                        ],
                        ]); ?> 
                </div>
                
                <?= $form->field($modelPerfilEmpleados, 'FECHA_ACTUALIZA', 
                    [
                        'template' => '{input}',
                    ]
                    )->hiddenInput(['value' => 'null'])->label(false); ?>

                <?= $form->field($modelPerfilEmpleados, 'FECHA_REGISTRO', 
                    [
                        'template' => '{input}',
                    ]
                    )->hiddenInput(['value' => 'null'])->label(false); ?>

                <?= $form->field($modelPerfilEmpleados, 'FK_EMPLEADO', 
                    [
                        'template' => '{input}',
                    ]
                    )->hiddenInput(['value' => '0'])->label(false); ?>

                <?= $form->field($modelPerfilEmpleados, 'UBICACION', 
                    [
                        'template' => '{input}',
                    ]
                    )->hiddenInput(['value' => 'null'])->label(false); ?>

                <?= $form->field($modelPerfilEmpleados, 'FK_ESTATUS_RECURSO', 
                    [
                        'template' => '{input}',
                    ]
                    )->hiddenInput(['value' => '3'])->label(false); ?>

                <?= $form->field($modelPerfilEmpleados, 'DURACION_CONTRATO', 
                    [
                        'template' => '{input}',
                    ]
                    )->hiddenInput(['value' => 'null'])->label(false); ?>

                <?= $form->field($modelPerfilEmpleados, 'USUARIO', 
                    [
                        'template' => '{input}',
                    ]
                    )->hiddenInput(['value' => 'null'])->label(false); ?>

                <?= $form->field($modelPerfilEmpleados, 'TIPO_OPERACION', 
                    [
                        'template' => '{input}',
                    ]
                    )->hiddenInput(['value' => 'null'])->label(false); ?>

                <input type="hidden" id="DESC_PUESTO_SELECCIONADO" name="DESC_PUESTO_SELECCIONADO" value="" >
                <input type="hidden" id="EMPLEADO_ASIGNABLE" name="EMPLEADO_ASIGNABLE" value="" >
            </div>
        </div>
        
        <div class="row form-container" style="box-shadow: 0px 0px 0px #FFFFFF;">
            <div class='col-xs-12 col-sm-12 col-md-12 col-lg-12'>
                    <div class='col-xs-12 col-sm-12 col-md-6 col-lg-4'>
                        <input type="hidden" id="idTempArchivos" name="idTempArchivos" value="<?= $extraVals['idTempArchivos']?>" />
                        <input id="sortpicture" type="file" name="sortpic" class="elegirArchivo"/>
                        <div class="help-block subirarchivo"></div>
                    </div>
                    <div class='col-xs-12 col-sm-12 col-md-6 col-lg-4'>
                        <input type="button" name="cargarDocumento" id="cargarDocumento" value="Cargar Documento" disabled="disabled">
                        <div class="help-block"></div>
                    </div>
            </div>
            </br></br>
            <div class='col-xs-12 col-sm-12 col-md-12 col-lg-12'>
                <div class='col-xs-12 col-sm-6 col-md-6 col-lg-2'>
                </div>
                <div class='col-xs-12 col-sm-12 col-md-12 col-lg-8'>
                    <table id="tablaDocumentosentos" class="table table-hover">
                        <tr>
                            <th></th>
                            <th>Tipo de Documento</th>
                            <th>Fecha</th>
                            <th>Usuario</th>
                        </tr>
                    </table>
                    <div class="form-group der">
                        <input type="button" class="btn btn-success" id="btnEliminarDocumento" name="btnEliminarDocumento" value="Eliminar">
                    </div>
                </div>  
                <div class='col-xs-12 col-sm-6 col-md-6 col-lg-2'>
                </div>
            </div>
            <div class='col-xs-12 col-sm-12 col-md-12 col-lg-12'> 
                <div class="form-group der">
                    <br><br><br><br>
                    <input type="hidden" id="datosRepetidos" value="false"/>
                    <?= Html::a('Cancelar', Url::to(['empleados/index']),['class'=>'btn btn-cancel btn-cancel-form']) ?>
                    <?= Html::submitButton( $model->isNewRecord ? 'GUARDAR' : 'MODIFICAR', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-success', 'id'=>'botonGuardar', 'onclick' => 'calcularCostoRecurso();validarAsignaciones();']) ?>
                </div>
            </div>
        </div>
    <?php ActiveForm::end(); ?>
</div>
<input type="hidden" id="valid_post" value="0">
<script type="text/javascript">

$(function() {
    setTimeout(function(){ 
    $('.datepicker').datepicker('remove');

    $('.datepicker-upa').datepicker({
                format: 'dd/mm/yyyy',
                autoclose: true,
                todayBtn: true,
                todayHighlight: true
            })
    }, 500);
});

function calcularCostoRecurso(){

    var administradora = $("#tblperfilempleados-fk_administradora").val();
    var porc_administradora = $("#administradora_"+administradora).val();
    var ISR = $("#tblperfilempleados-isr").val();
    var IMSS = $("#tblperfilempleados-aportacion_imss").val();
    var INFONAVIT = $("#tblperfilempleados-aportacion_infonavit").val();
    var sueldoDiario = $("#tblperfilempleados-sueldo_diario").val();
    var sueldoNeto = $("#tblperfilempleados-sueldo_neto").val();
    //alert("admin="+porc_administradora+" ISR="+ISR+" IMSS="+IMSS+" INFONAVIT="+INFONAVIT+" sueldoDiario="+sueldoDiario);
    if(administradora != '' && ISR != '' && IMSS != '' && INFONAVIT != '' && sueldoDiario != ''){
        
        porc_administradora = parseFloat(porc_administradora);
        ISR = parseFloat(ISR);
        IMSS = parseFloat(IMSS);
        INFONAVIT = parseFloat(INFONAVIT);
        sueldoDiario = parseFloat(sueldoDiario);
        
        var porcAdmon = parseFloat(porc_administradora/100);
        var sueldoNominal = parseFloat(sueldoDiario * 30);
        var sueldoLibre = parseFloat(sueldoNominal - (ISR + IMSS + INFONAVIT));
        var nominaModeloSocial = parseFloat(sueldoNeto - sueldoLibre - INFONAVIT);
        var cargaSocial = parseFloat((sueldoNominal * 0.3265).toFixed(2));
        var totalPeriodo = parseFloat(sueldoNominal + nominaModeloSocial + cargaSocial);
        var factorAdministracion = parseFloat((totalPeriodo * porcAdmon).toFixed(2));
        var subtotal = sueldoNominal + nominaModeloSocial + cargaSocial + factorAdministracion;
        var VAPA = parseFloat((subtotal * 0.14 + 1000).toFixed(2));
        var costorecurso = parseFloat((subtotal + VAPA).toFixed(2));
        
        $("#tblperfilempleados-costo_recurso").attr("value", costorecurso);
        $("#tblperfilempleados-tarifa").attr("value", (costorecurso / 180).toFixed(2));
    } else {
        $("#tblperfilempleados-costo_recurso").attr("value", '');
        $("#tblperfilempleados-tarifa").attr("value", '');
    }
    //Formula para calcular costo del recurso
    /*
    $porcAdministracion = ($modelAdministradora->PORC_COMISION_ADMIN_EMPLEADO)/100;
    $sueldoNominal = $modelPerfilEmpleados->SUELDO_DIARIO * 30;
    $sueldoLibre = $sueldoNominal - ($modelPerfilEmpleados->ISR + $modelPerfilEmpleados->APORTACION_IMSS + $modelPerfilEmpleados->APORTACION_INFONAVIT);
    $nominaoModeloSocial = $sueldoNominal - $sueldoLibre - $modelPerfilEmpleados->APORTACION_INFONAVIT;
    $cargaSocial = $sueldoLibre * 0.32;
    $totalPeriodo = $sueldoNominal + $nominaoModeloSocial + $cargaSocial;
    $factorAdministracion = $totalPeriodo * $porcAdministracion;
    $subtotal = $sueldoNominal + $nominaoModeloSocial + $cargaSocial + $factorAdministracion;
    $VAPA = $subtotal * 0.14 + 1000;
    $costoRecurso = $subtotal + $VAPA;
    */
}

function validarDatos(curp, nss, rfc){
    var validacion = false;
        $.ajax({
            url: '<?php echo Yii::$app->request->baseUrl. '/empleados/validar_campos' ?>',
            type: 'post',
            async: false,
            data: {
                curp: curp , 
                nss: nss,
                rfc: rfc,
                _csrf : '<?=Yii::$app->request->getCsrfToken()?>'
            },
            success: function (data) {
                validacion = data;
            },
            error: function(data){
                validacion = 'error';
            }
        });
    return validacion;
}

function validarAsignaciones(){

    var curp = $("#tblempleados-curp_emp").val();
    var nss = $("#tblempleados-nss_emp").val();
    var rfc = $("#tblempleados-rfc_emp").val();

    $("#datosRepetidos").val('false');
    $("#msjRepetidos").html('');

    var post = true;
    var errorCurp = '<li style="font-weight: bold;">CURP</li>';
    var errorNss = '<li style="font-weight: bold;">NSS</li>';
    var errorRfc = '<li style="font-weight: bold;">RFC</li>';    

    var validador = validarDatos(curp,nss,rfc);
    console.log(validador);

    //Valida si se tiene conexión a internet
    if(validador!='error'){
        //Valida CURP
        if(validador.curpRepetido == true){
            $("#tblempleados-curp_emp").val('');
            $("#msjRepetidos").append(errorCurp);
            $("#datosRepetidos").val('true');
        }

        //Valida NSS
        if(validador.nssRepetido == true){
            $("#tblempleados-nss_emp").val('');
            $("#msjRepetidos").append(errorNss);
            $("#datosRepetidos").val('true');
        }

        //Valida RFC
        if(validador.rfcRepetido == true){
            $("#tblempleados-rfc_emp").val('');
            $("#msjRepetidos").append(errorRfc);
            $("#datosRepetidos").val('true');
        }

        if($("#datosRepetidos").val() == 'true'){
            post = false;
            $("#datos-repetidos").modal('show');
        }
    } else {
            
        post = false;
        $("#modal-conexion").modal('show');
        setTimeout(function() {
            $('#modal-conexion').find('p').show();
        }, 500); 
    }   

    if(post == true && $("#tblperfilempleados-fk_tipo_servicio").val() == 2){
        
        $("#asignacion").modal('show');
        $('#mensaje-modal2').text("¿Este recurso es considerado para cubrir una asignación?");
    }else if(post == true){
        
        $("#form_empleados").submit();
    }

    console.log(post);
    $("#botonGuardar").prop('disabled','true');

    return post;
}

function mandar(){
    $("#form_empleados").submit();
}

$('#sortpicture').on('change', function(){
    var file = $(this).val();
    if(file==''){
        $('#cargarDocumento').prop('disabled',true);
    } else {
        $('#cargarDocumento').prop('disabled',false);
    }
});

$('#cargarDocumento').on('click', function() {
    var file_data = $('#sortpicture').prop('files')[0];
    console.log(file_data);
    $('.elegirArchivo').css({"border":"none", "color":"#000000"});
    $('.subirarchivo').html("");

    if (file_data['size'] > 10000000) {
      $('.elegirArchivo').css({"border":"1px solid red", "color":"red"});
      $('.subirarchivo').append("<span style='font-weight: bold;color: red;'>El archivo debe ser menor a 10MB</span>");
      return;
    }
    //var nombreArchivo = $('#nombreArchivo').val();
    var nombreArchivo = $('#sortpicture').val().replace(/C:\\fakepath\\/i, '');
    var nombreArchivoConExtension = nombreArchivo;
    var posPuntoNombre = nombreArchivo.indexOf(".");
    var nombreArchivo = nombreArchivo.substring(0, posPuntoNombre);
    var rutaNuevaArchivo;
    var form_data = new FormData();     
    var docRepetido = false;      
    //Envio de archivos y variables al nuevo form
    form_data.append('file', file_data);
    form_data.append('nombreArchivo', nombreArchivo);
    form_data.append('idTempArchivos', "<?= $extraVals['idTempArchivos']?>");
    form_data.append('ruta', '../../uploads/EmpleadosTEMP/');
    form_data.append('guardarRegistroBD','0' );

    //console.log(form_data);

    $('input[name^="chk_document_list"]').each(function(){
            var nombreChk = $(this).val();
            if(nombreChk == nombreArchivoConExtension){
                docRepetido = true;
            }
    }); 
     
    if(docRepetido == false) {
        var d = new Date();
        var year = d.getFullYear();
        var month = (d.getMonth()+1);
        var day = d.getDate();
        
        if(month< 10){
            month = "0"+month.toString();
        } else {
            month.toString();
        }

        if(day < 10){
            day = "0"+day.toString();
        } else {
            day.toString();
        }

        var strDate = year + '-'+ month + '-' + day;  

        $.ajax({
                    url: '../../views/empleados/saveFile.php', // point to server-side PHP script 
                    dataType: 'text',  // what to expect back from the PHP script, if anything
                    cache: false,
                    contentType: false,
                    processData: false,
                    data: form_data,                         
                    type: 'post',
                    success: function(php_script_response){
                        //console.log(php_script_response); // display response from the PHP script, if any
                        rutaNuevaArchivo = php_script_response;
                        var obtenerPosPunto = (rutaNuevaArchivo.slice(5)).indexOf(".") + 5;
                        var extensionArchivo = rutaNuevaArchivo.slice(obtenerPosPunto);
                        $("#tablaDocumentosentos").append(
                            "<tr>"+
                                "<td>"+
                                    "<input type='hidden' value='"+rutaNuevaArchivo+"' id='"+nombreArchivo+"' name='"+nombreArchivo+"' />"+
                                    "<input type='hidden' value='"+nombreArchivo+extensionArchivo+"' name='doc_list[]' />"+
                                    "<input type='checkbox' value='"+nombreArchivo+extensionArchivo+"' name='chk_document_list[]' />"+
                                "</td>"+
                                "<td>"+nombreArchivo+"</td>"+
                                "<td>"+strDate+"</td>"+
                                "<td></td>"+
                            "<tr>"
                        );
                        $("#sortpicture").val('');
                        $("#nombreArchivo").val('');
                        $('#cargarDocumento').prop('disabled',true);
                    },
                    error: function (error) {
                        console.log('error; ' + error);
                    }
        });
    //alert("capturado");
    }  else {
        alert("El nombre de documento ya existe");
        $("#nombreArchivo").val('');
    }       
    
});

$('#btnEliminarDocumento').on('click', function() {
    $('input[name^="chk_document_list"]').each(function(){
        var idTemp = '<?= $extraVals["idTempArchivos"]?>';
        if( $(this).is(':checked')) {
            var chkVal = $(this).val();
            var rutaArchivo = '../../uploads/EmpleadosTEMP/'+idTemp+'_'+chkVal;
            var eliminarRegistroBD = 0;
            
            $(this).parent().parent().remove();
            $.ajax({
                data:  "rutaArchivo="+rutaArchivo+"&eliminarRegistroBD="+eliminarRegistroBD,
                url:   '../../views/empleados/deleteFile.php',
                type:  'post',
            });
        } 
    });

});

</script>
<script type="text/javascript">
$(document).ready(function(){

    $("#tblperfilempleados-fecha_ingreso").on('change',function(){
        var fechaIngreso = $(this).val();
        if(fechaIngreso.length==10){
            //Se obtiene y transforma fecha de ingreso
            var arrayFechaIngreso = fechaIngreso.split("/");
            var fechaIngresoTransformada = new Date(arrayFechaIngreso[2],(arrayFechaIngreso[1]-1),arrayFechaIngreso[0]);

            //Se obtiene la fecha de hoy y se calcula la fecha minima de ingreso que se puede capturar en el sistema
            var diasRestarMilisegundos = 86400000 * 16; /*16 dias en milisegundos*/
            var fechaHoy = new Date();
            var fechaHoyMilisegundos = fechaHoy.getTime();
            fechaHoy.setTime(fechaHoyMilisegundos-diasRestarMilisegundos);
                            
            if(fechaIngresoTransformada<fechaHoy){
                $(this).val('');
                $("#fecha-ingreso-incorrecta").modal('show');
                $(".parrafo-modal").show();
            }
        } 
    });

    $("#tblperfilempleados-fk_tipo_servicio").on('change',function(){
        $("#tblperfilempleados-fk_area").val('');
        $("#select2-tblperfilempleados-fk_area-container").html('');
        $("#tblperfilempleados-fk_puesto").val('');
        $("#select2-tblperfilempleados-fk_puesto-container").html('');
    });

    $("#tblperfilempleados-fk_area").on('change',function(){
        $("#tblperfilempleados-fk_puesto").val('');
        $("#select2-tblperfilempleados-fk_puesto-container").html('');
    });

    $("button").click(function(){
        $("p").slideToggle();
    });

    //Llena un input hidden con la descripcion del ultimo puesto seleccionado
    $("#tblperfilempleados-fk_area").on('select2:opening',function(){
        var inputPuestoSeleccionado = $("#DESC_PUESTO_SELECCIONADO");
        var descPuesto = $("#select2-tblperfilempleados-fk_puesto-container").text();
        if(descPuesto.length>0){
            inputPuestoSeleccionado.val(descPuesto.substring(1));
        }
    });

    $("#hacerAsignacion").click(function(){
        $("#EMPLEADO_ASIGNABLE").val(1);
    });
});


</script>

<div class="modal fade" id="asignacion" role="dialog">
    <div class="modal-dialog modal-sm">
        
        <div class="modal-content" style='border-radius: 0px;'>
            <div class="modal-body" style="padding: 0px;">
                <div>
                    <img src="/erteisei_devcap_DES/web/iconos/linea - modal - cancelacion.png" width="100%" height="8px" />

                    <p id="mensaje-modal2"></p>

                    <div class="row" style="margin: 0px; padding: 0px; text-align: center; display:block;">
                    <button type="button" id="hacerAsignacion" class="btn btn-success" data-dismiss="modal" value="1" onclick="mandar()">Si</button>
                    <button type="button" id="btnNo" class="btn btn-success" data-dismiss="modal" value="0" onclick="mandar()">No</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="fecha-empleado" role="dialog">
    <div class="modal-dialog modal-sm">
        
        <div class="modal-content" style='border-radius: 0px;'>
            <div class="modal-body" style="padding: 0px;">
                <div>
                    <img src="/erteisei_devcap_DES/web/iconos/linea - modal - cancelacion.png" width="100%" height="8px" />

                    <p style="text-align: center; font-weight: bold; display:block;">
                    El empleado debe ser mayor a 18 años
                    </p>

                    <div class="row" style="margin: 0px; padding: 0px; text-align: center;">
                    <button type="button" class="btn btn-success" data-dismiss="modal">ACEPTAR</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="fecha-ingreso-incorrecta" role="dialog">
    <div class="modal-dialog modal-sm">
        
        <div class="modal-content" style='border-radius: 0px;'>
            <div class="modal-body" style="padding: 0px;">
                <div>
                    <img src="/erteisei_devcap_DES/web/iconos/linea - modal - cancelacion.png" width="100%" height="8px" />
                    <p style="text-align: center; font-weight: bold; display:block;" class="parrafo-modal">
                    La fecha de ingreso del empleado, no puede tener una antigüedad mayor a 15 dias respecto a la fecha de hoy
                    </p>
                    <div class="row" style="margin: 0px; padding: 0px; text-align: center;">
                    <button type="button" class="btn btn-success" data-dismiss="modal">ACEPTAR</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="datos-repetidos" role="dialog">
    <div class="modal-dialog modal-sm">
       
        <div class="modal-content" style='border-radius: 0px;'>
            <div class="modal-body" style="padding: 0px;">
                <div>
                    <img src="/erteisei_devcap_DES/web/iconos/linea - modal - cancelacion.png" width="100%" height="8px" />

                    <p style="text-align: center; font-weight: bold; display:block; padding: 20px 10px;" >
                        Los siguientes datos ya han sido utilizados en otros empleados:</br>
                    </p>
                    <ul style="list-style-type: square" id="msjRepetidos">

                    </ul>
                    <div class="row" style="margin: 0px; padding: 0px; text-align: center;">
                    <button type="button" class="btn btn-success" data-dismiss="modal" id="ocultarModal">ACEPTAR</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal-conexion" role="dialog">
    <div class="modal-dialog modal-sm">
       
        <div class="modal-content" style='border-radius: 0px;'>
            <div class="modal-body" style="padding: 0px;">
                <div>
                    <img src="/erteisei_devcap_DES/web/iconos/linea - modal - cancelacion.png" width="100%" height="8px" />
                    <p style="text-align: center; font-weight: bold; display:block; padding: 20px 10px;" >
                        La petición no puede ser procesada en este momento, favor de verificar su conexión de internet y contactar a su administrador</br>
                    </p>
                    <div class="row" style="margin: 0px; padding: 0px; text-align: center;">
                    <button type="button" class="btn btn-success" data-dismiss="modal" id="ocultarModal">ACEPTAR</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="cancelar-cambios" role="dialog">
    <div class="modal-dialog modal-sm">
        
        <div class="modal-content" style='border-radius: 0px;'>
            <div class="modal-body" style="padding: 0px;">
                <div>
                    <img src="/erteisei_devcap_DES/web/iconos/linea - modal - cancelacion.png" width="100%" height="8px" />

                    <p id="mensaje" style="text-align: center; font-weight: bold; display:block;">
                    ¿Esta seguro que desea salir sin </br>
                    guardar sus cambios?
                    </p>

                    <div class="row" style="margin: 0px; padding: 0px; text-align: center;">
                    <button type="button" class="btn btn-cancel">CANCELAR</button>
                    <button type="button" class="btn btn-success cancelar-cambios">ACEPTAR</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    jQuery(document).ready(function(){

        //$(".field-tblperfilempleados-fk_ubicacion_fisica").addClass('required');

        //Inhabilita y habilita el campo de jefe directo
        $("#tblperfilempleados-fk_puesto").on('change',function(){
            var PK_PUESTO = $(this).val();
            if(PK_PUESTO==1){
                $("#tblperfilempleados-fk_jefe_directo").val('').change();
                $("#tblperfilempleados-fk_jefe_directo").attr("disabled","disabled");
            } else {
                $("#tblperfilempleados-fk_jefe_directo").removeAttr("disabled");
            }
        });

        $("#tblempleados-rfc_emp").inputmask("aaaa-999999-***");

        $('#form_empleados input').keyup(function(){
            guardarActivar(form_empleados());
        });
        $('#form_empleados input').blur(function(){
            guardarActivar(form_empleados());
        });
        $('#form_empleados select').on('change', function(){
            guardarActivar(form_empleados());
        });
        if($('#form_empleados').length>0){
            guardarActivar(todoVacio);
        }
    });


    var form_empleados = function(){
                
        var elementos = jQuery('#form_empleados input, #form_empleados select');
        
        jQuery(elementos).each(function(index, el){
            var elemento=$(el)
            if(elemento.attr('name')!='_csrf'){
                if($.trim(elemento.val())==''){
                    todoVacio=true;
                    return todoVacio;
                }else{
                    todoVacio=false;
                    return todoVacio;
                }
            } 
        })
        return todoVacio        

    }
</script>

<?php if(isset($_GET['action'])){
        if($_GET['action']=='insert'){
            ?>
            <script>
                jQuery(document).ready(function($) {
                    $('#mensaje').html('Los datos se han guardado correctamente');
                    jQuery('#cancelar-cambios').modal();
                    jQuery('#cancelar-cambios .btn-cancel').hide();
                    jQuery('.btn-cancel-form').attr('href','<?php echo Url::to(["empleados/index"]) ?>')
                });
            </script>
            <?php 
        }
} ?>