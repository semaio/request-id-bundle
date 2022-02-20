<?php

declare(strict_types=1);

namespace Semaio\RequestId\Test;

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

final class TestKernel extends Kernel
{
    /**
     * @inheritDoc
     */
    public function registerBundles(): iterable
    {
        $bundles = array(
            new \Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new \Symfony\Bundle\TwigBundle\TwigBundle(),
            new \Symfony\Bundle\MonologBundle\MonologBundle(),
            new \Semaio\RequestId\SemaioRequestIdBundle(),
        );

        return $bundles;
    }

    /**
     * @inheritDoc
     */
    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load($this->getProjectDir().'/config/config.yml');
    }

    /**
     * @inheritDoc
     */
    public function getLogDir(): string
    {
        return __DIR__.'/tmp';
    }

    /**
     * @inheritDoc
     */
    public function getCacheDir(): string
    {
        return __DIR__.'/tmp';
    }

    /**
     * @inheritDoc
     */
    public function getProjectDir(): string
    {
        return __DIR__;
    }
}
