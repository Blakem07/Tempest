/*
 * Defines the network layer for the Tempest Azure deployment.
 *
 * Creates a virtual network using the 10.0.0.0/16 address space and adds a
 * single subnet using 10.0.0.0/24. The subnet ID is exported so the virtual
 * machine module can attach the VM network interface to this subnet.
 */
@description('Azure region for network resources.')
param location string

@description('Base prefix for resource names.')
param resourcePrefix string

resource virtualNetwork 'Microsoft.Network/virtualNetworks@2023-11-01' = {
  name: '${resourcePrefix}-vnet'
  location: location
  properties: {
    addressSpace: {
      addressPrefixes: [
        '10.0.0.0/16'
      ]
    }
    subnets: [
      {
        name: '${resourcePrefix}-subnet'
        properties: {
          addressPrefix: '10.0.0.0/24'
        }
      }
    ]
  }
}

output virtualNetworkName string = virtualNetwork.name
output subnetId string = virtualNetwork.properties.subnets[0].id
