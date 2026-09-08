# Grafana Cloud

What to import, and why these panels and not others.

## Importing

**Dashboards** — Grafana → Dashboards → New → Import → paste the JSON from `dashboards/`. It
asks which Loki data source to use; pick the one Grafana Cloud provisioned for you, usually
named `grafanacloud-<stack>-logs`.

**Alerts** — Grafana Cloud does not read provisioning files off disk the way a self-hosted
instance does, so `alerting/rules.yaml` is documentation-with-a-shape rather than something to
drop in a folder. Two ways to use it:

    # Through the API, with a token that has alerting write scope
    curl -X POST "https://<stack>.grafana.net/api/v1/provisioning/alert-rules" \
      -H "Authorization: Bearer <token>" \
      -H "Content-Type: application/json" \
      -d @alerting/<rule>.json

or read the query and threshold out of the YAML and create the rule in the UI, which for four
rules is honestly faster.

## What the logs look like

Two streams reach Loki, both from `LokiHandler`, both pushed synchronously from the request
because the host runs no agent.

| Label | Values |
| --- | --- |
| `app` | `wargames-api` |
| `env` | `prod`, `dev` |
| `level` | `info`, `warning`, `error`, `critical` |
| `channel` | `business` for the things that went right, everything else for the things that did not |
| `error_code` | only on errors: `AUTH-USR-005`, `BILL-SUB-004`, … |
| `event` | only on business logs: `user_signed_up`, `referral_confirmed`, … |

The line itself is JSON: `message`, `context`, `extra`.

`error_code` and `event` are labels rather than fields on purpose — both have a small, fixed set
of values, so they can be grouped by without bloating Loki's index. Anything with a wide range
of values (a user id, an email) stays inside the line, where it costs nothing.
