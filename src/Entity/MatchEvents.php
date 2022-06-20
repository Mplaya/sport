<?php

namespace App\Entity;

use App\Repository\MatchEventsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MatchEventsRepository::class)]
class MatchEvents
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'integer')]
    private $match_id;

    #[ORM\Column(type: 'integer')]
    private $team_id;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $player;

    #[ORM\Column(type: 'integer')]
    private $event_id;

    #[ORM\Column(type: 'datetime_immutable',insertable: false,updatable: false)]
    private $created_at;

    #[ORM\Column(type: 'datetime_immutable',insertable: false,updatable: false)]
    private $updated_at;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMatchId(): ?int
    {
        return $this->match_id;
    }

    public function setMatchId(int $match_id): self
    {
        $this->match_id = $match_id;

        return $this;
    }

    public function getTeamId(): ?int
    {
        return $this->team_id;
    }

    public function setTeamId(int $team_id): self
    {
        $this->team_id = $team_id;

        return $this;
    }

    public function getPlayer(): ?int
    {
        return $this->player;
    }

    public function setPlayer(?int $player): self
    {
        $this->player = $player;

        return $this;
    }

    public function getEventId(): ?int
    {
        return $this->event_id;
    }

    public function setEventId(int $event_id): self
    {
        $this->event_id = $event_id;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updated_at;
    }
}
