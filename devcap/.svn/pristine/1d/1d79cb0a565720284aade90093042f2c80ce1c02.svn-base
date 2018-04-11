<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\jui\DatePicker;
use yii\web\JsExpression;
use kartik\select2\Select2;

use app\models\tblcatpaises;
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
use app\models\TblCatEstatusRecursos;
use app\models\TblCatUnidadesNegocio;
use app\models\TblCatUnidadTrabajo;
use app\models\TblCatNivel;
use app\models\TblEmpleados;
use app\models\TblCatUbicaciones;
use app\models\TblCatCategoria;
use app\models\TblCatSubcategoria;
use app\models\TblCatPerfiles;
use app\models\TblCatEstadoProspectos;
/* @var $this yii\web\View */
/* @var $model app\models\tblempleados */
/* @var $form yii\widgets\ActiveForm */
$this->title = 'Modificar datos de empleado | '.$model->NOMBRE_EMP.' '.$model->APELLIDO_PAT_EMP.' '.$model->APELLIDO_MAT_EMP;
$this->params['breadcrumbs'][] = ['label' => 'Tblempleados', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->PK_EMPLEADO, 'url' => ['view', 'id' => $model->PK_EMPLEADO]];
$this->params['breadcrumbs'][] = 'Update';

$url_paises = \yii\helpers\Url::to(['site/paises']);
$url_estados = \yii\helpers\Url::to(['site/estados']);
$url_municipios = \yii\helpers\Url::to(['site/municipios']);
$url_areas = \yii\helpers\Url::to(['site/areas']);
$url_puestos = \yii\helpers\Url::to(['site/puestos']);
$url_estatus_recursos = \yii\helpers\Url::to(['site/estatus_recursos']);
$url_subcategorias = \yii\helpers\Url::to(['site/subcategoria']);


$datosDomicilios = ArrayHelper::map(tbldomicilios::find()->asArray()->all(), 'PK_DOMICILIO','COLONIA');
$datosGenero = ArrayHelper::map(tblcatgenero::find()->asArray()->all(), 'PK_GENERO','DESC_GENERO');
$datosCatPaises = ArrayHelper::map(tblcatpaises::find()->asArray()->all(), 'PK_PAIS', 'DESC_PAIS');
$datosCatNivel = ArrayHelper::map(tblcatnivel::find()->asArray()->all(), 'PK_NIVEL', 'DESC_NIVEL');
$datosCatCategoria = ArrayHelper::map(TblCatCategoria::find()->asArray()->all(), 'PK_CATEGORIA', 'DESC_CATEGORIA');
$datosCatPerfiles = ArrayHelper::map(TblCatPerfiles::find()->asArray()->all(), 'PK_PERFIL', 'DESCRIPCION');
$datosEstadoProspecto = ArrayHelper::map(TblCatEstadoProspectos::find()->andWhere(['IN','PK_ESTADO_PROSPECTO',array(1,8)])->asArray()->all(), 'PK_ESTADO_PROSPECTO', 'DESC_ESTADO_PROSPECTO');
$datosRazonSocial = ArrayHelper::map(TblCatRazonSocial::find()->orderBy(['DESC_RAZON_SOCIAL'=>SORT_ASC])->asArray()->all(), 'PK_RAZON_SOCIAL', 'DESC_RAZON_SOCIAL');
$datosUbicacion = ArrayHelper::map(TblCatUbicacionRazonSocial::find()->orderBy(['DESC_UBICACION'=>SORT_ASC])->asArray()->all(), 'PK_UBICACION_RAZON_SOCIAL', 'DESC_UBICACION');
$datosAdministradora = ArrayHelper::map(TblCatAdministradoras::find()->orderBy(['NOMBRE_ADMINISTRADORA'=>SORT_ASC])->asArray()->all(), 'PK_ADMINISTRADORA', 'NOMBRE_ADMINISTRADORA');
$datosDuracionTipoServicio = ArrayHelper::map(TblCatDuracionTipoServicios::find()->orderBy(['DESC_DURACION'=>SORT_ASC])->asArray()->all(), 'PK_DURACION', 'DESC_DURACION');
$datosTipoContrato = ArrayHelper::map(TblCatTipoContrato::find()->orderBy(['DESC_TIPO_CONTRATO'=>SORT_ASC])->asArray()->all(), 'PK_TIPO_CONTRATO', 'DESC_TIPO_CONTRATO');
$datosTipoServicios = ArrayHelper::map(TblCatTipoServicios::find()->orderBy(['DESC_TIPO_SERVICIO'=>SORT_ASC])->asArray()->all(), 'PK_TIPO_SERVICIO', 'DESC_TIPO_SERVICIO');
$datosAreas = ArrayHelper::map(TblCatAreas::find()->orderBy(['DESC_AREA'=>SORT_ASC])->asArray()->all(), 'PK_AREA', 'DESC_AREA');
$datosRankTecnico = ArrayHelper::map(TblCatRankTecnico::find()->orderBy(['DESC_RANK_TECNICO'=>SORT_ASC])->asArray()->all(), 'PK_RANK_TECNICO', 'DESC_RANK_TECNICO');
if(valida_permisos(['baja_empleado'])){
    $datosEstatus = ArrayHelper::map(TblCatEstatusRecursos::find()->orderBy(['DESC_ESTATUS_RECURSO'=>SORT_ASC])->asArray()->all(), 'PK_ESTATUS_RECURSO', 'DESC_ESTATUS_RECURSO');
}else{
    $datosEstatus = ArrayHelper::map(TblCatEstatusRecursos::find()->andWhere(['NOT IN','PK_ESTATUS_RECURSO',array(4,6)])->orderBy(['DESC_ESTATUS_RECURSO'=>SORT_ASC])->asArray()->all(), 'PK_ESTATUS_RECURSO', 'DESC_ESTATUS_RECURSO');
}
$datosUnidadNegocio = ArrayHelper::map(TblCatUnidadesNegocio::find()->orderBy(['DESC_UNIDAD_NEGOCIO'=>SORT_ASC])->asArray()->all(), 'PK_UNIDAD_NEGOCIO', 'DESC_UNIDAD_NEGOCIO');
$datosUnidadTrabajo = ArrayHelper::map(TblCatUnidadTrabajo::find()->orderBy(['DESC_UNIDAD_TRABAJO'=>SORT_ASC])->asArray()->all(), 'PK_UNIDAD_TRABAJO', 'DESC_UNIDAD_TRABAJO');

