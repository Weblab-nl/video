<?php
namespace Weblab;

/**
 * Helper class to get the video information for a video. Supported types are
 * vimea, youtube and wistia
 * 
 * @author Weblab.nl - Thomas Marinissen 
 */
abstract class Video {

    /**
     * The video url
     *  
     * @var string
     */
    protected $url;

    /**
     * The video type
     * 
     * @var string
     */
    protected $type;

    /**
     * The video identifier
     * 
     * @var string
     */
    protected $id;

    /**
     * The video thumbnail url
     * 
     * @var string
     */
    protected $thumbnail = null;
    
    /**
     * The video information from the api
     * 
     * @var mixed
     */
    protected $videoInformation = null;
    
    /**
     * Return the video class object for the url
     * 
     * @param   string                  The video url
     * @return  \Weblab\Video           The video object
     * 
     * @throws Exception                Whenever there is no supported type
     */
    public static function load($url) {
        return self::parseVideo($url);
    }
    
    /**
     * Get the video thumbnail
     * 
     * @return  string              The video thumbnail url
     */
    abstract public function thumbnail();
    /**
     * The video embed code
     * 
     * @param   int             The video height
     * @param   int             The video width
     * @return  string          The video embed code
     */
    abstract public function html($height, $width);
    
    /**
     * Get the video Api Information
     * 
     * @return mixed                    The video information from the video api of the type
     */
    abstract protected function videoApiInformation();

    /**
     * Get the video url
     * 
     * @return string               The video url
     */
    public function url() {
        return $this->url;
    }

    /**
     * Get the video type (vimea, youtube, wistia)
     * 
     * @return string               The video type
     */
    public function type() {
        return $this->type;
    }

    /**
     * Get the video identifier
     * 
     * @return string               The video identifier 
     */
    public function id() {
        return $this->id;
    }
    
    /**
     * Set the video thumbnail
     * 
     * @param   string                  The video thumbnail
     */
    public function setThumbnail($thumbnail) {
        $this->thumbnail = $thumbnail;
    }
    
    /**
     * Function to download the thumbnail file
     * 
     * @return \Th\FileDownloader
     */
    public function downloadThumb() {
        // create the path to the thumbnail file
        $file = 'http:' . $this->thumbnail();

        // set the allowed mime types
        $allowedMimeTypes = array('image/jpeg', 'image/png', 'image/gif'); 

        // set the allowed image extensions
        $allowedExtensions = array('jpg', 'jpeg', 'png', 'gif');

        // try downloading the file
        try {
            $downloader = new \Th\FileDownloader($file, $allowedMimeTypes, $allowedExtensions);
            $downloader->download();
        } catch (\Exception $exception) {
            return null;
        }
        
        // done, return the downloader
        return $downloader;
    }
    
    /**
     * Return whether the video is low res or not (by default this is always false)
     * 
     * @return boolean                          Whether the video is low res or not
     */
    public function isLowRes() {
        return false;
    }
    
    /**
     * Constructor
     * 
     * @param   string              The video url
     */
    protected function __construct($url) {
        // parse the video information
        $this->url = $url;

        // parse the video information
        $this->parse($url);
    }
    
    /**
     * Parse the video and return the video class object
     * 
     * @param   string              The video url
     * @return  \Weblab\Video       The video object
     * 
     * @throws Exception            Whenever there is no supported type
     */
    private static function parseVideo($url) {
        // parse the id and type
        if (preg_match('/youtu\.?be/', $url) == 1) {
            return new \Weblab\Video\Youtube($url);
        }
        
        // parse whenever we are dealing with vimeo
        if (preg_match('/vimeo/', $url) == 1) {
            return new \Weblab\Video\Vimeo($url);
        } 
        
        // parse in case of wistia
        if (preg_match('/https?:\/\/(.+)?(wistia.com|wi.st)\/(medias|embed)\/.*/', $url) == 1) {
            return new \Weblab\Video\Wistia($url);
        }
        
        // something went wrong, throw an exception
        throw new Exception('Not a valid video');
    }

}
