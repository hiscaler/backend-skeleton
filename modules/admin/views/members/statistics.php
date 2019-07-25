<?php

use app\modules\admin\components\JsBlock;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */

$this->title = Yii::t('app', 'Members');
$this->params['breadcrumbs'][] = $this->title;

$this->params['menus'] = [
    ['label' => Yii::t('app', 'List'), 'url' => ['index']],
];
?>
<div class="form-outside form-search form-layout-column">
    <div class="form">
        <?php
        $form = ActiveForm::begin([
            'id' => 'form-member-statistics',
            'action' => ['statistics'],
            'method' => 'get',
        ]);
        ?>
        <div class="entry">
            <div class="form-group field-membersearch-type has-success">
                <label class="control-label" for="type">统计类型</label>
                <select id="type" class="form-control" name="type" aria-invalid="false">
                    <option value="date">注册时间</option>
                    <option value="type">会员等级</option>
                </select>
            </div>
            <div class="form-group field-membersearch-type has-success">
                <label class="control-label" for="beginDate">起始时间</label>
                <input class="h5-date-picker form-control" type="date" id="beginDate" name="beginDate" />
                -
                <input class="h5-date-picker form-control" type="date" id="endDate" name="endDate" />
            </div>
        </div>
        <div class="form-group buttons">
            <?= Html::button('统计', ['class' => 'btn btn-primary', 'id' => 'btn-statistics']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>
<div id="chart" style="width: 100%; height: 600px;"></div>
<?php $this->registerJsFile(Yii::$app->getRequest()->getBaseUrl() . '/admin/js/echarts.min.js') ?>
<?php $this->registerJsFile(Yii::$app->getRequest()->getBaseUrl() . '/admin/js/axios.min.js') ?>
<?php JsBlock::begin() ?>
<script type="text/javascript">
    yadjet.urls = {
        member: {
            statistics: '<?= Url::toRoute(['/api/member/statistics', 'accessToken' => $accessToken]) ?>'
        }
    };

    let myChart = echarts.init(document.getElementById('chart'));
    $('#btn-statistics').on('click', function () {
        const beginDate = $('#beginDate').val();
        const endDate = $('#endDate').val();
        const type = $('#type').val();
        statistic(beginDate, endDate, type);
    });

    $(function () {
        statistic();
    });

    function statistic(beginDate = null, endDate = null, type = 'date') {
        let params = {
            type: type
        };
        if (beginDate && endDate) {
            params.beginDate = beginDate;
            params.endDate = endDate;
        }
        console.info(params);
        axios.get(yadjet.urls.member.statistics, {
            params: params,
        }).then(function (response) {
            let xData = [], yData = [];
            const items = response.data.data;
            for (let item of items) {
                xData.push(item.name);
                yData.push(item.value);
            }

            let option = {
                color: ['#3398DB'],
                tooltip: {
                    trigger: 'axis',
                    axisPointer: {            // 坐标轴指示器，坐标轴触发有效
                        type: 'shadow'        // 默认为直线，可选为：'line' | 'shadow'
                    }
                },
                grid: {
                    left: '3%',
                    right: '4%',
                    bottom: '0%',
                    containLabel: true
                },
                xAxis: [
                    {
                        type: 'category',
                        data: xData,
                        axisTick: {
                            alignWithLabel: true
                        }
                    }
                ],
                yAxis: [
                    {
                        type: 'value'
                    }
                ],
                series: [
                    {
                        name: '注册人数',
                        type: 'bar',
                        barWidth: '60%',
                        data: yData
                    }
                ]
            };
            myChart.setOption(option);
        }).catch(function (error) {
            console.log(error);
        });
    }
</script>
<?php JsBlock::end() ?>
