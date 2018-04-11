<?php

use yii\helpers\Html;
use yii\helpers\Url;


/* @var $this yii\web\View */
/* @var $model app\models\tblempleados */

$this->title = 'Alta Empleado';
$this->params['breadcrumbs'][] = ['label' => 'empleados', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="col-lg-12">

    <h1 class="title">
        <a href="<?php echo Url::to(['empleados/index_pendientes']); ?>"  class="return-arrow icon-12x21"></a> <?= Html::encode($this->title) ?>
    </h1>

    <?= $this->render('_form', [
        'model' => $model,
        'modelDomicilios' => $modelDomicilios,
        'modelPerfilEmpleados' => $modelPerfilEmpleados,
        'modelSubirFotoEmpleado' => $modelSubirFotoEmpleado,
        'modelSubirCVOriginal' => $modelSubirCVOriginal,
        'modelSubirCVEISEI' => $modelSubirCVEISEI,
        'modelMotivosContrato' => $modelMotivosContrato,
        'modelAdministradoraDatosPer' => $modelAdministradoraDatosPer,
        'modelAdministradoraBenef' => $modelAdministradoraBenef,
        'modelBitAdministradoraEmp' => $modelBitAdministradoraEmp,
        'SecondmodelBenef' => $SecondmodelBenef,
        //'lugarnaciemiento' => $lugarnaciemiento,
        'extraVals' => $extraVals,
        'extra' => $extra,
    ]) ?>

</div>