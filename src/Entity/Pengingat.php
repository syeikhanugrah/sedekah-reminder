<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PengingatRepository")
 * @ORM\Table(name="pengingat")
 */
class Pengingat
{
    /**
     * @var int
     *
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $judul;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $tanggalAwal;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $tanggalAkhir;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $selamanya;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=100)
     */
    private $perulangan;

    /**
     * @var int
     *
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $mingguanHariKe;

    /**
     * @var int
     *
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $bulananHariKe;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setJudul(?string $judul)
    {
        $this->judul = $judul;
    }

    public function getJudul(): ?string
    {
        return $this->judul;
    }

    public function setTanggalAwal(?\DateTime $tanggalAwal)
    {
        $this->tanggalAwal = $tanggalAwal;
    }

    public function getTanggalAwal(): ?\DateTime
    {
        return $this->tanggalAwal;
    }

    public function setTanggalAkhir(?\DateTime $tanggalAkhir)
    {
        $this->tanggalAkhir = $tanggalAkhir;
    }

    public function getTanggalAkhir(): ?\DateTime
    {
        return $this->tanggalAkhir;
    }

    public function setSelamanya(?bool $selamanya)
    {
        $this->selamanya = $selamanya;
    }

    public function isSelamanya(): ?bool
    {
        return $this->selamanya;
    }

    public function setPerulangan(string $perulangan)
    {
        $this->perulangan = $perulangan;
    }

    public function getPerulangan(): ?string
    {
        return $this->perulangan;
    }

    public function setMingguanHariKe(?int $mingguanHariKe)
    {
        $this->mingguanHariKe = $mingguanHariKe;
    }

    public function getMingguanHariKe(): ?int
    {
        return $this->mingguanHariKe;
    }

    public function setBulananHariKe(?int $bulananHariKe)
    {
        $this->bulananHariKe = $bulananHariKe;
    }

    public function getBulananHariKe(): ?int
    {
        return $this->bulananHariKe;
    }

    public function setUser(User $user)
    {
        $this->user = $user;
    }

    public function getUser(): User
    {
        return $this->user;
    }
}
