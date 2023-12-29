output "ec2instance1_ip" {
  description = "The public ip for ssh access"
  value       = aws_instance.webserver1.public_ip
}

output "ec2instance2_ip" {
  description = "The public ip for ssh access"
  value       = aws_instance.webserver2.public_ip
}
