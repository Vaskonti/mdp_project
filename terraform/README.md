# Terraform for AWS MDP PROJECT

## Description
This terraform code will create:
- VPC
- 2 EC2 instances
- 2 Security groups
- 2 Subnets
- 1 Load balancer
- 1 Target group
- 1 RDS instance
- 2 DocumentDB instances
- 1 OpenSearch domain
- 1 ElastiCache cluster

[//]: # (@Kaisiq opravi gore kakvoto trqbva)

## Requirements
1. **Terraform** installed on your machine
2. **AWS CLI** installed on your machine
## How to run
1. ``` aws configure```

2. ```terraform init``` 

3. ```terraform fmt -recursive```
4. ```terraform validate```(to validate the code)
5. ```terraform apply```
   1. enter all needed information
   2. enter "yes"
