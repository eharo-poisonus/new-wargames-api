<?php

namespace App\Communication\Emails\Domain;

interface EmailSender
{
    public function send(EmailMessage $emailMessage): void;
}
