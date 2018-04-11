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

    <div class="row form-container" style="box-shadow: 0px 0px 0px #FFFFFF;">
        <div class='col-xs-12 col-sm-6 col-md-6 col-lg-12'>
                <div class='col-xs-12 col-sm-6 col-md-6 col-lg-4'>
                    <input type="hidden" id="idTempArchivos" name="idTempArchivos" value="<?= $extraVals['idTempArchivos']?>" />
                    <input id="sortpicture" type="file" name="sortpic" />
                </div>
                <div class='col-xs-12 col-sm-6 col-md-6 col-lg-4'>
                    <input type="button" name="cargarDocumento" id="cargarDocumento" value="Cargar Documento" disabled="disabled">
                </div>
        </div>
        </br></br>
        <div class='col-xs-12 col-sm-6 col-md-6 col-lg-12'>
            <div class='col-xs-12 col-sm-6 col-md-6 col-lg-2'>
            </div>
            <div class='col-xs-12 col-sm-6 col-md-6 col-lg-8'>
                <table id="tablaDocumentosentos" class="table table-hover">
                    <tr>
                        <th></th>
                        <th>Tipo de Documento</th>
                        <th>Fecha</th>
                        <th>Usuario</th>
                    </tr>
                    <?php 
                        $arrayObjetos = ['.',' '];
                        foreach($modelDocumentosEmpleados as $arrayDocs)
                        {
                    ?>
                        <tr>
                            <td>
                                <input type='hidden' value='../..<?= $arrayDocs['RUTA_DOCUMENTO']?>' id='<?= $model->PK_EMPLEADO."_".$arrayDocs['NOMBRE_DOCUMENTO'] ?>' name='<?= $model->PK_EMPLEADO."_".$arrayDocs['NOMBRE_DOCUMENTO']  ?>' />
                                <input type='hidden' value='<?= $model->PK_EMPLEADO."_".$arrayDocs['NOMBRE_DOCUMENTO'] ?>' name='doc_list[]' />
                                <input type='checkbox' value='<?= $model->PK_EMPLEADO."_".$arrayDocs['NOMBRE_DOCUMENTO']  ?>' name='chk_document_list[]' />
                            </td>
                            <td>
                                <?= $arrayDocs['NOMBRE_DOCUMENTO'] ?>
                            </td>
                            <td>
                                <?= $arrayDocs['FECHA_CREACION'] ?>
                            </td>
                            <td>
                            </td>
                        </tr>
                    <?php
                        }
                    ?>
                </table>
                <div class="form-group der">
                    <input type="button" class="btn btn-success" id="btnEliminarDocumento" name="btnEliminarDocumento" value="Eliminar">
                </div>
            </div>  
        </div>
        <!-- <div class="form-group der">
            <br><br><br><br>
            <input type="hidden" name="postButton" id="postButton" value="0"/>
            <?//= Html::a('Cancelar', Url::to(['empleados/view', 'id' => $model->PK_EMPLEADO]),['class'=>'btn btn-cancel btn-cancel-form']) ?>
            <?//= Html::submitButton($model->isNewRecord ? 'GUARDAR' : 'MODIFICAR', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-success', 'id'=>'botonGuardar', 'name'=>'botonGuardar']) ?>
        </div>  -->
    </div>
    <?php ActiveForm::end(); ?>
</div>

<script type="text/javascript">
$('#botonGuardar').on('click',function(){
    $("#postButton").val(1);
});

$('#cargarDocumento').on('click', function() {
    var file_data = $('#sortpicture').prop('files')[0];  
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
    form_data.append('idTempArchivos', "<?= $model->PK_EMPLEADO ?>");
    form_data.append('ruta', '../../uploads/EmpleadosDocumentos/'); 
    form_data.append('guardarRegistroBD','1' );

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
                    url: '../../../views/empleados/saveFile.php',  // point to server-side PHP script 
                    dataType: 'text',  // what to expect back from the PHP script, if anything
                    cache: false,
                    contentType: false,
                    processData: false,
                    data: form_data,                         
                    type: 'post',
                    success: function(php_script_response){
                        rutaNuevaArchivo = php_script_response;
                        var obtenerPosPunto = (rutaNuevaArchivo.slice(5)).indexOf(".") + 5;
                        var extensionArchivo = rutaNuevaArchivo.slice(obtenerPosPunto);
                        $("#tablaDocumentosentos").append(
                            "<tr>"+
                                "<td>"+
                                    "<input type='hidden' value='"+rutaNuevaArchivo+"' id='<?= $model->PK_EMPLEADO.'_' ?>"+nombreArchivo+extensionArchivo+"' name='<?= $model->PK_EMPLEADO.'_' ?>"+nombreArchivo+extensionArchivo+"' />"+
                                    "<input type='hidden' value='<?= $model->PK_EMPLEADO.'_' ?>"+ nombreArchivo+extensionArchivo+"' name='doc_list[]' />"+
                                    "<input type='checkbox' value='<?= $model->PK_EMPLEADO.'_' ?>"+nombreArchivo+extensionArchivo+"' name='chk_document_list[]' />"+
                                "</td>"+
                                "<td>"+nombreArchivo+"</td>"+
                                "<td>"+strDate+"</td>"+
                                "<td></td>"+
                            "<tr>"
                        );
                        $("#sortpicture").val('');
                        $("#nombreArchivo").val('');
                        $('#cargarDocumento').prop('disabled',true);
                    }
        });
    }  else {
        alert("El nombre de documento ya existe");
        $("#nombreArchivo").val('');
    }            
    
});

$('#sortpicture').on('change', function(){
    var file = $(this).val();
    if(file==''){
        $('#cargarDocumento').prop('disabled',true);
    } else {
        $('#cargarDocumento').prop('disabled',false);
    }
});


$('#btnEliminarDocumento').on('click', function() {
    $('input[name^="chk_document_list"]').each(function(){
        if( $(this).is(':checked')) {
            var rutaArchivo = '../../uploads/EmpleadosDocumentos/'+ $(this).val();
            var eliminarRegistroBD = 1;
            $(this).parent().parent().remove();
            $.ajax({
                data:  "rutaArchivo="+rutaArchivo+"&eliminarRegistroBD="+eliminarRegistroBD,
                url:   '../../../views/empleados/deleteFile.php',
                type:  'post',
                success: function(php_script_response){
                }
            });
        } 
    });
});

</script>
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