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
/* @var $this yii\web\View */
/* @var $model app\models\tblempleados */
/* @var $form yii\widgets\ActiveForm */

use yii\helpers\Url;
use kartik\select2\Select2;
use yii\web\JsExpression;

$colums='col-xs-12 col-sm-6 col-md-6 col-lg-3';
$colums2='col-xs-6 col-sm-2 col-md-2 col-lg-2';
$colums3='col-xs-6 col-sm-2 col-md-2 col-lg-6';
$colums4='col-xs-6 col-sm-2 col-md-2 col-lg-12';
?>

<div class="tblempleados-form">
    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data', 'id' => 'form_empleados']]); ?>

            <div class="row form-container">  <!-- Form captura  -->
                
                <h3 class="campos-title font-bold">
                    <div class='circle-row izq'>4</div>Segundo Beneficiario
                </h3>
                <div class="clear"></div>

                <?= $form->field($SecondmodelBenef, 'NOMBRE_BEN', 
                    [
                        'template' => ' <div class="'.$colums.'">{label}{input}{error}{hint}<div class="clear"></div></div>',
                    ]
                    )->textInput(['maxlength' => true]) ?> 

                <?= $form->field($SecondmodelBenef, 'RFC_BEN', 
                    [
                        'template' => ' <div class="'.$colums.'">{label}{input}{error}{hint}<div class="clear"></div></div>',
                    ]
                    )->textInput(['maxlength' => true]) ?>

                <?= $form->field($SecondmodelBenef, 'PARENTESCO_BEN', 
                    [
                        'template' => ' <div class="'.$colums.'">{label}{input}{error}{hint}<div class="clear"></div></div>',
                    ]
                    )->textInput(['maxlength' => true]) ?>

                <?= $form->field($SecondmodelBenef, 'PORCENTAJE', 
                    [
                        'template' => ' <div class="'.$colums.'">{label}{input}{error}{hint}<div class="clear"></div></div>',
                    ]
                    )->textInput(['maxlength' => true]) ?>

                <?= $form->field($SecondmodelBenef, 'DOMICILIO', 
                    [
                        'template' => ' <div class="'.$colums.'">{label}{input}{error}{hint}<div class="clear"></div></div>',
                    ]
                    )->textInput(['maxlength' => true]) ?>

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
                
        var elementos = jQuery('#form_empleados input');
        
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