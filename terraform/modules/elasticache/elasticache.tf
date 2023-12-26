resource "aws_elasticache_subnet_group" "elasticache_subnet_group" {
  name = "elasticache_subnet_group"
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
    cidr_blocks = [
      var.cc_private_subnet_cidrs[0],
      var.cc_private_subnet_cidrs[1]
    ]
  }
}

resource "aws_elasticache_cluster" "elasticache_cluster" {
  cluster_id           = var.elasticache_name
  engine               = "redis"
  node_type            = "cache.t2.micro"
  num_cache_nodes      = 1
  subnet_group_name    = aws_elasticache_subnet_group.elasticache_subnet_group.name
  security_group_ids   = [aws_security_group.elasticache_sg.id]
  parameter_group_name = "default.redis5.0"

  tags = {
    Name    = "ElastiCache"
    Project = "MDP_Project"
  }
}