if($modelPerfilEmpleados->FK_ESTATUS_RECURSO==2){
    $ubicacionesConcatenadas = '';
    //Se valida si existe una asignación creada para continuar flujo aunque tenga estatus 2 de asignado.
    if($modelAsignaciones){
        if($modelAsignaciones[0]['FK_CLIENTE'] == 71 || $modelAsignaciones[0]['FK_CLIENTE'] == 90){
            $ubicacionesConcatenadas = [2,$modelAsignaciones[0]['FK_CLIENTE']];
        }else{
            $ubicacionesConcatenadas = $modelAsignaciones[0]['FK_CLIENTE'];
        }
        $datosUbicacionFisica = ArrayHelper::map(TblCatUbicaciones::find()->where(['=','PROPIA_CLIENTE','cliente'])->andWhere(['IN','FK_CLIENTE', $ubicacionesConcatenadas])->orderBy(['DESC_UBICACION'=>SORT_ASC])->asArray()->all(), 'PK_UBICACION', 'DESC_UBICACION');
    }else{
        $datosUbicacionFisica = ArrayHelper::map(TblCatUbicaciones::find()->where(['=','PROPIA_CLIENTE','PROPIA'])->orderBy(['DESC_UBICACION'=>SORT_ASC])->asArray()->all(), 'PK_UBICACION', 'DESC_UBICACION');
    }
} else {
    $datosUbicacionFisica = ArrayHelper::map(TblCatUbicaciones::find()->where(['=','PROPIA_CLIENTE','PROPIA'])->orderBy(['DESC_UBICACION'=>SORT_ASC])->asArray()->all(), 'PK_UBICACION', 'DESC_UBICACION');
}

$datosJefeDirecto = ArrayHelper::map(
                    TblEmpleados::find()
                    ->select(['PK_EMPLEADO',"CONCAT(NOMBRE_EMP,' ',APELLIDO_PAT_EMP,' ',APELLIDO_MAT_EMP) AS NOMBRE"])
                    ->join('LEFT JOIN','tbl_perfil_empleados',
                                'tbl_empleados.PK_EMPLEADO = tbl_perfil_empleados.FK_EMPLEADO')
                    ->join('LEFT JOIN','tbl_cat_puestos',
                                'tbl_perfil_empleados.FK_PUESTO = tbl_cat_puestos.PK_PUESTO')
                    ->where(['tbl_cat_puestos.PERMITIR_SUBORDINADOS' => 1])
                    ->andWhere('tbl_perfil_empleados.FK_EMPLEADO <>'.$model->PK_EMPLEADO)
                    ->andWhere(['IN','tbl_perfil_empleados.FK_ESTATUS_RECURSO',[1,3,5] ])
                    ->orderBy('NOMBRE')
                    ->asArray()
                    ->all()
                    , 'PK_EMPLEADO'
                    , 'NOMBRE');
$datosAdministradoraDummy = ArrayHelper::map(TblCatAdministradoras::find()->asArray()->all(), 'PK_ADMINISTRADORA', 'PORC_COMISION_ADMIN_EMPLEADO');//Sirve para sacar el % de cada administradora
$datosAplicaBono = [0=>'NO', 1=>'SI'];

$colums='col-xs-12 col-sm-6 col-md-3 col-lg-3';
$colums2='col-xs-6 col-sm-2 col-md-2 col-lg-2';
$colums3='col-xs-6 col-sm-2 col-md-2 col-lg-6';
$colums4='col-xs-6 col-sm-6 col-md-6 col-lg-12';
$colums5='col-xs-6 col-sm-12 col-md-12 col-lg-12';
?>

<div class="col-lg-12">
    <h1 class="title row">
        <div class="col-lg-7">
            <a href="<?php echo Url::to(['empleados/view','id'=>$model->PK_EMPLEADO]); ?>"  class="return-arrow icon-12x21"></a> <?= Html::encode($this->title) ?>
        </div>
    </h1>
    <style type="text/css">
        .datepicker {
            z-index: 999999 !important;
        }
        .form-control[disabled], .form-control[readonly], fieldset[disabled] .form-control{
            background-color: #eeeeee;
        }

        .modal-dialog{
              overflow-y: initial !important
        }
        .modal-body{
          height: 400px;
          overflow-y: auto;
        }
    </style>
<script>
  var ajaxUrlPerfil = '<?php echo Url::to(["empleados/cambiar_perfil", 'id'=>$model->PK_EMPLEADO]); ?>';
