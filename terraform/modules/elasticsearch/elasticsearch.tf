resource "aws_elasticsearch_domain" "elasticache_domain" {
  domain_name           = "elasticache-domain"
  elasticsearch_version = "7.10" # Adjust based on your desired version
  cluster_config {
    instance_type = "t2.small.elasticsearch" # Adjust based on your desired instance type
  }
  ebs_options {
    ebs_enabled = true
    volume_size = 10
  }
  vpc_options {
    subnet_ids = [var.cc_private_subnets[0].id]
    # var.cc_private_subnets[1].id]
  }
  access_policies = <<POLICY
{
  "Version": "2012-10-17",
  "Statement": [
    {
      "Effect": "Allow",
      "Principal": {
        "Service": "es.amazonaws.com"
      },
      "Action": "es:ESHttp*",
      "Resource": "arn:aws:es:us-east-1:123456789012:domain/my-elasticsearch-domain/*"
    }
  ]
}
POLICY
}

output "elasticsearch_endpoint" {
  value = aws_elasticsearch_domain.elasticache_domain.endpoint
}
