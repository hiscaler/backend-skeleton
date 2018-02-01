<?php

use app\modules\admin\components\MessageBox;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\models\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$baseUrl = Yii::$app->getRequest()->getBaseUrl() . '/admin';

$this->title = Yii::t('app', 'Users');
$this->params['breadcrumbs'][] = $this->title;

$this->params['menus'] = [
    ['label' => Yii::t('app', 'List'), 'url' => ['index']],
    ['label' => Yii::t('app', 'Create'), 'url' => ['create']],
    ['label' => Yii::t('app', 'Search'), 'url' => '#'],
];
?>
<div class="user-index">
    <?= $this->render('_search', ['model' => $searchModel]) ?>
    <?php
    $session = Yii::$app->getSession();
    if ($session->hasFlash('notice')) {
        echo MessageBox::widget([
            'title' => Yii::t('app', 'Prompt Message'),
            'message' => $session->getFlash('notice'),
            'showCloseButton' => true
        ]);
    }

    Pjax::begin([
        'formSelector' => '#form-user-search',
    ]);
    echo GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            [
                'class' => 'yii\grid\SerialColumn',
                'contentOptions' => ['class' => 'serial-number']
            ],
            [
                'attribute' => 'username',
                'format' => 'raw',
                'value' => function ($model) {
                    return Html::a($model['username'], ['update', 'id' => $model['id']]);
                },
                'contentOptions' => ['class' => 'username']
            ],
            [
                'attribute' => 'nickname',
                'contentOptions' => ['class' => 'username']
            ],
            [
                'attribute' => 'role',
                'format' => 'userRole',
                'contentOptions' => ['class' => 'user-role'],
            ],
            'email:email',
            [
                'attribute' => 'login_count',
                'contentOptions' => ['class' => 'number'],
            ],
            [
                'attribute' => 'created_at',
                'format' => 'datetime',
                'contentOptions' => ['class' => 'datetime'],
            ],
            [
                'attribute' => 'last_login_time',
                'format' => 'datetime',
                'contentOptions' => ['class' => 'datetime'],
            ],
            [
                'attribute' => 'status_text',
                'contentOptions' => ['class' => 'data-status']
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{update} {change-password} {auth} {delete}',
                'buttons' => [
                    'change-password' => function ($url, $model, $key) use ($baseUrl) {
                        return Html::a(Html::img($baseUrl . '/images/change-password.png'), $url, ['title' => Yii::t('app', 'Change Password')]);
                    },
                    'auth' => function ($url, $model, $key) use ($baseUrl) {
                        return Html::a(Html::img($baseUrl . '/images/auth.png'), $url, ['data-pjax' => 0, 'class' => 'user-auth', 'data-name' => $model['username'], 'title' => Yii::t('app', 'Please choice this user can manager categories')]);
                    },
                ],
                'headerOptions' => ['class' => 'buttons-4 last'],
            ],
        ],
    ]);
    Pjax::end();
    ?>
</div>
<?php
$this->registerJs('yadjet.actions.toggle("table td.enabled-enable-handler img", "' . Url::toRoute('toggle') . '");');

$title = Yii::t('app', 'Please choice this user can manager categories');

\app\modules\admin\components\JsBlock::begin();
?>
<script type="text/javascript">
    $(function () {
        jQuery(document).on('click', 'a.user-auth', function () {
            var t = $(this);
            var url = t.attr('href');
            $.ajax({
                type: 'GET',
                url: url,
                beforeSend: function (xhr) {
                    $.fn.lock();
                }, success: function (response) {
                    layer.open({
                        id: 'nodes-list',
                        title: "<?= $title ?>" + ' [ ' + t.attr('data-name') + ' ]',
                        content: response,
                        skin: 'layer-grid-view',
                        yes: function (index, layero) {
                            var nodes = $.fn.zTree.getZTreeObj("__ztree__").getCheckedNodes(true);
                            var ids = [];
                            for (var i = 0, l = nodes.length; i < l; i++) {
                                ids.push(nodes[i].id);
                            }

                            $.ajax({
                                type: 'POST',
                                url: url,
                                data: {choiceCategoryIds: ids.toString()},
                                dataType: 'json',
                                beforeSend: function (xhr) {
                                    $.fn.lock();
                                }, success: function (response) {
                                    if (response.success === false) {
                                        layer.alert(response.error.message);
                                    }
                                    $.fn.unlock();
                                }, error: function (XMLHttpRequest, textStatus, errorThrown) {
                                    layer.alert('[ ' + XMLHttpRequest.status + ' ] ' + XMLHttpRequest.responseText);
                                    $.fn.unlock();
                                }
                            });

                            layer.close(index);
                        }
                    });
                    $.fn.unlock();
                }, error: function (XMLHttpRequest, textStatus, errorThrown) {
                    layer.alert('[ ' + XMLHttpRequest.status + ' ] ' + XMLHttpRequest.responseText);
                    $.fn.unlock();
                }
            });

            return false;
        });
    });
</script>
<?php \app\modules\admin\components\JsBlock::end() ?>
