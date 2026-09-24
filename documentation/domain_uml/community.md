# Community

Perfil público. Tienda, club y partner son `Organization` con distinto `OrganizationType`. La relación con la cuenta la da `Membership`.

```mermaid
classDiagram
direction TB
class Player {
  <<AggregateRoot>>
  -PlayerId id
  -AccountId accountId
  -Username username
  -Biography bio
  -BirthDate birthDate
  -ImageUrl avatar
  -ImageUrl cover
  -List~Address~ addresses
  +rename(Username)
  +addAddress(Address)
}
class Organization {
  <<AggregateRoot>>
  -OrganizationId id
  -OrganizationType type
  -string name
  -Location location
}
class Membership {
  <<AggregateRoot>>
  -AccountId accountId
  -OrganizationId organizationId
  -MembershipRole role
}
class Address {
  <<ValueObject>>
  -string label
  -Location location
}
class Location {
  <<ValueObject>>
  -CountryCode country
  -string state
  -string city
  -ZipCode zipCode
  -string street
  -GeoPoint geo
}
class Account {
  <<Identity>>
}
Player "1" *-- "0..*" Address
Address *-- Location
Organization *-- Location
Player ..> Account : accountId
Membership ..> Account : accountId
Membership ..> Organization : organizationId
```

`city`, `zipCode` y `street` son obligatorios para tiendas. Esa regla la valida `Organization`, no `Location`.
