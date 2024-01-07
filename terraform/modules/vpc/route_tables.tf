resource "aws_route_table" "ccPublicRT" {
  vpc_id = aws_vpc.ccVPC.id
  route {
    cidr_block = "0.0.0.0/0"
    gateway_id = aws_internet_gateway.ccIGW.id
  }
  tags = {
    Name    = "ccPublicRT"
    Project = "CC TF Demo"
  }
}
resource "aws_route_table" "ccPrivateRT1" {
  vpc_id = aws_vpc.ccVPC.id
  route {
    cidr_block     = "0.0.0.0/0"
    nat_gateway_id = aws_nat_gateway.ccNatGateway1.id
  }
  tags = {
    Name    = "ccPrivateRT1"
    Project = "CC TF Demo"
  }
}

resource "aws_route_table_association" "ccPublicRTassociation1" {
  subnet_id      = aws_subnet.ccPublicSubnet1.id
  route_table_id = aws_route_table.ccPublicRT.id
}
resource "aws_route_table_association" "ccPublicRTassociation2" {
  subnet_id      = aws_subnet.ccPublicSubnet2.id
  route_table_id = aws_route_table.ccPublicRT.id
}
