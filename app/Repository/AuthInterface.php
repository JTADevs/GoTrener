<?php

namespace App\Repository;

interface AuthInterface
{
    public function register(array $data);
    public function login(array $data);
    public function login_google(array $data);
    public function confirm(array $data);
    public function setRole($uid, $role);
}