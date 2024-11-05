<?php

namespace Api\Enum;

enum TaskProposal : int
{
    case Created = 0;

    case Accepted = 1;

    case Rejected = 2;

}
