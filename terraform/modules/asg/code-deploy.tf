# <-------- CodeDeploy ----------->

resource "aws_iam_role" "mdp_project" {
  name = "hello-world-role"

  assume_role_policy = <<EOF
{
  "Version": "2012-10-17",
  "Statement": [
    {
      "Sid": "",
      "Effect": "Allow",
      "Principal": {
        "Service": "codedeploy.amazonaws.com"
      },
      "Action": "sts:AssumeRole"
    }
  ]
}
EOF
}

resource "aws_iam_role_policy_attachment" "AWSCodeDeployRole" {
  policy_arn = "arn:aws:iam::aws:policy/service-role/AWSCodeDeployRole"
  role       = aws_iam_role.mdp_project.name
}

resource "aws_codedeploy_app" "mdp_project" {
  name = "mdp_project"
}

resource "aws_codedeploy_deployment_group" "mdp_project" {
  depends_on = [aws_codedeploy_app.mdp_project]
  app_name   = aws_codedeploy_app.mdp_project.name
  # deployment_group_name = "laravel-app-deployment-group"
  deployment_group_name  = "mdp_project-deployment-group"
  deployment_config_name = "CodeDeployDefault.OneAtATime"
  # service_role_arn      = "arn:aws:iam::262736261154:role/CodeDeployRole"
  service_role_arn = aws_iam_role.mdp_project.arn

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
      name = var.elb_id
    }
  }
}
