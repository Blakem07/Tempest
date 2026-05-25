# Tempest Infrastructure as Code

## Overview

This folder contains the Azure Bicep templates for recreating the core Tempest cloud infrastructure.

The templates are modular, parameterised and suitable for version control. They document the infrastructure required to host the Tempest PHP application on Azure.

## Structure

```text
IaC/
├── main.bicep
├── parameters.dev.example.json
├── parameters.prod.example.json
├── parameters.dev.local.json
├── parameters.prod.local.json
├── README.md
└── modules/
    ├── network.bicep
    ├── security.bicep
    └── vm.bicep
```

## Committed and Local Files

Committed example files:

```text
parameters.dev.example.json
parameters.prod.example.json
```

Local files not committed to Git:

```text
parameters.dev.local.json
parameters.prod.local.json
```

The local files contain machine-specific values such as the trusted SSH source IP and SSH public key.

The example files keep the same structure but use placeholders.

## Resources Created

The Bicep templates create:

- Virtual network
- Subnet
- Network security group
- SSH rule restricted to a trusted IP address
- HTTP rule for public browser access
- Public IP address
- Network interface
- Ubuntu virtual machine
- Optional `CanNotDelete` resource lock

## Module Responsibilities

```text
main.bicep
```

Coordinates the deployment and passes parameters into the modules.

```text
modules/network.bicep
```

Creates the virtual network and subnet.

```text
modules/security.bicep
```

Creates the network security group and inbound rules.

```text
modules/vm.bicep
```

Creates the public IP address, network interface, Ubuntu VM and optional resource lock.

## Parameterisation

Deployment values are controlled through parameter files.

Parameters include:

- Azure region
- Project name
- Admin username
- SSH public key
- Trusted SSH source IP
- VM size
- Resource lock toggle

## Security Controls in IaC

The templates define these security controls:

- SSH is restricted to a trusted `/32` source IP address.
- HTTP is opened for public browser access.
- Password authentication is disabled on the Linux VM.
- SSH key authentication is used.
- A resource lock can be applied to reduce accidental deletion risk.
- Secrets are not stored in Bicep files.

Application secrets such as database passwords and OpenWeather API keys are stored separately in `.env`, which is excluded from Git.

## Development Deployment

Use a local parameter file with real values:

```text
IaC/parameters.dev.local.json
```

Create a resource group:

```bash
az group create \
  --name tempest-iac-validation-rg \
  --location swedencentral
```

Validate the deployment:

```bash
az deployment group validate \
  --resource-group tempest-iac-validation-rg \
  --template-file IaC/main.bicep \
  --parameters @IaC/parameters.dev.local.json
```

Deploy the infrastructure:

```bash
az deployment group create \
  --resource-group tempest-iac-validation-rg \
  --template-file IaC/main.bicep \
  --parameters @IaC/parameters.dev.local.json
```

## Production Deployment

Use a production local parameter file with production values:

```text
IaC/parameters.prod.local.json
```

Create a production resource group:

```bash
az group create \
  --name tempest-iac-prod-rg \
  --location swedencentral
```

Deploy the production infrastructure:

```bash
az deployment group create \
  --resource-group tempest-iac-prod-rg \
  --template-file IaC/main.bicep \
  --parameters @IaC/parameters.prod.local.json
```

## Bicep Build

Install Bicep if required:

```bash
az bicep install
```

Build the templates:

```bash
az bicep build --file IaC/main.bicep
az bicep build --file IaC/modules/network.bicep
az bicep build --file IaC/modules/security.bicep
az bicep build --file IaC/modules/vm.bicep
```

Generated `.json` files are build artefacts. They are not required to be committed.

## Validation

Run validation before deployment:

```bash
az deployment group validate \
  --resource-group tempest-iac-validation-rg \
  --template-file IaC/main.bicep \
  --parameters @IaC/parameters.dev.local.json
```

A successful validation confirms that Azure accepts the template structure and parameter values before resources are created.

Validation does not create the full production application or import the database; it checks that Azure accepts the infrastructure template and parameter values.

## Cleaning Up Validation Resources

Delete the validation resource group after testing if it is no longer needed:

```bash
az group delete \
  --name tempest-iac-validation-rg \
  --yes
```

## Advantages Compared With Manual Configuration

- Repeatable deployment
- Reduced configuration drift
- Faster rebuild after failure
- Better version control
- Easier collaboration
- Infrastructure changes are reviewable in Git
- Security rules are documented in code
- Environment-specific values are parameterised

## Limitations Compared With Manual Configuration

- Initial learning curve
- Deployment errors can be slower to debug
- Existing manually created resources may need careful alignment
- Small changes can take longer than portal edits
- Secrets must be managed separately and must not be hard-coded
- Azure policy or SKU availability can still block deployment

## Notes

The live Tempest environment was initially created manually during the foundation phase. The Bicep templates provide a repeatable infrastructure definition for recreating the core Azure environment.

The Azure database and application deployment steps are documented separately. The IaC in this folder focuses on the VM hosting infrastructure, networking, security rules and resource lock.
