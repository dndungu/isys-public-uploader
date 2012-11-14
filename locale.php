<?php 

header('Content-type: text/javascript');

require_once(dirname( __FILE__ ) . '/libraries/isys_visitor_posts.php');

isys_visitor_posts::initTranslator(dirname( __FILE__ ) . '/dk.xml');

print 'var isys_visitor_posts_locale = '.json_encode(isys_visitor_posts::$translation).';';

?>