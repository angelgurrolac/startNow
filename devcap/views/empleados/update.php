<?php

use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\tblempleados */

$this->title = 'Modificar datos de empleado | '.$model->NOMBRE_EMP.' '.$model->APELLIDO_PAT_EMP.' '.$model->APELLIDO_MAT_EMP;
$this->params['breadcrumbs'][] = ['label' => 'Tblempleados', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->PK_EMPLEADO, 'url' => ['view', 'id' => $model->PK_EMPLEADO]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="col-lg-12">

    <h1 class="title row">
        <div class="col-lg-7">
            <a href="<?php echo Url::to(['empleados/view','id'=>$model->PK_EMPLEADO]); ?>"  class="return-arrow icon-12x21"></a> <?= Html::encode($this->title) ?>
        </div>
    </h1> 

    <?php /* $this->render('_form', [
        'model' => $model,
    ]) */?>

    <?= $this->render($extra['formCargar'], [
        'model' => $model,
        'modelDomicilios' => $modelDomicilios,
        'modelPerfilEmpleados' => $modelPerfilEmpleados,
        'modelSubirFotoEmpleado' => $modelSubirFotoEmpleado,
        'modelSubirCVOriginal' => $modelSubirCVOriginal,
        'modelSubirCVEISEI' => $modelSubirCVEISEI,
        'extra' => $extra,
        'extraVals' => $extraVals,
        'modelDocumentosEmpleados' => $modelDocumentosEmpleados,
        
        'modelBitComentariosEmpleados' => $modelBitComentariosEmpleados,
        'modelAdministradoraBenef' => $modelAdministradoraBenef,
        'SecondmodelBenef' => $SecondmodelBenef,
        'modelContactos' => $modelContactos,
    ]) ?>

   

</div>
