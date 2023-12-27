resource "aws_docdb_subnet_group" "docDB_subnet_group" {
  name = "docdb-subnet-group"
  subnet_ids = [var.cc_private_subnets[0].id,
  var.cc_private_subnets[1].id]
}

resource "aws_security_group" "docdb_sg" {
  name        = "docdb-sg"
  description = "Security group for DocumentDB"
  vpc_id      = var.cc_vpc_id

  ingress {
    from_port = 27017
    to_port   = 27017
    protocol  = "tcp"
    cidr_blocks = [
      var.cc_private_subnet_cidrs[0],
      var.cc_private_subnet_cidrs[1]
    ]
  }
}

resource "aws_docdb_cluster" "docDB_cluster" {
  cluster_identifier     = var.docdb_name
  availability_zones     = [var.docdb_az]
  engine                 = "docdb"
  engine_version         = "5.0.0"
  apply_immediately      = true
  db_subnet_group_name   = aws_docdb_subnet_group.docDB_subnet_group.name
  vpc_security_group_ids = [aws_security_group.docdb_sg.id]
  master_username        = var.docdb_user_name
  master_password        = var.docdb_user_password
  skip_final_snapshot    = true
  #   snapshot_identifier       = "snapshot-identifier"
  #   final_snapshot_identifier = "final-snapshot-identifier"
  backup_retention_period = 5
  #   instance_class          = "db.t3.medium"
}

resource "aws_docdb_cluster_instance" "read_replica" {
  count              = 1
  engine             = "docdb"
  identifier         = "${aws_docdb_cluster.docDB_cluster.cluster_identifier}-replica"
  cluster_identifier = aws_docdb_cluster.docDB_cluster.id
  instance_class     = "db.t3.medium"
  availability_zone  = var.docdb_az
}

