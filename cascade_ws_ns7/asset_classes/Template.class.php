<?php 
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2016 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
  * 3/14/2016 Minor bug fix.
  * 5/28/2015 Added namespaces.
  * 7/3/2014 Added getPageRegionStdForPageConfiguration.
  * 6/4/2014 Added getPageRegionNames.
 */
namespace cascade_ws_asset;

use cascade_ws_constants as c;
use cascade_ws_AOHS      as aohs;
use cascade_ws_utility   as u;
use cascade_ws_exception as e;
use cascade_ws_property  as p;

/**
<documentation>
<description><h2>Introduction</h2>
<p>A <code>Template</code> object represents a template asset. It contains <a href="http://www.upstate.edu/cascade-admin/web-services/api/property-classes/page-region.php"><code>PageRegion</code></a> objects and can be associated with an <a href="http://www.upstate.edu/cascade-admin/web-services/api/asset-classes/xslt-format.php"><code>XsltFormat</code></a> object.</p>
<h2>Structure of <code>template</code></h2>
<pre>template
  id
  name
  parentFolderId
  parentFolderPath
  path
  lastModifiedDate
  lastModifiedBy
  createdDate
  createdBy
  siteId
  siteName
  targetId
  targetPath
  formatId
  formatPath
  formatRecycled
  xml
  pageRegions
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
<h2>Design Issues</h2>
<ul>
<li>There is no <code>set</code> method to work with targets.</li>
</ul>
</description>
<postscript><h2>Test Code</h2><ul><li><a href="https://github.com/wingmingchan/php-cascade-ws-ns-examples/blob/master/asset-class-test-code/template.php">template.php</a></li></ul></postscript>
</documentation>
*/
class Template extends ContainedAsset
{
    const DEBUG = false;
    const TYPE  = c\T::TEMPLATE;
    
/**
<documentation><description><p>The constructor, overriding the parent method to process page regions.</p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function __construct( 
        aohs\AssetOperationHandlerService $service, \stdClass $identifier )
    {
        parent::__construct( $service, $identifier );
        
        $this->page_regions     = array();
        $this->page_region_map  = array();
        
        if( !is_null( $this->getProperty()->pageRegions ) && 
            !is_null( $this->getProperty()->pageRegions->pageRegion ) )
            self::processPageRegions( $this->getProperty()->pageRegions->pageRegion, 
                $this->page_regions, $this->page_region_map, $this->getService() );
            
        $this->xml = $this->getProperty()->xml;
    }
    
/**
<documentation><description><p>Displays <code>xml</code> and returns the calling
object.</p></description>
<example>$t->displayXml();</example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function displayXml() : Asset
    {
        $xml_string = u\XMLUtility::replaceBrackets( $this->xml );
        
        echo S_H2 . "XML" . E_H2 .
             S_PRE . $xml_string . E_PRE . HR;
        
        return $this;
    }
    
/**
<documentation><description><p>Edits and returns the calling object.</p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function edit(
        p\Workflow $wf=NULL, 
        WorkflowDefinition $wd=NULL, 
        string $new_workflow_name="", 
        string $comment="",
        bool $exception=true 
    ) : Asset
    {
        $asset        = new \stdClass();
        $region_array = array();
        $region_count = count( $this->page_regions );
        
        // convert PageRegion objects back to stdClass objects
        for( $i = 0; $i < $region_count; $i++ )
        {
            $region_array[ $i ] = $this->page_regions[ $i ]->toStdClass();
        }

        $this->getProperty()->pageRegions->pageRegion = $region_array;
        $asset->{ $p = $this->getPropertyName() }     = $this->getProperty();

        // edit asset
        $service = $this->getService();
        $service->edit( $asset );
        
        if( !$service->isSuccessful() )
        {
            throw new e\EditingFailureException( 
                S_SPAN . c\M::EDIT_ASSET_FAILURE . E_SPAN . $service->getMessage() );
        }
        return $this->reloadProperty();
    }
    
/**
<documentation><description><p>Returns <code>createdBy</code>.</p></description>
<example>echo $t->getCreatedBy(), BR;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getCreatedBy() : string
    {
        return $this->getProperty()->createdBy;
    }
    
/**
<documentation><description><p>Returns <code>createdDate</code>.</p></description>
<example>echo $t->getCreatedDate(), BR;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getCreatedDate() : string
    {
        return $this->getProperty()->createdDate;
    }
    
/**
<documentation><description><p>Returns <code>NULL</code> or an <code>XsltFormat</code> object.</p></description>
<example>$f = $t->getFormat();</example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function getFormat()
    {
        if( isset( $this->getProperty()->formatId ) )
        {
            return Asset::getAsset( $this->getService(),
                XsltFormat::TYPE,
                $this->getProperty()->formatId );
        }
        
        return NULL;
    }
    
/**
<documentation><description><p>Returns <code>formatId</code> of the template.</p></description>
<example>echo u\StringUtility::getCoalescedString( $t->getFormatId() ), BR;</example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function getFormatId()
    {
        return $this->getProperty()->formatId;
    }
    
/**
<documentation><description><p>Returns <code>formatPath</code> of the template.</p></description>
<example>echo u\StringUtility::getCoalescedString( $t->getFormatPath() ), BR;</example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function getFormatPath()
    {
        return $this->getProperty()->formatPath;
    }
    
/**
<documentation><description><p>Returns <code>formatRecycled</code>.</p></description>
<example>echo u\StringUtility::boolToString( $t->getFormatRecycled() ), BR;</example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function getFormatRecycled() : bool
    {
        return $this->getProperty()->formatRecycled;
    }
    
/**
<documentation><description><p>Returns <code>lastModifiedBy</code>.</p></description>
<example>echo $t->getLastModifiedBy(), BR;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getLastModifiedBy() : string
    {
        return $this->getProperty()->lastModifiedBy;
    }
    
/**
<documentation><description><p>Returns <code>lastModifiedDate</code>.</p></description>
<example>echo $t->getLastModifiedDate(), BR;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getLastModifiedDate() : string
    {
        return $this->getProperty()->lastModifiedDate;
    }
    
/**
<documentation><description><p>Returns the <code>PageRegion</code> object bearing the name.</p></description>
<example>u\DebugUtility::dump( $t->getPageRegion( 'STORAGE' ) );</example>
<return-type>Property</return-type>
<exception></exception>
</documentation>
*/
    public function getPageRegion( string $name ) : p\Property
    {
        if( self::DEBUG ) { u\DebugUtility::dump( $this->page_region_map ); }
        
        if( !isset( $this->page_region_map[ $name ] ) )
            throw new e\NoSuchPageRegionException( 
                S_SPAN . "The region $name does not exist." . E_SPAN );
            
        return $this->page_region_map[ $name ];
    }
    
/**
<documentation><description><p>Returns the <code>Block</code> object of the region, or <code>NULL</code>.</p></description>
<example>$block = $t->getPageRegionBlock( 'STORAGE' );</example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getPageRegionBlock( string $region_name )
    {
        return $this->getPageRegion( $region_name )->getBlock();
    }
    
/**
<documentation><description><p>Returns the <code>Format</code> object of the region, or <code>NULL</code>.</p></description>
<example>$format = $t->getPageRegionFormat( 'STORAGE' );</example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getPageRegionFormat( string $region_name )
    {
        return $this->getPageRegion( $region_name )->getFormat();
    }
    
/**
<documentation><description><p>Returns an array of page region names.</p></description>
<example>u\DebugUtility::dump( $t->getPageRegionNames() );</example>
<return-type>array</return-type>
<exception></exception>
</documentation>
*/
    public function getPageRegionNames() : array
    {
        return array_keys( $this->page_region_map );
    }
    
/**
<documentation><description><p>Returns the array of <code>p\PageRegion</code> objects.</p></description>
<example>u\DebugUtility::dump( $t->getPageRegions() );</example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getPageRegions()
    {
        return $this->page_regions;
    }
    
/**
<documentation><description><p>Returns an <code>stdClass</code> object representing all page regions.</p></description>
<example>u\DebugUtility::dump( $t->getPageRegionStdForPageConfiguration() );</example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getPageRegionStdForPageConfiguration()
    {
        $temp = array();
        
        // there is at least 1
        foreach( $this->page_regions as $region )
        {
            // only returns regions with block and/or format
            if( $region->getBlockId() != NULL || $region->getFormatId() != NULL )
            {
                $temp[] = $region;
            }
        }
        
        $std          = new \stdClass();
        $region_count = count( $temp );
       
        if( $region_count == 0 )
        {
            // do nothing
        }
        else if( $region_count == 1 )
        {
            $std->pageRegions = new \stdClass();
            $std->pageRegions->pageRegion = $temp[ 0 ]->toStdClass();
        }
        else
        {
            $std->pageRegions = new \stdClass();
            $std->pageRegions->pageRegion = array();
            
            for( $i = 0; $i < $region_count; $i++ )
            {
                $std->pageRegions->pageRegion[] = $temp[ $i ]->toStdClass();
            }
        }
        
        return $std;
    }
    
/**
<documentation><description><p>An alias of <code>getPageRegionNames()</code>.</p></description>
<example></example>
<return-type></return-type>
<exception></exception>
</documentation>
*/
    public function getRegionNames()
    {
        return $this->getPageRegionNames();
    }
    
/**
<documentation><description><p>Returns <code>targetId</code>.</p></description>
<example></example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function getTargetId()
    {
        return $this->getProperty()->targetId;
    }
    
/**
<documentation><description><p>Returns <code>targetPath</code>.</p></description>
<example></example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function getTargetPath()
    {
        return $this->getProperty()->targetPath;
    }
    
/**
<documentation><description><p>Returns <code>xml</code>.</p></description>
<example>echo $t->getXml(), BR;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getXml() : string
    {
        return $this->xml;
    }
    
/**
<documentation><description><p>Returns a bool, indicating whether the <code>p\PageRegion</code> bearing that name exists.</p></description>
<example>echo u\StringUtility::boolToString( $t->hasPageRegion( 'STORAGE' ) ), BR;</example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function hasPageRegion( string $name ) : bool
    {
        return isset( $this->page_region_map[ $name ] );
    }
    
/**
<documentation><description><p>Sets the format, and returns the calling object.</p></description>
<example>$t->setFormat( $format )->edit();</example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function setFormat( Format $format=NULL ) : Asset
    {
        if( isset( $format ) )
        {
            // only XSLT format for templates
            if( $format->getType() != c\T::XSLTFORMAT )
            {
                throw new \Exception( 
                    S_SPAN . "Wrong type of format." . E_SPAN );
            }
            $this->getProperty()->formatId   = $format->getId();
            $this->getProperty()->formatPath = $format->getPath();
        }
        else
        {
            $this->getProperty()->formatId   = NULL;
            $this->getProperty()->formatPath = NULL;
        }
        
        return $this;
    }
    
/**
<documentation><description><p>Replaces the old <code>p\PageRegion</code> object with the new one, and returns the calling object.</p></description>
<example></example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function setPageRegion(
        string $name, p\PageRegion $page_region ) : Asset
    {
        if( !isset( $this->page_region_map[ $name ] ) )
        {
            throw new e\NoSuchPageRegionException( 
                S_SPAN . "The region $name does not exist." . E_SPAN );
        }
        
        $this->page_region_map[ $name ] = $page_region;
        
        $region_count = count( $this->page_regions );
        
        for( $i = 0; $i < $region_count; $i++ )
        {
            // use the new object to replace the old one        
            if( $this->page_regions[ $i ]->getName() == $name )
            {
                $this->page_regions[ $i ] = $page_region;
                break;
            }
        }
        
        return $this;
    }
    
/**
<documentation><description><p>Attaches the block to the named page region, and returns
the calling object.</p></description>
<example>$t->setPageRegionBlock( 'DEFAULT', NULL )->
    setPageRegionFormat( 'DEFAULT', NULL )->
    edit()->dump();</example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function setPageRegionBlock( 
        string $name, Block $block=NULL, 
        bool $block_recycled=false, bool $no_block=false ) : Asset
    {
        $page_region = $this->getPageRegion( $name );
        $page_region->setBlock( $block, $block_recycled, $no_block );
        $this->setPageRegion( $name, $page_region );
        
        return $this;
    }
    
/**
<documentation><description><p>Attaches the format to the named page region, and returns
the calling object.</p></description>
<example>$t->setPageRegionBlock( 'DEFAULT', NULL )->
    setPageRegionFormat( 'DEFAULT', NULL )->
    edit()->dump();</example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function setPageRegionFormat(
        string $name, Format $format=NULL,
        bool $format_recycled=false, bool $no_format=false ) : Asset
    {
        $page_region = $this->getPageRegion( $name );
        $page_region->setFormat( $format, $format_recycled, $no_format );
        $this->setPageRegion( $name, $page_region );
        
        return $this;
    }
    
/**
<documentation><description><p>Sets <code>xml</code> and returns the calling
object.</p></description>
<example>$t->setXML( $xml )->edit();</example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public function setXml( string $xml ) : Asset
    {
        if( trim( $xml ) == "" )
            throw new e\EmptyValueException( 
                S_SPAN . c\M::EMPTY_XML . E_SPAN );

        $this->getProperty()->xml = $xml;
        return $this;
    }
    
/**
<documentation><description><p>Processes page regions, used by this class and <a href="http://www.upstate.edu/cascade-admin/web-services/api/property-classes/page-configuration.php"><code>p\PageConfiguration</code></a>.</p></description>
<example></example>
<return-type>Asset</return-type>
<exception></exception>
</documentation>
*/
    public static function processPageRegions( 
        $regions, array &$page_regions, &$page_region_map, 
        aohs\AssetOperationHandlerService $service )
    {
        if( $regions == NULL )
            return;
            
        if( !is_array( $regions ) )
        {
            $regions = array( $regions );
        }
        
        $page_regions = array();
        $page_region_map = array();
        
        foreach( $regions as $region )
        {
            $pr             = new p\PageRegion( $region, $service );
            $page_regions[] = $pr;
            $page_region_map[ $region->name ] = $pr;
        }
    }

    private $format;
    private $page_regions;       // ordered PageRegion objects
    private $page_region_map;    // associative array: name => PageRegion objects
    private $xml;
}
?>