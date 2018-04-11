<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use app\models\TblCatPuestos;
use app\models\TblCatEstatusRecursos;
use app\models\TblCatAdministradoras;
use app\models\TblCatUbicacionRazonSocial;
use app\models\TblCatUnidadesNegocio;
use app\models\TblCatUbicaciones;
use yii\jui\DatePicker;

$modelo_cargado='empleados';
$datosCatPuestos = ArrayHelper::map(TblCatPuestos::find()->orderBy(['DESC_PUESTO'=>SORT_ASC])->asArray()->all(), 'PK_PUESTO', 'DESC_PUESTO');
$this->title = ' Candidatos Pendientes por Contratar';
$this->params['breadcrumbs'][] = $this->title;
?>
<script>
    var ajaxUrl =           '<?php echo Url::to(["$modelo_cargado/index_pendientes"]); ?>';
    var _csrfToken =        '<?=Yii::$app->request->getCsrfToken()?>';
    var url_view =          '<?php echo Url::to(["$modelo_cargado/view",'id'=>'lorem']); ?>';
    var url_pendientes =    '<?php echo Url::to(["$modelo_cargado/create",'id'=>'lorem']); ?>';
    var url_update =        '<?php echo Url::to(["$modelo_cargado/update",'id'=>'lorem']); ?>';
    var url_delete =        '<?php echo Url::to(["$modelo_cargado/delete",'id'=>'lorem']); ?>';
    var historial = 'empleados/index_pendientes';
    if(!localStorage.getItem(historial)){
        localStorage.setItem(historial, JSON.stringify([]));
    }
</script>

<style type="text/css">
.datepicker
{
    z-index: 99999 !important;
}
</style>
<div class="row">
    <h1 class="title col-lg-12"><a href="<?php echo Url::to(['empleados/index']); ?>"  class="return-arrow icon-12x21"></a><b><?= Html::encode($this->title) ?></b>
    </h1>
    <div class="clear"></div>

    <div class="col-lg-12">
        <div class="resultados ">
            <div class="campos">
                <h3 class="campos-title font-bold">B&uacute;squeda <a href="javascript:void(0)" class="arrow-event item-up-arrow icon-12x21 der"></a></h3>
                <?= Html::beginForm(Url::to(["$modelo_cargado/index_pendientes"]),'post',['class' => 'form', 'id'=>'form-ajax','style'=>'display:none','data-history'=>'true']); ?>
                    <div class="row">
                            <div class="form-group col-lg-2 col-md-4 col-sm-4">
                                <?= Html::label('Nombre','search',['class'=>'campos-label']) ?>
                                <?= Html::input('text','nombre',null,['id'=>'search','class'=>'search form-control']) ?>
                            </div>
                            <div class="form-group col-lg-2 col-md-4 col-sm-4">
                                <?= Html::label('Apellido Paterno','search',['class'=>'campos-label']) ?>
                                <?= Html::input('text','aPaterno',null,['id'=>'search','class'=>'search form-control']) ?>
                            </div>
                            <div class="form-group col-lg-2 col-md-4 col-sm-4">
                                <?= Html::label('Apellido Materno','search',['class'=>'campos-label']) ?>
                                <?= Html::input('text','aMaterno',null,['id'=>'search','class'=>'search form-control']) ?>
                            </div>
                            <div class="form-group col-lg-12 col-md-12 col-sm-12">
                                <?= Html::submitButton('BUSCAR',['class'=>'btn btn-success btn-buscar der']) ?>
                                <?= Html::resetButton('LIMPIAR', ['class' => 'btn btn-success reset btn-buscar der', 'onclick'=>'javascript:buscar_registros()']) ?>
                                <div class="form-group text-center der">
                                    <div class="pagination-sm" style="margin:0 15px;" id="top-pg"></div><p class="paginas text-center"></p>
                                </div>
                            </div>
                    </div>
                    <div class="clear"></div>
                <?= Html::endForm(); ?>
            </div>
            <div class="clear"></div>

            <div class="contenedor-parent row">
                <div class="contenedor empleados">
                    <div class="clear"></div>
                </div>
                <div class="form-group col-lg-3 der">
                    <form id="form-excel" name="form-excel" action="../../views/empleados/excel_index.php" method="post" target="_blank">
                        <input type="hidden" id="excel-nombre" name="excel-nombre" value="">
                        <input type="hidden" id="excel-aPaterno" name="excel-aPaterno" value="">
                        <input type="hidden" id="excel-idPuesto" name="excel-idPuesto" value="">
                        <input type="hidden" id="excel-idUbicacion" name="excel-idUbicacion" value="">
                        <input type="hidden" id="excel-administradora" name="excel-administradora" value="">
                        <input type="hidden" id="excel-ingresoFecha" name="excel-ingresoFecha" value="">
                        <input type="hidden" id="excel-estatusEmpleado" name="excel-estatusEmpleado" value="">
                        <input type="hidden" id="excel-unidadNegocio" name="excel-unidadNegocio" value="">
                        <input type="hidden" id="excel-estatusEmpleado" name="excel-estatusEmpleado" value="">
                        <input type="hidden" id="excel-idUbicacionFisica" name="excel-idUbicacionFisica" value="">
                    </form>
                </div>
            </div>

            <div class="clear"></div>
            <div class="campos">
                <div class="form-group col-lg-12 col-md-12 col-sm-12">
                    <div class="form-group col-lg-12 col-md-12 col-sm-12 text-center">
                        <div class="pagination-sm" id="bottom-pg"></div><p class="paginas text-center"></p>
                    </div>

                </div>
                <div class="clear"></div>
            </div>
        </div>
    </div>
