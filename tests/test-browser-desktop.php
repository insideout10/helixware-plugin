<?php

require_once 'vendor/autoload.php';

/**
 * WebDriverTestCase is an alias for PHPUnit_Extensions_Selenium2TestCase
 * since there is no readable documentation, refer to:
 * https://github.com/sebastianbergmann/phpunit-selenium/blob/master/Tests/Selenium2TestCaseTest.php
 * 
 */

class DesktopTest extends Sauce\Sausage\WebDriverTestCase
{

    protected $start_url = 'http://test.helixware.tv/video-001/';

    public static $browsers = array(
        
        // run FF15 on Windows 8 on Sauce
        array(
            'browserName' => 'firefox',
            'desiredCapabilities' => array(
                'version' => '15',
                'platform' => 'Windows 2012',
            )
        ),
        // run Chrome on Linux on Sauce
        /*array(
            'browserName' => 'chrome',
            'desiredCapabilities' => array(
                'platform' => 'Linux'
          )
        ),*/
        // run Mobile Safari on iOS
        array(
            'browserName' => '',
            'desiredCapabilities' => array(
                'app' => 'safari',
                'device' => 'iPhone Simulator',
                'version' => '6.1',
                'platform' => 'Mac 10.8',
            )
        )//,
        // run Chrome locally
        //array(
        //    'browserName' => 'chrome',
        //    'local' => true,
        //    'sessionStrategy' => 'shared'
        //)
    );
    
    // Check video loading and playing
    public function testVideoPlayerStarted()
    {
        // Check presence of video launch command in HTML source
        // (there is no <video> tag to check on)
        $pageHTML = $this->source();
        $this->assertContains( "jwplayer('hewa-player-", $pageHTML );
        
        $capab = $this->getDesiredCapabilities();
        if( stristr( $capab['platform'], 'Mac' ) ) {
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
        $this->timeouts()->asyncScript(10000);
        
        // The script will Wait 9 seconds and return player state
        $script = "var callback = arguments[0];
                   window.setTimeout(function() {
                       callback( jwplayer(0).getState() );
                   }, 9000);";
        
        // Execute script
        $result = $this->executeAsync(array(
            'script' => $script,
            'args'   => array()
        ));
        
        // Verify player was in PLAYING state
        // when something goes wrong, the state is BUFFERING or IDLE
        $this->assertEquals('PLAYING', $result);
    }
    
    // Link navigation
    public function testNavigation() {
        
        // Verify we are on test video page
        $this->assertEquals( 'Video 001 | HelixWare', $this->title() );
        
        // Click to see the previous post
        $element = $this->byLinkText('Hello world!');
        $element->click();
        $this->assertEquals('Hello world! | HelixWare', $this->title() );
        
        // Click to go back
        $element = $this->byLinkText('Video 001');
        $element->click();
        $this->assertEquals('Video 001 | HelixWare', $this->title() );
    }
}
