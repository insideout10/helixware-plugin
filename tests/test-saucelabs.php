<?php

require_once 'vendor/autoload.php';

/**
 * WebDriverTestCase is an alias for PHPUnit_Extensions_Selenium2TestCase
 * since there is no readable documentation, refer to:
 * https://github.com/sebastianbergmann/phpunit-selenium/blob/master/Tests/Selenium2TestCaseTest.php
 * 
 */

class HelixWareSauceTest extends Sauce\Sausage\WebDriverTestCase
{

    protected $start_url = 'http://test.helixware.tv/video-001/';

    public static $browsers = array(
        
        // INTERNET EXPLORER
        // run IE on Windows
        array(
            'browserName' => 'internet explorer',
            'desiredCapabilities' => array(
                'version' => '9',
                'platform' => 'Windows 7',
            )
        ),
        
        // FIREFOX
        // run FF15 on Windows
        array(
            'browserName' => 'firefox',
            'desiredCapabilities' => array(
                'version' => '15',
                'platform' => 'Windows 7',
            )
        ),
        // run FF15 on Mac
        array(
            'browserName' => 'firefox',
            'desiredCapabilities' => array(
                'version' => '15',
                'platform' => 'OS X 10.6',
            )
        ),

        // SAFARI
        // run Safari on Mac
        array(
            'browserName' => 'safari',
            'desiredCapabilities' => array(
                'version' => '5',
                'platform' => 'OS X 10.6',
            )
        ),
        // run Mobile Safari on iOS
        array(
            'browserName' => 'safari',
            'desiredCapabilities' => array(
                'app' => 'safari',
                'device' => 'iPhone Simulator',
                'version' => '6.1',
                'platform' => 'OS X 10.8',
            )
        ),
        
        // CHROME
        // run chrome on Mac
        array(
            'browserName' => 'chrome',
            'desiredCapabilities' => array(
                'version' => '31',
                'platform' => 'OS X 10.6',
            )
        ),
        // run chrome on Windows
        array(
            'browserName' => 'chrome',
            'desiredCapabilities' => array(
                'version' => '30',
                'platform' => 'Windows 7',
            )
        ),
        // run native browser on Android
        array(
            'browserName' => 'android',
            'desiredCapabilities' => array(
                'device' => 'Android',
                'version' => '4.3',
                'platform' => 'Linux',
            )
        ),
        
        // OPERA
        // run opera 12 on Windows
//        array(
//            'browserName' => 'opera',
//            'desiredCapabilities' => array(
//                'version' => '12',
//                'platform' => 'Windows 7',
//            )
//        )
    );
    
    public function setUpPage() {
        // Waiting time for the page to load
        $this->timeouts()->implicitWait(30000);
        $this->url( $this->start_url ); // Shoul be automatic, but it isn't
    }
    
    // Utility to make the WebDriver wait for the new page title after clicking
    // (TODO: wait for complete page load)
    public function clickAndWait($element, $landingPageTitle, $timeout=30000) {
        // Click on element
        $element->click();
        
        // Wait for next page to load (at least the title)
        $this->waitUntil(function($testCase) use ($landingPageTitle) {
            return strpos($testCase->title(), $landingPageTitle) !== False;
        }, $timeout);
    }
    
    // Check video loading and playing
    public function testVideoPlayerStarted()
    {
        // Check presence of video launch command in HTML source
        // (there is no <video> tag to check on)
        $pageHTML = $this->source();
        $this->assertContains( "jwplayer('hewa-player-", $pageHTML );
        
        $brow = $this->getBrowser();
        $isSafari = stristr( $brow, 'safari' ) !== False;
        $isAndroid = stristr( $brow, 'android' ) !== False;
        if( $isAndroid || $isSafari ) {
            // Verify <video> tag is there
            $element = $this->byTag('video');
            $this->assertEquals('video', $element->name());
        }    
        else {
            // Verify flash object started
            $element = $this->byTag('object');
            $this->assertEquals('object', $element->name());
        }
            
        ///////////////////////////////////////////////////
        // Verify actual video playback with a js script///
        ///////////////////////////////////////////////////
        
        // Extend timeout for scripts
        $this->timeouts()->asyncScript(20000);
        // The script will Wait 9 seconds and return player state
        $script = "var callback = arguments[0];
                   window.setTimeout(function() {
                       jwplayer(0).play(true);
                       // Another timeout to let the player buffer
                       window.setTimeout(function(){
                            callback( jwplayer(0).getState() );
                       }, 3000);
                   }, 5000);";
        // Execute script
        $result = $this->executeAsync(array(
            'script' => $script,
            'args'   => array()
        ));
        
        // Verify player was in PLAYING state
        // when something goes wrong, the state is BUFFERING or IDLE
        $this->assertEquals('PLAYING', $result);
    }
}
