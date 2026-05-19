/*
 * Deploys the core Tempest Azure infrastructure into the current resource group.
 *
 * This template coordinates the network, security, and virtual-machine modules.
 * It creates the virtual network and subnet, applies an SSH-restricted network
 * security group, provisions the Linux web-server VM, and exposes the key values
 * needed after deployment.
 *
 * SSH access is restricted to the supplied trusted public IP address. The VM can
 * optionally receive a CanNotDelete resource lock to reduce the risk of accidental
 * deletion.
 */
targetScope = 'resourceGroup'

@description('Azure region for the deployment.')
param location string = resourceGroup().location

@description('Base project name used for Azure resource names.')
param projectName string

@description('Admin username for the Linux VM.')
param adminUsername string

@description('SSH public key used for VM authentication.')
param sshPublicKey string

@description('Trusted public IP address allowed to connect over SSH. Use CIDR format such as 86.180.210.96/32.')
param trustedSshSourceIp string

@description('VM size for the web server.')
param vmSize string = 'Standard_B2s_v2'

@description('Whether to apply a CanNotDelete lock to the VM.')
param applyResourceLock bool = true

var resourcePrefix = projectName

module network 'modules/network.bicep' = {
  name: 'networkDeployment'
  params: {
    location: location
    resourcePrefix: resourcePrefix
  }
}

module security 'modules/security.bicep' = {
  name: 'securityDeployment'
  params: {
    location: location
    resourcePrefix: resourcePrefix
    trustedSshSourceIp: trustedSshSourceIp
  }
}

module vm 'modules/vm.bicep' = {
  name: 'vmDeployment'
  params: {
    location: location
    resourcePrefix: resourcePrefix
    adminUsername: adminUsername
    sshPublicKey: sshPublicKey
    vmSize: vmSize
    subnetId: network.outputs.subnetId
    networkSecurityGroupId: security.outputs.networkSecurityGroupId
    applyResourceLock: applyResourceLock
  }
}

output publicIpAddress string = vm.outputs.publicIpAddress
output vmName string = vm.outputs.vmName
output networkSecurityGroupName string = security.outputs.networkSecurityGroupName
