<div class="control-panel">
    <div class="inner">
        <div class="title shortcut">MTS</div>
        <div class="shortcuts">
            <?php
            echo yii\widgets\Menu::widget([
                'items' => $items,
                'itemOptions' => ['class' => 'clearfix'],
                'firstItemCssClass' => 'first',
                'lastItemCssClass' => 'last',
                'activateParents' => true,
            ]);
            ?>
        </div>
    </div>
</div>