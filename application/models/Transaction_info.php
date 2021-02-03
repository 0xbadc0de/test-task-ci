<?php

namespace Model;

use System\Core\Emerald_enum;

class Transaction_info extends Emerald_enum
{
    const TRANSACTION_TYPE_TOPUP = 'topup';
    const TRANSACTION_TYPE_LIKE = 'like';
    const TRANSACTION_TYPE_BOOSTER_PACK = 'booster_pack';
}