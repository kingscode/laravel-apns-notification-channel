<?php

namespace KingsCode\LaravelApnsNotificationChannel;

class Config
{
    /**
     * @var string
     */
    protected $privateKey;

    /**
     * @var string
     */
    protected $keyId;

    /**
     * @var string
     */
    protected $teamId;

    /**
     * @var string
     */
    protected $appBundle;

    /**
     * ApnChannel constructor.
     *
     * @param string $privateKey
     * @param string $keyId
     * @param string $teamId
     * @param string $appBundle
     */
    public function __construct(
        string $privateKey,
        string $keyId,
        string $teamId,
        string $appBundle
    ) {
        $this->privateKey = $privateKey;
        $this->keyId = $keyId;
        $this->teamId = $teamId;
        $this->appBundle = $appBundle;
    }

    /**
     * @return string
     */
    public function getPrivateKey(): string
    {
        return $this->privateKey;
    }

    /**
     * @return string
     */
    public function getKeyId(): string
    {
        return $this->keyId;
    }

    /**
     * @return string
     */
    public function getTeamId(): string
    {
        return $this->teamId;
    }

    /**
     * @return string
     */
    public function getAppBundle(): string
    {
        return $this->appBundle;
    }

}
