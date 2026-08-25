<?php

require_once __DIR__ . '/Model.php';

class Text extends Model
{
    protected $table = 'text';

    // 站点文案为单行配置，取第一行
    public function siteSettings()
    {
        return $this->first();
    }
}
