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

    public function testBuildAsset()
    {
        $data = [
            'sys' => [
                'type' => 'Asset',
                'id' => 'nyancat',
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
                'title' => 'Nyan cat',
                'description' => 'A typical picture of Nyancat including the famous rainbow trail.',
                'file' => [
                    'fileName' => 'nyancat.png',
                    'contentType' => 'image/png',
                    'details' => [
                        'image' => [
                            'width' => 250,
                            'height' => 250,
                        ],
                        'size' => 12273,
                    ],
                    'url' => '//images.contentful.com/cfexampleapi/4gp6taAwW4CmSgumq2ekUm/9da0cd1936871b8d72343e895a00d611/Nyan_cat_250px_frame.png',
                ],
            ],
        ];
        $asset = $this->builder->buildFromData($data);
        $this->assertInstanceOf('Markup\Contentful\AssetInterface', $asset);
        $this->assertEquals('nyancat.png', $asset->getFilename());
        $this->assertEquals('//images.contentful.com/cfexampleapi/4gp6taAwW4CmSgumq2ekUm/9da0cd1936871b8d72343e895a00d611/Nyan_cat_250px_frame.png', $asset->getUrl());
        $this->assertEquals(12273, $asset->getFileSizeInBytes());
        $this->assertEquals('nyancat', $asset->getId());
    }

    public function testBuildAssetFromUploadDataForm()
    {
        $data = [
            'sys' => [
                'type' => 'Asset',
                'id' => 'nyancat',
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
                'title' => 'Nyan cat',
                'description' => 'A typical picture of Nyancat including the famous rainbow trail.',
                'file' => [
                    'fileName' => 'nyancat.png',
                    'contentType' => 'image/png',
                    'upload' => '//images.contentful.com/cfexampleapi/4gp6taAwW4CmSgumq2ekUm/9da0cd1936871b8d72343e895a00d611/Nyan_cat_250px_frame.png',
                ],
            ],
        ];
        $asset = $this->builder->buildFromData($data);
        $this->assertInstanceOf('Markup\Contentful\AssetInterface', $asset);
        $this->assertEquals('//images.contentful.com/cfexampleapi/4gp6taAwW4CmSgumq2ekUm/9da0cd1936871b8d72343e895a00d611/Nyan_cat_250px_frame.png', $asset->getUrl());
        $this->assertNull($asset->getFileSizeInBytes());
    }

    public function testBuildContentType()
    {
        $data = [
            'sys' => [
                'type' => 'ContentType',
                'id' => 'cat',
            ],
            'name' => 'Cat',
            'description' => 'Meow.',
            'fields' => [
                [
                    'id' => 'name',
                    'name' => 'Name',
                    'type' => 'Text',
                    'localized' => true,
                ],
                [
                    'id' => 'diary',
                    'name' => 'Diary',
                    'type' => 'Text',
                ],
                [
                    'id' => 'likes',
                    'name' => 'Likes',
                    'type' => 'Array',
                    'items' => [
                        'type' => 'Symbol',
                    ],
                ],
                [
                    'id' => 'lifes',
                    'name' => 'Lifes left',
                    'type' => 'Integer',
                ],
            ],
        ];
        $contentType = $this->builder->buildFromData($data);
        $this->assertInstanceOf('Markup\Contentful\ContentTypeInterface', $contentType);
        $this->assertEquals('Cat', $contentType->getName());
        $fields = $contentType->getFields();
        $this->assertContainsOnlyInstancesOf('Markup\Contentful\ContentTypeField', $fields);
        $this->assertTrue($fields['name']->isLocalized());
        $this->assertFalse($fields['likes']->isLocalized());
        $this->assertEquals([], $fields['lifes']->getItems());
        $this->assertEquals(['type' => 'Symbol'], $fields['likes']->getItems());
    }

    public function testBuildEntryArray()
    {
        $data = [
            [
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
            ],
            [
                'sys' => [
                    'type' => 'Entry',
                    'id' => 'cat2',
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
            ],
        ];
        $entries = $this->builder->buildFromData($data);
        $this->assertCount(2, $entries);
        $this->assertContainsOnlyInstancesOf('Markup\Contentful\EntryInterface', $entries);
        $this->assertEquals('cat2', $entries[1]->getId());
    }

    public function testBuildEntryWithArrayOfLinks()
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
                    [
                        'sys' => [
                            'type' => 'Link',
                            'linkType' => 'Entry',
                            'id' => 'happycat',
                        ],
                    ],
                ],
            ]
        ];
        $entry = $this->builder->buildFromData($data);
        $links = $entry->getField('bestFriend');
        $this->assertInternalType('array', $links);
        $this->assertCount(1, $links);
        $this->assertContainsOnlyInstancesOf('Markup\Contentful\Link', $links);
    }

    public function testBuildArrayOfEntries()
    {
        $data = [
            'sys' => [
                'type' => 'Array',
            ],
            'total' => 1,
            'skip' => 0,
            'limit' => 100,
            'items' => [
                [
                    'fields' => [
                        'name' => 'Happy Cat',
                        'bestFriend' => [
                            'sys' => [
                                'type' => 'Link',
                                'linkType' => 'Entry',
                                'id' => 'nyancat',
                            ],
                        ],
                        'likes' => [
                            'cheezburger',
                        ],
                        'color' => 'gray',
                        'birthday' => '2003-10-28T23:00:00+00:00',
                        'lives' => 1,
                        'image' => [
                            'sys' => [
                                'type' => 'Link',
                                'linkType' => 'Asset',
                                'id' => 'happycat',
                            ],
                        ],
                    ],
                    'sys' => [
                        'space' => [
                            'sys' => [
                                'type' => 'Link',
                                'linkType' => 'Space',
                                'id' => 'cfexampleapi',
                            ],
                        ],
                        'type' => 'Entry',
                        'contentType' => [
                            'sys' => [
                                'type' => 'Link',
                                'linkType' => 'ContentType',
                                'id' => 'cat',
                            ],
                        ],
                        'id' => 'happycat',
                        'revision' => 8,
                        'createdAt' => '2013-06-27T22:46:20.171Z',
                        'updatedAt' => '2013-11-18T15:58:02.018Z',
                        'locale' => 'en-US',
                    ],
                ],
            ],
            'includes' => [
                'Entry' => [
                    [
                        'fields' => [
                            'name' => 'Nyan Cat',
                            'likes' => [
                                'rainbows',
                                'fish',
                            ],
                            'color' => 'rainbow',
                            'bestFriend' => [
                                'sys' => [
                                    'type' => 'Link',
                                    'linkType' => 'Entry',
                                    'id' => 'happycat',
                                ],
                            ],
                            'birthday' => '2011-04-04T22:00:00+00:00',
                            'lives' => 1337,
                            'image' => [
                                'sys' => [
                                    'type' => 'Link',
                                    'linkType' => 'Asset',
                                    'id' => 'nyancat',
                                ],
                            ],
                        ],
                        'sys' => [
                            'space' => [
                                'sys' => [
                                    'type' => 'Link',
                                    'linkType' => 'Space',
                                    'id' => 'cfexampleapi',
                                ],
                            ],
                            'type' => 'Entry',
                            'contentType' => [
                                'sys' => [
                                    'type' => 'Link',
                                    'linkType' => 'ContentType',
                                    'id' => 'cat',
                                ],
                            ],
                            'id' => 'nyancat',
                            'revision' => 5,
                            'createdAt' => '2013-06-27T22:46:19.513Z',
                            'updatedAt' => '2013-09-04T09:19:39.027Z',
                            'locale' => 'en-US',
                        ],
                    ],
                ],
                'Asset' => [
                    [
                        'fields' => [
                            'file' => [
                                'fileName' => 'happycatw.jpg',
                                'contentType' => 'image/jpeg',
                                'details' => [
                                    'image' => [
                                        'width' => 273,
                                        'height' => 397,
                                    ],
                                    'size' => 59939,
                                ],
                                'url' => '//images.contentful.com/cfexampleapi/3MZPnjZTIskAIIkuuosCss/382a48dfa2cb16c47aa2c72f7b23bf09/happycatw.jpg',
                            ],
                            'title' => 'Happy Cat',
                        ],
                        'sys' => [
                            'space' => [
                                'sys' => [
                                    'type' => 'Link',
                                    'linkType' => 'Space',
                                    'id' => 'cfexampleapi',
                                ],
                            ],
                            'type' => 'Asset',
                            'id' => 'happycat',
                            'revision' => 2,
                            'createdAt' => '2013-09-02T14:56:34.267Z',
                            'updatedAt' => '2013-09-02T15:11:24.361Z',
                            'locale' => 'en-US',
                        ],
                    ],
                    [
                        'fields' => [
                            'title' => 'Nyan Cat',
                            'file' => [
                                'fileName' => 'Nyan_cat_250px_frame.png',
                                'contentType' => 'image/png',
                                'details' => [
                                    'image' => [
                                        'width' => 250,
                                        'height' => 250,
                                    ],
                                    'size' => 12273,
                                ],
                                'url' => '//images.contentful.com/cfexampleapi/4gp6taAwW4CmSgumq2ekUm/9da0cd1936871b8d72343e895a00d611/Nyan_cat_250px_frame.png',
                            ],
                        ],
                        'sys' => [
                            'space' => [
                                'sys' => [
                                    'type' => 'Link',
                                    'linkType' => 'Space',
                                    'id' => 'cfexampleapi',
                                ],
                            ],
                            'type' => 'Asset',
                            'id' => 'nyancat',
                            'revision' => 1,
                            'createdAt' => '2013-09-02T14:56:34.240Z',
                            'updatedAt' => '2013-09-02T14:56:34.240Z',
                            'locale' => 'en-US',
                        ],
                    ],
                ],
            ],
        ];
        $array = $this->builder->buildFromData($data);
        $this->assertInstanceOf('Markup\Contentful\ResourceArray', $array);
        $this->assertCount(1, $array);
    }
}
