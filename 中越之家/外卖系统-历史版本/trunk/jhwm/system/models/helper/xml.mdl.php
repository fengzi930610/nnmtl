<?php
class Mdl_Helper_Xml extends Model
{
	public $parser;
	public $document;
	public $stack;
	public $data;
	public $last_opened_tag;
	public $isnormal;
	public $attrs = array();
	public $failed = FALSE;
	public function __construct($isnormal)
	{
		$this->isnormal = $isnormal;
		$this->parser = xml_parser_create('ISO-8859-1');
		xml_parser_set_option($this->parser, XML_OPTION_CASE_FOLDING, false);
		xml_set_object($this->parser, $this);
		xml_set_element_handler($this->parser, 'open','close');
		xml_set_character_data_handler($this->parser, 'data');
	}
	/**
	 * create xml
	 */
	public function XMLbuild($data, $htmlon = TRUE, $isnormal = FALSE, $level = 1)
	{
		$s = $level == 1 ? "<?xml version=\"1.0\" author='IJH.CC' encoding=\"ISO-8859-1\"?>\r\n<root>\r\n" : '';
		$space = str_repeat("\t", $level);
		foreach($arr as $k => $v) {
			if(!is_array($v)) {
				$s .= $space."<item id=\"$k\">".($htmlon ? '<![CDATA[' : '').$v.($htmlon ? ']]>' : '')."</item>\r\n";
			} else {
				$s .= $space."<item id=\"$k\">\r\n".array2xml($v, $htmlon, $isnormal, $level + 1).$space."</item>\r\n";
			}
		}
		$s = preg_replace("/([\x01-\x08\x0b-\x0c\x0e-\x1f])+/", ' ', $s);
		return $level == 1 ? $s."</root>" : $s;
	}
	public function XMLparse($xml, $isnormal=false)
	{
		$this->isnormal = $isnormal;
		$data = $this->parse($xml);
		xml_parser_free($this->parser);
		return $data;
	}
	public function createXML($data, $file)
	{
	}
	public function destruct() {
		xml_parser_free($this->parser);
	}
	public function parse(&$data)
	{
		$this->document = array();
		$this->stack	= array();
		return xml_parse($this->parser, $data, true) && !$this->failed ? $this->document : '';
	}
	public function open(&$parser, $tag, $attributes)
	{
		$this->data = '';
		$this->failed = FALSE;
		if(!$this->isnormal) {
			if(isset($attributes['id']) && !is_string($this->document[$attributes['id']])) {
				$this->document  = &$this->document[$attributes['id']];
			} else {
				$this->failed = TRUE;
			}
		} else {
			if(!isset($this->document[$tag]) || !is_string($this->document[$tag])) {
				$this->document  = &$this->document[$tag];
			} else {
				$this->failed = TRUE;
			}
		}
		$this->stack[] = &$this->document;
		$this->last_opened_tag = $tag;
		$this->attrs = $attributes;
	}
	public function data(&$parser, $data)
	{
		if($this->last_opened_tag != NULL) {
			$this->data .= $data;
		}
	}
	public function close(&$parser, $tag)
	{
		if($this->last_opened_tag == $tag) {
			$this->document = $this->data;
			$this->last_opened_tag = NULL;
		}
		array_pop($this->stack);
		if($this->stack) {
			$this->document = &$this->stack[count($this->stack)-1];
		}
	}
}
