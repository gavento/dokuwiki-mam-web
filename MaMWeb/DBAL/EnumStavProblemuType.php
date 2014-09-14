<?php

namespace MaMWeb\DBAL;

class EnumStavProblemuType extends EnumType
{
    protected $name = 'stav_problemu';
    protected $values = array('navrh', 'smazany', 'verejny');
}
