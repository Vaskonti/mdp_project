output "load_balancer_dns_name" {
  description = "Load Balancer DNS Name"
  value       = aws_lb.ccLoadBalancer.dns_name
}

output "webserver1_public_ip" {
  value = aws_instance.webserver1.public_ip
}

output "ami" {
  value = aws_instance.webserver1.ami
}

output "webserver2_public_ip" {
  value = aws_instance.webserver2.public_ip
}
