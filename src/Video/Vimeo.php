<?php
namespace Weblab\Video;

/**
 * Helper class to get the video information for a vimeo video.
 * 
 * @author Weblab.nl - Thomas Marinissen
 */
class Vimeo extends \Weblab\Video {

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
        // if the thumbnail is known, return it
        if (!is_null($this->thumbnail)) {
            return $this->thumbnail;
        }

        // get the video information
        $information = $this->videoApiInformation();

        // if there is no video image, return out
        if (!isset($information[0]) || (isset($information[0]) && !isset($information[0]['thumbnail_large']))) {
            return $this->thumbnail = '';
        }

        // done, set and return the thumbnail
        return $this->thumbnail = str_replace('https:', '', $information[0]['thumbnail_large']);
    }

    /**
     * Get the video title
     *
     * @return  string              The video title
     */
    public function title() {
        // if the title is known, return it
        if (!empty($this->title)) {
            return $this->title;
        }

        // get the video information
        $information = $this->videoApiInformation();

        // if there is no video title, return out
        if (!isset($information[0]) || (isset($information[0]) && !isset($information[0]['title']))) {
            return $this->title = '';
        }

        // done, set and return the title
        return $this->title = $information[0]['title'];
    }

    /**
     * Get the video description
     *
     * @return  string              The video description
     */
    public function description() {
        // if the description is known, return it
        if (!empty($this->description)) {
            return $this->description;
        }

        // get the video information
        $information = $this->videoApiInformation();

        // if there is no video description, return out
        if (!isset($information[0]) || (isset($information[0]) && !isset($information[0]['description']))) {
            return $this->description = '';
        }

        // done, set and return the description
        return $this->description = $information[0]['description'];
    }

    /**
     * The video embed code
     * 
     * @param   int             The video height
     * @param   int             The video width
     * @return  string          The video embed code
     */
    public function html($height, $width) {
        // create the vimeo iframe and return it
        return '<iframe id="' . $this->id() . '" class="vimeo-video-player" class="expand" height="'. $height . '" src="//player.vimeo.com/video/' . $this->id() . '?api=1&player_id=' . $this->id() . '" '
                . 'width="' . $width . '" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>';
    }

    /**
     * Helper function to parse an video information from the url
     * 
     * @param   string                The url to parse
     * @return  \Weblab\Video         The instance of this, to make chaining possible
     */
    protected function parse($url) {
        // parse whenever we are dealing with vimeo
        if (preg_match('/vimeo/', $url) == 0) {
            // something went wrong, throw an exception
            throw new \Exception('Not a valid video');
        }
        
        // try getting the id
        if (preg_match('/^https?:\/\/(www\.)?vimeo\.com\/(clip\:)?(\d+).*$/', $url, $match) != 0) {
            $this->id = $match[3];
        } elseif (preg_match('/^https?:\/\/(www\.)?vimeo\.com\/video\/(clip\:)?(\d+).*$/', $url, $match) != 0) {
            $this->id = $match[3];
        }

        // set the type
        $this->type = 'vimeo';

        // done, return out
        return $this;
    }
    
    /**
     * Get the videoApiInformation
     * 
     * @return mixed                    The video information from the video api of the type
     */
    protected function videoApiInformation() {
        // if the video information has been set before, return it
        if (!is_null($this->videoInformation)) {
            return $this->videoInformation;
        }
        
        // done, return the vimeo information
        return $this->videoInformation = unserialize(@file_get_contents('https://vimeo.com/api/v2/video/' . $this->id() . '.php'));
    }
    
}
