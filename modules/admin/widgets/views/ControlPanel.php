<div class="control-panel">
    <div class="inner">
        <div class="title shortcut">E2</div>
        <div class="shortcuts">
            <?= yii\widgets\Menu::widget([
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