/*
 * Defines the network security layer for the Tempest Azure deployment.
 *
 * Creates a network security group with inbound rules for SSH and HTTP traffic.
 * SSH access is restricted to the trusted public IP address supplied during
 * deployment, while HTTP access is open to the internet so the hosted web
 * application can be reached publicly.
 */
@description('Azure region for security resources.')
param location string

@description('Base prefix for resource names.')
param resourcePrefix string

@description('Trusted public IP address allowed to connect over SSH. Use CIDR format.')
param trustedSshSourceIp string

resource networkSecurityGroup 'Microsoft.Network/networkSecurityGroups@2023-11-01' = {
  name: '${resourcePrefix}-nsg'
  location: location
  properties: {
    securityRules: [
      {
        name: 'Allow-SSH-From-My-IP'
        properties: {
          priority: 1000
          direction: 'Inbound'
          access: 'Allow'
          protocol: 'Tcp'
          sourcePortRange: '*'
          destinationPortRange: '22'
          sourceAddressPrefix: trustedSshSourceIp
          destinationAddressPrefix: '*'
        }
      }
      {
        name: 'Allow-HTTP'
        properties: {
          priority: 1010
          direction: 'Inbound'
          access: 'Allow'
          protocol: 'Tcp'
          sourcePortRange: '*'
          destinationPortRange: '80'
          sourceAddressPrefix: 'Internet'
          destinationAddressPrefix: '*'
        }
      }
    ]
  }
}

output networkSecurityGroupId string = networkSecurityGroup.id
output networkSecurityGroupName string = networkSecurityGroup.name
