<?php

require_once __DIR__ . '/Model.php';

class About extends Model
{
    protected $table = 'about';

    // 「关于」为单行配置，取第一行
    public function content()
    {
        return $this->first();
    }
}
