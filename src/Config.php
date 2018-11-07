<?php

namespace KingsCode\LaravelApnsNotificationChannel;

class Config
{
    /**
     * @var string
     */
    protected $connection;

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
     * @var string|null
     */
    protected $privateKeyPassword;

    /**
     * ApnChannel constructor.
     *
     * @param string $connection
     * @param string $privateKey
     * @param string $keyId
     * @param string $teamId
     * @param string $appBundle
     * @param string $privateKeyPassword
     */
    public function __construct(
        string $connection,
        string $privateKey,
        string $keyId,
        string $teamId,
        string $appBundle,
        ?string $privateKeyPassword = null
    ) {
        $this->connection = $connection;
        $this->privateKey = $privateKey;
        $this->keyId = $keyId;
        $this->teamId = $teamId;
        $this->appBundle = $appBundle;
        $this->privateKeyPassword = $privateKeyPassword;
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

    /**
     * @return string|null
     */
    public function getPrivateKeyPassword(): ?string
    {
        return $this->privateKeyPassword;
    }

    /**
     * @return string
     */
    public function getConnection(): string
    {
        return $this->connection;
    }

}
