<?php

namespace App\Entity;

use App\Repository\ImageRepository;
use App\Services\ImageResize;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @ORM\Entity(repositoryClass=ImageRepository::class)
 * @ORM\HasLifecycleCallbacks()
 */
class Image
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $file;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date;

    public function __construct()
    {
        $this->date = new \DateTime('now');
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

    public function getFile(): ?File
    {
        $file = new File($_ENV['IMG_DIR'] . $this->file);
        return $file;
    }

    public function setFile(?UploadedFile $file): self
    {
        $file->move($_ENV['IMG_DIR']);
        $this->file = $file->getFilename();
        $this->name = $file->getClientOriginalName();

        $resizer = new ImageResize($this->getFile()->getRealPath());
        $resizer->resize('small', $_ENV['SMALL']);
        $resizer->resize('big', $_ENV['BIG'], 0, 95);

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(?\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    /**
     * @ORM\PreRemove()
     */
    public function removeFile()
    {
        if (file_exists($_ENV['IMG_DIR'] . $this->file)) {
            unlink($_ENV['IMG_DIR'] . $this->file);
        }
        if (file_exists($_ENV['IMG_DIR'] . $this->file. '_small')) {
            unlink($_ENV['IMG_DIR'] . $this->file . '_small');
        }
        if (file_exists($_ENV['IMG_DIR'] . $this->file. '_big')) {
            unlink($_ENV['IMG_DIR'] . $this->file . '_big');
        }
    }
}
