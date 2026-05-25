/*
 * Defines the virtual-machine layer for the Tempest Azure deployment.
 *
 * Creates a static public IP address, a network interface connected to the
 * supplied subnet and network security group, and an Ubuntu Linux virtual
 * machine configured for SSH key authentication. Password authentication is
 * disabled so access depends on the supplied SSH public key.
 *
 * A CanNotDelete lock can optionally be applied to the VM to reduce the risk of
 * accidental deletion after deployment. The VM name and public IP address are
 * exported for use after the deployment completes.
 */

@description('Azure region for VM resources.')
param location string

@description('Base prefix for resource names.')
param resourcePrefix string

@description('Admin username for the Linux VM.')
param adminUsername string

@description('SSH public key used for VM authentication.')
param sshPublicKey string

@description('VM size.')
param vmSize string

@description('Subnet ID for the network interface.')
param subnetId string

@description('Network security group ID for the network interface.')
param networkSecurityGroupId string

@description('Whether to apply a CanNotDelete lock to the VM.')
param applyResourceLock bool

resource publicIpAddress 'Microsoft.Network/publicIPAddresses@2023-11-01' = {
  name: '${resourcePrefix}-public-ip'
  location: location
  sku: {
    name: 'Standard'
  }
  properties: {
    publicIPAllocationMethod: 'Static'
  }
}

resource networkInterface 'Microsoft.Network/networkInterfaces@2023-11-01' = {
  name: '${resourcePrefix}-nic'
  location: location
  properties: {
    networkSecurityGroup: {
      id: networkSecurityGroupId
    }
    ipConfigurations: [
      {
        name: 'ipconfig1'
        properties: {
          privateIPAllocationMethod: 'Dynamic'
          subnet: {
            id: subnetId
          }
          publicIPAddress: {
            id: publicIpAddress.id
          }
        }
      }
    ]
  }
}

resource virtualMachine 'Microsoft.Compute/virtualMachines@2023-09-01' = {
  name: '${resourcePrefix}-vm'
  location: location
  properties: {
    hardwareProfile: {
      vmSize: vmSize
    }
    osProfile: {
      computerName: '${resourcePrefix}-vm'
      adminUsername: adminUsername
      linuxConfiguration: {
        disablePasswordAuthentication: true
        ssh: {
          publicKeys: [
            {
              path: '/home/${adminUsername}/.ssh/authorized_keys'
              keyData: sshPublicKey
            }
          ]
        }
      }
    }
    storageProfile: {
      imageReference: {
        publisher: 'Canonical'
        offer: '0001-com-ubuntu-server-jammy'
        sku: '22_04-lts-gen2'
        version: 'latest'
      }
      osDisk: {
        createOption: 'FromImage'
        managedDisk: {
          storageAccountType: 'Standard_LRS'
        }
      }
    }
    networkProfile: {
      networkInterfaces: [
        {
          id: networkInterface.id
        }
      ]
    }
  }
}

resource vmLock 'Microsoft.Authorization/locks@2020-05-01' = if (applyResourceLock) {
  name: '${resourcePrefix}-vm-delete-lock'
  scope: virtualMachine
  properties: {
    level: 'CanNotDelete'
    notes: 'Prevents accidental deletion of the Tempest VM.'
  }
}

output vmName string = virtualMachine.name
output publicIpAddress string = publicIpAddress.properties.ipAddress
