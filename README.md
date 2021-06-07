# Chia API
A PHP API for interacting with the Chia Network

## Install

```bash
composer require brooksyang/chia-api
```
## Requirements

* PHP >= 7.0

## Usage

```php
use BrooksYang\ChiaAPI\Chia;
use BrooksYang\ChiaAPI\Provider;
use BrooksYang\ChiaAPI\Exception\ChiaException;

$cert = '/path/to/crt';
$sslKey = '/path/to/ssl_key';
$fullNode = new HttpProvider('https://localhost:8555', $cert, $sslKey);
$walletServer = new HttpProvider('https://localhost:9256', $cert, $sslKey);

try {
    $chia = new Chia($fullNode, $walletServer);
} catch (ChiaException $e) {
    exit($e->getMessage());
}

// Get next address
chia->getNextAddress();

// Get wallet balance
$chia->getWalletBalance();

// Send transaction
$chia->sendTransaction('xch1w0k7fwzrdkt8xqth45zln0d9anvw7gs26lkgv3yhngrdct7hkpmqdpyhmp', 1);
```

## Donations
 - TRX: ```TB8gbB3erNn96poiMumkzX7my4Em9Lx9oG```
 - XCH: ```xch1w0k7fwzrdkt8xqth45zln0d9anvw7gs26lkgv3yhngrdct7hkpmqdpyhmp```
