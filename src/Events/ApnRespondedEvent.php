<?php

namespace KingsCode\LaravelApnsNotificationChannel\Events;

use Pushok\ApnsResponseInterface;

class ApnRespondedEvent
{
    /**
     * @var \Pushok\ApnsResponseInterface
     */
    protected $response;

    /**
     * ApnRespondedEvent constructor.
     *
     * @param \Pushok\ApnsResponseInterface $response
     */
    public function __construct(ApnsResponseInterface $response)
    {
        $this->response = $response;
    }

    /**
     * @return \Pushok\ApnsResponseInterface
     */
    public function getResponse(): ApnsResponseInterface
    {
        return $this->response;
    }
}
