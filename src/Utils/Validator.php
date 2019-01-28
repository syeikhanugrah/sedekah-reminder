<?php

namespace App\Utils;

use Symfony\Component\Console\Exception\InvalidArgumentException;

class Validator
{
    public function validateUsername(?string $username): string
    {
        if (empty($username)) {
            throw new InvalidArgumentException('Username tidak boleh kosong.');
        }

        if (1 !== preg_match('/^[a-z_]+$/', $username)) {
            throw new InvalidArgumentException('Username hanya boleh berisi huruf latin kecil dan garis bawah.');
        }

        return $username;
    }

    public function validatePassword(?string $plainPassword): string
    {
        if (empty($plainPassword)) {
            throw new InvalidArgumentException('Password tidak boleh kosong.');
        }

        if (mb_strlen(trim($plainPassword)) < 6) {
            throw new InvalidArgumentException('Password minimal 6 karakter.');
        }

        return $plainPassword;
    }

    public function validateEmail(?string $email): string
    {
        if (empty($email)) {
            throw new InvalidArgumentException('Email tidak boleh kosong.');
        }

        if (false === mb_strpos($email, '@')) {
            throw new InvalidArgumentException('Email tidak valid.');
        }

        return $email;
    }

    public function validateNamaLengkap(?string $namaLengkap): string
    {
        if (empty($namaLengkap)) {
            throw new InvalidArgumentException('Nama lengkap tidak boleh kosong.');
        }

        return $namaLengkap;
    }
}
