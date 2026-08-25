<?php

require_once __DIR__ . '/Model.php';

class DiySet extends Model
{
    protected $table = 'diySet';

    // 自定义设置（头部/尾部代码、CSS、Pjax 等）为单行配置
    public function settings()
    {
        return $this->first();
    }
}
