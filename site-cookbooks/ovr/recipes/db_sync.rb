
keys = data_bag_item("secret_keys", "keys")
default_attributes(
  "s3cmd" => {
    "access_key" => "Mike",
    "secret_key" => "Testing",
    "users" => "vagrant"
  }
)

include_recipe['s3cmd']

# install s3cmd
# use template to put s3cfg into root + vagrant user