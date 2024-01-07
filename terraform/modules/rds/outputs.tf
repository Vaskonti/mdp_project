output "rds-endpoint" {
  value = aws_db_instance.ccRDS.endpoint
}

output "rds-username" {
  value = aws_db_instance.ccRDS.username
}

output "rds-password" {
  value = aws_db_instance.ccRDS.password
}
