variable "cc_private_subnets" {
  description = "Private Subnets ID for docdb"
  type        = list(any)
}

variable "cc_private_subnet_cidrs" {
  description = "Private Subnet CIDRs for docdb"
  type        = list(any)
}

variable "cc_vpc_id" {
  description = "VPC Id"
  type        = string
  validation {
    condition     = length(var.cc_vpc_id) > 4 && substr(var.cc_vpc_id, 0, 4) == "vpc-"
    error_message = "VPC ID must not be empty."
  }
}

variable "elasticache_name" {
  description = "Name of DB"
  type        = string
  sensitive   = true
  validation {
    condition     = can(regex("^[a-zA-Z0-9\\-\\_]+$", var.elasticache_name))
    error_message = "DB Name must not be empty and can contain letters, numbers, underscores, and dashes."
  }
}
