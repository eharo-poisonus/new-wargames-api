<?php

namespace App\Communication\Emails\Domain;

use App\Platform\Communications\Domain\Exceptions\InvalidEmailRecipientException;

final readonly class EmailMessage
{
    public function __construct(
        private string $to,
        private string $toName,
        private string $subject,
        private string $htmlBody,
        private string $textBody
    ) {
        if (filter_var($this->to, FILTER_VALIDATE_EMAIL) === false) {
            throw new InvalidEmailRecipientException();
        }
    }

    public function to(): string
    {
        return $this->to;
    }

    public function toName(): string
    {
        return $this->toName;
    }

    public function subject(): string
    {
        return $this->subject;
    }

    public function htmlBody(): string
    {
        return $this->htmlBody;
    }

    public function textBody(): string
    {
        return $this->textBody;
    }
}
