# Chia API
A PHP API for interacting with the Chia Network

# Chia blockchain RPC
https://github.com/Chia-Network/chia-blockchain/wiki/RPC-Interfaces

## Install

```bash
composer require brooksyang/chia-api
```
## Requirements

* PHP >= 7.0

## Usage

```php
use BrooksYang\ChiaApi\Chia;
use BrooksYang\ChiaApi\Provider\HttpProvider;
use BrooksYang\ChiaApi\Exception\ChiaException;

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

// Get coin record by puzzle hash
$chia->getCoinRecordsByPuzzleHash('0x73ede4b8436d96730177ad05f9bda5ecd8ef220ad7ec8644979a06dc2fd7b076');

// Get coin record by adress（Automatically convert xch address to puzzle hash）
$chia->getCoinRecordsByAddress('xch1w0k7fwzrdkt8xqth45zln0d9anvw7gs26lkgv3yhngrdct7hkpmqdpyhmp');

// Get coin info
$parentCoinInfo = '0x2d9dadc33ae71e0452f96c9544d2040275c9b37025c42764b0017a63cc8a2af6';
$puzzleHash = '0x73ede4b8436d96730177ad05f9bda5ecd8ef220ad7ec8644979a06dc2fd7b076';
$amount = '8000000000'; // 0.008 xch
$chia->getCoinInfo($parentCoinInfo, $puzzleHash, $amount);

// response
0xb9f219f539783d3db37d04b164e2fcd2019419ce97f0d44da20754ec9b13a09c

// Send multiple payments in a single transaction
$fee = 10; // mojos
$additions = [
    [ 'amount' => 2500, 'address' => 'xch1w0k7fwzrdkt8xqth45zln0d9anvw7gs26lkgv3yhngrdct7hkpmqdpyhmp', ],
    [ 'amount' =>  500, 'address' => 'xch1d5xe39yle3p574mnwvrnc0g6lafgn4dvkgay8kpmxat30krt8m4qxzymda', 'memos' => [ 'chiameh' ], ],
];

$response = $chia->sendTransactionMulti('1', $additions, $fee);
$txn_id   = $response['transaction_id'];
$tx       = $response['transaction'];
```

## Donations
 - TRX: ```TB8gbB3erNn96poiMumkzX7my4Em9Lx9oG```
 - XCH: ```xch1w0k7fwzrdkt8xqth45zln0d9anvw7gs26lkgv3yhngrdct7hkpmqdpyhmp```
