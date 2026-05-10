# Tempest Infrastructure as Code

This folder will contain the Azure Infrastructure as Code files for recreating the Tempest cloud infrastructure.

## Purpose

The IaC implementation will support the KV6012 requirement to provide a cloud infrastructure template that demonstrates:

- Modularity
- Reusability
- Parameterisation
- Repeatable deployment
- Version-control suitability
- Maintainability
- Collaboration

## Planned Structure

```text
IaC/
├── main.bicep
├── parameters.dev.json
├── parameters.prod.json
└── modules/
    ├── network.bicep
    ├── vm.bicep
    ├── database.bicep
    └── security.bicep
```

## Phase 1 Status

Phase 1 uses manual Azure CLI deployment to create the initial VM foundation.

The final project will include modular Bicep files in this folder.

## Planned Deployment Commands

```bash
az group create \
  --name tempest-rg \
  --location uksouth

az deployment group create \
  --resource-group tempest-rg \
  --template-file IaC/main.bicep \
  --parameters @IaC/parameters.dev.json
```

## IaC Evaluation Points

The final video explanation will compare IaC with manual configuration.

Advantages:

- Repeatable deployment
- Lower configuration drift
- Easier collaboration
- Easier version control
- Faster rebuild after failure

Limitations:

- Initial learning curve
- Secrets management must be handled carefully
- Debugging deployment errors can take longer
- Small changes can feel heavier than portal edits
