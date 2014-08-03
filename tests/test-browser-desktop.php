<?php

require_once 'vendor/autoload.php';

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
        array(
            'browserName' => 'chrome',
            'desiredCapabilities' => array(
                'platform' => 'Linux'
          )
        ),
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

    public function testVideoPlayerStarted()
    {
        // Check presence of video launch command in HTML source
        // (there is no <video> tag to check on)
        $pageHTML = $this->source();
        $this->assertContains( "jwplayer('hewa-player-", $pageHTML );
        
        // Verify flash object started (should be flash only)
        $element = $this->byTag('object');
        $this->assertEquals('object', $element->name());
        
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
}
