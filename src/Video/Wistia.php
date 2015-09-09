<?php
namespace Weblab\Video;

/**
 * Helper class to get the wistia video information for a video.
 * 
 * @author Weblab.nl - Thomas Marinissen
 */
class Wistia extends \Weblab\Video {

    /**
     * The embed type
     * 
     * @var string
     */
    private $embedType = 'api';
    
    /**
     * The height of the video
     * 
     * @var int
     */
    private $height = null;

    /**
     * The width of the video
     * 
     * @var int 
     */
    private $width = null;
    
    /**
     * The embed body if set
     * 
     * @var string
     */
    private $embedBody = null;

    /**
     * The api information for the wistia video
     * 
     * @var type 
     */
    private $apiInformation = array();
    
    /**
     * Constructor
     * 
     * @param   string              The video url
     */
    public function __construct($url) {
        parent::__construct($url);
    }
    
    /**
     * Get the video thumbnail
     * 
     * @return  string              The video thumbnail url
     */
    public function thumbnail() {
        // get the video information
        $information = $this->videoApiInformation();
        
        // done, no thumbnail found, return out
        if (!is_object($information) || is_object($information) && !isset($information->thumbnail_url)) {
            return $this->thumbnail = '';
        }
                
        // done, return the thumbnail
        return $this->thumbnail = $information->thumbnail_url;
    }

    /**
     * Get the video thumbnail scaled
     * 
     * @param   int             The thumbnail height
     * @param   int             The thumbnail width     
     * @return  string          The video thumbnail url
     */
    public function thumbnailScaled($height, $width) {
        // get the thumbnail
        $thumbnail = parse_url($this->thumbnail());

        // done, return the thumbnail
        return $thumbnail['scheme'] . '://' . $thumbnail['host'] . $thumbnail['path'] . '?image_crop_resized=' . $width . 'x' . $height;
    }

    /**
     * The video embed code
     * 
     * @param   int             The video height
     * @param   int             The video width
     * @return  string          The video embed code
     */
    public function html($height, $width) {
        // set the height and width
        $this->setHeight($height)
            ->setWidth($width);
        
        // get the video information
        $information = $this->videoApiInformation();
        
        // if there is no embedBody, return out
        if (is_null($this->embedBody)) {
            return $information->html;
        }
        
        // get the link part
        $link = substr($information->html, 0, strpos($information->html, '<img src='));
        
        // get the script part
        $script = substr($information->html, strpos($information->html,'<script charset'), strlen($information->html) - strpos($information->html,'<script charset'));
        
        // done, return the html
        return $link . $this->embedBody . '</a>' . $script;
    }
    
    /**
     * Set the height
     * 
     * @param   int                           The height to set
     * @return  \Weblab\Video\Wistia          The instance of this, to make chaining possible
     */
    public function setHeight($height) {
        $this->height = $height;
        
        // done, return the instance of this, to make chaining possible
        return $this;
    }
    
    /**
     * Set the width
     * 
     * @param   int                           The width to set
     * @return  \Weblab\Video\Wistia          The instance of this, to make chaining possible
     */
    public function setWidth($width) {
        $this->width = $width;
        
        // done, return the instance of this, to make chaining possible
        return $this;
    }
    
    /**
     * Set the embed type
     * 
     * @param   string                        The embed type to use
     * @return  \Weblab\Video\Wistia          The instance of this, to make chaining possible
     */
    public function setEmbedType($embedType) {
        $this->embedType = $embedType;
        
        // done, return the instance of this, to make chaining possible
        return $this;
    }
    
    /**
     * Set the embed body
     * 
     * @param   string                        The embed body (html markup)
     * @return  \Weblab\Video\Wistia          The instance of this, to make chaining possible
     */
    public function setEmbedBody($embedBody) {
        // only a popover has an embed body, so return out if this is the wrong
        // type
        if ($this->embedType != 'popover') {
            return $this;
        }
        
        $this->embedBody = $embedBody;
        
        // done, return the instance of this, to make chaining possible
        return $this;
    }

    /**
     * Helper function to parse an video information from the url
     * 
     * @param   string                The url to parse
     * @return  \Weblab\Video         The instance of this, to make chaining possible
     */
    protected function parse($url) {
        // if this is not a valid wistia video, throw an error
        if (preg_match('/https?:\/\/(.+)?(wistia.com|wi.st)\/(medias|embed)\/.*/', $url) == 0) {
            // something went wrong, throw an exception
            throw new \Exception('Not a valid video');
        }
        
        // try getting the id
        if (preg_match('/https?:\/\/(.+)?(wistia.com|wi.st)\/(medias|embed)\/?(\S+).*$/', $url, $match) !== 0) {
            $this->id = $match[4];
        }

        // set the type
        $this->type = 'wistia';
            
        // done, return out
        return $this;        
    }
    
    /**
     * Get the videoApiInformation
     * 
     * @return mixed                    The video information from the video api of the type
     */
    protected function videoApiInformation() {
        // the url parameters
        $urlParameters = array(
            'handle' => 'oEmbedVideo',
            'embedType' => $this->embedType,
        );
        
        if (!is_null($this->width)) {
            $urlParameters['width'] = $this->width;
        }
        
        if (!is_null($this->height)) {
            $urlParameters['height'] = $this->height;
        }        
        
        // api information url
        $url = $this->url() . '?' . http_build_query($urlParameters);
        
        // if the api information is known already for the url, return it
        if (isset($this->apiInformation[md5($url)])) {
            return $this->apiInformation[md5($url)];
        }
        
        // done, return the video information
        return $this->apiInformation[md5($url)] = json_decode(file_get_contents('http://fast.wistia.com/oembed.json?url=' . urldecode($url)));
    }

}
