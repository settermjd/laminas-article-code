<?php

namespace App\Entity;

class LinkedInPost
{
    private int $id;
    private string $title;
    private string $body;
    private string $publishOn;

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function setBody(string $body): void
    {
        $this->body = $body;
    }

    public function setPublishOn(string $publishOn): void
    {
        $this->publishOn = $publishOn;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function getPublishOn(): string
    {
        return $this->publishOn;
    }

}