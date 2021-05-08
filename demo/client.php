<?php
require_once __DIR__.'/config.php';
$xunYun = new \Xyb2b\Util\XunYun("XXX", "XXXX");
$response = $xunYun->post("get_goods_list", [
        "category_id" => 0,
        "page_size" => 50,
        "page_index" => 1
    ]
);
var_dump($response);