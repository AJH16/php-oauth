<?php

namespace OAuth\Unit\UserData\Extractor;

use Gregwar\Image\Image;
use OAuth\UserData\Arguments\FieldsValues;
use OAuth\UserData\Extractor\Extractor;
use OAuth\UserData\Extractor\ExtractorInterface;

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.1 on 2014-02-08 at 10:56:38.
 */
class ExtractorTest extends \PHPUnit_Framework_TestCase
{

    protected $fields;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->fields = [
            ExtractorInterface::FIELD_UNIQUE_ID => ['support' => 'supportsUniqueId', 'getter' => 'getUniqueId', 'value' => '1234567'],
            ExtractorInterface::FIELD_USERNAME => ['support' => 'supportsUsername', 'getter' => 'getUsername', 'value' => 'johnnydonny'],
            ExtractorInterface::FIELD_FIRST_NAME => ['support' => 'supportsFirstName', 'getter' => 'getFirstName', 'value' => 'john'],
            ExtractorInterface::FIELD_LAST_NAME => ['support' => 'supportsLastName', 'getter' => 'getLastName', 'value' => 'doe'],
            ExtractorInterface::FIELD_FULL_NAME => ['support' => 'supportsFullName', 'getter' => 'getFullName', 'value' => 'john doe'],
            ExtractorInterface::FIELD_EMAIL => ['support' => 'supportsEmail', 'getter' => 'getEmail', 'value' => 'johndoe@gmail.com'],
            ExtractorInterface::FIELD_DESCRIPTION => ['support' => 'supportsDescription', 'getter' => 'getDescription', 'value' => 'A life on the edge'],
            ExtractorInterface::FIELD_LOCATION => ['support' => 'supportsLocation', 'getter' => 'getLocation', 'value' => 'Rome, Italy'],
            ExtractorInterface::FIELD_PROFILE_URL => ['support' => 'supportsProfileUrl', 'getter' => 'getProfileUrl', 'value' => 'http://social.co/johnnydonny'],
            ExtractorInterface::FIELD_IMAGE_URL => ['support' => 'supportsImageUrl', 'getter' => 'getImageUrl', 'value' => 'http://social.co/avatar/johnnydonny.jpg'],
            ExtractorInterface::FIELD_WEBSITES => ['support' => 'supportsWebsites', 'getter' => 'getWebsites', 'value' => [
                'http://johnnydonny.com',
                'http://blog.johnnydonny.com',
            ]],
            ExtractorInterface::FIELD_VERIFIED_EMAIL => ['support' => 'supportsVerifiedEmail', 'getter' => 'isEmailVerified', 'value' => TRUE],
            ExtractorInterface::FIELD_EXTRA => ['support' => 'supportsExtra', 'getter' => 'getExtras', 'value' => [
                'foo' => 'bar',
                'skills' => ['php', 'symfony', 'butterflies']
            ]]
        ];
    }

    public function testSupportsFields()
    {
        foreach ($this->fields as $field => $data)
        {
            $extractor = new Extractor(FieldsValues::construct([
                $field => $data[ 'value' ]
            ]));

            $this->assertTrue($extractor->{$data[ 'support' ]}(),
                sprintf('Failed asserting that "%s" must return true', $data[ 'support' ]));
            $this->assertEquals($data[ 'value' ], $extractor->{$data[ 'getter' ]}(),
                sprintf('Failed asserting that "%s" must return %s', $data[ 'getter' ], json_encode($data[ 'value' ])));
        }
    }

    public function testDoesNotSupportFields()
    {
        foreach ($this->fields as $field => $data) {
            $extractor = new Extractor(FieldsValues::construct());

            $this->assertFalse($extractor->{$data[ 'support' ]}(),
                sprintf('Failed asserting that "%s" must return false', $data[ 'support' ]));
            $this->assertNull($extractor->{$data[ 'getter' ]}(),
                sprintf('Failed asserting that "%s" must return null', $data[ 'getter' ]));
        }
    }

    public function testGetExtra()
    {
        $extractor = new Extractor(FieldsValues::construct([
            ExtractorInterface::FIELD_EXTRA => [
                'foo' => 'bar',
                'bar' => 'baz'
            ]
        ]));

        $this->assertEquals('bar', $extractor->getExtra('foo'));
        $this->assertEquals('baz', $extractor->getExtra('bar'));
        $this->assertNull($extractor->getExtra('baz'));
    }

    public function testSetService()
    {
        /**
         * @var \OAuth\Common\Service\ServiceInterface $service
         */
        $service = $this->getMock('\\OAuth\\Common\\Service\\ServiceInterface');
        $extractor = new Extractor();
        $extractor->setService($service);
    }

    public function testGetService()
    {
        /**
         * @var \OAuth\Common\Service\ServiceInterface $service
         */
        $service = $this->getMock('\\OAuth\\Common\\Service\\ServiceInterface');
        $extractor = new Extractor();
        $extractor->setService($service);

        $this->assertSame($service, $extractor->getService());
    }

    /**
     * @expectedException \OAuth\Common\Exception\Exception
     */
    public function testGetGetServiceThrowExceptionIfNoServiceWasSet()
    {
        $extractor = new Extractor();
        $extractor->getService();
    }

    public function testGetServiceId()
    {
        /**
         * @var \OAuth\Common\Service\ServiceInterface|\PHPUnit_Framework_MockObject_MockObject $service
         */
        $serviceName = 'test-service';
        $service = $this->getMock('\\OAuth\\Common\\Service\\AbstractService', [
            // Mocked
            'service',

            // Abstract
            'request', 'getAuthorizationUri'
        ], [], '', FALSE);

        $service->expects($this->once())->method('service')->will($this->returnValue($serviceName));
        $extractor = new Extractor();
        $extractor->setService($service);

        $this->assertSame($serviceName, $extractor->getServiceId());
    }

    public function testGetImageRawData()
    {
        $imageUrl = 'http://upload.wikimedia.org/wikipedia/commons/3/31/Red-dot-5px.png';
        // $imageRawBase64 = base64_encode(file_get_contents($imageUrl));
        $imageRawBase64 = 'iVBORw0KGgoAAAANSUhEUgAAAAUAAAAFCAYAAACNbyblAAAAHElEQVQI12P4//8/w38GIAXDIBKE0DHxgljNBAAO9TXL0Y4OHwAAAABJRU5ErkJggg==';

        /**
         * @var \OAuth\Common\Service\ServiceInterface|\PHPUnit_Framework_MockObject_MockObject $service
         */
        $service = $this->getMock('\\OAuth\\Common\\Service\\AbstractService', [
            // Mocked
            'httpRequest',

            // Abstract
            'request', 'getAuthorizationUri'
        ], [], '', FALSE);

        $service->expects($this->once())->method('httpRequest')->willReturn(base64_decode($imageRawBase64));

        $extractor = new Extractor(FieldsValues::construct([
            Extractor::FIELD_IMAGE_URL => $imageUrl
        ]));
        $extractor->setService($service);
        $this->assertSame(
            base64_encode(Image::fromData(base64_decode($imageRawBase64))->get()),
            base64_encode($extractor->getImageRawData())
        );
    }

    public function testGetResizedImageRawData()
    {
        $imageUrl = 'http://upload.wikimedia.org/wikipedia/commons/3/31/Red-dot-5px.png';
        // $imageRawBase64 = base64_encode(file_get_contents($imageUrl));
        $imageRawBase64 = 'iVBORw0KGgoAAAANSUhEUgAAAAUAAAAFCAYAAACNbyblAAAAHElEQVQI12P4//8/w38GIAXDIBKE0DHxgljNBAAO9TXL0Y4OHwAAAABJRU5ErkJggg==';

        /**
         * @var \OAuth\Common\Service\ServiceInterface|\PHPUnit_Framework_MockObject_MockObject $service
         */
        $service = $this->getMock('\\OAuth\\Common\\Service\\AbstractService', [
            // Mocked
            'httpRequest',

            // Abstract
            'request', 'getAuthorizationUri'
        ], [], '', FALSE);

        $service->expects($this->once())->method('httpRequest')->willReturn(base64_decode($imageRawBase64));

        $extractor = new Extractor(FieldsValues::construct([
            Extractor::FIELD_IMAGE_URL => $imageUrl
        ]));
        $extractor->setService($service);
        /** @noinspection PhpUndefinedMethodInspection */
        $this->assertSame(
            base64_encode(Image::fromData(base64_decode($imageRawBase64))->resize(10, 10)->get()),
            base64_encode($extractor->getImageRawData(10, 10))
        );
    }

    public function testSaveImage()
    {
        $imagePathToSave = __DIR__ . '/' . uniqid() . '.png';
        $imageUrl = 'http://upload.wikimedia.org/wikipedia/commons/3/31/Red-dot-5px.png';
        // $imageRawBase64 = base64_encode(file_get_contents($imageUrl));
        $imageRawBase64 = 'iVBORw0KGgoAAAANSUhEUgAAAAUAAAAFCAYAAACNbyblAAAAHElEQVQI12P4//8/w38GIAXDIBKE0DHxgljNBAAO9TXL0Y4OHwAAAABJRU5ErkJggg==';

        if (!is_writable(dirname($imagePathToSave))) $this->markTestSkipped('Directory not writable');

        /**
         * @var \OAuth\Common\Service\ServiceInterface|\PHPUnit_Framework_MockObject_MockObject $service
         */
        $service = $this->getMock('\\OAuth\\Common\\Service\\AbstractService', [
            // Mocked
            'httpRequest',

            // Abstract
            'request', 'getAuthorizationUri'
        ], [], '', FALSE);

        $service->expects($this->any())->method('httpRequest')->willReturn(base64_decode($imageRawBase64));

        $extractor = new Extractor(FieldsValues::construct([
            Extractor::FIELD_IMAGE_URL => $imageUrl
        ]));
        $extractor->setService($service);

        $this->assertFalse(is_file($imagePathToSave));
        $extractor->saveImage($imagePathToSave, 10, 10);
        $this->assertTrue(is_file($imagePathToSave));
        $fileContents = file_get_contents($imagePathToSave);
        unlink($imagePathToSave);

        /** @noinspection PhpUndefinedMethodInspection */
        $this->assertEquals(
            base64_encode(Image::fromData(Image::fromData(base64_decode($imageRawBase64))->resize(10, 10)->get())->get('png')),
            base64_encode($fileContents)
        );
    }
}