<?php
namespace Weblab\Video;

/**
 * Helper class to get the youtube video information for a video
 *
 * @author Weblab.nl - Thomas Marinissen
 */
class Youtube extends \Weblab\Video {

    /**
     * Whether the video is low res or not
     *
     * @var boolean
     */
    private $lowRes = false;

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

        // done, add and return the thumbnail
        return $this->thumbnail = '//img.youtube.com/vi/' . $this->id() . '/maxresdefault.jpg';
    }

    /**
     * The video embed code
     *
     * @param   int             The video height
     * @param   int             The video width
     * @return  string          The video embed code
     */
    public function html($height, $width) {
        // set the element that the player element will be linked to
        $html = '<div class="ytplayer" id="' . $this->id() . '" data-width="' . $width . '" data-height="' . $height . '" data-video-id="' . $this->id() . '"></div>';

        // return the markup
        return $html;
    }

    /**
     * Function to download the thumbnail file
     *
     * @param   string|null             Possible name, to use to store the file
     * @return  \Th\FileDownloader      The file download instance
     */
    public function downloadThumb($name = null) {
        // call the parent
        $downloader = parent::downloadThumb();

        // if there is a downloader, return it
        if (!is_null($downloader)) {
            return $downloader;
        }

        // no downloader available, so set a low res thumbnail to make the
        // thumb available still
        $this->thumbnail = '//img.youtube.com/vi/' . $this->id() . '/0.jpg';

        // set the video to be low res
        $this->lowRes = true;

        // done, return the downloader
        return parent::downloadThumb();
    }

    /**
     * Return whether the video is low res or not
     *
     * @return boolean                          Whether the video is low res or not
     */
    public function isLowRes() {
        return $this->lowRes;
    }

    /**
     * Helper function to parse an video information from the url
     *
     * @param   string                The url to parse
     * @return  \Weblab\Video         The instance of this, to make chaining possible
     */
    protected function parse($url) {
        // if something went wrong, throw an error
        if (preg_match('/youtu\.?be/', $url) == 0) {
            throw new \Exception('Not a valid video');
        }

        // filter out the id
        if (preg_match('%(?:youtube\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $url, $match)) {
            $this->id = $match[1];
        }

        // set the type
        $this->type = 'youtube';

        // done, return out
        return $this;
    }

    /**
     * Get the videoApiInformation
     *
     * @return mixed                    The video information from the video api of the type
     */
    protected function videoApiInformation() {
        return null;
    }

}
