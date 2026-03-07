<?php

namespace AFEB\Modules\WPImport;

if (!defined('ABSPATH')) {
	exit;
}

/**
 * "Vertex Addons for Elementor" Parser Class
 * 
 * @class Parser
 * @version 1.4.0
 */
class Parser
{
	public function parse($file)
	{
		if (extension_loaded('simplexml')) {
			$parser = new SimpleXMLParser();
			$result = $parser->parse($file);

			// If SimpleXML succeeds or this is an invalid WXR file then return the results.
			if (!is_wp_error($result) || 'SimpleXML_parse_error' != $result->get_error_code())
				return $result;
		} elseif (extension_loaded('xml')) {
			$parser = new XMLParser();
			$result = $parser->parse($file);

			// If XMLParser succeeds or this is an invalid WXR file then return the results.
			if (!is_wp_error($result) || 'XML_parse_error' != $result->get_error_code())
				return $result;
		}

		// Use regular expressions if nothing else available or this is bad XML.
		$parser = new RegexParser();
		return $parser->parse($file);
	}
}
