<?php

namespace App\Domain\Enveloppe\PlancherBas\Isolation;

enum TypeIsolation: string
{
    case ITI = 'iti';
    case ITE = 'ite';
    case ITR = 'itr';
    case ITI_ITE = 'iti_ite';
    case ITI_ITR = 'iti_itr';
    case ITE_ITR = 'ite_itr';
    case ITR_ITE_ITI = 'itr_ite_iti';
}
