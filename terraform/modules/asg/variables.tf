variable "cc_vpc_id" {
  description = "VPC ID"
  type        = string
  validation {
    condition     = length(var.cc_vpc_id) > 4 && substr(var.cc_vpc_id, 0, 4) == "vpc-"
    error_message = "VPC ID must not be empty."
  }
}

variable "elb_security_group_id" {
  description = "elb security group id"
  type        = string
}

variable "elb_id" {
  description = "ELB id"
}

variable "elb_name" {
  description = "ELB id"
}

variable "subnet1_id" {
  description = "public subnet 1 id"
  type        = string
}

variable "subnet2_id" {
  description = "public subnet 2 id"
  type        = string
}
