<?= \yadjet\ztree\ZTree::widget([
    'id' => '__ztree__',
    'nodes' => $categories,
    'settings' => [
        'check' => [
            'enable' => true
        ]
    ],
]);
