<?php

namespace App\Exceptions;

use RuntimeException;

/**
 * Dilempar ketika proses checkout tidak dapat dilanjutkan, misalnya karena
 * produk hilang atau stok berubah saat transaksi sedang diproses.
 */
class CheckoutException extends RuntimeException
{
}
