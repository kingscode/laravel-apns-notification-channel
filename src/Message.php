<?php

namespace App\Push\Apn;

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
     * @var int
     */
    protected $contentAvailable;

    /**
     * @var string
     */
    protected $category;

    /**
     * @var string
     */
    protected $threadId;

    /**
     * @return array
     */
    protected function build()
    {
        $payload = [];

        $payload['aps']['alert'] = $this->buildAlert();

        if (isset($this->badge)) {
            $payload['badge'] = $this->badge;
        }

        if (isset($this->sound)) {
            $payload['sound'] = $this->sound;
        }

        if (isset($this->contentAvailable)) {
            $payload['content-available'] = $this->contentAvailable;
        }

        if (isset($this->category)) {
            $payload['category'] = $this->category;
        }

        if (isset($this->threadId)) {
            $payload['thread-id'] = $this->threadId;
        }

        return $payload;
    }

    /**
     * @return array
     */
    protected function buildAlert()
    {
        $payload = [];

        if (isset($this->title)) {
            $payload['title'] = $this->title;
        }

        if (isset($this->body)) {
            $payload['body'] = $this->body;
        }

        if (isset($this->titleLocKey)) {
            $payload['title-loc-key'] = $this->titleLocKey;
        }

        if (isset($this->titleLocArgs)) {
            $payload['title-loc-args'] = $this->titleLocArgs;
        }

        if (isset($this->actionLocKey)) {
            $payload['action-loc-key'] = $this->actionLocKey;
        }

        if (isset($this->locKey)) {
            $payload['loc-key'] = $this->locKey;
        }

        if (isset($this->locArgs)) {
            $payload['loc-args'] = $this->locArgs;
        }

        return $payload;
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
     * @param array|null $titleLocKey
     * @return Message
     */
    public function setTitleLocKey(?array $titleLocKey): Message
    {
        $this->titleLocKey = $titleLocKey;

        return $this;
    }

    /**
     * @param null|string $titleLocArgs
     * @return Message
     */
    public function setTitleLocArgs(?string $titleLocArgs): Message
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
     * @param int $contentAvailable
     * @return Message
     */
    public function setContentAvailable(int $contentAvailable): Message
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
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->toPayload();
    }

    /**
     * @return array
     */
    public function toJson()
    {
        return $this->toArray();
    }

    public function toPayload()
    {
        return $this->build();
    }

}