</script>
    <div class="tblempleados-form">
        <input type="hidden" id="idPuestoActual" value="<?= $modelPerfilEmpleados->FK_PUESTO ?>">
        <?php
            foreach($modelPuestos as $array){
            ?>
                <input type="hidden" id="idPuesto_<?= $array['PK_PUESTO'] ?>" value="1">
            <?php
            }
        ?>
        <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data', 'id' => 'form_empleados']]); ?>

            <?php
            foreach ($datosAdministradoraDummy as $key => $value) {
                echo "<input type='hidden' id='administradora_$key' value='$value'/>";
            }
            ?>
                <div class="row form-container">
                    <h3 class="campos-title font-bold">
                        <div class='circle-row izq'>5</div>Perfil de Empleado
                    </h3>
                    <div class="clear"></div>
                    <?php
                    if(!valida_permisos(['campos_modificables_UB_empleado']) || !valida_permisos(['campos_modificables_UM_empleado']) || is_super_admin()){//Si el usuario tiene relacionado el item "campos_modificables_UB_empleado" en su rol, solo podra modificar el campo "Bono Puntualidad"
                    ?>
                    <div class='col-xs-12 col-sm-12 col-md-12 col-lg-3'>

                        <?= $form->field($modelSubirCVOriginal, '[2]file',
                        [
                            'template' => ' <div class="'.$colums4.' text-center">{input}<label class="control-label">Anexar CV Original</label>{error}{hint}<div class="clear"></div></div>',
                        ]
                        )->fileInput(['data-file'=>($modelPerfilEmpleados->CV_ORIGINAL)?'../../..'.$modelPerfilEmpleados->CV_ORIGINAL:'', 'data-name_file'=>'CVOriginal_'.$model->NOMBRE_EMP.' '.$model->APELLIDO_PAT_EMP.' '.$model->APELLIDO_MAT_EMP]) ?>

                        <?= $form->field($modelSubirCVEISEI, '[3]file',
                        [
                            'template' => ' <div class="'.$colums4.' text-center">{input}<label class="control-label">Anexar CV EISEI</label>{error}{hint}<div class="clear"></div></div>',
                        ]
                        )->fileInput(['data-file'=>($modelPerfilEmpleados->CV_EISEI)?'../../..'.$modelPerfilEmpleados->CV_EISEI:'', 'data-name_file'=>'CVEISEI_'.$model->NOMBRE_EMP.' '.$model->APELLIDO_PAT_EMP.' '.$model->APELLIDO_MAT_EMP]) ?>

                        <?= $form->field($modelPerfilEmpleados, 'CV_ORIGINAL')->hiddenInput()->label(false); ?>
                        <?= $form->field($modelPerfilEmpleados, 'CV_EISEI')->hiddenInput()->label(false); ?>

                    </div>
                    <?php
                    }//Cierra el if de permisos
                    ?>
                    <div class='col-xs-12 col-sm-12 col-md-12 col-lg-9'>
                        <?php
                        if(!valida_permisos(['campos_modificables_UB_empleado']) || !valida_permisos(['campos_modificables_UM_empleado']) || is_super_admin()){//Si el usuario tiene relacionado el item "campos_modificables_UB_empleado" en su rol, solo podra modificar el campo "Bono Puntualidad"
                        ?>
                            <div class="row">

                            <?= $form->field($modelPerfilEmpleados, 'FK_PUESTO',
                                [
                                    'template' => ' <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">{label}{input}{error}{hint}<div class="clear"></div></div>',
                                ])->widget(Select2::classname(), [
                                'initValueText'=>((isset($extra))?$extra['DESC_PUESTO']->DESC_PUESTO:''),
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
                            ]); ?>

                            <?= $form->field($modelPerfilEmpleados, 'FECHA_INGRESO',
                                    [
                                        'template' => ' <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">{label}{input}{error}{hint}<div class="clear"></div></div>',
                                    ]
                                    )->textInput(['maxlength' => true, 'class' => 'form-control datepicker datepicker-upa','placeholder'=>'DD/MM/AAAA'])
                            ?>

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

                        </div>
                        <div class="row">

                             <?php
                            if(!valida_permisos(['campos_modificables_UB_empleado']) || !valida_permisos(['campos_modificables_UM_empleado']) || is_super_admin()){//Si el usuario tiene relacionado el item "campos_modificables_UB_empleado" en su rol, solo podra modificar el campo "Bono Puntualidad"
                            ?>
                                <?= $form->field($modelPerfilEmpleados, 'FK_ESTATUS_RECURSO',
                                    [
                                        'template' => ' <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">{label}{input}{error}{hint}<div class="clear"></div></div>',
                                    ])->widget(Select2::classname(), [
                                    'initValueText'=>((isset($extra))?$extra['DESC_ESTATUS_RECURSO']->DESC_ESTATUS_RECURSO:''),
                                    // 'data' => $datosCatEstados,
                                    'options' => ['placeholder' => ''],
                                    'pluginOptions' => [
                                        'allowClear' => true,
                                        // 'minimumInputLength' => 1,
                                        'ajax' => [
                                            'url' => $url_estatus_recursos,
                                            'dataType' => 'json',
                                            'delay' => 250,
                                            'data' => new JsExpression('function(params) { return {q:params.term,p:$("#tblperfilempleados-fk_tipo_servicio").val()}; }')
                                        ],
                                        'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                                        'templateResult' => new JsExpression('function(data) { return data.text; }'),
                                        'templateSelection' => new JsExpression('function (data) { return data.text; }'),
                                    ],
                                    ]); ?>

                            <?php
                            }//Cierra el if de permisos
                            ?>

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
                        </div>
                        <div class="row">
                            <?= $form->field($modelPerfilEmpleados, 'FK_UBICACION_FISICA',
                                [
                                    'template' => ' <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">{label}{input}{error}{hint}<div class="clear"></div></div>',
                                ])->widget(Select2::classname(), [
                                'data' => $datosUbicacionFisica,
                                'options' => [
                                    'placeholder' => '',
                                ],
                                'pluginOptions' => [
                                    'allowClear' => true,
                                ],
                                ]); ?>

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
                        </div>
                        <div class="row">
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
                                'initValueText'=>((isset($extra))?$extra['DESC_AREA']->DESC_AREA:''),
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

                            <?= $form->field($modelPerfilEmpleados, 'FK_JEFE_DIRECTO',
                                [
                                    'template' => ' <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">{label}{input}{error}{hint}<div class="clear"></div></div>',
                                ])->widget(Select2::classname(), [
                                'disabled' => (($modelPerfilEmpleados->FK_PUESTO == 1)?'true':''),
                                'data' => $datosJefeDirecto,
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

                            <?= $form->field($modelPerfilEmpleados, 'SUELDO_NETO',
                                [
                                    'template' => ' <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4 numeric">{label}{input}{error}{hint}<div class="clear"></div></div>',
                                ]
                                    )->textInput(['maxlength' => true, 'onchange' => 'calcularCostoRecurso()','disabled' => true]) ?>

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

                            <?= $form->field($modelPerfilEmpleados, 'ISR',
                                [
                                    'template' => ' <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4 numeric">{label}{input}{error}{hint}<div class="clear"></div></div>',
                                ]
                                )->textInput(['maxlength' => true, 'onchange' => 'calcularCostoRecurso()']) ?>

                            <?= $form->field($modelPerfilEmpleados, 'APORTACION_INFONAVIT',
                                [
                                    'template' => ' <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4 numeric">{label}{input}{error}{hint}<div class="clear"></div></div>',
                                ]
                                )->textInput(['maxlength' => true, 'onchange' => 'calcularCostoRecurso()']) ?>

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
                            <?= $form->field($modelPerfilEmpleados, 'COSTO_RECURSO',
                                [
                                    'template' => ' <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4 numeric">{label}{input}{error}{hint}<div class="clear"></div></div>',
                                ]
                                )->textInput(['readonly' => 'readonly']);  ?>

                            <?= $form->field($modelPerfilEmpleados, 'TARIFA',
                                [
                                    'template' => ' <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4 numeric">{label}{input}{error}{hint}<div class="clear"></div></div>',
                                ]
                                )->textInput(['readonly' => 'readonly']) ?>

                            <?php /* $form->field($modelPerfilEmpleados, 'FK_ESTATUS_RECURSO',
                                [
                                    'template' => ' <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">{label}{input}{error}{hint}<div class="clear"></div></div>',
                                ])->widget(Select2::classname(), [
                                'data' => $datosEstatus,
                                'options' => ['placeholder' => ''],
                                'pluginOptions' => [
                                    'allowClear' => true,
                                ],
                                ]);*/ ?>
                        </div>
                        <?php
                        }//Cierra el if de permisos
                        ?>
                        <div class="row">

                            <?= $form->field($modelPerfilEmpleados, 'ID_EMP_ADMINISTRADORA',
                                [
                                    'template' => ' <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">{label}{input}{error}{hint}<div class="clear"></div></div>',
                                ]
                                )->textInput(['maxlength' => true, 'disabled' => false]) ?>
                        </div>

                        <?= $form->field($modelPerfilEmpleados, 'FK_EMPLEADO',
                            [
                                'template' => '{input}',
                            ]
                            )->hiddenInput()->label(false); ?>

                        <?= $form->field($modelPerfilEmpleados, 'UBICACION',
                            [
                                'template' => '{input}',
                            ]
                            )->hiddenInput(['value' => 'null'])->label(false); ?>

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
                        <?php
                            $tipoOperacion = $modelPerfilEmpleados->TIPO_OPERACION!=''?$modelPerfilEmpleados->TIPO_OPERACION:'null';
                        ?>
                        <?= $form->field($modelPerfilEmpleados, 'TIPO_OPERACION',
                            [
                                'template' => '{input}',
                            ]
                            )->hiddenInput(['value' => $tipoOperacion])->label(false); ?>

                        <?= $form->field($modelPerfilEmpleados, 'PK_PERFIL',
                            [
                                'template' => '{input}',
                            ]
                            )->hiddenInput()->label(false); ?>

                        <input type="hidden" id="ASIGNACIONES_PENDIENTES" name="ASIGNACIONES_PENDIENTES"  value="<?= $modelAsignaciones != '' ? count($modelAsignaciones) : 0 ?>">
                        <input type="hidden" id="FK_UNIDAD_NEGOCIO_ANTERIOR" name="FK_UNIDAD_NEGOCIO_ANTERIOR" value="<?= $modelPerfilEmpleados->FK_UNIDAD_NEGOCIO ?>">
                        <input type="hidden" id="FK_UBICACION_FISICA_ANTERIOR" name="FK_UBICACION_FISICA_ANTERIOR" value="<?= $modelPerfilEmpleados->FK_UBICACION_FISICA ?>">
                        <input type="hidden" id="FK_ESTATUS_RECURSO_ANTERIOR" name="FK_ESTATUS_RECURSO_ANTERIOR" value="<?= $modelPerfilEmpleados->FK_ESTATUS_RECURSO; ?>" >
                        <input type="hidden" id="FK_TIPO_SERVICIO_ANTERIOR" name="FK_TIPO_SERVICIO_ANTERIOR" value="<?= $modelPerfilEmpleados->FK_TIPO_SERVICIO; ?>" >
                        <input type="hidden" id="FK_AREA_ANTERIOR" name="FK_AREA_ANTERIOR" value="<?= $modelPerfilEmpleados->FK_AREA; ?>" >
                        <input type="hidden" id="FK_PUESTO_ANTERIOR" name="FK_PUESTO_ANTERIOR" value="<?= $modelPerfilEmpleados->FK_PUESTO; ?>" >
                        <input type="hidden" id="DESC_PUESTO_SELECCIONADO" name="DESC_PUESTO_SELECCIONADO" value="" >
                        <input type="hidden" id="hiddenJefeDirecto" name="hiddenJefeDirecto" value="<?= $modelPerfilEmpleados->FK_JEFE_DIRECTO ?>">
                        <input type="hidden" id="FK_USUARIO" name="FK_USUARIO" value="<?= $_SESSION['usuario']['PK_USUARIO'] ?>">
                        <input type="hidden" id="NOMBRE_EMPLEADO" name="NOMBRE_EMPLEADO" value="<?= $model->NOMBRE_EMP.' '.$model->APELLIDO_PAT_EMP.' '.$model->APELLIDO_MAT_EMP; ?>">
                        <input type="hidden" id="ULTIMA_FECHA_BAJA" name="ULTIMA_FECHA_BAJA" value="<?= $ultimaFechaBaja ?>">
                        <input type="hidden" id="FECHA_INGRESO_ANTERIOR" name="FECHA_INGRESO_ANTERIOR" value="<?= $modelPerfilEmpleados->FECHA_INGRESO; ?>" >
                        <input type="hidden" id="FECHA_ULTIMA_VACACIONES" name="FECHA_ULTIMA_VACACIONES" value="<?= $extra['ultima_fecha_vacaciones']; ?>" >
                    </div>

      <!-- MODAL BAJA EMPLEADOS -->
      <div class="modal fade" id="view-baja-empleado" role="dialog">
        <div class="modal-dialog modal-lg">
          <div class="modal-content" style='border-radius: 0px;'>
            <div class="modal-body" style="padding: 0px;">
              <img src="/erteisei_devcap_DES/web/iconos/linea - modal - cancelacion.png" width="100%" height="8px" />
              <!-- <?php //$form = ActiveForm::begin(['id'=>'form-baja-empleado2', 'action' => Url::to(["empleados/index"])  ]); ?> -->
                <div class="row" style="margin: 0px; padding: 0px;" id="form-baja-empleado">
                    <input type="hidden" id="idEmpleado" name="idEmpleado" value="<?= $model->PK_EMPLEADO?>"/>
                    <div class='col-xs-12 col-sm-12 col-md-12 col-lg-12'>
                        <p id="mensaje" style="font-weight: bold;">
                        Para dar de baja a un empleado se necesita llenar el siguiente formulario.
                        </p>
                        <?= $form->field($modelBitComentariosEmpleados, 'FECHA_BAJA',
                            [
                                'template' => ' <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">{label}{input}{error}{hint}<div class="clear"></div></div>',
                            ]
                            )->textInput(['maxlength' => true, 'class' => 'form-control datepicker datepicker-upa','placeholder'=>'DD/MM/AAAA'])
                        ?>
                    </div>

                    <div class='col-xs-12 col-sm-12 col-md-12 col-lg-12'>
                      <h3 class="campos-title font-bold">Motivo</h3>
                        <?= $form->field($modelBitComentariosEmpleados, 'MOTIVO_CAT',
                        [
                            'template' => ' <div class="clearField col-xs-12 col-sm-6 col-md-4 col-lg-4">{label}<label><font color="red">*</font></label>{input}{error}{hint}<div class="clear"></div></div>',
                        ]
                        )->widget(Select2::classname(), [
                            'data' => $datosCatCategoria,
                            'name'      => 'fk_categoria',
                            'id'      => 'fk_categoria',
                            'model'     => 'model',
                            'options' => ['placeholder' => ''],
                            'pluginOptions' => [
                                'allowClear' => true,
                            ],
                        ]); ?>

                        <?= $form->field($modelBitComentariosEmpleados, 'MOTIVO_SUBCAT',
                        [
                            'template' => ' <div class="col-xs-12 col-sm-6 col-md-4 col-lg-4">{label}{input}{error}{hint}<div class="clear"></div></div>',
                        ]
                        )->widget(Select2::classname(), [
                        'options' => ['placeholder' => ''],
                        'pluginOptions' => [
                            'allowClear' => true,
                            'ajax' => [
                                'url' => $url_subcategorias,
                                'dataType' => 'json',
                                'delay' => 250,
                                'data' => new JsExpression('function(params) { return {q:params.term,p:$("#tblbitcomentariosempleados-motivo_cat").val()}; }')
                            ],
                            'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                            'templateResult' => new JsExpression('function(data) { return data.text; }'),
                            'templateSelection' => new JsExpression('function (data) { return data.text; }'),
                        ],
                        ]); ?>

                        <?= $form->field($modelBitComentariosEmpleados, 'COMENTARIOS',
                        [
                            'template' => ' <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">{label}<label><font color="red">*</font></label>{input}{error}{hint}<div class="clear"></div></div>',
                        ]
                        )->textarea(['maxlength' => true, 'value' => ''])?>

                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">

                          <?= $form->field($modelProspectos, 'FK_ESTADO')->radio(['label' => 'Apto para ser prospecto', 'class' => 'apto', 'checked' => 'checked', 'value' => 1, 'uncheck' => null]) ?>
                          <!-- <input id="apto" type="checkbox" name="" value=""><label>Apto para ser prospecto</label> -->
                        </div>

                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" id="datosContratable">
                          <h3 class="campos-title font-bold">Experiencia adquirida</h3>

                          <?= $form->field($modelProspectosPerfiles, 'FK_PERFIL',
                          [
                              'template' => ' <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3 ContenedorNivel">{label}{input}{error}{hint}<div class="clear"></div></div>',
                          ]
                          )->widget(Select2::classname(), [
                              'data' => $datosCatPerfiles,
                              'name'      => 'fk_perfil',
                              'id'      => 'fk_perfil',
                              'model'     => 'model',
                              //'maintainOrder' => true,
                              'options' => ['placeholder' => '', 'multiple' => true],
                              'pluginOptions' => [
                                'tags' => true,
                                //'allowClear' => true,
                              ],
                          ]); ?>


                          <?= $form->field($modelProspectos, 'CAPACIDAD_RECURSO',
                          [
                              'template' => ' <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3">{label}{input}{error}{hint}<div class="clear"></div></div>',
                          ]
                          )->widget(Select2::classname(), [
                              'data' => ['Bueno' => 'Bueno', 'Regular' => 'Regular', 'Malo' => 'Malo'],
                              'model'     => 'model',
                              'options' => ['placeholder' => ''],
                              'pluginOptions' => [
                              'allowClear' => true,
                              ],
                          ]); ?>

                          <?= $form->field($modelProspectos, 'TACTO_CLIENTE',
                          [
                              'template' => ' <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3">{label}{input}{error}{hint}<div class="clear"></div></div>',
                          ]
                          )->widget(Select2::classname(), [
                              'data' => ['Bueno' => 'Bueno', 'Regular' => 'Regular', 'Malo' => 'Malo'],
                              'model'     => 'model',
                              'options' => ['placeholder' => ''],
                              'pluginOptions' => [
                              'allowClear' => true,
                              ],
                          ]); ?>

                          <?= $form->field($modelProspectos, 'DESEMPENIO_CLIENTE',
                          [
                              'template' => ' <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3">{label}{input}{error}{hint}<div class="clear"></div></div>',
                          ]
                          )->widget(Select2::classname(), [
                              'data' => ['Bueno' => 'Bueno', 'Regular' => 'Regular', 'Malo' => 'Malo'],
                              'model'     => 'model',
                              'options' => ['placeholder' => ''],
                              'pluginOptions' => [
                              'allowClear' => true,
                              ],
                          ]); ?>

                          <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                            <h3 class="campos-title font-bold">Nivel</h3>
                          </div>
                      <?= Html::beginForm(Url::to(["empleados/cambiar_perfil", 'id'=>$model->PK_EMPLEADO]),'post',['class' => 'form', 'id' => 'formDatosNivel']); ?>
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" id="contenedor-nivel"></div>
                      <?= Html::endForm(); ?>


                      <div class="selectPerfiles hidden" id="nivel_perfil">
                        <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4" id="nivelEmpleado">
                          <label class="perfiles"></label>
                          <select class="form-control" id="nivelPerfil" name="nivelPerfil[]">
                            <?php foreach ($datosRankTecnico as $key => $value): ?>
                              <?php echo "<option value='" . $key . "'>". $value ."</option>" ?>
                            <?php endforeach; ?>
                          </select>
                        </div>
                      </div>
                    </div>

                          <!-- Datos de No apto -->
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                          <?= $form->field($modelProspectos, 'FK_ESTADO')->radio(['label' => 'No apto para ser prospecto', 'class' => 'Noapto', 'value' => 8, 'uncheck' => null]) ?>
                          <!-- <input id="NoContratable" type="checkbox" name="" value=""><label>No apto para ser prospecto</label> -->
                        </div>

                          <div class='col-xs-12 col-sm-12 col-md-12 col-lg-12' id="datos-noContratable">
                              <?= $form->field($modelProspectos, 'COMENTARIOS',
                              [
                                  'template' => ' <div class="col-xs-12 col-sm-12 col-md-6 col-lg-12">{label}{input}{error}{hint}<div class="clear"></div></div>',
                              ]
                              )->textarea(['maxlength' => true, 'rows' => '3']) ?>
                          </div>

                      </div>
                    </div>
                  </div>
                  <div class="modal-footer">
                    <div class="row" style="margin: 0px; padding: 0px;">
                        <?= Html::submitButton('ACEPTAR', ['class' => 'btn btn-success der', 'id'=>'botonEnviar']) ?>
                        <button type="button" class="btn btn-success der" data-dismiss="modal">Cancelar</button>
                        <!--<a href="javascript: void(0);" type="button" class="btn btn-success" data-dismiss="modal" data-target="#cancelar-asignacion">ACEPTAR</a>-->
                    </div>
                    <!-- <?php// ActiveForm::end(); ?> -->
                  </div>
                </div>
          </div>
      </div>
            <!-- MODAL EMPLEADOS A CARGO -->
            <div class="modal fade" id="empleados-cargo" role="dialog">
            <div class="modal-dialog modal-lg">
            <div class="modal-content" style='border-radius: 0px;'>
              <div class="modal-body" style="padding: 0px; border:1px solid #dddddd; width:100%; height:300px; overflow: scroll;">
                  <div>
                      <img src="/erteisei_devcap_DES/web/iconos/linea - modal - cancelacion.png" width="100%" height="8px" />
                      <p id="mensaje-baja-asignacion" style="text-align: center; font-weight: bold;">
                      No es posible realizar esta accion debido a que este empleado
                      tiene personal a su cargo favor de asignarles un Jefe Directo
                      </p>
                      <?php
                          foreach ($datosEmpleadosACargo as $array){
                              $nuevosJefes = $datosJefeDirecto;
                              if(array_key_exists($array['PK_EMPLEADO'],$nuevosJefes)){
                                  unset($nuevosJefes[$array['PK_EMPLEADO']]);
                              }
                      ?>
                          <div class="row" style="margin: 0px; padding: 0px;">
                              <div class='col-xs-12 col-sm-12 col-md-6 col-lg-6'>
                                  <?= $array['NOMBRE_EMP'].' '.$array['APELLIDO_PAT_EMP'].' '.$array['APELLIDO_MAT_EMP']  ?>
                              </div>
                              <div class='col-xs-12 col-sm-12 col-md-6 col-lg-6'>
                                  <?= $form->field($modelPerfilEmpleados, 'FK_JEFE_DIRECTO',
                                      [
                                          'template' => '{input}{error}{hint}<div class="clear"></div>',
                                      ])->widget(Select2::classname(), [
                                      'data' => $nuevosJefes,
                                      'options' => [
                                          'placeholder' => '',
                                          'class' => 'select2-nuevo_jefe',
                                          'name' => "jefe_directo[$array[PK_EMPLEADO]]",
                                          'initValueText' => '',
                                          ],
                                      'pluginOptions' => [
                                          'allowClear' => true,
                                      ],
                                  ]); ?>
                              </div>
                          </div>
                      <?php
                          }
                      ?>
                      <div class="row" style="margin: 0px; padding: 0px; text-align: center;">
                          <?= Html::submitButton('ACEPTAR', ['class' => 'btn btn-success der', 'id'=>'botonEnviar2', 'disabled' => true,]) ?>
                          <button type="button" class="btn btn-success der" data-dismiss="modal">Cancelar</button>
                      </div>
                  </div>
              </div>
            </div>
            </div>
            </div>
            <div class="form-group der">
            <br><br><br><br>
            <?= Html::a('Cancelar', Url::to(['empleados/view', 'id' => $model->PK_EMPLEADO]),['class'=>'btn btn-cancel btn-cancel-form']) ?>
            <?= Html::submitButton($model->isNewRecord ? 'GUARDAR' : 'MODIFICAR', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-success', 'id'=>'botonGuardar', 'onclick' => 'calcularCostoRecurso();']) ?>
            </div>
            </div>
            <?php ActiveForm::end(); ?>
            </div>
</div>
<div class="modal fade" id="fecha-ingreso-incorrecta" role="dialog">
    <div class="modal-dialog modal-sm">
        <!-- Modal content-->
        <div class="modal-content" style='border-radius: 0px;'>
            <div class="modal-body" style="padding: 0px;">
                <div>
                    <img src="/erteisei_devcap_DES/web/iconos/linea - modal - cancelacion.png" width="100%" height="8px" />
                    <p style="text-align: center; font-weight: bold;">
                    La fecha de ingreso del empleado solo se puede modificar en un rango de 15 dias respecto a la fecha de ingreso actual
                    </p>
                    <div class="row" style="margin: 0px; padding: 0px; text-align: center;">
                    <button type="button" class="btn btn-success" data-dismiss="modal">ACEPTAR</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="fecha-ingreso-afecta-vacaciones" role="dialog">
    <div class="modal-dialog modal-sm">
        <!-- Modal content-->
        <div class="modal-content" style='border-radius: 0px;'>
            <div class="modal-body" style="padding: 0px;">
                <div>
                    <img src="/erteisei_devcap_DES/web/iconos/linea - modal - cancelacion.png" width="100%" height="8px" />
                    <p style="text-align: center; font-weight: bold;">
                    Este empleado ya recibio sus vacaciones este año, introducir la fecha de ingreso anterior, hara que al empleado se le asignen de nuevo sus dias de vacaciones.
                    </p>
                    <div class="row" style="margin: 0px; padding: 0px; text-align: center;">
                    <button type="button" class="btn btn-success" data-dismiss="modal">ACEPTAR</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="reingreso-fecha-ingreso" role="dialog">
    <div class="modal-dialog modal-sm">
        <!-- Modal content-->
        <div class="modal-content" style='border-radius: 0px;'>
            <div class="modal-body" style="padding: 0px;">
                <div>
                    <img src="/erteisei_devcap_DES/web/iconos/linea - modal - cancelacion.png" width="100%" height="8px" />
                    <p id="mensaje-baja-asignacion" style="text-align: center; font-weight: bold;">
                    La ultima fecha de baja del empleado es <b><?= $ultimaFechaBaja?></b>, la nueva fecha de ingreso debe ser posterior, favor de verificar.
                    </p>
                    <div class="row" style="margin: 0px; padding: 0px; text-align: center;">
                    <button type="button" class="btn btn-cancel" data-dismiss="modal">CANCELAR</button>
                    <button type="button" class="btn btn-success" data-dismiss="modal">ACEPTAR</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="baja-asignacion" role="dialog">
    <div class="modal-dialog modal-sm">
        <!-- Modal content-->
        <div class="modal-content" style='border-radius: 0px;'>
            <div class="modal-body" style="padding: 0px;">
                <div>
                    <img src="/erteisei_devcap_DES/web/iconos/linea - modal - cancelacion.png" width="100%" height="8px" />
                    <p id="mensaje-baja-asignacion" style="text-align: center; font-weight: bold;">
                    Este dato no es modificable debido a que tiene asignaciones con estatus "Pendiente" o "En Ejecución", favor de contactar de Oficina de Proyectos
                    </p>
                    <div class="row" style="margin: 0px; padding: 0px; text-align: center;">
                    <button type="button" class="btn btn-cancel" data-dismiss="modal">CANCELAR</button>
                    <button type="button" class="btn btn-success" data-dismiss="modal">ACEPTAR</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="cancelar-cambios" role="dialog">
    <div class="modal-dialog modal-sm">
        <!-- Modal content-->
        <div class="modal-content" style='border-radius: 0px;'>
            <div class="modal-body" style="padding: 0px;">
                <div>
                    <img src="/erteisei_devcap_DES/web/iconos/linea - modal - cancelacion.png" width="100%" height="8px" />
                    <p id="mensaje" style="text-align: center; font-weight: bold;">
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

var cantEmpleados = <?= count($datosEmpleadosACargo) ?>;
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
    if(porc_administradora != '' && ISR != '' && IMSS != '' && INFONAVIT != '' && sueldoDiario != ''){

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
    $cargaSocial = $sueldoNominal * 0.32;
    $totalPeriodo = $sueldoNominal + $nominaoModeloSocial + $cargaSocial;
    $factorAdministracion = $totalPeriodo * $porcAdministracion;
    $subtotal = $sueldoNominal + $nominaoModeloSocial + $cargaSocial + $factorAdministracion;
    $VAPA = $subtotal * 0.14 + 1000;
    $costoRecurso = $subtotal + $VAPA;
    */
}

function envioCorreoIntentoBaja(){
    var idEmpleado = $("#tblperfilempleados-fk_empleado").val();
    var FK_UNIDAD_NEGOCIO_ANTERIOR = $("#FK_UNIDAD_NEGOCIO_ANTERIOR").val();
    var NOMBRE_EMPLEADO = $("#NOMBRE_EMPLEADO").val();
        $.ajax({
            url: '<?php echo Yii::$app->request->baseUrl. '/empleados/envio_correo_intento_baja' ?>',
            type: 'post',
            data: {
                idEmpleado: idEmpleado ,
                FK_UNIDAD_NEGOCIO_ANTERIOR : FK_UNIDAD_NEGOCIO_ANTERIOR,
                NOMBRE_EMPLEADO : NOMBRE_EMPLEADO,
                _csrf : '<?=Yii::$app->request->getCsrfToken()?>'
            },
            success: function (data) {
                console.log(data);
            },
        });
}

function validarReingresoEmpleado(){
    var ultimaFechaBaja = $("#ULTIMA_FECHA_BAJA").val();
    var FECHA_INGRESO = $("#tblperfilempleados-fecha_ingreso").val();
    var FECHA_INGRESO_ANTERIOR = $("#FECHA_INGRESO_ANTERIOR").val();
    if(ultimaFechaBaja!='' && FECHA_INGRESO!=''){
        var fechaBajaConv = new Date(reverse_date_ddmmYY(ultimaFechaBaja)).getTime();
        var fechaIngresoConv = new Date(reverse_date_ddmmYY(FECHA_INGRESO)).getTime();
        if(fechaBajaConv!='NaN' && fechaIngresoConv!='NaN'){
            if(fechaBajaConv >= fechaIngresoConv){
                $("#tblperfilempleados-fecha_ingreso").val('');
                $("#reingreso-fecha-ingreso").modal('show');
                validacion = false;
            }
            else{
                validacion = true;
            }
        }else{
            validacion = false;
        }
    }else{
            validacion = false;
        }
    return validacion;
}

function isEmpty( el ){
     return !$.trim(el.html())
 }


jQuery(document).ready(function(){

  var DATA = [];
  var FK_PERFIL = [];

  $("#tblprospectosperfiles-fk_perfil").on('select2:select',function(){
      FK_PERFIL = $("#tblprospectosperfiles-fk_perfil").val();
      console.log('ADD' + FK_PERFIL);
      var NAME = [];
      if (isEmpty($('#contenedor-nivel'))) {
        $('#nivel_perfil select').attr('data-perfil', FK_PERFIL[0]);
          NAME[0] = $('#tblprospectosperfiles-fk_perfil option[value=' + FK_PERFIL[0] + ']').text();
          $('.selectPerfiles .perfiles').text(NAME[0]);
        $('#contenedor-nivel').html($('#nivel_perfil').html());
          DATA[0] = $('#contenedor-nivel select').attr('data-perfil');
          $('#nivel_perfil select').removeAttr('data-perfil');
      }
        else {
          console.log('ELSE' + FK_PERFIL);
          console.log('ELSE DATA' + DATA);
          $('#contenedor-nivel select').each(function(k) {

          $.each(FK_PERFIL, function(key){
              if ($.inArray(FK_PERFIL[key], DATA) === -1)
                  {
                    $('#nivel_perfil select').attr('data-perfil', FK_PERFIL[key]);
                      var DATAPERFIL = DATA.length;
                      DATA[DATAPERFIL] = FK_PERFIL[key];
                      NAME = $('#tblprospectosperfiles-fk_perfil option[value=' + FK_PERFIL[key] + ']').text();
                      $('.selectPerfiles .perfiles').text(NAME);
                    $('#contenedor-nivel').append($('#nivel_perfil').html());
                    $('#nivel_perfil select').removeAttr('data-perfil');
                  }
                });
              });
            }
        });

        $("#tblprospectosperfiles-fk_perfil").on("select2:unselect", function (e) {
          // console.log(e.params.data.id);
          var unselect = e.params.data.id;
          $('select[data-perfil =' + unselect +' ]').parent().remove();
          for (var i =0; i < DATA.length; i++)
           if (DATA[i] === unselect) {
              DATA.splice(i,1);
              //break;
           }
          //  for (var i =0; i < FK_PERFIL.length; i++)
          //   if (FK_PERFIL[i] === unselect) {
          //      FK_PERFIL.splice(i,1);
          //      //break;
          //   }
        FK_PERFIL = $('#tblprospectosperfiles-fk_perfil').val();
        console.log('QUITAR DATA' + DATA);
        console.log('QUITAR PERFILES ' + FK_PERFIL);
       });

$(".apto").attr('checked', 'checked');

$("#datos-noContratable *").prop('disabled', true);
    $('.apto').click(function(){
      $("#datos-noContratable *").prop('disabled', true);
      $("#datosContratable *").prop('disabled', false);
    });

    $('.Noapto').click(function(){
      $("#datos-noContratable *").prop('disabled', false);
      $("#datosContratable *").prop('disabled', true);
    });

    //Evento change de fecha de ingreso
    $("#tblperfilempleados-fecha_ingreso").on('change',function(){
        //COMENTADO PARA RESTAURACION INFO DEL EMPLEADO
        /*var FK_ESTATUS_RECURSO = $("#tblperfilempleados-fk_estatus_recurso").val();
        var FK_ESTATUS_RECURSO_ANTERIOR = $("#FK_ESTATUS_RECURSO_ANTERIOR").val();
        if((FK_ESTATUS_RECURSO_ANTERIOR==4 || FK_ESTATUS_RECURSO_ANTERIOR==6) && FK_ESTATUS_RECURSO!=4 && FK_ESTATUS_RECURSO!=6){
            validarReingresoEmpleado();
        }else{
            var fechaIngresoNueva = $(this).val();
            var fechaIngresoOriginal = $("#FECHA_INGRESO_ANTERIOR").val();
            if(fechaIngresoNueva.length==10 && fechaIngresoNueva!=fechaIngresoOriginal){
                //Se obtiene y transforma fecha de ingreso nueva
                var arrayFechaIngreso = fechaIngresoNueva.split("/");
                var fechaIngresoTransformada = new Date(arrayFechaIngreso[2],(arrayFechaIngreso[1]-1),arrayFechaIngreso[0]);
                var fechaIngresoMilisegundos = fechaIngresoTransformada.getTime();

                //Se obtiene y transforma fecha de ingreso original
                var arrayFechaIngresoOriginal = fechaIngresoOriginal.split("/");
                var fechaIngresoOriginalTransformada = new Date(arrayFechaIngresoOriginal[2],(arrayFechaIngresoOriginal[1]-1),arrayFechaIngresoOriginal[0]);
                var fechaIngresoOriginalMilisegundos = fechaIngresoOriginalTransformada.getTime();

                //Se obtiene la fecha de hoy
                var fechaHoy = new Date();
                var fechaHoyMilisegundos = fechaHoy.getTime();
                var anioActual = fechaHoy.getFullYear();

                //Se obtiene la ultima fecha en que se le dieorn vacaciones al empleado
                var ultimaFechaVacaciones = $("#FECHA_ULTIMA_VACACIONES").val();
                var ultimoAnioVacaciones = ultimaFechaVacaciones.substring(0,4);
                if(ultimoAnioVacaciones==anioActual){//Si al empleado ya se le dieron vacaciones el año actual, se valida para que la nueva fecha de ingreso que se le, siempre sea menor a la fecha de hoy, en cuanto a dis y mes
                    var booleanEmpleadoDuplicarVacaciones = false;
                    var nuevaFechaIngresoAnioActual = new Date(anioActual,(arrayFechaIngreso[1]-1),arrayFechaIngreso[0]);
                    if(nuevaFechaIngresoAnioActual>=fechaHoy){
                        booleanEmpleadoDuplicarVacaciones = true;
                    }
                }*/

                //Se hacen calculos para obtener de la fecha original en un rango de 16 dias antes y 16 dias despues
            //    var diasRestarMilisegundos = 86400000 * 16; /*16 dias en milisegundos*/
            /*    var fechaOriginalRangoMenor = new Date();
                var fechaOriginalRangoMayor = new Date();
                fechaOriginalRangoMenor.setTime(fechaIngresoOriginalMilisegundos-diasRestarMilisegundos);
                fechaOriginalRangoMayor.setTime(fechaIngresoOriginalMilisegundos+diasRestarMilisegundos);

                if(booleanEmpleadoDuplicarVacaciones){
                    $(this).val('');
                    $("#fecha-ingreso-afecta-vacaciones").modal('show');
                }else if(fechaIngresoTransformada<fechaOriginalRangoMenor || fechaIngresoTransformada>fechaOriginalRangoMayor){
                    $(this).val('');
                    $("#fecha-ingreso-incorrecta").modal('show');
                }
            }
        }   */
    });

    //Boton guardar de formulario
    $("#botonGuardar").click(function(){
        var statusOriginal = "<?= $modelPerfilEmpleados->FK_ESTATUS_RECURSO; ?>";
        var status = $("#tblperfilempleados-fk_estatus_recurso").val();
        var cant_asignaciones_pendientes = $('#ASIGNACIONES_PENDIENTES').val();
        var idPuestoAntiguo = $("#idPuestoActual").val();
        var idPuestoNuevo = $("#tblperfilempleados-fk_puesto").val();
        var PERMITIR_SUBORDINADOS = 0;
        if($("#idPuesto_"+idPuestoNuevo).length){
            PERMITIR_SUBORDINADOS = 1;
        }
        if($("#tblperfilempleados-fk_estatus_recurso").length > 0){//Se valida que el
            if((status==4 || status==6) && statusOriginal!=status && ((statusOriginal!=4 && status==6) || (statusOriginal!=6 && status==4)) ){
                if(cant_asignaciones_pendientes>0){
                    envioCorreoIntentoBaja();
                    $("#tblperfilempleados-fk_estatus_recurso").val(statusOriginal).change();
                    $('#baja-asignacion').modal('show');
                    return false;
                } else {
                    $('#view-baja-empleado').modal('show');
                    $('#tblbitcomentariosempleados-fecha_baja').datepicker('setDate',new Date());
                    return false;
                }
           } else {
                if(cantEmpleados>0 && PERMITIR_SUBORDINADOS==0){
                    $("#empleados-cargo").modal('show');
                    var posicion = $("#empleados-cargo").offset().top;
                    $('html,body').animate({scrollTop: posicion}, 0);
                    $(".select2-nuevo_jefe").show();
                    $(".select2-nuevo_jefe").val('');
                    return false;
                } else {
                    return true;
                }
           }
       } else {
            return true;
       }

    });

    $("#botonEnviar").click(function(){
        if(cantEmpleados>0){
            $('#view-baja-empleado').modal('hide');
            $("#empleados-cargo").modal('show');
            var posicion = $("#empleados-cargo").offset().top;
            $('html,body').animate({scrollTop: posicion}, 0);
            $(".select2-nuevo_jefe").show();
            $(".select2-nuevo_jefe").val('');
            return false;
        } else {
            return true;
        }
        nivelPerfil();
    });

    $(".select2-nuevo_jefe").on('change',function(){
        var contadorFaltantes = 0;
        $(".select2-nuevo_jefe").each(function (index){
            if($(this).val()==""){
                contadorFaltantes++;
            }
        });
        if(contadorFaltantes > 0){
            $("#botonEnviar2").prop('disabled',true);
        } else {
            $("#botonEnviar2").prop('disabled',false);
        }
    });
    
    $("#tblbitcomentariosempleados-motivo_cat").on('change',function(){

        $('#tblbitcomentariosempleados-motivo_subcat').val('');
        $("#select2-tblbitcomentariosempleados-motivo_subcat-container").html('');
    
    });

    //Evento que se ejecuta cuando se selecciona un valor en el select de unidad de negocio
    $("#tblperfilempleados-fk_unidad_negocio").on('select2:close',function(){
        var FK_UNIDAD_NEGOCIO = $('#tblperfilempleados-fk_unidad_negocio').val();
        var FK_UNIDAD_NEGOCIO_ANTERIOR = $("#FK_UNIDAD_NEGOCIO_ANTERIOR").val();
        var FK_ESTATUS_RECURSO_ANTERIOR = $("#FK_ESTATUS_RECURSO_ANTERIOR").val();
        if(FK_ESTATUS_RECURSO_ANTERIOR!=3 && FK_UNIDAD_NEGOCIO != FK_UNIDAD_NEGOCIO_ANTERIOR){
            $("#tblperfilempleados-fk_unidad_negocio").val(FK_UNIDAD_NEGOCIO_ANTERIOR).change();
            $('#baja-asignacion').modal('show');
        }
    });

    //Evento que se ejecuta cuando se selecciona un valor en el select de ubicacion fisica
    $("#tblperfilempleados-fk_ubicacion_fisica").on('select2:close',function(){
        var FK_UBICACION_FISICA = $('#tblperfilempleados-fk_ubicacion_fisica').val();
        var FK_UBICACION_FISICA_ANTERIOR = $("#FK_UBICACION_FISICA_ANTERIOR").val();
        var FK_ESTATUS_RECURSO_ANTERIOR = $("#FK_ESTATUS_RECURSO_ANTERIOR").val();
        if(FK_ESTATUS_RECURSO_ANTERIOR!=3 && FK_UBICACION_FISICA != FK_UBICACION_FISICA_ANTERIOR){
            $("#tblperfilempleados-fk_ubicacion_fisica").val(FK_UBICACION_FISICA_ANTERIOR).change();
            $('#baja-asignacion').modal('show');
        }
    });

    //Evento que se ejecuta cuando se selecciona un valor en el select de estatus de recurso
    $("#tblperfilempleados-fk_estatus_recurso").on('select2:close',function(){
        var FK_ESTATUS_RECURSO = $("#tblperfilempleados-fk_estatus_recurso").val();
        var FK_ESTATUS_RECURSO_ANTERIOR = $("#FK_ESTATUS_RECURSO_ANTERIOR").val();
        var bolValidaReingreso = false;
        bolValidaReingreso = validarReingresoEmpleado();
        if((FK_ESTATUS_RECURSO_ANTERIOR==4 || FK_ESTATUS_RECURSO_ANTERIOR==6) && !bolValidaReingreso){
            $("#tblperfilempleados-fk_estatus_recurso").val(FK_ESTATUS_RECURSO_ANTERIOR).change();
            $('#reingreso-fecha-ingreso').modal('show');
            envioCorreoIntentoBaja();
        }
    });

    //Evento que se ejecuta cuando se selecciona un valor en el select de tipo de Servicio
    $("#tblperfilempleados-fk_tipo_servicio").on('select2:close',function(){
        var FK_TIPO_SERVICIO = $("#tblperfilempleados-fk_tipo_servicio").val();
        var FK_TIPO_SERVICIO_ANTERIOR = $("#FK_TIPO_SERVICIO_ANTERIOR").val();
        var FK_ESTATUS_RECURSO_ANTERIOR = $("#FK_ESTATUS_RECURSO_ANTERIOR").val();
        if(FK_ESTATUS_RECURSO_ANTERIOR!=3 && FK_TIPO_SERVICIO != FK_TIPO_SERVICIO_ANTERIOR){
            $("#tblperfilempleados-fk_tipo_servicio").val(FK_TIPO_SERVICIO_ANTERIOR).change();
            $('#baja-asignacion').modal('show');
        } else {
            if(FK_TIPO_SERVICIO != FK_TIPO_SERVICIO_ANTERIOR){
                $("#tblperfilempleados-fk_area").val('');
                $("#select2-tblperfilempleados-fk_area-container").html('');
                $("#tblperfilempleados-fk_puesto").val('');
                $("#select2-tblperfilempleados-fk_puesto-container").html('');
                $("#tblperfilempleados-fk_estatus_recurso").val('');
                $("#select2-tblperfilempleados-fk_estatus_recurso-container").html('');
            }
        }
    });

    //Llena un input hidden con la descripcion del ultimo puesto seleccionado
    $("#tblperfilempleados-fk_area").on('select2:opening',function(){
        var inputPuestoSeleccionado = $("#DESC_PUESTO_SELECCIONADO");
        var descPuesto = $("#select2-tblperfilempleados-fk_puesto-container").text();
        if(descPuesto.length>0){
            inputPuestoSeleccionado.val(descPuesto.substring(1));
        }
    });

    $("#tblperfilempleados-fk_puesto").on('change',function(){
        var valor = $(this).val();
        if(valor == 1){
            $("#tblperfilempleados-fk_jefe_directo").val('');
            $("#tblperfilempleados-fk_jefe_directo").change();
            $("#tblperfilempleados-fk_jefe_directo").prop('disabled',true);
        } else {
            $("#tblperfilempleados-fk_jefe_directo").prop('disabled',false);
        }
        var jefeDirecto = $("#tblperfilempleados-fk_jefe_directo").val();
        $("#hiddenJefeDirecto").val(jefeDirecto);
    });

    $("#tblperfilempleados-fk_jefe_directo").on('change',function(){
        var jefeDirecto = $("#tblperfilempleados-fk_jefe_directo").val();
        $("#hiddenJefeDirecto").val(jefeDirecto);
    });

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
            //
        })
        return todoVacio
    }
</script>
