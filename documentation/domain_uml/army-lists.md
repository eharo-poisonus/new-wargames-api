# ArmyLists

`RosterUnit` guarda una copia del nombre y el coste para que una lista no cambie cuando se actualicen los puntos oficiales en [Catalog](catalog.md).

```mermaid
classDiagram
direction TB
class Roster {
  <<AggregateRoot>>
  -RosterId id
  -PlayerId ownerId
  -GameId gameId
  -FactionId factionId
  -RosterName name
  -string notes
  -Points pointsLimit
  -List~Squad~ squads
  -List~RosterUnit~ units
  +addUnit(RosterUnit)
  +addSquad(string) Squad
  +assignToSquad(UnitId, SquadId)
  +totalPoints() Points
  +isWithinLimit() bool
}
class Squad {
  <<Entity>>
  -SquadId id
  -string name
  -List~RosterUnit~ units
}
class RosterUnit {
  <<Entity>>
  -UnitId id
  -UnitProfileId profileId
  -string name
  -string officerName
  -Points cost
}
class Points {
  <<ValueObject>>
  -int value
  +add(Points) Points
  +exceeds(Points) bool
}
class Game {
  <<Catalog>>
}
class UnitProfile {
  <<Catalog>>
}
class Player {
  <<Community>>
}
Roster "1" *-- "0..*" Squad
Roster "1" *-- "0..*" RosterUnit
Squad o-- "0..*" RosterUnit
Roster ..> Points
Roster ..> Game : gameId, factionId
Roster ..> Player : ownerId
RosterUnit ..> UnitProfile : profileId
```

Invariante del agregado: una unidad solo puede estar en una escuadra, y la escuadra debe pertenecer a la misma lista. Lo garantiza `Roster::assignToSquad()`.
