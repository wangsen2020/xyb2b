<?php

namespace Xyb2b\Util;

class XunYun
{
    private $merchant_id;
    private $secret;
    private $baseUri = "http://bbcapi.test.xyb2b.com";
    private $apiUri = "/open/api/operate";

    /**
     * @var \GuzzleHttp\Client
     */
    private $client;

    public function __construct($merchant_id, $secret, $baseUri = "")
    {
        $this->client = new \GuzzleHttp\Client([
            'base_uri' => $baseUri ? $baseUri : $this->baseUri
        ]);
        $this->merchant_id = $merchant_id;
        $this->secret = $secret;
    }


    public function post($method, $params = [])
    {
        $params = array_merge([
            'opcode' => $method,
            'merchant_id' => $this->merchant_id,
            'sign_type' => 'MD5',
        ], $params);

        $params['sign'] = $this->signature($params);

        try {
            $response = $this->client->request("POST", $this->apiUri, [
                "json" => $params
            ]);
            $result = (string)$response->getBody();
            return json_decode($result, true, 512, JSON_BIGINT_AS_STRING);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function signature(array $params)
    {
        ksort($params);
        unset($params["sign"]);
        unset($params["sign_type"]);

        $waitSign = '';
        foreach ($params as $key => $value) {
            $waitSign .= ($key . "=" . $this->convertVal($value) . "&");
        }

        $waitSign = trim($waitSign, "&");

        return md5($waitSign . $this->secret);
    }

    /**
     * @param array|string|int|float $vals 转换值
     * @return string|string[]
     */
    private function convertVal($vals)
    {
        if (is_array($vals)) {
            return str_replace("\\/", "/", str_replace('"', "", json_encode($this->recursionSort($vals), JSON_UNESCAPED_UNICODE)));
        }
        return $vals;
    }

    /**
     * @param $arrays
     * @return array
     */
    public function recursionSort($arrays)
    {
        if (is_array($arrays)) {
            ksort($arrays);
            foreach ($arrays as $k => &$val) {
                $val = $this->recursionSort($val);
            }
        }
        return $arrays;
    }

    /**
     * @param $response
     * @param $sign
     * @return bool
     * @throws \Exception
     */
    public function validateSign($response, $sign)
    {
        /** @var string $content 因为java那边会有类似19.00这种浮点型传过来 而反json会变成19.0导致验签报错 */
        $content = preg_replace("/(\:)([0-9]\d*\.\d*)(\,?)/", '$1"$2"$3', $response);
        /** @var array $params */
        $params = json_decode($content, true, 1024, JSON_BIGINT_AS_STRING);
        if (!is_array($params)) {
            throw new \Exception("数据异常,无法生成签名");
        }

        $generateSign = $this->signature($params);
        if ($generateSign == $sign) {
            return true;
        }
        return false;
    }
}