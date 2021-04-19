<?php


namespace SallePW\SlimApp\Model;

class Game
{
    public function __construct(
        private String $title,
        private int $gameid,
        private float $price,
        private String $thumbnail
    ) {}

    /**
     * @return String
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return int
     */
    public function getGameId(): int
    {
        return $this->gameid;
    }

    /**
     * @return float
     */
    public function getPrice(): float
    {
        return $this->price;
    }

    /**
     * @return String
     */
    public function getThumbnail(): string
    {
        return $this->thumbnail;
    }
}