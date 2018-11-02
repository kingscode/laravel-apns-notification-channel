<?php

namespace KoenHoeijmakers\LaravelApnsNotificationChannel;

class Config
{
    /**
     * @var string
     */
    protected $privateKey;

    /**
     * @var string
     */
    protected $developerId;

    /**
     * @var string
     */
    protected $teamId;

    /**
     * @var string
     */
    protected $appBundle;

    /**
     * @var null|string
     */
    protected $password;

    /**
     * ApnChannel constructor.
     *
     * @param string      $privateKey
     * @param string      $developerId
     * @param string      $teamId
     * @param string      $appBundle
     * @param null|string $password
     */
    public function __construct(
        string $privateKey,
        string $developerId,
        string $teamId,
        string $appBundle,
        ?string $password = null
    ) {
        $this->privateKey = $privateKey;
        $this->developerId = $developerId;
        $this->teamId = $teamId;
        $this->appBundle = $appBundle;
        $this->password = $password;
    }
}
