<?php

namespace App\Entity;

use App\Repository\JokeRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=JokeRepository::class)
 */
class Joke
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     */
    private $Joke;

    /**
     * getId
     *
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * getJoke
     *
     * @return string|null
     */
    public function getJoke(): ?string
    {
        return $this->Joke;
    }

    /**
     * setJoke
     *
     * @param string $Joke
     * @return Joke
     */
    public function setJoke(string $Joke): self
    {
        $this->Joke = $Joke;

        return $this;
    }

    /**
     * toArray
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'id'   => $this->getId(),
            'joke' => $this->getJoke()
        ];
    }
}
