output "docdb-endpoint" {
  value = aws_docdb_cluster.docDB_cluster.endpoint
}

output "docdb-username" {
  value = aws_docdb_cluster.docDB_cluster.master_username
}

output "docdb-password" {
  value = aws_docdb_cluster.docDB_cluster.master_password
}
