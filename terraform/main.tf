terraform {
  required_version = "~> 1.3"
  required_providers {
    aws = {
      source  = "hashicorp/aws"
      version = "~> 4.0"
    }
  }
}

module "ccVPC" {
  source = "./modules/vpc"

  vpc_cidr             = local.vpc_cidr
  vpc_tags             = var.vpc_tags
  availability_zones   = local.availability_zones
  public_subnet_cidrs  = local.public_subnet_cidrs
  private_subnet_cidrs = local.private_subnet_cidrs
}

module "elasticsearch" {
  source = "./modules/elasticsearch"

  cc_private_subnets = module.ccVPC.private_subnets
}

module "rds" {
  source = "./modules/rds"

  cc_vpc_id               = module.ccVPC.vpc_id
  cc_private_subnets      = module.ccVPC.private_subnets
  cc_private_subnet_cidrs = local.private_subnet_cidrs

  rds_az            = local.availability_zones[0]
  rds_name          = "parking"
  rds_user_name     = "root"
  rds_user_password = "password"
}

module "docdb" {
  source = "./modules/docdb"

  cc_vpc_id               = module.ccVPC.vpc_id
  cc_private_subnets      = module.ccVPC.private_subnets
  cc_private_subnet_cidrs = local.private_subnet_cidrs

  docdb_az            = local.availability_zones[0]
  docdb_name          = "parking"
  docdb_user_name     = "root"
  docdb_user_password = "password"
}

module "elasticache" {
  source = "./modules/elasticache"

  cc_vpc_id               = module.ccVPC.vpc_id
  cc_private_subnets      = module.ccVPC.private_subnets
  cc_private_subnet_cidrs = local.private_subnet_cidrs

  elasticache_name = "elasticache-instance"
}

module "elb" {
  source     = "./modules/elb"
  cc_vpc_id  = module.ccVPC.vpc_id
  subnet1_id = module.ccVPC.public_subnets[0].id
  subnet2_id = module.ccVPC.public_subnets[1].id
}

module "asg" {
  source                = "./modules/asg"
  cc_vpc_id             = module.ccVPC.vpc_id
  elb_security_group_id = module.elb.elb_security_group_id
  elb_id                = module.elb.elb_id
  subnet1_id            = module.ccVPC.public_subnets[0].id
  subnet2_id            = module.ccVPC.public_subnets[1].id
}

resource "aws_key_pair" "ccKP" {
  key_name   = "ccKP"
  public_key = file("${path.module}/keypair/public-key.pub")
}

output "rds-endpoint" {
  value = module.rds.rds-endpoint
}

output "rds-url" {
  value = module.rds.rds-url
}

output "rds-replica-url" {
  value = module.rds.replica-url
}

output "docdb-endpoint" {
  value = module.docdb.docdb-endpoint
}

output "load_balancer_dns_name" {
  value = module.elb.elb_endpoint
}
