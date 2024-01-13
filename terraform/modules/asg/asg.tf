resource "aws_autoscaling_group" "webserver" {
  name             = "${aws_launch_template.webserverconfig.name}-asg"
  min_size         = 2
  desired_capacity = 2
  max_size         = 3

  vpc_zone_identifier = [
    var.subnet1_id,
    var.subnet2_id
  ]

  health_check_type = "ELB"
  load_balancers = [
    "${var.elb_id}"
  ]

  termination_policies = ["OldestInstance"]

  launch_template {
    id      = aws_launch_template.webserverconfig.id
    version = "$Latest"
  }

  enabled_metrics = [
    "GroupMinSize",
    "GroupMaxSize",
    "GroupDesiredCapacity",
    "GroupInServiceInstances",
    "GroupTotalInstances"
  ]
  metrics_granularity = "1Minute"

  lifecycle {
    create_before_destroy = true
    ignore_changes        = [desired_capacity]
  }
  tag {
    key                 = "Name"
    value               = "webserver"
    propagate_at_launch = true
  }
}
