<?php
/*
 * Created on 10/05/2006
 */
 
class api_index 
{
	const API_URL = '';
	
	private $_index;	
	private $_api;
	
	public function __construct($index_file, $api)
	{
		$this->_api = $api;
		$this->_index = new Zend_Search_Lucene($index_file, true);
		
		
	}
	
	function create_index()
	{
		echo "Building search index...\n";
		$files = $this->get_files($this->_api);
		$count = 0;
		foreach($files as $file)
		{
			$content = $this->get_details($file, $this->_api);
			
			$doc = new Zend_Search_Lucene_Document();
			
			$title = $content['class'];
			
			echo "  Adding ".$title."\n";
			
			//unsearchable text
			$doc->addField(Zend_Search_Lucene_Field::UnIndexed('link', $content['link']));
			$doc->addField(Zend_Search_Lucene_Field::UnIndexed('title', $title));
			//$doc->addField(Zend_Search_Lucene_Field::UnIndexed('text', $content['content']));
			
			//searchable
			$body = strtolower($this->sanitize($content['content'])).' '.strtolower($title);			
			$doc->addField(Zend_Search_Lucene_Field::Keyword('page', strtolower(str_replace('.',' ',$title))));
			$doc->addField(Zend_Search_Lucene_Field::Unstored('contents',$body));
			$this->_index->addDocument($doc);
			$count++;
		}
		$this->_index->commit();
		echo "\n {$count} files indexed.\n";
	}

	function sanitize($input) 
	{
		return htmlentities(strip_tags( $input ));
	}	

	function get_files($path)
	{
		$d = dir($path);
		
		$files = array();
		while (false !== ($entry = $d->read()))
		{
			$filepath = $path.'/'.$entry;
			if(is_file($filepath) && strpos($entry, 'class-')===0)
				$files[] = realpath($filepath);
		}
		$d->close();
		return $files;
	}
	
	function get_doc_content($file)
	{
		$content = file_get_contents($file);
		$html = preg_replace('/<h1>/','~~~', $content);
		$html = preg_replace('/<![^~]+/m', '', $html);
		$html = preg_replace('/<div class="credit">[\s\w\W\S]+/m', '', $html);
		$html = preg_replace('/&nbsp;|~+|\s{2,}/',' ',$html);
		$html = preg_replace('/\s{2,}/',' ',$html);
		$text = strip_tags($html);
		$text = str_replace(' , ',', ',$text);
		return $text;
	}
	
	function get_details($file, $base)
	{
		$result['content'] = $this->get_doc_content($file);
		$find = array($base, '.html', 'class-');
		$replace = array('', '', '');
		$path = str_replace($find, $replace, $file);
		$result['class'] = $path;
		$result['link'] = self::API_URL.$file;
		return $result;
	}
}
