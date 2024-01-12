resource "aws_elb" "web_elb" {
  name                      = "web-elb"
  security_groups           = ["${aws_security_group.ElbSG.id}"]
  subnets                   = ["${var.subnet1_id}", "${var.subnet2_id}"]
  cross_zone_load_balancing = true
  health_check {
    healthy_threshold   = 2
    unhealthy_threshold = 4
    timeout             = 10
    interval            = 30
    target              = "HTTP:80/"
  }
  listener {
    lb_port           = 80
    lb_protocol       = "http"
    instance_port     = "80"
    instance_protocol = "http"
  }
}
