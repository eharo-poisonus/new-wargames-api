# CampaignPlay

Una partida concreta de una campaña de [CampaignDesign](campaign-design.md). `NextNodeResolver` es un servicio de dominio porque necesita la plantilla (`Campaign`) y el estado (`CampaignRun`) a la vez.

```mermaid
classDiagram
direction TB
class CampaignRun {
  <<AggregateRoot>>
  -RunId id
  -CampaignId campaignId
  -RunStatus status
  -NodeId currentNodeId
  -List~Participant~ participants
  -List~NodeResult~ results
  -List~Territory~ territories
  +join(PlayerId, RosterId)
  +start(NodeId)
  +recordNodeResult(NodeId, PlayerId, List~ConditionState~)
  +chooseEdge(EdgeId, NodeId)
  +recordTileBattle(HexCoord, TileBattle)
  +complete()
  +cancel()
}
class Participant {
  <<Entity>>
  -PlayerId playerId
  -RosterId rosterId
}
class NodeResult {
  <<Entity>>
  -NodeResultId id
  -NodeId nodeId
  -EdgeId chosenEdgeId
  -PlayerId winnerId
  -string notes
  -DateTimeImmutable completedAt
  -List~ConditionState~ conditionStates
}
class ConditionState {
  <<ValueObject>>
  -PlayerId playerId
  -ConditionId conditionId
  -bool fulfilled
  -ActualValue actual
}
class Territory {
  <<Entity>>
  -HexCoord coord
  -PlayerId ownerId
  -List~TileBattle~ battles
  +conquer(PlayerId)
}
class TileBattle {
  <<ValueObject>>
  -PlayerId playerA
  -PlayerId playerB
  -BattleOutcome outcome
  -DateTimeImmutable playedAt
}
class RunStatus {
  <<Enumeration>>
  DRAFT
  ACTIVE
  COMPLETED
  CANCELLED
}
class NextNodeResolver {
  <<DomainService>>
  +availableEdges(Campaign, NodeResult) List~CampaignEdge~
}
class Campaign {
  <<CampaignDesign>>
}
class EdgeCondition {
  <<CampaignDesign>>
}
class Roster {
  <<ArmyLists>>
}
class Player {
  <<Community>>
}
CampaignRun "1" *-- "1..*" Participant
CampaignRun "1" *-- "0..*" NodeResult
CampaignRun "1" *-- "0..*" Territory
CampaignRun --> RunStatus
NodeResult "1" *-- "0..*" ConditionState
Territory "1" *-- "0..*" TileBattle
CampaignRun ..> Campaign : campaignId
Participant ..> Roster : rosterId
Participant ..> Player : playerId
ConditionState ..> EdgeCondition : conditionId
NextNodeResolver ..> CampaignRun
NextNodeResolver ..> Campaign
```

Nullables: `currentNodeId`, `chosenEdgeId`, `notes`, `Territory.ownerId` (casillas neutrales).
