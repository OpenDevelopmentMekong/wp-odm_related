<?php

require_once dirname(dirname(__FILE__)).'/utils/utils.php';

class ProfilePagesTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
      $GLOBALS['wp_odm_related_options'] = $this->getMockBuilder(Wp_odm_related_Options::class)
                                   ->setMethods(['get_option'])
                                   ->getMock();

      $GLOBALS['wp_odm_related_options']->method('get_option')
                          ->will($this->returnValueMap(array(
                               array('related_post_types', 'topic,announcement,story')
                           )));
    }

    public function tearDown()
    {
        // undo stuff here
    }

    public function testSupportedPostTypesOption()
    {
      $supported_post_types = supported_post_types_option();
      $this->assertEquals($supported_post_types,'topic,announcement,story');
    }

}
