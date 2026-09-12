<?php

namespace App\Identity\Players\Domain;

use App\Authentication\Accounts\Domain\ValueObjects\AccountId;
use App\Identity\Players\Domain\ValueObjects\Gender;
use App\Identity\Players\Domain\ValueObjects\PlayerId;
use App\Identity\Players\Domain\ValueObjects\Username;
use App\Shared\Domain\Aggregate\AggregateRoot;
use DateTimeImmutable;

class Player extends AggregateRoot
{
    public function __construct(
        private PlayerId $id,
        private ?AccountId $accountId,
        private Username $username,
        private ?string $avatarUrl,
        private ?string $coverUrl,
        private ?string $bio,
        private Gender $gender,
        private ?DateTimeImmutable $dateOfBirth,
        private ?string $country,
        private ?string $state,
        private ?string $city,
        private ?string $zipCode,
        private DateTimeImmutable $createdAt,
        private ?DateTimeImmutable $updatedAt,
        private ?DateTimeImmutable $deletedAt
    ) {
    }

    public static function create(
        PlayerId $id,
        AccountId $accountId,
        Username $username
    ): self {
        return new self(
            $id,
            $accountId,
            $username,
            null,
            null,
            null,
            Gender::UNKNOWN,
            null,
            null,
            null,
            null,
            null,
            new DateTimeImmutable(),
            null,
            null
        );
    }

    public function id(): PlayerId
    {
        return $this->id;
    }

    public function setId(PlayerId $id): void
    {
        $this->id = $id;
    }

    public function accountId(): ?AccountId
    {
        return $this->accountId;
    }

    public function setAccountId(?AccountId $accountId): void
    {
        $this->accountId = $accountId;
    }

    public function username(): Username
    {
        return $this->username;
    }

    public function setUsername(Username $username): void
    {
        $this->username = $username;
    }

    public function avatarUrl(): ?string
    {
        return $this->avatarUrl;
    }

    public function setAvatarUrl(?string $avatarUrl): void
    {
        $this->avatarUrl = $avatarUrl;
    }

    public function coverUrl(): ?string
    {
        return $this->coverUrl;
    }

    public function setCoverUrl(?string $coverUrl): void
    {
        $this->coverUrl = $coverUrl;
    }

    public function bio(): ?string
    {
        return $this->bio;
    }

    public function setBio(?string $bio): void
    {
        $this->bio = $bio;
    }

    public function gender(): Gender
    {
        return $this->gender;
    }

    public function setGender(Gender $gender): void
    {
        $this->gender = $gender;
    }

    public function dateOfBirth(): ?string
    {
        return $this->dateOfBirth;
    }

    public function setDateOfBirth(?string $dateOfBirth): void
    {
        $this->dateOfBirth = $dateOfBirth;
    }

    public function country(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): void
    {
        $this->country = $country;
    }

    public function state(): ?string
    {
        return $this->state;
    }

    public function setState(?string $state): void
    {
        $this->state = $state;
    }

    public function city(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): void
    {
        $this->city = $city;
    }

    public function zipCode(): ?string
    {
        return $this->zipCode;
    }

    public function setZipCode(?string $zipCode): void
    {
        $this->zipCode = $zipCode;
    }

    public function createdAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeImmutable $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function updatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?DateTimeImmutable $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    public function deletedAt(): ?DateTimeImmutable
    {
        return $this->deletedAt;
    }

    public function setDeletedAt(?DateTimeImmutable $deletedAt): void
    {
        $this->deletedAt = $deletedAt;
    }
}
