<?php

namespace App\Service;

class PengirimPesan
{
    private $perintah;
    private $tujuan;
    private $pesan;

    public function __construct($perintah)
    {
        $this->perintah = $perintah;
    }

    public function setTujuan(string $tujuan)
    {
        $this->tujuan = $tujuan;
    }

    public function setPesan(string $pesan)
    {
        $this->pesan = $pesan;
    }

    private function siapkanParameter()
    {
        $this->perintah = str_replace('%nomor%', urlencode($this->tujuan), $this->perintah);
        $this->perintah = str_replace('%pesan%', urlencode($this->pesan), $this->perintah);
    }

    public function kirim()
    {
        $this->siapkanParameter();

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);

        curl_setopt($ch, CURLOPT_URL, $this->perintah);
        curl_exec($ch);

        curl_close($ch);
    }
}