</div>

<iframe  id = "iframe"  style = " display : none ; " ></iframe>
    <style>
    .resultados .empleados .contenedor-item
    {
        min-height: 168px;
    }
    .resultados .contenedor .contenedor-item
    {
        height: auto;
    }
    .resultados .contenedor .contenedor-item:hover .cv_original
    {
        padding: 15px;
    }
    .resultados .contenedor .contenedor-item:hover> .row
    {
        position: relative;
        top: 0;
        left: 0;
        /* padding: 35px 0; */
        background-color: #fff;
        /*z-index: 3;*/
        box-shadow: 0px 0px 5px 3px #DDD;
        width: auto;
        height: auto;
    }
    .margin-card
    {
        margin:5px 0;
    }
    </style>

<script>
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
    function crear_elemento(data)
    {
    mensaje= 'No existen elementos que cuenten con los criterios especificados para los parámetros de búsqueda';
                var html='<div class="contenedor-item col-lg-12 col-md-12 col-sm-12 col-xs-12">'+
                        '<div class="col-lg-3 col-md-4 col-sm-4 col-xs-12" >'+
                            '<a href="<?php echo Url::to(['empleados/create']) ?>?id='+data.PK_REGISTRO+'">'+
                            '<div style="background:url(<?php echo get_upload_url() ?>'+((data.FOTO_EMP!='defoult'&&data.FOTO_EMP!=''&&data.FOTO_EMP!=null)?data.FOTO_EMP:'/uploads/EmpleadosFotos/sin_perfil.png')+');height: 100px;width: 100px;background-size: cover;background-position: 50% 0%;background-repeat: no-repeat; margin: 10px auto; border: 1px solid #ccc;"></div>'+
                            '</a>'+
                            '<div class="col-lg-12 text-center">'+
                                '<div class="col-lg-12">'+
                                data.CV+

                                '</div>'+
                            '</div>'+
                        '</div>'+
                        '<div class="col-lg-9 col-md-8 col-sm-8 col-xs-12" style="margin-top:5px">'+
                            '<div class="form-group font-bold">'+
                                '<a href="<?php echo Url::to(['empleados/create']) ?>?id='+data.PK_REGISTRO+'">'+
                                '<p class="razon_social">'+data.NOMBRE+' '+data.APELLIDO_PATERNO+' '+data.APELLIDO_MATERNO+'</p>'+
                                '</a>'+
                                '<div class="row">'+
                                    '<div class="col-lg-3 col-md-6 col-sm-12 col-xs-12 margin-card">'+
                                        '<label class="campos-label">RFC</label>'+
                                        '<p class="font-regular"> '+data.RFC+' </p>'+
                                    '</div>'+
                                    '<div class="col-lg-4 col-md-6 col-sm-12 col-xs-12 margin-card">'+
                                        '<label class="campos-label">CURP</label>'+
                                        '<p class="font-regular"> '+data.CURP+' </p>'+
                                    '</div>'+
                                    '<div class="col-lg-3 col-md-6 col-sm-6 col-xs-12 margin-card">'+
                                        '<label class="campos-label">Lugar de Nacimiento</label>'+
                                        '<p class="font-regular"> '+data.LUGAR_NACIMIENTO+' </p>'+
                                    '</div>'+
                                    '<div class="col-lg-2 col-md-3 col-sm-6 col-xs-12 margin-card">'+
                                        '<label class="campos-label">Estado</label>'+
                                        '<p class="font-regular"> '+data.ESTADO+' </p>'+
                                    '</div>'+
                                    '<div class="col-lg-1 col-md-2 col-sm-6 col-xs-12 margin-card">'+
                                        '<label class="campos-label">Edad</label>'+
                                        '<p class="font-regular"> '+data.EDAD+' </p>'+
                                    '</div>'+
                                    '<div class="col-lg-2 col-md-3 col-sm-6 col-xs-12 margin-card">'+
                                        '<label class="campos-label">Fecha nacimiento</label>'+
                                        '<p class="font-regular"> '+data.FECHA_NACIMIENTO+' </p>'+
                                    '</div>'+
                                    '<div class="col-lg-3 col-md-4 col-sm-6 col-xs-12 margin-card">'+
                                        '<label class="campos-label">Telefono</label>'+
                                        '<p class="font-regular"> '+data.TELEFONO+' </p>'+
                                    '</div>'+
                                     '<div class="col-lg-3 col-md-4 col-sm-6 col-xs-12 margin-card">'+
                                        '<label class="campos-label">Vacante a Contratar</label>'+
                                        '<p class="font-regular"> '+((data.VACANTE!=null)?data.VACANTE:'SIN VACANTE')+' </p>'+
                                    '</div>'+
                                '</div>'+
                             '</div>'+
                        '</div>'+
                    '<div class="estatus-item estatus-empleado-'+2+'">'+((data.ESTATUS!=null)?data.ESTATUS:'SIN ESTATUS')+'</div>'+
                '</div>';

                   return html;
            }
    function llamarExcel(){
        $('#form-excel input').remove();
        $('#form-ajax input, #form-ajax select').each(function(index, el) {
            if($(el).attr('name')!='_csrf'){
                console.log($(el).val());
                $('#form-excel').append('<input  type= "hidden" name="'+$(el).attr('name')+'" value="'+$(el).val()+'"/>');
            }
        });
        return false;
    }
</script>
