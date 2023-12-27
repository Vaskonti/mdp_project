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

variable "docdb_az" {
  description = "DB Availability Zone"
  type        = string
  validation {
    condition     = can(regex("^[a-zA-Z0-9\\-]+$", var.docdb_az))
    error_message = "DB Availability Zone must not be empty."
  }
}

variable "docdb_name" {
  description = "Name of DB"
  type        = string
  sensitive   = true
  validation {
    condition     = can(regex("^[a-zA-Z0-9\\-\\_]+$", var.docdb_name))
    error_message = "DB Name must not be empty and can contain letters, numbers, underscores, and dashes."
  }
}

variable "docdb_user_name" {
  description = "docdb User"
  type        = string
  sensitive   = true
  validation {
    condition     = length(var.docdb_user_name) > 5
    error_message = "DB UserName must not be empty."
  }
}

variable "docdb_user_password" {
  description = "docdb User Password"
  type        = string
  sensitive   = true
  validation {
    condition     = length(var.docdb_user_password) > 8
    error_message = "DB User Password must not be empty."
  }
}
