<?php
/* @var $this \yii\web\View */
/* @var string $filters */
/* @var string $actionButtons */
/* @var string $containerAttributes */

/* @var array $cardOptions */

use p4it\adminlte\widgets\card\Card;

?>
<?php if ($filters && $actionButtons) { ?>
    <div class="row">
        <div class="col-sm-10">
            <?php if ($filters) { ?>
                <?= $filters ?>
            <?php } ?>
        </div>
        <div class="col-sm-2 text-right mb-3">
            <?php if ($actionButtons) { ?>
                <?= $actionButtons ?>
            <?php } ?>
        </div>
    </div>
<?php } ?>
<?php if ($filters && !$actionButtons) { ?>
    <?= $filters ?>
<?php } ?>
<?php if (!$filters && $actionButtons) { ?>
    <div class="text-right mb-3">
        <?= $actionButtons ?>
    </div>
<?php } ?>

<?= Card::widget($cardOptions) ?>

