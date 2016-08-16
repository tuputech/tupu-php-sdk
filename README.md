# TUPU PHP SDK

SDK for TUPU visual recognition service
https://www.tuputech.com

## Example

```
require 'tupuclient.php';

//Using remote iamge URLs
//$images = array('http://img.xxx.com/1.jpg', 'http://img.xxx.com/2.jpg');
//Upload files
$images = array('@img/1.jpg', '@img/2.jpg');

//NOTE: Paste the path of your private key pem file here
// generating RSA private key:
// # openssl genrsa -out rsa_private_key.pem 1024
$privateKey = file_get_contents('./your_private_key.pem');

//NOTE: Paste your Screct-ID here
// Apply for account and secret ID: https://www.tuputech.com
$secretId = 'your_secret_id';

$tupu = new TupuClient($privateKey, $secretId);

$result = $tupu->recognition($images);
var_dump($result);
```

## License

[MIT](http://www.opensource.org/licenses/mit-license.php)
