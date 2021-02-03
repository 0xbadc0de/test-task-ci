<?php

namespace Model;

use System\Core\Emerald_enum;

class Transaction_type extends Emerald_enum
{
    const TRANSACTION_TYPE_WITHDRAW = 'withdraw';
    const TRANSACTION_TYPE_REFILL = 'refill';
}