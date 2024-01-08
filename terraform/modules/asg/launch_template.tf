resource "aws_launch_template" "webserverconfig" {
  name_prefix          = "web-"
  image_id             = "ami-0c7217cdde317cfec"
  instance_type        = "t2.micro"
  key_name             = "ccKP"
  security_group_names = [var.elb_security_group_name]
  user_data            = file("${path.module}/ec2-setup.sh")

  iam_instance_profile {
    arn = "arn:aws:iam::262736261154:instance-profile/WebServerRole"
  }

  update_default_version = true

  lifecycle {
    create_before_destroy = true
  }
}
