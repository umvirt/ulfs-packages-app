<?php
/**
 * ULFS Packages Web-Application
 *
 * Site startup file
 *
 * This file is loaded only by site php-files
 */

//start output buffering
ob_start();
//start session
session_start();
//load main startup file
include "main.php";

