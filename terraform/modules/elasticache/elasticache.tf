resource "aws_elasticache_subnet_group" "elasticache_subnet_group" {
  name = "elasticache-subnet-group"
  subnet_ids = [
    var.cc_private_subnets[0].id,
    var.cc_private_subnets[1].id
  ]
}

resource "aws_security_group" "elasticache_sg" {
  name        = "elasticache-sg"
  description = "Security group for ElastiCache"
  vpc_id      = var.cc_vpc_id

  ingress {
    from_port = 6379
    to_port   = 6379
    protocol  = "tcp"
    cidr_blocks = ["0.0.0.0/0"]
  }
}

resource "aws_elasticache_replication_group" "elasticache_cluster" {
  replication_group_id = var.elasticache_name
  description          = "Redis cluster for Hashicorp ElastiCache"
  engine               = "redis"

  node_type          = "cache.t2.micro"
  subnet_group_name  = aws_elasticache_subnet_group.elasticache_subnet_group.name
  security_group_ids = [aws_security_group.elasticache_sg.id]
  #   parameter_group_name = "default.redis5.0"

  snapshot_retention_limit = 5
  snapshot_window          = "00:00-05:00"

  tags = {
    Name    = "ElastiCache"
    Project = "MDP_Project"
  }
}
