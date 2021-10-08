<?php
/* @var $this \yii\web\View */
/* @var string $containerAttributes */
/* @var string $bodyContainerAttributes */
/* @var string $headerContainerAttributes */
/* @var null|string $title */
/* @var null|string $header */
/* @var null|string $body */
/* @var string[] $tools */
/* @var string[] $footers */
?>

<div <?= $containerAttributes ?>>
    <?php if ($title || $tools) { ?>
        <div <?= $headerContainerAttributes ?>>
            <?php if ($title) { ?>
                <h3 class="card-title"><?= $title ?></h3>
            <?php } ?>
            <?php if ($tools) { ?>
                <div class="card-tools">
                    <?php foreach ($tools as $tool) { ?>
                        <?= $tool ?>
                    <?php } ?>
                </div><!-- /.card-tools -->
            <?php } ?>
        </div><!-- /.card-header -->
    <?php } ?>
    <?php if ($header) { ?>
        <div <?= $headerContainerAttributes ?>>
            <?= $header ?>
        </div><!-- /.card-header -->
    <?php } ?>
    <?php if ($body) { ?>
        <div <?= $bodyContainerAttributes ?>>
            <?= $body ?>
        </div><!-- /.card-body -->
    <?php } ?>

    <?php foreach ($footers as $footerItem) { ?>
        <div class="card-footer">
            <?= $footerItem ?>
        </div><!-- /.card-footer -->
    <?php } ?>
</div><!-- /.card -->