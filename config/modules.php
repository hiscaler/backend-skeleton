<?php

return [
    /**
     * 'app-models-Article' => [
     *    'id' => 'articles', // 控制器名称（唯一）
     *    'label' => 'Articles', //  需要翻译的文本（app.php）
     *    'url' => ['/articles/index'], // 访问 URL
     *    'activeConditions' => [], // 激活条件，填写控制器 id
     *    'forceEmbed' => true, // 是否强制显示在控制面板中
     * ],
     */
    'System Manage' => [
        'app-models-User' => [
            'id' => 'users',
            'label' => 'Users',
            'url' => ['users/index'],
            'forceEmbed' => false,
        ],
        'app-models-Module' => [
            'id' => 'modules',
            'label' => 'Modules',
            'url' => ['modules/index'],
            'forceEmbed' => true,
        ],
        'app-models-meta' => [
            'id' => 'meta',
            'label' => 'Meta',
            'url' => ['meta/index'],
            'forceEmbed' => false,
        ],
        'app-models-Lookup' => [
            'id' => 'lookups',
            'label' => 'Lookups',
            'url' => ['lookups/form'],
            'forceEmbed' => false,
        ],
        'app-models-Category' => [
            'id' => 'categories',
            'label' => 'Categories',
            'url' => ['categories/index'],
            'forceEmbed' => true,
        ],
        'app-models-Label' => [
            'id' => 'labels',
            'label' => 'Labels',
            'url' => ['labels/index'],
            'forceEmbed' => false,
        ],
        'app-models-FileUploadConfig' => [
            'id' => 'file-upload-config',
            'label' => 'File Upload Configs',
            'url' => ['file-upload-configs/index'],
            'forceEmbed' => false,
        ],
        'app-models-MemberGroup' => [
            'id' => 'member-group',
            'label' => 'Member Groups',
            'url' => ['member-groups/index'],
            'forceEmbed' => false,
        ],
        'app-models-Member' => [
            'id' => 'member',
            'label' => 'Members',
            'url' => ['members/index'],
            'forceEmbed' => true,
        ],
        'app-models-MemberLoginLog' => [
            'id' => 'member-login-log',
            'label' => 'Member Login Logs',
            'url' => ['member-login-logs/index'],
            'forceEmbed' => true,
        ],
        'db' => [
            'id' => 'db',
            'label' => 'DB',
            'url' => ['db/index'],
            'forceEmbed' => false,
        ],
        'cache' => [
            'id' => 'cache',
            'label' => 'Cache',
            'url' => ['caches/index'],
            'forceEmbed' => true,
        ],
    ],
];