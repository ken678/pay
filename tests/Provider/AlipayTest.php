<?php

namespace Yansongda\Pay\Tests\Provider;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ServerRequest;
use Mockery;
use PHPUnit\Framework\TestCase;
use Yansongda\Pay\Contract\HttpClientInterface;
use Yansongda\Pay\Exception\InvalidParamsException;
use Yansongda\Pay\Pay;
use Yansongda\Pay\Plugin\Alipay\LaunchPlugin;
use Yansongda\Pay\Plugin\Alipay\PreparePlugin;
use Yansongda\Pay\Plugin\Alipay\RadarPlugin;
use Yansongda\Pay\Plugin\Alipay\SignPlugin;
use Yansongda\Pay\Plugin\ParserPlugin;

class AlipayTest extends TestCase
{
    protected function setUp(): void
    {
        $config = [
            'alipay' => [
                'default' => [
                    'app_id' => '2016082000295641',
                    'app_secret_cert' => 'MIIEvAIBADANBgkqhkiG9w0BAQEFAASCBKYwggSiAgEAAoIBAQCDRjOg5DnX+8L+rB8d2MbrQ30Z7JPM4hiDhawHSwQCQ7RlmQNpl6b/N6IrPLcPFC1uii179U5Il5xTZynfjkUyJjnHusqnmHskftLJDKkmGbSUFMAlOv+NlpUWMJ2A+VUopl+9FLyqcV+XgbaWizxU3LsTtt64v89iZ2iC16H6/6a3YcP+hDZUjiNGQx9cuwi9eJyykvcwhDkFPxeBxHbfwppsul+DYUyTCcl0Ltbga/mUechk5BksW6yPPwprYHQBXyM16Jc3q5HbNxh3660FyvUBFLuVWIBs6RtR2gZCa6b8rOtCkPQKhUKvzRMlgheOowXsWdk99GjxGQDK5W4XAgMBAAECggEAYPKnjlr+nRPBnnNfR5ugzH67FToyrU0M7ZT6xygPfdyijaXDb2ggXLupeGUOjIRKSSijDrjLZ7EQMkguFHvtfmvcoDTDFaL2zq0a3oALK6gwRGxOuzAnK1naINkmeOmqiqrUab+21emEv098mRGbLNEXGCgltCtz7SiRdo/pgIPZ1wHj4MH0b0K2bFG3xwr51EyaLXKYH4j6w9YAXXsTdvzcJ+eRE0Yq4uGPfkziqg8d0xXSEt90HmCGHKo4O2eh1w1IlBcHfK0F6vkeUAtrtAV01MU2bNoRU147vKFxjDOVBlY1nIZY/drsbiPMuAfSsodL0hJxGSYivbKTX4CWgQKBgQDd0MkF5AIPPdFC+fhWdNclePRw4gUkBwPTIUljMP4o+MhJNrHp0sEy0sr1mzYsOT4J20hsbw/qTnMKGdgy784bySf6/CC7lv2hHp0wyS3Es0DRJuN+aTyyONOKGvQqd8gvuQtuYJy+hkIoHygjvC3TKndX1v66f9vCr/7TS0QPywKBgQCXgVHERHP+CarSAEDG6bzI878/5yqyJVlUeVMG5OXdlwCl0GAAl4mDvfqweUawSVFE7qiSqy3Eaok8KHkYcoRlQmAefHg/C8t2PNFfNrANDdDB99f7UhqhXTdBA6DPyW02eKIaBcXjZ7jEXZzA41a/zxZydKgHvz4pUq1BdbU5ZQKBgHyqGCDgaavpQVAUL1df6X8dALzkuqDp9GNXxOgjo+ShFefX/pv8oCqRQBJTflnSfiSKAqU2skosdwlJRzIxhrQlFPxBcaAcl0VTcGL33mo7mIU0Bw2H1d4QhAuNZIbttSvlIyCQ2edWi54DDMswusyAhHxwz88/huJfiad1GLaLAoGASIweMVNuD5lleMWyPw2x3rAJRnpVUZTc37xw6340LBWgs8XCEsZ9jN4t6s9H8CZLiiyWABWEBufU6z+eLPy5NRvBlxeXJOlq9iVNRMCVMMsKybb6b1fzdI2EZdds69LSPyEozjkxdyE1sqH468xwv8xUPV5rD7qd83+pgwzwSJkCgYBrRV0OZmicfVJ7RqbWyneBG03r7ziA0WTcLdRWDnOujQ9orhrkm+EY2evhLEkkF6TOYv4QFBGSHfGJ0SwD7ghbCQC/8oBvNvuQiPWI8B+00LwyxXNrkFOxy7UfIUdUmLoLc1s/VdBHku+JEd0YmEY+p4sjmcRnlu4AlzLxkWUTTg==',
                    'app_public_cert_path' => __DIR__.'/../Stubs/cert/appCertPublicKey_2016082000295641.crt',
                    'alipay_public_cert_path' => __DIR__.'/../Stubs/cert/alipayCertPublicKey_RSA2.crt',
                    'alipay_root_cert_path' => __DIR__.'/../Stubs/cert/alipayRootCert.crt',
                ],
            ]
        ];
        Pay::config($config);
    }

