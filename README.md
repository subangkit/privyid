# Laravel Privy ID Integration

Package to integrate PrivyID with Laravel Application

## Installation

To install you using composer

```bash
composer require blackit/privyid
```

## Setup
### Publish migration and config

publish the application using 

```bash
php artisan vendor:publish --provider="BlackIT\PrivyID\PrivyIDServiceProvider"
php artisan migrate
```

### Set Enviroment Variable
setting your enviroment variable
```.env
PRIVYID_PRODUCTION=false
PRIVYID_USER=username
PRIVYID_PASSWORD=password
PRIVYID_MERCHANT_KEY=merchankey
PRIVYID_SANDBOX_USER=usernamesandbox
PRIVYID_SANDBOX_PASSWORD=passwordsandbox
PRIVYID_SANDBOX_MERCHANT_KEY=merchantsandbox
PRIVYID_SANDBOX_OWNER=ownercode
PRIVYID_SANDBOX_OWNER_ENTERPRISE_TOKEN=enterprisetoken
PRIVYID_CLIENT_ID=clientid
PRIVYID_SECRET_KEY=secretkey
PRIVYID_OWNER=ownercode
PRIVYID_OWNER_ENTERPRISE_TOKEN=enterprisetoken
```
Clear config cachee
```bash
php artisan config:clear
```

### Set config/app.php
```php
return [

    ...

    'providers' => [

        ...
        \BlackIT\PrivyID\PrivyIDServiceProvider::class

    ],

    ...

    'aliases' => [

        ...
        'File' => Illuminate\Support\Facades\File::class,
        'PrivyID' => \BlackIT\PrivyID\PrivyIDFacade::class,

    ],

];

```

## Usage
### Using PrivyIDAble in your Model for example User

```php
namespace App;

use BlackIT\PrivyID\PrivyIDAble;

class User 
{
    use PrivyIDAble;
    
    ...
}
```

### Add Button to Bind

```html
<a href="{{ PrivyID::getOAuthLink() }}">Bind Digital Signature</a>
```

### Set you callback

```php
namespace App\Http\Controllers;

use App\User;
use BlackIT\PrivyID\PrivyID;
use Illuminate\Http\Request;

class DigitalSignatureController extends Controller
{
    public function privyid(Request $request) {
        $code = $request->input('code');
        if ($code != '') {
            /**
             * @var $user User
             */
            $user = \Auth::user();

            try {
                $user->bind($code);
            } catch (\Exception $e) {
                \Toast::error('Gagal integrasi dengan Privy ID silahkan klik tombol bind kembali');
            }
        }
    }
}
```


### Get Identity and Update Identity

```php
    public function getIdentity(Request $request)
    {

        $user = Auth::user();
        $user->updateIdentity();
        $identity = $user->privyid();
        ...

    }
```

### Upload Document

```php
    public function uploadDocument(Request $request)
    {

        $user = Auth::user();
        ...
        $recipientBuilder = new PrivyIDRecipientBuilder();
        $recipientBuilder->addRecipient('PRIVYID', 'Signer');
        
        $checkUploadPrivy = $user->uploadDocument('CODE Document', 'Title Document', 'Parallel/Serial', 'File Location', $recipientBuilder->output());

    }
```
