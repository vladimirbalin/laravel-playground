<?php

namespace App\Enums;

enum LecturePaymentTypeEnum: int
{
    case FREE = 1;

    case PAY = 2;

    case PROMO = 3;
}
