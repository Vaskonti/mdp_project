ui = true

storage "file" {
  path = "./vault-volume"
}

listener "tcp" {
  address = "127.0.0.1:8201"
  tls_disable = "true"
}

api_addr = "http://127.0.0.1:8200"
