<?php
$this->title = Yii::t('app', 'Members');
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['index']];
$this->params['breadcrumbs'][] = '会员层级图';

$this->params['menus'] = [
    ['label' => Yii::t('app', 'List'), 'url' => ['index']],
    ['label' => Yii::t('app', 'Create'), 'url' => ['create']],
];

$baseUrl = Yii::$app->getRequest()->getBaseUrl() . '/admin';
?>
<div class="chart" id="main" style="width: 100%; min-height: 600px;">Loading...</div>
<?php
$this->registerJsFile($baseUrl . '/js/echarts.min.js');
?>
<?php \app\modules\admin\components\JsBlock::begin() ?>
    <script type="text/javascript">
        var myChart = echarts.init(document.getElementById('main'));
        var option = {
            toolbox: {
                show: true,
                feature: {
                    mark: { show: true },
                    dataView: { show: false, readOnly: false },
                    restore: { show: true },
                    saveAsImage: { show: false }
                }
            },
            series: [
                {
                    name: '会员层级图',
                    type: 'tree',
                    orient: 'vertical',  // vertical horizontal
                    rootLocation: { x: 'center', y: 'center' }, // 根节点位置  {x: 100, y: 'center'}
                    nodePadding: 8,
                    layerPadding: 200,
                    hoverable: true,
                    roam: true,
                    symbolSize: 20,
                    itemStyle: {
                        normal: {
                            color: '#4883b4',
                            label: {
                                show: true,
                                position: 'right',
                                formatter: "{b}",
                                textStyle: {
                                    color: '#000',
                                    fontSize: 14
                                }
                            },
                            lineStyle: {
                                color: '#ccc',
                                type: 'broken' // 'curve'|'broken'|'solid'|'dotted'|'dashed'

                            }
                        },
                        emphasis: {
                            color: '#4883b4',
                            label: {
                                show: true
                            },
                            borderWidth: 0
                        }
                    },

                    data: <?= json_encode($members) ?>
                }
            ]
        };
        myChart.setOption(option);
    </script>
<?php \app\modules\admin\components\JsBlock::end() ?>