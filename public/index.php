<?php
require __DIR__ . '/../vendor/autoload.php';
 
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
 
use \LINE\LINEBot;
use \LINE\LINEBot\HTTPClient\CurlHTTPClient;
use \LINE\LINEBot\MessageBuilder\MultiMessageBuilder;
use \LINE\LINEBot\MessageBuilder\TextMessageBuilder;
use \LINE\LINEBot\MessageBuilder\StickerMessageBuilder;
use \LINE\LINEBot\SignatureValidator as SignatureValidator;
 
$pass_signature = true;
 
// set LINE channel_access_token and channel_secret
$channel_access_token = "j46XEHQw7f2UGx3dpkvnJZ2Ru4cRXM7rgsLMSZoZtS/JtAGxQAnKPfyf2RgVDUDkfVLQ1GMa5/0EpVuelIV3JyS7Ldx6v1n2261P+xYQYV3m8OzMT+1IzQZXhvm5+7xNPlaZwlrAjMsqdd0V06qACgdB04t89/1O/w1cDnyilFU=";
$channel_secret = "8859509fc0d43f6d1b6c215aeec6be3e";
 
// inisiasi objek bot
$httpClient = new CurlHTTPClient($channel_access_token);
$bot = new LINEBot($httpClient, ['channelSecret' => $channel_secret]);
 
$app = AppFactory::create();
$app->setBasePath(basePath:"/public");
 
$app->get(pattern:'/', function (Request $request, Response $response, $args) {
    $response->getBody()->write(string: "Hello World!");
    return $response;
});
 
// buat route untuk webhook
$app->post(pattern:'/webhook', function (Request $request, Response $response) use ($channel_secret, $bot, $httpClient, $pass_signature) {
    // get request body and line signature header
    $body = $request->getBody();
    $signature = $request->getHeaderLine(name: 'HTTP_X_LINE_SIGNATURE');
 
    // log body and signature
    file_put_contents(Filename:'php://stderr', data: 'Body: ' . $body);
 
    if ($pass_signature === false) {
        // is LINE_SIGNATURE exists in request header?
        if (empty($signature)) {
            return $response->withStatus(code: 400, reasonPhrase: 'Signature not set');
        }
 
        // is this request comes from LINE?
        if (!SignatureValidator::validateSignature($body, $channel_secret, $signature)) {
            return $response->withStatus(code: 400, reasonPhrase: 'Invalid signature');
        }
    }
    
// kode aplikasi nanti disini
 
});
$app->run();
 
