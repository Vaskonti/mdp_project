locals {
  ami_id        = "ami-0c7217cdde317cfec"
  instance_type = "t2.micro"
  key_name      = "ccKP"
  setup_script  = file("${path.module}/ec2-setup.sh")
}