    protected function tearDown(): void
    {
        Pay::clear();
    }

    public function testShortcutNotFound()
    {
        self::expectException(InvalidParamsException::class);
        self::expectExceptionCode(InvalidParamsException::SHORTCUT_NOT_FOUND);

        Pay::alipay()->foo();
    }

    public function testShortcutIncompatible()
    {
        self::expectException(InvalidParamsException::class);
        self::expectExceptionCode(InvalidParamsException::SHORTCUT_NOT_FOUND);

        Pay::alipay()->foo();
    }

    public function testFindDefault()
    {
        $response = [
            "alipay_trade_query_response" => [
                "code" => "10000",
                "msg" => "Success",
                "buyer_logon_id" => "ghd***@sandbox.com",
                "buyer_pay_amount" => "0.00",
                "buyer_user_id" => "2088102174698127",
                "buyer_user_type" => "PRIVATE",
                "invoice_amount" => "0.00",
                "out_trade_no" => "yansongda-1622986519",
                "point_amount" => "0.00",
                "receipt_amount" => "0.00",
                "send_pay_date" => "2021-06-06 21:35:40",
                "total_amount" => "0.01",
                "trade_no" => "2021060622001498120501382075",
                "trade_status" => "TRADE_SUCCESS",
            ],
            "alipay_cert_sn" => "a359aaadd01ceca03dbc07537da539b9",
            "sign" => "Ipp1M3pwUFJ19Tx/D+40RZstXr3VSZzGxPB1Qfj1e837UkGxOJxFFK6EZ288SeEh06dPFd4qJ7BHfP/7mvkRqF1/mezBGvhBz03XTXfDn/O6IkoA+cVwpfm+i8MFvzC/ZQB0dgtZppu5qfzVyFaaNu8ct3L/NSQCMR1RXg2lH3HiwfxmIF35+LmCoL7ZPvTxB/epm7A/XNhAjLpK5GlJffPA0qwhhtQwaIZ7DHMXo06z03fbgxlBu2eEclQUm6Fobgj3JEERWLA0MDQiV1EYNWuHSSlHCMrIxWHba+Euu0jVkKKe0IFKsU8xJQbc7GTJXx/o0NfHqGwwq8hMvtgBkg==",
        ];

        $http = Mockery::mock(Client::class);
        $http->shouldReceive('sendRequest')->andReturn(new Response(200, [], json_encode($response)));
        Pay::set(HttpClientInterface::class, $http);

        $result = Pay::alipay()->find('yansongda-1622986519');

        self::assertEqualsCanonicalizing($response['alipay_trade_query_response'], $result->all());
    }

