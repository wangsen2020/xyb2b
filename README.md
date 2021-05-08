这是一个行云货仓的php调用和验签客户端
-------------------------------
- 由于行云货仓后端由java语言抒写,并且签名方式比较定义化,所以需要一些特殊的处理，你可以直接拿来用，也可以借鉴该代码重写
- 调用案例

````php
!defined('BASE_PATH') && define('BASE_PATH', __DIR__);
require BASE_PATH . '/vendor/autoload.php';
$xunYun = new \Xyb2b\Util\XunYun("XXX", "XXXX");
$response = $xunYun->post("get_goods_list", [
        "category_id" => 0,
        "page_size" => 50,
        "page_index" => 1
    ]
);