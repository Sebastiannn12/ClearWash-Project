<?php

namespace App;

enum TransactionStatus: string
{
    case Pending = 'Pending';
    case Paid = 'Paid';
    case Approve = 'Approved';
    case Expired = 'Expired';
    case Cancelled = 'Cancelled';
}
