<?php
/**
 * RSS class.
 */
namespace Bluejacket; 
class RSS
{
	public $RSS_Content = array();
	public $RSS_All = array();


	/**
	 * RSS_Tags function.
	 *
	 * @access public
	 * @param mixed $item

	 * @param mixed $type
	 * @return void
	 */
	public function RSS_Tags($item, $type)
	{
		$y = array();
		$tnl = $item->getElementsByTagName("title");
		$tnl = $tnl->item(0);
		$title = $tnl->firstChild->textContent;

		$tnl = $item->getElementsByTagName("link");
		$tnl = $tnl->item(0);
		$link = $tnl->firstChild->textContent;

		$tnl = $item->getElementsByTagName("pubDate");
		$tnl = $tnl->item(0);
		$date = $tnl->firstChild->textContent;

		$tnl = $item->getElementsByTagName("description");
		$tnl = $tnl->item(0);
		$description = $tnl->firstChild->textContent;

		$y["title"] = $title;
		$y["link"] = $link;
		$y["date"] = $date;
		$y["description"] = $description;
		$y["type"] = $type;

		return $y;
	}


	/**
	 * RSS_Channel function.
	 *
	 * @access private
	 * @param mixed $channel
	 * @return void
	 */
	private function RSS_Channel($channel)
	{
		$items = $channel->getElementsByTagName("item");

		// Processing channel

		$y = $this->RSS_Tags($channel, 0);  // get description of channel, type 0
		array_push($this->RSS_Content, $y);

		// Processing articles

		foreach($items as $item)
		{
			$y = $this->RSS_Tags($item, 1); // get description of article, type 1
			array_push($this->RSS_Content, $y);
		}
	}

	/**
	 * RSS_Retrieve function.
	 *
	 * @access private
	 * @param mixed $url
	 * @return void
	 */
	private function RSS_Retrieve($url)
	{
		$doc  = new DOMDocument();
		$doc->load($url);

		$channels = $doc->getElementsByTagName("channel");

		$this->RSS_Content = array();

		foreach($channels as $channel)
		{
			$this->RSS_Channel($channel);
		}

	}


	/**
	 * RSS_RetrieveLinks function.
	 *
	 * @access private
	 * @param mixed $url
	 * @return void
	 */
	private function RSS_RetrieveLinks($url)
	{
		$doc  = new DOMDocument();
		$doc->load($url);

		$channels = $doc->getElementsByTagName("channel");

		$this->RSS_Content = array();

		foreach($channels as $channel)
		{
			$items = $channel->getElementsByTagName("item");
			foreach($items as $item)
			{
				$y = $this->RSS_Tags($item, 1); // get description of article, type 1
				array_push($this->RSS_Content, $y);
			}

		}

	}


	/**
	 * RSS_Links function.
	 *
	 * @access public
	 * @param mixed $url

	 * @param int $size (default: 15)
	 * @return void
	 */
	public function RSS_Links($url, $size = 15)
	{

		$page = "<ul>";

		$this->RSS_RetrieveLinks($url);
		if($size > 0)
			$recents = array_slice($this->RSS_Content, 0, $size + 1);

		foreach($recents as $article)
		{
			$type = $article["type"];
			if($type == 0) continue;
			$title = $article["title"];
			$link = $article["link"];
			$page .= "<li><a href=\"$link\">$title</a></li>\n";
		}

		$page .="</ul>\n";

		return $page;

	}



	/**
	 * RSS_Display function.
	 *
	 * @access public
	 * @param mixed $url

	 * @param int $size (default: 15)

	 * @param bool $showDesc (default: false)

	 * @param int $site (default: 0)

	 * @param int $withdate (default: 0)
	 * @return void
	 */
	public function RSS_Display($url, $size = 15, $showDesc=false , $site = 0, $withdate = 0)
	{
		$opened = false;
		$page = "";
		$site = (intval($site) == 0) ? 1 : 0;

		$this->RSS_Retrieve($url);
		if($size > 0)
			$recents = array_slice($this->RSS_Content, $site, $size + 1 - $site);

		foreach($recents as $article)
		{
			$type = $article["type"];
			if($type == 0)
			{
				if($opened == true)
				{
					$page .="</ul>\n";
					$opened = false;
				}
				$page .="<b>";
			}
			else
			{
				if($opened == false)
				{
					$page .= "<ul>\n";
					$opened = true;
				}
			}
			$title = $article["title"];
			$link = $article["link"];
			$page .= "<li><a href=\"$link\" target=\"_blank\">$title</a>";
			if($withdate)
			{
				$date = $article["date"];
				$page .=' <span class="rssdate">'.$date.'</span>';
			}
			if($showDesc){
				$description = $article["description"];
				if($description != false)
				{
					$page .= "<br><span class='rssdesc'>$description</span>";
				}
			}
			$page .= "</li>\n";

			if($type==0)
			{
				$page .="</b><br />";
			}

		}

		if($opened == true)
		{
			$page .="</ul>\n";
		}
		return $page."\n";

	}


	/**
	 * date_compare function.
	 *
	 * @access public
	 * @param mixed $x

	 * @param mixed $y
	 * @return void
	 */
	public function date_compare($x, $y)
	{
		return strtotime($y["date"]) - strtotime($x["date"]);
	}

	/**
	 * RSS_Sort function.
	 *
	 * @access public
	 * @param mixed $urls

	 * @param int $size (default: 15)

	 * @param string $type (default: 'date')
	 * @return void
	 */
	public function RSS_Sort($urls, $size = 15, $type = 'date'){
		if(is_array($urls)){
			foreach( $urls as $url ){
				$site = (intval($site) == 0) ? 1 : 0;

				$this->RSS_Retrieve($url);
				if($size > 0)
					$recents = array_slice($this->RSS_Content, $site, $size + 1 - $site);

				array_push($this->RSS_All,$recents);
			}
		}else{
			$site = (intval($site) == 0) ? 1 : 0;

			$this->RSS_Retrieve($urls);
			if($size > 0)
				$recents = array_slice($this->RSS_Content, $site, $size + 1 - $site);

			$this->RSS_All = $recents;
		}

	}
}
?>
