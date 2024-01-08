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


# <-------- CodeDeploy ----------->

resource "aws_codedeploy_application" "mdp_project" {
  name = "mdp_project"
}

resource "aws_codedeploy_deployment_group" "laravel_app_deployment_group" {
  app_name              = aws_codedeploy_application.mdp_project.name
  deployment_group_name = "laravel-app-deployment-group"
  service_role_arn      = "arn:aws:iam::262736261154:role/CodeDeployRole"

  deployment_style {
    deployment_option = "WITH_TRAFFIC_CONTROL"
    deployment_type   = "BLUE_GREEN"
  }

  blue_green_deployment_config {
    terminate_blue_instances_on_deployment_success {
      action                           = "TERMINATE"
      termination_wait_time_in_minutes = 2
    }

    deployment_ready_option {
      action_on_timeout = "CONTINUE_DEPLOYMENT"
    }

    green_fleet_provisioning_option {
      action = "DISCOVER_EXISTING"
    }
  }

  auto_rollback_configuration {
    enabled = true
    events  = ["DEPLOYMENT_FAILURE"]
  }

  load_balancer_info {
    elb_info {
      name = var.elb_name
    }
  }

  deployment_config_name = "CodeDeployDefault.OneAtATime"
}
