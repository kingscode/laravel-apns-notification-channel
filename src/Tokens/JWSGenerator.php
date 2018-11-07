<?php

namespace KingsCode\LaravelApnsNotificationChannel\Tokens;

use Jose\Component\Core\AlgorithmManager;
use Jose\Component\Core\Converter\StandardConverter;
use Jose\Component\Core\JWK;
use Jose\Component\Signature\Algorithm\ES256;
use Jose\Component\Signature\JWSBuilder;

class JWSGenerator
{
    /**
     * @param string $iss
     * @param string $kid
     * @return \Jose\Component\Signature\JWS
     */
    public function generate(string $iss, string $kid)
    {
        $jsonConverter = new StandardConverter();

        $algorithmManager = AlgorithmManager::create([
            new ES256(),
        ]);

        $jwsBuilder = new JWSBuilder($jsonConverter, $algorithmManager);

        $payload = $jsonConverter->encode([
            'iss' => $iss,
            'iat' => time(),
        ]);

        $jwk = JWK::create([
            'kty' => 'RSA',
            'alg' => 'ES256',
            'kid' => $kid,
        ]);

        return $jwsBuilder->create()
            ->withPayload($payload)
            ->addSignature($jwk, [
                'alg' => 'ES256',
                'kid' => $jwk->get('kid'),
            ])
            ->build();
    }
}