    public function testFindTransfer()
    {
        $response = [
            "alipay_fund_trans_order_query_response" => [
                "code" => "10000",
                "msg" => "Success",
                "order_fee" => "0.00",
                "order_id" => "20210605110070001506210013918943",
                "out_biz_no" => "202106051432",
                "pay_date" => "2021-06-05 14:32:08",
                "status" => "SUCCESS",
            ],
            "alipay_cert_sn" => "a359aaadd01ceca03dbc07537da539b9",
            "sign" => "cihCFfEPKsNClEvyaf4s99WzvyVPUhbfgk4dXcZBnWZZ69Ng1Z9YekQd4Bt3WopXIkkCK96Rcd1jHPErBrN2V+XC9sDG/LkQbh8eTF6Hh1QYZl8ERpkjdb7H0xN77LmaUZwq82zVo57AT3B01HkMV4n8TTazVbzNdd8v8w/URQknZfo89H2YFiCMe78HJVSurza8kT2kIineUBy4CWA+9uZTEKOrntZLnggK/gtwm6nu7z0i5EptjoypBMfZflyC8AQqdXKGQXnrS8mC7eq7Y2HTm4pkFggmU4vvnVN0RbuwBMENBV5X3JuYs1hs3mKIAgvXZclHaVPMPJwhAel4rA==",
        ];

        $http = Mockery::mock(Client::class);
        $http->shouldReceive('sendRequest')->andReturn(new Response(200, [], json_encode($response)));
        Pay::set(HttpClientInterface::class, $http);

        $result = Pay::alipay()->find(['out_biz_no' => '202106051432', '_type' => 'transfer']);
        self::assertEqualsCanonicalizing($response['alipay_fund_trans_order_query_response'], $result->all());
    }

    public function testFindRefund()
    {
        $response = [
            "alipay_trade_fastpay_refund_query_response" => [
                "code" => "10000",
                "msg" => "Success",
                "out_request_no" => "1623160012",
                "out_trade_no" => "1623160012",
                "refund_amount" => "0.01",
                "total_amount" => "0.01",
                "trade_no" => "2021060822001498120501382932",
            ],
            "alipay_cert_sn" => "a359aaadd01ceca03dbc07537da539b9",
            "sign" => "WuqwIP2SBc7qPqwXkqI/MV2kRAbsODogr4bcHmM8svEHeNA37wGng/ApoVD1YqzTgD92dlJZ3/aUjRaLb94KyEaOGOcPLHucjRY/GyYJsmLfLfJfNjpjsglgc9ChUspkNm9r8PH+GKLm8PbY3BFyjjG59cfHIPpVHbTLxrLAcjOilKBJu1nNHiswTVGbAeHJkVrMdxRXxoBuSnuwr6fjy2h1w/evPE4dZ2xiPsKNv8B5swLd0jo0g/c6OFfF8sgFa6VbXMKyuj8/GHxwz4jxlTWdJYYWBeYlb7mXPYWnIvmM1e4qwwo4X4A9g8nDj+dsKSFyRvx879kNdtbP8B+SUw==",
        ];

        $http = Mockery::mock(Client::class);
        $http->shouldReceive('sendRequest')->twice()->andReturn(
            new Response(200, [], json_encode($response)), new Response(200, [], json_encode($response))
        );
        Pay::set(HttpClientInterface::class, $http);

        $result = Pay::alipay()->find([
            'out_trade_no' => '1623160012',
            'out_request_no' => '1623160012',
            '_type' => 'refund'
        ]);
        self::assertEqualsCanonicalizing($response['alipay_trade_fastpay_refund_query_response'], $result->all());

        $result1 = Pay::alipay()->find([
            'out_trade_no' => '1623160012',
            'out_request_no' => '1623160012',
        ]);
        self::assertEqualsCanonicalizing($response['alipay_trade_fastpay_refund_query_response'], $result1->all());
    }

