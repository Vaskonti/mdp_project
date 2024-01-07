variable "cc_vpc_id" {
  description = "VPC ID"
  type        = string
  validation {
    condition     = length(var.cc_vpc_id) > 4 && substr(var.cc_vpc_id, 0, 4) == "vpc-"
    error_message = "VPC ID must not be empty."
  }
}

variable "subnet1_id" {
  description = "public subnet 1 id"
  type        = string
}

variable "subnet2_id" {
  description = "public subnet 2 id"
  type        = string
}
