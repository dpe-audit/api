<?php

namespace App\Domain\Enveloppe\Paroi\Isolation;

enum TypeIsolation: string
{
    case ITI = 'iti';
    case ITE = 'ite';
    case ITR = 'itr';
    case ITI_ITE = 'iti_ite';
    case ITI_ITR = 'itr_iti';
    case ITE_ITR = 'itr_ite';
    case ITR_ITE_ITI = 'itr_iti_ite';
}
