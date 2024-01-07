# Create public subnet 1
resource "aws_eip" "ccNatGatewayEIP1" {
  tags = {
    Name    = "ccNatGatewayEIP1"
    Project = "CC TF Demo"
  }
}
resource "aws_nat_gateway" "ccNatGateway1" {
  allocation_id = aws_eip.ccNatGatewayEIP1.id
  subnet_id     = aws_subnet.ccPublicSubnet1.id
  tags = {
    Name    = "ccNatGateway1"
    Project = "CC TF Demo"
  }
}
resource "aws_subnet" "ccPublicSubnet1" {
  vpc_id                  = aws_vpc.ccVPC.id
  cidr_block              = var.public_subnet_cidrs[0]
  availability_zone       = var.availability_zones[0]
  map_public_ip_on_launch = true
  tags = {
    Name    = "ccPublicSubnet1"
    Project = "CC TF Demo"
  }
}
# Create public subnet 2
resource "aws_eip" "ccNatGatewayEIP2" {
  tags = {
    Name    = "ccNatGatewayEIP2"
    Project = "CC TF Demo"
  }
}
resource "aws_nat_gateway" "ccNatGateway2" {
  allocation_id = aws_eip.ccNatGatewayEIP2.id
  subnet_id     = aws_subnet.ccPublicSubnet1.id
  tags = {
    Name    = "ccNatGateway2"
    Project = "CC TF Demo"
  }
}
resource "aws_subnet" "ccPublicSubnet2" {
  vpc_id                  = aws_vpc.ccVPC.id
  cidr_block              = var.public_subnet_cidrs[1]
  availability_zone       = var.availability_zones[1]
  map_public_ip_on_launch = true
  tags = {
    Name    = "ccPublicSubnet2"
    Project = "CC TF Demo"
  }
}
# Create private subnet 1
resource "aws_subnet" "ccPrivateSubnet1" {
  vpc_id            = aws_vpc.ccVPC.id
  cidr_block        = var.private_subnet_cidrs[0]
  availability_zone = var.availability_zones[0]
  tags = {
    Name    = "ccPrivateSubnet1"
    Project = "CC TF Demo"
  }
}
# Create private subnet 2
resource "aws_subnet" "ccPrivateSubnet2" {
  vpc_id            = aws_vpc.ccVPC.id
  cidr_block        = var.private_subnet_cidrs[1]
  availability_zone = var.availability_zones[1]
  tags = {
    Name    = "ccPrivateSubnet2"
    Project = "CC TF Demo"
  }
}
