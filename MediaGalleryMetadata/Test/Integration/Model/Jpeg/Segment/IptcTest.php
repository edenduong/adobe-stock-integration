<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGalleryMetadata\Test\Integration\Model\Jpeg\Segment;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\DriverInterface;
use Magento\MediaGalleryMetadata\Model\Jpeg\FileReader;
use Magento\MediaGalleryMetadata\Model\Jpeg\Segment\IptcReader;
use Magento\MediaGalleryMetadata\Model\Jpeg\Segment\IptcWriter;
use Magento\MediaGalleryMetadata\Model\MetadataFactory;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * Test for IPTC reader and writer
 */
class IptcTest extends TestCase
{
    /**
     * @var IptcWriter
     */
    private $iptcWriter;

    /**
     * @var IptcReader
     */
    private $iptcReader;

    /**
     * @var DriverInterface
     */
    private $driver;

    /**
     * @var FileReader
     */
    private $fileReader;

    /**
     * @var MetadataFactory
     */
    private $metadataFactory;

    /**
     * @var WriteInterface
     */
    private $varDirectory;

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        $this->varDirectory = Bootstrap::getObjectManager()->get(Filesystem::class)
            ->getDirectoryWrite(DirectoryList::VAR_DIR);
        $this->iptcWriter = Bootstrap::getObjectManager()->get(IptcWriter::class);
        $this->iptcReader = Bootstrap::getObjectManager()->get(IptcReader::class);
        $this->fileReader = Bootstrap::getObjectManager()->get(FileReader::class);
        $this->driver = Bootstrap::getObjectManager()->get(DriverInterface::class);
        $this->metadataFactory = Bootstrap::getObjectManager()->get(MetadataFactory::class);
    }

    /**
     * Test for IPTC reader and writer
     *
     * @dataProvider filesProvider
     * @param string $fileName
     * @param string $title
     * @param string $description
     * @param array $keywords
     * @throws LocalizedException
     */
    public function testWriteRead(
        string $fileName,
        string $title,
        string $description,
        array $keywords
    ): void {
        $path = realpath(__DIR__ . '/../../../../_files/' . $fileName);
        $modifiableFilePath = $this->varDirectory->getAbsolutePath($fileName);
        $this->driver->copy(
            $path,
            $modifiableFilePath
        );
        $modifiableFilePath = $this->fileReader->execute($modifiableFilePath);
        $originalMetadata = $this->iptcReader->execute($modifiableFilePath);

        $this->assertEmpty($originalMetadata->getTitle());
        $this->assertEmpty($originalMetadata->getDescription());
        $this->assertEmpty($originalMetadata->getKeywords());

        $updatedFile = $this->iptcWriter->execute(
            $modifiableFilePath,
            $this->metadataFactory->create([
                'title' => $title,
                'description' => $description,
                'keywords' => $keywords
            ])
        );

        $updatedMetadata = $this->iptcReader->execute($updatedFile);

        $this->assertEquals($title, $updatedMetadata->getTitle());
        $this->assertEquals($description, $updatedMetadata->getDescription());
        $this->assertEquals($keywords, $updatedMetadata->getKeywords());
    }

    /**
     * Data provider for testExecute
     *
     * @return array[]
     */
    public function filesProvider(): array
    {
        return [
            [
                'empty_iptc.jpeg',
                'Updated Title',
                'Updated Description',
                [
                    'magento2',
                    'mediagallery'
                ]
            ]
        ];
    }
}
