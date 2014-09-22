<?php

namespace MaMWeb;

class Utils {
    public static function strtr_utf8($str, $from, $to)
    {
	$keys = array();
	$values = array();
	if(!is_array($from))
	{
	    preg_match_all('/./u', $from, $keys);
	    preg_match_all('/./u', $to, $values);
	    $mapping = array_combine($keys[0], $values[0]);
	}else
	    $mapping=$from;

	return strtr($str, $mapping);
    }

    public static function toASCII($str) {
	$s = Utils::strtr_utf8($str,
	    'ŠŒŽšœžŸ¥µÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝßàáâãäåæçèéêëìíîïðñòóôõöøùúûüýÿčČěĚřŘťŤůŮďĎŕŔôÔľĽňŇĺĹ',
	    'SOZsozYYuAAAAAAACEEEEIIIIDNOOOOOOUUUUYsaaaaaaaceeeeiiiionoooooouuuuyycCeErRtTuUdDrRoOlLnNlL'
	    );
	$s = preg_replace('@[^a-zA-Z0-9_ ().,-]@u', '', $s);
	return $s;
    }

    public static function toURL($str) {
	$s = Utils::toASCII($str);
	echo("AAA {$s} ZZZ");
	$s = preg_replace('@ @', '-', $s);
	echo("AAA {$s} ZZZ");
	$s = strtolower($s);
	echo("AAA {$s} ZZZ");
	return $s;
    }
}
