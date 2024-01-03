# Create a VPC
resource "aws_vpc" "ccVPC" {
  instance_tenancy = "default"
  cidr_block       = var.vpc_cidr
  tags             = var.vpc_tags
}

# Create gateway connected to vpc
resource "aws_internet_gateway" "ccIGW" {
  vpc_id = aws_vpc.ccVPC.id
  tags = {
    Name    = "ccIGW"
    Project = "CC TF Demo"
  }
}
