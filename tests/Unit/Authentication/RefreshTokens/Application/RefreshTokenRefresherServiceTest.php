<?php

namespace App\Tests\Unit\Authentication\RefreshTokens\Application;

use App\Authentication\Accounts\Domain\Account;
use App\Authentication\Accounts\Domain\AccountRepository;
use App\Authentication\Accounts\Domain\Exceptions\AccountDoesNotExistException;
use App\Authentication\Accounts\Domain\ValueObjects\AccountId;
use App\Authentication\Accounts\Domain\ValueObjects\AccountType;
use App\Authentication\Accounts\Domain\ValueObjects\Email;
use App\Authentication\Accounts\Domain\ValueObjects\HashedPassword;
use App\Authentication\Accounts\Domain\ValueObjects\TermsVersion;
use App\Authentication\Accounts\Domain\ValueObjects\Username;
use App\Authentication\Accounts\Infrastructure\Security\JwtAccessTokenUtils;
use App\Authentication\RefreshTokens\Application\CreateRefreshToken\RefreshTokenCreatorService;
use App\Authentication\RefreshTokens\Application\RevokeRefreshTokens\RefreshTokensRevokerService;
use App\Authentication\RefreshTokens\Application\RefreshRefreshToken\RefreshTokenRefresherService;
use App\Authentication\RefreshTokens\Domain\Exceptions\InvalidRefreshTokenException;
use App\Authentication\RefreshTokens\Domain\Exceptions\RefreshTokenExpiredException;
use App\Authentication\RefreshTokens\Domain\Exceptions\RefreshTokenRevokedException;
use App\Authentication\RefreshTokens\Domain\RefreshToken;
use App\Authentication\RefreshTokens\Domain\ValueObjects\RefreshTokenId;
use App\Authentication\RefreshTokens\Infrastructure\Tokens\RefreshTokenGenerator;
use App\Authentication\RefreshTokens\Infrastructure\Tokens\Sha256TokenHasher;
use App\Authentication\Sessions\Domain\Exceptions\SessionDoesNotExistException;
use App\Authentication\Sessions\Domain\Exceptions\SessionExpiredException;
use App\Authentication\Sessions\Domain\Session;
use App\Authentication\Sessions\Domain\SessionRepository;
use App\Authentication\Sessions\Domain\ValueObjects\SessionId;
use App\Tests\Unit\Authentication\RefreshTokens\Doubles\InMemoryRefreshTokenRepository;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

final class RefreshTokenRefresherServiceTest extends TestCase
{
    private const string JWT_SECRET = 'a-test-secret-that-is-at-least-32-characters-long';
    private const string PLAIN_REFRESH_TOKEN = 'a-perfectly-valid-refresh-token';

    private InMemoryRefreshTokenRepository $refreshTokenRepository;
    private Sha256TokenHasher $refreshTokenHasher;
    private JwtAccessTokenUtils $accessTokenUtils;
    private SessionId $sessionId;
    private AccountId $accountId;

    protected function setUp(): void
    {
        $this->refreshTokenRepository = new InMemoryRefreshTokenRepository();
        $this->refreshTokenHasher = new Sha256TokenHasher();
        $this->accessTokenUtils = new JwtAccessTokenUtils(self::JWT_SECRET, 'HS256', 900);
        $this->sessionId = SessionId::random();
        $this->accountId = AccountId::random();
    }

    public function testRotatesTheRefreshTokenAndIssuesANewAccessToken(): void
    {
        $currentRefreshToken = $this->storeRefreshToken(self::PLAIN_REFRESH_TOKEN);

        $response = ($this->service())($this->sessionId->value(), self::PLAIN_REFRESH_TOKEN);

        self::assertNotSame(self::PLAIN_REFRESH_TOKEN, $response->refreshToken());
        self::assertSame($this->sessionId->value(), $response->sessionId());
        self::assertTrue($this->accessTokenUtils->verify($response->jsonSerialize()['access_token']));
        self::assertTrue($currentRefreshToken->isRevoked());

        $stored = $this->liveRefreshTokens();
        self::assertCount(1, $stored);
        self::assertTrue(
            $this->refreshTokenHasher->verify($response->refreshToken(), $stored[0]->hashedToken())
        );
    }

    public function testKeepsTheSubjectOfTheAccountInTheNewAccessToken(): void
    {
        $this->storeRefreshToken(self::PLAIN_REFRESH_TOKEN);

        $response = ($this->service())($this->sessionId->value(), self::PLAIN_REFRESH_TOKEN);

        $claims = $this->accessTokenUtils->retrieveClaims($response->jsonSerialize()['access_token']);

        self::assertSame($this->accountId->value(), $claims->sub()->value());
    }

    public function testFailsWhenTheRefreshTokenIsEmpty(): void
    {
        $this->expectException(InvalidRefreshTokenException::class);

        ($this->service())($this->sessionId->value(), '');
    }

    public function testFailsWhenTheRefreshTokenIsUnknown(): void
    {
        $this->storeRefreshToken(self::PLAIN_REFRESH_TOKEN);

        $this->expectException(InvalidRefreshTokenException::class);

        ($this->service())($this->sessionId->value(), 'another-refresh-token');
    }

    public function testFailsWhenTheRefreshTokenIsExpired(): void
    {
        $this->storeRefreshToken(self::PLAIN_REFRESH_TOKEN, expiresAt: new DateTimeImmutable('-1 second'));

        $this->expectException(RefreshTokenExpiredException::class);

        ($this->service())($this->sessionId->value(), self::PLAIN_REFRESH_TOKEN);
    }

