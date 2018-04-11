<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\jui\DatePicker;

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
use app\models\TblContactos;
/* @var $this yii\web\View */
/* @var $model app\models\tblempleados */
/* @var $form yii\widgets\ActiveForm */

use yii\helpers\Url;
use kartik\select2\Select2;
use yii\web\JsExpression;

$url_paises = \yii\helpers\Url::to(['site/paises']);
$url_estados = \yii\helpers\Url::to(['site/estados']);
$url_municipios = \yii\helpers\Url::to(['site/municipios']);

$datosDomicilios = ArrayHelper::map(tbldomicilios::find()->asArray()->all(), 'PK_DOMICILIO','COLONIA');
$datosGenero = ArrayHelper::map(tblcatgenero::find()->asArray()->all(), 'PK_GENERO','DESC_GENERO');
$datosCatPaises = ArrayHelper::map(tblcatpaises::find()->asArray()->all(), 'PK_PAIS', 'DESC_PAIS');
$datosRazonSocial = ArrayHelper::map(TblCatRazonSocial::find()->asArray()->all(), 'PK_RAZON_SOCIAL', 'DESC_RAZON_SOCIAL');
$datosUbicacion = ArrayHelper::map(TblCatUbicacionRazonSocial::find()->asArray()->all(), 'PK_UBICACION_RAZON_SOCIAL', 'DESC_UBICACION');
$datosAdministradora = ArrayHelper::map(TblCatAdministradoras::find()->asArray()->all(), 'PK_ADMINISTRADORA', 'NOMBRE_ADMINISTRADORA');
$datosDuracionTipoServicio = ArrayHelper::map(TblCatDuracionTipoServicios::find()->asArray()->all(), 'PK_DURACION', 'DESC_DURACION');
$datosTipoContrato = ArrayHelper::map(TblCatTipoContrato::find()->asArray()->all(), 'PK_TIPO_CONTRATO', 'DESC_TIPO_CONTRATO');
$datosTipoServicios = ArrayHelper::map(TblCatTipoServicios::find()->asArray()->all(), 'PK_TIPO_SERVICIO', 'DESC_TIPO_SERVICIO');
$datosAreas = ArrayHelper::map(TblCatAreas::find()->asArray()->all(), 'PK_AREA', 'DESC_AREA');
$datosPuestos = ArrayHelper::map(TblCatPuestos::find()->asArray()->all(), 'PK_PUESTO', 'DESC_PUESTO');
$datosRankTecnico = ArrayHelper::map(TblCatRankTecnico::find()->asArray()->all(), 'PK_RANK_TECNICO', 'DESC_RANK_TECNICO');
$colums='col-xs-12 col-sm-6 col-md-6 col-lg-3';
$colums2='col-xs-6 col-sm-2 col-md-2 col-lg-2';
$colums3='col-xs-6 col-sm-2 col-md-2 col-lg-6';
$colums4='col-xs-6 col-sm-2 col-md-2 col-lg-12';

?>

<div class="tblempleados-form">
    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data', 'id' => 'form_empleados']]); ?>

            <div class="row form-container">  <!-- Form captura TBLDOMICILIOS -->
                
                <h3 class="campos-title font-bold">
                    <div class='circle-row izq'>2</div>Domicilio Actual
                </h3>
                <div class="clear"></div>

                <?= $form->field($modelDomicilios, 'CALLE', 
                    [
                        'template' => ' <div class="'.$colums.'">{label}{input}{error}{hint}<div class="clear"></div></div>',
                    ]
                    )->textInput(['maxlength' => true])->label('Calle y Número') ?> 

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

                <?= $form->field($modelDomicilios, 'PK_DOMICILIO', 
                    [
                        'template' => '{input}',
                    ]
                    )->hiddenInput()->label(false); ?>   

                <div class="form-group der">
                    <br><br><br><br>
                    <?= Html::a('Cancelar', Url::to(['empleados/view', 'id' => $model->PK_EMPLEADO]),['class'=>'btn btn-cancel btn-cancel-form']) ?>
                    <?= Html::submitButton($model->isNewRecord ? 'GUARDAR' : 'MODIFICAR', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-success', 'id'=>'botonGuardar']) ?>
                </div>             
                
            </div>
    <?php ActiveForm::end(); ?>
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
    jQuery(document).ready(function(){

        //var todoVacio = true;
        //habilitarGuardar();

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