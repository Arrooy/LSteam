<?php


namespace SallePW\SlimApp\Model;

use DateTime;

class Game
{
    public function __construct(
        private String $title,
        private int $gameid,
        private float $price,
        private String $thumbnail,
        private int $metacriticScore,
        private DateTime $releaseDate,
// TODO: AFEGIR
//        private float $cheapestPriceEver
        private bool $owned,
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

     /**
     * @return int
     */
    public function getMetacriticScore(): int
    {
        return $this->metacriticScore;
    }

     /**
     * @return DateTime
     */
    public function getReleaseDate(): DateTime
    {
        return $this->releaseDate;
    }

     /**
     * @return bool
     */
    public function getOwned(): bool
    {
        return $this->owned;
    }

    /**
     * @return bool
     */
    public function isOwned(): bool
    {
        return $this->owned;
    }

    /**
     * @param bool $owned
     */
    public function setOwned(bool $owned): void
    {
        $this->owned = $owned;
    }



    public function getMetacritireleaseDatecStore()
    {
        return $this->metacriticScore;
    }
}