<?php

namespace App\Communication\AccountValidation\Application\SendEmail;

use App\Authentication\Accounts\Domain\AccountRepository;
use App\Authentication\Accounts\Domain\ValueObjects\AccountId;
use App\Communication\Emails\Domain\EmailMessage;
use App\Communication\Emails\Domain\EmailSender;
use App\Identity\Players\Domain\ValueObjects\Username;

final readonly class ValidationEmailSenderService
{
    public function __construct(
        private AccountRepository $accountRepository,
        private EmailSender $emailSender
    ) {
    }

    public function __invoke(AccountId $id, string $validationToken, Username $username): void
    {
        $targetAccount = $this->accountRepository->id($id);

        $url = sprintf('http://localhost:82/api/accounts/%s/activate/%s', $id, $validationToken);

        $this->emailSender->send(
            new EmailMessage(
                $targetAccount->email()->value(),
                $username,
                "Te damos la bienvenida, " . $username,
                '<p>Tu cuenta de WarTable está lista, solo falta activarla.</p> <a href="'.$url.'">Activar</a><p>O copia y pega este enlace: '.$url.'</p>',
                ''
            )
        );
    }
}
