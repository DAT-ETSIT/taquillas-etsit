<?php

/***********************************************************************************
/*
/* Sistema de taquillas ETSIT UPM
/* @author Pablo Moncada Isla pmoncadaisla@gmail.com
/* @version 09/2013
/*
/***********************************************************************************/

global $mysqli;
$mysqli = new mysqli("localhost", "c5_dat_etsit", "passw0rd", "c5_dat_taquilla");

if (function_exists('mysql_set_charset') === false) {
    /**
     * Sets the client character set.
     *
     * Note: This function requires MySQL 5.0.7 or later.
     *
     * @see http://www.php.net/mysql-set-charset
     * @param string $charset A valid character set name
     * @param resource $link_identifier The MySQL connection
     * @return TRUE on success or FALSE on failure
     */
    function mysql_set_charset($charset, $link_identifier = null)
    {
        if ($link_identifier == null) {
			return $mysqli->query('SET CHARACTER SET "'.$charset.'"');
        } else {
			return $mysqli->query('SET CHARACTER SET "'.$charset.'"', $link_identifier);
        }
    }
}




?>
