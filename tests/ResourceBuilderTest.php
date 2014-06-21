<?php

namespace Markup\Contentful\Tests;

use Markup\Contentful\ResourceBuilder;

class ResourceBuilderTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->builder = new ResourceBuilder();
    }

    public function testBuildSpace()
    {
        $data = [
            'sys' => [
                'type' => 'Space',
                'id' => 'cfexampleapi',
            ],
            'name' => 'Contentful Example API',
            'locales' => [
                [
                    'code' => 'en-US',
                    'name' => 'English',
                ],
                [
                    'code' => 'tlh',
                    'name' => 'Klingon',
                ],
            ],
        ];
        $space = $this->builder->buildFromData($data);
        $this->assertInstanceOf('Markup\Contentful\SpaceInterface', $space);
        $this->assertEquals('cfexampleapi', $space->getId());
        $locale = $space->getLocales()[1];
        $this->assertInstanceOf('Markup\Contentful\Locale', $locale);
        $this->assertEquals('Klingon', $locale->getName());
    }

    public function testBuildEntry()
    {
        $data = [
            'sys' => [
                'type' => 'Entry',
                'id' => 'cat',
                'space' => [
                    'sys' => [
                        'type' => 'Link',
                        'linkType' => 'Space',
                        'id' => 'example',
                    ],
                ],
                'createdAt' => '2013-03-26T00:13:37.123Z',
                'updatedAt' => '2013-03-26T00:13:37.123Z',
                'revision' => 1,
            ],
            'fields' => [
                'name' => 'Nyan cat',
                'color' => 'Rainbow',
                'nyan' => true,
                'birthday' => '2011-04-02T00:00:00.000Z',
                'diary' => 'Nyan cat has an epic rainbow trail.',
                'likes' => ['rainbows', 'fish'],
                'bestFriend' => [
                    'sys' => [
                        'type' => 'Link',
                        'linkType' => 'Entry',
                        'id' => 'happycat',
                    ],
                ],
            ]
        ];
        $entry = $this->builder->buildFromData($data);
        $this->assertInstanceOf('Markup\Contentful\EntryInterface', $entry);
        $this->assertEquals('cat', $entry->getId());
        $spaceLink = $entry->getSpace();
        $this->assertInstanceOf('Markup\Contentful\Link', $spaceLink);
        $this->assertEquals('Space', $spaceLink->getLinkType());
        $fields = $entry->getFields();
        $this->assertCount(7, $fields);
        $this->assertEquals('Rainbow', $fields['color']);
        $bestFriend = $fields['bestFriend'];
        $this->assertInstanceOf('Markup\Contentful\Link', $bestFriend);
        $this->assertEquals('happycat', $bestFriend->getId());
    }
}