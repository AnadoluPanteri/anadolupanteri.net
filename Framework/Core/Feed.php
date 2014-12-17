<?php
class Feed
{
 private $items        = array( );
 private $channels     = array( );
 public function __construct( ){
   $count         = func_num_args();
   $parameters    = func_get_args();

 if( $count == 3 ){
   $this->setFeedTitle( $parameters[0] );
   $this->setFeedLink( $parameters[1] );
   $this->setFeedDesc( $parameters[2] );
 }

 else if ( $count == 1 AND is_array( $parameters[0] ) )
 foreach( $parameters[0] as $key => $value )
 if( $key == 'title' )
   $this->setFeedTitle( $value );
 else if( $key == 'link' )
   $this->setFeedLink( $value );
 else if( $key == 'description' )
   $this->setFeedDesc( $value );
 else if( $key == 'generator' )
   $this->setFeedGenerator( $value );
 else if( $key == 'language' )
   $this->setFeedLang( $value );
 else if( $key == 'image' )
   $this->setFeedImage( $value[0], $value[1], $value[2] );
 else
   $this->setChannelElm( $key, $value );
 }

 /*
 * Function responsible of treating the channel elements
 * Responsible of making the conversion of arrays to tags
 */
 private function meta_array( $array ){
 $output = '';

 foreach( $array as $key => $value )
   if( is_array( $value ) )
     $output .=  PHP_EOL . "<$key>" . $this->meta_array( $value ) . "</$key>" . PHP_EOL;
   else
     $output .= PHP_EOL . "<$key>$value</$key>" . PHP_EOL;

 return $output;
 }

 /*
 * Sets any channel element
 */
 public function setChannelElm( $tagName, $content ){
   $this->channels[ $tagName ] = $content ;
 }

 /*
 * Sets channel title
 */
 public function setFeedTitle( $title ){
   $this->setChannelElm( 'title', $title );
 }

 /*
 * Sets channel link
 */
 public function setFeedLink( $link ){
   $this->setChannelElm( 'link', $link );
 }

 /*
 * Sets channel description
 */
 public function setFeedDesc( $desc ){
   $this->setChannelElm( 'description', $desc);
 }

 /*
 * Sets channel language
 */
 public function setFeedLang( $lang='en_en' ){
   $this->setChannelElm( 'language', $lang );
 }

 /*
 * Sets channel image
 */
 public function setFeedImage( $title, $imag, $link, $width = '', $height = '' ){
   $this->setChannelElm('image', array(
   'title' => $title,
   'link'=> $link,
   'url' => $imag,
   'width' => $width,
   'height' => $height
   ) );
 }

 /*
 * Sets channel Feed Generator Script
 */
 private function setFeedGenerator( $desc = 'RSS Feed Generator - http://www.example.com/rss' ){
   $this->setChannelElm('generator', $desc );
 }

 private function genHead( ) {
   echo '<?xml version="1.0" encoding="utf-8"?>' . PHP_EOL . '<rss version="2.0">' . PHP_EOL;
 }

 /*
 * Generate and print the channel's elements
 */
 private function genChannel( ){
   echo '<channel>' . PHP_EOL;
   echo $this->meta_array( $this->channels );
 }

 /*
 * Add each Item object to an array to be treated after
 */
 public function addItem( $item ){
   if( is_array( $item ) )
     foreach( $item as $itm )
       $this->addItem( $itm );

   array_push( $this->items, $item );
 }

 private function genBody( ){
   foreach( $this->items as $item )
   $item->parseItem( );
 }

 private function genBottom( ){
   echo '</channel>' . PHP_EOL . '</rss>';
 }

 /*
 * Generates the Feed
 */
 public function genFeed( ){
   header( "Content-type: text/xml" );

   $this->genHead( );
   $this->genChannel( );
   $this->genBody( );
   $this->genBottom( );
 }
}

?>
