<?php
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2016 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
  * 9/17/2016 Added initialization of recycled.
  * 12/15/2015 Fixed a bug in getAsset.
  * 6/16/2015 Fixed a bug in toStdClass.
  * 5/28/2015 Added namespaces.
  * 8/22/2014 Fixed a bug in toXml.
  * 6/23/2014 Added lastmod attribute to toXml for site map.
  * 5/12/2014 data in $c can be NULL, for audit
 */
namespace cascade_ws_property;

use cascade_ws_constants as c;
use cascade_ws_AOHS      as aohs;
use cascade_ws_asset     as a;
use cascade_ws_utility   as u;
use cascade_ws_exception as e;
 
/**
<documentation><description><h2>Introduction</h2>
<p>A <code>Child</code> object represents a <code>child</code> property found in a <a href="site://cascade-admin-old/projects/web-services/oop/classes/asset-classes/container"><code>Container</code></a> object. <a href="/web-services/api/property-classes/identifier"><code>Identifier</code></a> is an empty sub-class (i.e., class alias) of <code>Child</code>.</p>
<h2>Structure of <code>child</code></h2>
<pre>child
  id
  path
  type
  recycled
</pre>
<h2>Design Issues</h2>
<ul>
<li>There are no <code>set</code> methods in this class.</li>
</ul>
</description>
<postscript><h2>Test Code</h2><ul><li><a href="https://github.com/wingmingchan/php-cascade-ws-ns-examples/blob/master/property-class-test-code/child.php">child.php</a></li></ul></postscript>
</documentation>
*/class Child extends Property
{
/**
<documentation><description><p>The constructor.</p></description>
<example></example>
<return-type></return-type>
<exception>NullIdentifierException</exception>
</documentation>
*/
    public function __construct(
        \stdClass $c=NULL, 
        aohs\AssetOperationHandlerService $service=NULL, 
        $data1=NULL, 
        $data2=NULL, 
        $data3=NULL )
    {
        if( isset( $c ) )
        {
            if( isset( $c->id ) )
                $this->id = $c->id;
                
            if( isset( $c->path ) )
                $this->path = new Path( $c->path );
            else
                $this->path = NULL;
            $this->type     = $c->type;
            
            if( isset( $c->recycled ) )
                $this->recycled = $c->recycled;
            else
            	$this->recycled = false;
        }
        else
        {
            throw new e\NullIdentifierException( c\M::NULL_IDENTIFIER );
        }
    }
    
/**
<documentation><description>Displays some basic information, and returns the calling object.</description>
<example>$c->display();</example>
<return-type>Property</return-type>
<exception></exception>
</documentation>
*/
    public function display() : Property
    {
        echo "Type: " . $this->type . BR .
             "Path: " . $this->path->getPath() . BR .
             "ID: "   . $this->id . BR . BR;
        return $this;
    }
    
/**
<documentation><description>Returns the corresponding <code>Asset</code> object.</description>
<example>$folder = $id_child->getAsset( $service );</example>
<return-type>Asset</return-type>
<exception>NullServiceException</exception>
</documentation>
*/
    public function getAsset( aohs\AssetOperationHandlerService $service ) : a\Asset
    {
        if( $service == NULL )
            throw new e\NullServiceException( c\M::NULL_SERVICE );
            
        if( isset( $this->id ) )
            return a\Asset::getAsset( $service, $this->type, $this->id );
        else
            return a\Asset::getAsset( 
                $service, 
                $this->type, $this->path->getPath(), $this->path->getSiteName() );
    }
    
/**
<documentation><description>Returns <code>id</code>.</description>
<example>echo $child->getId(), BR;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getId() : string
    {
        return $this->id;
    }
    
/**
<documentation><description>Returns <code>path</code> (a <a href="http://www.upstate.edu/cascade-admin/web-services/api/property-classes/path.php"><code>Path</code></a> object).</description>
<example>u\DebugUtility::dump( $child->getPath()->toStdClass() );</example>
<return-type>Property</return-type>
<exception></exception>
</documentation>
*/
    public function getPath() : Property
    {
        return $this->path;
    }
    
/**
<documentation><description>Returns the <code>path</code> string of <code>path</code>.</description>
<example>echo $child->getPathPath(), BR;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getPathPath() : string
    {
        if( isset( $this->path ) )
            return $this->path->getPath();
    }
    
/**
<documentation><description>Returns <code>siteId</code> of <code>path</code>.</description>
<example>echo $child->getPathSiteId(), BR;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getPathSiteId() : string
    {
        return $this->path->getSiteId();
    }
    
/**
<documentation><description>Returns <code>siteName</code> of <code>path</code>.</description>
<example>echo u\StringUtility::getCoalescedString( $child->getPathSiteName() ), BR;</example>
<return-type>mixed</return-type>
<exception></exception>
</documentation>
*/
    public function getPathSiteName()
    {
        if( isset( $this->path ) )
            return $this->path->getSiteName();
    }
    
/**
<documentation><description>Returns <code>recycled</code>.</description>
<example>echo u\StringUtility::boolToString( $child->getRecycled() ), BR;</example>
<return-type>bool</return-type>
<exception></exception>
</documentation>
*/
    public function getRecycled() : bool
    {
        return $this->recycled;
    }
    
/**
<documentation><description>Returns <code>type</code>.</description>
<example>echo $child->getType(), BR;</example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function getType() : string
    {
        return $this->type;
    }
    
/**
<documentation><description>Returns a string representation of the object as an
<code>li</code> element, used by <a href="http://www.upstate.edu/cascade-admin/web-services/api/asset-tree/index.php"><code>AssetTree</code></a>.</description>
<example></example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function toLiString() : string
    {
        return S_LI . $this->type . " " . 
            $this->path->getPath() . " " . $this->id . E_LI;
    }
    
/**
<documentation><description><p>Converts the object back to an <code>\stdClass</code> object.</p></description>
<example>$id_child = new p\Child( $id_std );
u\DebugUtility::dump( $id_child );</example>
<return-type>stdClass</return-type>
</documentation>
*/
    public function toStdClass()
    {
        $obj           = new \stdClass();
        
        if( isset( $this->id ) )
            $obj->id   = $this->id;
        
        if( isset( $this->path ) )
            $obj->path = $this->path->toStdClass();
            
        $obj->type     = $this->type;
        $obj->recycled = $this->recycled;
        return $obj;
    }
    
/**
<documentation><description>Returns a string representation of the object as an XML element, used by <code>a\AssetTree</code>.</description>
<example></example>
<return-type>string</return-type>
<exception></exception>
</documentation>
*/
    public function toXml( 
        string $indent="", aohs\AssetOperationHandlerService $service ) : string
    {
        if( isset( $service ) )
        {
            $asset = $this->getAsset( $service );
            
            if( method_exists( $asset, "getLastModifiedDate" ) )
            {
                $lastmod = $asset->getLastModifiedDate();
            }
        }
        return $indent . "<" . $this->type . " path=\"" .
            $this->path->getPath() . "\" id=\"" . $this->id . "\"" .
            ( isset( $lastmod ) ? " lastmod=\"" . $lastmod : ""  ) .
            "\"/>\n";
    }
    
    private $id;
    private $path;
    private $type;
    private $recycled;
}
?>
