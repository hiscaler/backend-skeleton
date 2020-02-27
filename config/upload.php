<?php

/**
 * 文件上传设置
 */
return [
    'dir' => 'uploads', // 文件保存目录（相对于根目录而言，请不要填写绝对路径）
    // 请参考 \yii\web\ImageValidator 类属性进行设置
    'image' => [
        'minSize' => 1024,
        'maxSize' => 1024 * 1024 * 200,
        'extensions' => 'png,gif,jpg,jpeg'
    ],
    // 请参考 \yii\web\FileValidator 类属性进行设置
    'file' => [
        'minSize' => 1024,
        'maxSize' => 1024 * 1024 * 200,
        'extensions' => 'zip,rar,7z,txt,pdf,doc,docx,xls,xlsx,ppt,pptx,wps'
    ]
];