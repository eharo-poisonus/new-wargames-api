<?php

namespace App\Authentication\Accounts\Application\SignIn;

use App\Authentication\Accounts\Domain\Exceptions\AccountDeletedException;
use App\Authentication\Accounts\Domain\Exceptions\AccountNotActivatedException;
use App\Authentication\Accounts\Domain\Exceptions\AccountNotVerifiedException;
use App\Authentication\Accounts\Domain\Exceptions\InvalidPasswordException;
use App\Authentication\Accounts\Domain\Exceptions\MoreThanOneAccountWithSameEmailException;
use App\Authentication\Accounts\Domain\Exceptions\TermsNotAcceptedException;
use App\Authentication\Accounts\Domain\ValueObjects\Email;
use App\Authentication\Accounts\Domain\ValueObjects\PlainPassword;
use App\Shared\Domain\Bus\Query\QueryHandler;

final readonly class SignInQueryHandler implements QueryHandler
{
    public function __construct(
        private SignInService $service
    ) {
    }

    /**
     * @throws TermsNotAcceptedException|AccountNotVerifiedException|AccountDeletedException
     * @throws MoreThanOneAccountWithSameEmailException|AccountNotActivatedException|InvalidPasswordException
     */
    public function __invoke(SignInQuery $query): SignInResponse
    {
        return ($this->service)(
            Email::fromString($query->email()),
            PlainPassword::fromString($query->password()),
            $query->device(),
            $query->ipAddress()
        );
    }
}