    public function testRevokesEveryLiveRefreshTokenOfTheSessionWhenAnAlreadyUsedOneIsReplayed(): void
    {
        $this->storeRefreshToken(self::PLAIN_REFRESH_TOKEN, revokedAt: new DateTimeImmutable('-1 minute'));
        $this->storeRefreshToken('the-rotated-refresh-token');

        try {
            ($this->service())($this->sessionId->value(), self::PLAIN_REFRESH_TOKEN);
            self::fail(sprintf('Expected a %s', RefreshTokenRevokedException::class));
        } catch (RefreshTokenRevokedException) {
        }

        self::assertSame([], $this->liveRefreshTokens());
    }

    public function testFailsWhenTheSessionIdIsMissing(): void
    {
        $this->storeRefreshToken(self::PLAIN_REFRESH_TOKEN);

        $this->expectException(SessionDoesNotExistException::class);

        ($this->service())('', self::PLAIN_REFRESH_TOKEN);
    }

    public function testFailsWhenTheSessionIdIsNotAUuid(): void
    {
        $this->storeRefreshToken(self::PLAIN_REFRESH_TOKEN);

        $this->expectException(SessionDoesNotExistException::class);

        ($this->service())('not-a-uuid', self::PLAIN_REFRESH_TOKEN);
    }

    public function testFailsWhenTheRefreshTokenBelongsToAnotherSession(): void
    {
        $this->storeRefreshToken(self::PLAIN_REFRESH_TOKEN, sessionId: SessionId::random());

        $this->expectException(InvalidRefreshTokenException::class);

        ($this->service())($this->sessionId->value(), self::PLAIN_REFRESH_TOKEN);
    }

    public function testFailsWhenTheAccountDoesNotExist(): void
    {
        $this->storeRefreshToken(self::PLAIN_REFRESH_TOKEN);

        $this->expectException(AccountDoesNotExistException::class);

        ($this->service(accountExists: false))($this->sessionId->value(), self::PLAIN_REFRESH_TOKEN);
    }

    public function testFailsWhenTheSessionDoesNotExist(): void
    {
        $this->storeRefreshToken(self::PLAIN_REFRESH_TOKEN);

        $this->expectException(SessionDoesNotExistException::class);

        ($this->service(sessionExists: false))($this->sessionId->value(), self::PLAIN_REFRESH_TOKEN);
    }

    public function testFailsWhenTheSessionIsExpired(): void
    {
        $this->storeRefreshToken(self::PLAIN_REFRESH_TOKEN);

        $this->expectException(SessionExpiredException::class);

        ($this->service(session: $this->session(new DateTimeImmutable('-1 second'))))(
            $this->sessionId->value(),
            self::PLAIN_REFRESH_TOKEN
        );
    }

    private function service(
        ?Session $session = null,
        ?Account $account = null,
        bool $sessionExists = true,
        bool $accountExists = true
    ): RefreshTokenRefresherService {
        $sessionRepository = $this->createMock(SessionRepository::class);
        $sessionRepository->method('id')->willReturn($sessionExists ? $session ?? $this->session() : null);

        $accountRepository = $this->createMock(AccountRepository::class);
        $accountRepository->method('id')->willReturn($accountExists ? $account ?? $this->account() : null);

        return new RefreshTokenRefresherService(
            $this->refreshTokenRepository,
            $sessionRepository,
            $accountRepository,
            $this->refreshTokenHasher,
            new RefreshTokenGenerator(),
            new RefreshTokenCreatorService($this->refreshTokenRepository),
            new RefreshTokensRevokerService($this->refreshTokenRepository),
            $this->accessTokenUtils,
            2592000
        );
    }

    private function storeRefreshToken(
        string $plainToken,
        ?DateTimeImmutable $expiresAt = null,
        ?DateTimeImmutable $revokedAt = null,
        ?SessionId $sessionId = null
    ): RefreshToken {
        $refreshToken = new RefreshToken(
            RefreshTokenId::random(),
            $sessionId ?? $this->sessionId,
            $this->refreshTokenHasher->hash($plainToken),
            new DateTimeImmutable('-1 hour'),
            $expiresAt ?? new DateTimeImmutable('+30 days'),
            $revokedAt
        );

        $this->refreshTokenRepository->save($refreshToken);

        return $refreshToken;
    }

    private function liveRefreshTokens(): array
    {
        return array_values(
            array_filter(
                $this->refreshTokenRepository->all(),
                fn (RefreshToken $refreshToken) => !$refreshToken->isRevoked()
            )
        );
    }

    private function session(?DateTimeImmutable $expiresAt = null): Session
    {
        return new Session(
            $this->sessionId,
            $this->accountId,
            'a-device',
            '127.0.0.1',
            new DateTimeImmutable('-1 hour'),
            $expiresAt ?? new DateTimeImmutable('+30 days')
        );
    }

    private function account(): Account
    {
        $account = Account::create(
            $this->accountId,
            AccountType::PERSONAL,
            Username::fromString('wargamer'),
            Email::fromString('wargamer@wargames.test'),
            HashedPassword::fromString('a-hashed-password'),
            null,
            false,
            true,
            TermsVersion::VERSION_1,
            'a-hashed-activation-token',
            'a-plain-activation-token'
        );

        $account->activate();
        $account->pullDomainEvents();

        return $account;
    }
}
