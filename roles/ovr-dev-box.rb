name "ovr-dev-box"
run_list(
  "recipe[apt]",
  "recipe[apache2]",
  "recipe[mysql]",
  "recipe[php]
)