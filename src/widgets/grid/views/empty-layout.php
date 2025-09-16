<?php
/* @var $this \yii\web\View */
/* @var string $filters */
/* @var string $actionButtons */
/* @var string $containerAttributes */
/* @var array $cardOptions */
use p4it\adminlte\widgets\card\Card;
?>
<?php if($actionButtons) { ?>
    <div class="row mb-3">
        <div class="offset-sm-10 col-sm-2 text-right">
            <?= $actionButtons ?>
        </div>
    </div>
<?php } ?>

<?= Card::widget($cardOptions) ?>

