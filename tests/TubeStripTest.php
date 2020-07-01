<?php

use acidjazz\tubestrip\TubeStrip;
use Orchestra\Testbench\TestCase;


class TubeStripTest extends TestCase
{
    const TERM = 'GETV: ansi art for the masses';
    const TERM_RESULT = [
        "id" => "r_cYOi3pnhA",
        "title" => "GETV: ANSI Art for the Masses",
    ];

    const ID = 'r_cYOi3pnhA';
    CONST GET_RESULT = [
      "title" => "GETV: ANSI Art for the Masses",
      "description" => <<<DESC
Back before there was the Internet, early caveman dialed into computer bulletin board systems or BBSes to get their online fix. Many of these boards distinguished themselves with ANSI art, an early form of electronic cave paintings. ANSI was an extension to the even earlier form of caveman communication known as ASCII used in MS-DOS based computers. It is with great pleasure that we happened upon the San Francisco hacker art gallery "20 goto 10″ where an ANSI art exhibition was in progress. Irina Slutsky talks to curator Kevin Olson who takes us on a tour of some of the amazing early work of two ANSI artists, lordjazz and somms.\r
\r
Originally posted:\r\nhttp://www.geekentertainment.tv/2008/...
DESC,
      "viewCount" => 55585,
      "date" => "Feb 9 2008",
    ];

    public function testSearch()
    {
        $ts = new TubeStrip();
        $results = $ts->search(self::TERM);
        $this->assertEquals($results[0], (object) self::TERM_RESULT);
    }

    public function testGet()
    {

        $ts = new TubeStrip();
        $result = $ts->get(self::ID);
        $this->assertEquals($result, (object) self::GET_RESULT);

    }
    public function testSearchJson()
    {
        $ts = new TubeStrip(true);
        $results = $ts->search(self::TERM);
        $this->assertEquals($results[0], (object) self::TERM_RESULT);
    }

    public function testGetJson()
    {
        $ts = new TubeStrip(true);
        $result = $ts->get(self::ID);
        $this->assertEquals($result, (object) self::GET_RESULT);
    }
}
