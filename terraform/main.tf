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
  rds_name          = "cc-rds-database-instance"
  rds_user_name     = var.rds_user_name
  rds_user_password = var.rds_user_password
}

module "docdb" {
  source = "./modules/docdb"

  cc_vpc_id               = module.ccVPC.vpc_id
  cc_private_subnets      = module.ccVPC.private_subnets
  cc_private_subnet_cidrs = local.private_subnet_cidrs

  docdb_az            = local.availability_zones[0]
  docdb_name          = "doc-db-instance"
  docdb_user_name     = var.docdb_user_name
  docdb_user_password = var.docdb_user_password
}

module "elasticache" {
  source = "./modules/elasticache"

  cc_vpc_id               = module.ccVPC.vpc_id
  cc_private_subnets      = module.ccVPC.private_subnets
  cc_private_subnet_cidrs = local.private_subnet_cidrs

  elasticache_name = "elasticache-instance"
}

module "webserver" {
  source = "./modules/webserver"

  cc_vpc_id         = module.ccVPC.vpc_id
  cc_public_subnets = module.ccVPC.public_subnets
}
