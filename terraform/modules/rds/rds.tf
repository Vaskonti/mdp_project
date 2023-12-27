resource "aws_db_subnet_group" "ccDBSubnetGroup" {
  name = "cc-db-subnet-group"
  subnet_ids = [
    var.cc_private_subnets[0].id,
    var.cc_private_subnets[1].id
  ]
  tags = {
    Name    = "ccDBSubnetGroup"
    Project = "CC TF Demo"
  }
}

resource "aws_security_group" "ccDBSecurityGroup" {
  name   = "cc-db-security-group"
  vpc_id = var.cc_vpc_id

  ingress {
    from_port = 3306
    to_port   = 3306
    protocol  = "tcp"
    cidr_blocks = [
      var.cc_private_subnet_cidrs[0],
      var.cc_private_subnet_cidrs[1]
    ]
  }
  tags = {
    Name    = "ccDBSecurityGroup"
    Project = "CC TF Demo"
  }
}

resource "aws_db_instance" "ccRDS" {
  availability_zone       = var.rds_az
  db_subnet_group_name    = aws_db_subnet_group.ccDBSubnetGroup.name
  vpc_security_group_ids  = [aws_security_group.ccDBSecurityGroup.id]
  allocated_storage       = 10
  identifier              = var.rds_name
  storage_type            = "standard"
  skip_final_snapshot     = true
  backup_retention_period = 7
  engine                  = "mysql"
  engine_version          = "8.0.35"
  instance_class          = "db.t2.micro"
  username                = var.rds_user_name
  password                = var.rds_user_password
  tags = {
    Name    = "ccRDS"
    Project = "CC TF Demo"
  }
}

output "rds-url" {
  value = aws_db_instance.ccRDS.endpoint
}

resource "aws_db_instance" "ccRDS-replica" {
  identifier              = "${aws_db_instance.ccRDS.identifier}-replica"
  instance_class          = "db.t2.micro"
  skip_final_snapshot     = true
  backup_retention_period = 7
  replicate_source_db     = aws_db_instance.ccRDS.identifier
}

output "replica-url" {
  value = aws_db_instance.ccRDS-replica.endpoint
}
