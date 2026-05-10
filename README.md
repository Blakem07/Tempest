# Tempest - Construction Project Cloud Dashboard

## Overview

Tempest is a PHP, CSS and JavaScript web application for construction project monitoring.

The project is being developed in phases. The final system will display project data, equipment resources, project locations, weather conditions, air-quality readings and operational risk recommendations.

At this stage, the project contains the local application foundation and a basic landing page.

## Current Phase

Phase 1 foundation.

Current implementation:

- Project folder structure
- README skeleton
- `.gitignore`
- `.env.example`
- Basic PHP layout files
- Basic public landing page
- Lightweight CSS
- Basic JavaScript file
- Evidence folder structure

Not yet implemented in the application:

- Database connection
- Project selector
- Equipment resource display
- Map marker
- Weather API integration
- Air-quality API integration
- Forecast page
- Historical lookup
- Infrastructure as Code deployment files

## Live Solution

Live URL: To be added after Azure deployment  
Repository: To be added  
Demo video: To be added  

## Technology Stack

Current stack:

- PHP
- HTML
- CSS
- JavaScript

Planned cloud stack:

- Azure Virtual Machine
- Ubuntu
- Apache
- PHP
- Azure-hosted database
- Leaflet
- OpenStreetMap
- OpenWeather API
- OpenWeather Air Pollution API
- Azure CLI
- Bicep for Infrastructure as Code

## Planned Features

- Azure VM hosted website
- Project dashboard
- Cloud database integration
- Project selector
- Project title, description and location display
- Equipment resource display
- Leaflet and OpenStreetMap project marker
- Current weather display
- Weather risk recommendations
- Current air-quality display
- AQI risk recommendations
- Forecast page
- Historical lookup
- API limitation handling
- Infrastructure as Code
- Security controls
- Sustainability notes

## Architecture

Architecture diagram to be added in:

`/docs/architecture.png`

Planned architecture:

User browser  
    |  
    v  
Azure Virtual Machine  
    |  
    v  
Apache and PHP  
    |  
    | Server-side API requests  
    v  
OpenWeather API  

PHP application  
    |  
    | Database connection  
    v  
Azure-hosted cloud database  

Browser  
    |  
    | Leaflet and OpenStreetMap tiles  
    v  
Project location map  

## Cloud Resources

Planned Azure resources:

- Resource group
- Ubuntu virtual machine
- Network security group
- Public IP address
- Virtual network
- Subnet
- Network interface
- Managed disk
- Azure-hosted database
- Resource lock
- Role assignments

## Database Design

To be implemented in Phase 2.

Planned tables:

- `projects`
- `resources`
- `project_resources`

Planned project fields:

- Project title
- Project description
- Location name
- Latitude
- Longitude
- Start date
- End date
- Allocated equipment

Planned equipment examples:

- Crane
- Digger
- Dumper truck
- Concrete mixer
- Scaffolding
- Generator

## Security Controls

Planned controls:

- SSH restricted to a trusted IP address
- HTTP open for public web access
- HTTPS to be considered if time permits
- API keys stored server-side only
- No OpenWeather API key in frontend JavaScript
- Database credentials stored outside the public directory
- `.env` excluded from Git
- Prepared SQL statements
- Input validation
- Output escaping with `htmlspecialchars`
- Generic user-facing error messages
- Azure resource lock
- Read-only reviewer access where required

Current repository controls:

- `.env` is excluded from Git
- `.env.example` is provided as a safe template
- Basic output escaping is used in the PHP layout

## Sustainability Considerations

Tempest is planned to reduce avoidable compute and transfer overhead by:

- Avoiding heavy frontend frameworks
- Keeping CSS small
- Keeping JavaScript small
- Using server-side API requests
- Avoiding unnecessary client-side dependencies
- Using a small Azure VM suitable for the project workload
- Using efficient database queries in later phases
- Adding short API caching where suitable in later phases
- Keeping image and asset use minimal

## API Integrations

To be implemented in later phases.

Planned APIs and services:

- OpenWeather Current Weather API
- OpenWeather Air Pollution API
- OpenWeather forecast and historical endpoints where account limits permit
- Leaflet with OpenStreetMap tiles

## Risk Recommendation Logic

To be implemented in later phases.

Planned weather logic:

- If wind speed is greater than 20mph and the project includes a crane, recommend that crane work should not be carried out.
- If rain is heavy intensity, very heavy or extreme and the project includes diggers and dumper trucks, recommend that works may be delayed due to rainfall.

Planned air-quality logic:

- If AQI is good or fair, earth-moving work can continue.
- If AQI is moderate, poor or very poor, recommend that digger or dumper truck work should not be carried out.

## Infrastructure as Code

IaC files will be stored in:

`/IaC`

Planned structure:

IaC/  
├── main.bicep  
├── parameters.dev.json  
├── parameters.prod.json  
└── modules/  
    ├── network.bicep  
    ├── vm.bicep  
    ├── database.bicep  
    └── security.bicep  

IaC goals:

- Repeatable deployment
- Modular structure
- Parameterised environments
- Easier rebuild after failure
- Reduced configuration drift
- Clear infrastructure evidence

## Local Setup

Run from the project root:

`php -S localhost:8080 -t public`

Visit:

`http://localhost:8080`

## Azure Deployment

Azure deployment evidence will be added after the VM deployment is completed and tested.

Planned manual deployment steps for Phase 1:

- Create Azure resource group
- Create Ubuntu VM
- Restrict SSH in the network security group
- Open HTTP for public access
- Install Apache
- Install PHP
- Configure Apache virtual host
- Deploy source files to `/var/www/tempest`
- Verify public homepage

Later phases will add Bicep files under:

`/IaC`

## Evidence

Evidence will be stored under:

`/docs`

Phase evidence folders:

- `/docs/phase-1`
- `/docs/phase-2`
- `/docs/phase-3`
- `/docs/phase-4`
- `/docs/phase-5`
- `/docs/phase-6`
- `/docs/phase-7`
- `/docs/phase-8`
- `/docs/phase-9`

Planned Phase 1 evidence:

- Azure resource group
- Azure VM overview
- Public IP address
- Network security group rules
- SSH restricted to trusted IP
- HTTP open for public access
- Apache running on VM
- PHP installed on VM
- Public homepage loaded in browser
- curl HTTP 200 response
- Git commit history

## Testing

Local PHP syntax check:

`find . -name "*.php" -print0 | xargs -0 -n1 php -l`

Local web server test:

`php -S localhost:8080 -t public`

Visit:

`http://localhost:8080`

Planned remote Azure test:

`curl -I http://<public-ip>`

Expected result after deployment:

`HTTP/1.1 200 OK`

## Known Limitations

Current Phase 1 foundation does not yet include:

- Database connection
- Project selector
- Project resources
- Map marker
- Weather API
- Air-quality API
- Forecast
- Historical lookup
- IaC deployment files
- Full security hardening
- Final Azure evidence screenshots

## Roadmap

Planned phases:

- Phase 1: Foundation and Azure VM
- Phase 2: Cloud database and project dashboard
- Phase 3: Leaflet and OpenStreetMap integration
- Phase 4: Current weather and weather risk engine
- Phase 5: Air-quality data and AQI risk engine
- Phase 6: Forecast and historical lookup
- Phase 7: Infrastructure as Code
- Phase 8: Security hardening
- Phase 9: Final packaging and evidence

## References

References will be added as external services and sources are implemented.

Planned references:

- Azure documentation
- PHP documentation
- Apache documentation
- OpenWeather documentation
- OpenWeather Air Pollution API documentation
- Leaflet documentation
- OpenStreetMap usage information
- Website Carbon information`