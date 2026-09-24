# CampaignDesign

La plantilla de una campaña: un grafo de nodos (misiones) y aristas (transiciones) con condiciones, más los mapas hexagonales para las campañas de mundo abierto.

```mermaid
classDiagram
direction TB
class Campaign {
  <<AggregateRoot>>
  -CampaignId id
  -GameId gameId
  -PlayerId authorId
  -CampaignType type
  -Visibility visibility
  -CampaignName name
  -string description
  -PlayerRange players
  -List~CampaignNode~ nodes
  -List~CampaignEdge~ edges
  -List~MapId~ mapIds
  +addNode(CampaignNode)
  +connect(NodeId, NodeId, string, List~EdgeCondition~)
  +startNode() CampaignNode
  +outgoingEdges(NodeId) List~CampaignEdge~
  +publish()
}
class CampaignNode {
  <<Entity>>
  -NodeId id
  -string name
  -string description
  -MissionTypeId missionTypeId
  -string mapSetupNotes
  -bool isStart
  -bool isEnd
}
class CampaignEdge {
  <<Entity>>
  -EdgeId id
  -NodeId source
  -NodeId target
  -string label
  -string description
  -List~EdgeCondition~ conditions
  +isSatisfiedBy(List~ConditionState~) bool
}
class EdgeCondition {
  <<Entity>>
  -ConditionId id
  -Condition condition
  -bool mandatory
}
class Condition {
  <<ValueObject>>
  -ConditionTypeCode type
  -MissionEntityId target
  -Operator operator
  -RequiredValue required
  -PreviousRole appliesTo
  +evaluate(ActualValue) bool
}
class RequiredValue {
  <<ValueObject>>
  -int number
  -bool flag
}
class PlayerRange {
  <<ValueObject>>
  -int min
  -int max
}
class Visibility {
  <<ValueObject>>
  -bool global
  -bool public
}
class MissionType {
  <<AggregateRoot>>
  -MissionTypeId id
  -string name
  -string description
}
class MissionEntity {
  <<AggregateRoot>>
  -MissionEntityId id
  -MissionEntityType type
  -string name
}
class ConditionType {
  <<AggregateRoot>>
  -ConditionTypeCode code
  -string name
}
class Map {
  <<AggregateRoot>>
  -MapId id
  -string name
  -MapTheme theme
  -Dimensions size
  -List~Tile~ tiles
  +tileAt(HexCoord) Tile
}
class Tile {
  <<ValueObject>>
  -HexCoord coord
  -TerrainType terrain
}
class HexCoord {
  <<ValueObject>>
  -int q
  -int r
  +neighbors() List~HexCoord~
}
class Game {
  <<Catalog>>
}
class Player {
  <<Community>>
}
Campaign "1" *-- "1..*" CampaignNode
Campaign "1" *-- "0..*" CampaignEdge
Campaign *-- PlayerRange
Campaign *-- Visibility
CampaignEdge "1" *-- "0..*" EdgeCondition
EdgeCondition *-- Condition
Condition *-- RequiredValue
Map "1" *-- "1..*" Tile
Tile *-- HexCoord
CampaignNode ..> MissionType : missionTypeId
Condition ..> MissionEntity : target
Condition ..> ConditionType : type
Campaign ..> Map : mapIds
Campaign ..> Game : gameId
Campaign ..> Player : authorId
```

Enums: `CampaignType` (LINEAR, BRANCHING, OPEN_WORLD), `Operator` (EQUALS, NOT_EQUALS, GREATER_THAN, LESS_THAN, IN_LIST), `PreviousRole` (WINNER, LOSER, PLAYER_A, PLAYER_B, ANY), `TerrainType`, `MapTheme`, `MissionEntityType`.
