<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="LogSmsKeluar")
 * @ORM\Table(name="log_sms_keluar")
 */
class LogSmsKeluar
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
    private $tujuan;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $pesan;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $tanggal;

    /**
     * @ORM\ManyToOne(targetEntity="Pengingat")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $pengingat;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setTujuan(string $tujuan)
    {
        $this->tujuan = $tujuan;
    }

    public function getTujuan(): ?string
    {
        return $this->tujuan;
    }

    public function setPesan(string $pesan)
    {
        $this->pesan = $pesan;
    }

    public function getPesan(): ?string
    {
        return $this->pesan;
    }

    public function setTanggal(?\DateTimeInterface $tanggal)
    {
        $this->tanggal = $tanggal;
    }

    public function getTanggal(): ?\DateTimeInterface
    {
        return $this->tanggal;
    }

    public function setPengingat(?Pengingat $pengingat)
    {
        $this->pengingat = $pengingat;
    }

    public function getPengingat(): ?Pengingat
    {
        return $this->pengingat;
    }
}
