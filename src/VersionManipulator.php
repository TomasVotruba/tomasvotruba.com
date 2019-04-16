<?php declare(strict_types=1);

namespace TomasVotruba\Website;

use Nette\Utils\Strings;
use PharIo\Version\Version;
use TomasVotruba\Website\Exception\ShouldNotHappenException;

final class VersionManipulator
{
    /**
     * @var Version[]
     */
    private $cachedVersionObjects = [];

    public function create(string $version): Version
    {
        if (isset($this->cachedVersionObjects[$version])) {
            return $this->cachedVersionObjects[$version];
        }

        if (! $this->isValid($version)) {
            throw new ShouldNotHappenException();
        }

        $version = $this->normalize($version);

        $this->cachedVersionObjects[$version] = new Version($version);

        return $this->cachedVersionObjects[$version];
    }

    public function isValid(string $version): bool
    {
        if (Strings::match($version, '#(dev|rc|alpha|beta)#i')) {
            return false;
        }

        // no dots
        if (! Strings::contains($version, '.')) {
            return false;
        }

        // too much dots
        if (substr_count($version, '.') > 2) {
            return false;
        }

        return true;
    }

    public function resolveToMinor(Version $version): string
    {
        return 'v' . $version->getMajor()->getValue() . '.' . $version->getMinor()->getValue();
    }

    private function normalize(string $version): string
    {
        if (Strings::startsWith($version, 'v')) {
            return $version;
        }

        return 'v' . $version;
    }
}
