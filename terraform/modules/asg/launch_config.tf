resource "aws_launch_configuration" "webserverconfig" {
  name_prefix                 = "web-"
  image_id                    = "ami-0c7217cdde317cfec"
  instance_type               = "t2.micro"
  key_name                    = "ccKP"
  security_groups             = [var.elb_security_group_id]
  associate_public_ip_address = true
  user_data                   = file("${path.module}/ec2-setup.sh")
  lifecycle {
    create_before_destroy = true
  }
}
