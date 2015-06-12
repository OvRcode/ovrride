# Get S3 Keys from data bag
keys = begin
            data_bag_item("secret_keys", "keys")
          rescue Net::HTTPServerException, Chef::Exceptions::ValidationFailed
            { "S3_ACCESS_KEY" => "FALSE", "S3_SECRET_KEY" => "FALSE" }
          end

node.set[:s3cmd][:users] = ["vagrant", :root]
node.set[:s3cmd][:access_key] = keys['S3_ACCESS_KEY']
node.set[:s3cmd][:secret_key] = keys['S3_SECRET_KEY']

include_recipe("s3cmd")