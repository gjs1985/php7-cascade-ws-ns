<?php 
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2016 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
  * 5/28/2015 Added namespaces.
  * 7/16/2014 Started using DebugUtility::out and DebugUtility::dump.
 */
namespace cascade_ws_property;

use cascade_ws_constants as c;
use cascade_ws_AOHS      as aohs;
use cascade_ws_utility   as u;
use cascade_ws_exception as e;
use cascade_ws_asset     as a;

/**
<documentation>
<description><h2>Introduction</h2>
<p>A <code>PageRegion</code> object represents a <code>pageRegion</code> property that can
be found in a <a href="http://www.upstate.edu/cascade-admin/web-services/api/asset-classes/template.php"><code>a\Template</code></a> object or a <a href="http://www.upstate.edu/cascade-admin/web-services/api/property-classes/page-configuration.php"><code>PageConfiguration</code></a> object. Since both a <a href="http://www.upstate.edu/cascade-admin/web-services/api/asset-classes/page-configuration-set.php"><code>a\PageConfigurationSet</code></a> object and a <a href="http://www.upstate.edu/cascade-admin/web-services/api/asset-classes/page.php"><code>a\Page</code></a> object contain <code>PageConfiguration</code> objects, both contain <code>PageRegion</code> objects. A page region is used to associate a block and/or a format to a template, a configuration, or a page.</p>
<h2>Structure of <code>pageRegion</code></h2>
<pre>pageRegions
  pageRegion (NULL, stdClass or array of stdClass)
    id
    name
    blockId
    blockPath
    blockRecycled
    noBlock
    formatId
    formatPath
    formatRecycled
    noFormat
</pre>
</description>
<postscript><h2>Test Code</h2><ul><li><a href="https://github.com/wingmingchan/php-cascade-ws-ns-examples/blob/master/property-class-test-code/page-region.php">page-region.php</a></li></ul></postscript>
</documentation>
*/
class PageRegion extends Property
{
    const DEBUG = false;
    const DUMP  = false;
    
/**
<documentation><description>The constructor.</description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function __construct( 
        \stdClass $region=NULL, 
        aohs\AssetOperationHandlerService $service=NULL, 
        $data1=NULL, 
        $data2=NULL, 
        $data3=NULL )
    {
        if( isset( $region ) )
        {
            if( isset( $region->id ) )
                $this->id              = $region->id;
            if( isset( $region->name ) )
                $this->name            = $region->name;
            if( isset( $region->blockId ) )
                $this->block_id        = $region->blockId; // NULL
            if( isset( $region->blockPath ) )
                $this->block_path      = $region->blockPath; // NULL
            if( isset( $region->blockRecycled ) )
                $this->block_recycled  = $region->blockRecycled;
            if( isset( $region->noBlock ) )
                $this->no_block        = $region->noBlock;
            if( isset( $region->formatId ) )
                $this->format_id       = $region->formatId; // NULL
            if( isset( $region->formatPath ) )
                $this->format_path     = $region->formatPath; // NULL
            if( isset( $region->formatRecycled ) )
                $this->format_recycled = $region->formatRecycled;
            if( isset( $region->noFormat ) )
                $this->no_format       = $region->noFormat;
            $this->service             = $service;
        
            if( self::DEBUG ) { u\DebugUtility::out( "Block ID: " . $this->block_id ); }
        }
    }
    
/**
<documentation><description>Displays some basic information of the page region,
and returns the calling object.</description>
<example>$region->display();</example>
<return-type>Property</return-type>
<exception></exception>
</documentation>
*/
    public function display() : Property
    {
        echo "ID: " . $this->id . BR .
             "Name: " . $this->name . BR;
        
        return $this;
    }
    
/**
<documentation><description>Returns the <code>a\Block</code> object of the region, or <code>NULL</code>.</description>
<example>$block = $region->getBlock();
echo u\StringUtility::boolToString( is_null( $block ) ), BR;</example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function getBlock()
    {
        if( self::DEBUG ) { u\DebugUtility::out( "Name: " . $this->name . BR . "Block ID: " . $this->block_id );; }
    
        if( isset( $this->block_id ) && $this->block_id != "" && isset( $this->service ) )
        {
            if( self::DEBUG ) {  u\DebugUtility::out( "Type of block: " . $this->getType( $this->block_id ) ); }
        
            return a\Asset::getAsset( 
                $this->service,
                $this->getType( $this->block_id ),
                $this->block_id );
        }
        return NULL;
    }
    
/**
<documentation><description>Returns <code>blockId</code>.</description>
<example>echo u\StringUtility::getCoalescedString( $region->getBlockId() ), BR;</example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function getBlockId()
    {
        return $this->block_id;
    }
    
/**
<documentation><description>Returns <code>blockPath</code>.</description>
<example>echo u\StringUtility::getCoalescedString( $region->getBlockPath() ), BR;</example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function getBlockPath()
    {
        return $this->block_path;
    }
    
/**
<documentation><description>Returns <code>blockRecycled</code>.</description>
<example>echo u\StringUtility::boolToString( $region->getBlockRecycled() ), BR;</example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function getBlockRecycled() : bool
    {
        return $this->block_recycled;
    }
    
/**
<documentation><description>Returns the <code>a\Format</code> object of the region, or <code>NULL</code>.</description>
<example>$format = $region->getFormat();</example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function getFormat()
    {
        if( isset( $this->format_id ) && $this->format_id != "" &&
            isset( $this->service ) )
        {
            if( self::DEBUG ) {  u\DebugUtility::out( __FUNCTION__ . BR . "Type of format: " . $this->getType( $this->format_id ) . BR . "Format ID: " . $this->format_id ); }
            
            return a\Asset::getAsset( 
                $this->service,
                $this->getType( $this->format_id ),
                $this->format_id );
        }
        return NULL;
    }
    
/**
<documentation><description>Returns <code>formatId</code>.</description>
<example>echo u\StringUtility::getCoalescedString( $region->getFormatId() ), BR;</example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function getFormatId()
    {
        return $this->format_id;
    }
    
/**
<documentation><description>Returns <code>formatPath</code>.</description>
<example>echo u\StringUtility::getCoalescedString( $region->getFormatPath() ), BR;</example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function getFormatPath()
    {
        return $this->format_path;
    }
    
/**
<documentation><description>Returns <code>formatRecycled</code>.</description>
<example>echo u\StringUtility::boolToString( $region->getFormatRecycled() ), BR;</example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function getFormatRecycled() : bool
    {
        return $this->format_recycled;
    }
    
/**
<documentation><description>Returns <code>id</code>.</description>
<example>echo $region->getId(), BR;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getId() : string
    {
        return $this->id;
    }
    
/**
<documentation><description>Returns <code>name</code>.</description>
<example>echo $region->getName(), BR;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getName()
    {
        return $this->name;
    }
    
/**
<documentation><description>Returns <code>noBlock</code>.</description>
<example>echo u\StringUtility::boolToString( $region->getNoBlock() ), BR;</example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function getNoBlock() : bool
    {
        return $this->no_block;
    }
    
/**
<documentation><description>Returns <code>noFormat</code>.</description>
<example>echo u\StringUtility::boolToString( $region->getNoFormat() ), BR;</example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function getNoFormat()
    {
        return $this->no_format;
    }
    
/**
<documentation><description>Attaches the block to the region, and returns the object.
If the block is NULL, then the block associated with the region at the page level will be
removed. Note that at the page level, a block cannot be removed if it is associated with
the region at the configuration level. Use the <code>noBlock</code> property to
disassociate the block from the region.</description>
<example>$region->setBlock(
    $cascade->getAsset(
        a\TextBlock::TYPE, "0bc94b1f8b7ffe83006a5cefe3ab1dac" ) );
</example>
<return-type>Property</return-type>
<exception></exception>
</documentation>
*/
    public function setBlock( 
        a\Block $b=NULL, bool $block_recycled=false, bool $no_block=false ) : Property
    {
        if( !c\BooleanValues::isBoolean( $block_recycled ) )
            throw new e\UnacceptableValueException(
                S_SPAN . "The value $block_recycled must be a boolean." . E_SPAN );
            
        if( !c\BooleanValues::isBoolean( $no_block ) )
            throw new e\UnacceptableValueException(
                S_SPAN . "The value $no_block must be a boolean." . E_SPAN );
            
        if( isset( $b ) )
        {
            if( strpos( get_class( $b ), 'Block' ) !== false )
            {
                $this->block_id   = $b->getId();
                $this->block_path = $b->getPath();
            }
            else
            {
                throw new e\NullAssetException(
                    S_SPAN . "The block " . $b->getName() . " does not exist." . E_SPAN );
            }
            
            $this->block_recycled = $block_recycled;
            $this->no_block       = $no_block;
        }
        else
        {
            $this->block_id   = NULL;
            $this->block_path = NULL;
        }
        return $this;
    }
    
/**
<documentation><description>Attaches the format to the region and returns the object.
If the format is NULL, then the format associated with the region at the page level will
be removed. Note that at the page level, a format cannot be removed if it is associated
with the region at the configuration level. Use the <code>noFormat</code> property to
disassociate the format from the region.</description>
<example>$region->setFormat(
    $cascade->getAsset(
        a\ScriptFormat::TYPE, "0bcf8ce48b7ffe83006a5cef7d7c12f5" ) );
</example>
<return-type>Property</return-type>
<exception>UnacceptableValueException, NullAssetException</exception>
</documentation>
*/
    public function setFormat( 
        a\Format $f=NULL, bool $format_recycled=false, bool $no_format=false ) : Property
    {
        if( !c\BooleanValues::isBoolean( $format_recycled ) )
            throw new e\UnacceptableValueException(
                S_SPAN . "The value $format_recycled must be a boolean." . E_SPAN );
            
        if( !c\BooleanValues::isBoolean( $no_format ) )
            throw new e\UnacceptableValueException(
                S_SPAN . "The value $no_format must be a boolean." . E_SPAN );
            
        if( isset( $f ) )
        {
            if( strpos( get_class( $f ), 'Format' ) !== false )
            {
                $this->format_id   = $f->getId();
                $this->format_path = $f->getPath();
            }
            else
            {
                throw new e\NullAssetException(
                    S_SPAN . "The format " . $f->getName() . " does not exist." . E_SPAN );
            }
            
            $this->format_recycled = $format_recycled;
            $this->no_format       = $no_format;
        }
        else
        {
            $this->format_id   = NULL;
            $this->format_path = NULL;
        }
        return $this;
    }
    
/**
<documentation><description>Sets <code>noBlock</code> and returns the calling
object.</description>
<example>$region->setNoBlock( true );
</example>
<return-type>Property</return-type>
<exception>UnacceptableValueException</exception>
</documentation>
*/
    public function setNoBlock( bool $value ) : Property
    {
        if( !c\BooleanValues::isBoolean( $value ) )
            throw new e\UnacceptableValueException(
                S_SPAN . "The value $value must be a boolean." . E_SPAN );
        $this->no_block = $value;
        return $this;
    }
    
/**
<documentation><description>Sets <code>noFormat</code> and returns the calling
object.</description>
<example>$region->setNoFormat( true );</example>
<return-type>Property</return-type>
<exception>UnacceptableValueException</exception>
</documentation>
*/
    public function setNoFormat( bool $value ) : Property
    {
        if( !c\BooleanValues::isBoolean( $value ) )
            throw new e\UnacceptableValueException(
                S_SPAN . "The value $value must be a boolean." . E_SPAN );
            
        $this->no_format = $value;
        return $this;
    }
    
/**
<documentation><description>Converts the object back to an <code>\stdClass</code>
object.</description>
<example>u\DebugUtility::dump( $region->toStdClass() );</example>
<return-type>stdClass</return-type>
<exception></exception>
</documentation>
*/
    public function toStdClass() : \stdClass
    {
        $obj                 = new \stdClass();
        $obj->id             = $this->id;
        $obj->name           = $this->name;
        $obj->blockId        = $this->block_id;
        $obj->blockPath      = $this->block_path;
        $obj->blockRecycled  = $this->block_recycled;
        $obj->noBlock        = $this->no_block;
        $obj->formatId       = $this->format_id;
        $obj->formatPath     = $this->format_path;
        $obj->formatRecycled = $this->format_recycled;
        $obj->noFormat       = $this->no_format;
        
        return $obj;
    }
    
