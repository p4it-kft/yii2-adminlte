<?php
/* @var $this \yii\web\View */
/* @var string[] $filters */
/* @var string $containerAttributes */

use yii\helpers\Html;

?>

<div <?= $containerAttributes ?>>
    <div class="form-inline">
        <?php foreach ($filters as $filter) { ?>
            <?= Html::tag('div',$filter, ['class' => 'mb-2 mr-2']); ?>
        <?php } ?>
        <button type="submit" class="btn btn-default hidden-sm hidden-xs mb-2"><i class="fa fa-search"></i></button>
    </div>
</div>

