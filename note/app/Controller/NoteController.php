<?php

namespace App\Controller;

use App\Service\NoteService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;

#[Controller]
class NoteController
{
    #[Inject]
    protected NoteService $noteService;

    #[GetMapping('/notes/info')]
    public function getNoteInfo()
    {
        return $this->noteService->getNoteInfo();
    }
}