    private function getType( string $id_string )
    {
        if( self::DEBUG) { u\DebugUtility::out( "string: " . $id_string ); }

        if( isset( $this->service ) )
        {
            $types = array( 'block', 'format' );
            $type_count = count( $types );
        
            for( $i = 0; $i < $type_count; $i++ )
            {
                $id = $this->service->createId( $types[ $i ], $id_string );
                $operation = new \stdClass();
                $read_op   = new \stdClass();
    
                $read_op->identifier = $id;
                $operation->read     = $read_op;
                $operations[]        = $operation;
            }
        
            $this->service->batch( $operations );
        
            $reply_array = $this->service->getReply()->batchReturn;
            
            if( self::DEBUG && self::DUMP ) { u\DebugUtility::dump( $reply_array ); }
        
            for( $j = 0; $j < $type_count; $j++ )
            {
                if( $reply_array[ $j ]->readResult->success == 'true' )
                {
                    foreach( c\T::$type_property_name_map as $type => $property )
                    {
                        //echo "$type => $property" . BR;
                        if( isset( $reply_array[ $j ]->readResult->asset->$property ) )
                        {
                            return $type;
                        }
                    }
                }
            }
        }
        return NULL;
    }
    
    private $id;
    private $name;
    private $block_id;
    private $block_path;
    private $block_recycled;
    private $no_block;
    private $format_id;
    private $format_path;
    private $format_recycled;
    private $no_format;
    private $service;
}
?>