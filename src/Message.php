<?php

namespace KingsCode\LaravelApnsNotificationChannel;

use Pushok\Payload;
use Pushok\Payload\Alert;

class Message
{
    /**
     * @var string
     */
    protected $title;

    /**
     * @var string
     */
    protected $body;

    /**
     * @var string|null
     */
    protected $titleLocKey;

    /**
     * @var array|null
     */
    protected $titleLocArgs;

    /**
     * @var string
     */
    protected $actionLocKey;

    /**
     * @var string
     */
    protected $locKey;

    /**
     * @var array
     */
    protected $locArgs;

    /**
     * @var int
     */
    protected $badge;

    /**
     * @var string
     */
    protected $sound;

    /**
     * @var bool
     */
    protected $contentAvailable = true;

    /**
     * @var string
     */
    protected $category;

    /**
     * @var string
     */
    protected $threadId;

    /**
     * @var array
     */
    protected $customData = [];

    /**
     * @return \Pushok\Payload
     */
    public function toPayload(): Payload
    {
        $payload = Payload::create();

        $payload->setContentAvailability($this->contentAvailable);

        if ($this->contentAvailable === true) {
            $payload->setAlert($this->buildAlert());
        }

        if (isset($this->badge)) {
            $payload->setBadge($this->badge);
        }

        if (isset($this->sound)) {
            $payload->setSound($this->sound);
        }

        if (isset($this->category)) {
            $payload->setCategory($this->category);
        }

        if (isset($this->threadId)) {
            $payload->setThreadId($this->threadId);
        }

        if (isset($this->customData)) {
            foreach ($this->customData as $key => $value) {
                $payload->setCustomValue($key, $value);
            }
        }

        return $payload;
    }

    /**
     * @return \Pushok\Payload\Alert
     */
    protected function buildAlert(): Alert
    {
        $alert = Alert::create();

        if (isset($this->title)) {
            $alert->setTitle($this->title);
        }

        if (isset($this->body)) {
            $alert->setBody($this->body);
        }

        if (isset($this->titleLocKey)) {
            $alert->setTitleLocKey($this->titleLocKey);
        }

        if (isset($this->titleLocArgs)) {
            $alert->setTitleLocArgs($this->titleLocArgs);
        }

        if (isset($this->actionLocKey)) {
            $alert->setActionLocKey($this->actionLocKey);
        }

        if (isset($this->locKey)) {
            $alert->setLocKey($this->locKey);
        }

        if (isset($this->locArgs)) {
            $alert->setLocArgs($this->locArgs);
        }

        return $alert;
    }

    /**
     * @param string $title
     * @return Message
     */
    public function setTitle(string $title): Message
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @param string $body
     * @return Message
     */
    public function setBody(string $body): Message
    {
        $this->body = $body;

        return $this;
    }

    /**
     * @param string|null $titleLocKey
     * @return Message
     */
    public function setTitleLocKey(?string $titleLocKey): Message
    {
        $this->titleLocKey = $titleLocKey;

        return $this;
    }

    /**
     * @param null|array $titleLocArgs
     * @return Message
     */
    public function setTitleLocArgs(?array $titleLocArgs): Message
    {
        $this->titleLocArgs = $titleLocArgs;

        return $this;
    }

    /**
     * @param string $locKey
     * @return Message
     */
    public function setLocKey(string $locKey): Message
    {
        $this->locKey = $locKey;

        return $this;
    }

    /**
     * @param array $locArgs
     * @return Message
     */
    public function setLocArgs(array $locArgs): Message
    {
        $this->locArgs = $locArgs;

        return $this;
    }

    /**
     * @param int $badge
     * @return Message
     */
    public function setBadge(int $badge): Message
    {
        $this->badge = $badge;

        return $this;
    }

    /**
     * @param string $sound
     * @return Message
     */
    public function setSound(string $sound): Message
    {
        $this->sound = $sound;

        return $this;
    }

    /**
     * @param bool $contentAvailable
     * @return Message
     */
    public function setContentAvailable(bool $contentAvailable): Message
    {
        $this->contentAvailable = $contentAvailable;

        return $this;
    }

    /**
     * @param string $category
     * @return Message
     */
    public function setCategory(string $category): Message
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @param string $threadId
     * @return Message
     */
    public function setThreadId(string $threadId): Message
    {
        $this->threadId = $threadId;

        return $this;
    }

    /**
     * @param array $customData
     * @return \KingsCode\LaravelApnsNotificationChannel\Message
     */
    public function setCustomData(array $customData): Message
    {
        $this->customData = $customData;

        return $this;
    }

    /**
     * @return \KingsCode\LaravelApnsNotificationChannel\Message
     */
    public static function create()
    {
        return new static();
    }
}
