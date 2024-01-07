resource "aws_autoscaling_group" "webserver" {
  name             = "${aws_launch_configuration.webserverconfig.name}-asg"
  min_size         = 2
  desired_capacity = 2
  max_size         = 3

  health_check_type = "ELB"
  load_balancers = [
    var.elb_id
  ]
  launch_configuration = aws_launch_configuration.webserverconfig.name
  enabled_metrics = [
    "GroupMinSize",
    "GroupMaxSize",
    "GroupDesiredCapacity",
    "GroupInServiceInstances",
    "GroupTotalInstances"
  ]
  metrics_granularity = "1Minute"
  vpc_zone_identifier = [
    var.subnet1_id,
    var.subnet2_id
  ]
  lifecycle {
    create_before_destroy = true
  }
  tag {
    key                 = "Name"
    value               = "webserver"
    propagate_at_launch = true
  }
}
