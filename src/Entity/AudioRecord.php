<?php

namespace App\Entity;

use App\Repository\AudioRecordRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=AudioRecordRepository::class)
 * @ORM\Table(name="audio_recordings")
 */
class AudioRecord
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private int $id;

    /**
     * @ORM\Column(type="string", nullable=false)
     */
    private string $name;

    /**
     * @ORM\Column(type="string", nullable=false)
     */
    private string $path;

    /**
     * @ORM\Column(name="`like`", type="boolean")
     */
    private bool $like = false;

    /**
     * @ORM\ManyToOne(targetEntity="Performer", fetch="EAGER")
     */
    private ?Performer $performer = null;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return Performer|null
     */
    public function getPerformer(): ?Performer
    {
        return $this->performer ?? new Performer();
    }

    /**
     * @param Performer|null $performer
     */
    public function setPerformer(?Performer $performer): void
    {
        $this->performer = $performer;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @param string $path
     */
    public function setPath(string $path): void
    {
        $this->path = $path;
    }

    /**
     * @return bool
     */
    public function isLike(): bool
    {
        return $this->like;
    }

    /**
     * @param bool $like
     */
    public function setLike(bool $like): void
    {
        $this->like = $like;
    }
}
