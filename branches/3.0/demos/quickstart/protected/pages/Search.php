<?php
/*
 * Created on 7/05/2006
 */

class Search extends TPage
{
	public function onLoad($param)
	{
		if(!$this->IsPostBack && strlen($text = $this->search->getText()) > 0)
		{
			$search = $this->getApplication()->getModule("search");
			$this->results->setDataSource($search->find($text));
			$this->results->dataBind();
		}
	}
	
	public function highlightSearch($text)
	{
		$words = str_word_count($text, 1);
		$keys = str_word_count(strtolower($this->search->getText()),1);
		$where = 0;
		$t = count($words);
		for($i = 0; $i<$t; $i++)
		{
			if($this->containsKeys($words[$i], $keys))
			{
				$words[$i] = '<span class="searchterm">'.$words[$i].'</span>';
				$where = $i;
				break;
			}
		}
		
		$min = 	$where - 15 < 0 ? 0 : $where - 15;
		$max = 	$where + 15 > $t ? $t : $where + 15;
		$subtext = array_splice($words, $min, $max-$min);
		$prefix = $min == 0 ? '' : '...';
		$suffix = $max == $t ? '' : '...';
		return $prefix.implode(' ', $subtext).$suffix;
	}
	
	protected function containsKeys($word, $keys)
	{
		foreach($keys as $key)
		{
			if(is_int(strpos($word, $key)))
				return true;	
		}
		return false;
	}
}

?>