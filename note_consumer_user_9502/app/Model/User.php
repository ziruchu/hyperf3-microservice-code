<?php

namespace App\Model;

class User extends \Hyperf\DbConnection\Model\Model
{
    protected ?string $table = 'users';

    protected array $guarded = [];

}