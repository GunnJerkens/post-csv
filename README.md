# post-csv

Just a quick way to take a csv and post it to an endpoint. Helpful for migrating data sets where the endpoint doesn't offer a built in import function.

## setup

Copy `config.sample.php` to `config.php` and put in the url and the absolute path to the file.

The append array is if you want to attach a $key=>$value of data to each of the post strings that you didn't add a column for previously in Excel.

## run

`php post-csv.php`

## license

MIT
