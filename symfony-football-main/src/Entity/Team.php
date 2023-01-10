<?php

namespace App\Entity;

use App\Repository\TeamRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TeamRepository::class)]
class Team
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $name = null;

    #[ORM\ManyToMany(targetEntity: Game::class, inversedBy: 'teams')]
    private Collection $Game;

    #[ORM\OneToMany(mappedBy: 'winner', targetEntity: Game::class)]
    private Collection $victory;

    public function __construct()
    {
        $this->Game = new ArrayCollection();
        $this->victory = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, Game>
     */
    public function getGame(): Collection
    {
        return $this->Game;
    }

    public function addGame(Game $game): self
    {
        if (!$this->Game->contains($game)) {
            $this->Game->add($game);
        }

        return $this;
    }

    public function removeGame(Game $game): self
    {
        $this->Game->removeElement($game);

        return $this;
    }

    /**
     * @return Collection<int, Game>
     */
    public function getVictory(): Collection
    {
        return $this->victory;
    }

    public function addVictory(Game $victory): self
    {
        if (!$this->victory->contains($victory)) {
            $this->victory->add($victory);
            $victory->setWinner($this);
        }

        return $this;
    }

    public function removeVictory(Game $victory): self
    {
        if ($this->victory->removeElement($victory)) {
            // set the owning side to null (unless already changed)
            if ($victory->getWinner() === $this) {
                $victory->setWinner(null);
            }
        }

        return $this;
    }
}
