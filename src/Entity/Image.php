<?php

namespace App\Entity;

use App\Repository\ImageRepository;
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

    private $params;

    public function __construct()
    {
        $container = new Container();
        $this->params = $container->getParameterBag();
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
        $file = new File($this->params->get('img_dir') . $this->file);
        return $file;
    }

    public function setFile(UploadedFile $file): self
    {
        $file->move($this->params->get('img_dir'));
        $this->file = $file->getFilename();
        $this->name = $file->getClientOriginalName();

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    /**
     * @ORM\PreRemove()
     */
    public function removeFile()
    {
        if (file_exists($this->params->get('img_dir') . $this->file)) {
            unlink($this->params->get('img_dir') . $this->file);
        }
    }
}
