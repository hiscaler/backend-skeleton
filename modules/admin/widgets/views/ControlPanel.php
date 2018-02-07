<div class="control-panel">
    <div class="inner">
        <div class="title shortcut">控制面板</div>
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