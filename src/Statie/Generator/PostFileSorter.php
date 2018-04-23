<?php declare(strict_types=1);

namespace TomasVotruba\Website\Statie\Generator;

use Symplify\Statie\Generator\Contract\ObjectSorterInterface;
use Symplify\Statie\Generator\FileNameObjectSorter;
use Symplify\Statie\Renderable\File\AbstractFile;
use TomasVotruba\Website\Statie\Exception\MissingIdException;

final class PostFileSorter implements ObjectSorterInterface
{
    /**
     * @var FileNameObjectSorter
     */
    private $fileNameObjectSorter;

    public function __construct()
    {
        $this->fileNameObjectSorter = new FileNameObjectSorter();
    }

    /**
     * @param AbstractFile[] $files
     * @return AbstractFile[]
     */
    public function sort(array $files): array
    {
        $files = $this->fileNameObjectSorter->sort($files);

        return $this->useIdsAsArrayKeys($files);
    }

    /**
     * @param AbstractFile[] $abstractFiles
     * @return AbstractFile[]
     */
    private function useIdsAsArrayKeys(array $abstractFiles): array
    {
        $arrayWithIdAsKey = [];

        foreach ($abstractFiles as $abstractFile) {
            $this->ensureIdIsSet($abstractFile);

            $arrayWithIdAsKey[$abstractFile->getId()] = $abstractFile;
        }

        dump($arrayWithIdAsKey);
        die;

        return $arrayWithIdAsKey;
    }

    private function ensureIdIsSet(AbstractFile $postFile): void
    {
        if ($postFile->getId()) {
            return;
        }

        throw new MissingIdException(sprintf(
            'File "%s" is missing "id:" in its configuration. Complete it.',
            $postFile->getFilePath()
        ));
    }
}