    public function testRefund()
    {
        $response = [
            "alipay_trade_refund_response" => [
                "code" => "10000",
                "msg" => "Success",
                "buyer_logon_id" => "ghd***@sandbox.com",
                "buyer_user_id" => "2088102174698127",
                "fund_change" => "Y",
                "gmt_refund_pay" => "2021-06-08 21:48:39",
                "out_trade_no" => "1623160012",
                "refund_fee" => "0.01",
                "send_back_fee" => "0.00",
                "trade_no" => "2021060822001498120501382932",
            ],
            "alipay_cert_sn" => "a359aaadd01ceca03dbc07537da539b9",
            "sign" => "QfN3w7SAOR1FxFko05q2RXzv3hHBxVn9hT7rKpn0DrZss370iRDQQaSxy5ILjGSqSx8ODMOnUWTslzm3yk0hKEkOCTeDO5QJpDWwjBV0m7AJzFGhvh64ITrqsNk5/wID2dhlRehjF9jvJBUPMmlXEjc06B2azHrRHW8eF5z1aZLvoNXvtXQ2HzGpp5moIZMJGEsUqT+Qa172S3z6sGcPnN3rivxedcZF8OWALr0/gAvA4l7E2ZZg8c2cTsc+napTp3cuH0J8borxT5D7hDOu7xdaFA8b4YFqxQPrKFotC1vTpzxb88ImpYnCZw4vA6GLPJwYUHqHRT6C4I2bl1QTlA==",
        ];

        $http = Mockery::mock(Client::class);
        $http->shouldReceive('sendRequest')->andReturn(new Response(200, [], json_encode($response)));
        Pay::set(HttpClientInterface::class, $http);

        $result = Pay::alipay()->refund(['out_trade_no' => '1623160012', 'refund_amount' => '0.01',]);

        self::assertEqualsCanonicalizing($response['alipay_trade_refund_response'], $result->all());
    }

    public function testVerifyResponse()
    {
        $url = 'http://127.0.0.1:8000/alipay/verify?charset=utf-8&out_trade_no=yansongda-1622986519&method=alipay.trade.page.pay.return&total_amount=0.01&sign=oSazH3ZnzPQBGfJ8piYuri0E683D7bEKtd1NPcuYctvCEiRWP1QBVWma3hwoTLc19KdXbMGGZcOS5UvtlWwIvcK3oqkuRkFOwcRRmyF0UScmdHrTEPO9VwcaEWPK9Hy%2BTSlYrlnfCae1zlDo4vvNojFZf%2BduaaYCGS2L4Q55atloeztOPsZTNSYI7Jy0rrQcOaAWL7F9aJNqFPW6WkWL31w6HwDHcRSEQzD9C9YTsRkQ7khPHFEw8CHSYp5h8XOq%2BfE0yRDAEEw2pxYYC5QhCtbqVjLdfFXp792cTRd31IB6iAznnDvOATZVgulpC0Z6MV0k0MInL2CarbuO5SZfRg%3D%3D&trade_no=2021060622001498120501382075&auth_app_id=2016082000295641&version=1.0&app_id=2016082000295641&sign_type=RSA2&seller_id=2088102172237210&timestamp=2021-06-06+21%3A35%3A50';
        parse_str(parse_url($url)['query'], $query);
        $request = new ServerRequest('GET', $url);
        $request = $request->withQueryParams($query);

        $result = Pay::alipay()->verify($request);
        self::assertNotEmpty($result->all());

        $result = Pay::alipay()->verify($query);
        self::assertNotEmpty($result->all());
    }

    public function testMergeCommonPlugins()
    {
        Pay::config([]);
        $plugins = [FooPluginStub::class];

        self::assertEquals(array_merge(
            [PreparePlugin::class],
            $plugins,
            [SignPlugin::class, RadarPlugin::class],
            [LaunchPlugin::class, ParserPlugin::class],
        ), Pay::alipay()->mergeCommonPlugins($plugins));
    }
}

class FooPluginStub
{
}

class FooShortcut
{
}
