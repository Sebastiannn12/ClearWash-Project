<?php

namespace App;

enum TransactionType: string
{
    case Application = 'Application';
    case Renewal = 'Renewal';
}
