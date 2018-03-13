<?php

namespace App\Handlers;

use GuzzleHttp\Client;
use Overtrue\Pinyin\Pinyin;

class SlugTranslateHandler
{
    public function translate($text)
    {
        // 实例化http客户端
        $http = new Client;

        // 初始化配置信息
        $api = 'http://api.fanyi.baidu.com/api/trans/vip/translate?';
        $appid = config('services.baidu_translate.appid');
        $key = config('services.baidu_translate.key');
        $salt = time();

        // 如果没有配置百度翻译，就使用拼音兼容方案
        if (empty($appid) || empty($key)) {
            return $this->pinyin($text);
        }

        //根据百度翻译文档生成签名
        // http://api.fanyi.baidu.com/api/trans/product/apidoc
        // appid+q+salt+密钥 的MD5值
        $sign = md5($appid . $text . $salt . $key);

        //构建请求参数
        $query = http_build_query([
            'q'     => $text,
            'from'  => 'zh',
            'to'    => 'en',
            'appid' => $appid,
            'salt'  => $salt,
            'sign'  => $sign,
        ]);

        //发送请求,正常情况下百度翻译返回的结果如下
        /*
        {
            "from": "zh",
            "to": "en",
            "trans_result": [
                {
                    "src": "深入理解nginx与php交互",
                    "dst": "A deep understanding of the interaction between nginx and PHP"
                }
            ]
        }
        dd($result)结果如下
        array:3 [
          "from" => "zh"
          "to" => "en"
          "trans_result" => array:1 [
            0 => array:2 [
              "src" => "深入理解nginx与php交互"
              "dst" => "A deep understanding of the interaction between nginx and PHP"
            ]
          ]
        ]
        */
        $response = $http->get($api . $query);
        $result = json_decode($response->getBody(), true);
        if (! isset($result['trans_result'][0]['dst'])) {
            return $this->pinyin($text);
        }
        return str_slug($result['trans_result'][0]['dst']);
    }

    /**
     * 如果百度翻译不可用则使用这个库备用
     * @date   2018-03-13
     * @param  [type]     $text [要转换的文本]
     */
    public function pinyin($text)
    {
        // $py = app(Pinyin::class);
        // dd($py);
        return str_slug(app(Pinyin::class)->permalink($text));
    }
}
