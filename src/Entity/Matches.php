<?php

namespace App\Entity;

use App\Repository\MatchesRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MatchesRepository::class)]
class Matches
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'integer')]
    private $home_team_id;

    #[ORM\Column(type: 'integer')]
    private $away_team_id;

    #[ORM\Column(type: 'integer')]
    private $status;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private $match_date;

    #[ORM\Column(type: 'integer')]
    private $home_team_score;

    #[ORM\Column(type: 'integer')]
    private $away_team_score;

    #[ORM\Column(type: 'datetime_immutable',insertable: false,updatable: false)]
    private $created_at;

    #[ORM\Column(type: 'datetime_immutable',insertable: false,updatable: false)]
    private $updated_at;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getHomeTeamId(): ?int
    {
        return $this->home_team_id;
    }

    public function setHomeTeamId(int $home_team_id): self
    {
        $this->home_team_id = $home_team_id;

        return $this;
    }

    public function getAwayTeamId(): ?int
    {
        return $this->away_team_id;
    }

    public function setAwayTeamId(int $away_team_id): self
    {
        $this->away_team_id = $away_team_id;

        return $this;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getMatchDate(): ?\DateTimeInterface
    {
        return $this->match_date;
    }

    public function setMatchDate(?\DateTimeInterface $match_date): self
    {
        $this->match_date = $match_date;

        return $this;
    }

    public function getHomeTeamScore(): ?int
    {
        return $this->home_team_score;
    }

    public function setHomeTeamScore(int $home_team_score): self
    {
        $this->home_team_score = $home_team_score;

        return $this;
    }

    public function getAwayTeamScore(): ?int
    {
        return $this->away_team_score;
    }

    public function setAwayTeamScore(int $away_team_score): self
    {
        $this->away_team_score = $away_team_score;

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
